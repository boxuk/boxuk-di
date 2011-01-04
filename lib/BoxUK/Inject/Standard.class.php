<?php

namespace BoxUK\Inject;

use BoxUK\Reflect\Reflector;
use BoxUK\Inject\Annotation\ScopeSingleton;
use BoxUK\Inject\Scope\SingletonScope;

use ReflectionClass;

/**
 * A lightweight injector for creating/accessing classes.
 *
 * @ScopeSingleton(implements="Injector,BoxUK\Inject\Injector")
 * 
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
class Standard implements Injector {

    /**
     * Inject annotations
     */
    const INJECT_METHOD = 'InjectMethod';
    const INJECT_PARAM = 'InjectParam';
    const INJECT_PROPERTY = 'InjectProperty';

    /**
     * @var BoxUK\Reflect\Reflector
     */
    private $reflector;

    /**
     * @var array Available scope handlers
     */
    private $scopes;

    /**
     * Create a new injector
     *
     * @param BoxUK\Reflect\Reflector $reflector
     */
    public function __construct( Reflector $reflector ) {

        $this->reflector = $reflector;
        $this->scopes = array();

    }

    /**
     * Initialise the injector
     * 
     */
    public function init() {
        
        $singletonScope = $this->getClass( 'BoxUK\Inject\Scope\SingletonScope' );
        $singletonScope->init( $this->reflector );

        $this->addScope( $singletonScope );

        $this->checkScope( $this );
        $this->checkScope( $this->reflector );

    }

    /**
     * Adds a scope to the injector
     * 
     * @param BoxUK\Inject\Scope $scope
     */
    public function addScope( Scope $scope ) {

        $this->scopes[] = $scope;

    }

    /**
     * Returns a scope by name (eg. 'singleton') or null otherwise
     *
     * @param string $name
     *
     * @return BoxUK\Inject\Scope
     */
    public function getScope( $name ) {

        foreach ( $this->scopes as $scope ) {
            if ( preg_match('/^.*' . $name . 'Scope$/i',get_class($scope)) ) {
                return $scope;
            }
        }

        return null;

    }

    /**
     * Tries to fetch an instance of the specified class.  Will return the
     * singleton instance if the class has been annotated as such.
     *
     * @param string $className
     *
     * @return object
     */
    public function getClass( $className ) {

        foreach ( $this->scopes as $scope ) {
            if ( $scope->has($className) ) {
                return $scope->get( $className );
            }
        }

        $object = $this->getNewClass( $className );

        $this->checkScope( $object );

        return $object;
        
    }

    /**
     * Checks a class scope to see if it needs any special treatment (eg. singleton)
     * This will walk up the inheritance chain until it either finds a scope
     * annotation or hits the root.
     *
     * @param object $object
     */
    public function checkScope( $object ) {

        $className = get_class( $object );

        do {
            foreach ( $this->scopes as $scope ) {
                if ( $scope->check($object,$className) ) {
                    break 2;
                }
            }
            $className = $this->reflector->getParentClass( $className );
        }
        
        while ( $className != null );

    }

    /**
     * Returns a new instance of the specified class
     *
     * @param string $className
     *
     * @return object
     */
    public function getNewClass( $className ) {

        $object = $this->hasInjectableConstructor( $className )
            ? $this->getInstance( $className )
            : new $className();

        $this->inject( $object );

        return $object;

    }

    /**
     * Do method and property injection on the specified object
     *
     * @param object $object
     */
    public function inject( $object ) {

        $this->injectMethods( $object );
        $this->injectProperties( $object );

    }

    /**
     * Indicates if the classes constructor is injectable or not.  Injectable
     * constructors will have class type-hints for all their arguments.
     *
     * @param string $className
     *
     * @return bool
     */
    protected function hasInjectableConstructor( $className ) {

        if ( $this->reflector->hasMethod($className,'__construct') ) {

            $params = $this->reflector->getMethodParams( $className, '__construct' );
            
            foreach ( $params as $aParam ) {
                list( $paramName, $paramClass ) = $aParam;
                if ( !$paramClass ) {
                    return false;
                }
            }
            
            return true;

        }

        return false;

    }

    /**
     * Returns an instance of the reflected class with dependencies injected
     * into the constructor.
     *
     * @param string $className
     *
     * @return object
     */
    protected function getInstance( $className ) {
        
        $params = $this->getMethodParams( $className, '__construct' );
        $reflectedClass = new ReflectionClass( $className );
        
        return $reflectedClass->newInstanceArgs( $params );

    }

    /**
     * Returns a method injectable parameters (this will throw an error if there
     * are any non-injectable parameters)
     * 
     * @param string $className
     * @param string $methodName
     * 
     * @return array
     */
    protected function getMethodParams( $className, $methodName ) {

        $params = array();
        $methodParams = $this->reflector->getMethodParams( $className, $methodName );

        foreach ( $methodParams as $param ) {
            list( $paramName, $paramClass ) = $param;
            $createClass = $this->getParamClass(
                $className, $methodName, $paramName, $paramClass
            );
            if ( !$createClass ) {
                throw new \Exception(sprintf(
                    'Non-injectable parameter found in method marked for injection: %s->%s( %s )',
                    $className,
                    $methodName,
                    $paramName
                ));
            }
            $params[] = $this->getClass( $createClass );
        }

        return $params;

    }

    /**
     * Gets the class to use to inject a paramater for
     *
     * @param string $className
     * @param string $methodName
     * @param string $paramName
     * @param string $paramClass
     *
     * @return string
     */
    protected function getParamClass( $className, $methodName, $paramName, $paramClass ) {

        return $this->reflector->methodHasAnnotation( $className, $methodName, self::INJECT_PARAM )
            ? $this->getParamClassFromAnnotation( $className, $methodName, $paramName, $paramClass )
            : $paramClass;

    }

    /**
     * Fetches the class to inject from a InjectParam annotation
     *
     * @param string $className
     * @param string $methodName
     * @param string $paramName
     *
     * @return string
     */
    protected function getParamClassFromAnnotation( $className, $methodName, $paramName, $paramClass ) {

        $annotations = $this->reflector->getMethodAnnotations( $className, $methodName, self::INJECT_PARAM );

        foreach ( $annotations as $annotation ) {
            if ( $annotation->variable == $paramName ) {
                $paramClass = $annotation->class;
                break;
            }
        }

        return $paramClass;

    }

    /**
     * Checks a classes method to find injectable ones (InjectMethod), but won't
     * descend into ignored parent classes.
     *
     * @param object $object
     */
    protected function injectMethods( $object ) {

        $className = get_class( $object );
        $methods = $this->reflector->getMethodsWithAnnotation( $className, self::INJECT_METHOD );

        foreach ( $methods as $methodName ) {
            $this->injectMethod( $object, $methodName );
        }

    }

    /**
     * Injects a method with it's parameters
     *
     * @param object $ $object
     * @param string $methodName
     */
    protected function injectMethod( $object, $methodName ) {

        $params = $this->getMethodParams( get_class($object), $methodName );

        call_user_func_array( array($object,$methodName), $params );
        
    }

    /**
     * Do property injection on the specified class
     *
     * @param object $object
     */
    protected function injectProperties( $object ) {

        $className = get_class( $object );
        $properties = $this->reflector->getPropertiesWithAnnotation( $className, self::INJECT_PROPERTY );
        
        foreach ( $properties as $propertyName ) {
            $this->injectProperty( $object, $propertyName );
        }

    }

    /**
     * Inject the specified property of the class
     *
     * @param object $object
     * @param string $propertyName
     */
    protected function injectProperty( $object, $propertyName ) {

        $className = get_class( $object );
        $annotation = $this->reflector->getPropertyAnnotation( $className, $propertyName, self::INJECT_PROPERTY );
        $propertyClass = $annotation->class
            ? $annotation->class
            : $this->reflector->getPropertyClass( $className, $propertyName );
        $propertyValue = $this->getClass( $propertyClass );

        if ( $this->reflector->isPublicProperty($className,$propertyName) ) {
            $object->$propertyName = $propertyValue;
        }

        else {
            $property = new \ReflectionProperty( $className, $propertyName );
            $property->setAccessible( true );
            $property->setValue( $object, $propertyValue );
        }

    }

}

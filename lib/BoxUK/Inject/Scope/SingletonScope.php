<?php

namespace BoxUK\Inject\Scope;

use BoxUK\Inject\Scope;
use BoxUK\Reflect\Reflector;

/**
 * A scope to handle storing singletons
 * 
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
class SingletonScope implements Scope {

    /**
     * Scope annotations
     */
    const SCOPE_SINGLETON = 'ScopeSingleton';

    /**
     * @var array Stored singletons
     */
    private $classes = array();

    /**
     * Init the scope
     *
     * @param AmaxusReflector $reflector
     */
    public function init( Reflector $reflector ) {

        $this->reflector = $reflector;
        
    }

    /**
     * Returns a singleton if it's been stored, or null
     *
     * @param string $className
     *
     * @return mixed
     */
    public function get( $className ) {

        return $this->has( $className )
            ? $this->classes[ $className ]
            : null;

    }

    /**
     * Indicates if the singleton has been stored
     *
     * @param string $className
     *
     * @return boolean
     */
    public function has( $className ) {

        return isset( $this->classes[$className] );

    }

    /**
     * Checks if the class has been annotated as a singleton
     *
     * @param object $object
     * @param ReflectionAnnotatedClass $oReflClass
     *
     * @return boolean
     */
    public function check( $object, $className ) {

        if ( $this->reflector->classHasAnnotation($className,self::SCOPE_SINGLETON) ) {
            
            $annotation = $this->reflector->getClassAnnotation( $className, self::SCOPE_SINGLETON );
            $this->set( $object );

            if ( $annotation->implements ) {
                $interfaces = explode( ',', $annotation->implements );
                foreach ( $interfaces as $interface ) {
                    $this->set( $object, $interface );
                }
            }

            return true;

        }

        return false;

    }

    /**
     * Stores the class in this scope
     *
     * @param object $object
     * @param string $className
     */
    public function set( $object, $className=null ) {

        if ( !$className ) {
            $className = get_class( $object );
        }

        $this->classes[ $className ] = $object;

    }

}

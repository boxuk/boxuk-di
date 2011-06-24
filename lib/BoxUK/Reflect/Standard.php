<?php

namespace BoxUK\Reflect;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

use ReflectionAnnotatedClass;
use ReflectionAnnotatedMethod;
use ReflectionAnnotatedProperty;

/**
 * Standard reflector implementation which provides methods for accessing
 * reflection and annotation information on classes.
 * 
 * @ScopeSingleton(implements="Reflector,BoxUK\Reflect\Reflector")
 *
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
class Standard implements Reflector {

    /**
     * @var array Regexes for classes to ignore
     */
    private $ignoredPatterns = array();

    /**
     * Adds a pattern of classes to ignore
     *
     * @param string $regex
     */
    public function addIgnoredClassPattern( $regex ) {
        
        $this->ignoredPatterns[] = $regex;

    }

    /**
     * Returns the parent class name, or ''
     *
     * @param string $className
     *
     * @return string
     */
    public function getParentClass( $className ) {

        if ( class_exists($className) ) {

            $class = new ReflectionClass( $className );
            $parent = $class->getParentClass();

            return $parent
                ? $parent->getName()
                : '';
            
        }

        return '';

    }

    /**
     * Indicates if a class has a method
     *
     * @param string $className
     * @param string $methodName
     *
     * @return boolean
     */
    public function hasMethod( $className, $methodName ) {

        $methods = $this->getMethods( $className, $methodName );

        foreach ( $methods as $realMethod ) {
            if ( $realMethod == $methodName ) {
                return true;
            }
        }

        return false;

    }

    /**
     * Returns info about a methods parameters
     *
     * @param string $className
     * @param string $methodName
     *
     * @return array
     */
    public function getMethodParams( $className, $methodName ) {

        $method = new ReflectionMethod( $className, $methodName );
        $params = array();
        
        foreach ( $method->getParameters() as $param ) {
            $class = $param->getClass();
            $params[] = array(
                $param->getName(),
                $class ? $class->getName() : false
            );
        }
        
        return $params;

    }

    /**
     * Returns a classes methods
     *
     * @param string $className
     *
     * @return array
     */
    public function getMethods( $className ) {

        $class = new ReflectionClass( $className );
        $methods = array();

        foreach ( $class->getMethods() as $method ) {

            $declaringClass = $method->getDeclaringClass()
                                     ->getName();

            if ( !$this->isIgnoredClass($declaringClass) ) {
                $methods[] = $method->getName();
            }

        }

        return $methods;

    }

    /**
     * Returns the classes methods with the specified annotation
     *
     * @param string $className
     * @param string $annotation
     *
     * @return array
     */
    public function getMethodsWithAnnotation( $className, $annotation ) {

        $methods = $this->getMethods( $className );
        $methodsWithAnnotation = array();
        
        foreach ( $methods as $methodName ) {
            $method = new ReflectionAnnotatedMethod( $className, $methodName );
            if ( $method->hasAnnotation($annotation) ) {
                $methodsWithAnnotation[] = $methodName;
            }
        }
        
        return $methodsWithAnnotation;

    }

    /**
     * Indicates if the class name matches one of the ignored patterns
     *
     * @param string $className
     *
     * @return bool
     */
    protected function isIgnoredClass( $className ) {

        foreach ( $this->ignoredPatterns as $ignoreRegex ) {
            if ( preg_match("/$ignoreRegex/",$className) ) {
                return true;
            }
        }

        return false;

    }

    /**
     * Indicates if a class has the specified annotation
     *
     * @param string $className
     * @param string $annotation
     *
     * @return boolean
     */
    public function classHasAnnotation( $className, $annotation ) {

        $class = new ReflectionAnnotatedClass( $className );
        
        return $class->hasAnnotation( $annotation );
        
    }

    /**
     * Indicates if a classes method has the specified annotation
     *
     * @param string $className
     * @param string $methodName
     * @param string $annotation
     *
     * @return boolean
     */
    public function methodHasAnnotation( $className, $methodName, $annotation ) {

        $method = new ReflectionAnnotatedMethod( $className, $methodName );

        return $method->hasAnnotation( $annotation );
        
    }

    /**
     * Returns an annotation on a method matching the name specified
     *
     * @param string $className
     * @param string $methodName
     * @param string $annotation
     *
     * @return Annotation
     */
    public function getMethodAnnotation( $className, $methodName, $annotation ) {

        $method = new ReflectionAnnotatedMethod( $className, $methodName );

        return $method->getAnnotation( $annotation );
        
    }

    /**
     * Returns all annotations on a method matching the name specified
     *
     * @param string $className
     * @param string $methodName
     * @param string $annotation
     *
     * @return array
     */
    public function getMethodAnnotations( $className, $methodName, $annotation ) {

        $method = new ReflectionAnnotatedMethod( $className, $methodName );

        return $method->getAllAnnotations( $annotation );

    }

    /**
     * Returns the specified annotation on a class
     *
     * @param string $className
     * @param string $annotation
     *
     * @return Annotation
     */
    public function getClassAnnotation( $className, $annotation ) {
        
        $class = new ReflectionAnnotatedClass( $className );

        return $class->getAnnotation( $annotation );

    }

    /**
     * Returns an array of the names of a classes properties (public and private)
     *
     * @param string $className
     *
     * @return array
     */
    public function getProperties( $className ) {
        
        $class = new ReflectionClass( $className );
        $properties = array();
        
        foreach ( $class->getProperties() as $property ) {

            $declaringClass = $property->getDeclaringClass()
                                       ->getName();

            if ( !$this->isIgnoredClass($declaringClass) ) {
                $properties[] = $property->getName();
            }

        }
        
        return $properties;

    }

    /**
     * Returns all the classes properties that have the specified annotation
     *
     * @param string $className
     * @param string $annotation
     *
     * @return array
     */
    public function getPropertiesWithAnnotation( $className, $annotation ) {
        
        $properties = $this->getProperties( $className );
        $propertiesWithAnnotation = array();
        
        foreach ( $properties as $propertyName ) {
            if ( $this->propertyHasAnnotation($className,$propertyName,$annotation) ) {
                $propertiesWithAnnotation[] = $propertyName;
            }
        }
        
        return $propertiesWithAnnotation;

    }

    /**
     * Indicates if a classes property has the specified annotation
     *
     * @param string $className
     * @param string $propertyName
     * @param string $annotation
     *
     * @return bool
     */
    public function propertyHasAnnotation( $className, $propertyName, $annotation ) {

        $property = new ReflectionAnnotatedProperty( $className, $propertyName );

        return $property->hasAnnotation( $annotation );

    }

    /**
     * Returns the declared class for a classes property (eg. @var SomeClass)
     *
     * @param string $className
     * @param string $propertyName
     *
     * @return string
     */
    public function getPropertyClass( $className, $propertyName ) {
        
        $property = new ReflectionProperty( $className, $propertyName );
        $comment = $property->getDocComment();

        if ( preg_match('/@var ([\w\\\\]+)/i',$comment,$matches) ) {
            return $matches[ 1 ];
        }

        return false;

    }

    /**
     * Returns a named annotation on the property
     *
     * @param string $className
     * @param string $propertyName
     * @param string $annotation
     *
     * @return Annotation
     */
    public function getPropertyAnnotation( $className, $propertyName, $annotation ) {
        
        $property = new ReflectionAnnotatedProperty( $className, $propertyName );
        
        return $property->getAnnotation( $annotation );

    }

    /**
     * Indicates if a given class property is public
     *
     * @param string $className
     * @param string $propertyName
     *
     * @return bool
     */
    public function isPublicProperty( $className, $propertyName ) {

        $property = new ReflectionProperty( $className, $propertyName );

        return $property->isPublic();

    }

}

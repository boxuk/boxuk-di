<?php

namespace BoxUK\Reflect;

use ReflectionClass;
use ReflectionMethod;

use ReflectionAnnotatedClass;
use ReflectionAnnotatedMethod;

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
     * @param string $class
     * @param string $method
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
     * @param string $class
     * @param string $method
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

            $className = $method->getDeclaringClass()
                                 ->getName();

            foreach ( $this->ignoredPatterns as $ignoreRegex ) {
                if ( preg_match("/$ignoreRegex/",$className) ) {
                    break 2;
                }
            }

            $methods[] = $method->getName();

        }

        return $methods;

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

}

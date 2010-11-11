<?php

namespace BoxUK\Reflect;

/**
 * Interface for reflectors which introspect on classes
 * 
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
interface Reflector {

    /**
     * Returns the parent class name, or ''
     *
     * @param string $className
     *
     * @return string
     */
    public function getParentClass( $className );

    /**
     * Indicates if a class has a method
     *
     * @param string $className
     * @param string $methodName
     *
     * @return boolean
     */
    public function hasMethod( $className, $methodName );

    /**
     * Returns the names of a method's parameters
     *
     * @param string $className
     * @param string $methodName
     *
     * @return array
     */
    public function getMethodParams( $className, $methodName );

    /**
     * Returns a classes methods
     *
     * @param string $className
     *
     * @return array
     */
    public function getMethods( $className );

    /**
     * Indicates if a class has the specified annotation
     *
     * @param string $className
     * @param string $annotation
     *
     * @return boolean
     */
    public function classHasAnnotation( $className, $annotation );

    /**
     * Indicates if a classes method has the specified annotation
     *
     * @param string $className
     * @param string $methodName
     * @param string $annotation
     *
     * @return boolean
     */
    public function methodHasAnnotation( $className, $methodName, $annotation );

    /**
     * Returns an annotation on a method matching the name specified
     *
     * @param string $className
     * @param string $methodName
     * @param string $annotation
     *
     * @return Annotation
     */
    public function getMethodAnnotation( $className, $methodName, $annotation );

    /**
     * Returns all annotations on a method matching the name specified
     *
     * @param string $className
     * @param string $methodName
     * @param string $annotation
     *
     * @return array
     */
    public function getMethodAnnotations( $className, $methodName, $annotation );

    /**
     * Returns the specified annotation on a class
     *
     * @param string $className
     * @param string $annotation
     *
     * @return Annotation
     */
    public function getClassAnnotation( $className, $annotation );

    /**
     * When using getMethods() and hasMethod() you can have the results filtered
     * to remove classes matching certain patterns.  This is useful when your
     * classes extend another library it's useless reflecting on.
     *
     * The class name can be a regular expression (eg. "Doctrine_.*")
     *
     * @param string $regex
     */
    public function addIgnoredClassPattern( $regex );

}

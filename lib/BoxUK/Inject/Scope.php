<?php

namespace BoxUK\Inject;

/**
 * Interface for injector scopes.
 *
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
interface Scope {

    /**
     * Tries to fetch a class from the scope.  Returns the class object if it
     * has been stored, or null otherwise
     *
     * @param string $className
     *
     * @return mixed
     */
    public function get( $className );

    /**
     * Sets a class in this scope, optionally specifying the name.
     *
     * @param object $object The object to set in this scope
     * @param string $className
     */
    public function set( $object, $className=null );

    /**
     * Indicates if the specified class name is stored in this scope
     *
     * @param string $className
     *
     * @return boolean
     */
    public function has( $className );

    /**
     * Uses the classes annotation object to check if it has been annotated
     * to be stored in this scope.  If it has it is stored and true is
     * returned, otherwise false is returned.
     *
     * If the reflected class is not passed in then it should be created.
     *
     * @param object $object Object to look for in this scope
     * @param string $className
     *
     * @return boolean
     */
    public function check( $object, $className );

}

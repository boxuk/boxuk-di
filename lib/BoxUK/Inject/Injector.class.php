<?php

namespace BoxUK\Inject;

use BoxUK\Inject\Scope;

/**
 * Interface for injectors to implement
 * 
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
interface Injector {

    /**
     * Gets a class from the injector, may be a singleton
     *
     * @param string $className
     *
     * @return object
     */
    public function getClass( $className );

    /**
     * Returns a class from the injector, creating it ignoring any annotations
     * that specify otherwise.
     *
     * @param string $className
     *
     * @return object
     */
    public function getNewClass( $className );

    /**
     * Adds a scope to the injector
     *
     * @param InjectorScope $scope
     */
    public function addScope( Scope $scope );

    /**
     * Returns a named scope if it has been added to the injector, null otherwise.
     *
     * Specify 'singleton' for SingletonScope, etc...
     *
     * @return InjectorScope
     */
    public function getScope( $name );

    /**
     * Checks a class for any scope annotations
     *
     * @param object $object
     */
    public function checkScope( $object );
    
}

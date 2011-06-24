<?php

namespace BoxUK\Inject\Scope;

use BoxUK\Reflect\Reflector;

/**
 * A scope to handle objects stored in the session.
 *
 * Classes can then be annotated as being ASessionScope, and when they are fetched
 * by the injector they'll be stored in the session.
 *
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
class SessionScope extends StorageScope {

    /**
     * Key in session where data is stored
     */
    const SESSION_KEY = '__sessionscope';

    /**
     * @var SessionHandler
     */
    private $sessionHandler;

    /**
     * Create a new session scope handler
     *
     */
    public function __construct( Reflector $reflector, SessionHandler $sessionHandler ) {

        parent::__construct( $reflector );

        $this->sessionHandler = $sessionHandler;

    }

    /**
     * Returns the data array we store in the session
     *
     * @return array
     */
    protected function getData() {

        return $this->sessionHandler->has( self::SESSION_KEY )
            ? $this->sessionHandler->get( self::SESSION_KEY )
            : array();

    }

    /**
     * Sets the specified data in the session
     *
     * @param array $data Key/value data
     */
    protected function setData( array $data ) {

        $this->sessionHandler->set( self::SESSION_KEY, $data );

    }

    /**
     * Returns the annotation this scope is marked with
     *
     * @return string
     */
    protected function getAnnotation() {

        return 'ScopeSession';
        
    }

}

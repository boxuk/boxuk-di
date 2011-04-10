<?php

namespace BoxUK\Inject\Scope;

/**
 * Interface for session handling classes to implement.
 * 
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
interface SessionHandler {

    /**
     * Indicates if the key exists in the session handler
     *
     * @return bool
     */
    public function has( $key );

    /**
     * Returns the value for the key in the session handler, or null if it
     * does not exist.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get( $key );

    /**
     * Sets the value for the key in the session
     *
     * @param string $key
     * @param string $value
     */
    public function set( $key, $value );

}

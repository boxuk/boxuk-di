<?php

namespace BoxUK\Reflect\Cache;

/**
 * Wrapper object for accessing APC
 *
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
class ApcWrapper {

    /**
     * Fetches the value stored for the specified key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function fetch( $key ) {

        return apc_fetch( $key );

    }

    /**
     * Store the value for the specified key
     *
     * @param string $key
     * @param mixed $data Cache data
     */
    public function store( $key, $data ) {

        apc_store( $key, $data );

    }

}

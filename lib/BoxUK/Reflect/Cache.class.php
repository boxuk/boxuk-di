<?php

namespace BoxUK\Reflect;

use BoxUK\Reflect\Cache\KeyListener;

/**
 * Interface for reflection caching classes
 *
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
interface Cache {

    /**
     * Reads the reflection cache data back from the cache, or returns an empty
     * array if there is no cached data.
     *
     * @returna rray
     */
    public function read();

    /**
     * Writes the array of reflection cache information to the cache object, but
     * this is not committed to the persistence layer yet.
     *
     * @param array $cacheData
     */
    public function write( array $cacheData );

    /**
     * Commits any dirty cache data to the persistence layer
     *
     */
    public function commit();

    /**
     * Set the key to use to write into the cache.
     *
     * @param string $key
     */
    public function setKey( $key );

    /**
     * Returns the current cache key (or null if there isn't one set)
     *
     * @return string
     */
    public function getKey();

    /**
     * Adds a listener for notification when the key changes
     *
     * @param KeyListener $listener
     */
    public function addKeyListener( KeyListener $listener );

    /**
     * Removes a listener that has been added for key changes
     *
     * @param KeyListener $listener
     */
    public function removeKeyListener( KeyListener $listener );
    
}

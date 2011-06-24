<?php

namespace BoxUK\Reflect\Cache;

use BoxUK\Reflect\Cache;

/**
 * Abstract class with some overlap implementation from cache classes
 *
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
abstract class Base implements Cache {

    /**
     * @var array Reflection cache data
     */
    protected $cacheData;

    /**
     * @var bool If the cache is dirty
     */
    protected $isDirty;

    /**
     * @var string The cache key
     */
    protected $key;

    /**
     * @var array Key listeners
     */
    protected $keyListeners;

    /**
     * Inits the base cache object
     * 
     */
    public function __construct() {
        
        $this->cacheData = null;
        $this->isDirty = false;
        $this->key = null;
        $this->keyListeners = array();

    }

    /**
     * Writes data into the cache (not committed yet though)
     *
     * @param array $cacheData Cache data
     */
    public function write( array $cacheData ) {

        $this->cacheData = $cacheData;
        $this->isDirty = true;

    }

    /**
     * Read data from the cache if we don't have it already.
     *
     * @return array
     */
    public function read() {

        if ( !$this->cacheData ) {
            $this->cacheData = $this->rawRead();
        }

        return $this->cacheData;
        
    }

    /**
     * Commit the cache if it's dirty
     *
     */
    public function commit() {

        if ( $this->isDirty ) {
            $this->rawCommit();
            $this->isDirty = false;
        }

    }

    /**
     * Set the current cache key
     *
     * @param string $key
     */
    public function setKey( $key ) {

        if ( $this->key != $key ) {
            $oldKey = $this->key;
            $newKey = $key;
            $this->fireKeyChange( 'before', $oldKey, $newKey );
            $this->key = $key;
            $this->fireKeyChange( 'after', $oldKey, $newKey );
        }
        
    }

    /**
     * Fires a key change event to registered listeners
     *
     * @param string $type 'before' or 'after'
     * @param string $oldKey
     * @param string $newKey
     */
    protected function fireKeyChange( $type, $oldKey, $newKey ) {

        $method = sprintf( '%sKeyChange', $type );

        foreach ( $this->keyListeners as $listener ) {
            $listener->$method( $oldKey, $newKey );
        }

    }

    /**
     * Returns the current cache key, or null if not set
     *
     * @return string
     */
    public function getKey() {

        return $this->key
            ? $this->key
            : $this->getDefaultKey();

    }

    /**
     * Returns the default cache key to use
     *
     * @return string
     */
    protected function getDefaultKey() {

        return get_called_class();

    }

    /**
     * Adds a key listener for notifications when the cache key is changed
     * 
     */
    public function addKeyListener( KeyListener $listener ) {

        $this->keyListeners[] = $listener;

    }

    /**
     * Removed a key listener if it has been added
     *
     */
    public function removeKeyListener( KeyListener $listener ) {

        $keepListeners = array();

        foreach ( $this->keyListeners as $keepListener ) {
            if ( $listener !== $keepListener ) {
                $keepListeners[] = $keepListener;
            }
        }

        $this->keyListeners = $keepListeners;

    }

}

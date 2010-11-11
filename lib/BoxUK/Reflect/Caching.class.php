<?php

namespace BoxUK\Reflect;

use BoxUK\Reflect\Cache\KeyListener;

/**
 * Extends the simple reflector and adds caching to it's methods
 *
 * This class uses a key listener on the cache object so it commits and re-loads
 * data from the cache the when the key is changed.  This is so any cache data
 * loaded until now will be saved and available the next time this object
 * is created, but cached data added after this point will also be saved and
 * reloaded the next time.
 *
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
class Caching extends Standard implements KeyListener {

    /**
     * @var BoxUK\Reflect\Cache
     */
    private $cache;

    /**
     * @var array
     */
    private $cacheData = array();

    /**
     * Creates a new caching reflector using the specified cache type
     * 
     * @param Cache $cache
     */
    public function __construct( Cache $cache ) {

        $this->cache = $cache;
        
    }

    /**
     * Returns the cache the reflector is using
     *
     * @return Cache
     */
    public function getCache() {

        return $this->cache;

    }

    /**
     * Finalises the reflector by persisting any cached data
     *
     */
    public function __destruct() {

        $this->cache->commit();

    }

    /**
     * Initialise the reflector with data from the cache if there is any
     *
     * @param string $cacheDir
     */
    public function init() {

        $this->cache->addKeyListener( $this );

        $this->load();

    }

    /**
     * Load data from the cache
     * 
     */
    protected function load() {
        
        $this->cacheData = $this->cache->read();

    }

    /**
     * The cache key has changed, we need to commit and reload the cache or
     * the data will not be available next run.
     *
     */
    public function keyChanged() {

        $this->cache->commit();
        $this->load();

    }

    /**
     * Handles a call to a method and caches the results
     *
     * @param string $method
     * @param array $params
     *
     * @return mixed
     */
    protected function handle( $method, $params ) {

        $values = array_merge( array($method), $params );
        $hash = md5( implode(',',$values) );

        if ( !isset($this->cacheData[$hash]) ) {
            $this->cacheData[ $hash ] = call_user_func_array(
                array( 'parent', $method ), $params
            );
            $this->cache->write( $this->cacheData );
        }

        return $this->cacheData[ $hash ];

    }

    ////////////////////// INTERCEPT CACHEABLE METHODS /////////////////////////

    /**
     * Intercept method
     */
    public function getParentClass( $className ) {

        return $this->handle( 'getParentClass', array( $className ) );

    }

    /**
     * Intercept method
     */
    public function hasMethod( $className, $method ) {

        return $this->handle( 'hasMethod', array( $className, $method ) );

    }

    /**
     * Intercept method
     */
    public function getMethodParams( $className, $method ) {

        return $this->handle( 'getMethodParams', array( $className, $method ) );

    }

    /**
     * Intercept method
     */
    public function getMethods( $className ) {

        return $this->handle( 'getMethods', array( $className ) );

    }

    /**
     * Intercept method
     */
    public function classHasAnnotation( $className, $annotation ) {

        return $this->handle( 'classHasAnnotation', array( $className, $annotation ) );

    }

    /**
     * Intercept method
     */
    public function methodHasAnnotation( $className, $methodName, $annotation ) {

        return $this->handle( 'methodHasAnnotation', array( $className, $methodName, $annotation ) );

    }

    /**
     * Intercept method
     */
    public function getMethodAnnotations( $className, $methodName, $annotation ) {

        return $this->handle( 'getMethodAnnotations', array( $className, $methodName, $annotation ) );

    }

    /**
     * Intercept method
     */
    public function getClassAnnotation( $className, $annotation ) {

        return $this->handle( 'getClassAnnotation', array( $className, $annotation ) );

    }

}

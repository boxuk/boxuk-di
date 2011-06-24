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
        
        $this->cacheData = $this->cache->rawRead();

    }

    /**
     * The cache key is about to change, we need to commit or
     * the data will not be available next run.
     *
     * @param string $oldKey
     * @param string $newKey
     */
    public function beforeKeyChange( $oldKey, $newKey ) {

        $this->cache->commit();

    }

    /**
     * The cache key has changed, see if there's any new cache to read
     *
     * @param string $oldKey
     * @param string $newKey
     */
    public function afterKeyChange( $oldKey, $newKey ) {

        $this->load();

    }

    /**
     * Handles a call to a method and caches the results
     *
     * @param string $method
     * @param array $params Method parameters to be passed to $method
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
     * 
     * @param string $className
     */
    public function getParentClass( $className ) {

        return $this->handle( 'getParentClass', array($className) );

    }

    /**
     * Intercept method
     * 
     * @param string $className
     * @param string $methodName
     */
    public function hasMethod( $className, $methodName ) {

        return $this->handle( 'hasMethod', array($className,$methodName) );

    }

    /**
     * Intercept method
     * 
     * @param string $className
     * @param string $methodName
     */
    public function getMethodParams( $className, $methodName ) {

        return $this->handle( 'getMethodParams', array($className,$methodName) );

    }

    /**
     * Intercept method
     * 
     * @param string $className
     */
    public function getMethods( $className ) {

        return $this->handle( 'getMethods', array($className) );

    }

    /**
     * Intercept method
     * 
     * @param string $className
     * @param string $annotation
     */
    public function classHasAnnotation( $className, $annotation ) {

        return $this->handle( 'classHasAnnotation', array($className,$annotation) );

    }

    /**
     * Intercept method
     * 
     * @param string $className
     * @param string $methodName
     * @param string $annotation
     */
    public function methodHasAnnotation( $className, $methodName, $annotation ) {

        return $this->handle( 'methodHasAnnotation', array($className,$methodName,$annotation) );

    }

    /**
     * Intercept method
     * 
     * @param string $className
     * @param string $methodName
     * @param string $annotation
     */
    public function getMethodAnnotations( $className, $methodName, $annotation ) {

        return $this->handle( 'getMethodAnnotations', array($className,$methodName,$annotation) );

    }

    /**
     * Intercept method
     * 
     * @param string $className
     * @param string $annotation
     */
    public function getClassAnnotation( $className, $annotation ) {

        return $this->handle( 'getClassAnnotation', array($className,$annotation) );

    }

    /**
     * Intercept method
     * 
     * @param string $className
     * @param string $methodName
     * @param string $annotation
     */
    public function getMethodAnnotation( $className, $methodName, $annotation ) {

        return $this->handle( 'getMethodAnnotation', array($className,$methodName,$annotation) );
        
    }

    /**
     * Intercept method
     * 
     * @param string $className
     */
    public function getProperties( $className ) {

        return $this->handle( 'getProperties', array($className) );

    }

    /**
     * Intercept method
     * 
     * @param string $className
     * @param string $propertyName
     * @param string $annotation
     */
    public function propertyHasAnnotation( $className, $propertyName, $annotation ) {

        return $this->handle( 'propertyHasAnnotation', array($className,$propertyName,$annotation) );

    }

    /**
     * Intercept method
     * 
     * @param string $className
     * @param string $propertyName
     */
    public function getPropertyClass( $className, $propertyName ) {

        return $this->handle( 'getPropertyClass', array($className,$propertyName) );

    }

    /**
     * Intercept method
     * 
     * @param string $className
     * @param string $propertyName
     * @param string $annotation
     */
    public function getPropertyAnnotation( $className, $propertyName, $annotation ) {

        return $this->handle( 'getPropertyAnnotation', array($className,$propertyName,$annotation) );
    }

    /**
     * Intercept method
     * 
     * @param string $className
     * @param string $propertyName
     */
    public function isPublicProperty( $className, $propertyName ) {

        return $this->handle( 'isPublicProperty', array($className,$propertyName) );
        
    }

    /**
     * Intercept method
     * 
     * @param string $className
     * @param string $annotation
     */
    public function getMethodsWithAnnotation( $className, $annotation ) {

        return $this->handle( 'getMethodsWithAnnotation', array($className,$annotation) );

    }

    /**
     * Intercept method
     * 
     * @param string $className
     * @param string $annotation
     */
    public function getPropertiesWithAnnotation( $className, $annotation ) {

        return $this->handle( 'getPropertiesWithAnnotation', array($className,$annotation) );

    }

}

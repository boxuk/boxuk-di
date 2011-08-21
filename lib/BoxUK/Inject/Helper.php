<?php

namespace BoxUK\Inject;

require_once __DIR__ . '/Annotation/ScopeSingleton.php';
require_once __DIR__ . '/Annotation/ScopeSession.php';
require_once __DIR__ . '/Annotation/InjectMethod.php';
require_once __DIR__ . '/Annotation/InjectParam.php';
require_once __DIR__ . '/Annotation/InjectProperty.php';

use BoxUK\Reflect\Cache;
use BoxUK\Reflect\Cache\Apc;
use BoxUK\Reflect\Cache\Memcache;

/**
 * Helper class with some methods for easily creating injectors and reflectors
 *
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
class Helper {

    /**
     * @var Config
     */
    private $config;

    /**
     * Create a new injection helper for creating classes
     * 
     */
    public function __construct( Config $config = null ) {
        
        $this->config = $config
            ? $config
            : $this->getDefaultConfig();
        
    }

    /**
     * Returns the default Config object which is used when none is specified
     * 
     * @return \BoxUK\Inject\Config\Standard
     */
    protected function getDefaultConfig() {

        return new \BoxUK\Inject\Config\Standard();

    }

    /**
     * Returns an injector configured by the given parameters
     *
     * @return \BoxUK\Inject\Injector
     */
    public function getInjector() {

        $reflector = $this->getReflector();
        
        $injector = new \BoxUK\Inject\Standard( $reflector );
        $injector->init();

        return $injector;

    }

    /**
     * Returns a reflector configured by the given parameters
     *
     * @return \BoxUK\Reflect\Reflector
     */
    public function getReflector() {

        if ( $this->config->getReflector() == Config::REFLECTOR_CACHING ) {

            $cache = $this->getReflectorCache();
            
            $reflector = new \BoxUK\Reflect\Caching( $cache );
            $reflector->init();

            return $reflector;

        }

        $reflector = new \BoxUK\Reflect\Standard();

        return $reflector;

    }

    /**
     * Returns the reflector cache to use based on the parameters
     *
     * @return \BoxUK\Reflect\Cache
     */
    public function getReflectorCache() {

        $cache = null;

        switch ( $this->config->getReflectorCache() ) {
            
            case Config::CACHE_MEMCACHE:
                $cache = new \BoxUK\Reflect\Cache\Memcache();
                $cache->init(
                    $this->get( 'memcacheHost', Memcache::DEFAULT_HOST ),
                    $this->get( 'memcachePort', Memcache::DEFAULT_PORT )
                );
                $this->setKey( $cache, 'memcacheKey' );
                break;

            case Config::CACHE_APC:
                $cache = new \BoxUK\Reflect\Cache\Apc();
                $cache->init();
                $this->setKey( $cache, 'apcKey' );
                break;

            default:
                $cache = new \BoxUK\Reflect\Cache\File();
                $cache->init( $this->get('fileCacheDirectory',sys_get_temp_dir()) );
                $this->setKey( $cache, 'filecacheFilename' );

        }

        return $cache;
        
    }

    /**
     * Sets the key for a cache if it has been configured.  The $type defines
     * the parameter to pass to the get() method.
     *
     * @param string $type
     */
    protected function setKey( Cache $cache, $type ) {

        $key = $this->get( $type, false );
        
        if ( $key ) {
            $cache->setKey( $key );
        }

    }

    /**
     * Gets a config value of uses the default if it's null
     *
     * @param string $name
     * @param mixed $default Default if not found
     *
     * @return mixed
     */
    protected function get( $name, $default ) {

        $method = sprintf( 'get%s', ucfirst($name) );
        $value = $this->config->$method();

        return $value
            ? $value
            : $default;

    }

}

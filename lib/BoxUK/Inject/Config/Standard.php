<?php

namespace BoxUK\Inject\Config;

/**
 * Standard config object which gets it's data from an array
 *
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
class Standard implements \BoxUK\Inject\Config {

    /**
     * @var array Name/value config data
     */
    private $data;

    /**
     * Returns the type of Reflector to use (self::REFLECTOR_*)
     *
     * @return integer
     */
    public function getReflector() {

        return $this->get( 'boxuk.reflector' );

    }

    /**
     * Returns the type of reflector cache to use (self::CACHE_*)
     *
     * @return integer
     */
    public function getReflectorCache() {

        return $this->get( 'boxuk.reflector.cache' );

    }

    /**
     * Returns the memcache host
     *
     * @return string
     */
    public function getMemcacheHost() {

        return $this->get( 'boxuk.reflector.memcache.host' );

    }

    /**
     * Returns the memcache port
     *
     * @return integer
     */
    public function getMemcachePort() {

        return $this->get( 'boxuk.reflector.memcache.port' );

    }

    /**
     * Returns the memcache key
     *
     * @return string
     */
    public function getMemcacheKey() {

        return $this->get( 'boxuk.reflector.memcache.key' );

    }

    /**
     * Returns APC cache key
     *
     * @return string
     */
    public function getApcKey() {

        return $this->get( 'boxuk.reflector.apc.key' );

    }

    /**
     * Returns the file cache directory
     *
     * @return string
     */
    public function getFileCacheDirectory() {
        
        return $this->get( 'boxuk.reflector.filecache.dir' );
        
    }

    /**
     * Returns the name of the cache file
     *
     * @return string
     */
    public function getFileCacheFilename() {

        return $this->get( 'boxuk.reflector.filecache.filename' );
        
    }

    /**
     * Initialise the config from an array of data
     *
     * @param array $data
     */
    public function initFromArray( array $data ) {

        $this->data = $data;

    }

    /**
     * Returns a setting value, or null if it doesn't exist
     *
     * @param string $setting
     *
     * @return mixed
     */
    protected function get( $setting ) {

        return isset( $this->data[$setting] )
            ? $this->data[ $setting ]
            : null;

    }

}
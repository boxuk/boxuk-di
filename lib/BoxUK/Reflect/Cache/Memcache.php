<?php

namespace BoxUK\Reflect\Cache;

use BoxUK\Reflect\Cache;

/**
 * Cache implementation that uses Memcached
 *
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
class Memcache extends Base {

    /**
     * Default memcached host
     */
    const DEFAULT_HOST = 'localhost';
    
    /**
     * Default memcached port
     */
    const DEFAULT_PORT = 11211;

    /**
     * @var Memcache The memcached connection
     */
    private $memcache;

    /**
     * @var string Memcache host
     */
    private $host;

    /**
     * @var integer Memcache port
     */
    private $port;

    /**
     * Initialise the cache object
     *
     * @param string $host
     * @param integer $port
     */
    public function init( $host, $port ) {
        
        $this->host = $host;
        $this->port = $port;

        $this->memcache = new \Memcache();
        $this->memcache->addServer( $host, $port, $persist=true );
        
    }

    /**
     * Reads the data from memcached, or returns empty array
     *
     * @return array
     */
    public function rawRead() {

        $cacheData = $this->memcache->get( $this->getKey() );

        return $cacheData
            ? unserialize( $cacheData )
            : array();
        
    }

    /**
     * Commits the cache data to memcached
     *
     */
    public function rawCommit() {

        $this->memcache->set(
            $this->getKey(),
            serialize( $this->cacheData )
        );

    }

    /**
     * Returns the memcache host
     *
     * @return string
     */
    public function getHost() {

        return $this->host;

    }

    /**
     * Returns the memcache port
     *
     * @return integer
     */
    public function getPort() {

        return $this->port;

    }

    /**
     * Sets the internal Memcache object
     * 
     */
    public function setMemcache( \Memcache $memcache ) {

        $this->memcache = $memcache;

    }

}

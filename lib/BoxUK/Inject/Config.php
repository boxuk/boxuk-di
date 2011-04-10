<?php

namespace BoxUK\Inject;

/**
 * Interface for different types of config object to implement
 *
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
interface Config {

    /**
     * Types of reflector
     */
    const REFLECTOR_STANDARD = 'standard';
    const REFLECTOR_CACHING = 'caching';

    /**
     * Types of reflector cache
     */
    const CACHE_FILE = 'file';
    const CACHE_MEMCACHE = 'memcache';
    const CACHE_APC = 'apc';

    /**
     * Returns the type of Reflector to use (self::REFLECTOR_*)
     *
     * @return integer
     */
    public function getReflector();

    /**
     * Returns the type of reflector cache to use (self::CACHE_*)
     *
     * @return integer
     */
    public function getReflectorCache();

    /**
     * Returns the memcache host
     *
     * @return string
     */
    public function getMemcacheHost();

    /**
     * Returns the memcache port
     *
     * @return integer
     */
    public function getMemcachePort();

    /**
     * Returns the memcache key
     *
     * @return string
     */
    public function getMemcacheKey();

    /**
     * Returns the APC key
     *
     * @return string
     */
    public function getApcKey();

    /**
     * Returns the file cache directory
     *
     * @return string
     */
    public function getFileCacheDirectory();

    /**
     * Returns the name of the cache file
     *
     * @return string
     */
    public function getFileCacheFilename();

}

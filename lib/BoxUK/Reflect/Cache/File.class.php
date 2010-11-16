<?php

namespace BoxUK\Reflect\Cache;

use BoxUK\Reflect\Cache;

/**
 * Implements a simple file based cache
 *
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
class File extends Base {

    /**
     * @var string The directory for the cache file
     */
    private $cacheDir;

    /**
     * Initialise the file cache for a specified directory
     *
     * @param string $cacheDir
     */
    public function init( $cacheDir ) {

        $this->cacheDir = $cacheDir;

    }

    /**
     * Loads cache data if it exists
     *
     * @return array
     */
    public function rawRead() {

        $cachePath = $this->getCachePath();

        return file_exists( $cachePath )
            ? unserialize( file_get_contents($cachePath) )
            : array();

    }

    /**
     * Commits the cache data if needed
     * 
     */
    public function rawCommit() {

        $cachePath = $this->getCachePath();

        file_put_contents(
            $cachePath,
            serialize( $this->cacheData )
        );

    }

    /**
     * Returns the cache directory
     *
     * @return string
     */
    public function getCacheDir() {

        return $this->cacheDir;
        
    }

    /**
     * Returns the name of the cache file
     *
     * @return string
     */
    public function getCacheFile() {
        
        return sprintf( '%s', $this->getKey() );

    }

    /**
     * Returns the cache key to use (suitable for part of a filename)
     *
     * @return string
     */
    public function getKey() {

        return strtolower(
            str_replace( '\\', '_', parent::getKey() )
        );
        
    }

    /**
     * Returns the default cache file name
     *
     * @return string
     */
    protected function getDefaultKey() {

        return sprintf( '%s.cache', parent::getDefaultKey() );
        
    }

    /**
     * Returns the full path to the cache file
     *
     * @return string
     */
    protected function getCachePath() {

        return sprintf( '%s/%s', $this->cacheDir, $this->getCacheFile() );
        
    }

}

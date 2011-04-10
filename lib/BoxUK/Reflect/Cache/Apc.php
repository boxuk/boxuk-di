<?php

namespace BoxUK\Reflect\Cache;

/**
 * Cache class for storing reflection data in APC
 *
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
class Apc extends Base {

    /**
     * @var ApcWrapper
     */
    private $apcWrapper;

    /**
     * Initialise the APC cache
     *
     */
    public function init() {

        $this->setApcWrapper( new ApcWrapper() );

    }

    /**
     * Set the APC wrapper to use
     *
     * @param ApcWrapper $apcWrapper
     */
    public function setApcWrapper( ApcWrapper $apcWrapper ) {

        $this->apcWrapper = $apcWrapper;
    }

    /**
     * Returns the current ApcWrapper object
     *
     * @return ApcWrapper
     */
    public function getApcWrapper() {

        return $this->apcWrapper;
        
    }

    /**
     * Try and read cached data from APC
     *
     * @return array
     */
    public function rawRead() {

        $data = $this->apcWrapper->fetch( $this->getKey() );

        return $data
            ? $data
            : array();

    }

    /**
     * Commit the cache data to APC
     *
     */
    public function rawCommit() {

        $this->apcWrapper->store(
            $this->getKey(),
            $this->cacheData
        );

    }

}

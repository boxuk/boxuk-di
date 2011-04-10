<?php

namespace BoxUK\Reflect\Cache;

/**
 * Interface for listeners for key changes on caches
 *
 */
interface KeyListener {

    /**
     * Indicates a key change is about to occur
     *
     * @param string $oldKey
     * @param string $newKey
     */
    public function beforeKeyChange( $oldKey, $newKey );

    /**
     * Indicates a key change has just occurred
     *
     * @param string $oldKey
     * @param string $newKey
     */
    public function afterKeyChange( $oldKey, $newKey );

}

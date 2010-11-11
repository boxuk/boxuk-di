<?php

namespace BoxUK\Reflect\Cache;

/**
 * Interface for listeners for key changes on caches
 *
 */
interface KeyListener {

    /**
     * Indicates
     */
    public function keyChanged();

}

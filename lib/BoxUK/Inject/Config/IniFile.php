<?php

namespace BoxUK\Inject\Config;

/**
 * A config object that can be loaded from an ini file
 *
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
class IniFile extends Standard {

    /**
     * Initialise the config from an ini file
     *
     * @param string $filePath
     */
    public function initFromFile( $filePath ) {

        $this->initFromArray( parse_ini_file($filePath) );
        
    }

}

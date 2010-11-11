<?php

namespace BoxUK\Inject\Config;

require_once 'tests/php/bootstrap.php';

use BoxUK\Inject\Config;

class IniFileTest extends \PHPUnit_Framework_TestCase {

    private $config;
    
    public function setUp() {
        $this->config = new IniFile();
    }

    public function testInitialisingFromFileSetsSettings() {
        $this->config->initFromFile( __DIR__ . '/../../../../resources/inifile.ini' );
        $this->assertEquals( Config::REFLECTOR_CACHING, $this->config->getReflector() );
    }

}

<?php

namespace BoxUK\Inject\Config;

require_once 'tests/php/bootstrap.php';

use BoxUK\Inject\Config;

class StandardTest extends \PHPUnit_Framework_TestCase {

    private $config;
    
    public function setUp() {
        $this->config = new Standard();
    }

    public function testInitialisingUsingAnArraySetsSettings() {
        $expected = Config::REFLECTOR_CACHING;
        $this->config->initFromArray(array( 'boxuk.reflector' => $expected ));
        $this->assertEquals( $expected, $this->config->getReflector() );
    }

    public function testGettingNonExistantSettingReturnsNull() {
        $this->assertNull( $this->config->getReflector() );
    }

    public function testGettingTheReflectorTypeSetting() {
        $expected = Config::REFLECTOR_CACHING;
        $this->config->initFromArray(array( 'boxuk.reflector' => $expected ));
        $this->assertEquals( $expected, $this->config->getReflector() );
    }

    public function testGettingTheReflectorCacheTypeSetting() {
        $expected = Config::CACHE_FILE;
        $this->config->initFromArray(array( 'boxuk.reflector.cache' => $expected ));
        $this->assertEquals( $expected, $this->config->getReflectorCache() );
    }

    public function testGettingTheMemcacheHostSetting() {
        $expected = 'domain.com';
        $this->config->initFromArray(array( 'boxuk.reflector.memcache.host' => $expected ));
        $this->assertEquals( $expected, $this->config->getMemcacheHost() );
    }

    public function testGettingTheMemcachePortSetting() {
        $expected = '12345';
        $this->config->initFromArray(array( 'boxuk.reflector.memcache.port' => $expected ));
        $this->assertEquals( $expected, $this->config->getMemcachePort() );
    }

    public function testGettingTheFileCacheDirectory() {
        $expected = '/some/cache/dir';
        $this->config->initFromArray(array( 'boxuk.reflector.filecache.dir' => $expected ));
        $this->assertEquals( $expected, $this->config->getFileCacheDirectory() );
    }

}

<?php

namespace BoxUK\Inject;

require_once 'tests/php/bootstrap.php';

class HelperTest extends \PHPUnit_Framework_TestCase {

    private function getHelper( array $data = array() ) {
        $config = new \BoxUK\Inject\Config\Standard();
        $config->initFromArray( $data );
        return new Helper( $config );
    }

    public function testStandardInjectorReturnedByDefault() {
        $injector = $this->getHelper()->getInjector();
        $this->assertInstanceOf( 'BoxUK\Inject\Standard', $injector );
    }

    public function testStandardReflectorUsedByDefault() {
        $reflector = $this->getHelper()->getReflector();
        $this->assertInstanceOf( 'BoxUK\Reflect\Standard', $reflector );
    }

    public function testCachingReflectorUsedWhenConfigured() {
        $helper = $this->getHelper(array( 'boxuk.reflector' => Config::REFLECTOR_CACHING ));
        $reflector = $helper->getReflector();
        $this->assertInstanceOf( 'BoxUK\Reflect\Caching', $reflector );
    }

    public function testFileCacheUsedByDefault() {
        $helper = $this->getHelper();
        $cache = $helper->getReflectorCache();
        $this->assertInstanceOf( 'BoxUK\Reflect\Cache\File', $cache );
    }

    public function testFileCacheDefaultsToSystemTempDirectoryIfNoneSpecified() {
        $cache = $this->getHelper()->getReflectorCache();
        $this->assertEquals( sys_get_temp_dir(), $cache->getCacheDir() );
    }

    public function testFileCacheLocationCanBeConfiguredWithGetFileCacheDirectoryConfig() {
        $dir = '/some/cache/dir';
        $helper = $this->getHelper(array('boxuk.reflector.filecache.dir' => $dir ));
        $cache = $helper->getReflectorCache();
        $this->assertEquals( $dir, $cache->getCacheDir() );
    }

    public function testMemcacheCanBeSpecifiedByGetReflectorCacheConfig() {
        $helper = $this->getHelper(array( 'boxuk.reflector.cache' => 'memcache' ));
        $this->assertInstanceOf( 'BoxUK\Reflect\Cache\Memcache', $helper->getReflectorCache() );
    }

    public function testApcCanBeSpecifiedByGetReflectorCacheConfig() {
        $helper = $this->getHelper(array( 'boxuk.reflector.cache' => Config::CACHE_APC ));
        $this->assertInstanceOf( 'BoxUK\Reflect\Cache\Apc', $helper->getReflectorCache() );
    }

    public function testMemcacheHostCanBeSetWithGetMemcacheHostConfig() {
        $host = 'memcache.com';
        $helper = $this->getHelper(array( 'boxuk.reflector.cache' => 'memcache', 'boxuk.reflector.memcache.host' => $host ));
        $cache = $helper->getReflectorCache();
        $this->assertEquals( $host, $cache->getHost() );
    }

    public function testMemcacheKeyCanBeSetWithMemcacheKeyConfig() {
        $key = 'hjk34h2jk4h23jk4k';
        $helper = $this->getHelper(array( 'boxuk.reflector.cache' => 'memcache', 'boxuk.reflector.memcache.key' => $key ) );
        $cache = $helper->getReflectorCache();
        $this->assertEquals( $key, $cache->getKey() );
    }

    public function testApcKeyCanBeSetWithApcKeyConfig() {
        $key = 'hjk34h2jk4h23jk4k';
        $helper = $this->getHelper(array( 'boxuk.reflector.cache' => 'apc', 'boxuk.reflector.apc.key' => $key ) );
        $cache = $helper->getReflectorCache();
        $this->assertEquals( $key, $cache->getKey() );
    }

    public function testFileCacheFilenameCanBeSetWithFilecacheFileConfig() {
        $filename = 'hjk34h2jk4h23jk4k.cache';
        $helper = $this->getHelper(array( 'boxuk.reflector.filecache.filename' => $filename ) );
        $cache = $helper->getReflectorCache();
        $this->assertEquals( $filename, $cache->getCacheFile() );
    }

    public function testMemcacheHostDefaultsToLocalhost() {
        $helper = $this->getHelper(array( 'boxuk.reflector.cache' => 'memcache' ));
        $cache = $helper->getReflectorCache();
        $this->assertEquals( 'localhost', $cache->getHost() );
    }

    public function testMemcachePortDefaultsTo11211() {
        $helper = $this->getHelper(array( 'boxuk.reflector.cache' => 'memcache' ));
        $cache = $helper->getReflectorCache();
        $this->assertEquals( 11211, $cache->getPort() );
    }

    public function testMemcachePortCanBeSetWithGetMemcachePortConfig() {
        $port = '12345';
        $helper = $this->getHelper(array( 'boxuk.reflector.cache' => 'memcache', 'boxuk.reflector.memcache.port' => $port ) );
        $cache = $helper->getReflectorCache();
        $this->assertEquals( $port, $cache->getPort() );
    }

    public function testConfigObjectIsOptionalInConstructor() {
        new Helper();
    }
    
    public function testDefaultsAreReturnedWhenNoConfigObjectSpecified() {
        $helper = new Helper();
        $this->assertInstanceOf( 'BoxUK\Inject\Standard', $helper->getInjector() );
        $this->assertInstanceOf( 'BoxUK\Reflect\Standard', $helper->getReflector() );
    }

}

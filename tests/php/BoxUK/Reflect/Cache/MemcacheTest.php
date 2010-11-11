<?php

namespace BoxUK\Reflect\Cache;

require_once 'tests/php/bootstrap.php';

class MemcacheTest extends \PHPUnit_Framework_TestCase {

    private $cache;
    
    public function setUp() {
        $this->cache = new Memcache();
    }

    public function testHostIsAvailableViaGetterAfterInitialisation() {
        $host = 'domain.com';
        $this->cache->init( $host, 11211 );
        $this->assertEquals( $host, $this->cache->getHost() );
    }

    public function testPortIsAvailableViaGetterAfterInitialisation() {
        $port = 12345;
        $this->cache->init( 'domain.com', $port );
        $this->assertEquals( $port, $this->cache->getPort() );
    }

    public function testReadingDataFromMemcache() {
        $data = 'fozzy';
        $mock = $this->getMock( '\Memcache' );
        $mock->expects( $this->once() )
             ->method( 'get' )
             ->will( $this->returnValue(serialize($data)) );
        $this->cache->setMemcache( $mock );
        ///////////
        $this->assertEquals( $data, $this->cache->read() );
    }

    public function testEmptyArrayReturnedWhenDataIsNotStoredInMemcache() {
        $mock = $this->getMock( '\Memcache' );
        $mock->expects( $this->once() )
             ->method( 'get' )
             ->will( $this->returnValue(null) );
        $this->cache->setMemcache( $mock );
        //////////
        $data = $this->cache->read();
        $this->assertTrue( is_array($data) );
        $this->assertTrue( empty($data) );
    }

    public function testWritingDataToMemcache() {
        $data = array( 'foo' => 'buzzy' );
        $mock = $this->getMock( '\Memcache' );
        $mock->expects( $this->once() )
             ->method( 'set' )
             ->with( $this->anything(), $this->equalTo(serialize($data)) );
        $this->cache->setMemcache( $mock );
        /////////
        $this->cache->write( $data );
        $this->cache->commit();
    }

    public function testCacheOnlyWrittenToMemcacheWhenItsDirty() {
        $mock = $this->getMock( '\Memcache' );
        $mock->expects( $this->once() )
             ->method( 'set' );
        $this->cache->setMemcache( $mock );
        /////////
        $this->cache->write( array(1,2,3) );
        $this->cache->commit();
        $this->cache->commit();
    }

}


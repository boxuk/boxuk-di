<?php

namespace BoxUK\Reflect\Cache;

require_once 'tests/php/bootstrap.php';

class BaseTest extends \PHPUnit_Framework_TestCase {

    private $cache;
    
    public function setUp() {
        $this->cache = $this->getMockForAbstractClass( '\BoxUK\Reflect\Cache\Base' );
    }

    public function testCacheReturnsSensibleDefaultWhenInitialised() {
        $this->assertNotNull( $this->cache->getKey() );
    }

    public function testCacheKeyCanBeCustomized() {
        $key = time() . 'key';
        $this->cache->setKey( $key );
        $this->assertEquals( $key, $this->cache->getKey() );
    }

    public function testKeyListenersAddedToCacheGetEventsWhenTheKeyIsChanged() {
        $listener = $this->getMock( 'BoxUK\Reflect\Cache\MyKeyListener', array('keyChanged') );
        $listener->expects( $this->once() )
                 ->method( 'keyChanged' );
        $this->cache->addKeyListener( $listener );
        $this->cache->setKey( 'new key' );
    }

    public function testKeyListenerDoesntReceiveKeyEventsAfterRemovingIt() {
        $listener = $this->getMock( 'BoxUK\Reflect\Cache\MyKeyListener', array('keyChanged') );
        $listener->expects( $this->never() )
                 ->method( 'keyChanged' );
        $this->cache->addKeyListener( $listener );
        $this->cache->removeKeyListener( $listener );
        $this->cache->setKey( 'new key' );
    }

    public function testKeyChangedEventNotFiredWhenTheNewKeyIsTheSameAsTheOldOne() {
        $listener = $this->getMock( 'BoxUK\Reflect\Cache\MyKeyListener', array('keyChanged') );
        $listener->expects( $this->once() )
                 ->method( 'keyChanged' );
        $this->cache->addKeyListener( $listener );
        $this->cache->setKey( 'new key' );
        $this->cache->setKey( 'new key' );
    }

}

abstract class MyKeyListener implements KeyListener {}

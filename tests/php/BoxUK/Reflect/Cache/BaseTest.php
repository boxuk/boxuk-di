<?php

namespace BoxUK\Reflect\Cache;

require_once 'tests/php/bootstrap.php';

class BaseTest extends \PHPUnit_Framework_TestCase {

    private $cache;

    private function getMockListener() {
        return $this->getMock(
            'BoxUK\Reflect\Cache\MyKeyListener',
            array( 'beforeKeyChange', 'afterKeyChange' )
        );
    }

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
        $listener = $this->getMockListener();
        $listener->expects( $this->once() )
                 ->method( 'beforeKeyChange' );
        $listener->expects( $this->once() )
                 ->method( 'afterKeyChange' );
        $this->cache->addKeyListener( $listener );
        $this->cache->setKey( 'new key' );
    }

    public function testKeyListenerDoesntReceiveKeyEventsAfterRemovingIt() {
        $listener = $this->getMockListener();
        $listener->expects( $this->never() )
                 ->method( 'beforeKeyChange' );
        $this->cache->addKeyListener( $listener );
        $this->cache->removeKeyListener( $listener );
        $this->cache->setKey( 'new key' );
    }

    public function testKeyChangedEventNotFiredWhenTheNewKeyIsTheSameAsTheOldOne() {
        $listener = $this->getMockListener();
        $listener->expects( $this->once() )
                 ->method( 'beforeKeyChange' );
        $this->cache->addKeyListener( $listener );
        $this->cache->setKey( 'new key' );
        $this->cache->setKey( 'new key' );
    }

    public function testBeforeKeyChangeReceivesTheOldKeyAndTheNewKey() {
        $listener = $this->getMockListener();
        $listener->expects( $this->once() )
                 ->method( 'beforeKeyChange' )
                 ->with( $this->equalTo('old'), $this->equalTo('new') );
        $this->cache->setKey( 'old' );
        $this->cache->addKeyListener( $listener );
        $this->cache->setKey( 'new' );
    }

    public function testAfterKeyChangeReceivesTheOldKeyAndTheNewKey() {
        $listener = $this->getMockListener();
        $listener->expects( $this->once() )
                 ->method( 'afterKeyChange' )
                 ->with( $this->equalTo('old'), $this->equalTo('new') );
        $this->cache->setKey( 'old' );
        $this->cache->addKeyListener( $listener );
        $this->cache->setKey( 'new' );
    }

}

abstract class MyKeyListener implements KeyListener {}

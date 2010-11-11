<?php

namespace BoxUK\Reflect\Cache;

require_once 'tests/php/bootstrap.php';

class ApcTest extends \PHPUnit_Framework_TestCase {

    private $cacheData = array( 1, 2, 3 );

    private function getCache( ApcWrapper $apcWrapper ) {
        $cache = new Apc();
        $cache->init();
        $cache->setApcWrapper( $apcWrapper );
        return $cache;
    }

    public function testDataIsReadBackWhenStoredInApc() {
        $wrapper = $this->getMock( '\BoxUK\Reflect\Cache\ApcWrapper' );
        $wrapper->expects( $this->once() )
                ->method( 'fetch' )
                ->will( $this->returnValue($this->cacheData) );
        $cache = $this->getCache( $wrapper );
        /////////////
        $this->assertEquals( $this->cacheData, $cache->read() );
    }

    public function testApcWrapperClassIsSetWhenCacheIsInitialised() {
        $cache = new Apc();
        $cache->init();
        $this->assertInstanceOf( 'BoxUK\Reflect\Cache\ApcWrapper', $cache->getApcWrapper() );
    }

    public function testEmptyArrayReturnedWhenDataNotInApc() {
        $wrapper = $this->getMock( '\BoxUK\Reflect\Cache\ApcWrapper' );
        $wrapper->expects( $this->once() )
                ->method( 'fetch' )
                ->will( $this->returnValue(null) );
        $cache = $this->getCache( $wrapper );
        /////////////
        $this->assertEquals( array(), $cache->read() );
    }

    public function testDataIsWrittenToApcOnCommit() {
        $wrapper = $this->getMock( '\BoxUK\Reflect\Cache\ApcWrapper' );
        $wrapper->expects( $this->once() )
                ->method( 'store' )
                ->with(
                    $this->anything(),
                    $this->equalTo($this->cacheData)
                );
        /////////////
        $cache = $this->getCache( $wrapper );
        $cache->write( $this->cacheData );
        $cache->commit();
    }

    public function testDataOnlyWrittenToApcWhenItsDirty() {
        $wrapper = $this->getMock( '\BoxUK\Reflect\Cache\ApcWrapper' );
        $wrapper->expects( $this->once() )
                ->method( 'store' );
        /////////////
        $cache = $this->getCache( $wrapper );
        $cache->write( array(1,2,3) );
        $cache->commit();
        $cache->commit();
    }

}

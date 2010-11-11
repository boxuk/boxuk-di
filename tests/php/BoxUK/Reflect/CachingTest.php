<?php

namespace BoxUK\Reflect;

require_once 'tests/php/bootstrap.php';

use BoxUK\Reflect\Cache\Base;
use BoxUK\Reflect\Cache\KeyListener;

class CachingTest extends \PHPUnit_Framework_TestCase {

    public function testReflectionCacheDataIsWrittenToCache() {
        $cache = $this->getMockForAbstractClass( 'BoxUK\Reflect\Cache\Base', array('rawCommit','rawRead') );
        $reflector = new Caching( $cache );
        ///////////
        $reflector->getParentClass( 'Caching' );
        $reflector->hasMethod( 'BoxUK\Reflect\Cache\Base', 'foo' );
        $reflector->getMethodParams( 'BoxUK\Reflect\Cache\Base', 'write' );
        $reflector->getMethods( 'BoxUK\Reflect\Cache\Base' );
        $reflector->classHasAnnotation( 'BoxUK\Reflect\Cache\Base', 'ScopeSingleton' );
        $reflector->methodHasAnnotation( 'BoxUK\Reflect\Cache\Base', 'read', 'ScopeSingleton' );
        $reflector->getMethodAnnotations( 'BoxUK\Reflect\Cache\Base', 'read', 'ScopeSingleton' );
        $reflector->getMethodAnnotation( 'BoxUK\Reflect\Cache\Base', 'read', 'ScopeSingleton' );
        $reflector->getClassAnnotation( 'BoxUK\Reflect\Cache\Base', 'ScopeSingleton' );
        ///////////
        $this->assertNotEmpty( $cache->read() );
    }

    public function testDataIsReadFromCacheWhenReflectorIsInitialised() {
        $cache = $this->getMock( 'BoxUK\Reflect\Cache\Base' );
        $cache->expects( $this->once() )
              ->method( 'read' );
        $reflector = new Caching( $cache );
        $reflector->init();
    }
    
    public function testCacheIsCommittedOnObjectDestruct() {
        $cache = $this->getMock( 'BoxUK\Reflect\Cache\Base' );
        $cache->expects( $this->once() )
              ->method( 'commit' );
        $reflector = new Caching( $cache );
        $reflector = null;
    }

    public function testCacheObjectCanBeAccessedViaGetter() {
        $cache = $this->getMock( 'BoxUK\Reflect\Cache\Base' );
        $reflector = new Caching( $cache );
        $this->assertSame( $cache, $reflector->getCache() );
    }

    public function testCacheIsCommittedAndThenDataReloadedWhenCacheKeyChanges() {
        $cache = $this->getMock( 'BoxUK\Reflect\Cache\Base', array('rawRead','rawCommit','read','commit') );
        $cache->expects( $this->exactly(1) ) // once on key change
              ->method( 'commit' );
        $cache->expects( $this->exactly(2) ) // once for init, then once for reload
              ->method( 'read' );
        $reflector = new Caching( $cache );
        $reflector->init();
        $cache->setKey( 'some-new-key' );
    }

}

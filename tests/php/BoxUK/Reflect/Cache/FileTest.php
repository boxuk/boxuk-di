<?php

namespace BoxUK\Reflect\Cache;

require_once 'tests/php/bootstrap.php';

use \vfsStream;
use \vfsStreamWrapper;
use \vfsStreamDirectory;

class FileTest extends \PHPUnit_Framework_TestCase {

    private $cache;
    
    public function setUp() {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot( new vfsStreamDirectory('tmpDir') );
        $this->cache = new File();
        $this->tmpDir = vfsStream::url( 'tmpDir' );
    }

    public function testCacheIsEmptyWhenCreated() {
        $cache = new File();
        $this->assertEmpty( $cache->read() );
    }

    public function testCacheDataIsReturnedAfterBeingCommitted() {
        $data = array( 'foo' => 'bar' );
        $cache1 = new File();
        $cache1->init( $this->tmpDir );
        $cache1->write( $data );
        $cache1->commit();
        $cache2 = new File();
        $cache2->init( $this->tmpDir );
        $this->assertEquals( $data, $cache2->read() );
    }

    public function testCacheDataIsReturnedAfterBeingWritten() {
        $data = array( 'foo' => 'bar' );
        $cache = new File();
        $cache->write( $data );
        $this->assertEquals( $data, $cache->read() );
    }

    public function testCacheDirectoryCanBeAccessedViaGetterAfterInit() {
        $dir = '/path/to/some/dir';
        $this->cache->init( $dir );
        $this->assertEquals( $dir, $this->cache->getCacheDir() );
    }

    public function testCacheWrittenToFileWhenItsDirty() {
        $dir = vfsStreamWrapper::getRoot();
        $this->cache->init( vfsStream::url('tmpDir') );
        $this->cache->write( array(1,2,3) );
        $this->assertFalse( $dir->hasChild($this->cache->getCacheFile()) );
        $this->cache->commit();
        $this->assertTrue( $dir->hasChild($this->cache->getCacheFile()) );
    }

    public function testCacheNotCommittedWhenItsNotDirty() {
        $dir = vfsStreamWrapper::getRoot();
        $this->cache->init( vfsStream::url('tmpDir') );
        $this->cache->commit();
        $this->assertFalse( $dir->hasChild($this->cache->getCacheFile()) );
    }

    public function testFileNameUsedForCacheContainsTheCacheKey() {
        $key = time() . 'key';
        $this->cache->setKey( $key );
        $this->assertContains( $key, $this->cache->getCacheFile() );
    }

    public function testDefaultFilenameUsedHasADotCacheExtension() {
        $this->assertContains( '.cache', $this->cache->getCacheFile() );
    }

}

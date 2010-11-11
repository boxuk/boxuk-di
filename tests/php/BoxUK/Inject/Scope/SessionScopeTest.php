<?php

namespace BoxUK\Inject\Scope;

require_once 'tests/php/bootstrap.php';

class SessionScopeTest extends \PHPUnit_Framework_TestCase {

    private function getInstance( &$data=array() ) {
        $session = new SessionManager();
        $session->initialise( $data );
        $reflector = new \BoxUK\Reflect\Standard();
        $scope = new SessionScope( $reflector, $session );
        $scope->init();
        return $scope;
    }

    public function testClassesNotStoredInitially() {
        $scope = $this->getInstance();
        $this->assertFalse( $scope->has('SettingManager') );
    }

    public function testNonSessionScopedClassesNotStored() {
        $scope = $this->getInstance();
        $className = 'BoxUK\Inject\Scope\SessionScopeTest_TestClass1';
        $class = new $className();
        $this->assertFalse( $scope->check($class,get_class($class)) );
        $this->assertFalse( $scope->has($className) );
        $this->assertNull( $scope->get($className) );
    }

    public function testSessionScopedClassesAreStored() {
        $scope = $this->getInstance();
        $className = 'BoxUK\Inject\Scope\SessionScopeTest_TestClass2';
        $class = new $className();
        $this->assertTrue( $scope->check($class,get_class($class)) );
        $this->assertTrue( $scope->has($className) );
        $this->assertEquals( $class, $scope->get($className) );
    }

    public function testStoredClassesPersistedAcrossLoads() {
        $aData = array();
        $className = 'BoxUK\Inject\Scope\SessionScopeTest_TestClass2';
        $scope1 = $this->getInstance( $aData );
        $class = new $className();
        $scope1->check($class,get_class($class));
        $scope1 = null;
        $scope2 = $this->getInstance( $aData );
        $this->assertTrue( $scope2->has($className) );
        $this->assertEquals( $class, $scope2->get($className) );
    }

}

class SessionScopeTest_TestClass1 {}

/**
 * @ScopeSession
 */
class SessionScopeTest_TestClass2 {}

class SessionManager implements SessionHandler {
    
    private $data;

    public function initialise( &$data ) {
        $this->data =& $data;
    }

    public function get( $key ) {
        return $this->has( $key )
            ? $this->data[ $key ]
            : null;
    }
    
    public function set( $key, $value ) {
        $this->data[ $key ] = $value;
    }
    
    public function has( $key ) {
        return isset( $this->data[$key] );
    }

}

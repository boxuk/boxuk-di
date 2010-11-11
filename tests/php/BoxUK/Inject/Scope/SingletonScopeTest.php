<?php

namespace BoxUK\Inject\Scope;

require_once 'tests/php/bootstrap.php';

class SingletonScopeTest extends \PHPUnit_Framework_TestCase {

    private function getInjector() {
        $injector = new \BoxUK\Inject\Standard( new \BoxUK\Reflect\Standard() );
        $injector->init();
        return $injector;
    }

    private function getInstance() {
        $scope = new SingletonScope();
        $scope->init( new \BoxUK\Reflect\Standard() );
        return $scope;
    }

    public function testInterfaceImplementedCanBeSpecifiedForSingletons() {
        $oInject = $this->getInjector();
        $class1 = $oInject->getClass( 'BoxUK\Inject\Scope\SingletonScopeTest_TestClass7' );
        $class2 = $oInject->getClass( 'BoxUK\Inject\Scope\SingletonScopeTest_TestClass7' );
        $class3 = $oInject->getClass( 'BoxUK\Inject\Scope\SingletonScopeTest_TestInterface2' );
        $this->assertSame( $class1, $class2 );
        $this->assertSame( $class2, $class3 );
    }

    public function testMultipleInterfacesCanBeSpecifiedWithCommers() {
        $oInject = $this->getInjector();
        $class1 = $oInject->getClass( 'BoxUK\Inject\Scope\SingletonScopeTest_TestClass8' );
        $class2 = $oInject->getClass( 'BoxUK\Inject\Scope\SingletonScopeTest_TestInterface2' );
        $class3 = $oInject->getClass( 'FirstInterface' );
        $this->assertSame( $class1, $class2 );
        $this->assertSame( $class2, $class3 );
    }

    public function testNothingInitiallyStored() {
        $scope = $this->getInstance();
        $this->assertFalse( $scope->has('SomeClass') );
    }

    public function testItemsCanBeRetreivedWhenTheyveBeenStored() {
        $scope = $this->getInstance();
        $class = new \BoxUK\Inject\Scope\SettingManager();
        $scope->set( $class );
        $class2 = $scope->get( 'BoxUK\Inject\Scope\SettingManager' );
        $this->assertEquals( $class, $class2 );
    }

    public function testHasReturnsTrueWhenTheItemIsStored() {
        $scope = $this->getInstance();
        $scope->set( new \BoxUK\Inject\Scope\SettingManager() );
        $this->assertTrue( $scope->has('BoxUK\Inject\Scope\SettingManager') );
    }

    public function testHasReturnsFalseWhenTheItemIsNotStored() {
        $scope = $this->getInstance();
        $this->assertFalse( $scope->has('SomeClass') );
    }

    public function testCheckPicksUpClassesAnnotatedToBeSingletons() {
        $scope = $this->getInstance();
        $className = 'BoxUK\Inject\Scope\SingletonScopeTest_TestClass3';
        $class = new $className();
        $this->assertTrue( $scope->check($class,get_class($class)) );
        $this->assertTrue( $scope->has($className) );
    }

    public function testCheckDoesntPickUpClassesNotAnnotatedAsSingletons() {
        $scope = $this->getInstance();
        $className = 'BoxUK\Inject\Scope\SingletonScopeTest_TestClass2';
        $class = new $className();
        $this->assertFalse( $scope->check($class,get_class($class)) );
        $this->assertFalse( $scope->has($className) );
    }

}

class SingletonScopeTest_TestClass2 {}

/**
 *  @ScopeSingleton
 */
class SingletonScopeTest_TestClass3 {}

interface SingletonScopeTest_TestInterface2 {}

/**
 *  @ScopeSingleton(implements="BoxUK\Inject\Scope\SingletonScopeTest_TestInterface2")
 */
class SingletonScopeTest_TestClass7 {

    public $object = null;

    /**
     * @InjectMethod
     *
     */
    public function setSomething( SingletonScopeTest_TestClass3 $object ) {
        $this->object = $object;
    }

}

/**
 *  @ScopeSingleton(implements="FirstInterface,BoxUK\Inject\Scope\SingletonScopeTest_TestInterface2")
 */
class SingletonScopeTest_TestClass8 {}

class SettingManager {}

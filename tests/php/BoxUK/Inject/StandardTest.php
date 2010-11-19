<?php

namespace BoxUK\Inject;

require_once 'tests/php/bootstrap.php';

class StandardTest extends \PHPUnit_Framework_TestCase {

    public function testConstructor() {
        $inject = $this->getInstance();
        $this->assertTrue( $inject instanceof Standard );
        $this->assertTrue( $inject instanceof Injector );
    }

    private function getInstance() {
        $injector = new Standard( new \BoxUK\Reflect\Standard() );
        $injector->init();
        return $injector;
    }

    public function testGettingANewClassReturnsIt() {
        $inject = $this->getInstance();
        $class = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass' );
        $this->assertEquals( get_class($class), 'BoxUK\Inject\StandardInjectorTest_TestClass' );
    }
    
    public function testDependenciesInjectedIntoConstructMethodWhenObjectCreated() {
        $inject = $this->getInstance();
        $class = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass2' );
        $this->assertEquals( get_class($class->class), 'BoxUK\Inject\StandardInjectorTest_TestClass' );
    }

    public function testGettingASingletonReturnsSameInstance() {
        $inject = $this->getInstance();
        $class1 = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass3' );
        $class2 = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass3' );
        $this->assertSame( $class1, $class2 );
    }

    public function testGettingClassAlwaysReturnsNewInstanceWhenNotSingleton() {
        $inject = $this->getInstance();
        $class1 = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass' );
        $class2 = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass' );
        $this->assertNotSame( $class1, $class2 );
    }

    public function testGettingNewClassAlwaysReturnsNewInstance() {
        $inject = $this->getInstance();
        $class1 = $inject->getNewClass( 'BoxUK\Inject\StandardInjectorTest_TestClass3' );
        $class2 = $inject->getNewClass( 'BoxUK\Inject\StandardInjectorTest_TestClass3' );
        $this->assertNotSame( $class1, $class2 );
    }

    public function testStandardInjectorRegistersItselfAsTheInjectorInstance() {
        $inject1 = $this->getInstance();
        $inject2 = $inject1->getClass( 'Injector' );
        $this->assertSame( $inject1, $inject2 );
    }

    public function testStandardInjectorRegistersItselfAsTheInjectorInstanceWithItsFullyQualifiedName() {
        $inject1 = $this->getInstance();
        $inject2 = $inject1->getClass( 'BoxUK\Inject\Injector' );
        $this->assertSame( $inject1, $inject2 );
    }

    public function testInjectAnnotationCanBeUsedToSpecifyClassToInjectForParam() {
        $inject = $this->getInstance();
        $class = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass5' );
        $this->assertTrue( $class instanceof StandardInjectorTest_TestClass5 );
        $this->assertTrue( $class->class instanceof StandardInjectorTest_TestClass6 );
    }

    public function testMethodsCanBeInjectedAtConstructTime() {
        $inject = $this->getInstance();
        $class = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass7' );
        $this->assertTrue( $class->oObject instanceof StandardInjectorTest_TestClass3 );
    }

    public function testSingletonsCanBeAddedAndDefaultToClassName() {
        $inject = $this->getInstance();
        $class1 = new StandardInjectorTest_TestClass();
        $inject->getScope( 'singleton' )->set( $class1 );
        $class2 = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass' );
        $this->assertSame( $class1, $class2 );
    }

    public function testClassNameCanBeSpecifiedWhenAddingSingleton() {
        $inject = $this->getInstance();
        $class1 = new StandardInjectorTest_TestClass();
        $inject->getScope( 'singleton' )->set( $class1, 'SomeInterface' );
        $class2 = $inject->getClass( 'SomeInterface' );
        $this->assertSame( $class1, $class2 );
    }

    public function testParentMethodsAlsoInjected() {
        $inject = $this->getInstance();
        $class = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass8' );
        $this->assertNotNull( $class->oObject );
    }

    public function testParentMethodsNotInjectedIfTheyveBeenOverridden() {
        $inject = $this->getInstance();
        $class = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass9' );
        $this->assertNull( $class->oObject );
    }

    public function testScopeIsInheritedFromParentClass() {
        $inject = $this->getInstance();
        $class1 = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass10' );
        $class2 = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass10' );
        $this->assertSame( $class1, $class2 );
    }

    public function testGettingAScopeByName() {
        $inject = $this->getInstance();
        $scope = $inject->getScope( 'singleton' );
        $this->assertNotNull( $scope );
        $this->assertEquals( get_class($scope), 'BoxUK\Inject\Scope\SingletonScope' );
    }

    public function testGettingAnUnknownScopeReturnsNull() {
        $inject = $this->getInstance();
        $scope = $inject->getScope( 'unknown' );
        $this->assertNull( $scope );
    }

    public function testCheckingClassForScopeWillActOnIt() {
        $inject = $this->getInstance();
        $class = new StandardInjectorTest_TestClass3();
        $inject->checkScope( $class );
        $this->assertSame( $class, $inject->getClass('BoxUK\Inject\StandardInjectorTest_TestClass3') );
    }

    public function testNonInjectableConstructorsAreAssumedToHaveDefaultsSpecified() {
        $inject = $this->getInstance();
        $className = 'BoxUK\Inject\StandardInjectorTest_TestModel2';
        $class = $inject->getClass( $className );
        $this->assertNotNull( $class );
        $this->assertEquals( $className, get_class($class) );
    }

    public function testClassesWithNoDefinedConstructorCanBeCreated() {
        $inject = $this->getInstance();
        $class = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass' );
        $this->assertNotNull( $class );
    }

    public function testInjectParamsAreIgnoredWhenTheyDontMatchParameters() {
        $inject = $this->getInstance();
        $class = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass11' );
    }

    public function testPropertiesWithInjectPropertyAnnotationAreInjectorByVarType() {
        $inject = $this->getInstance();
        $class = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass7' );
        $this->assertInstanceOf( 'BoxUK\Inject\StandardInjectorTest_TestClass3', $class->publicProperty );
    }

    public function testPrivatePropertiesCanBeInjected() {
        $inject = $this->getInstance();
        $class = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass7' );
        $this->assertInstanceOf( 'BoxUK\Inject\StandardInjectorTest_TestClass3', $class->getPrivateProperty() );
    }

    public function testInjectMethodDoesMethodInjection() {
        $inject = $this->getInstance();
        $class = new StandardInjectorTest_TestModel1();
        $inject->inject( $class );
        $this->assertInstanceOf( 'BoxUK\Inject\StandardInjectorTest_TestClass3', $class->object );
    }
    
    public function testInjectMethodDoesPropertyInjection() {
        $inject = $this->getInstance();
        $class = new StandardInjectorTest_TestClass7();
        $inject->inject( $class );
        $this->assertInstanceOf( 'BoxUK\Inject\StandardInjectorTest_TestClass3', $class->publicProperty );
    }

    public function testPropertyInjectionCanHaveTheClassSpecified() {
        $inject = $this->getInstance();
        $class = $inject->getClass( 'BoxUK\Inject\StandardInjectorTest_TestClass7' );
        $this->assertInstanceOf( 'BoxUK\Inject\StandardInjectorTest_TestClass', $class->publicPropertyWithClass );
    }

}

class StandardInjectorTest_TestClass {}

class StandardInjectorTest_TestClass2 {

    public $class = null;

    public function __construct( StandardInjectorTest_TestClass $class ) {
        $this->class = $class;
    }

}

/**
 * @ScopeSingleton
 */
class StandardInjectorTest_TestClass3 {}

class StandardInjectorTest_TestClass4 {
    public function __construct( $CANTCONSTRUCT ) {}
}

interface StandardInjectorTest_TestInterface {}

class StandardInjectorTest_TestClass5 {
    public $class;
    /**
     * @InjectParam(variable="class", class="BoxUK\Inject\StandardInjectorTest_TestClass6")
     *
     */
    public function __construct( StandardInjectorTest_TestInterface $class ) {
        $this->class = $class;
    }
}

class StandardInjectorTest_TestClass6 implements StandardInjectorTest_TestInterface {}

interface StandardInjectorTest_TestInterface2 {}

/**
 *  @ScopeSingleton(implements="BoxUK\Inject\StandardInjectorTest_TestInterface2")
 */
class StandardInjectorTest_TestClass7 {

    public $oObject = null;

    /**
     * @InjectProperty
     * @var BoxUK\Inject\StandardInjectorTest_TestClass3
     */
    public $publicProperty;

    /**
     * @InjectProperty(class="BoxUK\Inject\StandardInjectorTest_TestClass")
     * @var BoxUK\Inject\StandardInjectorTest_TestClass3
     */
    public $publicPropertyWithClass;

    /**
     * @InjectProperty
     * @var BoxUK\Inject\StandardInjectorTest_TestClass3
     */
    private $privateProperty;

    public function getPrivateProperty() {
        return $this->privateProperty;
    }
    
    /**
     * @InjectMethod
     *
     */
    public function setSomething( StandardInjectorTest_TestClass3 $oObject ) {
        $this->oObject = $oObject;
    }

}

class StandardInjectorTest_TestClass8 extends StandardInjectorTest_TestClass7 {}

class StandardInjectorTest_TestClass9 extends StandardInjectorTest_TestClass7 {

    public function setSomething( StandardInjectorTest_TestClass3 $oObject ) {}
    
}

class StandardInjectorTest_TestClass10 extends StandardInjectorTest_TestClass3 {}

class SomeClass {}

class StandardInjectorTest_TestModel1 extends SomeClass {

    public $object = null;

    /**
     * @InjectMethod
     *
     */
    public function setSomething( StandardInjectorTest_TestClass3 $object ) {
        $this->object = $object;
    }

    public function construct() {}

}

class StandardInjectorTest_TestModel2 {
    public function __construct( array $array=array(), $string='foo', $int=123 ) {}
}

class StandardInjectorTest_TestClass11 {
    /**
     * @InjectMethod
     * @InjectParam
     */
    public function setSomething( StandardInjectorTest_TestClass3 $oObject ) {}
}


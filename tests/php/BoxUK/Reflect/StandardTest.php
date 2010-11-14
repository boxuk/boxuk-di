<?php

namespace BoxUK\Reflect;

require_once 'tests/php/bootstrap.php';

class StandardTest extends \PHPUnit_Framework_TestCase {

    private $reflector;

    public function setUp() {
        $this->reflector = new Standard();
    }

    public function testGettingTheParentClassOfAClassReturnsItsNameAsAString() {
        $this->assertEquals( 'BoxUK\Reflect\SimpleReflectorTest_Class1', $this->reflector->getParentClass('BoxUK\Reflect\SimpleReflectorTest_Class2') );
    }
    
    public function testGettingTheParentClassReturnsTheEmptyStringWhenThereIsntOne() {
        $this->assertEquals( '', $this->reflector->getParentClass('BoxUK\Reflect\SimpleReflectorTest_Class1') );
    }

    public function testHasMethodReturnsTrueWhenClassHasAMethod() {
        $this->assertTrue( $this->reflector->hasMethod('BoxUK\Reflect\SimpleReflectorTest_Class1','foo') );
    }

    public function testHasMethodReturnsFalseWhenClassDoesntHaveAMethod() {
        $this->assertFalse( $this->reflector->hasMethod('BoxUK\Reflect\SimpleReflectorTest_Class1','bar') );
    }

    public function testHasMethodReturnsTrueWhenTheMethodIsInAParentClass() {
        $this->assertTrue( $this->reflector->hasMethod('BoxUK\Reflect\SimpleReflectorTest_Class2','foo') );
    }

    public function testGettingMethodParametersReturnsAnArrayOfNameAndTypeInfo() {
        $params = $this->reflector->getMethodParams( 'BoxUK\Reflect\SimpleReflectorTest_Class1', 'bazzle' );
        $this->assertEquals( 3, count($params) );
        $this->assertEquals( $params[0], array('oClass1','BoxUK\Reflect\SimpleReflectorTest_Class1') );
        $this->assertEquals( $params[1], array('string',false) );
        $this->assertEquals( $params[2], array('aArray',false) );
    }

    public function testGettingTheMethodsForAClassReturnsAnArrayOfTheirNames() {
        $methods = $this->reflector->getMethods( 'BoxUK\Reflect\SimpleReflectorTest_Class1' );
        $this->assertEquals( 2, count($methods) );
        $this->assertEquals( $methods, array('foo','bazzle') );
    }

    public function testMethodHasAnnotationReturnsTrueWhenItDoes() {
        $this->assertTrue( $this->reflector->methodHasAnnotation('BoxUK\Reflect\SimpleReflectorTest_Class1','bazzle','InjectParam') );
    }

    public function testMethodHasAnnotationReturnsFalseWhenItDoesnt() {
        $this->assertFalse( $this->reflector->methodHasAnnotation('BoxUK\Reflect\SimpleReflectorTest_Class1','foo','InjectMethod') );
        $this->assertFalse( $this->reflector->methodHasAnnotation('BoxUK\Reflect\SimpleReflectorTest_Class1','bazzle','InjectMethod') );
    }

    public function testGettingAllAnnotationsOfASpecificTypeForAMethodReturnsThem() {
        $annotations = $this->reflector->getMethodAnnotations('BoxUK\Reflect\SimpleReflectorTest_Class1', 'bazzle', 'InjectParam' );
        $this->assertEquals( 2, count($annotations) );
    }

    public function testGettingAClassAnnotationReturnsIt() {
        $annotation = $this->reflector->getClassAnnotation( 'BoxUK\Reflect\SimpleReflectorTest_Class2', 'ScopeSingleton' );
        $this->assertEquals( 'FooBar', $annotation->implements );
    }

    public function testIgnoredClassPatternsRespectedWhenGettingAClassesMethods() {
        $this->reflector->addIgnoredClassPattern( 'Doctrine_.*' );
        $methods = $this->reflector->getMethods( '\BoxUK\Reflect\ChildClass' );
        $this->assertEquals( 0, count($methods) );
    }

    public function testIgnoredClassPatternsRespectedWhenDeterminingIfAClassHasAMethod() {
        $this->reflector->addIgnoredClassPattern( 'Doctrine_.*' );
        $this->assertFalse( $this->reflector->hasMethod('\BoxUK\Reflect\ChildClass','docFoo') );
    }

    public function testGettingAMethodAnnotationReturnsIt() {
        $annotation = $this->reflector->getMethodAnnotation( 'BoxUK\Reflect\ChildClass', 'docFoo', 'InjectMethod' );
        $this->assertInstanceOf( 'InjectMethod', $annotation );
    }

    public function testPropertiesReturnedByGetClassProperties() {
        $properties = $this->reflector->getProperties( 'BoxUK\Reflect\SimpleReflectorTest_Class1' );
        $this->assertEquals( 4, count($properties) );
    }

    public function testGetClassPropertiesReturnsPrivateProperties() {
        $properties = $this->reflector->getProperties( 'BoxUK\Reflect\SimpleReflectorTest_Class1' );
        $this->assertTrue( in_array('private',$properties) );
    }

    public function testGetClassPropertiesReturnsProtectedProperties() {
        $properties = $this->reflector->getProperties( 'BoxUK\Reflect\SimpleReflectorTest_Class1' );
        $this->assertTrue( in_array('protected',$properties) );
    }

    public function testPropertyHasAnnotationReturnsTrueWhenThePropertyHasTheAnnotation() {
        $this->assertTrue( $this->reflector->propertyHasAnnotation('BoxUK\Reflect\SimpleReflectorTest_Class1','public','InjectProperty') );
    }

    public function testPropertyHasAnnotationReturnsFalseWhenThePropertyDoesntHaveTheAnnotation() {
        $this->assertFalse( $this->reflector->propertyHasAnnotation('BoxUK\Reflect\SimpleReflectorTest_Class1','another','InjectProperty') );
    }

    public function testAtVarClassNameReturnedForClassProperty() {
        $this->assertEquals( 'ChildClass', $this->reflector->getPropertyClass('BoxUK\Reflect\SimpleReflectorTest_Class1','public') );
    }

    public function testGettingAPropertiesClassCanHandleNamespaces() {
        $this->assertEquals( 'BoxUK\Reflect\SimpleReflectorTest_Class2', $this->reflector->getPropertyClass('BoxUK\Reflect\ChildClass','someProperty') );
    }
    
    public function testFalseReturnedWhenClassPropertyDoesntHaveAnAtVar() {
        $this->assertFalse( $this->reflector->getPropertyClass('BoxUK\Reflect\SimpleReflectorTest_Class1','another') );
    }

    public function testGetpropertiesDoesntReturnPropertiesFromIgnoredClasses() {
        $this->reflector->addIgnoredClassPattern( 'Doctrine_.*' );
        $properties = $this->reflector->getProperties( 'BoxUK\Reflect\ChildClass' );
        $this->assertFalse( in_array('ignoreMe',$properties) );
    }

    public function testIspublicpropertyReturnsTrueWhenPropertyIsPublic() {
        $this->assertTrue( $this->reflector->isPublicProperty('BoxUK\Reflect\SimpleReflectorTest_Class1','public') );
    }

    public function testIspublicpropertyReturnsFalseWhenPropertyIsProtected() {
        $this->assertFalse( $this->reflector->isPublicProperty('BoxUK\Reflect\SimpleReflectorTest_Class1','protected') );
    }

    public function testIspublicpropertyReturnsFalseWhenPropertyIsPrivate() {
        $this->assertFalse( $this->reflector->isPublicProperty('BoxUK\Reflect\SimpleReflectorTest_Class1','private') );
    }

}

class SimpleReflectorTest_Class1 {
    /**
     * @InjectProperty
     * @var ChildClass
     */
    public $public;
    public $another;
    protected $protected;
    /**
     * @InjectProperty
     * @var ChildClass
     */
    private $private;
    public function foo() {}
    /**
     * @InjectParam(variable=oClass1)
     * @InjectParam
     */
    public function bazzle( SimpleReflectorTest_Class1 $oClass1, $string, array $aArray ) {}
}

/**
 * @ScopeSingleton(implements="FooBar")
 * 
 */
class SimpleReflectorTest_Class2 extends SimpleReflectorTest_Class1 {
    public function subfoo() {}
}

class Doctrine_Class {
    public $ignoreMe;
    /**
     * @InjectMethod
     */
    public function docFoo() {}
}

class ChildClass extends Doctrine_Class {
    /**
     * @InjectProperty
     * @var BoxUK\Reflect\SimpleReflectorTest_Class2
     */
    public $someProperty;
}

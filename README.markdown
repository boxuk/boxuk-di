# Unmaintained

# Box UK - Dependency Injection, Reflection and Annotations

The BoxUK-DI library enables you to easily handle dependency injection between components using annotations and type-hinting, similar to Guice and Spring.

## Dependencies

* PHP 5.3+
* Addendum 0.4.0+

## Including the Library

To include the library just include the bootstrap file in your application.

<pre>
include '../path/to/boxuk-di/lib/bootstrap.php';
</pre>

## The Standard Injector

The main part of the library is the DI container, a simple example to fetch a class...

<pre>
$libraryLoader = $injector->getClass( 'LibraryLoader' );
</pre>

By default, the injector will create a new class each time it's asked for it.  Its constructor parameters will be analysed to check types so that any dependencies can be injected into the new object (if these dependencies don't exist they will be created).  It's methods will also be checked on creation for any that have been annotated for method injection (see below)

## Scopes

By default, new objects will be instantiated for each requested class.  To use objects in a different scope, singleton for example, just annotate them as so:

<pre>
/**
 * @ScopeSingleton
 */
class LibraryLoader {
}
</pre>

### Available Scopes

#### Singleton

The singleton scope is defined by the annotation @ScopeSingleton, and lasts for the lifetime of a request.  Objects annotated as singletons will only be created once by the injector, and then the same object is returned on each subsequent request.

<pre>
/**
 * @ScopeSingleton
 */
class MyClass {}
</pre>

#### Session

The session scope will store objects in the users session, and these will be available for the lifetime of the session.  This can be used for things like a logged in user, a shopping cart, etc...  To give a class session scope just annotate it as so.

<pre>
/**
 * @ScopeSession
 */
class MyShoppingCart {}
</pre>

To use this scope you will first need to define a class that implements the _SessionHandler_ interface and bind it to this name (SessionHandler).

### Interface Binding

If your class is implementing an interface which it is type hinted for then you can specify this by the above annotation:

<pre>
/**
 * @ScopeSingleton(implements="SomeInterface")
 */
class MyClass implements SomeInterface, AnotherInterface {}
</pre>

Then requests for that interface will return this singleton:

<pre>
$oInjector->getClass( 'SomeInterface' );
</pre>

### 3rd Party Singletons

To add 3rd party singletons to the injector just go through the _getScope()_ method.

<pre>
$this->injector->getScope( 'singleton' )->set( $doctrineManager );
</pre>

## Method Injection

You can also annotate methods to be injected:

<pre>
/**
 * @InjectMethod
 */
public function setClassLoader( ClassLoader $oClassLoader ) {}
</pre>

*NB:* When doing method injection there is no constraint on the name of the method, or the number of parameters injected.

### Parameter Types

If your method requires tweaking the injected parameter types then you can specify these with another annotation:

<pre>
/**
 * @InjectMethod
 * @InjectParam(variable="class", class="ModuleRegistry")
 */
public function setSomething( SomeInterface $class ) {
    // will receive a ModuleRegistry
}
</pre>

This can also be used for constructors.

## Property Injection

The final type of injection available is property injection.  This can be used for public *and* private properties.

<pre>
/**
 * @InjectProperty
 * @var SomeClass
 */
private $someClass;
</pre>

The type of object injected is specified by the *@var* PHPDoc.

## Inheritance

### Methods

When doing method injection, the injector will ascend up the inheritance chain to also inject methods in parent classes.  If you override a method in your child class though this method will only be injected (if annotated) in the child class.

### Scopes

When checking a class for scope, the injector will ascend up the inheritance chain and stop at the first scope annotation it encounters.

## Fetching New Classes

To ignore any scope annotations you can force fetching a new instance of the class you want:

<pre>
$oInjector->getNewClass( 'SomeClass' );
</pre>

## Constructor Patterns

The one requirement of the injector is that type hinting or _@InjectParam_ annotations need to be used to identify dependencies, so only classes can be dependencies.  This makes a clean seperation between class dependencies and class configuration.  For classes created with the injector you will not be able to pass in strings or arrays to the constructor.  You can think of this as...

1. Objects are dependencies
2. Anything else is configuration

So you will need to remove any configuration from your constructors and injected methods, this will be moved to initialisation time for your class:

<pre>
$class = $injector->getClass( 'MyClass' );
$class->initialise( $port, array( 'some', 'values' ) );
</pre>

*NB:* initialise() here is just an arbitrary method on the class being created.

## Using the Injector

So, your class has been injected with all it's dependencies, but what if you want to create more objects inside your class?  Well just ask for the injector as one of your dependencies:

<pre>
<code class="php">
private $injector;

public function __construct( BoxUK\Inject\Injector $injector ) {
    $this->injector = $injector;
}

private function myMethod() {
    $class = $this->injector->getClass( 'SomethingElse' );
}
</code>
</pre>

Don't use the injector as a service locator though inside your class, always specify your dependencies to be injected at construct time.

## Inject Arbitrary Objects

The injector also provides an *inject()* method which can be used to do method injection and property injection on arbitrary objects.  These objects can have been created elsewhere but the injector will scan them for dependencies to inject.

<pre>
$injector->inject( $someObject );
</pre>

## The Helper

The easiest way to create an injector is to use the _Helper_ class.

<pre>
$helper = new BoxUK\Inject\Helper();
$injector = $helper->getInjector();
</pre>

### Configuration

When you create the helper you can pass in a _Config_ object.  This example shows a config object generated from an _.ini_ file.

<pre>
$config = new BoxUK\Inject\Config\IniFile();
$config->initFromFile( 'path/to/file.ini' );
$helper = new BoxUK\Inject\Helper( $config );
</pre>

The injector will be all set up and ready to go.  There are also methods to create reflectors and caches.

## Reflection and Annotations

The second part of the library, which the injector is built on is the reflector.  You can use this class to access reflection and annotation information on classes.

<pre>
$reflector = $helper->getReflector();
</pre>

### Caching

Reflection can be slow, so for your applications production mode it's reccomended to use the _BoxUK\Reflect\Caching_ reflector instead.  You can get this through configuration.

<pre>
boxuk.reflector = caching
</pre>

The complete list of configuration options is as follows:

<table>
    <tr>
        <th>Setting</th>
        <th>Values</th>
        <th>Default</th>
    </tr>
    <tr>
        <td>boxuk.reflector</td>
        <td>standard, caching</td>
        <td>standard</td>
    </tr>
    <tr>
        <td>boxuk.reflector.cache</td>
        <td>file, memcache, apc</td>
        <td>file</td>
    </tr>
    <tr>
        <td>boxuk.reflector.filecache.dir</td>
        <td>(path to cache directory)</td>
        <td>(sys_get_temp_dir())</td>
    </tr>
    <tr>
        <td>boxuk.reflector.filecache.filename</td>
        <td>(name of cache file)</td>
        <td>$CLASS.cache</td>
    </tr>
    <tr>
        <td>boxuk.reflector.memcache.host</td>
        <td>(memcache host)</td>
        <td>localhost</td>
    </tr>
    <tr>
        <td>boxuk.reflector.memcache.port</td>
        <td>(memcache port)</td>
        <td>11211</td>
    </tr>
    <tr>
        <td>boxuk.reflector.memcache.key</td>
        <td>(memcache key)</td>
        <td>$CLASS</td>
    </tr>
    <tr>
        <td>boxuk.reflector.apc.key</td>
        <td>(APC key)</td>
        <td>$CLASS</td>
    </tr>
</table>

_($CLASS means the fully qualified name of the class concerned)_

## Unit Testing

You can unit test these classes using:

<pre>
phing test
</pre>

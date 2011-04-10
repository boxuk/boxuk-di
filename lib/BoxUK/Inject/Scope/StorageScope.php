<?php

namespace BoxUK\Inject\Scope;

use BoxUK\Inject\Scope;
use BoxUK\Reflect\Reflector;

/**
 * A base class for scopes that need to store their data somewhere (eg. session).
 *
 * This uses lazy unserializing from storage so it can handle things like being
 * stored in the session where it would usually throw an error because classes
 * hadn't been included yet.
 * 
 * @copyright Copyright (c) 2010, Box UK
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://github.com/boxuk/boxuk-di
 * @since 1.0
 */
abstract class StorageScope implements Scope {
    
    /**
     * Indicates a class is pending unserialization from the session
     */
    const PENDING_UNSERIALISE = true;

    /**
     * @var BoxUK\Reflect\Reflector
     */
    private $reflector;

    /**
     * @var array Internal data store
     */
    private $data;

    /**
     * Constructor
     * 
     */
    public function __construct( Reflector $reflector ) {

        $this->reflector = $reflector;
        $this->data = array();
        
    }

    /**
     * Initialise the data from storage
     *
     */
    public function init() {

        foreach ( $this->getData() as $key => $value ) {
            $this->data[ $key ] = self::PENDING_UNSERIALISE;
        }

    }


    /**
     * Returns an object from storage if it exists, null otherwise
     *
     * @param string $className
     *
     * @return object
     */
    public function get( $className ) {

        if ( isset($this->data[$className]) ) {
            if ( $this->data[$className] === self::PENDING_UNSERIALISE && class_exists($className) ) {
                $sessionData = $this->getData();
                $this->data[ $className ] = unserialize( $sessionData[$className] );
            }
            return $this->data[ $className ];
        }

        return null;

    }

    /**
     * Checks a class to see if it is annotated with the storage scope
     *
     * @param object $object
     * @param string $className
     *
     * @return boolean
     */
    public function check( $object, $className ) {

        if ( $this->reflector->classHasAnnotation($className,$this->getAnnotation()) ) {
            $this->set( $object );
            return true;
        }

        return false;

    }

    /**
     * Indicates if the class is stored
     *
     * @param string $className
     *
     * @return boolean
     */
    public function has( $className ) {

        return isset( $this->data[$className] );

    }

    /**
     * Stores the object, optionally specifying the name
     *
     * @param object $object
     * @param string $className
     */
    public function set( $object, $className=null ) {

        if ( !$className ) {
            $className = get_class( $object );
        }

        $this->data[ $className ] = $object;

    }

    /**
     * Destructor to serialise data to storage
     *
     */
    public function __destruct() {

        $serialised = array();

        foreach ( $this->data as $key => $value ) {
            $data = $this->get( $key );
            if ( $data != null ) {
                $serialised[ $key ] = serialize( $data );
            }
        }

        $this->setData( $serialised );

    }

    /**
     * Returns the data from storage
     *
     * @return array
     */
    abstract protected function getData();

    /**
     * Persists the array of data in storage
     *
     * @param array $data
     */
    abstract protected function setData( array $data );

    /**
     * Returns the name of the storage annotation
     *
     * @return string
     */
    abstract protected function getAnnotation();

}

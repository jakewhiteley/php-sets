<?php

/**
 * Pre PHP 7.0 Set
 * Behaves like a set in java/javascript etc 
 *
 * Stores one dimensional unique data
 */
class Set extends ArrayObject
{
    /**
     * The current amount of values in the set.
     * @var integer
     */
    public $size = 0;

    /**
     * Create new Set
     *
     * @param mixed ...$args Any number of items to add to the Set object
     */
    public function __construct ()
    {
        parent::__construct( [], ArrayObject::STD_PROP_LIST );
        foreach ( func_get_args() as $insert ) {
            $this->add( $insert );
        }
        $this->size = $this->count();
    }

    /**
     * Appends a new element with the given value to the Set object. Returns the Set object.
     * 
     * @param mixed $value Value to add
     * @return Set
     */
    public function add( $value )
    {
        if ( ! $this->has( $value ) ) {
            $this->append( $value );
            $this->size++;  
        }
        return $this;
    }

    /**
     * Removes all elements from the Set object. Returns the Set object.
     * 
     * @return Set
     */
    public function clear()
    {
        $this->exchangeArray( [] );
        $this->size = 0;
        return $this;
    }

    /**
     * Removes the element associated to the value.
     * 
     * @param  mixed $value The value to remove from the Set object
     * @return boolean      True on success, false on failure to delete value
     */
    public function delete( $value )
    {
        $key = array_search( $value, $this->getArrayCopy(), true );
        if ( $key !== false ) {
            unset($this[$key]);
            $this->exchangeArray( array_values($this->getArrayCopy()) );
            $this->size--;
            return true;
        }
        return false;
    }

    /**
     * Calls $callback once for each value present in the Set object, in insertion order.
     * 
     * Any number of additional arguments can be passed to the callback.
     * 
     * @param  callable $callback The callback function
     *                            The callback is called with argument 1 being the current iterated value
     * @param  mixed    ...$args  Additional arguments to pass to the callback function
     * @return ArrayIterator
     */
    public function each( $callback )
    {
        $vars = func_get_args();

        $iterator = $this->entries();
        while ( $iterator->valid() ) {
            unset( $vars[0] );
            array_unshift( $vars, $iterator->current() );
            call_user_func_array( $callback, $vars );
            $iterator->next();
        }
        return $this;
    }

    /**
     * Returns a new ArrayIterator object that contains an array of each element in the Set object, in insertion order.
     *
     * @see http://php.net/manual/en/class.arrayiterator.php
     * @return ArrayIterator
     */
    public function entries()
    {
        return $this->getIterator();
    }

    /**
     * Returns a boolean asserting whether an element is present with the given value in the Set object or not.
     * 
     * @param  mixed  $value The value to check for.
     * @return boolean        
     */
    public function has( $value )
    {
        return array_search( $value, $this->getArrayCopy(), true ) !== false ;
    }   

    /**
     * Returns an array of the values values with the Set onject
     * 
     * @return array
     */
    public function values()
    {
        return $this->getArrayCopy();
    }
}

/**
 * An additional function which allows a non OOP initialization of the Set object
 *
 * @param  mixed ...$args Values to initially add to the Set object
 */
function set() 
{
    return ( new ReflectionClass('Set') )->newInstanceArgs( func_get_args() );
}

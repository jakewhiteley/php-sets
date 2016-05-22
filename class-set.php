<?php

/**
 * PHP implementation of a Java like Set object
 *
 * Stores one dimensional unique data
 * 
 * @author Jake Whiteley <https://github.com/jakewhiteley>
 * @version 1.0
 * @license http://www.gnu.org/licenses/gpl-3.0.en.html
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
        foreach ( func_get_args() as $insert )
            $this->add( $insert );
    }

    /**
     * Appends a new element with the given value to the Set object. Returns the Set object.
     * 
     * @param mixed $value Value to add
     * @return Set
     */
    public function add( $value )
    {
        $this->append( $value );
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

    /**
     * The main setter for the Array functionality.
     *
     * Ensures duplicates cant exist and updates the size property on value insert
     * 
     * @param  int|null $offset  The key to insert a value at
     * @param  mixed    $value   The value to insert
     * @return void
     */
    public function offsetSet( $offset, $value ) 
    {
        $temp = $this->values();
        if ( $this->has( $value ) === false ) {
            if ( is_null($offset) )    
                $temp[] = $value;
            else
                $temp[$offset] = $value;
        }
        $this->exchangeArray($temp);
        $this->size = $this->count();
    }

    /**
     * The main UNsetter for the Array functionality.
     * 
     * @param  int $offset  The key to remove a value at
     * @return void
     */
    public function offsetUnset( $offset ) 
    {
        $temp = $this->values();
        if ( isset($temp[$offset]) )
            unset($temp[$offset]);
        $this->exchangeArray( array_values($temp) );
        $this->size = $this->count();
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

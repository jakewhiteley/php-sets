<?php

namespace PhpSets;

use ArrayIterator;
use ArrayObject;

/**
 * PHP implementation of a Java like Set object
 *
 * Stores one dimensional unique data
 *
 * @author  Jake Whiteley <https://github.com/jakewhiteley>
 * @version 1.2.0
 * @license http://www.gnu.org/licenses/gpl-3.0.en.html
 */
class Set extends ArrayObject
{
    /**
     * The current amount of values in the set.
     *
     * @var integer
     */
    public $size = 0;

    /**
     * Create new Set
     *
     * @param mixed $args Any number of items to add to the Set object
     */
    public function __construct(...$args)
    {
        parent::__construct([], ArrayObject::STD_PROP_LIST);

        foreach ($args as $insert) {
            $this->add($insert);
        }
    }

    /**
     * Appends a new element with the given value to the Set object. Returns the Set object.
     *
     * @param mixed $value Value to add
     * @return Set
     */
    public function add($value): Set
    {
        $this->append($value);
        return $this;
    }

    /**
     * Removes the element associated to the value.
     *
     * @param mixed $value The value to remove from the Set object
     * @return boolean
     */
    public function delete($value): bool
    {
        $key = array_search($value, $this->getArrayCopy(), true);

        if ($key !== false) {
            unset($this[$key]);
            return true;
        }

        return false;
    }

    /**
     * Removes all elements from the Set object. Returns the Set object.
     *
     * @return Set
     */
    public function clear(): Set
    {
        $this->exchangeArray([]);
        $this->size = 0;
        return $this;
    }

    /**
     * Returns a boolean asserting whether an element is present with the given value in the Set object or not.
     *
     * @param mixed $value The value to check for.
     * @return boolean
     */
    public function has($value): bool
    {
        return array_search($value, $this->getArrayCopy(), true) !== false;
    }

    /**
     * Calls $callback once for each value present in the Set object, in insertion order.
     *
     * Any number of additional arguments can be passed to the callback.
     *
     * @param callable $callback  The callback function
     *                            The callback is called with argument 1 being the current iterated value
     * @param mixed    $args      Additional arguments to pass to the callback function
     * @return Set
     */
    public function each(callable $callback, ...$args): Set
    {
        $iterator = $this->entries();

        while ($iterator->valid()) {
            array_unshift($args, $iterator->current());
            call_user_func_array($callback, $args);
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
    public function entries(): ArrayIterator
    {
        return $this->getIterator();
    }

    /**
     * Returns an array of the values values with the Set onject
     *
     * @return array
     */
    public function values(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * Returns a new Set which contains the unique items of the current and a given Set.
     *
     * Elements from the merged set are appended to the current instance's values.
     *
     * @param Set $set The set to append
     * @return Set A new set containing the merged items
     */
    public function unison(Set $set): Set
    {
        $merged = new Set();
        $merged->exchangeArray($this->values());

        foreach ($set->values() as $value) {
            if (!$this->has($value)) {
                $merged->add($value);
            }
        }

        return $merged;
    }

    /**
     * Returns a new Set containing values preset in this set, but not in another given set.
     *
     * @param Set $set Set to compare against
     * @return Set
     *
     */
    public function difference(Set $set): Set
    {
        if ($this->size === 0) {
            return new Set();
        }

        if ($set->size === 0) {
            return (new Set())->unison($this);
        }

        $intersect = new Set;

        foreach ($this->values() as $value) {
            if (!$set->has($value)) {
                $intersect->add($value);
            }
        }

        return $intersect;
    }

    public function symmetricDifference(Set $set): Set
    {
        $diff = clone $this;

        foreach ($set->values() as $value) {
            if ($diff->has($value)) {
                $diff->delete($value);
            } else {
                $diff->add($value);
            }
        }

        return $diff;
    }

    /**
     * Checks if a given $set is a subset of the current instance
     * All values should be present, but ordinality does not matter
     *
     * @param Set $set The Set to check against
     * @return bool Whether $set was a subset of $set
     */
    public function subset(Set $set): bool
    {
        // iterate through $set and return false is an uncommon value is present
        foreach ($set->values() as $value) {
            if (!$this->has($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns a new Set object containing the common elements between two given sets
     *
     * @param Set $set
     * @return Set
     */
    public function intersect(Set $set): Set
    {
        $intersect = new Set();

        foreach ($set->values() as $value) {
            if ($this->has($value)) {
                $intersect->add($value);
            }
        }

        return $intersect;
    }

    /**
     * The main setter for the Array functionality.
     *
     * Ensures duplicates cant exist and updates the size property on value insert
     *
     * @param int|null $offset The key to insert a value at
     * @param mixed    $value  The value to insert
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $temp = $this->values();

        if ($this->has($value) === false) {
            if (is_null($offset)) {
                $temp[] = $value;
            } else {
                $temp[$offset] = $value;
            }
        }

        $this->exchangeArray($temp);

        $this->size = $this->count();
    }

    /**
     * The main un-setter for the Array functionality.
     *
     * @param int $offset The key to remove a value at
     * @return void
     */
    public function offsetUnset($offset)
    {
        $temp = $this->values();

        if (isset($temp[$offset])) {
            unset($temp[$offset]);
        }

        $this->exchangeArray(array_values($temp));

        $this->size = $this->count();
    }
}

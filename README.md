# php-set-data-structure
An implementation of a Java-like Set data structure for PHP. A Set allows storage of any values without duplicates.

Set objects are collections of values, you can iterate its elements in insertion order. A value in the Set may only occur once; it is unique in the Set's collection. 

Implementation is based on the [MDN JS Reference](https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Global_Objects/Set) for Sets in EMCA 6 JavaScript.

Sets require a min PHP version of 5.4.


## Initialization
A set instance can be created either as an object, or as a native-like function. When you create a set, you can insert intial values or keep it empty.
````php
// Function creation method
$set = set();
// Object creation method
$set = new Set( 1, 2, 3 );
````

Sets cannot contain duplicate values, and values are stored in insertion order.
```` php
$set = set( 1, 2, 1, 3, 2 );
// $set now contains [ 0 => 1, 1 => 2, 2 => 3 ]
````

## Adding values
New values are added to a set via the `add()` method. This method is chainable. Values can be of any type including Array and Object instances.

It is worth noting that uniqueness is on a **strict type** basis, so `(string) '1' !==  (int) 1 !==  (float) 1.0`. This also is true for Objects within the set and an object with a classA is not equal to an object with classB, even if the properties etc are the same.
```` php
// create empty Set
$set = set(); 

$set->add( 'a' );
// $set => [ 0 => 'a' ]

$set->add(1 )->add( 'a' );
// $set => [ 0 => 'a', 1 => 1 ]

$set->add( '1' );
// $set => [ 0 => 'a', 1 => 1, 2 => '1' ]
````
As this project aims to make Set feel like a native data structure, you can also add values as you would with a standard Array.
```` php
$set = set();

$set[] = 1;
// $set => [ 0 => 1 ]

// as values must be unique attempting to add a duplicate value fails
$set[] = 1;
// $set still => [ 0 => 1 ]

// you can also replace values by key, provided the new value is unique
$newSet = set( 'a', 'b' );
$newSet[0] = 2;
// $newSet => [ 0 => 2, 1 => 'b' ]

// If a key is not currently in the array, the value is appended to maintain insertion order
$newSet[4] => 'foo';
// $newSet => [ 0 => 2, 1 => 'b', 3 => 'foo' ]
````
## Removing values
Values can be removed individually via `delete()`, or all at once via the `clear()` method.
```` php
$set = set( 1, 2, 3 );

// delete by value
$set->delete( 2 );
// $set => [ 0 => 1, 1 => 3 ]

// You can also delete values by key
unset( $set[0] );
// $set => [ 0 => 3 ]

$set->clear();
// $set => []
````

## Testing if a value is present
Testing for values is one of the main reasons to use a Set. We can do this via the `has( $value)` method.

As with the other methods, this is strict type testing.

```` php
$set = set( 'a', [1,2] );

$set->add( 1.0 );

$set->has( 'a' ); // true
$set->has( [1,2] ); // true
$set->has( 1 ) // false
$set->has( [1,'2'] ); // false
$set->has( 'foo' ); // false
````

## Iteration
There are many ways to iterate a Set:
* Like a traditional PHP array
* Using `entries()` to return an instance of PHP's `ArrayIterator`
* Using `each()` and a provided callback function
* Using `values()` which returns a traditional PHP Array version of the Set

#### As a traditional Array
The Set object extends a PHP ArrayObject, and as such can be iterated like a normal array
```` php
$set = set( 1, 2 );

foreach ( $set as $val )
    print($val);
// prints 12
````

#### Using `entries()`
The `entries()` method returns an [ArrayIterator](http://php.net/manual/en/class.arrayiterator.php) object.
```` php
$set = set( 1, 2 );

$iterator = $set->entries();

while ( $iterator->valid() ) {
    echo $iterator->current();
    $iterator->next();
}
// prints 12
````

#### Using `each( $callback, ...$args )`
You can also iterate a Set via a callback function. The callback is called with the current value as parameter 1, with any additional specified params after.

```` php
$set = set( 1, 2 );

function cb( $value, $param)
{
  echo $value * $param;
}

$set->each( 'cb', 10 );
// prints 1020
````
The callback is called via [call_user_func_array](http://php.net/manual/en/function.call-user-func-array.php) so any context can be passed as a callback.
```` php
class callbackClass {
    private $values = [];

    public function cb ($a) {
        $this->values[] = $a;
    }
}

$class = new callbackClass;

$set->each( [$class, 'cb'] );

print_r($class);
/* prints callbackClass Object
(
    [values:callbackClass:private] => Array
        (
            [0] => 1
            [1] => 2
        )

)
*/
````

#### Using `values()`
The `values()` method returns a standard array representation of the Set. You can use this how you would with any other Array.
```` php
$set = set( 1, 2 );
$vals = $set->values();

foreach ( $vals as $val ){
  echo $val;
}
// prints 12
````

Contributions and changes welcome!

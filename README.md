[![Travis (.com)](https://img.shields.io/travis/com/jakewhiteley/php-sets?style=flat-square)](https://travis-ci.com/jakewhiteley/php-sets)

# php-set-data-structure
A PHP implementation of a Java-like Set data structure.

A set is simply a group of unique things that can be iterated by the order they were inserted. So, a significant characteristic of any set is that it does not contain duplicates.

Implementation is based on the [MDN JS Reference](https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Global_Objects/Set) for Sets in EMCA 6 JavaScript.

Sets require a min PHP version of 7.1.

* [Installation](#installation)
* [**Basic usage:**](#basic-usage)
    * [Creating a Set](#creating)
    * [Adding values](#adding-values)
    * [Removing values](#removing-values)
    * [Testing if a value is present](#testing-if-a-value-is-present)
    * [Counting items](#counting-items)
* [**Set Iteration**](#iteration)
    * [As a traditional Array](#as-a-traditional-array)
    * [Using `entries()`](#using-entries)
    * [Using `each`](#using-eachcallback-args)
    * [Using `values`](#using-values)
* [**Set operations**](#set-operations)
    * [Union](#union)
    * [Difference](#difference)
    * [Symmetric difference](#symmetric-difference)
    * [Intersect](#intersect)
    * [Subsets](#subsets)


## Installation
You can download the latest release via the releases link on this page.

PHP-Sets is available via [Composer](https://packagist.org/packages/jakewhiteley/php-sets) by running the following command:

````bash
composer require jakewhiteley/php-sets
````

then include the library in your project like so:
````php
include('vendor/autoload.php');

use PhpSets\Set;
````


## Basic Usage

#### Creating a Set

When you create a set, you can insert initial values or keep it empty.
````php
$set = new Set(1, 2, 3);
$emptySet = new Set();
````

Sets cannot contain duplicate values, and values are stored in insertion order.
````php
// $set contains [1, 2, 3] as duplicates are not stored
$set = new Set(1, 2, 1, 3, 2);
````

#### Adding values
Values of any type (Including Objects, arrays, and other `Sets`) are added to a set via the `add()` method.

It is worth noting that uniqueness is on a **strict type** basis, so `(string) '1' !==  (int) 1 !==  (float) 1.0`. This also is true for Objects within the set and an object with a classA is not equal to an object with classB, even if the properties etc are the same.
````php
// create empty Set
$set = new Set(); 

$set->add('a');
// $set => ['a']

$set->add(1)
    ->add('a');
// $set => ['a', 1]

$set->add('1');
// $set => ['a', 1, '1']
````

As `Sets` implements the `ArrayAccess` interface, you can also add values as you would with a standard Array.
````php
$set = new Set();

$set[] = 1;
$set[] = 'foo';
// $set => [1, 'foo']


// You can also replace values by key, provided the new value is unique within the Set
$set[0] = 2;
// $set => [2, 'foo']

// If a key is not currently in the array, the value is appended to maintain insertion order
$set[4] = 'foo';
// $newSet => [2, 'foo', 'foo']
````

#### Removing values
Values can be removed individually via `delete()`, or all at once via the `clear()` method.
````php
$set = new Set(1, 2, 3);

$set->delete(2);
// $set => [1, 3]

$set->clear();
// $set => []
````

You can also delete methods via `ArrayAccess`:
````php
$set = new Set(1, 2, 3);

unset($set[0]);
// $set => [2, 3]
````

#### Testing if a value is present
You can easily test if a `Set` contains a value via the `has($value)` method.

As with the other methods, this is a **strict type** test.

````php
$set = new Set('a', [1, 2], 1.0);

$set->has('a');      // true
$set->has([1, 2]);   // true
$set->has(1);        // false
$set->has([1, '2']); // false
$set->has('foo');    // false
````


#### Counting items
This is done using the `count` method:
````php
$set = new Set(1, 2, 3);

echo $set->count(); // 3
````




## Iteration
There are many ways to iterate a `Set`:
* Like a traditional PHP array
* Using `entries()` to return an instance of PHP's `ArrayIterator`
* Using `each()` and a provided callback function
* Using `values()` which returns a traditional PHP Array version of the Set

#### As a traditional Array
The Set object extends an `ArrayObject`, and can be iterated like a normal array:
````php
$set = new Set(1, 2);

foreach ($set as $val) {
    print($val);
}
````

#### Using `entries()`
The `entries()` method returns an [ArrayIterator](http://php.net/manual/en/class.arrayiterator.php) object.
````php
$iterator = $set->entries();

while ($iterator->valid()) {
    echo $iterator->current();
    $iterator->next();
}
````

#### Using `each($callback, ...$args)`
You can also iterate a `Set` via a provided [callable](https://www.php.net/manual/en/language.types.callable.php) method. 

The callback is called with the current item as parameter 1, with any additional specified params passed after.

````php
function cb($item, $parameter) {
  echo $item * $parameter;
}

$set = new Set(1, 2);

$set->each('cb', 10);
// prints 10 20
````


## Set operations

#### Union
Appends a second `Set` onto a given `Set` without creating duplicates:
````php
$a = new Set(1, 2, 3);
$b = new Set(2, 3, 4);

$merged = $a->union($b);

print_r($merged->values()); // [1, 2, 3, 4]
````

#### Difference
The `difference()` method will return a new `Set` containing values present in the original `Set` but not present in another. 

This is also known as the _relative complement_.
````php
$a = new Set(1, 2, 3, 4);
$b = new Set(3, 4, 5, 6);

print_r($a->difference($b)->values()); // [1, 2]
print_r($b->difference($a)->values()); // [5, 6]
````

#### Symmetric Difference
The `symmetricDifference()` method also returns a new `Set` but differs to the `difference` method in that it will return **all** uncommon values between both `Sets`.

````php
$a = new Set(1, 2, 3, 4);
$b = new Set(3, 4, 5, 6);

print_r($a->symmetricDifference($b)->values()); // [1, 2, 5, 6]
````

#### Intersect
Returns a new `Set` containing the items common (present in both) between two sets:
````php
$a = new Set(1, 2, 3);
$b = new Set(2, 3, 4);

$intersect = $a->intersect($b);

print_r($intersect->values()); // [2, 3]
````

#### Subsets
The `isSupersetOf` method returns a `bool` indicating if a given `Set` is a subset of the current `Set`.

The order of values does not matter, but a subset must only contain items present in the original `Set`:

````php
$a = new Set(1, 2, 3);
$b = new Set(2, 3);

var_Dump($b->isSupersetOf($a)); // true
var_Dump($a->isSupersetOf($b)); // false
````

### Contributing
Contributions and changes welcome! Just open an issue or submit a PR :muscle:

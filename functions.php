<?php

/**
 * Native-like helper functions for working with Sets
 */

/**
 * An additional function which allows a non OOP initialization of the Set object
 *
 * @param  mixed ...$args Values to initially add to the Set object
 * @return \PhpSets\Set
 */
function set() {
	return ( new \ReflectionClass( '\PhpSets\Set' ) )->newInstanceArgs( func_get_args() );
}

/**
 * Returns a new Set which contains the unique items of both sets
 *
 * @param  \PhpSets\Set $set           The original set
 * @param  \PhpSets\Set $additionalSet The set to append
 * @return \PhpSets\Set A new set containing the merged items
 */
function set_merge( \PhpSets\Set $set, \PhpSets\Set $additionalSet ) {
	$iterator = $additionalSet->entries();

	// create a copy of $set
	$merged = new \PhpSets\Set();
	$merged->exchangeArray( $set->values() );

	// add values from $additionalSet if not present
	while ( $iterator->valid() ) {
		if ( ! $set->has( $iterator->current() ) ) {
			$merged->add( $iterator->current() );
		}
		$iterator->next();
	}
	$iterator->rewind();

	return $merged;
}

/**
 * Returns a new Set object containing the common elements between two given sets
 *
 * @param  \PhpSets\Set $set
 * @param  \PhpSets\Set $additionalSet
 * @return \PhpSets\Set
 */
function set_intersect( \PhpSets\Set $set, \PhpSets\Set $additionalSet ) {
	$iterator = $additionalSet->entries();
	$intersect = new \PhpSets\Set;

	while ( $iterator->valid() ) {
		if ( $set->has( $iterator->current() ) ) {
			$intersect->add( $iterator->current() );
		}
		$iterator->next();
	}
	$iterator->rewind();

	return $intersect;
}

/**
 * Returns a new Set containing all uncommon items between the two given Sets
 *
 * @todo  This is not very efficient as it iterates both Sets completely
 *
 * @param  \PhpSets\Set $set
 * @param  \PhpSets\Set $additionalSet
 * @return \PhpSets\Set
 */
function set_diff( \PhpSets\Set $set, \PhpSets\Set $additionalSet ) {
	$originalArray = $set->entries();
	$iterator = $additionalSet->entries();
	$intersect = new \PhpSets\Set;

	// check $set values
	while ( $originalArray->valid() ) {
		if ( ! $additionalSet->has( $originalArray->current() ) ) {
			$intersect->add( $originalArray->current() );
		}
		$originalArray->next();
	}

	// check $additionalSet values
	while ( $iterator->valid() ) {
		if ( ! $set->has( $iterator->current() ) ) {
			$intersect->add( $iterator->current() );
		}
		$iterator->next();
	}
	// reset both Set internal pointers
	$originalArray->rewind();
	$iterator->rewind();

	return $intersect;
}


/**
 * Checks if a given $additionalSet is a subset of $set
 * All values should be present, but ordinality does not matter
 *
 * @param  \PhpSets\Set $set           The original Set to check against
 * @param  \PhpSets\Set $additionalSet The subset
 * @return bool Whether $additionalSet was a subset of $set
 */
function set_subset( \PhpSets\Set $set, \PhpSets\Set $additionalSet ) {
	$iterator = $additionalSet->entries();

	// iterate through $additionalSet and return false is an uncommon value is present
	while ( $iterator->valid() ) {
		if ( ! $set->has( $iterator->current() ) ) {
			return false;
		}
		$iterator->next();
	}
	$iterator->rewind();

	return true;
}

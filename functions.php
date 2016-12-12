<?php

/**
 * An additional function which allows a non OOP initialization of the Set object
 *
 * @param  mixed ...$args Values to initially add to the Set object
 */
function set() 
{
    return ( new \ReflectionClass('\JakeWhiteley\PhpSets\Set') )->newInstanceArgs( func_get_args() );
}

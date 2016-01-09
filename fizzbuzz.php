<?php

require 'vendor/autoload.php';

// (integer d, Functor s, MonadPlus s) => d -> a -> d -> s a
function calc(...$args){
    return f(function($d, $s, $n){
        return fconst($s, guard($n % $d === 0));
    }, ...$args);
}

/*
$tmp = f(function($n){
    return fromMaybe($n, mappend(calc(3, "Fizz"), calc(5, "Buzz"), $n));
});
var_dump($tmp(9));
var_dump($tmp(10));
var_dump($tmp(11));
var_dump($tmp(15));

var_dump(fmap($tmp, range(1, 100)));
*/

$fizzbuzz = fromMaybe()->ap(mappend(calc(3, "Fizz"), calc(5, "Buzz")));

/*
var_dump($fizzbuzz(9));
var_dump($fizzbuzz(10));
var_dump($fizzbuzz(11));
var_dump($fizzbuzz(15));

var_dump(fmap($fizzbuzz, range(1, 100)));
*/

function pr(...$args){
    return f(function($a){
        echo $a, "\n";
        return $a;
    }, ...$args);
}

$ret = fmap(pr()->compose($fizzbuzz), range(1, 100));

echo "\n\n ======== Done.\n\n";

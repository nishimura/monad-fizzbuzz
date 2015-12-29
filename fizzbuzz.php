<?php

function prompt($msg){
    echo "\n\n ======== $msg [y/n]: ";
    $line = strtolower(trim(fgets(STDIN)));
    if ($line[0] === 'y')
        return true;
    else if ($line[0] === 'n')
        return false;
    return prompt($msg);
}

require 'vendor/autoload.php';
Laiz\Monad\Func::importUtil();

/*
 * Section: 1
 */
use Laiz\Monad\Func;
use Laiz\Monad\Maybe;
use Laiz\Monad\MonadList;

function ＜＄(...$args){
    $ret = fmap()->compose(cnst());
    if ($args) $ret = $ret(...$args);
    return $ret;
}

var_dump(＜＄(1, Just(0)));
var_dump(＜＄(1, Nothing()));
var_dump(＜＄(1, MonadList::cast([1,2,3,4,5])));

if (!prompt('Section 1 is Finished. Continue?')) return;

/*
 * Section: 2
 */

function ｜(...$args){
    return f(function($bool){
        if ($bool) return Just(0);
        else return Nothing();
    }, ...$args);
}

function 〜＞(...$args){
    return f(function($d, $s, $n){
        return ＜＄($s, ｜($n % $d === 0));
    }, ...$args);
}

var_dump(〜＞(3, "Fizz", 5));
var_dump(〜＞(3, "Fizz", 6));

if (!prompt('Section 2 is Finished. Continue?')) return;

/*
 * Section: 3
 */

function mappend(...$args){
    return f(function($m1, $m2){
        // Monoid String
        if (is_string($m1) && is_string($m2)){
            return $m1 . $m2;
        }

        // Monoid b => Monoid (a -> b)
        if ($m1 instanceof Func &&
            $m2 instanceof Func){
            return f(function($a) use ($m1, $m2){
                return mappend($m1($a), $m2($a));
            });
        }

        // Monoid a => Monoid (Maybe a)
        if ($m1 instanceof Maybe &&
            $m2 instanceof Maybe){
            if ($m1 == Nothing()){
                return $m2;
            }else if ($m2 == Nothing()){
                return $m1;
            }else{
                return $m1->bind(function($v1) use ($m2){
                    return $m2->fmap(function($v2) use ($v1){
                        return mappend($v1, $v2);
                    });
                });
            }
        }
    }, ...$args);
}
function ＜＞(...$args){
    return mappend(...$args);
}

var_dump(＜＞(Just('Fizz'), Just('Buzz')));
var_dump(＜＞(Just('Fizz'), Nothing()));
var_dump(＜＞(Nothing(), Just('Buzz')));
var_dump(＜＞(Nothing(), Nothing()));

var_dump(＜＞(〜＞(3, "Fizz"), 〜＞(5, "Buzz"), 9));
var_dump(＜＞(〜＞(3, "Fizz"), 〜＞(5, "Buzz"), 10));
var_dump(＜＞(〜＞(3, "Fizz"), 〜＞(5, "Buzz"), 15));
var_dump(＜＞(〜＞(3, "Fizz"), 〜＞(5, "Buzz"), 11));

if (!prompt('Section 3 is Finished. Continue?')) return;

/*
 * Section: 4
 */

$tmp = f(function($n){
    return fromMaybe($n, ＜＞(〜＞(3, "Fizz"), 〜＞(5, "Buzz"), $n));
});
var_dump($tmp(9));
var_dump($tmp(10));
var_dump($tmp(11));
var_dump($tmp(15));
var_dump(MonadList::cast(range(1, 100))->map($tmp));

if (!prompt('Section 4 is Finished. Continue?')) return;

/*
 * Section: 5
 */

$fizzbuzz = fromMaybe()->ap(＜＞(〜＞(3, "Fizz"), 〜＞(5, "Buzz")));

var_dump($fizzbuzz(9));
var_dump($fizzbuzz(10));
var_dump($fizzbuzz(11));
var_dump($fizzbuzz(15));

var_dump(MonadList::cast(range(1, 100))->map($fizzbuzz));

function pr(...$args){
    return f(function($a){
        echo $a, "\n";
        return $a;
    }, ...$args);
}

MonadList::cast(range(1, 100))->fmap(pr()->compose($fizzbuzz));

echo "\n\n ======== Done.\n\n";

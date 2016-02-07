Recursor
========

Recursor enabled execution of recursive algorithms in an iterative manner by utiliting PHP's generator feature.
This is particularly useful, because there may be algorithms that are easy to implement recursively but hard
iteratively. Also, PHP imposes an artificial nesting limit on recursion.

To transform a recursive function into a quasi-recursive one using Recursor, basically the only work that is needed is
to replace `return` keywords with `yield` and recusrive function calls should also be prefixed with `yield`.

An example that will generate the nth Fibonacci-number:

    $fibonacci = function ($x) use (&$fibonacci) {
        if ($x === 0) {
            yield 0;
        } else if ($x === 1) {
            yield 1;
        } else {
            $x1 = (yield $fibonacci($x - 1));   //retrieves return value of recursive call
            $x2 = (yield $fibonacci($x - 2));
            yield $x1 + $x2;                    //yielding a non-generator acts as a return
        }
    };

    $wrapped = new Recursor($fibonacci);

    $wrapped(5); // returns 5
    $wrapped(6); // returns 8

Notes
--------

 * Recursor is built for PHP 5.5. This means that PHP7's generator-return is not supported. As a workaround, functions will
   "return" the first non-generator value it yields and because of this, they will not be able to generate sequences.
 * If you wish to yield a generator that should not be executed, you can wrap it in `\IteratorIterator` or a custom wrapper.

Downsides
--------

Recursor relies heavily on generators. Each recursive call instantiates and executes a generator, which has a certain
CPU and memory overhead. Because of this, relying on Recurson in performance-sensitive applications is not recommended.
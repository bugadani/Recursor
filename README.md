Recursor
========

Recursor enabled execution of recursive algorithms in an iterative manner by utilising PHP's generator feature.
This is particularly useful, because there may be algorithms that are easy to implement recursively but hard
iteratively. Also, PHP imposes an artificial nesting limit on recursion.

To transform a recursive function into a quasi-recursive one using Recursor, basically the only work that is needed is
to prefix recursive function calls with `yield`.

An example that will generate the nth Fibonacci-number:

    $fibonacci = function ($x) use (&$fibonacci) {
        if ($x === 0) {
            return 0;
        } else if ($x === 1) {
            return 1;
        } else {
            $x1 = (yield $fibonacci($x - 1));   //retrieves return value of recursive call
            $x2 = (yield $fibonacci($x - 2));
            return $x1 + $x2;
        }
    };

    $wrapped = new Recursor($fibonacci);

    $wrapped(5); // returns 5
    $wrapped(6); // returns 8

Downsides
--------

Recursor relies heavily on generators. Each recursive call instantiates and executes a generator, which has a certain
CPU and memory overhead. Also, the actual executor function is quite complicated which imposes even more overhead.
Because of this, relying on Recurson in performance-sensitive applications is not recommended and an iterative solution
should be implemented.
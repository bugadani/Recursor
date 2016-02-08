<?php

namespace Recursor;

/**
 * This class allows recursive functions to be executed iteratively by using generators.
 * This is especially useful in PHP because the language imposes an artificial nesting limit.
 *
 * In order for a function to be executed, each recursive call must be yielded.
 * The last yield may be a scalar value; that value will be returned by this method.
 *
 * If a generator is to be yielded but should not be executed, it should be wrapped in \IteratorIterator.
 *
 * @param \Generator $generator
 *
 * @return mixed
 */

class Recursor
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    private function execute(\Generator $generator)
    {
        $stack = [];

        //This is basically a simple iterative in-order tree traversal algorithm
        $yielded = $generator->current();

        //This is a depth-first traversal
        while ($yielded instanceof \Generator) {
            //... push it to the stack
            $stack[] = $generator;

            $generator = $yielded;
            $yielded   = $generator->current();
        }

        while (!empty($stack)) {
            //We've reached the end of the branch, let's step back on the stack
            $generator = array_pop($stack);

            //step the generator
            $yielded = $generator->send($yielded);

            //Depth-first traversal
            while ($yielded instanceof \Generator) {
                //... push it to the stack
                $stack[] = $generator;

                $generator = $yielded;
                $yielded   = $generator->current();
            }
        }

        return $yielded;
    }

    public function __invoke()
    {
        $generator = call_user_func_array($this->callback, func_get_args());

        return $this->execute($generator);
    }
}
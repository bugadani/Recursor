<?php

namespace Recursor;

/**
 * This class allows recursive functions to be executed iteratively by using generators.
 * This is especially useful in PHP because the language imposes an artificial nesting limit.
 *
 * In order for a function to be executed, each recursive call must be yielded.
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
    private $quasiRecursiveFunction;
    private $stack = [];

    public function __construct(callable $callback)
    {
        $this->quasiRecursiveFunction = $callback;
    }

    private function execute(\Generator $generator)
    {
        $yielded = $this->traverseDepth($generator);

        while (!empty($this->stack)) {
            //We've reached the end of the branch, let's step back on the stack
            $generator = array_pop($this->stack);

            //step the generator
            $generator->send($yielded);
            $yielded = $this->traverseDepth($generator);
        }

        return $yielded;
    }

    /**
     * @param \Generator $generator
     * @return mixed The return value of the deepest generator
     * @internal param $stack
     */
    private function traverseDepth(\Generator $generator)
    {
        $yielded = $generator->current();
        while ($yielded instanceof \Generator) {
            //... push it to the stack
            $this->stack[] = $generator;

            $generator = $yielded;
            $yielded   = $generator->current();
        }
        if ($generator->valid()) {
            array_push($this->stack, $generator);
        } else {
            $yielded = $generator->getReturn();
        }

        return $yielded;
    }

    public function __invoke(...$args)
    {
        $callback = $this->quasiRecursiveFunction;

        $generator = $callback(...$args);

        if (!$generator instanceof \Generator) {
            return $generator;
        }

        return $this->execute($generator);
    }
}
<?php

namespace Recursor\Test;

use Recursor\Recursor;

class RecursorTest extends \PHPUnit_Framework_TestCase
{
    public function testExecution()
    {
        $fibonacci = function ($x) use (&$fibonacci) {
            if ($x === 0) {
                yield 0;
            } else if ($x === 1) {
                yield 1;
            } else {
                $x1 = (yield $fibonacci($x - 1));
                $x2 = (yield $fibonacci($x - 2));
                yield $x1 + $x2;
            }
        };

        $wrapped = new Recursor($fibonacci);

        $this->assertEquals(8, $wrapped(6));
    }

    public function testTreeIteration()
    {
        $traverse = function ($x) use (&$traverse) {
            if (is_array($x)) {
                $string = '';
                foreach ($x as $y) {
                    $string .= (yield $traverse($y));
                }
                yield $string;

            } else {
                yield $x;
            }
        };

        $wrapped = new Recursor($traverse);

        $tree = [
            [
                'a',
                'b',
                [
                    'c',
                    [
                        'd',
                        'e'
                    ],
                    'f'
                ]
            ]
        ];

        $this->assertEquals('abcdef', $wrapped($tree));
    }
}

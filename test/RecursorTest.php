<?php

namespace Recursor\Test;

use Recursor\Recursor;

class RecursorTest extends \PHPUnit_Framework_TestCase
{
    public function testExecution()
    {
        $fibonacci = function ($x) use (&$fibonacci) {
            if ($x === 0) {
                return 0;
            } else if ($x === 1) {
                return 1;
            } else {
                $x1 = (yield $fibonacci($x - 1));
                $x2 = (yield $fibonacci($x - 2));
                return $x1 + $x2;
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
                return $string;

            } else {
                return $x;
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

    public function testScalarValueIsSentBack()
    {
        $function = function () use (&$function) {
            if (yield true) {
                return true;
            } else {
                return false;
            }
        };

        $wrapped = new Recursor($function);

        $this->assertTrue($wrapped());
    }
}

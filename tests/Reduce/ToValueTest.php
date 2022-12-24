<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToValueTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         toValue array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        callable $reducer
     * @param        mixed $defaultCarry
     * @param        mixed $expected
     */
    public function testArray(array $data, callable $reducer, $defaultCarry, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toValue($data, $reducer, $defaultCarry);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        $sum = static function ($carry, $datum) {
            return $carry + $datum;
        };
        $concat = static function ($carry, $datum) {
            return $carry . $datum;
        };

        return [
            //  data            reducer     carry   expected
            [   [],             $sum,       null,   null    ],
            [   [],             $sum,       0,      0       ],
            [   [],             $sum,       1,      1       ],
            [   [],             $sum,       'a',    'a'     ],
            [   [0],            $sum,       null,   0       ],
            [   [null],         $sum,       null,   0       ],
            [   [null, 1, 2],   $sum,       null,   3       ],
            [   [1, 2, 3],      $sum,       null,   6       ],
            [   [],             $concat,    null,   null    ],
            [   [],             $concat,    0,      0       ],
            [   [],             $concat,    1,      1       ],
            [   [],             $concat,    'a',    'a'     ],
            [   [0],            $concat,    null,   '0'     ],
            [   [null],         $concat,    null,   ''      ],
            [   ['a', 'b'],     $concat,    null,   'ab'    ],
            [   [null, 2, 3],   $concat,    null,   '23',   ],
        ];
    }

    /**
     * @test         toValue generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        callable $reducer
     * @param        mixed $defaultCarry
     * @param        mixed $expected
     */
    public function testGenerators(\Generator $data, callable $reducer, $defaultCarry, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toValue($data, $reducer, $defaultCarry);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $sum = static function ($carry, $datum) {
            return $carry + $datum;
        };
        $concat = static function ($carry, $datum) {
            return $carry . $datum;
        };

        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            //  data                reducer     carry   expected
            [   $gen([]),           $sum,       null,   null    ],
            [   $gen([]),           $sum,       0,      0       ],
            [   $gen([]),           $sum,       1,      1       ],
            [   $gen([]),           $sum,       'a',    'a'     ],
            [   $gen([0]),          $sum,       null,   0       ],
            [   $gen([null]),       $sum,       null,   0       ],
            [   $gen([null, 1, 2]), $sum,       null,   3       ],
            [   $gen([1, 2, 3]),    $sum,       null,   6       ],
            [   $gen([]),           $concat,    null,   null    ],
            [   $gen([]),           $concat,    0,      0       ],
            [   $gen([]),           $concat,    1,      1       ],
            [   $gen([]),           $concat,    'a',    'a'     ],
            [   $gen([0]),          $concat,    null,   '0'     ],
            [   $gen([null]),       $concat,    null,   ''      ],
            [   $gen(['a', 'b']),   $concat,    null,   'ab'    ],
            [   $gen([null, 2, 3]), $concat,    null,   '23',   ],
        ];
    }

    /**
     * @test         toValue iterators
     * @dataProvider dataProviderForIterators
     * @param        \Generator $data
     * @param        callable $reducer
     * @param        mixed $defaultCarry
     * @param        mixed $expected
     */
    public function testIterators(\Iterator $data, callable $reducer, $defaultCarry, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toValue($data, $reducer, $defaultCarry);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $sum = static function ($carry, $datum) {
            return $carry + $datum;
        };
        $concat = static function ($carry, $datum) {
            return $carry . $datum;
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            //  data                 reducer     carry   expected
            [   $iter([]),           $sum,       null,   null    ],
            [   $iter([]),           $sum,       0,      0       ],
            [   $iter([]),           $sum,       1,      1       ],
            [   $iter([]),           $sum,       'a',    'a'     ],
            [   $iter([0]),          $sum,       null,   0       ],
            [   $iter([null]),       $sum,       null,   0       ],
            [   $iter([null, 1, 2]), $sum,       null,   3       ],
            [   $iter([1, 2, 3]),    $sum,       null,   6       ],
            [   $iter([]),           $concat,    null,   null    ],
            [   $iter([]),           $concat,    0,      0       ],
            [   $iter([]),           $concat,    1,      1       ],
            [   $iter([]),           $concat,    'a',    'a'     ],
            [   $iter([0]),          $concat,    null,   '0'     ],
            [   $iter([null]),       $concat,    null,   ''      ],
            [   $iter(['a', 'b']),   $concat,    null,   'ab'    ],
            [   $iter([null, 2, 3]), $concat,    null,   '23',   ],
        ];
    }

    /**
     * @test         toValue traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        callable $reducer
     * @param        mixed $defaultCarry
     * @param        mixed $expected
     */
    public function testTraversables(\Traversable $data, callable $reducer, $defaultCarry, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toValue($data, $reducer, $defaultCarry);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $sum = static function ($carry, $datum) {
            return $carry + $datum;
        };
        $concat = static function ($carry, $datum) {
            return $carry . $datum;
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            //  data                 reducer     carry   expected
            [   $trav([]),           $sum,       null,   null    ],
            [   $trav([]),           $sum,       0,      0       ],
            [   $trav([]),           $sum,       1,      1       ],
            [   $trav([]),           $sum,       'a',    'a'     ],
            [   $trav([0]),          $sum,       null,   0       ],
            [   $trav([null]),       $sum,       null,   0       ],
            [   $trav([null, 1, 2]), $sum,       null,   3       ],
            [   $trav([1, 2, 3]),    $sum,       null,   6       ],
            [   $trav([]),           $concat,    null,   null    ],
            [   $trav([]),           $concat,    0,      0       ],
            [   $trav([]),           $concat,    1,      1       ],
            [   $trav([]),           $concat,    'a',    'a'     ],
            [   $trav([0]),          $concat,    null,   '0'     ],
            [   $trav([null]),       $concat,    null,   ''      ],
            [   $trav(['a', 'b']),   $concat,    null,   'ab'    ],
            [   $trav([null, 2, 3]), $concat,    null,   '23',   ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToMaxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         toMax array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        int|float $expected
     */
    public function testArray(array $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toMax($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            //  data                    expected
            [   [],                     null   ],
            [   [0],                    0      ],
            [   [INF],                  INF    ],
            [   [-INF],                 -INF   ],
            [   [INF, -INF],            INF    ],
            [   [INF, -INF, 10, -1],    INF    ],
            [   [1, 2, 3],              3      ],
            [   [3, 2, 1],              3      ],
            [   [2, 3, 1],              3      ],
            [   [1, 2.1],               2.1    ],
            [   [2.1, 1],               2.1    ],
            [   [2, 1.1],               2      ],
            [   [2.2, 1.1],             2.2    ],
            [   [1.1, 2.2],             2.2    ],
            [   ['a', 'b', 'c'],        'c'    ],
            [   ['b', 'c', 'a'],        'c'    ],
            [   ['c', 'b', 'a'],        'c'    ],
            [   ['ab', 'ba', 'b'],      'ba'   ],
            [   ['ba', 'b', 'ab'],      'ba'   ],
            [   [[]],                   []     ],
            [   [[2]],                  [2]    ],
            [   [[], []],               []     ],
            [   [[], [2]],              [2]    ],
            [   [[2], []],              [2]    ],
            [   [[], [null]],           [null] ],
            [   [[null], []],           [null] ],
            [   [[null], [null]],       [null] ],
            [   [[1, 2], [2]],          [1, 2] ],
            [   [[3, 2], [2]],          [3, 2] ],
            [   [[1, 2], [2, 1]],       [2, 1] ],
            [   [[2, 1], [1, 2]],       [2, 1] ],
            [   [['a'], ['b']],         ['b']  ],
            [   [['a', 'a'], ['b']],    ['a', 'a']  ],
        ];
    }

    /**
     * @test         toMax generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testGenerators(\Generator $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toMax($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            //  data                          expected
            [  $gen([]),                     null   ],
            [  $gen([0]),                    0      ],
            [  $gen([INF]),                  INF    ],
            [  $gen([-INF]),                 -INF   ],
            [  $gen([INF, -INF]),            INF    ],
            [  $gen([INF, -INF, 10, -1]),    INF    ],
            [  $gen([1, 2, 3]),              3      ],
            [  $gen([3, 2, 1]),              3      ],
            [  $gen([2, 3, 1]),              3      ],
            [  $gen([1, 2.1]),               2.1    ],
            [  $gen([2.1, 1]),               2.1    ],
            [  $gen([2, 1.1]),               2      ],
            [  $gen([2.2, 1.1]),             2.2    ],
            [  $gen([1.1, 2.2]),             2.2    ],
            [  $gen(['a', 'b', 'c']),        'c'    ],
            [  $gen(['b', 'c', 'a']),        'c'    ],
            [  $gen(['c', 'b', 'a']),        'c'    ],
            [  $gen(['ab', 'ba', 'b']),      'ba'   ],
            [  $gen(['ba', 'b', 'ab']),      'ba'   ],
            [  $gen([[]]),                   []     ],
            [  $gen([[2]]),                  [2]    ],
            [  $gen([[], []]),               []     ],
            [  $gen([[], [2]]),              [2]    ],
            [  $gen([[2], []]),              [2]    ],
            [  $gen([[], [null]]),           [null] ],
            [  $gen([[null], []]),           [null] ],
            [  $gen([[null], [null]]),       [null] ],
            [  $gen([[1, 2], [2]]),          [1, 2] ],
            [  $gen([[3, 2], [2]]),          [3, 2] ],
            [  $gen([[1, 2], [2, 1]]),       [2, 1] ],
            [  $gen([[2, 1], [1, 2]]),       [2, 1] ],
            [  $gen([['a'], ['b']]),         ['b']  ],
            [  $gen([['a', 'a'], ['b']]),    ['a', 'a']  ],
        ];
    }

    /**
     * @test         toMax iterators
     * @dataProvider dataProviderForIterators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testIterators(\Iterator $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toMax($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            //  data                          expected
            [  $iter([]),                     null   ],
            [  $iter([0]),                    0      ],
            [  $iter([INF]),                  INF    ],
            [  $iter([-INF]),                 -INF   ],
            [  $iter([INF, -INF]),            INF    ],
            [  $iter([INF, -INF, 10, -1]),    INF    ],
            [  $iter([1, 2, 3]),              3      ],
            [  $iter([3, 2, 1]),              3      ],
            [  $iter([2, 3, 1]),              3      ],
            [  $iter([1, 2.1]),               2.1    ],
            [  $iter([2.1, 1]),               2.1    ],
            [  $iter([2, 1.1]),               2      ],
            [  $iter([2.2, 1.1]),             2.2    ],
            [  $iter([1.1, 2.2]),             2.2    ],
            [  $iter(['a', 'b', 'c']),        'c'    ],
            [  $iter(['b', 'c', 'a']),        'c'    ],
            [  $iter(['c', 'b', 'a']),        'c'    ],
            [  $iter(['ab', 'ba', 'b']),      'ba'   ],
            [  $iter(['ba', 'b', 'ab']),      'ba'   ],
            [  $iter([[]]),                   []     ],
            [  $iter([[2]]),                  [2]    ],
            [  $iter([[], []]),               []     ],
            [  $iter([[], [2]]),              [2]    ],
            [  $iter([[2], []]),              [2]    ],
            [  $iter([[], [null]]),           [null] ],
            [  $iter([[null], []]),           [null] ],
            [  $iter([[null], [null]]),       [null] ],
            [  $iter([[1, 2], [2]]),          [1, 2] ],
            [  $iter([[3, 2], [2]]),          [3, 2] ],
            [  $iter([[1, 2], [2, 1]]),       [2, 1] ],
            [  $iter([[2, 1], [1, 2]]),       [2, 1] ],
            [  $iter([['a'], ['b']]),         ['b']  ],
            [  $iter([['a', 'a'], ['b']]),    ['a', 'a']  ],
        ];
    }

    /**
     * @test         toMax traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        mixed $expected
     */
    public function testTraversables(\Traversable $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toMax($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            //  data                          expected
            [  $trav([]),                     null   ],
            [  $trav([0]),                    0      ],
            [  $trav([INF]),                  INF    ],
            [  $trav([-INF]),                 -INF   ],
            [  $trav([INF, -INF]),            INF    ],
            [  $trav([INF, -INF, 10, -1]),    INF    ],
            [  $trav([1, 2, 3]),              3      ],
            [  $trav([3, 2, 1]),              3      ],
            [  $trav([2, 3, 1]),              3      ],
            [  $trav([1, 2.1]),               2.1    ],
            [  $trav([2.1, 1]),               2.1    ],
            [  $trav([2, 1.1]),               2      ],
            [  $trav([2.2, 1.1]),             2.2    ],
            [  $trav([1.1, 2.2]),             2.2    ],
            [  $trav(['a', 'b', 'c']),        'c'    ],
            [  $trav(['b', 'c', 'a']),        'c'    ],
            [  $trav(['c', 'b', 'a']),        'c'    ],
            [  $trav(['ab', 'ba', 'b']),      'ba'   ],
            [  $trav(['ba', 'b', 'ab']),      'ba'   ],
            [  $trav([[]]),                   []     ],
            [  $trav([[2]]),                  [2]    ],
            [  $trav([[], []]),               []     ],
            [  $trav([[], [2]]),              [2]    ],
            [  $trav([[2], []]),              [2]    ],
            [  $trav([[], [null]]),           [null] ],
            [  $trav([[null], []]),           [null] ],
            [  $trav([[null], [null]]),       [null] ],
            [  $trav([[1, 2], [2]]),          [1, 2] ],
            [  $trav([[3, 2], [2]]),          [3, 2] ],
            [  $trav([[1, 2], [2, 1]]),       [2, 1] ],
            [  $trav([[2, 1], [1, 2]]),       [2, 1] ],
            [  $trav([['a'], ['b']]),         ['b']  ],
            [  $trav([['a', 'a'], ['b']]),    ['a', 'a']  ],
        ];
    }
}

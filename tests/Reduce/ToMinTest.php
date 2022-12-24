<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToMinTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         toMin array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        int|float $expected
     */
    public function testArray(array $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toMin($data);

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
            [   [INF, -INF],            -INF   ],
            [   [INF, -INF, 10, -1],    -INF   ],
            [   [1, 2, 3],              1      ],
            [   [3, 2, 1],              1      ],
            [   [3, 2, 1],              1      ],
            [   [2.1, 1],               1      ],
            [   [2, 1.1],               1.1    ],
            [   [2.2, 1.1],             1.1    ],
            [   [1.1, 2.2],             1.1    ],
            [   ['a', 'b', 'c'],        'a'    ],
            [   ['b', 'c', 'a'],        'a'    ],
            [   ['c', 'b', 'a'],        'a'    ],
            [   ['ab', 'ba', 'b'],      'ab'   ],
            [   ['ba', 'b', 'ab'],      'ab'   ],
            [   [[]],                   []     ],
            [   [[2]],                  [2]    ],
            [   [[], []],               []     ],
            [   [[], [2]],              []     ],
            [   [[2], []],              []     ],
            [   [[], [null]],           []     ],
            [   [[null], []],           []     ],
            [   [[null], [null]],       [null] ],
            [   [[1, 2], [2]],          [2]    ],
            [   [[3, 2], [2]],          [2]    ],
            [   [[1, 2], [2, 1]],       [1, 2] ],
            [   [[2, 1], [1, 2]],       [1, 2] ],
            [   [['a'], ['b']],         ['a']  ],
            [   [['a', 'a'], ['b']],    ['b']  ],
        ];
    }

    /**
     * @test         toMin generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testGenerators(\Generator $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toMin($data);

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
            [   $gen([]),                     null   ],
            [   $gen([0]),                    0      ],
            [   $gen([INF]),                  INF    ],
            [   $gen([-INF]),                 -INF   ],
            [   $gen([INF, -INF]),            -INF   ],
            [   $gen([INF, -INF, 10, -1]),    -INF   ],
            [   $gen([1, 2, 3]),              1      ],
            [   $gen([3, 2, 1]),              1      ],
            [   $gen([2, 1, 3]),              1      ],
            [   $gen([2.1, 1]),               1      ],
            [   $gen([2, 1.1]),               1.1    ],
            [   $gen([2.2, 1.1]),             1.1    ],
            [   $gen([1.1, 2.2]),             1.1    ],
            [   $gen(['a', 'b', 'c']),        'a'    ],
            [   $gen(['b', 'c', 'a']),        'a'    ],
            [   $gen(['c', 'b', 'a']),        'a'    ],
            [   $gen(['ab', 'ba', 'b']),      'ab'   ],
            [   $gen(['ba', 'b', 'ab']),      'ab'   ],
            [   $gen([[]]),                   []     ],
            [   $gen([[2]]),                  [2]    ],
            [   $gen([[], []]),               []     ],
            [   $gen([[], [2]]),              []     ],
            [   $gen([[2], []]),              []     ],
            [   $gen([[], [null]]),           []     ],
            [   $gen([[null], []]),           []     ],
            [   $gen([[null], [null]]),       [null] ],
            [   $gen([[1, 2], [2]]),          [2]    ],
            [   $gen([[3, 2], [2]]),          [2]    ],
            [   $gen([[1, 2], [2, 1]]),       [1, 2] ],
            [   $gen([[2, 1], [1, 2]]),       [1, 2] ],
            [   $gen([['a'], ['b']]),         ['a']  ],
            [   $gen([['a', 'a'], ['b']]),    ['b']  ],
        ];
    }

    /**
     * @test         toMin iterators
     * @dataProvider dataProviderForIterators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testIterators(\Iterator $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toMin($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            //  data                           expected
            [   $iter([]),                     null   ],
            [   $iter([0]),                    0      ],
            [   $iter([INF]),                  INF    ],
            [   $iter([-INF]),                 -INF   ],
            [   $iter([INF, -INF]),            -INF   ],
            [   $iter([INF, -INF, 10, -1]),    -INF   ],
            [   $iter([1, 2, 3]),              1      ],
            [   $iter([3, 2, 1]),              1      ],
            [   $iter([2, 1, 3]),              1      ],
            [   $iter([2.1, 1]),               1      ],
            [   $iter([2, 1.1]),               1.1    ],
            [   $iter([2.2, 1.1]),             1.1    ],
            [   $iter([1.1, 2.2]),             1.1    ],
            [   $iter(['a', 'b', 'c']),        'a'    ],
            [   $iter(['b', 'c', 'a']),        'a'    ],
            [   $iter(['c', 'b', 'a']),        'a'    ],
            [   $iter(['ab', 'ba', 'b']),      'ab'   ],
            [   $iter(['ba', 'b', 'ab']),      'ab'   ],
            [   $iter([[]]),                   []     ],
            [   $iter([[2]]),                  [2]    ],
            [   $iter([[], []]),               []     ],
            [   $iter([[], [2]]),              []     ],
            [   $iter([[2], []]),              []     ],
            [   $iter([[], [null]]),           []     ],
            [   $iter([[null], []]),           []     ],
            [   $iter([[null], [null]]),       [null] ],
            [   $iter([[1, 2], [2]]),          [2]    ],
            [   $iter([[3, 2], [2]]),          [2]    ],
            [   $iter([[1, 2], [2, 1]]),       [1, 2] ],
            [   $iter([[2, 1], [1, 2]]),       [1, 2] ],
            [   $iter([['a'], ['b']]),         ['a']  ],
            [   $iter([['a', 'a'], ['b']]),    ['b']  ],
        ];
    }

    /**
     * @test         toMin traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        mixed $expected
     */
    public function testTraversables(\Traversable $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::toMin($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            //  data                           expected
            [   $trav([]),                     null   ],
            [   $trav([0]),                    0      ],
            [   $trav([INF]),                  INF    ],
            [   $trav([-INF]),                 -INF   ],
            [   $trav([INF, -INF]),            -INF   ],
            [   $trav([INF, -INF, 10, -1]),    -INF   ],
            [   $trav([1, 2, 3]),              1      ],
            [   $trav([3, 2, 1]),              1      ],
            [   $trav([2, 1, 3]),              1      ],
            [   $trav([2.1, 1]),               1      ],
            [   $trav([2, 1.1]),               1.1    ],
            [   $trav([2.2, 1.1]),             1.1    ],
            [   $trav([1.1, 2.2]),             1.1    ],
            [   $trav(['a', 'b', 'c']),        'a'    ],
            [   $trav(['b', 'c', 'a']),        'a'    ],
            [   $trav(['c', 'b', 'a']),        'a'    ],
            [   $trav(['ab', 'ba', 'b']),      'ab'   ],
            [   $trav(['ba', 'b', 'ab']),      'ab'   ],
            [   $trav([[]]),                   []     ],
            [   $trav([[2]]),                  [2]    ],
            [   $trav([[], []]),               []     ],
            [   $trav([[], [2]]),              []     ],
            [   $trav([[2], []]),              []     ],
            [   $trav([[], [null]]),           []     ],
            [   $trav([[null], []]),           []     ],
            [   $trav([[null], [null]]),       [null] ],
            [   $trav([[1, 2], [2]]),          [2]    ],
            [   $trav([[3, 2], [2]]),          [2]    ],
            [   $trav([[1, 2], [2, 1]]),       [1, 2] ],
            [   $trav([[2, 1], [1, 2]]),       [1, 2] ],
            [   $trav([['a'], ['b']]),         ['a']  ],
            [   $trav([['a', 'a'], ['b']]),    ['b']  ],
        ];
    }
}

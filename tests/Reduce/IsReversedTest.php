<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class IsReversedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         isSortedReversely array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        int|float $expected
     */
    public function testArray(array $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::isReversed($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            //  data                    expected
            [   [],                     true      ],
            [   [0],                    true      ],
            [   [null],                 true      ],
            [   [0, 1],                 false     ],
            [   [null, null],           true      ],
            [   [null, 1],              false     ],
            [   [1, null],              true      ],
            [   [-1, null],             true      ],
            [   [0, 0],                 true      ],
            [   [1, 1],                 true      ],
            [   [1, 0],                 true      ],
            [   [1, 2, 3],              false     ],
            [   [3, 2, 1],              true      ],
            [   [2, 2, 2],              true      ],
            [   [2, 2, 3],              false     ],
            [   [2, 3, 1],              false     ],
            [   ['a', 'b', 'c'],        false     ],
            [   ['b', 'a', 'c'],        false     ],
            [   [['a'], ['b'], ['c']],  false     ],
            [   [['b'], ['a'], ['c']],  false     ],
            [   [['b'], ['a', 'a']],    false     ],
            [   [['bb'], ['a', 'a']],   false     ],
            [   [['a', 'a'], ['b']],    true      ],
        ];
    }

    /**
     * @test         isSortedReversely generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testGenerators(\Generator $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::isReversed($data);

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
            [   $gen([]),                     true      ],
            [   $gen([0]),                    true      ],
            [   $gen([null]),                 true      ],
            [   $gen([0, 1]),                 false     ],
            [   $gen([null, null]),           true      ],
            [   $gen([null, 1]),              false     ],
            [   $gen([1, null]),              true      ],
            [   $gen([-1, null]),             true      ],
            [   $gen([0, 0]),                 true      ],
            [   $gen([1, 1]),                 true      ],
            [   $gen([1, 0]),                 true      ],
            [   $gen([1, 2, 3]),              false     ],
            [   $gen([3, 2, 1]),              true      ],
            [   $gen([2, 2, 2]),              true      ],
            [   $gen([2, 2, 3]),              false     ],
            [   $gen([2, 3, 1]),              false     ],
            [   $gen(['a', 'b', 'c']),        false     ],
            [   $gen(['b', 'a', 'c']),        false     ],
            [   $gen([['a'], ['b'], ['c']]),  false     ],
            [   $gen([['b'], ['a'], ['c']]),  false     ],
            [   $gen([['b'], ['a', 'a']]),    false     ],
            [   $gen([['bb'], ['a', 'a']]),   false     ],
            [   $gen([['a', 'a'], ['b']]),    true      ],
        ];
    }

    /**
     * @test         isSortedReversely iterators
     * @dataProvider dataProviderForIterators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testIterators(\Iterator $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::isReversed($data);

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
            [   $iter([]),                     true      ],
            [   $iter([0]),                    true      ],
            [   $iter([null]),                 true      ],
            [   $iter([0, 1]),                 false     ],
            [   $iter([null, null]),           true      ],
            [   $iter([null, 1]),              false     ],
            [   $iter([1, null]),              true      ],
            [   $iter([-1, null]),             true      ],
            [   $iter([0, 0]),                 true      ],
            [   $iter([1, 1]),                 true      ],
            [   $iter([1, 0]),                 true      ],
            [   $iter([1, 2, 3]),              false     ],
            [   $iter([3, 2, 1]),              true      ],
            [   $iter([2, 2, 2]),              true      ],
            [   $iter([2, 2, 3]),              false     ],
            [   $iter([2, 3, 1]),              false     ],
            [   $iter(['a', 'b', 'c']),        false     ],
            [   $iter(['b', 'a', 'c']),        false     ],
            [   $iter([['a'], ['b'], ['c']]),  false     ],
            [   $iter([['b'], ['a'], ['c']]),  false     ],
            [   $iter([['b'], ['a', 'a']]),    false     ],
            [   $iter([['bb'], ['a', 'a']]),   false     ],
            [   $iter([['a', 'a'], ['b']]),    true      ],
        ];
    }

    /**
     * @test         isSortedReversely traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        mixed $expected
     */
    public function testTraversables(\Traversable $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::isReversed($data);

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
            [   $trav([]),                     true      ],
            [   $trav([0]),                    true      ],
            [   $trav([null]),                 true      ],
            [   $trav([0, 1]),                 false     ],
            [   $trav([null, null]),           true      ],
            [   $trav([null, 1]),              false     ],
            [   $trav([1, null]),              true      ],
            [   $trav([-1, null]),             true      ],
            [   $trav([0, 0]),                 true      ],
            [   $trav([1, 1]),                 true      ],
            [   $trav([1, 0]),                 true      ],
            [   $trav([1, 2, 3]),              false     ],
            [   $trav([3, 2, 1]),              true      ],
            [   $trav([2, 2, 2]),              true      ],
            [   $trav([2, 2, 3]),              false     ],
            [   $trav([2, 3, 1]),              false     ],
            [   $trav(['a', 'b', 'c']),        false     ],
            [   $trav(['b', 'a', 'c']),        false     ],
            [   $trav([['a'], ['b'], ['c']]),  false     ],
            [   $trav([['b'], ['a'], ['c']]),  false     ],
            [   $trav([['b'], ['a', 'a']]),    false     ],
            [   $trav([['bb'], ['a', 'a']]),   false     ],
            [   $trav([['a', 'a'], ['b']]),    true      ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class IsSortedDirectlyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         isSortedDirectly array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        int|float $expected
     */
    public function testArray(array $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::isSortedDirectly($data);

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
            [   [0, 1],                 true      ],
            [   [null, null],           true      ],
            [   [null, 1],              true      ],
            [   [1, null],              false     ],
            [   [-1, null],             false     ],
            [   [0, 0],                 true      ],
            [   [1, 1],                 true      ],
            [   [1, 0],                 false     ],
            [   [1, 2, 3],              true      ],
            [   [3, 2, 1],              false     ],
            [   [2, 2, 2],              true      ],
            [   [2, 2, 3],              true      ],
            [   [2, 3, 1],              false     ],
            [   ['a', 'b', 'c'],        true      ],
            [   ['b', 'a', 'c'],        false     ],
            [   [['a'], ['b'], ['c']],  true      ],
            [   [['b'], ['a'], ['c']],  false     ],
            [   [['b'], ['a', 'a']],    true      ],
            [   [['bb'], ['a', 'a']],   true      ],
            [   [['a', 'a'], ['b']],    false     ],
        ];
    }

    /**
     * @test         isSortedDirectly generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testGenerators(\Generator $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::isSortedDirectly($data);

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
            [   $gen([0, 1]),                 true      ],
            [   $gen([null, null]),           true      ],
            [   $gen([null, 1]),              true      ],
            [   $gen([1, null]),              false     ],
            [   $gen([-1, null]),             false     ],
            [   $gen([0, 0]),                 true      ],
            [   $gen([1, 1]),                 true      ],
            [   $gen([1, 0]),                 false     ],
            [   $gen([1, 2, 3]),              true      ],
            [   $gen([3, 2, 1]),              false     ],
            [   $gen([2, 2, 2]),              true      ],
            [   $gen([2, 2, 3]),              true      ],
            [   $gen([2, 3, 1]),              false     ],
            [   $gen(['a', 'b', 'c']),        true      ],
            [   $gen(['b', 'a', 'c']),        false     ],
            [   $gen([['a'], ['b'], ['c']]),  true      ],
            [   $gen([['b'], ['a'], ['c']]),  false     ],
            [   $gen([['b'], ['a', 'a']]),    true      ],
            [   $gen([['bb'], ['a', 'a']]),   true      ],
            [   $gen([['a', 'a'], ['b']]),    false     ],
        ];
    }

    /**
     * @test         isSortedDirectly iterators
     * @dataProvider dataProviderForIterators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testIterators(\Iterator $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::isSortedDirectly($data);

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
            [   $iter([0, 1]),                 true      ],
            [   $iter([null, null]),           true      ],
            [   $iter([null, 1]),              true      ],
            [   $iter([1, null]),              false     ],
            [   $iter([-1, null]),             false     ],
            [   $iter([0, 0]),                 true      ],
            [   $iter([1, 1]),                 true      ],
            [   $iter([1, 0]),                 false     ],
            [   $iter([1, 2, 3]),              true      ],
            [   $iter([3, 2, 1]),              false     ],
            [   $iter([2, 2, 2]),              true      ],
            [   $iter([2, 2, 3]),              true      ],
            [   $iter([2, 3, 1]),              false     ],
            [   $iter(['a', 'b', 'c']),        true      ],
            [   $iter(['b', 'a', 'c']),        false     ],
            [   $iter([['a'], ['b'], ['c']]),  true      ],
            [   $iter([['b'], ['a'], ['c']]),  false     ],
            [   $iter([['b'], ['a', 'a']]),    true      ],
            [   $iter([['bb'], ['a', 'a']]),   true      ],
            [   $iter([['a', 'a'], ['b']]),    false     ],
        ];
    }

    /**
     * @test         isSortedDirectly traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        mixed $expected
     */
    public function testTraversables(\Traversable $data, $expected)
    {
        // Given: $data

        // When
        $result = Reduce::isSortedDirectly($data);

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
            [   $trav([0, 1]),                 true      ],
            [   $trav([null, null]),           true      ],
            [   $trav([null, 1]),              true      ],
            [   $trav([1, null]),              false     ],
            [   $trav([-1, null]),             false     ],
            [   $trav([0, 0]),                 true      ],
            [   $trav([1, 1]),                 true      ],
            [   $trav([1, 0]),                 false     ],
            [   $trav([1, 2, 3]),              true      ],
            [   $trav([3, 2, 1]),              false     ],
            [   $trav([2, 2, 2]),              true      ],
            [   $trav([2, 2, 3]),              true      ],
            [   $trav([2, 3, 1]),              false     ],
            [   $trav(['a', 'b', 'c']),        true      ],
            [   $trav(['b', 'a', 'c']),        false     ],
            [   $trav([['a'], ['b'], ['c']]),  true      ],
            [   $trav([['b'], ['a'], ['c']]),  false     ],
            [   $trav([['b'], ['a', 'a']]),    true      ],
            [   $trav([['bb'], ['a', 'a']]),   true      ],
            [   $trav([['a', 'a'], ['b']]),    false     ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Set;

use IterTools\Set;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class PartialIntersectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArraySets
     * @dataProvider dataProviderForArrayMultisets
     * @param array<array> $iterables
     * @param positive-int $minIntersectionCount
     * @param array $expected
     */
    public function testArray(array $iterables, int $minIntersectionCount, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::partialIntersection($minIntersectionCount, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public function dataProviderForArraySets(): array
    {
        return [
            [
                [],
                1,
                [],
            ],
            [
                [],
                2,
                [],
            ],
            [
                [
                    [],
                ],
                1,
                [],
            ],
            [
                [
                    [],
                ],
                2,
                [],
            ],
            [
                [
                    [],
                    [],
                ],
                1,
                [],
            ],
            [
                [
                    [],
                    [],
                ],
                2,
                [],
            ],
            [
                [
                    [],
                    [],
                ],
                3,
                [],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                ],
                2,
                [1, 2, 3, 4, 5],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, '3', '4', 5],
                ],
                2,
                [1, 2, 5],
            ],
            [
                [
                    ['1', '2', 3, 4, 5],
                    ['1', 2, '3', '4', 5],
                ],
                2,
                ['1', 5],
            ],
            [
                [
                    [1, 1, 1, 1, 1],
                    [1, 2, 3, 4, 5, 6],
                    [1, 5, 5, 5, 5, 5, 5],
                ],
                2,
                [1, 5],
            ],
            [
                [
                    [1, 1, 1, 1, 1],
                    [1, 2, 3, 4, '5', 6],
                    [1, 5, 5, 5, 5, 5, 5],
                ],
                2,
                [1],
            ],
            [
                [
                    [1, 1, 1, 1, 1],
                    [1, 2, 3, 4, 5, 6],
                    [1, 5, 5, 5, 5, 5, 5],
                ],
                3,
                [1],
            ],
            [
                [
                    [1, true, '1', 1.0, '1.0'],
                    [true, true, true, true, true, true],
                ],
                2,
                [true],
            ],
            [
                [
                    [1, 2, 3, 4, 5, 6, 7, 8, 9],
                    ['1', '2', 3, 4, 5, 6, 7, '8', '9'],
                    [1, 3, 5, 7, 9, 11],
                ],
                2,
                [1, 3, 4, 5, 6, 7, 9],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 10, 11],
                    [1, 2, 3, 12],
                    [1, 4, 13, 14],
                ],
                1,
                [1, 2, 3, 4, 5, 10, 11, 12, 13, 14],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 10, 11],
                    [1, 2, 3, 12],
                    [1, 4, 13, 14],
                ],
                2,
                [1, 2, 3, 4],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 10, 11],
                    [1, 2, 3, 12],
                    [1, 4, 13, 14],
                ],
                3,
                [1, 2],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 10, 11],
                    [1, 2, 3, 12],
                    [1, 4, 13, 14],
                ],
                4,
                [1],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 10, 11],
                    [1, 2, 3, 12],
                    [1, 4, 13, 14],
                ],
                5,
                [],
            ],
            [
                [
                    ['c++', 'java', 'c#', 'go', 'haskell'],
                    ['php', 'python', 'javascript', 'perl'],
                    ['c++', 'java', 'c#', 'go', 'php']
                ],
                2,
                ['c++', 'java', 'c#', 'go', 'php'],
            ],
        ];
    }

    public function dataProviderForArrayMultisets(): array
    {
        return [
            [
                [
                    [1, 1, 2],
                    [2, 2, 3],
                ],
                2,
                [2],
            ],
            [
                [
                    [1, 1, 1, 3],
                    [1, 1, 2],
                ],
                2,
                [1, 1],
            ],
            [
                [
                    [1, 1, 2, 4],
                    [1, 1, 1, 2, 3],
                ],
                2,
                [1, 1, 2],
            ],
            [
                [
                    [1, 1, 2, 2, 1, 1],
                    [2, 2, 1, 1, 2, 2],
                ],
                2,
                [2, 1, 2, 1],
            ],
            [
                [
                    [1, 1, 2, 2, 1, 1],
                    [2, 2, 1, 1, '2', '2'],
                ],
                2,
                [2, 1, 2, 1],
            ],
            [
                [
                    [1, 1, 2, 2, 1, 1],
                    [2, 2, '1', '1', 2, 2],
                ],
                2,
                [2, 2],
            ],
            [
                [
                    [1, 1, 1, 1, 1],
                    [1, 2, 3, 4, 5, 1, 2, 3, 4, 5],
                    [5, 5, 5, 5, 5, 1, 5, 5, 1],
                ],
                2,
                [1, 1, 5, 5],
            ],
            [
                [
                    [1, 1, 1, 1, 1],
                    [1, 2, 3, 4, 5, 1, 2, 3, 4, 5],
                    [5, 5, 5, 5, 5, 1, 5, 5, 1],
                ],
                3,
                [1, 1],
            ],
            [
                [
                    [1, 1, 1, 1, 'a'],
                    [1, 2, 3, 4, 5, 'a', 2, 3, 4, 5],
                    [5, 5, 5, 5, 5, 'a', 5, 5, 1],
                ],
                2,
                ['a', 1, 5, 5],
            ],
            [
                [
                    ['l', 'l', 'm', 'n', 'p', 'q', 'q', 'r'],
                    ['l', 'm', 'm', 'p', 'q', 'r', 'r', 'r', 'r']
                ],
                2,
                ['l', 'm', 'p', 'q', 'r'],
            ],
            [
                [
                    [1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6],
                    [4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 9],
                ],
                2,
                [4, 4, 5, 5, 6, 6],
            ],
            [
                [
                    ['a', 'a', 'b', 'b', 'b', 'c', 'd', 'd'],
                    ['b', 'b', 'c', 'c', 'c', 'd', 'd', 'e'],
                ],
                2,
                ['b', 'b', 'c', 'd', 'd'],
            ],
            [
                [
                    [1, 2],
                    [1],
                ],
                1,
                [1, 2],
            ],
            [
                [
                    [1, 2],
                    [1, 1],
                ],
                1,
                [1, 1, 2],
            ],
            [
                [
                    [1, 1],
                    [1, 1, 1, 2],
                ],
                1,
                [1, 1, 1, 2],
            ],
            [
                [
                    [1, 1, 3, 5],
                    [1, 2, 4, 5],
                ],
                1,
                [1, 1, 2, 3, 4, 5],
            ],
            [
                [
                    [1, 1, 4, 7],
                    [1, 2, 5, 7],
                    [1, 3, 6, 8],
                ],
                1,
                [1, 1, 2, 3, 4, 5, 6, 7, 8],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param array<\Generator> $iterables
     * @param positive-int $minIntersectionCount
     * @param array $expected
     */
    public function testGenerators(array $iterables, int $minIntersectionCount, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::partialIntersection($minIntersectionCount, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                [],
                1,
                [],
            ],
            [
                [],
                2,
                [],
            ],
            [
                [
                    $gen([]),
                ],
                1,
                [],
            ],
            [
                [
                    $gen([]),
                ],
                2,
                [],
            ],
            [
                [
                    $gen([]),
                    $gen([]),
                ],
                1,
                [],
            ],
            [
                [
                    $gen([]),
                    $gen([]),
                ],
                2,
                [],
            ],
            [
                [
                    $gen([]),
                    $gen([]),
                ],
                3,
                [],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([1, 2, 3, 4, 5]),
                ],
                2,
                [1, 2, 3, 4, 5],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([1, 2, '3', '4', 5]),
                ],
                2,
                [1, 2, 5],
            ],
            [
                [
                    $gen(['1', '2', 3, 4, 5]),
                    $gen(['1', 2, '3', '4', 5]),
                ],
                2,
                ['1', 5],
            ],
            [
                [
                    $gen([1, 1, 1, 1, 1]),
                    $gen([1, 2, 3, 4, 5, 6]),
                    $gen([1, 5, 5, 5, 5, 5, 5]),
                ],
                2,
                [1, 5],
            ],
            [
                [
                    $gen([1, 1, 1, 1, 1]),
                    $gen([1, 2, 3, 4, '5', 6]),
                    $gen([1, 5, 5, 5, 5, 5, 5]),
                ],
                2,
                [1],
            ],
            [
                [
                    $gen([1, 1, 1, 1, 1]),
                    $gen([1, 2, 3, 4, 5, 6]),
                    $gen([1, 5, 5, 5, 5, 5, 5]),
                ],
                3,
                [1],
            ],
            [
                [
                    $gen([1, true, '1', 1.0, '1.0']),
                    $gen([true, true, true, true, true, true]),
                ],
                2,
                [true],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                    $gen(['1', '2', 3, 4, 5, 6, 7, '8', '9']),
                    $gen([1, 3, 5, 7, 9, 11]),
                ],
                2,
                [1, 3, 4, 5, 6, 7, 9],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([1]),
                ],
                1,
                [1, 2],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([1, 1]),
                ],
                1,
                [1, 1, 2],
            ],
            [
                [
                    $gen([1, 1]),
                    $gen([1, 1, 1, 2]),
                ],
                1,
                [1, 1, 1, 2],
            ],
            [
                [
                    $gen([1, 1, 3, 5]),
                    $gen([1, 2, 4, 5]),
                ],
                1,
                [1, 1, 2, 3, 4, 5],
            ],
            [
                [
                    $gen([1, 1, 4, 7]),
                    $gen([1, 2, 5, 7]),
                    $gen([1, 3, 6, 8]),
                ],
                1,
                [1, 1, 2, 3, 4, 5, 6, 7, 8],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param array<\Iterator> $iterables
     * @param positive-int $minIntersectionCount
     * @param array $expected
     */
    public function testIterators(array $iterables, int $minIntersectionCount, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::partialIntersection($minIntersectionCount, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                [],
                1,
                [],
            ],
            [
                [],
                2,
                [],
            ],
            [
                [
                    $iter([]),
                ],
                1,
                [],
            ],
            [
                [
                    $iter([]),
                ],
                2,
                [],
            ],
            [
                [
                    $iter([]),
                    $iter([]),
                ],
                1,
                [],
            ],
            [
                [
                    $iter([]),
                    $iter([]),
                ],
                2,
                [],
            ],
            [
                [
                    $iter([]),
                    $iter([]),
                ],
                3,
                [],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([1, 2, 3, 4, 5]),
                ],
                2,
                [1, 2, 3, 4, 5],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([1, 2, '3', '4', 5]),
                ],
                2,
                [1, 2, 5],
            ],
            [
                [
                    $iter(['1', '2', 3, 4, 5]),
                    $iter(['1', 2, '3', '4', 5]),
                ],
                2,
                ['1', 5],
            ],
            [
                [
                    $iter([1, 1, 1, 1, 1]),
                    $iter([1, 2, 3, 4, 5, 6]),
                    $iter([1, 5, 5, 5, 5, 5, 5]),
                ],
                2,
                [1, 5],
            ],
            [
                [
                    $iter([1, 1, 1, 1, 1]),
                    $iter([1, 2, 3, 4, '5', 6]),
                    $iter([1, 5, 5, 5, 5, 5, 5]),
                ],
                2,
                [1],
            ],
            [
                [
                    $iter([1, 1, 1, 1, 1]),
                    $iter([1, 2, 3, 4, 5, 6]),
                    $iter([1, 5, 5, 5, 5, 5, 5]),
                ],
                3,
                [1],
            ],
            [
                [
                    $iter([1, true, '1', 1.0, '1.0']),
                    $iter([true, true, true, true, true, true]),
                ],
                2,
                [true],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                    $iter(['1', '2', 3, 4, 5, 6, 7, '8', '9']),
                    $iter([1, 3, 5, 7, 9, 11]),
                ],
                2,
                [1, 3, 4, 5, 6, 7, 9],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([1]),
                ],
                1,
                [1, 2],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([1, 1]),
                ],
                1,
                [1, 1, 2],
            ],
            [
                [
                    $iter([1, 1]),
                    $iter([1, 1, 1, 2]),
                ],
                1,
                [1, 1, 1, 2],
            ],
            [
                [
                    $iter([1, 1, 3, 5]),
                    $iter([1, 2, 4, 5]),
                ],
                1,
                [1, 1, 2, 3, 4, 5],
            ],
            [
                [
                    $iter([1, 1, 4, 7]),
                    $iter([1, 2, 5, 7]),
                    $iter([1, 3, 6, 8]),
                ],
                1,
                [1, 1, 2, 3, 4, 5, 6, 7, 8],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param array<\Traversable> $iterables
     * @param positive-int $minIntersectionCount
     * @param array $expected
     */
    public function testTraversables(array $iterables, int $minIntersectionCount, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::partialIntersection($minIntersectionCount, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                [],
                1,
                [],
            ],
            [
                [],
                2,
                [],
            ],
            [
                [
                    $trav([]),
                ],
                1,
                [],
            ],
            [
                [
                    $trav([]),
                ],
                2,
                [],
            ],
            [
                [
                    $trav([]),
                    $trav([]),
                ],
                1,
                [],
            ],
            [
                [
                    $trav([]),
                    $trav([]),
                ],
                2,
                [],
            ],
            [
                [
                    $trav([]),
                    $trav([]),
                ],
                3,
                [],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([1, 2, 3, 4, 5]),
                ],
                2,
                [1, 2, 3, 4, 5],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([1, 2, '3', '4', 5]),
                ],
                2,
                [1, 2, 5],
            ],
            [
                [
                    $trav(['1', '2', 3, 4, 5]),
                    $trav(['1', 2, '3', '4', 5]),
                ],
                2,
                ['1', 5],
            ],
            [
                [
                    $trav([1, 1, 1, 1, 1]),
                    $trav([1, 2, 3, 4, 5, 6]),
                    $trav([1, 5, 5, 5, 5, 5, 5]),
                ],
                2,
                [1, 5],
            ],
            [
                [
                    $trav([1, 1, 1, 1, 1]),
                    $trav([1, 2, 3, 4, '5', 6]),
                    $trav([1, 5, 5, 5, 5, 5, 5]),
                ],
                2,
                [1],
            ],
            [
                [
                    $trav([1, 1, 1, 1, 1]),
                    $trav([1, 2, 3, 4, 5, 6]),
                    $trav([1, 5, 5, 5, 5, 5, 5]),
                ],
                3,
                [1],
            ],
            [
                [
                    $trav([1, true, '1', 1.0, '1.0']),
                    $trav([true, true, true, true, true, true]),
                ],
                2,
                [true],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                    $trav(['1', '2', 3, 4, 5, 6, 7, '8', '9']),
                    $trav([1, 3, 5, 7, 9, 11]),
                ],
                2,
                [1, 3, 4, 5, 6, 7, 9],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([1]),
                ],
                1,
                [1, 2],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([1, 1]),
                ],
                1,
                [1, 1, 2],
            ],
            [
                [
                    $trav([1, 1]),
                    $trav([1, 1, 1, 2]),
                ],
                1,
                [1, 1, 1, 2],
            ],
            [
                [
                    $trav([1, 1, 3, 5]),
                    $trav([1, 2, 4, 5]),
                ],
                1,
                [1, 1, 2, 3, 4, 5],
            ],
            [
                [
                    $trav([1, 1, 4, 7]),
                    $trav([1, 2, 5, 7]),
                    $trav([1, 3, 6, 8]),
                ],
                1,
                [1, 1, 2, 3, 4, 5, 6, 7, 8],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForMixed
     * @param array<mixed> $iterables
     * @param positive-int $minIntersectionCount
     * @param array $expected
     */
    public function testMixed(array $iterables, int $minIntersectionCount, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::partialIntersection($minIntersectionCount, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public function dataProviderForMixed(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new \ArrayIterator($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                [
                    [],
                    $gen([]),
                    $iter([]),
                    $trav([]),
                ],
                1,
                [],
            ],
            [
                [
                    [],
                    $gen([]),
                    $iter([]),
                    $trav([]),
                ],
                2,
                [],
            ],
            [
                [
                    [],
                    $gen([]),
                    $iter([]),
                    $trav([]),
                ],
                3,
                [],
            ],
            [
                [
                    [],
                    $gen([]),
                    $iter([]),
                    $trav([]),
                ],
                4,
                [],
            ],
            [
                [
                    [],
                    $gen([]),
                    $iter([]),
                    $trav([]),
                ],
                5,
                [],
            ],
            [
                [
                    [1, 5],
                    $gen([2, 6]),
                    $iter([3, 7]),
                    $trav([4]),
                ],
                1,
                [1, 2, 3, 4, 5, 6, 7],
            ],
            [
                [
                    [1, 5],
                    $gen([2, 6]),
                    $iter([3, 7]),
                    $trav([4]),
                ],
                2,
                [],
            ],
            [
                [
                    [1, 1, 1, 1, 1],
                    $gen(['1', true, '1.0', 1, 1.0, 'a', 'b', 'c']),
                    $iter([1, '1', 1, '1']),
                    $trav([1, true, 1]),
                ],
                2,
                [1, '1', true, 1],
            ],
            [
                [
                    [1, 1, 1, 1, 1],
                    $gen(['1', true, '1.0', 1, 1.0, 'a', 'b', 'c']),
                    $iter([1, '1', 1, '1']),
                    $trav([1, true, 1]),
                ],
                3,
                [1, 1],
            ],
            [
                [
                    [1, 1, 1, 1, 1],
                    $gen(['1', true, '1.0', 1, 1.0, 'a', 'b', 'c']),
                    $iter([1, '1', 1, '1']),
                    $trav([1, true, 1]),
                ],
                4,
                [1],
            ],
            [
                [
                    [1, 1, 1, 1, 1],
                    $gen(['1', true, '1.0', 1, 1.0, 'a', 'b', 'c']),
                    $iter([1, '1', 1, '1']),
                    $trav([1, true, 1]),
                ],
                5,
                [],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForDemo
     * @param array $a
     * @param array $b
     * @param array $c
     * @param array $d
     * @param int $m
     * @param array $expected
     * @return void
     */
    public function testDemo(
        array $a,
        array $b,
        array $c,
        array $d,
        int $m,
        array $expected
    ): void {
        // Given
        $result = [];

        // When
        foreach (Set::partialIntersection($m, $a, $b, $c, $d) as $value) {
            $result[] = $value;
        }
        sort($result);

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public function dataProviderForDemo(): array
    {
        $a = [1, 2, 3, 4, 5];
        $b = [1, 2, 10, 11];
        $c = [1, 2, 3, 12];
        $d = [1, 4, 13, 14];

        return [
            [$a, $b, $c, $d, 1, [1, 2, 3, 4, 5, 10, 11, 12, 13, 14]],
            [$a, $b, $c, $d, 2, [1, 2, 3, 4]],
            [$a, $b, $c, $d, 3, [1, 2]],
            [$a, $b, $c, $d, 4, [1]],
            [$a, $b, $c, $d, 5, []],
        ];
    }

    /**
     * @dataProvider dataProviderForIteratorToArray
     * @param int $minIntersectionCount
     * @param array<array> ...$sets
     * @param array $expected
     * @return void
     */
    public function testIteratorToArray(int $minIntersectionCount, array $sets, array $expected): void
    {
        // Given
        $partIntersect = Set::partialIntersection($minIntersectionCount, ...$sets);

        // When
        $actual = iterator_to_array($partIntersect);

        // Then
        $this->assertEquals($expected, $actual);
    }

    public function dataProviderForIteratorToArray(): array
    {
        return [
            [
                1,
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                    [4, 5, 6, 7, 8],
                ],
                [1, 2, 3, 4, 5, 6, 7, 8],
            ],
            [
                2,
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                    [4, 5, 6, 7, 8],
                ],
                [2, 3, 4, 5, 6, 7],
            ],
            [
                3,
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                    [4, 5, 6, 7, 8],
                ],
                [3, 4, 5, 6],
            ],
            [
                4,
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                    [4, 5, 6, 7, 8],
                ],
                [4, 5],
            ],
            [
                5,
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                    [4, 5, 6, 7, 8],
                ],
                [],
            ],
        ];
    }

    /**
     * @test iterator_to_array
     * @dataProvider dataProviderForArraySets
     * @dataProvider dataProviderForArrayMultisets
     * @param array<array> $iterables
     * @param positive-int $minIntersectionCount
     * @param array $expected
     */
    public function testArrayIteratorToArray(array $iterables, int $minIntersectionCount, array $expected): void
    {
        // Given
        $iterator = Set::partialIntersection($minIntersectionCount, ...$iterables);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }
}

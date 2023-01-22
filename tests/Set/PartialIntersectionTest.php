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
     * @dataProvider dataProviderForArray
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
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForArray(): array
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
        $this->assertEquals($expected, $result);
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
        $this->assertEquals($expected, $result);
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
        $this->assertEquals($expected, $result);
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
        $this->assertEquals($expected, $result);
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
}

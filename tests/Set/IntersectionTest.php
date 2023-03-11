<?php

declare(strict_types=1);

namespace IterTools\Tests\Set;

use IterTools\Set;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class IntersectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArraySets
     * @dataProvider dataProviderForArrayMultisets
     * @param        array<array> $iterables
     * @param        array $expected
     */
    public function testArray(array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::intersection(...$iterables) as $datum) {
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
                [],
            ],
            [
                [
                    [],
                ],
                [],
            ],
            [
                [
                    [],
                    [],
                ],
                [],
            ],
            [
                [
                    [],
                    [],
                    [],
                ],
                [],
            ],
            [
                [
                    [1],
                    [],
                ],
                [],
            ],
            [
                [
                    [],
                    [2],
                ],
                [],
            ],
            [
                [
                    [2],
                    [2],
                ],
                [2],
            ],
            [
                [
                    [1, 2],
                    [3, 4],
                ],
                [],
            ],
            [
                [
                    [1, 2],
                    [2, 3],
                ],
                [2],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                ],
                [1, 2, 3, 4, 5],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, '4', '5'],
                ],
                [1, 2, 3],
            ],
            [
                [
                    ['1', '2', '3', 4, 5],
                    [1, 2, 3, '4', '5'],
                ],
                [],
            ],
            [
                [
                    [null, 1, 2, 3, 100, null],
                    [null, 0, 1, 2, 3, 4, null],
                    [null, -1, 0, 1, 2, 3, 4, 5, null],
                    [null, -2, -1, 0, 1, 2, 3, 4, 5, 6, null],
                    [null, -3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, null],
                ],
                [null, 1, 2, 3, null],
            ],
            [
                [
                    ['1', 2, '3.3', true, false],
                    [true, '2', 3.3, '4', true],
                ],
                [true],
            ],
            [
                [
                    [1, 2, 3, 4, 5, 6, 7, 8, 9],
                    ['1', '2', 3, 4, 5, 6, 7, '8', '9'],
                    [1, 3, 5, 7, 9, 11],
                ],
                [3, 5, 7],
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
                [2],
            ],
            [
                [
                    [1, 1, 1, 3],
                    [1, 1, 2],
                ],
                [1, 1],
            ],
            [
                [
                    [1, 1, 2, 4],
                    [1, 1, 1, 2, 3],
                ],
                [1, 1, 2],
            ],
            [
                [
                    [1, 1, 2, 2, 1, 1],
                    [2, 2, 1, 1, 2, 2],
                ],
                [2, 1, 2, 1],
            ],
            [
                [
                    [1, 1, 2, 2, 1, 1],
                    [2, 2, 1, 1, '2', '2'],
                ],
                [2, 1, 2, 1],
            ],
            [
                [
                    [1, 1, 2, 2, 1, 1],
                    [2, 2, '1', '1', 2, 2],
                ],
                [2, 2],
            ],
            [
                [
                    [1, 1, 1, 1, 1],
                    [1, 2, 3, 4, 5, 1, 2, 3, 4, 5],
                    [5, 5, 5, 5, 5, 1, 5, 5, 1],
                ],
                [1, 1],
            ],
            [
                [
                    [1, 1, 1, 1, 'a'],
                    [1, 2, 3, 4, 5, 'a', 2, 3, 4, 5],
                    [5, 5, 5, 5, 5, 'a', 5, 5, 1],
                ],
                ['a', 1],
            ],
            [
                [
                    ['l', 'l', 'm', 'n', 'p', 'q', 'q', 'r'],
                    ['l', 'm', 'm', 'p', 'q', 'r', 'r', 'r', 'r']
                ],
                ['l', 'm', 'p', 'q', 'r'],
            ],
            [
                [
                    [1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6],
                    [4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 9],
                ],
                [4, 4, 5, 5, 6, 6],
            ],
            [
                [
                    ['a', 'a', 'b', 'b', 'b', 'c', 'd', 'd'],
                    ['b', 'b', 'c', 'c', 'c', 'd', 'd', 'e'],
                ],
                ['b', 'b', 'c', 'd', 'd'],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param        array<\Generator> $iterables
     * @param        array $expected
     */
    public function testGenerators(array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::intersection(...$iterables) as $datum) {
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
                [],
            ],
            [
                [
                    $gen([]),
                ],
                [],
            ],
            [
                [
                    $gen([]),
                    $gen([]),
                ],
                [],
            ],
            [
                [
                    $gen([]),
                    $gen([]),
                    $gen([]),
                ],
                [],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([1, 2, 3, 4, 5]),
                ],
                [1, 2, 3, 4, 5],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([1, 2, 3, '4', '5']),
                ],
                [1, 2, 3],
            ],
            [
                [
                    $gen(['1', '2', '3', 4, 5]),
                    $gen([1, 2, 3, '4', '5']),
                ],
                [],
            ],
            [
                [
                    $gen([1, 1, 2, 2, 1, 1]),
                    $gen([2, 2, 1, 1, 2, 2]),
                ],
                [2, 1, 2, 1],
            ],
            [
                [
                    $gen([1, 1, 2, 2, 1, 1]),
                    $gen([2, 2, 1, 1, '2', '2']),
                ],
                [2, 1, 2, 1],
            ],
            [
                [
                    $gen([1, 1, 2, 2, 1, 1]),
                    $gen([2, 2, '1', '1', 2, 2]),
                ],
                [2, 2],
            ],
            [
                [
                    $gen([1, 1, 1, 1, 1]),
                    $gen([1, 2, 3, 4, 5, 1, 2, 3, 4, 5]),
                    $gen([5, 5, 5, 5, 5, 1, 5, 5, 1]),
                ],
                [1, 1],
            ],
            [
                [
                    $gen([1, 1, 1, 1, 'a']),
                    $gen([1, 2, 3, 4, 5, 'a', 2, 3, 4, 5]),
                    $gen([5, 5, 5, 5, 5, 'a', 5, 5, 1]),
                ],
                ['a', 1],
            ],
            [
                [
                    $gen([null, 1, 2, 3, 100, null]),
                    $gen([null, 0, 1, 2, 3, 4, null]),
                    $gen([null, -1, 0, 1, 2, 3, 4, 5, null]),
                    $gen([null, -2, -1, 0, 1, 2, 3, 4, 5, 6, null]),
                    $gen([null, -3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, null]),
                ],
                [null, 1, 2, 3, null],
            ],
            [
                [
                    $gen(['1', 2, '3.3', true, false]),
                    $gen([true, '2', 3.3, '4', true]),
                ],
                [true],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                    $gen(['1', '2', 3, 4, 5, 6, 7, '8', '9']),
                    $gen([1, 3, 5, 7, 9, 11]),
                ],
                [3, 5, 7],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param        array<\Iterator> $iterables
     * @param        array $expected
     */
    public function testIterators(array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::intersection(...$iterables) as $datum) {
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
                [],
            ],
            [
                [
                    $iter([]),
                ],
                [],
            ],
            [
                [
                    $iter([]),
                    $iter([]),
                ],
                [],
            ],
            [
                [
                    $iter([]),
                    $iter([]),
                    $iter([]),
                ],
                [],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([1, 2, 3, 4, 5]),
                ],
                [1, 2, 3, 4, 5],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([1, 2, 3, '4', '5']),
                ],
                [1, 2, 3],
            ],
            [
                [
                    $iter(['1', '2', '3', 4, 5]),
                    $iter([1, 2, 3, '4', '5']),
                ],
                [],
            ],
            [
                [
                    $iter([1, 1, 2, 2, 1, 1]),
                    $iter([2, 2, 1, 1, 2, 2]),
                ],
                [2, 1, 2, 1],
            ],
            [
                [
                    $iter([1, 1, 2, 2, 1, 1]),
                    $iter([2, 2, 1, 1, '2', '2']),
                ],
                [2, 1, 2, 1],
            ],
            [
                [
                    $iter([1, 1, 2, 2, 1, 1]),
                    $iter([2, 2, '1', '1', 2, 2]),
                ],
                [2, 2],
            ],
            [
                [
                    $iter([1, 1, 1, 1, 1]),
                    $iter([1, 2, 3, 4, 5, 1, 2, 3, 4, 5]),
                    $iter([5, 5, 5, 5, 5, 1, 5, 5, 1]),
                ],
                [1, 1],
            ],
            [
                [
                    $iter([1, 1, 1, 1, 'a']),
                    $iter([1, 2, 3, 4, 5, 'a', 2, 3, 4, 5]),
                    $iter([5, 5, 5, 5, 5, 'a', 5, 5, 1]),
                ],
                ['a', 1],
            ],
            [
                [
                    $iter([null, 1, 2, 3, 100, null]),
                    $iter([null, 0, 1, 2, 3, 4, null]),
                    $iter([null, -1, 0, 1, 2, 3, 4, 5, null]),
                    $iter([null, -2, -1, 0, 1, 2, 3, 4, 5, 6, null]),
                    $iter([null, -3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, null]),
                ],
                [null, 1, 2, 3, null],
            ],
            [
                [
                    $iter(['1', 2, '3.3', true, false]),
                    $iter([true, '2', 3.3, '4', true]),
                ],
                [true],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                    $iter(['1', '2', 3, 4, 5, 6, 7, '8', '9']),
                    $iter([1, 3, 5, 7, 9, 11]),
                ],
                [3, 5, 7],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param        array<\Traversable> $iterables
     * @param        array $expected
     */
    public function testTraversables(array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::intersection(...$iterables) as $datum) {
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
                [],
            ],
            [
                [
                    $trav([]),
                ],
                [],
            ],
            [
                [
                    $trav([]),
                    $trav([]),
                ],
                [],
            ],
            [
                [
                    $trav([]),
                    $trav([]),
                    $trav([]),
                ],
                [],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([1, 2, 3, 4, 5]),
                ],
                [1, 2, 3, 4, 5],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([1, 2, 3, '4', '5']),
                ],
                [1, 2, 3],
            ],
            [
                [
                    $trav(['1', '2', '3', 4, 5]),
                    $trav([1, 2, 3, '4', '5']),
                ],
                [],
            ],
            [
                [
                    $trav([1, 1, 2, 2, 1, 1]),
                    $trav([2, 2, 1, 1, 2, 2]),
                ],
                [2, 1, 2, 1],
            ],
            [
                [
                    $trav([1, 1, 2, 2, 1, 1]),
                    $trav([2, 2, 1, 1, '2', '2']),
                ],
                [2, 1, 2, 1],
            ],
            [
                [
                    $trav([1, 1, 2, 2, 1, 1]),
                    $trav([2, 2, '1', '1', 2, 2]),
                ],
                [2, 2],
            ],
            [
                [
                    $trav([1, 1, 1, 1, 1]),
                    $trav([1, 2, 3, 4, 5, 1, 2, 3, 4, 5]),
                    $trav([5, 5, 5, 5, 5, 1, 5, 5, 1]),
                ],
                [1, 1],
            ],
            [
                [
                    $trav([1, 1, 1, 1, 'a']),
                    $trav([1, 2, 3, 4, 5, 'a', 2, 3, 4, 5]),
                    $trav([5, 5, 5, 5, 5, 'a', 5, 5, 1]),
                ],
                ['a', 1],
            ],
            [
                [
                    $trav([null, 1, 2, 3, 100, null]),
                    $trav([null, 0, 1, 2, 3, 4, null]),
                    $trav([null, -1, 0, 1, 2, 3, 4, 5, null]),
                    $trav([null, -2, -1, 0, 1, 2, 3, 4, 5, 6, null]),
                    $trav([null, -3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, null]),
                ],
                [null, 1, 2, 3, null],
            ],
            [
                [
                    $trav(['1', 2, '3.3', true, false]),
                    $trav([true, '2', 3.3, '4', true]),
                ],
                [true],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                    $trav(['1', '2', 3, 4, 5, 6, 7, '8', '9']),
                    $trav([1, 3, 5, 7, 9, 11]),
                ],
                [3, 5, 7],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForMixed
     * @param        array<mixed> $iterables
     * @param        array $expected
     */
    public function testMixed(array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::intersection(...$iterables) as $datum) {
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
                [],
            ],
            [
                [
                    [1, 1, 1, 1, 1],
                    $gen([1, 2, 3, 4, 5, 1, 2, 3, 4, 5]),
                    $iter([5, 5, 5, 5, 5, 1, 5, 5, 1]),
                    $trav([]),
                ],
                [],
            ],
            [
                [
                    [1, 1, 1, 1, 1],
                    $gen([1, 2, 3, 4, 5, 1, 2, 3, 4, 5]),
                    $iter([5, 5, 5, 5, 5, 1, 5, 5, 1]),
                    $trav([5, 2, 5, 1, 1, 2]),
                ],
                [1, 1],
            ],
            [
                [
                    [1, 1, 1, 1, '1'],
                    $gen([1, 2, 3, 4, 5, '1', 2, 3, 4, 5]),
                    $iter([5, 5, 5, 5, 5, '1', 5, 5, 1]),
                    $trav([5, 2, 5, 1, 1, 2]),
                ],
                [1],
            ],
        ];
    }

    /**
     * @test         iterator_to_array
     * @dataProvider dataProviderForArraySets
     * @dataProvider dataProviderForArrayMultisets
     * @param        array<array> $iterables
     * @param        array $expected
     */
    public function testIteratorToArray(array $iterables, array $expected): void
    {
        // Given
        $iterator = Set::intersection(...$iterables);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }
}

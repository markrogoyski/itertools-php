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
     * @dataProvider dataProviderForArray
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
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForArray(): array
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
                    [null, null],
                ],
                [null, null],
            ],
            [
                [
                    [null, null],
                    [],
                ],
                [],
            ],
            [
                [
                    [null, null, null],
                    [null, null],
                    [null],
                ],
                [null],
            ],
            [
                [
                    [1, 2, 3],
                    [2, 3, 4],
                ],
                [2, 3],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                ],
                [3, 4, 5],
            ],
            [
                [
                    [2, 3, 4, 5],
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                ],
                [3, 4, 5],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6],
                ],
                [3, 4, 5],
            ],
            [
                [
                    [1, 1, 1, 1, 1, 3],
                    [2, 2, 3, 1, 1],
                    [3, 1, 1, 3, 3],
                ],
                [1, 1, 3],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6, 1],
                    [3, 4, 5, 6, 1],
                ],
                [3, 4, 5, 1],
            ],
            [
                [
                    [1, 2, 3, 4, 3, 5],
                    [2, 3, 4, 5, 6, 1, 2, 3],
                    [3, 4, 5, 6, 1, 2, 3],
                ],
                [3, 4, 5, 1, 2, 3],
            ],
            [
                [
                    [2, 3, 4, 5],
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6],
                ],
                [3, 4, 5],
            ],
            [
                [
                    [2, 3, 4, 5],
                    [2, 3, 4, 5, 6],
                    [],
                ],
                [],
            ],
            [
                [
                    [1.1, 2.1, 3.1],
                    [2.1, 3.1, 4.1],
                ],
                [2.1, 3.1],
            ],
            [
                [
                    ['1', '2', '3'],
                    ['2', '3', '4'],
                ],
                ['2', '3'],
            ],
            [
                [
                    ['1', '2', '3', '2', '11'],
                    ['2', '3', '4', '12', '13', '2'],
                ],
                ['2', '3', '2'],
            ],
            [
                [
                    ['1', 2, '3.3', true, false],
                    [true, '2', 3.3, '4', true],
                ],
                [true, 2, 3.3, true],
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
                    $gen([null, null]),
                ],
                [null, null],
            ],
            [
                [
                    $gen([null, null]),
                    $gen([]),
                ],
                [],
            ],
            [
                [
                    $gen([null, null, null]),
                    $gen([null, null]),
                    $gen([null]),
                ],
                [null],
            ],
            [
                [
                    $gen([1, 2, 3]),
                    $gen([2, 3, 4]),
                ],
                [2, 3],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([2, 3, 4, 5, 6]),
                    $gen([3, 4, 5, 6, 7]),
                ],
                [3, 4, 5],
            ],
            [
                [
                    $gen([2, 3, 4, 5]),
                    $gen([2, 3, 4, 5, 6]),
                    $gen([3, 4, 5, 6, 7]),
                ],
                [3, 4, 5],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([2, 3, 4, 5, 6]),
                    $gen([3, 4, 5, 6]),
                ],
                [3, 4, 5],
            ],
            [
                [
                    $gen([1, 1, 1, 1, 1, 3]),
                    $gen([2, 2, 3, 1, 1]),
                    $gen([3, 1, 1, 3, 3]),
                ],
                [1, 1, 3],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([2, 3, 4, 5, 6, 1]),
                    $gen([3, 4, 5, 6, 1]),
                ],
                [3, 4, 5, 1],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 3, 5]),
                    $gen([2, 3, 4, 5, 6, 1, 2, 3]),
                    $gen([3, 4, 5, 6, 1, 2, 3]),
                ],
                [3, 4, 5, 1, 2, 3],
            ],
            [
                [
                    $gen([2, 3, 4, 5]),
                    $gen([2, 3, 4, 5, 6]),
                    $gen([3, 4, 5, 6]),
                ],
                [3, 4, 5],
            ],
            [
                [
                    $gen([2, 3, 4, 5]),
                    $gen([2, 3, 4, 5, 6]),
                    $gen([]),
                ],
                [],
            ],
            [
                [
                    $gen([1.1, 2.1, 3.1]),
                    $gen([2.1, 3.1, 4.1]),
                ],
                [2.1, 3.1],
            ],
            [
                [
                    $gen(['1', '2', '3']),
                    $gen(['2', '3', '4']),
                ],
                ['2', '3'],
            ],
            [
                [
                    $gen(['1', '2', '3', '2', '11']),
                    $gen(['2', '3', '4', '12', '13', '2']),
                ],
                ['2', '3', '2'],
            ],
            [
                [
                    $gen(['1', 2, '3.3', true, false]),
                    $gen([true, '2', 3.3, '4', true]),
                ],
                [true, 2, 3.3, true],
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
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = static function (array $data) {
            return new \ArrayIterator($data);
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
                    $iter([null, null]),
                ],
                [null, null],
            ],
            [
                [
                    $iter([null, null]),
                    $iter([]),
                ],
                [],
            ],
            [
                [
                    $iter([null, null, null]),
                    $iter([null, null]),
                    $iter([null]),
                ],
                [null],
            ],
            [
                [
                    $iter([1, 2, 3]),
                    $iter([2, 3, 4]),
                ],
                [2, 3],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([2, 3, 4, 5, 6]),
                    $iter([3, 4, 5, 6, 7]),
                ],
                [3, 4, 5],
            ],
            [
                [
                    $iter([2, 3, 4, 5]),
                    $iter([2, 3, 4, 5, 6]),
                    $iter([3, 4, 5, 6, 7]),
                ],
                [3, 4, 5],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([2, 3, 4, 5, 6]),
                    $iter([3, 4, 5, 6]),
                ],
                [3, 4, 5],
            ],
            [
                [
                    $iter([1, 1, 1, 1, 1, 3]),
                    $iter([2, 2, 3, 1, 1]),
                    $iter([3, 1, 1, 3, 3]),
                ],
                [1, 1, 3],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([2, 3, 4, 5, 6, 1]),
                    $iter([3, 4, 5, 6, 1]),
                ],
                [3, 4, 5, 1],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 3, 5]),
                    $iter([2, 3, 4, 5, 6, 1, 2, 3]),
                    $iter([3, 4, 5, 6, 1, 2, 3]),
                ],
                [3, 4, 5, 1, 2, 3],
            ],
            [
                [
                    $iter([2, 3, 4, 5]),
                    $iter([2, 3, 4, 5, 6]),
                    $iter([3, 4, 5, 6]),
                ],
                [3, 4, 5],
            ],
            [
                [
                    $iter([2, 3, 4, 5]),
                    $iter([2, 3, 4, 5, 6]),
                    $iter([]),
                ],
                [],
            ],
            [
                [
                    $iter([1.1, 2.1, 3.1]),
                    $iter([2.1, 3.1, 4.1]),
                ],
                [2.1, 3.1],
            ],
            [
                [
                    $iter(['1', '2', '3']),
                    $iter(['2', '3', '4']),
                ],
                ['2', '3'],
            ],
            [
                [
                    $iter(['1', '2', '3', '2', '11']),
                    $iter(['2', '3', '4', '12', '13', '2']),
                ],
                ['2', '3', '2'],
            ],
            [
                [
                    $iter(['1', 2, '3.3', true, false]),
                    $iter([true, '2', 3.3, '4', true]),
                ],
                [true, 2, 3.3, true],
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
                    $trav([null, null]),
                ],
                [null, null],
            ],
            [
                [
                    $trav([null, null]),
                    $trav([]),
                ],
                [],
            ],
            [
                [
                    $trav([null, null, null]),
                    $trav([null, null]),
                    $trav([null]),
                ],
                [null],
            ],
            [
                [
                    $trav([1, 2, 3]),
                    $trav([2, 3, 4]),
                ],
                [2, 3],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([2, 3, 4, 5, 6]),
                    $trav([3, 4, 5, 6, 7]),
                ],
                [3, 4, 5],
            ],
            [
                [
                    $trav([2, 3, 4, 5]),
                    $trav([2, 3, 4, 5, 6]),
                    $trav([3, 4, 5, 6, 7]),
                ],
                [3, 4, 5],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([2, 3, 4, 5, 6]),
                    $trav([3, 4, 5, 6]),
                ],
                [3, 4, 5],
            ],
            [
                [
                    $trav([1, 1, 1, 1, 1, 3]),
                    $trav([2, 2, 3, 1, 1]),
                    $trav([3, 1, 1, 3, 3]),
                ],
                [1, 1, 3],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([2, 3, 4, 5, 6, 1]),
                    $trav([3, 4, 5, 6, 1]),
                ],
                [3, 4, 5, 1],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 3, 5]),
                    $trav([2, 3, 4, 5, 6, 1, 2, 3]),
                    $trav([3, 4, 5, 6, 1, 2, 3]),
                ],
                [3, 4, 5, 1, 2, 3],
            ],
            [
                [
                    $trav([2, 3, 4, 5]),
                    $trav([2, 3, 4, 5, 6]),
                    $trav([3, 4, 5, 6]),
                ],
                [3, 4, 5],
            ],
            [
                [
                    $trav([2, 3, 4, 5]),
                    $trav([2, 3, 4, 5, 6]),
                    $trav([]),
                ],
                [],
            ],
            [
                [
                    $trav([1.1, 2.1, 3.1]),
                    $trav([2.1, 3.1, 4.1]),
                ],
                [2.1, 3.1],
            ],
            [
                [
                    $trav(['1', '2', '3']),
                    $trav(['2', '3', '4']),
                ],
                ['2', '3'],
            ],
            [
                [
                    $trav(['1', '2', '3', '2', '11']),
                    $trav(['2', '3', '4', '12', '13', '2']),
                ],
                ['2', '3', '2'],
            ],
            [
                [
                    $trav(['1', 2, '3.3', true, false]),
                    $trav([true, '2', 3.3, '4', true]),
                ],
                [true, 2, 3.3, true],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForMixed
     * @param        array<\iterable> $iterables
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
                [],
            ],
            [
                [
                    [null, null, null, null, null],
                    $gen([null, null, null, null]),
                    $iter([null, null, null]),
                    $trav([null, null]),
                ],
                [null, null],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    $gen([2, 3, 4, 5, 6]),
                    $iter([3, 4, 5, 6, 7]),
                    $trav([4, 5, 6, 7, 8]),
                ],
                [4, 5],
            ],
            [
                [
                    [1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2, 3, 4, 5],
                    $gen([2, 3, 4, 5, 6, 2, 3, 4, 5, 6, 2, 3, 4, 5, 6]),
                    $iter([3, 4, 5, 6, 7, 3, 4, 5, 6, 7, 3, 4, 5, 6, 7]),
                    $trav([4, 5, 6, 7, 8, 4, 5, 6, 7, 8, 4, 5, 6, 7, 8]),
                ],
                [4, 5, 4, 5, 4, 5],
            ],
            [
                [
                    [1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 11],
                    $gen([2, 3, 4, 5, 6, 2, 3, 4, 5, 6, 2, 3, 4, 5, 6, 12, 13]),
                    $iter([3, 4, 5, 6, 7, 3, 4, 5, 6, 7, 3, 4, 5, 6, 7, 14, 15, 16]),
                    $trav([4, 5, 6, 7, 8, 4, 5, 6, 7, 8, 4, 5, 6, 7, 8, 17, 18, 19, 20]),
                ],
                [4, 5, 4, 5, 4, 5],
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Set;

use IterTools\Set;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class SymmetricDifferenceTest extends \PHPUnit\Framework\TestCase
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
        foreach (Set::symmetricDifference(...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
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
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, 4, 5],
                ],
                [],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [1, 2, 3, '4', '5'],
                ],
                [4, '4', 5, '5'],
            ],
            [
                [
                    ['1', '2', '3', 4, 5],
                    [1, 2, 3, '4', '5'],
                ],
                ['1', 1, '2', 2, '3', 3, 4, '4', 5, '5'],
            ],
            [
                [
                    [1, 1, 2, 2, 1, 1],
                    [2, 2, 1, 1, 2, 2],
                ],
                [1, 1, 2, 2],
            ],
            [
                [
                    [1, 1, 2, 2, 1, 1],
                    [2, 2, 1, 1, '2', '2'],
                ],
                [1, 1, '2', '2'],
            ],
            [
                [
                    [1, 1, 2, 2, 1, 1],
                    [2, 2, '1', '1', 2, 2],
                ],
                [1, 1, 1, 1, 2, 2, '1', '1'],
            ],
            [
                [
                    [1, 1, 1, 1, 1],
                    [1, 2, 3, 4, 5, 1, 2, 3, 4, 5],
                    [5, 5, 5, 5, 5, 1, 5, 5, 1],
                ],
                [1, 1, 1, 5, 5, 5, 5, 5, 2, 2, 3, 3, 4, 4],
            ],
            [
                [
                    [1, 1, '1', '1'],
                    [1, 2, 3, 1, 2, 3],
                    ['2', '3'],
                ],
                ['2', 2, 2, '3', '1', '1', 3, 3],
            ],
            [
                [
                    [null, 1, 6, null],
                    [null, 2, 7, 11, null],
                    [null, 3, 8, 12, 15, null],
                    [null, 4, 9, 13, 16, 18, null],
                    [null, 5, 10, 14, 17, 19, 20, null],
                ],
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                [
                    [null, 1, 6, null],
                    [null, '1', 7, 11, null],
                    [null, '1.0', 8, 12, 15, null],
                    [null, true, 9, 13, 16, 18, null],
                    [null, 'true', 10, 14, 17, 19, 20, null],
                ],
                [1, '1', '1.0', true, 'true', 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                [
                    ['1', 2, '3.3', true, false],
                    [true, '2', 3.3, '4', '5'],
                ],
                ['1', 2, '2', '3.3', 3.3, '4', false, '5'],
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
        foreach (Set::symmetricDifference(...$iterables) as $datum) {
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
                [],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([1, 2, 3, '4', '5']),
                ],
                [4, '4', 5, '5'],
            ],
            [
                [
                    $gen(['1', '2', '3', 4, 5]),
                    $gen([1, 2, 3, '4', '5']),
                ],
                ['1', 1, '2', 2, '3', 3, 4, '4', 5, '5'],
            ],
            [
                [
                    $gen([1, 1, 2, 2, 1, 1]),
                    $gen([2, 2, 1, 1, 2, 2]),
                ],
                [1, 1, 2, 2],
            ],
            [
                [
                    $gen([1, 1, 2, 2, 1, 1]),
                    $gen([2, 2, 1, 1, '2', '2']),
                ],
                [1, 1, '2', '2'],
            ],
            [
                [
                    $gen([1, 1, 2, 2, 1, 1]),
                    $gen([2, 2, '1', '1', 2, 2]),
                ],
                [1, 1, 1, 1, 2, 2, '1', '1'],
            ],
            [
                [
                    $gen([1, 1, 1, 1, 1]),
                    $gen([1, 2, 3, 4, 5, 1, 2, 3, 4, 5]),
                    $gen([5, 5, 5, 5, 5, 1, 5, 5, 1]),
                ],
                [1, 1, 1, 5, 5, 5, 5, 5, 2, 2, 3, 3, 4, 4],
            ],
            [
                [
                    $gen([1, 1, '1', '1']),
                    $gen([1, 2, 3, 1, 2, 3]),
                    $gen(['2', '3']),
                ],
                ['2', 2, 2, '3', '1', '1', 3, 3],
            ],
            [
                [
                    $gen([null, 1, 6, null]),
                    $gen([null, 2, 7, 11, null]),
                    $gen([null, 3, 8, 12, 15, null]),
                    $gen([null, 4, 9, 13, 16, 18, null]),
                    $gen([null, 5, 10, 14, 17, 19, 20, null]),
                ],
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                [
                    $gen([null, 1, 6, null]),
                    $gen([null, '1', 7, 11, null]),
                    $gen([null, '1.0', 8, 12, 15, null]),
                    $gen([null, true, 9, 13, 16, 18, null]),
                    $gen([null, 'true', 10, 14, 17, 19, 20, null]),
                ],
                [1, '1', '1.0', true, 'true', 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                [
                    $gen(['1', 2, '3.3', true, false]),
                    $gen([true, '2', 3.3, '4', '5']),
                ],
                ['1', 2, '2', '3.3', 3.3, '4', false, '5'],
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
        foreach (Set::symmetricDifference(...$iterables) as $datum) {
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
                [],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([1, 2, 3, '4', '5']),
                ],
                [4, '4', 5, '5'],
            ],
            [
                [
                    $iter(['1', '2', '3', 4, 5]),
                    $iter([1, 2, 3, '4', '5']),
                ],
                ['1', 1, '2', 2, '3', 3, 4, '4', 5, '5'],
            ],
            [
                [
                    $iter([1, 1, 2, 2, 1, 1]),
                    $iter([2, 2, 1, 1, 2, 2]),
                ],
                [1, 1, 2, 2],
            ],
            [
                [
                    $iter([1, 1, 2, 2, 1, 1]),
                    $iter([2, 2, 1, 1, '2', '2']),
                ],
                [1, 1, '2', '2'],
            ],
            [
                [
                    $iter([1, 1, 2, 2, 1, 1]),
                    $iter([2, 2, '1', '1', 2, 2]),
                ],
                [1, 1, 1, 1, 2, 2, '1', '1'],
            ],
            [
                [
                    $iter([1, 1, 1, 1, 1]),
                    $iter([1, 2, 3, 4, 5, 1, 2, 3, 4, 5]),
                    $iter([5, 5, 5, 5, 5, 1, 5, 5, 1]),
                ],
                [1, 1, 1, 5, 5, 5, 5, 5, 2, 2, 3, 3, 4, 4],
            ],
            [
                [
                    $iter([1, 1, '1', '1']),
                    $iter([1, 2, 3, 1, 2, 3]),
                    $iter(['2', '3']),
                ],
                ['2', 2, 2, '3', '1', '1', 3, 3],
            ],
            [
                [
                    $iter([null, 1, 6, null]),
                    $iter([null, 2, 7, 11, null]),
                    $iter([null, 3, 8, 12, 15, null]),
                    $iter([null, 4, 9, 13, 16, 18, null]),
                    $iter([null, 5, 10, 14, 17, 19, 20, null]),
                ],
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                [
                    $iter([null, 1, 6, null]),
                    $iter([null, '1', 7, 11, null]),
                    $iter([null, '1.0', 8, 12, 15, null]),
                    $iter([null, true, 9, 13, 16, 18, null]),
                    $iter([null, 'true', 10, 14, 17, 19, 20, null]),
                ],
                [1, '1', '1.0', true, 'true', 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                [
                    $iter(['1', 2, '3.3', true, false]),
                    $iter([true, '2', 3.3, '4', '5']),
                ],
                ['1', 2, '2', '3.3', 3.3, '4', false, '5'],
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
        foreach (Set::symmetricDifference(...$iterables) as $datum) {
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
                [],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([1, 2, 3, '4', '5']),
                ],
                [4, '4', 5, '5'],
            ],
            [
                [
                    $trav(['1', '2', '3', 4, 5]),
                    $trav([1, 2, 3, '4', '5']),
                ],
                ['1', 1, '2', 2, '3', 3, 4, '4', 5, '5'],
            ],
            [
                [
                    $trav([1, 1, 2, 2, 1, 1]),
                    $trav([2, 2, 1, 1, 2, 2]),
                ],
                [1, 1, 2, 2],
            ],
            [
                [
                    $trav([1, 1, 2, 2, 1, 1]),
                    $trav([2, 2, 1, 1, '2', '2']),
                ],
                [1, 1, '2', '2'],
            ],
            [
                [
                    $trav([1, 1, 2, 2, 1, 1]),
                    $trav([2, 2, '1', '1', 2, 2]),
                ],
                [1, 1, 1, 1, 2, 2, '1', '1'],
            ],
            [
                [
                    $trav([1, 1, 1, 1, 1]),
                    $trav([1, 2, 3, 4, 5, 1, 2, 3, 4, 5]),
                    $trav([5, 5, 5, 5, 5, 1, 5, 5, 1]),
                ],
                [1, 1, 1, 5, 5, 5, 5, 5, 2, 2, 3, 3, 4, 4],
            ],
            [
                [
                    $trav([1, 1, '1', '1']),
                    $trav([1, 2, 3, 1, 2, 3]),
                    $trav(['2', '3']),
                ],
                ['2', 2, 2, '3', '1', '1', 3, 3],
            ],
            [
                [
                    $trav([null, 1, 6, null]),
                    $trav([null, 2, 7, 11, null]),
                    $trav([null, 3, 8, 12, 15, null]),
                    $trav([null, 4, 9, 13, 16, 18, null]),
                    $trav([null, 5, 10, 14, 17, 19, 20, null]),
                ],
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                [
                    $trav([null, 1, 6, null]),
                    $trav([null, '1', 7, 11, null]),
                    $trav([null, '1.0', 8, 12, 15, null]),
                    $trav([null, true, 9, 13, 16, 18, null]),
                    $trav([null, 'true', 10, 14, 17, 19, 20, null]),
                ],
                [1, '1', '1.0', true, 'true', 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                [
                    $trav(['1', 2, '3.3', true, false]),
                    $trav([true, '2', 3.3, '4', '5']),
                ],
                ['1', 2, '2', '3.3', 3.3, '4', false, '5'],
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
        foreach (Set::symmetricDifference(...$iterables) as $datum) {
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
                    [1, 1, 1],
                    $gen([1, 2, 3, 1, 2, 3]),
                    $iter([3, 3, 3, 1]),
                    $trav([]),
                ],
                [1, 3, 2, 2],
            ],
            [
                [
                    [1, 1, 1],
                    $gen([1, 2, 3, 1, 2, 3]),
                    $iter(['3', 3, 3, 1]),
                    $trav([]),
                ],
                [1, '3', 2, 2],
            ],
            [
                [
                    [1, 1, 1, 1, 1],
                    $gen([1, 1, 1, 1, '1']),
                    $iter([1, 1, 1, 1, 1.0]),
                    $trav([1, 1, 1, 1, true]),
                ],
                [1, '1', 1.0, true],
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Set;

use IterTools\Set;
use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class PartialIntersectionCoerciveTest extends \PHPUnit\Framework\TestCase
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
        foreach (Set::partialIntersectionCoercive($minIntersectionCount, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public static function dataProviderForArray(): array
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
                    [],
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
                    [],
                ],
                2,
                [],
            ],
            [
                [
                    [],
                    [],
                    [],
                ],
                3,
                [],
            ],
            [
                [
                    [],
                    [],
                    [],
                ],
                4,
                [],
            ],
            [
                [
                    [1, 2, 3],
                ],
                1,
                [1, 2, 3],
            ],
            [
                [
                    [1, 2, 3],
                ],
                2,
                [],
            ],
            [
                [
                    [1, 2, 3],
                    [2, 3, 4, 5],
                ],
                1,
                [1, 2, 3, 4, 5],
            ],
            [
                [
                    [1, 2, 3],
                    [2, 3, 4, 5],
                ],
                2,
                [2, 3],
            ],
            [
                [
                    [1, 2, 3],
                    [2, 3, 4, 5],
                ],
                3,
                [],
            ],
            [
                [
                    [1, 2, 3, 2],
                    [2, 3, 4, 5, 2, 1],
                ],
                1,
                [1, 2, 3, 4, 2, 5],
            ],
            [
                [
                    [1, 2, 3, 2],
                    [2, 3, 4, 5, 2, 1],
                ],
                2,
                [2, 3, 2, 1],
            ],
            [
                [
                    [1, 2, 3, 2],
                    [2, 3, 4, 5, 2, 1],
                ],
                3,
                [],
            ],
            [
                [
                    [1, 2, 3, 2],
                    [2, 3, 4, 5, 2, 1],
                    [1, 2, 3],
                ],
                1,
                [1, 2, 3, 4, 2, 5],
            ],
            [
                [
                    [1, 2, 3, 2],
                    [2, 3, 4, 5, 2, 1],
                    [1, 2, 3],
                ],
                2,
                [1, 2, 3, 2],
            ],
            [
                [
                    [1, 2, 3, '2'],
                    ['2', '3', 4, 5, 2, 1],
                    [1, '2', '3'],
                ],
                2,
                [1, 2, 3, 2],
            ],
            [
                [
                    [1, 2, 3],
                    ['2', '3', 4, 5],
                    [1, '2'],
                ],
                2,
                [1, 2, 3],
            ],
            [
                [
                    [1, 2, 3, 2],
                    [2, 3, 4, 5, 2, 1],
                    [1, 2, 3],
                ],
                3,
                [2, 3, 1],
            ],
            [
                [
                    [null, null],
                    [null, null, null],
                ],
                1,
                [null, null, null],
            ],
            [
                [
                    [null, null],
                    [null, null, null],
                ],
                2,
                [null, null],
            ],
            [
                [
                    [null, null],
                    [null, null, null],
                ],
                3,
                [],
            ],
            [
                [
                    ['php', 'python', 'c++', 'java', 'c#', 'javascript', 'typescript'],
                    ['php', 'python', 'javascript', 'typescript'],
                    ['php', 'java', 'c#', 'typescript'],
                ],
                2,
                ['php', 'python', 'java', 'typescript', 'c#', 'javascript'],
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
        foreach (Set::partialIntersectionCoercive($minIntersectionCount, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public static function dataProviderForGenerators(): array
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
                    $gen([]),
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
                    $gen([]),
                ],
                2,
                [],
            ],
            [
                [
                    $gen([]),
                    $gen([]),
                    $gen([]),
                ],
                3,
                [],
            ],
            [
                [
                    $gen([]),
                    $gen([]),
                    $gen([]),
                ],
                4,
                [],
            ],
            [
                [
                    $gen([1, 2, 3]),
                ],
                1,
                [1, 2, 3],
            ],
            [
                [
                    $gen([1, 2, 3]),
                ],
                2,
                [],
            ],
            [
                [
                    $gen([1, 2, 3]),
                    $gen([2, 3, 4, 5]),
                ],
                1,
                [1, 2, 3, 4, 5],
            ],
            [
                [
                    $gen([1, 2, 3]),
                    $gen([2, 3, 4, 5]),
                ],
                2,
                [2, 3],
            ],
            [
                [
                    $gen([1, 2, 3]),
                    $gen([2, 3, 4, 5]),
                ],
                3,
                [],
            ],
            [
                [
                    $gen([1, 2, 3, 2]),
                    $gen([2, 3, 4, 5, 2, 1]),
                ],
                1,
                [1, 2, 3, 4, 2, 5],
            ],
            [
                [
                    $gen([1, 2, 3, 2]),
                    $gen([2, 3, 4, 5, 2, 1]),
                ],
                2,
                [2, 3, 2, 1],
            ],
            [
                [
                    $gen([1, 2, 3, 2]),
                    $gen([2, 3, 4, 5, 2, 1]),
                ],
                3,
                [],
            ],
            [
                [
                    $gen([1, 2, 3, 2]),
                    $gen([2, 3, 4, 5, 2, 1]),
                    $gen([1, 2, 3]),
                ],
                1,
                [1, 2, 3, 4, 2, 5],
            ],
            [
                [
                    $gen([1, 2, 3, 2]),
                    $gen([2, 3, 4, 5, 2, 1]),
                    $gen([1, 2, 3]),
                ],
                2,
                [1, 2, 3, 2],
            ],
            [
                [
                    $gen([1, 2, 3, 2]),
                    $gen([2, 3, 4, 5, 2, 1]),
                    $gen([1, 2, 3]),
                ],
                3,
                [2, 3, 1],
            ],
            [
                [
                    $gen([null, null]),
                    $gen([null, null, null]),
                ],
                1,
                [null, null, null],
            ],
            [
                [
                    $gen([null, null]),
                    $gen([null, null, null]),
                ],
                2,
                [null, null],
            ],
            [
                [
                    $gen([null, null]),
                    $gen([null, null, null]),
                ],
                3,
                [],
            ],
            [
                [
                    $gen(['php', 'python', 'c++', 'java', 'c#', 'javascript', 'typescript']),
                    $gen(['php', 'python', 'javascript', 'typescript']),
                    $gen(['php', 'java', 'c#', 'typescript']),
                ],
                2,
                ['php', 'python', 'java', 'typescript', 'c#', 'javascript'],
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
        foreach (Set::partialIntersectionCoercive($minIntersectionCount, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public static function dataProviderForIterators(): array
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
                    $iter([]),
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
                    $iter([]),
                ],
                2,
                [],
            ],
            [
                [
                    $iter([]),
                    $iter([]),
                    $iter([]),
                ],
                3,
                [],
            ],
            [
                [
                    $iter([]),
                    $iter([]),
                    $iter([]),
                ],
                4,
                [],
            ],
            [
                [
                    $iter([1, 2, 3]),
                ],
                1,
                [1, 2, 3],
            ],
            [
                [
                    $iter([1, 2, 3]),
                ],
                2,
                [],
            ],
            [
                [
                    $iter([1, 2, 3]),
                    $iter([2, 3, 4, 5]),
                ],
                1,
                [1, 2, 3, 4, 5],
            ],
            [
                [
                    $iter([1, 2, 3]),
                    $iter([2, 3, 4, 5]),
                ],
                2,
                [2, 3],
            ],
            [
                [
                    $iter([1, 2, 3]),
                    $iter([2, 3, 4, 5]),
                ],
                3,
                [],
            ],
            [
                [
                    $iter([1, 2, 3, 2]),
                    $iter([2, 3, 4, 5, 2, 1]),
                ],
                1,
                [1, 2, 3, 4, 2, 5],
            ],
            [
                [
                    $iter([1, 2, 3, 2]),
                    $iter([2, 3, 4, 5, 2, 1]),
                ],
                2,
                [2, 3, 2, 1],
            ],
            [
                [
                    $iter([1, 2, 3, 2]),
                    $iter([2, 3, 4, 5, 2, 1]),
                ],
                3,
                [],
            ],
            [
                [
                    $iter([1, 2, 3, 2]),
                    $iter([2, 3, 4, 5, 2, 1]),
                    $iter([1, 2, 3]),
                ],
                1,
                [1, 2, 3, 4, 2, 5],
            ],
            [
                [
                    $iter([1, 2, 3, 2]),
                    $iter([2, 3, 4, 5, 2, 1]),
                    $iter([1, 2, 3]),
                ],
                2,
                [1, 2, 3, 2],
            ],
            [
                [
                    $iter([1, 2, 3, 2]),
                    $iter([2, 3, 4, 5, 2, 1]),
                    $iter([1, 2, 3]),
                ],
                3,
                [2, 3, 1],
            ],
            [
                [
                    $iter([null, null]),
                    $iter([null, null, null]),
                ],
                1,
                [null, null, null],
            ],
            [
                [
                    $iter([null, null]),
                    $iter([null, null, null]),
                ],
                2,
                [null, null],
            ],
            [
                [
                    $iter([null, null]),
                    $iter([null, null, null]),
                ],
                3,
                [],
            ],
            [
                [
                    $iter(['php', 'python', 'c++', 'java', 'c#', 'javascript', 'typescript']),
                    $iter(['php', 'python', 'javascript', 'typescript']),
                    $iter(['php', 'java', 'c#', 'typescript']),
                ],
                2,
                ['php', 'python', 'java', 'typescript', 'c#', 'javascript'],
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
        foreach (Set::partialIntersectionCoercive($minIntersectionCount, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public static function dataProviderForTraversables(): array
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
                    $trav([]),
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
                    $trav([]),
                ],
                2,
                [],
            ],
            [
                [
                    $trav([]),
                    $trav([]),
                    $trav([]),
                ],
                3,
                [],
            ],
            [
                [
                    $trav([]),
                    $trav([]),
                    $trav([]),
                ],
                4,
                [],
            ],
            [
                [
                    $trav([1, 2, 3]),
                ],
                1,
                [1, 2, 3],
            ],
            [
                [
                    $trav([1, 2, 3]),
                ],
                2,
                [],
            ],
            [
                [
                    $trav([1, 2, 3]),
                    $trav([2, 3, 4, 5]),
                ],
                1,
                [1, 2, 3, 4, 5],
            ],
            [
                [
                    $trav([1, 2, 3]),
                    $trav([2, 3, 4, 5]),
                ],
                2,
                [2, 3],
            ],
            [
                [
                    $trav([1, 2, 3]),
                    $trav([2, 3, 4, 5]),
                ],
                3,
                [],
            ],
            [
                [
                    $trav([1, 2, 3, 2]),
                    $trav([2, 3, 4, 5, 2, 1]),
                ],
                1,
                [1, 2, 3, 4, 2, 5],
            ],
            [
                [
                    $trav([1, 2, 3, 2]),
                    $trav([2, 3, 4, 5, 2, 1]),
                ],
                2,
                [2, 3, 2, 1],
            ],
            [
                [
                    $trav([1, 2, 3, 2]),
                    $trav([2, 3, 4, 5, 2, 1]),
                ],
                3,
                [],
            ],
            [
                [
                    $trav([1, 2, 3, 2]),
                    $trav([2, 3, 4, 5, 2, 1]),
                    $trav([1, 2, 3]),
                ],
                1,
                [1, 2, 3, 4, 2, 5],
            ],
            [
                [
                    $trav([1, 2, 3, 2]),
                    $trav([2, 3, 4, 5, 2, 1]),
                    $trav([1, 2, 3]),
                ],
                2,
                [1, 2, 3, 2],
            ],
            [
                [
                    $trav([1, 2, 3, 2]),
                    $trav([2, 3, 4, 5, 2, 1]),
                    $trav([1, 2, 3]),
                ],
                3,
                [2, 3, 1],
            ],
            [
                [
                    $trav([null, null]),
                    $trav([null, null, null]),
                ],
                1,
                [null, null, null],
            ],
            [
                [
                    $trav([null, null]),
                    $trav([null, null, null]),
                ],
                2,
                [null, null],
            ],
            [
                [
                    $trav([null, null]),
                    $trav([null, null, null]),
                ],
                3,
                [],
            ],
            [
                [
                    $trav(['php', 'python', 'c++', 'java', 'c#', 'javascript', 'typescript']),
                    $trav(['php', 'python', 'javascript', 'typescript']),
                    $trav(['php', 'java', 'c#', 'typescript']),
                ],
                2,
                ['php', 'python', 'java', 'typescript', 'c#', 'javascript'],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForMixed
     * @param array<iterable> $iterables
     * @param positive-int $minIntersectionCount
     * @param array $expected
     */
    public function testMixed(array $iterables, int $minIntersectionCount, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::partialIntersectionCoercive($minIntersectionCount, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public static function dataProviderForMixed(): array
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
                    [null, null],
                    $gen([null, 1, null]),
                    $iter([null, 2, 3, null]),
                    $trav([null, 2, 4, 3, null]),
                ],
                1,
                [null, null, 1, 2, 3, 4],
            ],
            [
                [
                    [null, null],
                    $gen([null, 1, null]),
                    $iter([null, 2, 3, null]),
                    $trav([null, 2, 4, 3, null]),
                ],
                2,
                [null, 2, null, 3],
            ],
            [
                [
                    [null, null],
                    $gen([null, 1, null]),
                    $iter([null, 2, 3, null]),
                    $trav([null, 2, 4, 3, null]),
                ],
                3,
                [null, null],
            ],
            [
                [
                    [null, null],
                    $gen([null, 1, null]),
                    $iter([null, 2, 3, null]),
                    $trav([null, 2, 4, 3, null]),
                ],
                4,
                [null, null],
            ],
            [
                [
                    [null, null],
                    $gen([null, 1, null]),
                    $iter([null, 2, 3, null]),
                    $trav([null, 2, 4, 3, null]),
                ],
                5,
                [],
            ],
            [
                [
                    [null, null],
                    $gen([null, null, null]),
                    $iter([null, null, null, null]),
                    $trav([null, null, null, null, null]),
                ],
                1,
                [null, null, null, null, null],
            ],
            [
                [
                    [null, null],
                    $gen([null, null, null]),
                    $iter([null, null, null, null]),
                    $trav([null, null, null, null, null]),
                ],
                2,
                [null, null, null, null],
            ],
            [
                [
                    [null, null],
                    $gen([null, null, null]),
                    $iter([null, null, null, null]),
                    $trav([null, null, null, null, null]),
                ],
                3,
                [null, null, null],
            ],
            [
                [
                    [null, null],
                    $gen([null, null, null]),
                    $iter([null, null, null, null]),
                    $trav([null, null, null, null, null]),
                ],
                4,
                [null, null],
            ],
            [
                [
                    [null, null],
                    $gen([null, null, null]),
                    $iter([null, null, null, null]),
                    $trav([null, null, null, null, null]),
                ],
                5,
                [],
            ],
            [
                [
                    [1, 2],
                    $gen([1, 2, 3]),
                    $iter([1, 2, 3, 4]),
                    $trav([1, 2, 3, 4, 5]),
                ],
                1,
                [1, 2, 3, 4, 5],
            ],
            [
                [
                    [1, 2],
                    $gen([1, 2, 3]),
                    $iter([1, 2, 3, 4]),
                    $trav([1, 2, 3, 4, 5]),
                ],
                2,
                [1, 2, 3, 4],
            ],
            [
                [
                    [1, 2],
                    $gen([1, 2, 3]),
                    $iter([1, 2, 3, 4]),
                    $trav([1, 2, 3, 4, 5]),
                ],
                3,
                [1, 2, 3],
            ],
            [
                [
                    [1, 2],
                    $gen([1, 2, 3]),
                    $iter([1, 2, 3, 4]),
                    $trav([1, 2, 3, 4, 5]),
                ],
                4,
                [1, 2],
            ],
            [
                [
                    [1, 2],
                    $gen([1, 2, 3]),
                    $iter([1, 2, 3, 4]),
                    $trav([1, 2, 3, 4, 5]),
                ],
                5,
                [],
            ],
            [
                [
                    [1, 2, 1, 2],
                    $gen([1, 2, 3, 1]),
                    $iter([1, 2, 3, 4]),
                    $trav([1, 2, 3, 4]),
                ],
                1,
                [1, 2, 1, 3, 2, 4],
            ],
            [
                [
                    [1, 2, 1, 2],
                    $gen([1, 2, 3, 1]),
                    $iter([1, 2, 3, 4]),
                    $trav([1, 2, 3, 4]),
                ],
                2,
                [1, 2, 3, 1, 4],
            ],
            [
                [
                    [1, 2, 1, 2],
                    $gen([1, 2, 3, 1]),
                    $iter([1, 2, 3, 4]),
                    $trav([1, 2, 3, 4]),
                ],
                3,
                [1, 2, 3],
            ],
            [
                [
                    [1, 2, 1, 2],
                    $gen([1, 2, 3, 1]),
                    $iter([1, 2, 3, 4]),
                    $trav([1, 2, 3, 4]),
                ],
                4,
                [1, 2],
            ],
            [
                [
                    [1, 2, 1, 2],
                    $gen([1, 2, 3, 1]),
                    $iter([1, 2, 3, 4]),
                    $trav([1, 2, 3, 4]),
                ],
                5,
                [],
            ],
            [
                [
                    [5, 6, 7, 8],
                    $gen([4, 5, 6, 7]),
                    $iter([3, 4, 5, 6]),
                    $trav([2, 3, 4, 5]),
                ],
                1,
                [5, 4, 3, 2, 6, 7, 8],
            ],
            [
                [
                    [5, 6, 7, 8],
                    $gen([4, 5, 6, 7, 4]),
                    $iter([3, 4, 5, 6, 3, 4]),
                    $trav([2, 3, 4, 5, 2, 3, 4]),
                ],
                1,
                [5, 4, 3, 2, 6, 7, 8, 4, 3, 2],
            ],
            [
                [
                    [1, 2, 1, 2],
                    $gen([1, 2, 3, 1, 2, 3]),
                    $iter([1, 2, 3, 4, 1, 2, 3, 4]),
                    $trav([1, 2, 3, 4, 5, 1, 2, 3, 4, 5]),
                ],
                1,
                [1, 2, 1, 3, 2, 4, 5, 3, 4, 5],
            ],
            [
                [
                    [1, 2, 1, 2],
                    $gen([1, 2, 1, 2]),
                    $iter([1, 2, 1, 2]),
                    $trav([1, 2, 1, 2]),
                ],
                1,
                [1, 2, 1, 2],
            ],
            [
                [
                    [1, 2, 1, 2],
                    $gen([1, 2, 1, 2]),
                    $iter([1, 2, 1, 2]),
                    $trav([1, 2, 1, 2]),
                ],
                2,
                [1, 2, 1, 2],
            ],
            [
                [
                    [1, 2, 1, 2],
                    $gen([1, 2, 1, 2]),
                    $iter([1, 2, 1, 2]),
                    $trav([1, 2, 1, 2]),
                ],
                3,
                [1, 2, 1, 2],
            ],
            [
                [
                    [1, 2, 1, 2],
                    $gen([1, 2, 1, 2]),
                    $iter([1, 2, 1, 2]),
                    $trav([1, 2, 1, 2]),
                ],
                4,
                [1, 2, 1, 2],
            ],
            [
                [
                    [1, 2, 1, 2],
                    $gen([1, 2, 1, 2]),
                    $iter([1, 2, 1, 2]),
                    $trav([1, 2, 1, 2]),
                ],
                5,
                [],
            ],
            [
                [
                    [1, 2, 1, 2],
                    $gen([1, 2, 1, 2, 1, 2]),
                    $iter([1, 2, 1, 2, 1, 2, 1, 2]),
                    $trav([1, 2, 1, 2, 1, 2, 1, 2, 1, 2]),
                ],
                1,
                [1, 2, 1, 2, 1, 2, 1, 2, 1, 2],
            ],
        ];
    }

    /**
     * @test iterator_to_array
     * @dataProvider dataProviderForArray
     * @param array<array> $iterables
     * @param positive-int $minIntersectionCount
     * @param array $expected
     */
    public function testIteratorToArray(array $iterables, int $minIntersectionCount, array $expected): void
    {
        // Given
        $iterator = Set::partialIntersectionCoercive($minIntersectionCount, ...$iterables);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    /**
     * @test partialIntersectionCoercive accepts named arguments that match its internal parameter names
     */
    public function testPartialIntersectionCoerciveAcceptsNamedArgumentsMatchingInternalParameterNames(): void
    {
        // When
        $result = \iterator_to_array(Set::partialIntersectionCoercive(
            2,
            strict: [1, 2, 3],
            filler: ['2', '3', '4'],
        ), false);

        // Then
        $this->assertSame([2, 3], $result);
    }

    /**
     * @test partialIntersectionCoercive throws for a minimum intersection count below 1
     * @dataProvider dataProviderForNonPositiveMinIntersectionCount
     * @param int $minIntersectionCount
     * @param array<iterable> $iterables
     */
    public function testNonPositiveMinIntersectionCountThrows(int $minIntersectionCount, array $iterables): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Minimum intersection count must be ≥ 1. Got {$minIntersectionCount}");

        // When
        foreach (Set::partialIntersectionCoercive($minIntersectionCount, ...$iterables) as $_) {
            break;
        }
    }

    public static function dataProviderForNonPositiveMinIntersectionCount(): array
    {
        return [
            [0, [[1, 2, 3], [2, 3, 4]]],
            [-1, [[1, 2, 3], [2, 3, 4]]],
            [\PHP_INT_MIN, [[1, 2, 3], [2, 3, 4]]],
            [0, [GeneratorFixture::getGenerator([1, 2, 3]), GeneratorFixture::getGenerator([2, 3, 4])]],
            [-1, [GeneratorFixture::getGenerator([1, 2, 3]), GeneratorFixture::getGenerator([2, 3, 4])]],
            [0, [new ArrayIteratorFixture([1, 2, 3]), new ArrayIteratorFixture([2, 3, 4])]],
            [-1, [new ArrayIteratorFixture([1, 2, 3]), new ArrayIteratorFixture([2, 3, 4])]],
            [0, [new IteratorAggregateFixture([1, 2, 3]), new IteratorAggregateFixture([2, 3, 4])]],
            [-1, [new IteratorAggregateFixture([1, 2, 3]), new IteratorAggregateFixture([2, 3, 4])]],
            [0, [[], []]],
            [0, [[1, 2, 3]]],
            [0, []],
        ];
    }

    /**
     * @test partialIntersectionCoercive reads an iterator passed more than once as independent copies of it
     */
    public function testIteratorPassedTwice(): void
    {
        // Given
        $iterator = new ArrayIteratorFixture([1, 2, 2, 3]);

        // When
        $result = \iterator_to_array(Set::partialIntersectionCoercive(2, $iterator, $iterator), false);

        // Then
        $this->assertSame([1, 2, 2, 3], $result);
    }

    /**
     * @test partialIntersectionCoercive reads a generator passed more than once as independent copies of it
     */
    public function testGeneratorPassedTwice(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 2, 3]);

        // When
        $result = \iterator_to_array(Set::partialIntersectionCoercive(2, $generator, $generator), false);

        // Then
        $this->assertSame([1, 2, 2, 3], $result);
    }

    /**
     * @test partialIntersectionCoercive reads an iterator passed around another iterable as independent copies of it
     */
    public function testIteratorPassedTwiceAroundAnotherIterable(): void
    {
        // Given
        $iterator = new ArrayIteratorFixture([1, 2, 2, 3]);

        // When
        $result = \iterator_to_array(Set::partialIntersectionCoercive(2, $iterator, [2, 3, 4], $iterator), false);

        // Then
        $this->assertSame([1, 2, 2, 3], $result);
    }

    /**
     * @test partialIntersectionCoercive reads a stream passed more than once as independent copies of it
     */
    public function testStreamPassedTwice(): void
    {
        // Given
        $stream = Stream::of(GeneratorFixture::getGenerator([1, 2, 2, 3]));

        // When
        $result = \iterator_to_array(Set::partialIntersectionCoercive(2, $stream, $stream), false);

        // Then
        $this->assertSame([1, 2, 2, 3], $result);
    }

    /**
     * @test partialIntersectionCoercive reads a stream passed around another iterable as independent copies of it
     */
    public function testStreamPassedTwiceAroundAnotherIterable(): void
    {
        // Given
        $stream = Stream::of(GeneratorFixture::getGenerator([1, 2, 2, 3]));

        // When
        $result = \iterator_to_array(Set::partialIntersectionCoercive(2, $stream, [2, 3, 4], $stream), false);

        // Then
        $this->assertSame([1, 2, 2, 3], $result);
    }
}

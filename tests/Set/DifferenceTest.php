<?php

declare(strict_types=1);

namespace IterTools\Tests\Set;

use IterTools\Set;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class DifferenceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArraySets
     * @dataProvider dataProviderForArrayMultisets
     * @param        iterable<mixed> $a
     * @param        array<iterable<mixed>> $iterables
     * @param        array $expected
     */
    public function testArray(iterable $a, array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::difference($a, ...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public static function dataProviderForArraySets(): array
    {
        return [
            [
                [],
                [],
                [],
            ],
            [
                [],
                [[]],
                [],
            ],
            [
                [],
                [[], []],
                [],
            ],
            [
                [1, 2, 3],
                [],
                [1, 2, 3],
            ],
            [
                [1, 2, 3],
                [[]],
                [1, 2, 3],
            ],
            [
                [],
                [[1, 2, 3]],
                [],
            ],
            [
                [2],
                [[2]],
                [],
            ],
            [
                [1, 2, 3, 4, 5],
                [[1, 2, 3, 4, 5]],
                [],
            ],
            [
                [1, 2, 3, 4, 5],
                [[3, 4, 5, 6, 7]],
                [1, 2],
            ],
            [
                [1, 2, 3, 4, 5],
                [[1, 2, 3, '4', '5']],
                [4, 5],
            ],
            [
                ['1', '2', '3', 4, 5],
                [[1, 2, 3, '4', '5']],
                ['1', '2', '3', 4, 5],
            ],
            [
                [1, 2, 3, 4, 5],
                [[3, 4], [5, 6]],
                [1, 2],
            ],
            [
                [1, 2, 3, 4, 5],
                [[2, 4], [1, 3]],
                [5],
            ],
            [
                [null, 1, 2, null],
                [[null, 3, null]],
                [1, 2],
            ],
            [
                ['1', 2, '3.3', true, false],
                [[true, '2', 3.3, '4', '5']],
                ['1', 2, '3.3', false],
            ],
        ];
    }

    public static function dataProviderForArrayMultisets(): array
    {
        return [
            [
                [2, 2],
                [[]],
                [2, 2],
            ],
            [
                [2, 2],
                [[2]],
                [2],
            ],
            [
                [2, 2],
                [[2, 2]],
                [],
            ],
            [
                [2, 2, 3],
                [[2, 2, 4]],
                [3],
            ],
            [
                [1, 1, 2, 2, 1, 1],
                [[2, 2, 1, 1, 2, 2]],
                [1, 1],
            ],
            [
                [1, 1, 2, 2, 1, 1],
                [[2, 2, 1, 1, '2', '2']],
                [1, 1],
            ],
            [
                [1, 1, 2, 2, 1, 1],
                [[2, 2, '1', '1', 2, 2]],
                [1, 1, 1, 1],
            ],
            [
                [1, 1, 1, 1, 1],
                [[1, 1], [1]],
                [1, 1],
            ],
            [
                [1, 1, 1, 1, 1],
                [[1, 2, 3, 4, 5, 1, 2, 3, 4, 5]],
                [1, 1, 1],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $a
     * @param        array<\Generator> $iterables
     * @param        array $expected
     */
    public function testGenerators(\Generator $a, array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::difference($a, ...$iterables) as $datum) {
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
                $gen([]),
                [],
                [],
            ],
            [
                $gen([]),
                [$gen([])],
                [],
            ],
            [
                $gen([1, 2, 3]),
                [],
                [1, 2, 3],
            ],
            [
                $gen([1, 2, 3]),
                [$gen([])],
                [1, 2, 3],
            ],
            [
                $gen([]),
                [$gen([1, 2, 3])],
                [],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                [$gen([1, 2, 3, 4, 5])],
                [],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                [$gen([3, 4, 5, 6, 7])],
                [1, 2],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                [$gen([1, 2, 3, '4', '5'])],
                [4, 5],
            ],
            [
                $gen(['1', '2', '3', 4, 5]),
                [$gen([1, 2, 3, '4', '5'])],
                ['1', '2', '3', 4, 5],
            ],
            [
                $gen([1, 1, 2, 2, 1, 1]),
                [$gen([2, 2, 1, 1, 2, 2])],
                [1, 1],
            ],
            [
                $gen([1, 1, 2, 2, 1, 1]),
                [$gen([2, 2, '1', '1', 2, 2])],
                [1, 1, 1, 1],
            ],
            [
                $gen([1, 1, 1, 1, 1]),
                [$gen([1, 2, 3, 4, 5, 1, 2, 3, 4, 5])],
                [1, 1, 1],
            ],
            [
                $gen([null, 1, 2, null]),
                [$gen([null, 3, null])],
                [1, 2],
            ],
            [
                $gen(['1', 2, '3.3', true, false]),
                [$gen([true, '2', 3.3, '4', '5'])],
                ['1', 2, '3.3', false],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param        \Iterator $a
     * @param        array<\Iterator> $iterables
     * @param        array $expected
     */
    public function testIterators(\Iterator $a, array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::difference($a, ...$iterables) as $datum) {
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
                $iter([]),
                [],
                [],
            ],
            [
                $iter([]),
                [$iter([])],
                [],
            ],
            [
                $iter([1, 2, 3]),
                [],
                [1, 2, 3],
            ],
            [
                $iter([1, 2, 3]),
                [$iter([])],
                [1, 2, 3],
            ],
            [
                $iter([]),
                [$iter([1, 2, 3])],
                [],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                [$iter([1, 2, 3, 4, 5])],
                [],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                [$iter([3, 4, 5, 6, 7])],
                [1, 2],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                [$iter([1, 2, 3, '4', '5'])],
                [4, 5],
            ],
            [
                $iter(['1', '2', '3', 4, 5]),
                [$iter([1, 2, 3, '4', '5'])],
                ['1', '2', '3', 4, 5],
            ],
            [
                $iter([1, 1, 2, 2, 1, 1]),
                [$iter([2, 2, 1, 1, 2, 2])],
                [1, 1],
            ],
            [
                $iter([1, 1, 2, 2, 1, 1]),
                [$iter([2, 2, '1', '1', 2, 2])],
                [1, 1, 1, 1],
            ],
            [
                $iter([1, 1, 1, 1, 1]),
                [$iter([1, 2, 3, 4, 5, 1, 2, 3, 4, 5])],
                [1, 1, 1],
            ],
            [
                $iter([null, 1, 2, null]),
                [$iter([null, 3, null])],
                [1, 2],
            ],
            [
                $iter(['1', 2, '3.3', true, false]),
                [$iter([true, '2', 3.3, '4', '5'])],
                ['1', 2, '3.3', false],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $a
     * @param        array<\Traversable> $iterables
     * @param        array $expected
     */
    public function testTraversables(\Traversable $a, array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::difference($a, ...$iterables) as $datum) {
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
                $trav([]),
                [],
                [],
            ],
            [
                $trav([]),
                [$trav([])],
                [],
            ],
            [
                $trav([1, 2, 3]),
                [],
                [1, 2, 3],
            ],
            [
                $trav([1, 2, 3]),
                [$trav([])],
                [1, 2, 3],
            ],
            [
                $trav([]),
                [$trav([1, 2, 3])],
                [],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                [$trav([1, 2, 3, 4, 5])],
                [],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                [$trav([3, 4, 5, 6, 7])],
                [1, 2],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                [$trav([1, 2, 3, '4', '5'])],
                [4, 5],
            ],
            [
                $trav(['1', '2', '3', 4, 5]),
                [$trav([1, 2, 3, '4', '5'])],
                ['1', '2', '3', 4, 5],
            ],
            [
                $trav([1, 1, 2, 2, 1, 1]),
                [$trav([2, 2, 1, 1, 2, 2])],
                [1, 1],
            ],
            [
                $trav([1, 1, 2, 2, 1, 1]),
                [$trav([2, 2, '1', '1', 2, 2])],
                [1, 1, 1, 1],
            ],
            [
                $trav([1, 1, 1, 1, 1]),
                [$trav([1, 2, 3, 4, 5, 1, 2, 3, 4, 5])],
                [1, 1, 1],
            ],
            [
                $trav([null, 1, 2, null]),
                [$trav([null, 3, null])],
                [1, 2],
            ],
            [
                $trav(['1', 2, '3.3', true, false]),
                [$trav([true, '2', 3.3, '4', '5'])],
                ['1', 2, '3.3', false],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForMixed
     * @param        iterable<mixed> $a
     * @param        array<iterable<mixed>> $iterables
     * @param        array $expected
     */
    public function testMixed(iterable $a, array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::difference($a, ...$iterables) as $datum) {
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
                [],
                [$gen([]), $iter([]), $trav([])],
                [],
            ],
            [
                [1, 2, 3, 4, 5],
                [$gen([2, 3]), $iter([4])],
                [1, 5],
            ],
            [
                [1, 1, 1],
                [$gen([1, 2, 3, 1, 2, 3]), $iter([3, 3, 3, 1]), $trav([])],
                [],
            ],
            [
                [1, 1, 1, 1, 1],
                [$gen([1, 1, 1, 1, '1']), $iter([]), $trav([])],
                [1],
            ],
        ];
    }

    /**
     * @test         iterator_to_array
     * @dataProvider dataProviderForArraySets
     * @dataProvider dataProviderForArrayMultisets
     * @param        iterable<mixed> $a
     * @param        array<iterable<mixed>> $iterables
     * @param        array $expected
     */
    public function testIteratorToArray(iterable $a, array $iterables, array $expected): void
    {
        // Given
        $iterator = Set::difference($a, ...$iterables);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Set;

use IterTools\Set;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class UnionTest extends \PHPUnit\Framework\TestCase
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
        foreach (Set::union(...$iterables) as $datum) {
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
                [[]],
                [],
            ],
            [
                [[], []],
                [],
            ],
            [
                [[], [], []],
                [],
            ],
            [
                [[1]],
                [1],
            ],
            [
                [[1, 2]],
                [1, 2],
            ],
            [
                [[1, 2], [2, 3]],
                [1, 2, 3],
            ],
            [
                [[1, 2], [3, 4]],
                [1, 2, 3, 4],
            ],
            [
                [[1, 2], [3, 4], [4, 5]],
                [1, 2, 3, 4, 5],
            ],
            [
                [[1, 2], [3, 4], [5, 6]],
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    [1, 2.2, '3', true, null, [1], (object)[1]],
                    [1.0, 2.2, 3, false, INF, [1], (object)[2]],
                ],
                [1, 1.0, 2.2, '3', 3, true, false, null, INF, [1], (object)[1], (object)[2]],
            ],
        ];
    }

    public function dataProviderForArrayMultisets(): array
    {
        return [
            [
                [[], [1, 1]],
                [1, 1],
            ],
            [
                [[1], [1, 1]],
                [1, 1],
            ],
            [
                [[1, 1], [1, 1]],
                [1, 1],
            ],
            [
                [[1, 1, 1], [1, 1]],
                [1, 1, 1],
            ],
            [
                [[1, 2], [1, 1]],
                [1, 1, 2],
            ],
            [
                [[1, 2], [3, 3]],
                [1, 2, 3, 3],
            ],
            [
                [[1, 2], [3, 3], [4, 5, 5]],
                [1, 2, 3, 3, 4, 5, 5],
            ],
            [
                [[1], [1, 1, 2], [1, 1, 1, 3]],
                [1, 1, 1, 2, 3],
            ],
            [
                [
                    [1, 1, 2.2, '3', true, null, [1], [1], (object)[1]],
                    [1.0, 1.0, 2.2, 3, false, INF, [1], (object)[2]],
                ],
                [1, 1, 1.0, 1.0, 2.2, '3', 3, true, false, null, INF, [1], [1], (object)[1], (object)[2]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGeneratorsSets
     * @dataProvider dataProviderForGeneratorsMultisets
     * @param        array<\Generator> $iterables
     * @param        array $expected
     */
    public function testGenerators(array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::union(...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public function dataProviderForGeneratorsSets(): array
    {
        $gen = fn ($data) => GeneratorFixture::getGenerator($data);

        return [
            [
                [],
                [],
            ],
            [
                [$gen([])],
                [],
            ],
            [
                [$gen([]), $gen([])],
                [],
            ],
            [
                [$gen([]), $gen([]), $gen([])],
                [],
            ],
            [
                [$gen([1])],
                [1],
            ],
            [
                [$gen([1, 2])],
                [1, 2],
            ],
            [
                [$gen([1, 2]), $gen([2, 3])],
                [1, 2, 3],
            ],
            [
                [$gen([1, 2]), $gen([3, 4])],
                [1, 2, 3, 4],
            ],
            [
                [$gen([1, 2]), $gen([3, 4]), $gen([4, 5])],
                [1, 2, 3, 4, 5],
            ],
            [
                [$gen([1, 2]), $gen([3, 4]), $gen([5, 6])],
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    $gen([1, 2.2, '3', true, null, [1], (object)[1]]),
                    $gen([1.0, 2.2, 3, false, INF, [1], (object)[2]]),
                ],
                [1, 1.0, 2.2, '3', 3, true, false, null, INF, [1], (object)[1], (object)[2]],
            ],
        ];
    }

    public function dataProviderForGeneratorsMultisets(): array
    {
        $gen = fn ($data) => GeneratorFixture::getGenerator($data);

        return [
            [
                [$gen([]), $gen([1, 1])],
                [1, 1],
            ],
            [
                [$gen([1]), $gen([1, 1])],
                [1, 1],
            ],
            [
                [$gen([1, 1]), $gen([1, 1])],
                [1, 1],
            ],
            [
                [$gen([1, 1, 1]), $gen([1, 1])],
                [1, 1, 1],
            ],
            [
                [$gen([1, 2]), $gen([1, 1])],
                [1, 1, 2],
            ],
            [
                [$gen([1, 2]), $gen([3, 3])],
                [1, 2, 3, 3],
            ],
            [
                [$gen([1, 2]), $gen([3, 3]), $gen([4, 5, 5])],
                [1, 2, 3, 3, 4, 5, 5],
            ],
            [
                [$gen([1]), $gen([1, 1, 2]), $gen([1, 1, 1, 3])],
                [1, 1, 1, 2, 3],
            ],
            [
                [
                    $gen([1, 1, 2.2, '3', true, null, [1], [1], (object)[1]]),
                    $gen([1.0, 1.0, 2.2, 3, false, INF, [1], (object)[2]]),
                ],
                [1, 1, 1.0, 1.0, 2.2, '3', 3, true, false, null, INF, [1], [1], (object)[1], (object)[2]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIteratorsSets
     * @dataProvider dataProviderForIteratorsMultisets
     * @param        array<\Iterator> $iterables
     * @param        array $expected
     */
    public function testIterators(array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::union(...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public function dataProviderForIteratorsSets(): array
    {
        $iter = fn ($data) => new ArrayIteratorFixture($data);

        return [
            [
                [],
                [],
            ],
            [
                [$iter([])],
                [],
            ],
            [
                [$iter([]), $iter([])],
                [],
            ],
            [
                [$iter([]), $iter([]), $iter([])],
                [],
            ],
            [
                [$iter([1])],
                [1],
            ],
            [
                [$iter([1, 2])],
                [1, 2],
            ],
            [
                [$iter([1, 2]), $iter([2, 3])],
                [1, 2, 3],
            ],
            [
                [$iter([1, 2]), $iter([3, 4])],
                [1, 2, 3, 4],
            ],
            [
                [$iter([1, 2]), $iter([3, 4]), $iter([4, 5])],
                [1, 2, 3, 4, 5],
            ],
            [
                [$iter([1, 2]), $iter([3, 4]), $iter([5, 6])],
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    $iter([1, 2.2, '3', true, null, [1], (object)[1]]),
                    $iter([1.0, 2.2, 3, false, INF, [1], (object)[2]]),
                ],
                [1, 1.0, 2.2, '3', 3, true, false, null, INF, [1], (object)[1], (object)[2]],
            ],
        ];
    }

    public function dataProviderForIteratorsMultisets(): array
    {
        $iter = fn ($data) => new ArrayIteratorFixture($data);

        return [
            [
                [$iter([]), $iter([1, 1])],
                [1, 1],
            ],
            [
                [$iter([1]), $iter([1, 1])],
                [1, 1],
            ],
            [
                [$iter([1, 1]), $iter([1, 1])],
                [1, 1],
            ],
            [
                [$iter([1, 1, 1]), $iter([1, 1])],
                [1, 1, 1],
            ],
            [
                [$iter([1, 2]), $iter([1, 1])],
                [1, 1, 2],
            ],
            [
                [$iter([1, 2]), $iter([3, 3])],
                [1, 2, 3, 3],
            ],
            [
                [$iter([1, 2]), $iter([3, 3]), $iter([4, 5, 5])],
                [1, 2, 3, 3, 4, 5, 5],
            ],
            [
                [$iter([1]), $iter([1, 1, 2]), $iter([1, 1, 1, 3])],
                [1, 1, 1, 2, 3],
            ],
            [
                [
                    $iter([1, 1, 2.2, '3', true, null, [1], [1], (object)[1]]),
                    $iter([1.0, 1.0, 2.2, 3, false, INF, [1], (object)[2]]),
                ],
                [1, 1, 1.0, 1.0, 2.2, '3', 3, true, false, null, INF, [1], [1], (object)[1], (object)[2]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversablesSets
     * @dataProvider dataProviderForTraversablesMultisets
     * @param        array<\Traversable> $iterables
     * @param        array $expected
     */
    public function testTraversables(array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::union(...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public function dataProviderForTraversablesSets(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                [],
                [],
            ],
            [
                [$trav([])],
                [],
            ],
            [
                [$trav([]), $trav([])],
                [],
            ],
            [
                [$trav([]), $trav([]), $trav([])],
                [],
            ],
            [
                [$trav([1])],
                [1],
            ],
            [
                [$trav([1, 2])],
                [1, 2],
            ],
            [
                [$trav([1, 2]), $trav([2, 3])],
                [1, 2, 3],
            ],
            [
                [$trav([1, 2]), $trav([3, 4])],
                [1, 2, 3, 4],
            ],
            [
                [$trav([1, 2]), $trav([3, 4]), $trav([4, 5])],
                [1, 2, 3, 4, 5],
            ],
            [
                [$trav([1, 2]), $trav([3, 4]), $trav([5, 6])],
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    $trav([1, 2.2, '3', true, null, [1], (object)[1]]),
                    $trav([1.0, 2.2, 3, false, INF, [1], (object)[2]]),
                ],
                [1, 1.0, 2.2, '3', 3, true, false, null, INF, [1], (object)[1], (object)[2]],
            ],
        ];
    }

    public function dataProviderForTraversablesMultisets(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                [$trav([]), $trav([1, 1])],
                [1, 1],
            ],
            [
                [$trav([1]), $trav([1, 1])],
                [1, 1],
            ],
            [
                [$trav([1, 1]), $trav([1, 1])],
                [1, 1],
            ],
            [
                [$trav([1, 1, 1]), $trav([1, 1])],
                [1, 1, 1],
            ],
            [
                [$trav([1, 2]), $trav([1, 1])],
                [1, 1, 2],
            ],
            [
                [$trav([1, 2]), $trav([3, 3])],
                [1, 2, 3, 3],
            ],
            [
                [$trav([1, 2]), $trav([3, 3]), $trav([4, 5, 5])],
                [1, 2, 3, 3, 4, 5, 5],
            ],
            [
                [$trav([1]), $trav([1, 1, 2]), $trav([1, 1, 1, 3])],
                [1, 1, 1, 2, 3],
            ],
            [
                [
                    $trav([1, 1, 2.2, '3', true, null, [1], [1], (object)[1]]),
                    $trav([1.0, 1.0, 2.2, 3, false, INF, [1], (object)[2]]),
                ],
                [1, 1, 1.0, 1.0, 2.2, '3', 3, true, false, null, INF, [1], [1], (object)[1], (object)[2]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForMixedSets
     * @dataProvider dataProviderForMixedMultisets
     * @param        array<iterable> $iterables
     * @param        array $expected
     */
    public function testMixed(array $iterables, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Set::union(...$iterables) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public function dataProviderForMixedSets(): array
    {
        $gen = fn ($data) => GeneratorFixture::getGenerator($data);
        $iter = fn ($data) => new ArrayIteratorFixture($data);
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                [[], $gen([]), $trav([]), $trav([])],
                [],
            ],
            [
                [[1], $gen([1, 2]), $iter([3, 4]), $trav([4, 5])],
                [1, 2, 3, 4, 5],
            ],
            [
                [$trav([1, 2]), $trav([3, 4]), $trav([5, 6]), [7, 8]],
                [1, 2, 3, 4, 5, 6, 7, 8],
            ],
            [
                [
                    [1, 2.2, '3', true, null, [1]],
                    $gen([1, 2.2, '3', true, null, [1], (object)[1]]),
                    $iter([1.0, 2.2, 3, false, INF, [1], (object)[2]]),
                    $trav([1.0, 2.2, 3, false, INF, [1]]),
                ],
                [1, 1.0, 2.2, '3', 3, true, false, null, INF, [1], (object)[1], (object)[2]],
            ],
        ];
    }

    public function dataProviderForMixedMultisets(): array
    {
        $gen = fn ($data) => GeneratorFixture::getGenerator($data);
        $iter = fn ($data) => new ArrayIteratorFixture($data);
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                [[], $gen([]), $iter([]), $trav([1, 1])],
                [1, 1],
            ],
            [
                [[1, 1], $gen([1, 1]), $iter([1, 1]), $trav([1, 1])],
                [1, 1],
            ],
            [
                [[1], $gen([]), $iter([1, 1, 1]), $trav([1, 1])],
                [1, 1, 1],
            ],
            [
                [[1, 1], $gen([1, 2]), $iter([3, 3]), $trav([4, 5, 5])],
                [1, 1, 2, 3, 3, 4, 5, 5],
            ],
            [
                [[1, 2], $gen([1]), $iter([1, 1, 2]), $trav([1, 1, 1, 3])],
                [1, 1, 1, 2, 3],
            ],
            [
                [
                    [1, 1, 2.2, '3', true, null, [1], [1]],
                    $gen([1, 1, 2.2, '3', true, null, [1], [1], (object)[1]]),
                    $iter([1.0, 1.0, 2.2, 3, false, INF, [1], (object)[2]]),
                    $trav([1.0, 1.0, 2.2, 3, false, INF, [1]]),
                ],
                [1, 1, 1.0, 1.0, 2.2, '3', 3, true, false, null, INF, [1], [1], (object)[1], (object)[2]],
            ],
        ];
    }
}

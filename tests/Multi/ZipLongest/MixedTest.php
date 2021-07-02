<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipLongest;

use IterTools\Multi;
use IterTools\Tests\Fixture;

class MixedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zipLongest with three different iterables of the same size
     * @dataProvider dataProviderForZipLongestThreeIterablesSameSize
     * @param        array        $array
     * @param        \Iterator    $iter
     * @param        \Traversable $traversable
     * @param        array        $expected
     */
    public function testZipLongestThreeIterablesSameSize(array $array, \Iterator $iter, \Traversable $traversable, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zipLongest($array, $iter, $traversable) as [$value1, $value2, $value3]) {
            $result[] = [$value1, $value2, $value3];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipLongestThreeIterablesSameSize(): array
    {
        return [
            [
                [],
                new Fixture\ArrayIteratorFixture([]),
                new Fixture\IteratorAggregateFixture([]),
                [],
            ],
            [
                [1],
                new Fixture\ArrayIteratorFixture([2]),
                new Fixture\IteratorAggregateFixture([3]),
                [[1, 2, 3]],
            ],
            [
                [1, 2],
                new Fixture\ArrayIteratorFixture([3, 4]),
                new Fixture\IteratorAggregateFixture([5, 6]),
                [[1, 3, 5], [2, 4, 6]],
            ],
            [
                [1, 2, 3],
                new Fixture\ArrayIteratorFixture([4, 5, 6]),
                new Fixture\IteratorAggregateFixture([7, 8, 9]),
                [[1, 4, 7], [2, 5, 8], [3, 6, 9]],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                new Fixture\ArrayIteratorFixture([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                new Fixture\IteratorAggregateFixture([0, 9, 8, 7, 6, 5, 4, 3, 2]),
                [[1, 4, 0], [2, 5, 9], [3, 6, 8], [4, 7, 7], [5, 8, 6], [6, 9, 5], [7, 1, 4], [8, 2, 3], [9, 3, 2]],
            ],
        ];
    }

    /**
     * @test         zipLongest with three different iterables of differentSizes
     * @dataProvider dataProviderForZipLongestThreeIterablesDifferentSize
     * @param        array        $array
     * @param        \Iterator    $iter
     * @param        \Traversable $traversable
     * @param        array        $expected
     */
    public function testZipLongestThreeIterablesDifferentSize(array $array, \Iterator $iter, \Traversable $traversable, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zipLongest($array, $iter, $traversable) as [$value1, $value2, $value3]) {
            $result[] = [$value1, $value2, $value3];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipLongestThreeIterablesDifferentSize(): array
    {
        return [
            [
                [],
                new Fixture\ArrayIteratorFixture([1]),
                new Fixture\IteratorAggregateFixture([1, 2]),
                [[null, 1, 1], [null, null, 2]],
            ],
            [
                [1],
                new Fixture\ArrayIteratorFixture([2, 2]),
                new Fixture\IteratorAggregateFixture([3, 3, 3]),
                [[1, 2, 3], [null, 2, 3], [null, null, 3]],
            ],
            [
                [1, 2, 3],
                new Fixture\ArrayIteratorFixture([3, 4]),
                new Fixture\IteratorAggregateFixture([5, 6, 7]),
                [[1, 3, 5], [2, 4, 6], [3, null, 7]],
            ],
            [
                [1, 2, 3],
                new Fixture\ArrayIteratorFixture([4, 5, 6]),
                new Fixture\IteratorAggregateFixture([7]),
                [[1, 4, 7], [2, 5, null], [3, 6, null]],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                new Fixture\ArrayIteratorFixture([4, 5, 6, 7, 8, 9, 1, 2]),
                new Fixture\IteratorAggregateFixture([0, 9, 8, 7, 6, 5, 4, 3, 2]),
                [[1, 4, 0], [2, 5, 9], [3, 6, 8], [4, 7, 7], [5, 8, 6], [6, 9, 5], [7, 1, 4], [8, 2, 3], [9, null, 2]],
            ],
        ];
    }
}

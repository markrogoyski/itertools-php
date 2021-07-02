<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipLongest;

use IterTools\Multi;
use IterTools\Tests\Fixture;

class TraversableTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zipLongest with two traversable objects of the same size
     * @dataProvider dataProviderForZipLongestTwoTraversableSameSize
     * @param        \Traversable $iter1
     * @param        \Traversable $iter2
     * @param        array     $expected
     */
    public function testZipLongestTwoIteratorSameSize(\Traversable $iter1, \Traversable $iter2, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zipLongest($iter1, $iter2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipLongestTwoTraversableSameSize(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([]),
                new Fixture\IteratorAggregateFixture([]),
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([1]),
                new Fixture\IteratorAggregateFixture([2]),
                [[1, 2]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2]),
                new Fixture\IteratorAggregateFixture([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                new Fixture\IteratorAggregateFixture([4, 5, 6]),
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                new Fixture\IteratorAggregateFixture([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 3]],
            ],
        ];
    }

    /**
     * @test         zipLongest with two traversable objects of the different sizes
     * @dataProvider dataProviderForZipLongestTwoTraversableDifferentSize
     * @param        \Traversable $iter1
     * @param        \Traversable $iter2
     * @param        array     $expected
     */
    public function testZipLongestTwoTraversableDifferentSize(\Traversable $iter1, \Traversable $iter2, array $expected): void
    {
        // Given
        $result = [];

        foreach (Multi::zipLongest($iter1, $iter2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipLongestTwoTraversableDifferentSize(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([1]),
                new Fixture\IteratorAggregateFixture([]),
                [[1, null]],
            ],
            [
                new Fixture\IteratorAggregateFixture([]),
                new Fixture\IteratorAggregateFixture([2]),
                [[null, 2]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2]),
                new Fixture\IteratorAggregateFixture([4]),
                [[1, 4], [2, null]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1]),
                new Fixture\IteratorAggregateFixture([4, 5]),
                [[1, 4], [null, 5]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                new Fixture\IteratorAggregateFixture([4, 5]),
                [[1, 4], [2, 5], [3, null]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2]),
                new Fixture\IteratorAggregateFixture([4, 5, 6]),
                [[1, 4], [2, 5], [null, 6]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                new Fixture\IteratorAggregateFixture([4]),
                [[1, 4], [2, null], [3, null]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1]),
                new Fixture\IteratorAggregateFixture([4, 5, 6]),
                [[1, 4], [null, 5], [null, 6]],
            ],
        ];
    }
}

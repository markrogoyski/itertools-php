<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipEqual;

use IterTools\Multi;
use IterTools\Tests\Fixture;

class MixedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zip with three different iterables of the same size
     * @dataProvider dataProviderForZipStrictThreeIterablesSameSize
     * @param        array        $array
     * @param        \Iterator    $iter
     * @param        \Traversable $traversable
     * @param        array        $expected
     */
    public function testZipStrictThreeIterablesSameSize(
        array $array,
        \Iterator $iter,
        \Traversable $traversable,
        array $expected
    ): void {
        // Given
        $result = [];

        // When
        foreach (Multi::zipEqual($array, $iter, $traversable) as [$value1, $value2, $value3]) {
            $result[] = [$value1, $value2, $value3];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipStrictThreeIterablesSameSize(): array
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
     * @test         zip with three different iterables of differentSizes
     * @dataProvider dataProviderForZipStrictThreeIterablesDifferentSize
     * @param        array        $array
     * @param        \Iterator    $iter
     * @param        \Traversable $traversable
     * @param        array        $expected
     */
    public function testZipStrictThreeIterablesDifferentSize(
        array $array,
        \Iterator $iter,
        \Traversable $traversable,
        array $expected
    ): void {
        // Given
        $result = [];

        // When
        try {
            foreach (Multi::zipEqual($array, $iter, $traversable) as [$value1, $value2, $value3]) {
                $result[] = [$value1, $value2, $value3];
            }

            $this->fail();
        } catch (\LengthException $e) {
            // Then
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @return array
     */
    public function dataProviderForZipStrictThreeIterablesDifferentSize(): array
    {
        return [
            [
                [],
                new Fixture\ArrayIteratorFixture([1]),
                new Fixture\IteratorAggregateFixture([1, 2]),
                [],
            ],
            [
                [1],
                new Fixture\ArrayIteratorFixture([2, 2]),
                new Fixture\IteratorAggregateFixture([3, 3, 3]),
                [[1, 2, 3]],
            ],
            [
                [1, 2, 3],
                new Fixture\ArrayIteratorFixture([3, 4]),
                new Fixture\IteratorAggregateFixture([5, 6, 7]),
                [[1, 3, 5], [2, 4, 6]],
            ],
            [
                [1, 2, 3],
                new Fixture\ArrayIteratorFixture([4, 5, 6]),
                new Fixture\IteratorAggregateFixture([7]),
                [[1, 4, 7]],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                new Fixture\ArrayIteratorFixture([4, 5, 6, 7, 8, 9, 1, 2]),
                new Fixture\IteratorAggregateFixture([0, 9, 8, 7, 6, 5, 4, 3, 2]),
                [[1, 4, 0], [2, 5, 9], [3, 6, 8], [4, 7, 7], [5, 8, 6], [6, 9, 5], [7, 1, 4], [8, 2, 3]],
            ],
        ];
    }
}

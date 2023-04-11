<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipFilled;

use IterTools\Multi;
use IterTools\Tests\Fixture;

class MixedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zipFilled with three different iterables of the same size
     * @dataProvider dataProviderForZipFilledThreeIterablesSameSize
     * @param        mixed        $filler
     * @param        array        $array
     * @param        \Iterator    $iter
     * @param        \Traversable $traversable
     * @param        array        $expected
     */
    public function testZipFilledThreeIterablesSameSize($filler, array $array, \Iterator $iter, \Traversable $traversable, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zipFilled($filler, $array, $iter, $traversable) as [$value1, $value2, $value3]) {
            $result[] = [$value1, $value2, $value3];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipFilledThreeIterablesSameSize(): array
    {
        return [
            [
                'filler',
                [],
                new Fixture\ArrayIteratorFixture([]),
                new Fixture\IteratorAggregateFixture([]),
                [],
            ],
            [
                'filler',
                [1],
                new Fixture\ArrayIteratorFixture([2]),
                new Fixture\IteratorAggregateFixture([3]),
                [[1, 2, 3]],
            ],
            [
                'filler',
                [1, 2],
                new Fixture\ArrayIteratorFixture([3, 4]),
                new Fixture\IteratorAggregateFixture([5, 6]),
                [[1, 3, 5], [2, 4, 6]],
            ],
            [
                'filler',
                [1, 2, 3],
                new Fixture\ArrayIteratorFixture([4, 5, 6]),
                new Fixture\IteratorAggregateFixture([7, 8, 9]),
                [[1, 4, 7], [2, 5, 8], [3, 6, 9]],
            ],
            [
                'filler',
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                new Fixture\ArrayIteratorFixture([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                new Fixture\IteratorAggregateFixture([0, 9, 8, 7, 6, 5, 4, 3, 2]),
                [[1, 4, 0], [2, 5, 9], [3, 6, 8], [4, 7, 7], [5, 8, 6], [6, 9, 5], [7, 1, 4], [8, 2, 3], [9, 3, 2]],
            ],
        ];
    }

    /**
     * @test         zipFilled with three different iterables of differentSizes
     * @dataProvider dataProviderForZipFilledThreeIterablesDifferentSize
     * @param        mixed        $filler
     * @param        array        $array
     * @param        \Iterator    $iter
     * @param        \Traversable $traversable
     * @param        array        $expected
     */
    public function testZipFilledThreeIterablesDifferentSize($filler, array $array, \Iterator $iter, \Traversable $traversable, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zipFilled($filler, $array, $iter, $traversable) as [$value1, $value2, $value3]) {
            $result[] = [$value1, $value2, $value3];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipFilledThreeIterablesDifferentSize(): array
    {
        return [
            [
                ['f', 'i', 'l', 'l'],
                [],
                new Fixture\ArrayIteratorFixture([1]),
                new Fixture\IteratorAggregateFixture([1, 2]),
                [[['f', 'i', 'l', 'l'], 1, 1], [['f', 'i', 'l', 'l'], ['f', 'i', 'l', 'l'], 2]],
            ],
            [
                ['f', 'i', 'l', 'l'],
                [1],
                new Fixture\ArrayIteratorFixture([2, 2]),
                new Fixture\IteratorAggregateFixture([3, 3, 3]),
                [[1, 2, 3], [['f', 'i', 'l', 'l'], 2, 3], [['f', 'i', 'l', 'l'], ['f', 'i', 'l', 'l'], 3]],
            ],
            [
                ['f', 'i', 'l', 'l'],
                [1, 2, 3],
                new Fixture\ArrayIteratorFixture([3, 4]),
                new Fixture\IteratorAggregateFixture([5, 6, 7]),
                [[1, 3, 5], [2, 4, 6], [3, ['f', 'i', 'l', 'l'], 7]],
            ],
            [
                ['f', 'i', 'l', 'l'],
                [1, 2, 3],
                new Fixture\ArrayIteratorFixture([4, 5, 6]),
                new Fixture\IteratorAggregateFixture([7]),
                [[1, 4, 7], [2, 5, ['f', 'i', 'l', 'l']], [3, 6, ['f', 'i', 'l', 'l']]],
            ],
            [
                ['f', 'i', 'l', 'l'],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                new Fixture\ArrayIteratorFixture([4, 5, 6, 7, 8, 9, 1, 2]),
                new Fixture\IteratorAggregateFixture([0, 9, 8, 7, 6, 5, 4, 3, 2]),
                [[1, 4, 0], [2, 5, 9], [3, 6, 8], [4, 7, 7], [5, 8, 6], [6, 9, 5], [7, 1, 4], [8, 2, 3], [9, ['f', 'i', 'l', 'l'], 2]],
            ],
        ];
    }
}

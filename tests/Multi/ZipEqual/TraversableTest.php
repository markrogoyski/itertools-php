<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipEqual;

use IterTools\Multi;
use IterTools\Tests\Fixture;

class TraversableTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zip with two traversable objects of the same size
     * @dataProvider dataProviderForZipStrictTwoTraversableSameSize
     * @param        \Traversable $iter1
     * @param        \Traversable $iter2
     * @param        array     $expected
     */
    public function testZipStrictTwoIteratorSameSize(\Traversable $iter1, \Traversable $iter2, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zipEqual($iter1, $iter2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipStrictTwoTraversableSameSize(): array
    {
        $trav = static function (array $input) {
            return new Fixture\IteratorAggregateFixture($input);
        };

        return [
            [
                $trav([]),
                $trav([]),
                [],
            ],
            [
                $trav([1]),
                $trav([2]),
                [[1, 2]],
            ],
            [
                $trav([1, 2]),
                $trav([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                $trav([1, 2, 3]),
                $trav([4, 5, 6]),
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                $trav([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 3]],
            ],
            [
                $trav(['a', 'b', 'c', 'd']),
                $trav(['one', 'two', 'three', 'four']),
                [['a', 'one'], ['b', 'two'], ['c', 'three'], ['d', 'four']],
            ],
        ];
    }

    /**
     * @test         zip with two traversable objects of the different sizes
     * @dataProvider dataProviderForZipStrictTwoTraversableDifferentSize
     * @param        \Traversable $iter1
     * @param        \Traversable $iter2
     * @param        array     $expected
     */
    public function testZipStrictTwoTraversableDifferentSize(
        \Traversable $iter1,
        \Traversable $iter2,
        array $expected
    ): void {
        // Given
        $result = [];

        // When
        try {
            foreach (Multi::zipEqual($iter1, $iter2) as [$value1, $value2]) {
                $result[] = [$value1, $value2];
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
    public function dataProviderForZipStrictTwoTraversableDifferentSize(): array
    {
        $trav = static function (array $input) {
            return new Fixture\IteratorAggregateFixture($input);
        };

        return [
            [
                $trav([1]),
                $trav([]),
                [],
            ],
            [
                $trav([]),
                $trav([2]),
                [],
            ],
            [
                $trav([1, 2]),
                $trav([4]),
                [[1, 4]],
            ],
            [
                $trav([1]),
                $trav([4, 5]),
                [[1, 4]],
            ],
            [
                $trav([1, 2, 3]),
                $trav([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                $trav([1, 2]),
                $trav([4, 5, 6]),
                [[1, 4], [2, 5]],
            ],
            [
                $trav([1, 2, 3]),
                $trav([4]),
                [[1, 4]],
            ],
            [
                $trav([1]),
                $trav([4, 5, 6]),
                [[1, 4]],
            ],
        ];
    }
}

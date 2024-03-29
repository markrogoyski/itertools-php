<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipFilled;

use IterTools\Multi;
use IterTools\Tests\Fixture;

class IteratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zipFilled with two iterators of the same size
     * @dataProvider dataProviderForZipFilledTwoIteratorsSameSize
     * @param        mixed $filler
     * @param        \Iterator $iter1
     * @param        \Iterator $iter2
     * @param        array     $expected
     */
    public function testZipFilledTwoIteratorSameSize($filler, \Iterator $iter1, \Iterator $iter2, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zipFilled($filler, $iter1, $iter2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipFilledTwoIteratorsSameSize(): array
    {
        return [
            [
                'filler',
                new Fixture\ArrayIteratorFixture([]),
                new Fixture\ArrayIteratorFixture([]),
                [],
            ],
            [
                'filler',
                new Fixture\ArrayIteratorFixture([1]),
                new Fixture\ArrayIteratorFixture([2]),
                [[1, 2]],
            ],
            [
                'filler',
                new Fixture\ArrayIteratorFixture([1, 2]),
                new Fixture\ArrayIteratorFixture([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                'filler',
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                new Fixture\ArrayIteratorFixture([4, 5, 6]),
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                'filler',
                new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                new Fixture\ArrayIteratorFixture([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 3]],
            ],
            [
                'filler',
                new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5, 6, 7, 8]),
                new Fixture\ArrayIteratorFixture([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], ['filler', 3]],
            ],
        ];
    }

    /**
     * @test         zipFilled with two iterators of the different sizes
     * @dataProvider dataProviderForZipFilledTwoIteratorsDifferentSize
     * @param        mixed $filler
     * @param        \Iterator $iter1
     * @param        \Iterator $iter2
     * @param        array     $expected
     */
    public function testZipFilledTwoIteatorsDifferentSize($filler, \Iterator $iter1, \Iterator $iter2, array $expected): void
    {
        // Given
        $result = [];

        foreach (Multi::zipFilled($filler, $iter1, $iter2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipFilledTwoIteratorsDifferentSize(): array
    {
        return [
            [
                [],
                new Fixture\ArrayIteratorFixture([1]),
                new Fixture\ArrayIteratorFixture([]),
                [[1, []]],
            ],
            [
                [],
                new Fixture\ArrayIteratorFixture([]),
                new Fixture\ArrayIteratorFixture([2]),
                [[[], 2]],
            ],
            [
                [],
                new Fixture\ArrayIteratorFixture([1, 2]),
                new Fixture\ArrayIteratorFixture([4]),
                [[1, 4], [2, []]],
            ],
            [
                [],
                new Fixture\ArrayIteratorFixture([1]),
                new Fixture\ArrayIteratorFixture([4, 5]),
                [[1, 4], [[], 5]],
            ],
            [
                [],
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                new Fixture\ArrayIteratorFixture([4, 5]),
                [[1, 4], [2, 5], [3, []]],
            ],
            [
                [],
                new Fixture\ArrayIteratorFixture([1, 2]),
                new Fixture\ArrayIteratorFixture([4, 5, 6]),
                [[1, 4], [2, 5], [[], 6]],
            ],
            [
                [],
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                new Fixture\ArrayIteratorFixture([4]),
                [[1, 4], [2, []], [3, []]],
            ],
            [
                [],
                new Fixture\ArrayIteratorFixture([1]),
                new Fixture\ArrayIteratorFixture([4, 5, 6]),
                [[1, 4], [[], 5], [[], 6]],
            ],
        ];
    }
}

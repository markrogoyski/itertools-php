<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipEqual;

use IterTools\Multi;
use IterTools\Tests\Fixture;

class IteratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zip with two iterators of the same size
     * @dataProvider dataProviderForZipStrictTwoIteratorsSameSize
     * @param        \Iterator $iter1
     * @param        \Iterator $iter2
     * @param        array     $expected
     */
    public function testZipStrictTwoIteratorSameSize(\Iterator $iter1, \Iterator $iter2, array $expected): void
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
    public static function dataProviderForZipStrictTwoIteratorsSameSize(): array
    {
        $iter = static function (array $input) {
            return new Fixture\ArrayIteratorFixture($input);
        };

        return [
            [
                $iter([]),
                $iter([]),
                [],
            ],
            [
                $iter([1]),
                $iter([2]),
                [[1, 2]],
            ],
            [
                $iter([1, 2]),
                $iter([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                $iter([1, 2, 3]),
                $iter([4, 5, 6]),
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                $iter([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 3]],
            ],
            [
                $iter(['a', 'b', 'c', 'd']),
                $iter(['one', 'two', 'three', 'four']),
                [['a', 'one'], ['b', 'two'], ['c', 'three'], ['d', 'four']],
            ],
        ];
    }

    /**
     * @test         zip with two iterators of the different sizes
     * @dataProvider dataProviderForZipStrictTwoIteratorsDifferentSize
     * @param        \Iterator $iter1
     * @param        \Iterator $iter2
     * @param        array     $expected
     */
    public function testZipStrictTwoIteratorsDifferentSize(\Iterator $iter1, \Iterator $iter2, array $expected): void
    {
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
    public static function dataProviderForZipStrictTwoIteratorsDifferentSize(): array
    {
        $iter = static function (array $input) {
            return new Fixture\ArrayIteratorFixture($input);
        };

        return [
            [
                $iter([1]),
                $iter([]),
                [],
            ],
            [
                $iter([]),
                $iter([2]),
                [],
            ],
            [
                $iter([1, 2]),
                $iter([4]),
                [[1, 4]],
            ],
            [
                $iter([1]),
                $iter([4, 5]),
                [[1, 4]],
            ],
            [
                $iter([1, 2, 3]),
                $iter([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                $iter([1, 2]),
                $iter([4, 5, 6]),
                [[1, 4], [2, 5]],
            ],
            [
                $iter([1, 2, 3]),
                $iter([4]),
                [[1, 4]],
            ],
            [
                $iter([1]),
                $iter([4, 5, 6]),
                [[1, 4]],
            ],
        ];
    }

    /**
     * @test zipEqual reads an iterator passed more than once as independent copies of it
     */
    public function testZipEqualIteratorPassedTwice(): void
    {
        // Given
        $iterator = new Fixture\ArrayIteratorFixture([1, 2, 2, 3]);

        // When
        $result = \iterator_to_array(Multi::zipEqual($iterator, $iterator), false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [2, 2], [3, 3]], $result);
    }

    /**
     * @test zipEqual reads an iterator passed around an equal-length iterable as independent copies of it
     */
    public function testZipEqualIteratorPassedTwiceAroundEqualLengthIterable(): void
    {
        // Given
        $iterator = new Fixture\ArrayIteratorFixture([1, 2]);

        // When
        $result = \iterator_to_array(Multi::zipEqual($iterator, [4, 5], $iterator), false);

        // Then
        $this->assertSame([[1, 4, 1], [2, 5, 2]], $result);
    }

    /**
     * @test         zipEqual throws when an iterator passed more than once and another iterable differ in length
     * @dataProvider dataProviderForZipEqualIteratorPassedTwiceAroundIterableOfDifferentLength
     * @param        array $other
     * @param        array $expected
     */
    public function testZipEqualIteratorPassedTwiceAroundIterableOfDifferentLength(array $other, array $expected): void
    {
        // Given
        $iterator = new Fixture\ArrayIteratorFixture([1, 2]);
        $result   = [];

        // When
        try {
            foreach (Multi::zipEqual($iterator, $other, $iterator) as $values) {
                $result[] = $values;
            }

            $this->fail();
        } catch (\LengthException $e) {
            // Then
            $this->assertSame($expected, $result);
        }
    }

    /**
     * @return array
     */
    public static function dataProviderForZipEqualIteratorPassedTwiceAroundIterableOfDifferentLength(): array
    {
        return [
            [
                [4],
                [[1, 4, 1]],
            ],
            [
                [4, 5, 6],
                [[1, 4, 1], [2, 5, 2]],
            ],
        ];
    }
}

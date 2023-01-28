<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipLongest;

use IterTools\Multi;

class IteratorToArrayTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zipLongest with two arrays of the same size
     * @dataProvider dataProviderForZipLongestTwoArraysSameSize
     * @param        array $array1
     * @param        array $array2
     * @param        array $expected
     */
    public function testZipLongestTwoArraysSameSize(array $array1, array $array2, array $expected): void
    {
        // Given
        $iterator = Multi::zipLongest($array1, $array2);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipLongestTwoArraysSameSize(): array
    {
        return [
            [
                [],
                [],
                [],
            ],
            [
                [1],
                [2],
                [[1, 2]],
            ],
            [
                [1, 2],
                [4, 5],
                [[1, 4], [2, 5]],
            ],
            [
                [1, 2, 3],
                [4, 5, 6],
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [4, 5, 6, 7, 8, 9, 1, 2, 3],
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 3]],
            ],
        ];
    }

    /**
     * @test         zipLongest with two arrays of the different sizes
     * @dataProvider dataProviderForZipLongestTwoArraysDifferentSize
     * @param        array $array1
     * @param        array $array2
     * @param        array $expected
     */
    public function testZipLongestTwoArraysDifferentSize(array $array1, array $array2, array $expected): void
    {
        // Given
        $iterator = Multi::zipLongest($array1, $array2);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipLongestTwoArraysDifferentSize(): array
    {
        return [
            [
                [1],
                [],
                [[1, null]],
            ],
            [
                [],
                [2],
                [[null, 2]],
            ],
            [
                [1, 2],
                [4],
                [[1, 4], [2, null]],
            ],
            [
                [1],
                [4, 5],
                [[1, 4], [null, 5]],
            ],
            [
                [1, 2, 3],
                [4, 5],
                [[1, 4], [2, 5], [3, null]],
            ],
            [
                [1, 2],
                [4, 5, 6],
                [[1, 4], [2, 5], [null, 6]],
            ],
            [
                [1, 2, 3],
                [4],
                [[1, 4], [2, null], [3, null]],
            ],
            [
                [1],
                [4, 5, 6],
                [[1, 4], [null, 5], [null, 6]],
            ],
        ];
    }

    /**
     * @test         zipLongest with three arrays of the same size
     * @dataProvider dataProviderForZipLongestThreeArraysSameSize
     * @param        array $array1
     * @param        array $array2
     * @param        array $array3
     * @param        array $expected
     */
    public function testZipLongestThreeArraysSameSize(array $array1, array $array2, array $array3, array $expected): void
    {
        // Given
        $iterator = Multi::zipLongest($array1, $array2, $array3);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         zipLongest with three arrays of the same size - unpacking
     * @dataProvider dataProviderForZipLongestThreeArraysSameSize
     * @param        array $array1
     * @param        array $array2
     * @param        array $array3
     * @param        array $expected
     */
    public function testZipLongestThreeArraysSameSizeUsingUnpacking(array $array1, array $array2, array $array3, array $expected): void
    {
        // Given
        $iterator = Multi::zipLongest(...[$array1, $array2, $array3]);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipLongestThreeArraysSameSize(): array
    {
        return [
            [
                [],
                [],
                [],
                [],
            ],
            [
                [1],
                [2],
                [3],
                [[1, 2, 3]],
            ],
            [
                [1, 2],
                [4, 5],
                [7, 8],
                [[1, 4, 7], [2, 5, 8]],
            ],
            [
                [1, 2, 3],
                [4, 5, 6],
                [7, 8, 9],
                [[1, 4, 7], [2, 5, 8], [3, 6, 9]],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [4, 5, 6, 7, 8, 9, 1, 2, 3],
                [7, 8, 9, 1, 2, 3, 4, 5, 6],
                [[1, 4, 7], [2, 5, 8], [3, 6, 9], [4, 7, 1], [5, 8, 2], [6, 9, 3], [7, 1, 4], [8, 2, 5], [9, 3, 6]],
            ],
        ];
    }

    /**
     * @test         zipLongest with three arrays of different size
     * @dataProvider dataProviderForZipLongestThreeArraysDifferentSize
     * @param        array $array1
     * @param        array $array2
     * @param        array $array3
     * @param        array $expected
     */
    public function testZipLongestThreeArraysDifferentSize(array $array1, array $array2, array $array3, array $expected): void
    {
        // Given
        $iterator = Multi::zipLongest($array1, $array2, $array3);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipLongestThreeArraysDifferentSize(): array
    {
        return [
            [
                [],
                [1],
                [2, 3],
                [[null, 1, 2], [null, null, 3]],
            ],
            [
                [1],
                [2, 3],
                [4, 5, 6],
                [[1, 2, 4], [null, 3, 5], [null, null, 6]],
            ],
            [
                [1, 2],
                [4, 5, 7, 8],
                [9, 1, 2, 3, 4, 5, 6],
                [[1, 4, 9], [2, 5, 1], [null, 7, 2], [null, 8, 3], [null, null, 4], [null, null, 5], [null, null, 6]],
            ],
            [
                [1, 2, 3, 4],
                [4, 5, 6],
                [7, 8, 9, 0],
                [[1, 4, 7], [2, 5, 8], [3, 6, 9], [4, null, 0]],
            ],
        ];
    }

    /**
     * @test array keys are reset as ordered sequence of integers, and not an array of multiple iterator indexes
     */
    public function testArrayKeysResetFromDefaultArrayKeys()
    {
        // Given
        $array1 = ['a', 'b', 'c',];
        $array2 = [1, 2, 3, 4, 5];

        // When
        $iterator = Multi::zipLongest($array1, $array2);
        $result = iterator_to_array($iterator);

        // Then
        $expected = [0 => ['a', 1], 1 => ['b', 2], 2 => ['c', 3], 3 => [null, 4], 4 => [null, 5]];
        $this->assertEquals($expected, $result);
    }

    /**
     * @test array keys are reset as ordered sequence of integers starting at zero
     */
    public function testArrayKeysResetFromCustomArrayKeys()
    {
        // Given
        $array1 = ['l1' => 'a', 'l2' => 'b', 'l3' => 'c'];
        $array2 = [2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5];

        // When
        $iterator = Multi::zipLongest($array1, $array2);
        $result = iterator_to_array($iterator);

        // Then
        $expected = [0 => ['a', 1], 1 => ['b', 2], 2 => ['c', 3], 3 => [null, 4], 4 => [null, 5]];
        $this->assertEquals($expected, $result);
    }
}

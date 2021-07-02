<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipLongest;

use IterTools\Multi;

class ArrayTest extends \PHPUnit\Framework\TestCase
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
        $result = [];

        // When
        foreach (Multi::zipLongest($array1, $array2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

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
        $result = [];

        foreach (Multi::zipLongest($array1, $array2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

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
        $result = [];

        // When
        foreach (Multi::zipLongest($array1, $array2, $array3) as [$value1, $value2, $value3]) {
            $result[] = [$value1, $value2, $value3];
        }

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
        $result = [];

        // When
        foreach (Multi::zipLongest(...[$array1, $array2, $array3]) as [$value1, $value2, $value3]) {
            $result[] = [$value1, $value2, $value3];
        }

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
        $result = [];

        // When
        foreach (Multi::zipLongest($array1, $array2, $array3) as [$value1, $value2, $value3]) {
            $result[] = [$value1, $value2, $value3];
        }

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
}

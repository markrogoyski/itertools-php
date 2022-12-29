<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipEqual;

use IterTools\Multi;

class ArrayTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zipEqual with two arrays of the same size
     * @dataProvider dataProviderForZipStrictTwoArraysSameSize
     * @param        array $array1
     * @param        array $array2
     * @param        array $expected
     */
    public function testZipStrictTwoArraysSameSize(array $array1, array $array2, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zipEqual($array1, $array2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipStrictTwoArraysSameSize(): array
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
     * @test         zipEqual with two arrays of the different sizes
     * @dataProvider dataProviderForZipStrictTwoArraysDifferentSize
     * @param        array $array1
     * @param        array $array2
     * @param        array $expected
     */
    public function testZipStrictTwoArraysDifferentSize(array $array1, array $array2, array $expected): void
    {
        // Given
        $result = [];

        // When
        try {
            foreach (Multi::zipEqual($array1, $array2) as [$value1, $value2]) {
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
    public function dataProviderForZipStrictTwoArraysDifferentSize(): array
    {
        return [
            [
                [1],
                [],
                [],
            ],
            [
                [],
                [2],
                [],
            ],
            [
                [1, 2],
                [4],
                [[1, 4]],
            ],
            [
                [1],
                [4, 5],
                [[1, 4]],
            ],
            [
                [1, 2, 3],
                [4, 5],
                [[1, 4], [2, 5]],
            ],
            [
                [1, 2],
                [4, 5, 6],
                [[1, 4], [2, 5]],
            ],
            [
                [1, 2, 3],
                [4],
                [[1, 4]],
            ],
            [
                [1],
                [4, 5, 6],
                [[1, 4]],
            ],
        ];
    }

    /**
     * @test         zipEqual with three arrays of the same size
     * @dataProvider dataProviderForZipStrictThreeArraysSameSize
     * @param        array $array1
     * @param        array $array2
     * @param        array $array3
     * @param        array $expected
     */
    public function testZipStrictThreeArraysSameSize(
        array $array1,
        array $array2,
        array $array3,
        array $expected
    ): void {
        // Given
        $result = [];

        // When
        foreach (Multi::zipEqual($array1, $array2, $array3) as [$value1, $value2, $value3]) {
            $result[] = [$value1, $value2, $value3];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         zipEqual with three arrays of the same size - unpacking
     * @dataProvider dataProviderForZipStrictThreeArraysSameSize
     * @param        array $array1
     * @param        array $array2
     * @param        array $array3
     * @param        array $expected
     */
    public function testZipStrictThreeArraysSameSizeUsingUnpacking(
        array $array1,
        array $array2,
        array $array3,
        array $expected
    ): void {
        // Given
        $result = [];

        // When
        foreach (Multi::zipEqual(...[$array1, $array2, $array3]) as [$value1, $value2, $value3]) {
            $result[] = [$value1, $value2, $value3];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipStrictThreeArraysSameSize(): array
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
     * @test         zipEqual with three arrays of different size
     * @dataProvider dataProviderForZipStrictThreeArraysDifferentSize
     * @param        array $array1
     * @param        array $array2
     * @param        array $array3
     * @param        array $expected
     */
    public function testZipStrictThreeArraysDifferentSize(
        array $array1,
        array $array2,
        array $array3,
        array $expected
    ): void {
        // Given
        $result = [];

        // When
        try {
            foreach (Multi::zipEqual($array1, $array2, $array3) as [$value1, $value2, $value3]) {
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
    public function dataProviderForZipStrictThreeArraysDifferentSize(): array
    {
        return [
            [
                [],
                [1],
                [2, 3],
                [],
            ],
            [
                [1],
                [2, 3],
                [4, 5, 6],
                [[1, 2, 4]],
            ],
            [
                [1, 2],
                [4, 5, 7, 8],
                [9, 1, 2, 3, 4, 5, 6],
                [[1, 4, 9], [2, 5, 1]],
            ],
            [
                [1, 2, 3, 4],
                [4, 5, 6],
                [7, 8, 9, 0],
                [[1, 4, 7], [2, 5, 8], [3, 6, 9]],
            ],
        ];
    }
}

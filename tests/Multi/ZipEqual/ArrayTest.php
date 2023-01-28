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
            [
                ['a', 'b', 'c', 'd'],
                ['one', 'two', 'three', 'four'],
                [['a', 'one'], ['b', 'two'], ['c', 'three'], ['d', 'four']],
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
            [
                ['a'],
                [1, 2],
                [['a', 1]],
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
            [
                ['a', 'b', 'c'],
                [1, 2, 3],
                [true, false, true],
                [['a', 1, true], ['b', 2, false], ['c', 3, true]],
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

    /**
     * @test array keys are reset as ordered sequence of integers, and not an array of multiple iterator indexes
     */
    public function testArrayKeysResetFromDefaultArrayKeys()
    {
        // Given
        $array1 = ['a', 'b', 'c', 'd', 'e'];
        $array2 = [1, 2, 3, 4, 5];

        // And
        $result = [];

        // When
        foreach (Multi::zipEqual($array1, $array2) as $key => [$value1, $value2]) {
            $result[$key] = [$value1, $value2];
        }

        // Then
        $expected = [0 => ['a', 1], 1 => ['b', 2], 2 => ['c', 3], 3 => ['d', 4], 4 => ['e', 5]];
        $this->assertEquals($expected, $result);
    }

    /**
     * @test array keys are reset as ordered sequence of integers starting at zero
     */
    public function testArrayKeysResetFromCustomArrayKeys()
    {
        // Given
        $array1 = ['l1' => 'a', 'l2' => 'b', 'l3' => 'c', 'l4' => 'd', 'l5' => 'e'];
        $array2 = [2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5];

        // And
        $result = [];

        // When
        foreach (Multi::zipEqual($array1, $array2) as $key => [$value1, $value2]) {
            $result[$key] = [$value1, $value2];
        }

        // Then
        $expected = [0 => ['a', 1], 1 => ['b', 2], 2 => ['c', 3], 3 => ['d', 4], 4 => ['e', 5]];
        $this->assertEquals($expected, $result);
    }
}

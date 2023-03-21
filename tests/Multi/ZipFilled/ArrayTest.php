<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipFilled;

use IterTools\Multi;

class ArrayTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zipFilled with two arrays of the same size
     * @dataProvider dataProviderForZipFilledTwoArraysSameSize
     * @param        mixed $filler
     * @param        array $array1
     * @param        array $array2
     * @param        array $expected
     */
    public function testZipFilledTwoArraysSameSize($filler, array $array1, array $array2, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zipFilled($filler, $array1, $array2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipFilledTwoArraysSameSize(): array
    {
        return [
            [
                'filler',
                [],
                [],
                [],
            ],
            [
                'filler',
                [1],
                [2],
                [[1, 2]],
            ],
            [
                'filler',
                [1, 2],
                [4, 5],
                [[1, 4], [2, 5]],
            ],
            [
                'filler',
                [1, 2, 3],
                [4, 5, 6],
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                'filler',
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [4, 5, 6, 7, 8, 9, 1, 2, 3],
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 3]],
            ],
        ];
    }

    /**
     * @test         zipFilled with two arrays of the different sizes
     * @dataProvider dataProviderForZipFilledTwoArraysDifferentSize
     * @param        mixed $filler
     * @param        array $array1
     * @param        array $array2
     * @param        array $expected
     */
    public function testZipFilledTwoArraysDifferentSize($filler, array $array1, array $array2, array $expected): void
    {
        // Given
        $result = [];

        foreach (Multi::zipFilled($filler, $array1, $array2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipFilledTwoArraysDifferentSize(): array
    {
        return [
            [
                'filler',
                [1],
                [],
                [[1, 'filler']],
            ],
            [
                'filler',
                [],
                [2],
                [['filler', 2]],
            ],
            [
                'filler',
                [1, 2],
                [4],
                [[1, 4], [2, 'filler']],
            ],
            [
                'filler',
                [1],
                [4, 5],
                [[1, 4], ['filler', 5]],
            ],
            [
                'filler',
                [1, 2, 3],
                [4, 5],
                [[1, 4], [2, 5], [3, 'filler']],
            ],
            [
                'filler',
                [1, 2],
                [4, 5, 6],
                [[1, 4], [2, 5], ['filler', 6]],
            ],
            [
                'filler',
                [1, 2, 3],
                [4],
                [[1, 4], [2, 'filler'], [3, 'filler']],
            ],
            [
                'filler',
                [1],
                [4, 5, 6],
                [[1, 4], ['filler', 5], ['filler', 6]],
            ],
        ];
    }

    /**
     * @test         zipFilled with three arrays of the same size
     * @dataProvider dataProviderForZipFilledThreeArraysSameSize
     * @param        mixed $filler
     * @param        array $array1
     * @param        array $array2
     * @param        array $array3
     * @param        array $expected
     */
    public function testZipFilledThreeArraysSameSize($filler, array $array1, array $array2, array $array3, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zipFilled($filler, $array1, $array2, $array3) as [$value1, $value2, $value3]) {
            $result[] = [$value1, $value2, $value3];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         zipFilled with three arrays of the same size - unpacking
     * @dataProvider dataProviderForZipFilledThreeArraysSameSize
     * @param        mixed $filler
     * @param        array $array1
     * @param        array $array2
     * @param        array $array3
     * @param        array $expected
     */
    public function testZipFilledThreeArraysSameSizeUsingUnpacking($filler, array $array1, array $array2, array $array3, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zipFilled($filler, ...[$array1, $array2, $array3]) as [$value1, $value2, $value3]) {
            $result[] = [$value1, $value2, $value3];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipFilledThreeArraysSameSize(): array
    {
        return [
            [
                'filler',
                [],
                [],
                [],
                [],
            ],
            [
                'filler',
                [1],
                [2],
                [3],
                [[1, 2, 3]],
            ],
            [
                'filler',
                [1, 2],
                [4, 5],
                [7, 8],
                [[1, 4, 7], [2, 5, 8]],
            ],
            [
                'filler',
                [1, 2, 3],
                [4, 5, 6],
                [7, 8, 9],
                [[1, 4, 7], [2, 5, 8], [3, 6, 9]],
            ],
            [
                'filler',
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [4, 5, 6, 7, 8, 9, 1, 2, 3],
                [7, 8, 9, 1, 2, 3, 4, 5, 6],
                [[1, 4, 7], [2, 5, 8], [3, 6, 9], [4, 7, 1], [5, 8, 2], [6, 9, 3], [7, 1, 4], [8, 2, 5], [9, 3, 6]],
            ],
        ];
    }

    /**
     * @test         zipFilled with three arrays of different size
     * @dataProvider dataProviderForZipFilledThreeArraysDifferentSize
     * @param        mixed $filler
     * @param        array $array1
     * @param        array $array2
     * @param        array $array3
     * @param        array $expected
     */
    public function testZipFilledThreeArraysDifferentSize($filler, array $array1, array $array2, array $array3, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::zipFilled($filler, $array1, $array2, $array3) as [$value1, $value2, $value3]) {
            $result[] = [$value1, $value2, $value3];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipFilledThreeArraysDifferentSize(): array
    {
        return [
            [
                'filler',
                [],
                [1],
                [2, 3],
                [['filler', 1, 2], ['filler', 'filler', 3]],
            ],
            [
                'filler',
                [1],
                [2, 3],
                [4, 5, 6],
                [[1, 2, 4], ['filler', 3, 5], ['filler', 'filler', 6]],
            ],
            [
                'filler',
                [1, 2],
                [4, 5, 7, 8],
                [9, 1, 2, 3, 4, 5, 6],
                [[1, 4, 9], [2, 5, 1], ['filler', 7, 2], ['filler', 8, 3], ['filler', 'filler', 4], ['filler', 'filler', 5], ['filler', 'filler', 6]],
            ],
            [
                'filler',
                [1, 2, 3, 4],
                [4, 5, 6],
                [7, 8, 9, 0],
                [[1, 4, 7], [2, 5, 8], [3, 6, 9], [4, 'filler', 0]],
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

        // And
        $result = [];

        // When
        foreach (Multi::zipFilled(['filler'], $array1, $array2) as $key => [$value1, $value2]) {
            $result[$key] = [$value1, $value2];
        }

        // Then
        $expected = [0 => ['a', 1], 1 => ['b', 2], 2 => ['c', 3], 3 => [['filler'], 4], 4 => [['filler'], 5]];
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

        // And
        $result = [];

        // When
        foreach (Multi::zipFilled('', $array1, $array2) as $key => [$value1, $value2]) {
            $result[$key] = [$value1, $value2];
        }

        // Then
        $expected = [0 => ['a', 1], 1 => ['b', 2], 2 => ['c', 3], 3 => ['', 4], 4 => ['', 5]];
        $this->assertEquals($expected, $result);
    }
}

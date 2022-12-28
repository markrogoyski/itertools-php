<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipStrict;

use IterTools\Multi;
use IterTools\Tests\Fixture;

class GeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zip with two generators of the same size
     * @dataProvider dataProviderForZipStrictTwoGeneratorsSameSize
     * @param        \Generator $generator1
     * @param        \Generator $generator2
     * @param        array      $expected
     */
    public function testZipStrictTwoGeneratorsSameSize(
        \Generator $generator1,
        \Generator $generator2,
        array $expected
    ): void {
        // Given
        $result = [];

        // When
        foreach (Multi::zipStrict($generator1, $generator2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipStrictTwoGeneratorsSameSize(): array
    {
        $gen = static function (array $input) {
            return Fixture\GeneratorFixture::getGenerator($input);
        };

        return [
            [
                $gen([]),
                $gen([]),
                [],
            ],
            [
                $gen([1]),
                $gen([2]),
                [[1, 2]],
            ],
            [
                $gen([1, 2]),
                $gen([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                $gen([1, 2, 3]),
                $gen([4, 5, 6]),
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                $gen([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 3]],
            ],
        ];
    }

    /**
     * @test         zipStrict with two generators of the same size
     * @dataProvider dataProviderForZipStrictTwoArraysDifferentSize
     * @param        \Generator $generator1
     * @param        \Generator $generator2
     * @param        array $expected
     */
    public function testZipStrictTwoArraysDifferentSize(
        \Generator $generator1,
        \Generator $generator2,
        array $expected
    ): void {
        // Given
        $result = [];

        // When
        try {
            foreach (Multi::zipStrict($generator1, $generator2) as [$value1, $value2]) {
                $result[] = [$value1, $value2];
            }

            $this->fail();
        } catch (\OutOfRangeException $e) {
            // Then
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @return array
     */
    public function dataProviderForZipStrictTwoArraysDifferentSize(): array
    {
        $gen = static function (array $input) {
            return Fixture\GeneratorFixture::getGenerator($input);
        };

        return [
            [
                $gen([1]),
                $gen([]),
                [],
            ],
            [
                $gen([]),
                $gen([2]),
                [],
            ],
            [
                $gen([1, 2]),
                $gen([4]),
                [[1, 4]],
            ],
            [
                $gen([1]),
                $gen([4, 5]),
                [[1, 4]],
            ],
            [
                $gen([1, 2, 3]),
                $gen([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                $gen([1, 2]),
                $gen([4, 5, 6]),
                [[1, 4], [2, 5]],
            ],
            [
                $gen([1, 2, 3]),
                $gen([4]),
                [[1, 4]],
            ],
            [
                $gen([1]),
                $gen([4, 5, 6]),
                [[1, 4]],
            ],
        ];
    }

    /**
     * @test         zipStrict with three arrays of different size
     * @dataProvider dataProviderForZipStrictThreeArraysDifferentSize
     * @param        \Generator $generator1
     * @param        \Generator $generator2
     * @param        \Generator $generator3
     * @param        array $expected
     */
    public function testZipStrictThreeArraysDifferentSize(
        \Generator $generator1,
        \Generator $generator2,
        \Generator $generator3,
        array $expected
    ): void {
        // Given
        $result = [];

        // When
        try {
            foreach (Multi::zipStrict($generator1, $generator2, $generator3) as [$value1, $value2, $value3]) {
                $result[] = [$value1, $value2, $value3];
            }

            $this->fail();
        } catch (\OutOfRangeException $e) {
            // Then
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @return array
     */
    public function dataProviderForZipStrictThreeArraysDifferentSize(): array
    {
        $gen = static function (array $input) {
            return Fixture\GeneratorFixture::getGenerator($input);
        };

        return [
            [
                $gen([]),
                $gen([1]),
                $gen([2, 3]),
                [],
            ],
            [
                $gen([1]),
                $gen([2, 3]),
                $gen([4, 5, 6]),
                [[1, 2, 4]],
            ],
            [
                $gen([1, 2]),
                $gen([4, 5, 7, 8]),
                $gen([9, 1, 2, 3, 4, 5, 6]),
                [[1, 4, 9], [2, 5, 1]],
            ],
            [
                $gen([1, 2, 3, 4]),
                $gen([4, 5, 6]),
                $gen([7, 8, 9, 0]),
                [[1, 4, 7], [2, 5, 8], [3, 6, 9]],
            ],
        ];
    }
}

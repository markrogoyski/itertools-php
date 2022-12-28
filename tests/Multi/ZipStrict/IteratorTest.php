<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\ZipStrict;

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
        foreach (Multi::zipStrict($iter1, $iter2) as [$value1, $value2]) {
            $result[] = [$value1, $value2];
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipStrictTwoIteratorsSameSize(): array
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
            foreach (Multi::zipStrict($iter1, $iter2) as [$value1, $value2]) {
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
    public function dataProviderForZipStrictTwoIteratorsDifferentSize(): array
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
}

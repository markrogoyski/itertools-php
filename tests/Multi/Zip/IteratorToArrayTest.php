<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi\Zip;

use IterTools\Multi;
use IterTools\Tests\Fixture;

class IteratorToArrayTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         zip with two arrays of the same size
     * @dataProvider dataProviderForZipTwoArraysSameSize
     * @param        array $array1
     * @param        array $array2
     * @param        array $expected
     */
    public function testZipTwoArraysSameSize(array $array1, array $array2, array $expected): void
    {
        // Given
        $iterator = Multi::zip($array1, $array2);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipTwoArraysSameSize(): array
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
     * @test         zip with two arrays of the different sizes
     * @dataProvider dataProviderForZipTwoArraysDifferentSize
     * @param        array $array1
     * @param        array $array2
     * @param        array $expected
     */
    public function testZipTwoArraysDifferentSize(array $array1, array $array2, array $expected): void
    {
        // Given
        $iterator = Multi::zip($array1, $array2);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipTwoArraysDifferentSize(): array
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
     * @test         zip with three arrays of the same size
     * @dataProvider dataProviderForZipThreeArraysSameSize
     * @param        array $array1
     * @param        array $array2
     * @param        array $array3
     * @param        array $expected
     */
    public function testZipThreeArraysSameSize(array $array1, array $array2, array $array3, array $expected): void
    {
        // Given
        $iterator = Multi::zip($array1, $array2, $array3);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         zip with three arrays of the same size - unpacking
     * @dataProvider dataProviderForZipThreeArraysSameSize
     * @param        array $array1
     * @param        array $array2
     * @param        array $array3
     * @param        array $expected
     */
    public function testZipThreeArraysSameSizeUsingUnpacking(array $array1, array $array2, array $array3, array $expected): void
    {
        // Given
        $iterator = Multi::zip(...[$array1, $array2, $array3]);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipThreeArraysSameSize(): array
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
     * @test         zip with three arrays of different size
     * @dataProvider dataProviderForZipThreeArraysDifferentSize
     * @param        array $array1
     * @param        array $array2
     * @param        array $array3
     * @param        array $expected
     */
    public function testZipThreeArraysDifferentSize(array $array1, array $array2, array $array3, array $expected): void
    {
        // Given
        $iterator = Multi::zip($array1, $array2, $array3);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipThreeArraysDifferentSize(): array
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
     * @test         iterator_to_array with two generators of the same size
     * @dataProvider dataProviderForZipTwoGeneratorsSameSize
     * @param        \Generator $generator1
     * @param        \Generator $generator2
     * @param        array      $expected
     */
    public function testZipTwoGeneratorsSameSize(\Generator $generator1, \Generator $generator2, array $expected): void
    {
        // Given
        $iterator = Multi::zip($generator1, $generator2);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipTwoGeneratorsSameSize(): array
    {
        return [
            [
                Fixture\GeneratorFixture::getGenerator([]),
                Fixture\GeneratorFixture::getGenerator([]),
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1]),
                Fixture\GeneratorFixture::getGenerator([2]),
                [[1, 2]],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2]),
                Fixture\GeneratorFixture::getGenerator([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                Fixture\GeneratorFixture::getGenerator([4, 5, 6]),
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                Fixture\GeneratorFixture::getGenerator([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 3]],
            ],
        ];
    }

    /**
     * @test         iterator_to_array with two iterators of the same size
     * @dataProvider dataProviderForZipTwoIteratorsSameSize
     * @param        \Iterator $iter1
     * @param        \Iterator $iter2
     * @param        array     $expected
     */
    public function testZipTwoIteratorSameSize(\Iterator $iter1, \Iterator $iter2, array $expected): void
    {
        // Given
        $iterator = Multi::zip($iter1, $iter2);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipTwoIteratorsSameSize(): array
    {
        return [
            [
                new Fixture\ArrayIteratorFixture([]),
                new Fixture\ArrayIteratorFixture([]),
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([1]),
                new Fixture\ArrayIteratorFixture([2]),
                [[1, 2]],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2]),
                new Fixture\ArrayIteratorFixture([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                new Fixture\ArrayIteratorFixture([4, 5, 6]),
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                new Fixture\ArrayIteratorFixture([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 3]],
            ],
        ];
    }

    /**
     * @test         iterator_to_array with two traversable objects of the same size
     * @dataProvider dataProviderForZipTwoTraversableSameSize
     * @param        \Traversable $iter1
     * @param        \Traversable $iter2
     * @param        array     $expected
     */
    public function testZipTwoTraversablesSameSize(\Traversable $iter1, \Traversable $iter2, array $expected): void
    {
        // Given
        $iterator = Multi::zip($iter1, $iter2);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function dataProviderForZipTwoTraversableSameSize(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([]),
                new Fixture\IteratorAggregateFixture([]),
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([1]),
                new Fixture\IteratorAggregateFixture([2]),
                [[1, 2]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2]),
                new Fixture\IteratorAggregateFixture([4, 5]),
                [[1, 4], [2, 5]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                new Fixture\IteratorAggregateFixture([4, 5, 6]),
                [[1, 4], [2, 5], [3, 6]],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                new Fixture\IteratorAggregateFixture([4, 5, 6, 7, 8, 9, 1, 2, 3]),
                [[1, 4], [2, 5], [3, 6], [4, 7], [5, 8], [6, 9], [7, 1], [8, 2], [9, 3]],
            ],
        ];
    }
}

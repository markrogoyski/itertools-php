<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

use IterTools\Multi;
use IterTools\Tests\Fixture;

class ChainTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         chain array
     * @dataProvider dataProviderForArray
     * @param        array $first
     * @param        array $second
     * @param        array $expected
     */
    public function testArray(array $first, array $second, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Multi::chain($first, $second) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                [],
                [],
            ],
            [
                [1],
                [],
                [1],
            ],
            [
                [],
                [1],
                [1],
            ],
            [
                [1],
                [2],
                [1, 2],
            ],
            [
                [1, 2, 3],
                [4, 5, 6],
                [1, 2, 3, 4, 5, 6],
            ],
            [
                ['a', 'b', 'c'],
                ['x', 'y', 'z'],
                ['a', 'b', 'c', 'x', 'y', 'z'],
            ],
            [
                [1, 2, 3],
                ['a', 'b', 'c'],
                [1, 2, 3, 'a', 'b', 'c'],
            ],
            [
                [1, [1, 2, 3], 'abc', true],
                [9, 3.5, false, null, \INF, '日本語', ['a', 3, 'false']],
                [1, [1, 2, 3], 'abc', true, 9, 3.5, false, null, \INF, '日本語', ['a', 3, 'false']],
            ]
        ];
    }

    /**
     * @test chain multiple arrays
     */
    public function testChainMultipleArrays()
    {
        // Given
        $array1 = [1, 2, 3];
        $array2 = ['a', 'b', 'c'];
        $array3 = [1.1, 2.2, 3.3];
        $array4 = [true, false, true];

        // And
        $result   = [];
        $expected = [1, 2, 3, 'a', 'b', 'c', 1.1, 2.2, 3.3, true, false, true];

        // When
        foreach (Multi::chain($array1, $array2, $array3, $array4) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test chain generators
     */
    public function testChainGenerators()
    {
        // Given
        $generator1 = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);
        $generator2 = Fixture\GeneratorFixture::getGenerator(['a', 'b', 'c']);

        // And
        $result   = [];
        $expected = [1, 2, 3, 'a', 'b', 'c'];

        // When
        foreach (Multi::chain($generator1, $generator2) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test chain iterators
     */
    public function testChainIterators()
    {
        // Given
        $iterator1 = new Fixture\ArrayIteratorFixture([1, 2, 3]);
        $iterator2 = new Fixture\ArrayIteratorFixture(['a', 'b', 'c']);

        // And
        $result   = [];
        $expected = [1, 2, 3, 'a', 'b', 'c'];

        // When
        foreach (Multi::chain($iterator1, $iterator2) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test chain traversables
     */
    public function testChainTraversables()
    {
        // Given
        $traversable1 = new Fixture\IteratorAggregateFixture([1, 2, 3]);
        $traversable2 = new Fixture\IteratorAggregateFixture(['a', 'b', 'c']);

        // And
        $result   = [];
        $expected = [1, 2, 3, 'a', 'b', 'c'];

        // When
        foreach (Multi::chain($traversable1, $traversable2) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test chain empty collections
     */
    public function testChainEmptyCollections()
    {
        // Given
        $array1       = [];
        $array2       = [1];
        $generator1   = Fixture\GeneratorFixture::getGenerator([]);
        $generator2   = Fixture\GeneratorFixture::getGenerator([2]);
        $iterator1    = new Fixture\ArrayIteratorFixture([]);
        $iterator2    = new Fixture\ArrayIteratorFixture([3]);
        $traversable1 = new Fixture\IteratorAggregateFixture([]);
        $traversable2 = new Fixture\IteratorAggregateFixture([4]);


        // And
        $result   = [];
        $expected = [1, 2, 3, 4];

        // When
        foreach (Multi::chain($array1, $array2, $generator1, $generator2, $iterator1, $iterator2, $traversable1, $traversable2) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }
}

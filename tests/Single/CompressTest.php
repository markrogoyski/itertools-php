<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture;

class CompressTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         compress array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        array $selectors
     * @param        array $expected
     */
    public function testArray(array $data, array $selectors, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Single::compress($data, $selectors) as $datum) {
            $result[] = $datum;
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
                [0],
                [0],
                [],
            ],
            [
                [1],
                [1],
                [1]
            ],
            [
                [1, 1, 1],
                [1, 0, 1],
                [1, 1]
            ],
            [
                [1, 2, 3],
                [0, 0, 1],
                [3],
            ],
            [
                [1, 2, 3, 4, 5],
                [0, 1, 1, 1, 0],
                [2, 3, 4],
            ],
            [
                [1, 2, 3, -4, 5],
                [true, true, false, false, false],
                [1, 2]
            ],
            [
                [1, 2, 3, -4, -5],
                [1, 'true', true, 0, false],
                [1, 2, 3],
            ],
        ];
    }

    /**
     * @test         compress generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        \Generator $selectors
     * @param        array      $expected
     */
    public function testGenerators(\Generator $data, \Generator $selectors, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Single::compress($data, $selectors) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        return [
            [
                Fixture\GeneratorFixture::getGenerator([]),
                Fixture\GeneratorFixture::getGenerator([]),
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0]),
                Fixture\GeneratorFixture::getGenerator([0]),
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1]),
                Fixture\GeneratorFixture::getGenerator([1]),
                [1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 1, 1]),
                Fixture\GeneratorFixture::getGenerator([1, 0, 1]),
                [1, 1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                Fixture\GeneratorFixture::getGenerator([1, 0, 1]),
                [1, 3],
            ],
        ];
    }

    /**
     * @test         compress iterators
     * @dataProvider dataProviderForIterators
     * @param        iterable $data
     * @param        iterable $selectors
     * @param        array    $expected
     */
    public function testIterators(iterable $data, iterable $selectors, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Single::compress($data, $selectors) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        return [
            [
                new Fixture\ArrayIteratorFixture([]),
                new Fixture\ArrayIteratorFixture([]),
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([0]),
                new Fixture\ArrayIteratorFixture([0]),
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([1]),
                new Fixture\ArrayIteratorFixture([1]),
                [1],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 1, 1]),
                new Fixture\ArrayIteratorFixture([1, 0, 1]),
                [1, 1],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                new Fixture\ArrayIteratorFixture([1, 0, 1]),
                [1, 3],
            ],
        ];
    }

    /**
     * @test         compress traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        \Traversable $selectors
     * @param        array        $expected
     */
    public function testTraversables(\Traversable $data, \Traversable $selectors, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Single::compress($data, $selectors) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([]),
                new Fixture\IteratorAggregateFixture([]),
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([0]),
                new Fixture\IteratorAggregateFixture([0]),
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([1]),
                new Fixture\IteratorAggregateFixture([1]),
                [1],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 1, 1]),
                new Fixture\IteratorAggregateFixture([1, 0, 1]),
                [1, 1],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                new Fixture\IteratorAggregateFixture([1, 0, 1]),
                [1, 3],
            ],
        ];
    }

    /**
     * @test         compress iterator_to_array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        array $selectors
     * @param        array $expected
     */
    public function testIteratorToArray(array $data, array $selectors, array $expected)
    {
        // Given
        $iterator = Single::compress($data, $selectors);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

use IterTools\Math;
use IterTools\Tests\Fixture;

class RunningMinTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         runningMin array
     * @dataProvider dataProviderForArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testArray(array $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningMin($numbers) as $runningMin) {
            $result[] = $runningMin;
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
            ],
            [
                [0],
                [0],
            ],
            [
                [1],
                [1],
            ],
            [
                [-1],
                [-1],
            ],
            [
                [1, 1, 1],
                [1, 1, 1],
            ],
            [
                [1, 2, 3],
                [1, 1, 1],
            ],
            [
                [1, 2, 3, 4, 5],
                [1, 1, 1, 1, 1],
            ],
            [
                [1, 2, 3, -4, 5],
                [1, 1, 1, -4, -4],
            ],
            [
                [1, 2, 3, -4, -5],
                [1, 1, 1, -4, -5],
            ],
            [
                [5, 4, 3, 2, 1],
                [5, 4, 3, 2, 1],
            ],
            [
                [10, 1, 9, 2, 8, 3, 7, 4, 5, 5, 10, 1, 0, -1],
                [10, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, -1]
            ],
        ];
    }

    /**
     * @test         runningMin array with initial value
     * @dataProvider dataProviderForArrayWithInitialValue
     * @param        array $numbers
     * @param        int   $initialValue
     * @param        array $expected
     */
    public function testArrayWithInitialValue(array $numbers, int $initialValue, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningMin($numbers, $initialValue) as $runningMin) {
            $result[] = $runningMin;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForArrayWithInitialValue(): array
    {
        return [
            [
                [],
                5,
                [5],
            ],
            [
                [0],
                5,
                [5, 0],
            ],
            [
                [1],
                5,
                [5, 1],
            ],
            [
                [1, 1, 1],
                5,
                [5, 1, 1, 1],
            ],
            [
                [1, 2, 3],
                5,
                [5, 1, 1, 1],
            ],
            [
                [1, 2, 3, 4, 5],
                5,
                [5, 1, 1, 1, 1, 1],
            ],
            [
                [1, 2, 3, -4, 5],
                5,
                [5, 1, 1, 1, -4, -4],
            ],
            [
                [1, 2, 3, -4, -5],
                5,
                [5, 1, 1, 1, -4, -5],
            ],
            [
                [5, 4, 3, 2, 1],
                5,
                [5, 5, 4, 3, 2, 1],
            ],
            [
                [10, 1, 9, 2, 8, 3, 7, 4, 5, 5, 10, 1, 0, -1],
                5,
                [5, 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, -1]
            ],
        ];
    }

    /**
     * @test         runningMin generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $numbers
     * @param        array      $expected
     */
    public function testGenerators(\Generator $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningMin($numbers) as $runningMin) {
            $result[] = $runningMin;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        return [
            [
                Fixture\GeneratorFixture::getGenerator([]),
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0]),
                [0],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1]),
                [1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 1, 1]),
                [1, 1, 1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                [1, 1, 1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5]),
                [1, 1, 1, 1, 1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, -4, 5]),
                [1, 1, 1, -4, -4],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, -4, -5]),
                [1, 1, 1, -4, -5],
            ],
        ];
    }

    /**
     * @test         runningMin iterators
     * @dataProvider dataProviderForIterators
     * @param        iterable $numbers
     * @param        array    $expected
     */
    public function testIterators(iterable $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningMin($numbers) as $runningMin) {
            $result[] = $runningMin;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        return [
            [
                new Fixture\ArrayIteratorFixture([]),
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([0]),
                [0],
            ],
            [
                new Fixture\ArrayIteratorFixture([1]),
                [1],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 1, 1]),
                [1, 1, 1],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                [1, 1, 1],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5]),
                [1, 1, 1, 1, 1],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, -4, 5]),
                [1, 1, 1, -4, -4],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, -4, -5]),
                [1, 1, 1, -4, -5],
            ],
        ];
    }

    /**
     * @test         runningMin traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $numbers
     * @param        array        $expected
     */
    public function testTraversables(\Traversable $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningMin($numbers) as $runningMin) {
            $result[] = $runningMin;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([]),
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([0]),
                [0],
            ],
            [
                new Fixture\IteratorAggregateFixture([1]),
                [1],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 1, 1]),
                [1, 1, 1],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                [1, 1, 1],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5]),
                [1, 1, 1, 1, 1],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, -4, 5]),
                [1, 1, 1, -4, -4],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, -4, -5]),
                [1, 1, 1, -4, -5],
            ],
        ];
    }
}

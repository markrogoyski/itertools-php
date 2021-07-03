<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

use IterTools\Math;
use IterTools\Tests\Fixture;

class RunningMaxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         runningMax array
     * @dataProvider dataProviderForArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testArray(array $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningMax($numbers) as $runningMax) {
            $result[] = $runningMax;
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
                [1, 2, 3],
            ],
            [
                [1, 2, 3, 4, 5],
                [1, 2, 3, 4, 5],
            ],
            [
                [1, 2, 3, -4, 5],
                [1, 2, 3, 3, 5],
            ],
            [
                [1, 2, 3, -4, -5],
                [1, 2, 3, 3, 3],
            ],
            [
                [5, 4, 3, 2, 1],
                [5, 5, 5, 5, 5],
            ],
            [
                [10, 1, 9, 2, 8, 3, 7, 4, 5, 5, 10, 1, 0, -1],
                [10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10]
            ],
            [
                [1, 9, 2, 8, 3, 7, 4, 5, 5, 10, 1, 0, -1],
                [1, 9, 9, 9, 9, 9, 9, 9, 9, 10, 10, 10, 10],
            ],
        ];
    }

    /**
     * @test         runningMax array with initial value
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
        foreach (Math::runningMax($numbers, $initialValue) as $runningMax) {
            $result[] = $runningMax;
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
                [5, 5],
            ],
            [
                [1],
                5,
                [5, 5],
            ],
            [
                [1, 1, 1],
                5,
                [5, 5, 5, 5],
            ],
            [
                [1, 2, 3],
                5,
                [5, 5, 5, 5],
            ],
            [
                [1, 2, 3, 4, 5, 6],
                5,
                [5, 5, 5, 5, 5, 5, 6],
            ],
            [
                [1, 2, 3, -4, 5, 6],
                5,
                [5, 5, 5, 5, 5, 5, 6],
            ],
            [
                [1, 2, 3, -4, -5, 6],
                5,
                [5, 5, 5, 5, 5, 5, 6],
            ],
            [
                [5, 4, 3, 2, 1],
                5,
                [5, 5, 5, 5, 5, 5],
            ],
            [
                [1, 9, 2, 8, 3, 7, 4, 5, 5, 10, 1, 0, -1],
                5,
                [5, 5, 9, 9, 9, 9, 9, 9, 9, 9, 10, 10, 10, 10]
            ],
        ];
    }

    /**
     * @test         runningMax generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $numbers
     * @param        array      $expected
     */
    public function testGenerators(\Generator $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningMax($numbers) as $runningMax) {
            $result[] = $runningMax;
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
                [1, 2, 3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5]),
                [1, 2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, -4, 5]),
                [1, 2, 3, 3, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, -4, -5]),
                [1, 2, 3, 3, 3],
            ],
        ];
    }

    /**
     * @test         runningMax iterators
     * @dataProvider dataProviderForIterators
     * @param        iterable $numbers
     * @param        array    $expected
     */
    public function testIterators(iterable $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningMax($numbers) as $runningMax) {
            $result[] = $runningMax;
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
                [1, 2, 3],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5]),
                [1, 2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, -4, 5]),
                [1, 2, 3, 3, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, -4, -5]),
                [1, 2, 3, 3, 3],
            ],
        ];
    }

    /**
     * @test         runningMax traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $numbers
     * @param        array        $expected
     */
    public function testTraversables(\Traversable $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningMax($numbers) as $runningMax) {
            $result[] = $runningMax;
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
                [1, 2, 3],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5]),
                [1, 2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, -4, 5]),
                [1, 2, 3, 3, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, -4, -5]),
                [1, 2, 3, 3, 3],
            ],
        ];
    }
}

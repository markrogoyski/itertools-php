<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

use IterTools\Math;
use IterTools\Tests\Fixture;

class RunningAverageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         runningAverage array
     * @dataProvider dataProviderForArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testArray(array $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers) as $runningAverage) {
            $result[] = $runningAverage;
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
                [2],
                [2],
            ],
            [
                [1, 1, 1],
                [1, 1, 1],
            ],
            [
                [1, 2, 3],
                [1, 1.5, 2],
            ],
            [
                [2, 2, 2],
                [2, 2, 2],
            ],
            [
                [1, 3, 5],
                [1, 2, 3],
            ],
            [
                [1, 2, 3, 4, 5],
                [1, 1.5, 2, 10/4, 3],
            ],
            [
                [1, 2, 3, -4, 5],
                [1, 1.5, 2, 0.5, 7/5],
            ],
            [
                [1, 2, 3, -4, -5],
                [1, 1.5, 2, 0.5, -3/5],
            ],
        ];
    }

    /**
     * @test         runningAverage array with initial value
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
        foreach (Math::runningAverage($numbers, $initialValue) as $runningAverage) {
            $result[] = $runningAverage;
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
                [5, 5/2],
            ],
            [
                [1],
                5,
                [5, 3],
            ],
            [
                [2],
                5,
                [5, 7/2],
            ],
            [
                [1, 1, 1],
                5,
                [5, 3, 7/3, 2],
            ],
            [
                [1, 2, 3],
                5,
                [5, 3, 8/3, 11/4],
            ],
            [
                [2, 2, 2],
                5,
                [5, 7/2, 3, 11/4],
            ],
            [
                [1, 3, 5],
                5,
                [5, 3, 3, 14/4],
            ],
            [
                [1, 2, 3, 4, 5],
                5,
                [5, 3, 8/3, 11/4, 3, 20/6],
            ],
            [
                [1, 2, 3, -4, 5],
                5,
                [5, 3, 8/3, 11/4, 7/5, 2],
            ],
            [
                [1, 2, 3, -4, -5],
                5,
                [5, 3, 8/3, 11/4, 7/5, 2/6],
            ],
        ];
    }

    /**
     * @test         runningAverage generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $numbers
     * @param        array      $expected
     */
    public function testGenerators(\Generator $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers) as $runningAverage) {
            $result[] = $runningAverage;
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
                [1, 3/2, 2],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5]),
                [1, 3/2, 2, 10/4, 3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, -4, 5]),
                [1, 3/2, 2, 2/4, 7/5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, -4, -5]),
                [1, 3/2, 2, 2/4, -3/5],
            ],
        ];
    }

    /**
     * @test         runningAverage iterators
     * @dataProvider dataProviderForIterators
     * @param        iterable $numbers
     * @param        array    $expected
     */
    public function testIterators(iterable $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers) as $runningAverage) {
            $result[] = $runningAverage;
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
                [1, 3/2, 2],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5]),
                [1, 3/2, 2, 10/4, 3],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, -4, 5]),
                [1, 3/2, 2, 2/4, 7/5],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, -4, -5]),
                [1, 3/2, 2, 2/4, -3/5],
            ],
        ];
    }

    /**
     * @test         runningAverage traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $numbers
     * @param        array        $expected
     */
    public function testTraversables(\Traversable $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers) as $runningAverage) {
            $result[] = $runningAverage;
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
                [1, 3/2, 2],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5]),
                [1, 3/2, 2, 10/4, 3],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, -4, 5]),
                [1, 3/2, 2, 2/4, 7/5],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, -4, -5]),
                [1, 3/2, 2, 2/4, -3/5],
            ],
        ];
    }

    /**
     * @test         runningAverage iterator_to_array
     * @dataProvider dataProviderForArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testIteratorToArray(array $numbers, array $expected)
    {
        // Given
        $iterator = Math::runningAverage($numbers);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }
}

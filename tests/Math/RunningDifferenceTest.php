<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

use IterTools\Math;
use IterTools\Tests\Fixture;

class RunningDifferenceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         runningDifference array
     * @dataProvider dataProviderForArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testArray(array $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningDifference($numbers) as $runningDifference) {
            $result[] = $runningDifference;
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
                [-1],
            ],
            [
                [1, 1, 1],
                [-1, -2, -3],
            ],
            [
                [1, 2, 3],
                [-1, -3, -6],
            ],
            [
                [1, 2, 3, 4, 5],
                [-1, -3, -6, -10, -15],
            ],
            [
                [1, 2, 3, -4, 5],
                [-1, -3, -6, -2, -7],
            ],
            [
                [1, 2, 3, -4, -5],
                [-1, -3, -6, -2, 3],
            ],
        ];
    }

    /**
     * @test         runningDifference array with initial value
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
        foreach (Math::runningDifference($numbers, $initialValue) as $runningDifference) {
            $result[] = $runningDifference;
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
                [5, 4],
            ],
            [
                [1, 1, 1],
                5,
                [5, 4, 3, 2],
            ],
            [
                [1, 2, 3],
                5,
                [5, 4, 2, -1],
            ],
            [
                [1, 2, 3, 4, 5],
                5,
                [5, 4, 2, -1, -5, -10],
            ],
            [
                [1, 2, 3, -4, 5],
                5,
                [5, 4, 2, -1, 3, -2],
            ],
            [
                [1, 2, 3, -4, -5],
                5,
                [5, 4, 2, -1, 3, 8],
            ],
        ];
    }

    /**
     * @test         runningDifference generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $numbers
     * @param        array      $expected
     */
    public function testGenerators(\Generator $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningDifference($numbers) as $runningDifference) {
            $result[] = $runningDifference;
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
                [-1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 1, 1]),
                [-1, -2, -3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                [-1, -3, -6],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5]),
                [-1, -3, -6, -10, -15],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, -4, 5]),
                [-1, -3, -6, -2, -7],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, -4, -5]),
                [-1, -3, -6, -2, 3],
            ],
        ];
    }

    /**
     * @test         runningDifference iterators
     * @dataProvider dataProviderForIterators
     * @param        iterable $numbers
     * @param        array    $expected
     */
    public function testIterators(iterable $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningDifference($numbers) as $runningDifference) {
            $result[] = $runningDifference;
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
                [-1],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 1, 1]),
                [-1, -2, -3],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                [-1, -3, -6],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5]),
                [-1, -3, -6, -10, -15],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, -4, 5]),
                [-1, -3, -6, -2, -7],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, -4, -5]),
                [-1, -3, -6, -2, 3],
            ],
        ];
    }

    /**
     * @test         runningDifference traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $numbers
     * @param        array        $expected
     */
    public function testTraversables(\Traversable $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningDifference($numbers) as $runningDifference) {
            $result[] = $runningDifference;
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
                [-1],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 1, 1]),
                [-1, -2, -3],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                [-1, -3, -6],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5]),
                [-1, -3, -6, -10, -15],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, -4, 5]),
                [-1, -3, -6, -2, -7],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, -4, -5]),
                [-1, -3, -6, -2, 3],
            ],
        ];
    }

    /**
     * @test         runningDifference iterator_to_array
     * @dataProvider dataProviderForArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testIteratorToArray(array $numbers, array $expected)
    {
        // Given
        $iterator = Math::runningDifference($numbers);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }
}

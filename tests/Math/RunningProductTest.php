<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

use IterTools\Math;
use IterTools\Tests\Fixture;

class RunningProductTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         runningProduct array
     * @dataProvider dataProviderForArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testArray(array $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningProduct($numbers) as $runningProduct) {
            $result[] = $runningProduct;
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
                [2, 2, 2],
                [2, 4, 8],
            ],
            [
                [1, 2, 3],
                [1, 2, 6],
            ],
            [
                [1, 2, 3, 4, 5],
                [1, 2, 6, 24, 120],
            ],
            [
                [1, 2, 3, -4, 5],
                [1, 2, 6, -24, -120],
            ],
            [
                [1, 2, 3, -4, -5],
                [1, 2, 6, -24, 120],
            ],
        ];
    }

    /**
     * @test         runningProduct array with initial value
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
        foreach (Math::runningProduct($numbers, $initialValue) as $runningProduct) {
            $result[] = $runningProduct;
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
                [5, 5],
            ],
            [
                [1, 1, 1],
                5,
                [5, 5, 5, 5],
            ],
            [
                [2, 2, 2],
                5,
                [5, 10, 20, 40],
            ],
            [
                [1, 2, 3],
                5,
                [5, 5, 10, 30],
            ],
            [
                [1, 2, 3, 4, 5],
                5,
                [5, 5, 10, 30, 120, 600],
            ],
            [
                [1, 2, 3, -4, 5],
                5,
                [5, 5, 10, 30, -120, -600],
            ],
            [
                [1, 2, 3, -4, -5],
                5,
                [5, 5, 10, 30, -120, 600],
            ],
        ];
    }

    /**
     * @test         runningProduct generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $numbers
     * @param        array      $expected
     */
    public function testGenerators(\Generator $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningProduct($numbers) as $runningProduct) {
            $result[] = $runningProduct;
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
                [1, 2, 6],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5]),
                [1, 2, 6, 24, 120],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, -4, 5]),
                [1, 2, 6, -24, -120],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, -4, -5]),
                [1, 2, 6, -24, 120],
            ],
        ];
    }

    /**
     * @test         runningProduct iterators
     * @dataProvider dataProviderForIterators
     * @param        iterable $numbers
     * @param        array    $expected
     */
    public function testIterators(iterable $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningProduct($numbers) as $runningProduct) {
            $result[] = $runningProduct;
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
                [1, 2, 6],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5]),
                [1, 2, 6, 24, 120],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, -4, 5]),
                [1, 2, 6, -24, -120],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, -4, -5]),
                [1, 2, 6, -24, 120],
            ],
        ];
    }

    /**
     * @test         runningProduct traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $numbers
     * @param        array        $expected
     */
    public function testTraversables(\Traversable $numbers, array $expected)
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningProduct($numbers) as $runningProduct) {
            $result[] = $runningProduct;
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
                [1, 2, 6],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5]),
                [1, 2, 6, 24, 120],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, -4, 5]),
                [1, 2, 6, -24, -120],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, -4, -5]),
                [1, 2, 6, -24, 120],
            ],
        ];
    }
}

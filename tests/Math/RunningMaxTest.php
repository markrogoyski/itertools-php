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

    public static function dataProviderForArray(): array
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
            [
                [1, \NAN, 3],
                [1, 1, 3],
            ],
            [
                [5, \NAN, 3],
                [5, 5, 5],
            ],
            [
                [1, 2, \NAN, 3, \NAN, 5],
                [1, 2, 2, 3, 3, 5],
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

    public static function dataProviderForArrayWithInitialValue(): array
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

    public static function dataProviderForGenerators(): array
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
            [
                Fixture\GeneratorFixture::getGenerator([1, \NAN, 3]),
                [1, 1, 3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([5, \NAN, 3]),
                [5, 5, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, \NAN, 3, \NAN, 5]),
                [1, 2, 2, 3, 3, 5],
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

    public static function dataProviderForIterators(): array
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
            [
                new Fixture\ArrayIteratorFixture([1, \NAN, 3]),
                [1, 1, 3],
            ],
            [
                new Fixture\ArrayIteratorFixture([5, \NAN, 3]),
                [5, 5, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, \NAN, 3, \NAN, 5]),
                [1, 2, 2, 3, 3, 5],
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

    public static function dataProviderForTraversables(): array
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
            [
                new Fixture\IteratorAggregateFixture([1, \NAN, 3]),
                [1, 1, 3],
            ],
            [
                new Fixture\IteratorAggregateFixture([5, \NAN, 3]),
                [5, 5, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, \NAN, 3, \NAN, 5]),
                [1, 2, 2, 3, 3, 5],
            ],
        ];
    }

    /**
     * @test         runningMax iterator_to_array
     * @dataProvider dataProviderForArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testIteratorToArray(array $numbers, array $expected)
    {
        // Given
        $iterator = Math::runningMax($numbers);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test runningMax leading NaN yields NaN then recovers
     */
    public function testLeadingNanArray(): void
    {
        // When
        $result = \iterator_to_array(Math::runningMax([\NAN, 1, 2]));

        // Then
        $this->assertNan($result[0]);
        $this->assertEquals(1, $result[1]);
        $this->assertEquals(2, $result[2]);
    }

    /**
     * @test runningMax all NaN yields all NaN
     */
    public function testAllNanArray(): void
    {
        // When
        $result = \iterator_to_array(Math::runningMax([\NAN, \NAN, \NAN]));

        // Then
        $this->assertCount(3, $result);
        $this->assertNan($result[0]);
        $this->assertNan($result[1]);
        $this->assertNan($result[2]);
    }

    /**
     * @test runningMax single NaN yields NaN
     */
    public function testSingleNanArray(): void
    {
        // When
        $result = \iterator_to_array(Math::runningMax([\NAN]));

        // Then
        $this->assertCount(1, $result);
        $this->assertNan($result[0]);
    }

    /**
     * @test runningMax NaN initialValue is yielded then ignored for accumulation
     */
    public function testNanInitialValueArray(): void
    {
        // When
        $result = \iterator_to_array(Math::runningMax([1, 2, 3], \NAN));

        // Then
        $this->assertCount(4, $result);
        $this->assertNan($result[0]);
        $this->assertEquals(1, $result[1]);
        $this->assertEquals(2, $result[2]);
        $this->assertEquals(3, $result[3]);
    }

    /**
     * @test runningMax NaN initialValue with NaN in stream
     */
    public function testNanInitialValueWithNanInStreamArray(): void
    {
        // When
        $result = \iterator_to_array(Math::runningMax([\NAN, 1, 2], \NAN));

        // Then
        $this->assertCount(4, $result);
        $this->assertNan($result[0]);
        $this->assertNan($result[1]);
        $this->assertEquals(1, $result[2]);
        $this->assertEquals(2, $result[3]);
    }
}

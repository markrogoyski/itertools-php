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
    public function testArray(array $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningProduct($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python:
     *   list(itertools.accumulate(data, operator.mul))
     * The int-vs-float result type is the library's contract, not the oracle's: an int result
     * past the int64 range is a float.
     */
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
            [
                [0.5, 0.5, 0.5],
                [0.5, 0.25, 0.125],
            ],
            [
                [0.1, 0.2, 0.3],
                [0.1, 0.020000000000000004, 0.006000000000000001],
            ],
            [
                [1.5, -2.0, 4.0],
                [1.5, -3.0, -12.0],
            ],
            [
                [2, 0.5, 3],
                [2, 1.0, 3.0],
            ],
            [
                [1.0E+200, 1.0E+200],
                [1.0E+200, \INF],
            ],
            [
                [1.0E-200, 1.0E-200],
                [1.0E-200, 0.0],
            ],
        ];
    }

    /**
     * @test         runningProduct array of integers beyond 2^31
     * @dataProvider dataProviderForArray64Bit
     * @param        array $numbers
     * @param        array $expected
     */
    public function testArray64Bit(array $numbers, array $expected): void
    {
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers.');
        }

        // Given
        $result = [];

        // When
        foreach (Math::runningProduct($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python:
     *   list(itertools.accumulate(data, operator.mul))
     * The int-vs-float result type is the library's contract, not the oracle's: an int result
     * past the int64 range is a float.
     */
    public static function dataProviderForArray64Bit(): array
    {
        return [
            [
                [4294967296, 2],
                [4294967296, 8589934592],
            ],
            [
                [3037000499, 3037000499],
                [3037000499, 9223372030926249001],
            ],
            [
                [4294967296, 4294967296],
                [4294967296, 1.8446744073709552E+19],
            ],
            [
                [\PHP_INT_MAX, 2],
                [\PHP_INT_MAX, 1.8446744073709552E+19],
            ],
        ];
    }

    /**
     * @test         runningProduct array with initial value
     * @dataProvider dataProviderForArrayWithInitialValue
     * @param        array     $numbers
     * @param        int|float $initialValue
     * @param        array     $expected
     */
    public function testArrayWithInitialValue(array $numbers, int|float $initialValue, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningProduct($numbers, $initialValue) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python:
     *   list(itertools.accumulate(data, operator.mul, initial=initial))
     * The int-vs-float result type is the library's contract, not the oracle's: an int result
     * past the int64 range is a float.
     */
    public static function dataProviderForArrayWithInitialValue(): array
    {
        return [
            [
                [],
                0,
                [0],
            ],
            [
                [1, 2, 3],
                0,
                [0, 0, 0, 0],
            ],
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
            [
                [1, 2],
                0.5,
                [0.5, 0.5, 1.0],
            ],
            [
                [0.2, 0.3],
                0.1,
                [0.1, 0.020000000000000004, 0.006000000000000001],
            ],
            [
                [1.5, -2.5],
                -5,
                [-5, -7.5, 18.75],
            ],
        ];
    }

    /**
     * @test         runningProduct array of integers beyond 2^31 with initial value
     * @dataProvider dataProviderForArrayWithInitialValue64Bit
     * @param        array $numbers
     * @param        int   $initialValue
     * @param        array $expected
     */
    public function testArrayWithInitialValue64Bit(array $numbers, int $initialValue, array $expected): void
    {
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers.');
        }

        // Given
        $result = [];

        // When
        foreach (Math::runningProduct($numbers, $initialValue) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python:
     *   list(itertools.accumulate(data, operator.mul, initial=initial))
     * The int-vs-float result type is the library's contract, not the oracle's: an int result
     * past the int64 range is a float.
     */
    public static function dataProviderForArrayWithInitialValue64Bit(): array
    {
        return [
            [
                [2, 3],
                4294967296,
                [4294967296, 8589934592, 25769803776],
            ],
            [
                [2],
                \PHP_INT_MAX,
                [\PHP_INT_MAX, 1.8446744073709552E+19],
            ],
            [
                [2],
                \PHP_INT_MIN,
                [\PHP_INT_MIN, -1.8446744073709552E+19],
            ],
        ];
    }

    /**
     * @test         runningProduct generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $numbers
     * @param        array      $expected
     */
    public function testGenerators(\Generator $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningProduct($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python:
     *   list(itertools.accumulate(data, operator.mul))
     * The int-vs-float result type is the library's contract, not the oracle's: an int result
     * past the int64 range is a float.
     */
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
                Fixture\GeneratorFixture::getGenerator([2]),
                [2],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 1, 1]),
                [1, 1, 1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([2, 2, 2]),
                [2, 4, 8],
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
            [
                Fixture\GeneratorFixture::getGenerator([0.5, 0.5, 0.5]),
                [0.5, 0.25, 0.125],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0.1, 0.2, 0.3]),
                [0.1, 0.020000000000000004, 0.006000000000000001],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1.5, -2.0, 4.0]),
                [1.5, -3.0, -12.0],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([2, 0.5, 3]),
                [2, 1.0, 3.0],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1.0E+200, 1.0E+200]),
                [1.0E+200, \INF],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1.0E-200, 1.0E-200]),
                [1.0E-200, 0.0],
            ],
        ];
    }

    /**
     * @test         runningProduct iterators
     * @dataProvider dataProviderForIterators
     * @param        iterable $numbers
     * @param        array    $expected
     */
    public function testIterators(iterable $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningProduct($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python:
     *   list(itertools.accumulate(data, operator.mul))
     * The int-vs-float result type is the library's contract, not the oracle's: an int result
     * past the int64 range is a float.
     */
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
                new Fixture\ArrayIteratorFixture([2]),
                [2],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 1, 1]),
                [1, 1, 1],
            ],
            [
                new Fixture\ArrayIteratorFixture([2, 2, 2]),
                [2, 4, 8],
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
            [
                new Fixture\ArrayIteratorFixture([0.5, 0.5, 0.5]),
                [0.5, 0.25, 0.125],
            ],
            [
                new Fixture\ArrayIteratorFixture([0.1, 0.2, 0.3]),
                [0.1, 0.020000000000000004, 0.006000000000000001],
            ],
            [
                new Fixture\ArrayIteratorFixture([1.5, -2.0, 4.0]),
                [1.5, -3.0, -12.0],
            ],
            [
                new Fixture\ArrayIteratorFixture([2, 0.5, 3]),
                [2, 1.0, 3.0],
            ],
            [
                new Fixture\ArrayIteratorFixture([1.0E+200, 1.0E+200]),
                [1.0E+200, \INF],
            ],
            [
                new Fixture\ArrayIteratorFixture([1.0E-200, 1.0E-200]),
                [1.0E-200, 0.0],
            ],
        ];
    }

    /**
     * @test         runningProduct traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $numbers
     * @param        array        $expected
     */
    public function testTraversables(\Traversable $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningProduct($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python:
     *   list(itertools.accumulate(data, operator.mul))
     * The int-vs-float result type is the library's contract, not the oracle's: an int result
     * past the int64 range is a float.
     */
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
                new Fixture\IteratorAggregateFixture([2]),
                [2],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 1, 1]),
                [1, 1, 1],
            ],
            [
                new Fixture\IteratorAggregateFixture([2, 2, 2]),
                [2, 4, 8],
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
            [
                new Fixture\IteratorAggregateFixture([0.5, 0.5, 0.5]),
                [0.5, 0.25, 0.125],
            ],
            [
                new Fixture\IteratorAggregateFixture([0.1, 0.2, 0.3]),
                [0.1, 0.020000000000000004, 0.006000000000000001],
            ],
            [
                new Fixture\IteratorAggregateFixture([1.5, -2.0, 4.0]),
                [1.5, -3.0, -12.0],
            ],
            [
                new Fixture\IteratorAggregateFixture([2, 0.5, 3]),
                [2, 1.0, 3.0],
            ],
            [
                new Fixture\IteratorAggregateFixture([1.0E+200, 1.0E+200]),
                [1.0E+200, \INF],
            ],
            [
                new Fixture\IteratorAggregateFixture([1.0E-200, 1.0E-200]),
                [1.0E-200, 0.0],
            ],
        ];
    }

    /**
     * @test         runningProduct iterator_to_array
     * @dataProvider dataProviderForArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testIteratorToArray(array $numbers, array $expected): void
    {
        // Given
        $iterator = Math::runningProduct($numbers);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertSame($expected, $result);
    }
}

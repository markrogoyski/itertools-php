<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

use IterTools\Math;
use IterTools\Tests\Fixture;

class RunningTotalTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         runningTotal array
     * @dataProvider dataProviderForArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testArray(array $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningTotal($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python:
     *   list(itertools.accumulate(data))
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
                [1, 1, 1],
                [1, 2, 3],
            ],
            [
                [1, 2, 3],
                [1, 3, 6],
            ],
            [
                [1, 2, 3, 4, 5],
                [1, 3, 6, 10, 15],
            ],
            [
                [1, 2, 3, -4, 5],
                [1, 3, 6, 2, 7],
            ],
            [
                [1, 2, 3, -4, -5],
                [1, 3, 6, 2, -3],
            ],
            [
                [0.5],
                [0.5],
            ],
            [
                [0.1, 0.2, 0.3],
                [0.1, 0.30000000000000004, 0.6000000000000001],
            ],
            [
                [1.5, 2.5, -4.0],
                [1.5, 4.0, 0.0],
            ],
            [
                [1, 2.5, 3],
                [1, 3.5, 6.5],
            ],
            [
                [0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1],
                [0.1, 0.2, 0.30000000000000004, 0.4, 0.5, 0.6, 0.7, 0.7999999999999999, 0.8999999999999999, 0.9999999999999999],
            ],
            [
                [1.7976931348623157E+308, 1.7976931348623157E+308],
                [1.7976931348623157E+308, \INF],
            ],
        ];
    }

    /**
     * @test         runningTotal array of integers beyond 2^31
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
        foreach (Math::runningTotal($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python:
     *   list(itertools.accumulate(data))
     * The int-vs-float result type is the library's contract, not the oracle's: an int result
     * past the int64 range is a float.
     */
    public static function dataProviderForArray64Bit(): array
    {
        return [
            [
                [4294967296, 4294967296],
                [4294967296, 8589934592],
            ],
            [
                [9007199254740992, 1, 1],
                [9007199254740992, 9007199254740993, 9007199254740994],
            ],
            [
                [\PHP_INT_MAX, 1],
                [\PHP_INT_MAX, 9.223372036854776E+18],
            ],
            [
                [\PHP_INT_MIN, -1],
                [\PHP_INT_MIN, -9.223372036854776E+18],
            ],
        ];
    }

    /**
     * @test         runningTotal array with initial value
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
        foreach (Math::runningTotal($numbers, $initialValue) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python:
     *   list(itertools.accumulate(data, initial=initial))
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
                [0, 1, 3, 6],
            ],
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
                [5, 6],
            ],
            [
                [1, 1, 1],
                5,
                [5, 6, 7, 8],
            ],
            [
                [1, 2, 3],
                5,
                [5, 6, 8, 11],
            ],
            [
                [1, 2, 3, 4, 5],
                5,
                [5, 6, 8, 11, 15, 20],
            ],
            [
                [1, 2, 3, -4, 5],
                5,
                [5, 6, 8, 11, 7, 12],
            ],
            [
                [1, 2, 3, -4, -5],
                5,
                [5, 6, 8, 11, 7, 2],
            ],
            [
                [1, 2],
                0.5,
                [0.5, 1.5, 3.5],
            ],
            [
                [0.2, 0.3],
                0.1,
                [0.1, 0.30000000000000004, 0.6000000000000001],
            ],
            [
                [1.5, -2.5],
                -5,
                [-5, -3.5, -6.0],
            ],
        ];
    }

    /**
     * @test         runningTotal array of integers beyond 2^31 with initial value
     * @dataProvider dataProviderForArrayWithInitialValue64Bit
     * @param        array     $numbers
     * @param        int|float $initialValue
     * @param        array     $expected
     */
    public function testArrayWithInitialValue64Bit(array $numbers, int|float $initialValue, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers.');
        }
        $this->assertIsInt($initialValue);
        $result = [];

        // When
        foreach (Math::runningTotal($numbers, $initialValue) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python:
     *   list(itertools.accumulate(data, initial=initial))
     * The int-vs-float result type is the library's contract, not the oracle's: an int result
     * past the int64 range is a float.
     */
    public static function dataProviderForArrayWithInitialValue64Bit(): array
    {
        return [
            [
                [1, 2],
                4294967296,
                [4294967296, 4294967297, 4294967299],
            ],
            [
                [1],
                \PHP_INT_MAX,
                [\PHP_INT_MAX, 9.223372036854776E+18],
            ],
            [
                [-1],
                \PHP_INT_MIN,
                [\PHP_INT_MIN, -9.223372036854776E+18],
            ],
        ];
    }

    /**
     * @test         runningTotal generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $numbers
     * @param        array      $expected
     */
    public function testGenerators(\Generator $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningTotal($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python:
     *   list(itertools.accumulate(data))
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
                Fixture\GeneratorFixture::getGenerator([1, 1, 1]),
                [1, 2, 3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                [1, 3, 6],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5]),
                [1, 3, 6, 10, 15],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, -4, 5]),
                [1, 3, 6, 2, 7],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, -4, -5]),
                [1, 3, 6, 2, -3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0.5]),
                [0.5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0.1, 0.2, 0.3]),
                [0.1, 0.30000000000000004, 0.6000000000000001],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1.5, 2.5, -4.0]),
                [1.5, 4.0, 0.0],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2.5, 3]),
                [1, 3.5, 6.5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1]),
                [0.1, 0.2, 0.30000000000000004, 0.4, 0.5, 0.6, 0.7, 0.7999999999999999, 0.8999999999999999, 0.9999999999999999],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1.7976931348623157E+308, 1.7976931348623157E+308]),
                [1.7976931348623157E+308, \INF],
            ],
        ];
    }

    /**
     * @test         runningTotal iterators
     * @dataProvider dataProviderForIterators
     * @param        iterable $numbers
     * @param        array    $expected
     */
    public function testIterators(iterable $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningTotal($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python:
     *   list(itertools.accumulate(data))
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
                new Fixture\ArrayIteratorFixture([1, 1, 1]),
                [1, 2, 3],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                [1, 3, 6],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5]),
                [1, 3, 6, 10, 15],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, -4, 5]),
                [1, 3, 6, 2, 7],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, -4, -5]),
                [1, 3, 6, 2, -3],
            ],
            [
                new Fixture\ArrayIteratorFixture([0.5]),
                [0.5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0.1, 0.2, 0.3]),
                [0.1, 0.30000000000000004, 0.6000000000000001],
            ],
            [
                new Fixture\ArrayIteratorFixture([1.5, 2.5, -4.0]),
                [1.5, 4.0, 0.0],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2.5, 3]),
                [1, 3.5, 6.5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1]),
                [0.1, 0.2, 0.30000000000000004, 0.4, 0.5, 0.6, 0.7, 0.7999999999999999, 0.8999999999999999, 0.9999999999999999],
            ],
            [
                new Fixture\ArrayIteratorFixture([1.7976931348623157E+308, 1.7976931348623157E+308]),
                [1.7976931348623157E+308, \INF],
            ],
        ];
    }

    /**
     * @test         runningTotal traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $numbers
     * @param        array        $expected
     */
    public function testTraversables(\Traversable $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningTotal($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python:
     *   list(itertools.accumulate(data))
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
                new Fixture\IteratorAggregateFixture([1, 1, 1]),
                [1, 2, 3],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                [1, 3, 6],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5]),
                [1, 3, 6, 10, 15],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, -4, 5]),
                [1, 3, 6, 2, 7],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, -4, -5]),
                [1, 3, 6, 2, -3],
            ],
            [
                new Fixture\IteratorAggregateFixture([0.5]),
                [0.5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0.1, 0.2, 0.3]),
                [0.1, 0.30000000000000004, 0.6000000000000001],
            ],
            [
                new Fixture\IteratorAggregateFixture([1.5, 2.5, -4.0]),
                [1.5, 4.0, 0.0],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2.5, 3]),
                [1, 3.5, 6.5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1]),
                [0.1, 0.2, 0.30000000000000004, 0.4, 0.5, 0.6, 0.7, 0.7999999999999999, 0.8999999999999999, 0.9999999999999999],
            ],
            [
                new Fixture\IteratorAggregateFixture([1.7976931348623157E+308, 1.7976931348623157E+308]),
                [1.7976931348623157E+308, \INF],
            ],
        ];
    }

    /**
     * @test         runningTotal iterator_to_array
     * @dataProvider dataProviderForArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testIteratorToArray(array $numbers, array $expected): void
    {
        // Given
        $iterator = Math::runningTotal($numbers);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertSame($expected, $result);
    }
}

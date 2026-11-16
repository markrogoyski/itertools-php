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

    /**
     * @test         runningMax array of an int beyond 2^53 and a float, which compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionArray
     * @param        array        $numbers
     * @param        array        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionArray(array $numbers, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Math::runningMax($numbers) as $runningMax) {
            $result[] = $runningMax;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which compares int and float by exact value:
     *   list(itertools.accumulate(data, max))
     * The data has no exact int/float tie, so the tie choice doesn't decide any result.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionArray(): array
    {
        return [
            'float 2.0^53 then int 2^53 + 1, equal as floats' => [
                [9007199254740992.0, 9007199254740993],
                [9007199254740992.0, 9007199254740993],
            ],
            'int 2^53 + 1 then float 2.0^53' => [
                [9007199254740993, 9007199254740992.0],
                [9007199254740993, 9007199254740993],
            ],
            'floats and an int that equal as floats' => [
                [9007199254740992.0, 9007199254740993, 9007199254740994.0, 9007199254740993],
                [9007199254740992.0, 9007199254740993, 9007199254740994.0, 9007199254740994.0],
            ],
            'int PHP_INT_MAX then float 2.0^63' => [
                [9223372036854775807, 9.223372036854776e+18],
                [9223372036854775807, 9.223372036854776e+18],
            ],
        ];
    }

    /**
     * @test         runningMax generators of an int beyond 2^53 and a float, which compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionGenerators
     * @param        \Generator   $numbers
     * @param        array        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionGenerators(\Generator $numbers, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Math::runningMax($numbers) as $runningMax) {
            $result[] = $runningMax;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which compares int and float by exact value:
     *   list(itertools.accumulate(data, max))
     * The data has no exact int/float tie, so the tie choice doesn't decide any result.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionGenerators(): array
    {
        $gen = static function (array $data) {
            return Fixture\GeneratorFixture::getGenerator($data);
        };

        return [
            'float 2.0^53 then int 2^53 + 1, equal as floats' => [
                $gen([9007199254740992.0, 9007199254740993]),
                [9007199254740992.0, 9007199254740993],
            ],
            'int 2^53 + 1 then float 2.0^53' => [
                $gen([9007199254740993, 9007199254740992.0]),
                [9007199254740993, 9007199254740993],
            ],
            'floats and an int that equal as floats' => [
                $gen([9007199254740992.0, 9007199254740993, 9007199254740994.0, 9007199254740993]),
                [9007199254740992.0, 9007199254740993, 9007199254740994.0, 9007199254740994.0],
            ],
            'int PHP_INT_MAX then float 2.0^63' => [
                $gen([9223372036854775807, 9.223372036854776e+18]),
                [9223372036854775807, 9.223372036854776e+18],
            ],
        ];
    }

    /**
     * @test         runningMax iterators of an int beyond 2^53 and a float, which compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionIterators
     * @param        \Iterator    $numbers
     * @param        array        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionIterators(\Iterator $numbers, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Math::runningMax($numbers) as $runningMax) {
            $result[] = $runningMax;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which compares int and float by exact value:
     *   list(itertools.accumulate(data, max))
     * The data has no exact int/float tie, so the tie choice doesn't decide any result.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionIterators(): array
    {
        $iter = static function (array $data) {
            return new Fixture\ArrayIteratorFixture($data);
        };

        return [
            'float 2.0^53 then int 2^53 + 1, equal as floats' => [
                $iter([9007199254740992.0, 9007199254740993]),
                [9007199254740992.0, 9007199254740993],
            ],
            'int 2^53 + 1 then float 2.0^53' => [
                $iter([9007199254740993, 9007199254740992.0]),
                [9007199254740993, 9007199254740993],
            ],
            'floats and an int that equal as floats' => [
                $iter([9007199254740992.0, 9007199254740993, 9007199254740994.0, 9007199254740993]),
                [9007199254740992.0, 9007199254740993, 9007199254740994.0, 9007199254740994.0],
            ],
            'int PHP_INT_MAX then float 2.0^63' => [
                $iter([9223372036854775807, 9.223372036854776e+18]),
                [9223372036854775807, 9.223372036854776e+18],
            ],
        ];
    }

    /**
     * @test         runningMax traversables of an int beyond 2^53 and a float, which compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionTraversables
     * @param        \Traversable $numbers
     * @param        array        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionTraversables(\Traversable $numbers, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Math::runningMax($numbers) as $runningMax) {
            $result[] = $runningMax;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which compares int and float by exact value:
     *   list(itertools.accumulate(data, max))
     * The data has no exact int/float tie, so the tie choice doesn't decide any result.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionTraversables(): array
    {
        $trav = static function (array $data) {
            return new Fixture\IteratorAggregateFixture($data);
        };

        return [
            'float 2.0^53 then int 2^53 + 1, equal as floats' => [
                $trav([9007199254740992.0, 9007199254740993]),
                [9007199254740992.0, 9007199254740993],
            ],
            'int 2^53 + 1 then float 2.0^53' => [
                $trav([9007199254740993, 9007199254740992.0]),
                [9007199254740993, 9007199254740993],
            ],
            'floats and an int that equal as floats' => [
                $trav([9007199254740992.0, 9007199254740993, 9007199254740994.0, 9007199254740993]),
                [9007199254740992.0, 9007199254740993, 9007199254740994.0, 9007199254740994.0],
            ],
            'int PHP_INT_MAX then float 2.0^63' => [
                $trav([9223372036854775807, 9.223372036854776e+18]),
                [9223372036854775807, 9.223372036854776e+18],
            ],
        ];
    }

    /**
     * @test         runningMax of tied values returns the first of them, whatever the PHP version
     * @dataProvider dataProviderForTieKeepsFirst
     * @param        array $data
     * @param        array $expected
     */
    public function testTieKeepsFirstArray(array $data, array $expected): void
    {
        // When
        $result = \iterator_to_array(Math::runningMax($data), false);

        // Then (-0.0 === 0.0, so compare the exported forms, which differ in the sign)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         runningMax of tied values in a generator returns the first of them, whatever the PHP version
     * @dataProvider dataProviderForTieKeepsFirst
     * @param        array $data
     * @param        array $expected
     */
    public function testTieKeepsFirstGenerators(array $data, array $expected): void
    {
        // Given
        $iterable = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = \iterator_to_array(Math::runningMax($iterable), false);

        // Then (-0.0 === 0.0, so compare the exported forms, which differ in the sign)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         runningMax of tied values in an iterator returns the first of them, whatever the PHP version
     * @dataProvider dataProviderForTieKeepsFirst
     * @param        array $data
     * @param        array $expected
     */
    public function testTieKeepsFirstIterators(array $data, array $expected): void
    {
        // Given
        $iterable = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = \iterator_to_array(Math::runningMax($iterable), false);

        // Then (-0.0 === 0.0, so compare the exported forms, which differ in the sign)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         runningMax of tied values in an IteratorAggregate returns the first of them, whatever the PHP version
     * @dataProvider dataProviderForTieKeepsFirst
     * @param        array $data
     * @param        array $expected
     */
    public function testTieKeepsFirstTraversables(array $data, array $expected): void
    {
        // Given
        $iterable = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = \iterator_to_array(Math::runningMax($iterable), false);

        // Then (-0.0 === 0.0, so compare the exported forms, which differ in the sign)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python, which compares int and float by exact value and returns the first of
     * equal values:
     *   list(itertools.accumulate(data, max))
     */
    public static function dataProviderForTieKeepsFirst(): array
    {
        return [
            'int then equal float' => [
                [1, 1.0],
                [1, 1],
            ],
            'float then equal int' => [
                [1.0, 1],
                [1.0, 1.0],
            ],
            'int zero then negative zero' => [
                [0, -0.0],
                [0, 0],
            ],
            'negative zero then int zero' => [
                [-0.0, 0],
                [-0.0, -0.0],
            ],
            'negative zero then zero' => [
                [-0.0, 0.0],
                [-0.0, -0.0],
            ],
            'zero then negative zero' => [
                [0.0, -0.0],
                [0.0, 0.0],
            ],
            'tied minimum after a larger value' => [
                [2, 1, 1.0],
                [2, 2, 2],
            ],
            'tied maximum after a smaller value' => [
                [0, 1.0, 1],
                [0, 1.0, 1.0],
            ],
        ];
    }
}

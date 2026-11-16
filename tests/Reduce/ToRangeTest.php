<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToRangeTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test         toRange array
     * @dataProvider dataProviderForArray
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testArray(array $data, int|float $expected): void
    {
        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * Numeric strings are converted as PHP does: integer strings to int, the rest to float.
     * The int-vs-float result type is the library's contract, not the oracle's.
     */
    public static function dataProviderForArray(): array
    {
        return [
            [
                [0],
                0,
            ],
            [
                [1],
                0,
            ],
            [
                [-1],
                0,
            ],
            [
                [-1, -3, -5],
                4,
            ],
            [
                [3, 1, 2, -3, -1, -2],
                6,
            ],
            [
                [2.2, 3.3, 1.1],
                2.1999999999999997,
            ],
            [
                [2, 3.3, 1.1],
                2.1999999999999997,
            ],
            [
                [2.2, -3.3, -1.1, 2.2, 5.5],
                8.8,
            ],
            [
                ['2.2', '-3.3', '-1.1', '2.2', '5.5'],
                8.8,
            ],
            [
                ['3', '4', '1'],
                3,
            ],
            [
                [2, -3.3, '-1.1', 2.2, '5'],
                8.3,
            ],
        ];
    }

    /**
     * @test         toRange generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        int|float  $expected
     */
    public function testGenerators(\Generator $data, int|float $expected): void
    {
        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * Numeric strings are converted as PHP does: integer strings to int, the rest to float.
     * The int-vs-float result type is the library's contract, not the oracle's.
     */
    public static function dataProviderForGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([0]),
                0,
            ],
            [
                $gen([1]),
                0,
            ],
            [
                $gen([-1]),
                0,
            ],
            [
                $gen([-1, -3, -5]),
                4,
            ],
            [
                $gen([3, 1, 2, -3, -1, -2]),
                6,
            ],
            [
                $gen([2.2, 3.3, 1.1]),
                2.1999999999999997,
            ],
            [
                $gen([2, 3.3, 1.1]),
                2.1999999999999997,
            ],
            [
                $gen([2.2, -3.3, -1.1, 2.2, 5.5]),
                8.8,
            ],
            [
                $gen(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                8.8,
            ],
            [
                $gen(['3', '4', '1']),
                3,
            ],
            [
                $gen([2, -3.3, '-1.1', 2.2, '5']),
                8.3,
            ],
        ];
    }

    /**
     * @test         toRange iterators
     * @dataProvider dataProviderForIterators
     * @param        \Iterator $data
     * @param        int|float $expected
     */
    public function testIterators(\Iterator $data, int|float $expected): void
    {
        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * Numeric strings are converted as PHP does: integer strings to int, the rest to float.
     * The int-vs-float result type is the library's contract, not the oracle's.
     */
    public static function dataProviderForIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $iter([0]),
                0,
            ],
            [
                $iter([1]),
                0,
            ],
            [
                $iter([-1]),
                0,
            ],
            [
                $iter([-1, -3, -5]),
                4,
            ],
            [
                $iter([3, 1, 2, -3, -1, -2]),
                6,
            ],
            [
                $iter([2.2, 3.3, 1.1]),
                2.1999999999999997,
            ],
            [
                $iter([2, 3.3, 1.1]),
                2.1999999999999997,
            ],
            [
                $iter([2.2, -3.3, -1.1, 2.2, 5.5]),
                8.8,
            ],
            [
                $iter(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                8.8,
            ],
            [
                $iter(['3', '4', '1']),
                3,
            ],
            [
                $iter([2, -3.3, '-1.1', 2.2, '5']),
                8.3,
            ],
        ];
    }

    /**
     * @test         toRange traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        int|float    $expected
     */
    public function testTraversables(\Traversable $data, int|float $expected): void
    {
        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * Numeric strings are converted as PHP does: integer strings to int, the rest to float.
     * The int-vs-float result type is the library's contract, not the oracle's.
     */
    public static function dataProviderForTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([0]),
                0,
            ],
            [
                $trav([1]),
                0,
            ],
            [
                $trav([-1]),
                0,
            ],
            [
                $trav([-1, -3, -5]),
                4,
            ],
            [
                $trav([3, 1, 2, -3, -1, -2]),
                6,
            ],
            [
                $trav([2.2, 3.3, 1.1]),
                2.1999999999999997,
            ],
            [
                $trav([2, 3.3, 1.1]),
                2.1999999999999997,
            ],
            [
                $trav([2.2, -3.3, -1.1, 2.2, 5.5]),
                8.8,
            ],
            [
                $trav(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                8.8,
            ],
            [
                $trav(['3', '4', '1']),
                3,
            ],
            [
                $trav([2, -3.3, '-1.1', 2.2, '5']),
                8.3,
            ],
        ];
    }

    /**
     * @test         toRange returns zero on empty
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptyReturnsZero(iterable $data): void
    {
        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame(0, $result);
    }

    /**
     * @test         toRange array of an int beyond 2^53 and a float, which compare equal or subtract wrongly as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionArray
     * @param        array        $data
     * @param        float        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionArray(array $data, float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact comparison and rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * NAN is skipped, and integer strings are converted to int, as PHP does.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionArray(): array
    {
        return [
            'int 2^53 + 1 below float 2.0^53 + 2' => [
                [9007199254740993, 9007199254740994.0],
                1.0,
            ],
            'int 2^53 + 1 above float 2.0^53, equal as floats' => [
                [9007199254740993, 9007199254740992.0],
                1.0,
            ],
            'float 2.0^53 first, below int 2^53 + 1, equal as floats' => [
                [9007199254740992.0, 9007199254740993],
                1.0,
            ],
            'minimum is the float that equals the last int as floats' => [
                [9007199254740994, 9007199254740992.0, 9007199254740993],
                2.0,
            ],
            'minimum is the first float, maximum the last int' => [
                [9007199254740992.0, 9007199254740993, 9007199254740994],
                2.0,
            ],
            'negative int -(2^53 + 1) below float -2.0^53' => [
                [-9007199254740993, -9007199254740992.0],
                1.0,
            ],
            'int 2^62 + 1 below float 2.0^62 + 1024' => [
                [4611686018427387905, 4611686018427388928.0],
                1023.0,
            ],
            'NAN skipped' => [
                [9007199254740993, \NAN, 9007199254740992.0],
                1.0,
            ],
            'integer string 2^53 + 1 above float 2.0^53' => [
                ['9007199254740993', 9007199254740992.0],
                1.0,
            ],
        ];
    }

    /**
     * @test         toRange generators of an int beyond 2^53 and a float, which compare equal or subtract wrongly as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionGenerators
     * @param        \Generator   $data
     * @param        float        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionGenerators(\Generator $data, float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact comparison and rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * NAN is skipped, and integer strings are converted to int, as PHP does.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            'int 2^53 + 1 below float 2.0^53 + 2' => [
                $gen([9007199254740993, 9007199254740994.0]),
                1.0,
            ],
            'int 2^53 + 1 above float 2.0^53, equal as floats' => [
                $gen([9007199254740993, 9007199254740992.0]),
                1.0,
            ],
            'float 2.0^53 first, below int 2^53 + 1, equal as floats' => [
                $gen([9007199254740992.0, 9007199254740993]),
                1.0,
            ],
            'minimum is the float that equals the last int as floats' => [
                $gen([9007199254740994, 9007199254740992.0, 9007199254740993]),
                2.0,
            ],
            'minimum is the first float, maximum the last int' => [
                $gen([9007199254740992.0, 9007199254740993, 9007199254740994]),
                2.0,
            ],
            'negative int -(2^53 + 1) below float -2.0^53' => [
                $gen([-9007199254740993, -9007199254740992.0]),
                1.0,
            ],
            'int 2^62 + 1 below float 2.0^62 + 1024' => [
                $gen([4611686018427387905, 4611686018427388928.0]),
                1023.0,
            ],
            'NAN skipped' => [
                $gen([9007199254740993, \NAN, 9007199254740992.0]),
                1.0,
            ],
            'integer string 2^53 + 1 above float 2.0^53' => [
                $gen(['9007199254740993', 9007199254740992.0]),
                1.0,
            ],
        ];
    }

    /**
     * @test         toRange iterators of an int beyond 2^53 and a float, which compare equal or subtract wrongly as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionIterators
     * @param        \Iterator    $data
     * @param        float        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionIterators(\Iterator $data, float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact comparison and rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * NAN is skipped, and integer strings are converted to int, as PHP does.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            'int 2^53 + 1 below float 2.0^53 + 2' => [
                $iter([9007199254740993, 9007199254740994.0]),
                1.0,
            ],
            'int 2^53 + 1 above float 2.0^53, equal as floats' => [
                $iter([9007199254740993, 9007199254740992.0]),
                1.0,
            ],
            'float 2.0^53 first, below int 2^53 + 1, equal as floats' => [
                $iter([9007199254740992.0, 9007199254740993]),
                1.0,
            ],
            'minimum is the float that equals the last int as floats' => [
                $iter([9007199254740994, 9007199254740992.0, 9007199254740993]),
                2.0,
            ],
            'minimum is the first float, maximum the last int' => [
                $iter([9007199254740992.0, 9007199254740993, 9007199254740994]),
                2.0,
            ],
            'negative int -(2^53 + 1) below float -2.0^53' => [
                $iter([-9007199254740993, -9007199254740992.0]),
                1.0,
            ],
            'int 2^62 + 1 below float 2.0^62 + 1024' => [
                $iter([4611686018427387905, 4611686018427388928.0]),
                1023.0,
            ],
            'NAN skipped' => [
                $iter([9007199254740993, \NAN, 9007199254740992.0]),
                1.0,
            ],
            'integer string 2^53 + 1 above float 2.0^53' => [
                $iter(['9007199254740993', 9007199254740992.0]),
                1.0,
            ],
        ];
    }

    /**
     * @test         toRange traversables of an int beyond 2^53 and a float, which compare equal or subtract wrongly as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionTraversables
     * @param        \Traversable $data
     * @param        float        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionTraversables(\Traversable $data, float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact comparison and rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     * NAN is skipped, and integer strings are converted to int, as PHP does.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            'int 2^53 + 1 below float 2.0^53 + 2' => [
                $trav([9007199254740993, 9007199254740994.0]),
                1.0,
            ],
            'int 2^53 + 1 above float 2.0^53, equal as floats' => [
                $trav([9007199254740993, 9007199254740992.0]),
                1.0,
            ],
            'float 2.0^53 first, below int 2^53 + 1, equal as floats' => [
                $trav([9007199254740992.0, 9007199254740993]),
                1.0,
            ],
            'minimum is the float that equals the last int as floats' => [
                $trav([9007199254740994, 9007199254740992.0, 9007199254740993]),
                2.0,
            ],
            'minimum is the first float, maximum the last int' => [
                $trav([9007199254740992.0, 9007199254740993, 9007199254740994]),
                2.0,
            ],
            'negative int -(2^53 + 1) below float -2.0^53' => [
                $trav([-9007199254740993, -9007199254740992.0]),
                1.0,
            ],
            'int 2^62 + 1 below float 2.0^62 + 1024' => [
                $trav([4611686018427387905, 4611686018427388928.0]),
                1023.0,
            ],
            'NAN skipped' => [
                $trav([9007199254740993, \NAN, 9007199254740992.0]),
                1.0,
            ],
            'integer string 2^53 + 1 above float 2.0^53' => [
                $trav(['9007199254740993', 9007199254740992.0]),
                1.0,
            ],
        ];
    }

    /**
     * @test         toRange array whose exact int difference is beyond the int range, correctly rounded to a float
     * @dataProvider dataProviderForIntDifferenceBeyondIntRangeArray
     * @param        array        $data
     * @param        float        $expected
     */
    public function testIntDifferenceBeyondIntRangeArray(array $data, float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact comparison and rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     */
    public static function dataProviderForIntDifferenceBeyondIntRangeArray(): array
    {
        return [
            'both odd, operands round to a tie' => [
                [4611686018427388929, -4611686018427387905],
                9.223372036854778e+18,
            ],
            'even maximum, odd minimum' => [
                [4611686018427388928, -4611686018427387905],
                9.223372036854778e+18,
            ],
            'odd maximum, even minimum' => [
                [4611686018427388929, -4611686018427387904],
                9.223372036854778e+18,
            ],
            'operands round up past the true difference' => [
                [4611686018427390975, -4611686018427387904],
                9.223372036854778e+18,
            ],
            'minimum first, both odd' => [
                [-4611686018427388929, 4611686018427387905],
                9.223372036854778e+18,
            ],
            'exact tie rounds to even' => [
                [4611686018427388928, -4611686018427387904],
                9.223372036854776e+18,
            ],
            'PHP_INT_MIN and PHP_INT_MAX' => [
                [\PHP_INT_MIN, \PHP_INT_MAX],
                1.8446744073709552e+19,
            ],
            'PHP_INT_MIN and 2^53 + 1, just past the int range' => [
                [\PHP_INT_MIN, 9007199254740993],
                9.232379236109517e+18,
            ],
            'int minimum, integral float maximum' => [
                [-4611686018427387905, 4611686018427388928.0],
                9.223372036854778e+18,
            ],
            'integral float minimum, int maximum' => [
                [-4611686018427387904.0, 4611686018427388929],
                9.223372036854778e+18,
            ],
            'three values, extremes in the middle' => [
                [0, 4611686018427388929, -4611686018427387905, 1],
                9.223372036854778e+18,
            ],
        ];
    }

    /**
     * @test         toRange generators whose exact int difference is beyond the int range, correctly rounded to a float
     * @dataProvider dataProviderForIntDifferenceBeyondIntRangeGenerators
     * @param        \Generator   $data
     * @param        float        $expected
     */
    public function testIntDifferenceBeyondIntRangeGenerators(\Generator $data, float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact comparison and rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     */
    public static function dataProviderForIntDifferenceBeyondIntRangeGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            'both odd, operands round to a tie' => [
                $gen([4611686018427388929, -4611686018427387905]),
                9.223372036854778e+18,
            ],
            'even maximum, odd minimum' => [
                $gen([4611686018427388928, -4611686018427387905]),
                9.223372036854778e+18,
            ],
            'odd maximum, even minimum' => [
                $gen([4611686018427388929, -4611686018427387904]),
                9.223372036854778e+18,
            ],
            'operands round up past the true difference' => [
                $gen([4611686018427390975, -4611686018427387904]),
                9.223372036854778e+18,
            ],
            'minimum first, both odd' => [
                $gen([-4611686018427388929, 4611686018427387905]),
                9.223372036854778e+18,
            ],
            'exact tie rounds to even' => [
                $gen([4611686018427388928, -4611686018427387904]),
                9.223372036854776e+18,
            ],
            'PHP_INT_MIN and PHP_INT_MAX' => [
                $gen([\PHP_INT_MIN, \PHP_INT_MAX]),
                1.8446744073709552e+19,
            ],
            'PHP_INT_MIN and 2^53 + 1, just past the int range' => [
                $gen([\PHP_INT_MIN, 9007199254740993]),
                9.232379236109517e+18,
            ],
            'int minimum, integral float maximum' => [
                $gen([-4611686018427387905, 4611686018427388928.0]),
                9.223372036854778e+18,
            ],
            'integral float minimum, int maximum' => [
                $gen([-4611686018427387904.0, 4611686018427388929]),
                9.223372036854778e+18,
            ],
            'three values, extremes in the middle' => [
                $gen([0, 4611686018427388929, -4611686018427387905, 1]),
                9.223372036854778e+18,
            ],
        ];
    }

    /**
     * @test         toRange iterators whose exact int difference is beyond the int range, correctly rounded to a float
     * @dataProvider dataProviderForIntDifferenceBeyondIntRangeIterators
     * @param        \Iterator    $data
     * @param        float        $expected
     */
    public function testIntDifferenceBeyondIntRangeIterators(\Iterator $data, float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact comparison and rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     */
    public static function dataProviderForIntDifferenceBeyondIntRangeIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            'both odd, operands round to a tie' => [
                $iter([4611686018427388929, -4611686018427387905]),
                9.223372036854778e+18,
            ],
            'even maximum, odd minimum' => [
                $iter([4611686018427388928, -4611686018427387905]),
                9.223372036854778e+18,
            ],
            'odd maximum, even minimum' => [
                $iter([4611686018427388929, -4611686018427387904]),
                9.223372036854778e+18,
            ],
            'operands round up past the true difference' => [
                $iter([4611686018427390975, -4611686018427387904]),
                9.223372036854778e+18,
            ],
            'minimum first, both odd' => [
                $iter([-4611686018427388929, 4611686018427387905]),
                9.223372036854778e+18,
            ],
            'exact tie rounds to even' => [
                $iter([4611686018427388928, -4611686018427387904]),
                9.223372036854776e+18,
            ],
            'PHP_INT_MIN and PHP_INT_MAX' => [
                $iter([\PHP_INT_MIN, \PHP_INT_MAX]),
                1.8446744073709552e+19,
            ],
            'PHP_INT_MIN and 2^53 + 1, just past the int range' => [
                $iter([\PHP_INT_MIN, 9007199254740993]),
                9.232379236109517e+18,
            ],
            'int minimum, integral float maximum' => [
                $iter([-4611686018427387905, 4611686018427388928.0]),
                9.223372036854778e+18,
            ],
            'integral float minimum, int maximum' => [
                $iter([-4611686018427387904.0, 4611686018427388929]),
                9.223372036854778e+18,
            ],
            'three values, extremes in the middle' => [
                $iter([0, 4611686018427388929, -4611686018427387905, 1]),
                9.223372036854778e+18,
            ],
        ];
    }

    /**
     * @test         toRange traversables whose exact int difference is beyond the int range, correctly rounded to a float
     * @dataProvider dataProviderForIntDifferenceBeyondIntRangeTraversables
     * @param        \Traversable $data
     * @param        float        $expected
     */
    public function testIntDifferenceBeyondIntRangeTraversables(\Traversable $data, float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toRange($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact comparison and rational difference, correctly rounded to a float):
     *   float(max(map(Fraction, data)) - min(map(Fraction, data)))
     */
    public static function dataProviderForIntDifferenceBeyondIntRangeTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            'both odd, operands round to a tie' => [
                $trav([4611686018427388929, -4611686018427387905]),
                9.223372036854778e+18,
            ],
            'even maximum, odd minimum' => [
                $trav([4611686018427388928, -4611686018427387905]),
                9.223372036854778e+18,
            ],
            'odd maximum, even minimum' => [
                $trav([4611686018427388929, -4611686018427387904]),
                9.223372036854778e+18,
            ],
            'operands round up past the true difference' => [
                $trav([4611686018427390975, -4611686018427387904]),
                9.223372036854778e+18,
            ],
            'minimum first, both odd' => [
                $trav([-4611686018427388929, 4611686018427387905]),
                9.223372036854778e+18,
            ],
            'exact tie rounds to even' => [
                $trav([4611686018427388928, -4611686018427387904]),
                9.223372036854776e+18,
            ],
            'PHP_INT_MIN and PHP_INT_MAX' => [
                $trav([\PHP_INT_MIN, \PHP_INT_MAX]),
                1.8446744073709552e+19,
            ],
            'PHP_INT_MIN and 2^53 + 1, just past the int range' => [
                $trav([\PHP_INT_MIN, 9007199254740993]),
                9.232379236109517e+18,
            ],
            'int minimum, integral float maximum' => [
                $trav([-4611686018427387905, 4611686018427388928.0]),
                9.223372036854778e+18,
            ],
            'integral float minimum, int maximum' => [
                $trav([-4611686018427387904.0, 4611686018427388929]),
                9.223372036854778e+18,
            ],
            'three values, extremes in the middle' => [
                $trav([0, 4611686018427388929, -4611686018427387905, 1]),
                9.223372036854778e+18,
            ],
        ];
    }

    /**
     * @test         toRange of an array never returns negative zero
     * @dataProvider dataProviderForNegativeZeroArray
     * @param        array $data
     * @param        int|float $expected
     */
    public function testNegativeZeroArray(array $data, int|float $expected): void
    {
        // When
        $result = Reduce::toRange($data);

        // Then
        // -0.0 === 0.0, so compare the exported forms, which differ in the sign.
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with NumPy (Fraction has no signed zero; ptp is max - min in float64 and
     * keeps the sign of a zero). Generator script: tools/oracles/range_signed_zero.py
     *   numpy.ptp(numpy.array(data, dtype=float))
     * The int-vs-float result type is the library's contract, not the oracle's: a range of int
     * values stays an int, which numpy.ptp(numpy.array(data, dtype=numpy.int64)) confirms is 0.
     * Each bound is the first of the values tied for it, so [0, -0.0] has int bounds and an int range.
     */
    public static function dataProviderForNegativeZeroArray(): array
    {
        return [
            'negative zero then zero' => [
                [-0.0, 0.0],
                0.0,
            ],
            'zero then negative zero' => [
                [0.0, -0.0],
                0.0,
            ],
            'two negative zeros' => [
                [-0.0, -0.0],
                0.0,
            ],
            'two zeros' => [
                [0.0, 0.0],
                0.0,
            ],
            'single negative zero' => [
                [-0.0],
                0.0,
            ],
            'negative zero, zero, negative zero' => [
                [-0.0, 0.0, -0.0],
                0.0,
            ],
            'negative zero then int zero' => [
                [-0.0, 0],
                0.0,
            ],
            'int zero then negative zero' => [
                [0, -0.0],
                0,
            ],
            'signed zeros around a nonzero range' => [
                [-0.0, 0.0, 1.5],
                1.5,
            ],
            'int zero range stays int' => [
                [5, 5],
                0,
            ],
        ];
    }

    /**
     * @test         toRange of a generator never returns negative zero
     * @dataProvider dataProviderForNegativeZeroGenerators
     * @param        \Generator $data
     * @param        int|float $expected
     */
    public function testNegativeZeroGenerators(\Generator $data, int|float $expected): void
    {
        // When
        $result = Reduce::toRange($data);

        // Then
        // -0.0 === 0.0, so compare the exported forms, which differ in the sign.
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with NumPy (Fraction has no signed zero; ptp is max - min in float64 and
     * keeps the sign of a zero). Generator script: tools/oracles/range_signed_zero.py
     *   numpy.ptp(numpy.array(data, dtype=float))
     * The int-vs-float result type is the library's contract, not the oracle's: a range of int
     * values stays an int, which numpy.ptp(numpy.array(data, dtype=numpy.int64)) confirms is 0.
     * Each bound is the first of the values tied for it, so [0, -0.0] has int bounds and an int range.
     */
    public static function dataProviderForNegativeZeroGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            'negative zero then zero' => [
                $gen([-0.0, 0.0]),
                0.0,
            ],
            'zero then negative zero' => [
                $gen([0.0, -0.0]),
                0.0,
            ],
            'two negative zeros' => [
                $gen([-0.0, -0.0]),
                0.0,
            ],
            'two zeros' => [
                $gen([0.0, 0.0]),
                0.0,
            ],
            'single negative zero' => [
                $gen([-0.0]),
                0.0,
            ],
            'negative zero, zero, negative zero' => [
                $gen([-0.0, 0.0, -0.0]),
                0.0,
            ],
            'negative zero then int zero' => [
                $gen([-0.0, 0]),
                0.0,
            ],
            'int zero then negative zero' => [
                $gen([0, -0.0]),
                0,
            ],
            'signed zeros around a nonzero range' => [
                $gen([-0.0, 0.0, 1.5]),
                1.5,
            ],
            'int zero range stays int' => [
                $gen([5, 5]),
                0,
            ],
        ];
    }

    /**
     * @test         toRange of an iterator never returns negative zero
     * @dataProvider dataProviderForNegativeZeroIterators
     * @param        \Iterator $data
     * @param        int|float $expected
     */
    public function testNegativeZeroIterators(\Iterator $data, int|float $expected): void
    {
        // When
        $result = Reduce::toRange($data);

        // Then
        // -0.0 === 0.0, so compare the exported forms, which differ in the sign.
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with NumPy (Fraction has no signed zero; ptp is max - min in float64 and
     * keeps the sign of a zero). Generator script: tools/oracles/range_signed_zero.py
     *   numpy.ptp(numpy.array(data, dtype=float))
     * The int-vs-float result type is the library's contract, not the oracle's: a range of int
     * values stays an int, which numpy.ptp(numpy.array(data, dtype=numpy.int64)) confirms is 0.
     * Each bound is the first of the values tied for it, so [0, -0.0] has int bounds and an int range.
     */
    public static function dataProviderForNegativeZeroIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            'negative zero then zero' => [
                $iter([-0.0, 0.0]),
                0.0,
            ],
            'zero then negative zero' => [
                $iter([0.0, -0.0]),
                0.0,
            ],
            'two negative zeros' => [
                $iter([-0.0, -0.0]),
                0.0,
            ],
            'two zeros' => [
                $iter([0.0, 0.0]),
                0.0,
            ],
            'single negative zero' => [
                $iter([-0.0]),
                0.0,
            ],
            'negative zero, zero, negative zero' => [
                $iter([-0.0, 0.0, -0.0]),
                0.0,
            ],
            'negative zero then int zero' => [
                $iter([-0.0, 0]),
                0.0,
            ],
            'int zero then negative zero' => [
                $iter([0, -0.0]),
                0,
            ],
            'signed zeros around a nonzero range' => [
                $iter([-0.0, 0.0, 1.5]),
                1.5,
            ],
            'int zero range stays int' => [
                $iter([5, 5]),
                0,
            ],
        ];
    }

    /**
     * @test         toRange of an iterator aggregate never returns negative zero
     * @dataProvider dataProviderForNegativeZeroTraversables
     * @param        \Traversable $data
     * @param        int|float $expected
     */
    public function testNegativeZeroTraversables(\Traversable $data, int|float $expected): void
    {
        // When
        $result = Reduce::toRange($data);

        // Then
        // -0.0 === 0.0, so compare the exported forms, which differ in the sign.
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with NumPy (Fraction has no signed zero; ptp is max - min in float64 and
     * keeps the sign of a zero). Generator script: tools/oracles/range_signed_zero.py
     *   numpy.ptp(numpy.array(data, dtype=float))
     * The int-vs-float result type is the library's contract, not the oracle's: a range of int
     * values stays an int, which numpy.ptp(numpy.array(data, dtype=numpy.int64)) confirms is 0.
     * Each bound is the first of the values tied for it, so [0, -0.0] has int bounds and an int range.
     */
    public static function dataProviderForNegativeZeroTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            'negative zero then zero' => [
                $trav([-0.0, 0.0]),
                0.0,
            ],
            'zero then negative zero' => [
                $trav([0.0, -0.0]),
                0.0,
            ],
            'two negative zeros' => [
                $trav([-0.0, -0.0]),
                0.0,
            ],
            'two zeros' => [
                $trav([0.0, 0.0]),
                0.0,
            ],
            'single negative zero' => [
                $trav([-0.0]),
                0.0,
            ],
            'negative zero, zero, negative zero' => [
                $trav([-0.0, 0.0, -0.0]),
                0.0,
            ],
            'negative zero then int zero' => [
                $trav([-0.0, 0]),
                0.0,
            ],
            'int zero then negative zero' => [
                $trav([0, -0.0]),
                0,
            ],
            'signed zeros around a nonzero range' => [
                $trav([-0.0, 0.0, 1.5]),
                1.5,
            ],
            'int zero range stays int' => [
                $trav([5, 5]),
                0,
            ],
        ];
    }

    /**
     * @test         Stream::toRange of an array never returns negative zero
     * @dataProvider dataProviderForNegativeZeroArray
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testNegativeZeroStream(array $data, int|float $expected): void
    {
        // When
        $result = Stream::of($data)->toRange();

        // Then
        // -0.0 === 0.0, so compare the exported forms, which differ in the sign.
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         toRange of tied values returns the first of them, whatever the PHP version
     * @dataProvider dataProviderForTieKeepsFirst
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testTieKeepsFirstArray(array $data, int|float $expected): void
    {
        // When
        $result = Reduce::toRange($data);

        // Then (-0.0 === 0.0, so compare the exported forms, which differ in the sign)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         toRange of tied values in a generator returns the first of them, whatever the PHP version
     * @dataProvider dataProviderForTieKeepsFirst
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testTieKeepsFirstGenerators(array $data, int|float $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toRange($iterable);

        // Then (-0.0 === 0.0, so compare the exported forms, which differ in the sign)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         toRange of tied values in an iterator returns the first of them, whatever the PHP version
     * @dataProvider dataProviderForTieKeepsFirst
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testTieKeepsFirstIterators(array $data, int|float $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toRange($iterable);

        // Then (-0.0 === 0.0, so compare the exported forms, which differ in the sign)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         toRange of tied values in an IteratorAggregate returns the first of them, whatever the PHP version
     * @dataProvider dataProviderForTieKeepsFirst
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testTieKeepsFirstTraversables(array $data, int|float $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toRange($iterable);

        // Then (-0.0 === 0.0, so compare the exported forms, which differ in the sign)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python, which compares int and float by exact value and returns the first of
     * equal values:
     *   max(data) - min(data)
     */
    public static function dataProviderForTieKeepsFirst(): array
    {
        return [
            'int then equal float' => [
                [1, 1.0],
                0,
            ],
            'float then equal int' => [
                [1.0, 1],
                0.0,
            ],
            'int zero then negative zero' => [
                [0, -0.0],
                0,
            ],
            'negative zero then int zero' => [
                [-0.0, 0],
                0.0,
            ],
            'negative zero then zero' => [
                [-0.0, 0.0],
                0.0,
            ],
            'zero then negative zero' => [
                [0.0, -0.0],
                0.0,
            ],
            'tied minimum after a larger value' => [
                [2, 1, 1.0],
                1,
            ],
            'tied maximum after a smaller value' => [
                [0, 1.0, 1],
                1.0,
            ],
        ];
    }
}

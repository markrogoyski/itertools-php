<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Precision;

use IterTools\Util\Precision\Arithmetic;

class ArithmeticTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test toExactInt returns the integer a number equals, or null when no integer equals it
     * @dataProvider dataProviderForToExactInt
     * @param int|float $number
     * @param int|null  $expected
     */
    public function testToExactInt(int|float $number, ?int $expected): void
    {
        // When
        $result = Arithmetic::toExactInt($number);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   int(x) if isinstance(x, int) or (x.is_integer() and -2**63 <= int(x) < 2**63) else None
     *
     * @return array<array{int|float, int|null}>
     */
    public static function dataProviderForToExactInt(): array
    {
        return [
            [0, 0],
            [-7, -7],
            [\PHP_INT_MAX, \PHP_INT_MAX],
            [\PHP_INT_MIN, \PHP_INT_MIN],
            [0.0, 0],
            [-0.0, 0],
            [1.0, 1],
            [-1.0, -1],
            [2.0 ** 63, null],
            [-(2.0 ** 63) - 2048.0, null],
            [2.0 ** 64, null],
            [1.0e300, null],
            [0.5, null],
            [-0.5, null],
            [2.0 ** 51 + 0.5, null],
            [5.0e-324, null],
            [\INF, null],
            [-\INF, null],
            [\NAN, null],
        ];
    }

    /**
     * @test toExactInt returns the 64-bit integer a float equals
     * @dataProvider dataProviderForToExactIntSixtyFourBit
     * @param float     $number
     * @param int|float $expected
     */
    public function testToExactIntSixtyFourBit(float $number, int|float $expected): void
    {
        // Given (on 32-bit PHP these literals parse as floats and the floats are out of int range)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Arithmetic::toExactInt($number);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   int(x) if x.is_integer() and -2**63 <= int(x) < 2**63 else None
     *
     * @return array<array{float, int|float}>
     */
    public static function dataProviderForToExactIntSixtyFourBit(): array
    {
        return [
            [1.0e18, 1000000000000000000],
            [2.0 ** 53, 9007199254740992],
            [2.0 ** 53 + 2.0, 9007199254740994],
            [-(2.0 ** 62), -4611686018427387904],
            [2.0 ** 63 - 1024.0, 9223372036854774784],
            [-(2.0 ** 63), \PHP_INT_MIN],
        ];
    }

    /**
     * @test compare orders two numbers by their exact values
     * @dataProvider dataProviderForCompare
     * @param int|float $a
     * @param int|float $b
     * @param int       $expected
     */
    public function testCompare(int|float $a, int|float $b, int $expected): void
    {
        // When
        $result = Arithmetic::compare($a, $b);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with (Python compares an int and a float by their exact values):
     *   (a > b) - (a < b)
     *
     * @return array<array{int|float, int|float, int}>
     */
    public static function dataProviderForCompare(): array
    {
        return [
            [1, 2, -1],
            [2, 1, 1],
            [1, 1, 0],
            [1.5, 1.5, 0],
            [1, 1.0, 0],
            [1.0, 1, 0],
            [1, 1.5, -1],
            [1.5, 1, 1],
            [-1, -0.5, -1],
            [0, -0.0, 0],
            [-0.0, 0, 0],
            [-0.0, 0.0, 0],
            [3, \INF, -1],
            [\INF, 3, 1],
            [3, -\INF, 1],
            [-\INF, 3, -1],
            [\INF, \INF, 0],
        ];
    }

    /**
     * @test compare orders a 64-bit integer and a float that compare equal once the integer is converted to float
     * @dataProvider dataProviderForCompareSixtyFourBit
     * @param int|float $a
     * @param int|float $b
     * @param int       $expected
     */
    public function testCompareSixtyFourBit(int|float $a, int|float $b, int $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Arithmetic::compare($a, $b);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with (Python compares an int and a float by their exact values):
     *   (a > b) - (a < b)
     *
     * @return array<array{int|float, int|float, int}>
     */
    public static function dataProviderForCompareSixtyFourBit(): array
    {
        return [
            [9007199254740993, 9007199254740992.0, 1],
            [9007199254740992.0, 9007199254740993, -1],
            [9007199254740993, 9007199254740994.0, -1],
            [9007199254740994.0, 9007199254740993, 1],
            [-9007199254740993, -9007199254740992.0, -1],
            [\PHP_INT_MAX, 2.0 ** 63, -1],
            [2.0 ** 63, \PHP_INT_MAX, 1],
            [\PHP_INT_MAX, 2.0 ** 63 - 1024.0, 1],
            [\PHP_INT_MIN, -(2.0 ** 63), 0],
            [\PHP_INT_MIN + 1, -(2.0 ** 63), 1],
            [-(2.0 ** 63), \PHP_INT_MIN + 1, -1],
            [\PHP_INT_MAX, 1.0e300, -1],
            [\PHP_INT_MIN, -1.0e300, 1],
        ];
    }

    /**
     * @test compareValues, min and max order a 64-bit integer and a float by their exact values
     * @dataProvider dataProviderForMinMaxSixtyFourBit
     * @param int|float $a
     * @param int|float $b
     * @param int       $expectedOrder
     * @param int|float $expectedMin
     * @param int|float $expectedMax
     */
    public function testMinMaxSixtyFourBit(
        int|float $a,
        int|float $b,
        int $expectedOrder,
        int|float $expectedMin,
        int|float $expectedMax
    ): void {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $order = Arithmetic::compareValues($a, $b);
        $min = Arithmetic::min($a, $b);
        $max = Arithmetic::max($a, $b);

        // Then
        $this->assertSame($expectedOrder, $order);
        $this->assertSame($expectedMin, $min);
        $this->assertSame($expectedMax, $max);
    }

    /**
     * Test data generated with (Python compares an int and a float by their exact values):
     *   (a > b) - (a < b), min(a, b), max(a, b)
     * No row is an exact int/float tie, so the tie choice doesn't decide any result.
     *
     * @return array<array{int|float, int|float, int, int|float, int|float}>
     */
    public static function dataProviderForMinMaxSixtyFourBit(): array
    {
        return [
            [9007199254740993, 9007199254740992.0, 1, 9007199254740992.0, 9007199254740993],
            [9007199254740992.0, 9007199254740993, -1, 9007199254740992.0, 9007199254740993],
            [9007199254740993, 9007199254740994.0, -1, 9007199254740993, 9007199254740994.0],
            [9007199254740994.0, 9007199254740993, 1, 9007199254740993, 9007199254740994.0],
            [-9007199254740993, -9007199254740992.0, -1, -9007199254740993, -9007199254740992.0],
            [\PHP_INT_MAX, 2.0 ** 63, -1, \PHP_INT_MAX, 2.0 ** 63],
            [2.0 ** 63, \PHP_INT_MAX, 1, \PHP_INT_MAX, 2.0 ** 63],
            [\PHP_INT_MIN + 1, -(2.0 ** 63), 1, -(2.0 ** 63), \PHP_INT_MIN + 1],
        ];
    }

    /**
     * @test min and max return the first of two exactly equal values, whatever the PHP version
     * @dataProvider dataProviderForMinMaxTie
     * @param int|float $a
     * @param int|float $b
     * @param int|float $expectedMin
     * @param int|float $expectedMax
     */
    public function testMinMaxTie(int|float $a, int|float $b, int|float $expectedMin, int|float $expectedMax): void
    {
        // When
        $min = Arithmetic::min($a, $b);
        $max = Arithmetic::max($a, $b);

        // Then (-0.0 === 0.0, so compare the exported forms, which differ in the sign)
        $this->assertSame(\var_export($expectedMin, true), \var_export($min, true));
        $this->assertSame(\var_export($expectedMax, true), \var_export($max, true));
    }

    /**
     * Test data generated with Python, which compares int and float by exact value and returns the first of
     * equal values:
     *   min(a, b), max(a, b)
     *
     * @return array<array{int|float, int|float, int|float, int|float}>
     */
    public static function dataProviderForMinMaxTie(): array
    {
        return [
            [1, 1.0, 1, 1],
            [1.0, 1, 1.0, 1.0],
            [0, -0.0, 0, 0],
            [-0.0, 0, -0.0, -0.0],
            [-0.0, 0.0, -0.0, -0.0],
            [0.0, -0.0, 0.0, 0.0],
            [2 ** 53, 2.0 ** 53, 2 ** 53, 2 ** 53],
            [2.0 ** 53, 2 ** 53, 2.0 ** 53, 2.0 ** 53],
        ];
    }

    /**
     * @test min and max return the first of a 64-bit int and a float that are exactly equal
     * @dataProvider dataProviderForMinMaxTieSixtyFourBit
     * @param int|float $a
     * @param int|float $b
     * @param int|float $expectedMin
     * @param int|float $expectedMax
     */
    public function testMinMaxTieSixtyFourBit(
        int|float $a,
        int|float $b,
        int|float $expectedMin,
        int|float $expectedMax
    ): void {
        // Given (on 32-bit PHP, PHP_INT_MIN is -2^31, which no longer equals -2.0^63)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $min = Arithmetic::min($a, $b);
        $max = Arithmetic::max($a, $b);

        // Then
        $this->assertSame($expectedMin, $min);
        $this->assertSame($expectedMax, $max);
    }

    /**
     * Test data generated with Python, which compares int and float by exact value and returns the first of
     * equal values:
     *   min(a, b), max(a, b)
     *
     * @return array<array{int|float, int|float, int|float, int|float}>
     */
    public static function dataProviderForMinMaxTieSixtyFourBit(): array
    {
        return [
            [\PHP_INT_MIN, -(2.0 ** 63), \PHP_INT_MIN, \PHP_INT_MIN],
            [-(2.0 ** 63), \PHP_INT_MIN, -(2.0 ** 63), -(2.0 ** 63)],
        ];
    }

    /**
     * @test compareValues, min and max order a pair that isn't an int and a float by PHP's comparison, as <=>,
     *       \min() and \max() do, and return the first of a pair that compares equal
     * @dataProvider dataProviderForMinMaxNotNumbers
     * @param mixed $a
     * @param mixed $b
     * @param mixed $expectedMin
     * @param mixed $expectedMax
     */
    public function testMinMaxNotNumbers(mixed $a, mixed $b, mixed $expectedMin, mixed $expectedMax): void
    {
        // When
        $order = Arithmetic::compareValues($a, $b);
        $min = Arithmetic::min($a, $b);
        $max = Arithmetic::max($a, $b);

        // Then
        $this->assertSame($a <=> $b, $order);
        $this->assertSame($expectedMin, $min);
        $this->assertSame($expectedMax, $max);
    }

    /**
     * Expected values follow PHP's documented rule for min() and max(): the lesser or greater value by PHP's
     * comparison, and the first of two values that compare equal (the last four pairs). PHP 8.2's min() and
     * max(), and a dynamic call to them on later versions, give the same values.
     *
     * @return array<array{mixed, mixed, mixed, mixed}>
     */
    public static function dataProviderForMinMaxNotNumbers(): array
    {
        return [
            ['a', 'b', 'a', 'b'],
            ['b', 'a', 'a', 'b'],
            ['10', 9, 9, '10'],
            [9, '10', 9, '10'],
            ['abc', 0, 0, 'abc'],
            [[1, 2], [1, 3], [1, 2], [1, 3]],
            ['9007199254740993', 9007199254740992.0, '9007199254740993', '9007199254740993'],
            [true, 1, true, true],
            [1, true, 1, 1],
            [null, 0, null, null],
        ];
    }

    /**
     * @test mixesFloatAndLargeInt finds a float and an int beyond ±2^53 together, which <=> can misorder
     * @dataProvider dataProviderForMixesFloatAndLargeInt
     * @param list<int|float> $values
     * @param bool            $expected
     */
    public function testMixesFloatAndLargeInt(array $values, bool $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Arithmetic::mixesFloatAndLargeInt($values);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with (tools/oracles/mixes_float_and_large_int.py):
     *   any(isinstance(v, float) for v in x) and any(isinstance(v, int) and abs(v) > 2**53 for v in x)
     * The rows reach each early return: an int sum, bounds within ±2^53, and the full scan.
     *
     * @return array<string, array{list<int|float>, bool}>
     */
    public static function dataProviderForMixesFloatAndLargeInt(): array
    {
        return [
            'empty' => [[], false],
            'small ints' => [[1, -2, 3], false],
            'large ints, no float' => [[4611686018427387904, -4611686018427387904, 9007199254740993], false],
            'ints whose sum overflows, no float' => [[4611686018427387904, 4611686018427387904, 4611686018427387904], false],
            'floats only' => [[0.5, 1e300, -1e300], false],
            'small ints and floats' => [[1, 0.5, 9007199254740992, -9007199254740992], false],
            'int 2^53 + 1 and a float' => [[9007199254740993, 0.5], true],
            'int -(2^53 + 1) and a float' => [[-9007199254740993, 0.5], true],
            'int PHP_INT_MAX and a float' => [[\PHP_INT_MAX, 0.5], true],
            'large int and INF' => [[4611686018427387904, \INF], true],
            'float 2.0^53 and small ints' => [[9007199254740992.0, 1, 2], false],
            'float beyond 2^53 and small ints' => [[1e300, 1, 2], false],
        ];
    }

    /**
     * @test midpoint is the correctly rounded midpoint, an int only for two ints with an even sum
     * @dataProvider dataProviderForMidpoint
     * @param int|float $lo
     * @param int|float $hi
     * @param int|float $expected
     */
    public function testMidpointIsCorrectlyRounded(int|float $lo, int|float $hi, int|float $expected): void
    {
        // When
        $result = Arithmetic::midpoint($lo, $hi);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame(\fdiv(1.0, $expected), \fdiv(1.0, $result));
    }

    /**
     * Test data generated with fractions.Fraction (tools/oracles/arithmetic_midpoint_oracles.py):
     *   m = (Fraction(lo) + Fraction(hi)) / 2
     *   m.numerator if lo and hi are both int and m.denominator == 1 else float(m)
     * keeping only rows with ints in [-2^63, 2^63) or finite floats, a nonzero m or two ints, and not an int
     * beyond ±2^53 paired with a float that is non-integral or outside [-2^63, 2^63), whose midpoint the
     * docblock says may round twice. float(m) rounds once, ties to even, keeping the sign of an underflow.
     * Ints, integral floats and the sum of two exact-int operands stay within ±(2^31 - 1), so the
     * rows hold on 32-bit PHP too.
     *
     * @return array<string, array{int|float, int|float, int|float}>
     */
    public static function dataProviderForMidpoint(): array
    {
        return [
            'two ints, even sum' => [2, 6, 4],
            'two ints, odd sum' => [1, 2, 1.5],
            'negative ints, odd sum' => [-7, -2, -4.5],
            'mixed signs, even sum' => [-3, 7, 2],
            'mixed signs, odd sum' => [-4, 7, 1.5],
            'reversed order' => [9, 2, 5.5],
            'equal ints' => [5, 5, 5],
            'opposite ints give int 0' => [-3, 3, 0],
            'zero ints' => [0, 0, 0],
            'int and an equal integral float' => [1, 1.0, 1.0],
            'int and integral float, odd sum' => [2, 5.0, 3.5],
            'integral floats, even sum' => [2.0, 6.0, 4.0],
            'int and non-integral float' => [1, 0.5, 0.75],
            'non-integral floats' => [0.1, 0.2, 0.15000000000000002],
            'equal floats' => [0.1, 0.1, 0.1],
            'negative floats' => [-1.5, -2.25, -1.875],
            'one third and two thirds' => [0.3333333333333333, 0.6666666666666666, 0.5],
            '1 + 2^-53 sum ties to even' => [1.0, 1.1102230246251565e-16, 0.5],
            '1 + 3 * 2^-53 sum rounds up' => [1.0, 3.3306690738754696e-16, 0.5000000000000002],
            'subnormals 2^-1074 and 2^-1073' => [5e-324, 1e-323, 1e-323],
            '0 and 2^-1074 underflows to +0' => [0.0, 5e-324, 0.0],
            '-2^-1074 and 0 underflows to -0' => [-5e-324, 0.0, -0.0],
            'PHP_FLOAT_MIN and the largest subnormal' => [\PHP_FLOAT_MIN, 2.225073858507201e-308, \PHP_FLOAT_MIN],
            'PHP_FLOAT_MIN and PHP_FLOAT_MAX' => [\PHP_FLOAT_MIN, \PHP_FLOAT_MAX, 8.988465674311579e+307],
            '2^-1074 and PHP_FLOAT_MAX' => [5e-324, \PHP_FLOAT_MAX, 8.988465674311579e+307],
            '-PHP_FLOAT_MAX and 2^-1074' => [-\PHP_FLOAT_MAX, 5e-324, -8.988465674311579e+307],
            'PHP_FLOAT_MAX with itself' => [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \PHP_FLOAT_MAX],
            '-PHP_FLOAT_MAX with itself' => [-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX],
            'PHP_FLOAT_MAX and its predecessor tie to even' => [1.7976931348623155e+308, \PHP_FLOAT_MAX, 1.7976931348623155e+308],
            '1.0 and PHP_FLOAT_MAX' => [1.0, \PHP_FLOAT_MAX, 8.988465674311579e+307],
            'int 3 and PHP_FLOAT_MAX' => [3, \PHP_FLOAT_MAX, 8.988465674311579e+307],
            '1e308 and PHP_FLOAT_MAX' => [1e+308, \PHP_FLOAT_MAX, 1.398846567431158e+308],
            '-1e308 and PHP_FLOAT_MAX' => [-1e+308, \PHP_FLOAT_MAX, 3.9884656743115785e+307],
            '-PHP_FLOAT_MAX and -1e308' => [-\PHP_FLOAT_MAX, -1e+308, -1.398846567431158e+308],
            '2^1023 and PHP_FLOAT_MAX' => [2.0 ** 1023, \PHP_FLOAT_MAX, 1.348269851146737e+308],
            'seeded floats 0' => [4.0359023823205895e-304, -7.393531142365605e+24, -3.6967655711828024e+24],
            'seeded floats 3' => [-1.2522229165529293e-135, 8.560753164582992e+30, 4.280376582291496e+30],
            'seeded floats 4' => [-2.6747123933672023e-135, -1.3191076424054947e+308, -6.595538212027473e+307],
            'seeded floats 5' => [1.1297491323885996e-292, -799095465739209.5, -399547732869604.75],
        ];
    }

    /**
     * @test midpoint is the correctly rounded midpoint of 64-bit integers, alone or with a float
     * @dataProvider dataProviderForMidpointSixtyFourBit
     * @param int|float $lo
     * @param int|float $hi
     * @param int|float $expected
     */
    public function testMidpointIsCorrectlyRoundedSixtyFourBit(int|float $lo, int|float $hi, int|float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Arithmetic::midpoint($lo, $hi);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame(\fdiv(1.0, $expected), \fdiv(1.0, $result));
    }

    /**
     * Test data generated with fractions.Fraction (tools/oracles/arithmetic_midpoint_oracles.py):
     *   m = (Fraction(lo) + Fraction(hi)) / 2
     *   m.numerator if lo and hi are both int and m.denominator == 1 else float(m)
     * keeping only rows with ints in [-2^63, 2^63) or finite floats, a nonzero m or two ints, and not an int
     * beyond ±2^53 paired with a float that is non-integral or outside [-2^63, 2^63), whose midpoint the
     * docblock says may round twice. float(m) rounds once, ties to even, keeping the sign of an underflow.
     * Rows that need 64-bit integers.
     *
     * @return array<string, array{int|float, int|float, int|float}>
     */
    public static function dataProviderForMidpointSixtyFourBit(): array
    {
        return [
            'PHP_INT_MAX with itself' => [\PHP_INT_MAX, \PHP_INT_MAX, \PHP_INT_MAX],
            'PHP_INT_MIN with itself' => [\PHP_INT_MIN, \PHP_INT_MIN, \PHP_INT_MIN],
            'PHP_INT_MIN and PHP_INT_MAX' => [\PHP_INT_MIN, \PHP_INT_MAX, -0.5],
            'PHP_INT_MAX and PHP_INT_MIN, reversed' => [\PHP_INT_MAX, \PHP_INT_MIN, -0.5],
            'PHP_INT_MAX - 1 and PHP_INT_MAX overflow, odd' => [\PHP_INT_MAX - 1, \PHP_INT_MAX, 2.0 ** 63],
            'PHP_INT_MAX - 2 and PHP_INT_MAX overflow, even' => [\PHP_INT_MAX - 2, \PHP_INT_MAX, \PHP_INT_MAX - 1],
            'PHP_INT_MIN and PHP_INT_MIN + 1 overflow, odd' => [\PHP_INT_MIN, \PHP_INT_MIN + 1, -(2.0 ** 63)],
            'PHP_INT_MIN and PHP_INT_MIN + 2 overflow, even' => [\PHP_INT_MIN, \PHP_INT_MIN + 2, \PHP_INT_MIN + 1],
            '2^53 + 1 and 2^53 + 2' => [9007199254740993, 9007199254740994, 9007199254740994.0],
            '-(2^53 + 1) and -(2^53 + 2)' => [-9007199254740993, -9007199254740994, -9007199254740994.0],
            'int 2^53 + 1 and integral float 2^53 + 2' => [9007199254740993, 9007199254740994.0, 9007199254740994.0],
            'int beyond 2^53 and integral float 2^62' => [9007199254740993, 2.0 ** 62, 2.3103466088410644e+18],
            'PHP_INT_MAX and integral float 2^63 - 1024' => [\PHP_INT_MAX, 9.223372036854775e+18, 9.223372036854775e+18],
            'PHP_INT_MIN and float -2^63' => [\PHP_INT_MIN, -(2.0 ** 63), -(2.0 ** 63)],
            'PHP_INT_MIN + 1 and float -2^63' => [\PHP_INT_MIN + 1, -(2.0 ** 63), -(2.0 ** 63)],
            'int 2^53 and 0.5' => [9007199254740992, 0.5, 4503599627370496.0],
            'int -2^53 and -0.5' => [-9007199254740992, -0.5, -4503599627370496.0],
            'int 2^53 and 1e300' => [9007199254740992, 1e+300, 5e+299],
            'seeded floats 1' => [3.4856336547065346e-308, -6666128422376452.0, -3333064211188226.0],
            'seeded floats 2' => [5528322270187989.0, 1.6107096637724073e+31, 8.053548318862039e+30],
            'seeded ints 0' => [2394582595055999598, -5837970713172036238, -1721694059058018320],
            'seeded ints 1' => [487877555895914345, 2573607608345598640, 1.5307425821207565e+18],
            'seeded ints 2' => [-4028039775367791454, 5826747858334422499, 8.993540414833156e+17],
            'seeded int within 2^53 and float 0' => [-2524662411319700, 1354216334576032.0, -585223038371834.0],
            'seeded int within 2^53 and float 1' => [3843370488640164, 4948.458557351797, 1921685244322556.2],
            'seeded int within 2^53 and float 2' => [4042877466629682, 4313.526504301031, 2021438733316997.8],
        ];
    }

    /**
     * @test midpoint of an exactly zero sum with a float operand is -0.0 only for two -0.0, and 0.0 otherwise
     * @dataProvider dataProviderForMidpointSignedZeros
     * @param int|float $lo
     * @param int|float $hi
     * @param float     $expected
     */
    public function testMidpointOfAnExactlyZeroSumIsASignedZero(int|float $lo, int|float $hi, float $expected): void
    {
        // When
        $result = Arithmetic::midpoint($lo, $hi);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame(\fdiv(1.0, $expected), \fdiv(1.0, $result));
    }

    /**
     * Test data generated with fractions.Fraction (tools/oracles/arithmetic_midpoint_oracles.py):
     *   (Fraction(lo) + Fraction(hi)) / 2 == 0, with lo or hi a float
     * The zero's sign, which Fraction has no value for, follows IEEE 754-2019 section 6.3: an exactly zero
     * sum is +0 under roundTiesToEven, except that (-0) + (-0) is -0, and halving keeps the sign. So the
     * expected value is -0.0 if both operands are -0.0, and 0.0 otherwise (an int 0 counts as +0).
     *
     * @return array<string, array{int|float, int|float, float}>
     */
    public static function dataProviderForMidpointSignedZeros(): array
    {
        return [
            '+0 and +0' => [0.0, 0.0, 0.0],
            '-0 and -0' => [-0.0, -0.0, -0.0],
            '-0 and +0' => [-0.0, 0.0, 0.0],
            '+0 and -0' => [0.0, -0.0, 0.0],
            'int 0 and -0' => [0, -0.0, 0.0],
            '-0 and int 0' => [-0.0, 0, 0.0],
            '-1.5 and 1.5' => [-1.5, 1.5, 0.0],
            'int -1 and 1.0' => [-1, 1.0, 0.0],
            '-2^-1074 and 2^-1074' => [-5e-324, 5e-324, 0.0],
            '-PHP_FLOAT_MAX and PHP_FLOAT_MAX' => [-\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 0.0],
        ];
    }

    /**
     * @test integerMidpoint is the exact midpoint for an even sum, and the correctly rounded float for an odd one
     * @dataProvider dataProviderForIntegerMidpoint
     * @param int       $lo
     * @param int       $hi
     * @param int|float $expected
     */
    public function testIntegerMidpointIsExactOrCorrectlyRounded(int $lo, int $hi, int|float $expected): void
    {
        // When
        $result = Arithmetic::integerMidpoint($lo, $hi);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python int arithmetic and fractions.Fraction
     * (tools/oracles/arithmetic_midpoint_oracles.py):
     *   (lo + hi) // 2 if (lo + hi) % 2 == 0 else float(Fraction(lo + hi, 2))
     * keeping only rows with lo and hi ints in [-2^63, 2^63). float() rounds once, ties to even.
     * The ints and their sum stay within ±(2^31 - 1), so the rows hold on 32-bit PHP too.
     *
     * @return array<string, array{int, int, int|float}>
     */
    public static function dataProviderForIntegerMidpoint(): array
    {
        return [
            'even sum' => [2, 6, 4],
            'odd sum' => [1, 2, 1.5],
            'negative, odd sum' => [-7, -2, -4.5],
            'mixed signs, odd sum' => [-4, 7, 1.5],
            'reversed order' => [9, 2, 5.5],
            'equal' => [5, 5, 5],
            '-1 and 0 give -0.5' => [-1, 0, -0.5],
            'zeros' => [0, 0, 0],
            'opposites give int 0' => [-3, 3, 0],
            'largest portable opposites' => [2147483647, -2147483647, 0],
        ];
    }

    /**
     * @test integerMidpoint of 64-bit integers is exact for an even sum, even an overflowing one, and otherwise correctly rounded
     * @dataProvider dataProviderForIntegerMidpointSixtyFourBit
     * @param int|float $lo
     * @param int|float $hi
     * @param int|float $expected
     */
    public function testIntegerMidpointIsExactOrCorrectlyRoundedSixtyFourBit(
        int|float $lo,
        int|float $hi,
        int|float $expected
    ): void {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $this->assertIsInt($lo);
        $this->assertIsInt($hi);

        // When
        $result = Arithmetic::integerMidpoint($lo, $hi);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python int arithmetic and fractions.Fraction
     * (tools/oracles/arithmetic_midpoint_oracles.py):
     *   (lo + hi) // 2 if (lo + hi) % 2 == 0 else float(Fraction(lo + hi, 2))
     * keeping only rows with lo and hi ints in [-2^63, 2^63). float() rounds once, ties to even.
     * Rows that need 64-bit integers.
     *
     * @return array<string, array{int|float, int|float, int|float}>
     */
    public static function dataProviderForIntegerMidpointSixtyFourBit(): array
    {
        return [
            '2^31 - 1 and -2^31' => [2147483647, -2147483648, -0.5],
            '2^31 - 1 with itself, past 32-bit sum' => [2147483647, 2147483647, 2147483647],
            'PHP_INT_MAX with itself' => [\PHP_INT_MAX, \PHP_INT_MAX, \PHP_INT_MAX],
            'PHP_INT_MIN with itself' => [\PHP_INT_MIN, \PHP_INT_MIN, \PHP_INT_MIN],
            'PHP_INT_MIN and PHP_INT_MAX' => [\PHP_INT_MIN, \PHP_INT_MAX, -0.5],
            'PHP_INT_MAX and PHP_INT_MIN, reversed' => [\PHP_INT_MAX, \PHP_INT_MIN, -0.5],
            'PHP_INT_MAX - 1 and PHP_INT_MAX' => [\PHP_INT_MAX - 1, \PHP_INT_MAX, 2.0 ** 63],
            'PHP_INT_MAX - 2 and PHP_INT_MAX' => [\PHP_INT_MAX - 2, \PHP_INT_MAX, \PHP_INT_MAX - 1],
            'PHP_INT_MIN and PHP_INT_MIN + 1' => [\PHP_INT_MIN, \PHP_INT_MIN + 1, -(2.0 ** 63)],
            'PHP_INT_MIN and PHP_INT_MIN + 2' => [\PHP_INT_MIN, \PHP_INT_MIN + 2, \PHP_INT_MIN + 1],
            'PHP_INT_MIN + 1 and PHP_INT_MIN, reversed' => [\PHP_INT_MIN + 1, \PHP_INT_MIN, -(2.0 ** 63)],
            '2^62 with itself' => [4611686018427387904, 4611686018427387904, 4611686018427387904],
            '2^62 and 2^62 + 2' => [4611686018427387904, 4611686018427387906, 4611686018427387905],
            '2^62 and 2^62 + 1' => [4611686018427387904, 4611686018427387905, 2.0 ** 62],
            '2^53 + 1 and 2^53 + 2' => [9007199254740993, 9007199254740994, 9007199254740994.0],
            '-2^62 and -2^62 - 1' => [-4611686018427387904, -4611686018427387905, -(2.0 ** 62)],
            'sum 2^63 + 1023' => [4611686018427388415, 4611686018427388416, 2.0 ** 62],
            'sum -(2^63 + 1023)' => [-4611686018427388415, -4611686018427388416, -(2.0 ** 62)],
            'sum 2^63 + 1024' => [4611686018427388416, 4611686018427388416, 4611686018427388416],
            'sum -(2^63 + 1024)' => [-4611686018427388416, -4611686018427388416, -4611686018427388416],
            'sum 2^63 + 1025' => [4611686018427388416, 4611686018427388417, 4.611686018427389e+18],
            'sum -(2^63 + 1025)' => [-4611686018427388416, -4611686018427388417, -4.611686018427389e+18],
            'sum 2^63 + 3071' => [4611686018427389439, 4611686018427389440, 4.611686018427389e+18],
            'sum -(2^63 + 3071)' => [-4611686018427389439, -4611686018427389440, -4.611686018427389e+18],
            'sum 2^63 + 3072' => [4611686018427389440, 4611686018427389440, 4611686018427389440],
            'sum -(2^63 + 3072)' => [-4611686018427389440, -4611686018427389440, -4611686018427389440],
            'sum 2^63 + 3073' => [4611686018427389440, 4611686018427389441, 4.61168601842739e+18],
            'sum -(2^63 + 3073)' => [-4611686018427389440, -4611686018427389441, -4.61168601842739e+18],
            'seeded same sign 0' => [7577422020003341975, 6852283550064351478, 7.214852785033847e+18],
            'seeded same sign 1' => [8027697459347580297, 6487481765930213869, 7257589612638897083],
            'seeded same sign 2' => [7618493483275807478, 6035372008479067604, 6826932745877437541],
            'seeded same sign 3' => [8037863345568282040, 4720560737441700204, 6379212041504991122],
            'seeded mixed 0' => [-1240819850975622103, 4048933818720459157, 1404056983872418527],
            'seeded mixed 1' => [6783388099842016223, -4208667861649115818, 1.2873601190964503e+18],
        ];
    }

    /**
     * @test integerMidpoint of 32-bit integers whose sum overflows is an int for an even sum, as without overflow
     * @dataProvider dataProviderForIntegerMidpointThirtyTwoBit
     * @param int       $lo
     * @param int       $hi
     * @param int|float $expected
     */
    public function testIntegerMidpointOfAnOverflowingSumThirtyTwoBit(int $lo, int $hi, int|float $expected): void
    {
        // Given (only on 32-bit PHP does the sum overflow; on 64-bit PHP these rows take the direct division)
        if (\PHP_INT_SIZE !== 4) {
            $this->markTestSkipped('Requires 32-bit integers');
        }

        // When
        $result = Arithmetic::integerMidpoint($lo, $hi);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python int arithmetic and fractions.Fraction
     * (tools/oracles/arithmetic_midpoint_oracles.py):
     *   (lo + hi) // 2 if (lo + hi) % 2 == 0 else float(Fraction(lo + hi, 2))
     * keeping only rows with lo and hi in [-2^31, 2^31) whose sum is outside [-2^31, 2^31 - 1], so it
     * overflows on 32-bit PHP. -2^31 is written (-2147483647 - 1): the literal -2147483648 is a float there.
     *
     * @return array<string, array{int, int, int|float}>
     */
    public static function dataProviderForIntegerMidpointThirtyTwoBit(): array
    {
        return [
            'positive, even sum' => [2147483645, 2147483647, 2147483646],
            'positive, odd sum' => [2147483646, 2147483647, 2147483646.5],
            'positive, even sum, reversed' => [2147483647, 2147483645, 2147483646],
            'positive, odd sum, reversed' => [2147483647, 2147483646, 2147483646.5],
            'negative, even sum' => [(-2147483647 - 1), -2147483646, -2147483647],
            'negative, odd sum' => [(-2147483647 - 1), -2147483647, -2147483647.5],
            'negative, even sum, reversed' => [-2147483646, (-2147483647 - 1), -2147483647],
            'negative, odd sum, reversed' => [-2147483647, (-2147483647 - 1), -2147483647.5],
            '2^31 - 1 with itself' => [2147483647, 2147483647, 2147483647],
            '-2^31 with itself' => [(-2147483647 - 1), (-2147483647 - 1), (-2147483647 - 1)],
            'smallest positive overflow, sum 2^31' => [1073741824, 1073741824, 1073741824],
            'positive overflow, sum 2^31 + 1' => [1073741824, 1073741825, 1073741824.5],
            'negative overflow, sum -2^31 - 1' => [-1073741824, -1073741825, -1073741824.5],
            'negative overflow, sum -2^31 - 2' => [-1073741825, -1073741825, -1073741825],
        ];
    }

    /**
     * @test integerDifference is the exact difference of two integers
     * @dataProvider dataProviderForIntegerDifference
     * @param int       $a
     * @param int       $b
     * @param int|float $expected
     */
    public function testIntegerDifferenceIsExactOrCorrectlyRounded(int $a, int $b, int|float $expected): void
    {
        // When
        $result = Arithmetic::integerDifference($a, $b);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python int arithmetic (tools/oracles/arithmetic_midpoint_oracles.py):
     *   a - b if -2**63 <= a - b < 2**63 else float(a - b)
     * keeping only rows with a and b ints in [-2^63, 2^63). float() rounds once, ties to even.
     * The ints and their difference stay within ±(2^31 - 1), so the rows hold on 32-bit PHP too.
     *
     * @return array<string, array{int, int, int|float}>
     */
    public static function dataProviderForIntegerDifference(): array
    {
        return [
            'positive' => [7, 3, 4],
            'negative' => [3, 7, -4],
            'negative minus positive' => [-7, 3, -10],
            'negative minus negative' => [-3, -7, 4],
            'zeros' => [0, 0, 0],
            'equal' => [5, 5, 0],
            'portable bound' => [1000000000, -1000000000, 2000000000],
        ];
    }

    /**
     * @test integerDifference of 64-bit integers is an exact int when it fits, and otherwise the correctly rounded float
     * @dataProvider dataProviderForIntegerDifferenceSixtyFourBit
     * @param int|float $a
     * @param int|float $b
     * @param int|float $expected
     */
    public function testIntegerDifferenceIsExactOrCorrectlyRoundedSixtyFourBit(
        int|float $a,
        int|float $b,
        int|float $expected
    ): void {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $this->assertIsInt($a);
        $this->assertIsInt($b);

        // When
        $result = Arithmetic::integerDifference($a, $b);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python int arithmetic (tools/oracles/arithmetic_midpoint_oracles.py):
     *   a - b if -2**63 <= a - b < 2**63 else float(a - b)
     * keeping only rows with a and b ints in [-2^63, 2^63). float() rounds once, ties to even.
     * Rows that need 64-bit integers.
     *
     * @return array<string, array{int|float, int|float, int|float}>
     */
    public static function dataProviderForIntegerDifferenceSixtyFourBit(): array
    {
        return [
            '2^31 - 1 minus -(2^31 - 1), past 32-bit' => [2147483647, -2147483647, 4294967294],
            'PHP_INT_MAX minus PHP_INT_MIN' => [\PHP_INT_MAX, \PHP_INT_MIN, 2.0 ** 64],
            'PHP_INT_MIN minus PHP_INT_MAX' => [\PHP_INT_MIN, \PHP_INT_MAX, -(2.0 ** 64)],
            'PHP_INT_MAX minus -1' => [\PHP_INT_MAX, -1, 2.0 ** 63],
            'PHP_INT_MIN minus 1' => [\PHP_INT_MIN, 1, -(2.0 ** 63)],
            '-1 minus PHP_INT_MAX fits' => [-1, \PHP_INT_MAX, \PHP_INT_MIN],
            '-1 minus PHP_INT_MIN fits' => [-1, \PHP_INT_MIN, \PHP_INT_MAX],
            '0 minus PHP_INT_MIN' => [0, \PHP_INT_MIN, 2.0 ** 63],
            'PHP_INT_MIN minus itself' => [\PHP_INT_MIN, \PHP_INT_MIN, 0],
            'PHP_INT_MAX minus 0' => [\PHP_INT_MAX, 0, \PHP_INT_MAX],
            '0 minus PHP_INT_MAX' => [0, \PHP_INT_MAX, \PHP_INT_MIN + 1],
            '2^62 minus -2^62' => [4611686018427387904, -4611686018427387904, 2.0 ** 63],
            '2^62 + 1 minus -(2^62 + 1)' => [4611686018427387905, -4611686018427387905, 2.0 ** 63],
            'd = 2^63 + 1023, a even, b odd' => [4611686018427388504, -4611686018427388327, 2.0 ** 63],
            '-(d = 2^63 + 1023, a even, b odd)' => [-4611686018427388504, 4611686018427388327, -(2.0 ** 63)],
            'd = 2^63 + 1023, a odd, b even' => [4611686018427388505, -4611686018427388326, 2.0 ** 63],
            '-(d = 2^63 + 1023, a odd, b even)' => [-4611686018427388505, 4611686018427388326, -(2.0 ** 63)],
            'd = 2^63 + 1024, a even, b even' => [4611686018427388504, -4611686018427388328, 2.0 ** 63],
            '-(d = 2^63 + 1024, a even, b even)' => [-4611686018427388504, 4611686018427388328, -(2.0 ** 63)],
            'd = 2^63 + 1024, a odd, b odd' => [4611686018427388505, -4611686018427388327, 2.0 ** 63],
            '-(d = 2^63 + 1024, a odd, b odd)' => [-4611686018427388505, 4611686018427388327, -(2.0 ** 63)],
            'd = 2^63 + 1025, a even, b odd' => [4611686018427388504, -4611686018427388329, 9.223372036854778e+18],
            '-(d = 2^63 + 1025, a even, b odd)' => [-4611686018427388504, 4611686018427388329, -9.223372036854778e+18],
            'd = 2^63 + 1025, a odd, b even' => [4611686018427388505, -4611686018427388328, 9.223372036854778e+18],
            '-(d = 2^63 + 1025, a odd, b even)' => [-4611686018427388505, 4611686018427388328, -9.223372036854778e+18],
            'd = 2^63 + 3071, a even, b odd' => [4611686018427388504, -4611686018427390375, 9.223372036854778e+18],
            '-(d = 2^63 + 3071, a even, b odd)' => [-4611686018427388504, 4611686018427390375, -9.223372036854778e+18],
            'd = 2^63 + 3071, a odd, b even' => [4611686018427388505, -4611686018427390374, 9.223372036854778e+18],
            '-(d = 2^63 + 3071, a odd, b even)' => [-4611686018427388505, 4611686018427390374, -9.223372036854778e+18],
            'd = 2^63 + 3072, a even, b even' => [4611686018427388504, -4611686018427390376, 9.22337203685478e+18],
            '-(d = 2^63 + 3072, a even, b even)' => [-4611686018427388504, 4611686018427390376, -9.22337203685478e+18],
            'd = 2^63 + 3072, a odd, b odd' => [4611686018427388505, -4611686018427390375, 9.22337203685478e+18],
            '-(d = 2^63 + 3072, a odd, b odd)' => [-4611686018427388505, 4611686018427390375, -9.22337203685478e+18],
            'd = 2^63 + 3073, a even, b odd' => [4611686018427388504, -4611686018427390377, 9.22337203685478e+18],
            '-(d = 2^63 + 3073, a even, b odd)' => [-4611686018427388504, 4611686018427390377, -9.22337203685478e+18],
            'd = 2^63 + 3073, a odd, b even' => [4611686018427388505, -4611686018427390376, 9.22337203685478e+18],
            '-(d = 2^63 + 3073, a odd, b even)' => [-4611686018427388505, 4611686018427390376, -9.22337203685478e+18],
            'seeded overflow 0' => [-4875385508720580250, 7987669265127000805, -1.286305477384758e+19],
            'seeded overflow 1' => [-7593611745849744564, 5973263871548721205, -1.3566875617398467e+19],
            'seeded overflow 2' => [-7062350531075291905, 5557201348007446768, -1.2619551879082738e+19],
            'seeded overflow 3' => [8787165588813781646, -5429196237832586984, 1.4216361826646368e+19],
            'seeded 0' => [-948151055645296053, 840645158612638637, -1788796214257934690],
            'seeded 1' => [2282319872562884113, -3776872805468151057, 6059192678031035170],
        ];
    }
}

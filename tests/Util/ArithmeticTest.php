<?php

declare(strict_types=1);

namespace IterTools\Tests\Util;

use IterTools\Util\Arithmetic;

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
     * @test halfFloatSpacing returns half the gap between a float and the next float away from zero
     * @dataProvider dataProviderForHalfFloatSpacing
     * @param float $x
     * @param float $expected
     */
    public function testHalfFloatSpacing(float $x, float $expected): void
    {
        // When
        $result = Arithmetic::halfFloatSpacing($x);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   math.ulp(x) / 2
     *
     * @return array<array{float, float}>
     */
    public static function dataProviderForHalfFloatSpacing(): array
    {
        return [
            [1.0, 1.1102230246251565e-16],
            [-1.0, 1.1102230246251565e-16],
            [1.5, 1.1102230246251565e-16],
            [2.0, 2.220446049250313e-16],
            [1.9999999999999998, 1.1102230246251565e-16],
            [3.0, 2.220446049250313e-16],
            [0.75, 5.551115123125783e-17],
            [0.1, 6.938893903907228e-18],
            [-0.1, 6.938893903907228e-18],
            [1.0e15, 0.0625],
            [1.0e16, 1.0],
            [2.0 ** 53, 1.0],
            [2.0 ** 53 - 1.0, 0.5],
            [0.125, 1.3877787807814457e-17],
            [2.0 ** 100, 140737488355328.0],
            [2.0 ** 100 - 2.0 ** 47, 70368744177664.0],
            [2.0 ** -1000, 1.036131e-317],
            [1.0e-300, 8.289046e-317],
            [1.0e300, 7.435084542388915e+283],
            [2.0 ** 1023, 9.9792015476736e+291],
            [-\PHP_FLOAT_MAX, 9.9792015476736e+291],
            [\PHP_FLOAT_MAX, 9.9792015476736e+291],
            [\PHP_FLOAT_MIN, 0.0],
            [2.0 * \PHP_FLOAT_MIN, 5e-324],
            [2.225073858507201e-308, 0.0],
            [1.0e-310, 0.0],
            [5.0e-324, 0.0],
            [0.0, 0.0],
            [-0.0, 0.0],
        ];
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Precision;

use IterTools\Util\Precision\Ulp;

class UlpTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test halfFloatSpacing returns half the gap between a float and the next float away from zero
     * @dataProvider dataProviderForHalfFloatSpacing
     * @param float $x
     * @param float $expected
     */
    public function testHalfFloatSpacing(float $x, float $expected): void
    {
        // When
        $result = Ulp::halfFloatSpacing($x);

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

    /**
     * @test nextFloat returns the adjacent float above or below a finite float
     * @dataProvider dataProviderForNextFloat
     * @param float $x
     * @param bool  $up
     * @param float $expected
     */
    public function testNextFloatReturnsAdjacentFloatInDirection(float $x, bool $up, float $expected): void
    {
        // Given (nextFloat works on the 64-bit bit pattern)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Ulp::nextFloat($x, $up);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame(\fdiv(1.0, $expected), \fdiv(1.0, $result));
    }

    /**
     * Test data generated with:
     *   math.nextafter(x, math.inf) if up else math.nextafter(x, -math.inf), for finite x only
     *
     * @return array<string, array{float, bool, float}>
     */
    public static function dataProviderForNextFloat(): array
    {
        return [
            '0.0 up' => [0.0, true, 5e-324],
            '0.0 down' => [0.0, false, -5e-324],
            '-0.0 up' => [-0.0, true, 5e-324],
            '-0.0 down' => [-0.0, false, -5e-324],
            '2^-1074 up' => [5e-324, true, 1e-323],
            '2^-1074 down' => [5e-324, false, 0.0],
            '-2^-1074 up' => [-5e-324, true, -0.0],
            '-2^-1074 down' => [-5e-324, false, -1e-323],
            '2^-1073 up' => [1e-323, true, 1.5e-323],
            '2^-1073 down' => [1e-323, false, 5e-324],
            'PHP_FLOAT_MIN up' => [\PHP_FLOAT_MIN, true, 2.225073858507202e-308],
            'PHP_FLOAT_MIN down' => [\PHP_FLOAT_MIN, false, 2.225073858507201e-308],
            '-PHP_FLOAT_MIN up' => [-\PHP_FLOAT_MIN, true, -2.225073858507201e-308],
            '-PHP_FLOAT_MIN down' => [-\PHP_FLOAT_MIN, false, -2.225073858507202e-308],
            'largest subnormal up' => [2.225073858507201e-308, true, \PHP_FLOAT_MIN],
            'largest subnormal down' => [2.225073858507201e-308, false, 2.2250738585072004e-308],
            '-largest subnormal up' => [-2.225073858507201e-308, true, -2.2250738585072004e-308],
            '-largest subnormal down' => [-2.225073858507201e-308, false, -\PHP_FLOAT_MIN],
            '0.5 up' => [0.5, true, 0.5000000000000001],
            '0.5 down' => [0.5, false, 0.49999999999999994],
            '1.0 up' => [1.0, true, 1.0000000000000002],
            '1.0 down' => [1.0, false, 0.9999999999999999],
            '-1.0 up' => [-1.0, true, -0.9999999999999999],
            '-1.0 down' => [-1.0, false, -1.0000000000000002],
            '1.9999999999999998 up' => [1.9999999999999998, true, 2.0],
            '1.9999999999999998 down' => [1.9999999999999998, false, 1.9999999999999996],
            '2.0 up' => [2.0, true, 2.0000000000000004],
            '2.0 down' => [2.0, false, 1.9999999999999998],
            '-2.0 up' => [-2.0, true, -1.9999999999999998],
            '-2.0 down' => [-2.0, false, -2.0000000000000004],
            '2^53 up' => [9007199254740992.0, true, 9007199254740994.0],
            '2^53 down' => [9007199254740992.0, false, 9007199254740991.0],
            '-2^53 up' => [-9007199254740992.0, true, -9007199254740991.0],
            '-2^53 down' => [-9007199254740992.0, false, -9007199254740994.0],
            '2^1023 up' => [8.98846567431158e+307, true, 8.988465674311582e+307],
            '2^1023 down' => [8.98846567431158e+307, false, 8.988465674311579e+307],
            '-2^1023 up' => [-8.98846567431158e+307, true, -8.988465674311579e+307],
            '-2^1023 down' => [-8.98846567431158e+307, false, -8.988465674311582e+307],
            'PHP_FLOAT_MAX up' => [\PHP_FLOAT_MAX, true, \INF],
            'PHP_FLOAT_MAX down' => [\PHP_FLOAT_MAX, false, 1.7976931348623155e+308],
            '-PHP_FLOAT_MAX up' => [-\PHP_FLOAT_MAX, true, -1.7976931348623155e+308],
            '-PHP_FLOAT_MAX down' => [-\PHP_FLOAT_MAX, false, -\INF],
            '0.1 up' => [0.1, true, 0.10000000000000002],
            '0.1 down' => [0.1, false, 0.09999999999999999],
            '-0.1 up' => [-0.1, true, -0.09999999999999999],
            '-0.1 down' => [-0.1, false, -0.10000000000000002],
            '1e300 up' => [1e+300, true, 1.0000000000000002e+300],
            '1e300 down' => [1e+300, false, 9.999999999999999e+299],
            '-1e-300 up' => [-1e-300, true, -9.999999999999999e-301],
            '-1e-300 down' => [-1e-300, false, -1.0000000000000002e-300],
            'random 1 up' => [1.278622391350082e-11, true, 1.2786223913500822e-11],
            'random 1 down' => [1.278622391350082e-11, false, 1.2786223913500819e-11],
            'random 2 up' => [-1.8852257052242e-201, true, -1.8852257052241995e-201],
            'random 2 down' => [-1.8852257052242e-201, false, -1.8852257052242002e-201],
            'random 3 up' => [1.8495478477068476e+289, true, 1.8495478477068478e+289],
            'random 3 down' => [1.8495478477068476e+289, false, 1.8495478477068474e+289],
            'random 4 up' => [-1.4788019135862963e+18, true, -1.478801913586296e+18],
            'random 4 down' => [-1.4788019135862963e+18, false, -1.4788019135862966e+18],
            'random 5 up' => [1.3056383685098237e+305, true, 1.3056383685098239e+305],
            'random 5 down' => [1.3056383685098237e+305, false, 1.3056383685098235e+305],
            'random 6 up' => [4.171883120538905e+231, true, 4.1718831205389054e+231],
            'random 6 down' => [4.171883120538905e+231, false, 4.171883120538904e+231],
        ];
    }

    /**
     * @test floatBits returns a float's 64-bit pattern read as a signed integer
     * @dataProvider dataProviderForFloatBits
     * @param float     $x
     * @param int|float $expected
     */
    public function testFloatBitsReturnsBitPattern(float $x, int|float $expected): void
    {
        // Given (on 32-bit PHP these literals parse as floats and pack('q') is unavailable)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Ulp::floatBits($x);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   struct.unpack('<q', struct.pack('<d', x))[0], for x not NAN
     *
     * @return array<string, array{float, int}>
     */
    public static function dataProviderForFloatBits(): array
    {
        return [
            '0.0' => [0.0, 0],
            '-0.0' => [-0.0, \PHP_INT_MIN],
            '1.0' => [1.0, 4607182418800017408],
            '-1.0' => [-1.0, -4616189618054758400],
            '2.0' => [2.0, 4611686018427387904],
            '0.1' => [0.1, 4591870180066957722],
            '2^-1074' => [5e-324, 1],
            '-2^-1074' => [-5e-324, -9223372036854775807],
            'largest subnormal' => [2.225073858507201e-308, 4503599627370495],
            'PHP_FLOAT_MIN' => [\PHP_FLOAT_MIN, 4503599627370496],
            '-PHP_FLOAT_MIN' => [-\PHP_FLOAT_MIN, -9218868437227405312],
            'PHP_FLOAT_MAX' => [\PHP_FLOAT_MAX, 9218868437227405311],
            '-PHP_FLOAT_MAX' => [-\PHP_FLOAT_MAX, -4503599627370497],
            'INF' => [\INF, 9218868437227405312],
            '-INF' => [-\INF, -4503599627370496],
            'random 1' => [3839.2783783307978, 4660661077512209762],
            'random 2' => [-2.997151462327892e+126, -2724196315373624535],
            'random 3' => [5.8305525076974476e+243, 8253782275126916484],
            'random 4' => [-4.620381460486248e-189, -7434152098751893843],
            'random 5' => [-4.2335894302650833e+229, -1181090495804489381],
            'random 6' => [-2.8589460503159658e-297, -9052821880874082722],
        ];
    }

    /**
     * @test evenOf returns whichever of two adjacent floats has an even bit pattern
     * @dataProvider dataProviderForEvenOf
     * @param float $a
     * @param float $b
     * @param float $expected
     */
    public function testEvenOfReturnsAdjacentFloatWithEvenBitPattern(float $a, float $b, float $expected): void
    {
        // Given (evenOf reads the 64-bit bit pattern)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Ulp::evenOf($a, $b);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame(\fdiv(1.0, $expected), \fdiv(1.0, $result));
    }

    /**
     * Test data generated with:
     *   a if struct.unpack('<q', struct.pack('<d', a))[0] & 1 == 0 else b,
     *   for finite a, b with b == math.nextafter(a, math.inf) or b == math.nextafter(a, -math.inf)
     *
     * @return array<string, array{float, float, float}>
     */
    public static function dataProviderForEvenOf(): array
    {
        return [
            '1.0 and above' => [1.0, 1.0000000000000002, 1.0],
            '1.0 and above, reversed' => [1.0000000000000002, 1.0, 1.0],
            '1.0 and below' => [1.0, 0.9999999999999999, 1.0],
            '1.0 and below, reversed' => [0.9999999999999999, 1.0, 1.0],
            '2.0 and below' => [2.0, 1.9999999999999998, 2.0],
            '2.0 and below, reversed' => [1.9999999999999998, 2.0, 2.0],
            '-1.0 and below' => [-1.0, -1.0000000000000002, -1.0],
            '-1.0 and below, reversed' => [-1.0000000000000002, -1.0, -1.0],
            '-2.0 and above' => [-2.0, -1.9999999999999998, -2.0],
            '-2.0 and above, reversed' => [-1.9999999999999998, -2.0, -2.0],
            '0.1 and above' => [0.1, 0.10000000000000002, 0.1],
            '0.1 and above, reversed' => [0.10000000000000002, 0.1, 0.1],
            'PHP_FLOAT_MIN and below' => [\PHP_FLOAT_MIN, 2.225073858507201e-308, \PHP_FLOAT_MIN],
            'PHP_FLOAT_MIN and below, reversed' => [2.225073858507201e-308, \PHP_FLOAT_MIN, \PHP_FLOAT_MIN],
            'PHP_FLOAT_MIN and above' => [\PHP_FLOAT_MIN, 2.225073858507202e-308, \PHP_FLOAT_MIN],
            'PHP_FLOAT_MIN and above, reversed' => [2.225073858507202e-308, \PHP_FLOAT_MIN, \PHP_FLOAT_MIN],
            '-PHP_FLOAT_MIN and above' => [-\PHP_FLOAT_MIN, -2.225073858507201e-308, -\PHP_FLOAT_MIN],
            '-PHP_FLOAT_MIN and above, reversed' => [-2.225073858507201e-308, -\PHP_FLOAT_MIN, -\PHP_FLOAT_MIN],
            '0.0 and 2^-1074' => [0.0, 5e-324, 0.0],
            '0.0 and 2^-1074, reversed' => [5e-324, 0.0, 0.0],
            '-0.0 and -2^-1074' => [-0.0, -5e-324, -0.0],
            '-0.0 and -2^-1074, reversed' => [-5e-324, -0.0, -0.0],
            '2^-1074 and 2^-1073' => [5e-324, 1e-323, 1e-323],
            '2^-1074 and 2^-1073, reversed' => [1e-323, 5e-324, 1e-323],
            'PHP_FLOAT_MAX and below' => [\PHP_FLOAT_MAX, 1.7976931348623155e+308, 1.7976931348623155e+308],
            'PHP_FLOAT_MAX and below, reversed' => [1.7976931348623155e+308, \PHP_FLOAT_MAX, 1.7976931348623155e+308],
            '-PHP_FLOAT_MAX and above' => [-\PHP_FLOAT_MAX, -1.7976931348623155e+308, -1.7976931348623155e+308],
            '-PHP_FLOAT_MAX and above, reversed' => [-1.7976931348623155e+308, -\PHP_FLOAT_MAX, -1.7976931348623155e+308],
            '0.0 and -2^-1074' => [0.0, -5e-324, 0.0],
            '0.0 and -2^-1074, reversed' => [-5e-324, 0.0, 0.0],
            '-0.0 and 2^-1074' => [-0.0, 5e-324, -0.0],
            '-0.0 and 2^-1074, reversed' => [5e-324, -0.0, -0.0],
            'random 1 and above' => [-4.015211284284195e+245, -4.0152112842841946e+245, -4.015211284284195e+245],
            'random 1 and above, reversed' => [-4.0152112842841946e+245, -4.015211284284195e+245, -4.015211284284195e+245],
            'random 2 and above' => [3.3619009816731713e-146, 3.3619009816731717e-146, 3.3619009816731713e-146],
            'random 2 and above, reversed' => [3.3619009816731717e-146, 3.3619009816731713e-146, 3.3619009816731713e-146],
            'random 3 and above' => [-6.664940449358543e-165, -6.664940449358542e-165, -6.664940449358542e-165],
            'random 3 and above, reversed' => [-6.664940449358542e-165, -6.664940449358543e-165, -6.664940449358542e-165],
        ];
    }
}

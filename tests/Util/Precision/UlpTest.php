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
}

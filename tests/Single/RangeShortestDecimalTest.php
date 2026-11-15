<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;

/**
 * The shortest decimal that reads back as a float, which range() takes a non-integral float operand for.
 *
 * Just below a power of two, floats are twice as dense as just above it, so the decimal nearest the float at a
 * digit count may not read back as it while its neighbour in the last digit does.
 */
class RangeShortestDecimalTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         range ending on a power of two, whose shortest decimal makes the step equal the span
     * @dataProvider dataProviderForRangeWithPowerOfTwoEndpoint
     * @param        float        $start
     * @param        float        $end
     * @param        float        $step
     * @param        array<float> $expected
     */
    public function testRangeWithPowerOfTwoEndpoint(float $start, float $end, float $step, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's Fraction, taking each operand as the decimal it is written as:
     *   s, e, st = Fraction(repr(start)), Fraction(repr(end)), Fraction(repr(step))
     *   sign = 1 if e > s else -1
     *   [float(s + sign * i * abs(st)) for i in range(floor(abs(e - s) / abs(st)) + 1)]
     */
    public static function dataProviderForRangeWithPowerOfTwoEndpoint(): array
    {
        return [
            'up to 2^-77' => [
                -2e-25, 6.617444900424222e-24, 6.817444900424222e-24,
                [-2e-25, 6.617444900424222e-24],
            ],
            'down from 2^-77' => [
                6.617444900424222e-24, -2e-25, -6.817444900424222e-24,
                [6.617444900424222e-24, -2e-25],
            ],
            'up to 2^-383' => [
                -2e-117, 5.075883674631299e-116, 5.275883674631299e-116,
                [-2e-117, 5.075883674631299e-116],
            ],
            'up to 2^-489' => [
                -2e-149, 6.256509672447191e-148, 6.456509672447191e-148,
                [-2e-149, 6.256509672447191e-148],
            ],
            'up to 2^-695' => [
                -2e-211, 6.083493012144512e-210, 6.283493012144512e-210,
                [-2e-211, 6.083493012144512e-210],
            ],
            'down from 2^-791' => [
                7.678447687145631e-239, -2e-240, -7.878447687145631e-239,
                [7.678447687145631e-239, -2e-240],
            ],
            'up to 2^-791' => [
                -2e-240, 7.678447687145631e-239, 7.878447687145631e-239,
                [-2e-240, 7.678447687145631e-239],
            ],
        ];
    }
}

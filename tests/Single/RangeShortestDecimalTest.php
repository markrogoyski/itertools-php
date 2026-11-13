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
     * @test         shortestDecimal finds the shortest decimal of a power of two whose nearest decimal at that length
     *               doesn't read back
     * @dataProvider dataProviderForShortestDecimalAtPowerOfTwo
     * @param        float                    $x
     * @param        array{string, int, int} $expected
     */
    public function testShortestDecimalAtPowerOfTwo(float $x, array $expected): void
    {
        // Given
        $method = new \ReflectionMethod(Single::class, 'shortestDecimal');

        // When
        $result = $method->invoke(null, $x);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's shortest round-tripping repr, as its significand d.ddd, its exponent and
     * the number of digits after the point:
     *   t = Decimal(repr(2.0 ** k)).normalize().as_tuple(); digits = ''.join(map(str, t.digits))
     *   [digits[0] + ('.' + digits[1:] if len(digits) > 1 else ''), t.exponent + len(digits) - 1, len(digits) - 1]
     */
    public static function dataProviderForShortestDecimalAtPowerOfTwo(): array
    {
        return [
            '2^-77'   => [2.0 ** -77, ['6.617444900424222', -24, 15]],
            '2^-24'   => [2.0 ** -24, ['5.960464477539063', -8, 15]],
            '2^-44'   => [2.0 ** -44, ['5.684341886080802', -14, 15]],
            '2^-97'   => [2.0 ** -97, ['6.310887241768095', -30, 15]],
            '2^-1017' => [2.0 ** -1017, ['7.120236347223045', -307, 15]],
            '2^89'    => [2.0 ** 89, ['6.189700196426902', 26, 15]],
            '2^976'   => [2.0 ** 976, ['6.386688990511104', 293, 15]],
        ];
    }

    /**
     * @test         shortestDecimal of floats whose nearest decimal at the shortest length reads back
     * @dataProvider dataProviderForShortestDecimalOfOtherFloats
     * @param        float                    $x
     * @param        array{string, int, int} $expected
     */
    public function testShortestDecimalOfOtherFloats(float $x, array $expected): void
    {
        // Given
        $method = new \ReflectionMethod(Single::class, 'shortestDecimal');

        // When
        $result = $method->invoke(null, $x);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's shortest round-tripping repr, as its significand d.ddd, its exponent and
     * the number of digits after the point:
     *   t = Decimal(repr(x)).normalize().as_tuple(); digits = ''.join(map(str, t.digits))
     *   [digits[0] + ('.' + digits[1:] if len(digits) > 1 else ''), t.exponent + len(digits) - 1, len(digits) - 1]
     */
    public static function dataProviderForShortestDecimalOfOtherFloats(): array
    {
        return [
            '0.1'                 => [0.1, ['1', -1, 0]],
            '0.3'                 => [0.3, ['3', -1, 0]],
            'smallest subnormal'  => [5e-324, ['5', -324, 0]],
            'largest float'       => [1.7976931348623157e308, ['1.7976931348623157', 308, 16]],
            '2^-76'               => [2.0 ** -76, ['1.3234889800848443', -23, 16]],
            '30.664096287827093'  => [30.664096287827093, ['3.0664096287827093', 1, 16]],
            '1e23'                => [1e23, ['1', 23, 0]],
            '2^54'                => [2.0 ** 54, ['1.8014398509481984', 16, 16]],
        ];
    }

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

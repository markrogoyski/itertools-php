<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;

/**
 * range() behaves the same with 32-bit and 64-bit ints: integral floats past 2^53 and integral float steps at the
 * edge of the int range.
 */
class RangePortabilityTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         isShortestDecimal tells whether an integral float past 2^53 is the shortest decimal that reads back
     *               as it, also for decimals of more digits than a 32-bit int holds
     * @dataProvider dataProviderForIsShortestDecimal
     * @param        float $x
     * @param        bool  $expected
     */
    public function testIsShortestDecimal(float $x, bool $expected): void
    {
        // Given
        $method = new \ReflectionMethod(Single::class, 'isShortestDecimal');

        // When
        $result = $method->invoke(null, $x);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, comparing the shortest round-tripping decimal with the float exactly:
     *   Fraction(repr(x)) == Fraction(x)
     */
    public static function dataProviderForIsShortestDecimal(): array
    {
        return [
            '2^53 + 2'                => [9007199254740994.0, true],
            '2^54'                    => [2.0 ** 54, true],
            '27021597764222988.0'     => [27021597764222988.0, true],
            '1e16'                    => [1e16, true],
            '1e22'                    => [1e22, true],
            '1.2345e20'               => [1.2345e20, true],
            '1.2345e21'               => [1.2345e21, false],
            '1e23'                    => [1e23, false],
            '2^60'                    => [2.0 ** 60, false],
            '2^63'                    => [2.0 ** 63, false],
            'largest float'           => [1.7976931348623157e308, false],
        ];
    }

    /**
     * @test         range with an integral float step at the limits of the int range on either platform
     * @dataProvider dataProviderForIntegralFloatStepAtIntRangeLimits
     * @param        int          $start
     * @param        int          $end
     * @param        float        $step
     * @param        array<mixed> $expected
     */
    public function testIntegralFloatStepAtIntRangeLimits(int $start, int $end, float $step, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's exact integers, as the progression from start by the step, converted to
     * float when the step is beyond the int range:
     *   [start + sign * i * abs(step) for i in range(abs(end - start) // abs(step) + 1)]
     */
    public static function dataProviderForIntegralFloatStepAtIntRangeLimits(): array
    {
        return [
            'up by 2^31 - 1'                  => [0, 2147483647, 2147483647.0, [0, 2147483647]],
            'down by 2^31 - 1'                => [2147483647, 0, -2147483647.0, [2147483647, 0]],
            'up by the first step past ints'  => [
                \PHP_INT_MIN, \PHP_INT_MAX, -(float)\PHP_INT_MIN,
                [(float)\PHP_INT_MIN, 0.0],
            ],
        ];
    }

    /**
     * @test         range with a step of (float) PHP_INT_MAX on 32-bit PHP, where it is exact and within the int range
     * @dataProvider dataProviderForIntegralFloatStepOn32BitIntRangeLimits
     * @param        int          $start
     * @param        int          $end
     * @param        float        $step
     * @param        array<mixed> $expected
     */
    public function testIntegralFloatStepOn32BitIntRangeLimits(int $start, int $end, float $step, array $expected): void
    {
        // Given (on 64-bit PHP, (float) PHP_INT_MAX rounds up to 2^63, past the int range and the span)
        if (\PHP_INT_SIZE !== 4) {
            $this->markTestSkipped('Requires 32-bit integers');
        }

        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's exact integers, with PHP_INT_MAX = 2**31 - 1, as the progression from start
     * by the step:
     *   [start + sign * i * abs(step) for i in range(abs(end - start) // abs(step) + 1)]
     */
    public static function dataProviderForIntegralFloatStepOn32BitIntRangeLimits(): array
    {
        return [
            'up by (float) PHP_INT_MAX'   => [0, \PHP_INT_MAX, (float)\PHP_INT_MAX, [0, \PHP_INT_MAX]],
            'down by (float) PHP_INT_MAX' => [\PHP_INT_MAX, 0, -(float)\PHP_INT_MAX, [\PHP_INT_MAX, 0]],
        ];
    }
}

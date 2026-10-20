<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;

class RangeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         range valid inputs
     * @dataProvider dataProviderForValidRanges
     * @param        int|float       $start
     * @param        int|float       $end
     * @param        int|float       $step
     * @param        array<int|float> $expected
     */
    public function testValidRange(int|float $start, int|float $end, int|float $step, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::range($start, $end, $step) as $value) {
            $result[] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   list(range(start, end + sign, sign * abs(step))), sign being 1 for an ascending and -1 for a
     *   descending range (Single::range takes its direction from the operands, not from the step's sign)
     */
    public static function dataProviderForValidRanges(): array
    {
        return [
            // ascending int positive step
            'ascending ints default step' => [1, 5, 1, [1, 2, 3, 4, 5]],
            'ascending ints zero to ten' => [0, 10, 1, [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]],

            // descending int positive step (direction inferred from operands)
            'descending ints positive step' => [5, 1, 1, [5, 4, 3, 2, 1]],

            // descending int negative step (matching direction)
            'descending ints negative step' => [5, 1, -1, [5, 4, 3, 2, 1]],

            // step > 1 evenly divides
            'step evenly divides' => [0, 10, 2, [0, 2, 4, 6, 8, 10]],

            // step > 1 doesn't evenly divide
            'step does not evenly divide' => [0, 10, 3, [0, 3, 6, 9]],

            // step exactly equal to span
            'step equal to span' => [1, 5, 4, [1, 5]],
            'step equal to span descending' => [5, 1, 4, [5, 1]],

            // single-element range (start == end)
            'single-element start equals end' => [1, 1, 1, [1]],
            'single-element start equals end larger step' => [1, 1, 10, [1]],
            'single-element start equals end negative step' => [1, 1, -1, [1]],

            // negative numbers
            'ascending negatives' => [-5, -1, 1, [-5, -4, -3, -2, -1]],
            'descending negatives' => [-1, -5, 1, [-1, -2, -3, -4, -5]],
            'across zero ascending' => [-2, 2, 1, [-2, -1, 0, 1, 2]],
            'across zero descending' => [2, -2, 1, [2, 1, 0, -1, -2]],
        ];
    }

    /**
     * @test         range valid float inputs yield the correctly rounded values
     * @dataProvider dataProviderForValidFloatRanges
     * @param        float        $start
     * @param        float        $end
     * @param        float        $step
     * @param        array<float> $expected
     */
    public function testValidFloatRange(float $start, float $end, float $step, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with (tools/oracles/range_float_steps.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / Fraction(step)) + 1
     *   value_i = float(Fraction(start) ± i * Fraction(step))
     */
    public static function dataProviderForValidFloatRanges(): array
    {
        return [
            'ascending floats' => [0.5, 5.5, 1.0, [0.5, 1.5, 2.5, 3.5, 4.5, 5.5]],
            'descending floats' => [5.5, 0.5, 1.0, [5.5, 4.5, 3.5, 2.5, 1.5, 0.5]],
            'single-element float' => [2.5, 2.5, 1.0, [2.5]],
        ];
    }

    /**
     * @test         range valid float inputs yield the oracle's count and values within float rounding
     * @dataProvider dataProviderForValidFloatRangesWithinRounding
     * @param        float        $start
     * @param        float        $end
     * @param        float        $step
     * @param        array<float> $expected
     */
    public function testValidFloatRangeWithinRounding(float $start, float $end, float $step, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertCount(\count($expected), $result);
        $this->assertContainsOnly('float', $result);
        $this->assertEqualsWithDelta($expected, $result, self::roundingDelta($start, $end));
    }

    /**
     * Test data generated with (tools/oracles/range_float_steps.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / Fraction(step)) + 1
     *   value_i = float(Fraction(start) ± i * Fraction(step))
     */
    public static function dataProviderForValidFloatRangesWithinRounding(): array
    {
        return [
            // 3 * 1.2 = 3.5999999999999996 in float arithmetic
            'ascending floats fractional step' => [0.0, 5.0, 1.2, [0.0, 1.2, 2.4, 3.6, 4.8]],
        ];
    }

    /**
     * Delta for comparing a float range with the correctly rounded oracle values.
     *
     * Single::range computes each value as $start ± i·$step in float arithmetic, not correctly rounded.
     * Against the exact value, storing $start and $step as floats costs half an ulp each, relative, and
     * i·$step and the sum are each rounded once more; correctly rounding the oracle value adds another
     * half ulp. The offset i·$step is at most the span, which is at most 2·max(|$start|, |$end|), so
     * the difference is at most 3.5·ε·max(|$start|, |$end|).
     */
    private static function roundingDelta(int|float $start, int|float $end): float
    {
        return 4 * \PHP_FLOAT_EPSILON * \max(\abs((float)$start), \abs((float)$end));
    }

    /**
     * @test large-N range produces expected length and endpoints
     */
    public function testLargeRange(): void
    {
        // Given
        $start = 0;
        $end = 1000;

        // When
        $result = \iterator_to_array(Single::range($start, $end), false);

        // Then
        $this->assertCount(1001, $result);
        $this->assertEquals(0, $result[0]);
        $this->assertEquals(1000, $result[1000]);
    }

    /**
     * @test range is lazy — does not materialize entire sequence up-front
     */
    public function testLazyEvaluation(): void
    {
        // Given
        $generator = Single::range(1, \PHP_INT_MAX);

        // When
        $first5 = [];
        foreach ($generator as $value) {
            $first5[] = $value;
            if (\count($first5) === 5) {
                break;
            }
        }

        // Then
        $this->assertEquals([1, 2, 3, 4, 5], $first5);
    }

    /**
     * @test         range invalid inputs throw InvalidArgumentException
     * @dataProvider dataProviderForInvalidRanges
     * @param        int|float $start
     * @param        int|float $end
     * @param        int|float $step
     */
    public function testInvalidRange(int|float $start, int|float $end, int|float $step): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (Single::range($start, $end, $step) as $_) {
            // unreachable
        }
    }

    public static function dataProviderForInvalidRanges(): array
    {
        return [
            'zero step' => [1, 5, 0],
            'zero step start equals end' => [1, 1, 0],
            'zero step float' => [0.0, 1.0, 0.0],

            'conflicting direction ascending operands negative step' => [1, 5, -1],
            'step greater than span ascending' => [1, 5, 10],
            'step greater than span descending' => [5, 1, 10],
            'step greater than span float' => [0.0, 1.0, 1.5],
        ];
    }

    /**
     * @test conflicting direction throws
     */
    public function testConflictingDirectionAscendingOperandsNegativeStep(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (Single::range(1, 5, -1) as $_) {
            // unreachable
        }
    }

    /**
     * @test conflicting direction descending operands negative step ambiguity is allowed since direction inferred from operands and matches negative step
     */
    public function testDescendingOperandsNegativeStepIsValid(): void
    {
        // Given/When
        $result = \iterator_to_array(Single::range(5, 1, -1), false);

        // Then
        $this->assertEquals([5, 4, 3, 2, 1], $result);
    }

    /**
     * @test step strictly greater than span throws
     */
    public function testStepGreaterThanSpanThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (Single::range(1, 5, 10) as $_) {
            // unreachable
        }
    }

    /**
     * @test step equal to span is valid (boundary case)
     */
    public function testStepEqualToSpanIsValid(): void
    {
        // Given/When
        $result = \iterator_to_array(Single::range(1, 5, 4), false);

        // Then
        $this->assertEquals([1, 5], $result);
    }

    /**
     * @test         non-finite operands throw with parameter named in message
     * @dataProvider dataProviderForNonFiniteOperands
     * @param        int|float $start
     * @param        int|float $end
     * @param        int|float $step
     * @param        string    $expectedParamInMessage
     */
    public function testNonFiniteOperandsThrow(
        int|float $start,
        int|float $end,
        int|float $step,
        string $expectedParamInMessage
    ): void {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/' . $expectedParamInMessage . '/');

        // When
        foreach (Single::range($start, $end, $step) as $_) {
            // unreachable
        }
    }

    /**
     * @test float range with magnitude smaller than float spacing terminates (regression: no infinite loop)
     */
    public function testFloatRangeWithMagnitudeBelowFloatSpacingTerminates(): void
    {
        // Given
        // 1e16 + 1.0 == 1e16 in IEEE 754 (gaps at this magnitude exceed 1), so naive
        // accumulator-based iteration would never advance. Iteration must terminate
        // regardless and yield exactly floor(span / step) + 1 elements.
        $start = 1e16;
        $end   = 1e16 + 10.0;
        $step  = 1.0;
        $expectedCount = 11;
        $cap = $expectedCount * 5;

        // When
        $count = 0;
        foreach (Single::range($start, $end, $step) as $_) {
            $count++;
            if ($count > $cap) {
                break;
            }
        }

        // Then
        $this->assertSame($expectedCount, $count);
    }

    /**
     * @test         float range with a step below float spacing yields floor(span / step) + 1 elements, no extra trailing one
     * @dataProvider dataProviderForStepBelowFloatSpacing
     * @param        int|float   $start
     * @param        int|float   $end
     * @param        float       $step
     * @param        array<float> $expected
     */
    public function testFloatRangeWithStepBelowFloatSpacingHasNoExtraTrailingElement(
        int|float $start,
        int|float $end,
        float $step,
        array $expected
    ): void {
        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Float spacing is 2 at 1e16, so the values round, but the count is still floor(span / step) + 1:
     * span 4 / step 0.5 = 8 → 9 elements.
     *
     * Test data generated with (tools/oracles/range_float_steps.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / Fraction(step)) + 1
     *   value_i = float(Fraction(start) ± i * Fraction(step))
     */
    public static function dataProviderForStepBelowFloatSpacing(): array
    {
        return [
            'ascending 1e16 to 1e16 + 4 step 0.5' => [
                1e16, 1e16 + 4.0, 0.5,
                [
                    1e16, 1e16, 1e16,
                    10000000000000002.0, 10000000000000002.0, 10000000000000002.0,
                    10000000000000004.0, 10000000000000004.0, 10000000000000004.0,
                ],
            ],
            'descending 1e16 + 4 to 1e16 step 0.5' => [
                1e16 + 4.0, 1e16, 0.5,
                [
                    10000000000000004.0, 10000000000000004.0, 10000000000000004.0,
                    10000000000000002.0, 10000000000000002.0, 10000000000000002.0,
                    1e16, 1e16, 1e16,
                ],
            ],
            // Non-integral quotients: span 4 / step 1.5 = 2.67 → 3 elements; span 6 / step 2.3 = 2.61 → 3.
            'ascending 1e16 to 1e16 + 4 step 1.5' => [
                1e16, 1e16 + 4.0, 1.5,
                [1e16, 10000000000000002.0, 10000000000000004.0],
            ],
            'descending 1e16 + 4 to 1e16 step 1.5' => [
                1e16 + 4.0, 1e16, 1.5,
                [10000000000000004.0, 10000000000000002.0, 1e16],
            ],
            'ascending 1e16 to 1e16 + 6 step 2.3' => [
                1e16, 1e16 + 6.0, 2.3,
                [1e16, 10000000000000002.0, 10000000000000004.0],
            ],
        ];
    }

    /**
     * @test         int operands beyond 2^53 with a step below float spacing yield floor(span / step) + 1 elements
     * @dataProvider dataProviderForStepBelowFloatSpacingIntOperands
     * @param        int          $start
     * @param        int          $end
     * @param        float        $step
     * @param        array<float> $expected
     */
    public function testFloatRangeWithStepBelowFloatSpacingOnIntOperands(
        int $start,
        int $end,
        float $step,
        array $expected
    ): void {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Float spacing is 2 at 2^53: span 3 / step 1.5 = 2 → 3 elements.
     *
     * Test data generated with (tools/oracles/range_float_steps.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / Fraction(step)) + 1
     *   value_i = float(Fraction(start) ± i * Fraction(step))
     */
    public static function dataProviderForStepBelowFloatSpacingIntOperands(): array
    {
        return [
            'int operands 2^53 to 2^53 + 3 step 1.5' => [
                9007199254740992, 9007199254740995, 1.5,
                [9007199254740992.0, 9007199254740994.0, 9007199254740996.0],
            ],
        ];
    }

    /**
     * @test         float range keeps the endpoint when the last value is reconstructed past $end
     * @dataProvider dataProviderForLastValueReconstructedPastEnd
     * @param        int|float $start
     * @param        int|float $end
     * @param        float     $step
     * @param        int       $count
     * @param        float     $last
     */
    public function testFloatRangeKeepsEndpointWhenLastValueIsReconstructedPastEnd(
        int|float $start,
        int|float $end,
        float $step,
        int $count,
        float $last
    ): void {
        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertCount($count, $result);
        $this->assertSame($last, $result[$count - 1]);
    }

    /**
     * 50 * 1.1 = 55.000000000000007 and 100 * 0.07 = 7.0000000000000009 overshoot $end.
     *
     * Test data generated with (tools/oracles/range_float_steps.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / Fraction(step)) + 1
     *   last = float(Fraction(start) ± (count - 1) * Fraction(step))
     */
    public static function dataProviderForLastValueReconstructedPastEnd(): array
    {
        return [
            'int operands 0 to 55 step 1.1' => [0, 55, 1.1, 51, 55.0],
            'float operands 0.0 to 55.0 step 1.1' => [0.0, 55.0, 1.1, 51, 55.0],
            'int operands 55 to 0 step 1.1' => [55, 0, 1.1, 51, 0.0],
            'float operands 0.0 to 7.0 step 0.07' => [0.0, 7.0, 0.07, 101, 7.0],
        ];
    }

    /**
     * @test         int operands beyond 2^53 whose start rounds away from $end still end on $end
     * @dataProvider dataProviderForIntStartRoundingAwayFromEnd
     * @param        int   $start
     * @param        int   $end
     * @param        float $step
     * @param        int   $count
     * @param        float $first
     * @param        float $last
     */
    public function testFractionalStepOnIntsBeyondFloatPrecisionEndsOnEnd(
        int $start,
        int $end,
        float $step,
        int $count,
        float $first,
        float $last
    ): void {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertCount($count, $result);
        $this->assertSame($first, $result[0]);
        $this->assertSame($last, $result[$count - 1]);
    }

    /**
     * 2^53 + 1 rounds down to 2^53 and 2^53 + 3 rounds up to 2^53 + 4, away from the other end, so
     * start + span falls two short of (float)$end. Only the count and endpoints are asserted: the inner
     * values are $start + i·step in float arithmetic, which is not correctly rounded once (float)$start
     * is inexact.
     *
     * Test data generated with (tools/oracles/range_float_steps.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / Fraction(step)) + 1
     *   first, last = float(Fraction(start)), float(Fraction(end))
     */
    public static function dataProviderForIntStartRoundingAwayFromEnd(): array
    {
        return [
            'ascending 2^53 + 1 to 2^53 + 3' => [
                9007199254740993, 9007199254740995, 0.5,
                5, 9007199254740992.0, 9007199254740996.0,
            ],
            'descending 2^53 + 3 to 2^53 + 1' => [
                9007199254740995, 9007199254740993, 0.5,
                5, 9007199254740996.0, 9007199254740992.0,
            ],
        ];
    }

    /**
     * @test         float range with large operands does not add an element for a quotient that is clearly not integral
     * @dataProvider dataProviderForLargeOperandsWithNonIntegralQuotient
     * @param        float $start
     * @param        float $end
     * @param        float $step
     * @param        int   $count
     * @param        float $last
     */
    public function testFloatRangeWithLargeOperandsHasNoExtraElementForNonIntegralQuotient(
        float $start,
        float $end,
        float $step,
        int $count,
        float $last
    ): void {
        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertCount($count, $result);
        $this->assertSame($last, $result[$count - 1]);
    }

    /**
     * The float spacing is 1/64 at 1e14 and 1/8 at 1e15, so the stored operands are off by at most half of
     * it each. No decimal operands that round to these floats give a whole number of steps: span 1.46875 /
     * step 0.3 = 4.896 → 5 elements; span 3.125 / step 1.1 = 2.84 → 3. Span 1 / step 0.278 = 3.597 → 4 is a
     * whole number of steps only if both operands were rounded the furthest possible in opposite directions,
     * which is too weak a reason to add an element.
     *
     * Test data generated with (tools/oracles/range_float_steps.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / Fraction(step)) + 1
     *   last = float(Fraction(start) ± (count - 1) * Fraction(step))
     */
    public static function dataProviderForLargeOperandsWithNonIntegralQuotient(): array
    {
        return [
            'ascending 1e14 to 1e14 + 1.47 step 0.3' => [1e14, 1e14 + 1.47, 0.3, 5, 100000000000001.2],
            'descending 1e14 + 1.47 to 1e14 step 0.3' => [1e14 + 1.47, 1e14, 0.3, 5, 100000000000000.27],
            'ascending 1e15 to 1e15 + 3.125 step 1.1' => [1e15, 1e15 + 3.125, 1.1, 3, 1000000000000002.2],
            'descending 1e15 + 3.125 to 1e15 step 1.1' => [1e15 + 3.125, 1e15, 1.1, 3, 1000000000000000.9],
            'ascending 1e15 to 1e15 + 1 step 0.278' => [1e15, 1e15 + 1, 0.278, 4, 1000000000000000.9],
            'descending 1e15 + 1 to 1e15 step 0.278' => [1e15 + 1, 1e15, 0.278, 4, 1000000000000000.1],
        ];
    }

    /**
     * @test float range with a step that resolves large operands keeps the endpoint lost to decimal rounding
     */
    public function testFloatRangeWithLargeOperandsKeepsEndpointLostToDecimalRounding(): void
    {
        // Given
        // At 1e12 the float spacing is about 1.2e-4, so a 0.001 step still resolves, but 1e12 + 0.4 and
        // 1e12 + 0.5 are stored inexactly and span / step comes out just under 100.
        $start = 1e12 + 0.4;
        $end   = 1e12 + 0.5;
        $step  = 0.001;

        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertCount(101, $result);
        $this->assertSame($end, $result[100]);
    }

    /**
     * @test         float range near PHP_FLOAT_MAX keeps the endpoint lost to decimal rounding
     * @dataProvider dataProviderForOperandsNearFloatMax
     * @param        float $start
     * @param        float $end
     * @param        float $step
     * @param        int   $count
     * @param        float $last
     */
    public function testFloatRangeNearFloatMaxKeepsEndpointLostToDecimalRounding(
        float $start,
        float $end,
        float $step,
        int $count,
        float $last
    ): void {
        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertCount($count, $result);
        $this->assertSame($last, $result[$count - 1]);
    }

    /**
     * |start| + |end| is about 2e308, past PHP_FLOAT_MAX, but the step resolves the operands, and span / step
     * comes out just under 1000. Within a few hundred float spacings of PHP_FLOAT_MAX, log2 of the operands
     * rounds up to 1024.
     *
     * Test data generated with (tools/oracles/range_float_steps.py), operands taken as written
     * (PHP_FLOAT_MAX exact, decimal literals exact):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / Fraction(step)) + 1
     *   last = float(Fraction(start) ± (count - 1) * Fraction(step))
     */
    public static function dataProviderForOperandsNearFloatMax(): array
    {
        return [
            'ascending 1e308 to 1.00001e308' => [1e308, 1.00001e308, 1e300, 1001, 1.00001e308],
            'descending 1.00001e308 to 1e308' => [1.00001e308, 1e308, 1e300, 1001, 1e308],
            'descending -1e308 to -1.00001e308' => [-1e308, -1.00001e308, 1e300, 1001, -1.00001e308],
            'ascending -1.00001e308 to -1e308' => [-1.00001e308, -1e308, 1e300, 1001, -1e308],
            'ascending to PHP_FLOAT_MAX step 1e293' => [
                \PHP_FLOAT_MAX - 1e294, \PHP_FLOAT_MAX, 1e293,
                11, \PHP_FLOAT_MAX,
            ],
            'descending from PHP_FLOAT_MAX step 1e293' => [
                \PHP_FLOAT_MAX, \PHP_FLOAT_MAX - 1e294, 1e293,
                11, 1.7976931348623057E308,
            ],
            'ascending to PHP_FLOAT_MAX step 1e300' => [
                \PHP_FLOAT_MAX - 1e303, \PHP_FLOAT_MAX, 1e300,
                1001, \PHP_FLOAT_MAX,
            ],
            'descending to -PHP_FLOAT_MAX step 1e300' => [
                -\PHP_FLOAT_MAX + 1e303, -\PHP_FLOAT_MAX, 1e300,
                1001, -\PHP_FLOAT_MAX,
            ],
        ];
    }

    /**
     * @test         distinct int operands beyond 2^53 with a fractional step use the exact int span, not the collapsed float span
     * @dataProvider dataProviderForDistinctIntsBeyondFloatPrecision
     * @param        int          $start
     * @param        int          $end
     * @param        float        $step
     * @param        array<float> $expected
     */
    public function testFractionalStepOnDistinctIntsBeyondFloatPrecision(
        int $start,
        int $end,
        float $step,
        array $expected
    ): void {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * 2^53 and 2^53 + 1 are the same float, but the span is 1, so step 0.5 gives 3 elements.
     *
     * Test data generated with (tools/oracles/range_float_steps.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / Fraction(step)) + 1
     *   value_i = float(Fraction(start) ± i * Fraction(step))
     */
    public static function dataProviderForDistinctIntsBeyondFloatPrecision(): array
    {
        return [
            'ascending 2^53 to 2^53 + 1' => [
                9007199254740992, 9007199254740993, 0.5,
                [9007199254740992.0, 9007199254740992.0, 9007199254740992.0],
            ],
            'descending 2^53 + 1 to 2^53' => [
                9007199254740993, 9007199254740992, 0.5,
                [9007199254740992.0, 9007199254740992.0, 9007199254740992.0],
            ],
            'ascending PHP_INT_MAX - 1 to PHP_INT_MAX' => [
                \PHP_INT_MAX - 1, \PHP_INT_MAX, 0.5,
                [9.2233720368547758E18, 9.2233720368547758E18, 9.2233720368547758E18],
            ],
            // The int span is exact, so 1 / 0.6 = 1.67 → 2 elements, however coarse the floats are there.
            'ascending PHP_INT_MAX - 1 to PHP_INT_MAX step 0.6' => [
                \PHP_INT_MAX - 1, \PHP_INT_MAX, 0.6,
                [9.2233720368547758E18, 9.2233720368547758E18],
            ],
        ];
    }

    /**
     * @test distinct int operands beyond 2^53 still reject a fractional step that exceeds their exact span
     */
    public function testFractionalStepExceedingExactIntSpanBeyondFloatPrecisionThrows(): void
    {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must not exceed span (1)');

        // When
        foreach (Single::range(9007199254740992, 9007199254740993, 1.5) as $_) {
            break;
        }
    }

    /**
     * @test         an int operand beyond 2^53 with an integral float operand uses the exact span, not the collapsed float span
     * @dataProvider dataProviderForIntAndIntegralFloatBeyondFloatPrecision
     * @param        int|float $start
     * @param        int|float $end
     * @param        float     $step
     * @param        int       $count
     * @param        float     $first
     * @param        float     $last
     */
    public function testFractionalStepOnIntAndIntegralFloatBeyondFloatPrecision(
        int|float $start,
        int|float $end,
        float $step,
        int $count,
        float $first,
        float $last
    ): void {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertCount($count, $result);
        $this->assertSame($first, $result[0]);
        $this->assertSame($last, $result[$count - 1]);
    }

    /**
     * Only the count and endpoints are asserted: the inner values are $start + i·step in float arithmetic,
     * which is not correctly rounded once (float)$start is inexact.
     *
     * Test data generated with:
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / Fraction(step)) + 1
     *   first, last = float(Fraction(start)), float(Fraction(end))
     */
    public static function dataProviderForIntAndIntegralFloatBeyondFloatPrecision(): array
    {
        return [
            'ascending 2^53 + 3 to 2.0^53 + 4' => [
                9007199254740995, 9007199254740996.0, 0.5,
                3, 9007199254740996.0, 9007199254740996.0,
            ],
            'descending 2.0^53 + 4 to 2^53 + 3' => [
                9007199254740996.0, 9007199254740995, 0.5,
                3, 9007199254740996.0, 9007199254740996.0,
            ],
            'ascending 2^53 + 1 to 2.0^53 + 4' => [
                9007199254740993, 9007199254740996.0, 0.5,
                7, 9007199254740992.0, 9007199254740996.0,
            ],
            'descending 2^53 + 1 to 2.0^53, equal as floats' => [
                9007199254740993, 9007199254740992.0, 0.5,
                3, 9007199254740992.0, 9007199254740992.0,
            ],
            'ascending 2.0^53 to 2^53 + 1, equal as floats' => [
                9007199254740992.0, 9007199254740993, 0.5,
                3, 9007199254740992.0, 9007199254740992.0,
            ],
            'ascending 2.0^63 - 1024 to PHP_INT_MAX step 600' => [
                9.2233720368547748E18, \PHP_INT_MAX, 600.0,
                2, 9.2233720368547748E18, 9.2233720368547758E18,
            ],
        ];
    }

    /**
     * @test         an int operand beyond 2^53 with an integral float operand rejects a step that exceeds their exact span
     * @dataProvider dataProviderForStepExceedingExactIntAndIntegralFloatSpan
     * @param        int|float $start
     * @param        int|float $end
     * @param        float     $step
     * @param        string    $span
     */
    public function testFractionalStepExceedingExactIntAndIntegralFloatSpanThrows(
        int|float $start,
        int|float $end,
        float $step,
        string $span
    ): void {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("must not exceed span ({$span})");

        // When
        foreach (Single::range($start, $end, $step) as $_) {
            break;
        }
    }

    /**
     * Test data generated with:
     *   abs(Fraction(end) - Fraction(start)) < Fraction(step), span printed as abs(Fraction(end) - Fraction(start))
     */
    public static function dataProviderForStepExceedingExactIntAndIntegralFloatSpan(): array
    {
        return [
            '2^53 + 1 to 2.0^53 + 2 step 1.5' => [9007199254740993, 9007199254740994.0, 1.5, '1'],
            '2.0^63 - 1024 to PHP_INT_MAX step 1023.5' => [9.2233720368547748E18, \PHP_INT_MAX, 1023.5, '1023'],
        ];
    }

    /**
     * @test         a step that exceeds the exact int span near the int range limits throws, although it doesn't
     *               exceed the span converted to float
     * @dataProvider dataProviderForStepExceedingExactIntSpanAtIntRangeLimits
     * @param        int|float $start
     * @param        int|float $end
     * @param        int|float $step
     * @param        string    $message
     */
    public function testStepExceedingExactIntSpanAtIntRangeLimitsThrows(
        int|float $start,
        int|float $end,
        int|float $step,
        string $message
    ): void {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        // When
        foreach (Single::range($start, $end, $step) as $_) {
            break;
        }
    }

    /**
     * A span that overflows int is named by its operands.
     *
     * Test data generated with (tools/oracles/range_step_exceeds_exact_int_span.py):
     *   Fraction(abs(step)) > abs(Fraction(end) - Fraction(start)), span printed as abs(Fraction(end) - Fraction(start))
     */
    public static function dataProviderForStepExceedingExactIntSpanAtIntRangeLimits(): array
    {
        return [
            '0 to PHP_INT_MAX step (float) PHP_INT_MAX' => [
                0, \PHP_INT_MAX, (float)\PHP_INT_MAX,
                'must not exceed span (9223372036854775807)',
            ],
            'PHP_INT_MAX to 0 step -(float) PHP_INT_MAX' => [
                \PHP_INT_MAX, 0, -(float)\PHP_INT_MAX,
                'must not exceed span (9223372036854775807)',
            ],
            'PHP_INT_MAX to 0 step (float) PHP_INT_MAX' => [
                \PHP_INT_MAX, 0, (float)\PHP_INT_MAX,
                'must not exceed span (9223372036854775807)',
            ],
            '0.0 to PHP_INT_MAX - 1 step PHP_INT_MAX' => [
                0.0, \PHP_INT_MAX - 1, \PHP_INT_MAX,
                'step magnitude (9223372036854775807) must not exceed span (9223372036854775806)',
            ],
            'PHP_INT_MAX - 1 to 0.0 step -PHP_INT_MAX' => [
                \PHP_INT_MAX - 1, 0.0, -\PHP_INT_MAX,
                'step magnitude (9223372036854775807) must not exceed span (9223372036854775806)',
            ],
            '0.0 to 2^62 step 2^62 + 1' => [
                0.0, 4611686018427387904, 4611686018427387905,
                'step magnitude (4611686018427387905) must not exceed span (4611686018427387904)',
            ],
            'PHP_INT_MIN to PHP_INT_MAX step 2.0^64' => [
                \PHP_INT_MIN, \PHP_INT_MAX, 1.8446744073709552E19,
                'must not exceed span between -9223372036854775808 and 9223372036854775807',
            ],
            'PHP_INT_MAX to PHP_INT_MIN step -2.0^64' => [
                \PHP_INT_MAX, \PHP_INT_MIN, -1.8446744073709552E19,
                'must not exceed span between 9223372036854775807 and -9223372036854775808',
            ],
            '-1 to PHP_INT_MAX step 2.0^63 + 2048' => [
                -1, \PHP_INT_MAX, 9.2233720368547779E18,
                'must not exceed span between -1 and 9223372036854775807',
            ],
        ];
    }

    /**
     * @test         a step equal to or just under the exact int span near the int range limits doesn't throw
     * @dataProvider dataProviderForStepWithinExactIntSpanAtIntRangeLimits
     * @param        int|float    $start
     * @param        int|float    $end
     * @param        int|float    $step
     * @param        array<float> $expected
     */
    public function testStepWithinExactIntSpanAtIntRangeLimits(
        int|float $start,
        int|float $end,
        int|float $step,
        array $expected
    ): void {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with (tools/oracles/range_step_exceeds_exact_int_span.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / abs(Fraction(step))) + 1
     *   value_i = float(Fraction(start) ± i * abs(Fraction(step)))
     */
    public static function dataProviderForStepWithinExactIntSpanAtIntRangeLimits(): array
    {
        return [
            'step equal to span: 0.0 to PHP_INT_MAX step PHP_INT_MAX' => [
                0.0, \PHP_INT_MAX, \PHP_INT_MAX,
                [0.0, 9.223372036854776E18],
            ],
            'step just under span: 0.0 to PHP_INT_MAX step PHP_INT_MAX - 1' => [
                0.0, \PHP_INT_MAX, \PHP_INT_MAX - 1,
                [0.0, 9.223372036854776E18],
            ],
            'step equal to overflowing span: -1 to PHP_INT_MAX step 2.0^63' => [
                -1, \PHP_INT_MAX, 9.223372036854776E18,
                [-1.0, 9.223372036854776E18],
            ],
            'step equal to overflowing span: PHP_INT_MAX to -1 step -2.0^63' => [
                \PHP_INT_MAX, -1, -9.223372036854776E18,
                [9.223372036854776E18, -1.0],
            ],
            'step just under overflowing span: -2 to PHP_INT_MAX step 2.0^63' => [
                -2, \PHP_INT_MAX, 9.223372036854776E18,
                [-2.0, 9.223372036854776E18],
            ],
        ];
    }

    /**
     * @test         a whole step on int operands takes its element count from the exact span and each value from the
     *               exact sum, not from the span or the operands converted to float
     * @dataProvider dataProviderForWholeStepOnIntOperandsBeyondFloatPrecision
     * @param        int|float    $start
     * @param        int|float    $end
     * @param        int|float    $step
     * @param        array<float> $expected
     */
    public function testWholeStepOnIntOperandsBeyondFloatPrecision(
        int|float $start,
        int|float $end,
        int|float $step,
        array $expected
    ): void {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with (tools/oracles/range_exact_span_count_and_step_tie.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / abs(Fraction(step))) + 1
     *   value_i = float(Fraction(start) + sign * i * abs(Fraction(step)))
     */
    public static function dataProviderForWholeStepOnIntOperandsBeyondFloatPrecision(): array
    {
        return [
            'PHP_INT_MIN to PHP_INT_MAX step 2.0^63' => [
                \PHP_INT_MIN, \PHP_INT_MAX, 9.223372036854776E18,
                [-9.223372036854776E18, 0.0],
            ],
            'PHP_INT_MAX to PHP_INT_MIN step -2.0^63' => [
                \PHP_INT_MAX, \PHP_INT_MIN, -9.223372036854776E18,
                [9.223372036854776E18, -1.0],
            ],
            '0.0 to PHP_INT_MAX step 2^62' => [
                0.0, \PHP_INT_MAX, 4611686018427387904,
                [0.0, 4.611686018427388E18],
            ],
            'PHP_INT_MAX to 0.0 step -2^62' => [
                \PHP_INT_MAX, 0.0, -4611686018427387904,
                [9.223372036854776E18, 4.611686018427388E18],
            ],
            'PHP_INT_MAX to 0.0 step -(PHP_INT_MAX - 1)' => [
                \PHP_INT_MAX, 0.0, -(\PHP_INT_MAX - 1),
                [9.223372036854776E18, 1.0],
            ],
            'PHP_INT_MIN to PHP_INT_MAX step 2.0^64 - 2048' => [
                \PHP_INT_MIN, \PHP_INT_MAX, 1.844674407370955E19,
                [-9.223372036854776E18, 9.223372036854774E18],
            ],
            'PHP_INT_MAX to PHP_INT_MIN step -(2.0^64 - 2048)' => [
                \PHP_INT_MAX, \PHP_INT_MIN, -1.844674407370955E19,
                [9.223372036854776E18, -9.223372036854774E18],
            ],
            '0.0 to 2^52 + 1 step 2^51' => [
                0.0, 4503599627370497, 2251799813685248,
                [0.0, 2251799813685248.0, 4503599627370496.0],
            ],
            '2^52 + 1 to 0.0 step -2^51' => [
                4503599627370497, 0.0, -2251799813685248,
                [4503599627370497.0, 2251799813685249.0, 1.0],
            ],
            '2^53 + 1 to 2.0^53 + 6 step 2' => [
                9007199254740993, 9007199254740998.0, 2,
                [9007199254740992.0, 9007199254740996.0, 9007199254740996.0],
            ],
            '2.0^53 + 6 to 2^53 + 1 step -2' => [
                9007199254740998.0, 9007199254740993, -2,
                [9007199254740998.0, 9007199254740996.0, 9007199254740994.0],
            ],
        ];
    }

    /**
     * @test a whole step on int operands is limited by the exact element count, not by the span converted to float:
     *       PHP_INT_MAX elements are accepted
     */
    public function testWholeStepOnIntOperandsAcceptsPhpIntMaxElements(): void
    {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Single::range(0.0, \PHP_INT_MAX - 1, 1) as $value) {
            $result[] = $value;
            if (\count($result) === 3) {
                break;
            }
        }

        // Then (oracle: tools/oracles/range_exact_span_count_and_step_tie.py, count 9223372036854775807)
        $this->assertSame([0.0, 1.0, 2.0], $result);
    }

    /**
     * @test a whole step on int operands that would give more than PHP_INT_MAX elements is rejected
     */
    public function testWholeStepOnIntOperandsRejectsMoreThanPhpIntMaxElements(): void
    {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('exceeds representable iteration count');

        // When
        foreach (Single::range(\PHP_INT_MIN, 0.0, 1) as $_) {
            break;
        }
    }

    /**
     * @test         a fractional step on int operands whose start a float can't hold gives each value from the exact
     *               start, not from the start converted to float
     * @dataProvider dataProviderForFractionalStepOnIntStartBeyondFloatPrecision
     * @param        int          $start
     * @param        int          $end
     * @param        float        $step
     * @param        array<float> $expected
     */
    public function testFractionalStepOnIntStartBeyondFloatPrecision(
        int $start,
        int $end,
        float $step,
        array $expected
    ): void {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with (tools/oracles/range_exact_span_count_and_step_tie.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / abs(Fraction(step))) + 1
     *   value_i = float(Fraction(start) + sign * i * abs(Fraction(step)))
     */
    public static function dataProviderForFractionalStepOnIntStartBeyondFloatPrecision(): array
    {
        return [
            '2^53 + 1 to 2^53 + 3 step 0.5' => [
                9007199254740993, 9007199254740995, 0.5,
                [9007199254740992.0, 9007199254740994.0, 9007199254740994.0, 9007199254740994.0, 9007199254740996.0],
            ],
            '2^53 + 3 to 2^53 + 1 step -0.5' => [
                9007199254740995, 9007199254740993, -0.5,
                [9007199254740996.0, 9007199254740994.0, 9007199254740994.0, 9007199254740994.0, 9007199254740992.0],
            ],
            '2^53 + 1 to 2^53 + 2 step 0.3' => [
                9007199254740993, 9007199254740994, 0.3,
                [9007199254740992.0, 9007199254740994.0, 9007199254740994.0, 9007199254740994.0],
            ],
            '2^53 + 3 to 0 step -(2^52 - 0.5)' => [
                9007199254740995, 0, -4503599627370495.5,
                [9007199254740996.0, 4503599627370500.0, 4.0],
            ],
            '-(2^53 + 3) to 0 step 2^52 - 0.5' => [
                -9007199254740995, 0, 4503599627370495.5,
                [-9007199254740996.0, -4503599627370500.0, -4.0],
            ],
            '2^53 + 1 to 2^53 - 2 step -0.3' => [
                9007199254740993, 9007199254740990, -0.3,
                [
                    9007199254740992.0, 9007199254740992.0, 9007199254740992.0, 9007199254740992.0,
                    9007199254740992.0, 9007199254740992.0, 9007199254740991.0, 9007199254740991.0,
                    9007199254740991.0, 9007199254740990.0, 9007199254740990.0,
                ],
            ],
            '2^63 - 512 to 2^63 - 514 step -0.5' => [
                9223372036854775296, 9223372036854775294, -0.5,
                [
                    9.223372036854776E18, 9.223372036854775E18, 9.223372036854775E18, 9.223372036854775E18,
                    9.223372036854775E18,
                ],
            ],
            'PHP_INT_MIN + 512 to PHP_INT_MIN + 514 step 0.5' => [
                -9223372036854775296, -9223372036854775294, 0.5,
                [
                    -9.223372036854776E18, -9.223372036854775E18, -9.223372036854775E18, -9.223372036854775E18,
                    -9.223372036854775E18,
                ],
            ],
            '2^53 + 1 to 2^53 - 5 step -0.3888888888888889' => [
                9007199254740993, 9007199254740987, -0.3888888888888889,
                [
                    9007199254740992.0, 9007199254740992.0, 9007199254740992.0, 9007199254740992.0,
                    9007199254740991.0, 9007199254740991.0, 9007199254740991.0, 9007199254740990.0,
                    9007199254740990.0, 9007199254740989.0, 9007199254740989.0, 9007199254740989.0,
                    9007199254740988.0, 9007199254740988.0, 9007199254740988.0, 9007199254740987.0,
                ],
            ],
            '-(2^53 + 1) to -(2^53 - 5) step 0.3888888888888889' => [
                -9007199254740993, -9007199254740987, 0.3888888888888889,
                [
                    -9007199254740992.0, -9007199254740992.0, -9007199254740992.0, -9007199254740992.0,
                    -9007199254740991.0, -9007199254740991.0, -9007199254740991.0, -9007199254740990.0,
                    -9007199254740990.0, -9007199254740989.0, -9007199254740989.0, -9007199254740989.0,
                    -9007199254740988.0, -9007199254740988.0, -9007199254740988.0, -9007199254740987.0,
                ],
            ],
        ];
    }

    /**
     * @test         a fractional step on int operands whose start a float can't hold, over a span beyond the int range,
     *               gives each value from the exact start
     * @dataProvider dataProviderForFractionalStepOnIntStartBeyondFloatPrecisionLongRange
     * @param        int               $start
     * @param        int               $end
     * @param        float             $step
     * @param        int               $count
     * @param        array<int, float> $expected values at chosen indices
     */
    public function testFractionalStepOnIntStartBeyondFloatPrecisionLongRange(
        int $start,
        int $end,
        float $step,
        int $count,
        array $expected
    ): void {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertCount($count, $result);
        $this->assertSame($expected, \array_intersect_key($result, $expected));
    }

    /**
     * Test data generated with (tools/oracles/range_exact_span_count_and_step_tie.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / abs(Fraction(step))) + 1
     *   value_i = float(Fraction(start) + sign * i * abs(Fraction(step)))
     */
    public static function dataProviderForFractionalStepOnIntStartBeyondFloatPrecisionLongRange(): array
    {
        return [
            'PHP_INT_MIN + 512 to PHP_INT_MAX step 2^52 - 0.5' => [
                -9223372036854775296, \PHP_INT_MAX, 4503599627370495.5, 4097,
                [
                    0 => -9.223372036854776E18,
                    1 => -9.218868437227405E18,
                    2047 => -4503599627371008.0,
                    2048 => -512.0,
                    2049 => 4503599627369983.5,
                    3000 => 4.287426845256711E18,
                    4095 => 9.218868437227404E18,
                    4096 => 9.223372036854774E18,
                ],
            ],
            'PHP_INT_MAX - 511 to PHP_INT_MIN step -(2^52 - 0.5)' => [
                9223372036854775296, \PHP_INT_MIN, -4503599627370495.5, 4097,
                [
                    0 => 9.223372036854776E18,
                    1 => 9.218868437227405E18,
                    2047 => 4503599627371008.0,
                    2048 => 512.0,
                    2049 => -4503599627369983.5,
                    3000 => -4.287426845256711E18,
                    4095 => -9.218868437227404E18,
                    4096 => -9.223372036854774E18,
                ],
            ],
        ];
    }

    /**
     * @test         a step equal to the float span of float operands throws when the exact span is smaller
     * @dataProvider dataProviderForStepEqualToFloatSpanExceedingExactSpan
     * @param        float $start
     * @param        float $end
     * @param        float $step
     */
    public function testStepEqualToFloatSpanExceedingExactSpanThrows(float $start, float $end, float $step): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must not exceed span');

        // When
        foreach (Single::range($start, $end, $step) as $_) {
            break;
        }
    }

    /**
     * Test data generated with (tools/oracles/range_exact_span_count_and_step_tie.py):
     *   Fraction(abs(step)) > abs(Fraction(end) - Fraction(start)), where abs(end - start) == abs(step) in float
     */
    public static function dataProviderForStepEqualToFloatSpanExceedingExactSpan(): array
    {
        return [
            '0.1 to 2.0^53 step 2.0^53' => [0.1, 9007199254740992.0, 9007199254740992.0],
            '2.0^53 to 0.1 step -2.0^53' => [9007199254740992.0, 0.1, -9007199254740992.0],
            '-2.0^53 to -0.1 step 2.0^53' => [-9007199254740992.0, -0.1, 9007199254740992.0],
            '2.5 to 2.0^53 + 2 step 2.0^53' => [2.5, 9007199254740994.0, 9007199254740992.0],
            '0.3 to 1e16 step 1e16' => [0.3, 1.0E16, 1.0E16],
            '0.1 to 2.0^63 step 2.0^63' => [0.1, 9.223372036854776E18, 9.223372036854776E18],
            '2.0^63 to 0.1 step -2.0^63' => [9.223372036854776E18, 0.1, -9.223372036854776E18],
            '-2.0^64 to -0.5 step 2.0^64' => [-1.8446744073709552E19, -0.5, 1.8446744073709552E19],
        ];
    }

    /**
     * @test         an int step equal to the float span of float operands throws when the exact span is smaller
     * @dataProvider dataProviderForIntStepEqualToFloatSpanExceedingExactSpan
     * @param        float $start
     * @param        float $end
     * @param        int   $step
     */
    public function testIntStepEqualToFloatSpanExceedingExactSpanThrows(float $start, float $end, int $step): void
    {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must not exceed span');

        // When
        foreach (Single::range($start, $end, $step) as $_) {
            break;
        }
    }

    /**
     * Test data generated with (tools/oracles/range_exact_span_count_and_step_tie.py):
     *   Fraction(abs(step)) > abs(Fraction(end) - Fraction(start)), where abs(end - start) == float(abs(step))
     */
    public static function dataProviderForIntStepEqualToFloatSpanExceedingExactSpan(): array
    {
        return [
            '2.0 to 2.0^60 step 2^60 - 1' => [2.0, 1.152921504606847E18, 1152921504606846975],
            '1.0 to 2.0^60 step 2^60' => [1.0, 1.152921504606847E18, 1152921504606846976],
        ];
    }

    /**
     * @test         a step equal to the float span of float operands doesn't throw when it is within the exact span
     * @dataProvider dataProviderForStepEqualToFloatSpanWithinExactSpan
     * @param        float        $start
     * @param        float        $end
     * @param        float        $step
     * @param        array<float> $expected
     */
    public function testStepEqualToFloatSpanWithinExactSpan(float $start, float $end, float $step, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with (tools/oracles/range_exact_span_count_and_step_tie.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / abs(Fraction(step))) + 1
     *   value_i = float(Fraction(start) + sign * i * abs(Fraction(step)))
     */
    public static function dataProviderForStepEqualToFloatSpanWithinExactSpan(): array
    {
        return [
            '-0.1 to 2.0^53 step 2.0^53' => [-0.1, 9007199254740992.0, 9007199254740992.0, [-0.1, 9007199254740992.0]],
            '-0.1 to 2.0^63 step 2.0^63' => [
                -0.1, 9.223372036854776E18, 9.223372036854776E18, [-0.1, 9.223372036854776E18],
            ],
        ];
    }

    /**
     * @test an int step equal to the float span of float operands doesn't throw when it equals the exact span
     */
    public function testIntStepEqualToFloatSpanAndExactSpan(): void
    {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = \iterator_to_array(Single::range(1.0, 1.152921504606847E18, 1152921504606846975), false);

        // Then (oracle: tools/oracles/range_exact_span_count_and_step_tie.py, '1.0 to 2.0^60 step 2^60 - 1')
        $this->assertSame([1.0, 1.152921504606847E18], $result);
    }

    /**
     * @test         a decimal step equal to the decimal span is accepted, although the exact span of the floats the
     *               operands round to is slightly smaller than the step
     * @dataProvider dataProviderForStepEqualToDecimalSpan
     * @param        float        $start
     * @param        float        $end
     * @param        float        $step
     * @param        array<float> $expected
     */
    public function testStepEqualToDecimalSpanIsValid(float $start, float $end, float $step, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with (tools/oracles/range_exact_span_count_and_step_tie.py, decimal literals as written):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / abs(Fraction(step))) + 1
     *   value_i = float(Fraction(start) + sign * i * abs(Fraction(step)))
     */
    public static function dataProviderForStepEqualToDecimalSpan(): array
    {
        return [
            '-10.0 to -3.6 step 6.4' => [-10.0, -3.6, 6.4, [-10.0, -3.6]],
            '0.1 to 0.7 step 0.6' => [0.1, 0.7, 0.6, [0.1, 0.7]],
            '0.7 to 0.1 step -0.6' => [0.7, 0.1, -0.6, [0.7, 0.1]],
        ];
    }

    /**
     * @test         float operands land on $end only when the exact span is a whole number of steps within the
     *               operands' rounding, so a span past 2^53 that rounds to whole steps doesn't
     * @dataProvider dataProviderForLandingOnEndOfExactDoubles
     * @param        float        $start
     * @param        float        $end
     * @param        float        $step
     * @param        array<float> $expected
     */
    public function testFloatOperandsLandOnEndOnlyWithinOperandRounding(
        float $start,
        float $end,
        float $step,
        array $expected
    ): void {
        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Both models agree on every row: the floats passed, and the decimals they are written as.
     *
     * Test data generated with (tools/oracles/range_landing_within_operand_rounding.py, doubles):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / abs(Fraction(step))) + 1
     *   value_i = float(Fraction(start) + sign * i * abs(Fraction(step)))
     */
    public static function dataProviderForLandingOnEndOfExactDoubles(): array
    {
        return [
            '2.0^53 to -0.1 step -2.0^53' => [
                9007199254740992.0, -0.1, -9007199254740992.0, [9007199254740992.0, 0.0],
            ],
            '-2.0^53 to 0.1 step 2.0^53' => [
                -9007199254740992.0, 0.1, 9007199254740992.0, [-9007199254740992.0, 0.0],
            ],
            '2.0^54 to 0.3 step -2.0^53' => [
                1.8014398509481984E16, 0.3, -9007199254740992.0, [1.8014398509481984E16, 9007199254740992.0],
            ],
            '2.0^53 to -0.1 step -2.0^52' => [
                9007199254740992.0, -0.1, -4503599627370496.0, [9007199254740992.0, 4503599627370496.0, 0.0],
            ],
            '2.0^53 to 0.5 step -2.0^52' => [
                9007199254740992.0, 0.5, -4503599627370496.0, [9007199254740992.0, 4503599627370496.0],
            ],
            '1e16 to -0.5 step -1e16' => [1.0E16, -0.5, -1.0E16, [1.0E16, 0.0]],
            // Neighbours that don't change
            '2.0^53 to 0.0 step -2.0^53' => [9007199254740992.0, 0.0, -9007199254740992.0, [9007199254740992.0, 0.0]],
            '-0.1 to 2.0^53 step 2.0^53' => [-0.1, 9007199254740992.0, 9007199254740992.0, [-0.1, 9007199254740992.0]],
            '-0.5 to 1e20 step 2e19' => [-0.5, 1.0E20, 2.0E19, [-0.5, 2.0E19, 4.0E19, 6.0E19, 8.0E19, 1.0E20]],
        ];
    }

    /**
     * @test         a step above the float span of decimal operands is accepted while the shortfall is within the
     *               operands' rounding, and the range then lands on $end
     * @dataProvider dataProviderForLandingOnEndOfDecimals
     * @param        float        $start
     * @param        float        $end
     * @param        float        $step
     * @param        array<float> $expected
     */
    public function testDecimalOperandsLandOnEndWithinOperandRounding(
        float $start,
        float $end,
        float $step,
        array $expected
    ): void {
        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * The floats passed don't land on $end in any row (and those of the first four throw), but the decimals they are
     * written as do.
     *
     * Test data generated with (tools/oracles/range_landing_within_operand_rounding.py, decimal literals as written):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / abs(Fraction(step))) + 1
     *   value_i = float(Fraction(start) + sign * i * abs(Fraction(step)))
     */
    public static function dataProviderForLandingOnEndOfDecimals(): array
    {
        return [
            '0.1 to 0.3 step 0.2' => [0.1, 0.3, 0.2, [0.1, 0.3]],
            '0.3 to 0.1 step -0.2' => [0.3, 0.1, -0.2, [0.3, 0.1]],
            '-0.3 to -0.1 step 0.2' => [-0.3, -0.1, 0.2, [-0.3, -0.1]],
            '1.1 to 1.3 step 0.2' => [1.1, 1.3, 0.2, [1.1, 1.3]],
            // Neighbours that don't change
            '0.1 to 0.3 step 0.1' => [0.1, 0.3, 0.1, [0.1, 0.2, 0.3]],
            '0.7 to 0.8 step 0.1' => [0.7, 0.8, 0.1, [0.7, 0.8]],
            '0.0 to 1e23 step 1e22' => [
                0.0, 1.0E23, 1.0E22,
                [0.0, 1.0E22, 2.0E22, 3.0E22, 4.0E22, 5.0E22, 6.0E22, 7.0E22, 8.0E22, 9.0E22, 1.0E23],
            ],
        ];
    }

    /**
     * @test         a step above the span by more than the operands' rounding throws
     * @dataProvider dataProviderForStepAboveSpanBeyondOperandRounding
     * @param        float $start
     * @param        float $end
     * @param        float $step
     */
    public function testStepAboveSpanBeyondOperandRoundingThrows(float $start, float $end, float $step): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must not exceed span');

        // When
        foreach (Single::range($start, $end, $step) as $_) {
            break;
        }
    }

    /**
     * Test data generated with (tools/oracles/range_landing_within_operand_rounding.py, doubles; the decimal model
     * agrees):
     *   abs(Fraction(step)) > abs(Fraction(end) - Fraction(start))
     */
    public static function dataProviderForStepAboveSpanBeyondOperandRounding(): array
    {
        return [
            '0.1 to 0.3 step 0.20000000000000004' => [0.1, 0.3, 0.20000000000000004],
            '0.0 to 1.0 step 1.0000000000000002' => [0.0, 1.0, 1.0000000000000002],
            // Within half a float spacing of the operands, but beyond half a unit in the last digit of the shortest
            // decimals they are written as
            '1.7235207201277944 to 739.4286550878337 step 737.705134367706' => [
                1.7235207201277944, 739.4286550878337, 737.705134367706,
            ],
            '-387.62386526512876 to -356.9597689773017 step 30.664096287827093' => [
                -387.62386526512876, -356.9597689773017, 30.664096287827093,
            ],
            // The operands' half float spacings would cover the shortfall, but their shortest decimals fall short as
            // the floats do: 0.10000000000000002 > 0.2 - 0.1
            '0.1 to 0.2 step 0.10000000000000002' => [0.1, 0.2, 0.10000000000000002],
            '0.2 to 0.1 step -0.10000000000000002' => [0.2, 0.1, -0.10000000000000002],
            '1.1 to 1.3 step 0.20000000000000004' => [1.1, 1.3, 0.20000000000000004],
        ];
    }

    /**
     * @test         a step computed in float as ($end - $start) / $n still ends on $end after $n steps
     * @dataProvider dataProviderForLinspaceStep
     * @param        float $start
     * @param        float $end
     * @param        int   $n
     */
    public function testLinspaceStepEndsOnEnd(float $start, float $end, int $n): void
    {
        // Given
        $step = ($end - $start) / $n;

        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertCount($n + 1, $result);
        $this->assertSame($start, $result[0]);
        $this->assertSame($end, $result[$n]);
    }

    /**
     * The step carries the rounding of the float subtraction, which neither the floats' exact span nor their shortest
     * decimals make a whole number of steps.
     *
     * Test data generated with (tools/oracles/range_landing_within_operand_rounding.py):
     *   count, first, last = len(ls), ls[0], ls[-1] where ls = numpy.linspace(start, end, n + 1)
     */
    public static function dataProviderForLinspaceStep(): array
    {
        return [
            '-104.81949906807756 to 975.2888809333513, n 932' => [-104.81949906807756, 975.2888809333513, 932],
            '-499.457509919089 to 44.25084769001114, n 40' => [-499.457509919089, 44.25084769001114, 40],
        ];
    }

    /**
     * @test         a range whose exact last value rounds to $end yields $end
     * @dataProvider dataProviderForExactLastValueRoundingToEnd
     * @param        float             $start
     * @param        float             $end
     * @param        float             $step
     * @param        int               $count
     * @param        array<int, float> $expected values at chosen indices
     */
    public function testExactLastValueRoundingToEndYieldsEnd(
        float $start,
        float $end,
        float $step,
        int $count,
        array $expected
    ): void {
        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertCount($count, $result);
        $this->assertSame($expected, \array_intersect_key($result, $expected));
    }

    /**
     * Both models agree on every row.
     *
     * Test data generated with (tools/oracles/range_landing_within_operand_rounding.py, doubles):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / abs(Fraction(step))) + 1
     *   value_i = float(Fraction(start) + sign * i * abs(Fraction(step)))
     */
    public static function dataProviderForExactLastValueRoundingToEnd(): array
    {
        return [
            '-9.47 to 1e17 step 1.4285714285714286e16' => [-9.47, 1.0E17, 1.4285714285714286E16, 8, [0 => -9.47, 7 => 1.0E17]],
            '9.71 to -1e17 step -1.4285714285714286e16' => [
                9.71, -1.0E17, -1.4285714285714286E16, 8, [0 => 9.71, 7 => -1.0E17],
            ],
        ];
    }

    /**
     * @test         a fractional step on int operands lands on $end only within the step's rounding
     * @dataProvider dataProviderForFractionalStepOnIntOperandsLanding
     * @param        int|float         $start
     * @param        int|float         $end
     * @param        float             $step
     * @param        int               $count
     * @param        array<int, float> $expected values at chosen indices
     */
    public function testFractionalStepOnIntOperandsLandsOnEndOnlyWithinStepRounding(
        int|float $start,
        int|float $end,
        float $step,
        int $count,
        array $expected
    ): void {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertCount($count, $result);
        $this->assertSame($expected, \array_intersect_key($result, $expected));
    }

    /**
     * Both models agree on every row.
     *
     * Test data generated with (tools/oracles/range_landing_within_operand_rounding.py, doubles):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / abs(Fraction(step))) + 1
     *   value_i = float(Fraction(start) + sign * i * abs(Fraction(step)))
     */
    public static function dataProviderForFractionalStepOnIntOperandsLanding(): array
    {
        return [
            'PHP_INT_MIN to 0 step 2^52 - 0.5' => [
                \PHP_INT_MIN, 0, 4503599627370495.5, 2049,
                [0 => -9.223372036854776E18, 2048 => -1024.0],
            ],
            '0 to 2^62 step 2^52 - 0.5' => [
                0, 4611686018427387904, 4503599627370495.5, 1025,
                [0 => 0.0, 1 => 4503599627370495.5, 1024 => 4.6116860184273874E18],
            ],
            // The exact last value rounds to $end
            '402792930821199 to 9223372036854774271 step 4346356853875566.5' => [
                402792930821199, 9223372036854774271, 4346356853875566.5, 2123,
                [0 => 402792930821199.0, 2122 => 9.223372036854774E18],
            ],
        ];
    }

    /**
     * @test an int operand beyond 2^53 and an integral float operand that are equal as floats still reject a step
     *       against their direction
     */
    public function testStepDirectionConflictOnIntAndIntegralFloatEqualAsFloatsThrows(): void
    {
        // Given (on 32-bit PHP these literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('step direction');

        // When
        foreach (Single::range(9007199254740992.0, 9007199254740993, -0.5) as $_) {
            break;
        }
    }

    /**
     * @test integer range at PHP_INT_MAX endpoint terminates (regression: accumulator overflow to float)
     */
    public function testIntegerRangeAtPhpIntMaxEndpointTerminates(): void
    {
        // Given
        // Accumulator-based iteration overflows on $v += 1 once $v reaches PHP_INT_MAX,
        // promoting $v to float at PHP_INT_MAX's float representation. Subsequent
        // $v <= PHP_INT_MAX comparisons stay true (float equality), so the loop hangs.
        $start = \PHP_INT_MAX - 2;
        $end   = \PHP_INT_MAX;
        $step  = 1;
        $expectedCount = 3;
        $cap = $expectedCount * 5;

        // When
        $count = 0;
        $last = null;
        foreach (Single::range($start, $end, $step) as $v) {
            $count++;
            $last = $v;
            if ($count > $cap) {
                break;
            }
        }

        // Then
        $this->assertSame($expectedCount, $count);
        $this->assertSame(\PHP_INT_MAX, $last);
    }

    /**
     * @test integer range descending into PHP_INT_MIN terminates (regression)
     */
    public function testIntegerRangeAtPhpIntMinEndpointTerminates(): void
    {
        // Given
        $start = \PHP_INT_MIN + 2;
        $end   = \PHP_INT_MIN;
        $step  = 1;
        $expectedCount = 3;
        $cap = $expectedCount * 5;

        // When
        $count = 0;
        $last = null;
        foreach (Single::range($start, $end, $step) as $v) {
            $count++;
            $last = $v;
            if ($count > $cap) {
                break;
            }
        }

        // Then
        $this->assertSame($expectedCount, $count);
        $this->assertSame(\PHP_INT_MIN, $last);
    }

    /**
     * @test integer range whose span exceeds PHP_INT_MAX iterates lazily (regression: previously rejected)
     */
    public function testIntegerRangeWithSpanExceedingPhpIntMaxIteratesLazily(): void
    {
        // Given
        // PHP_INT_MAX - PHP_INT_MIN = 2^64 - 1, which overflows int subtraction.
        // The implementation must iterate lazily with overflow-safe step advances
        // — never materializing the count up-front — so callers can take(N) finitely.

        // When
        $first5 = [];
        foreach (Single::range(\PHP_INT_MIN, \PHP_INT_MAX, 1) as $v) {
            $first5[] = $v;
            if (\count($first5) === 5) {
                break;
            }
        }

        // Then
        $this->assertSame(
            [\PHP_INT_MIN, \PHP_INT_MIN + 1, \PHP_INT_MIN + 2, \PHP_INT_MIN + 3, \PHP_INT_MIN + 4],
            $first5
        );
    }

    /**
     * @test opposite-sign integer span near PHP_INT_MAX iterates lazily
     */
    public function testIntegerRangeOppositeSignSpanAtBoundaryIteratesLazily(): void
    {
        // Given
        // The span (-2 → PHP_INT_MAX-1) overflows int subtraction. Lazy iteration
        // must start cleanly from $start and not precompute the count.

        // When
        $first5 = [];
        foreach (Single::range(-2, \PHP_INT_MAX - 1, 1) as $v) {
            $first5[] = $v;
            if (\count($first5) === 5) {
                break;
            }
        }

        // Then
        $this->assertSame([-2, -1, 0, 1, 2], $first5);
    }

    /**
     * @test float range whose span overflows to INF is rejected cleanly
     */
    public function testFloatRangeWithInfiniteSpanRejects(): void
    {
        // Given
        // (PHP_FLOAT_MAX - (-PHP_FLOAT_MAX)) overflows to INF; the resulting quotient
        // (INF / 1.0) is also INF and cannot be cast to int. Reject with a clear
        // InvalidArgumentException instead of emitting a PHP deprecation/warning
        // and yielding only the start value.

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        \iterator_to_array(Single::range(-\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 1.0), false);
    }

    /**
     * @test float range with finite-but-too-large quotient is rejected cleanly
     */
    public function testFloatRangeWithFiniteButTooLargeQuotientRejects(): void
    {
        // Given
        // span / step = 1.0e20, which is finite but exceeds PHP_INT_MAX (~9.22e18),
        // so a naive (int) cast on the quotient triggers a PHP 8.5 deprecation
        // and produces a wrong bound. Reject explicitly.

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (Single::range(0.0, 1.0e20, 1.0) as $_) {
            // unreachable
        }
    }

    /**
     * @test PHP_INT_MIN as step is rejected (its absolute value overflows int)
     */
    public function testIntegerStepEqualToPhpIntMinRejects(): void
    {
        // Given
        // |PHP_INT_MIN| = PHP_INT_MAX + 1 overflows int, so \abs(PHP_INT_MIN) returns
        // a float. Downstream \intdiv() requires int operands under strict types and
        // would TypeError before any iteration. Native PHP \range() also rejects this.

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (Single::range(\PHP_INT_MAX, 0, \PHP_INT_MIN) as $_) {
            // unreachable
        }
    }

    /**
     * @test PHP_INT_MIN as step also rejected when direction is ascending (sign conflict raised first)
     */
    public function testIntegerStepEqualToPhpIntMinAscendingRejects(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (Single::range(0, \PHP_INT_MAX, \PHP_INT_MIN) as $_) {
            // unreachable
        }
    }

    /**
     * @test         large but bounded integer ranges with non-unit step terminate at the endpoint
     * @dataProvider dataProviderForLargeIntRanges
     * @param        int   $start
     * @param        int   $end
     * @param        int   $step
     * @param        int   $expectedCount
     * @param        int   $expectedLast
     */
    public function testLargeIntegerRangeAtBoundary(int $start, int $end, int $step, int $expectedCount, int $expectedLast): void
    {
        // Given
        $cap = $expectedCount * 5;

        // When
        $count = 0;
        $last = null;
        foreach (Single::range($start, $end, $step) as $v) {
            $count++;
            $last = $v;
            if ($count > $cap) {
                break;
            }
        }

        // Then
        $this->assertSame($expectedCount, $count);
        $this->assertSame($expectedLast, $last);
    }

    public static function dataProviderForLargeIntRanges(): array
    {
        return [
            'ascending PHP_INT_MAX non-unit step lands on endpoint' => [
                \PHP_INT_MAX - 6, \PHP_INT_MAX, 3, 3, \PHP_INT_MAX,
            ],
            'ascending PHP_INT_MAX non-unit step stops short' => [
                \PHP_INT_MAX - 7, \PHP_INT_MAX, 3, 3, \PHP_INT_MAX - 1,
            ],
            'descending PHP_INT_MIN non-unit step lands on endpoint' => [
                \PHP_INT_MIN + 6, \PHP_INT_MIN, 3, 3, \PHP_INT_MIN,
            ],
            'step = PHP_INT_MAX with zero start' => [
                0, \PHP_INT_MAX, \PHP_INT_MAX, 2, \PHP_INT_MAX,
            ],
            'step = PHP_INT_MAX with PHP_INT_MIN+1 start, descending' => [
                \PHP_INT_MAX, 0, \PHP_INT_MAX, 2, 0,
            ],
        ];
    }

    /**
     * @test float range with quotient just under (float)PHP_INT_MAX boundary is accepted
     */
    public function testFloatRangeWithQuotientJustUnderBoundaryAccepted(): void
    {
        // Given
        // Use a span well under (float)PHP_INT_MAX but with magnitude exercising the
        // 2^53 mantissa precision boundary. The first 5 values should be accurate;
        // beyond that, float precision collapse makes increments meaningless — but
        // iteration must terminate.
        $start = 0.0;
        $end   = 100.0;
        $step  = 1.0;
        $expectedCount = 101;

        // When
        $count = 0;
        foreach (Single::range($start, $end, $step) as $_) {
            $count++;
        }

        // Then
        $this->assertSame($expectedCount, $count);
    }

    /**
     * @test float range with PHP_FLOAT_MAX magnitudes terminates if step covers the span
     */
    public function testFloatRangeWithPhpFloatMaxMagnitudesTerminates(): void
    {
        // Given
        // span = PHP_FLOAT_MAX (from 0 to PHP_FLOAT_MAX), step = PHP_FLOAT_MAX.
        // Quotient = 1.0, well under the int-boundary guard. Should yield two values.
        $result = [];
        foreach (Single::range(0.0, \PHP_FLOAT_MAX, \PHP_FLOAT_MAX) as $v) {
            $result[] = $v;
            if (\count($result) > 5) {
                break;
            }
        }

        // Then
        $this->assertCount(2, $result);
        $this->assertSame(0.0, $result[0]);
        $this->assertSame(\PHP_FLOAT_MAX, $result[1]);
    }

    /**
     * @test float range with denormal-sized step matching span terminates
     */
    public function testFloatRangeWithEpsilonStepTerminates(): void
    {
        // Given
        // Use a tiny but finite step at a magnitude where it's representable.
        // span = 5 * PHP_FLOAT_EPSILON, step = PHP_FLOAT_EPSILON. Quotient = 5.
        $start = 0.0;
        $end   = 5 * \PHP_FLOAT_EPSILON;
        $step  = \PHP_FLOAT_EPSILON;
        $expectedCount = 6;
        $cap = $expectedCount * 5;

        // When
        $count = 0;
        foreach (Single::range($start, $end, $step) as $_) {
            $count++;
            if ($count > $cap) {
                break;
            }
        }

        // Then
        $this->assertSame($expectedCount, $count);
    }

    /**
     * @test ascending range crossing zero terminates with mixed-sign operands
     */
    public function testIntegerRangeCrossingZeroTerminates(): void
    {
        // Given/When
        $result = \iterator_to_array(Single::range(-3, 3, 2), false);

        // Then
        $this->assertSame([-3, -1, 1, 3], $result);
    }

    /**
     * @test negative zero start is treated as zero
     */
    public function testNegativeZeroFloatStartTreatedAsZero(): void
    {
        // Given
        $result = \iterator_to_array(Single::range(-0.0, 2.0, 1.0), false);

        // Then
        $this->assertCount(3, $result);
        $this->assertSame(0.0, $result[0] + 0.0);
        $this->assertSame(1.0, $result[1]);
        $this->assertSame(2.0, $result[2]);
    }

    /**
     * @test float range whose quotient exactly equals (float)PHP_INT_MAX is rejected
     */
    public function testFloatRangeWithQuotientAtPhpIntMaxBoundaryRejects(): void
    {
        // Given
        // (float)PHP_INT_MAX rounds to 2^63 (one past PHP_INT_MAX). A `> (float)PHP_INT_MAX`
        // check lets this slip through, but casting the quotient to int falls into
        // the implementation-defined "non-representable float" region — PHP 8.5 emits
        // a deprecation and yields a bad bound. Use `>=` to block the boundary too.

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (Single::range(0.0, (float)\PHP_INT_MAX, 1.0) as $_) {
            // unreachable
        }
    }

    /**
     * @test start == end with one float operand yields a float (matches native range)
     */
    public function testEqualStartEndWithFloatOperandYieldsFloat(): void
    {
        // Given/When
        $result = \iterator_to_array(Single::range(1, 1.0), false);

        // Then
        $this->assertSame([1.0], $result);

        // And reversed
        $result2 = \iterator_to_array(Single::range(1.0, 1), false);
        $this->assertSame([1.0], $result2);
    }

    /**
     * @test         float range ends on $end where rounding puts the last value past it and native \range() drops it
     * @dataProvider dataProviderForEndpointNativeDrops
     * @param        float        $start
     * @param        float        $end
     * @param        float        $step
     * @param        array<float> $expected
     */
    public function testFloatRangeEndsOnEndWhereNativeDropsIt(float $start, float $end, float $step, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * 0.3 + 3 * 0.2 = 0.9000000000000001 and 0.9 - 3 * 0.2 = 0.29999999999999993. Native \range() returns
     * [0.3, 0.5, 0.7] and [0.9, 0.7, 0.5].
     *
     * Test data generated with (tools/oracles/range_float_steps.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / Fraction(step)) + 1
     *   value_i = float(Fraction(start) ± i * Fraction(step))
     */
    public static function dataProviderForEndpointNativeDrops(): array
    {
        return [
            'ascending 0.3 to 0.9 step 0.2' => [0.3, 0.9, 0.2, [0.3, 0.5, 0.7, 0.9]],
            'descending 0.9 to 0.3 step 0.2' => [0.9, 0.3, 0.2, [0.9, 0.7, 0.5, 0.3]],
        ];
    }

    /**
     * @test float range does not yield values past $end (ascending)
     */
    public function testFloatRangeAscendingDoesNotOvershoot(): void
    {
        // Given
        // 0.3 + 3 * 0.2 = 0.9000000000000001 in IEEE 754 — strictly greater than $end (0.9).
        // Our implementation must not yield values past $end due to float rounding.

        // When
        $result = \iterator_to_array(Single::range(0.3, 0.9, 0.2), false);

        // Then
        foreach ($result as $v) {
            $this->assertLessThanOrEqual(0.9, $v, "value {$v} exceeds end (0.9)");
        }
    }

    /**
     * @test float range does not yield values past $end (descending)
     */
    public function testFloatRangeDescendingDoesNotUndershoot(): void
    {
        // Given
        // 0.9 - 3 * 0.2 = 0.29999999999999993 — strictly less than $end (0.3).

        // When
        $result = \iterator_to_array(Single::range(0.9, 0.3, 0.2), false);

        // Then
        foreach ($result as $v) {
            $this->assertGreaterThanOrEqual(0.3, $v, "value {$v} is below end (0.3)");
        }
    }

    /**
     * @test integer-valued float step on int operands keeps int output (matches PHP 8.3+ native range)
     */
    public function testIntegerValuedFloatStepKeepsIntOutput(): void
    {
        // Given
        // PHP 8.3+ native range(1, 5, 1.0) → [int(1), int(2), int(3), int(4), int(5)]; PHP 8.2
        // sends any float step down the float path and yields floats. Single::range pins the
        // 8.3+ types on every supported version, hence the literal expectation below.
        // Only a step with a non-integer fractional part (e.g. 1.5) promotes ints to floats.

        // When
        $result = \iterator_to_array(Single::range(1, 5, 1.0), false);

        // Then
        $this->assertSame([1, 2, 3, 4, 5], $result);
    }

    /**
     * @test single-element int range with integer-valued float step keeps int output
     */
    public function testSingleElementIntegerValuedFloatStepKeepsInt(): void
    {
        // When
        $result = \iterator_to_array(Single::range(1, 1, 1.0), false);

        // Then
        $this->assertSame([1], $result);
    }

    /**
     * @test fractional float step promotes int operands to float output
     */
    public function testFractionalFloatStepPromotesToFloat(): void
    {
        // When
        $result = \iterator_to_array(Single::range(1, 5, 1.5), false);

        // Then
        $this->assertSame([1.0, 2.5, 4.0], $result);
    }

    /**
     * @test integer-valued float step exceeding int range rejects span violation cleanly (no warning)
     */
    public function testLargeIntegerValuedFloatStepExceedingSpanRejectsCleanly(): void
    {
        // Given
        // 1e20 is integer-valued (\fmod is 0.0 since the float has no fractional bits
        // at this magnitude) but exceeds PHP_INT_MAX. A naive int-path takes
        // (int)1e20 which under PHP 8.5+ emits a non-representable-cast warning
        // and yields a garbage int, then reports a wrong magnitude in the
        // InvalidArgumentException message. Route to the float path so the
        // (magnitude > span) check fires cleanly with the actual value.
        $caughtWarning = false;
        $prev = \set_error_handler(static function (int $errno, string $errstr) use (&$caughtWarning): bool {
            $caughtWarning = true;
            return true;
        });

        try {
            $threw = null;
            try {
                foreach (Single::range(0, 100, 1e20) as $_) {
                    // unreachable
                }
            } catch (\Throwable $e) {
                $threw = $e;
            }
        } finally {
            \set_error_handler($prev);
        }

        // Then
        $this->assertFalse($caughtWarning, 'no PHP warning should be emitted');
        $this->assertInstanceOf(\InvalidArgumentException::class, $threw);
        $this->assertStringContainsString('1.0E+20', (string)$threw->getMessage());
    }

    /**
     * @test float step at (float)PHP_INT_MAX boundary rejects the step exceeding the span cleanly, without TypeError
     */
    public function testFloatStepAtPhpIntMaxBoundaryHandledCleanly(): void
    {
        // Given
        // (float)PHP_INT_MAX rounds up to 2^63, just past PHP_INT_MAX. A naive int
        // path triggers a non-representable cast (PHP 8.5+ warning) and then
        // intdiv() TypeErrors on the float-typed result. The step 2^63 exceeds the
        // exact span 2^63 - 1, so the range rejects it with an InvalidArgumentException.
        // On 32-bit PHP, (float)PHP_INT_MAX is exact and equal to the span, so it doesn't apply.
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $caughtWarning = false;
        $prev = \set_error_handler(static function (int $errno, string $errstr) use (&$caughtWarning): bool {
            $caughtWarning = true;
            return true;
        });

        try {
            $threw = null;
            try {
                foreach (Single::range(0, \PHP_INT_MAX, (float)\PHP_INT_MAX) as $_) {
                    // unreachable
                }
            } catch (\Throwable $e) {
                $threw = $e;
            }
        } finally {
            \set_error_handler($prev);
        }

        // Then
        $this->assertFalse($caughtWarning, 'no PHP warning should be emitted');
        $this->assertNotInstanceOf(\TypeError::class, $threw, 'no TypeError should be thrown');
        $this->assertInstanceOf(\InvalidArgumentException::class, $threw);
        $this->assertSame(
            'Single::range: step magnitude (' . (float)\PHP_INT_MAX . ') must not exceed span (' . \PHP_INT_MAX . ')',
            $threw->getMessage()
        );
    }

    /**
     * @test integer range from PHP_INT_MIN to 0 iterates lazily from start (regression: previously rejected)
     */
    public function testIntegerRangeFromPhpIntMinToZeroIteratesLazily(): void
    {
        // Given
        // 0 - PHP_INT_MIN = PHP_INT_MAX + 1, which overflows int subtraction. Lazy
        // iteration with overflow-safe advances yields the sequence starting at
        // PHP_INT_MIN without ever materializing the count.

        // When
        $first5 = [];
        foreach (Single::range(\PHP_INT_MIN, 0, 1) as $v) {
            $first5[] = $v;
            if (\count($first5) === 5) {
                break;
            }
        }

        // Then
        $this->assertSame(
            [\PHP_INT_MIN, \PHP_INT_MIN + 1, \PHP_INT_MIN + 2, \PHP_INT_MIN + 3, \PHP_INT_MIN + 4],
            $first5
        );
    }

    /**
     * @test descending integer range from 0 to PHP_INT_MIN iterates lazily from start
     */
    public function testIntegerRangeFromZeroToPhpIntMinIteratesLazily(): void
    {
        // When
        $first5 = [];
        foreach (Single::range(0, \PHP_INT_MIN, 1) as $v) {
            $first5[] = $v;
            if (\count($first5) === 5) {
                break;
            }
        }

        // Then
        $this->assertSame([0, -1, -2, -3, -4], $first5);
    }

    public static function dataProviderForNonFiniteOperands(): array
    {
        return [
            'INF start'  => [\INF, 5, 1, 'start'],
            '-INF start' => [-\INF, 5, 1, 'start'],
            'NAN start'  => [\NAN, 5, 1, 'start'],
            'INF end'    => [1, \INF, 1, 'end'],
            '-INF end'   => [1, -\INF, 1, 'end'],
            'NAN end'    => [1, \NAN, 1, 'end'],
            'INF step'   => [1, 5, \INF, 'step'],
            'NAN step'   => [1, 5, \NAN, 'step'],
        ];
    }

    // -----------------------------------------------------------------------
    // Bug-fix regression tests (added 2026-05-28)
    // -----------------------------------------------------------------------

    /**
     * @test         span overflow with a PHP_INT_MAX step yields the pinned sequence
     * @dataProvider dataProviderForLargeStepSpanOverflow
     *
     * The integer span (endI - startI) overflows PHP_INT_MAX, but the step magnitude
     * is large enough that only 2–3 elements result. The previous span-overflow guard
     * rejected these outright.
     *
     * Expectations are literals rather than native \range() output, because native is
     * not version-stable here: PHP 8.2's range() carries the step in a C double and
     * then casts it to zend_ulong, and (double)PHP_INT_MAX rounds up to 2^63 — so on
     * 8.2 the step becomes PHP_INT_MAX + 1, which either trips the span check (throwing
     * ValueError) or advances one element too far. PHP 8.3+ keeps an int step exact.
     * Single::range uses the exact step on every supported version.
     *
     * @param int       $start
     * @param int       $end
     * @param int       $step
     * @param list<int> $expected
     */
    public function testLargeStepSpanOverflowYieldsPinnedSequence(
        int $start,
        int $end,
        int $step,
        array $expected
    ): void {
        // When
        $actual = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{int, int, int, list<int>}>
     */
    public static function dataProviderForLargeStepSpanOverflow(): array
    {
        return [
            'ascending PHP_INT_MIN to 0 with PHP_INT_MAX step' => [
                \PHP_INT_MIN, 0, \PHP_INT_MAX,
                [-9223372036854775807 - 1, -1],
            ],
            'ascending PHP_INT_MIN to PHP_INT_MAX with PHP_INT_MAX step' => [
                \PHP_INT_MIN, \PHP_INT_MAX, \PHP_INT_MAX,
                [-9223372036854775807 - 1, -1, 9223372036854775806],
            ],
            'descending 0 to PHP_INT_MIN with PHP_INT_MAX step' => [
                0, \PHP_INT_MIN, \PHP_INT_MAX,
                [0, -9223372036854775807],
            ],
            'descending PHP_INT_MAX to PHP_INT_MIN with PHP_INT_MAX step' => [
                \PHP_INT_MAX, \PHP_INT_MIN, \PHP_INT_MAX,
                [9223372036854775807, 0, -9223372036854775807],
            ],
            'ascending -2 to PHP_INT_MAX-1 with PHP_INT_MAX step' => [
                -2, \PHP_INT_MAX - 1, \PHP_INT_MAX,
                [-2, 9223372036854775805],
            ],
            'descending PHP_INT_MAX-1 to -2 with PHP_INT_MAX step' => [
                \PHP_INT_MAX - 1, -2, \PHP_INT_MAX,
                [9223372036854775806, -1],
            ],
            'ascending 0 to PHP_INT_MAX with PHP_INT_MAX step (step equals span)' => [
                0, \PHP_INT_MAX, \PHP_INT_MAX,
                [0, 9223372036854775807],
            ],
            'descending PHP_INT_MAX to 0 with PHP_INT_MAX step (step equals span)' => [
                \PHP_INT_MAX, 0, \PHP_INT_MAX,
                [9223372036854775807, 0],
            ],
            'ascending PHP_INT_MIN to -1 with PHP_INT_MAX step' => [
                \PHP_INT_MIN, -1, \PHP_INT_MAX,
                [-9223372036854775807 - 1, -1],
            ],
            'descending -1 to PHP_INT_MIN with PHP_INT_MAX step' => [
                -1, \PHP_INT_MIN, \PHP_INT_MAX,
                [-1, -9223372036854775807 - 1],
            ],
        ];
    }

    /**
     * @test         float range keeps the last element despite quotient precision loss
     * @dataProvider dataProviderForFloatPrecisionRegression
     *
     * For inputs like (0.4, 0.5, 0.05), the quotient (end-start)/step evaluates to
     * 1.999...e0 in IEEE 754 even though the mathematically correct quotient is 2.
     * The naive `(int)floor(quotient)` truncates one step too soon and the final
     * element is dropped.
     *
     * @param float        $start
     * @param float        $end
     * @param float        $step
     * @param array<float> $expected
     */
    public function testFloatPrecisionLoss(float $start, float $end, float $step, array $expected): void
    {
        // When
        $actual = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $actual);
    }

    /**
     * Pairs where (end - start) / step suffers float precision loss: quotient evaluates to N - epsilon
     * instead of N, so floor() drops one element.
     *
     * Test data generated with (tools/oracles/range_float_steps.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / Fraction(step)) + 1
     *   value_i = float(Fraction(start) ± i * Fraction(step))
     */
    public static function dataProviderForFloatPrecisionRegression(): array
    {
        return [
            '(0.4, 0.5, 0.05) → 3' => [0.4, 0.5, 0.05, [0.4, 0.45, 0.5]],
            '(1.0, 0.9, 0.02) → 6' => [1.0, 0.9, 0.02, [1.0, 0.98, 0.96, 0.94, 0.92, 0.9]],
            '(1.0, 0.9, 0.05) → 3' => [1.0, 0.9, 0.05, [1.0, 0.95, 0.9]],
        ];
    }

    /**
     * @test         float range keeps the last element despite quotient precision loss, values within float rounding
     * @dataProvider dataProviderForFloatPrecisionRegressionWithinRounding
     * @param        float        $start
     * @param        float        $end
     * @param        float        $step
     * @param        array<float> $expected
     */
    public function testFloatPrecisionLossWithinRounding(float $start, float $end, float $step, array $expected): void
    {
        // When
        $actual = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertCount(\count($expected), $actual);
        $this->assertContainsOnly('float', $actual);
        $this->assertEqualsWithDelta($expected, $actual, self::roundingDelta($start, $end));
    }

    /**
     * Same quotient precision loss as dataProviderForFloatPrecisionRegression; here $start ± i·$step in
     * float arithmetic misses the correctly rounded value of some elements (see roundingDelta).
     *
     * Test data generated with (tools/oracles/range_float_steps.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / Fraction(step)) + 1
     *   value_i = float(Fraction(start) ± i * Fraction(step))
     */
    public static function dataProviderForFloatPrecisionRegressionWithinRounding(): array
    {
        return [
            '(0.4, 0.5, 0.02) → 6' => [0.4, 0.5, 0.02, [0.4, 0.42, 0.44, 0.46, 0.48, 0.5]],
            '(0.4, 0.5, 0.01) → 11' => [
                0.4, 0.5, 0.01,
                [0.4, 0.41, 0.42, 0.43, 0.44, 0.45, 0.46, 0.47, 0.48, 0.49, 0.5],
            ],
            '(0.2, 0.5, 0.05) → 7' => [0.2, 0.5, 0.05, [0.2, 0.25, 0.3, 0.35, 0.4, 0.45, 0.5]],
            '(0.2, 0.5, 0.1) → 4' => [0.2, 0.5, 0.1, [0.2, 0.3, 0.4, 0.5]],
            '(0.3, 1.0, 0.05) → 15' => [
                0.3, 1.0, 0.05,
                [0.3, 0.35, 0.4, 0.45, 0.5, 0.55, 0.6, 0.65, 0.7, 0.75, 0.8, 0.85, 0.9, 0.95, 1.0],
            ],
            '(0.3, 1.0, 0.07) → 11' => [
                0.3, 1.0, 0.07,
                [0.3, 0.37, 0.44, 0.51, 0.58, 0.65, 0.72, 0.79, 0.86, 0.93, 1.0],
            ],
            '(0.3, 1.0, 0.1) → 8' => [0.3, 1.0, 0.1, [0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0]],
            '(0.3, 2.0, 0.17) → 11' => [
                0.3, 2.0, 0.17,
                [0.3, 0.47, 0.64, 0.81, 0.98, 1.15, 1.32, 1.49, 1.66, 1.83, 2.0],
            ],
            '(0.4, 1.0, 0.05) → 13' => [
                0.4, 1.0, 0.05,
                [0.4, 0.45, 0.5, 0.55, 0.6, 0.65, 0.7, 0.75, 0.8, 0.85, 0.9, 0.95, 1.0],
            ],
            '(0.4, 1.0, 0.1) → 7' => [0.4, 1.0, 0.1, [0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0]],
            '(0.1, 2.0, 0.05) → 39' => [
                0.1, 2.0, 0.05,
                [
                    0.1, 0.15, 0.2, 0.25, 0.3, 0.35, 0.4, 0.45, 0.5, 0.55, 0.6, 0.65, 0.7, 0.75, 0.8, 0.85, 0.9, 0.95,
                    1.0, 1.05, 1.1, 1.15, 1.2, 1.25, 1.3, 1.35, 1.4, 1.45, 1.5, 1.55, 1.6, 1.65, 1.7, 1.75, 1.8, 1.85,
                    1.9, 1.95, 2.0,
                ],
            ],
            '(0.1, 2.0, 0.1) → 20' => [
                0.1, 2.0, 0.1,
                [0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0, 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 1.8, 1.9, 2.0],
            ],
            '(1.0, 0.9, 0.01) → 11' => [
                1.0, 0.9, 0.01,
                [1.0, 0.99, 0.98, 0.97, 0.96, 0.95, 0.94, 0.93, 0.92, 0.91, 0.9],
            ],
        ];
    }

    // -----------------------------------------------------------------------
    // Exhaustive oracle-driven test suite
    //
    // The integer matrix compares against native \range() and deliberately covers
    // only inputs whose native behavior is stable across every supported PHP
    // version: all-int operands with a small int step. The float matrix takes its
    // expected values from exact rational arithmetic (tools/oracles/range_float_steps.py).
    //
    // The cases where native \range() differs by version are asserted separately
    // against literals, never against the running PHP:
    //   - integer-valued float steps (8.2 yields floats, 8.3+ ints)
    //     → testIntegerValuedFloatStepKeepsIntOutput,
    //       testMixedIntFloatOperandsProduceDocumentedTypes
    //   - negative steps on increasing ranges (8.2 abs()'d them, 8.3+ throws)
    //     → testConflictingDirectionAscendingOperandsNegativeStep
    //   - non-finite operands (8.3+ throws) → testNonFiniteOperandsThrow
    //   - a step of PHP_INT_MAX, which 8.2 inflates to PHP_INT_MAX + 1 by routing
    //     it through a C double → testLargeStepSpanOverflowYieldsPinnedSequence
    // -----------------------------------------------------------------------

    /**
     * @test         integer range matches native \range() across a broad input matrix
     * @dataProvider dataProviderForIntegerOracleMatrix
     */
    public function testIntegerRangeMatchesNativeOracle(int $start, int $end, int $step): void
    {
        // Given
        $expected = \range($start, $end, $step);

        // When
        $actual = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $actual);
    }

    public static function dataProviderForIntegerOracleMatrix(): array
    {
        $cases = [];

        // Regular ascending/descending with various step magnitudes
        $combos = [
            // [start, end, step]
            [0, 0, 1], [5, 5, 3], [-1, -1, 1],
            [1, 5, 1], [1, 5, 2], [1, 5, 3], [1, 5, 4],
            [5, 1, 1], [5, 1, 2], [5, 1, 3], [5, 1, 4],
            [0, 10, 1], [0, 10, 2], [0, 10, 3], [0, 10, 5], [0, 10, 7], [0, 10, 10],
            [10, 0, 1], [10, 0, 2], [10, 0, 3], [10, 0, 5], [10, 0, 7], [10, 0, 10],
            [-10, 10, 1], [-10, 10, 2], [-10, 10, 3], [-10, 10, 7], [-10, 10, 20],
            [10, -10, 1], [10, -10, 2], [10, -10, 3], [10, -10, 7], [10, -10, 20],
            [-5, -1, 1], [-1, -5, 1], [-5, -1, 4], [-1, -5, 4],
            [1, 100, 7], [100, 1, 7], [1, 100, 13], [100, 1, 13],
            // PHP_INT boundary
            [\PHP_INT_MAX - 10, \PHP_INT_MAX, 1], [\PHP_INT_MAX - 10, \PHP_INT_MAX, 2], [\PHP_INT_MAX - 10, \PHP_INT_MAX, 3],
            [\PHP_INT_MIN, \PHP_INT_MIN + 10, 1], [\PHP_INT_MIN, \PHP_INT_MIN + 10, 2], [\PHP_INT_MIN, \PHP_INT_MIN + 10, 3],
            [\PHP_INT_MAX, \PHP_INT_MAX - 10, 1], [\PHP_INT_MAX, \PHP_INT_MAX - 10, 2],
            [\PHP_INT_MIN + 10, \PHP_INT_MIN, 1], [\PHP_INT_MIN + 10, \PHP_INT_MIN, 2],
            // Step exactly equal to span
            [0, 5, 5], [5, 0, 5], [-5, 5, 10], [5, -5, 10],
            // NOTE: steps of PHP_INT_MAX are deliberately absent — see
            // testLargeStepSpanOverflowYieldsPinnedSequence for why native \range()
            // cannot be the oracle there.
        ];
        foreach ($combos as $c) {
            $cases["range({$c[0]}, {$c[1]}, {$c[2]})"] = $c;
        }
        return $cases;
    }

    /**
     * @test         float range matches the exact-arithmetic oracle across a broad input matrix
     * @dataProvider dataProviderForFloatOracleMatrix
     * @param        float        $start
     * @param        float        $end
     * @param        float        $step
     * @param        array<float> $expected
     */
    public function testFloatRangeMatchesOracle(float $start, float $end, float $step, array $expected): void
    {
        // When
        $actual = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $actual);
    }

    /**
     * Overshoot cases are left out: where 0.3 + 3 * 0.2 rounds past 0.9, native drops the endpoint and
     * Single::range keeps it (see testFloatRangeEndsOnEndWhereNativeDropsIt).
     *
     * Test data generated with (tools/oracles/range_float_steps.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / Fraction(step)) + 1
     *   value_i = float(Fraction(start) ± i * Fraction(step))
     */
    public static function dataProviderForFloatOracleMatrix(): array
    {
        return [
            'range(0.0, 1.0, 0.25)' => [0.0, 1.0, 0.25, [0.0, 0.25, 0.5, 0.75, 1.0]],
            'range(0.0, 1.0, 0.5)' => [0.0, 1.0, 0.5, [0.0, 0.5, 1.0]],
            'range(0.0, 1.0, 1.0)' => [0.0, 1.0, 1.0, [0.0, 1.0]],
            'range(0.0, 10.0, 0.5)' => [
                0.0, 10.0, 0.5,
                [
                    0.0, 0.5, 1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0, 5.5, 6.0, 6.5, 7.0, 7.5, 8.0, 8.5, 9.0,
                    9.5, 10.0,
                ],
            ],
            'range(0.0, 10.0, 1.0)' => [0.0, 10.0, 1.0, [0.0, 1.0, 2.0, 3.0, 4.0, 5.0, 6.0, 7.0, 8.0, 9.0, 10.0]],
            'range(0.0, 10.0, 2.5)' => [0.0, 10.0, 2.5, [0.0, 2.5, 5.0, 7.5, 10.0]],
            'range(0.0, 100.0, 1.0)' => [
                0.0, 100.0, 1.0,
                [
                    0.0, 1.0, 2.0, 3.0, 4.0, 5.0, 6.0, 7.0, 8.0, 9.0, 10.0, 11.0, 12.0, 13.0, 14.0, 15.0, 16.0, 17.0,
                    18.0, 19.0, 20.0, 21.0, 22.0, 23.0, 24.0, 25.0, 26.0, 27.0, 28.0, 29.0, 30.0, 31.0, 32.0, 33.0,
                    34.0, 35.0, 36.0, 37.0, 38.0, 39.0, 40.0, 41.0, 42.0, 43.0, 44.0, 45.0, 46.0, 47.0, 48.0, 49.0,
                    50.0, 51.0, 52.0, 53.0, 54.0, 55.0, 56.0, 57.0, 58.0, 59.0, 60.0, 61.0, 62.0, 63.0, 64.0, 65.0,
                    66.0, 67.0, 68.0, 69.0, 70.0, 71.0, 72.0, 73.0, 74.0, 75.0, 76.0, 77.0, 78.0, 79.0, 80.0, 81.0,
                    82.0, 83.0, 84.0, 85.0, 86.0, 87.0, 88.0, 89.0, 90.0, 91.0, 92.0, 93.0, 94.0, 95.0, 96.0, 97.0,
                    98.0, 99.0, 100.0,
                ],
            ],
            'range(0.0, 100.0, 10.0)' => [
                0.0, 100.0, 10.0,
                [0.0, 10.0, 20.0, 30.0, 40.0, 50.0, 60.0, 70.0, 80.0, 90.0, 100.0],
            ],
            'range(1.0, 2.0, 0.25)' => [1.0, 2.0, 0.25, [1.0, 1.25, 1.5, 1.75, 2.0]],
            'range(1.0, 5.0, 0.5)' => [1.0, 5.0, 0.5, [1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0]],
            'range(0.1, 1.0, 0.3)' => [0.1, 1.0, 0.3, [0.1, 0.4, 0.7, 1.0]],
            'range(0.5, 5.5, 1.0)' => [0.5, 5.5, 1.0, [0.5, 1.5, 2.5, 3.5, 4.5, 5.5]],
            'range(1.0, 0.0, 0.25)' => [1.0, 0.0, 0.25, [1.0, 0.75, 0.5, 0.25, 0.0]],
            'range(1.0, 0.0, 0.5)' => [1.0, 0.0, 0.5, [1.0, 0.5, 0.0]],
            'range(10.0, 0.0, 0.5)' => [
                10.0, 0.0, 0.5,
                [
                    10.0, 9.5, 9.0, 8.5, 8.0, 7.5, 7.0, 6.5, 6.0, 5.5, 5.0, 4.5, 4.0, 3.5, 3.0, 2.5, 2.0, 1.5, 1.0,
                    0.5, 0.0,
                ],
            ],
            'range(10.0, 0.0, 1.0)' => [10.0, 0.0, 1.0, [10.0, 9.0, 8.0, 7.0, 6.0, 5.0, 4.0, 3.0, 2.0, 1.0, 0.0]],
            'range(5.5, 0.5, 1.0)' => [5.5, 0.5, 1.0, [5.5, 4.5, 3.5, 2.5, 1.5, 0.5]],
            'range(2.0, 1.0, 0.25)' => [2.0, 1.0, 0.25, [2.0, 1.75, 1.5, 1.25, 1.0]],
            'range(-1.0, 1.0, 0.5)' => [-1.0, 1.0, 0.5, [-1.0, -0.5, 0.0, 0.5, 1.0]],
            'range(1.0, -1.0, 0.5)' => [1.0, -1.0, 0.5, [1.0, 0.5, 0.0, -0.5, -1.0]],
            'range(-2.5, 2.5, 0.5)' => [-2.5, 2.5, 0.5, [-2.5, -2.0, -1.5, -1.0, -0.5, 0.0, 0.5, 1.0, 1.5, 2.0, 2.5]],
            'range(0.0, 0.0, 1.0)' => [0.0, 0.0, 1.0, [0.0]],
            'range(2.5, 2.5, 1.0)' => [2.5, 2.5, 1.0, [2.5]],
            'range(1.0, 0.0, 1.0)' => [1.0, 0.0, 1.0, [1.0, 0.0]],
            'range(0.5, 1.5, 1.0)' => [0.5, 1.5, 1.0, [0.5, 1.5]],
            'range(0.4, 0.5, 0.05)' => [0.4, 0.5, 0.05, [0.4, 0.45, 0.5]],
            'range(0.2, 0.5, 0.06)' => [0.2, 0.5, 0.06, [0.2, 0.26, 0.32, 0.38, 0.44, 0.5]],
            'range(1.0, 0.9, 0.02)' => [1.0, 0.9, 0.02, [1.0, 0.98, 0.96, 0.94, 0.92, 0.9]],
            'range(1.0, 0.9, 0.05)' => [1.0, 0.9, 0.05, [1.0, 0.95, 0.9]],
        ];
    }

    /**
     * @test         float range matches the exact-arithmetic oracle within float rounding across a broad input matrix
     * @dataProvider dataProviderForFloatOracleMatrixWithinRounding
     * @param        float        $start
     * @param        float        $end
     * @param        float        $step
     * @param        array<float> $expected
     */
    public function testFloatRangeMatchesOracleWithinRounding(
        float $start,
        float $end,
        float $step,
        array $expected
    ): void {
        // When
        $actual = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertCount(\count($expected), $actual, 'element count must match the oracle');
        $this->assertContainsOnly('float', $actual);
        $this->assertEqualsWithDelta($expected, $actual, self::roundingDelta($start, $end));
    }

    /**
     * Rows where $start ± i·$step in float arithmetic misses the correctly rounded value of some elements
     * (see roundingDelta).
     *
     * Test data generated with (tools/oracles/range_float_steps.py):
     *   count = math.floor(abs(Fraction(end) - Fraction(start)) / Fraction(step)) + 1
     *   value_i = float(Fraction(start) ± i * Fraction(step))
     */
    public static function dataProviderForFloatOracleMatrixWithinRounding(): array
    {
        return [
            'range(0.0, 1.0, 0.1)' => [0.0, 1.0, 0.1, [0.0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0]],
            'range(0.0, 1.0, 0.2)' => [0.0, 1.0, 0.2, [0.0, 0.2, 0.4, 0.6, 0.8, 1.0]],
            'range(0.1, 1.0, 0.1)' => [0.1, 1.0, 0.1, [0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0]],
            'range(0.1, 1.0, 0.2)' => [0.1, 1.0, 0.2, [0.1, 0.3, 0.5, 0.7, 0.9]],
            'range(0.0, 5.0, 1.2)' => [0.0, 5.0, 1.2, [0.0, 1.2, 2.4, 3.6, 4.8]],
            'range(1.0, 0.0, 0.1)' => [1.0, 0.0, 0.1, [1.0, 0.9, 0.8, 0.7, 0.6, 0.5, 0.4, 0.3, 0.2, 0.1, 0.0]],
            'range(-5.5, 5.5, 1.1)' => [-5.5, 5.5, 1.1, [-5.5, -4.4, -3.3, -2.2, -1.1, 0.0, 1.1, 2.2, 3.3, 4.4, 5.5]],
            'range(5.5, -5.5, 1.1)' => [5.5, -5.5, 1.1, [5.5, 4.4, 3.3, 2.2, 1.1, 0.0, -1.1, -2.2, -3.3, -4.4, -5.5]],
            'range(0.4, 0.5, 0.02)' => [0.4, 0.5, 0.02, [0.4, 0.42, 0.44, 0.46, 0.48, 0.5]],
            'range(0.4, 0.5, 0.01)' => [
                0.4, 0.5, 0.01,
                [0.4, 0.41, 0.42, 0.43, 0.44, 0.45, 0.46, 0.47, 0.48, 0.49, 0.5],
            ],
            'range(0.2, 0.5, 0.05)' => [0.2, 0.5, 0.05, [0.2, 0.25, 0.3, 0.35, 0.4, 0.45, 0.5]],
            'range(0.2, 0.5, 0.1)' => [0.2, 0.5, 0.1, [0.2, 0.3, 0.4, 0.5]],
            'range(0.3, 1.0, 0.05)' => [
                0.3, 1.0, 0.05,
                [0.3, 0.35, 0.4, 0.45, 0.5, 0.55, 0.6, 0.65, 0.7, 0.75, 0.8, 0.85, 0.9, 0.95, 1.0],
            ],
            'range(0.3, 1.0, 0.07)' => [
                0.3, 1.0, 0.07,
                [0.3, 0.37, 0.44, 0.51, 0.58, 0.65, 0.72, 0.79, 0.86, 0.93, 1.0],
            ],
            'range(0.3, 1.0, 0.1)' => [0.3, 1.0, 0.1, [0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0]],
            'range(0.3, 2.0, 0.17)' => [
                0.3, 2.0, 0.17,
                [0.3, 0.47, 0.64, 0.81, 0.98, 1.15, 1.32, 1.49, 1.66, 1.83, 2.0],
            ],
            'range(0.4, 1.0, 0.05)' => [
                0.4, 1.0, 0.05,
                [0.4, 0.45, 0.5, 0.55, 0.6, 0.65, 0.7, 0.75, 0.8, 0.85, 0.9, 0.95, 1.0],
            ],
            'range(0.4, 1.0, 0.1)' => [0.4, 1.0, 0.1, [0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0]],
            'range(0.1, 2.0, 0.05)' => [
                0.1, 2.0, 0.05,
                [
                    0.1, 0.15, 0.2, 0.25, 0.3, 0.35, 0.4, 0.45, 0.5, 0.55, 0.6, 0.65, 0.7, 0.75, 0.8, 0.85, 0.9, 0.95,
                    1.0, 1.05, 1.1, 1.15, 1.2, 1.25, 1.3, 1.35, 1.4, 1.45, 1.5, 1.55, 1.6, 1.65, 1.7, 1.75, 1.8, 1.85,
                    1.9, 1.95, 2.0,
                ],
            ],
            'range(0.1, 2.0, 0.1)' => [
                0.1, 2.0, 0.1,
                [0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0, 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 1.8, 1.9, 2.0],
            ],
            'range(1.0, 0.9, 0.01)' => [
                1.0, 0.9, 0.01,
                [1.0, 0.99, 0.98, 0.97, 0.96, 0.95, 0.94, 0.93, 0.92, 0.91, 0.9],
            ],
        ];
    }

    /**
     * @test mixed int/float operands produce the documented output types
     *
     * Expectations are literals rather than native \range() output on purpose. PHP 8.3
     * changed range() so that an integer-valued float step is interpreted as an int,
     * while PHP 8.2 sends any float step down the float path. Single::range pins the
     * PHP 8.3+ types on every supported version, so comparing against the running PHP's
     * \range() would assert different things on 8.2 than on 8.3+.
     */
    public function testMixedIntFloatOperandsProduceDocumentedTypes(): void
    {
        // int start, float end → all floats
        $this->assertSame([1.0, 2.0, 3.0, 4.0, 5.0], \iterator_to_array(Single::range(1, 5.0), false));

        // float start, int end → all floats
        $this->assertSame([1.0, 2.0, 3.0, 4.0, 5.0], \iterator_to_array(Single::range(1.0, 5), false));

        // int start, int end, int step → all ints
        $this->assertSame([1, 2, 3, 4, 5], \iterator_to_array(Single::range(1, 5, 1), false));

        // int operands, integer-valued float step → all ints (PHP 8.3+ native behavior)
        $this->assertSame([1, 2, 3, 4, 5], \iterator_to_array(Single::range(1, 5, 1.0), false));

        // int operands, fractional float step → all floats
        $this->assertSame([1.0, 2.5, 4.0], \iterator_to_array(Single::range(1, 5, 1.5), false));
    }
}

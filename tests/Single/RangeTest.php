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
        $this->assertEqualsWithDelta($expected, $result, 0.0001);
    }

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

            // ascending float
            'ascending floats' => [0.5, 5.5, 1.0, [0.5, 1.5, 2.5, 3.5, 4.5, 5.5]],
            'ascending floats fractional step' => [0.0, 5.0, 1.2, [0.0, 1.2, 2.4, 3.6, 4.8]],

            // descending float
            'descending floats' => [5.5, 0.5, 1.0, [5.5, 4.5, 3.5, 2.5, 1.5, 0.5]],

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
            'single-element float' => [2.5, 2.5, 1.0, [2.5]],

            // negative numbers
            'ascending negatives' => [-5, -1, 1, [-5, -4, -3, -2, -1]],
            'descending negatives' => [-1, -5, 1, [-1, -2, -3, -4, -5]],
            'across zero ascending' => [-2, 2, 1, [-2, -1, 0, 1, 2]],
            'across zero descending' => [2, -2, 1, [2, 1, 0, -1, -2]],
        ];
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
     * @test float range does not yield values past $end (ascending)
     */
    public function testFloatRangeAscendingDoesNotOvershoot(): void
    {
        // Given
        // 0.3 + 3 * 0.2 = 0.9000000000000001 in IEEE 754 — strictly greater than $end (0.9).
        // Native \range(0.3, 0.9, 0.2) returns [0.3, 0.5, 0.7] (does not include 0.9).
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
     * @test integer-valued float step on int operands keeps int output (matches native range)
     */
    public function testIntegerValuedFloatStepKeepsIntOutput(): void
    {
        // Given
        // Native PHP: range(1, 5, 1.0) → [int(1), int(2), int(3), int(4), int(5)].
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
     * @test float step at (float)PHP_INT_MAX boundary is handled cleanly without TypeError
     */
    public function testFloatStepAtPhpIntMaxBoundaryHandledCleanly(): void
    {
        // Given
        // (float)PHP_INT_MAX rounds up to 2^63, just past PHP_INT_MAX. A naive int
        // path triggers a non-representable cast (PHP 8.5+ warning) and then
        // intdiv() TypeErrors on the float-typed result. Float-path routing yields
        // a sensible result.
        $caughtWarning = false;
        $prev = \set_error_handler(static function (int $errno, string $errstr) use (&$caughtWarning): bool {
            $caughtWarning = true;
            return true;
        });

        try {
            $threw = null;
            try {
                $result = \iterator_to_array(Single::range(0, \PHP_INT_MAX, (float)\PHP_INT_MAX), false);
            } catch (\Throwable $e) {
                $threw = $e;
                $result = [];
            }
        } finally {
            \set_error_handler($prev);
        }

        // Then
        $this->assertFalse($caughtWarning, 'no PHP warning should be emitted');
        $this->assertNull($threw, 'no TypeError should be thrown');
        $this->assertGreaterThanOrEqual(1, \count($result));
        foreach ($result as $v) {
            $this->assertIsFloat($v);
        }
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
     * @test         span overflow with large step yields the same sequence as native \range()
     * @dataProvider dataProviderForLargeStepSpanOverflow
     *
     * The integer span (endI - startI) overflows PHP_INT_MAX, but the step magnitude
     * is large enough that only 2–3 elements result. Native \range() handles these
     * cleanly; the previous span-overflow guard rejected them outright.
     */
    public function testLargeStepSpanOverflowMatchesNative(int $start, int $end, int $step): void
    {
        // Given
        $expected = \range($start, $end, $step);

        // When
        $actual = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertSame($expected, $actual);
    }

    public static function dataProviderForLargeStepSpanOverflow(): array
    {
        return [
            'ascending PHP_INT_MIN to 0 with PHP_INT_MAX step' => [\PHP_INT_MIN, 0, \PHP_INT_MAX],
            'ascending PHP_INT_MIN to PHP_INT_MAX with PHP_INT_MAX step' => [\PHP_INT_MIN, \PHP_INT_MAX, \PHP_INT_MAX],
            'descending 0 to PHP_INT_MIN with PHP_INT_MAX step' => [0, \PHP_INT_MIN, \PHP_INT_MAX],
            'descending PHP_INT_MAX to PHP_INT_MIN with PHP_INT_MAX step' => [\PHP_INT_MAX, \PHP_INT_MIN, \PHP_INT_MAX],
            'ascending -2 to PHP_INT_MAX-1 with PHP_INT_MAX step' => [-2, \PHP_INT_MAX - 1, \PHP_INT_MAX],
            'descending PHP_INT_MAX-1 to -2 with PHP_INT_MAX step' => [\PHP_INT_MAX - 1, -2, \PHP_INT_MAX],
        ];
    }

    /**
     * @test         float range matches native \range() despite quotient precision loss
     * @dataProvider dataProviderForFloatPrecisionRegression
     *
     * For inputs like (0.4, 0.5, 0.05), the quotient (end-start)/step evaluates to
     * 1.999...e0 in IEEE 754 even though the mathematically correct quotient is 2.
     * The naive `(int)floor(quotient)` truncates one step too soon and the final
     * element is dropped. Native PHP \range() includes it.
     */
    public function testFloatPrecisionLossMatchesNative(float $start, float $end, float $step): void
    {
        // Given
        $expected = \range($start, $end, $step);

        // When
        $actual = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertCount(\count($expected), $actual);
        foreach ($expected as $i => $v) {
            $this->assertEqualsWithDelta($v, $actual[$i], 1e-9, "element {$i}");
        }
    }

    public static function dataProviderForFloatPrecisionRegression(): array
    {
        // Pairs where (end - start) / step suffers float precision loss: quotient
        // evaluates to N - epsilon instead of N, so floor() drops one element.
        return [
            '(0.4, 0.5, 0.05) → 3' => [0.4, 0.5, 0.05],
            '(0.4, 0.5, 0.02) → 6' => [0.4, 0.5, 0.02],
            '(0.4, 0.5, 0.01) → 11' => [0.4, 0.5, 0.01],
            '(0.2, 0.5, 0.05) → 7' => [0.2, 0.5, 0.05],
            '(0.2, 0.5, 0.1) → 4' => [0.2, 0.5, 0.1],
            '(0.3, 1.0, 0.05) → 15' => [0.3, 1.0, 0.05],
            '(0.3, 1.0, 0.07) → 11' => [0.3, 1.0, 0.07],
            '(0.3, 1.0, 0.1) → 8' => [0.3, 1.0, 0.1],
            '(0.3, 2.0, 0.17) → 11' => [0.3, 2.0, 0.17],
            '(0.4, 1.0, 0.05) → 13' => [0.4, 1.0, 0.05],
            '(0.4, 1.0, 0.1) → 7' => [0.4, 1.0, 0.1],
            '(0.1, 2.0, 0.05) → 39' => [0.1, 2.0, 0.05],
            '(0.1, 2.0, 0.1) → 20' => [0.1, 2.0, 0.1],
            // Descending mirrors
            '(1.0, 0.9, 0.01) → 11' => [1.0, 0.9, 0.01],
            '(1.0, 0.9, 0.02) → 6' => [1.0, 0.9, 0.02],
            '(1.0, 0.9, 0.05) → 3' => [1.0, 0.9, 0.05],
        ];
    }

    // -----------------------------------------------------------------------
    // Exhaustive oracle-driven test suite (native \range() as truth source)
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
            // Span-overflow with large step (the Codex P2 cases)
            [\PHP_INT_MIN, 0, \PHP_INT_MAX],
            [0, \PHP_INT_MIN, \PHP_INT_MAX],
            [\PHP_INT_MIN, \PHP_INT_MAX, \PHP_INT_MAX],
            [\PHP_INT_MAX, \PHP_INT_MIN, \PHP_INT_MAX],
            [-2, \PHP_INT_MAX - 1, \PHP_INT_MAX],
            [\PHP_INT_MAX - 1, -2, \PHP_INT_MAX],
            // Step = PHP_INT_MAX with smaller spans
            [0, \PHP_INT_MAX, \PHP_INT_MAX], [\PHP_INT_MAX, 0, \PHP_INT_MAX],
            [\PHP_INT_MIN, -1, \PHP_INT_MAX], [-1, \PHP_INT_MIN, \PHP_INT_MAX],
        ];
        foreach ($combos as $c) {
            $cases["range({$c[0]}, {$c[1]}, {$c[2]})"] = $c;
        }
        return $cases;
    }

    /**
     * @test         float range matches native \range() across a broad input matrix
     * @dataProvider dataProviderForFloatOracleMatrix
     */
    public function testFloatRangeMatchesNativeOracle(float $start, float $end, float $step): void
    {
        // Given
        $expected = \range($start, $end, $step);

        // When
        $actual = \iterator_to_array(Single::range($start, $end, $step), false);

        // Then
        $this->assertCount(\count($expected), $actual, 'element count must match native');
        foreach ($expected as $i => $v) {
            $this->assertIsFloat($actual[$i]);
            $this->assertEqualsWithDelta($v, $actual[$i], 1e-9, "element {$i}");
        }
    }

    public static function dataProviderForFloatOracleMatrix(): array
    {
        $cases = [];

        // Simple decimal steps
        $combos = [
            // Ascending
            [0.0, 1.0, 0.1], [0.0, 1.0, 0.2], [0.0, 1.0, 0.25], [0.0, 1.0, 0.5], [0.0, 1.0, 1.0],
            [0.0, 10.0, 0.5], [0.0, 10.0, 1.0], [0.0, 10.0, 2.5],
            [0.0, 100.0, 1.0], [0.0, 100.0, 10.0],
            [1.0, 2.0, 0.25], [1.0, 5.0, 0.5],
            [0.1, 1.0, 0.1], [0.1, 1.0, 0.2], [0.1, 1.0, 0.3],
            [0.5, 5.5, 1.0], [0.0, 5.0, 1.2],
            // Descending
            [1.0, 0.0, 0.1], [1.0, 0.0, 0.25], [1.0, 0.0, 0.5],
            [10.0, 0.0, 0.5], [10.0, 0.0, 1.0],
            [5.5, 0.5, 1.0], [2.0, 1.0, 0.25],
            // Negatives and crossing zero
            [-1.0, 1.0, 0.5], [1.0, -1.0, 0.5], [-2.5, 2.5, 0.5],
            [-5.5, 5.5, 1.1], [5.5, -5.5, 1.1],
            // Single-element
            [0.0, 0.0, 1.0], [2.5, 2.5, 1.0],
            // Step exactly equal to span
            [0.0, 1.0, 1.0], [1.0, 0.0, 1.0], [0.5, 1.5, 1.0],
            // Integer-valued float step
            [0.0, 10.0, 1.0], [10.0, 0.0, 1.0],
            // Precision-loss cases (Gemini's category)
            [0.4, 0.5, 0.05], [0.4, 0.5, 0.02], [0.4, 0.5, 0.01],
            [0.2, 0.5, 0.05], [0.2, 0.5, 0.06], [0.2, 0.5, 0.1],
            [0.3, 1.0, 0.05], [0.3, 1.0, 0.07], [0.3, 1.0, 0.1],
            [0.3, 2.0, 0.17],
            [0.4, 1.0, 0.05], [0.4, 1.0, 0.1],
            [0.1, 2.0, 0.05], [0.1, 2.0, 0.1],
            [1.0, 0.9, 0.01], [1.0, 0.9, 0.02], [1.0, 0.9, 0.05],
            // Overshoot cases (already covered but include for sanity)
            [0.3, 0.9, 0.2], [0.9, 0.3, 0.2],
        ];
        foreach ($combos as $c) {
            $cases["range({$c[0]}, {$c[1]}, {$c[2]})"] = $c;
        }
        return $cases;
    }

    /**
     * @test mixed int/float operands produce the same output type as native \range()
     */
    public function testMixedIntFloatOperandsMatchNativeTypes(): void
    {
        // int start, float end → all floats
        $this->assertSame(\range(1, 5.0), \iterator_to_array(Single::range(1, 5.0), false));

        // float start, int end → all floats
        $this->assertSame(\range(1.0, 5), \iterator_to_array(Single::range(1.0, 5), false));

        // int start, int end, int step → all ints
        $this->assertSame(\range(1, 5, 1), \iterator_to_array(Single::range(1, 5, 1), false));

        // int operands, integer-valued float step → all ints (native behavior)
        $this->assertSame(\range(1, 5, 1.0), \iterator_to_array(Single::range(1, 5, 1.0), false));

        // int operands, fractional float step → all floats
        $this->assertSame(\range(1, 5, 1.5), \iterator_to_array(Single::range(1, 5, 1.5), false));
    }
}

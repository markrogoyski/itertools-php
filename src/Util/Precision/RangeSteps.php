<?php

declare(strict_types=1);

namespace IterTools\Util\Precision;

/**
 * @internal
 * The step arithmetic of Single::range(): element counts, rounded values and rounding allowances of float ranges.
 */
final class RangeSteps
{
    /**
     * The float range of int operands by a whole step, which {@see \IterTools\Single::range()} has checked against the
     * span.
     *
     * The element count is taken from the exact span, and each value is the exact int $start ± i·$magnitude,
     * converted to float once, so it is correctly rounded. A step of at least 2^63 is a float, and fits only a
     * span beyond the int range, which is below 2^64, so it fits once.
     *
     * @param int       $start
     * @param int       $end
     * @param int|float $magnitude whole and positive
     * @param bool      $ascending
     * @param float     $first     $start as a float, which is -0.0 for a -0.0 start descending
     *
     * @return \Generator<int, float>
     *
     * @throws \InvalidArgumentException when the range has more than PHP_INT_MAX elements
     */
    public static function rangeOfWholeStepOnInts(
        int $start,
        int $end,
        int|float $magnitude,
        bool $ascending,
        float $first
    ): \Generator {
        [$low, $high] = $ascending ? [$start, $end] : [$end, $start];

        $magnitudeI = Arithmetic::toExactInt($magnitude);
        if ($magnitudeI === null) {
            // The span is at least 2^63, so $low < 0 <= $high, and $low + 2^63, $high - 2^63 and the step's
            // excess over 2^63 are exact ints.
            $magnitudeExcess = (int)((float)$magnitude + (float)\PHP_INT_MIN);
            yield $first;
            yield (float)($ascending
                ? ($low - \PHP_INT_MIN) + $magnitudeExcess
                : ($high + \PHP_INT_MIN) - $magnitudeExcess);
            return;
        }

        // The span fits in an int unless $low is negative and $high - $low passes PHP_INT_MAX.
        $spanFitsInt = $low >= 0 || $high <= \PHP_INT_MAX + $low;
        if ($spanFitsInt) {
            $steps = \intdiv($high - $low, $magnitudeI);
        } elseif ($magnitudeI === 1) {
            $steps = null;
        } else {
            // The span is 2^63 + $excess, which overflowed. With 2^63 = $magnitudeI·$quotient + $remainder, 0
            // < $remainder <= $magnitudeI, the remainders of 2^63 and of $excess add up to one more step when
            // their sum reaches $magnitudeI. At most PHP_INT_MAX steps fit, as $magnitudeI >= 2.
            $excess = $high - ($low - \PHP_INT_MIN);
            $quotient = \intdiv(\PHP_INT_MAX, $magnitudeI);
            $remainder = \PHP_INT_MAX % $magnitudeI + 1;
            $steps = $quotient + \intdiv($excess, $magnitudeI)
                + ($excess % $magnitudeI >= $magnitudeI - $remainder ? 1 : 0);
        }

        if ($steps === null || $steps >= \PHP_INT_MAX) {
            $spanDescription = $spanFitsInt ? 'span=' . ($high - $low) : "span between {$start} and {$end}";
            throw new \InvalidArgumentException(
                'Single::range: span / step exceeds representable iteration count '
                . "({$spanDescription}, step={$magnitudeI})"
            );
        }

        // The values stay between $start and $end, so the int arithmetic doesn't overflow.
        $value = $start;
        yield $first;
        if ($ascending) {
            for ($i = 0; $i < $steps; ++$i) {
                $value += $magnitudeI;
                yield (float)$value;
            }
        } else {
            for ($i = 0; $i < $steps; ++$i) {
                $value -= $magnitudeI;
                yield (float)$value;
            }
        }
    }

    /**
     * $start ± $i·$magnitude, correctly rounded to float, for an int $start past 2^53 that a float can't hold
     * and a fractional $magnitude, as {@see \IterTools\Single::range()} yields it.
     *
     * $i·$magnitude is split exactly (TwoProduct, then TwoSum) into an int and a fraction in (-1, 1), so the
     * value is an exact int $base plus a fraction. Past 2^53 the fraction only decides a tie, as a sticky bit
     * does. Within 2^53, $base converts to float exactly and the fraction is added with one rounding; its low
     * part is rounded to odd first, so it can't make or break a tie.
     *
     * The value lies between $start and $end, and so does $base, which is within 1 of it.
     *
     * @param int   $start
     * @param int   $i         positive and at most 2^53, so (float) $i is exact
     * @param float $magnitude fractional and positive, so below 2^52
     * @param bool  $ascending
     *
     * @return float
     */
    public static function roundIntPlusMultiple(int $start, int $i, float $magnitude, bool $ascending): float
    {
        // $product + $error is exactly $i·$magnitude.
        [$product, $error] = ExactFloat::twoProduct((float)$i, $magnitude);

        // Integer parts, truncated toward zero, and the fractions left over, all exact. $product is positive.
        $productInt = \floor($product);
        $errorInt = $error < 0.0 ? \ceil($error) : \floor($error);
        $productFrac = $product - $productInt;
        $errorFrac = $error - $errorInt;

        // TwoSum (Knuth): $fracHi + $fracLo is exactly $productFrac + $errorFrac, in (-1, 2). Its integer part
        // moves to $small, which leaves |$fracHi + $fracLo| < 1, with the sign of $fracHi unless it is 0.
        $fracHi = $productFrac + $errorFrac;
        $errorPart = $fracHi - $productFrac;
        $fracLo = ($productFrac - ($fracHi - $errorPart)) + ($errorFrac - $errorPart);
        $carry = $fracHi < 0.0 ? \ceil($fracHi) : \floor($fracHi);
        $fracHi -= $carry;
        $small = (int)$errorInt + (int)$carry;

        // $base = $start ± ($productInt + $small), added in an order that stays within the int range. Past
        // 2^63, $i·$magnitude spans 0, from a $start on the other side of it, so $start ± 2^63 is an int.
        // The exact product is below 2^64, so $product reaches 2^64 only by rounding up, with $error in
        // [-1024, 0), and the shift then leaves 2^63, one more than PHP_INT_MAX. That one moves to $small, which
        // stays negative, since $base is within 1 of a value in the int range: the second sum below takes it.
        $sign = $ascending ? 1 : -1;
        $from = $start;
        $productIntRange = $productInt;
        if ($productInt >= -(float)\PHP_INT_MIN) {
            $from = $ascending ? $start - \PHP_INT_MIN : $start + \PHP_INT_MIN;
            $productIntRange = $productInt + (float)\PHP_INT_MIN;
        }
        if ($productIntRange >= -(float)\PHP_INT_MIN) {
            // $product rounded up to 2^64: 2^63 is one more than PHP_INT_MAX, so the one moves to $small.
            $whole = \PHP_INT_MAX;
            ++$small;
        } else {
            $whole = (int)$productIntRange;
        }
        $base = $small >= 0
            ? ($from + $sign * $small) + $sign * $whole
            : $from + $sign * ($whole + $small);

        $hi = $ascending ? $fracHi : -$fracHi;
        $lo = $ascending ? $fracLo : -$fracLo;
        if ($hi === 0.0 && $lo === 0.0) {
            return (float)$base;
        }

        if ($base > 2 ** 53 || $base < -(2 ** 53)) {
            // The float spacing is at least 2 there, so only the side of $base the fraction is on matters. Within
            // 2^62, 2·$base ± 1 rounds as $base ± the fraction does; past it, the spacing is at least 512, and so
            // does $base with its lowest bit set ($base ± 1 when it is even), which can't be a tie.
            $towards = ($hi !== 0.0 ? $hi : $lo) > 0.0 ? 1 : -1;
            if ($base > -(2 ** 62) && $base < 2 ** 62) {
                return (float)(2 * $base + $towards) * 0.5;
            }
            return (float)($towards > 0 ? $base | 1 : ($base - 1) | 1);
        }

        // TwoSum: $sum + $sumError is exactly $base + $hi, where $base is exact as a float.
        $baseF = (float)$base;
        $sum = $baseF + $hi;
        $hiPart = $sum - $baseF;
        $sumError = ($baseF - ($sum - $hiPart)) + ($hi - $hiPart);

        // TwoSum: $rest + $restError is exactly $sumError + $lo. Rounded to odd, $rest keeps the sign of
        // $restError as a sticky bit far below the spacing at $sum, so $sum + $rest rounds as the exact value.
        $rest = $sumError + $lo;
        $loPart = $rest - $sumError;
        $restError = ($sumError - ($rest - $loPart)) + ($lo - $loPart);
        if ($restError !== 0.0) {
            $spacing = 2.0 * Ulp::halfFloatSpacing($rest);
            if ($spacing > 0.0 && \fmod($rest / $spacing, 2.0) === 0.0) {
                // Toward 0 from a power of two, the next float down is half a spacing away.
                $towardZero = ($rest > 0.0) !== ($restError > 0.0);
                $nudge = $towardZero && \abs($rest) === $spacing * 4503599627370496.0 ? $spacing / 2.0 : $spacing;
                $rest += $restError > 0.0 ? $nudge : -$nudge;
            }
        }

        return $sum + $rest;
    }

    /**
     * $start ± $i·$step, correctly rounded to float, for an int step past 2^53 that a float can't hold, as
     * {@see \IterTools\Single::range()} yields it from a float $start.
     *
     * The value is summed exactly, as an expansion() of $start and the TwoProducts of $i with each part of the
     * step. Its components added in float give a float within a spacing or so of it, which is moved to its
     * neighbour until roundsTo() says the value rounds to it.
     *
     * @param float $start
     * @param int   $i         positive and at most 2^53, so (float) $i is exact
     * @param float $stepHi    the step magnitude as a float
     * @param float $stepLo    the step magnitude minus $stepHi, exactly (see intToFloats())
     * @param bool  $ascending
     *
     * @return float
     */
    public static function roundFloatPlusMultiple(
        float $start,
        int $i,
        float $stepHi,
        float $stepLo,
        bool $ascending
    ): float {
        $sign = $ascending ? 1.0 : -1.0;
        [$hiProduct, $hiError] = ExactFloat::twoProduct((float)$i, $stepHi);
        [$loProduct, $loError] = ExactFloat::twoProduct((float)$i, $stepLo);
        $value = ExactFloat::expansion([
            $start,
            $sign * $hiProduct,
            $sign * $hiError,
            $sign * $loProduct,
            $sign * $loError,
        ]);

        $rounded = 0.0;
        foreach ($value as $component) {
            $rounded += $component;
        }
        // The value is 0 or at least 1 in magnitude: an integer from an integral $start, or more than 2^52 from a
        // non-integral one, which is below 2^52. So the loop stays clear of the subnormals, where
        // halfFloatSpacing() is 0.
        $offset = ExactFloat::expansion([...$value, -$rounded]);
        while (\abs($rounded) >= \PHP_FLOAT_MIN && !ExactFloat::roundsTo($rounded, $offset)) {
            // To the neighbour on the side of the value. Toward 0 from a power of two, it is half a spacing away.
            $halfSpacing = Ulp::halfFloatSpacing($rounded);
            $up = ExactFloat::expansionSign($offset) > 0;
            $towardZero = $up === ($rounded < 0.0);
            $rounded += ($up ? 1.0 : -1.0)
                * ($towardZero && \abs($rounded) === $halfSpacing * 2.0 ** 53.0 ? $halfSpacing : 2.0 * $halfSpacing);
            $offset = ExactFloat::expansion([...$value, -$rounded]);
        }

        return $rounded;
    }

    /**
     * Whether a step at least the float span $high - $low exceeds their exact span by more than the operands'
     * rounding.
     *
     * The float span rounds the exact one, so it can round up to the step: in range(0.1, 2.0 ** 53, 2.0 ** 53),
     * the exact span 2^53 - 0.1 rounds to 2^53. A deficit within $allowance isn't taken as one: the exact span of
     * the floats is 6.4 - 4.4e-16 in range(-10.0, -3.6, 6.4), and 0.2 - 2.8e-17 in range(0.1, 0.3, 0.2), but the
     * decimals span the step.
     *
     * @param int|float   $magnitude the step magnitude, at least $high - $low in float
     * @param float       $low
     * @param float       $high
     * @param list<float> $allowance the rounding of the operands and the step (see roundingAllowances())
     *
     * @return bool
     */
    public static function stepExceedsExactFloatSpan(int|float $magnitude, float $low, float $high, array $allowance): bool
    {
        // An int step past 2^53 is the sum of its float and the rest. The float span is at most the step, so it
        // is finite.
        $terms = [
            ...(\is_int($magnitude) ? ExactFloat::intToFloats($magnitude) : [$magnitude]),
            ...ExactFloat::negated(ExactFloat::twoSum(-$low, $high)),
            ...ExactFloat::negated($allowance),
        ];

        return ExactFloat::expansionSign(ExactFloat::expansion($terms)) > 0;
    }

    /**
     * Whether a step exceeds the span of $start and $end taken as the decimals they are written as, or null where
     * that can't be decided exactly here.
     *
     * A non-integral float is taken as its shortest decimal, while roundingAllowances() gives the operands an
     * allowance, and the step always is; an integral float or an int step stands for itself, as in
     * roundingAllowance(). An int $start or $end, which reaches here only paired with a non-integral float or a
     * float beyond the int range, is converted to float, as range() counts the steps in that span: in range(0.5,
     * 2 ** 53 + 1, 2.0 ** 53), the step exceeds the span 2^53 - 0.5, although it is within the exact span.
     * Bounding each decimal's distance from its float, as the fallback does, also accepts a step that the decimals
     * reject: in range(0.1, 0.2, 0.10000000000000002), each float is within half a spacing of a decimal that
     * makes the step fit, but 0.10000000000000002 > 0.2 - 0.1. So the decimals are compared exactly, each as a sum
     * of integers times powers of ten, all scaled by the largest power of ten in their denominators. That is null
     * when a scaled value would overflow or an operand taken as exact is below 2^-900, where the products aren't
     * exact.
     *
     * @param int|float $start
     * @param int|float $end
     * @param int|float $magnitude the step magnitude
     * @param bool      $ascending whether $end is above $start
     *
     * @return bool|null
     */
    public static function stepExceedsDecimalSpan(
        int|float $start,
        int|float $end,
        int|float $magnitude,
        bool $ascending
    ): ?bool {
        $operandsAsDecimals = self::roundingAllowances($start, $end, $magnitude, false) !== [];
        // The step minus the span: + step - high + low, each a list of [integer value, power of ten divided by].
        $terms = [];
        $operands = [
            [$magnitude, 1.0, true, true],
            [$ascending ? $end : $start, -1.0, $operandsAsDecimals, false],
            [$ascending ? $start : $end, 1.0, $operandsAsDecimals, false],
        ];
        foreach ($operands as [$operand, $sign, $asDecimal, $isStep]) {
            if (\is_float($operand) && $asDecimal && \floor($operand) !== $operand) {
                // Split into parts of at most 9 digits, which floats hold exactly, also where ints have 32 bits.
                [$significand, $exponent, $digits] = Decimal::shortestDecimal($operand);
                $integer = \str_replace('.', '', $significand);
                $scale = $digits - $exponent;
                $sign = $operand < 0.0 ? -$sign : $sign;
                $length = \strlen($integer);
                if ($length > 9) {
                    $terms[] = [$sign * (float)\substr($integer, 0, $length - 9), $scale - 9];
                    $integer = \substr($integer, $length - 9);
                }
                $terms[] = [$sign * (float)$integer, $scale];
            } elseif (\is_int($operand) && $isStep) {
                // An int step stands for itself, exactly.
                foreach (ExactFloat::intToFloats($operand) as $part) {
                    $terms[] = [$sign * $part, 0];
                }
            } elseif (\is_int($operand)) {
                // An int $start or $end is paired here with a non-integral float or a float beyond the int range,
                // and is converted to float, as for counting: else a step past the span the count sees passes.
                $terms[] = [$sign * (float)$operand, 0];
            } else {
                if ($operand !== 0.0 && \abs($operand) < 2.0 ** -900.0) {
                    return null;
                }
                $terms[] = [$sign * $operand, 0];
            }
        }

        $maxScale = \max(0, ...\array_column($terms, 1));
        $scaled = [];
        foreach ($terms as [$value, $scale]) {
            $parts = [$value];
            // Multiply by 10^($maxScale - $scale), in factors of at most 10^22, which floats hold exactly.
            for ($power = $maxScale - $scale; $power > 0; $power -= 22) {
                $factor = (float)('1e' . \min($power, 22));
                $products = [];
                foreach ($parts as $part) {
                    [$product, $error] = ExactFloat::twoProduct($part, $factor);
                    if (!(\abs($product) < 2.0 ** 990.0) || !\is_finite($error)) {
                        return null;
                    }
                    $products[] = $product;
                    $products[] = $error;
                }
                $parts = ExactFloat::expansion($products);
            }
            \array_push($scaled, ...$parts);
        }

        return ExactFloat::expansionSign(ExactFloat::expansion($scaled)) > 0;
    }

    /**
     * The number of steps of $magnitude in $span, and whether the range lands on $end, for {@see \IterTools\Single::range()}.
     *
     * The range lands on $end when the exact span is a whole number of steps, give or take the operands'
     * rounding: the sum of $operandAllowance, and $stepAllowance for each step taken, since a decimal step that
     * rounds to $magnitude is off by up to that much each time. range(0.0, 1.0, 0.1) lands on 1.0 although 10
     * steps of the float 0.1 exceed the span by 5.6e-17, as that is within 10 · 6.9e-18. Otherwise the steps are
     * those that fit exactly: in range(2.0 ** 53, -0.1, -2.0 ** 53), the span 2^53 + 0.1 rounds to the step in
     * float, but is 0.1 past it, beyond the 6.9e-18 that -0.1 may carry, so the range ends on 0.0. It still ends
     * on $end when the exact last value rounds to it, as computing that value in float may not.
     *
     * $quotient is within 1/8 of the exact quotient, so the nearest whole number of steps is round($quotient),
     * and the exact remainder decides between it and one fewer. Only it can be within the allowance, which is
     * under 3/8 of a step: the operands' share is under a quarter, and fewer than 2^48 steps add under 2^-5.
     *
     * @param list<float> $span             the exact span, as a sum of floats
     * @param int|float   $magnitude        at most the span plus $operandAllowance and $stepAllowance
     * @param float       $quotient         the float quotient of the span and the step, below 2^48
     * @param list<float> $operandAllowance see roundingAllowances()
     * @param float       $stepAllowance    see roundingAllowance()
     * @param float       $end              $end as a float
     * @param float       $endRest          $end minus that float, exactly
     * @param bool        $ascending
     *
     * @return array{int, bool}
     */
    public static function countFloatSteps(
        array $span,
        int|float $magnitude,
        float $quotient,
        array $operandAllowance,
        float $stepAllowance,
        float $end,
        float $endRest,
        bool $ascending
    ): array {
        // $remainder is exactly $span - $steps·$magnitude (TwoProduct of each part of the step).
        $steps = (int)\round($quotient);
        $terms = $span;
        foreach (\is_int($magnitude) ? ExactFloat::intToFloats($magnitude) : [$magnitude] as $part) {
            [$product, $error] = ExactFloat::twoProduct((float)$steps, $part);
            $terms[] = -$product;
            $terms[] = -$error;
        }
        $remainder = ExactFloat::expansion($terms);
        $remainderSign = ExactFloat::expansionSign($remainder);

        // |$remainder| - the allowance
        $terms = $remainderSign < 0 ? ExactFloat::negated($remainder) : $remainder;
        $allowance = [...$operandAllowance, (float)$steps * $stepAllowance];
        foreach ($allowance as $term) {
            $terms[] = -$term;
        }
        if ($steps > 0 && ExactFloat::expansionSign(ExactFloat::expansion($terms)) <= 0) {
            return [$steps, true];
        }
        if ($remainderSign < 0) {
            return [$steps - 1, false];
        }

        // The last value, $end ∓ $remainder, as an offset from the float $end.
        $offset = ExactFloat::expansion([$endRest, ...($ascending ? ExactFloat::negated($remainder) : $remainder)]);

        return [$steps, ExactFloat::roundsTo($end, $offset)];
    }

    /**
     * The rounding an operand of {@see \IterTools\Single::range()} may carry from the decimal it is written as, or 0 where it
     * is taken as exact.
     *
     * An int is exact. For deciding whether the range lands on $end ($forLanding), a non-integral float may carry
     * half its float spacing, and an integral float is exact while it is its own shortest decimal, as 2^53 + 2,
     * 1e16 and 1e22 are, or within 2^53: 1e23 is the float 99999999999999991611392, and is written for 10^23. For
     * deciding whether a step exceeds the span where stepExceedsDecimalSpan() can't, an integral float stands for
     * itself, as range() takes it next to an int operand, and a non-integral float is taken as its shortest decimal:
     * it may carry at most half a unit in that decimal's last digit, which bounds its distance to the float more
     * tightly than half a spacing when the decimal is long, as for 30.664096287827093.
     *
     * @param int|float $operand
     * @param bool      $forLanding
     *
     * @return float
     */
    public static function roundingAllowance(int|float $operand, bool $forLanding): float
    {
        if (\is_int($operand)) {
            return 0.0;
        }
        $halfSpacing = Ulp::halfFloatSpacing($operand);
        if (\floor($operand) === $operand) {
            $exact = !$forLanding || \abs($operand) <= 2.0 ** 53.0 || Decimal::isShortestDecimal($operand);
            return $exact ? 0.0 : $halfSpacing;
        }
        if ($forLanding) {
            return $halfSpacing;
        }

        [, $exponent, $digits] = Decimal::shortestDecimal($operand);
        return \min($halfSpacing, (float)('5e' . ($exponent - $digits - 1)));
    }

    /**
     * The roundingAllowance() of $start and $end, or none when they add up to a quarter of $magnitude or more.
     *
     * Past that, the step is near or below the float spacing and the decimal intent can't be recovered, so the
     * operands are taken as exact: range(1e15 + 0.125, 1e15 + 1.125, 0.3) would otherwise take the span of 1 for 3
     * steps of 0.3 and end on $end. The cut-off is taken on the allowances for landing either way, the larger
     * ones, so a step that range() accepts above the span always lands on $end.
     *
     * For landing, they come with the differenceAllowance() for the rounding of the operands' float difference,
     * while the total stays under a quarter of a step: a step computed from the operands, such as ($end - $start) /
     * $n, carries it. range(-104.81949906807756, 975.2888809333513, 1.158914570816984), with the step computed for
     * 932 steps, ends on $end, although the floats' span is 1.1e-13 short of 932 steps.
     *
     * @param int|float           $start
     * @param int|float           $end
     * @param int|float           $magnitude
     * @param bool                $forLanding
     * @param array{float, float} $floatDifference the operands' float difference and its rounding error (see
     *                                             ExactFloat::twoSum()), for landing
     *
     * @return list<float>
     */
    public static function roundingAllowances(
        int|float $start,
        int|float $end,
        int|float $magnitude,
        bool $forLanding,
        array $floatDifference = [0.0, 0.0]
    ): array {
        $startAllowance = self::roundingAllowance($start, true);
        $endAllowance = self::roundingAllowance($end, true);
        $sum = $startAllowance + $endAllowance;
        if ($sum >= 0.25 * (float)$magnitude) {
            return [];
        }
        if (!$forLanding) {
            return [self::roundingAllowance($start, false), self::roundingAllowance($end, false)];
        }

        $differenceAllowance = self::differenceAllowance($floatDifference, $sum, $magnitude, true);
        return $sum + $differenceAllowance < 0.25 * (float)$magnitude
            ? [$startAllowance, $endAllowance, $differenceAllowance]
            : [$startAllowance, $endAllowance];
    }

    /**
     * The rounding of the operands' float difference that the exact span is allowed, as a step computed from that
     * difference, such as ($end - $start) / $n, carries it.
     *
     * A step that is that quotient in float, for the nearest whole $n, and that roundingAllowance() doesn't take as
     * exact, is allowed all of it: range(0.1, 5.0, (5.0 - 0.1) / 10) ends on 5.0, and range(-0.97, -4.0, -4.0 + 0.97)
     * yields [-0.97, -4.0], although 5.0 - 0.1 and -4.0 + 0.97 round by 3.6e-16 and 4.4e-16, far more than the
     * operands' own rounding. Any other step is allowed it up to twice the operands' allowance, $operandAllowance,
     * which bounds it when the larger operand isn't exact. Past that, the difference has absorbed part of an operand
     * that is exact, which a step that wasn't computed from it doesn't carry: range(2.5, 2.0 ** 53,
     * 3002399751580329.5) doesn't end on 2^53, as its 3 steps fall 1.0 short, beyond their rounding, and 2^53 - 2.5
     * rounds to 9007199254740990.0, which gives the step 3002399751580330.0, not this one. An exact step is taken as
     * written, even when it is that quotient: in range(2.0 ** 53, -0.1, -2.0 ** 53), the span 2^53 + 0.1 rounds to
     * the step.
     *
     * @param array{float, float} $floatDifference  the operands' float difference and its rounding error (see
     *                                              ExactFloat::twoSum())
     * @param float               $operandAllowance the sum of the operands' half float spacings
     * @param int|float           $magnitude        the step magnitude
     * @param bool                $forLanding       as for roundingAllowance(), which decides whether the step is
     *                                              exact
     *
     * @return float
     */
    public static function differenceAllowance(
        array $floatDifference,
        float $operandAllowance,
        int|float $magnitude,
        bool $forLanding
    ): float {
        [$span, $spanError] = $floatDifference;
        $spanError = \abs($spanError);
        $steps = \round($span / (float)$magnitude);
        $isQuotient = $steps >= 1.0 && $span / $steps === (float)$magnitude;

        return $isQuotient && self::roundingAllowance($magnitude, $forLanding) > 0.0
            ? $spanError
            : \min($spanError, 2.0 * $operandAllowance);
    }
}

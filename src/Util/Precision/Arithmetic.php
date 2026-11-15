<?php

declare(strict_types=1);

namespace IterTools\Util\Precision;

/**
 * @internal
 * Exact numeric helpers shared by the library's int/float arithmetic.
 */
final class Arithmetic
{
    /**
     * 2^53: every int from -2^53 to 2^53 converts to float exactly, so <=> compares it with a float exactly.
     */
    public const FLOAT_EXACT_INT_LIMIT = 9007199254740992;

    /**
     * Whether <=> can order the values differently from {@see compare()}: only if there are a float and an
     * int beyond ±2^53, which it compares as floats. Otherwise callers keep <=> and \sort(), which give the
     * same order much faster than a comparator written in PHP. NAN is unordered; callers exclude it.
     *
     * Two checks in C settle most lists before the loop in PHP: an int sum means there is no float (a float,
     * or an overflow, makes it a float), and bounds within ±2^53 mean there is no large int. \max() and \min()
     * compare an int and a float as floats, but rounding is monotonic, so an int beyond ±2^53 still puts its
     * bound at or past ±2^53.
     *
     * @param list<int|float> $values
     *
     * @return bool
     */
    public static function mixesFloatAndLargeInt(array $values): bool
    {
        if ($values === [] || \is_int(\array_sum($values))) {
            return false;
        }
        if (\max($values) < self::FLOAT_EXACT_INT_LIMIT && \min($values) > -self::FLOAT_EXACT_INT_LIMIT) {
            return false;
        }

        $hasFloat = false;
        $hasLargeInt = false;
        foreach ($values as $value) {
            if (\is_float($value)) {
                $hasFloat = true;
            } elseif ($value > self::FLOAT_EXACT_INT_LIMIT || $value < -self::FLOAT_EXACT_INT_LIMIT) {
                $hasLargeInt = true;
            }
        }

        return $hasFloat && $hasLargeInt;
    }

    /**
     * Returns the number as an integer if it is one, or a float that converts to an integer exactly.
     *
     * @param int|float $number
     *
     * @return int|null null for a non-integral float, or one outside the integer range
     */
    public static function toExactInt(int|float $number): ?int
    {
        if (\is_int($number)) {
            return $number;
        }

        // (float) PHP_INT_MIN is exactly -2^63, so the range is [-2^63, 2^63). The upper bound is
        // derived from it because (float) PHP_INT_MAX rounds up to 2^63, which is out of range.
        // INF, -INF and NAN fail these comparisons.
        if ($number >= (float) \PHP_INT_MIN && $number < -(float) \PHP_INT_MIN && \floor($number) === $number) {
            return (int) $number;
        }

        return null;
    }

    /**
     * Compares two numbers by their exact values, returning -1, 0 or 1 as the spaceship operator does.
     *
     * PHP compares an int with a float by converting the int to float, which rounds an int beyond ±2^53:
     * 2^53 + 1 <=> 2.0^53 is 0. NAN is unordered; callers exclude it.
     *
     * @param int|float $a
     * @param int|float $b
     *
     * @return int
     */
    public static function compare(int|float $a, int|float $b): int
    {
        if (\is_int($a) && \is_float($b)) {
            return self::compareIntToFloat($a, $b);
        }
        if (\is_float($a) && \is_int($b)) {
            return -self::compareIntToFloat($b, $a);
        }

        return $a <=> $b;
    }

    /**
     * Compares two values as the spaceship operator does, except that an int and a float are compared by
     * their exact values ({@see compare()}).
     *
     * PHP evaluates $a < $b as ($a <=> $b) < 0 and $a > $b as ($b <=> $a) < 0, so callers replacing an
     * operator keep its result for every other pair of values by writing it the same way.
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return int
     */
    public static function compareValues(mixed $a, mixed $b): int
    {
        if ((\is_int($a) || \is_float($a)) && (\is_int($b) || \is_float($b))) {
            return self::compare($a, $b);
        }

        return $a <=> $b;
    }

    /**
     * \min() of two values, except that an int and a float are compared by their exact values. Every other
     * pair, and an exact tie, goes to \min(), whose choice on a tie depends on the PHP version.
     *
     * @template T
     *
     * @param T $a
     * @param T $b
     *
     * @return T
     */
    public static function min(mixed $a, mixed $b): mixed
    {
        if ((\is_int($a) || \is_float($a)) && (\is_int($b) || \is_float($b))) {
            $order = self::compare($a, $b);
            if ($order !== 0) {
                return $order < 0 ? $a : $b;
            }
        }

        return \min($a, $b);
    }

    /**
     * \max() of two values, except that an int and a float are compared by their exact values. Every other
     * pair, and an exact tie, goes to \max(), whose choice on a tie depends on the PHP version.
     *
     * @template T
     *
     * @param T $a
     * @param T $b
     *
     * @return T
     */
    public static function max(mixed $a, mixed $b): mixed
    {
        if ((\is_int($a) || \is_float($a)) && (\is_int($b) || \is_float($b))) {
            $order = self::compare($a, $b);
            if ($order !== 0) {
                return $order > 0 ? $a : $b;
            }
        }

        return \max($a, $b);
    }

    /**
     * Rounding to float is monotonic, so (float) $int <=> $float is right whenever it finds the two unequal.
     * Equal, the float is an integer, compared in the int domain, unless it is 2^63, just past PHP_INT_MAX.
     */
    private static function compareIntToFloat(int $int, float $float): int
    {
        $order = (float) $int <=> $float;
        if ($order !== 0) {
            return $order;
        }

        $exact = self::toExactInt($float);

        return $exact === null ? -1 : $int <=> $exact;
    }

    /**
     * Computes the correctly rounded midpoint of two numbers without overflowing on extreme magnitudes.
     *
     * Equal values short-circuit, and so does an infinity, which no finite partner can move. Beyond
     * that, the midpoint is rounded once, so it is the float nearest the true midpoint (ties to even):
     *
     * - Two integers (or an integral float within the integer range, which converts exactly): the
     *   sum is taken in integer arithmetic, and when it overflows, the floor of the midpoint is.
     *   An even sum gives an exact integer, and an odd one gives a half, which is converted to
     *   a float exactly once. Forms like $lo + ($hi - $lo) / 2 round twice past 2^53, first
     *   converting $lo and then adding the half, which turns …993.5 into …992 instead of …994.
     *   A float operand makes the result a float, as plain arithmetic would.
     * - Floats: ($lo + $hi) / 2 rounds once (in the sum; halving is exact unless the sum is so
     *   small that it was itself exact). Past PHP_FLOAT_MAX / 2 the sum could overflow, so each
     *   operand is halved instead, except one too small to halve exactly, which is added whole.
     *
     * An integer beyond ±2^53 paired with a non-integral float or a float beyond the integer range
     * is converted to a float before averaging. This can lose precision, and cancellation can
     * amplify the error substantially: on 64-bit PHP, [PHP_INT_MIN + 1, -(float) PHP_INT_MIN]
     * gives 0.0 instead of 0.5. Rounding it correctly needs an error-free three-term sum.
     *
     * @param int|float $lo
     * @param int|float $hi
     *
     * @return int|float
     */
    public static function midpoint(int|float $lo, int|float $hi): int|float
    {
        // The midpoint of a value and itself is that value. Taken first so that two identical
        // infinities return that infinity rather than the NAN that INF - INF would give below.
        // -0.0 === 0.0 holds too, so zeros are summed instead: -0.0 + 0.0 is 0.0 in either order,
        // and only two -0.0 give -0.0, as ($lo + $hi) / 2 does.
        if ($lo === $hi) {
            /** @psalm-suppress InvalidOperand */
            return $lo == 0 ? $lo + $hi : $lo;
        }

        // An infinity paired with a finite value is that infinity, and opposite infinities are NAN;
        // the plain sum gives both.
        if (\is_infinite((float) $lo) || \is_infinite((float) $hi)) {
            /** @psalm-suppress InvalidOperand */
            return $lo + $hi;
        }

        $intLo = Arithmetic::toExactInt($lo);
        $intHi = Arithmetic::toExactInt($hi);

        if ($intLo !== null && $intHi !== null) {
            $midpoint = self::integerMidpoint($intLo, $intHi);

            return (\is_float($lo) || \is_float($hi)) ? (float) $midpoint : $midpoint;
        }

        $floatLo = (float) $lo;
        $floatHi = (float) $hi;

        if (\abs($floatLo) <= \PHP_FLOAT_MAX / 2.0 && \abs($floatHi) <= \PHP_FLOAT_MAX / 2.0) {
            return ($floatLo + $floatHi) / 2.0;
        }

        // At least one operand is huge, so the sum could overflow. Halving a normal float is
        // exact, but halving one below 2 * PHP_FLOAT_MIN can drop its lowest bit.
        if (\abs($floatLo) < 2.0 * \PHP_FLOAT_MIN) {
            return $floatLo + $floatHi / 2.0;
        }
        if (\abs($floatHi) < 2.0 * \PHP_FLOAT_MIN) {
            return $floatLo / 2.0 + $floatHi;
        }

        return $floatLo / 2.0 + $floatHi / 2.0;
    }

    /**
     * Computes the correctly rounded midpoint of two integers.
     *
     * The midpoint is an integer when the sum is even, and otherwise lies halfway between two
     * integers. A sum within the integer range (always, for opposite signs) is divided directly:
     * PHP's int division gives the exact integer, or converts the exact odd sum to a float once
     * and halves it exactly.
     *
     * An overflowing sum means both values share a sign. With 32-bit integers both are within
     * ±2^53, so they convert to floats exactly, their float sum is exact, and halving it is exact.
     * With 64-bit integers the midpoint's magnitude is at least 2^62, where floats are at least 512
     * apart and the midway points between them are even. The true midpoint m + 0.5 (m its floor)
     * is never one of those midway points, so it rounds like whichever of m and m + 1 is odd, and
     * that one, m | 1, converts with a single rounding.
     *
     * @param int $lo
     * @param int $hi
     *
     * @return int|float
     */
    public static function integerMidpoint(int $lo, int $hi): int|float
    {
        $overflows = ($lo > 0 && $hi > \PHP_INT_MAX - $lo) || ($lo < 0 && $hi < \PHP_INT_MIN - $lo);
        if (!$overflows) {
            return ($lo + $hi) / 2;
        }

        if (\abs($lo) <= 9007199254740992 && \abs($hi) <= 9007199254740992) {
            return ((float) $lo + (float) $hi) / 2.0;
        }

        if ($lo > $hi) {
            [$lo, $hi] = [$hi, $lo];
        }

        // Same sign, so the span cannot overflow; it is non-negative, so intdiv floors it.
        $span  = $hi - $lo;
        $floor = $lo + \intdiv($span, 2);

        return $span % 2 === 0 ? $floor : (float) ($floor | 1);
    }

    /**
     * Computes the difference of two integers: exact when it is within the integer range, and
     * otherwise correctly rounded to a float.
     *
     * PHP's overflowing int subtraction converts each operand to a float and subtracts the floats.
     * Operands within ±2^53 convert exactly, so only the subtraction rounds. Otherwise (64-bit
     * integers) the difference d is at least 2^63 in magnitude. It is 2q + r, with
     * q = (a >> 1) - (b >> 1), which cannot overflow, and r = (a & 1) - (b & 1) in {-1, 0, 1}, so
     * |q| >= 2^62, where floats are at least 1024 apart and the midway points between them are even.
     * d / 2 is q, or for odd d halfway between two integers, which is never one of those midway
     * points, so it rounds like whichever of the two is odd, as in {@see integerMidpoint()}. That
     * integer converts with a single rounding, and doubling it is exact.
     *
     * @param int $a
     * @param int $b
     *
     * @return int|float
     */
    public static function integerDifference(int $a, int $b): int|float
    {
        $overflows = ($b < 0 && $a > \PHP_INT_MAX + $b) || ($b > 0 && $a < \PHP_INT_MIN + $b);
        if (!$overflows) {
            return $a - $b;
        }

        if (\abs($a) <= 9007199254740992 && \abs($b) <= 9007199254740992) {
            return (float) $a - (float) $b;
        }

        $half      = ($a >> 1) - ($b >> 1);
        $remainder = ($a & 1) - ($b & 1);
        if ($remainder === 0) {
            return 2.0 * (float) $half;
        }

        // d / 2 is $half + 0.5, or $half - 0.5 = ($half - 1) + 0.5.
        $below = $remainder > 0 ? $half : $half - 1;

        return 2.0 * (float) ($below | 1);
    }
}

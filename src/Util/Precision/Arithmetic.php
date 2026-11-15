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
}

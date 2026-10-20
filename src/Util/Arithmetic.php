<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 * Exact numeric helpers shared by the library's int/float arithmetic.
 */
final class Arithmetic
{
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
     * Half the float spacing at $x: 2^-53 times the largest power of two not above |x|.
     *
     * Bounding it by PHP_FLOAT_EPSILON / 2 * |x| instead is up to twice as loose, which makes
     * {@see \IterTools\Single::range()} accept range(1e15, 1e15 + 3.125, 1.1) as 3 steps when no
     * operands that round to these floats give that.
     * Subnormals are taken as exact: half their spacing, 2^-1075, isn't representable.
     *
     * @param float $x
     *
     * @return float
     */
    public static function halfFloatSpacing(float $x): float
    {
        $x = \abs($x);
        if ($x < \PHP_FLOAT_MIN) {
            return 0.0;
        }
        // log() can be off by one at powers of two; correct the power to bracket $x. Near
        // PHP_FLOAT_MAX it rounds up to 1024, and 2^1024 overflows to INF, so cap it first.
        $power = 2.0 ** \min(\floor(\log($x, 2)), 1023.0);
        if ($power > $x) {
            $power /= 2.0;
        } elseif ($power * 2.0 <= $x) {
            $power *= 2.0;
        }
        return $power * (\PHP_FLOAT_EPSILON / 2.0);
    }
}

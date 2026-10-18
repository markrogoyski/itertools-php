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

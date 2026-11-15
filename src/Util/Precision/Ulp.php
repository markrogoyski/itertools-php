<?php

declare(strict_types=1);

namespace IterTools\Util\Precision;

/**
 * @internal
 * Float neighbours, bit patterns and spacing (units in the last place).
 */
final class Ulp
{
    /**
     * The adjacent float above or below a finite one, through its bit pattern (64-bit PHP only).
     *
     * @param float $x
     * @param bool  $up
     *
     * @return float
     */
    public static function nextFloat(float $x, bool $up): float
    {
        if ($x === 0.0) {
            $tiny = \PHP_FLOAT_MIN * \PHP_FLOAT_EPSILON;

            return $up ? $tiny : -$tiny;
        }

        // A float's magnitude rises with its bit pattern read as an integer, and the sign bit is its own.
        $bits = self::floatBits($x) + (($x > 0.0) === $up ? 1 : -1);

        /** @var array{1: float} $unpacked */
        $unpacked = \unpack('d', \pack('q', $bits));

        return $unpacked[1];
    }

    /**
     * Of two adjacent floats, the one whose last significand bit is 0.
     *
     * @param float $a
     * @param float $b
     *
     * @return float
     */
    public static function evenOf(float $a, float $b): float
    {
        return (self::floatBits($a) & 1) === 0 ? $a : $b;
    }

    /**
     * A float's bit pattern as an integer (64-bit PHP only).
     *
     * @param float $x
     *
     * @return int
     */
    public static function floatBits(float $x): int
    {
        /** @var array{1: int} $unpacked */
        $unpacked = \unpack('q', \pack('d', $x));

        return $unpacked[1];
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

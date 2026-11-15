<?php

declare(strict_types=1);

namespace IterTools\Util\Precision;

/**
 * @internal
 * Correctly rounded interpolation between sorted values, for the percentile and quantile reductions.
 */
final class Interpolation
{
    /**
     * The most moves of a correctly rounded interpolation's candidate before it is a logic error: it starts
     * within an ulp or so and moves at most a couple of times.
     */
    private const MAX_ROUNDING_MOVES = 8;

    /**
     * Locates the R-7 rank h = $last * $fraction / $scale between two indices of the sorted values.
     *
     * Rounding (n - 1) * p, p / 100, or a quantile scaled to a percentile, can put a rank that is just off
     * an integer onto it, or one that is an integer just off it, so neither the floor nor the test for an
     * integer can use a rounded rank. Both come from the exact product $last * $fraction, held as the
     * unevaluated sum $hi + $lo (Dekker's TwoProduct, exact here even for a subnormal product, as $last is an
     * integer): $scale * $j is exact, as $last * $scale is below 2^53, and so is $hi - $scale * $j (Sterbenz,
     * or $j = 0), so the remainder's sign and zero are too. Past that bound S * j rounds, and the weights, and
     * even which is the smaller, are wrong; a percentile's caller would need over 2^53 / 100 (about 9 * 10^13)
     * values in memory to get there.
     *
     * The weights are returned in units of 1/S of the rank, for the scale S = $scale (100 for a percentile,
     * 1 for a quantile): $above = S * (h - j), zero exactly when h is an integer, and $below =
     * S * (j + 1 - h). The smaller one, which an interpolation from the nearer value multiplies, is correctly
     * rounded, even when it is far below 1. The larger is only within an ulp: where $below is the larger, it
     * can round twice.
     *
     * For {@see Interpolation::interpolateIntegers()}, the smaller weight is also returned exactly, as floats whose
     * unevaluated sum it is, with whether it is $above (so the interpolation is from $lo), decided on the
     * exact weights: past 2^53 a weight's rounding can decide a tie, and when both round to S / 2, an offset
     * taken from the wrong end can exceed half a span past PHP_INT_MAX.
     *
     * @param int   $last     the last index, $count - 1, with $last * $scale below 2^53
     * @param float $fraction in the inclusive range [0, $scale]
     * @param float $scale    100.0 for a percentile, 1.0 for a quantile
     *
     * @return array{int, float, float, list<float>, bool} [j, $above, $below, exact smaller weight, from $lo]
     */
    public static function percentileRank(int $last, float $fraction, float $scale): array
    {
        $a    = (float) $last;
        $half = $scale / 2.0;

        // $last is below 2^53 and $fraction at most S, so no product here comes near overflow.
        [$hi, $lo] = ExactFloat::twoProduct($a, $fraction);

        // $hi / S can round up onto $j + 1 only from below it, never down, so $j is right or one too high.
        $j = (int) \floor($hi / $scale);
        $x = $hi - $scale * (float) $j;
        [$d, $dError] = ExactFloat::twoSum($x, $lo);

        if ($d < 0) {
            return [$j - 1, $scale + $d, -$d, [-$d, -$dError], false];
        }

        // S - $x is exact when $x >= S / 2, where $below is the smaller weight.
        $below = ($scale - $x) - $lo;

        // $d + $dError is the exact weight, with $dError below half an ulp of $d: it is at most S / 2 exactly
        // when $d is below S / 2, or is S / 2 with a correction that is not positive.
        if ($d < $half || ($d === $half && $dError <= 0.0)) {
            return [$j, $d, $below, [$d, $dError], true];
        }
        [$b, $bError] = ExactFloat::twoSum($scale, -$d);

        return [$j, $d, $below, [$b, $bError, -$dError], false];
    }

    /**
     * Linearly interpolates between two numbers without overflowing on extreme magnitudes.
     *
     * The weights are in units of 1/S, for the scale S = $scale (100 for a percentile, 1 for a quantile):
     * $above = S * (h - j) and $below = S - $above, both positive. The point is reached from the nearer
     * value, $lo + ($above / S) * ($hi - $lo) or $hi - ($below / S) * ($hi - $lo), whose small weight is the
     * accurate one and keeps its digits where its complement would round to 1. This direct form returns the
     * value exactly when the two are equal, which the weighted form does not (0.1 * 0.7 + 0.1 * 0.3 is not
     * 0.1). A weight below S * PHP_FLOAT_MIN multiplies the span before the division by S, which would
     * otherwise underflow.
     *
     * The weighted form $lo * $below / S + $hi * $above / S is used only when the span $hi - $lo is
     * unrepresentable. Being a convex combination it lies between $lo and $hi and cannot overflow. A
     * weight that underflows there becomes the smallest positive float, so it still carries an infinite
     * value's infinity; next to a value past PHP_FLOAT_MAX / 2, anything it adds is lost to rounding.
     *
     * Weights of exactly S / 2 are delegated to {@see Arithmetic::midpoint()}: that is the one interpolation
     * computable exactly for integer bounds, and delegating keeps toPercentile($data, 50) and
     * toQuantile($data, 0.5) identical to toMedian($data) rather than merely close to it.
     *
     * @param int|float   $lo
     * @param int|float   $hi
     * @param float       $above      S * the weight on $hi, in (0, S)
     * @param float       $below      S * the weight on $lo, in (0, S)
     * @param list<float> $nearWeight the smaller of $above and $below exactly, as an unevaluated sum
     * @param bool        $fromLo     whether $nearWeight is $above, from the exact weights
     * @param float       $scale      S: 100.0 for a percentile, 1.0 for a quantile
     *
     * @return int|float
     */
    public static function interpolate(
        int|float $lo,
        int|float $hi,
        float $above,
        float $below,
        array $nearWeight,
        bool $fromLo,
        float $scale
    ): int|float {
        $half = $scale / 2.0;

        // Past 2^53 an integer endpoint would round on conversion to float before its offset is added, rounding
        // twice: 2^53 + 1 to 2^53, then 2^53 + 1.75 to 2^53. The offset's integer part is added exactly instead.
        // This takes a weight that merely rounds to S / 2 too, which can be a tie the midpoint would break wrongly.
        $loInt = Arithmetic::toExactInt($lo);
        $hiInt = Arithmetic::toExactInt($hi);
        if (
            $loInt !== null && $hiInt !== null
            && ($loInt < -Arithmetic::FLOAT_EXACT_INT_LIMIT || $hiInt > Arithmetic::FLOAT_EXACT_INT_LIMIT)
            && $nearWeight !== [$half, 0.0]
        ) {
            return self::interpolateIntegers($loInt, $hiInt, $nearWeight, $fromLo, $scale);
        }

        if ($above === $half) {
            return Arithmetic::midpoint($lo, $hi);
        }

        /** @psalm-suppress InvalidOperand */
        $span = $hi - $lo;

        if (\is_finite((float) $span)) {
            if ($above <= $below) {
                /** @psalm-suppress InvalidOperand */
                return $above < $scale * \PHP_FLOAT_MIN ? $lo + $above * $span / $scale : $lo + $above / $scale * $span;
            }

            /** @psalm-suppress InvalidOperand */
            return $below < $scale * \PHP_FLOAT_MIN ? $hi - $below * $span / $scale : $hi - $below / $scale * $span;
        }

        $toHi = \max($above / $scale, \PHP_FLOAT_MIN * \PHP_FLOAT_EPSILON);
        $toLo = \max($below / $scale, \PHP_FLOAT_MIN * \PHP_FLOAT_EPSILON);

        /** @psalm-suppress InvalidOperand */
        return $lo * $toLo + $hi * $toHi;
    }

    /**
     * Interpolates between two integers, correctly rounded.
     *
     * S times the result, for the scale S = $scale (100 for a percentile, 1 for a quantile), is held
     * exactly, as a nonoverlapping expansion (a list of floats whose unevaluated sum it is, see
     * {@see ExactFloat::growExpansion()}): S * $lo + w * $hi - w * $lo from $lo, or
     * S * $hi - w * $hi + w * $lo from $hi, for the exact smaller weight w. Each endpoint is split into its
     * float and integral rest ({@see ExactFloat::intToFloats()}), and each product taken as a float and its
     * error ({@see ExactFloat::twoProduct()}), which is exact as one factor is an integer: no span is formed,
     * so none can overflow. The value is then rounded once, by {@see Interpolation::roundExpansionDividedBy()}, so
     * no intermediate rounding can cancel into the result (-1 + 0.9 is not -0.1), break a tie or make a
     * false one.
     *
     * @param int         $lo
     * @param int         $hi
     * @param list<float> $weight S * the weight of the nearer endpoint's partner, at most S / 2, exactly as
     *                            an unevaluated sum
     * @param bool        $fromLo whether $lo is the nearer endpoint, so $weight is the weight on $hi
     * @param float       $scale  S: 100.0 for a percentile, 1.0 for a quantile
     *
     * @return float
     */
    public static function interpolateIntegers(int $lo, int $hi, array $weight, bool $fromLo, float $scale): float
    {
        // The rests are integral and at most 2^10 in magnitude, so S times them is exact.
        [$lo1, $lo2] = ExactFloat::intToFloats($lo);
        [$hi1, $hi2] = ExactFloat::intToFloats($hi);
        [$near1, $near2, $sign] = $fromLo ? [$lo1, $lo2, 1.0] : [$hi1, $hi2, -1.0];

        // Every product here multiplies a factor of at most S (100 for a percentile, 1 for a quantile) by one
        // below 2^64, one of them integer-valued: far from overflow, so each TwoProduct is exact, even for a
        // subnormal product.
        $terms   = ExactFloat::twoProduct($scale, $near1);
        $terms[] = $scale * $near2;
        foreach ($weight as $w) {
            foreach ([$hi1, $hi2] as $x) {
                \array_push($terms, ...ExactFloat::twoProduct($sign * $w, $x));
            }
            foreach ([$lo1, $lo2] as $x) {
                \array_push($terms, ...ExactFloat::twoProduct(-$sign * $w, $x));
            }
        }

        $scaled = [];
        foreach ($terms as $term) {
            $scaled = ExactFloat::growExpansion($scaled, $term);
        }

        return self::roundExpansionDividedBy($scaled, $scale);
    }

    /**
     * Rounds the value of a nonoverlapping expansion, divided by the scale S = $scale, to the nearest float
     * (ties to even).
     *
     * The quotient of the expansion's approximate sum is within a few ulps. Each neighbouring midpoint m is
     * then compared exactly with the value, through the sign of the expansion minus S * m (that is,
     * S / 2 * the candidate + S / 2 * its neighbour, as exact products), and the candidate moves one float
     * towards the value while it lies past one.
     *
     * The expansion must be nonadjacent, as {@see ExactFloat::growExpansion()} keeps one built up from
     * empty: no two components are adjacent bit ranges. Then the approximate sum is within an ulp, and the
     * candidate moves at most a couple of times. Merely nonoverlapping components can sum far off
     * ([2^-54, -(1 - 2^-53), 1] sums to 2^-52, not 2^-54 + 2^-53), and the loop would then take
     * astronomically many steps; so would a midpoint comparison at the wrong scale. The moves are therefore
     * bounded, and running out of them is a logic error rather than an endless loop.
     *
     * An exact zero gives 0.0, as $x - $x does; a negative value that underflows keeps its sign, -0.0.
     *
     * The value must also be exactly zero or have |value / S| above 2^-1021: below that, halving a float for
     * the S / 2 midpoint in {@see Interpolation::compareWithMidpoint()} can round across the value. An interpolation
     * between integers is always in this domain. Outside it the result can be wrong: [2^-1074] divided by 1.0
     * gives 2^-1073.
     *
     * @param list<float> $expansion nonadjacent, in increasing magnitude, of magnitude below 2^70 and either
     *                               exactly zero or above S * 2^-1021
     * @param float       $scale     S: 100.0 for a percentile, 1.0 for a quantile
     *
     * @return float
     *
     * @throws \LogicException if the candidate is still moving after the bounded number of moves
     */
    public static function roundExpansionDividedBy(array $expansion, float $scale): float
    {
        $approx = 0.0;
        foreach ($expansion as $component) {
            $approx += $component;
        }
        // -0.0 === 0.0, so both zeros become 0.0. A nonadjacent expansion with a nonzero value has a nonzero
        // approximate sum, so only an exact zero is replaced.
        $candidate = $approx === 0.0 ? 0.0 : $approx / $scale;

        // Each pass either returns or moves the candidate one float; a couple of moves suffice.
        for ($moves = 0; $moves < self::MAX_ROUNDING_MOVES; ++$moves) {
            $up = Ulp::nextFloat($candidate, true);
            $aboveMidpoint = self::compareWithMidpoint($expansion, $candidate, $up, $scale);
            if ($aboveMidpoint > 0) {
                $candidate = $up;
                continue;
            }
            if ($aboveMidpoint === 0) {
                return Ulp::evenOf($candidate, $up);
            }

            $down = Ulp::nextFloat($candidate, false);
            $belowMidpoint = self::compareWithMidpoint($expansion, $candidate, $down, $scale);
            if ($belowMidpoint < 0) {
                $candidate = $down;
                continue;
            }
            if ($belowMidpoint === 0) {
                return Ulp::evenOf($candidate, $down);
            }

            return $candidate;
        }

        throw new \LogicException('Rounding an interpolated value did not converge.');
    }

    /**
     * The sign of the value of $expansion minus S times the midpoint of two adjacent floats, exactly, for the
     * scale S = $scale (100 for a percentile, 1 for a quantile).
     *
     * S / 2 times a float is exact as a product and its error: 50 is an integer, and 0.5 halves, which is
     * exact unless the half is subnormal, for a float below 2^-1021 in magnitude, where it is off by at most
     * 2^-1075. An interpolation between integers past 2^53 is then either exactly zero, whose candidate 0.0
     * is already its rounding (a false tie with 2^-1074 still gives 0.0), or of magnitude above 2^-1021, so
     * at least 2^-1074 past any midpoint of such small floats, a margin the error cannot cross.
     *
     * @param list<float> $expansion nonoverlapping, in increasing magnitude
     * @param float       $a         a candidate, below 2^64 in magnitude
     * @param float       $b         a float adjacent to $a
     * @param float       $scale     S: 100.0 for a percentile, 1.0 for a quantile
     *
     * @return int -1, 0 or 1
     */
    private static function compareWithMidpoint(array $expansion, float $a, float $b, float $scale): int
    {
        // Each product multiplies S / 2 (0.5 or 50) by a float below 2^64 in magnitude: far from overflow.
        $minusHalf = -$scale / 2.0;
        foreach ([...ExactFloat::twoProduct($minusHalf, $a), ...ExactFloat::twoProduct($minusHalf, $b)] as $term) {
            $expansion = ExactFloat::growExpansion($expansion, $term);
        }

        return ExactFloat::expansionSign($expansion);
    }
}

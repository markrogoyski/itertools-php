<?php

declare(strict_types=1);

namespace IterTools\Util\Precision;

/**
 * @internal
 * Error-free float operations and nonoverlapping expansions: exact sums, products and int spans as floats.
 */
final class ExactFloat
{
    /**
     * Whether $float plus the sum of $offset, an expansion(), rounds to $float (to nearest, ties to even).
     *
     * @param float       $float
     * @param list<float> $offset
     *
     * @return bool
     */
    public static function roundsTo(float $float, array $offset): bool
    {
        $offsetSign = self::expansionSign($offset);
        if ($offsetSign === 0) {
            return true;
        }
        $halfSpacing = Ulp::halfFloatSpacing($float);
        // Toward 0 from a power of two, the next float is half a spacing closer.
        $towardZero = ($offsetSign > 0) === ($float < 0.0);
        $halfGap = $towardZero && \abs($float) === $halfSpacing * 2.0 ** 53.0 ? $halfSpacing / 2.0 : $halfSpacing;

        $terms = $offsetSign > 0 ? $offset : self::negated($offset);
        $terms[] = -$halfGap;
        $excess = self::expansionSign(self::expansion($terms));

        return $excess < 0
            || ($excess === 0 && $halfSpacing > 0.0 && \fmod(\abs($float) / (2.0 * $halfSpacing), 2.0) === 0.0);
    }

    /**
     * $high - $low, exactly, as a sum of floats. Past the int range, it is 2^63 plus an excess that is an int.
     *
     * @param int $low
     * @param int $high at least $low
     *
     * @return list<float>
     */
    public static function exactIntSpan(int $low, int $high): array
    {
        // The span fits in an int unless $low is negative and $high - $low passes PHP_INT_MAX.
        if ($low >= 0 || $high <= \PHP_INT_MAX + $low) {
            return self::intToFloats($high - $low);
        }

        return [-(float)\PHP_INT_MIN, ...self::intToFloats($high - ($low - \PHP_INT_MIN))];
    }

    /**
     * $n, exactly, as its float and the rest.
     *
     * Any int is accepted, PHP_INT_MAX included, whose float rounds up to 2^63 on 64-bit PHP. The rest is
     * integral and at most 2^10 in magnitude, so a small integer times it is exact; on 32-bit PHP it is 0.0.
     *
     * @param int $n
     *
     * @return array{float, float}
     */
    public static function intToFloats(int $n): array
    {
        $float = (float)$n;
        // (float) PHP_INT_MAX rounds up to 2^63, which isn't an int.
        $rest = $float >= -(float)\PHP_INT_MIN ? ($n - \PHP_INT_MAX) - 1 : $n - (int)$float;

        return [$float, (float)$rest];
    }

    /**
     * @param list<float> $terms
     *
     * @return list<float>
     */
    public static function negated(array $terms): array
    {
        $negated = [];
        foreach ($terms as $term) {
            $negated[] = -$term;
        }

        return $negated;
    }

    /**
     * $a + $b, exactly, as the float sum and its rounding error (Knuth's TwoSum).
     *
     * Exact for finite operands whose float sum does not overflow, under round-to-nearest (PHP's only rounding
     * mode). The operands need no order of magnitude. A difference $high - $low is twoSum(-$low, $high), signed
     * zeros included.
     *
     * @param float $a
     * @param float $b
     *
     * @return array{float, float}
     */
    public static function twoSum(float $a, float $b): array
    {
        $sum = $a + $b;
        $back = $sum - $a;
        $error = ($a - ($sum - $back)) + ($b - $back);

        return [$sum, $error];
    }

    /**
     * $a·$b, exactly, as the float product and its rounding error (Dekker's TwoProduct, with Veltkamp's split).
     *
     * Exact unless the product overflows or underflows, or a factor is past 2^996, where the split overflows.
     * It is also exact for a subnormal product when one factor is integer-valued and no intermediate product
     * (the split multiplications, the product, the partial products) overflows: the partial products are then
     * multiples of the other factor's ulp, which even a subnormal result can hold. When one factor is a power
     * of two and the product is normal, the product is exact and the error is 0.
     *
     * @param float $a
     * @param float $b
     *
     * @return array{float, float}
     */
    public static function twoProduct(float $a, float $b): array
    {
        $product = $a * $b;
        $split = 134217729.0 * $a;
        $aHi = $split - ($split - $a);
        $aLo = $a - $aHi;
        $split = 134217729.0 * $b;
        $bHi = $split - ($split - $b);
        $bLo = $b - $bHi;
        $error = (($aHi * $bHi - $product) + $aHi * $bLo + $aLo * $bHi) + $aLo * $bLo;

        return [$product, $error];
    }

    /**
     * The exact sum of $terms, which must not overflow, as a nonoverlapping expansion: floats in order of
     * magnitude, each below the lowest bit of the next (Shewchuk's Grow-Expansion with TwoSum, zeros dropped).
     *
     * The terms are finite and may come in any order and overlap: expansion([1.0, 1.0]) is [2.0]. Zero terms
     * are skipped, so no terms, or only zeros, give []. Neither the exact sum nor any partial sum may overflow.
     * Under round-to-nearest the result is also nonadjacent: no two components are adjacent bit ranges. After
     * cancellation it can end in 0.0.
     *
     * @param list<float> $terms finite
     *
     * @return list<float>
     */
    public static function expansion(array $terms): array
    {
        /** @var list<float> $expansion */
        $expansion = [];
        foreach ($terms as $sum) {
            if ($sum === 0.0) {
                continue;
            }
            $grown = [];
            foreach ($expansion as $component) {
                $partial = $sum + $component;
                $componentPart = $partial - $sum;
                $error = ($sum - ($partial - $componentPart)) + ($component - $componentPart);
                if ($error !== 0.0) {
                    $grown[] = $error;
                }
                $sum = $partial;
            }
            $grown[] = $sum;
            $expansion = $grown;
        }

        return $expansion;
    }

    /**
     * $expansion + $b, exactly, as a nonoverlapping expansion (Shewchuk's Grow-Expansion with TwoSum, zero
     * rounding errors dropped).
     *
     * $expansion must be nonoverlapping, its components in increasing magnitude, as expansion() and this
     * method build one. $b is any finite float, and the sum must not overflow. Unlike in expansion(), a zero
     * $b is kept: growExpansion([], 0.0) is [0.0]. The result is again nonoverlapping and in increasing
     * magnitude, so its largest nonzero component carries the sign of the sum (see expansionSign()), and it
     * can end in 0.0 after cancellation. Under round-to-nearest it is also nonadjacent when the input is, as
     * an expansion grown from empty always is (Shewchuk's Grow-Expansion theorem).
     *
     * @param list<float> $expansion nonoverlapping, in increasing magnitude
     * @param float       $b         finite
     *
     * @return list<float>
     */
    public static function growExpansion(array $expansion, float $b): array
    {
        $grown = [];
        $sum = $b;
        foreach ($expansion as $component) {
            $partial = $sum + $component;
            $componentPart = $partial - $sum;
            $error = ($sum - ($partial - $componentPart)) + ($component - $componentPart);
            if ($error !== 0.0) {
                $grown[] = $error;
            }
            $sum = $partial;
        }
        $grown[] = $sum;

        return $grown;
    }

    /**
     * The sign of the sum of an expansion(): that of its largest nonzero component.
     *
     * @param list<float> $expansion nonoverlapping, in increasing magnitude; zero components are allowed
     *
     * @return int -1, 0 or 1
     */
    public static function expansionSign(array $expansion): int
    {
        for ($i = \count($expansion) - 1; $i >= 0; --$i) {
            if ($expansion[$i] !== 0.0) {
                return $expansion[$i] > 0.0 ? 1 : -1;
            }
        }

        return 0;
    }
}

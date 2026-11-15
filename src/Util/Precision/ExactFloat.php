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
     * $a·$b, exactly, as the float product and its rounding error (Dekker's TwoProduct, with Veltkamp's split).
     *
     * Exact unless the product overflows or underflows, or a factor is past 2^996, where the split overflows.
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
     * @param list<float> $terms
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
     * The sign of the sum of an expansion(): that of its largest nonzero component.
     *
     * @param list<float> $expansion
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

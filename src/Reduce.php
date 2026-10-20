<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\Arithmetic;
use IterTools\Util\MeanAccumulator;
use IterTools\Util\NoValueMonad;
use IterTools\Util\ValueCounter;

final class Reduce
{
    /**
     * Reduces given collection like array_reduce() function.
     *
     * But unlike array_reduce(), it works with all iterable types.
     *
     * @template T
     * @param iterable<mixed> $data
     * @param callable        $reducer
     * @param T               $initialValue
     *
     * @return T
     */
    public static function toValue(iterable $data, callable $reducer, mixed $initialValue = null): mixed
    {
        /** @var mixed $carry */
        $carry = $initialValue;

        foreach ($data as $datum) {
            /** @var mixed $datum */
            $carry = $reducer($carry, $datum);
        }

        /** @var T */
        return $carry;
    }

    /**
     * Reduces given iterable to its min value.
     *
     * Optional callable param $compareBy must return comparable value.
     * If $compareBy is not provided then items of given collection must be comparable.
     *
     * If several elements tie on the $compareBy value, returns the first of them.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<mixed> $data
     * @param callable|null   $compareBy (optional) function to extract comparable value from element. Ex: $item->getSomeValue()
     *
     * @return mixed|null
     */
    public static function toMin(iterable $data, ?callable $compareBy = null): mixed
    {
        if ($compareBy !== null) {
            /** @var mixed|NoValueMonad $result */
            $result = static::toValue(
                $data,
                static function (mixed $carry, mixed $datum) use ($compareBy): mixed {
                    $comparableValue = $compareBy($datum);
                    if (\is_float($comparableValue) && \is_nan($comparableValue)) {
                        return $carry;
                    }
                    // Compare exactly, since an int beyond ±2^53 and a float can compare equal as floats when
                    // they are not. Arithmetic::min() and max() do the same below.
                    return ($carry instanceof NoValueMonad
                        || Arithmetic::compareValues($comparableValue, $compareBy($carry)) < 0)
                        ? $datum
                        : $carry;
                },
                NoValueMonad::getInstance()
            );
        } else {
            /** @var mixed|NoValueMonad $result */
            $result = static::toValue(
                $data,
                static function (mixed $carry, mixed $datum): mixed {
                    if (\is_float($datum) && \is_nan($datum)) {
                        return $carry;
                    }
                    return ($carry instanceof NoValueMonad) ? $datum : Arithmetic::min($carry, $datum);
                },
                NoValueMonad::getInstance()
            );
        }

        return ($result instanceof NoValueMonad) ? null : $result;
    }

    /**
     * Reduces given iterable to its max value.
     *
     * Optional callable param $compareBy must return comparable value.
     * If $compareBy is not provided then items of given collection must be comparable.
     *
     * If several elements tie on the $compareBy value, returns the first of them.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<mixed> $data
     * @param callable|null   $compareBy (optional) function to extract comparable value from element. Ex: $item->getSomeValue()
     *
     * @return mixed|null
     */
    public static function toMax(iterable $data, ?callable $compareBy = null): mixed
    {
        if ($compareBy !== null) {
            /** @var mixed|NoValueMonad $result */
            $result = static::toValue(
                $data,
                static function (mixed $carry, mixed $datum) use ($compareBy): mixed {
                    $comparableValue = $compareBy($datum);
                    if (\is_float($comparableValue) && \is_nan($comparableValue)) {
                        return $carry;
                    }
                    // Compare exactly, since an int beyond ±2^53 and a float can compare equal as floats when
                    // they are not. Arithmetic::min() and max() do the same below.
                    return ($carry instanceof NoValueMonad
                        || Arithmetic::compareValues($compareBy($carry), $comparableValue) < 0)
                        ? $datum
                        : $carry;
                },
                NoValueMonad::getInstance()
            );
        } else {
            /** @var mixed|NoValueMonad $result */
            $result = static::toValue(
                $data,
                static function (mixed $carry, mixed $datum): mixed {
                    if (\is_float($datum) && \is_nan($datum)) {
                        return $carry;
                    }
                    return ($carry instanceof NoValueMonad) ? $datum : Arithmetic::max($carry, $datum);
                },
                NoValueMonad::getInstance()
            );
        }

        return ($result instanceof NoValueMonad) ? null : $result;
    }

    /**
     * Reduces given collection to array of its upper and lower bounds.
     *
     * Callable param $compareBy must return comparable value.
     *
     * If $compareBy is not proposed then items of given collection must be comparable.
     *
     * If several elements tie on the $compareBy value for the minimum or for the maximum, each bound is the
     * first of them.
     *
     * Returns [null, null] if given collection is empty.
     *
     * @param iterable<numeric> $numbers
     * @param callable|null     $compareBy
     *
     * @return array{numeric, numeric}|array{null, null}
     */
    public static function toMinMax(iterable $numbers, ?callable $compareBy = null): array
    {
        if ($compareBy !== null) {
            /** @var array{mixed|NoValueMonad, mixed|NoValueMonad} $result */
            $result = static::toValue($numbers, static function (array $carry, mixed $datum) use ($compareBy): array {
                $comparableValue = $compareBy($datum);
                if (\is_float($comparableValue) && \is_nan($comparableValue)) {
                    return $carry;
                }
                // Compare exactly, since an int beyond ±2^53 and a float can compare equal as floats when
                // they are not. Arithmetic::min() and max() do the same below. Only a strictly smaller
                // or larger value replaces the carried one, so the first of tied elements wins, as in
                // toMin() and toMax().
                return [
                    ($carry[0] instanceof NoValueMonad
                        || Arithmetic::compareValues($comparableValue, $compareBy($carry[0])) < 0)
                        ? $datum
                        : $carry[0],
                    ($carry[1] instanceof NoValueMonad
                        || Arithmetic::compareValues($compareBy($carry[1]), $comparableValue) < 0)
                        ? $datum
                        : $carry[1],
                ];
            }, [NoValueMonad::getInstance(), NoValueMonad::getInstance()]);
        } else {
            /** @var array{mixed|NoValueMonad, mixed|NoValueMonad} $result */
            $result = static::toValue(
                $numbers,
                static function (array $carry, mixed $datum): array {
                    if (\is_float($datum) && \is_nan($datum)) {
                        return $carry;
                    }
                    return [
                        ($carry[0] instanceof NoValueMonad) ? $datum : Arithmetic::min($carry[0], $datum),
                        ($carry[1] instanceof NoValueMonad) ? $datum : Arithmetic::max($carry[1], $datum),
                    ];
                },
                [NoValueMonad::getInstance(), NoValueMonad::getInstance()]
            );
        }

        /** @var array{numeric, numeric}|array{null, null} */
        return [
            ($result[0] instanceof NoValueMonad) ? null : $result[0],
            ($result[1] instanceof NoValueMonad) ? null : $result[1],
        ];
    }

    /**
     * Reduces given iterable to its length.
     *
     * @param iterable<mixed> $data
     *
     * @return int
     */
    public static function toCount(iterable $data): int
    {
        if (\is_countable($data)) {
            return \count($data);
        }

        return static::toValue($data, fn (int $carry): int => $carry + 1, 0);
    }

    /**
     * Reduces given iterable to an array of counts keyed by the value returned
     * from the key function.
     *
     * Single pass over the input. The key function must return an int or string
     * (the only valid array key types); any other return type throws a \TypeError
     * naming the offending type. This avoids PHP's implicit array-key coercion
     * (deprecation notices for null and float keys, surprising bool→1/0 collapse).
     *
     * Note: PHP arrays coerce numeric-string keys to int — a key function that
     * returns the string "1" and one that returns the int 1 collapse into a single
     * int key 1 with the combined count. Callers needing to distinguish those should
     * use a different data structure upstream.
     *
     * @template T
     *
     * @param iterable<T>       $data
     * @param callable(T): mixed $keyFunc must return int|string at runtime
     *
     * @return array<int|string, int>
     *
     * @throws \TypeError if $keyFunc returns a value that is not int|string
     */
    public static function toCountBy(iterable $data, callable $keyFunc): array
    {
        $counts = [];

        foreach ($data as $datum) {
            $key = $keyFunc($datum);
            if (!\is_int($key) && !\is_string($key)) {
                throw new \TypeError(
                    \sprintf('Key function must return int|string, got %s', \gettype($key)),
                );
            }

            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        return $counts;
    }

    /**
     * Reduces given collection to the sum of its items.
     *
     * @param iterable<numeric> $data
     *
     * @return int|float
     * @phpstan-ignore return.unusedType
     */
    public static function toSum(iterable $data): int|float
    {
        /** @psalm-suppress MixedOperand */
        return static::toValue($data, fn (int|float $carry, mixed $datum): int|float => $carry + $datum, 0); // @phpstan-ignore binaryOp.invalid
    }

    /**
     * Reduces given collection to the product of its items.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<numeric> $data
     *
     * @return int|float|null
     * @phpstan-ignore return.unusedType, return.unusedType
     */
    public static function toProduct(iterable $data): int|float|null
    {
        /** @psalm-suppress MixedOperand */
        return static::toValue($data, fn (int|float|null $carry, mixed $datum): int|float => ($carry ?? 1) * $datum); // @phpstan-ignore binaryOp.invalid
    }

    /**
     * Reduces given collection to the mean average of its items.
     *
     * The mean of finite values is finite even when their sum overflows, as for
     * [PHP_FLOAT_MAX, PHP_FLOAT_MAX], whose mean is PHP_FLOAT_MAX.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<numeric> $data
     *
     * @return int|float|null
     */
    public static function toAverage(iterable $data): int|float|null
    {
        $mean = new MeanAccumulator();
        foreach ($data as $datum) {
            $mean->add($datum);
        }

        return $mean->count() === 0 ? null : $mean->mean();
    }

    /**
     * Reduces given collection to its median value.
     *
     * For an even number of elements the median is the linear interpolation
     * (mean) of the two middle values, computed so that it does not overflow
     * for middle values whose sum exceeds PHP_FLOAT_MAX.
     *
     * A NAN anywhere in the input yields NAN, whatever its position, since a NAN has no place in the
     * order the median is read from. (toMin and toMax, which select a value, skip NAN instead.)
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<int|float> $data
     *
     * @return int|float|null
     */
    public static function toMedian(iterable $data): int|float|null
    {
        $values = static::toNumericList($data);
        if (static::containsNan($values)) {
            return \NAN;
        }
        \sort($values);

        $count = \count($values);
        if ($count === 0) {
            return null;
        }

        $mid = \intdiv($count, 2);

        if ($count % 2 === 1) {
            return $values[$mid];
        }

        return static::midpoint($values[$mid - 1], $values[$mid]);
    }

    /**
     * Reduces given collection to a list of its modes (the most frequent values).
     *
     * Returns every value tied for the maximum frequency, in first-seen order
     * (so an all-unique input returns all of its values). Multimodal inputs
     * return multiple modes.
     *
     * Returns an empty array if given collection is empty.
     *
     * Equality follows the strict rules in README "Strict and Coercive Types". Retains one
     * representative per distinct value, since it is part of the output.
     *
     * @param iterable<mixed> $data
     *
     * @return list<mixed>
     */
    public static function toMode(iterable $data): array
    {
        // See ValueCounter: it pins the identity anchors a repeated value's hash depends on.
        $counter = new ValueCounter(true, retainValues: true);

        foreach ($data as $datum) {
            $counter->add($datum);
        }

        $counts = $counter->counts();

        if (\count($counts) === 0) {
            return [];
        }

        $values   = $counter->values();
        $maxCount = \max($counts);

        /** @var list<mixed> $modes */
        $modes = [];
        foreach ($counts as $hash => $count) {
            if ($count === $maxCount) {
                /** @var mixed $mode */
                $mode = $values[$hash];
                $modes[] = $mode;
            }
        }

        return $modes;
    }

    /**
     * Reduces given collection to the variance of its values.
     *
     * Population variance by default; pass $sample = true for the sample variance
     * (Bessel's correction — divides by N - 1).
     *
     * A single pass over the collection in O(1) memory, so the input is never materialized.
     * Two refinements to the textbook online algorithm keep the result finite and stable:
     * the running mean is carried in compensated (double-double) form, and the variance is
     * carried in scaled form so that no intermediate quantity overflows while the final
     * result is still representable.
     *
     * The result is stable across orderings of the input to within floating-point rounding.
     * It is not bit-reproducible: floating-point addition is not associative, so different
     * orderings may differ in the last ulp.
     *
     * A non-finite value anywhere in the input yields NAN, since deviations from an infinite
     * mean are INF - INF. A variance that is itself too large to represent (for example values
     * straddling zero at PHP_FLOAT_MAX, whose true variance approaches 1e616) is reported as
     * INF, never as a negative number.
     *
     * Returns null if given collection is empty. Also returns null for the sample
     * variance of a single value (N - 1 = 0 is undefined). The population variance
     * of a single value is 0.0.
     *
     * Those null cases take precedence over NAN: toVariance([INF], true) is null, not NAN,
     * because with a single observation there is no sample variance to compute at all,
     * whatever that observation happens to be. The population variance of one value is
     * defined, so toVariance([INF]) is NAN.
     *
     * @param iterable<int|float> $data
     * @param bool                $sample population variance when false (default), sample variance when true
     *
     * @return float|null
     */
    public static function toVariance(iterable $data, bool $sample = false): ?float
    {
        [$count, $hasNonFinite, $scale, $weight] = self::toScaledVariance($data);

        // Checked before $hasNonFinite: an undefined divisor is a property of the collection's size,
        // not of its values, so null wins over NAN for the sample variance of one non-finite value.
        if ($count === 0 || ($sample && $count === 1)) {
            return null;
        }

        if ($hasNonFinite) {
            return \NAN;
        }

        // Unscale by interleaving the two multiplications. ($scale * $scale) * $weight would reach
        // INF for a representable variance whenever $weight < 1, which is exactly the prefix-overflow
        // case; multiplying $weight in first keeps every intermediate within range.
        $variance = ($scale * $weight) * $scale;

        // Bessel's correction, applied as a single ratio so that a large population variance is not
        // pushed to INF by an intermediate multiplication.
        /** @psalm-suppress InvalidOperand */
        return $sample ? $variance * ($count / ($count - 1)) : $variance;
    }

    /**
     * Reduces given collection to the standard deviation of its values.
     *
     * Square root of the variance. Population standard deviation by default; pass
     * $sample = true for the sample standard deviation (Bessel's correction).
     *
     * Inherits the single-pass, O(1)-memory and overflow behavior of {@see Reduce::toVariance()},
     * but is not computed through the variance itself, so a representable standard deviation is not
     * lost when its square is out of range. toStandardDeviation([-1e200, 1e200]) is 1e200 although
     * the variance 1e400 is reported as INF, and toStandardDeviation([-1e-200, 1e-200]) is 1e-200
     * although the variance 1e-400 underflows to 0.0. A deviation from the mean that itself overflows
     * still yields INF: toStandardDeviation([-PHP_FLOAT_MAX, PHP_FLOAT_MAX]) is INF.
     *
     * The null cases likewise take precedence over NAN: toStandardDeviation([INF], true)
     * is null, while toStandardDeviation([INF]) is NAN.
     *
     * Returns null whenever the underlying variance is null (empty collection, or
     * the sample standard deviation of a single value). The population standard
     * deviation of a single value is 0.0.
     *
     * @param iterable<int|float> $data
     * @param bool                $sample population standard deviation when false (default), sample when true
     *
     * @return float|null
     */
    public static function toStandardDeviation(iterable $data, bool $sample = false): ?float
    {
        [$count, $hasNonFinite, $scale, $weight] = self::toScaledVariance($data);

        // The same null-over-NAN precedence as toVariance.
        if ($count === 0 || ($sample && $count === 1)) {
            return null;
        }

        if ($hasNonFinite) {
            return \NAN;
        }

        // Bessel's correction goes into $weight, which is at most about $count, so it cannot overflow.
        if ($sample) {
            /** @psalm-suppress InvalidOperand */
            $weight *= $count / ($count - 1);
        }

        // $scale * sqrt($weight) rather than sqrt($scale ** 2 * $weight): the variance squares the
        // magnitude and so overflows or underflows for a representable standard deviation, as with
        // [-1e200, 1e200] (variance 1e400) or [-1e-200, 1e-200] (variance 1e-400).
        return $scale * \sqrt($weight);
    }

    /**
     * The single-pass accumulation behind toVariance and toStandardDeviation.
     *
     * Returns the variance in scaled form: the population variance is $scale ** 2 * $weight.
     * Keeping the two factors apart lets the caller unscale without forming $scale ** 2 when
     * only the square root is wanted.
     *
     * @param iterable<int|float> $data
     *
     * @return array{int, bool, float, float} count, whether any value was non-finite, scale, weight
     */
    private static function toScaledVariance(iterable $data): array
    {
        $count = 0;

        // The running mean is carried in double-double form ($meanHi + $meanLo). A mean that is not
        // representable as a single double — the case for a large offset with an ulp-scale spread,
        // such as 1e16, 1e16 + 2, 1e16 + 4, whose mean 1e16 + 2 lands between representable
        // neighbours as the sequence is built up — would otherwise round away the very spread being
        // measured, making the result depend on the order the values arrive in.
        $meanHi = 0.0;
        $meanLo = 0.0;

        // The variance is carried in scaled form as $scale ** 2 * $weight, and $scale ** 2 is never
        // materialized. Storing the variance directly overflows on an intermediate prefix even when
        // the final result is representable: for [-1.4e154, 1.4e154, 0] the variance of the leading
        // pair is 1.96e308, past PHP_FLOAT_MAX, and a saturated accumulator can never come back down
        // to the representable answer of 1.31e308. $scale tracks the largest deviation step seen and
        // $weight the remaining factor, so magnitude and multiplicity stay separated until the end.
        $scale  = 0.0;
        $weight = 0.0;

        // A non-finite input has to be tracked explicitly rather than inferred from the accumulator:
        // for all-equal values such as [5, 5, NAN] the spread stays zero and the NAN leaves no trace.
        $hasNonFinite = false;

        foreach ($data as $value) {
            ++$count;

            if (!\is_finite((float) $value)) {
                $hasNonFinite = true;
            }

            /** @psalm-suppress InvalidOperand */
            $delta = ($value - $meanHi) - $meanLo;
            /** @psalm-suppress InvalidOperand */
            $step = $delta / $count;

            // $meanHi + $step in double-double: 2Sum recovers the low bits the addition rounds off.
            $sum     = $meanHi + $step;
            $shifted = $sum - $meanHi;
            $meanLo += ($meanHi - ($sum - $shifted)) + ($step - $shifted);
            $meanHi  = $sum;

            if (\is_finite($meanHi)) {
                // Renormalize so $meanHi carries the leading bits and $meanLo only the residue.
                $renormalized = $meanHi + $meanLo;
                $meanLo      -= $renormalized - $meanHi;
                $meanHi       = $renormalized;
            } else {
                // The mean itself overflowed, making the compensation term NAN (from INF - INF).
                // Discard it so that subsequent deviations stay signed infinities rather than NAN.
                $meanLo = 0.0;
            }

            if ($count > 1) {
                // The normalized form of Welford's M2_n = M2_{n-1} + delta^2 * (n-1)/n, divided
                // through by n and then rewritten over $scale: with t = |delta/n| the update is
                // var_n = var_{n-1} * (n-1)/n + t^2 * (n-1). Both terms are non-negative, so the
                // accumulator can reach INF but can never go negative.
                $t      = \abs($step);
                $shrink = ($count - 1) / $count;

                if ($t > $scale) {
                    // Rebase onto the larger scale. The ratio is below 1, so nothing can overflow.
                    $ratio = $scale / $t;
                    /** @psalm-suppress InvalidOperand */
                    $weight = $weight * $ratio * $ratio * $shrink + ($count - 1);
                    $scale  = $t;
                } else {
                    // $scale is 0 only while every step has been 0, and infinite only once a
                    // deviation has already overflowed; in both cases the contribution is 0.
                    $ratio = ($scale > 0.0 && \is_finite($scale)) ? $t / $scale : 0.0;
                    /** @psalm-suppress InvalidOperand */
                    $weight = $weight * $shrink + $ratio * $ratio * ($count - 1);
                }
            }
        }

        return [$count, $hasNonFinite, $scale, $weight];
    }

    /**
     * Reduces given collection to its value at the given percentile.
     *
     * Uses the R-7 / linear-interpolation method (the NumPy default): the
     * percentile rank is mapped onto the sorted values and interpolated between
     * the two nearest ranks. Percentile 0 is the minimum, 100 is the maximum.
     *
     * The interpolation does not overflow when the two neighbouring values span more
     * than PHP_FLOAT_MAX (for example straddling zero at extreme magnitudes).
     *
     * A NAN anywhere in the input yields NAN, whatever its position, as for toMedian.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<int|float> $data
     * @param float               $percentile in the inclusive range [0, 100]
     *
     * @return int|float|null
     *
     * @throws \InvalidArgumentException if $percentile is outside [0, 100] or NAN
     */
    public static function toPercentile(iterable $data, float $percentile): int|float|null
    {
        if (\is_nan($percentile) || $percentile < 0 || $percentile > 100) {
            $shown = \is_nan($percentile) ? 'NAN' : (string) $percentile;
            throw new \InvalidArgumentException("Percentile must be between 0 and 100. Got {$shown}.");
        }

        $values = static::toNumericList($data);
        if (static::containsNan($values)) {
            return \NAN;
        }
        \sort($values);

        $count = \count($values);
        if ($count === 0) {
            return null;
        }

        if ($count === 1) {
            return $values[0];
        }

        /** @psalm-suppress InvalidOperand */
        $rank       = ($count - 1) * ($percentile / 100);
        $lowerIndex = (int) \floor($rank);
        /** @psalm-suppress InvalidOperand */
        $fraction   = $rank - $lowerIndex;

        if ($fraction === 0.0) {
            return $values[$lowerIndex];
        }

        return static::interpolate($values[$lowerIndex], $values[$lowerIndex + 1], $fraction);
    }

    /**
     * Reduces given collection to its value at the given quantile.
     *
     * Thin wrapper over {@see Reduce::toPercentile()} that accepts a quantile in the
     * inclusive range [0, 1] (e.g. 0.25 is the first quartile / 25th percentile).
     *
     * A NAN anywhere in the input yields NAN, whatever its position, as for toMedian.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<int|float> $data
     * @param float               $quantile in the inclusive range [0, 1]
     *
     * @return int|float|null
     *
     * @throws \InvalidArgumentException if $quantile is outside [0, 1] or NAN
     */
    public static function toQuantile(iterable $data, float $quantile): int|float|null
    {
        if (\is_nan($quantile) || $quantile < 0 || $quantile > 1) {
            $shown = \is_nan($quantile) ? 'NAN' : (string) $quantile;
            throw new \InvalidArgumentException("Quantile must be between 0 and 1. Got {$shown}.");
        }

        return static::toPercentile($data, $quantile * 100.0);
    }

    /**
     * Materializes an iterable of numbers into a list.
     *
     * @param iterable<int|float> $data
     *
     * @return list<int|float>
     */
    private static function toNumericList(iterable $data): array
    {
        $values = [];
        foreach ($data as $datum) {
            $values[] = $datum;
        }

        return $values;
    }

    /**
     * Checks whether a list of numbers holds a float NAN.
     *
     * NAN has no place in an order, so the order statistics (median, percentile, quantile) can't
     * sort around it; they propagate it instead, as the arithmetic statistics do.
     *
     * @param list<int|float> $values
     */
    private static function containsNan(array $values): bool
    {
        foreach ($values as $value) {
            if (\is_float($value) && \is_nan($value)) {
                return true;
            }
        }

        return false;
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
    private static function midpoint(int|float $lo, int|float $hi): int|float
    {
        // The midpoint of a value and itself is that value. Taken first so that two identical
        // infinities return that infinity rather than the NAN that INF - INF would give below.
        if ($lo === $hi) {
            return $lo;
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
            $midpoint = static::integerMidpoint($intLo, $intHi);

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
    private static function integerMidpoint(int $lo, int $hi): int|float
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
    private static function integerDifference(int $a, int $b): int|float
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

    /**
     * Linearly interpolates between two numbers without overflowing on extreme magnitudes.
     *
     * The direct form $lo + $fraction * ($hi - $lo) is preferred: it returns $lo exactly when the
     * two values are equal, which the weighted form does not (0.1 * 0.7 + 0.1 * 0.3 is not 0.1).
     * It is only abandoned when the span $hi - $lo is itself unrepresentable, where the weighted
     * form is used instead — being a convex combination it always lies between $lo and $hi and so
     * cannot overflow.
     *
     * A fraction of exactly 0.5 is delegated to {@see Reduce::midpoint()}: that is the one
     * interpolation computable exactly for integer bounds, and delegating keeps
     * toPercentile($data, 50) identical to toMedian($data) rather than merely close to it.
     *
     * @param int|float $lo
     * @param int|float $hi
     * @param float     $fraction in the inclusive range [0, 1]
     *
     * @return int|float
     */
    private static function interpolate(int|float $lo, int|float $hi, float $fraction): int|float
    {
        if ($fraction === 0.5) {
            return static::midpoint($lo, $hi);
        }

        /** @psalm-suppress InvalidOperand */
        $span = $hi - $lo;

        if (\is_finite((float) $span)) {
            /** @psalm-suppress InvalidOperand */
            return $lo + $fraction * $span;
        }

        /** @psalm-suppress InvalidOperand */
        return $lo * (1 - $fraction) + $hi * $fraction;
    }

    /**
     * Reduces to a string with optional glue, prefix, and suffix.
     *
     * Returns empty string (with optional prefix and suffix) if collection is empty.
     *
     * @param iterable<mixed> $data
     * @param string          $separator (optional) inserted between each item. Ex: ', ' for 1, 2, 3, ...
     * @param string          $prefix (optional) prepended to string
     * @param string          $suffix (optional) appended to string
     *
     * @return string
     */
    public static function toString(iterable $data, string $separator = '', string $prefix = '', string $suffix = ''): string
    {
        /** @var list<string|int|float> $items */
        $items = [];
        foreach ($data as $datum) {
            /** @var string|int|float $datum */
            $items[] = \is_float($datum) && \is_nan($datum) ? 'NAN' : $datum;
        }

        $joined = \implode($separator, $items);
        return $prefix . $joined . $suffix;
    }

    /**
     * Reduces given collection to its range.
     *
     * Returns 0 if given collection is empty.
     *
     * Values are compared exactly, and two ints, or an int beyond ±2^53 and an integral float within the int
     * range, are subtracted exactly when their difference fits in an int: `toRange([2 ** 53 + 1, 2.0 ** 53])`
     * is `1.0`. A larger difference is correctly rounded to a float, where PHP's `max - min` rounds the
     * operands first: `toRange([-(2 ** 62 + 1), 2.0 ** 62 + 1024])` is 2^63 + 2048, not 2^63.
     * Paired with a non-integral float or a float beyond the int range, the int is converted to float in the
     * subtraction and the range can be off: `toRange([PHP_INT_MAX, 2.0 ** 63])` is `0.0`, where the true range is 1.
     *
     * @param iterable<numeric> $numbers
     *
     * @return int|float
     */
    public static function toRange(iterable $numbers): int|float
    {
        $min = null;
        $max = null;
        foreach ($numbers as $number) {
            if (!\is_int($number) && !\is_float($number)) {
                // Numeric strings convert as they would in the subtraction.
                /** @psalm-suppress InvalidOperand */
                $number = $number + 0;
            }
            if (\is_float($number) && \is_nan($number)) {
                continue;
            }

            // Compare exactly, since an int beyond ±2^53 and a float can compare equal as floats when
            // they are not.
            $min = $min === null ? $number : Arithmetic::min($min, $number);
            $max = $max === null ? $number : Arithmetic::max($max, $number);
        }

        if ($min === null || $max === null) {
            return 0;
        }

        // PHP's overflowing int subtraction rounds each operand to float before subtracting.
        if (\is_int($min) && \is_int($max)) {
            return static::integerDifference($max, $min);
        }

        // Subtract an int and an integral float in the int domain: 2^53 + 1 - 2.0^53 is 1, not 0.0.
        if (\is_int($min) !== \is_int($max)) {
            $minInt = Arithmetic::toExactInt($min);
            $maxInt = Arithmetic::toExactInt($max);
            if ($minInt !== null && $maxInt !== null) {
                return (float) static::integerDifference($maxInt, $minInt);
            }
        }

        /** @psalm-suppress InvalidOperand */
        $range = $max - $min;

        // A range is never negative, but the tie rule can leave -0.0 as the maximum and 0.0 as the minimum.
        return $range === 0.0 ? 0.0 : $range;
    }

    /**
     * Reduces given collection to its first value.
     *
     * @param iterable<mixed> $data
     * @return mixed
     *
     * @throws \LengthException if collection is empty
     */
    public static function toFirst(iterable $data): mixed
    {
        foreach ($data as $datum) {
            /** @var mixed $datum */
            return $datum;
        }

        throw new \LengthException('collection is empty');
    }

    /**
     * Reduces given collection to its last value.
     *
     * @param iterable<mixed> $data
     * @return mixed
     *
     * @throws \LengthException if collection is empty
     */
    public static function toLast(iterable $data): mixed
    {
        /** @var mixed|NoValueMonad $result */
        $result = static::toValue($data, fn (mixed $carry, mixed $datum): mixed => $datum, NoValueMonad::getInstance());

        if ($result instanceof NoValueMonad) {
            throw new \LengthException('collection is empty');
        }

        return $result;
    }

    /**
     * Reduces given collection to its first and last values.
     *
     * @param iterable<mixed> $data
     * @return array{mixed, mixed}
     *
     * @throws \LengthException if collection is empty
     */
    public static function toFirstAndLast(iterable $data): array
    {
        $hasFirst = false;
        /** @var mixed $first */
        $first = null;
        /** @var mixed $last */
        $last = null;

        foreach ($data as $datum) {
            /** @var mixed $datum */
            if (!$hasFirst) {
                $first    = $datum;
                $hasFirst = true;
            }
            $last = $datum;
        }

        if (!$hasFirst) {
            throw new \LengthException('collection is empty');
        }

        return [$first, $last];
    }

    /**
     * Reduces given collection random value in from within it.
     *
     * @param iterable<mixed> $data
     *
     * @return mixed
     *
     * @throws \LengthException if given iterable is empty
     */
    public static function toRandomValue(iterable $data): mixed
    {
        if (\is_countable($data)) {
            if (\count($data) === 0) {
                throw new \LengthException('Given iterable must be non-empty');
            }

            $targetIndex = \mt_rand(0, \count($data) - 1);

            $index = 0;
            foreach ($data as $datum) {
                /** @var mixed $datum */
                if ($targetIndex === $index) {
                    return $datum;
                }

                ++$index;
            }
        }

        $data = Transform::toArray($data);

        if (\count($data) === 0) {
            throw new \LengthException('Given iterable must be non-empty');
        }

        return $data[\array_rand($data)];
    }

    /**
     * Reduces given iterable to the first element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast, matching Summary::allMatch/anyMatch.
     *
     * Short-circuits on first match — does not consume the rest of the iterable.
     *
     * If no element matches, returns $default (null by default).
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     * @param mixed           $default value returned when no element matches
     *
     * @return mixed
     */
    public static function toFirstMatch(iterable $data, callable $predicate, mixed $default = null): mixed
    {
        foreach ($data as $datum) {
            /** @var mixed $datum */
            if ((bool) $predicate($datum)) {
                return $datum;
            }
        }

        return $default;
    }

    /**
     * Reduces given iterable to the zero-based position of the first element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast, matching Summary::allMatch/anyMatch.
     *
     * Short-circuits on first match — does not consume the rest of the iterable.
     *
     * If no element matches, returns $default (null by default).
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     * @param mixed           $default value returned when no element matches
     *
     * @return mixed
     */
    public static function toFirstMatchIndex(iterable $data, callable $predicate, mixed $default = null): mixed
    {
        $index = 0;
        foreach ($data as $datum) {
            /** @var mixed $datum */
            if ((bool) $predicate($datum)) {
                return $index;
            }
            ++$index;
        }

        return $default;
    }

    /**
     * Reduces given iterable to the source key of the first element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast, matching Summary::allMatch/anyMatch.
     *
     * Short-circuits on first match — does not consume the rest of the iterable.
     *
     * If no element matches, returns $default (null by default).
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     * @param mixed           $default value returned when no element matches
     *
     * @return mixed
     */
    public static function toFirstMatchKey(iterable $data, callable $predicate, mixed $default = null): mixed
    {
        foreach ($data as $key => $datum) {
            /** @var mixed $datum */
            if ((bool) $predicate($datum)) {
                return $key;
            }
        }

        return $default;
    }

    /**
     * Reduces given iterable to the last element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast, matching Summary::allMatch/anyMatch.
     *
     * Consumes the entire iterable (cannot short-circuit).
     *
     * If no element matches, returns $default (null by default).
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     * @param mixed           $default value returned when no element matches
     *
     * @return mixed
     */
    public static function toLastMatch(iterable $data, callable $predicate, mixed $default = null): mixed
    {
        /** @var mixed $result */
        $result = $default;
        foreach ($data as $datum) {
            /** @var mixed $datum */
            if ((bool) $predicate($datum)) {
                $result = $datum;
            }
        }

        return $result;
    }

    /**
     * Reduces given iterable to the zero-based position of the last element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast, matching Summary::allMatch/anyMatch.
     *
     * Consumes the entire iterable (cannot short-circuit).
     *
     * If no element matches, returns $default (null by default).
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     * @param mixed           $default value returned when no element matches
     *
     * @return mixed
     */
    public static function toLastMatchIndex(iterable $data, callable $predicate, mixed $default = null): mixed
    {
        /** @var mixed $result */
        $result = $default;
        $index  = 0;
        foreach ($data as $datum) {
            /** @var mixed $datum */
            if ((bool) $predicate($datum)) {
                $result = $index;
            }
            ++$index;
        }

        return $result;
    }

    /**
     * Reduces given iterable to the source key of the last element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast, matching Summary::allMatch/anyMatch.
     *
     * Consumes the entire iterable (cannot short-circuit).
     *
     * If no element matches, returns $default (null by default).
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     * @param mixed           $default value returned when no element matches
     *
     * @return mixed
     */
    public static function toLastMatchKey(iterable $data, callable $predicate, mixed $default = null): mixed
    {
        /** @var mixed $result */
        $result = $default;
        foreach ($data as $key => $datum) {
            /** @var mixed $datum */
            if ((bool) $predicate($datum)) {
                /** @var mixed $result */
                $result = $key;
            }
        }

        return $result;
    }

    /**
     * Reduces given iterable to its sole element.
     *
     * Throws \LengthException if the iterable contains zero or two-or-more elements.
     *
     * Compose with Stream::filter()->toOnly() if you need a predicate variant.
     *
     * @param iterable<mixed> $data
     *
     * @return mixed
     *
     * @throws \LengthException if iterable is empty or contains more than one element
     */
    public static function toOnly(iterable $data): mixed
    {
        $result = NoValueMonad::getInstance();
        $found  = false;
        foreach ($data as $datum) {
            if ($found) {
                throw new \LengthException('iterable must contain exactly one element; found 2 or more');
            }
            /** @var mixed $result */
            $result = $datum;
            $found  = true;
        }

        if (!$found) {
            throw new \LengthException('iterable must contain exactly one element; found 0');
        }

        return $result;
    }

    /**
     * Drains the given iterable, discarding values.
     *
     * Useful for forcing evaluation of a lazy pipeline whose only purpose is its side effects
     * (e.g. a side-effectful Single::map() or a Generator that writes to a log).
     *
     * @param iterable<mixed> $data
     *
     * @return void
     */
    public static function consume(iterable $data): void
    {
        foreach ($data as $_) {
            // intentionally empty
        }
    }

    /**
     * Reduces given iterable to the value at the nth position.
     *
     * @template T
     *
     * @param iterable<T> $data
     * @param int $position
     *
     * @return T
     *
     * @throws \LengthException if given iterable does not contain item with target position.
     */
    public static function toNth(iterable $data, int $position): mixed
    {
        if ($position < 0) {
            throw new \InvalidArgumentException("Position must be non-negative. Got {$position}.");
        }

        if (\is_countable($data) && \count($data) <= $position) {
            throw new \LengthException("Given iterable does not contain item with position {$position}");
        }

        $i = 0;
        foreach ($data as $datum) {
            if ($i === $position) {
                return $datum;
            }
            ++$i;
        }

        throw new \LengthException("Given iterable does not contain item with position {$position}");
    }
}

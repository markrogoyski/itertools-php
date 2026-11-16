<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\Precision\Arithmetic;
use IterTools\Util\Precision\Interpolation;
use IterTools\Util\Precision\MeanAccumulator;
use IterTools\Util\Values\NoValueMonad;
use IterTools\Util\Values\Truthiness;
use IterTools\Util\Values\ValueCounter;

final class Reduce
{
    /**
     * The exact power-of-two factor toScaledVariance rescales its state by once a Welford step underflows
     * (2^128), and its inverse. Large enough that a step of a rescaled subnormal gap divided by any int
     * count is normal (2^(128 - 1074 - 63) > 2^-1022).
     */
    private const VARIANCE_UPSCALE   = 2.0 ** 128.0;
    private const VARIANCE_DOWNSCALE = 2.0 ** -128.0;

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
                    if ($carry instanceof NoValueMonad) {
                        return $datum;
                    }
                    // The operator compares an int and a float as floats, which only goes wrong for a pair it finds
                    // equal: an int beyond ±2^53 and a float. Arithmetic compares that pair exactly. The types are
                    // tested first, as == on a projection such as an array would compare it twice.
                    $carryValue = $compareBy($carry);
                    $exact = \is_int($comparableValue) !== \is_int($carryValue)
                        && \is_float($comparableValue) !== \is_float($carryValue)
                        && $comparableValue == $carryValue;
                    $smaller = $exact
                        ? Arithmetic::compareValues($comparableValue, $carryValue) < 0
                        : $comparableValue < $carryValue;
                    return $smaller ? $datum : $carry;
                },
                NoValueMonad::getInstance()
            );
        } else {
            // A loop rather than toValue(), which would call a closure for each element.
            $result = NoValueMonad::getInstance();
            foreach ($data as $datum) {
                if (\is_float($datum) && \is_nan($datum)) {
                    continue;
                }
                if ($result instanceof NoValueMonad) {
                    $result = $datum;
                } elseif (\is_int($result) !== \is_int($datum) && \is_float($result) !== \is_float($datum) && $result == $datum) {
                    // < compares an int and a float as floats, which only goes wrong for a pair it finds equal: an
                    // int beyond ±2^53 and a float. Arithmetic::min() compares that pair exactly. The types are
                    // tested first, as == on other values, such as arrays or Stringables, would compare them twice.
                    $result = Arithmetic::min($result, $datum);
                } else {
                    // As Arithmetic::min(): the first of tied values, which a two-argument \min() can't promise.
                    $result = $datum < $result ? $datum : $result;
                }
            }
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
                    if ($carry instanceof NoValueMonad) {
                        return $datum;
                    }
                    // Compare an int and a float exactly, as in toMin().
                    $carryValue = $compareBy($carry);
                    $exact = \is_int($comparableValue) !== \is_int($carryValue)
                        && \is_float($comparableValue) !== \is_float($carryValue)
                        && $comparableValue == $carryValue;
                    $larger = $exact
                        ? Arithmetic::compareValues($carryValue, $comparableValue) < 0
                        : $carryValue < $comparableValue;
                    return $larger ? $datum : $carry;
                },
                NoValueMonad::getInstance()
            );
        } else {
            // A loop, and an exact comparison only for an int and a float that compare equal, as in toMin().
            $result = NoValueMonad::getInstance();
            foreach ($data as $datum) {
                if (\is_float($datum) && \is_nan($datum)) {
                    continue;
                }
                if ($result instanceof NoValueMonad) {
                    $result = $datum;
                } elseif (\is_int($result) !== \is_int($datum) && \is_float($result) !== \is_float($datum) && $result == $datum) {
                    $result = Arithmetic::max($result, $datum);
                } else {
                    // As Arithmetic::max(): the first of tied values.
                    $result = $datum <= $result ? $result : $datum;
                }
            }
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
     * @param iterable<mixed> $numbers
     * @param callable|null   $compareBy
     *
     * @return array{mixed, mixed}|array{null, null}
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
                if ($carry[0] instanceof NoValueMonad) {
                    return [$datum, $datum];
                }
                // Compare an int and a float exactly, as in toMin(). Only a strictly smaller or larger value
                // replaces the carried one, so the first of tied elements wins, as in toMin() and toMax().
                $minValue = $compareBy($carry[0]);
                $exact = \is_int($comparableValue) !== \is_int($minValue)
                    && \is_float($comparableValue) !== \is_float($minValue)
                    && $comparableValue == $minValue;
                $smaller = $exact
                    ? Arithmetic::compareValues($comparableValue, $minValue) < 0
                    : $comparableValue < $minValue;
                $maxValue = $compareBy($carry[1]);
                $exact = \is_int($comparableValue) !== \is_int($maxValue)
                    && \is_float($comparableValue) !== \is_float($maxValue)
                    && $comparableValue == $maxValue;
                $larger = $exact
                    ? Arithmetic::compareValues($maxValue, $comparableValue) < 0
                    : $maxValue < $comparableValue;
                return [$smaller ? $datum : $carry[0], $larger ? $datum : $carry[1]];
            }, [NoValueMonad::getInstance(), NoValueMonad::getInstance()]);
        } else {
            // A loop, and an exact comparison only for an int and a float that compare equal, as in toMin().
            $min = $max = NoValueMonad::getInstance();
            foreach ($numbers as $datum) {
                if (\is_float($datum) && \is_nan($datum)) {
                    continue;
                }
                if ($min instanceof NoValueMonad) {
                    $min = $max = $datum;
                    continue;
                }
                if (\is_int($min) !== \is_int($datum) && \is_float($min) !== \is_float($datum) && $min == $datum) {
                    $min = Arithmetic::min($min, $datum);
                } else {
                    // As Arithmetic::min() and max(): the first of tied values.
                    $min = $datum < $min ? $datum : $min;
                }
                if (\is_int($max) !== \is_int($datum) && \is_float($max) !== \is_float($datum) && $max == $datum) {
                    $max = Arithmetic::max($max, $datum);
                } else {
                    $max = $datum <= $max ? $max : $datum;
                }
            }
            $result = [$min, $max];
        }

        return [
            ($result[0] instanceof NoValueMonad) ? null : $result[0],
            ($result[1] instanceof NoValueMonad) ? null : $result[1],
        ];
    }

    /**
     * Reduces given iterable to its length.
     *
     * Uses Countable::count() when the iterable is countable, without traversing it, so a Countable that
     * misreports its size (such as a CachingIterator over a source it has not read yet) gives a wrong length.
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
     * An int sum that overflows continues as a float, as PHP arithmetic does, so it may lose precision.
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
     * Values are ordered by exact value, so an int beyond ±2^53 and a float take their true places.
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
        static::sortNumbers($values);

        $count = \count($values);
        if ($count === 0) {
            return null;
        }

        $mid = \intdiv($count, 2);

        if ($count % 2 === 1) {
            return $values[$mid];
        }

        return Arithmetic::midpoint($values[$mid - 1], $values[$mid]);
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
     * A subnormal variance takes Bessel's correction before it is rounded to the subnormal grid, so
     * toVariance([0, 3e-162], true) is 5e-324, not 0.0. It is still not correctly rounded.
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
        [$count, $hasNonFinite, $scale, $weight, $rescaled] = self::toScaledVariance($data);

        // Checked before $hasNonFinite: an undefined divisor is a property of the collection's size,
        // not of its values, so null wins over NAN for the sample variance of one non-finite value.
        if ($count === 0 || ($sample && $count === 1)) {
            return null;
        }

        if ($hasNonFinite) {
            return \NAN;
        }

        // Undo the rescaling first. It is exact unless $scale lands below PHP_FLOAT_MIN, and then the
        // variance, at most about $scale ** 2 * $count, is subnormal and taken below.
        $unscaled = $rescaled ? $scale * self::VARIANCE_DOWNSCALE : $scale;

        // Unscale by interleaving the two multiplications. ($scale * $scale) * $weight would reach
        // INF for a representable variance whenever $weight < 1, which is exactly the prefix-overflow
        // case; multiplying $weight in first keeps every intermediate within range.
        $variance = ($unscaled * $weight) * $unscaled;

        // Bessel's correction, applied as a single ratio so that a large population variance is not
        // pushed to INF by an intermediate multiplication.
        if ($sample) {
            /** @psalm-suppress InvalidOperand */
            $variance *= $count / ($count - 1);
        }
        if ($variance >= \PHP_FLOAT_MIN) {
            return $variance;
        }

        // A subnormal variance has lost resolution to each rounding onto the subnormal grid, and a
        // population variance that rounded to 0.0 stays 0.0 whatever the correction: the sample variance
        // of [0, 3e-162], 4.5e-324, would come out 0.0, not 5e-324. So Bessel's correction goes into
        // $weight, which is at most about $count, and the variance, now far from overflow, is rounded
        // onto the grid once, by the last multiplication.
        if ($sample) {
            /** @psalm-suppress InvalidOperand */
            $weight *= $count / ($count - 1);
        }
        $variance = ($scale * $weight) * $scale;

        return $rescaled ? $variance * (self::VARIANCE_DOWNSCALE * self::VARIANCE_DOWNSCALE) : $variance;
    }

    /**
     * Reduces given collection to the standard deviation of its values.
     *
     * Square root of the variance. Population standard deviation by default; pass
     * $sample = true for the sample standard deviation (Bessel's correction).
     *
     * Inherits the single-pass, O(1)-memory and overflow behavior of {@see Reduce::toVariance()}. It is the
     * square root of the variance where that is finite and normal, and is otherwise computed without forming
     * the variance, so a representable standard deviation is not lost when its square is out of range.
     * toStandardDeviation([-1e200, 1e200]) is 1e200 although the variance 1e400 is reported as INF, and
     * toStandardDeviation([-1e-200, 1e-200]) is 1e-200 although the variance 1e-400 underflows to 0.0.
     * Values further apart than PHP_FLOAT_MAX are no obstacle either: toStandardDeviation([-PHP_FLOAT_MAX,
     * PHP_FLOAT_MAX]) is PHP_FLOAT_MAX. Only a standard deviation that is itself beyond PHP_FLOAT_MAX yields
     * INF, as for the sample standard deviation of that pair, PHP_FLOAT_MAX * sqrt(2). The result is not
     * correctly rounded, so one within a few ulps of PHP_FLOAT_MAX may also come out as INF.
     *
     * Subnormal values keep their resolution: a step of the running mean that would fall below
     * PHP_FLOAT_MIN is taken on values rescaled by an exact power of two, so
     * toStandardDeviation([0, 5e-324], true) is 5e-324, not 0.0. A standard deviation of a few subnormal
     * steps is still not correctly rounded, and when the exact value lies at or near the midpoint of two
     * subnormals it can come out one step (5e-324) off.
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
        [$count, $hasNonFinite, $scale, $weight, $rescaled] = self::toScaledVariance($data);

        // The same null-over-NAN precedence as toVariance.
        if ($count === 0 || ($sample && $count === 1)) {
            return null;
        }

        if ($hasNonFinite) {
            return \NAN;
        }

        if (!$rescaled) {
            // The square root of the variance, formed exactly as toVariance() forms it, wherever that is
            // finite and normal: one rounding fewer than $scale * sqrt($weight), so it is correctly rounded
            // whenever the variance is.
            $variance = ($scale * $weight) * $scale;
            if ($sample) {
                /** @psalm-suppress InvalidOperand */
                $variance = $variance * ($count / ($count - 1));
            }
            if (\is_finite($variance) && $variance >= \PHP_FLOAT_MIN) {
                return \sqrt($variance);
            }
        }

        // Bessel's correction goes into $weight, which is at most about $count, so it cannot overflow.
        if ($sample) {
            /** @psalm-suppress InvalidOperand */
            $weight *= $count / ($count - 1);
        }

        if ($rescaled) {
            // Undo the rescaling last, so that a subnormal standard deviation is rounded to the subnormal
            // grid once, from the full-precision product. The product cannot overflow: the rescaling ends
            // at a value beyond PHP_FLOAT_MAX / 2^128, and the standard deviation of values within that
            // bound, two of which (the pair whose step underflowed) are next to zero, is below it.
            return ($scale * \sqrt($weight)) * self::VARIANCE_DOWNSCALE;
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
     * only the square root is wanted. When $rescaled is true, $scale is VARIANCE_UPSCALE times
     * too large (see the underflow branch below).
     *
     * @param iterable<int|float> $data
     *
     * @return array{int, bool, float, float, bool} count, whether any value was non-finite, scale,
     *                                              weight, whether $scale is rescaled
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

        // Whether $meanHi, $meanLo and $scale, and each value as it arrives, are multiplied by
        // VARIANCE_UPSCALE, which happens only once a step underflows (see below).
        $rescaled = false;

        foreach ($data as $value) {
            ++$count;

            if (!\is_finite((float) $value)) {
                $hasNonFinite = true;
            }

            if ($rescaled) {
                /** @psalm-suppress InvalidOperand */
                $rescaledValue = $value * self::VARIANCE_UPSCALE;

                if (\is_finite($rescaledValue)) {
                    $value = $rescaledValue;
                } else {
                    // A value beyond PHP_FLOAT_MAX / 2^128 (or a non-finite one) cannot be rescaled.
                    // Return to the original scale: the rounding of the subnormal-scale state that this
                    // can cause is negligible next to the gap to such a value.
                    $meanHi  *= self::VARIANCE_DOWNSCALE;
                    $meanLo  *= self::VARIANCE_DOWNSCALE;
                    $scale   *= self::VARIANCE_DOWNSCALE;
                    $rescaled = false;
                }
            }

            if (
                \is_int($value)
                && ($value > Arithmetic::FLOAT_EXACT_INT_LIMIT || $value < -Arithmetic::FLOAT_EXACT_INT_LIMIT)
            ) {
                // Converting an int beyond ±2^53 to float rounds off its low bits, which for [2^53, 2^53 + 1]
                // are the whole spread. Split it into two parts that each convert exactly: the int with its low
                // 11 bits cleared (at most 52 significant bits, even at PHP_INT_MIN and PHP_INT_MAX) and those
                // bits. The first less $meanHi is exact once the mean is close, an integer as $meanHi is beyond
                // 2^53, and adding the low bits to it then is exact too. $meanLo goes last, as in the float path
                // below: subtracted from the low bits first, it would round to the resolution of up to 2047, and
                // lose the bits the spread is made of. The sum of the two parts can still round, as the first
                // value's delta, the value itself, does: 2Sum recovers that rounding, and its share of the step
                // goes into $meanLo, so the mean of [2^53 + 1] is exact.
                $lowBits  = (float) ($value & 0x7FF);
                $high     = (float) ($value - ($value & 0x7FF)) - $meanHi;
                $integral = $high + $lowBits;
                $shifted  = $integral - $high;
                $delta    = $integral - $meanLo;
                /** @psalm-suppress InvalidOperand */
                $meanLo  += (($high - ($integral - $shifted)) + ($lowBits - $shifted)) / $count;
            } else {
                /** @psalm-suppress InvalidOperand */
                $delta = ($value - $meanHi) - $meanLo;
            }
            /** @psalm-suppress InvalidOperand */
            $step = $delta / $count;

            if (!\is_finite($delta) && !$hasNonFinite) {
                // The gap between a finite value and the running mean can exceed PHP_FLOAT_MAX, as for
                // [-1e308, 1e308], although no deviation from the true mean does. Redo the step on halved
                // operands: for the gap to overflow, $value and $meanHi must both exceed 2^970 in
                // magnitude, so halving them is exact (halving $meanLo can drop only a subnormal bit, far
                // below the rounding of a gap this large), and the step rounds as it would with an
                // unbounded exponent. It is at most PHP_FLOAT_MAX, since $count >= 2 here. Only an
                // overflow takes this branch, so every other result is unchanged.
                /** @psalm-suppress InvalidOperand */
                $halfDelta = ($value * 0.5 - $meanHi * 0.5) - $meanLo * 0.5;
                /** @psalm-suppress InvalidOperand */
                $step = ($halfDelta / $count) * 2.0;
            } elseif (
                $step < \PHP_FLOAT_MIN
                && $step > -\PHP_FLOAT_MIN
                && !$rescaled
                && $delta !== 0.0
                && $count > 1
                && $scale < 1.0
                && \abs($meanHi) < 1.0
            ) {
                // The step underflows: below PHP_FLOAT_MIN it is rounded to a multiple of 2^-1074, which
                // for [0, 5e-324] turns 2.5e-324 into 0.0, a 100% error in the spread being measured.
                // Rescale the state by 2^128 and redo the step, then carry on rescaled. Multiplying by a
                // power of two is exact while nothing overflows, which the bounds on $scale and $meanHi
                // ensure, so from here on every step rounds as it would with an unbounded exponent range:
                // the step of a rescaled gap of even one subnormal ulp is normal. Only an underflowing step
                // takes this branch (the step of a single value is the value itself, exact), so every
                // other result is unchanged.
                $meanHi  *= self::VARIANCE_UPSCALE;
                $meanLo  *= self::VARIANCE_UPSCALE;
                $scale   *= self::VARIANCE_UPSCALE;
                $rescaled = true;
                /** @psalm-suppress InvalidOperand */
                $value   *= self::VARIANCE_UPSCALE;
                /** @psalm-suppress InvalidOperand */
                $delta    = ($value - $meanHi) - $meanLo;
                /** @psalm-suppress InvalidOperand */
                $step     = $delta / $count;
            }

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

        return [$count, $hasNonFinite, $scale, $weight, $rescaled];
    }

    /**
     * Reduces given collection to its value at the given percentile.
     *
     * Uses the R-7 / linear-interpolation method (the NumPy default): the
     * percentile rank is mapped onto the sorted values and interpolated between
     * the two nearest ranks. Percentile 0 is the minimum, 100 is the maximum. Values are ordered by exact
     * value, so an int beyond ±2^53 and a float take their true places, as for toMin and toMax.
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

        return static::valueAtRank($data, $percentile, 100.0);
    }

    /**
     * The R-7 value at the rank $last * $fraction / $scale of the sorted values, for toPercentile ($scale 100)
     * and toQuantile ($scale 1).
     *
     * The rank is located from the exact product of $fraction and the last index, so a quantile is never
     * first scaled to a percentile, which would round it.
     *
     * @param iterable<int|float> $data
     * @param float               $fraction in the inclusive range [0, $scale]
     * @param float               $scale    100.0 for a percentile, 1.0 for a quantile
     *
     * @return int|float|null
     */
    private static function valueAtRank(iterable $data, float $fraction, float $scale): int|float|null
    {
        $values = static::toNumericList($data);
        if (static::containsNan($values)) {
            return \NAN;
        }
        static::sortNumbers($values);

        $count = \count($values);
        if ($count === 0) {
            return null;
        }

        if ($count === 1) {
            return $values[0];
        }

        [$lowerIndex, $above, $below, $nearWeight, $fromLo] = Interpolation::percentileRank($count - 1, $fraction, $scale);

        if ($above === 0.0) {
            return $values[$lowerIndex];
        }

        return Interpolation::interpolate(
            $values[$lowerIndex],
            $values[$lowerIndex + 1],
            $above,
            $below,
            $nearWeight,
            $fromLo,
            $scale
        );
    }

    /**
     * Reduces given collection to its value at the given quantile.
     *
     * Uses the same R-7 / linear-interpolation method as {@see Reduce::toPercentile()}, with a quantile in
     * the inclusive range [0, 1] (e.g. 0.25 is the first quartile / 25th percentile). The rank is located from
     * the quantile itself, exactly, not from the quantile scaled to a percentile, which would round it first.
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

        return static::valueAtRank($data, $quantile, 1.0);
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
     * Sorts a list of numbers holding no NAN in ascending order of exact value.
     *
     * \sort() compares an int and a float as floats, so an int beyond ±2^53 can sort on the wrong side of a
     * float it rounds to: [2^62 + 500, 2.0^62] stays in that order. Only such a mix needs the exact
     * comparison, which is much slower than \sort().
     *
     * @param list<int|float> $values
     */
    private static function sortNumbers(array &$values): void
    {
        if (Arithmetic::mixesFloatAndLargeInt($values)) {
            \usort($values, Arithmetic::compare(...));
        } else {
            \sort($values);
        }
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

            if ($min === null || $max === null) {
                $min = $max = $number;
                continue;
            }
            // < and <= compare an int and a float as floats, which only goes wrong for a pair they find equal: an
            // int beyond ±2^53 and a float. Arithmetic::min() and max() compare that pair exactly. The numbers are
            // compared first, as that is cheaper than testing their types. Each bound is the first of the values
            // tied for it, as in Arithmetic::min() and max().
            $min = $min == $number && \is_int($min) !== \is_int($number)
                ? Arithmetic::min($min, $number)
                : ($number < $min ? $number : $min);
            $max = $max == $number && \is_int($max) !== \is_int($number)
                ? Arithmetic::max($max, $number)
                : ($number <= $max ? $max : $number);
        }

        if ($min === null || $max === null) {
            return 0;
        }

        // PHP's overflowing int subtraction rounds each operand to float before subtracting.
        if (\is_int($min) && \is_int($max)) {
            return Arithmetic::integerDifference($max, $min);
        }

        // Subtract an int and an integral float in the int domain: 2^53 + 1 - 2.0^53 is 1, not 0.0.
        if (\is_int($min) !== \is_int($max)) {
            $minInt = Arithmetic::toExactInt($min);
            $maxInt = Arithmetic::toExactInt($max);
            if ($minInt !== null && $maxInt !== null) {
                return (float) Arithmetic::integerDifference($maxInt, $minInt);
            }
        }

        // The bounds of a zero range are the same value, the first of the tied values, so the range is 0.0, never
        // -0.0.
        /** @psalm-suppress InvalidOperand */
        return $max - $min;
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
     * Uses Countable::count() when the iterable is countable and reports a nonzero size to choose the index,
     * so a Countable that under-reports its size picks from only its first count() elements. One that
     * reports 0 (such as a CachingIterator over a source it has not read yet) is read in full. One that
     * over-reports is read once, up to the chosen index or, when that index is past its end, in full, and
     * each of its elements is equally likely.
     *
     * @param iterable<mixed> $data
     *
     * @return mixed
     *
     * @throws \LengthException if given iterable is empty
     */
    public static function toRandomValue(iterable $data): mixed
    {
        if (\is_countable($data) && \count($data) > 0) {
            $targetIndex = \mt_rand(0, \count($data) - 1);

            // If count() over-reports, the source runs out before the target index, and a single-pass source
            // can't be read again: keep one element read, sampled uniformly (reservoir sampling), to fall back
            // on. An array's count is exact, so it needs no fallback.
            $sampleFallback = !\is_array($data);
            /** @var mixed $fallback */
            $fallback = null;

            $index = 0;
            foreach ($data as $datum) {
                /** @var mixed $datum */
                if ($targetIndex === $index) {
                    return $datum;
                }

                if ($sampleFallback && \mt_rand(0, $index) === 0) {
                    $fallback = $datum;
                }

                ++$index;
            }

            if ($index > 0) {
                return $fallback;
            }

            throw new \LengthException('Given iterable must be non-empty');
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
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
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
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
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
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
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
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
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
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
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
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
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
     * Reads the elements, never Countable::count(), so a Countable that misreports its size
     * (such as a CachingIterator) does not make an element missing or present.
     *
     * @template T
     *
     * @param iterable<T> $data
     * @param int $position
     *
     * @return T
     *
     * @throws \LengthException if given iterable does not contain item with target position.
     * @throws \InvalidArgumentException if the position is negative.
     */
    public static function toNth(iterable $data, int $position): mixed
    {
        if ($position < 0) {
            throw new \InvalidArgumentException("Position must be non-negative. Got {$position}.");
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

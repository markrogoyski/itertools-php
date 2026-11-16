<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\Precision\Arithmetic;
use IterTools\Util\Precision\MeanAccumulator;
use IterTools\Util\Values\ValueCounter;

final class Math
{
    /**
     * Returns a frequency distribution of iterable elements
     * showing how often each different value in the collection occurs.
     *
     * Equality follows the strict or coercive rules in README "Strict and Coercive Types".
     * Retains one representative per distinct value, since it is part of the output.
     *
     * @template T
     *
     * @param iterable<T> $data
     * @param bool $strict
     *
     * @return \Generator<T, int>
     */
    public static function frequencies(iterable $data, bool $strict = true): \Generator
    {
        // See ValueCounter: it pins the identity anchors a repeated value's hash depends on.
        $counter = new ValueCounter($strict, retainValues: true);

        foreach ($data as $datum) {
            $counter->add($datum);
        }

        /**
         * @var T $value
         * @var int $usageCount
         */
        foreach (Multi::zipEqual($counter->values(), $counter->counts()) as [$value, $usageCount]) {
            yield $value => $usageCount;
        }
    }

    /**
     * Returns a relative frequency distribution of iterable elements
     * showing how often each different value in the collection occurs.
     *
     * Shares the retention behavior of {@see Math::frequencies()}: one representative per
     * distinct value is retained, since it is part of the output.
     *
     * @template T
     *
     * @param iterable<T> $data
     * @param bool $strict
     *
     * @return \Generator<T, float>
     */
    public static function relativeFrequencies(iterable $data, bool $strict = true): \Generator
    {
        $frequencies = [];
        $totalCount = 0;

        foreach (self::frequencies($data, $strict) as $value => $count) {
            $frequencies[] = [$value, $count];
            $totalCount += $count;
        }

        foreach ($frequencies as [$value, $count]) {
            yield $value => ((float) $count / (float) $totalCount);
        }
    }

    /**
     * Returns a frequency distribution of iterable elements grouped by the value
     * returned from the key function, showing how often each group occurs.
     *
     * The key function must return an int or string (the only valid array key
     * types); any other return type throws a \TypeError naming the offending type.
     * This avoids PHP's implicit array-key coercion (deprecation notices for null
     * and float keys, surprising bool→1/0 collapse).
     *
     * The $strict flag controls value-hash strictness exactly as in
     * {@see Math::frequencies()}; see README "Strict and Coercive Types" — under coercive
     * comparison a numeric-string key such as "1" collapses with the int key 1. Retains one
     * representative per distinct key, since it is part of the output.
     *
     * Under strict comparison those stay separate groups, but PHP array keys canonicalize
     * numeric strings, so materializing the generator (via iterator_to_array, or a Stream
     * terminal that builds an array) merges them and keeps only one of the two counts.
     * Iterate the generator directly to observe every group, or use
     * {@see Reduce::toCountBy()}, which combines such keys into one summed count instead.
     *
     * @template T
     *
     * @param iterable<T>       $data
     * @param callable(T): mixed $keyFunc must return int|string at runtime
     * @param bool              $strict
     *
     * @return \Generator<int|string, int>
     *
     * @throws \TypeError if $keyFunc returns a value that is not int|string
     */
    public static function frequenciesBy(iterable $data, callable $keyFunc, bool $strict = true): \Generator
    {
        // See ValueCounter: it pins the identity anchors a repeated value's hash depends on.
        // Keys here are always int|string, so no anchors are ever produced, but the counter is
        // used anyway to keep this in step with frequencies().
        $counter = new ValueCounter($strict, retainValues: true);

        foreach ($data as $datum) {
            $key = $keyFunc($datum);
            if (!\is_int($key) && !\is_string($key)) {
                throw new \TypeError(
                    \sprintf('Key function must return int|string, got %s', \gettype($key)),
                );
            }

            $counter->add($key);
        }

        /**
         * @var int|string $key
         * @var int        $usageCount
         */
        foreach (Multi::zipEqual($counter->values(), $counter->counts()) as [$key, $usageCount]) {
            yield $key => $usageCount;
        }
    }

    /**
     * Returns a relative frequency distribution of iterable elements grouped by the
     * value returned from the key function, normalized to the range [0, 1].
     *
     * Shares the int|string key-function contract, $strict semantics, and retention
     * behavior of {@see Math::frequenciesBy()}.
     *
     * @template T
     *
     * @param iterable<T>       $data
     * @param callable(T): mixed $keyFunc must return int|string at runtime
     * @param bool              $strict
     *
     * @return \Generator<int|string, float>
     *
     * @throws \TypeError if $keyFunc returns a value that is not int|string
     */
    public static function relativeFrequenciesBy(iterable $data, callable $keyFunc, bool $strict = true): \Generator
    {
        $frequencies = [];
        $totalCount = 0;

        foreach (self::frequenciesBy($data, $keyFunc, $strict) as $key => $count) {
            $frequencies[] = [$key, $count];
            $totalCount += $count;
        }

        foreach ($frequencies as [$key, $count]) {
            yield $key => ((float) $count / (float) $totalCount);
        }
    }

    /**
     * Accumulate the running total over a list of numbers
     *
     * @param iterable<int|float> $numbers
     * @param int|float           $initialValue (Optional) If provided, the running total leads off with the initial value.
     *
     * @return \Generator<int|float>
     */
    public static function runningTotal(iterable $numbers, int|float|null $initialValue = null): \Generator
    {
        if ($initialValue !== null) {
            yield $initialValue;
        }

        $total = $initialValue ?? 0;
        foreach ($numbers as $number) {
            /** @psalm-suppress InvalidOperand */
            $total += $number;
            yield $total;
        }
    }

    /**
     * Accumulate the running product over a list of numbers
     *
     * @param iterable<int|float> $numbers
     * @param int|float           $initialValue (Optional) If provided, the running product leads off with the initial value.
     *
     * @return \Generator<int|float>
     */
    public static function runningProduct(iterable $numbers, int|float|null $initialValue = null): \Generator
    {
        if ($initialValue !== null) {
            yield $initialValue;
        }

        $product = $initialValue ?? 1;
        foreach ($numbers as $number) {
            /** @psalm-suppress InvalidOperand */
            $product *= $number;
            yield $product;
        }
    }

    /**
     * Accumulate the running difference over a list of numbers
     *
     * @param iterable<int|float> $numbers
     * @param int|float           $initialValue (Optional) If provided, the running difference leads off with the initial value.
     *
     * @return \Generator<int|float>
     */
    public static function runningDifference(iterable $numbers, int|float|null $initialValue = null): \Generator
    {
        if ($initialValue !== null) {
            yield $initialValue;
        }

        $difference = $initialValue ?? 0;
        foreach ($numbers as $number) {
            /** @psalm-suppress InvalidOperand */
            $difference -= $number;
            yield $difference;
        }
    }

    /**
     * Accumulate the running max over a list of numbers
     *
     * @param iterable<int|float> $numbers
     * @param int|float           $initialValue (Optional) If provided, the running max leads off with the initial value.
     *
     * @return \Generator<int|float>
     */
    public static function runningMax(iterable $numbers, int|float|null $initialValue = null): \Generator
    {
        if ($initialValue !== null) {
            yield $initialValue;
        }

        $max = ($initialValue !== null && \is_float($initialValue) && \is_nan($initialValue)) ? null : $initialValue;
        foreach ($numbers as $number) {
            if (\is_float($number) && \is_nan($number)) {
                yield $max ?? \NAN;
                continue;
            }
            if ($max === null) {
                $max = $number;
            } elseif ($max == $number && \is_int($max) !== \is_int($number)) {
                // <= compares an int and a float as floats, which only goes wrong for a pair it finds equal: an int
                // beyond ±2^53 and a float. Arithmetic::max() compares that pair exactly. The numbers are compared
                // first, as that is cheaper than testing their types.
                $max = Arithmetic::max($max, $number);
            } else {
                // As Arithmetic::max(): the first of tied values.
                $max = $number <= $max ? $max : $number;
            }
            yield $max;
        }
    }

    /**
     * Accumulate the running min over a list of numbers
     *
     * @param iterable<int|float> $numbers
     * @param int|float           $initialValue (Optional) If provided, the running min leads off with the initial value.
     *
     * @return \Generator<int|float>
     */
    public static function runningMin(iterable $numbers, int|float|null $initialValue = null): \Generator
    {
        if ($initialValue !== null) {
            yield $initialValue;
        }

        $min = ($initialValue !== null && \is_float($initialValue) && \is_nan($initialValue)) ? null : $initialValue;
        foreach ($numbers as $number) {
            if (\is_float($number) && \is_nan($number)) {
                yield $min ?? \NAN;
                continue;
            }
            if ($min === null) {
                $min = $number;
            } elseif ($min == $number && \is_int($min) !== \is_int($number)) {
                // < compares an int and a float as floats, which only goes wrong for a pair it finds equal: an int
                // beyond ±2^53 and a float. Arithmetic::min() compares that pair exactly. The numbers are compared
                // first, as that is cheaper than testing their types.
                $min = Arithmetic::min($min, $number);
            } else {
                // As Arithmetic::min(): the first of tied values, which a two-argument \min() can't promise.
                $min = $number < $min ? $number : $min;
            }
            yield $min;
        }
    }

    /**
     * Accumulate the running average (mean) over a list of numbers
     *
     * The mean of finite values is finite even when their running total overflows, as for
     * [PHP_FLOAT_MAX, PHP_FLOAT_MAX], whose running averages are both PHP_FLOAT_MAX.
     *
     * @param iterable<int|float> $numbers
     * @param int|float           $initialValue (Optional) If provided, the running average leads off with the initial value.
     *
     * @return \Generator<int|float>
     */
    public static function runningAverage(iterable $numbers, int|float|null $initialValue = null): \Generator
    {
        // The sum is kept here, as MeanAccumulator keeps it, until it overflows; the accumulator then takes over
        // from the sum so far. Calling it for every number would take twice as long.
        $count = 0;
        $sum = 0;
        $mean = null;

        if ($initialValue !== null) {
            $count = 1;
            $sum = $initialValue;
            /** @psalm-suppress InvalidOperand */
            yield $sum / $count;
        }

        foreach ($numbers as $number) {
            if ($mean !== null) {
                $mean->add($number);
                yield $mean->mean();
                continue;
            }

            $previous = $sum;
            /** @psalm-suppress InvalidOperand */
            $sum += $number;
            ++$count;

            // $sum - $sum is 0 for any finite int or float and NAN for INF or NAN, so only an INF or NAN sum
            // gets past it; the != is loose on purpose, as an int sum gives int 0, which !== 0.0 would not
            // match. A finite sum that turned infinite hands over to the accumulator from before this number.
            /** @psalm-suppress InvalidOperand */
            if ($sum - $sum != 0 && \is_infinite($sum) && \is_finite($previous)) {
                $mean = MeanAccumulator::resume($previous, $count - 1);
                $mean->add($number);
                yield $mean->mean();
                continue;
            }

            /** @psalm-suppress InvalidOperand */
            yield $sum / $count;
        }
    }
}

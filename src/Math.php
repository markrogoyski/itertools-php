<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\UniqueExtractor;

final class Math
{
    /**
     * Returns a frequency distribution of iterable elements
     * showing how often each different value in the collection occurs.
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
        $usages = [];
        $values = [];

        foreach ($data as $datum) {
            $hash = UniqueExtractor::getString($datum, $strict);

            if (!\array_key_exists($hash, $usages)) {
                $usages[$hash] = 0;
                $values[$hash] = $datum;
            }

            $usages[$hash]++;
        }

        /**
         * @var T $value
         * @var int $usageCount
         */
        foreach (Multi::zipEqual($values, $usages) as [$value, $usageCount]) {
            yield $value => $usageCount;
        }
    }

    /**
     * Returns a relative frequency distribution of iterable elements
     * showing how often each different value in the collection occurs.
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
            yield $value => ($count / $totalCount);
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
     * {@see Math::frequencies()} — under non-strict comparison a numeric-string key
     * such as "1" collapses with the int key 1.
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
        $usages = [];
        $keys   = [];

        foreach ($data as $datum) {
            $key = $keyFunc($datum);
            if (!\is_int($key) && !\is_string($key)) {
                throw new \TypeError(
                    \sprintf('Key function must return int|string, got %s', \gettype($key)),
                );
            }

            $hash = UniqueExtractor::getString($key, $strict);

            if (!\array_key_exists($hash, $usages)) {
                $usages[$hash] = 0;
                $keys[$hash]   = $key;
            }

            $usages[$hash]++;
        }

        /**
         * @var int|string $key
         * @var int        $usageCount
         */
        foreach (Multi::zipEqual($keys, $usages) as [$key, $usageCount]) {
            yield $key => $usageCount;
        }
    }

    /**
     * Returns a relative frequency distribution of iterable elements grouped by the
     * value returned from the key function, normalized to the range [0, 1].
     *
     * Shares the int|string key-function contract and $strict semantics of
     * {@see Math::frequenciesBy()}.
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
            yield $key => ($count / $totalCount);
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
            $max = $max === null ? $number : \max($max, $number);
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
            $min = $min === null ? $number : \min($min, $number);
            yield $min;
        }
    }

    /**
     * Accumulate the running average (mean) over a list of numbers
     *
     * @param iterable<int|float> $numbers
     * @param int|float           $initialValue (Optional) If provided, the running average leads off with the initial value.
     *
     * @return \Generator<int|float>
     */
    public static function runningAverage(iterable $numbers, int|float|null $initialValue = null): \Generator
    {
        $n = 0;
        foreach (Math::runningTotal($numbers, $initialValue) as $runningTotal) {
            $n++;
            /** @psalm-suppress InvalidOperand */
            yield $runningTotal / $n;
        }
    }
}

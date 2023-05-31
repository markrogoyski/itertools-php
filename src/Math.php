<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\UniqueExtractor;

class Math
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
        $usages = [];
        $values = [];
        $totalCount = 0;

        foreach ($data as $datum) {
            $hash = UniqueExtractor::getString($datum, $strict);

            if (!\array_key_exists($hash, $usages)) {
                $usages[$hash] = 0;
                $values[$hash] = $datum;
            }

            $usages[$hash]++;
            $totalCount++;
        }

        /**
         * @var T $value
         * @var int $usageCount
         */
        foreach (Multi::zipEqual($values, $usages) as [$value, $usageCount]) {
            yield $value => ($usageCount / $totalCount);
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
    public static function runningTotal(iterable $numbers, $initialValue = null): \Generator
    {
        if ($initialValue !== null) {
            yield $initialValue;
        }

        $total = $initialValue ?? 0;
        foreach ($numbers as $number) {
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
    public static function runningProduct(iterable $numbers, $initialValue = null): \Generator
    {
        if ($initialValue !== null) {
            yield $initialValue;
        }

        $product = $initialValue ?? 1;
        foreach ($numbers as $number) {
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
    public static function runningDifference(iterable $numbers, $initialValue = null): \Generator
    {
        if ($initialValue !== null) {
            yield $initialValue;
        }

        $difference = $initialValue ?? 0;
        foreach ($numbers as $number) {
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
    public static function runningMax(iterable $numbers, $initialValue = null): \Generator
    {
        if ($initialValue !== null) {
            yield $initialValue;
        }

        $max = $initialValue ?? -\INF;
        foreach ($numbers as $number) {
            $max = \max($max, $number);
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
    public static function runningMin(iterable $numbers, $initialValue = null): \Generator
    {
        if ($initialValue !== null) {
            yield $initialValue;
        }

        $min = $initialValue ?? \INF;
        foreach ($numbers as $number) {
            $min = \min($min, $number);
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
    public static function runningAverage(iterable $numbers, $initialValue = null): \Generator
    {
        $n = 0;
        foreach (Math::runningTotal($numbers, $initialValue) as $runningTotal) {
            $n++;
            yield $runningTotal / $n;
        }
    }
}

<?php

declare(strict_types=1);

namespace IterTools;

class Math
{
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

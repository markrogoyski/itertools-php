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
}

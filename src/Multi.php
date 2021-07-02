<?php

declare(strict_types=1);

namespace IterTools;

class Multi
{
    /**
     * Iterate multiple iterable collections simultaneously.
     *
     * Make an iterator that aggregates items from multiple iterators.
     * Similar to Python's zip function.
     *
     * For uneven lengths, iterations stops when the shortest iterable is exhausted.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \MultipleIterator<mixed>
     */
    public static function zip(iterable ...$iterables): \MultipleIterator
    {
        $zippedIterator = new \MultipleIterator();
        foreach ($iterables as $iterable) {
            $zippedIterator->attachIterator(Util::makeIterator($iterable));
        }

        return $zippedIterator;
    }

    /**
     * Iterate multiple iterable collections simultaneously.
     *
     * Make an iterator that aggregates items from multiple iterators.
     * Similar to Python's zip_longest function
     *
     * Iteration continues until the longest iterable is exhausted.
     * For uneven lengths, the exhausted iterables will produce null for the remaining iterations.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \MultipleIterator<mixed>
     */
    public static function zipLongest(iterable ...$iterables): \MultipleIterator
    {
        $zippedIterator = new \MultipleIterator();
        foreach ($iterables as $iterable) {
            $zippedIterator->attachIterator(Util::makeIterator($iterable));
        }
        $zippedIterator->setFlags(\MultipleIterator::MIT_NEED_ANY);

        return $zippedIterator;
    }
}

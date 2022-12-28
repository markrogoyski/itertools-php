<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Utility\StrictMultipleIterator;

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

    /**
     * Iterate multiple iterable collections simultaneously.
     *
     * Works like Multi::zipStrict() method
     * but throws OutOfRangeException if at least one iterator ends before the others.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \MultipleIterator<mixed>
     * @throws \OutOfRangeException
     */
    public static function zipStrict(iterable ...$iterables): \MultipleIterator
    {
        $zippedIterator = new StrictMultipleIterator(\MultipleIterator::MIT_NEED_ALL);
        foreach ($iterables as $iterable) {
            $zippedIterator->attachIterator(Util::makeIterator($iterable));
        }
        $zippedIterator->setFlags(\MultipleIterator::MIT_NEED_ALL);

        return $zippedIterator;
    }

    /**
     * Chain multiple iterables together into a single iteration.
     *
     * Makes a single continuous sequence out of multiple sequences.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    public static function chain(iterable ...$iterables): \Generator
    {
        foreach ($iterables as $iterable) {
            yield from $iterable;
        }
    }
}

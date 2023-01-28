<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\IteratorFactory;
use IterTools\Util\StrictMultipleIterator;

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
     * @return \Generator<array<mixed>>
     */
    public static function zip(iterable ...$iterables): \Generator
    {
        $zippedIterator = new \MultipleIterator();
        foreach ($iterables as $iterable) {
            $zippedIterator->attachIterator(IteratorFactory::makeIterator($iterable));
        }

        foreach ($zippedIterator as $values) {
            yield $values;
        }
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
     * @return \Generator<array<mixed>>
     */
    public static function zipLongest(iterable ...$iterables): \Generator
    {
        $zippedIterator = new \MultipleIterator();
        foreach ($iterables as $iterable) {
            $zippedIterator->attachIterator(IteratorFactory::makeIterator($iterable));
        }
        $zippedIterator->setFlags(\MultipleIterator::MIT_NEED_ANY);

        foreach ($zippedIterator as $values) {
            yield $values;
        }
    }

    /**
     * Iterate multiple iterable collections of equal lengths simultaneously.
     *
     * Works like Multi::zip() method but throws \LengthException if lengths not equal,
     * i.e., at least one iterator ends before the others.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<array<mixed>>
     * @throws \LengthException if during iteration one iterable ends before the others, indicating unequal lengths
     */
    public static function zipEqual(iterable ...$iterables): \Generator
    {
        $zippedIterator = new StrictMultipleIterator(\MultipleIterator::MIT_NEED_ALL);
        foreach ($iterables as $iterable) {
            $zippedIterator->attachIterator(IteratorFactory::makeIterator($iterable));
        }
        $zippedIterator->setFlags(\MultipleIterator::MIT_NEED_ALL);

        foreach ($zippedIterator as $values) {
            yield $values;
        }
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
            foreach ($iterable as $item) {
                yield $item;
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\Iterators\JustifyMultipleIterator;
use IterTools\Util\Iterators\StrictMultipleIterator;

final class Multi
{
    /**
     * Iterate multiple iterable collections simultaneously.
     *
     * Make an iterator that aggregates items from multiple iterators.
     * Similar to Python's zip function.
     *
     * For uneven lengths, iterations stops when the shortest iterable is exhausted.
     *
     * Note: Passing the same iterator instance more than once (e.g., zip($it, $it)) will not work
     * as expected because PHP's MultipleIterator silently ignores duplicate attachments.
     * To chunk a stream, use Single::chunkwise() instead.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<array<mixed>>
     */
    public static function zip(iterable ...$iterables): \Generator
    {
        $zippedIterator = new \MultipleIterator();
        foreach ($iterables as $iterable) {
            $zippedIterator->attachIterator(Transform::toIterator($iterable));
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
     * Note: Passing the same iterator instance more than once will not work as expected
     * because PHP's MultipleIterator silently ignores duplicate attachments.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<array<mixed>>
     */
    public static function zipLongest(iterable ...$iterables): \Generator
    {
        $zippedIterator = new \MultipleIterator();
        foreach ($iterables as $iterable) {
            $zippedIterator->attachIterator(Transform::toIterator($iterable));
        }
        $zippedIterator->setFlags(\MultipleIterator::MIT_NEED_ANY);

        foreach ($zippedIterator as $values) {
            yield $values;
        }
    }

    /**
     * Iterate multiple iterable collections simultaneously, using a default filler value if lengths are not equal
     *
     * Make an iterator that aggregates items from multiple iterators.
     * Similar to Python's zip_longest function
     *
     * Iteration continues until the longest iterable is exhausted.
     * For uneven lengths, the exhausted iterables will produce the $filler value for the remaining iterations.
     *
     * Note: Passing the same iterator instance more than once will not work as expected
     * because PHP's MultipleIterator silently ignores duplicate attachments.
     *
     * @param mixed $filler
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<array<mixed>>
     */
    public static function zipFilled(mixed $filler, iterable ...$iterables): \Generator
    {
        $iterator = new JustifyMultipleIterator($filler, ...$iterables);

        foreach ($iterator as $values) {
            yield $values;
        }
    }

    /**
     * Iterate multiple iterable collections of equal lengths simultaneously.
     *
     * Works like Multi::zip() method but throws \LengthException if lengths not equal,
     * i.e., at least one iterator ends before the others.
     *
     * Note: Passing the same iterator instance more than once will not work as expected
     * because PHP's MultipleIterator silently ignores duplicate attachments.
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
            /** @psalm-suppress MixedArgumentTypeCoercion */
            $zippedIterator->attachIterator(Transform::toIterator($iterable));
        }
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
                /** @var mixed $item */
                yield $item;
            }
        }
    }

    /**
     * Yield one value at a time from multiple iterables in round-robin order.
     *
     * On each round, takes one value from each iterable that still has values; once an iterable
     * is exhausted, it is skipped in subsequent rounds. Iteration ends when every iterable is
     * exhausted. Unlike zip, values are yielded individually rather than as tuples.
     *
     * Keys from the source iterables are discarded; the output is sequentially re-indexed.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    public static function roundRobin(iterable ...$iterables): \Generator
    {
        $iterators = [];
        foreach ($iterables as $iterable) {
            $iterator = Transform::toIterator($iterable);
            $iterator->rewind();
            if ($iterator->valid()) {
                $iterators[] = $iterator;
            }
        }

        while (\count($iterators) > 0) {
            $stillActive = [];
            foreach ($iterators as $iterator) {
                /** @var mixed $value */
                $value = $iterator->current();
                yield $value;
                $iterator->next();
                if ($iterator->valid()) {
                    $stillActive[] = $iterator;
                }
            }
            $iterators = $stillActive;
        }
    }
}

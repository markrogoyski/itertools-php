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

    /**
     * Transpose a sequence of rows into columns — the inverse of zip.
     *
     * Each element of $rows must itself be iterable. Yields one array per column whose values
     * are taken positionally from each row. Mirrors Multi::zip semantics for uneven lengths:
     * the column count is the width of the shortest row, so trailing cells of longer rows are
     * discarded. For any uniform-positive-width input, the round-trip identity
     * Multi::zip(...Multi::unzip($rows)) holds. The identity does not extend to zero-width
     * input (e.g. [[], []] yields no columns, losing the row count) nor to output produced by
     * zipFilled / zipLongest (truncation drops the padded trailing cells).
     *
     * Note: although this method returns a Generator, it is not lazy in any meaningful sense —
     * the entire input must be buffered before the first column can be yielded, since column 0
     * cannot be emitted until every row's first cell has been seen. The Generator return shape
     * is for consistency with the rest of Multi.
     *
     * Keys from rows and from cells within rows are both discarded; output columns are
     * sequentially indexed lists.
     *
     * @param iterable<mixed> $rows
     *
     * @return \Generator<array<mixed>>
     *
     * @throws \InvalidArgumentException if any element of $rows is not iterable
     */
    public static function unzip(iterable $rows): \Generator
    {
        $buffered = [];
        $shortest = null;
        $rowIndex = 0;
        foreach ($rows as $row) {
            if (!\is_iterable($row)) {
                throw new \InvalidArgumentException(
                    "Multi::unzip requires every row to be iterable; row {$rowIndex} is "
                    . \get_debug_type($row)
                );
            }
            $values = [];
            foreach ($row as $value) {
                /** @var mixed $value */
                $values[] = $value;
            }
            $buffered[] = $values;
            $width = \count($values);
            if ($shortest === null || $width < $shortest) {
                $shortest = $width;
            }
            ++$rowIndex;
        }

        if ($shortest === null || $shortest === 0) {
            return;
        }

        for ($i = 0; $i < $shortest; ++$i) {
            $column = [];
            foreach ($buffered as $row) {
                /** @var mixed $cell */
                $cell = $row[$i];
                $column[] = $cell;
            }
            yield $column;
        }
    }
}

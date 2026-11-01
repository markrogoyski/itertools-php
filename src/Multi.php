<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\Iterators\JustifyMultipleIterator;
use IterTools\Util\Iterators\ShortestMultipleIterator;
use IterTools\Util\Iterators\StrictMultipleIterator;
use IterTools\Util\MergeSortedHeap;

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
     * An iterator or generator passed more than once, directly or through a Stream or other \IteratorAggregate
     * that returns it, is read as independent copies of itself: each step reads it once and gives its value to
     * every position it was passed in. To chunk an iterable, as Python's zip(it, it) does, use
     * Single::chunkwise() instead.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<array<mixed>>
     */
    public static function zip(iterable ...$iterables): \Generator
    {
        $iterator = new ShortestMultipleIterator(...$iterables);

        foreach ($iterator as $values) {
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
     * An iterator or generator passed more than once, directly or through a Stream or other \IteratorAggregate
     * that returns it, is read as independent copies of itself: each step reads it once and gives its value to
     * every position it was passed in.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<array<mixed>>
     */
    public static function zipLongest(iterable ...$iterables): \Generator
    {
        $iterator = new JustifyMultipleIterator(null, ...\array_values($iterables));

        foreach ($iterator as $values) {
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
     * An iterator or generator passed more than once, directly or through a Stream or other \IteratorAggregate
     * that returns it, is read as independent copies of itself: each step reads it once and gives its value to
     * every position it was passed in.
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
     * An iterator or generator passed more than once, directly or through a Stream or other \IteratorAggregate
     * that returns it, is read as independent copies of itself: each step reads it once and gives its value to
     * every position it was passed in.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<array<mixed>>
     * @throws \LengthException if during iteration one iterable ends before the others, indicating unequal lengths
     */
    public static function zipEqual(iterable ...$iterables): \Generator
    {
        $iterator = new StrictMultipleIterator(...$iterables);

        foreach ($iterator as $values) {
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
     * Lazily merge sorted iterables, preserving source order for equal values.
     *
     * Inputs must be sorted in non-decreasing order. An int and a float compare by exact value. Source keys
     * are discarded.
     *
     * An iterator or generator passed more than once, directly or through a Stream or other \IteratorAggregate
     * that returns it, is read as independent copies of itself, one per position it was passed in, each ordered
     * among equal values by its position. Memory is then not limited to one pending value per source: the
     * copies share a buffer holding the run of equal values between them. That run can be the whole source, and
     * for an infinite source whose values are all equal the buffer grows without bound.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    public static function mergeSorted(iterable ...$iterables): \Generator
    {
        yield from self::mergeSortedInternal(
            static fn (mixed $value): mixed => $value,
            'Multi::mergeSorted cannot order NAN',
            ...\array_values($iterables),
        );
    }

    /**
     * Lazily merge iterables sorted by a projected key.
     *
     * Inputs must be sorted in non-decreasing projected-key order. An int and a float key compare by exact
     * value. Source keys are discarded.
     *
     * An iterator or generator passed more than once, directly or through a Stream or other \IteratorAggregate
     * that returns it, is read as independent copies of itself, one per position it was passed in, each ordered
     * among equal keys by its position, and each projecting its own values. Memory is then not limited to one
     * pending value per source: the copies share a buffer holding the run of equal-key values between them. That
     * run can be the whole source, and for an infinite source whose keys are all equal the buffer grows without
     * bound.
     *
     * @param callable(mixed): mixed $keyFunc
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    public static function mergeSortedBy(callable $keyFunc, iterable ...$iterables): \Generator
    {
        yield from self::mergeSortedInternal(
            $keyFunc,
            'Multi::mergeSortedBy key function returned NAN',
            ...\array_values($iterables),
        );
    }

    /**
     * @param callable(mixed): mixed $keyFunc
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    private static function mergeSortedInternal(
        callable $keyFunc,
        string $nanMessage,
        iterable ...$iterables,
    ): \Generator {
        $heap = new MergeSortedHeap();

        // Ordinals are list indexes, counted independently of the variadic's own keys: named arguments give
        // those keys strings, which would collapse to the same heap tie-breaker. Every source is resolved
        // before any is read, and the list holds each iterator, so an id cannot be reused by a later one.
        $iterators = [];
        $ordinalsById = [];
        foreach ($iterables as $iterable) {
            $iterator = Transform::toIterator($iterable);
            $ordinalsById[\spl_object_id($iterator)][] = \count($iterators);
            $iterators[] = $iterator;
        }

        // An iterator passed more than once is split into one copy per position, each keeping its position's
        // ordinal, so it reads as independent copies of itself. Sharing one cursor would skip values. The copies
        // are of its values alone: tee buffers keys too, and the merge never reads a source's keys.
        foreach ($ordinalsById as $ordinals) {
            if (\count($ordinals) > 1) {
                $copies = Transform::tee(self::mergeValues($iterators[$ordinals[0]]), \count($ordinals));
                foreach ($ordinals as $index => $ordinal) {
                    $iterators[$ordinal] = $copies[$index];
                }
            }
        }

        foreach ($iterators as $sourceOrdinal => $iterator) {
            $iterator->rewind();
            if ($iterator->valid()) {
                $value = $iterator->current();
                $key = $keyFunc($value);
                self::assertMergeKeyIsNotNan($key, $nanMessage);
                $heap->insert([$key, $sourceOrdinal, $value, $iterator]);
            }
        }

        while (!$heap->isEmpty()) {
            [$_key, $sourceOrdinal, $value, $iterator] = $heap->extract();
            yield $value;

            $iterator->next();
            if ($iterator->valid()) {
                $nextValue = $iterator->current();
                $nextKey = $keyFunc($nextValue);
                self::assertMergeKeyIsNotNan($nextKey, $nanMessage);
                $heap->insert([$nextKey, $sourceOrdinal, $nextValue, $iterator]);
            }
        }
    }

    /**
     * @param \Iterator<mixed> $iterator
     *
     * @return \Generator<int, mixed>
     */
    private static function mergeValues(\Iterator $iterator): \Generator
    {
        for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
            yield $iterator->current();
        }
    }

    private static function assertMergeKeyIsNotNan(mixed $key, string $message): void
    {
        if (\is_float($key) && \is_nan($key)) {
            throw new \InvalidArgumentException($message);
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
     * Lazy: no source is converted, rewound or read until its first turn comes, and a source is
     * advanced only when its next turn comes. Stopping after a yielded value therefore pulls no
     * further value from any source and starts no source whose turn has not come, so a
     * side-effecting source (file handle, HTTP pagination, database cursor) is not over-read.
     *
     * Each source is rewound on its first turn: an iterator that was already advanced restarts
     * from its first element, and a generator that was already started throws at that point,
     * after the values of earlier sources have been yielded.
     *
     * An iterator or generator passed more than once, directly or through a Stream or other \IteratorAggregate
     * that returns it, is read as independent copies of itself: each round reads it once, at the turn of its
     * first position, and gives that value to every position it was passed in, so the copies run out together.
     * Only that one value is held.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    public static function roundRobin(iterable ...$iterables): \Generator
    {
        // A source is converted and rewound on its first turn and advanced on every later one. Started sources
        // are keyed by the id of their iterator, so an iterator passed more than once is one source: it is
        // advanced at the first of its positions to take a turn in a round, and every later position in that
        // round gives the value read there. The entries hold each iterator, so its id cannot be reused by a
        // later one while sources are still being started, which all happens in the first round. $values holds
        // a started source's value for the round it was last read in, and loses it when the source runs out;
        // at the end of each round, the iterators of sources that ran out are released.
        $iterators   = [];
        $readInRound = [];
        $values      = [];
        $idsByIndex  = [];
        $round       = 0;
        while (\count($iterables) > 0) {
            ++$round;
            foreach ($iterables as $index => $iterable) {
                if (isset($idsByIndex[$index])) {
                    $id = $idsByIndex[$index];
                } else {
                    $iterator = Transform::toIterator($iterable);
                    $id       = \spl_object_id($iterator);
                    $idsByIndex[$index] = $id;
                    if (!isset($iterators[$id])) {
                        $iterators[$id] = $iterator;
                        $iterator->rewind();
                        $readInRound[$id] = $round;
                        if ($iterator->valid()) {
                            $values[$id] = [$iterator->current()];
                        }
                    }
                }
                if ($readInRound[$id] < $round) {
                    $iterator = $iterators[$id];
                    $iterator->next();
                    $readInRound[$id] = $round;
                    if ($iterator->valid()) {
                        $values[$id] = [$iterator->current()];
                    } else {
                        unset($values[$id]);
                    }
                }
                if (!isset($values[$id])) {
                    unset($iterables[$index], $idsByIndex[$index]);
                    continue;
                }
                yield $values[$id][0];
            }
            foreach (\array_keys($iterators) as $id) {
                if (!isset($values[$id])) {
                    unset($iterators[$id], $readInRound[$id]);
                }
            }
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

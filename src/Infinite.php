<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\Iterators\MemoizedIterable;
use IterTools\Util\Iterators\ReplayIterator;

final class Infinite
{
    /**
     * Count sequentially forever
     *
     * Every value is an int. The count stops at the edge of the integer range: once the next value
     * would be greater than PHP_INT_MAX or less than PHP_INT_MIN, advancing throws instead of
     * yielding a float.
     *
     * @param int $start Starting point
     * @param int $step  How much to increment the count at each step
     *
     * @return \Generator<int>
     *
     * @throws \InvalidArgumentException if step is zero
     * @throws \OverflowException if the count would go past the integer range
     *
     * Psalm infers `never` for a generator whose only exits are throws, ignoring the yield.
     * @psalm-suppress InvalidReturnType
     */
    public static function count(int $start = 1, int $step = 1): \Generator
    {
        if ($step === 0) {
            throw new \InvalidArgumentException("Step must be non-zero. Got 0. Use Infinite::repeat() to yield the same value infinitely.");
        }

        $i = $start;
        while (true) {
            yield $i;

            if ($step > 0 ? $i > \PHP_INT_MAX - $step : $i < \PHP_INT_MIN - $step) {
                throw new \OverflowException("Count overflowed the integer range: {$i} + {$step} is out of range.");
            }
            $i += $step;
        }
    }

    /**
     * Cycle through the elements of a collection sequentially forever
     *
     * Arrays are cycled directly. Any other iterable is read only once, lazily: the first pass pulls
     * elements from the source as they are consumed and caches them, and every later pass replays the
     * cache. This makes one-pass sources (generators, tee copies, database cursors, wrappers around
     * them) cycle correctly, at the cost of holding one copy of the source's elements in memory.
     * Source keys are preserved on every pass. An empty source yields nothing.
     *
     * @param iterable<mixed> $iterable Finite array or traversable object
     *
     * @return \InfiniteIterator<mixed, mixed, \Iterator<mixed, mixed>>
     */
    public static function cycle(iterable $iterable): \InfiniteIterator
    {
        /** @var \Iterator<mixed, mixed> $iterator */
        $iterator = \is_array($iterable)
            ? new \ArrayIterator($iterable)
            : new ReplayIterator(new MemoizedIterable($iterable));

        return new \InfiniteIterator($iterator);
    }

    /**
     * Repeat an item forever
     *
     * @param mixed $item
     *
     * @return \Generator<mixed>
     */
    public static function repeat(mixed $item): \Generator
    {
        while (true) {
            yield $item;
        }
    }

    /**
     * Iterate forever by repeatedly applying a function to its previous output
     *
     * Yields $initial, then $function($initial), then $function($function($initial)), ...
     *
     * @template T
     *
     * @param T            $initial  Starting value (yielded first)
     * @param callable(T):T $function Applied to the previously yielded value to produce the next
     *
     * @return \Generator<T>
     */
    public static function iterate(mixed $initial, callable $function): \Generator
    {
        $current = $initial;
        while (true) {
            yield $current;
            $current = $function($current);
        }
    }

    /**
     * Yield values produced by a zero-arg supplier forever.
     *
     * @template T
     *
     * @param callable():T $supplier
     *
     * @return \Generator<T>
     */
    public static function generate(callable $supplier): \Generator
    {
        while (true) {
            yield $supplier();
        }
    }
}

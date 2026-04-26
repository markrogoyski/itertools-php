<?php

declare(strict_types=1);

namespace IterTools;

final class Infinite
{
    /**
     * Count sequentially forever
     *
     * @param int $start Starting point
     * @param int $step  How much to increment the count at each step
     *
     * @return \Generator<int>
     */
    public static function count(int $start = 1, int $step = 1): \Generator
    {
        if ($step === 0) {
            throw new \InvalidArgumentException("Step must be non-zero. Got 0. Use Infinite::repeat() to yield the same value infinitely.");
        }

        for ($i = $start; $i < \INF; $i += $step) {
            yield $i;
        }
    }

    /**
     * Cycle through the elements of a collection sequentially forever
     *
     * @param iterable<mixed> $iterable Finite array or traversable object
     *
     * @return \InfiniteIterator<mixed, mixed, \Iterator<mixed, mixed>>
     */
    public static function cycle(iterable $iterable): \InfiniteIterator
    {
        if ($iterable instanceof \IteratorAggregate) {
            $inner = $iterable->getIterator();
            if ($inner instanceof \Generator) {
                $iterable = \iterator_to_array($inner, false);
            }
        } elseif ($iterable instanceof \Generator) {
            $iterable = \iterator_to_array($iterable, false);
        }

        return new \InfiniteIterator(Transform::toIterator($iterable));
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
}

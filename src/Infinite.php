<?php

declare(strict_types=1);

namespace IterTools;

class Infinite
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
        for ($i = $start; $i < \INF; $i += $step) {
            yield $i;
        }
    }

    /**
     * Cycle through the elements of a collection sequentially forever
     *
     * @param iterable<mixed> $iterable Finite array or traversable object
     *
     * @return \InfiniteIterator<mixed>
     */
    public static function cycle(iterable $iterable): \InfiniteIterator
    {
        if ($iterable instanceof \Generator) {
            $iterable = \iterator_to_array($iterable);
        }
        return new \InfiniteIterator(Util::makeIterator($iterable));
    }

    /**
     * Repeat an item forever
     *
     * @param mixed $item
     *
     * @return \Generator<mixed>
     */
    public static function repeat($item): \Generator
    {
        // @phpstan-ignore-next-line
        while (true) {
            yield $item;
        }
    }
}

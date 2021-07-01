<?php

declare(strict_types=1);

namespace IterTools;

class Multi
{
    /**
     * Iterate multiple iterable collections simultaneously.
     *
     * Make an iterator that aggregates items from multiple iterators.
     * Similar to Python's zip function
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \MultipleIterator<mixed>
     */
    public static function zip(iterable ...$iterables): \MultipleIterator
    {
        $zippedIterator = new \MultipleIterator();
        foreach ($iterables as $iterable) {
            $zippedIterator->attachIterator(self::makeIterator($iterable));
        }

        return $zippedIterator;
    }

    /**
     * @param iterable<mixed> $iterable
     *
     * @return \Iterator<mixed>|\IteratorIterator<mixed>|\ArrayIterator<mixed>
     */
    private static function makeIterator(iterable $iterable): \Iterator
    {
        switch (true) {
            case $iterable instanceof \Iterator:
                return $iterable;

            case $iterable instanceof \Traversable:
                return new \IteratorIterator($iterable);

            case \is_array($iterable):
                return new \ArrayIterator($iterable);
        }

        throw new \LogicException(\gettype($iterable) . ' type is not an expected iterable type (Iterator|Traversable|array)');
    }
}

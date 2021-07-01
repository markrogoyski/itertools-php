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
            $zippedIterator->attachIterator(Util::makeIterator($iterable));
        }

        return $zippedIterator;
    }
}

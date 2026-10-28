<?php

namespace IterTools\Util\Iterators;

/**
 * @internal
 * Iterates until the shortest iterable is exhausted.
 */
final class ShortestMultipleIterator extends AbstractMultipleIterator
{
    /**
     * {@inheritDoc}
     */
    public function valid(): bool
    {
        if ($this->iterators === []) {
            return false;
        }

        foreach ($this->iterators as $iterator) {
            if (!$iterator->valid()) {
                return false;
            }
        }

        return true;
    }
}

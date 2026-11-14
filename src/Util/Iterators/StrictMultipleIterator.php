<?php

declare(strict_types=1);

namespace IterTools\Util\Iterators;

/**
 * @internal
 * Iterates until the iterables are exhausted, which must happen at the same step.
 */
final class StrictMultipleIterator extends AbstractMultipleIterator
{
    /**
     * {@inheritDoc}
     *
     * @throws \LengthException if at least one iterable is exhausted before the others
     */
    public function valid(): bool
    {
        $anyValid = false;
        $allValid = true;
        foreach ($this->iterators as $iterator) {
            if ($iterator->valid()) {
                $anyValid = true;
            } else {
                $allValid = false;
            }
        }

        if ($anyValid && !$allValid) {
            throw new \LengthException('Iterables of unequal sizes');
        }

        return $anyValid;
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Util\Iterators;

/**
 * @internal
 * Iterates until the longest iterable is exhausted, giving $filler for the iterables already exhausted.
 */
final class JustifyMultipleIterator extends AbstractMultipleIterator
{
    /**
     * @var mixed
     */
    protected $filler;

    /**
     * @param mixed $filler
     * @param iterable<mixed> ...$iterables
     */
    public function __construct(mixed $filler, iterable ...$iterables)
    {
        parent::__construct(...$iterables);
        $this->filler = $filler;
    }

    /**
     * {@inheritDoc}
     */
    public function valid(): bool
    {
        foreach ($this->iterators as $iterator) {
            if ($iterator->valid()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function currentOf(\Iterator $iterator): mixed
    {
        return $iterator->valid() ? $iterator->current() : $this->filler;
    }
}

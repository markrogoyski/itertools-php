<?php

declare(strict_types=1);

namespace IterTools\Util\Iterators;

/**
 * @internal
 *
 * @template TKey
 * @template TValue
 *
 * @implements \Iterator<TKey, TValue>
 */
final class RelatedIterator implements \Iterator
{
    /**
     * @param TeeIterator<TKey, TValue> $parentIterator
     * @param int $id
     */
    public function __construct(
        private readonly TeeIterator $parentIterator,
        private int $id,
    ) {
    }

    /**
     * A clone is its own branch: it continues independently from the position of the original, and that position
     * is where it starts, so it can be rewound until it advances from there.
     */
    public function __clone()
    {
        $this->id = $this->parentIterator->fork($this);
    }

    public function __destruct()
    {
        $this->parentIterator->detach($this);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function next(): void
    {
        $this->parentIterator->next($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return TValue
     */
    public function current(): mixed
    {
        /** @var TValue */
        return $this->parentIterator->current($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return TKey
     */
    public function key(): mixed
    {
        /** @var TKey */
        return $this->parentIterator->key($this);
    }

    /**
     * {@inheritDoc}
     */
    public function valid(): bool
    {
        return $this->parentIterator->valid($this);
    }

    /**
     * {@inheritDoc}
     *
     * Rewinding is a no-op until the iterator advances from where it started (the first element, or for a clone
     * the position it was cloned at); after that the elements it has moved past are gone.
     *
     * @throws \LogicException if the iterator has advanced
     */
    public function rewind(): void
    {
        if ($this->parentIterator->hasAdvanced($this)) {
            throw new \LogicException('RelatedIterator cannot be rewinded after it has advanced');
        }
    }
}

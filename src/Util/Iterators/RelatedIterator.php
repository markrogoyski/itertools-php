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
     * @var bool
     */
    private bool $isRewinded = false;

    /**
     * @param TeeIterator<TKey, TValue> $parentIterator
     * @param int $id
     */
    public function __construct(
        private readonly TeeIterator $parentIterator,
        private readonly int $id,
    ) {
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
     * @throws \LogicException for repeated calls
     */
    public function rewind(): void
    {
        if (!$this->isRewinded) {
            $this->isRewinded = true;
        } else {
            throw new \LogicException('RelatedIterator cannot be rewinded repeatedly');
        }
    }
}

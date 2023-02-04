<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 *
 * @template TKey
 * @template TValue
 *
 * @implements \Iterator<TKey, TValue>
 */
class RelatedIterator implements \Iterator
{
    /**
     * @var TeeIterator<TKey, TValue>
     */
    protected TeeIterator $parentIterator;
    /**
     * @var int
     */
    private int $id;
    /**
     * @var bool
     */
    private bool $isRewinded = false;

    /**
     * @param TeeIterator<TKey, TValue> $parentIterator
     * @param int $id
     */
    public function __construct(TeeIterator $parentIterator, int $id)
    {
        $this->parentIterator = $parentIterator;
        $this->id = $id;
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
    public function current()
    {
        return $this->parentIterator->current($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return TKey
     */
    public function key()
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

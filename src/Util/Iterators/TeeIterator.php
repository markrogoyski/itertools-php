<?php

declare(strict_types=1);

namespace IterTools\Util\Iterators;

use IterTools\Util\Iterators\RelatedIterator;

/**
 * @internal
 *
 * @template TKey
 * @template TValue
 */
class TeeIterator extends \NoRewindIterator
{
    /**
     * @var array<RelatedIterator<TKey, TValue>>
     */
    private array $related;
    /**
     * @var array<int>
     */
    private array $positions;
    /**
     * @var array<int, TKey>
     */
    private array $cacheKeys = [];
    /**
     * @var array<int, TValue>
     */
    private array $cacheValues = [];

    /**
     * @param \Iterator<TKey, TValue> $iterator
     * @param positive-int $relatedCount
     */
    public function __construct(\Iterator $iterator, int $relatedCount)
    {
        parent::__construct($iterator);
        $iterator->rewind();

        for ($i=0; $i<$relatedCount; ++$i) {
            $this->related[] = new RelatedIterator($this, $i);
            $this->positions[] = 0;
        }

        if (parent::valid()) {
            $this->cacheKeys[] = parent::key();
            $this->cacheValues[] = parent::current();
        }
    }

    /**
     * @return array<RelatedIterator<TKey, TValue>>
     */
    public function getRelatedIterators(): array
    {
        return $this->related;
    }

    /**
     * {@inheritDoc}
     *
     * @param RelatedIterator<TKey, TValue>|null $related
     */
    public function next(RelatedIterator $related = null): void
    {
        if ($related === null) {
            throw new \LogicException();
        }

        if (!$related->valid()) {
            return;
        }

        [$relPos, $minPos, $maxPos] = [$this->getPosition($related), \min($this->positions), \max($this->positions)];

        if ($relPos === $maxPos) {
            parent::next();
            if (parent::valid()) {
                $this->cacheKeys[] = parent::key();
                $this->cacheValues[] = parent::current();
            }
        }

        $this->incrementPosition($related);

        if ($minPos < \min($this->positions)) {
            unset($this->cacheKeys[$minPos]);
            unset($this->cacheValues[$minPos]);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param RelatedIterator<TKey, TValue>|null $related
     */
    #[\ReturnTypeWillChange]
    public function current(RelatedIterator $related = null)
    {
        if ($related === null) {
            throw new \LogicException();
        }

        return $this->cacheValues[$this->getPosition($related)] ?? parent::current();
    }

    /**
     * {@inheritDoc}
     *
     * @param RelatedIterator<TKey, TValue>|null $related
     *
     * @return TKey
     */
    #[\ReturnTypeWillChange]
    public function key(RelatedIterator $related = null)
    {
        if ($related === null) {
            throw new \LogicException();
        }

        return $this->cacheKeys[$this->getPosition($related)] ?? parent::key();
    }

    /**
     * {@inheritDoc}
     *
     * @param RelatedIterator<TKey, TValue>|null $related
     */
    public function valid(RelatedIterator $related = null): bool
    {
        if ($related === null) {
            throw new \LogicException();
        }

        [$relPos, $maxPos] = [$this->getPosition($related), \max($this->positions)];

        return $relPos !== $maxPos || parent::valid();
    }

    /**
     * @param RelatedIterator<TKey, TValue> $related
     *
     * @return int
     */
    private function getPosition(RelatedIterator $related): int
    {
        return $this->positions[$related->getId()];
    }

    /**
     * @param RelatedIterator<TKey, TValue> $related
     *
     * @return void
     */
    private function incrementPosition(RelatedIterator $related): void
    {
        $this->positions[$related->getId()]++;
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Util;

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
     * @param int $relatedCount
     */
    public function __construct(\Iterator $iterator, int $relatedCount)
    {
        parent::__construct($iterator);

        for ($i=0; $i<$relatedCount; ++$i) {
            $this->related[] = new RelatedIterator($this, $i);
            $this->positions[] = 0;
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

        [$relPos, $minPos, $maxPos] = [$this->getPosition($related), \min($this->positions), \max($this->positions)];

        if ($relPos === $minPos) {
            unset($this->cacheKeys[$minPos]);
            unset($this->cacheValues[$minPos]);
        }

        if ($relPos === $maxPos) {
            parent::next();
            $this->cacheKeys[$maxPos] = parent::key();
            $this->cacheValues[$maxPos] = parent::current();
        }

        $this->incrementPosition($related);
    }

    /**
     * {@inheritDoc}
     *
     * @param RelatedIterator<TKey, TValue>|null $related
     */
    public function current(RelatedIterator $related = null)
    {
        if ($related === null) {
            throw new \LogicException();
        }

        return $this->cacheValues[$this->getPosition($related)];
    }

    /**
     * {@inheritDoc}
     *
     * @param RelatedIterator<TKey, TValue>|null $related
     *
     * @return TKey
     */
    public function key(RelatedIterator $related = null)
    {
        if ($related === null) {
            throw new \LogicException();
        }

        return $this->cacheKeys[$this->getPosition($related)];
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

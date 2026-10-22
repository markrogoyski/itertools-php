<?php

declare(strict_types=1);

namespace IterTools\Util\Iterators;

/**
 * Shared, lazily filled buffer behind a set of RelatedIterators.
 *
 * - The source is read only when a related iterator first needs an element it has not been given yet.
 * - Each element's key and value are buffered until every live related iterator has moved past it.
 * - Related iterators are not retained here, so dropping them frees everything without the cycle collector.
 *
 * @internal
 *
 * @template TKey
 * @template TValue
 */
final class TeeIterator
{
    /**
     * @var \Iterator<TKey, TValue>
     */
    private \Iterator $source;

    /**
     * Current position of every live related iterator, by id.
     *
     * @var array<int, int>
     */
    private array $positions = [];

    /**
     * Buffered keys, by absolute position.
     *
     * @var array<int, TKey>
     */
    private array $keys = [];

    /**
     * Buffered values, by absolute position. Kept apart from the keys so an element costs no array of its own.
     *
     * @var array<int, TValue>
     */
    private array $values = [];

    /**
     * Lowest position still held in the buffer.
     */
    private int $bufferStart = 0;

    /**
     * Number of elements read from the source so far.
     */
    private int $fetched = 0;

    private bool $rewound = false;
    private bool $needsAdvance = false;
    private bool $exhausted = false;
    private bool $handedOut = false;

    /**
     * Id for the next cloned related iterator.
     */
    private int $nextId;

    /**
     * @param \Iterator<TKey, TValue> $iterator
     * @param int $relatedCount
     */
    public function __construct(\Iterator $iterator, private readonly int $relatedCount)
    {
        if ($relatedCount < 1) {
            throw new \InvalidArgumentException("Count must be ≥ 1. Got {$relatedCount}");
        }

        $this->source = $iterator;
        $this->positions = \array_fill(0, $relatedCount, 0);
        $this->nextId = $relatedCount;
    }

    /**
     * Create the related iterators. They are the only holders of this object, and it holds none of them.
     *
     * @return array<RelatedIterator<TKey, TValue>>
     *
     * @throws \LogicException if called more than once
     */
    public function getRelatedIterators(): array
    {
        if ($this->handedOut) {
            throw new \LogicException('Related iterators have already been created');
        }
        $this->handedOut = true;

        $related = [];
        for ($i = 0; $i < $this->relatedCount; ++$i) {
            $related[] = new RelatedIterator($this, $i);
        }

        return $related;
    }

    /**
     * @param RelatedIterator<TKey, TValue>|null $related
     */
    public function next(?RelatedIterator $related = null): void
    {
        if ($related === null) {
            throw new \LogicException();
        }

        $position = $this->positions[$related->getId()];
        if (!$this->hasElementAt($position)) {
            return;
        }

        $this->positions[$related->getId()] = $position + 1;
        $this->releaseConsumed();
    }

    /**
     * @param RelatedIterator<TKey, TValue>|null $related
     *
     * @return TValue|null
     */
    public function current(?RelatedIterator $related = null): mixed
    {
        if ($related === null) {
            throw new \LogicException();
        }

        $position = $this->positions[$related->getId()];

        return $this->hasElementAt($position) ? $this->values[$position] : null;
    }

    /**
     * @param RelatedIterator<TKey, TValue>|null $related
     *
     * @return TKey|null
     */
    public function key(?RelatedIterator $related = null): mixed
    {
        if ($related === null) {
            throw new \LogicException();
        }

        $position = $this->positions[$related->getId()];

        return $this->hasElementAt($position) ? $this->keys[$position] : null;
    }

    /**
     * @param RelatedIterator<TKey, TValue>|null $related
     */
    public function valid(?RelatedIterator $related = null): bool
    {
        if ($related === null) {
            throw new \LogicException();
        }

        return $this->hasElementAt($this->positions[$related->getId()]);
    }

    /**
     * @param RelatedIterator<TKey, TValue> $related
     */
    public function getPosition(RelatedIterator $related): int
    {
        return $this->positions[$related->getId()];
    }

    /**
     * Register a new branch that continues from the position of an existing one.
     *
     * @param RelatedIterator<TKey, TValue> $related
     *
     * @return int id of the new branch
     */
    public function fork(RelatedIterator $related): int
    {
        $id = $this->nextId++;
        $this->positions[$id] = $this->positions[$related->getId()];

        return $id;
    }

    /**
     * A related iterator is gone: it no longer holds back buffered elements.
     *
     * @param RelatedIterator<TKey, TValue> $related
     */
    public function detach(RelatedIterator $related): void
    {
        unset($this->positions[$related->getId()]);
        $this->releaseConsumed();
    }

    /**
     * Read from the source until the element at $position is buffered, if the source has one.
     */
    private function hasElementAt(int $position): bool
    {
        while ($this->fetched <= $position && !$this->exhausted) {
            $this->fetch();
        }

        return $position < $this->fetched;
    }

    /**
     * Read one element from the source.
     *
     * The key and value are buffered only once both were read, so they cannot get out of step.
     */
    private function fetch(): void
    {
        if (!$this->rewound) {
            $this->source->rewind();
            $this->rewound = true;
        } elseif ($this->needsAdvance) {
            $this->source->next();
            $this->needsAdvance = false;
        }

        if (!$this->source->valid()) {
            $this->exhausted = true;
            $this->source = new \EmptyIterator();
            return;
        }

        $value = $this->source->current();
        $key = $this->source->key();

        $this->keys[$this->fetched] = $key;
        $this->values[$this->fetched] = $value;
        ++$this->fetched;
        $this->needsAdvance = true;
    }

    /**
     * Drop buffered elements that every live related iterator has moved past.
     */
    private function releaseConsumed(): void
    {
        $lowest = $this->positions === [] ? $this->fetched : \min($this->positions);

        while ($this->bufferStart < $lowest) {
            // Moved past before either entry is freed: freeing one can drop a related iterator, whose detach()
            // releases from $bufferStart on.
            $position = $this->bufferStart++;
            unset($this->keys[$position], $this->values[$position]);
        }
    }
}

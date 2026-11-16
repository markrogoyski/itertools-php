<?php

declare(strict_types=1);

namespace IterTools\Util\Iterators;

use IterTools\Transform;

/**
 * Shared, lazily filled buffer behind a set of RelatedIterators.
 *
 * - The source is read only when a related iterator first needs an element it has not been given yet. Until then
 *   it is not even resolved to an iterator: an \IteratorAggregate's getIterator() is called on that first read,
 *   once, and what it throws is a source failure like any other.
 * - Each element's key and value are buffered until every live related iterator has moved past it.
 * - A source failure is kept: every related iterator that reaches it gets the same exception, and the source is
 *   not read again.
 * - The source is not advanced re-entrantly: a related iterator read from inside the source (its getIterator(), an
 *   iterator method or a generator body) for an element not yet buffered throws \LogicException, as MemoCache does.
 *   If the source lets it through, it is a source failure like any other; if the source catches it, nothing is lost.
 * - Related iterators are not retained here, so dropping them frees everything without the cycle collector.
 *   One exception: with zend.exception_ignore_args off, a kept source failure's trace holds the arguments of the
 *   calls that read the source, which can include related iterators. Those form a cycle through this object, so
 *   dropping one does not free it, or the elements it holds back, while another related iterator is alive. Once
 *   none is, the cycle collector frees them all. The failure is kept regardless: without it, a related iterator
 *   reaching it would end early with no exception.
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
     * The iterable the source is resolved from on the first read; released (an empty array) once resolved.
     *
     * @var iterable<TKey, TValue>
     */
    private iterable $pending;

    /**
     * Current position of every live related iterator, by id.
     *
     * @var array<int, int>
     */
    private array $positions = [];

    /**
     * Position every live related iterator started from, by id: 0, or for a clone the position it was cloned at.
     *
     * @var array<int, int>
     */
    private array $starts = [];

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
     * Whether the source is being read, so that a related iterator read from inside it cannot advance it again.
     */
    private bool $advancing = false;

    /**
     * What the source threw, at position $fetched. Nothing is read from the source after it.
     */
    private ?\Throwable $failure = null;

    /**
     * Id for the next cloned related iterator.
     */
    private int $nextId;

    /**
     * @param iterable<TKey, TValue> $iterable not resolved to an iterator until a related iterator first reads
     * @param int $relatedCount
     */
    public function __construct(iterable $iterable, private readonly int $relatedCount)
    {
        if ($relatedCount < 1) {
            throw new \InvalidArgumentException("Count must be ≥ 1. Got {$relatedCount}");
        }

        $this->pending = $iterable;
        $this->source = new \EmptyIterator();
        $this->positions = \array_fill(0, $relatedCount, 0);
        $this->starts = $this->positions;
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
     * Whether a related iterator has moved past the position it started from, so it can no longer be rewound.
     *
     * @param RelatedIterator<TKey, TValue> $related
     */
    public function hasAdvanced(RelatedIterator $related): bool
    {
        return $this->positions[$related->getId()] > $this->starts[$related->getId()];
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
        $this->starts[$id] = $this->positions[$id];

        return $id;
    }

    /**
     * A related iterator is gone: it no longer holds back buffered elements.
     *
     * @param RelatedIterator<TKey, TValue> $related
     */
    public function detach(RelatedIterator $related): void
    {
        unset($this->positions[$related->getId()], $this->starts[$related->getId()]);
        $this->releaseConsumed();
    }

    /**
     * Read from the source until the element at $position is buffered, if the source has one.
     *
     * @throws \Throwable what the source threw, to every related iterator that reaches the position it failed at
     * @throws \LogicException if called from inside the source while it is being read, to read a position not yet
     *                         buffered
     */
    private function hasElementAt(int $position): bool
    {
        while ($this->fetched <= $position && !$this->exhausted) {
            if ($this->failure !== null) {
                throw $this->failure;
            }
            // Thrown before the try, so the nested read records no failure and leaves the source in place. If the
            // source lets it through, the outer read below records it as the source failure.
            if ($this->advancing) {
                throw new \LogicException('Tee iterator cannot advance its source re-entrantly');
            }

            $this->advancing = true;
            try {
                $this->fetch();
            } catch (\Throwable $exception) {
                $this->failure = $exception;
                $this->source = new \EmptyIterator();
                throw $exception;
            } finally {
                $this->advancing = false;
            }
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
            // Released first, so the iterable is not held once resolved. A getIterator() that throws is called
            // once: hasElementAt() keeps the failure, and nothing is read after it.
            $pending = $this->pending;
            $this->pending = [];
            $this->source = Transform::toIterator($pending);
            // Not held here either, so that releasing the source below drops its last reference.
            unset($pending);
            $this->source->rewind();
            $this->rewound = true;
        } elseif ($this->needsAdvance) {
            $this->source->next();
            $this->needsAdvance = false;
        }

        if (!$this->source->valid()) {
            // Released before it is marked exhausted: a destructor that throws is then a source failure,
            // kept by hasElementAt() for every related iterator, not only the one whose read released it.
            $this->source = new \EmptyIterator();
            $this->exhausted = true;
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

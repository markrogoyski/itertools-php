<?php

declare(strict_types=1);

namespace IterTools\Util\Iterators;

use IterTools\Transform;

/**
 * State of a memoized iterable: the source cursor, the cache, the failure, and the re-entrancy guard.
 *
 * Kept in one object so that every handle on the memoized sequence, including a clone of
 * MemoizedIterable, advances the same source and replays the same cache.
 *
 * @internal
 *
 * @template TKey
 * @template TValue
 */
final class MemoCache
{
    /** @var iterable<mixed, mixed>|null */
    private ?iterable $iterable;

    /** @var \Iterator<mixed, mixed>|null */
    private ?\Iterator $source = null;

    /** @var list<TKey> */
    private array $keys = [];

    /** @var list<TValue> */
    private array $values = [];

    /** Number of cached elements; a cached null value is not a gap, so occupancy is not read with isset. */
    private int $cached = 0;

    private bool $initialized = false;
    private bool $exhausted = false;
    private ?\Throwable $failure = null;
    private ?int $failureIndex = null;
    private bool $advancing = false;

    /** @param iterable<TKey, TValue> $iterable */
    public function __construct(iterable $iterable)
    {
        $this->iterable = $iterable;
    }

    /** @return \Generator<TKey, TValue> */
    public function replay(): \Generator
    {
        $position = 0;
        while (true) {
            if ($position < $this->cached) {
                $key = $this->keys[$position];
                $value = $this->values[$position];
                ++$position;
                yield $key => $value;
                continue;
            }

            if ($this->failure !== null && $position === $this->failureIndex) {
                throw $this->failure;
            }
            if ($this->exhausted) {
                return;
            }

            $this->advanceFrontier();
        }
    }

    private function advanceFrontier(): void
    {
        if ($this->advancing) {
            throw new \LogicException('Memoized iterable cannot advance its source re-entrantly');
        }

        $this->advancing = true;
        try {
            if (!$this->initialized) {
                $this->initializeSource();
            } else {
                $this->advanceSource();
            }
        } catch (\Throwable $exception) {
            $this->failure = $exception;
            $this->failureIndex = $this->cached;
            // Replay rethrows the failure at this index and never reads the source again.
            $this->source = null;
        } finally {
            $this->advancing = false;
        }
    }

    private function initializeSource(): void
    {
        $this->initialized = true;
        $iterable = $this->iterable;
        $this->iterable = null;
        if ($iterable === null) {
            throw new \LogicException('Memoized iterable source is unavailable');
        }

        $this->source = Transform::toIterator($iterable);
        $this->source->rewind();
        $this->cacheCurrentSourceValue();
    }

    private function advanceSource(): void
    {
        if ($this->source === null) {
            throw new \LogicException('Memoized iterable source is unavailable');
        }

        $this->source->next();
        $this->cacheCurrentSourceValue();
    }

    private function cacheCurrentSourceValue(): void
    {
        if ($this->source === null) {
            throw new \LogicException('Memoized iterable source is unavailable');
        }
        if (!$this->source->valid()) {
            // Replay ends at the cached elements from now on, so the source is released: a tee copy read
            // through it then detaches, and its siblings stop buffering for it.
            $this->exhausted = true;
            $this->source = null;
            return;
        }

        // The value is read before its key, as foreach does: reading it can move the key, as SplFileObject
        // skipping an empty line does.
        $value = $this->source->current();
        $key = $this->source->key();
        $this->keys[] = $key;
        $this->values[] = $value;
        ++$this->cached;
    }
}

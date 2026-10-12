<?php

declare(strict_types=1);

namespace IterTools\Util\Iterators;

/**
 * Rewindable view over a memoized one-pass source.
 *
 * Every rewind() starts a fresh replay of the shared cache, so the underlying source is pulled lazily,
 * element by element, during the first pass only and is never rewound or re-iterated.
 *
 * @internal
 *
 * @template TKey
 * @template TValue
 * @implements \Iterator<TKey, TValue>
 */
final class ReplayIterator implements \Iterator
{
    /** @var MemoizedIterable<TKey, TValue> */
    private MemoizedIterable $memoized;

    /** @var \Generator<TKey, TValue> */
    private \Generator $replay;

    /** @param MemoizedIterable<TKey, TValue> $memoized */
    public function __construct(MemoizedIterable $memoized)
    {
        $this->memoized = $memoized;
        $this->replay   = $memoized->getIterator();
    }

    public function rewind(): void
    {
        $this->replay = $this->memoized->getIterator();
        $this->replay->current();
    }

    public function valid(): bool
    {
        return $this->replay->valid();
    }

    /** @return TValue */
    public function current(): mixed
    {
        return $this->replay->current();
    }

    /** @return TKey */
    public function key(): mixed
    {
        return $this->replay->key();
    }

    public function next(): void
    {
        $this->replay->next();
    }
}

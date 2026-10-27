<?php

declare(strict_types=1);

namespace IterTools\Util\Iterators;

/**
 * Every iterator replays the shared cache from the start. A clone is another handle on the same
 * memoized sequence: it shares the source and the cache with the original.
 *
 * @internal
 *
 * @template TKey
 * @template TValue
 * @implements \IteratorAggregate<TKey, TValue>
 */
final class MemoizedIterable implements \IteratorAggregate
{
    /** @var MemoCache<TKey, TValue> */
    private readonly MemoCache $cache;

    /** @param iterable<TKey, TValue> $iterable */
    public function __construct(iterable $iterable)
    {
        $this->cache = new MemoCache($iterable);
    }

    /** @return \Generator<TKey, TValue> */
    public function getIterator(): \Generator
    {
        return $this->cache->replay();
    }
}

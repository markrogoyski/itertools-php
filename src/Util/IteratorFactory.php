<?php

declare(strict_types=1);

namespace IterTools\Util;

use IterTools\Transform;
use IterTools\Util\Iterators\TeeIterator;

/**
 * @internal
 *
 * @template TKey
 * @template TValue
 */
class IteratorFactory
{
    /**
     * @param iterable<TKey, TValue> $iterable
     * @param positive-int $count
     *
     * @return array<\Iterator<TKey, TValue>>
     */
    public static function tee(iterable $iterable, int $count): array
    {
        $iterator = Transform::toIterator($iterable);
        return (new TeeIterator($iterator, $count))->getRelatedIterators();
    }
}

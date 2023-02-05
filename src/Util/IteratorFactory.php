<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 *
 * @template TKey
 * @template TValue
 */
class IteratorFactory
{
    /**
     * @internal
     * @param iterable<TKey, TValue> $iterable
     *
     * @return \Iterator<TKey, TValue>
     */
    public static function makeIterator(iterable $iterable): \Iterator
    {
        switch (true) {
            case $iterable instanceof \Iterator:
                return $iterable;

            case $iterable instanceof \Traversable:
                // @phpstan-ignore-next-line
                return new \IteratorIterator($iterable);

            case \is_array($iterable):
                return new \ArrayIterator($iterable);
        }

        throw new \LogicException(\gettype($iterable) . ' type is not an expected iterable type (Iterator|Traversable|array)');
    }

    /**
     * @param iterable<TKey, TValue> $iterable
     * @param positive-int $count
     *
     * @return array<\Iterator<TKey, TValue>>
     */
    public static function tee(iterable $iterable, int $count): array
    {
        $iterator = static::makeIterator($iterable);
        return (new TeeIterator($iterator, $count))->getRelatedIterators();
    }
}

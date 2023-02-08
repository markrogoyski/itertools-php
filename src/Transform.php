<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\Iterators\TeeIterator;

class Transform
{
    /**
     * @template TKey
     * @template TValue
     *
     * @param iterable<TKey, TValue> $iterable
     *
     * @return \Iterator<TKey, TValue>
     */
    public static function toIterator(iterable $iterable): \Iterator
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
     * @template TKey
     * @template TValue
     *
     * @param iterable<TKey, TValue> $iterable
     * @param positive-int $count
     *
     * @return array<\Iterator<TKey, TValue>>
     */
    public static function tee(iterable $iterable, int $count): array
    {
        $iterator = static::toIterator($iterable);
        return (new TeeIterator($iterator, $count))->getRelatedIterators();
    }

    /**
     * @template TValue
     *
     * @param iterable<TValue> $iterable
     * @return array<TValue>
     */
    public static function toArray(iterable $iterable): array
    {
        // TODO implement
        return [];
    }

    /**
     * Converts given iterable to an associative array.
     *
     * @param iterable<mixed> $iterable
     * @param callable|null $keyFunc fn ($value, $key) => Custom Logic
     * @param callable|null $valueFunc fn ($value, $key) => Custom logic
     *
     * @return array<mixed>
     */
    public function toAssociativeArray(
        iterable $iterable,
        callable $keyFunc = null,
        callable $valueFunc = null
    ): array {
        // TODO implement
        return [];
    }
}

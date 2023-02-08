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
     * Converts iterable source to array.
     *
     * @template T
     *
     * @param iterable<T> $iterable
     *
     * @return array<T>
     */
    public static function toArray(iterable $iterable): array
    {
        $result = [];
        foreach ($iterable as $item) {
            $result[] = $item;
        }
        return $result;
    }

    /**
     * Converts given iterable to an associative array.
     *
     * @template TKey
     * @template TValue
     *
     * @param iterable<TKey, TValue> $iterable
     * @param callable(mixed $value, mixed $key): mixed|null $keyFunc
     * @param callable(mixed $value, mixed $key): mixed|null $valueFunc
     *
     * @return array<TKey|numeric|string, TValue|mixed>
     */
    public static function toAssociativeArray(
        iterable $iterable,
        callable $keyFunc = null,
        callable $valueFunc = null
    ): array {
        if ($keyFunc === null) {
            $keyFunc = fn ($item, $key) => $key;
        }
        if ($valueFunc === null) {
            $valueFunc = fn ($item, $key) => $item;
        }

        $result = [];
        foreach ($iterable as $key => $item) {
            $result[$keyFunc($item, $key)] = $valueFunc($item, $key);
        }
        return $result;
    }

    /**
     * Return several independent iterators from a single iterable.
     *
     * Once a tee() has been created, the original iterable should not be used anywhere else;
     * otherwise, the iterable could get advanced without the tee objects being informed.
     *
     * This tool may require significant auxiliary storage (depending on how much temporary data needs to be stored).
     * In general, if one iterator uses most or all of the data before another iterator starts,
     * it is faster to use toArray() instead of tee().
     *
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
}

<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\Iterators\TeeIterator;

class Transform
{
    /**
     * Converts iterable source to array without saving keys.
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
     * Converts given iterable to Iterator.
     *
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
     * Return several independent (duplicated) iterators from a single iterable.
     *
     * Once tee has been called to duplicate iterators, it is advisable to not use the original input iterator any further.
     *
     * Duplicating iterators can use up memory. Consider if tee is the right solution. For example, arrays and most
     * iterators can be rewound and reiterated without need for duplication.
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

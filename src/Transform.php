<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\Iterators\TeeIterator;

final class Transform
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
     * @param callable(mixed, mixed): mixed|null $keyFunc
     * @param callable(mixed, mixed): mixed|null $valueFunc
     *
     * @return array<TKey|int|string, TValue|mixed>
     */
    public static function toAssociativeArray(
        iterable $iterable,
        ?callable $keyFunc = null,
        ?callable $valueFunc = null
    ): array {
        $keyFunc ??= fn (mixed $item, mixed $key): mixed => $key;
        $valueFunc ??= fn (mixed $item, mixed $_key): mixed => $item;

        $result = [];
        foreach ($iterable as $key => $item) {
            /** @var int|string $computedKey */
            $computedKey = $keyFunc($item, $key);
            $result[$computedKey] = $valueFunc($item, $key);
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
        /** @psalm-suppress DocblockTypeContradiction, MixedArgumentTypeCoercion */
        return match (true) { // @phpstan-ignore return.type
            $iterable instanceof \Iterator => $iterable,
            $iterable instanceof \Traversable => new \IteratorIterator($iterable),
            \is_array($iterable) => new \ArrayIterator($iterable), // @phpstan-ignore function.alreadyNarrowedType
            default => throw new \LogicException(\gettype($iterable) . ' type is not an expected iterable type (Iterator|Traversable|array)'),
        };
    }

    /**
     * Partitions iterable into two lists based on a predicate.
     *
     * Returns a two-element list array: [truthyValues, falsyValues].
     * Both output arrays are reindexed (list arrays); source keys are discarded.
     *
     * Predicate return value is coerced via (bool) cast.
     *
     * @template T
     *
     * @param iterable<T> $data
     * @param callable    $predicate
     *
     * @return array{0: array<T>, 1: array<T>}
     */
    public static function partition(iterable $data, callable $predicate): array
    {
        $truthy = [];
        $falsy  = [];
        foreach ($data as $datum) {
            if ((bool) $predicate($datum)) {
                $truthy[] = $datum;
            } else {
                $falsy[] = $datum;
            }
        }
        return [$truthy, $falsy];
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

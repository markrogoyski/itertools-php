<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\Iterators\TeeIterator;
use IterTools\Util\Iterators\MemoizedIterable;
use IterTools\Util\Values\Truthiness;

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
     * Keys follow PHP array semantics: a repeated key keeps its first position and its last value, and a decimal
     * integer string such as '1' is stored as the int 1.
     *
     * @template TKey
     * @template TValue
     *
     * @param iterable<TKey, TValue> $iterable
     * @param callable(mixed, mixed): mixed|null $keyFunc
     * @param callable(mixed, mixed): mixed|null $valueFunc
     *
     * @return array<TKey|int|string, TValue|mixed>
     *
     * @throws \InvalidArgumentException if a key is not an int or string; PHP would convert or reject it.
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
            $computedKey = $keyFunc($item, $key);
            if (!\is_int($computedKey) && !\is_string($computedKey)) {
                throw new \InvalidArgumentException(
                    'Associative array key must be int or string, got ' . \get_debug_type($computedKey)
                );
            }
            $result[$computedKey] = $valueFunc($item, $key);
        }
        return $result;
    }

    /**
     * Converts given iterable to Iterator.
     *
     * An \IteratorAggregate, such as a Stream, gives the iterator its getIterator() returns, following nested
     * aggregates to the first iterator; it is not wrapped. So an aggregate over an iterator or generator gives
     * that same object, and one that returns a fresh iterator per call gives a fresh one each time.
     *
     * @template TKey
     * @template TValue
     *
     * @param iterable<TKey, TValue> $iterable
     *
     * @return \Iterator<TKey, TValue>
     *
     * @throws \LogicException if an aggregate's getIterator() returns a non-Traversable, or aggregates form a
     *                         cycle, such as one whose getIterator() returns itself.
     */
    public static function toIterator(iterable $iterable): \Iterator
    {
        /** @psalm-suppress DocblockTypeContradiction, MixedArgumentTypeCoercion */
        return match (true) { // @phpstan-ignore return.type
            $iterable instanceof \Iterator => $iterable,
            $iterable instanceof \IteratorAggregate => self::toIterator(self::unwrapAggregate($iterable)),
            $iterable instanceof \Traversable => new \IteratorIterator($iterable),
            \is_array($iterable) => new \ArrayIterator($iterable), // @phpstan-ignore function.alreadyNarrowedType
            default => throw new \LogicException(\gettype($iterable) . ' type is not an expected iterable type (Iterator|Traversable|array)'),
        };
    }

    /**
     * Follows getIterator() through nested aggregates to the first Traversable that is not an aggregate.
     *
     * @template TKey
     * @template TValue
     *
     * @param \IteratorAggregate<TKey, TValue> $aggregate
     *
     * @return \Traversable<TKey, TValue>
     *
     * @throws \LogicException if getIterator() returns a non-Traversable, or the aggregates form a cycle.
     */
    private static function unwrapAggregate(\IteratorAggregate $aggregate): \Traversable
    {
        // Aggregates seen so far, by object id. Holding them keeps their ids from being reused by a later
        // aggregate, which would read as a false cycle.
        $seen        = [];
        $traversable = $aggregate;
        while ($traversable instanceof \IteratorAggregate) {
            $id = \spl_object_id($traversable);
            if (isset($seen[$id])) {
                throw new \LogicException(
                    \get_class($traversable) . '::getIterator() leads back to itself; aggregates form a cycle'
                );
            }
            $seen[$id] = $traversable;

            /** @var mixed $next a getIterator() marked #[\ReturnTypeWillChange] can return anything */
            $next = $traversable->getIterator();
            if (!($next instanceof \Traversable)) {
                throw new \LogicException(
                    \get_class($traversable) . '::getIterator() must return an object that implements Traversable'
                );
            }
            $traversable = $next;
        }

        return $traversable;
    }

    /**
     * Lazily cache an iterable so current and future consumers can replay it.
     *
     * A clone of the result is another handle on the same memoized sequence: it shares the source and
     * the cache with the original.
     *
     * @template TKey
     * @template TValue
     *
     * @param iterable<TKey, TValue> $iterable
     *
     * @return \IteratorAggregate<TKey, TValue>
     */
    public static function memoize(iterable $iterable): \IteratorAggregate
    {
        return new MemoizedIterable($iterable);
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
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
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
     * Lazy: the count is checked first, and the iterable is not touched (no element read, no getIterator() call on
     * an \IteratorAggregate) until a copy is first used; getIterator() is then called once for all copies.
     * A copy can be rewound until it advances. A clone of a copy is an independent copy from the position it was
     * cloned at, and can be rewound until it advances from there.
     *
     * @template TKey
     * @template TValue
     *
     * @param iterable<TKey, TValue> $iterable
     * @param positive-int $count
     *
     * @return array<\Iterator<TKey, TValue>>
     *
     * @throws \InvalidArgumentException if $count is less than 1
     */
    public static function tee(iterable $iterable, int $count): array
    {
        // The count is checked before the iterable is touched: TeeIterator resolves it only on the first read.
        return (new TeeIterator($iterable, $count))->getRelatedIterators();
    }
}

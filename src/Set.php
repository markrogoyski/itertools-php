<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\Iterators\JustifyMultipleIterator;
use IterTools\Util\NoValueMonad;
use IterTools\Util\UniqueExtractor;
use IterTools\Util\UsageMap;

final class Set
{
    /**
     * Iterate only the distinct elements.
     *
     * Strict-type comparisons:
     *  - scalars: compares strictly by type
     *  - objects: always treats different instances as not equal to each other
     *  - arrays: compares serialized
     *
     * Coercive (non-strict) type comparisons:
     *  - scalars: compares non-strictly by value
     *  - objects: compares serialized (throws \InvalidArgumentException if not serializable)
     *  - arrays: compares serialized
     *
     * @template T
     * @param iterable<T> $data
     * @param bool $strict
     *
     * @return \Generator<T>
     */
    public static function distinct(iterable $data, bool $strict = true): \Generator
    {
        $map = [];

        foreach ($data as $datum) {
            $hash = UniqueExtractor::getString($datum, $strict);

            if (!isset($map[$hash])) {
                $map[$hash] = true;
                yield $datum;
            }
        }
    }

    /**
     * Iterate only the distinct elements using $compareBy function for getting comparable value.
     *
     * @template T
     *
     * @param iterable<T> $data
     * @param callable $compareBy
     *
     * @return \Generator<T>
     */
    public static function distinctBy(iterable $data, callable $compareBy): \Generator
    {
        $map = [];

        foreach ($data as $datum) {
            $hash = UniqueExtractor::getString($compareBy($datum), true);

            if (!isset($map[$hash])) {
                $map[$hash] = true;
                yield $datum;
            }
        }
    }

    /**
     * Remove only consecutive duplicates from the iterable (Unix `uniq` behavior).
     *
     * Each element is compared strictly (===) to the previous element yielded.
     * Non-adjacent duplicates are kept. Runs in O(1) memory.
     *
     * Source keys are discarded; output is a sequentially re-indexed list.
     *
     * @template T
     *
     * @param iterable<T> $data
     *
     * @return \Generator<T>
     */
    public static function distinctAdjacent(iterable $data): \Generator
    {
        $hasPrevious = false;
        $previous = null;

        foreach ($data as $datum) {
            if (!$hasPrevious || $datum !== $previous) {
                yield $datum;
                $previous = $datum;
                $hasPrevious = true;
            }
        }
    }

    /**
     * Remove only consecutive duplicates from the iterable, comparing values returned by $keyFn.
     *
     * Each element's extracted key is compared strictly (===) to the previous element's key.
     * Non-adjacent duplicates are kept. Runs in O(1) memory and calls $keyFn once per element.
     *
     * Source keys are discarded; output is a sequentially re-indexed list.
     *
     * @template T
     *
     * @param iterable<T> $data
     * @param callable $keyFn
     *
     * @return \Generator<T>
     */
    public static function distinctAdjacentBy(iterable $data, callable $keyFn): \Generator
    {
        $hasPrevious = false;
        $previousKey = null;

        foreach ($data as $datum) {
            $currentKey = $keyFn($datum);
            if (!$hasPrevious || $currentKey !== $previousKey) {
                yield $datum;
                $previousKey = $currentKey;
                $hasPrevious = true;
            }
        }
    }

    /**
     * Iterates the intersection of iterables in strict type mode.
     *
     * If input iterables produce duplicate items, then multiset intersection rules apply.
     *
     * Strict-type comparisons:
     *  - scalars: compares strictly by type
     *  - objects: always treats different instances as not equal to each other
     *  - arrays: compares serialized
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    public static function intersection(iterable ...$iterables): \Generator
    {
        yield from static::intersectionInternal(true, \count($iterables), ...$iterables);
    }

    /**
     * Iterates the intersection of iterables using type coercion.
     *
     * If input iterables produce duplicate items, then multiset intersection rules apply.
     *
     * Coercive (non-strict) type comparisons:
     *  - scalars: compares non-strictly by value
     *  - objects: compares serialized (throws \InvalidArgumentException if not serializable)
     *  - arrays: compares serialized
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    public static function intersectionCoercive(iterable ...$iterables): \Generator
    {
        yield from static::intersectionInternal(false, \count($iterables), ...$iterables);
    }

    /**
     * Iterates partial intersection of iterables in strict type mode.
     *
     * If input iterables produce duplicate items, then multiset intersection rules apply.
     * If minIntersectionCount is 1, then multiset union rules apply.
     *
     * Strict-type comparisons:
     *  - scalars: compares strictly by type
     *  - objects: always treats different instances as not equal to each other
     *  - arrays: compares serialized
     *
     * @param positive-int $minIntersectionCount
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    public static function partialIntersection(int $minIntersectionCount, iterable ...$iterables): \Generator
    {
        yield from static::intersectionInternal(true, $minIntersectionCount, ...$iterables);
    }

    /**
     * Iterates partial intersection of iterables using type coercion.
     *
     * If input iterables produce duplicate items, then multiset intersection rules apply.
     * If minIntersectionCount is 1, then multiset union rules apply.
     *
     * Coercive (non-strict) type comparisons:
     *  - scalars: compares non-strictly by value
     *  - objects: compares serialized (throws \InvalidArgumentException if not serializable)
     *  - arrays: compares serialized
     *
     * @param positive-int $minIntersectionCount
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    public static function partialIntersectionCoercive(int $minIntersectionCount, iterable ...$iterables): \Generator
    {
        yield from static::intersectionInternal(false, $minIntersectionCount, ...$iterables);
    }

    /**
     * Iterates union of given iterables in strict type mode.
     *
     * If input iterables produce duplicate items, then multiset intersection rules apply.
     *
     * Strict-type comparisons:
     *  - scalars: compares strictly by type
     *  - objects: always treats different instances as not equal to each other
     *  - arrays: compares serialized
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator
     */
    public static function union(iterable ...$iterables): \Generator
    {
        return static::partialIntersection(1, ...$iterables);
    }

    /**
     * Iterates union of given iterables using type coercion.
     *
     * If input iterables produce duplicate items, then multiset intersection rules apply.
     *
     * Coercive (non-strict) type comparisons:
     *  - scalars: compares non-strictly by value
     *  - objects: compares serialized (throws \InvalidArgumentException if not serializable)
     *  - arrays: compares serialized
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator
     */
    public static function unionCoercive(iterable ...$iterables): \Generator
    {
        return static::partialIntersectionCoercive(1, ...$iterables);
    }

    /**
     * Iterates the difference of iterables in strict type mode.
     *
     * Returns elements from the first iterable not present in any other iterables.
     * If input iterables produce duplicate items, then multiset difference rules apply.
     *
     * Strict-type comparisons:
     *  - scalars: compares strictly by type
     *  - objects: always treats different instances as not equal to each other
     *  - arrays: compares serialized
     *
     * @param iterable<mixed> $a
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    public static function difference(iterable $a, iterable ...$iterables): \Generator
    {
        yield from self::differenceInternal(true, $a, ...$iterables);
    }

    /**
     * Iterates the difference of iterables using type coercion.
     *
     * Returns elements from the first iterable not present in any other iterables.
     * If input iterables produce duplicate items, then multiset difference rules apply.
     *
     * Coercive (non-strict) type comparisons:
     *  - scalars: compares non-strictly by value
     *  - objects: compares serialized (throws \InvalidArgumentException if not serializable)
     *  - arrays: compares serialized
     *
     * @param iterable<mixed> $a
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    public static function differenceCoercive(iterable $a, iterable ...$iterables): \Generator
    {
        yield from self::differenceInternal(false, $a, ...$iterables);
    }

    /**
     * Iterates the symmetric difference of iterables in strict type mode.
     *
     * If input iterables produce duplicate items, then multiset difference rules apply.
     *
     * Strict-type comparisons:
     *  - scalars: compares strictly by type
     *  - objects: always treats different instances as not equal to each other
     *  - arrays: compares serialized
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    public static function symmetricDifference(iterable ...$iterables): \Generator
    {
        yield from self::symmetricDifferenceInternal(true, ...$iterables);
    }

    /**
     * Iterates the symmetric difference of iterables using type coercion.
     *
     * If input iterables produce duplicate items, then multiset intersection rules apply.
     *
     * Coercive (non-strict) type comparisons:
     *  - scalars: compares non-strictly by value
     *  - objects: compares serialized (throws \InvalidArgumentException if not serializable)
     *  - arrays: compares serialized
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    public static function symmetricDifferenceCoercive(iterable ...$iterables): \Generator
    {
        yield from self::symmetricDifferenceInternal(false, ...$iterables);
    }

    /**
     * Iterates the intersection of iterables.
     *
     * @param bool $strict
     * @param int $minIntersectionCount
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    protected static function intersectionInternal(
        bool $strict,
        int $minIntersectionCount,
        iterable ...$iterables
    ): \Generator {
        $usageMap = new UsageMap($strict);

        $multipleIterator = new JustifyMultipleIterator(NoValueMonad::getInstance(), ...$iterables);

        foreach ($multipleIterator as $values) {
            foreach ($values as $owner => $value) {
                if ($value instanceof NoValueMonad) {
                    continue;
                }

                $usageMap->addUsage($value, (string)$owner);

                if ($usageMap->getOwnersCount($value) === $minIntersectionCount) {
                    yield $value;
                    $usageMap->deleteUsage($value);
                }
            }
        }
    }

    /**
     * Iterates the symmetric difference of iterables.
     *
     * @param bool $strict
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    protected static function symmetricDifferenceInternal(
        bool $strict,
        iterable ...$iterables
    ): \Generator {
        $usageMap = new UsageMap($strict);
        $valuesMap = [];

        $multipleIterator = new JustifyMultipleIterator(NoValueMonad::getInstance(), ...$iterables);

        foreach ($multipleIterator as $values) {
            foreach ($values as $owner => $value) {
                if ($value instanceof NoValueMonad) {
                    continue;
                }

                $usageMap->addUsage($value, (string)$owner);

                $valuesMap[UniqueExtractor::getString($value, $strict)] = $value;

                if ($usageMap->getOwnersCount($value) === \count($iterables)) {
                    $usageMap->deleteUsage($value);
                }
            }
        }

        foreach ($valuesMap as $value) {
            foreach (Single::repeat($value, $usageMap->getUsagesCount($value)) as $item) {
                yield $item;
            }
        }
    }

    /**
     * Iterates the difference of iterables.
     *
     * @param bool $strict
     * @param iterable<mixed> $a
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<mixed>
     */
    protected static function differenceInternal(
        bool $strict,
        iterable $a,
        iterable ...$iterables
    ): \Generator {
        $subtractCounts = [];

        foreach ($iterables as $iterable) {
            foreach ($iterable as $value) {
                $hash = UniqueExtractor::getString($value, $strict);
                $subtractCounts[$hash] = ($subtractCounts[$hash] ?? 0) + 1;
            }
        }

        foreach ($a as $value) {
            $hash = UniqueExtractor::getString($value, $strict);

            if (isset($subtractCounts[$hash]) && $subtractCounts[$hash] > 0) {
                $subtractCounts[$hash]--;
            } else {
                yield $value;
            }
        }
    }
}

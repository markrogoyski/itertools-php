<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\UniqueExtractor;
use IterTools\Util\UsageMap;

/**
 * Tools to get summarized answers about iterables.
 */
final class Summary
{
    /**
     * Returns true if all elements match the predicate function.
     *
     * Empty iterables return true.
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     *
     * @return bool
     */
    public static function allMatch(iterable $data, callable $predicate): bool
    {
        foreach ($data as $datum) {
            if (!(bool) $predicate($datum)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if any element matches the predicate function.
     *
     * Empty iterables return false.
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     *
     * @return bool
     */
    public static function anyMatch(iterable $data, callable $predicate): bool
    {
        foreach ($data as $datum) {
            if ((bool) $predicate($datum)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if no element matches the predicate function.
     *
     * Empty iterables return true.
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     *
     * @return bool
     */
    public static function noneMatch(iterable $data, callable $predicate): bool
    {
        foreach ($data as $datum) {
            if ((bool) $predicate($datum)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return true if all elements in given collection are unique.
     *
     * Empty iterables return true.
     *
     * @param iterable<mixed> $data
     * @param bool $strict
     *
     * @return bool
     */
    public static function allUnique(iterable $data, bool $strict = true): bool
    {
        $usageMap = [];

        foreach ($data as $datum) {
            $hash = UniqueExtractor::getString($datum, $strict);
            if (\array_key_exists($hash, $usageMap)) {
                return false;
            }
            $usageMap[$hash] = true;
        }

        return true;
    }

    /**
     * Returns true if given collection is sorted in ascending order; otherwise false.
     *
     * Items of given collection must be comparable.
     *
     * Returns true if given collection is empty or has only one element.
     *
     * @param iterable<mixed> $data
     *
     * @return bool
     */
    public static function isSorted(iterable $data): bool
    {
        foreach (Single::pairwise($data) as [$lhs, $rhs]) {
            if ((\is_float($lhs) && \is_nan($lhs)) || (\is_float($rhs) && \is_nan($rhs))) {
                return false;
            }
            if ($rhs < $lhs) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if given collection is sorted in reverse descending order; otherwise false.
     *
     * Items of given collection must be comparable.
     *
     * Returns true if given collection is empty or has only one element.
     *
     * @param iterable<mixed> $data
     *
     * @return bool
     */
    public static function isReversed(iterable $data): bool
    {
        foreach (Single::pairwise($data) as [$lhs, $rhs]) {
            if ((\is_float($lhs) && \is_nan($lhs)) || (\is_float($rhs) && \is_nan($rhs))) {
                return false;
            }
            if ($rhs > $lhs) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if all given collections are the same.
     *
     * For single iterable or empty iterables list returns true.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     */
    public static function same(iterable ...$iterables): bool
    {
        try {
            foreach (Multi::zipEqual(...$iterables) as $values) {
                foreach (Single::pairwise($values) as [$lhs, $rhs]) {
                    if ($lhs !== $rhs) {
                        return false;
                    }
                }
            }
        } catch (\LengthException $e) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if all given collections have the same lengths.
     *
     * For single iterable or empty iterables list returns true.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     */
    public static function sameCount(iterable ...$iterables): bool
    {
        if (\count($iterables) <= 1) {
            return true;
        }

        $counts = \array_map([Reduce::class, 'toCount'], $iterables);
        return \count(\array_unique($counts)) === 1;
    }

    /**
     * Returns true if exactly n items in the iterable are true where the predicate function is true.
     *
     * Default predicate if not provided is the boolean value of each data item.
     *
     * @param iterable<mixed> $data
     * @param int             $n
     * @param callable|null   $predicate
     *
     * @return bool
     */
    public static function exactlyN(iterable $data, int $n, ?callable $predicate = null): bool
    {
        if ($n < 0) {
            return false;
        }

        $predicate ??= fn(mixed $datum): bool => \boolval($datum);

        $count = 0;
        foreach ($data as $datum) {
            if ((bool) $predicate($datum)) {
                $count++;
                if ($count > $n) {
                    return false;
                }
            }
        }

        return $count === $n;
    }

    /**
     * Returns true if given collections are permutations of each other (using strict-type comparisons).
     *
     * Returns true if no collections given or for single collection.
     *
     * Strict-type comparisons:
     *  - scalars: compares strictly by type
     *  - objects: always treats different instances as not equal to each other
     *  - arrays: compares serialized
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     *
     * @see https://en.cppreference.com/w/cpp/algorithm/is_permutation
     */
    public static function arePermutations(iterable ...$iterables): bool
    {
        return self::arePermutationsInternal(true, ...$iterables);
    }

    /**
     * Returns true if given collections are permutations of each other (using type coercion).
     *
     * Returns true if no collections given or for single collection.
     *
     * Coercive (non-strict) type comparisons:
     *  - scalars: compares non-strictly by value
     *  - objects: compares serialized (throws \InvalidArgumentException if not serializable)
     *  - arrays: compares serialized
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     *
     * @see https://en.cppreference.com/w/cpp/algorithm/is_permutation
     */
    public static function arePermutationsCoercive(iterable ...$iterables): bool
    {
        return self::arePermutationsInternal(false, ...$iterables);
    }

    /**
     * Internal function helper for arePermutations() and arePermutationsCoercive()
     *
     * @see Summary::arePermutations()
     * @see Summary::arePermutationsCoercive()
     *
     * @param bool $strict
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     */
    private static function arePermutationsInternal(bool $strict, iterable ...$iterables): bool
    {
        if (\count($iterables) < 2) {
            return true;
        }

        $usageMap = new UsageMap($strict);
        $map = [];

        try {
            foreach (Multi::zipEqual(...$iterables) as $values) {
                foreach ($values as $collectionIndex => $value) {
                    $hash = $usageMap->addUsage($value, \strval($collectionIndex));
                    $map[$hash] = $value;
                }
            }
        } catch (\LengthException $e) {
            return false;
        }

        foreach ($map as $value) {
            if (!$usageMap->hasSameOwnerCount($value, \count($iterables))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if the given iterable contains the needle (using strict-type comparison).
     *
     * Strict-type comparison:
     *  - scalars: compares strictly by type (1 does not match '1', 0 does not match false)
     *  - objects: matches only the same instance
     *  - arrays: compares strictly by ===
     *  - NaN: never matches NaN (since NaN !== NaN)
     *
     * Short-circuits on first match.
     *
     * @param iterable<mixed> $data
     * @param mixed           $needle
     *
     * @return bool
     */
    public static function contains(iterable $data, mixed $needle): bool
    {
        foreach ($data as $datum) {
            if ($datum === $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if the given iterable contains the needle (using type coercion).
     *
     * Coercive (non-strict) type comparison:
     *  - scalars: compares non-strictly by value (1 matches '1', 0 matches false, '1e2' matches 100)
     *  - objects: compares serialized (throws \InvalidArgumentException if needle or any visited datum is not serializable)
     *  - arrays: compares serialized
     *  - NaN: matches NaN (consistent with other coercive operations in this library)
     *
     * Short-circuits on first match: a non-serializable datum is only reached if no
     * earlier datum has matched, so a match before such a datum returns true without throwing.
     *
     * @param iterable<mixed> $data
     * @param mixed           $needle
     *
     * @return bool
     *
     * @throws \InvalidArgumentException if the needle is not serializable, or if a non-serializable datum is reached before any match
     */
    public static function containsCoercive(iterable $data, mixed $needle): bool
    {
        $needleHash = UniqueExtractor::getString($needle, false);

        foreach ($data as $datum) {
            if (UniqueExtractor::getString($datum, false) === $needleHash) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if all elements of given collection that satisfy the predicate
     * appear before all elements that don't.
     *
     * Returns true for empty collection or for collection with single item.
     *
     * Default predicate if not provided is the boolean value of each data item.
     *
     * @see https://en.cppreference.com/w/cpp/algorithm/is_partitioned
     *
     * @param iterable<mixed> $data
     * @param callable|null $predicate
     *
     * @return bool
     */
    public static function isPartitioned(iterable $data, ?callable $predicate = null): bool
    {
        $predicate ??= fn (mixed $item): bool => \boolval($item);

        $allTrueSoFar = true;

        foreach ($data as $datum) {
            $currentItemTrue = (bool) $predicate($datum);

            if ($allTrueSoFar && !$currentItemTrue) {
                $allTrueSoFar = false;
                continue;
            }

            if (!$allTrueSoFar && $currentItemTrue) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if given iterable is empty.
     *
     * For rewindable iterators, reports whether the underlying collection is empty
     * regardless of cursor position. For generators (which cannot be rewound),
     * reports whether there are remaining elements from the current position.
     *
     * @param iterable<mixed> $data
     *
     * @return bool
     */
    public static function isEmpty(iterable $data): bool
    {
        $iterator = Transform::toIterator($data);

        if (!($iterator instanceof \Generator)) {
            $iterator->rewind();
        }

        return !$iterator->valid();
    }
}

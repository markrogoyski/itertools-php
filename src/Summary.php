<?php

declare(strict_types=1);

namespace IterTools;

/**
 * Tools to get summarized answers about iterables.
 */
class Summary
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
        $count = 0;

        foreach ($data as $datum) {
            if ($predicate($datum) === false) {
                return false;
            }
            $count++;
        }

        return $count > 0;
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
            if ($predicate($datum) === true) {
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
            if ($predicate($datum) === true) {
                return false;
            }
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
    public static function exactlyN(iterable $data, int $n, callable $predicate = null): bool
    {
        if ($n < 0) {
            return false;
        }

        if ($predicate === null) {
            $predicate = fn($datum) => boolval($datum);
        }

        $count = 0;
        foreach ($data as $datum) {
            if ($predicate($datum)) {
                $count++;
                if ($count > $n) {
                    return false;
                }
            }
        }

        return $count === $n;
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
    public static function isPartitioned(iterable $data, callable $predicate = null): bool
    {
        $allTrue = true;

        foreach ($data as $datum) {
            $status = ($predicate !== null)
                ? $predicate($datum)
                : boolval($datum);

            if ($allTrue && !$status) {
                $allTrue = false;
                continue;
            }

            if (!$allTrue && $status) {
                return false;
            }
        }

        return true;
    }
}

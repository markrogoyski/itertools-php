<?php

declare(strict_types=1);

namespace IterTools;

/**
 * Tools to get summarized answers about iterables.
 */
class Summary
{
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
}

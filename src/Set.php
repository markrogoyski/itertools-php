<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\JustifyMultipleIterator;
use IterTools\Util\NoValueMonad;
use IterTools\Util\UniqueExtractor;
use IterTools\Util\UsageMap;

class Set
{
    /**
     * Filter out elements from the iterable only returning unique elements.
     *
     * If $strict is true:
     *  - scalars: compares strictly by type;
     *  - objects: always treats different instances as not equal to each other;
     *  - arrays: compares serialized.
     *
     * If $strict is false:
     *  - scalars: compares non-strictly by value;
     *  - objects: compares serialized;
     *  - arrays: compares serialized.
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
     * Iterates the intersection of iterables in strict type mode.
     *
     *  - scalars: compares strictly by type;
     *  - objects: always treats different instances as not equal to each other;
     *  - arrays: compares serialized.
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
     * Iterates the intersection of iterables in non-strict type mode.
     *
     *  - scalars: compares non-strictly by value;
     *  - objects: compares serialized;
     *  - arrays: compares serialized.
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
     *  - scalars: compares strictly by type;
     *  - objects: always treats different instances as not equal to each other;
     *  - arrays: compares serialized.
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
     * Iterates partial intersection of iterables in non-strict type mode.
     *
     *  - scalars: compares non-strictly by value;
     *  - objects: compares serialized;
     *  - arrays: compares serialized.
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
     * Iterates the symmetric difference of iterables in strict type mode.
     *
     *  - scalars: compares strictly by type;
     *  - objects: always treats different instances as not equal to each other;
     *  - arrays: compares serialized.
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
     * Iterates the symmetric difference of iterables in non-strict type mode.
     *
     *  - scalars: compares non-strictly by value;
     *  - objects: compares serialized;
     *  - arrays: compares serialized.
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

        $multipleIterator = new JustifyMultipleIterator(...$iterables);

        foreach ($multipleIterator as $index => $values) {
            foreach ($values as $owner => $value) {
                if ($value instanceof NoValueMonad) {
                    continue;
                }

                $usageMap->addUsage($value, (string)$owner);

                if ($usageMap->getOwnersCount($value) === $minIntersectionCount) {
                    yield $index => $value;
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

        $multipleIterator = new JustifyMultipleIterator(...$iterables);

        foreach ($multipleIterator as $values) {
            foreach ($values as $owner => $value) {
                if ($value instanceof NoValueMonad) {
                    continue;
                }

                $usageMap->addUsage($value, (string)$owner);

                $valuesMap[UniqueExtractor::getString($value, $strict)] = $value;

                if ($usageMap->getOwnersCount($value) === count($iterables)) {
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
}

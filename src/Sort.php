<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\SortBoundedHeap;

final class Sort
{
    /**
     * Sorts the given iterable, maintaining index key associations.
     *
     * If comparator is null, the elements of given iterable must be comparable.
     *
     * @param iterable<mixed> $data
     * @param callable(mixed, mixed):int|null $comparator (optional) function to determine how to sort elements if default sort is not appropriate.
     *
     * @return \Generator
     */
    public static function asort(iterable $data, ?callable $comparator = null): \Generator
    {
        /** @var list<array{0: mixed, 1: mixed}> $pairs */
        $pairs = [];
        foreach (Transform::toIterator($data) as $key => $value) {
            $pairs[] = [$key, $value];
        }

        if ($comparator === null) {
            \usort($pairs, static fn (array $a, array $b) => $a[1] <=> $b[1]);
        } else {
            \usort($pairs, static fn (array $a, array $b) => $comparator($a[1], $b[1]));
        }

        foreach ($pairs as [$key, $value]) {
            yield $key => $value;
        }
    }

    /**
     * Sorts the given iterable
     *
     * If comparator is null, the elements of given iterable must be comparable.
     *
     * @param iterable<mixed> $data
     * @param callable(mixed, mixed):int|null $comparator (optional) function to determine how to sort elements if default sort is not appropriate.
     *
     * @return \Generator
     */
    public static function sort(iterable $data, ?callable $comparator = null): \Generator
    {
        $array = \iterator_to_array(Transform::toIterator($data), false);

        if ($comparator === null) {
            \sort($array);
        } else {
            \usort($array, $comparator);
        }

        foreach ($array as $datum) {
            yield $datum;
        }
    }

    /**
     * Sorts the given iterable using a key-extraction function (Schwartzian transform).
     *
     * The key function is called exactly once per element. Source keys are discarded
     * (matching Sort::sort).
     *
     * Stable: elements with equal extracted keys preserve their original relative order.
     *
     * @param iterable<mixed>           $data
     * @param callable(mixed):mixed     $keyFn function used to extract the comparison key from each element.
     *
     * @return \Generator
     */
    public static function sortBy(iterable $data, callable $keyFn): \Generator
    {
        /** @var list<array{0: mixed, 1: mixed}> $decorated */
        $decorated = [];
        foreach (Transform::toIterator($data) as $value) {
            $decorated[] = [$keyFn($value), $value];
        }

        \usort($decorated, static fn (array $a, array $b) => $a[0] <=> $b[0]);

        foreach ($decorated as [, $value]) {
            yield $value;
        }
    }

    /**
     * Sorts the given iterable using a key-extraction function (Schwartzian transform),
     * preserving key associations.
     *
     * The key function is called exactly once per element. Source keys are preserved
     * (matching Sort::asort).
     *
     * Stable: elements with equal extracted keys preserve their original relative order.
     *
     * @param iterable<mixed>           $data
     * @param callable(mixed):mixed     $keyFn function used to extract the comparison key from each element.
     *
     * @return \Generator
     */
    public static function asortBy(iterable $data, callable $keyFn): \Generator
    {
        /** @var list<array{0: mixed, 1: mixed, 2: mixed}> $decorated */
        $decorated = [];
        foreach (Transform::toIterator($data) as $key => $value) {
            $decorated[] = [$keyFn($value), $key, $value];
        }

        \usort($decorated, static fn (array $a, array $b) => $a[0] <=> $b[0]);

        foreach ($decorated as [, $key, $value]) {
            yield $key => $value;
        }
    }

    /**
     * Yields the n largest elements of the given iterable, in descending order.
     *
     * Uses a bounded heap of size n internally — the full input is never sorted.
     *
     * Stable: elements with equal comparison keys are retained in original input order
     * when ties exceed available slots, and emitted in insertion order among ties.
     *
     * NaN policy: elements whose comparison key is NaN are skipped.
     *
     * @param iterable<mixed>            $data
     * @param int                        $n     number of largest elements to keep (must be non-negative).
     * @param callable(mixed):mixed|null $keyFn (optional) function used to extract the comparison key from each element.
     *
     * @return \Generator
     *
     * @throws \InvalidArgumentException if $n is negative.
     */
    public static function largest(iterable $data, int $n, ?callable $keyFn = null): \Generator
    {
        if ($n < 0) {
            throw new \InvalidArgumentException('n must be non-negative');
        }
        if ($n === 0) {
            return;
        }

        // Min-heap (root is the smallest retained element); evict on strict-greater key.
        $heap = new SortBoundedHeap(-1);

        $idx = 0;
        foreach ($data as $value) {
            $key = $keyFn === null ? $value : $keyFn($value);

            if (\is_float($key) && \is_nan($key)) {
                ++$idx;
                continue;
            }

            $entry = [$key, $idx, $value];

            if ($heap->count() < $n) {
                $heap->insert($entry);
            } else {
                $root = $heap->top();
                if (($key <=> $root[0]) > 0) {
                    $heap->extract();
                    $heap->insert($entry);
                }
            }

            ++$idx;
        }

        /** @var list<array{0: mixed, 1: int, 2: mixed}> $out */
        $out = [];
        foreach ($heap as $item) {
            $out[] = $item;
        }
        \usort(
            $out,
            static fn (array $a, array $b) => ($b[0] <=> $a[0]) ?: ($a[1] <=> $b[1])
        );

        foreach ($out as $item) {
            yield $item[2];
        }
    }

    /**
     * Yields the n smallest elements of the given iterable, in ascending order.
     *
     * Uses a bounded heap of size n internally — the full input is never sorted.
     *
     * Stable: elements with equal comparison keys are retained in original input order
     * when ties exceed available slots, and emitted in insertion order among ties.
     *
     * NaN policy: elements whose comparison key is NaN are skipped.
     *
     * @param iterable<mixed>            $data
     * @param int                        $n     number of smallest elements to keep (must be non-negative).
     * @param callable(mixed):mixed|null $keyFn (optional) function used to extract the comparison key from each element.
     *
     * @return \Generator
     *
     * @throws \InvalidArgumentException if $n is negative.
     */
    public static function smallest(iterable $data, int $n, ?callable $keyFn = null): \Generator
    {
        if ($n < 0) {
            throw new \InvalidArgumentException('n must be non-negative');
        }
        if ($n === 0) {
            return;
        }

        // Max-heap (root is the largest retained element); evict on strict-less key.
        $heap = new SortBoundedHeap(1);

        $idx = 0;
        foreach ($data as $value) {
            $key = $keyFn === null ? $value : $keyFn($value);

            if (\is_float($key) && \is_nan($key)) {
                ++$idx;
                continue;
            }

            $entry = [$key, $idx, $value];

            if ($heap->count() < $n) {
                $heap->insert($entry);
            } else {
                $root = $heap->top();
                if (($key <=> $root[0]) < 0) {
                    $heap->extract();
                    $heap->insert($entry);
                }
            }

            ++$idx;
        }

        /** @var list<array{0: mixed, 1: int, 2: mixed}> $out */
        $out = [];
        foreach ($heap as $item) {
            $out[] = $item;
        }
        \usort(
            $out,
            static fn (array $a, array $b) => ($a[0] <=> $b[0]) ?: ($a[1] <=> $b[1])
        );

        foreach ($out as $item) {
            yield $item[2];
        }
    }
}

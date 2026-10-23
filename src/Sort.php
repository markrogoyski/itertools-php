<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\Arithmetic;
use IterTools\Util\SortBoundedHeap;

final class Sort
{
    /**
     * Sorts the given iterable, maintaining index key associations.
     *
     * If comparator is null, the elements of given iterable must be comparable. The default comparison
     * compares an int and a float by exact value, and sorts a float NAN after every other value, keeping
     * NaNs in input order. A NAN nested inside a composite value is not detected; see README "NaN and
     * Ordering". A custom comparator is used as given.
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

        if ($comparator !== null) {
            \usort($pairs, static fn (array $a, array $b) => $comparator($a[1], $b[1]));
        } elseif (self::needsExactComparison(\array_column($pairs, 1))) {
            \usort($pairs, static fn (array $a, array $b) => self::compareDefault($a[1], $b[1]));
        } else {
            \usort($pairs, static fn (array $a, array $b) => $a[1] <=> $b[1]);
        }

        foreach ($pairs as [$key, $value]) {
            yield $key => $value;
        }
    }

    /**
     * Sorts the given iterable
     *
     * If comparator is null, the elements of given iterable must be comparable. The default comparison
     * compares an int and a float by exact value, and sorts a float NAN after every other value. A NAN
     * nested inside a composite value is not detected; see README "NaN and Ordering". A custom comparator
     * is used as given.
     *
     * @param iterable<mixed> $data
     * @param callable(mixed, mixed):int|null $comparator (optional) function to determine how to sort elements if default sort is not appropriate.
     *
     * @return \Generator
     */
    public static function sort(iterable $data, ?callable $comparator = null): \Generator
    {
        $array = \iterator_to_array(Transform::toIterator($data), false);

        if ($comparator !== null) {
            \usort($array, $comparator);
        } elseif (self::needsExactComparison($array)) {
            \usort($array, self::compareDefault(...));
        } else {
            \sort($array);
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
     * An int and a float key compare by exact value, and an element whose key is a float NAN sorts after
     * every other element. A NAN nested inside a composite key is not detected; see README "NaN and
     * Ordering".
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

        if (self::needsExactComparison(\array_column($decorated, 0))) {
            \usort($decorated, static fn (array $a, array $b) => self::compareDefault($a[0], $b[0]));
        } else {
            \usort($decorated, static fn (array $a, array $b) => $a[0] <=> $b[0]);
        }

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
     * An int and a float key compare by exact value, and an element whose key is a float NAN sorts after
     * every other element. A NAN nested inside a composite key is not detected; see README "NaN and
     * Ordering".
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

        if (self::needsExactComparison(\array_column($decorated, 0))) {
            \usort($decorated, static fn (array $a, array $b) => self::compareDefault($a[0], $b[0]));
        } else {
            \usort($decorated, static fn (array $a, array $b) => $a[0] <=> $b[0]);
        }

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
     * An int and a float key compare by exact value, as in the default comparison of sort.
     *
     * NaN policy: elements whose comparison key is NaN are skipped. A NaN nested inside a composite
     * key (e.g. [NAN, $id]) is not detected; see README "NaN and Ordering".
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
                // <=> is exact except that it can find an int beyond ±2^53 and a float equal when they are
                // not (rounding to float is monotonic). Most keys fall below the root, so test that first.
                if (($key <=> $root[0]) >= 0) {
                    if (($key <=> $root[0]) > 0 || Arithmetic::compareValues($key, $root[0]) > 0) {
                        $heap->extract();
                        $heap->insert($entry);
                    }
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
            static fn (array $a, array $b) => ($b[0] <=> $a[0])
                ?: ((\is_int($a[0]) !== \is_int($b[0]) ? Arithmetic::compareValues($b[0], $a[0]) : 0)
                    ?: ($a[1] <=> $b[1]))
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
     * An int and a float key compare by exact value, as in the default comparison of sort.
     *
     * NaN policy: elements whose comparison key is NaN are skipped. A NaN nested inside a composite
     * key (e.g. [NAN, $id]) is not detected; see README "NaN and Ordering".
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
                // <=> is exact except that it can find an int beyond ±2^53 and a float equal when they are
                // not (rounding to float is monotonic). Most keys fall above the root, so test that first.
                if (($key <=> $root[0]) <= 0) {
                    if (($key <=> $root[0]) < 0 || Arithmetic::compareValues($key, $root[0]) < 0) {
                        $heap->extract();
                        $heap->insert($entry);
                    }
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
            static fn (array $a, array $b) => ($a[0] <=> $b[0])
                ?: ((\is_int($a[0]) !== \is_int($b[0]) ? Arithmetic::compareValues($a[0], $b[0]) : 0)
                    ?: ($a[1] <=> $b[1]))
        );

        foreach ($out as $item) {
            yield $item[2];
        }
    }

    /**
     * Yields the elements of the given iterable in a random order.
     *
     * Materializes the input. Source keys are discarded; output keys are sequential 0-indexed.
     *
     * @param iterable<mixed>     $data
     * @param \Random\Engine|null $engine optional Random engine (defaults to PHP default).
     *
     * @return \Generator
     */
    public static function shuffle(iterable $data, ?\Random\Engine $engine = null): \Generator
    {
        $array = \iterator_to_array(Transform::toIterator($data), false);

        $randomizer = new \Random\Randomizer($engine);
        /** @var array<int, mixed> $shuffled */
        $shuffled = $randomizer->shuffleArray($array);

        foreach ($shuffled as $value) {
            yield $value;
        }
    }

    /**
     * The default comparison of sort, asort, sortBy and asortBy: a float NAN sorts after every other value
     * and ties with another NAN, so the stable sort keeps NaNs in input order; every other pair is compared
     * by {@see Arithmetic::compareValues()}, which is <=> except that an int and a float compare exactly.
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return int
     */
    private static function compareDefault(mixed $a, mixed $b): int
    {
        $aIsNan = \is_float($a) && \is_nan($a);
        $bIsNan = \is_float($b) && \is_nan($b);
        if ($aIsNan || $bIsNan) {
            return $aIsNan <=> $bIsNan;
        }

        return Arithmetic::compareValues($a, $b);
    }

    /**
     * Whether {@see compareDefault()} can order the values differently from <=>: only if one is a float NAN,
     * or there are a float and an int beyond ±2^53, which <=> compares as floats. Otherwise callers keep <=>
     * and \sort(), which give the same order much faster than a comparator written in PHP.
     *
     * @param array<mixed> $values
     *
     * @return bool
     */
    private static function needsExactComparison(array $values): bool
    {
        $hasFloat = false;
        $hasLargeInt = false;
        foreach ($values as $value) {
            if (\is_float($value)) {
                if (\is_nan($value)) {
                    return true;
                }
                $hasFloat = true;
            } elseif (
                \is_int($value)
                && ($value > Arithmetic::FLOAT_EXACT_INT_LIMIT || $value < -Arithmetic::FLOAT_EXACT_INT_LIMIT)
            ) {
                $hasLargeInt = true;
            }
        }

        return $hasFloat && $hasLargeInt;
    }
}

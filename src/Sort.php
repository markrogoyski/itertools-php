<?php

declare(strict_types=1);

namespace IterTools;

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
}

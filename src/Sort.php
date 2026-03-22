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
}

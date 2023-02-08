<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\IteratorFactory;

class Sort
{
    /**
     * Sorts the given iterable, maintaining index key associations.
     *
     * If comparator is null, the elements of given iterable must be comparable.
     *
     * @param iterable<mixed> $data
     * @param callable|null $comparator (optional) function to determine how to sort elements if default sort is not appropriate.
     *
     * @return \Generator
     */
    public static function asort(iterable $data, callable $comparator = null): \Generator
    {
        $array = \iterator_to_array(IteratorFactory::makeIterator($data), true);

        if ($comparator === null) {
            \asort($array);
        } else {
            \uasort($array, $comparator);
        }

        foreach ($array as $key => $datum) {
            yield $key => $datum;
        }
    }

    /**
     * Sorts the given iterable
     *
     * If comparator is null, the elements of given iterable must be comparable.
     *
     * @param iterable<mixed> $data
     * @param callable|null $comparator (optional) function to determine how to sort elements if default sort is not appropriate.
     *
     * @return \Generator
     */
    public static function sort(iterable $data, callable $comparator = null): \Generator
    {
        $array = \iterator_to_array(IteratorFactory::makeIterator($data));

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

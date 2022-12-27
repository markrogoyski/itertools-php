<?php

declare(strict_types=1);

namespace IterTools;

class Single
{
    /**
     * Iterate the individual characters of a string
     *
     * @param string $string
     *
     * @return \Generator<string>
     */
    public static function string(string $string): \Generator
    {
        $characters = \mb_str_split($string);
        foreach ($characters as $character) {
            yield $character;
        }
    }

    /**
     * Repeat an item
     *
     * @param mixed $item
     * @param int   $repetitions
     *
     * @return \Generator<mixed>
     */
    public static function repeat($item, int $repetitions): \Generator
    {
        if ($repetitions < 0) {
            throw new \RangeException("Number of repetitions cannot be negative: {$repetitions}");
        }
        for ($i = $repetitions; $i > 0; $i--) {
            yield $item;
        }
    }

    /**
     * Compress an iterable by filtering out data that is not selected.
     *
     * Selectors indicates which data. True value selects item. False value filters out data.
     *
     * @param iterable<mixed> $data
     * @param iterable<bool> $selectors
     *
     * @return \Generator<mixed>
     */
    public static function compress(iterable $data, iterable $selectors): \Generator
    {
        foreach (Multi::zip($data, $selectors) as [$datum, $selector]) {
            if ($selector) {
                yield $datum;
            }
        }
    }

    /**
     * Drop elements from the iterable while the predicate function is true.
     *
     * Once the predicate function returns false once, all remaining elements are returned.
     *
     * @param iterable<mixed> $data
     * @param callable $predicate
     *
     * @return \Generator<mixed>
     */
    public static function dropWhile(iterable $data, callable $predicate): \Generator
    {
        $drop = true;
        foreach ($data as $datum) {
            if ($drop === true) {
                if (!$predicate($datum)) {
                    $drop = false;
                    yield $datum;
                    continue;
                }
                continue;
            }
            yield $datum;
        }
    }

    /**
     * Filter out elements from the iterable only returning elements where the predicate function is false.
     *
     * If no predicate is provided, the boolean value of the data is used.
     *
     * @param iterable<mixed> $data
     * @param callable|null $predicate
     *
     * @return \Generator<mixed>
     */
    public static function filterFalse(iterable $data, callable $predicate = null): \Generator
    {
        if ($predicate === null) {
            $predicate = fn($datum) => boolval($datum);
        }

        foreach ($data as $datum) {
            if (!$predicate($datum)) {
                yield $datum;
            }
        }
    }

    /**
     * Filter out elements from the iterable only returning elements where there predicate function is true.
     *
     * If no predicate is provided, the boolean value of the data is used.
     *
     * @param iterable<mixed> $data
     * @param callable|null $predicate
     *
     * @return \Generator<mixed>
     */
    public static function filterTrue(iterable $data, callable $predicate = null): \Generator
    {
        if ($predicate === null) {
            $predicate = fn($datum) => boolval($datum);
        }

        foreach ($data as $datum) {
            if ($predicate($datum)) {
                yield $datum;
            }
        }
    }

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
    public static function filterUnique(iterable $data, bool $strict = true): \Generator
    {
        $map = [];

        foreach ($data as $datum) {
            switch (true) {
                case is_array($datum):
                    $hash = 'array_'.md5(serialize($datum));
                    break;
                case is_object($datum):
                    $hash = 'object_'.($strict ? spl_object_id($datum) : md5(serialize($datum)));
                    break;
                case $strict:
                    $hash = gettype($datum).'_'.$datum;
                    break;
                case gettype($datum) === 'boolean':
                    $hash = 'boolean_'.(int)$datum;
                    break;
                case !$datum:
                    $hash = 'boolean_0';
                    break;
                case (string)$datum === '1':
                    $hash = 'boolean_1';
                    break;
                case is_numeric($datum):
                    $hash = 'numeric_'.(float)$datum;
                    break;
                default:
                    $hash = 'scalar_'.$datum;
                    break;
            }

            if (!isset($map[$hash])) {
                $map[$hash] = true;
                yield $datum;
            }
        }
    }

    /**
     * Group data by a common data element.
     *
     * The groupKeyFunction determines the key to group elements by.
     *
     * @param iterable<mixed> $data
     * @param callable        $groupKeyFunction
     *
     * @return \Generator<mixed>
     */
    public static function groupBy(iterable $data, callable $groupKeyFunction): \Generator
    {
        $groups = [];
        foreach ($data as $groupItem) {
            $groups[$groupKeyFunction($groupItem)][] = $groupItem;
        }
        foreach ($groups as $groupName => $groupData) {
            yield $groupName => $groupData;
        }
    }

    /**
     * Return elements from the iterable as long as the predicate is true.
     *
     * If no predicate is provided, the boolean value of the data is used.
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     *
     * @return \Generator<mixed>
     */
    public static function takeWhile(iterable $data, callable $predicate): \Generator
    {
        foreach ($data as $datum) {
            if ($predicate($datum)) {
                yield $datum;
            } else {
                break;
            }
        }
    }

    /**
     * Return pairs of elements from given collection.
     *
     * Returns empty generator if given collection contains less than 2 elements.
     *
     * @template T
     * @param iterable<T> $data
     *
     * @return \Generator<array{T, T}>
     */
    public static function pairwise(iterable $data): \Generator
    {
        $prevDatum = null;
        $prevDatumInitialized = false;

        foreach ($data as $datum) {
            if (!$prevDatumInitialized) {
                $prevDatum = $datum;
                $prevDatumInitialized = true;
                continue;
            }

            /** @var T $prevDatum */
            yield [$prevDatum, $datum];
            $prevDatum = $datum;
        }
    }
}

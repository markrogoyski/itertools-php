<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\UniqueExtractor;

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
            $predicate = fn($datum) => \boolval($datum);
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
            $predicate = fn($datum) => \boolval($datum);
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
        $chunked = static::chunkwiseOverlap($data, 2, 1);

        foreach ($chunked as $chunk) {
            if (count($chunk) !== 2) {
                break;
            }

            /** @var array{T, T} $chunk */
            yield $chunk;
        }
    }

    /**
     * Return chunks of elements from given collection.
     *
     * Chunk size must be at least 1.
     *
     * @template T
     * @param iterable<T> $data
     * @param int $chunkSize
     *
     * @return \Generator<array<T>>
     */
    public static function chunkwise(iterable $data, int $chunkSize): \Generator
    {
        return static::chunkwiseOverlap($data, $chunkSize, 0);
    }

    /**
     * Return overlapped chunks of elements from given collection.
     *
     * Chunk size must be at least 1.
     *
     * Overlap size must be less than chunk size.
     *
     * @template T
     * @param iterable<T> $data
     * @param int $chunkSize
     * @param int $overlapSize
     *
     * @return \Generator<array<T>>
     */
    public static function chunkwiseOverlap(iterable $data, int $chunkSize, int $overlapSize): \Generator
    {
        if ($chunkSize < 1) {
            throw new \InvalidArgumentException("Chunk size must be ≥ 1. Got {$chunkSize}");
        }

        if ($overlapSize >= $chunkSize) {
            throw new \InvalidArgumentException("Overlap size must be less than chunk size");
        }

        $chunk = [];
        $isLastIterationYielded = false;

        foreach ($data as $datum) {
            $isLastIterationYielded = false;
            $chunk[] = $datum;

            if (\count($chunk) === $chunkSize) {
                yield $chunk;
                $chunk = \array_slice($chunk, $chunkSize-$overlapSize);
                $isLastIterationYielded = true;
            }
        }

        if (!$isLastIterationYielded && \count($chunk) > 0) {
            yield $chunk;
        }
    }

    /**
     * Limit iteration to a max size limit
     *
     * @param iterable<mixed> $data
     * @param int             $limit ≥ 0, max count of iteration
     *
     * @return \Generator<mixed>
     */
    public static function limit(iterable $data, int $limit): \Generator
    {
        if ($limit < 0) {
            throw new \InvalidArgumentException("Limit must be ≥ 0. Got $limit");
        }

        $i = 0;
        foreach ($data as $datum) {
            if ($i >= $limit) {
                return;
            }
            yield $datum;
            $i++;
        }
    }

    /**
     * Map a function onto every element of the iteration
     *
     * @param iterable<mixed> $data
     * @param callable        $func
     *
     * @return \Generator
     */
    public static function map(iterable $data, callable $func): \Generator
    {
        foreach ($data as $datum) {
            yield $func($datum);
        }
    }
}

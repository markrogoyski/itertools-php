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
     * Return elements from the iterable only by given keys.
     *
     * Iterable data must contain only integer or string keys.
     *
     * Array of keys must contain only integer or string items.
     *
     * @param iterable<int|string, mixed> $data
     * @param array<int|string> $keys
     *
     * @return \Generator
     */
    public static function compressAssociative(iterable $data, array $keys): \Generator
    {
        $keyMap = \array_flip($keys);
        foreach ($data as $key => $datum) {
            if (\array_key_exists($key, $keyMap)) {
                yield $key => $datum;
            }
        }
    }

    /**
     * Return elements indexed by callback-function.
     *
     * @param iterable<mixed> $data
     * @param callable(mixed $value, mixed $key): mixed $reindexer
     *
     * @return \Generator
     */
    public static function reindex(iterable $data, callable $reindexer): \Generator
    {
        foreach ($data as $index => $datum) {
            yield $reindexer($datum, $index) => $datum;
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
        foreach ($data as $key => $datum) {
            if ($drop === true) {
                if (!$predicate($datum)) {
                    $drop = false;
                    yield $key => $datum;
                    continue;
                }
                continue;
            }
            yield $datum;
        }
    }

    /**
     * Filter out elements from the iterable only returning elements where there predicate function is true.
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     *
     * @return \Generator<mixed>
     */
    public static function filter(iterable $data, callable $predicate): \Generator
    {
        foreach ($data as $key => $datum) {
            if ($predicate($datum)) {
                yield $key => $datum;
            }
        }
    }

    /**
     * Filter out elements from the iterable that are naturally false.
     *
     * If predicate is provided, filters iterable to only elements where predicate is false.
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

        foreach ($data as $key => $datum) {
            if (!$predicate($datum)) {
                yield $key => $datum;
            }
        }
    }

    /**
     * Filter out elements from the iterable that are naturally true.
     *
     * If predicate is provided, filters iterable to only elements where predicate is true.
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

        foreach ($data as $key => $datum) {
            if ($predicate($datum)) {
                yield $key => $datum;
            }
        }
    }

    /**
     * Filter out elements from the iterable only returning elements for which keys the predicate function is true.
     *
     * @param iterable<mixed> $data
     * @param callable $predicate
     *
     * @return \Generator<mixed>
     */
    public static function filterKeys(iterable $data, callable $predicate): \Generator
    {
        foreach ($data as $key => $datum) {
            if ($predicate($key)) {
                yield $key => $datum;
            }
        }
    }

    /**
     * Flatten an iterable by a number of dimensions.
     *
     * Ex: [[1, 2], [3, 4], 5] => [1, 2, 3, 4, 5] // Flattened by one dimension
     *
     * @param iterable<mixed> $data
     * @param int             $dimensions
     *
     * @return \Generator<mixed>
     */
    public static function flatten(iterable $data, int $dimensions = 1): \Generator
    {
        if ($dimensions < 1) {
            return yield from $data;
        }

        foreach ($data as $datum) {
            if (\is_iterable($datum)) {
                yield from self::flatten($datum, $dimensions - 1);
            } else {
                yield $datum;
            }
        }
    }

    /**
     * Group data by a common data element.
     *
     * The groupKeyFunction determines the key (or multiple keys) to group elements by.
     *
     * The itemKeyFunction (optional) determines the key of element in group.
     *
     * @param iterable<mixed> $data
     * @param callable        $groupKeyFunction
     * @param callable|null   $itemKeyFunction
     *
     * @return \Generator<mixed>
     */
    public static function groupBy(
        iterable $data,
        callable $groupKeyFunction,
        callable $itemKeyFunction = null
    ): \Generator {
        $itemKeyFunction ??= fn ($x) => null;
        $groups = [];

        foreach ($data as $item) {
            $group = $groupKeyFunction($item);
            $itemKey = $itemKeyFunction($item);
            $itemGroups = \is_iterable($group)
                ? $group
                : [$group];

            foreach (Set::distinct($itemGroups) as $itemGroup) {
                if ($itemKey === null) {
                    $groups[$itemGroup][] = $item;
                } else {
                    $groups[$itemGroup][$itemKey] = $item;
                }
            }
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
        foreach ($data as $key => $datum) {
            if ($predicate($datum)) {
                yield $key => $datum;
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
        $chunked = static::chunkwiseOverlap($data, 2, 1, false);

        foreach ($chunked as $chunk) {
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
     * @param bool $includeIncompleteTail
     *
     * @return \Generator<array<T>>
     */
    public static function chunkwiseOverlap(
        iterable $data,
        int $chunkSize,
        int $overlapSize,
        bool $includeIncompleteTail = true
    ): \Generator {
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

        if (!$isLastIterationYielded && \count($chunk) > 0 && $includeIncompleteTail) {
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
        foreach ($data as $key => $datum) {
            if ($i >= $limit) {
                return;
            }
            yield $key => $datum;
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
        foreach ($data as $key => $datum) {
            yield $key => $func($datum);
        }
    }

    /**
     * Returns a new collection formed by applying a given callback mapper function to each element
     * of the given collection, and then flattening the result by one level.
     *
     * The mapper function can return scalar or collections as a result.
     *
     * @param iterable<mixed> $data
     * @param callable        $mapper
     *
     * @return \Generator
     */
    public static function flatMap(iterable $data, callable $mapper): \Generator
    {
        foreach ($data as $datum) {
            $unflattened = $mapper($datum, $mapper);
            if (\is_iterable($unflattened)) {
                foreach ($unflattened as $flattenedItem) {
                    yield $flattenedItem;
                }
            } else {
                yield $unflattened;
            }
        }
    }

    /**
     * Reverse given iterable.
     *
     * @param iterable<mixed> $data
     *
     * @return \Generator
     */
    public static function reverse(iterable $data): \Generator
    {
        $keyStack = [];
        $valueStack = [];

        foreach ($data as $key => $datum) {
            $keyStack[] = $key;
            $valueStack[] = $datum;
        }

        while (\count($keyStack) > 0) {
            yield \array_pop($keyStack) => \array_pop($valueStack);
        }
    }

    /**
     * Extract a slice of the collection.
     *
     * @template T
     *
     * @param iterable<T> $data
     * @param int<0, max> $start
     * @param int<0, max>|null $count
     * @param positive-int $step
     *
     * @return \Generator<T>
     */
    public static function slice(iterable $data, int $start = 0, int $count = null, int $step = 1): \Generator
    {
        /* @phpstan-ignore-next-line */
        if ($start < 0) {
            throw new \InvalidArgumentException("Parameter 'start' cannot be negative");
        }

        /* @phpstan-ignore-next-line */
        if ($count !== null && $count < 0) {
            throw new \InvalidArgumentException("Parameter 'count' cannot be negative");
        }

        /* @phpstan-ignore-next-line */
        if ($step <= 0) {
            throw new \InvalidArgumentException("Parameter 'step' must be positive");
        }

        $index = 0;
        $yielded = 0;
        foreach ($data as $datum) {
            if ($index++ < $start || ($index - $start - 1) % $step !== 0) {
                continue;
            }

            if ($yielded++ === $count && $count !== null) {
                break;
            }

            yield $datum;
        }
    }
}

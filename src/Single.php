<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\UniqueExtractor;

final class Single
{
    /**
     * Accumulate the running result of applying a binary operator across an iterable.
     *
     * With no initial value:
     *   - first yielded element is the first datum unchanged;
     *   - each subsequent yielded element is $op(accumulator, nextDatum).
     *
     * With an initial value:
     *   - first yielded element is the initial value;
     *   - each subsequent yielded element is $op(accumulator, nextDatum).
     *
     * The initial value is variadic (0 or 1 values) rather than nullable so that explicit
     * null is a legitimate initial value. This diverges from Math::running*, which treats
     * null as "no initial" because those helpers operate on numeric data where null is not
     * a meaningful accumulator.
     *
     * @param iterable<mixed>              $data
     * @param callable(mixed, mixed): mixed $op
     * @param mixed                        ...$initial (Optional) zero or one initial values.
     *
     * @return \Generator<mixed>
     *
     * @throws \InvalidArgumentException if more than one initial value is passed.
     */
    public static function accumulate(iterable $data, callable $op, mixed ...$initial): \Generator
    {
        if (\count($initial) > 1) {
            throw new \InvalidArgumentException(
                'accumulate expects at most one initial value, got ' . \count($initial)
            );
        }

        $hasInitial = \count($initial) === 1;
        $acc = null;
        $started = false;

        if ($hasInitial) {
            /** @var mixed $acc */
            $acc = $initial[0];
            $started = true;
            yield $acc;
        }

        foreach ($data as $datum) {
            if (!$started) {
                /** @var mixed $acc */
                $acc = $datum;
                $started = true;
            } else {
                /** @var mixed $acc */
                $acc = $op($acc, $datum);
            }
            yield $acc;
        }
    }

    /**
     * Iterate the individual characters of a string
     *
     * @param string $string
     *
     * @return \Generator<string>
     */
    public static function string(string $string): \Generator
    {
        foreach (\mb_str_split($string) as $character) {
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
    public static function repeat(mixed $item, int $repetitions): \Generator
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
        /** @var mixed $datum */
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
            if ($drop === true && $predicate($datum)) {
                continue;
            }
            $drop = false;
            yield $key => $datum;
        }
    }

    /**
     * Yield [index, value] pairs from the iterable.
     *
     * The index is sequential starting from $start, independent of the source iterable's keys.
     *
     * Negative $start is allowed.
     *
     * @param iterable<mixed> $data
     * @param int             $start
     *
     * @return \Generator<array{int, mixed}>
     */
    public static function enumerate(iterable $data, int $start = 0): \Generator
    {
        $index = $start;
        foreach ($data as $datum) {
            yield [$index, $datum];
            $index++;
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
    public static function filterFalse(iterable $data, ?callable $predicate = null): \Generator
    {
        $predicate ??= fn(mixed $datum): bool => \boolval($datum);

        foreach ($data as $key => $datum) {
            if (!(bool) $predicate($datum)) {
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
    public static function filterTrue(iterable $data, ?callable $predicate = null): \Generator
    {
        $predicate ??= fn(mixed $datum): bool => \boolval($datum);

        foreach ($data as $key => $datum) {
            if ((bool) $predicate($datum)) {
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
     * Filter the iterable, keeping only elements for which the key-aware predicate is true.
     *
     * Unlike {@see Single::filter()}, the predicate receives both the value and the key:
     * $predicate($value, $key). The predicate's return value is coerced via a (bool) cast.
     * Keys are preserved on the kept elements.
     *
     * @param iterable<mixed>             $data
     * @param callable(mixed, mixed): mixed $predicate fn($value, $key): bool
     *
     * @return \Generator<mixed>
     */
    public static function filterWithKeys(iterable $data, callable $predicate): \Generator
    {
        foreach ($data as $key => $datum) {
            if ((bool) $predicate($datum, $key)) {
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
        ?callable $itemKeyFunction = null
    ): \Generator {
        $itemKeyFunction ??= fn (mixed $_x): mixed => null;
        $groups = [];

        foreach ($data as $item) {
            $group = $groupKeyFunction($item);
            $itemKey = $itemKeyFunction($item);
            $itemGroups = \is_iterable($group)
                ? $group
                : [$group];

            foreach (Set::distinct($itemGroups) as $itemGroup) {
                /** @var int|string $itemGroup */
                if ($itemKey === null) {
                    $groups[$itemGroup][] = $item;
                } else {
                    /** @var int|string $itemKey */
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
     *
     * @psalm-suppress MoreSpecificReturnType
     */
    public static function pairwise(iterable $data): \Generator
    {
        /** @psalm-suppress LessSpecificReturnStatement @phpstan-ignore generator.valueType */
        yield from static::chunkwiseOverlap($data, 2, 1, false);
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

        if ($overlapSize < 0 || $overlapSize >= $chunkSize) {
            throw new \InvalidArgumentException("Overlap size must be ≥ 0 and less than chunk size. Got {$overlapSize}");
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
     * Lazy: the source is never advanced beyond the elements that are yielded. Once $limit
     * elements have been yielded, iteration stops without pulling a further element, so a
     * side-effecting source (file handle, HTTP pagination, DB cursor) is not over-read. A
     * $limit of 0 does not touch the source at all.
     *
     * @param iterable<mixed> $data
     * @param int             $limit ≥ 0, max count of iteration
     *
     * @return \Generator<mixed>
     *
     * @throws \InvalidArgumentException if $limit is negative
     */
    public static function limit(iterable $data, int $limit): \Generator
    {
        if ($limit < 0) {
            throw new \InvalidArgumentException("Limit must be ≥ 0. Got $limit");
        }

        if ($limit === 0) {
            return;
        }

        $i = 0;
        foreach ($data as $key => $datum) {
            yield $key => $datum;
            if (++$i >= $limit) {
                return;
            }
        }
    }

    /**
     * Iterate the last $count elements of the iteration.
     *
     * Yields the final $count elements, preserving their keys. Lazy-but-bounded: only a
     * ring buffer of size $count is ever held in memory, so it is safe over very large
     * (but finite) inputs. If $count is larger than the iteration length, all elements
     * are yielded. If $count is 0, nothing is yielded.
     *
     * @param iterable<mixed> $data
     * @param int             $count ≥ 0, number of elements to take from the end
     *
     * @return \Generator<mixed>
     *
     * @throws \InvalidArgumentException if $count is negative
     */
    public static function takeLast(iterable $data, int $count): \Generator
    {
        if ($count < 0) {
            throw new \InvalidArgumentException("Count must be ≥ 0. Got $count");
        }

        if ($count === 0) {
            return;
        }

        $buffer = [];
        $index = 0;
        foreach ($data as $key => $datum) {
            $buffer[$index] = [$key, $datum];
            if ($index >= $count) {
                unset($buffer[$index - $count]);
            }
            $index++;
        }

        foreach ($buffer as [$key, $datum]) {
            yield $key => $datum;
        }
    }

    /**
     * Iterate all elements of the iteration except the last $count.
     *
     * Yields every element except the final $count, preserving keys. Queue-based, single
     * pass: an element is only yielded once $count further elements have been seen, so the
     * trailing $count elements are never emitted. If $count is ≥ the iteration length,
     * nothing is yielded. If $count is 0, all elements are yielded.
     *
     * @param iterable<mixed> $data
     * @param int             $count ≥ 0, number of elements to drop from the end
     *
     * @return \Generator<mixed>
     *
     * @throws \InvalidArgumentException if $count is negative
     */
    public static function dropLast(iterable $data, int $count): \Generator
    {
        if ($count < 0) {
            throw new \InvalidArgumentException("Count must be ≥ 0. Got $count");
        }

        if ($count === 0) {
            yield from $data;
            return;
        }

        $buffer = [];
        $index = 0;
        foreach ($data as $key => $datum) {
            $buffer[$index] = [$key, $datum];
            if ($index >= $count) {
                /** @psalm-suppress PossiblyInvalidArrayOffset */
                [$bufferedKey, $bufferedDatum] = $buffer[$index - $count];
                unset($buffer[$index - $count]);
                yield $bufferedKey => $bufferedDatum;
            }
            $index++;
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
     * Map a function onto every element of the iteration, passing both value and key to the callback.
     *
     * Unlike {@see Single::map()}, the callback receives both the value and the key:
     * $func($value, $key). The transformed value is yielded with its original key preserved.
     *
     * @param iterable<mixed>             $data
     * @param callable(mixed, mixed): mixed $func fn($value, $key): mixed
     *
     * @return \Generator
     */
    public static function mapWithKeys(iterable $data, callable $func): \Generator
    {
        foreach ($data as $key => $datum) {
            yield $key => $func($datum, $key);
        }
    }

    /**
     * Map a function onto every element of the iteration, unpacking each element positionally as arguments.
     *
     * Each element of $data must itself be iterable. Its values are passed positionally
     * to $function via the splat operator. Inner keys are discarded — values flow positionally
     * regardless of whether each inner element is a list or an associative array. Outer keys
     * are preserved (matching {@see Single::map()}).
     *
     * @param iterable<mixed> $data
     * @param callable        $function
     *
     * @return \Generator
     *
     * @throws \InvalidArgumentException if any inner element is not iterable
     */
    public static function mapSpread(iterable $data, callable $function): \Generator
    {
        foreach ($data as $key => $item) {
            if (!\is_iterable($item)) {
                throw new \InvalidArgumentException(\sprintf(
                    'Single::mapSpread requires each element to be iterable; element at key %s is %s',
                    \var_export($key, true),
                    \get_debug_type($item)
                ));
            }
            $args = \is_array($item)
                ? \array_values($item)
                : \iterator_to_array($item, false);
            yield $key => $function(...$args);
        }
    }

    /**
     * Returns a new collection formed by applying a given callback mapper function to each element
     * of the given collection, and then flattening the result by one level.
     *
     * The mapper function can return scalar or collections as a result.
     *
     * The mapper receives itself as its second argument, enabling recursive flatMap with arrow-function
     * syntax (which cannot otherwise reference itself). For example, to fully flatten a nested structure:
     *
     *     Single::flatMap($data, fn ($item, $self) => \is_iterable($item)
     *         ? Single::flatMap($item, $self)
     *         : [$item]);
     *
     * @param iterable<mixed>                  $data
     * @param callable(mixed, callable): mixed $mapper
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
     * Map a key-aware callback over each element, then flatten the result by one level.
     *
     * Unlike {@see Single::flatMap()}, the callback receives both the value and the key, plus
     * the function itself as a third argument:
     *
     *     $func($value, $key, callable $self): mixed|iterable
     *
     * The third argument enables recursive flat-mapping over nested iterables with arrow-function
     * syntax (which cannot otherwise reference itself). For example, to fully flatten a nested
     * structure regardless of its keys:
     *
     *     Single::flatMapWithKeys($data, fn ($value, $key, $self) => \is_iterable($value)
     *         ? Single::flatMapWithKeys($value, $self)
     *         : [$value]);
     *
     * Like {@see Single::flatMap()}, the callback may return a scalar (yielded as-is) or an
     * iterable (flattened by one level). Outer and inner keys are discarded — the result is
     * yielded with auto-generated sequential numeric keys, because flattening produces key
     * collisions and surprising results. For key-preserving 1:1 mapping, use
     * {@see Single::mapWithKeys()} instead.
     *
     * @param iterable<mixed>                       $data
     * @param callable(mixed, mixed, callable): mixed $func fn($value, $key, $self): mixed|iterable
     *
     * @return \Generator
     */
    public static function flatMapWithKeys(iterable $data, callable $func): \Generator
    {
        foreach ($data as $key => $datum) {
            $unflattened = $func($datum, $key, $func);
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
     * Insert a separator between consecutive elements of an iterable.
     *
     * Yields: element, separator, element, separator, …, element.
     * The separator is not emitted before the first element or after the last one.
     * An empty input yields nothing; a single-element input yields just that element.
     *
     * The separator is yielded as-is on each pass: arrays are not expanded, objects
     * retain identity. Source keys are discarded — the output is a list with
     * sequential integer keys, because inserted separators have no natural source key.
     *
     * @param iterable<mixed> $data
     * @param mixed           $separator
     *
     * @return \Generator<mixed>
     */
    public static function intersperse(iterable $data, mixed $separator): \Generator
    {
        $first = true;
        foreach ($data as $datum) {
            if ($first) {
                $first = false;
            } else {
                yield $separator;
            }
            yield $datum;
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
     * Lazy: the source is never advanced beyond the last element that is yielded. Once $count
     * elements have been yielded, iteration stops without pulling a further element, so a
     * side-effecting source (file handle, HTTP pagination, DB cursor) is not over-read. A
     * $count of 0 does not touch the source at all. Note that elements skipped by $start or
     * $step must still be pulled to be skipped over.
     *
     * @template T
     *
     * @param iterable<T> $data
     * @param int $start
     * @param int|null $count
     * @param int $step
     *
     * @return \Generator<T>
     */
    public static function slice(iterable $data, int $start = 0, ?int $count = null, int $step = 1): \Generator
    {
        if ($start < 0) {
            throw new \InvalidArgumentException("Parameter 'start' cannot be negative");
        }

        if ($count !== null && $count < 0) {
            throw new \InvalidArgumentException("Parameter 'count' cannot be negative");
        }

        if ($step <= 0) {
            throw new \InvalidArgumentException("Parameter 'step' must be positive");
        }

        if ($count === 0) {
            return;
        }

        $index = 0;
        $yielded = 0;
        foreach ($data as $datum) {
            if ($index++ < $start || ($index - $start - 1) % $step !== 0) {
                continue;
            }

            $yielded++;

            yield $datum;

            if ($count !== null && $yielded === $count) {
                return;
            }
        }
    }

    /**
     * Skip n elements in the iterable after optional offset offset.
     *
     * @param iterable<mixed> $data
     * @param int $count
     * @param int $offset
     *
     * @return \Generator
     */
    public static function skip(iterable $data, int $count, int $offset = 0): \Generator
    {
        if ($count < 0 || $offset < 0) {
            throw new \InvalidArgumentException();
        }

        $skipped = -$offset;
        foreach ($data as $key => $datum) {
            if ($skipped < 0 || $skipped >= $count) {
                yield $key => $datum;
            }
            ++$skipped;
        }
    }

    /**
     * Split an iterable into groups, starting a new group every time $predicate matches.
     *
     * The matching element starts the next group (i.e. it is the first element of that group).
     * No leading empty group is yielded if the predicate matches the first element.
     * Empty input yields nothing. Source keys are discarded; outer is sequential, inner groups
     * are list arrays.
     *
     * Example: [1,2,0,3,0,4] with predicate fn($x) => $x === 0 yields [[1,2],[0,3],[0,4]].
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     *
     * @return \Generator<int, list<mixed>>
     */
    public static function splitWhen(iterable $data, callable $predicate): \Generator
    {
        /** @var list<mixed> $current */
        $current = [];
        $started = false;

        foreach ($data as $datum) {
            if ((bool) $predicate($datum)) {
                if ($started) {
                    yield $current;
                }
                $current = [$datum];
                $started = true;
            } else {
                $current[] = $datum;
                $started = true;
            }
        }

        if ($started) {
            yield $current;
        }
    }

    /**
     * Group adjacent elements that share a key returned by $keyFn.
     *
     * Yields [groupKey, list<value>] pairs sequentially (not associatively). Repeated keys
     * appearing in non-adjacent runs produce separate groups. Source keys are discarded;
     * outer is sequential, inner groups are list arrays.
     *
     * Example: [1,1,2,2,1,3] keyed by identity yields [[1,[1,1]],[2,[2,2]],[1,[1]],[3,[3]]].
     *
     * @param iterable<mixed>      $data
     * @param callable(mixed):mixed $keyFn
     *
     * @return \Generator<int, array{0: mixed, 1: list<mixed>}>
     */
    public static function groupAdjacentBy(iterable $data, callable $keyFn): \Generator
    {
        $hasPrevious = false;
        $previousKey = null;
        /** @var list<mixed> $current */
        $current = [];

        foreach ($data as $datum) {
            /** @var mixed $key */
            $key = $keyFn($datum);
            if (!$hasPrevious) {
                $previousKey = $key;
                $current = [$datum];
                $hasPrevious = true;
                continue;
            }

            if ($key === $previousKey) {
                $current[] = $datum;
            } else {
                yield [$previousKey, $current];
                $previousKey = $key;
                $current = [$datum];
            }
        }

        if ($hasPrevious) {
            yield [$previousKey, $current];
        }
    }

    /**
     * Pad an iterable on the left so its yielded length is at least $length.
     *
     * If the source is already $length or longer, all elements pass through unchanged.
     * Source keys are discarded; output keys are sequential 0-indexed.
     *
     * @param iterable<mixed> $data
     * @param int             $length minimum final length (must be non-negative)
     * @param mixed           $fill   value used to pad
     *
     * @return \Generator<int, mixed>
     *
     * @throws \InvalidArgumentException if $length is negative.
     */
    public static function padLeft(iterable $data, int $length, mixed $fill): \Generator
    {
        if ($length < 0) {
            throw new \InvalidArgumentException("Length cannot be negative: {$length}");
        }

        /** @var list<mixed> $buffer */
        $buffer = [];
        $count = 0;

        foreach ($data as $datum) {
            $buffer[] = $datum;
            ++$count;
        }

        $padding = $length - $count;
        for ($i = 0; $i < $padding; ++$i) {
            yield $fill;
        }

        foreach ($buffer as $datum) {
            yield $datum;
        }
    }

    /**
     * Pad an iterable on the right so its yielded length is at least $length.
     *
     * If the source is already $length or longer, all elements pass through unchanged.
     * Source keys are discarded; output keys are sequential 0-indexed.
     *
     * @param iterable<mixed> $data
     * @param int             $length minimum final length (must be non-negative)
     * @param mixed           $fill   value used to pad
     *
     * @return \Generator<int, mixed>
     *
     * @throws \InvalidArgumentException if $length is negative.
     */
    public static function padRight(iterable $data, int $length, mixed $fill): \Generator
    {
        if ($length < 0) {
            throw new \InvalidArgumentException("Length cannot be negative: {$length}");
        }

        $count = 0;
        foreach ($data as $datum) {
            yield $datum;
            ++$count;
        }

        for ($i = $count; $i < $length; ++$i) {
            yield $fill;
        }
    }

    /**
     * Yield a finite arithmetic progression of numbers, lazily.
     *
     * Direction is inferred from `$start` vs `$end`; the step's magnitude (`abs($step)`)
     * is used internally. A negative step is accepted only when the inferred direction
     * is descending, or when `$start == $end` (single-element range, step sign ignored).
     *
     * Output type follows native `\range()`: a float `$start` or `$end` always promotes;
     * an integer-valued float step on int operands preserves int output.
     *
     * @param int|float $start
     * @param int|float $end
     * @param int|float $step (optional) Step magnitude. Defaults to 1.
     *
     * @return \Generator<int, int|float>
     *
     * @throws \InvalidArgumentException when any operand is non-finite, when `$step` is zero,
     *         when the step direction conflicts with the start→end direction, or when the
     *         step magnitude exceeds the span between `$start` and `$end` and `$start != $end`.
     */
    public static function range(int|float $start, int|float $end, int|float $step = 1): \Generator
    {
        if (\is_float($start) && !\is_finite($start)) {
            throw new \InvalidArgumentException('Single::range: start must be finite, got: ' . \var_export($start, true));
        }
        if (\is_float($end) && !\is_finite($end)) {
            throw new \InvalidArgumentException('Single::range: end must be finite, got: ' . \var_export($end, true));
        }
        if (\is_float($step) && !\is_finite($step)) {
            throw new \InvalidArgumentException('Single::range: step must be finite, got: ' . \var_export($step, true));
        }
        if ($step == 0) {
            throw new \InvalidArgumentException('Single::range: step must not be zero');
        }
        // \abs(PHP_INT_MIN) overflows int and returns a float; downstream int
        // arithmetic (\intdiv) would then TypeError. Native PHP \range() also
        // rejects this. Block it explicitly.
        if ($step === \PHP_INT_MIN) {
            throw new \InvalidArgumentException(
                'Single::range: step magnitude exceeds PHP_INT_MAX (PHP_INT_MIN is not a valid step)'
            );
        }

        // Route oversized integer-valued float steps to the float path to avoid PHP 8.5
        // non-representable int-cast warnings and downstream \intdiv() TypeErrors.
        $useFloat = \is_float($start)
            || \is_float($end)
            || (\is_float($step) && (
                \fmod($step, 1.0) !== 0.0
                || \abs($step) >= (float)\PHP_INT_MAX
            ));

        if ($start == $end) {
            yield $useFloat ? (float)$start : $start;
            return;
        }

        $ascending = $end > $start;

        if ($ascending && $step < 0) {
            throw new \InvalidArgumentException(
                "Single::range: step direction ({$step}) conflicts with start ({$start}) → end ({$end})"
            );
        }

        if ($useFloat) {
            $startF = (float)$start;
            $endF = (float)$end;
            $magnitudeF = \abs((float)$step);
            $spanF = \abs($endF - $startF);

            if ($magnitudeF > $spanF) {
                throw new \InvalidArgumentException(
                    "Single::range: step magnitude ({$magnitudeF}) must not exceed span ({$spanF})"
                );
            }

            // Compute element count up front and derive each value from $start to avoid
            // (a) accumulator non-termination when the step is smaller than float
            // spacing at the operand magnitude (e.g. range(1e16, 1e16 + 10, 1.0)),
            // and (b) drift from accumulated rounding error over long ranges.
            $stepsQuotient = $spanF / $magnitudeF;
            // Reject quotients that aren't representable as int. INF / NAN fail
            // is_finite(); finite-but-too-large floats (e.g. 1.0e20) exceed PHP_INT_MAX
            // and casting them to int produces a wrong bound plus a PHP 8.5 deprecation.
            // Use `>=` not `>`: (float)PHP_INT_MAX rounds to 2^63 (one past PHP_INT_MAX),
            // so a quotient *equal* to that float is already in the unrepresentable region.
            if (!\is_finite($stepsQuotient) || $stepsQuotient >= (float)\PHP_INT_MAX) {
                throw new \InvalidArgumentException(
                    'Single::range: span / step exceeds representable iteration count '
                    . "(span={$spanF}, step={$magnitudeF})"
                );
            }
            // Add +1 to cover the case where $stepsQuotient is mathematically an
            // integer but evaluates to N - epsilon in IEEE 754 (e.g. (0.5-0.4)/0.05
            // → 1.999... not 2.0), which would otherwise truncate the final element.
            // The clamp below guards against an actual overshoot.
            $stepsF = (int)\floor($stepsQuotient) + 1;
            $signF = $ascending ? 1.0 : -1.0;

            for ($i = 0; $i <= $stepsF; ++$i) {
                $valF = $startF + $signF * ((float)$i * $magnitudeF);
                // Quotient can be off-by-one when very close to an integer, causing
                // the reconstructed value to overshoot $end (e.g. 0.3 + 3*0.2 ==
                // 0.9000000000000001). Clamp.
                if ($ascending ? $valF > $endF : $valF < $endF) {
                    break;
                }
                yield $valF;
            }
        } else {
            $startI = (int)$start;
            $endI = (int)$end;
            $magnitudeI = \abs((int)$step);

            // Step magnitude vs span, computed without subtracting $endI - $startI
            // (which may overflow int when operands sit on opposite sides of zero).
            // Ascending: reject when $magnitudeI > $endI - $startI, i.e. when
            // $endI < $startI + $magnitudeI. If $startI + $magnitudeI overflows
            // (only possible when $startI > PHP_INT_MAX - $magnitudeI), the true
            // sum exceeds PHP_INT_MAX ≥ $endI, so the rejection still holds.
            // Descending: symmetric on $endI + $magnitudeI vs $startI.
            $stepExceedsSpan = $ascending
                ? ($startI > \PHP_INT_MAX - $magnitudeI || $endI < $startI + $magnitudeI)
                : ($endI > \PHP_INT_MAX - $magnitudeI || $startI < $endI + $magnitudeI);
            if ($stepExceedsSpan) {
                throw new \InvalidArgumentException(
                    "Single::range: step magnitude ({$magnitudeI}) must not exceed span between {$startI} and {$endI}"
                );
            }

            // Lazy iteration with overflow-safe step advances. Handles arbitrary
            // spans — including those that overflow int subtraction — by never
            // precomputing the count. Naive `$v += $magnitudeI` at int boundaries
            // would promote $v to float and never terminate, so each advance is
            // gated on whether it would cross PHP_INT_MAX / PHP_INT_MIN.
            $val = $startI;
            yield $val;
            if ($ascending) {
                while ($val <= \PHP_INT_MAX - $magnitudeI) {
                    $val += $magnitudeI;
                    if ($val > $endI) {
                        break;
                    }
                    yield $val;
                }
            } else {
                while ($val >= \PHP_INT_MIN + $magnitudeI) {
                    $val -= $magnitudeI;
                    if ($val < $endI) {
                        break;
                    }
                    yield $val;
                }
            }
        }
    }

    /**
     * Pair every element with a boolean flag marking whether it is the first element.
     *
     * Yields [bool $isFirst, mixed $value] tuples. Fully lazy with O(1) memory.
     *
     * Source keys are discarded; output keys are sequential 0-indexed (matching the
     * house convention for tuple-emitting methods such as {@see Single::enumerate()}).
     *
     * @param iterable<mixed> $data
     *
     * @return \Generator<int, array{0: bool, 1: mixed}>
     */
    public static function withFirst(iterable $data): \Generator
    {
        $isFirst = true;
        foreach ($data as $datum) {
            yield [$isFirst, $datum];
            $isFirst = false;
        }
    }

    /**
     * Pair every element with a boolean flag marking whether it is the last element.
     *
     * Yields [bool $isLast, mixed $value] tuples. Uses a single-element lookahead, so it is
     * lazy with O(1) memory.
     *
     * Source keys are discarded; output keys are sequential 0-indexed (matching the
     * house convention for tuple-emitting methods such as {@see Single::enumerate()}).
     *
     * @param iterable<mixed> $data
     *
     * @return \Generator<int, array{0: bool, 1: mixed}>
     */
    public static function withLast(iterable $data): \Generator
    {
        $hasPrevious = false;
        /** @var mixed $previous */
        $previous = null;

        foreach ($data as $datum) {
            if ($hasPrevious) {
                yield [false, $previous];
            }
            $previous = $datum;
            $hasPrevious = true;
        }

        if ($hasPrevious) {
            yield [true, $previous];
        }
    }

    /**
     * Pair every element with boolean flags marking whether it is the first and/or last element.
     *
     * Yields [bool $isFirst, bool $isLast, mixed $value] tuples — the common "mark ends" pattern.
     * A single-element input yields one [true, true, $value] tuple. Uses a single-element
     * lookahead, so it is lazy with O(1) memory.
     *
     * Source keys are discarded; output keys are sequential 0-indexed (matching the
     * house convention for tuple-emitting methods such as {@see Single::enumerate()}).
     *
     * @param iterable<mixed> $data
     *
     * @return \Generator<int, array{0: bool, 1: bool, 2: mixed}>
     */
    public static function withFirstAndLast(iterable $data): \Generator
    {
        $hasPrevious = false;
        $isFirst = true;
        /** @var mixed $previous */
        $previous = null;

        foreach ($data as $datum) {
            if ($hasPrevious) {
                yield [$isFirst, false, $previous];
                $isFirst = false;
            }
            $previous = $datum;
            $hasPrevious = true;
        }

        if ($hasPrevious) {
            yield [$isFirst, true, $previous];
        }
    }

    /**
     * Yield sliding windows of $size elements, advancing $step elements between windows.
     *
     * A step-based sibling of {@see Single::chunkwiseOverlap()} that additionally supports
     * gapped windows ($step > $size), which `chunkwiseOverlap` cannot express.
     *
     * Each window is a 0-indexed list array; source keys are discarded. Memory is bounded by
     * O($size).
     *
     * For 1 <= $step <= $size this is exactly equivalent to
     * `chunkwiseOverlap($data, $size, $size - $step, includeIncompleteTail: $partial)`: at most one
     * trailing partial window is emitted, and only when the final element did not already complete
     * a full window. Note $partial defaults to false — intentionally the opposite of
     * `chunkwiseOverlap`'s $includeIncompleteTail default of true.
     *
     * For $step > $size, windows start at element indices 0, $step, 2*$step, …; the
     * ($step - $size) elements following each full window are dropped, and a trailing partial may
     * begin only at a valid window start, never inside a skip gap.
     *
     * Implementation note: the gap is handled by a dedicated skip counter — a
     * chunkwiseOverlap-style buffer-drop alone cannot express it and would silently collapse
     * $step > $size down to $step == $size.
     *
     * @template T
     * @param iterable<T> $data
     * @param int $size  window length (must be ≥ 1)
     * @param int $step  number of elements to advance between window starts (must be ≥ 1)
     * @param bool $partial whether to emit a final incomplete window
     *
     * @return \Generator<int, list<T>>
     *
     * @throws \InvalidArgumentException if $size < 1 or $step < 1
     */
    public static function windowed(iterable $data, int $size, int $step = 1, bool $partial = false): \Generator
    {
        if ($size < 1) {
            throw new \InvalidArgumentException("Window size must be ≥ 1. Got {$size}");
        }
        if ($step < 1) {
            throw new \InvalidArgumentException("Step must be ≥ 1. Got {$step}");
        }

        /** @var list<T> $window */
        $window = [];
        $skip = 0;
        $lastIterationYielded = false;

        foreach ($data as $datum) {
            $lastIterationYielded = false;

            // In the gap between windows ($step > $size); drop the element (see doc block)
            if ($skip > 0) {
                $skip--;
                continue;
            }

            $window[] = $datum;

            if (\count($window) === $size) {
                yield $window;
                $lastIterationYielded = true;

                if ($step < $size) {
                    // Overlapping: retain the trailing ($size - $step) elements
                    $window = \array_slice($window, $step);
                } else {
                    // Tiling or gapped
                    $window = [];
                    $skip = $step - $size;
                }
            }
        }

        // At most one trailing partial window (see doc block)
        if ($partial && !$lastIterationYielded && \count($window) > 0) {
            yield $window;
        }
    }
}

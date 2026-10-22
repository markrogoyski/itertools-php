<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\Arithmetic;
use IterTools\Util\Truthiness;
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
            $acc = \array_values($initial)[0];
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
            if (\is_float($selector) ? Truthiness::of($selector) : (bool) $selector) {
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
            if ($drop === true && Truthiness::of($predicate($datum))) {
                continue;
            }
            $drop = false;
            yield $key => $datum;
        }
    }

    /**
     * Drop elements while a key-aware predicate is truthy.
     *
     * @param iterable<mixed> $data
     * @param callable(mixed, mixed): mixed $predicate
     *
     * @return \Generator<mixed>
     */
    public static function dropWhileWithKeys(iterable $data, callable $predicate): \Generator
    {
        $drop = true;
        foreach ($data as $key => $datum) {
            if ($drop && Truthiness::of($predicate($datum, $key))) {
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
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
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
        $predicate ??= static fn (mixed $datum): mixed => $datum;

        foreach ($data as $key => $datum) {
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? !Truthiness::of($verdict) : !$verdict) {
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
        $predicate ??= static fn (mixed $datum): mixed => $datum;

        foreach ($data as $key => $datum) {
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
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
            $verdict = $predicate($key);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
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
            $verdict = $predicate($datum, $key);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
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

        // The keys are renumbered once, here. The nested levels pass each element up by yield from, which
        // hands it straight to this loop, rather than yielding it again at every level.
        foreach (self::flattenElements($data, $dimensions) as $datum) {
            yield $datum;
        }
    }

    /**
     * The elements of $data, with each iterable among them flattened by $dimensions - 1 dimensions. Keys are
     * not meaningful: {@see Single::flatten()} renumbers them.
     *
     * @param iterable<mixed> $data
     * @param int             $dimensions
     *
     * @return \Generator<mixed>
     */
    private static function flattenElements(iterable $data, int $dimensions): \Generator
    {
        foreach ($data as $datum) {
            if ($dimensions > 0 && \is_iterable($datum)) {
                yield from self::flattenElements($datum, $dimensions - 1);
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
     * A group name must be an int, a string or a bool. A bool becomes the key 0 (false) or 1 (true), so it
     * merges with those ints. Any other name (float, null, array, object, ...) throws a \TypeError, whether
     * the groupKeyFunction returns it alone or inside a list of names. Reduce::toCountBy rejects bool too.
     *
     * @param iterable<mixed> $data
     * @param callable        $groupKeyFunction
     * @param callable|null   $itemKeyFunction
     *
     * @return \Generator<mixed>
     *
     * @throws \TypeError if a group name is not an int, string or bool
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

            // Dedupe by array key, so names PHP normalizes to the same key (2020 and '2020') count once.
            $distinctItemGroups = [];
            foreach ($itemGroups as $itemGroup) {
                if (!\is_int($itemGroup) && !\is_string($itemGroup) && !\is_bool($itemGroup)) {
                    throw new \TypeError(
                        \sprintf('Group name must be int|string|bool, got %s', \gettype($itemGroup))
                    );
                }
                // A bool name becomes the int key 0 or 1, merging with those ints.
                $distinctItemGroups[\is_bool($itemGroup) ? (int) $itemGroup : $itemGroup] = true;
            }

            foreach (\array_keys($distinctItemGroups) as $itemGroup) {
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
     * @param iterable<mixed> $data
     * @param callable        $predicate
     *
     * @return \Generator<mixed>
     */
    public static function takeWhile(iterable $data, callable $predicate): \Generator
    {
        foreach ($data as $key => $datum) {
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
                yield $key => $datum;
            } else {
                break;
            }
        }
    }

    /**
     * Return elements while a key-aware predicate is truthy.
     *
     * @param iterable<mixed> $data
     * @param callable(mixed, mixed): mixed $predicate
     *
     * @return \Generator<mixed>
     */
    public static function takeWhileWithKeys(iterable $data, callable $predicate): \Generator
    {
        foreach ($data as $key => $datum) {
            $verdict = $predicate($datum, $key);
            if (\is_float($verdict) ? !Truthiness::of($verdict) : !$verdict) {
                return;
            }
            yield $key => $datum;
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
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
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
     * Output type pins native `\range()`'s PHP 8.3+ semantics on every supported PHP version:
     * a float `$start` or `$end` always promotes, and an integer-valued float step on int
     * operands preserves int output. This is deliberately not a passthrough to whatever the
     * running PHP does — PHP 8.2's native `\range()` sends any float step down the float path,
     * so `range(1, 5, 1.0)` yields floats there but ints here.
     *
     * The span of an int operand beyond ±2^53 is exact when the other operand is an int or an integral float
     * within the int range. Paired with a non-integral float or a float beyond the int range, the int is
     * converted to float first and the span can be off: `range(PHP_INT_MAX, 2.0 ** 63, 0.5)` sees a span of 0
     * and yields one element, where the true span of 1 gives three.
     *
     * With such exact int operands and a whole step (an int or an integral float), the element count comes
     * from the exact span and each value is `$start ± i·|$step|` computed exactly and correctly rounded to
     * float: `range(PHP_INT_MIN, PHP_INT_MAX, 2.0 ** 63)` yields `[-2^63, 0.0]`. With a fractional step, the
     * values are computed in float, as for float operands, except from an int `$start` past 2^53 that a float
     * can't hold: those are `$start ± i·|$step|` computed exactly and correctly rounded, since a value
     * computed from `(float) $start` carries its rounding error. `range(2 ** 53 + 3, 0, -(2.0 ** 52 - 0.5))`
     * yields `[2^53 + 4, 2^52 + 4, 4.0]`, where computing from `(float) $start` ends on 5.0.
     *
     * A float range ends on `$end` when the exact span is a whole number of steps, give or take the rounding the
     * operands carry from the decimals they are written as: half a float spacing of each non-integral float
     * operand, and of a non-integral step for each step taken. An int, or an integral float that is its own
     * shortest decimal (2.0 ** 53, 1e16), counts as exact; 1e23 isn't, and stands for 10^23. So `range(0.0, 1.0,
     * 0.1)` ends on 1.0, but `range(2.0 ** 53, -0.1, -2.0 ** 53)` yields `[2^53, 0.0]`: its span 2^53 + 0.1
     * rounds to the step in float, but holds one step and 0.1 more. On int operands, `range(PHP_INT_MIN, 0,
     * 2.0 ** 52 - 0.5)` ends on -1024.0, as 2048 steps fall 1024 short, beyond the 512 their rounding allows. A
     * step computed in float, such as `($end - $start) / $n`, also carries the rounding of the subtraction, which
     * is allowed too, up to twice the operands' own: such a range ends on `$end`, unless the larger operand is
     * exact and the subtraction absorbed part of the smaller one, as in the 2.0 ** 53 example. A range whose exact
     * last value rounds to `$end` ends on `$end` too. Past 2^48 steps, with a step below 2^-900 or an operand
     * beyond 2^995, the float quotient decides instead, to within 2ε of its value plus the operands' half
     * spacings.
     *
     * A step at or above the span of two float operands computed in float is compared with their exact span,
     * which the float span can round up to the step: `range(0.1, 2.0 ** 53, 2.0 ** 53)` throws, as the exact
     * span is 2^53 - 0.1. The step is also accepted when the decimals the operands are written as span it, each
     * non-integral float taken as its shortest decimal and an integral float as itself: `range(-10.0, -3.6, 6.4)`
     * yields `[-10.0, -3.6]`, although the floats' exact span is 6.4 - 4.4e-16, and `range(0.1, 0.3, 0.2)` yields
     * `[0.1, 0.3]`, although their float span is 0.19999999999999998. `range(0.1, 0.2, 0.10000000000000002)`
     * throws, as neither span reaches the step. A step computed as `$end - $start` is allowed the rounding of the
     * subtraction, capped as for landing: `range(10.19, 33.0, 33.0 - 10.19)` yields `[10.19, 33.0]`, although the
     * step 22.810000000000002 exceeds the exact span.
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

        // Compare and subtract an int operand and an integral float operand in the int domain. The float converts
        // to int exactly, while the int past 2^53 may not convert to float exactly: 2^53 + 1 and 2.0^53 compare
        // equal as floats.
        $startExact = $start;
        $endExact = $end;
        if (\is_int($start)) {
            $endExact = Arithmetic::toExactInt($end) ?? $end;
        } elseif (\is_int($end)) {
            $startExact = Arithmetic::toExactInt($start) ?? $start;
        }

        if ($startExact == $endExact) {
            yield $useFloat ? (float)$start : $start;
            return;
        }

        $ascending = $endExact > $startExact;

        if ($ascending && $step < 0) {
            throw new \InvalidArgumentException(
                "Single::range: step direction ({$step}) conflicts with start ({$start}) → end ({$end})"
            );
        }

        if ($useFloat) {
            $startF = (float)$start;
            $endF = (float)$end;
            $magnitudeF = \abs((float)$step);
            // Take the span of int operands from the ints themselves. Distinct ints past 2^53 can share a
            // float (2^53 and 2^53 + 1), which would collapse the span to 0. An overflowing int subtraction
            // promotes to float, which is accurate enough there. An integral float operand counts as an int (above).
            $intOperands = \is_int($startExact) && \is_int($endExact);
            $spanF = $intOperands
                ? \abs((float)($endExact - $startExact))
                : \abs($endF - $startF);

            if ($intOperands) {
                // Compare the step with the exact int span. Converted to float, a span of PHP_INT_MAX rounds up
                // to 2^63 and lets a step of 2^63 through. A span that overflows int is in [2^63, 2^64), so it
                // exceeds 2^63 by $high - ($low - PHP_INT_MIN), which fits int; a step at least 2^63 is a float,
                // and its excess over 2^63 is exact.
                $magnitude = \abs($step);
                $magnitudeDescription = "{$magnitude}";
                [$low, $high] = $ascending ? [$startExact, $endExact] : [$endExact, $startExact];
                $span = $high - $low;
                if (\is_int($span)) {
                    $stepExceedsSpan = Arithmetic::compare($magnitude, $span) > 0;
                    $spanDescription = "({$span})";
                } else {
                    $excess = $high - ($low - \PHP_INT_MIN);
                    $stepExceedsSpan = Arithmetic::compare((float)$magnitude + (float)\PHP_INT_MIN, $excess) > 0;
                    $spanDescription = "between {$startExact} and {$endExact}";
                }
            } else {
                $magnitude = $magnitudeF;
                // A step below the float span is below the exact span too, since rounding is monotonic. A step at
                // or above it may still be within the exact span (2^53 - 0.1 rounds to the step 2^53), or within
                // the span of the decimals the operands are written as (0.3 - 0.1 gives 0.19999999999999998, below
                // the step 0.2). It exceeds the span when it exceeds both.
                // A step computed as $end - $start carries the rounding of the subtraction, which the exact span is
                // allowed, as for landing, so it is accepted as ($end - $start) / $n is: 33.0 - 10.19 rounds up to
                // 22.810000000000002. As for landing, the allowance is capped at twice the operands' half float
                // spacings, which bound that rounding when the larger operand isn't integral; an integral float is
                // taken as exact, so range(0.1, 2.0 ** 53, 2.0 ** 53) still throws. It is worked out only for a
                // step at least the float span, which a step below it can't exceed.
                $stepExceedsSpan = false;
                if ($magnitudeF >= $spanF) {
                    $spanError = self::exactFloatDifference(\max($startF, $endF), \min($startF, $endF))[1];
                    $operandAllowance = 0.0;
                    if (self::roundingAllowances($start, $end, \abs($step), false) !== []) {
                        foreach ([$start, $end] as $operand) {
                            if (\is_float($operand) && \floor($operand) !== $operand) {
                                $operandAllowance += Arithmetic::halfFloatSpacing($operand);
                            }
                        }
                    }
                    $differenceAllowance = [\min(\abs($spanError), 2.0 * $operandAllowance)];
                    $stepExceedsSpan = self::stepExceedsExactFloatSpan(
                        \abs($step),
                        \min($startF, $endF),
                        \max($startF, $endF),
                        $differenceAllowance
                    ) && (self::stepExceedsDecimalSpan($start, $end, \abs($step), $ascending)
                        ?? self::stepExceedsExactFloatSpan(
                            \abs($step),
                            \min($startF, $endF),
                            \max($startF, $endF),
                            [
                                ...self::roundingAllowances($start, $end, \abs($step), false),
                                self::roundingAllowance(\abs($step), false),
                            ]
                        ));
                }
                $magnitudeDescription = "{$magnitude}";
                $spanDescription = "({$spanF})";
                if ("{$magnitudeF}" === "{$spanF}") {
                    // Printed to 14 digits, the step and the span can look equal. Print as many as tell them
                    // apart, or name the exact span when the float span is the step.
                    $magnitudeDescription = \var_export($magnitudeF, true);
                    $spanDescription = $magnitudeF === $spanF
                        ? '(the exact span between ' . \var_export($start, true) . ' and ' . \var_export($end, true)
                            . ', which rounds to the step in float)'
                        : '(' . \var_export($spanF, true) . ')';
                }
            }

            if ($stepExceedsSpan) {
                throw new \InvalidArgumentException(
                    "Single::range: step magnitude ({$magnitudeDescription}) must not exceed span {$spanDescription}"
                );
            }

            // A whole step on int operands is counted and applied in the int domain, exactly. Converted to
            // float, a span past 2^53 rounds, and the count with it: range(0.0, PHP_INT_MAX, 2**62) would take
            // the span 2^63 - 1 as 2^63, two steps, and end on 2^63.
            $wholeStep = \is_int($magnitude) || \floor($magnitude) === $magnitude;
            if (\is_int($startExact) && \is_int($endExact) && $wholeStep) {
                // The first value is taken as the float path takes it, $startF ± 0.0, which keeps a -0.0 start
                // that toExactInt() made 0 when descending.
                $first = $startF + ($ascending ? 0.0 : -0.0);
                yield from self::rangeOfWholeStepOnInts($startExact, $endExact, $magnitude, $ascending, $first);
                return;
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
            // The range lands on $end when the exact span is a whole number of steps, give or take the rounding
            // of decimal operands stored as floats: (0.5 - 0.4) / 0.05 evaluates to 1.999..., which would
            // otherwise drop the last element. The count and that decision are taken from the exact span and
            // step (see countFloatSteps()) while the products involved can't overflow or underflow and the
            // quotient is far enough below 2^53 to tell its whole steps apart.
            if (
                $stepsQuotient < 2.0 ** 48.0
                && $magnitudeF >= 2.0 ** -900.0
                && \max(\abs($startF), \abs($endF), $magnitudeF) < 2.0 ** 995.0
            ) {
                if (\is_int($startExact) && \is_int($endExact)) {
                    [$low, $high] = $ascending ? [$startExact, $endExact] : [$endExact, $startExact];
                    $exactSpan = self::exactIntSpan($low, $high);
                    $operandAllowance = [];
                } else {
                    $exactSpan = self::exactFloatDifference(\max($startF, $endF), \min($startF, $endF));
                    $operandAllowance = self::roundingAllowances($start, $end, \abs($step), true, $exactSpan[1]);
                }
                [$stepsF, $landsOnEnd] = self::countFloatSteps(
                    $exactSpan,
                    \abs($step),
                    $stepsQuotient,
                    $operandAllowance,
                    self::roundingAllowance(\abs($step), true),
                    $endF,
                    \is_int($endExact) ? self::intToFloats($endExact)[1] : 0.0,
                    $ascending
                );
            } else {
                // Past those limits, the quotient is taken as integral when it is within its rounding error of
                // an integer: the error of the division, the step and the span (about half an epsilon each,
                // relative), plus the error from storing decimal operands as floats (at most half the float
                // spacing each). The operand term only counts while it is under a quarter of a step, as in
                // roundingAllowances(); int operands are exact, since their span is.
                $operandTolerance = $intOperands
                    ? 0.0
                    : (Arithmetic::halfFloatSpacing($startF) + Arithmetic::halfFloatSpacing($endF)) / $magnitudeF;
                if ($operandTolerance >= 0.25) {
                    $operandTolerance = 0.0;
                }
                $quotientTolerance = $operandTolerance + 2.0 * \PHP_FLOAT_EPSILON * $stepsQuotient;
                $stepsF = (int)\round($stepsQuotient);
                $landsOnEnd = \abs($stepsQuotient - (float)$stepsF) <= $quotientTolerance;
                if (!$landsOnEnd) {
                    $stepsF = (int)\floor($stepsQuotient);
                }
            }
            $signF = $ascending ? 1.0 : -1.0;
            // An integral quotient ends on $end exactly, so its last value is yielded after the loop.
            // Reconstructing it can miss either way: 50 * 1.1 = 55.000000000000007, and
            // range(2^53 + 1, 2^53 + 3, 0.5) starts from 2^53.
            $lastIndex = $landsOnEnd ? $stepsF - 1 : $stepsF;
            // The end is yielded as the float values $start ± i·|$step| reach it: a -0.0 end as 0.0, since
            // x + (-x) is 0.0 (and the exact int path yields (float) 0).
            $lastF = $endF + 0.0;

            // An int $start that a float can't hold (past 2^53) is off by up to half the float spacing there
            // once converted, and every value computed from it would carry that error: range(2^53 + 3, 0,
            // -(2^52 - 0.5)) would end on 5.0, not 4.0. Its values are computed from the exact $start instead.
            // A $start that a float holds keeps the computation below, so its values are unchanged.
            $exactStart = \is_int($startExact) && \is_int($endExact) && Arithmetic::toExactInt($startF) !== $startExact;

            // A value can pass $end through rounding: of $startF (int operands past 2^53), of the offset or of
            // the addition. It passes by less than a float spacing, so it lands on $end. The common case has a
            // loop of its own, which keeps the cost per value down.
            if (!$exactStart) {
                for ($i = 0; $i <= $lastIndex; ++$i) {
                    $valF = $startF + $signF * ((float)$i * $magnitudeF);
                    if ($ascending ? $valF > $endF : $valF < $endF) {
                        $valF = $lastF;
                    }
                    yield $valF;
                }
            } else {
                for ($i = 0; $i <= $lastIndex; ++$i) {
                    // (float) $i is exact up to 2^53, which iteration never reaches.
                    $valF = $i > 0 && $i <= 2 ** 53
                        ? self::roundIntPlusMultiple($startExact, $i, $magnitudeF, $ascending)
                        : $startF + $signF * ((float)$i * $magnitudeF);
                    if ($ascending ? $valF > $endF : $valF < $endF) {
                        $valF = $lastF;
                    }
                    yield $valF;
                }
            }

            if ($landsOnEnd) {
                yield $lastF;
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
     * The float range of int operands by a whole step, which {@see Single::range()} has checked against the
     * span.
     *
     * The element count is taken from the exact span, and each value is the exact int $start ± i·$magnitude,
     * converted to float once, so it is correctly rounded. A step of at least 2^63 is a float, and fits only a
     * span beyond the int range, which is below 2^64, so it fits once.
     *
     * @param int       $start
     * @param int       $end
     * @param int|float $magnitude whole and positive
     * @param bool      $ascending
     * @param float     $first     $start as a float, which is -0.0 for a -0.0 start descending
     *
     * @return \Generator<int, float>
     *
     * @throws \InvalidArgumentException when the range has more than PHP_INT_MAX elements
     */
    private static function rangeOfWholeStepOnInts(
        int $start,
        int $end,
        int|float $magnitude,
        bool $ascending,
        float $first
    ): \Generator {
        [$low, $high] = $ascending ? [$start, $end] : [$end, $start];

        $magnitudeI = Arithmetic::toExactInt($magnitude);
        if ($magnitudeI === null) {
            // The span is at least 2^63, so $low < 0 <= $high, and $low + 2^63, $high - 2^63 and the step's
            // excess over 2^63 are exact ints.
            $magnitudeExcess = (int)((float)$magnitude + (float)\PHP_INT_MIN);
            yield $first;
            yield (float)($ascending
                ? ($low - \PHP_INT_MIN) + $magnitudeExcess
                : ($high + \PHP_INT_MIN) - $magnitudeExcess);
            return;
        }

        // The span fits in an int unless $low is negative and $high - $low passes PHP_INT_MAX.
        $spanFitsInt = $low >= 0 || $high <= \PHP_INT_MAX + $low;
        if ($spanFitsInt) {
            $steps = \intdiv($high - $low, $magnitudeI);
        } elseif ($magnitudeI === 1) {
            $steps = null;
        } else {
            // The span is 2^63 + $excess, which overflowed. With 2^63 = $magnitudeI·$quotient + $remainder, 0
            // < $remainder <= $magnitudeI, the remainders of 2^63 and of $excess add up to one more step when
            // their sum reaches $magnitudeI. At most PHP_INT_MAX steps fit, as $magnitudeI >= 2.
            $excess = $high - ($low - \PHP_INT_MIN);
            $quotient = \intdiv(\PHP_INT_MAX, $magnitudeI);
            $remainder = \PHP_INT_MAX % $magnitudeI + 1;
            $steps = $quotient + \intdiv($excess, $magnitudeI)
                + ($excess % $magnitudeI >= $magnitudeI - $remainder ? 1 : 0);
        }

        if ($steps === null || $steps >= \PHP_INT_MAX) {
            $spanDescription = $spanFitsInt ? 'span=' . ($high - $low) : "span between {$start} and {$end}";
            throw new \InvalidArgumentException(
                'Single::range: span / step exceeds representable iteration count '
                . "({$spanDescription}, step={$magnitudeI})"
            );
        }

        // The values stay between $start and $end, so the int arithmetic doesn't overflow.
        $value = $start;
        yield $first;
        if ($ascending) {
            for ($i = 0; $i < $steps; ++$i) {
                $value += $magnitudeI;
                yield (float)$value;
            }
        } else {
            for ($i = 0; $i < $steps; ++$i) {
                $value -= $magnitudeI;
                yield (float)$value;
            }
        }
    }

    /**
     * $start ± $i·$magnitude, correctly rounded to float, for an int $start past 2^53 that a float can't hold
     * and a fractional $magnitude, as {@see Single::range()} yields it.
     *
     * $i·$magnitude is split exactly (TwoProduct, then TwoSum) into an int and a fraction in (-1, 1), so the
     * value is an exact int $base plus a fraction. Past 2^53 the fraction only decides a tie, as a sticky bit
     * does. Within 2^53, $base converts to float exactly and the fraction is added with one rounding; its low
     * part is rounded to odd first, so it can't make or break a tie.
     *
     * The value lies between $start and $end, and so does $base, which is within 1 of it.
     *
     * @param int   $start
     * @param int   $i         positive and at most 2^53, so (float) $i is exact
     * @param float $magnitude fractional and positive, so below 2^52
     * @param bool  $ascending
     *
     * @return float
     */
    private static function roundIntPlusMultiple(int $start, int $i, float $magnitude, bool $ascending): float
    {
        // $product + $error is exactly $i·$magnitude.
        [$product, $error] = self::twoProduct((float)$i, $magnitude);

        // Integer parts, truncated toward zero, and the fractions left over, all exact. $product is positive.
        $productInt = \floor($product);
        $errorInt = $error < 0.0 ? \ceil($error) : \floor($error);
        $productFrac = $product - $productInt;
        $errorFrac = $error - $errorInt;

        // TwoSum (Knuth): $fracHi + $fracLo is exactly $productFrac + $errorFrac, in (-1, 2). Its integer part
        // moves to $small, which leaves |$fracHi + $fracLo| < 1, with the sign of $fracHi unless it is 0.
        $fracHi = $productFrac + $errorFrac;
        $errorPart = $fracHi - $productFrac;
        $fracLo = ($productFrac - ($fracHi - $errorPart)) + ($errorFrac - $errorPart);
        $carry = $fracHi < 0.0 ? \ceil($fracHi) : \floor($fracHi);
        $fracHi -= $carry;
        $small = (int)$errorInt + (int)$carry;

        // $base = $start ± ($productInt + $small), added in an order that stays within the int range. Past
        // 2^63, $i·$magnitude spans 0, from a $start on the other side of it, so $start ± 2^63 is an int.
        $sign = $ascending ? 1 : -1;
        $from = $start;
        $productIntRange = $productInt;
        if ($productInt >= -(float)\PHP_INT_MIN) {
            $from = $ascending ? $start - \PHP_INT_MIN : $start + \PHP_INT_MIN;
            $productIntRange = $productInt + (float)\PHP_INT_MIN;
        }
        $whole = (int)$productIntRange;
        $base = $small >= 0
            ? ($from + $sign * $small) + $sign * $whole
            : $from + $sign * ($whole + $small);

        $hi = $ascending ? $fracHi : -$fracHi;
        $lo = $ascending ? $fracLo : -$fracLo;
        if ($hi === 0.0 && $lo === 0.0) {
            return (float)$base;
        }

        if ($base > 2 ** 53 || $base < -(2 ** 53)) {
            // The float spacing is at least 2 there, so only the side of $base the fraction is on matters. Within
            // 2^62, 2·$base ± 1 rounds as $base ± the fraction does; past it, the spacing is at least 512, and so
            // does $base with its lowest bit set ($base ± 1 when it is even), which can't be a tie.
            $towards = ($hi !== 0.0 ? $hi : $lo) > 0.0 ? 1 : -1;
            if ($base > -(2 ** 62) && $base < 2 ** 62) {
                return (float)(2 * $base + $towards) * 0.5;
            }
            return (float)($towards > 0 ? $base | 1 : ($base - 1) | 1);
        }

        // TwoSum: $sum + $sumError is exactly $base + $hi, where $base is exact as a float.
        $baseF = (float)$base;
        $sum = $baseF + $hi;
        $hiPart = $sum - $baseF;
        $sumError = ($baseF - ($sum - $hiPart)) + ($hi - $hiPart);

        // TwoSum: $rest + $restError is exactly $sumError + $lo. Rounded to odd, $rest keeps the sign of
        // $restError as a sticky bit far below the spacing at $sum, so $sum + $rest rounds as the exact value.
        $rest = $sumError + $lo;
        $loPart = $rest - $sumError;
        $restError = ($sumError - ($rest - $loPart)) + ($lo - $loPart);
        if ($restError !== 0.0) {
            $spacing = 2.0 * Arithmetic::halfFloatSpacing($rest);
            if ($spacing > 0.0 && \fmod($rest / $spacing, 2.0) === 0.0) {
                // Toward 0 from a power of two, the next float down is half a spacing away.
                $towardZero = ($rest > 0.0) !== ($restError > 0.0);
                $nudge = $towardZero && \abs($rest) === $spacing * 4503599627370496.0 ? $spacing / 2.0 : $spacing;
                $rest += $restError > 0.0 ? $nudge : -$nudge;
            }
        }

        return $sum + $rest;
    }

    /**
     * Whether a step at least the float span $high - $low exceeds their exact span by more than the operands'
     * rounding.
     *
     * The float span rounds the exact one, so it can round up to the step: in range(0.1, 2.0 ** 53, 2.0 ** 53),
     * the exact span 2^53 - 0.1 rounds to 2^53. A deficit within $allowance isn't taken as one: the exact span of
     * the floats is 6.4 - 4.4e-16 in range(-10.0, -3.6, 6.4), and 0.2 - 2.8e-17 in range(0.1, 0.3, 0.2), but the
     * decimals span the step.
     *
     * @param int|float   $magnitude the step magnitude, at least $high - $low in float
     * @param float       $low
     * @param float       $high
     * @param list<float> $allowance the rounding of the operands and the step (see roundingAllowances())
     *
     * @return bool
     */
    private static function stepExceedsExactFloatSpan(int|float $magnitude, float $low, float $high, array $allowance): bool
    {
        // An int step past 2^53 is the sum of its float and the rest. The float span is at most the step, so it
        // is finite.
        $terms = [
            ...(\is_int($magnitude) ? self::intToFloats($magnitude) : [$magnitude]),
            ...self::negated(self::exactFloatDifference($high, $low)),
            ...self::negated($allowance),
        ];

        return self::expansionSign(self::expansion($terms)) > 0;
    }

    /**
     * Whether a step exceeds the span of $start and $end taken as the decimals they are written as, or null where
     * that can't be decided exactly here.
     *
     * A non-integral float is taken as its shortest decimal, while roundingAllowances() gives the operands an
     * allowance, and the step always is; an int or an integral float stands for itself, as in roundingAllowance().
     * Bounding each decimal's distance from its float, as the fallback does, also accepts a step that the decimals
     * reject: in range(0.1, 0.2, 0.10000000000000002), each float is within half a spacing of a decimal that
     * makes the step fit, but 0.10000000000000002 > 0.2 - 0.1. So the decimals are compared exactly, each as a sum
     * of integers times powers of ten, all scaled by the largest power of ten in their denominators. That is null
     * when a scaled value would overflow or an operand taken as exact is below 2^-900, where the products aren't
     * exact.
     *
     * @param int|float $start
     * @param int|float $end
     * @param int|float $magnitude the step magnitude
     * @param bool      $ascending whether $end is above $start
     *
     * @return bool|null
     */
    private static function stepExceedsDecimalSpan(
        int|float $start,
        int|float $end,
        int|float $magnitude,
        bool $ascending
    ): ?bool {
        $operandsAsDecimals = self::roundingAllowances($start, $end, $magnitude, false) !== [];
        // The step minus the span: + step - high + low, each a list of [integer value, power of ten divided by].
        $terms = [];
        $operands = [
            [$magnitude, 1.0, true],
            [$ascending ? $end : $start, -1.0, $operandsAsDecimals],
            [$ascending ? $start : $end, 1.0, $operandsAsDecimals],
        ];
        foreach ($operands as [$operand, $sign, $asDecimal]) {
            if (\is_float($operand) && $asDecimal && \floor($operand) !== $operand) {
                // Split into parts of at most 9 digits, which floats hold exactly, also where ints have 32 bits.
                [$significand, $exponent, $digits] = self::shortestDecimal($operand);
                $integer = \str_replace('.', '', $significand);
                $scale = $digits - $exponent;
                $sign = $operand < 0.0 ? -$sign : $sign;
                $length = \strlen($integer);
                if ($length > 9) {
                    $terms[] = [$sign * (float)\substr($integer, 0, $length - 9), $scale - 9];
                    $integer = \substr($integer, $length - 9);
                }
                $terms[] = [$sign * (float)$integer, $scale];
            } elseif (\is_int($operand)) {
                foreach (self::intToFloats($operand) as $part) {
                    $terms[] = [$sign * $part, 0];
                }
            } else {
                if ($operand !== 0.0 && \abs($operand) < 2.0 ** -900.0) {
                    return null;
                }
                $terms[] = [$sign * $operand, 0];
            }
        }

        $maxScale = \max(0, ...\array_column($terms, 1));
        $scaled = [];
        foreach ($terms as [$value, $scale]) {
            $parts = [$value];
            // Multiply by 10^($maxScale - $scale), in factors of at most 10^22, which floats hold exactly.
            for ($power = $maxScale - $scale; $power > 0; $power -= 22) {
                $factor = (float)('1e' . \min($power, 22));
                $products = [];
                foreach ($parts as $part) {
                    [$product, $error] = self::twoProduct($part, $factor);
                    if (!(\abs($product) < 2.0 ** 990.0) || !\is_finite($error)) {
                        return null;
                    }
                    $products[] = $product;
                    $products[] = $error;
                }
                $parts = self::expansion($products);
            }
            \array_push($scaled, ...$parts);
        }

        return self::expansionSign(self::expansion($scaled)) > 0;
    }

    /**
     * The number of steps of $magnitude in $span, and whether the range lands on $end, for {@see Single::range()}.
     *
     * The range lands on $end when the exact span is a whole number of steps, give or take the operands'
     * rounding: the sum of $operandAllowance, and $stepAllowance for each step taken, since a decimal step that
     * rounds to $magnitude is off by up to that much each time. range(0.0, 1.0, 0.1) lands on 1.0 although 10
     * steps of the float 0.1 exceed the span by 5.6e-17, as that is within 10 · 6.9e-18. Otherwise the steps are
     * those that fit exactly: in range(2.0 ** 53, -0.1, -2.0 ** 53), the span 2^53 + 0.1 rounds to the step in
     * float, but is 0.1 past it, beyond the 6.9e-18 that -0.1 may carry, so the range ends on 0.0. It still ends
     * on $end when the exact last value rounds to it, as computing that value in float may not.
     *
     * $quotient is within 1/8 of the exact quotient, so the nearest whole number of steps is round($quotient),
     * and the exact remainder decides between it and one fewer. Only it can be within the allowance, which is
     * under 3/8 of a step: the operands' share is under a quarter, and fewer than 2^48 steps add under 2^-5.
     *
     * @param list<float> $span             the exact span, as a sum of floats
     * @param int|float   $magnitude        at most the span plus $operandAllowance and $stepAllowance
     * @param float       $quotient         the float quotient of the span and the step, below 2^48
     * @param list<float> $operandAllowance see roundingAllowances()
     * @param float       $stepAllowance    see roundingAllowance()
     * @param float       $end              $end as a float
     * @param float       $endRest          $end minus that float, exactly
     * @param bool        $ascending
     *
     * @return array{int, bool}
     */
    private static function countFloatSteps(
        array $span,
        int|float $magnitude,
        float $quotient,
        array $operandAllowance,
        float $stepAllowance,
        float $end,
        float $endRest,
        bool $ascending
    ): array {
        // $remainder is exactly $span - $steps·$magnitude (TwoProduct of each part of the step).
        $steps = (int)\round($quotient);
        $terms = $span;
        foreach (\is_int($magnitude) ? self::intToFloats($magnitude) : [$magnitude] as $part) {
            [$product, $error] = self::twoProduct((float)$steps, $part);
            $terms[] = -$product;
            $terms[] = -$error;
        }
        $remainder = self::expansion($terms);
        $remainderSign = self::expansionSign($remainder);

        // |$remainder| - the allowance
        $terms = $remainderSign < 0 ? self::negated($remainder) : $remainder;
        $allowance = [...$operandAllowance, (float)$steps * $stepAllowance];
        foreach ($allowance as $term) {
            $terms[] = -$term;
        }
        if ($steps > 0 && self::expansionSign(self::expansion($terms)) <= 0) {
            return [$steps, true];
        }
        if ($remainderSign < 0) {
            return [$steps - 1, false];
        }

        // The last value, $end ∓ $remainder, as an offset from the float $end.
        $offset = self::expansion([$endRest, ...($ascending ? self::negated($remainder) : $remainder)]);

        return [$steps, self::roundsTo($end, $offset)];
    }

    /**
     * Whether $float plus the sum of $offset, an expansion(), rounds to $float (to nearest, ties to even).
     *
     * @param float       $float
     * @param list<float> $offset
     *
     * @return bool
     */
    private static function roundsTo(float $float, array $offset): bool
    {
        $offsetSign = self::expansionSign($offset);
        if ($offsetSign === 0) {
            return true;
        }
        $halfSpacing = Arithmetic::halfFloatSpacing($float);
        // Toward 0 from a power of two, the next float is half a spacing closer.
        $towardZero = ($offsetSign > 0) === ($float < 0.0);
        $halfGap = $towardZero && \abs($float) === $halfSpacing * 2.0 ** 53.0 ? $halfSpacing / 2.0 : $halfSpacing;

        $terms = $offsetSign > 0 ? $offset : self::negated($offset);
        $terms[] = -$halfGap;
        $excess = self::expansionSign(self::expansion($terms));

        return $excess < 0
            || ($excess === 0 && $halfSpacing > 0.0 && \fmod(\abs($float) / (2.0 * $halfSpacing), 2.0) === 0.0);
    }

    /**
     * The rounding an operand of {@see Single::range()} may carry from the decimal it is written as, or 0 where it
     * is taken as exact.
     *
     * An int is exact. For deciding whether the range lands on $end ($forLanding), a non-integral float may carry
     * half its float spacing, and an integral float is exact while it is its own shortest decimal, as 2^53 + 2,
     * 1e16 and 1e22 are, or within 2^53: 1e23 is the float 99999999999999991611392, and is written for 10^23. For
     * deciding whether a step exceeds the span where stepExceedsDecimalSpan() can't, an integral float stands for
     * itself, as range() takes it next to an int operand, and a non-integral float is taken as its shortest decimal:
     * it may carry at most half a unit in that decimal's last digit, which bounds its distance to the float more
     * tightly than half a spacing when the decimal is long, as for 30.664096287827093.
     *
     * @param int|float $operand
     * @param bool      $forLanding
     *
     * @return float
     */
    private static function roundingAllowance(int|float $operand, bool $forLanding): float
    {
        if (\is_int($operand)) {
            return 0.0;
        }
        $halfSpacing = Arithmetic::halfFloatSpacing($operand);
        if (\floor($operand) === $operand) {
            $exact = !$forLanding || \abs($operand) <= 2.0 ** 53.0 || self::isShortestDecimal($operand);
            return $exact ? 0.0 : $halfSpacing;
        }
        if ($forLanding) {
            return $halfSpacing;
        }

        [, $exponent, $digits] = self::shortestDecimal($operand);
        return \min($halfSpacing, (float)('5e' . ($exponent - $digits - 1)));
    }

    /**
     * The roundingAllowance() of $start and $end, or none when they add up to a quarter of $magnitude or more.
     *
     * Past that, the step is near or below the float spacing and the decimal intent can't be recovered, so the
     * operands are taken as exact: range(1e15 + 0.125, 1e15 + 1.125, 0.3) would otherwise take the span of 1 for 3
     * steps of 0.3 and end on $end. The cut-off is taken on the allowances for landing either way, the larger
     * ones, so a step that range() accepts above the span always lands on $end.
     *
     * For landing, they come with the rounding of the operands' float difference, $spanError, while the total stays
     * under a quarter of a step: a step computed from the operands, such as ($end - $start) / $n, carries it. It
     * counts up to twice the operands' allowance, which bounds it when the larger operand isn't exact; past that,
     * the difference has absorbed part of an operand that is exact: in range(2.0 ** 53, -0.1, -2.0 ** 53), the
     * span 2^53 + 0.1 rounds to the step. range(-104.81949906807756, 975.2888809333513, 1.158914570816984), with
     * the step computed for 932 steps, ends on $end, although the floats' span is 1.1e-13 short of 932 steps.
     *
     * @param int|float $start
     * @param int|float $end
     * @param int|float $magnitude
     * @param bool      $forLanding
     * @param float     $spanError  the float difference of the operands minus their exact difference
     *
     * @return list<float>
     */
    private static function roundingAllowances(
        int|float $start,
        int|float $end,
        int|float $magnitude,
        bool $forLanding,
        float $spanError = 0.0
    ): array {
        $startAllowance = self::roundingAllowance($start, true);
        $endAllowance = self::roundingAllowance($end, true);
        $sum = $startAllowance + $endAllowance;
        if ($sum >= 0.25 * (float)$magnitude) {
            return [];
        }
        if (!$forLanding) {
            return [self::roundingAllowance($start, false), self::roundingAllowance($end, false)];
        }

        $differenceAllowance = \min(\abs($spanError), 2.0 * $sum);
        return $sum + $differenceAllowance < 0.25 * (float)$magnitude
            ? [$startAllowance, $endAllowance, $differenceAllowance]
            : [$startAllowance, $endAllowance];
    }

    /**
     * Whether an integral float past 2^53 is its own shortest decimal: the shortest one that reads back as it.
     *
     * The shortest decimal is a few digits D times 10^k. It is exact when 10^k divides the float and D is the
     * quotient, which needs k <= 22, as 5^23 doesn't divide a 53-bit significand.
     *
     * @param float $x integral, past 2^53
     *
     * @return bool
     */
    private static function isShortestDecimal(float $x): bool
    {
        [$significand, $exponent, $digits] = self::shortestDecimal($x);
        $power = $exponent - $digits;
        if ($power < 0 || $power > 22) {
            return false;
        }
        $scale = (float)('1e' . $power);
        $magnitude = \abs($x);

        return \fmod($magnitude, $scale) === 0.0
            && Arithmetic::compare((int)\str_replace('.', '', $significand), $magnitude / $scale) === 0;
    }

    /**
     * The shortest decimal that reads back as |$x|, as its significand d.ddd, its exponent and the number of digits
     * after the point.
     *
     * @param float $x finite and nonzero
     *
     * @return array{string, int, int}
     */
    private static function shortestDecimal(float $x): array
    {
        $magnitude = \abs($x);
        $digits = 0;
        $decimal = \sprintf('%.0e', $magnitude);
        while ((float)$decimal !== $magnitude) {
            ++$digits;
            $decimal = \sprintf('%.' . $digits . 'e', $magnitude);
        }
        [$significand, $exponent] = \explode('e', $decimal);

        return [$significand, (int)$exponent, $digits];
    }

    /**
     * $high - $low, exactly, as the float difference and its rounding error (TwoSum).
     *
     * @param float $high
     * @param float $low
     *
     * @return list<float>
     */
    private static function exactFloatDifference(float $high, float $low): array
    {
        $difference = $high - $low;
        $highPart = $difference + $low;
        $error = ($high - $highPart) + (-$low - ($difference - $highPart));

        return [$difference, $error];
    }

    /**
     * $high - $low, exactly, as a sum of floats. Past the int range, it is 2^63 plus an excess that is an int.
     *
     * @param int $low
     * @param int $high at least $low
     *
     * @return list<float>
     */
    private static function exactIntSpan(int $low, int $high): array
    {
        // The span fits in an int unless $low is negative and $high - $low passes PHP_INT_MAX.
        if ($low >= 0 || $high <= \PHP_INT_MAX + $low) {
            return self::intToFloats($high - $low);
        }

        return [-(float)\PHP_INT_MIN, ...self::intToFloats($high - ($low - \PHP_INT_MIN))];
    }

    /**
     * $n, exactly, as its float and the rest.
     *
     * @param int $n
     *
     * @return list<float>
     */
    private static function intToFloats(int $n): array
    {
        $float = (float)$n;
        // (float) PHP_INT_MAX rounds up to 2^63, which isn't an int.
        $rest = $float >= -(float)\PHP_INT_MIN ? ($n - \PHP_INT_MAX) - 1 : $n - (int)$float;

        return [$float, (float)$rest];
    }

    /**
     * @param list<float> $terms
     *
     * @return list<float>
     */
    private static function negated(array $terms): array
    {
        $negated = [];
        foreach ($terms as $term) {
            $negated[] = -$term;
        }

        return $negated;
    }

    /**
     * $a·$b, exactly, as the float product and its rounding error (Dekker's TwoProduct, with Veltkamp's split).
     *
     * Exact unless the product overflows or underflows, or a factor is past 2^996, where the split overflows.
     *
     * @param float $a
     * @param float $b
     *
     * @return array{float, float}
     */
    private static function twoProduct(float $a, float $b): array
    {
        $product = $a * $b;
        $split = 134217729.0 * $a;
        $aHi = $split - ($split - $a);
        $aLo = $a - $aHi;
        $split = 134217729.0 * $b;
        $bHi = $split - ($split - $b);
        $bLo = $b - $bHi;
        $error = (($aHi * $bHi - $product) + $aHi * $bLo + $aLo * $bHi) + $aLo * $bLo;

        return [$product, $error];
    }

    /**
     * The exact sum of $terms, which must not overflow, as a nonoverlapping expansion: floats in order of
     * magnitude, each below the lowest bit of the next (Shewchuk's Grow-Expansion with TwoSum, zeros dropped).
     *
     * @param list<float> $terms
     *
     * @return list<float>
     */
    private static function expansion(array $terms): array
    {
        /** @var list<float> $expansion */
        $expansion = [];
        foreach ($terms as $sum) {
            if ($sum === 0.0) {
                continue;
            }
            $grown = [];
            foreach ($expansion as $component) {
                $partial = $sum + $component;
                $componentPart = $partial - $sum;
                $error = ($sum - ($partial - $componentPart)) + ($component - $componentPart);
                if ($error !== 0.0) {
                    $grown[] = $error;
                }
                $sum = $partial;
            }
            $grown[] = $sum;
            $expansion = $grown;
        }

        return $expansion;
    }

    /**
     * The sign of the sum of an expansion(): that of its largest nonzero component.
     *
     * @param list<float> $expansion
     *
     * @return int -1, 0 or 1
     */
    private static function expansionSign(array $expansion): int
    {
        for ($i = \count($expansion) - 1; $i >= 0; --$i) {
            if ($expansion[$i] !== 0.0) {
                return $expansion[$i] > 0.0 ? 1 : -1;
            }
        }

        return 0;
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

<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\Arithmetic;
use IterTools\Util\Identity;
use IterTools\Util\Iterators\RelatedIterator;
use IterTools\Util\Snapshot;
use IterTools\Util\Truthiness;
use IterTools\Util\UniqueExtractor;
use IterTools\Util\UsageMap;
use IterTools\Util\ValueCounter;

/**
 * Tools to get summarized answers about iterables.
 */
final class Summary
{
    /**
     * Returns true if all elements match the predicate function.
     *
     * Empty iterables return true.
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     *
     * @return bool
     */
    public static function allMatch(iterable $data, callable $predicate): bool
    {
        foreach ($data as $datum) {
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? !Truthiness::of($verdict) : !$verdict) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if all elements match a key-aware predicate.
     *
     * @param iterable<mixed> $data
     * @param callable(mixed, mixed): mixed $predicate
     */
    public static function allMatchWithKeys(iterable $data, callable $predicate): bool
    {
        foreach ($data as $key => $datum) {
            $verdict = $predicate($datum, $key);
            if (\is_float($verdict) ? !Truthiness::of($verdict) : !$verdict) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if any element matches the predicate function.
     *
     * Empty iterables return false.
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     *
     * @return bool
     */
    public static function anyMatch(iterable $data, callable $predicate): bool
    {
        foreach ($data as $datum) {
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if any element matches a key-aware predicate.
     *
     * @param iterable<mixed> $data
     * @param callable(mixed, mixed): mixed $predicate
     */
    public static function anyMatchWithKeys(iterable $data, callable $predicate): bool
    {
        foreach ($data as $key => $datum) {
            $verdict = $predicate($datum, $key);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if no element matches the predicate function.
     *
     * Empty iterables return true.
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     *
     * @return bool
     */
    public static function noneMatch(iterable $data, callable $predicate): bool
    {
        foreach ($data as $datum) {
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns true if no element matches a key-aware predicate.
     *
     * @param iterable<mixed> $data
     * @param callable(mixed, mixed): mixed $predicate
     */
    public static function noneMatchWithKeys(iterable $data, callable $predicate): bool
    {
        foreach ($data as $key => $datum) {
            $verdict = $predicate($datum, $key);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return true if all elements in given collection are unique.
     *
     * Empty iterables return true. Equality follows the strict or coercive rules in README
     * "Strict and Coercive Types". Retains the objects, closures, generators, and resources
     * it has compared by instance for as long as iteration continues.
     *
     * @param iterable<mixed> $data
     * @param bool $strict
     *
     * @return bool
     */
    public static function allUnique(iterable $data, bool $strict = true): bool
    {
        // See ValueCounter: it pins the identity anchors a repeated value's hash depends on.
        $counter = new ValueCounter($strict);

        foreach ($data as $datum) {
            if ($counter->add($datum) > 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true when all values have the same UniqueExtractor identity, per README
     * "Strict and Coercive Types". Retains the first value's identity-bearing objects,
     * closures, generators, and resources for as long as iteration continues.
     *
     * Every value is hashed, including one that is the very same instance as its predecessor:
     * in coercive mode equivalence is decided by current serialized state, so a shared object
     * the caller mutates between iterations is not equal to itself as first seen. See
     * ValueCounter::add() and Identity for why the first value's anchors stay held throughout.
     *
     * @param iterable<mixed> $data
     */
    public static function allEqual(iterable $data, bool $strict = true): bool
    {
        // See ValueCounter: it pins the identity anchors a repeated value's hash depends on.
        $counter = new ValueCounter($strict);

        foreach ($data as $datum) {
            $counter->add($datum);
            if ($counter->size() > 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true when all projected keys have the same UniqueExtractor identity, per README
     * "Strict and Coercive Types". Retains the first projection's identity-bearing objects,
     * closures, generators, and resources for as long as iteration continues; see
     * {@see Summary::allEqual()} for the mechanics, which this delegates to. $keyFunc is
     * projected lazily and is not called past the first mismatch.
     *
     * @param iterable<mixed> $data
     * @param callable(mixed): mixed $keyFunc
     */
    public static function allEqualBy(iterable $data, callable $keyFunc, bool $strict = true): bool
    {
        return self::allEqual(Single::map($data, $keyFunc), $strict);
    }

    /**
     * Returns true if given collection is sorted in ascending order; otherwise false.
     *
     * Items of given collection must be comparable. Each item is compared as it was when read; see README
     * "Strict and Coercive Types" > Values compared after later reads.
     *
     * Returns true if given collection is empty or has only one element.
     *
     * @param iterable<mixed> $data
     *
     * @return bool
     */
    public static function isSorted(iterable $data): bool
    {
        $hasPrevious = false;
        $lhs = null;

        foreach ($data as $rhs) {
            if ($hasPrevious) {
                if ((\is_float($lhs) && \is_nan($lhs)) || (\is_float($rhs) && \is_nan($rhs))) {
                    return false;
                }
                // Compare exactly, since an int beyond ±2^53 and a float can compare equal as floats when
                // they are not. Rounding to float is monotonic, so only an int and a float that <=> finds
                // equal need it.
                $order = $rhs <=> $lhs;
                if ($order === 0 && \is_int($rhs) !== \is_int($lhs)) {
                    $order = Arithmetic::compareValues($rhs, $lhs);
                }
                if ($order < 0) {
                    return false;
                }
            }
            // The previous element is compared as it was when read; see Snapshot.
            $lhs = \is_array($rhs) ? Snapshot::of($rhs) : $rhs;
            $hasPrevious = true;
        }

        return true;
    }

    /**
     * Returns true if given collection is sorted in reverse descending order; otherwise false.
     *
     * Items of given collection must be comparable. Each item is compared as it was when read; see README
     * "Strict and Coercive Types" > Values compared after later reads.
     *
     * Returns true if given collection is empty or has only one element.
     *
     * @param iterable<mixed> $data
     *
     * @return bool
     */
    public static function isReversed(iterable $data): bool
    {
        $hasPrevious = false;
        $lhs = null;

        foreach ($data as $rhs) {
            if ($hasPrevious) {
                if ((\is_float($lhs) && \is_nan($lhs)) || (\is_float($rhs) && \is_nan($rhs))) {
                    return false;
                }
                // Compare exactly, as in isSorted(). $rhs > $lhs, written as PHP evaluates it: $lhs < $rhs.
                $order = $lhs <=> $rhs;
                if ($order === 0 && \is_int($lhs) !== \is_int($rhs)) {
                    $order = Arithmetic::compareValues($lhs, $rhs);
                }
                if ($order < 0) {
                    return false;
                }
            }
            // The previous element is compared as it was when read; see Snapshot.
            $lhs = \is_array($rhs) ? Snapshot::of($rhs) : $rhs;
            $hasPrevious = true;
        }

        return true;
    }

    /**
     * Returns true if the values projected by the key function are sorted in
     * non-decreasing (ascending) order; otherwise false.
     *
     * Projected values must be comparable.
     *
     * Returns true if given collection is empty or has only one element.
     *
     * @param iterable<mixed>       $data
     * @param callable(mixed): mixed $keyFunc
     *
     * @return bool
     */
    public static function isSortedBy(iterable $data, callable $keyFunc): bool
    {
        return self::isSorted(Single::map($data, $keyFunc));
    }

    /**
     * Returns true if the values projected by the key function are sorted in
     * non-increasing (reverse descending) order; otherwise false.
     *
     * Projected values must be comparable.
     *
     * Returns true if given collection is empty or has only one element.
     *
     * @param iterable<mixed>       $data
     * @param callable(mixed): mixed $keyFunc
     *
     * @return bool
     */
    public static function isReversedBy(iterable $data, callable $keyFunc): bool
    {
        return self::isReversed(Single::map($data, $keyFunc));
    }

    /**
     * Returns true if all given collections are the same.
     *
     * For single iterable or empty iterables list returns true without reading the iterable. So does one
     * iterator or generator passed more than once, directly or through a Stream or other \IteratorAggregate
     * that returns it: same($it, $it) returns true without reading $it.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     */
    public static function same(iterable ...$iterables): bool
    {
        if (\count($iterables) < 2) {
            return true;
        }

        $iterator = self::comparisonIterator(...$iterables);

        // MultipleIterator attaches an Iterator once however many times it is passed, and an iterator is the
        // same as itself, so with one attached iterator there is nothing to compare.
        if ($iterator->countIterators() < 2) {
            return true;
        }

        foreach ($iterator as $values) {
            foreach (Single::pairwise($values) as [$lhs, $rhs]) {
                if ($lhs !== $rhs) {
                    return false;
                }
            }
        }

        // Lockstep iteration ends when any input ends; all inputs must have ended.
        $iterator->setFlags(\MultipleIterator::MIT_NEED_ANY);
        return !$iterator->valid();
    }

    /**
     * Returns true if all given collections have the same lengths.
     *
     * For single iterable or empty iterables list returns true.
     *
     * An array or \Countable is counted with count(), without being traversed. One iterator or generator
     * passed more than once, directly or through a Stream or other \IteratorAggregate that returns it, is
     * read as independent copies of itself: it is traversed once and that count is used for every position.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     */
    public static function sameCount(iterable ...$iterables): bool
    {
        if (\count($iterables) <= 1) {
            return true;
        }

        // Counts of the iterators traversed so far. The storage holds each iterator, so its identity cannot
        // pass to a later, unrelated iterator.
        /** @var \SplObjectStorage<\Iterator<mixed>, int> $traversedCounts */
        $traversedCounts = new \SplObjectStorage();

        $counts = [];
        foreach ($iterables as $iterable) {
            if (\is_countable($iterable)) {
                $counts[] = \count($iterable);
                continue;
            }

            $iterator = Transform::toIterator($iterable);
            if (!isset($traversedCounts[$iterator])) {
                // Traversed with foreach rather than iterator_count(), which skips current(): an
                // \SplFileObject reads its lines in current() and would never reach EOF. Not
                // Reduce::toCount(), which would take count() of an unwrapped Countable that misreports
                // its size.
                $traversedCounts[$iterator] = Reduce::toValue($iterator, static fn (int $carry): int => $carry + 1, 0);
            }
            $counts[] = $traversedCounts[$iterator];
        }

        return \count(\array_unique($counts)) === 1;
    }

    /**
     * Returns true if exactly n items in the iterable are true where the predicate function is true.
     *
     * Default predicate if not provided is the boolean value of each data item.
     *
     * @param iterable<mixed> $data
     * @param int             $n
     * @param callable|null   $predicate
     *
     * @return bool
     */
    public static function exactlyN(iterable $data, int $n, ?callable $predicate = null): bool
    {
        if ($n < 0) {
            return false;
        }

        $predicate ??= static fn (mixed $datum): mixed => $datum;

        $count = 0;
        foreach ($data as $datum) {
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
                $count++;
                if ($count > $n) {
                    return false;
                }
            }
        }

        return $count === $n;
    }

    /**
     * Returns true if at least n items in the iterable are true where the predicate function is true.
     *
     * Default predicate if not provided is the boolean value of each data item.
     *
     * Short-circuits as soon as the count reaches $n.
     *
     * Edge cases:
     *  - $n <= 0 → always true (no work done).
     *
     * @param iterable<mixed> $data
     * @param int             $n
     * @param callable|null   $predicate
     *
     * @return bool
     */
    public static function atLeastN(iterable $data, int $n, ?callable $predicate = null): bool
    {
        if ($n <= 0) {
            return true;
        }

        $predicate ??= static fn (mixed $datum): mixed => $datum;

        $count = 0;
        foreach ($data as $datum) {
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
                $count++;
                if ($count >= $n) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns true if at most n items in the iterable are true where the predicate function is true.
     *
     * Default predicate if not provided is the boolean value of each data item.
     *
     * Short-circuits as soon as the count exceeds $n.
     *
     * Edge cases:
     *  - $n < 0 → always false (mirrors exactlyN(-1) === false).
     *  - $n === 0 → true iff zero matches.
     *
     * @param iterable<mixed> $data
     * @param int             $n
     * @param callable|null   $predicate
     *
     * @return bool
     */
    public static function atMostN(iterable $data, int $n, ?callable $predicate = null): bool
    {
        if ($n < 0) {
            return false;
        }

        $predicate ??= static fn (mixed $datum): mixed => $datum;

        $count = 0;
        foreach ($data as $datum) {
            $verdict = $predicate($datum);
            if (\is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict) {
                $count++;
                if ($count > $n) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Returns true if given collections are permutations of each other (using strict-type comparisons).
     *
     * Returns true if no collections given or for single collection.
     *
     * Equality follows the strict rules in README "Strict and Coercive Types".
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     *
     * @see https://en.cppreference.com/w/cpp/algorithm/is_permutation
     */
    public static function arePermutations(iterable ...$iterables): bool
    {
        return self::arePermutationsInternal(true, ...\array_values($iterables));
    }

    /**
     * Returns true if given collections are permutations of each other (using type coercion).
     *
     * Returns true if no collections given or for single collection.
     *
     * Equality follows the coercive rules in README "Strict and Coercive Types".
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     *
     * @see https://en.cppreference.com/w/cpp/algorithm/is_permutation
     */
    public static function arePermutationsCoercive(iterable ...$iterables): bool
    {
        return self::arePermutationsInternal(false, ...\array_values($iterables));
    }

    /**
     * Internal function helper for arePermutations() and arePermutationsCoercive()
     *
     * @see Summary::arePermutations()
     * @see Summary::arePermutationsCoercive()
     *
     * @param bool $strict
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     */
    private static function arePermutationsInternal(bool $strict, iterable ...$iterables): bool
    {
        if (\count($iterables) < 2) {
            return true;
        }

        $usageMap = new UsageMap($strict);

        $iterator = self::comparisonIterator(...$iterables);
        foreach ($iterator as $values) {
            foreach ($values as $collectionIndex => $value) {
                $usageMap->addUsage($value, \strval($collectionIndex));
            }
        }

        $iterator->setFlags(\MultipleIterator::MIT_NEED_ANY);
        if ($iterator->valid()) {
            return false;
        }

        // MultipleIterator attaches an Iterator once however many times it is passed, and an iterator is a
        // permutation of itself, so a value is owned by every attached iterator: fewer than the inputs when
        // one repeats.
        $owners = $iterator->countIterators();
        foreach ($usageMap->getHashes() as $hash) {
            if (!$usageMap->hasSameOwnerCount($hash, $owners)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Builds a lockstep iterator without turning unequal lengths into exceptions.
     * The caller checks MIT_NEED_ANY after iteration to detect remaining input.
     * Exceptions raised by the inputs propagate unchanged.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \MultipleIterator<mixed, mixed>
     */
    private static function comparisonIterator(iterable ...$iterables): \MultipleIterator
    {
        $iterator = new \MultipleIterator(\MultipleIterator::MIT_NEED_ALL);
        foreach ($iterables as $iterable) {
            $iterator->attachIterator(Transform::toIterator($iterable));
        }

        return $iterator;
    }

    /**
     * Returns true if the iterable starts with the given prefix (using strict-type comparison).
     *
     * Compares values pairwise; keys are ignored.
     *
     * Empty prefix → true without consuming the source. So is an iterator, generator or \IteratorAggregate
     * passed as its own prefix: startsWith($it, $it).
     *
     * Lazy: the source is never advanced beyond the last element compared. On a match it is
     * left on the last prefix element; on a mismatch, on the mismatching element; if it is
     * shorter than the prefix, exhausted. A side-effecting source (file handle, HTTP
     * pagination, DB cursor) is not over-read.
     *
     * Strict-type comparison uses ===; see README "Strict and Coercive Types".
     *
     * @param iterable<mixed> $data
     * @param iterable<mixed> $prefix must be finite
     *
     * @return bool
     */
    public static function startsWith(iterable $data, iterable $prefix): bool
    {
        return self::startsWithInternal($data, $prefix, true);
    }

    /**
     * Returns true if the iterable starts with the given prefix (using type coercion).
     *
     * Compares values pairwise; keys are ignored.
     *
     * Empty prefix → true without consuming the source. So is an iterator, generator or \IteratorAggregate
     * passed as its own prefix: startsWith($it, $it).
     *
     * Lazy: the source is never advanced beyond the last element compared. On a match it is
     * left on the last prefix element; on a mismatch, on the mismatching element; if it is
     * shorter than the prefix, exhausted. A side-effecting source (file handle, HTTP
     * pagination, DB cursor) is not over-read.
     *
     * Equality follows the coercive rules in README "Strict and Coercive Types".
     *
     * @param iterable<mixed> $data
     * @param iterable<mixed> $prefix must be finite
     *
     * @return bool
     *
     * @throws \InvalidArgumentException if a non-serializable object is reached during comparison
     */
    public static function startsWithCoercive(iterable $data, iterable $prefix): bool
    {
        return self::startsWithInternal($data, $prefix, false);
    }

    /**
     * Returns true if the iterable ends with the given suffix (using strict-type comparison).
     *
     * Compares values pairwise; keys are ignored.
     *
     * Empty suffix → true without consuming the source. So is an iterator, generator or \IteratorAggregate
     * passed as its own suffix: endsWith($it, $it).
     *
     * Both source and suffix must be finite. Buffers up to count($suffix) elements via a sliding window.
     * Each element is compared as it was when read; see README "Strict and Coercive Types" > Values
     * compared after later reads.
     *
     * Strict-type comparison uses ===; see README "Strict and Coercive Types".
     *
     * @param iterable<mixed> $data   must be finite
     * @param iterable<mixed> $suffix must be finite
     *
     * @return bool
     */
    public static function endsWith(iterable $data, iterable $suffix): bool
    {
        return self::endsWithInternal($data, $suffix, true);
    }

    /**
     * Returns true if the iterable ends with the given suffix (using type coercion).
     *
     * Compares values pairwise; keys are ignored.
     *
     * Empty suffix → true without consuming the source. So is an iterator, generator or \IteratorAggregate
     * passed as its own suffix: endsWith($it, $it).
     *
     * Both source and suffix must be finite. Buffers up to count($suffix) elements via a sliding window.
     * Each array or object element is hashed when read, so it is compared as it was then; see README
     * "Strict and Coercive Types" > Values compared after later reads.
     *
     * Equality follows the coercive rules in README "Strict and Coercive Types".
     *
     * @param iterable<mixed> $data   must be finite
     * @param iterable<mixed> $suffix must be finite
     *
     * @return bool
     *
     * @throws \InvalidArgumentException if a non-serializable object is reached during comparison
     */
    public static function endsWithCoercive(iterable $data, iterable $suffix): bool
    {
        return self::endsWithInternal($data, $suffix, false);
    }

    /**
     * Internal helper for startsWith() and startsWithCoercive().
     *
     * @param iterable<mixed> $data
     * @param iterable<mixed> $prefix
     * @param bool            $strict
     *
     * @return bool
     */
    private static function startsWithInternal(iterable $data, iterable $prefix, bool $strict): bool
    {
        // An iterator or aggregate starts with itself, and reading the prefix could advance the source with it:
        // an aggregate may return one shared iterator, such as a Stream over a generator.
        if (\is_object($data) && $data === $prefix) {
            return true;
        }

        // Two objects can also resolve to one iterator, as two Streams over one generator do. Seen through
        // Streams alone, that iterator is recognized before anything is read. Otherwise the source is resolved
        // only once the prefix has an element, so an empty prefix leaves it untouched, and a source behind
        // another aggregate is recognized only then, after the prefix has been rewound and read.
        //
        // The prefix is read with valid()/current()/next(), not foreach, because foreach rewinds. Its one
        // rewind is in rewindOrIsSource(), which tells a rewind refused because the prefix is the source's own
        // iterator, already read. A second rewind could also throw (an empty generator, once run).
        $shareable = \is_object($data) && \is_object($prefix);
        if ($shareable && self::isOneStreamedIterator($data, $prefix)) {
            return true;
        }
        $prefixIt = Transform::toIterator($prefix);
        if (self::rewindOrIsSource($prefixIt, $shareable ? $data : null)) {
            return true;
        }

        $dataIt = null;
        for (; $prefixIt->valid(); $prefixIt->next()) {
            $expected = $prefixIt->current();
            if ($dataIt === null) {
                $dataIt = Transform::toIterator($data);
                if ($shareable && $dataIt === $prefixIt) {
                    return true;
                }
                if (!($dataIt instanceof \Generator)) {
                    $dataIt->rewind();
                }
            } else {
                // Advance only when another prefix element needs comparing, so the source is
                // never pulled past the last element compared.
                $dataIt->next();
            }
            if (!$dataIt->valid()) {
                return false;
            }
            $actual = $dataIt->current();
            if (!self::valuesEqual($actual, $expected, $strict)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Internal helper for endsWith() and endsWithCoercive().
     *
     * @param iterable<mixed> $data
     * @param iterable<mixed> $suffix
     * @param bool            $strict
     *
     * @return bool
     */
    private static function endsWithInternal(iterable $data, iterable $suffix, bool $strict): bool
    {
        // An iterator or aggregate ends with itself, and reading the suffix could exhaust the source first:
        // an aggregate may return one shared iterator, such as a Stream over a generator.
        if (\is_object($data) && $data === $suffix) {
            return true;
        }

        // Two objects can also resolve to one iterator, as two Streams over one generator do. Seen through
        // Streams alone, that iterator is recognized before anything is read. Otherwise the source is resolved
        // only once the suffix has an element, so an empty suffix leaves it untouched, and a source behind
        // another aggregate is recognized only then, after the suffix has been rewound and read.
        //
        // The suffix is read with valid()/current()/next(), not foreach, because foreach rewinds. Its one
        // rewind is in rewindOrIsSource(), which tells a rewind refused because the suffix is the source's own
        // iterator, already read. A second rewind could also throw (an empty generator, once run).
        $shareable = \is_object($data) && \is_object($suffix);
        if ($shareable && self::isOneStreamedIterator($data, $suffix)) {
            return true;
        }
        $suffixIt = Transform::toIterator($suffix);
        if (self::rewindOrIsSource($suffixIt, $shareable ? $data : null)) {
            return true;
        }

        // The suffix and the window are kept while later elements are read, so each element is captured
        // as it is when read. Strict: an array as a Snapshot, so a row filled through a shared reference
        // keeps the values it held. Coercive: an array or object as its key, since an object is compared
        // by serialized state, which a source yielding one mutated object changes on every read; any other
        // value cannot change and is hashed when compared. An element that cannot be hashed is kept as the
        // exception, thrown only if comparison reaches it, as before.
        $suffixArr = [];
        for (; $suffixIt->valid(); $suffixIt->next()) {
            $value = $suffixIt->current();
            if ($strict) {
                $suffixArr[] = \is_array($value) ? Snapshot::of($value) : $value;
            } else {
                // A suffix key is kept as its Identity, which pins the objects and resources it was
                // derived from. A source key needs no anchors: it was computed while its element was
                // alive, so it can only match a suffix identity if it is that same, still pinned, value.
                try {
                    $suffixArr[] = UniqueExtractor::identify($value, false);
                } catch (\InvalidArgumentException $e) {
                    $suffixArr[] = $e;
                }
            }
            // Resolved after the first element is captured, which resolving could change, and before the
            // suffix advances, which would advance a shared source with it.
            if ($shareable) {
                $shareable = false;
                $data      = Transform::toIterator($data);
                if ($data === $suffixIt) {
                    return true;
                }
            }
        }
        $suffixLen = \count($suffixArr);

        if ($suffixLen === 0) {
            return true;
        }

        // The window is a ring buffer of the last $suffixLen elements: element $count goes in slot
        // $count % $suffixLen, so evicting the oldest is an overwrite rather than a reindex.
        $window = [];
        $count = 0;
        if ($strict) {
            foreach ($data as $value) {
                $window[$count++ % $suffixLen] = \is_array($value) ? Snapshot::of($value) : $value;
            }
        } else {
            foreach ($data as $value) {
                $entry = $value;
                if (\is_array($value) || \is_object($value)) {
                    // Wrapped in an array, which no other window entry is, to tell the key from a string
                    // element.
                    try {
                        $entry = [UniqueExtractor::getString($value, false)];
                    } catch (\InvalidArgumentException $e) {
                        $entry = $e;
                    }
                }
                $window[$count++ % $suffixLen] = $entry;
            }
        }

        if ($count < $suffixLen) {
            return false;
        }

        // Once full, the oldest element is in the slot the next one would have been written to.
        for ($i = 0, $slot = $count % $suffixLen; $i < $suffixLen; $i++, $slot = ($slot + 1) % $suffixLen) {
            if ($strict) {
                if ($window[$slot] !== $suffixArr[$i]) {
                    return false;
                }
                continue;
            }
            // Thrown in the order the elements would have been hashed: the source's, then the suffix's.
            if ($window[$slot] instanceof \InvalidArgumentException) {
                throw $window[$slot];
            }
            if ($suffixArr[$i] instanceof \InvalidArgumentException) {
                throw $suffixArr[$i];
            }
            $actual = \is_array($window[$slot])
                ? $window[$slot][0]
                : UniqueExtractor::getString($window[$slot], false);
            /** @var Identity $expected */
            $expected = $suffixArr[$i];
            if ($actual !== $expected->key) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tells whether two objects are, or are Streams (possibly nested) over, one and the same iterator.
     *
     * Nothing is read, rewound or resolved: only Streams are looked through, by their source, and no
     * getIterator() is called. Any other aggregate, an array-backed Stream or a Traversable that is not an
     * Iterator is unknown, and false.
     *
     * @param object $a
     * @param object $b
     *
     * @return bool
     */
    private static function isOneStreamedIterator(object $a, object $b): bool
    {
        $iterator = self::streamedIterator($a);

        return $iterator !== null && $iterator === self::streamedIterator($b);
    }

    /**
     * The iterator an object is, or that the Streams it is (possibly nested) are over, or null if unknown.
     *
     * A Stream's source is only ever the iterable it was created from, or a generator a step made over the
     * previous one, so following sources always ends, at a non-Stream.
     *
     * @param object $object
     *
     * @return \Iterator<mixed>|null
     */
    private static function streamedIterator(object $object): ?\Iterator
    {
        /** @var \ReflectionProperty|null $source */
        static $source = null;
        $source ??= new \ReflectionProperty(Stream::class, 'iterable');

        while ($object instanceof Stream) {
            $iterable = $source->getValue($object);
            if (!\is_object($iterable)) {
                return null;
            }
            $object = $iterable;
        }

        return $object instanceof \Iterator ? $object : null;
    }

    /**
     * Rewinds a prefix or suffix, and tells whether it is the source's own iterator, which the source starts
     * and ends with.
     *
     * Only if the rewind is refused, as it is for a generator or tee copy already read, is the source resolved
     * to tell, so a prefix or suffix that rewinds leaves it untouched. One that is not the source's own keeps
     * the refusal, also when resolving the source fails. Any other exception propagates.
     *
     * @param \Iterator<mixed>     $iterator prefix or suffix
     * @param iterable<mixed>|null $data     source, or null when it cannot share the iterator
     *
     * @return bool
     *
     * @throws \Throwable the rewind's, or the generator body's, if the iterator is not the source's own
     */
    private static function rewindOrIsSource(\Iterator $iterator, ?iterable $data): bool
    {
        // A generator is started first, outside the catch below: an unstarted one runs its body to its first
        // yield here, as rewind() would, so whatever the body throws propagates. Once started, rewind() runs
        // no code of the generator's, only PHP's check that it has not moved past its first yield.
        if ($iterator instanceof \Generator) {
            $iterator->valid();
        }

        try {
            $iterator->rewind();
        } catch (\Exception $e) {
            // A refused rewind can mean the prefix or suffix is the source's own iterator, already read: a user
            // aggregate over a shared generator advanced or exhausted before the call, or over an advanced tee
            // copy, which Streams cannot be looked through to see. Only that refusal, by PHP or by a tee copy,
            // whose rewind() is a position check, is taken as a sign, and only if the source resolves to the
            // same iterator.
            if ($data === null || !self::isRewindRefusal($iterator, $e)) {
                throw $e;
            }
            try {
                $isSource = Transform::toIterator($data) === $iterator;
            } catch (\Throwable) {
                throw $e;
            }
            if ($isSource) {
                return true;
            }
            throw $e;
        }

        return false;
    }

    /**
     * Tells whether an exception from rewinding an iterator is its refusal to rewind after it has advanced.
     *
     * @param \Iterator<mixed> $iterator
     * @param \Exception       $e        thrown by $iterator->rewind()
     *
     * @return bool
     */
    private static function isRewindRefusal(\Iterator $iterator, \Exception $e): bool
    {
        if ($iterator instanceof \Generator) {
            return \get_class($e) === \Exception::class
                && $e->getMessage() === 'Cannot rewind a generator that was already run';
        }

        return $iterator instanceof RelatedIterator
            && \get_class($e) === \LogicException::class
            && $e->getMessage() === 'RelatedIterator cannot be rewinded after it has advanced';
    }

    /**
     * Pairwise equality used by startsWith and startsWithCoercive.
     *
     * Equality follows the strict or coercive rules in README "Strict and Coercive Types".
     *
     * @param mixed $a
     * @param mixed $b
     * @param bool  $strict
     *
     * @return bool
     */
    private static function valuesEqual(mixed $a, mixed $b, bool $strict): bool
    {
        if ($strict) {
            return $a === $b;
        }
        return UniqueExtractor::getString($a, false) === UniqueExtractor::getString($b, false);
    }

    /**
     * Returns true if the given iterable contains the needle (using strict-type comparison).
     *
     * Equality follows the strict rules in README "Strict and Coercive Types" (NaN never
     * matches NaN here, since this comparison uses === directly, not UniqueExtractor).
     *
     * Short-circuits on first match.
     *
     * @param iterable<mixed> $data
     * @param mixed           $needle
     *
     * @return bool
     */
    public static function contains(iterable $data, mixed $needle): bool
    {
        foreach ($data as $datum) {
            if ($datum === $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if the given iterable contains the needle (using type coercion).
     *
     * Equality follows the coercive rules in README "Strict and Coercive Types".
     *
     * Short-circuits on first match: a non-serializable datum is only reached if no
     * earlier datum has matched, so a match before such a datum returns true without throwing.
     *
     * @param iterable<mixed> $data
     * @param mixed           $needle
     *
     * @return bool
     *
     * @throws \InvalidArgumentException if the needle is not serializable, or if a non-serializable datum is reached before any match
     */
    public static function containsCoercive(iterable $data, mixed $needle): bool
    {
        // The Identity, not just its key, is held for the whole loop: it pins the needle's nested
        // closures, generators and resources so a later datum cannot inherit their ids.
        $needleIdentity = UniqueExtractor::identify($needle, false);

        foreach ($data as $datum) {
            if (UniqueExtractor::getString($datum, false) === $needleIdentity->key) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if all elements of given collection that satisfy the predicate
     * appear before all elements that don't.
     *
     * Returns true for empty collection or for collection with single item.
     *
     * Default predicate if not provided is the boolean value of each data item.
     *
     * @see https://en.cppreference.com/w/cpp/algorithm/is_partitioned
     *
     * @param iterable<mixed> $data
     * @param callable|null $predicate
     *
     * @return bool
     */
    public static function isPartitioned(iterable $data, ?callable $predicate = null): bool
    {
        $predicate ??= static fn (mixed $item): mixed => $item;

        $allTrueSoFar = true;

        foreach ($data as $datum) {
            $verdict = $predicate($datum);
            $currentItemTrue = \is_float($verdict) ? Truthiness::of($verdict) : (bool) $verdict;

            if ($allTrueSoFar && !$currentItemTrue) {
                $allTrueSoFar = false;
                continue;
            }

            if (!$allTrueSoFar && $currentItemTrue) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if given iterable is empty.
     *
     * For rewindable iterators, reports whether the underlying collection is empty
     * regardless of cursor position. For generators (which cannot be rewound),
     * reports whether there are remaining elements from the current position.
     *
     * @param iterable<mixed> $data
     *
     * @return bool
     */
    public static function isEmpty(iterable $data): bool
    {
        $iterator = Transform::toIterator($data);

        if (!($iterator instanceof \Generator)) {
            $iterator->rewind();
        }

        return !$iterator->valid();
    }
}

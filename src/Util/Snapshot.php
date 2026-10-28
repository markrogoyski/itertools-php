<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 * A copy of a value that later writes through the references it shares cannot change.
 *
 * PHP keeps an array's reference slots when the array is copied, so a retained copy of a row filled
 * through a shared reference (the PDOStatement::bindColumn() pattern) follows the next row. A function
 * that keeps an element while it reads the next one, and compares it later, keeps a snapshot instead.
 * It takes the snapshot before it yields anything, since a consumer can write through the slot too.
 *
 * Only arrays are copied. Any other value is returned as is: an object stays the same instance whatever
 * its state, which is what strict comparison and `<=>` on the same instance see.
 */
final class Snapshot
{
    /**
     * Returns an array rebuilt without its reference slots, at every depth; any other value as is.
     *
     * Only the arrays that hold a reference slot, and the arrays above them, are rebuilt. The others are
     * kept as they are, so subarrays that share PHP's storage stay shared: an array without reference
     * slots is returned as is, though it is still read in full to find that out. A rebuilt subarray is
     * reused for every later subarray identical to it (`===`, with the same signs of zero), so one that
     * occurs many times, sharing PHP's storage, is rebuilt once.
     *
     * Arrays nested deeper than {@see UniqueExtractor::MAX_DEPTH} are kept as they are, which also ends
     * the recursion for a self-referential array. A loop that snapshots every element writes
     * `\is_array($x) ? Snapshot::of($x) : $x`, which skips the call for all but arrays.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function of(mixed $value): mixed
    {
        if (!\is_array($value)) {
            return $value;
        }

        $copies = [];
        $truncated = false;
        $hasZero = false;

        return self::withoutReferences($value, 1, $copies, $truncated, $hasZero) ?? $value;
    }

    /**
     * @param array<mixed> $array
     * @param int $depth number of arrays enclosing $array's elements, 1 at the top value
     * @param array<int, list<array{array<mixed>, array<mixed>, bool}>> $copies subarrays rebuilt so far,
     *        as [original, copy, whether it holds a float zero] by count
     * @param bool $truncated set to true if the depth limit was reached below $array
     * @param bool $hasZero set to true if $array holds a float zero at any depth
     *
     * @return array<mixed>|null the rebuilt array, or null if $array holds no reference slot at any depth
     */
    private static function withoutReferences(
        array $array,
        int $depth,
        array &$copies,
        bool &$truncated,
        bool &$hasZero
    ): ?array {
        if ($depth > UniqueExtractor::MAX_DEPTH) {
            $truncated = true;
            return null;
        }

        /** @var array<array-key, array<mixed>> $rebuilt */
        $rebuilt = [];
        $hasReference = false;
        foreach ($array as $key => $element) {
            if (\is_array($element)) {
                $nested = self::nestedWithoutReferences($element, $depth + 1, $copies, $truncated, $hasZero);
                if ($nested !== null) {
                    $rebuilt[$key] = $nested;
                    $hasReference = true;
                    continue;
                }
            } elseif ($element === 0.0) {
                $hasZero = true;
            }
            if (!$hasReference && \ReflectionReference::fromArrayElement($array, $key) !== null) {
                $hasReference = true;
            }
        }

        if (!$hasReference) {
            return null;
        }

        // A new array, not a modified copy of $array: assigning to a copy's reference slot would write
        // through it. Iterating by value reads through a reference slot, so $element is the value it
        // holds now.
        $copy = [];
        foreach ($array as $key => $element) {
            $copy[$key] = \array_key_exists($key, $rebuilt) ? $rebuilt[$key] : $element;
        }

        return $copy;
    }

    /**
     * {@see withoutReferences()} for a subarray, reusing the copy of an identical one rebuilt earlier.
     *
     * @param array<mixed> $array
     * @param int $depth
     * @param array<int, list<array{array<mixed>, array<mixed>, bool}>> $copies
     * @param bool $truncated
     * @param bool $hasZero
     *
     * @return array<mixed>|null
     */
    private static function nestedWithoutReferences(
        array $array,
        int $depth,
        array &$copies,
        bool &$truncated,
        bool &$hasZero
    ): ?array {
        $count = \count($array);
        foreach ($copies[$count] ?? [] as $entry) {
            // `===` returns at once for the same storage, which is how a shared subarray recurs. An equal
            // one in other storage matches too, and its copy holds the same values, except that
            // -0.0 === 0.0, so the signs of any zeros are checked. The original goes first: it is
            // acyclic, so the comparison ends even if $array refers to itself through a reference.
            if ($entry[0] === $array && (!$entry[2] || self::sameZeroSigns($entry[0], $array))) {
                $hasZero = $hasZero || $entry[2];
                return $entry[1];
            }
        }

        $subtreeTruncated = false;
        $subtreeHasZero = false;
        $copy = self::withoutReferences($array, $depth, $copies, $subtreeTruncated, $subtreeHasZero);
        $hasZero = $hasZero || $subtreeHasZero;
        if ($subtreeTruncated) {
            // A self-referential array ends here; keeping it out of $copies keeps every original acyclic.
            $truncated = true;
        } elseif ($copy !== null) {
            $copies[$count][] = [$array, $copy, $subtreeHasZero];
        }

        return $copy;
    }

    /**
     * Whether each float zero in $original has the same sign in $equal, an array `===` to it.
     *
     * @param array<mixed> $original acyclic
     * @param array<mixed> $equal
     */
    private static function sameZeroSigns(array $original, array $equal): bool
    {
        foreach ($original as $key => $element) {
            if (\is_array($element)) {
                /** @var array<mixed> $nested an array, since $equal === $original */
                $nested = $equal[$key];
                if (!self::sameZeroSigns($element, $nested)) {
                    return false;
                }
            } elseif ($element === 0.0) {
                /** @var float $other a zero, since $equal === $original */
                $other = $equal[$key];
                if (\fdiv(1, $element) !== \fdiv(1, $other)) {
                    return false;
                }
            }
        }

        return true;
    }
}

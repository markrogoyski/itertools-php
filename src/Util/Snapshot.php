<?php

declare(strict_types=1);

namespace IterTools\Util;

use IterTools\Util\Values\UniqueExtractor;

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
     * kept in a {@see SnapshotCache} for the call and reused for every later subarray identical to it
     * (`===`, with the same signs of zero) where its copy fits under the depth limit, wherever its
     * siblings differ from it, so one that occurs many times, sharing PHP's storage, is rebuilt once.
     *
     * Arrays nested deeper than {@see UniqueExtractor::MAX_DEPTH} are kept as they are, which also ends
     * the recursion for a self-referential array. What a walk cut short that way gave for an array held
     * by a reference slot is reused where the reference recurs at the same depth, so an array that refers
     * to itself through several slots is walked once per depth. A loop that snapshots every element writes
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

        $cache = null;
        $height = 0;
        $zeroKeys = [];
        $kids = [];

        return self::withoutReferences($value, 1, $cache, $height, $zeroKeys, $kids) ?? $value;
    }

    /**
     * @param array<mixed> $array
     * @param int $depth number of arrays enclosing $array's elements, 1 at the top value
     * @param int $height set to the number of levels of arrays nested in $array, as far as the walk went:
     *        $depth + $height is more than the depth limit if the walk was cut short by it
     * @param list<array-key> $zeroKeys set to the keys whose element is a float zero or an array holding one
     * @param array<array-key, int> $kids set to the entry ids of the subarray elements kept, by key
     *
     * @return array<mixed>|null the rebuilt array, or null if $array holds no reference slot at any depth
     */
    private static function withoutReferences(
        array $array,
        int $depth,
        ?SnapshotCache &$cache,
        int &$height,
        array &$zeroKeys,
        array &$kids
    ): ?array {
        $height = 0;
        if ($depth > UniqueExtractor::MAX_DEPTH) {
            return null;
        }

        /** @var array<array-key, array<mixed>> $rebuilt */
        $rebuilt = [];
        $hasReference = false;
        foreach ($array as $key => $element) {
            if (\is_array($element)) {
                $nestedHeight = 0;
                $id = null;
                $hasZero = false;
                $nested = self::nestedWithoutReferences(
                    $array,
                    $key,
                    $element,
                    $depth + 1,
                    $cache,
                    $nestedHeight,
                    $id,
                    $hasZero
                );
                if ($nestedHeight >= $height) {
                    $height = $nestedHeight + 1;
                }
                if ($id !== null) {
                    $kids[$key] = $id;
                }
                if ($hasZero) {
                    $zeroKeys[] = $key;
                }
                if ($nested !== null) {
                    $rebuilt[$key] = $nested;
                    $hasReference = true;
                    continue;
                }
            } elseif ($element === 0.0) {
                $zeroKeys[] = $key;
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
     * {@see withoutReferences()} for the subarray $array at $key in $parent, through the cache.
     *
     * @param array<mixed> $parent
     * @param array<mixed> $array
     * @param int|null $id set to the id of $array's class in the cache, or null if it is not kept
     * @param bool $hasZero set to whether $array holds a float zero at any depth
     *
     * @return array<mixed>|null
     */
    private static function nestedWithoutReferences(
        array $parent,
        int|string $key,
        array $array,
        int $depth,
        ?SnapshotCache &$cache,
        int &$height,
        ?int &$id,
        bool &$hasZero
    ): ?array {
        $reference = null;
        $referenceRead = false;
        if ($cache !== null && $cache->hasTruncations()) {
            $reference = \ReflectionReference::fromArrayElement($parent, $key)?->getId();
            $referenceRead = true;
            $truncation = $reference === null ? null : $cache->truncation($depth, $reference);
            if ($truncation !== null) {
                // Cut short, so no array above is kept either, and its zeros and class are not needed.
                [$copy, $height] = $truncation;
                return $copy;
            }
        }

        $fingerprint = null;
        $id = $cache?->find($array, $depth, $fingerprint);
        if ($cache !== null && $id !== null) {
            $entry = $cache->entry($id);
            $height = $entry->height;
            $hasZero = $entry->zeroKeys !== [];
            return $entry->copy;
        }

        $zeroKeys = [];
        $kids = [];
        $copy = self::withoutReferences($array, $depth, $cache, $height, $zeroKeys, $kids);
        $hasZero = $zeroKeys !== [];
        if ($depth + $height > UniqueExtractor::MAX_DEPTH) {
            // Cut short by the depth limit, which ends a self-referential array: not kept as a class, which
            // keeps every original acyclic, but kept by its reference, if it is held by one.
            if (!$referenceRead) {
                $reference = \ReflectionReference::fromArrayElement($parent, $key)?->getId();
            }
            if ($reference !== null) {
                ($cache ??= new SnapshotCache())->keepTruncation($depth, $reference, $copy, $height);
            }
            return $copy;
        }
        if ($copy === null) {
            // Not kept: a later array equal to it may hold reference slots, since `===` reads through them,
            // and could then not be kept as it is.
            return null;
        }

        // Created on the first subarray kept: a value without one, the common case, never needs it.
        $cache ??= new SnapshotCache();
        $id = $cache->add($array, $copy, $height, $zeroKeys, $kids, $fingerprint);

        return $cache->entry($id)->copy;
    }
}

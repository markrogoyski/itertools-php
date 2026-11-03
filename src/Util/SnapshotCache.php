<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 * The subarrays one {@see Snapshot::of()} has rebuilt, each class of identical ones (`===`, with the same
 * float bits, so the same signs of zero) kept once, and found again without a scan of the candidates.
 *
 * PHP exposes no identity for an array, so a subarray is found by what it holds. Arrays are grouped by
 * count and a constant-time fingerprint. Within a group, a discrimination tree tells the classes apart:
 * each inner node reads the value at a key path that two of its classes were seen to differ at, and
 * routes on it, and each leaf is one class. A lookup follows one route to a leaf and confirms with `===`,
 * which returns at once for the same storage, which is how a shared subarray recurs. A route that ends
 * before a leaf is a miss without any `===`. The tree never forgets a class, so a class is rebuilt once
 * whatever its siblings hold and wherever they differ from it.
 *
 * It also keeps what a walk cut short by the depth limit gave for an array held by a reference slot, by
 * the reference and the depth, see {@see truncation()}.
 */
final class SnapshotCache
{
    /**
     * Number of leading bytes of a string element that {@see fingerprint()} uses.
     */
    private const FINGERPRINT_STRING_BYTES = 32;

    /**
     * Least number of float zeros in an array, at least a quarter of its count, from which
     * {@see sameSigns()} reads their signs at once, through C, rather than one by one.
     */
    private const MIN_JOINED_ZEROS = 32;

    /**
     * @var list<SnapshotEntry>
     */
    private array $entries = [];

    /**
     * @var array<int, array<string, SnapshotNode|int>> root of each group, by count and fingerprint
     */
    private array $roots = [];

    /**
     * @var array<int, array<string, int>> id of the class last found or kept per group, or -1 once that
     *      guess was wrong for a class found in the tree
     */
    private array $recent = [];

    /**
     * @var array<int, array<string, array{array<mixed>|null, int}>> by depth and reference id, see
     *      {@see truncation()}
     */
    private array $truncations = [];

    /**
     * @var array<int, array{array<array-key, true>, string, list<array-key>}|null> by entry id, see
     *      {@see joinZeros()}
     */
    private array $joinedZeros = [];

    public function entry(int $id): SnapshotEntry
    {
        return $this->entries[$id];
    }

    /**
     * The id of the class $array belongs to, if one was kept whose copy fits under the depth limit at
     * $depth.
     *
     * @param array<mixed> $array
     * @param int $depth the depth $array is at, as {@see Snapshot} counts it
     * @param string|null $fingerprint set to $array's fingerprint if it was computed, for {@see add()}
     */
    public function find(array $array, int $depth, ?string &$fingerprint): ?int
    {
        $count = \count($array);
        if (!isset($this->roots[$count])) {
            return null;
        }

        $fingerprint = self::fingerprint($array);
        $node = $this->roots[$count][$fingerprint] ?? null;
        if ($node === null) {
            return null;
        }

        // The class last found or kept in the group is tried first: a nested doubling finds each level
        // there without walking the tree. Once it was wrong for a class the tree then found, the group
        // stops trying it, so a wasted comparison is charged to a rebuild, or happens once per group.
        $recent = $this->recent[$count][$fingerprint];
        if ($recent >= 0 && $this->matches($array, $recent, $depth)) {
            return $recent;
        }

        if ($node instanceof SnapshotNode) {
            $last = $node;
            $node = self::descend($node, $array, $last);
        }
        if ($node === null || !$this->matches($array, $node, $depth)) {
            return null;
        }
        if ($recent >= 0) {
            $this->recent[$count][$fingerprint] = -1;
        }

        return $node;
    }

    /**
     * Keeps a class for $array, which {@see find()} did not find, and returns its id; or the id of a kept
     * class with the same float bits that `===` rejected, as it holds NAN.
     *
     * @param array<mixed> $array acyclic
     * @param array<mixed> $copy
     * @param list<array-key> $zeroKeys
     * @param array<array-key, int> $kids
     * @param string|null $fingerprint as {@see find()} set it
     */
    public function add(
        array $array,
        array $copy,
        int $height,
        array $zeroKeys,
        array $kids,
        ?string $fingerprint
    ): int {
        $count = \count($array);
        $fingerprint ??= self::fingerprint($array);
        $id = $this->insert(new SnapshotEntry($array, $copy, $height, $zeroKeys, $kids), $count, $fingerprint);
        if (($this->recent[$count][$fingerprint] ?? 0) >= 0) {
            $this->recent[$count][$fingerprint] = $id;
        }

        return $id;
    }

    /**
     * Whether any walk cut short was kept, so {@see truncation()} is worth asking.
     */
    public function hasTruncations(): bool
    {
        return $this->truncations !== [];
    }

    /**
     * What the walk of the array held by reference $reference gave at $depth, if it was cut short by the
     * depth limit: [the rebuilt array or null, its height].
     *
     * A reference holds the same array throughout a call, so the walk at the same depth gives the same
     * result. An array that refers to itself through several reference slots, which is never kept as a
     * class, as the depth limit cuts its walk short, would otherwise be walked once per path down to the
     * limit: 2^256 times for two slots.
     *
     * @return array{array<mixed>|null, int}|null
     */
    public function truncation(int $depth, string $reference): ?array
    {
        return $this->truncations[$depth][$reference] ?? null;
    }

    /**
     * Keeps what the walk of the array held by reference $reference gave at $depth, cut short by the
     * depth limit.
     *
     * @param array<mixed>|null $copy
     */
    public function keepTruncation(int $depth, string $reference, ?array $copy, int $height): void
    {
        $this->truncations[$depth][$reference] = [$copy, $height];
    }

    /**
     * Whether $array is identical to the original of entry $id, with the same signs of zero, and the
     * entry's copy fits under the depth limit at $depth: reusing a copy where it does not would break
     * reference slots that a rebuild there keeps.
     *
     * @param array<mixed> $array
     */
    private function matches(array $array, int $id, int $depth): bool
    {
        $entry = $this->entries[$id];

        return $depth + $entry->height <= UniqueExtractor::MAX_DEPTH
            && self::identical($entry->original, $array)
            && ($entry->zeroKeys === [] || $this->sameSigns($array, $id));
    }

    /**
     * Whether $original, which is acyclic, is `===` to $array, which may not be.
     *
     * A comparison that meets an array it is already comparing on its left side throws, and PHP may swap
     * the operands of `===`, so a self-referential $array may make it throw. It is then not identical to
     * $original: the comparison would otherwise have ended within $original's depth.
     *
     * @param array<mixed> $original
     * @param array<mixed> $array
     */
    private static function identical(array $original, array $array): bool
    {
        try {
            return $original === $array;
        } catch (\Error) { // @phpstan-ignore catch.neverThrown
            return false;
        }
    }

    /**
     * {@see add()} for the group of $entry's original, by its count and fingerprint.
     */
    private function insert(SnapshotEntry $entry, int $count, string $fingerprint): int
    {
        $id = \count($this->entries);
        $array = $entry->original;

        $node = $this->roots[$count][$fingerprint] ?? null;
        if ($node === null) {
            $this->entries[] = $entry;
            $this->roots[$count][$fingerprint] = $id;
            return $id;
        }

        // Follow $array's answers to the node it falls out of, or to the leaf of a class it differs from.
        $parent = null;
        if ($node instanceof SnapshotNode) {
            $last = $node;
            $node = self::descend($node, $array, $last);
            if ($node === null) {
                $this->entries[] = $entry;
                self::attach($last, $array, $id);
                return $id;
            }
            $parent = $last;
        }

        $other = $this->entries[$node];
        $test = $this->difference($array, $entry->kids, $other->original, $other->kids);
        if ($test === null) {
            return $node;
        }

        $this->entries[] = $entry;
        [$path, $keys] = $test;
        $tail = null;
        if ($parent !== null && \array_slice($path, 0, \count($parent->path)) === $parent->path) {
            $tail = \array_slice($path, \count($parent->path));
        }
        $split = new SnapshotNode($path, $keys, $tail);
        self::attach($split, $other->original, $node);
        self::attach($split, $array, $id);
        if ($parent === null) {
            $this->roots[$count][$fingerprint] = $split;
        } else {
            self::attach($parent, $other->original, $split);
        }

        return $id;
    }

    /**
     * Follows $array's answers down from $node, and returns the leaf they lead to, or null if a node has
     * no branch for one.
     *
     * @param array<mixed> $array
     * @param SnapshotNode $last set to the last node passed: the one without a branch, if none
     */
    private static function descend(SnapshotNode $node, array $array, SnapshotNode &$last): ?int
    {
        $value = $array;
        $found = true;
        while (true) {
            if ($node->tail === null) {
                $value = $array;
                $found = true;
                $keys = $node->path;
            } else {
                $keys = $node->tail;
            }
            foreach ($keys as $key) {
                if (!$found || !\is_array($value) || !\array_key_exists($key, $value)) {
                    $found = false;
                    break;
                }
                /** @var mixed $value */
                $value = $value[$key];
            }

            // An int or string element, the common answer, without building the answer.
            if ($found && !$node->keys && \is_int($value)) {
                $child = $node->byOther['i' . $value] ?? null;
            } elseif ($found && !$node->keys && \is_string($value)) {
                $child = $node->byString[$value] ?? null;
            } else {
                $answer = self::answer($node, $value, $found);
                $child = \is_string($answer[0])
                    ? ($node->byString[$answer[0]] ?? null)
                    : ($node->byOther[$answer[1]] ?? null);
            }
            if (!$child instanceof SnapshotNode) {
                $last = $node;
                return $child;
            }
            $node = $child;
        }
    }

    /**
     * Makes $child the child of $node for $array's answer.
     *
     * @param array<mixed> $array
     */
    private static function attach(SnapshotNode $node, array $array, SnapshotNode|int $child): void
    {
        $value = $array;
        $found = true;
        foreach ($node->path as $key) {
            if (!\is_array($value) || !\array_key_exists($key, $value)) {
                $found = false;
                break;
            }
            /** @var mixed $value */
            $value = $value[$key];
        }

        $answer = self::answer($node, $value, $found);
        if (\is_string($answer[0])) {
            $node->byString[$answer[0]] = $child;
        } else {
            $node->byOther[$answer[1]] = $child;
        }
    }

    /**
     * The answer to $node's test of $value, found at its path or not: [the string found, ''] if it is a
     * string, otherwise [null, a description of it].
     *
     * @return array{string|null, string}
     */
    private static function answer(SnapshotNode $node, mixed $value, bool $found): array
    {
        if (!$found) {
            return [null, 'missing'];
        }
        if ($node->keys) {
            return [null, \is_array($value) ? self::describeKeys($value) : 'missing'];
        }
        if (\is_string($value)) {
            return [$value, ''];
        }

        return [null, self::describe($value)];
    }

    /**
     * A description of a value, equal for identical values with the same float bits, and different for
     * any others but arrays of the same count.
     */
    private static function describe(mixed $value): string
    {
        if (\is_int($value)) {
            return 'i' . $value;
        }
        if (\is_float($value)) {
            return 'f' . \pack('e', $value);
        }
        if (\is_array($value)) {
            return 'a' . \count($value);
        }
        if (\is_object($value)) {
            return 'o' . \spl_object_id($value);
        }
        // A closed resource is no longer \is_resource(), but it keeps its id, and `===` still tells it
        // apart from other closed ones.
        if (\is_resource($value) || \gettype($value) === 'resource (closed)') {
            /** @var resource $value */
            return 'r' . \get_resource_id($value);
        }
        if (\is_string($value)) {
            return 's' . $value;
        }

        return \get_debug_type($value) . (\is_bool($value) && $value ? '1' : '');
    }

    /**
     * @param array<mixed> $array
     */
    private static function describeKeys(array $array): string
    {
        $keys = '';
        foreach ($array as $key => $_) {
            $keys .= \is_int($key) ? 'i' . $key . ',' : 's' . \strlen($key) . ':' . $key;
        }

        return $keys;
    }

    /**
     * A test that $a and $b, which have the same count and are not identical, answer differently, or null
     * if they hold the same keys in the same order and the same float bits throughout (NAN being what
     * `===` rejected).
     *
     * Subarrays with entries are compared by entry id, so the search descends into one pair of distinct
     * classes at most per level.
     *
     * @param array<mixed> $a
     * @param array<array-key, int> $aKids
     * @param array<mixed> $b
     * @param array<array-key, int> $bKids
     *
     * @return array{list<array-key>, bool}|null
     */
    private function difference(array $a, array $aKids, array $b, array $bKids): ?array
    {
        if (\array_keys($a) !== \array_keys($b)) {
            foreach ($a as $key => $_) {
                if (!\array_key_exists($key, $b)) {
                    return [[$key], false];
                }
            }
            foreach ($b as $key => $_) {
                if (!\array_key_exists($key, $a)) {
                    return [[$key], false];
                }
            }

            return [[], true];
        }

        foreach ($a as $key => $x) {
            /** @var mixed $y */
            $y = $b[$key];
            if (\is_array($x) && \is_array($y)) {
                if (isset($aKids[$key], $bKids[$key]) && $aKids[$key] === $bKids[$key]) {
                    continue;
                }
                if (\count($x) !== \count($y)) {
                    return [[$key], false];
                }
                $nested = $this->difference(
                    $x,
                    isset($aKids[$key]) ? $this->entries[$aKids[$key]]->kids : [],
                    $y,
                    isset($bKids[$key]) ? $this->entries[$bKids[$key]]->kids : []
                );
                if ($nested !== null) {
                    return [[$key, ...$nested[0]], $nested[1]];
                }
            } elseif (\is_string($x) && \is_string($y) ? $x !== $y : self::describe($x) !== self::describe($y)) {
                return [[$key], false];
            }
        }

        return null;
    }

    /**
     * Whether each float zero in $array has the sign it has in the original of entry $id, which is `===`
     * to it.
     *
     * Only the elements that hold a zero are read, through the entries of their subarrays.
     *
     * @param array<mixed> $array
     */
    private function sameSigns(array $array, int $id): bool
    {
        $entry = $this->entries[$id];
        $keys = $entry->zeroKeys;
        if (\count($keys) >= self::MIN_JOINED_ZEROS) {
            if (!\array_key_exists($id, $this->joinedZeros)) {
                $this->joinedZeros[$id] = self::joinZeros($entry);
            }
            $joined = $this->joinedZeros[$id];
            if ($joined !== null) {
                // The elements at those keys are float zeros, as in the original: -0.0 converts to "-0".
                /** @var array<float> $zeros */
                $zeros = \array_intersect_key($array, $joined[0]);
                if (\implode(',', $zeros) !== $joined[1]) {
                    return false;
                }
                $keys = $joined[2];
            }
        }

        foreach ($keys as $key) {
            /** @var mixed $element */
            $element = $array[$key];
            /** @var mixed $original */
            $original = $entry->original[$key];
            if (\is_array($element)) {
                if (isset($entry->kids[$key])) {
                    if (!$this->sameSigns($element, $entry->kids[$key])) {
                        return false;
                    }
                } elseif (!\is_array($original) || !self::sameZeroSigns($original, $element)) {
                    return false;
                }
            } elseif (!\is_float($element) || !\is_float($original) || \fdiv(1, $element) !== \fdiv(1, $original)) {
                return false;
            }
        }

        return true;
    }

    /**
     * The keys of $entry's float zeros, their original converted to one string, and its other zero keys;
     * or null if they are too few for that to be faster than reading them one by one.
     *
     * @return array{array<array-key, true>, string, list<array-key>}|null
     */
    private static function joinZeros(SnapshotEntry $entry): ?array
    {
        $floats = [];
        $others = [];
        foreach ($entry->zeroKeys as $key) {
            if (\is_float($entry->original[$key])) {
                $floats[$key] = true;
            } else {
                $others[] = $key;
            }
        }
        if (\count($floats) < self::MIN_JOINED_ZEROS || 4 * \count($floats) < \count($entry->original)) {
            return null;
        }

        /** @var array<float> $zeros */
        $zeros = \array_intersect_key($entry->original, $floats);

        return [$floats, \implode(',', $zeros), $others];
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
            /** @var mixed $other */
            $other = $equal[$key];
            if (\is_array($element)) {
                if (!\is_array($other) || !self::sameZeroSigns($element, $other)) {
                    return false;
                }
            } elseif ($element === 0.0) {
                if (!\is_float($other) || \fdiv(1, $element) !== \fdiv(1, $other)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * A summary of a few of $array's keys and elements, read in constant time, equal for arrays that are
     * identical (`===`, so -0.0 and 0.0 alike).
     *
     * It reads the first and last keys and elements, and the element at the key half the count, if any.
     * A subarray element is summarized by its count only, which also keeps a self-referential one finite.
     *
     * @param array<mixed> $array
     */
    private static function fingerprint(array $array): string
    {
        $first = \array_key_first($array);
        $last = \array_key_last($array);
        if ($first === null || $last === null) {
            return '';
        }
        $middle = \intdiv(\count($array), 2);

        return self::summary($first) . '|' . self::summary($last) . '|'
            . self::summary($array[$first]) . '|' . self::summary($array[$last]) . '|'
            . (\array_key_exists($middle, $array) ? self::summary($array[$middle]) : '');
    }

    /**
     * A summary of one key or element for {@see fingerprint()}, equal for identical values.
     */
    private static function summary(mixed $value): string
    {
        if (\is_int($value)) {
            return 'i' . $value;
        }
        if (\is_string($value)) {
            return 's' . \strlen($value) . ':' . \substr($value, 0, self::FINGERPRINT_STRING_BYTES);
        }
        if (\is_float($value)) {
            // -0.0 === 0.0, but they convert to different strings.
            return $value == 0.0 ? 'f0' : \sprintf('f%.17g', $value);
        }
        if (\is_array($value)) {
            return 'a' . \count($value);
        }
        if (\is_object($value)) {
            return 'o' . \spl_object_id($value);
        }

        return \get_debug_type($value) . (\is_bool($value) && $value ? '1' : '');
    }
}

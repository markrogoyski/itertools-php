<?php

declare(strict_types=1);

namespace IterTools;

final class Combinatorics
{
    /**
     * Cartesian product of the input iterables.
     *
     * Output tuples are list arrays (0-indexed, in input order). Source keys are ignored.
     * The output order follows Python's itertools.product (lexicographic, input-order-preserving):
     * the rightmost iterable advances fastest.
     *
     * Input iterables are finite and are consumed once; they are materialized internally,
     * so passing generators is supported (but they cannot be re-iterated afterwards).
     *
     * Note: Passing the same non-rewindable iterator instance (e.g. a Generator) more than
     * once is not supported — the second occurrence will throw because the iterator has
     * already been consumed. Pass distinct instances instead.
     *
     * Special cases:
     *  - product() of zero iterables yields one empty tuple: [[]]
     *  - product of any iterable with an empty iterable yields nothing
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return \Generator<list<mixed>>
     */
    public static function product(iterable ...$iterables): \Generator
    {
        $pools = [];
        foreach ($iterables as $iterable) {
            $pool = Transform::toArray($iterable);
            if (\count($pool) === 0) {
                return;
            }
            $pools[] = $pool;
        }

        $poolCount = \count($pools);

        if ($poolCount === 0) {
            yield [];
            return;
        }

        $indices = \array_fill(0, $poolCount, 0);

        while (true) {
            $tuple = [];
            for ($i = 0; $i < $poolCount; $i++) {
                $tuple[] = $pools[$i][$indices[$i]];
            }
            yield $tuple;

            $i = $poolCount - 1;
            while ($i >= 0) {
                $indices[$i]++;
                if ($indices[$i] < \count($pools[$i])) {
                    break;
                }
                $indices[$i] = 0;
                $i--;
            }
            if ($i < 0) {
                return;
            }
        }
    }

    /**
     * Permutations of the input iterable.
     *
     * Output tuples are list arrays (0-indexed, in input order). Source keys are ignored.
     * The output order follows Python's itertools.permutations (lexicographic by input
     * position, not by value), so duplicate values are treated as position-unique:
     * permutations([1, 1]) yields [[1, 1], [1, 1]].
     *
     * Input iterable is finite and consumed once (materialized internally), so passing
     * a generator is supported (but it cannot be re-iterated afterwards).
     *
     * Special cases:
     *  - $r = 0 yields one empty tuple: [[]]
     *  - $r greater than count($data) yields nothing
     *  - $r = null means full-length permutations (equivalent to $r = count($data))
     *  - empty input with $r = null (or $r = 0) yields one empty tuple: [[]]
     *
     * @param iterable<mixed> $data
     * @param int|null        $r length of each permutation; null means full length
     *
     * @return \Generator<list<mixed>>
     *
     * @throws \InvalidArgumentException if $r is negative
     */
    public static function permutations(iterable $data, ?int $r = null): \Generator
    {
        if ($r !== null && $r < 0) {
            throw new \InvalidArgumentException("r must be non-negative. Got {$r}.");
        }

        $pool = Transform::toArray($data);
        $n = \count($pool);
        $r = $r ?? $n;

        if ($r > $n) {
            return;
        }

        $indices = \array_fill(0, \max($n, 1), 0);
        for ($i = 0; $i < $n; $i++) {
            $indices[$i] = $i;
        }

        $cycles = \array_fill(0, \max($r, 1), 0);
        for ($i = 0; $i < $r; $i++) {
            $cycles[$i] = $n - $i;
        }

        $tuple = [];
        for ($i = 0; $i < $r; $i++) {
            $tuple[] = $pool[$indices[$i]];
        }
        yield $tuple;

        if ($n === 0) {
            return;
        }

        while (true) {
            $advanced = false;
            for ($i = $r - 1; $i >= 0; $i--) {
                $cycles[$i]--;
                if ($cycles[$i] === 0) {
                    $rotated = $indices[$i];
                    for ($k = $i; $k < $n - 1; $k++) {
                        $indices[$k] = $indices[$k + 1];
                    }
                    $indices[$n - 1] = $rotated;
                    $cycles[$i] = $n - $i;
                } else {
                    $j = $cycles[$i];
                    $tmp = $indices[$i];
                    $indices[$i] = $indices[$n - $j];
                    $indices[$n - $j] = $tmp;
                    $tuple = [];
                    for ($k = 0; $k < $r; $k++) {
                        $tuple[] = $pool[$indices[$k]];
                    }
                    yield $tuple;
                    $advanced = true;
                    break;
                }
            }
            if (!$advanced) {
                return;
            }
        }
    }

    /**
     * Combinations (without replacement) of the input iterable.
     *
     * Output tuples are list arrays (0-indexed, in input order). Source keys are ignored.
     * Output order follows Python's itertools.combinations (lexicographic by input position,
     * not by value), so duplicate values are treated as position-unique:
     * combinations([1, 1], 2) yields [[1, 1]].
     *
     * Input iterable is finite and consumed once (materialized internally), so passing
     * a generator is supported (but it cannot be re-iterated afterwards).
     *
     * Special cases:
     *  - $r = 0 yields one empty tuple: [[]]
     *  - $r greater than count($data) yields nothing
     *  - $r = count($data) yields exactly one tuple containing all input values
     *
     * @param iterable<mixed> $data
     * @param int             $r length of each combination
     *
     * @return \Generator<list<mixed>>
     *
     * @throws \InvalidArgumentException if $r is negative
     */
    public static function combinations(iterable $data, int $r): \Generator
    {
        if ($r < 0) {
            throw new \InvalidArgumentException("r must be non-negative. Got {$r}.");
        }

        $pool = Transform::toArray($data);
        $n = \count($pool);

        if ($r > $n) {
            return;
        }

        $indices = [];
        for ($i = 0; $i < $r; $i++) {
            $indices[] = $i;
        }

        $tuple = [];
        for ($i = 0; $i < $r; $i++) {
            $tuple[] = $pool[$indices[$i]];
        }
        yield $tuple;

        if ($r === 0) {
            return;
        }

        while (true) {
            $pivot = -1;
            for ($i = $r - 1; $i >= 0; $i--) {
                if ($indices[$i] !== $i + $n - $r) {
                    $pivot = $i;
                    break;
                }
            }
            if ($pivot === -1) {
                return;
            }

            $indices[$pivot]++;
            for ($j = $pivot + 1; $j < $r; $j++) {
                $indices[$j] = $indices[$j - 1] + 1;
            }

            $tuple = [];
            for ($i = 0; $i < $r; $i++) {
                $tuple[] = $pool[$indices[$i]];
            }
            yield $tuple;
        }
    }

    /**
     * Combinations with replacement of the input iterable.
     *
     * Output tuples are list arrays (0-indexed, in input order). Source keys are ignored.
     * Output order follows Python's itertools.combinations_with_replacement (lexicographic
     * by input position, not by value), so duplicate input values are treated as
     * position-unique and may produce duplicate output tuples:
     * combinationsWithReplacement([1, 1], 2) yields [[1, 1], [1, 1], [1, 1]].
     *
     * Input iterable is finite and consumed once (materialized internally), so passing
     * a generator is supported (but it cannot be re-iterated afterwards).
     *
     * Unlike combinations(), $r may exceed count($data) — elements repeat.
     *
     * Special cases:
     *  - $r = 0 yields one empty tuple: [[]]
     *  - empty input with $r > 0 yields nothing
     *  - empty input with $r = 0 yields one empty tuple: [[]]
     *
     * @param iterable<mixed> $data
     * @param int             $r length of each combination
     *
     * @return \Generator<list<mixed>>
     *
     * @throws \InvalidArgumentException if $r is negative
     */
    public static function combinationsWithReplacement(iterable $data, int $r): \Generator
    {
        if ($r < 0) {
            throw new \InvalidArgumentException("r must be non-negative. Got {$r}.");
        }

        $pool = Transform::toArray($data);
        $n = \count($pool);

        if ($n === 0 && $r > 0) {
            return;
        }

        $indices = \array_fill(0, \max($r, 1), 0);

        $tuple = [];
        for ($i = 0; $i < $r; $i++) {
            $tuple[] = $pool[$indices[$i]];
        }
        yield $tuple;

        if ($r === 0) {
            return;
        }

        while (true) {
            $pivot = -1;
            for ($i = $r - 1; $i >= 0; $i--) {
                if ($indices[$i] !== $n - 1) {
                    $pivot = $i;
                    break;
                }
            }
            if ($pivot === -1) {
                return;
            }

            $newIndex = $indices[$pivot] + 1;
            for ($j = $pivot; $j < $r; $j++) {
                $indices[$j] = $newIndex;
            }

            $tuple = [];
            for ($i = 0; $i < $r; $i++) {
                $tuple[] = $pool[$indices[$i]];
            }
            yield $tuple;
        }
    }
}

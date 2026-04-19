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
}

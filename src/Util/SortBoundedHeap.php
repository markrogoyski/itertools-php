<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * Bounded heap of (key, insertion-index, value) triples used by Sort::largest and Sort::smallest.
 *
 * Configurable as min-heap (for largest) or max-heap (for smallest) by direction:
 *   - 1  => max-heap (root is the largest element; used by smallest, evict on strict less)
 *   - -1 => min-heap (root is the smallest element; used by largest, evict on strict greater)
 *
 * Among elements with equal keys, the one with the higher insertion index has higher priority
 * (lives closer to the root and will be evicted first), so the earliest insertions are retained.
 *
 * @internal
 *
 * @extends \SplHeap<array{0: mixed, 1: int, 2: mixed}>
 */
final class SortBoundedHeap extends \SplHeap
{
    public function __construct(private readonly int $direction)
    {
    }

    /**
     * @param array{0: mixed, 1: int, 2: mixed} $value1
     * @param array{0: mixed, 1: int, 2: mixed} $value2
     */
    protected function compare(mixed $value1, mixed $value2): int
    {
        $cmp = ($value1[0] <=> $value2[0]) * $this->direction;
        if ($cmp !== 0) {
            return $cmp;
        }
        return $value1[1] <=> $value2[1];
    }
}

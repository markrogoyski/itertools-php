<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 *
 * @extends \SplHeap<array{0: mixed, 1: int, 2: mixed, 3: \Iterator}>
 */
final class MergeSortedHeap extends \SplHeap
{
    /**
     * @param array{0: mixed, 1: int, 2: mixed, 3: \Iterator} $value1
     * @param array{0: mixed, 1: int, 2: mixed, 3: \Iterator} $value2
     */
    protected function compare(mixed $value1, mixed $value2): int
    {
        $comparison = $value2[0] <=> $value1[0];
        if ($comparison !== 0) {
            return $comparison;
        }

        return $value2[1] <=> $value1[1];
    }
}

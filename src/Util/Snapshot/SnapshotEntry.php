<?php

declare(strict_types=1);

namespace IterTools\Util\Snapshot;

/**
 * @internal
 * One class of identical subarrays (`===`, with the same float bits) that {@see Snapshot::of()} rebuilt.
 */
final class SnapshotEntry
{
    /**
     * @param array<mixed> $original the first subarray of the class met; acyclic, as its walk was not cut
     *        short by the depth limit
     * @param array<mixed> $copy its rebuilt copy
     * @param int $height number of levels of arrays nested in $original
     * @param list<array-key> $zeroKeys keys whose element is a float zero or an array holding one
     * @param array<array-key, int> $kids entry ids of the subarray elements that have an entry, by key
     */
    public function __construct(
        public readonly array $original,
        public readonly array $copy,
        public readonly int $height,
        public readonly array $zeroKeys,
        public readonly array $kids
    ) {
    }
}

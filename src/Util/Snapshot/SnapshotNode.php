<?php

declare(strict_types=1);

namespace IterTools\Util\Snapshot;

/**
 * @internal
 * A test in {@see SnapshotCache}'s discrimination tree: the value found at $path (or, if $keys, the key
 * list of the array found there), routing to a child node or an entry id per answer.
 *
 * If its parent's path is a prefix of $path, $tail holds the rest, so a route reads on from the value it
 * found for the parent rather than from the top: a route through a tower of nested arrays, whose tests
 * each go one level deeper, reads each level once.
 */
final class SnapshotNode
{
    /**
     * @var array<array-key, SnapshotNode|int> by a string value itself, so its cached hash is reused
     */
    public array $byString = [];

    /**
     * @var array<string, SnapshotNode|int> by {@see SnapshotCache::describe()} of any other answer
     */
    public array $byOther = [];

    /**
     * @param list<array-key> $path
     * @param list<array-key>|null $tail
     */
    public function __construct(
        public readonly array $path,
        public readonly bool $keys,
        public readonly ?array $tail
    ) {
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 *
 * Every query takes the hash {@see self::addUsage()} returned rather than a value. A value is
 * hashed exactly once, when it is registered: a retained array holding a reference slot follows
 * later assignments to that slot, so hashing it again could produce a different key and miss
 * (or hit the wrong) counts.
 */
final class UsageMap
{
    /**
     * @var array<string, array<string, int>>
     */
    private array $addedMap = [];
    /**
     * @var array<string, int>
     */
    private array $deletedMap = [];
    /**
     * The most recently registered value behind each hash, keyed in first-seen order.
     *
     * Registering a value also retains it: in strict mode an object's ID string comes from its
     * spl_object_id, which PHP reuses once the object is freed. Holding the value keeps that ID
     * reserved so a later, unrelated object cannot inherit it and merge with its usage counts.
     * Overwriting does not weaken that: values sharing a hash in strict mode are the same
     * instance, so the retained object is never released, and the hashes that do merge distinct
     * values (coercive scalars and serialized objects) are not derived from an ID at all.
     *
     * The last value wins so that consumers report the same representative of an equivalence
     * class that they did before this map retained values -- for coercive comparisons that is an
     * observable type-level difference, e.g. the '1' rather than the 1 of [1] vs ['1', '1'].
     *
     * Registering a value additionally retains the identity anchors its hash depends on, in
     * {@see self::$anchors}, so nested identities stay pinned independently of which
     * representative the last-seen slot above currently holds; see {@see Identity}.
     *
     * @var array<string, mixed>
     */
    private array $values = [];
    /**
     * Every anchor of every distinct hash this map has seen, appended on first sight and never
     * removed -- deleting a usage does not delete the identities it depended on. See
     * {@see Identity}; {@see ValueCounter} retains anchors for the same reason.
     *
     * Deliberately write-only: retention is achieved by holding the references, not by reading
     * them back, so there is no consumer of this list inside the class.
     *
     * @var list<object|resource|closed-resource>
     */
    // @phpstan-ignore property.onlyWritten
    private array $anchors = [];
    /**
     * @param bool $strict
     */
    public function __construct(private readonly bool $strict)
    {
    }

    /**
     * Registers usage of the value by owner.
     *
     * @param mixed $value
     * @param string $owner
     *
     * @return string unique hash string
     */
    public function addUsage(mixed $value, string $owner): string
    {
        $identity = UniqueExtractor::identify($value, $this->strict);
        $hash = $identity->key;

        $this->values[$hash] = $value;

        if (!isset($this->addedMap[$hash])) {
            $this->addedMap[$hash] = [];

            foreach ($identity->anchors as $anchor) {
                $this->anchors[] = $anchor;
            }
        }

        if (!isset($this->addedMap[$hash][$owner])) {
            $this->addedMap[$hash][$owner] = 0;
        }

        $this->addedMap[$hash][$owner]++;

        return $hash;
    }

    /**
     * Returns the latest registered value for each unique hash string, in first-seen key order.
     *
     * @return array<string, mixed>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Unregister usage of the value registered under the hash.
     *
     * @param string $hash as returned by {@see self::addUsage()}
     */
    public function deleteUsage(string $hash): void
    {
        if (!isset($this->deletedMap[$hash])) {
            $this->deletedMap[$hash] = 0;
        }

        $this->deletedMap[$hash]++;
    }

    /**
     * Returns number of owners of the value registered under the hash.
     *
     * @param string $hash as returned by {@see self::addUsage()}
     *
     * @return int
     */
    public function getOwnersCount(string $hash): int
    {
        $deletesCount = $this->deletedMap[$hash] ?? 0;

        $count = 0;
        foreach ($this->addedMap[$hash] ?? [] as $usageCount) {
            if ($usageCount > $deletesCount) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Returns number of usages of the value registered under the hash, with limitation by max owners.
     *
     * @param string $hash as returned by {@see self::addUsage()}
     * @param int $maxOwnersCount
     *
     * @return int
     */
    public function getUsagesCount(string $hash, int $maxOwnersCount = 1): int
    {
        $deletesCount = $this->deletedMap[$hash] ?? 0;

        $ownersMap = [];
        foreach ($this->addedMap[$hash] ?? [] as $owner => $count) {
            $adjusted = $count - $deletesCount;
            if ($adjusted > 0) {
                $ownersMap[$owner] = $adjusted;
            }
        }

        while (\count($ownersMap) > $maxOwnersCount) {
            /** @var non-empty-array<string, int<1, max>> $ownersMap */
            $minValue = \min($ownersMap);
            $filtered = [];
            foreach ($ownersMap as $owner => $count) {
                $adjusted = $count - $minValue;
                if ($adjusted > 0) {
                    $filtered[$owner] = $adjusted;
                }
            }
            $ownersMap = $filtered;
        }

        return \array_sum($ownersMap);
    }

    /**
     * Returns true if all owners have used the value registered under the hash the same number of times.
     *
     * @param string $hash as returned by {@see self::addUsage()}
     * @param int $ownersCount
     *
     * @return bool
     */
    public function hasSameOwnerCount(string $hash, int $ownersCount): bool
    {
        $map = $this->addedMap[$hash] ?? [];

        if (\count($map) !== $ownersCount) {
            return false;
        }

        return \count(\array_unique($map)) <= 1;
    }
}

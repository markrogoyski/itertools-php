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
 *
 * Identity is pinned by the anchors alone (see {@see self::$anchors}). Representatives are kept
 * only when asked for at construction, by the one consumer that reports them (symmetric
 * difference); keeping them otherwise would hold every distinct value of a stream in memory.
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
     * The most recently registered value behind each hash, keyed in first-seen order; empty
     * unless representatives are retained.
     *
     * The last value wins so that symmetric difference reports the same representative of an
     * equivalence class that it always has -- for coercive comparisons that is an observable
     * type-level difference, e.g. the '1' rather than the 1 of [1] vs ['1', '1'].
     *
     * @var array<string, mixed>
     */
    private array $values = [];
    /**
     * Every anchor of every distinct hash this map has seen, recorded on first sight and never
     * removed -- deleting a usage does not delete the identities it depended on. See
     * {@see Identity}; {@see ValueCounter} retains anchors for the same reason.
     *
     * Keyed by {@see UniqueExtractor::anchorId()}, so the same object or resource nested in many
     * distinct registered values is held once instead of once per hash — see {@see Identity}.
     *
     * Deliberately write-only: retention is achieved by holding the references, not by reading
     * them back, so there is no consumer of this map inside the class.
     *
     * @var array<string, object|resource|closed-resource>
     */
    // @phpstan-ignore property.onlyWritten
    private array $anchors = [];
    /**
     * @param bool $strict
     * @param bool $retainValues whether to keep the last-seen representative per hash for {@see self::getValues()}
     */
    public function __construct(private readonly bool $strict, private readonly bool $retainValues = false)
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

        if ($this->retainValues) {
            $this->values[$hash] = $value;
        }

        if (!isset($this->addedMap[$hash])) {
            $this->addedMap[$hash] = [];

            foreach ($identity->anchors as $anchor) {
                $this->anchors[UniqueExtractor::anchorId($anchor)] = $anchor;
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
     *
     * @throws \LogicException if the map was constructed without retaining values
     */
    public function getValues(): array
    {
        if (!$this->retainValues) {
            throw new \LogicException('UsageMap was constructed without retaining values');
        }

        return $this->values;
    }

    /**
     * Returns each unique hash string once, in first-seen order.
     *
     * @return list<string>
     */
    public function getHashes(): array
    {
        return \array_keys($this->addedMap);
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

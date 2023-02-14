<?php

declare(strict_types=1);

namespace IterTools\Util;

use IterTools\Reduce;
use IterTools\Stream;

/**
 * @internal
 */
class UsageMap
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
     * @var bool
     */
    private bool $strict;

    /**
     * @param bool $strict
     */
    public function __construct(bool $strict)
    {
        $this->strict = $strict;
    }

    /**
     * Registers usage of the value by owner.
     *
     * @param mixed $value
     * @param string $owner
     *
     * @return string unique hash string
     */
    public function addUsage($value, string $owner): string
    {
        $hash = UniqueExtractor::getString($value, $this->strict);

        if (!isset($this->addedMap[$hash])) {
            $this->addedMap[$hash] = [];
        }

        if (!isset($this->addedMap[$hash][$owner])) {
            $this->addedMap[$hash][$owner] = 0;
        }

        $this->addedMap[$hash][$owner]++;

        return $hash;
    }

    /**
     * Unregister usage of the value.
     *
     * @param mixed $value
     *
     * @return string unique hash string
     */
    public function deleteUsage($value): string
    {
        $hash = UniqueExtractor::getString($value, $this->strict);

        if (!isset($this->deletedMap[$hash])) {
            $this->deletedMap[$hash] = 0;
        }

        $this->deletedMap[$hash]++;

        return $hash;
    }

    /**
     * Returns number of value's owners.
     *
     * @param mixed $value
     *
     * @return int
     */
    public function getOwnersCount($value): int
    {
        $hash = UniqueExtractor::getString($value, $this->strict);
        $deletesCount = $this->deletedMap[$hash] ?? 0;

        return Stream::of($this->addedMap[$hash] ?? [])
            ->filterTrue(fn ($count) => $count > $deletesCount)
            ->toCount();
    }

    /**
     * Returns number of value usages with limitation by max owners.
     *
     * @param mixed $value
     * @param int $maxOwnersCount
     *
     * @return int
     */
    public function getUsagesCount($value, int $maxOwnersCount = 1): int
    {
        $hash = UniqueExtractor::getString($value, $this->strict);
        $deletesCount = $this->deletedMap[$hash] ?? 0;

        $ownersMap = Stream::of($this->addedMap[$hash] ?? [])
            ->map(fn ($value) => $value - $deletesCount)
            ->filterTrue(fn ($value) => $value > 0)
            ->toArray();

        while (count($ownersMap) > $maxOwnersCount) {
            $minValue = Reduce::toMin($ownersMap);
            $ownersMap = Stream::of($ownersMap)
                ->map(fn ($value) => $value - $minValue)
                ->filterTrue(fn ($value) => $value > 0)
                ->toArray();
        }

        /** @var array<string, int> $ownersMap */
        return (int)Reduce::toSum($ownersMap);
    }

    /**
     * Returns true if all owners have used given value the same number of times.
     *
     * @param mixed $value
     * @param int $ownersCount
     *
     * @return bool
     */
    public function hasSameOwnerCount($value, int $ownersCount): bool
    {
        $hash = UniqueExtractor::getString($value, $this->strict);
        $map = $this->addedMap[$hash] ?? [];

        if (\count($map) !== $ownersCount) {
            return false;
        }

        $differentUsageCounts = Stream::of($map)
            ->distinct()
            ->toCount();

        return $differentUsageCounts <= 1;
    }
}

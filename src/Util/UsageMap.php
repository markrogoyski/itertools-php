<?php

declare(strict_types=1);

namespace IterTools\Util;

use IterTools\Reduce;
use IterTools\Stream;

/**
 * @internal
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
     *
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    public function deleteUsage(mixed $value): string
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
    public function getOwnersCount(mixed $value): int
    {
        $hash = UniqueExtractor::getString($value, $this->strict);
        $deletesCount = $this->deletedMap[$hash] ?? 0;

        return Stream::of($this->addedMap[$hash] ?? [])
            ->filterTrue(fn (int $count): bool => $count > $deletesCount)
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
    public function getUsagesCount(mixed $value, int $maxOwnersCount = 1): int
    {
        $hash = UniqueExtractor::getString($value, $this->strict);
        $deletesCount = $this->deletedMap[$hash] ?? 0;

        $ownersMap = Stream::of($this->addedMap[$hash] ?? [])
            ->map(fn (int $value) => $value - $deletesCount)
            ->filterTrue(fn (int $value) => $value > 0)
            ->toArray();

        while (count($ownersMap) > $maxOwnersCount) {
            /** @var int $minValue */
            $minValue = Reduce::toMin($ownersMap);
            $ownersMap = Stream::of($ownersMap)
                ->map(fn (int $value) => $value - $minValue)
                ->filterTrue(fn (int $value) => $value > 0)
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
    public function hasSameOwnerCount(mixed $value, int $ownersCount): bool
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

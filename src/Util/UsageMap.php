<?php

declare(strict_types=1);

namespace IterTools\Util;

use IterTools\Reduce;
use IterTools\Stream;

class UsageMap
{
    /**
     * @var array<string, array<string, int>>
     */
    protected array $addedMap = [];
    /**
     * @var array<string, int>
     */
    protected array $deletedMap = [];
    /**
     * @var bool
     */
    protected bool $strict;

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
     * @return void
     */
    public function addUsage($value, string $owner): void
    {
        $hash = UniqueExtractor::getString($value, $this->strict);

        if (!isset($this->addedMap[$hash])) {
            $this->addedMap[$hash] = [];
        }

        if (!isset($this->addedMap[$hash][$owner])) {
            $this->addedMap[$hash][$owner] = 0;
        }

        $this->addedMap[$hash][$owner]++;
    }

    /**
     * Unregister usage of the value.
     *
     * @param mixed $value
     *
     * @return void
     */
    public function deleteUsage($value): void
    {
        $hash = UniqueExtractor::getString($value, $this->strict);

        if (!isset($this->deletedMap[$hash])) {
            $this->deletedMap[$hash] = 0;
        }

        $this->deletedMap[$hash]++;
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
}

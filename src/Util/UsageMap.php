<?php

declare(strict_types=1);

namespace IterTools\Util;

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
}

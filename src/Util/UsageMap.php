<?php

declare(strict_types=1);

namespace IterTools\Util;

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
     * Values behind each hash, in first-seen order.
     *
     * Registering a value also retains it: in strict mode an object's ID string comes from its
     * spl_object_id, which PHP reuses once the object is freed. Holding the value keeps that ID
     * reserved so a later, unrelated object cannot inherit it and merge with its usage counts.
     *
     * @var array<string, mixed>
     */
    private array $values = [];
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

        if (!\array_key_exists($hash, $this->values)) {
            $this->values[$hash] = $value;
        }

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
     * Returns every registered value, keyed by its unique hash string, in first-seen order.
     *
     * @return array<string, mixed>
     */
    public function getValues(): array
    {
        return $this->values;
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

        $count = 0;
        foreach ($this->addedMap[$hash] ?? [] as $usageCount) {
            if ($usageCount > $deletesCount) {
                $count++;
            }
        }

        return $count;
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

        return \count(\array_unique($map)) <= 1;
    }
}

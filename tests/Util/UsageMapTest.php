<?php

declare(strict_types=1);

namespace IterTools\Tests\Util;

use IterTools\Util\UsageMap;

/**
 * Hash strings are internal to UniqueExtractor: these tests assert counts, values and their
 * order, never how a hash is spelled.
 */
class UsageMapTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test addUsage keeps distinct keys for objects whose freed ids PHP could recycle
     *
     * Passes today because the map's last-seen representative already pins each hash's
     * identity; this guards that existing behavior alongside the new anchor retention below.
     */
    public function testAddUsageKeepsRecycledObjectIdsApart(): void
    {
        // Given
        $usageMap = new UsageMap(true);

        // When
        $hashes = [];
        foreach (self::freshObjectsDroppingEach(3) as $object) {
            $hashes[] = $usageMap->addUsage($object, 'owner');
            unset($object);
        }

        // Then
        $this->assertCount(3, \array_unique($hashes));
        $this->assertCount(3, $usageMap->getValues());
    }

    /**
     * @test a registered value's anchor stays alive for as long as the map does, although the
     * caller's own reference is dropped
     */
    public function testAddUsageRetainsAnchorForTheLifetimeOfTheMap(): void
    {
        // Given
        $usageMap = new UsageMap(true);
        $object = new \stdClass();
        $weakReference = \WeakReference::create($object);

        // When
        $usageMap->addUsage($object, 'owner');
        unset($object);

        // Then
        $this->assertNotNull($weakReference->get());

        // When
        unset($usageMap);

        // Then
        $this->assertNull($weakReference->get());
    }

    /**
     * @test deleteUsage does not drop the anchor for the key it deletes
     */
    public function testDeleteUsageDoesNotDropAnchor(): void
    {
        // Given
        $usageMap = new UsageMap(true);
        $object = new \stdClass();
        $weakReference = \WeakReference::create($object);
        $usageMap->addUsage($object, 'owner');

        // When
        $usageMap->deleteUsage($object);
        unset($object);

        // Then
        $this->assertNotNull($weakReference->get());
    }

    /**
     * Yields fresh objects, dropping each one before the next is created, so that PHP is free to
     * hand the freed spl_object_id to its successor.
     *
     * @param int $count
     *
     * @return \Generator<int, \stdClass>
     */
    private static function freshObjectsDroppingEach(int $count): \Generator
    {
        for ($i = 0; $i < $count; $i++) {
            $object = new \stdClass();
            yield $object;
            unset($object);
        }
    }
}

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
     * The map retains no values here, so it is the anchors alone that pin each hash's identity.
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
        $this->assertCount(3, $usageMap->getHashes());
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
        $hash = $usageMap->addUsage($object, 'owner');

        // When
        $usageMap->deleteUsage($hash);
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

    /**
     * @test getValues() is unavailable unless the map retains values
     */
    public function testGetValuesThrowsWithoutRetention(): void
    {
        // Given
        $usageMap = new UsageMap(true);
        $usageMap->addUsage(1, 'owner');

        // Then
        $this->expectException(\LogicException::class);

        // When
        $usageMap->getValues();
    }

    /**
     * @test without value retention, a coercively compared object is not kept alive
     */
    public function testCoerciveObjectIsNotRetainedWithoutValueRetention(): void
    {
        // Given
        $usageMap = new UsageMap(false);
        $object = (object) ['value' => 1];
        $weakReference = \WeakReference::create($object);

        // When
        $hash = $usageMap->addUsage($object, 'owner');
        unset($object);

        // Then
        $this->assertNull($weakReference->get());
        $this->assertSame(1, $usageMap->getOwnersCount($hash));
    }

    /**
     * @test with value retention, the last value registered under a hash is its representative
     */
    public function testRetainsLastSeenRepresentativeWithValueRetention(): void
    {
        // Given
        $usageMap = new UsageMap(false, retainValues: true);

        // When
        $usageMap->addUsage(1, 'a');
        $usageMap->addUsage('1', 'b');

        // Then
        $this->assertSame(['1'], \array_values($usageMap->getValues()));
    }

    /**
     * @test getHashes() lists each distinct hash once, in first-seen order, without retaining values
     */
    public function testGetHashesListsDistinctHashesInFirstSeenOrder(): void
    {
        // Given
        $usageMap = new UsageMap(true);

        // When
        $b = $usageMap->addUsage('b', 'owner');
        $a = $usageMap->addUsage('a', 'owner');
        $usageMap->addUsage('b', 'other');

        // Then
        $this->assertSame([$b, $a], $usageMap->getHashes());
    }
}

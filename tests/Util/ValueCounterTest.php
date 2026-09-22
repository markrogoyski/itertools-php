<?php

declare(strict_types=1);

namespace IterTools\Tests\Util;

use IterTools\Util\ValueCounter;

/**
 * Hash strings are internal to UniqueExtractor: these tests assert counts, sizes, representatives
 * and their order, never how a hash is spelled.
 */
class ValueCounterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test add() returns the running count of equal values
     */
    public function testAddReturnsRunningCount(): void
    {
        // Given
        $counter = new ValueCounter(true);

        // When + Then
        $this->assertSame(1, $counter->add(1));
        $this->assertSame(2, $counter->add(1));
        $this->assertSame(3, $counter->add(1));
    }

    /**
     * @test add() counts each distinct value separately
     */
    public function testAddCountsDistinctValuesSeparately(): void
    {
        // Given
        $counter = new ValueCounter(true);

        // When + Then
        $this->assertSame(1, $counter->add('a'));
        $this->assertSame(1, $counter->add('b'));
        $this->assertSame(2, $counter->add('a'));
        $this->assertSame(2, $counter->add('b'));
    }

    /**
     * @test size() counts distinct values
     */
    public function testSizeCountsDistinctValues(): void
    {
        // Given
        $counter = new ValueCounter(true);

        // When
        $counter->add('a');
        $counter->add('b');
        $counter->add('a');

        // Then
        $this->assertSame(2, $counter->size());
    }

    /**
     * @test coercive mode merges equivalent values and keeps the first representative
     */
    public function testCoerciveModeKeepsFirstRepresentative(): void
    {
        // Given
        $counter = new ValueCounter(false);

        // When
        $counter->add(1);
        $counter->add('1');
        $counter->add(1.0);

        // Then
        $this->assertSame(1, $counter->size());
        $this->assertSame([1], \array_values($counter->values()));
        $this->assertSame([3], \array_values($counter->counts()));
    }

    /**
     * @test strict mode keeps an int and its string apart
     */
    public function testStrictModeKeepsIntAndStringApart(): void
    {
        // Given
        $counter = new ValueCounter(true);

        // When
        $counter->add(1);
        $counter->add('1');

        // Then
        $this->assertSame(2, $counter->size());
        $this->assertSame([1, '1'], \array_values($counter->values()));
        $this->assertSame([1, 1], \array_values($counter->counts()));
    }

    /**
     * @test values() and counts() are keyed by hash in first-seen order
     */
    public function testValuesAndCountsAreInFirstSeenOrder(): void
    {
        // Given
        $counter = new ValueCounter(true);

        // When
        $counter->add('b');
        $counter->add('a');
        $counter->add('b');
        $counter->add('c');
        $counter->add('a');
        $counter->add('b');

        // Then
        $this->assertSame(['b', 'a', 'c'], \array_values($counter->values()));
        $this->assertSame([3, 2, 1], \array_values($counter->counts()));
        $this->assertSame(\array_keys($counter->values()), \array_keys($counter->counts()));
    }

    /**
     * @test an empty counter has no size, values or counts
     */
    public function testEmptyCounter(): void
    {
        // Given
        $counter = new ValueCounter(true);

        // When + Then
        $this->assertSame(0, $counter->size());
        $this->assertSame([], $counter->values());
        $this->assertSame([], $counter->counts());
    }

    /**
     * @test registered objects stay distinct although PHP would recycle their freed ids
     */
    public function testAnchorsKeepRecycledObjectIdsApart(): void
    {
        // Given
        $counter = new ValueCounter(true);

        // When
        $counts = [];
        foreach (self::freshObjectsDroppingEach(3) as $object) {
            $counts[] = $counter->add($object);
            unset($object);
        }

        // Then
        $this->assertSame([1, 1, 1], $counts);
        $this->assertSame(3, $counter->size());
    }

    /**
     * @test anchors() reports the identity-bearing values, once per distinct value
     */
    public function testAnchorsReportsIdentityBearingValuesOncePerDistinctValue(): void
    {
        // Given
        $counter = new ValueCounter(true);
        $object = new \stdClass();
        $resource = \fopen('php://memory', 'r');

        // When
        $counter->add($object);
        $counter->add($object);
        $counter->add($resource);
        $counter->add('a scalar has no anchor');

        // Then
        $this->assertSame([$object, $resource], $counter->anchors());
    }

    /**
     * @test a registered object stays alive for as long as the counter does
     */
    public function testRegisteredObjectIsRetained(): void
    {
        // Given
        $counter = new ValueCounter(true);
        $object = new \stdClass();
        $weakReference = \WeakReference::create($object);

        // When
        $counter->add($object);
        unset($object);

        // Then
        $this->assertNotNull($weakReference->get());
    }

    /**
     * @test coercive mode registers closures by instance although they are not serializable
     */
    public function testCoerciveModeRegistersClosuresByInstance(): void
    {
        // Given
        $counter = new ValueCounter(false);
        $closure = static fn (int $x): int => $x + 1;
        $otherClosure = static fn (int $x): int => $x + 1;

        // When
        $first = $counter->add($closure);
        $second = $counter->add($closure);
        $third = $counter->add($otherClosure);

        // Then
        $this->assertSame(1, $first);
        $this->assertSame(2, $second);
        $this->assertSame(1, $third);
        $this->assertSame(2, $counter->size());
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

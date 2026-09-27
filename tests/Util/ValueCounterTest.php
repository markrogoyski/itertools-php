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
        $counter = new ValueCounter(false, retainValues: true);

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
        $counter = new ValueCounter(true, retainValues: true);

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
        $counter = new ValueCounter(true, retainValues: true);

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
        $counter = new ValueCounter(true, retainValues: true);

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
     * @test arrays sharing one reference slot stay distinct, and their objects stay alive
     */
    public function testArraysSharingAReferenceSlotStayDistinct(): void
    {
        // Given
        $counter = new ValueCounter(true);
        $weakReferences = [];

        // When
        $counts = [];
        foreach (self::arraysSharingAReferenceSlot(5, $weakReferences) as $array) {
            $counts[] = $counter->add($array);
        }
        unset($array);

        // Then
        $this->assertSame([1, 1, 1, 1, 1], $counts);
        $this->assertSame(5, $counter->size());

        $alive = [];
        foreach ($weakReferences as $weakReference) {
            $alive[] = $weakReference->get() !== null;
        }
        $this->assertSame([true, true, true, true, true], $alive);

        // When the counter goes, so do the identities it pinned
        unset($counter);

        $aliveAfterwards = [];
        foreach ($weakReferences as $weakReference) {
            $aliveAfterwards[] = $weakReference->get() !== null;
        }
        $this->assertSame([false, false, false, false, false], $aliveAfterwards);
    }

    /**
     * Yields arrays that all share one reference slot: each yielded [&$slot] follows the next
     * assignment to $slot, so retaining the array does not retain the object it held when it was
     * yielded. Only the anchors the counter keeps can do that.
     *
     * @param int $count
     * @param list<\WeakReference<\stdClass>> $weakReferences filled with one reference per object
     *
     * @return \Generator<int, array{0: \stdClass}>
     */
    private static function arraysSharingAReferenceSlot(int $count, array &$weakReferences): \Generator
    {
        $slot = null;

        for ($i = 0; $i < $count; $i++) {
            $slot = new \stdClass();
            $weakReferences[] = \WeakReference::create($slot);

            yield [&$slot];
        }
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
     * @test values() is unavailable unless the counter retains values
     */
    public function testValuesThrowsWithoutRetention(): void
    {
        // Given
        $counter = new ValueCounter(true);
        $counter->add(1);

        // Then
        $this->expectException(\LogicException::class);

        // When
        $counter->values();
    }

    /**
     * @test without value retention, a coercively compared object is not kept alive
     */
    public function testCoerciveObjectIsNotRetainedWithoutValueRetention(): void
    {
        // Given
        $counter = new ValueCounter(false);
        $object = (object) ['value' => 1];
        $weakReference = \WeakReference::create($object);

        // When
        $counter->add($object);
        unset($object);

        // Then
        $this->assertNull($weakReference->get());
        $this->assertSame(1, $counter->size());
    }

    /**
     * @test with value retention, a coercively compared object is kept as its representative
     */
    public function testCoerciveObjectIsRetainedWithValueRetention(): void
    {
        // Given
        $counter = new ValueCounter(false, retainValues: true);
        $object = (object) ['value' => 1];
        $weakReference = \WeakReference::create($object);

        // When
        $counter->add($object);
        unset($object);

        // Then
        $this->assertNotNull($weakReference->get());
    }
}

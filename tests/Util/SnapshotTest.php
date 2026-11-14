<?php

declare(strict_types=1);

namespace IterTools\Tests\Util;

use IterTools\Util\Snapshot;
use IterTools\Util\Values\UniqueExtractor;

class SnapshotTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         of returns a value that is not an array as is
     * @dataProvider dataProviderForNonArray
     * @param        mixed $value
     */
    public function testNonArrayReturnedAsIs(mixed $value): void
    {
        // When
        $result = Snapshot::of($value);

        // Then
        $this->assertSame($value, $result);
    }

    public static function dataProviderForNonArray(): array
    {
        return [
            [null],
            [true],
            [0],
            [-0.0],
            [INF],
            ['a'],
            [new \stdClass()],
        ];
    }

    /**
     * @test         of returns an array equal to the original, keys and order included
     * @dataProvider dataProviderForArray
     * @param        array<mixed> $value
     */
    public function testArrayEqualToOriginal(array $value): void
    {
        // When
        $result = Snapshot::of($value);

        // Then
        $this->assertSame($value, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [[]],
            [[1, 2, 3]],
            [['b' => 1, 'a' => 2, 5 => 3]],
            [[[1, [2, [3]]], 'x' => ['y' => 'z']]],
        ];
    }

    /**
     * @test of keeps an object in an array as the same instance
     */
    public function testObjectInArrayIsSameInstance(): void
    {
        // Given
        $object = new \stdClass();

        // When
        $result = Snapshot::of([$object]);

        // Then
        $this->assertSame($object, $result[0]);
    }

    /**
     * @test of keeps the value a reference slot holds now, unaffected by later assignments
     */
    public function testReferenceSlotIsBroken(): void
    {
        // Given
        $id = 1;
        $row = ['id' => &$id];

        // When
        $result = Snapshot::of($row);
        $id = 2;

        // Then
        $this->assertSame(['id' => 1], $result);
    }

    /**
     * @test of breaks a reference slot nested inside an array
     */
    public function testNestedReferenceSlotIsBroken(): void
    {
        // Given
        $id = 1;
        $row = ['row' => ['ids' => [&$id]]];

        // When
        $result = Snapshot::of($row);
        $id = 2;

        // Then
        $this->assertSame(['row' => ['ids' => [1]]], $result);
    }

    /**
     * @test of ends for a self-referential array
     */
    public function testSelfReferentialArrayEnds(): void
    {
        // Given
        $array = [1];
        $array[] = &$array;

        // When
        $result = Snapshot::of($array);

        // Then
        $this->assertIsArray($result);
        $this->assertSame(1, $result[0]);
    }

    /**
     * Regression: an array without reference slots is not rebuilt, so subarrays that share PHP's
     * storage stay shared. Doubling [0] sixteen times makes 2^16 logical leaves in 17 arrays; a
     * rebuild of every branch would allocate tens of megabytes.
     *
     * @test of does not copy an array without reference slots
     */
    public function testSharedSubarraysWithoutReferencesAreNotCopied(): void
    {
        // Given
        $array = [0];
        for ($i = 0; $i < 16; $i++) {
            $array = [$array, $array];
        }

        // When
        $before = \memory_get_usage();
        $result = Snapshot::of($array);
        $allocated = \memory_get_usage() - $before;

        // Then
        // assertSame() would export the 2^16 leaves on failure, so the identity is asserted as a bool.
        $this->assertTrue($result === $array);
        $this->assertLessThan(1024 * 1024, $allocated);
    }

    /**
     * Regression: subarrays that share PHP's storage and hold a reference slot are rebuilt once, and
     * the copy is shared in turn. Doubling [&$id] sixteen times makes 2^16 logical leaves in 17 arrays;
     * rebuilding each occurrence would allocate tens of megabytes.
     *
     * @test of rebuilds a shared subarray with a reference slot once
     */
    public function testSharedSubarraysWithReferencesAreRebuiltOnce(): void
    {
        // Given
        $id = 0;
        $array = [&$id];
        $expected = [0];
        for ($i = 0; $i < 16; $i++) {
            $array = [$array, $array];
            $expected = [$expected, $expected];
        }

        // When
        $before = \memory_get_usage();
        $result = Snapshot::of($array);
        $allocated = \memory_get_usage() - $before;
        $id = 1;

        // Then
        // assertSame() would export the 2^16 leaves on failure, so the equality is asserted as a bool.
        $this->assertTrue($result === $expected);
        $this->assertLessThan(1024 * 1024, $allocated);
    }

    /**
     * Regression: -0.0 === 0.0, so a subarray matching an earlier one must not take its copy unless its
     * zeros have the same signs.
     *
     * @test of keeps the sign of a zero in a subarray equal to an earlier one
     */
    public function testSignedZeroInEqualSubarraysIsKept(): void
    {
        // Given
        $negative = -0.0;
        $positive = 0.0;
        $array = [[&$negative], [&$positive], [&$negative]];

        // When
        $result = Snapshot::of($array);

        // Then
        $this->assertSame(-INF, \fdiv(1, $result[0][0]));
        $this->assertSame(INF, \fdiv(1, $result[1][0]));
        $this->assertSame(-INF, \fdiv(1, $result[2][0]));
    }

    /**
     * @test of rebuilds a shared subarray with a reference slot to a zero once
     */
    public function testSharedSubarraysWithReferencesToZeroAreRebuiltOnce(): void
    {
        // Given
        $zero = -0.0;
        $array = [&$zero];
        for ($i = 0; $i < 16; $i++) {
            $array = [$array, $array];
        }

        // When
        $before = \memory_get_usage();
        $result = Snapshot::of($array);
        $allocated = \memory_get_usage() - $before;

        // Then
        $leaf = $result;
        while (\is_array($leaf)) {
            $leaf = $leaf[\array_key_last($leaf)];
        }
        $this->assertSame(-INF, \fdiv(1, $leaf));
        $this->assertLessThan(1024 * 1024, $allocated);
    }

    /**
     * @test of ends for two distinct self-referential arrays of the same shape
     */
    public function testDistinctSelfReferentialArraysEnd(): void
    {
        // Given
        $lhs = [1];
        $lhs[] = &$lhs;
        $rhs = [1];
        $rhs[] = &$rhs;

        // When
        $result = Snapshot::of([$lhs, $rhs]);

        // Then
        $this->assertIsArray($result);
        $this->assertSame(1, $result[0][0]);
        $this->assertSame(1, $result[1][0]);
    }

    /**
     * @test of breaks a reference slot that sits next to subarrays without reference slots
     */
    public function testReferenceSlotBesideSharedSubarraysIsBroken(): void
    {
        // Given
        $id = 1;
        $shared = [1, [2, 3]];
        $row = ['a' => $shared, 'id' => &$id, 'b' => $shared];

        // When
        $result = Snapshot::of($row);
        $id = 2;

        // Then
        $this->assertSame(['a' => [1, [2, 3]], 'id' => 1, 'b' => [1, [2, 3]]], $result);
    }

    /**
     * Distinct subarrays of the same size, each with its own reference slot, far more of them than the
     * copies kept for reuse.
     *
     * @test of breaks the reference slots of many distinct subarrays of the same size
     */
    public function testManyDistinctSubarraysWithReferencesAreBroken(): void
    {
        // Given
        $slots = \range(1, 1000);
        $row = [];
        foreach ($slots as &$slot) {
            $row[] = [&$slot];
        }
        unset($slot);

        // When
        $result = Snapshot::of($row);
        foreach ($slots as &$slot) {
            $slot = 0;
        }
        unset($slot);

        // Then
        $this->assertSame(\array_map(static fn (int $id): array => [$id], \range(1, 1000)), $result);
    }

    /**
     * A shared subarray that recurs after many distinct subarrays of the same size, enough to push its
     * copy out of the ones kept for reuse, is rebuilt again with the values it holds.
     *
     * @test of breaks the reference slot of a shared subarray that recurs after many distinct ones
     */
    public function testSharedSubarrayRecurringAfterManyDistinctOnesIsBroken(): void
    {
        // Given
        $id = -0.0;
        $shared = [&$id];
        $slots = \range(1, 1000);
        $row = [$shared];
        foreach ($slots as &$slot) {
            $row[] = [&$slot];
        }
        unset($slot);
        $row[] = $shared;

        // When
        $result = Snapshot::of($row);
        $id = 1.0;

        // Then
        $this->assertIsArray($result);
        $this->assertCount(1002, $result);
        $this->assertSame([-0.0], $result[0]);
        $this->assertSame(-INF, \fdiv(1, $result[0][0]));
        $this->assertSame([1], $result[1]);
        $this->assertSame([1000], $result[1000]);
        $this->assertSame([-0.0], $result[1001]);
        $this->assertSame(-INF, \fdiv(1, $result[1001][0]));
    }

    /**
     * Regression: seventeen shared subarrays with a reference slot, each of 1024 elements, recurring in
     * turn 64 times, are each rebuilt once. Rebuilding every occurrence would allocate about 17 MiB.
     *
     * @test of rebuilds shared subarrays with reference slots once when more than sixteen recur in turn
     */
    public function testSharedSubarraysWithReferencesRecurringInTurnAreRebuiltOnce(): void
    {
        // Given
        $slots = \range(0, 16);
        $blocks = [];
        foreach ($slots as &$slot) {
            $block = \array_fill(0, 1024, 0);
            $block[0] = &$slot;
            $blocks[] = $block;
        }
        unset($slot, $block);
        $row = [];
        for ($i = 0; $i < 64; $i++) {
            foreach ($blocks as $block) {
                $row[] = $block;
            }
        }

        // When
        $before = \memory_get_usage();
        $result = Snapshot::of($row);
        $allocated = \memory_get_usage() - $before;
        foreach ($slots as &$slot) {
            $slot = -1;
        }
        unset($slot);

        // Then
        // assertIsArray() and assertCount() would export the 1088 subarrays, so scalars are asserted.
        $this->assertSame(17 * 64, \count($result));
        $this->assertSame(0, $result[0][0]);
        $this->assertSame(16, $result[17 * 64 - 1][0]);
        $this->assertSame(0, $result[17 * 64 - 1][1023]);
        $this->assertLessThan(4 * 1024 * 1024, $allocated);
    }

    /**
     * Regression: seventeen shared subarrays of sixteen elements that differ only in the reference slot
     * at one position, recurring in turn 1024 times, are each rebuilt once. Rebuilding every occurrence
     * would allocate about 7 MiB.
     *
     * @dataProvider dataProviderForReferencePosition
     * @test of rebuilds shared subarrays that differ in one reference slot once when they recur in turn
     */
    public function testSharedSubarraysDifferingInOneReferenceSlotRecurringInTurnAreRebuiltOnce(int $position): void
    {
        // Given
        $slots = \range(0, 16);
        $blocks = [];
        foreach ($slots as &$slot) {
            $block = \array_fill(0, 16, 0);
            $block[$position] = &$slot;
            $blocks[] = $block;
        }
        unset($slot, $block);
        $row = [];
        for ($i = 0; $i < 1024; $i++) {
            foreach ($blocks as $block) {
                $row[] = $block;
            }
        }

        // When
        $before = \memory_get_usage();
        $result = Snapshot::of($row);
        $allocated = \memory_get_usage() - $before;
        foreach ($slots as &$slot) {
            $slot = -1;
        }
        unset($slot);

        // Then
        // assertIsArray() and assertCount() would export the 17408 subarrays, so scalars are asserted.
        $this->assertSame(17 * 1024, \count($result));
        $this->assertSame(0, $result[0][$position]);
        $this->assertSame(16, $result[17 * 1024 - 1][$position]);
        $this->assertLessThan(1024 * 1024, $allocated);
    }

    public static function dataProviderForReferencePosition(): array
    {
        return [
            'first' => [0],
            'second' => [1],
            'third' => [2],
            'eighth' => [7],
            'middle' => [8],
            'second to last' => [14],
            'last' => [15],
        ];
    }

    /**
     * Regression: seventeen shared subarrays of sixteen elements that hold the same reference slot and
     * differ only in one plain element, recurring in turn 1024 times, are each rebuilt once. Rebuilding
     * every occurrence would allocate about 7 MiB.
     *
     * @dataProvider dataProviderForPlainElementPosition
     * @test of rebuilds shared subarrays that differ in one plain element once when they recur in turn
     */
    public function testSharedSubarraysDifferingInOnePlainElementRecurringInTurnAreRebuiltOnce(int $position): void
    {
        // Given
        $common = 7;
        $blocks = [];
        for ($i = 0; $i < 17; $i++) {
            $block = \array_fill(0, 16, 0);
            $block[0] = &$common;
            $block[$position] = $i + 1;
            $blocks[] = $block;
            unset($block);
        }
        $row = [];
        for ($i = 0; $i < 1024; $i++) {
            foreach ($blocks as $block) {
                $row[] = $block;
            }
        }

        // When
        $before = \memory_get_usage();
        $result = Snapshot::of($row);
        $allocated = \memory_get_usage() - $before;
        $common = 8;

        // Then
        // assertIsArray() and assertCount() would export the 17408 subarrays, so scalars are asserted.
        $this->assertSame(17 * 1024, \count($result));
        $this->assertSame(7, $result[17 * 1024 - 1][0]);
        $this->assertSame(17, $result[17 * 1024 - 1][$position]);
        $this->assertSame(1, $result[0][$position]);
        $this->assertLessThan(1024 * 1024, $allocated);
    }

    public static function dataProviderForPlainElementPosition(): array
    {
        return [
            'second' => [1],
            'third' => [2],
            'eighth' => [7],
            'second to last' => [14],
        ];
    }

    /**
     * Regression: 64 shared subarrays that hold the same reference slot, each differing from the others
     * at a position of its own, so that no fixed set of positions tells them apart, recurring in turn 16
     * times, are each rebuilt once. Rebuilding every occurrence would allocate about 20 MiB.
     *
     * @test of rebuilds shared subarrays once when each differs from the others at a position of its own
     */
    public function testSharedSubarraysEachDifferingAtItsOwnPositionAreRebuiltOnce(): void
    {
        // Given
        $common = 0;
        $blocks = [];
        for ($i = 0; $i < 64; $i++) {
            $block = \array_fill(0, 1024, 0);
            $block[0] = &$common;
            $block[16 * $i + 1] = 1;
            $blocks[] = $block;
            unset($block);
        }
        $row = [];
        for ($i = 0; $i < 16; $i++) {
            foreach ($blocks as $block) {
                $row[] = $block;
            }
        }

        // When
        $before = \memory_get_usage();
        $result = Snapshot::of($row);
        $allocated = \memory_get_usage() - $before;
        $common = 1;

        // Then
        $this->assertSame(0, $result[64 * 16 - 1][0]);
        $this->assertSame(1, $result[64 * 16 - 1][16 * 63 + 1]);
        $this->assertSame(0, $result[64 * 16 - 1][16 * 62 + 1]);
        $this->assertLessThan(4 * 1024 * 1024, $allocated);
    }

    /**
     * Regression: pairs [$b, $b] of seventeen shared subarrays that differ only in a reference slot at an
     * unsampled position, recurring in turn 32 times, stay shared at both levels. Rebuilding every
     * occurrence would allocate about 11 MiB.
     *
     * @test of rebuilds nested shared subarrays once when their subarrays collide with distinct siblings
     */
    public function testNestedSharedSubarraysWithCollidingSiblingsAreRebuiltOnce(): void
    {
        // Given
        $slots = \range(0, 16);
        $pairs = [];
        foreach ($slots as &$slot) {
            $block = \array_fill(0, 1024, 0);
            $block[1] = &$slot;
            $pairs[] = [$block, $block];
        }
        unset($slot, $block);
        $row = [];
        for ($i = 0; $i < 32; $i++) {
            foreach ($pairs as $pair) {
                $row[] = $pair;
            }
        }

        // When
        $before = \memory_get_usage();
        $result = Snapshot::of($row);
        $allocated = \memory_get_usage() - $before;
        foreach ($slots as &$slot) {
            $slot = -1;
        }
        unset($slot);

        // Then
        $this->assertSame(0, $result[0][1][1]);
        $this->assertSame(16, $result[17 * 32 - 1][0][1]);
        $this->assertSame(16, $result[17 * 32 - 1][1][1]);
        $this->assertLessThan(2 * 1024 * 1024, $allocated);
    }

    /**
     * Regression: seventeen shared subarrays of 1024 float zeros, each with its own reference slot and a
     * -0.0 at a position of its own, recurring in turn 64 times, are each rebuilt once and keep their
     * signs. Rebuilding every occurrence would allocate about 21 MiB.
     *
     * @test of rebuilds shared subarrays of zeros once and keeps their signs when they recur in turn
     */
    public function testSharedSubarraysOfZerosRecurringInTurnAreRebuiltOnceWithTheirSigns(): void
    {
        // Given
        $slots = \range(0, 16);
        $blocks = [];
        foreach ($slots as $i => &$slot) {
            $block = \array_fill(0, 1024, 0.0);
            $block[1] = &$slot;
            $block[100 + $i] = -0.0;
            $blocks[] = $block;
        }
        unset($slot, $block);
        $row = [];
        for ($i = 0; $i < 64; $i++) {
            foreach ($blocks as $block) {
                $row[] = $block;
            }
        }

        // When
        $before = \memory_get_usage();
        $result = Snapshot::of($row);
        $allocated = \memory_get_usage() - $before;

        // Then
        $this->assertSame(-INF, \fdiv(1, $result[17 * 64 - 1][116]));
        $this->assertSame(INF, \fdiv(1, $result[17 * 64 - 1][115]));
        $this->assertSame(-INF, \fdiv(1, $result[17 * 64 - 2][115]));
        $this->assertSame(INF, \fdiv(1, $result[17 * 64 - 2][116]));
        $this->assertLessThan(2 * 1024 * 1024, $allocated);
    }

    /**
     * Equal subarrays in distinct storage whose zeros differ in sign only at an unsampled position, each
     * recurring in turn, keep their own signs.
     *
     * @test of keeps signs of zero in equal recurring subarrays that differ only at an unsampled position
     */
    public function testSignedZeroAtUnsampledPositionInRecurringSubarraysIsKept(): void
    {
        // Given
        $common = 1;
        $negative = \array_fill(0, 64, 0.0);
        $negative[0] = &$common;
        $negative[5] = -0.0;
        $positive = \array_fill(0, 64, 0.0);
        $positive[0] = &$common;
        $row = [];
        for ($i = 0; $i < 32; $i++) {
            $row[] = $positive;
            $row[] = $negative;
        }

        // When
        $result = Snapshot::of($row);

        // Then
        $this->assertSame(INF, \fdiv(1, $result[62][5]));
        $this->assertSame(-INF, \fdiv(1, $result[63][5]));
        $this->assertSame(INF, \fdiv(1, $result[63][6]));
    }

    /**
     * Subarrays with a reference slot that differ only in which closed handle they hold keep their own
     * handles: a closed handle is no longer \is_resource(), but it keeps its identity.
     *
     * @test of keeps distinct closed resources in subarrays that differ only in them
     */
    public function testDistinctClosedResourcesInSubarraysAreKept(): void
    {
        // Given
        $common = 1;
        $first = \fopen('php://memory', 'r');
        $second = \fopen('php://memory', 'r');
        \fclose($first);
        \fclose($second);

        // When
        $result = Snapshot::of([[&$common, $first], [&$common, $second]]);

        // Then
        $this->assertSame($first, $result[0][1]);
        $this->assertSame($second, $result[1][1]);
    }

    /**
     * A subarray equal to an earlier one without reference slots, but holding a reference slot itself,
     * has the slot broken: `===` reads through reference slots, so equality alone does not show that a
     * subarray can be kept as it is.
     *
     * @test of breaks a reference slot in a subarray equal to an earlier one without reference slots
     */
    public function testReferenceSlotInSubarrayEqualToEarlierOneWithoutReferencesIsBroken(): void
    {
        // Given
        $zero = 0;
        $free = \array_fill(0, 64, 0);
        $withReference = \array_fill(0, 64, 0);
        $withReference[5] = &$zero;

        // When
        $result = Snapshot::of([[$free], [$withReference]]);
        $zero = 1;

        // Then
        $this->assertSame(0, $result[1][0][5]);
        $this->assertNull(\ReflectionReference::fromArrayElement($result[1][0], 5));
    }

    /**
     * Regression: an array that refers to itself through two reference slots has 2^256 paths down to the
     * depth limit, which rebuilding each would never finish. The levels above the limit are rebuilt, and
     * the array below it kept as it is.
     *
     * @test of ends for an array that refers to itself through two reference slots
     */
    public function testSelfReferentialArrayWithTwoSlotsEnds(): void
    {
        // Given
        $array = [];
        $array[0] = &$array;
        $array[1] = &$array;

        // When
        $before = \memory_get_usage();
        $result = Snapshot::of($array);
        $allocated = \memory_get_usage() - $before;

        // Then
        $levels = 0;
        $node = $result;
        while (\ReflectionReference::fromArrayElement($node, 1) === null) {
            $node = $node[1];
            $levels++;
        }
        $this->assertSame(UniqueExtractor::MAX_DEPTH, $levels);
        $this->assertNull(\ReflectionReference::fromArrayElement($result[0][1][0], 0));
        $this->assertLessThan(1024 * 1024, $allocated);
    }

    /**
     * Regression: PHP may swap the operands of `===`, which throws when it meets an array it is already
     * comparing on its left side, so comparing a self-referential subarray with an earlier one of the same
     * size and fingerprint threw "Nesting level too deep".
     *
     * @test of ends for a self-referential subarray after a subarray of the same size
     */
    public function testSelfReferentialSubarrayAfterSubarrayOfSameSizeEnds(): void
    {
        // Given
        $id = 1;
        $array = [];
        $array[0] = &$array;

        // When
        $result = Snapshot::of([[[&$id]], $array]);
        $id = 2;

        // Then
        $this->assertSame(1, $result[0][0][0]);
        $this->assertNull(\ReflectionReference::fromArrayElement($result[1], 0));
    }

    /**
     * Regression: a shared subarray rebuilt where it fits under the depth limit is not reused where it
     * reaches past it, since a rebuild there keeps the reference slots below the limit.
     *
     * @test of keeps the reference slots below the depth limit of a shared subarray rebuilt above it
     */
    public function testSharedSubarrayReachingPastDepthLimitKeepsReferencesBelowIt(): void
    {
        // Given
        $id = 1;
        $shared = [&$id];
        for ($i = 0; $i < 9; $i++) {
            $shared = [$shared];
        }
        $wrapped = $shared;
        for ($i = 0; $i < 250; $i++) {
            $wrapped = [$wrapped];
        }

        // When
        $result = Snapshot::of([$shared, $wrapped]);

        // Then
        $shallow = $result[0];
        for ($i = 0; $i < 9; $i++) {
            $shallow = $shallow[0];
        }
        $deep = $result[1];
        for ($i = 0; $i < 259; $i++) {
            $deep = $deep[0];
        }
        $this->assertNull(\ReflectionReference::fromArrayElement($shallow, 0));
        $this->assertNotNull(\ReflectionReference::fromArrayElement($deep, 0));
    }
}

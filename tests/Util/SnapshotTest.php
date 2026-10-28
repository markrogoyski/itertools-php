<?php

declare(strict_types=1);

namespace IterTools\Tests\Util;

use IterTools\Util\Snapshot;

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
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamSmallestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::smallest example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [3, 1, 4, 1, 5, 9, 2, 6];

        // When
        $result = Stream::of($data)
            ->smallest(3)
            ->toArray();

        // Then
        $this->assertSame([1, 1, 2], $result);
    }

    /**
     * @test Stream::smallest with keyFn
     */
    public function testWithKeyFn(): void
    {
        // Given
        $requests = [
            (object)['id' => 'r1', 'durationMs' => 120],
            (object)['id' => 'r2', 'durationMs' => 50],
            (object)['id' => 'r3', 'durationMs' => 200],
            (object)['id' => 'r4', 'durationMs' => 80],
        ];

        // When
        $result = Stream::of($requests)
            ->smallest(2, fn ($r) => $r->durationMs)
            ->map(fn ($r) => $r->id)
            ->toArray();

        // Then
        $this->assertSame(['r2', 'r4'], $result);
    }

    /**
     * @test Stream::smallest with empty source
     */
    public function testEmptySource(): void
    {
        // When
        $result = Stream::of([])
            ->smallest(3)
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::smallest with n = 0
     */
    public function testZeroN(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4, 5])
            ->smallest(0)
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }
}

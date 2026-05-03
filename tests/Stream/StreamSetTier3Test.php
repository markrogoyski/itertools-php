<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamSetTier3Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @test duplicates delegates to Set::duplicates
     */
    public function testDuplicates(): void
    {
        // When
        $result = Stream::of([1, 2, 1, 1, 2, 3])->duplicates()->toArray();

        // Then
        $this->assertSame([1, 2], $result);
    }

    /**
     * @test duplicates composes with upstream operations
     */
    public function testDuplicatesAfterMap(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4])
            ->map(fn (int $n): int => $n % 2)
            ->duplicates()
            ->toArray();

        // Then
        $this->assertSame([1, 0], $result);
    }

    /**
     * @test duplicatesBy delegates to Set::duplicatesBy
     */
    public function testDuplicatesBy(): void
    {
        // Given
        $data = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 1],
        ];

        // When
        $result = Stream::of($data)
            ->duplicatesBy(fn (array $x): int => $x['id'])
            ->toArray();

        // Then
        $this->assertSame([['id' => 1]], $result);
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamSortTier3Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @test shuffle delegates to Sort::shuffle (multiset preservation)
     */
    public function testShuffle(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5, 6, 7];

        // When
        $result = Stream::of($data)->shuffle()->toArray();

        // Then
        $this->assertCount(\count($data), $result);
        $sorted = $result;
        \sort($sorted);
        $this->assertSame($data, $sorted);
    }

    /**
     * @test sample delegates to Random::sample
     */
    public function testSample(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5];

        // When
        $result = Stream::of($data)->sample(3)->toArray();

        // Then
        $this->assertCount(3, $result);
        foreach ($result as $item) {
            $this->assertContains($item, $data);
        }
    }

    /**
     * @test sample throws on oversize
     */
    public function testSampleOversizeThrows(): void
    {
        // Then
        $this->expectException(\LengthException::class);

        // When
        Stream::of([1, 2, 3])->sample(4)->toArray();
    }

    /**
     * @test shuffle composes with upstream filter
     */
    public function testShuffleAfterFilter(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4, 5, 6])
            ->filter(fn (int $n): bool => $n % 2 === 0)
            ->shuffle()
            ->toArray();

        // Then
        $sorted = $result;
        \sort($sorted);
        $this->assertSame([2, 4, 6], $sorted);
    }
}

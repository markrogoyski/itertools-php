<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamAsortByTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::asortBy example usage with associative score table
     */
    public function testExampleUsage(): void
    {
        // Given
        $scores = [
            'Alice' => 87,
            'Bob'   => 92,
            'Carol' => 75,
        ];

        // When
        $result = Stream::of($scores)
            ->asortBy(fn (int $score) => $score)
            ->toAssociativeArray();

        // Then
        $this->assertSame(['Carol' => 75, 'Alice' => 87, 'Bob' => 92], $result);
    }

    /**
     * @test Stream::asortBy preserves keys for associative input
     */
    public function testPreservesKeys(): void
    {
        // Given
        $people = [
            'alice' => (object)['age' => 30],
            'bob'   => (object)['age' => 20],
            'carol' => (object)['age' => 40],
        ];

        // When
        $result = Stream::of($people)
            ->asortBy(fn ($p) => $p->age)
            ->toAssociativeArray();

        // Then
        $this->assertSame(['bob', 'alice', 'carol'], \array_keys($result));
    }

    /**
     * @test Stream::asortBy on empty source
     */
    public function testEmptySource(): void
    {
        // When
        $result = Stream::of([])
            ->asortBy(fn ($x) => $x)
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }
}

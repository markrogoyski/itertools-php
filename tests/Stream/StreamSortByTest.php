<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamSortByTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::sortBy example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = ['banana', 'fig', 'cherry', 'apple'];

        // When
        $result = Stream::of($data)
            ->sortBy(fn (string $s) => \strlen($s))
            ->toArray();

        // Then
        $this->assertSame(['fig', 'apple', 'banana', 'cherry'], $result);
    }

    /**
     * @test Stream::sortBy on objects sorted by property
     */
    public function testSortObjectsByProperty(): void
    {
        // Given
        $people = [
            (object)['name' => 'Alice', 'age' => 30],
            (object)['name' => 'Bob',   'age' => 20],
            (object)['name' => 'Carol', 'age' => 40],
        ];

        // When
        $result = Stream::of($people)
            ->sortBy(fn ($p) => $p->age)
            ->map(fn ($p) => $p->name)
            ->toArray();

        // Then
        $this->assertSame(['Bob', 'Alice', 'Carol'], $result);
    }

    /**
     * @test Stream::sortBy on associative input discards keys
     */
    public function testAssociativeInputDiscardsKeys(): void
    {
        // Given
        $data = ['c' => 3, 'a' => 1, 'b' => 2];

        // When
        $result = Stream::of($data)
            ->sortBy(fn (int $x) => $x)
            ->toArray();

        // Then
        $this->assertSame([1, 2, 3], $result);
    }

    /**
     * @test Stream::sortBy on empty source
     */
    public function testEmptySource(): void
    {
        // When
        $result = Stream::of([])
            ->sortBy(fn ($x) => $x)
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }
}

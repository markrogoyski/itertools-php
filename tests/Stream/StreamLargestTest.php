<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamLargestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::largest example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [3, 1, 4, 1, 5, 9, 2, 6];

        // When
        $result = Stream::of($data)
            ->largest(3)
            ->toArray();

        // Then
        $this->assertSame([9, 6, 5], $result);
    }

    /**
     * @test Stream::largest with keyFn
     */
    public function testWithKeyFn(): void
    {
        // Given
        $people = [
            (object)['name' => 'Alice', 'age' => 30],
            (object)['name' => 'Bob',   'age' => 20],
            (object)['name' => 'Carol', 'age' => 40],
            (object)['name' => 'Dave',  'age' => 35],
        ];

        // When
        $result = Stream::of($people)
            ->largest(2, fn ($p) => $p->age)
            ->map(fn ($p) => $p->name)
            ->toArray();

        // Then
        $this->assertSame(['Carol', 'Dave'], $result);
    }

    /**
     * @test Stream::largest with empty source
     */
    public function testEmptySource(): void
    {
        // When
        $result = Stream::of([])
            ->largest(3)
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::largest with n = 0
     */
    public function testZeroN(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4, 5])
            ->largest(0)
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }
}

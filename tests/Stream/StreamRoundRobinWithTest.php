<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamRoundRobinWithTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test Stream::roundRobinWith example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = Stream::of($data)
            ->roundRobinWith([4, 5, 6])
            ->toArray();

        // Then
        $this->assertSame([1, 4, 2, 5, 3, 6], $result);
    }

    /**
     * @test Stream::roundRobinWith with unequal lengths
     */
    public function testUnequalLengths(): void
    {
        // Given
        $data = [1, 2, 3, 4];

        // When
        $result = Stream::of($data)
            ->roundRobinWith([10, 20])
            ->toArray();

        // Then
        $this->assertSame([1, 10, 2, 20, 3, 4], $result);
    }

    /**
     * @test Stream::roundRobinWith with multiple iterables
     */
    public function testMultipleIterables(): void
    {
        // Given
        $data = ['A', 'B', 'C'];

        // When
        $result = Stream::of($data)
            ->roundRobinWith(['D', 'E'], ['F', 'G', 'H'])
            ->toArray();

        // Then
        $this->assertSame(['A', 'D', 'F', 'B', 'E', 'G', 'C', 'H'], $result);
    }

    /**
     * @test Stream::roundRobinWith with no extra iterables
     */
    public function testNoExtraIterables(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = Stream::of($data)
            ->roundRobinWith()
            ->toArray();

        // Then
        $this->assertSame([1, 2, 3], $result);
    }

    /**
     * @test Stream::roundRobinWith on empty source
     */
    public function testEmptySource(): void
    {
        // When
        $result = Stream::of([])
            ->roundRobinWith([1, 2, 3])
            ->toArray();

        // Then
        $this->assertSame([1, 2, 3], $result);
    }

    /**
     * @test Stream::roundRobinWith with mixed iterable types
     */
    public function testMixedIterableTypes(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator(['a', 'b']);
        $iterator = new ArrayIteratorFixture([10, 20, 30]);
        $traversable = new IteratorAggregateFixture(['x']);

        // When
        $result = Stream::of([1, 2, 3])
            ->roundRobinWith($generator, $iterator, $traversable)
            ->toArray();

        // Then
        $this->assertSame([1, 'a', 10, 'x', 2, 'b', 20, 3, 30], $result);
    }

    /**
     * @test Stream::roundRobinWith chains with other operations
     */
    public function testChainableWithOtherOperations(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5];

        // When
        $result = Stream::of($data)
            ->filter(fn (int $n): bool => $n % 2 === 1)
            ->roundRobinWith([10, 20])
            ->map(fn (int $n): int => $n * 10)
            ->toArray();

        // Then
        // filter -> [1, 3, 5]; roundRobin with [10, 20] -> [1, 10, 3, 20, 5]; *10 -> [10, 100, 30, 200, 50]
        $this->assertSame([10, 100, 30, 200, 50], $result);
    }
}

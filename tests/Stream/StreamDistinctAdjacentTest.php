<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamDistinctAdjacentTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test Stream::distinctAdjacent example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 1, 2, 2, 3, 1, 1];

        // When
        $result = Stream::of($data)
            ->distinctAdjacent()
            ->toArray();

        // Then
        $this->assertSame([1, 2, 3, 1], $result);
    }

    /**
     * @test         Stream::distinctAdjacent (array)
     * @dataProvider dataProviderForDistinctAdjacent
     * @param        array<mixed> $data
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentArray(array $data, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->distinctAdjacent()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::distinctAdjacent (Generator)
     * @dataProvider dataProviderForDistinctAdjacent
     * @param        array<mixed> $data
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentGenerator(array $data, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($iterable)
            ->distinctAdjacent()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::distinctAdjacent (Iterator)
     * @dataProvider dataProviderForDistinctAdjacent
     * @param        array<mixed> $data
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentIterator(array $data, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iterable)
            ->distinctAdjacent()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::distinctAdjacent (IteratorAggregate)
     * @dataProvider dataProviderForDistinctAdjacent
     * @param        array<mixed> $data
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentIteratorAggregate(array $data, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)
            ->distinctAdjacent()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForDistinctAdjacent(): array
    {
        return [
            [[], []],
            [[1], [1]],
            [[1, 1, 1, 1], [1]],
            [[1, 2, 3], [1, 2, 3]],
            [[1, 1, 2, 2, 3, 1, 1], [1, 2, 3, 1]],
            [['a', 'a', 'b', 'a'], ['a', 'b', 'a']],
        ];
    }

    /**
     * @test         Stream::distinctAdjacent on empty input
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmpty(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->distinctAdjacent()
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::distinctAdjacent is chainable with other stream operations
     */
    public function testChainableWithOtherOperations(): void
    {
        // Given
        $data = [1, 1, 2, 2, 3, 3, 4, 4, 5, 5];

        // When
        $result = Stream::of($data)
            ->distinctAdjacent()
            ->map(fn (int $n): int => $n * 10)
            ->toArray();

        // Then
        $this->assertSame([10, 20, 30, 40, 50], $result);
    }
}

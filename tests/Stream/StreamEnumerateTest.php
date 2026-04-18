<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamEnumerateTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test Stream::enumerate example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $words = ['apple', 'banana', 'cherry'];

        // When
        $result = Stream::of($words)
            ->enumerate()
            ->toArray();

        // Then
        $this->assertSame([[0, 'apple'], [1, 'banana'], [2, 'cherry']], $result);
    }

    /**
     * @test         Stream::enumerate (array)
     * @dataProvider dataProviderForEnumerate
     * @param        array<mixed> $data
     * @param        int          $start
     * @param        array<array{int, mixed}> $expected
     */
    public function testEnumerateArray(array $data, int $start, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->enumerate($start)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::enumerate (Generator)
     * @dataProvider dataProviderForEnumerate
     * @param        array<mixed> $data
     * @param        int          $start
     * @param        array<array{int, mixed}> $expected
     */
    public function testEnumerateGenerator(array $data, int $start, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($iterable)
            ->enumerate($start)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::enumerate (Iterator)
     * @dataProvider dataProviderForEnumerate
     * @param        array<mixed> $data
     * @param        int          $start
     * @param        array<array{int, mixed}> $expected
     */
    public function testEnumerateIterator(array $data, int $start, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iterable)
            ->enumerate($start)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::enumerate (IteratorAggregate)
     * @dataProvider dataProviderForEnumerate
     * @param        array<mixed> $data
     * @param        int          $start
     * @param        array<array{int, mixed}> $expected
     */
    public function testEnumerateIteratorAggregate(array $data, int $start, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)
            ->enumerate($start)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForEnumerate(): array
    {
        return [
            // single element, default start
            [['only'], 0, [[0, 'only']]],
            // multiple elements, default start
            [['a', 'b', 'c'], 0, [[0, 'a'], [1, 'b'], [2, 'c']]],
            // explicit start = 1
            [['x', 'y', 'z'], 1, [[1, 'x'], [2, 'y'], [3, 'z']]],
            // negative start
            [['a', 'b', 'c'], -2, [[-2, 'a'], [-1, 'b'], [0, 'c']]],
        ];
    }

    /**
     * @test         Stream::enumerate empty stream yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyStream(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->enumerate()
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::enumerate is chainable with other stream operations
     */
    public function testChainableWithOtherOperations(): void
    {
        // Given
        $data = [10, 20, 30, 40, 50];

        // When
        $result = Stream::of($data)
            ->filter(fn (int $n): bool => $n >= 20)
            ->enumerate(1)
            ->toArray();

        // Then
        $this->assertSame([[1, 20], [2, 30], [3, 40], [4, 50]], $result);
    }

    /**
     * @test Stream::enumerate index independent of source keys
     */
    public function testIndexIndependentOfSourceKeys(): void
    {
        // Given
        $data = ['x' => 'apple', 'y' => 'banana', 'z' => 'cherry'];

        // When
        $result = Stream::of($data)
            ->enumerate()
            ->toArray();

        // Then
        $this->assertSame([[0, 'apple'], [1, 'banana'], [2, 'cherry']], $result);
    }
}

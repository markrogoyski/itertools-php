<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamAccumulateTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test Stream::accumulate example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $numbers = [1, 2, 3, 4, 5];

        // When
        $result = Stream::of($numbers)
            ->accumulate(fn ($a, $b) => $a + $b)
            ->toArray();

        // Then
        $this->assertSame([1, 3, 6, 10, 15], $result);
    }

    /**
     * @test         Stream::accumulate without initial (array)
     * @dataProvider dataProviderForAccumulate
     * @param        array<mixed> $data
     * @param        callable     $op
     * @param        array<mixed> $expected
     */
    public function testAccumulateArray(array $data, callable $op, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->accumulate($op)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::accumulate without initial (Generator)
     * @dataProvider dataProviderForAccumulate
     * @param        array<mixed> $data
     * @param        callable     $op
     * @param        array<mixed> $expected
     */
    public function testAccumulateGenerator(array $data, callable $op, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($iterable)
            ->accumulate($op)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::accumulate without initial (Iterator)
     * @dataProvider dataProviderForAccumulate
     * @param        array<mixed> $data
     * @param        callable     $op
     * @param        array<mixed> $expected
     */
    public function testAccumulateIterator(array $data, callable $op, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iterable)
            ->accumulate($op)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::accumulate without initial (IteratorAggregate)
     * @dataProvider dataProviderForAccumulate
     * @param        array<mixed> $data
     * @param        callable     $op
     * @param        array<mixed> $expected
     */
    public function testAccumulateIteratorAggregate(array $data, callable $op, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)
            ->accumulate($op)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForAccumulate(): array
    {
        $sum  = fn ($a, $b) => $a + $b;
        $prod = fn ($a, $b) => $a * $b;
        $cat  = fn ($a, $b) => $a . $b;

        return [
            [[7], $sum, [7]],
            [[1, 2, 3, 4, 5], $sum, [1, 3, 6, 10, 15]],
            [[1, 2, 3, 4, 5], $prod, [1, 2, 6, 24, 120]],
            [['a', 'b', 'c'], $cat, ['a', 'ab', 'abc']],
        ];
    }

    /**
     * @test Stream::accumulate with initial value
     */
    public function testAccumulateWithInitial(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5];

        // When
        $result = Stream::of($data)
            ->accumulate(fn ($a, $b) => $a + $b, 100)
            ->toArray();

        // Then
        $this->assertSame([100, 101, 103, 106, 110, 115], $result);
    }

    /**
     * @test Stream::accumulate treats explicit null as a legitimate initial value
     */
    public function testAccumulateWithExplicitNullInitial(): void
    {
        // Given
        $data = [1, 2, 3];
        $op   = fn (mixed $a, int $b): int => ($a ?? 0) + $b;

        // When
        $result = Stream::of($data)
            ->accumulate($op, null)
            ->toArray();

        // Then
        $this->assertSame([null, 1, 3, 6], $result);
    }

    /**
     * @test         Stream::accumulate empty stream without initial yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyStreamNoInitial(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->accumulate(fn ($a, $b) => $a + $b)
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test         Stream::accumulate empty stream with initial yields only the initial
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyStreamWithInitial(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->accumulate(fn ($a, $b) => $a + $b, 42)
            ->toArray();

        // Then
        $this->assertSame([42], $result);
    }

    /**
     * @test Stream::accumulate is chainable with other stream operations
     */
    public function testChainableWithOtherOperations(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5, 6];

        // When
        $result = Stream::of($data)
            ->filter(fn (int $n): bool => $n % 2 === 0)
            ->accumulate(fn ($a, $b) => $a + $b)
            ->toArray();

        // Then
        // even numbers: [2, 4, 6]; running sum: [2, 6, 12]
        $this->assertSame([2, 6, 12], $result);
    }

    /**
     * @test Stream::accumulate throws InvalidArgumentException when more than one initial is given
     */
    public function testTwoInitialsThrows(): void
    {
        // Given
        $stream = Stream::of([1, 2, 3])
            ->accumulate(fn ($a, $b) => $a + $b, 0, 1);

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        $stream->toArray();
    }
}

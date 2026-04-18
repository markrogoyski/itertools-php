<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamToPartitionTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test Stream::toPartition example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $numbers = [1, 2, 3, 4, 5, 6];

        // When
        [$evens, $odds] = Stream::of($numbers)
            ->toPartition(fn (int $n): bool => $n % 2 === 0);

        // Then
        $this->assertSame([2, 4, 6], $evens);
        $this->assertSame([1, 3, 5], $odds);
    }

    /**
     * @test         Stream::toPartition (array)
     * @dataProvider dataProviderForPartition
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        array{0: array<mixed>, 1: array<mixed>} $expected
     */
    public function testPartitionArray(array $data, callable $predicate, array $expected): void
    {
        // When
        $result = Stream::of($data)->toPartition($predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::toPartition (Generator)
     * @dataProvider dataProviderForPartition
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        array{0: array<mixed>, 1: array<mixed>} $expected
     */
    public function testPartitionGenerator(array $data, callable $predicate, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($iterable)->toPartition($predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::toPartition (Iterator)
     * @dataProvider dataProviderForPartition
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        array{0: array<mixed>, 1: array<mixed>} $expected
     */
    public function testPartitionIterator(array $data, callable $predicate, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iterable)->toPartition($predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::toPartition (IteratorAggregate)
     * @dataProvider dataProviderForPartition
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        array{0: array<mixed>, 1: array<mixed>} $expected
     */
    public function testPartitionIteratorAggregate(array $data, callable $predicate, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)->toPartition($predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForPartition(): array
    {
        $isEven = fn (int $n): bool => $n % 2 === 0;
        $truthy = fn (mixed $v): mixed => $v;

        return [
            // all truthy
            [[2, 4, 6], $isEven, [[2, 4, 6], []]],
            // all falsy
            [[1, 3, 5], $isEven, [[], [1, 3, 5]]],
            // mixed — preserves input order on each side
            [[1, 2, 3, 4, 5, 6], $isEven, [[2, 4, 6], [1, 3, 5]]],
            // coercion — 0, '', null, [] are falsy
            [[1, 0, 'x', '', 'y', null, [1], []], $truthy, [[1, 'x', 'y', [1]], [0, '', null, []]]],
        ];
    }

    /**
     * @test         Stream::toPartition empty stream returns two empty arrays
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyStream(iterable $data): void
    {
        // When
        $result = Stream::of($data)->toPartition(fn (mixed $x): bool => (bool) $x);

        // Then
        $this->assertSame([[], []], $result);
    }

    /**
     * @test Stream::toPartition composes with upstream chainable operations
     */
    public function testComposesWithUpstreamOperations(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        // When
        [$even, $odd] = Stream::of($data)
            ->filter(fn (int $n): bool => $n >= 3)
            ->map(fn (int $n): int => $n * 10)
            ->toPartition(fn (int $n): bool => $n % 20 === 0);

        // Then
        $this->assertSame([40, 60, 80, 100], $even);
        $this->assertSame([30, 50, 70, 90], $odd);
    }
}

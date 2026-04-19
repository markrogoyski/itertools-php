<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamPermutationsTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test Stream::permutations example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = Stream::of($data)
            ->permutations()
            ->toArray();

        // Then
        $this->assertSame(
            [
                [1, 2, 3],
                [1, 3, 2],
                [2, 1, 3],
                [2, 3, 1],
                [3, 1, 2],
                [3, 2, 1],
            ],
            $result
        );
    }

    /**
     * @test         Stream::permutations (array)
     * @dataProvider dataProviderForPermutations
     * @param        array<mixed>        $data
     * @param        int|null            $r
     * @param        array<array<mixed>> $expected
     */
    public function testPermutationsArray(array $data, ?int $r, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->permutations($r)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::permutations (Generator)
     * @dataProvider dataProviderForPermutations
     * @param        array<mixed>        $data
     * @param        int|null            $r
     * @param        array<array<mixed>> $expected
     */
    public function testPermutationsGenerator(array $data, ?int $r, array $expected): void
    {
        // Given
        $gen = GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($gen)
            ->permutations($r)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::permutations (Iterator)
     * @dataProvider dataProviderForPermutations
     * @param        array<mixed>        $data
     * @param        int|null            $r
     * @param        array<array<mixed>> $expected
     */
    public function testPermutationsIterator(array $data, ?int $r, array $expected): void
    {
        // Given
        $iter = new ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iter)
            ->permutations($r)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::permutations (IteratorAggregate)
     * @dataProvider dataProviderForPermutations
     * @param        array<mixed>        $data
     * @param        int|null            $r
     * @param        array<array<mixed>> $expected
     */
    public function testPermutationsIteratorAggregate(array $data, ?int $r, array $expected): void
    {
        // Given
        $agg = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($agg)
            ->permutations($r)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForPermutations(): array
    {
        return [
            // full-length permutations of two elements
            [
                [1, 2],
                null,
                [[1, 2], [2, 1]],
            ],
            // $r less than length
            [
                [1, 2, 3],
                2,
                [
                    [1, 2], [1, 3],
                    [2, 1], [2, 3],
                    [3, 1], [3, 2],
                ],
            ],
            // $r = 0 — one empty tuple
            [
                [1, 2, 3],
                0,
                [[]],
            ],
            // $r greater than length → empty
            [
                [1, 2],
                3,
                [],
            ],
            // duplicates are position-unique
            [
                [1, 1],
                null,
                [[1, 1], [1, 1]],
            ],
        ];
    }

    /**
     * @test         Stream::permutations with empty iterable and default $r yields one empty tuple
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableDefaultR(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->permutations()
            ->toArray();

        // Then
        $this->assertSame([[]], $result);
    }

    /**
     * @test         Stream::permutations with empty iterable and positive $r yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterablePositiveR(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->permutations(2)
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::permutations throws on negative $r
     */
    public function testNegativeRThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of([1, 2, 3])
            ->permutations(-1)
            ->toArray();
    }

    /**
     * @test Stream::permutations chains with other stream operations
     */
    public function testChainableWithOtherOperations(): void
    {
        // Given
        $source = [1, 2, 3, 4];

        // When
        $result = Stream::of($source)
            ->filter(fn (int $n): bool => $n % 2 === 0)
            ->permutations()
            ->toArray();

        // Then
        $this->assertSame(
            [
                [2, 4],
                [4, 2],
            ],
            $result
        );
    }
}

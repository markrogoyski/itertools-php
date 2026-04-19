<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamCombinationsTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test Stream::combinations example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2, 3, 4];

        // When
        $result = Stream::of($data)
            ->combinations(2)
            ->toArray();

        // Then
        $this->assertSame(
            [
                [1, 2],
                [1, 3],
                [1, 4],
                [2, 3],
                [2, 4],
                [3, 4],
            ],
            $result
        );
    }

    /**
     * @test         Stream::combinations (array)
     * @dataProvider dataProviderForCombinations
     * @param        array<mixed>        $data
     * @param        int                 $r
     * @param        array<array<mixed>> $expected
     */
    public function testCombinationsArray(array $data, int $r, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->combinations($r)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::combinations (Generator)
     * @dataProvider dataProviderForCombinations
     * @param        array<mixed>        $data
     * @param        int                 $r
     * @param        array<array<mixed>> $expected
     */
    public function testCombinationsGenerator(array $data, int $r, array $expected): void
    {
        // Given
        $gen = GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($gen)
            ->combinations($r)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::combinations (Iterator)
     * @dataProvider dataProviderForCombinations
     * @param        array<mixed>        $data
     * @param        int                 $r
     * @param        array<array<mixed>> $expected
     */
    public function testCombinationsIterator(array $data, int $r, array $expected): void
    {
        // Given
        $iter = new ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iter)
            ->combinations($r)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::combinations (IteratorAggregate)
     * @dataProvider dataProviderForCombinations
     * @param        array<mixed>        $data
     * @param        int                 $r
     * @param        array<array<mixed>> $expected
     */
    public function testCombinationsIteratorAggregate(array $data, int $r, array $expected): void
    {
        // Given
        $agg = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($agg)
            ->combinations($r)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForCombinations(): array
    {
        return [
            // two elements, r = 2
            [
                [1, 2],
                2,
                [[1, 2]],
            ],
            // three elements, r = 2
            [
                [1, 2, 3],
                2,
                [
                    [1, 2],
                    [1, 3],
                    [2, 3],
                ],
            ],
            // $r = 0 — one empty tuple
            [
                [1, 2, 3],
                0,
                [[]],
            ],
            // $r > count → empty
            [
                [1, 2],
                3,
                [],
            ],
            // duplicates are position-unique
            [
                [1, 1],
                2,
                [[1, 1]],
            ],
        ];
    }

    /**
     * @test         Stream::combinations with empty iterable and $r = 0 yields one empty tuple
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableRZero(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->combinations(0)
            ->toArray();

        // Then
        $this->assertSame([[]], $result);
    }

    /**
     * @test         Stream::combinations with empty iterable and positive $r yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterablePositiveR(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->combinations(2)
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::combinations throws on negative $r
     */
    public function testNegativeRThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of([1, 2, 3])
            ->combinations(-1)
            ->toArray();
    }

    /**
     * @test Stream::combinations chains with other stream operations
     */
    public function testChainableWithOtherOperations(): void
    {
        // Given
        $source = [1, 2, 3, 4];

        // When
        $result = Stream::of($source)
            ->filter(fn (int $n): bool => $n % 2 === 0)
            ->combinations(2)
            ->toArray();

        // Then
        $this->assertSame(
            [
                [2, 4],
            ],
            $result
        );
    }
}

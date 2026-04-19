<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamCombinationsWithReplacementTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test Stream::combinationsWithReplacement example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = Stream::of($data)
            ->combinationsWithReplacement(2)
            ->toArray();

        // Then
        $this->assertSame(
            [
                [1, 1],
                [1, 2],
                [1, 3],
                [2, 2],
                [2, 3],
                [3, 3],
            ],
            $result
        );
    }

    /**
     * @test         Stream::combinationsWithReplacement (array)
     * @dataProvider dataProviderForCombinationsWithReplacement
     * @param        array<mixed>        $data
     * @param        int                 $r
     * @param        array<array<mixed>> $expected
     */
    public function testCombinationsWithReplacementArray(array $data, int $r, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->combinationsWithReplacement($r)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::combinationsWithReplacement (Generator)
     * @dataProvider dataProviderForCombinationsWithReplacement
     * @param        array<mixed>        $data
     * @param        int                 $r
     * @param        array<array<mixed>> $expected
     */
    public function testCombinationsWithReplacementGenerator(array $data, int $r, array $expected): void
    {
        // Given
        $gen = GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($gen)
            ->combinationsWithReplacement($r)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::combinationsWithReplacement (Iterator)
     * @dataProvider dataProviderForCombinationsWithReplacement
     * @param        array<mixed>        $data
     * @param        int                 $r
     * @param        array<array<mixed>> $expected
     */
    public function testCombinationsWithReplacementIterator(array $data, int $r, array $expected): void
    {
        // Given
        $iter = new ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iter)
            ->combinationsWithReplacement($r)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::combinationsWithReplacement (IteratorAggregate)
     * @dataProvider dataProviderForCombinationsWithReplacement
     * @param        array<mixed>        $data
     * @param        int                 $r
     * @param        array<array<mixed>> $expected
     */
    public function testCombinationsWithReplacementIteratorAggregate(array $data, int $r, array $expected): void
    {
        // Given
        $agg = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($agg)
            ->combinationsWithReplacement($r)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForCombinationsWithReplacement(): array
    {
        return [
            // two elements, r = 2
            [
                [1, 2],
                2,
                [
                    [1, 1],
                    [1, 2],
                    [2, 2],
                ],
            ],
            // three elements, r = 2
            [
                [1, 2, 3],
                2,
                [
                    [1, 1],
                    [1, 2],
                    [1, 3],
                    [2, 2],
                    [2, 3],
                    [3, 3],
                ],
            ],
            // $r larger than count
            [
                [1, 2],
                3,
                [
                    [1, 1, 1],
                    [1, 1, 2],
                    [1, 2, 2],
                    [2, 2, 2],
                ],
            ],
            // $r = 0 — one empty tuple
            [
                [1, 2, 3],
                0,
                [[]],
            ],
            // duplicates produce duplicate output tuples
            [
                [1, 1],
                2,
                [
                    [1, 1],
                    [1, 1],
                    [1, 1],
                ],
            ],
        ];
    }

    /**
     * @test         Stream::combinationsWithReplacement with empty iterable and $r = 0 yields one empty tuple
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableRZero(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->combinationsWithReplacement(0)
            ->toArray();

        // Then
        $this->assertSame([[]], $result);
    }

    /**
     * @test         Stream::combinationsWithReplacement with empty iterable and positive $r yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterablePositiveR(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->combinationsWithReplacement(2)
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::combinationsWithReplacement throws on negative $r
     */
    public function testNegativeRThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of([1, 2, 3])
            ->combinationsWithReplacement(-1)
            ->toArray();
    }

    /**
     * @test Stream::combinationsWithReplacement chains with other stream operations
     */
    public function testChainableWithOtherOperations(): void
    {
        // Given
        $source = [1, 2, 3, 4];

        // When
        $result = Stream::of($source)
            ->filter(fn (int $n): bool => $n % 2 === 0)
            ->combinationsWithReplacement(2)
            ->toArray();

        // Then
        $this->assertSame(
            [
                [2, 2],
                [2, 4],
                [4, 4],
            ],
            $result
        );
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamZipTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    // ---------------------------------------------------------------------
    // Stream::zip — happy path across iterable types
    // ---------------------------------------------------------------------

    /**
     * @test         Stream::zip (array)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipArray(array $rows, array $expected): void
    {
        // When
        $result = Stream::of($rows)
            ->zip()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::zip (Generator)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipGenerator(array $rows, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($rows);

        // When
        $result = Stream::of($iterable)
            ->zip()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::zip (Iterator)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipIterator(array $rows, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($rows);

        // When
        $result = Stream::of($iterable)
            ->zip()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::zip (IteratorAggregate)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipIteratorAggregate(array $rows, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($rows);

        // When
        $result = Stream::of($iterable)
            ->zip()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    // ---------------------------------------------------------------------
    // Stream::zipLongest — happy path across iterable types
    // ---------------------------------------------------------------------

    /**
     * @test         Stream::zipLongest (array)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipLongestArray(array $rows, array $expected): void
    {
        // When
        $result = Stream::of($rows)
            ->zipLongest()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::zipLongest (Generator)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipLongestGenerator(array $rows, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($rows);

        // When
        $result = Stream::of($iterable)
            ->zipLongest()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::zipLongest (Iterator)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipLongestIterator(array $rows, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($rows);

        // When
        $result = Stream::of($iterable)
            ->zipLongest()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::zipLongest (IteratorAggregate)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipLongestIteratorAggregate(array $rows, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($rows);

        // When
        $result = Stream::of($iterable)
            ->zipLongest()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    // ---------------------------------------------------------------------
    // Stream::zipFilled — happy path across iterable types
    // ---------------------------------------------------------------------

    /**
     * @test         Stream::zipFilled (array)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipFilledArray(array $rows, array $expected): void
    {
        // When
        $result = Stream::of($rows)
            ->zipFilled('filler')
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::zipFilled (Generator)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipFilledGenerator(array $rows, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($rows);

        // When
        $result = Stream::of($iterable)
            ->zipFilled('filler')
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::zipFilled (Iterator)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipFilledIterator(array $rows, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($rows);

        // When
        $result = Stream::of($iterable)
            ->zipFilled('filler')
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::zipFilled (IteratorAggregate)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipFilledIteratorAggregate(array $rows, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($rows);

        // When
        $result = Stream::of($iterable)
            ->zipFilled('filler')
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    // ---------------------------------------------------------------------
    // Stream::zipEqual — happy path across iterable types
    // ---------------------------------------------------------------------

    /**
     * @test         Stream::zipEqual (array)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipEqualArray(array $rows, array $expected): void
    {
        // When
        $result = Stream::of($rows)
            ->zipEqual()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::zipEqual (Generator)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipEqualGenerator(array $rows, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($rows);

        // When
        $result = Stream::of($iterable)
            ->zipEqual()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::zipEqual (Iterator)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipEqualIterator(array $rows, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($rows);

        // When
        $result = Stream::of($iterable)
            ->zipEqual()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::zipEqual (IteratorAggregate)
     * @dataProvider dataProviderForZipEqualLength
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testZipEqualIteratorAggregate(array $rows, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($rows);

        // When
        $result = Stream::of($iterable)
            ->zipEqual()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Shared data across all four variants — all rows are equal length
     * so every variant yields the same tuples.
     *
     * @return array<string, array{array<array<mixed>>, array<array<mixed>>}>
     */
    public static function dataProviderForZipEqualLength(): array
    {
        return [
            'single row'       => [[[1, 2, 3]], [[1], [2], [3]]],
            'two equal rows'   => [[[1, 2], [3, 4]], [[1, 3], [2, 4]]],
            'three equal rows' => [
                [[1, 2, 3], [4, 5, 6], [7, 8, 9]],
                [[1, 4, 7], [2, 5, 8], [3, 6, 9]],
            ],
            'string values'    => [
                [['a', 'b'], ['c', 'd']],
                [['a', 'c'], ['b', 'd']],
            ],
        ];
    }

    // ---------------------------------------------------------------------
    // Edge cases
    // ---------------------------------------------------------------------

    /**
     * @test         Stream::zip empty outer stream yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testZipEmptyOuterStream(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->zip()
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test         Stream::zipLongest empty outer stream yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testZipLongestEmptyOuterStream(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->zipLongest()
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test         Stream::zipFilled empty outer stream yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testZipFilledEmptyOuterStream(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->zipFilled('x')
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test         Stream::zipEqual empty outer stream yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testZipEqualEmptyOuterStream(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->zipEqual()
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::zip unequal row lengths stops at shortest
     */
    public function testZipUnequalRowLengthsStopsAtShortest(): void
    {
        // Given
        $rows = [[1, 2, 3], [4, 5]];

        // When
        $result = Stream::of($rows)
            ->zip()
            ->toArray();

        // Then
        $this->assertSame([[1, 4], [2, 5]], $result);
    }

    /**
     * @test Stream::zipLongest unequal row lengths fills with null
     */
    public function testZipLongestUnequalRowLengthsFillsWithNull(): void
    {
        // Given
        $rows = [[1, 2, 3], [4, 5]];

        // When
        $result = Stream::of($rows)
            ->zipLongest()
            ->toArray();

        // Then
        $this->assertSame([[1, 4], [2, 5], [3, null]], $result);
    }

    /**
     * @test Stream::zipFilled unequal row lengths fills with filler
     */
    public function testZipFilledUnequalRowLengthsFillsWithFiller(): void
    {
        // Given
        $rows = [[1, 2, 3], [4, 5]];

        // When
        $result = Stream::of($rows)
            ->zipFilled('x')
            ->toArray();

        // Then
        $this->assertSame([[1, 4], [2, 5], [3, 'x']], $result);
    }

    /**
     * @test Stream::zipEqual unequal row lengths throws \LengthException
     */
    public function testZipEqualUnequalRowLengthsThrows(): void
    {
        // Given
        $rows = [[1, 2, 3], [4, 5]];

        // Then
        $this->expectException(\LengthException::class);

        // When
        Stream::of($rows)
            ->zipEqual()
            ->toArray();
    }

    /**
     * @test Stream::zip empty inner row yields nothing
     */
    public function testZipEmptyInnerRowYieldsNothing(): void
    {
        // Given
        $rows = [[1, 2], []];

        // When
        $result = Stream::of($rows)
            ->zip()
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::zipLongest empty inner row contributes nulls
     */
    public function testZipLongestEmptyInnerRowContributesNulls(): void
    {
        // Given
        $rows = [[1, 2], []];

        // When
        $result = Stream::of($rows)
            ->zipLongest()
            ->toArray();

        // Then
        $this->assertSame([[1, null], [2, null]], $result);
    }

    /**
     * @test Stream::zipFilled empty inner row contributes filler
     */
    public function testZipFilledEmptyInnerRowContributesFiller(): void
    {
        // Given
        $rows = [[1, 2], []];

        // When
        $result = Stream::of($rows)
            ->zipFilled('x')
            ->toArray();

        // Then
        $this->assertSame([[1, 'x'], [2, 'x']], $result);
    }

    /**
     * @test Stream::zipEqual empty inner row throws \LengthException
     */
    public function testZipEqualEmptyInnerRowThrows(): void
    {
        // Given
        $rows = [[1, 2], []];

        // Then
        $this->expectException(\LengthException::class);

        // When
        Stream::of($rows)
            ->zipEqual()
            ->toArray();
    }

    /**
     * @test Stream::zip non-iterable element throws \InvalidArgumentException
     */
    public function testZipNonIterableElementThrows(): void
    {
        // Given
        $rows = [[1], 2];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of($rows)
            ->zip()
            ->toArray();
    }

    /**
     * @test Stream::zipLongest non-iterable element throws \InvalidArgumentException
     */
    public function testZipLongestNonIterableElementThrows(): void
    {
        // Given
        $rows = [[1], 2];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of($rows)
            ->zipLongest()
            ->toArray();
    }

    /**
     * @test Stream::zipFilled non-iterable element throws \InvalidArgumentException
     */
    public function testZipFilledNonIterableElementThrows(): void
    {
        // Given
        $rows = [[1], 2];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of($rows)
            ->zipFilled('x')
            ->toArray();
    }

    /**
     * @test Stream::zipEqual non-iterable element throws \InvalidArgumentException
     */
    public function testZipEqualNonIterableElementThrows(): void
    {
        // Given
        $rows = [[1], 2];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of($rows)
            ->zipEqual()
            ->toArray();
    }

    /**
     * @test Stream::zip deferred execution — call does nothing, iteration consumes outer
     */
    public function testZipDeferredExecution(): void
    {
        // Given
        $counter  = 0;
        $genMaker = static function () use (&$counter): \Generator {
            foreach ([[1, 2], [3, 4], [5, 6]] as $row) {
                $counter++;
                yield $row;
            }
        };

        // When
        $stream = Stream::of($genMaker())->zip();

        // Then — call alone did not consume the source
        $this->assertSame(0, $counter);

        // When
        $stream->toArray();

        // Then — iteration consumed all three outer rows
        $this->assertSame(3, $counter);
    }

    /**
     * @test Stream::zip non-iterable element timing — error deferred until iteration
     */
    public function testZipNonIterableDeferredUntilIteration(): void
    {
        // Given
        $stream = Stream::of([[1], 2])->zip();

        // When — no exception at call time
        $this->assertInstanceOf(Stream::class, $stream);

        // Then — exception only when iterated
        $this->expectException(\InvalidArgumentException::class);
        $stream->toArray();
    }

    /**
     * @test Stream::zip error message names zero-based positional row index (associative keys)
     */
    public function testZipRowIndexIsPositionalNotAssociativeKey(): void
    {
        // Given
        $rows = ['a' => [1], 'b' => 2];

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/row 1\b/');

        // When
        Stream::of($rows)
            ->zip()
            ->toArray();
    }

    /**
     * @test Stream::zip error message names zero-based positional row index (sparse numeric keys)
     */
    public function testZipRowIndexIsPositionalNotSparseNumericKey(): void
    {
        // Given
        $rows = [10 => [1], 20 => 2];

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/row 1\b/');

        // When
        Stream::of($rows)
            ->zip()
            ->toArray();
    }

    /**
     * @test Stream::zip error message does not use associative key for row index
     */
    public function testZipRowIndexDoesNotUseAssociativeKey(): void
    {
        // Given
        $rows = ['a' => [1], 'b' => 2];

        // When
        try {
            Stream::of($rows)
                ->zip()
                ->toArray();
            $this->fail('Expected \InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            // Then
            $this->assertStringNotContainsString('row b', $e->getMessage());
        }
    }

    /**
     * @test Stream::zip works with mixed inner iterable types
     */
    public function testZipMixedInnerIterableTypes(): void
    {
        // Given
        $rows = [
            [1, 2, 3],
            GeneratorFixture::getGenerator([4, 5, 6]),
            new ArrayIteratorFixture([7, 8, 9]),
            new IteratorAggregateFixture([10, 11, 12]),
        ];

        // When
        $result = Stream::of($rows)
            ->zip()
            ->toArray();

        // Then
        $this->assertSame(
            [[1, 4, 7, 10], [2, 5, 8, 11], [3, 6, 9, 12]],
            $result
        );
    }

    /**
     * @test Stream::zip composes with lazy upstream (chunkwise)
     */
    public function testZipChainableWithUpstream(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5, 6];

        // When — chunkwise(3) yields [[1,2,3],[4,5,6]]; zip transposes them
        $result = Stream::of($data)
            ->chunkwise(3)
            ->zip()
            ->toArray();

        // Then
        $this->assertSame([[1, 4], [2, 5], [3, 6]], $result);
    }
}

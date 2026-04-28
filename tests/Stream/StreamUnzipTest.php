<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamUnzipTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         Stream::unzip (array)
     * @dataProvider dataProviderForUnzip
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testUnzipArray(array $rows, array $expected): void
    {
        // When
        $result = Stream::of($rows)
            ->unzip()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::unzip (Generator)
     * @dataProvider dataProviderForUnzip
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testUnzipGenerator(array $rows, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($rows);

        // When
        $result = Stream::of($iterable)
            ->unzip()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::unzip (Iterator)
     * @dataProvider dataProviderForUnzip
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testUnzipIterator(array $rows, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($rows);

        // When
        $result = Stream::of($iterable)
            ->unzip()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::unzip (IteratorAggregate)
     * @dataProvider dataProviderForUnzip
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testUnzipIteratorAggregate(array $rows, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($rows);

        // When
        $result = Stream::of($iterable)
            ->unzip()
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @return array<array{0: array<array<mixed>>, 1: array<array<mixed>>}>
     */
    public static function dataProviderForUnzip(): array
    {
        return [
            // Empty
            [
                [],
                [],
            ],
            // Single row
            [
                [[1, 'a']],
                [[1], ['a']],
            ],
            // Canonical
            [
                [[1, 'a'], [2, 'b'], [3, 'c']],
                [[1, 2, 3], ['a', 'b', 'c']],
            ],
            // Three columns
            [
                [[1, 'a', true], [2, 'b', false], [3, 'c', true]],
                [[1, 2, 3], ['a', 'b', 'c'], [true, false, true]],
            ],
            // Uneven rows: truncate to shortest
            [
                [[1, 'a', 'x'], [2, 'b'], [3, 'c', 'z']],
                [[1, 2, 3], ['a', 'b', 'c']],
            ],
        ];
    }

    /**
     * @test Stream::unzip composes with downstream operations
     */
    public function testChainsWithOtherOperations(): void
    {
        // Given
        $rows = [[1, 'a'], [2, 'b'], [3, 'c']];

        // When: unzip then take just the first column (numbers), then sum
        $result = Stream::of($rows)
            ->unzip()
            ->limit(1)
            ->toArray();

        // Then
        $this->assertSame([[1, 2, 3]], $result);
    }

    /**
     * @test Stream::unzip round-trips with Stream::zip
     */
    public function testRoundTripWithZip(): void
    {
        // Given
        $rows = [[1, 'a', true], [2, 'b', false], [3, 'c', true]];

        // When
        $columns = Stream::of($rows)->unzip()->toArray();
        $rebuilt = Stream::of($columns)->zip()->toArray();

        // Then
        $this->assertSame($rows, $rebuilt);
    }
}

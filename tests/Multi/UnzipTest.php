<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

use IterTools\Multi;
use IterTools\Tests\Fixture;

class UnzipTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         unzip (array)
     * @dataProvider dataProviderForUnzip
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testUnzipArray(array $rows, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Multi::unzip($rows) as $column) {
            $result[] = $column;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         unzip (Generator)
     * @dataProvider dataProviderForUnzip
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testUnzipGenerator(array $rows, array $expected): void
    {
        // Given
        $iterable = Fixture\GeneratorFixture::getGenerator($rows);
        $result = [];

        // When
        foreach (Multi::unzip($iterable) as $column) {
            $result[] = $column;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         unzip (Iterator)
     * @dataProvider dataProviderForUnzip
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testUnzipIterator(array $rows, array $expected): void
    {
        // Given
        $iterable = new Fixture\ArrayIteratorFixture($rows);
        $result = [];

        // When
        foreach (Multi::unzip($iterable) as $column) {
            $result[] = $column;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         unzip (IteratorAggregate)
     * @dataProvider dataProviderForUnzip
     * @param        array<array<mixed>> $rows
     * @param        array<array<mixed>> $expected
     */
    public function testUnzipIteratorAggregate(array $rows, array $expected): void
    {
        // Given
        $iterable = new Fixture\IteratorAggregateFixture($rows);
        $result = [];

        // When
        foreach (Multi::unzip($iterable) as $column) {
            $result[] = $column;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @return array<array{0: array<array<mixed>>, 1: array<array<mixed>>}>
     */
    public static function dataProviderForUnzip(): array
    {
        return [
            // Empty input → empty output
            [
                [],
                [],
            ],
            // Single row of two columns
            [
                [[1, 'a']],
                [[1], ['a']],
            ],
            // Single row of three columns
            [
                [[1, 'a', true]],
                [[1], ['a'], [true]],
            ],
            // Canonical: list of pairs
            [
                [[1, 'a'], [2, 'b'], [3, 'c']],
                [[1, 2, 3], ['a', 'b', 'c']],
            ],
            // Three columns
            [
                [[1, 'a', true], [2, 'b', false], [3, 'c', true]],
                [[1, 2, 3], ['a', 'b', 'c'], [true, false, true]],
            ],
            // Uneven rows: truncate to shortest row width (mirrors Multi::zip)
            [
                [[1, 'a', 'x'], [2, 'b'], [3, 'c', 'z']],
                [[1, 2, 3], ['a', 'b', 'c']],
            ],
            // Uneven rows: shortest first
            [
                [[1], [2, 'b'], [3, 'c', 'z']],
                [[1, 2, 3]],
            ],
            // Single-column rows
            [
                [[1], [2], [3]],
                [[1, 2, 3]],
            ],
            // Empty inner row terminates output (shortest = 0)
            [
                [[1, 'a'], [], [3, 'c']],
                [],
            ],
            // Mixed scalar values
            [
                [['x', 1, true], ['y', 2, false]],
                [['x', 'y'], [1, 2], [true, false]],
            ],
        ];
    }

    /**
     * @test unzip on empty input yields nothing
     */
    public function testEmptyInput(): void
    {
        // When
        $result = [];
        foreach (Multi::unzip([]) as $column) {
            $result[] = $column;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test unzip with mixed inner-row iterable types
     */
    public function testMixedInnerIterableTypes(): void
    {
        // Given: rows of different iterable types
        $rows = [
            [1, 'a', true],
            Fixture\GeneratorFixture::getGenerator([2, 'b', false]),
            new Fixture\ArrayIteratorFixture([3, 'c', true]),
            new Fixture\IteratorAggregateFixture([4, 'd', false]),
        ];

        // When
        $result = [];
        foreach (Multi::unzip($rows) as $column) {
            $result[] = $column;
        }

        // Then
        $this->assertSame(
            [
                [1, 2, 3, 4],
                ['a', 'b', 'c', 'd'],
                [true, false, true, false],
            ],
            $result
        );
    }

    /**
     * @test unzip discards source row keys (output columns are sequentially indexed)
     */
    public function testRowKeysDiscarded(): void
    {
        // Given: associative row keys
        $rows = [
            'first'  => [1, 'a'],
            'second' => [2, 'b'],
            'third'  => [3, 'c'],
        ];

        // When
        $columns = [];
        foreach (Multi::unzip($rows) as $column) {
            $columns[] = $column;
        }

        // Then: column values are positional, not keyed by row key
        $this->assertSame([[1, 2, 3], ['a', 'b', 'c']], $columns);
        foreach ($columns as $column) {
            $this->assertSame([0, 1, 2], \array_keys($column));
        }
    }

    /**
     * @test unzip discards inner cell keys
     */
    public function testInnerCellKeysDiscarded(): void
    {
        // Given: rows with associative inner keys (keys must be ignored, values taken positionally)
        $rows = [
            ['x' => 1, 'y' => 'a'],
            ['x' => 2, 'y' => 'b'],
        ];

        // When
        $columns = [];
        foreach (Multi::unzip($rows) as $column) {
            $columns[] = $column;
        }

        // Then
        $this->assertSame([[1, 2], ['a', 'b']], $columns);
    }

    /**
     * @test unzip preserves object identity in cell values
     */
    public function testObjectAndArrayValuesPassThrough(): void
    {
        // Given
        $obj1 = new \stdClass();
        $obj1->name = 'first';
        $obj2 = new \stdClass();
        $obj2->name = 'second';
        $arr1 = ['nested', 'list'];
        $arr2 = ['another'];

        // When
        $columns = [];
        foreach (Multi::unzip([[$obj1, $arr1], [$obj2, $arr2]]) as $column) {
            $columns[] = $column;
        }

        // Then
        $this->assertCount(2, $columns);
        $this->assertSame($obj1, $columns[0][0]);
        $this->assertSame($obj2, $columns[0][1]);
        $this->assertSame($arr1, $columns[1][0]);
        $this->assertSame($arr2, $columns[1][1]);
    }

    /**
     * @test zip(...unzip($rows)) round-trips
     */
    public function testRoundTripWithZip(): void
    {
        // Given
        $rows = [[1, 'a', true], [2, 'b', false], [3, 'c', true]];

        // When
        $columns = [];
        foreach (Multi::unzip($rows) as $column) {
            $columns[] = $column;
        }

        $rebuilt = [];
        foreach (Multi::zip(...$columns) as $row) {
            $rebuilt[] = $row;
        }

        // Then
        $this->assertSame($rows, $rebuilt);
    }

    /**
     * @test zip(...unzip($rows)) round-trips with uneven rows (truncates to shortest, matching zip semantics)
     */
    public function testRoundTripWithZipTruncatesToShortest(): void
    {
        // Given: shortest row has width 2
        $rows = [[1, 'a', 'x'], [2, 'b'], [3, 'c', 'z']];

        // When
        $columns = [];
        foreach (Multi::unzip($rows) as $column) {
            $columns[] = $column;
        }

        $rebuilt = [];
        foreach (Multi::zip(...$columns) as $row) {
            $rebuilt[] = $row;
        }

        // Then: identity holds modulo the truncated trailing cells
        $this->assertSame([[1, 'a'], [2, 'b'], [3, 'c']], $rebuilt);
    }

    /**
     * @test unzip throws when a row is not iterable, naming the zero-based row index
     */
    public function testNonIterableRowThrows(): void
    {
        // Given
        $rows = [[1, 2], 3, [4, 5]];

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('row 1');

        // When: must consume the generator to trigger the throw
        foreach (Multi::unzip($rows) as $_) {
        }
    }

    /**
     * @test unzip throws on first non-iterable row even when others are valid
     */
    public function testNonIterableFirstRowThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('row 0');

        // When
        foreach (Multi::unzip(['not-a-row', [1, 2]]) as $_) {
        }
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Combinatorics;

use IterTools\Combinatorics;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ProductTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test product example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $a = [1, 2];
        $b = ['a', 'b'];

        // When
        $result = [];
        foreach (Combinatorics::product($a, $b) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame(
            [
                [1, 'a'],
                [1, 'b'],
                [2, 'a'],
                [2, 'b'],
            ],
            $result
        );
    }

    /**
     * @test         product (array)
     * @dataProvider dataProviderForProduct
     * @param        array<array<mixed>> $iterables
     * @param        array<array<mixed>> $expected
     */
    public function testProductArray(array $iterables, array $expected): void
    {
        // When
        $result = [];
        foreach (Combinatorics::product(...$iterables) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         product (Generator)
     * @dataProvider dataProviderForProduct
     * @param        array<array<mixed>> $iterables
     * @param        array<array<mixed>> $expected
     */
    public function testProductGenerator(array $iterables, array $expected): void
    {
        // Given
        $generators = [];
        foreach ($iterables as $iterable) {
            $generators[] = GeneratorFixture::getGenerator($iterable);
        }

        // When
        $result = [];
        foreach (Combinatorics::product(...$generators) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         product (Iterator)
     * @dataProvider dataProviderForProduct
     * @param        array<array<mixed>> $iterables
     * @param        array<array<mixed>> $expected
     */
    public function testProductIterator(array $iterables, array $expected): void
    {
        // Given
        $iterators = [];
        foreach ($iterables as $iterable) {
            $iterators[] = new ArrayIteratorFixture($iterable);
        }

        // When
        $result = [];
        foreach (Combinatorics::product(...$iterators) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         product (IteratorAggregate)
     * @dataProvider dataProviderForProduct
     * @param        array<array<mixed>> $iterables
     * @param        array<array<mixed>> $expected
     */
    public function testProductIteratorAggregate(array $iterables, array $expected): void
    {
        // Given
        $aggregates = [];
        foreach ($iterables as $iterable) {
            $aggregates[] = new IteratorAggregateFixture($iterable);
        }

        // When
        $result = [];
        foreach (Combinatorics::product(...$aggregates) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForProduct(): array
    {
        return [
            // single iterable — one-element tuples preserving input order
            [
                [[1, 2, 3]],
                [[1], [2], [3]],
            ],
            // single iterable with one element
            [
                [['only']],
                [['only']],
            ],
            // two iterables, Python itertools lexicographic order
            [
                [[1, 2], ['a', 'b']],
                [[1, 'a'], [1, 'b'], [2, 'a'], [2, 'b']],
            ],
            // two iterables, one element each
            [
                [['x'], ['y']],
                [['x', 'y']],
            ],
            // three iterables — cartesian product
            [
                [[0, 1], [0, 1], [0, 1]],
                [
                    [0, 0, 0], [0, 0, 1], [0, 1, 0], [0, 1, 1],
                    [1, 0, 0], [1, 0, 1], [1, 1, 0], [1, 1, 1],
                ],
            ],
            // mixed-size iterables
            [
                [[1, 2, 3], ['a']],
                [[1, 'a'], [2, 'a'], [3, 'a']],
            ],
            [
                [['a'], [1, 2, 3]],
                [['a', 1], ['a', 2], ['a', 3]],
            ],
            // mixed types
            [
                [[1, 'two'], [true, null]],
                [[1, true], [1, null], ['two', true], ['two', null]],
            ],
            // duplicate values preserved (positional)
            [
                [[1, 1], ['a', 'a']],
                [[1, 'a'], [1, 'a'], [1, 'a'], [1, 'a']],
            ],
        ];
    }

    /**
     * @test product of zero iterables yields one empty tuple
     */
    public function testZeroIterablesYieldsSingleEmptyTuple(): void
    {
        // When
        $result = [];
        foreach (Combinatorics::product() as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame([[]], $result);
    }

    /**
     * @test         product with any empty iterable yields nothing (array)
     * @dataProvider dataProviderForEmptyInProduct
     * @param        array<array<mixed>> $iterables
     */
    public function testEmptyInProductArray(array $iterables): void
    {
        // When
        $result = [];
        foreach (Combinatorics::product(...$iterables) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame([], $result);
    }

    public static function dataProviderForEmptyInProduct(): array
    {
        return [
            [[[]]],
            [[[], [1, 2]]],
            [[[1, 2], []]],
            [[[1, 2], [], [3, 4]]],
            [[[1], [2], []]],
        ];
    }

    /**
     * @test product discards associative input keys — tuples are list arrays
     */
    public function testAssociativeKeysDiscarded(): void
    {
        // Given
        $a = ['x' => 1, 'y' => 2];
        $b = ['p' => 'a', 'q' => 'b'];

        // When
        $result = [];
        foreach (Combinatorics::product($a, $b) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame(
            [
                [1, 'a'],
                [1, 'b'],
                [2, 'a'],
                [2, 'b'],
            ],
            $result
        );
    }

    /**
     * @test product yields list-array tuples (0-indexed)
     */
    public function testOutputTuplesAreListArrays(): void
    {
        // Given
        $a = [1, 2];
        $b = ['a', 'b'];

        // When
        $first = null;
        foreach (Combinatorics::product($a, $b) as $tuple) {
            $first = $tuple;
            break;
        }

        // Then
        $this->assertIsArray($first);
        $this->assertSame([0, 1], \array_keys($first));
    }

    /**
     * @test product throws when the same generator instance is passed as multiple arguments
     */
    public function testSameGeneratorInstancePassedTwiceThrows(): void
    {
        // Given — a non-rewindable generator reused as two distinct dimensions
        $g = GeneratorFixture::getGenerator([1, 2]);

        // Then
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot traverse an already closed generator');

        // When
        foreach (Combinatorics::product($g, $g) as $tuple) {
            // force iteration
        }
    }

    /**
     * @test product consumes generator inputs once (no rewind)
     */
    public function testInputGeneratorsConsumedOnce(): void
    {
        // Given
        $a = GeneratorFixture::getGenerator([1, 2]);
        $b = GeneratorFixture::getGenerator(['a', 'b']);

        // When
        $result = [];
        foreach (Combinatorics::product($a, $b) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame(
            [
                [1, 'a'],
                [1, 'b'],
                [2, 'a'],
                [2, 'b'],
            ],
            $result
        );
    }
}

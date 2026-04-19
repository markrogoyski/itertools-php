<?php

declare(strict_types=1);

namespace IterTools\Tests\Combinatorics;

use IterTools\Combinatorics;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class CombinationsTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test combinations example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2, 3, 4];

        // When
        $result = [];
        foreach (Combinatorics::combinations($data, 2) as $tuple) {
            $result[] = $tuple;
        }

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
     * @test         combinations (array)
     * @dataProvider dataProviderForCombinations
     * @param        array<mixed>        $data
     * @param        int                 $r
     * @param        array<array<mixed>> $expected
     */
    public function testCombinationsArray(array $data, int $r, array $expected): void
    {
        // When
        $result = [];
        foreach (Combinatorics::combinations($data, $r) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         combinations (Generator)
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
        $result = [];
        foreach (Combinatorics::combinations($gen, $r) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         combinations (Iterator)
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
        $result = [];
        foreach (Combinatorics::combinations($iter, $r) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         combinations (IteratorAggregate)
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
        $result = [];
        foreach (Combinatorics::combinations($agg, $r) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForCombinations(): array
    {
        return [
            // single element, r = 1
            [
                ['a'],
                1,
                [['a']],
            ],
            // two elements, r = 1
            [
                [1, 2],
                1,
                [[1], [2]],
            ],
            // two elements, r = 2
            [
                [1, 2],
                2,
                [[1, 2]],
            ],
            // three elements, r = 1
            [
                [1, 2, 3],
                1,
                [[1], [2], [3]],
            ],
            // three elements, r = 2 — Python itertools order
            [
                [1, 2, 3],
                2,
                [
                    [1, 2],
                    [1, 3],
                    [2, 3],
                ],
            ],
            // three elements, r = 3 (r = count) → single tuple
            [
                [1, 2, 3],
                3,
                [[1, 2, 3]],
            ],
            // four elements, r = 2
            [
                [1, 2, 3, 4],
                2,
                [
                    [1, 2],
                    [1, 3],
                    [1, 4],
                    [2, 3],
                    [2, 4],
                    [3, 4],
                ],
            ],
            // four elements, r = 3
            [
                [1, 2, 3, 4],
                3,
                [
                    [1, 2, 3],
                    [1, 2, 4],
                    [1, 3, 4],
                    [2, 3, 4],
                ],
            ],
            // $r = 0 — one empty tuple
            [
                [1, 2, 3],
                0,
                [[]],
            ],
            // $r = 0 on empty input — one empty tuple
            [
                [],
                0,
                [[]],
            ],
            // $r > count → empty
            [
                [1, 2],
                3,
                [],
            ],
            // $r = count + 1 → empty
            [
                [1, 2, 3],
                4,
                [],
            ],
            // duplicates are position-unique
            [
                [1, 1],
                2,
                [[1, 1]],
            ],
            // duplicates pick 2 from 3 — position-unique
            [
                ['a', 'a', 'b'],
                2,
                [
                    ['a', 'a'],
                    ['a', 'b'],
                    ['a', 'b'],
                ],
            ],
            // mixed types
            [
                [1, 'two', null],
                2,
                [
                    [1, 'two'],
                    [1, null],
                    ['two', null],
                ],
            ],
        ];
    }

    /**
     * @test         combinations with empty iterable and $r = 0 yields one empty tuple
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableRZero(iterable $data): void
    {
        // When
        $result = [];
        foreach (Combinatorics::combinations($data, 0) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame([[]], $result);
    }

    /**
     * @test         combinations with empty iterable and positive $r yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterablePositiveR(iterable $data): void
    {
        // When
        $result = [];
        foreach (Combinatorics::combinations($data, 2) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test combinations discards associative input keys — tuples are list arrays
     */
    public function testAssociativeKeysDiscarded(): void
    {
        // Given
        $data = ['x' => 1, 'y' => 2, 'z' => 3];

        // When
        $result = [];
        foreach (Combinatorics::combinations($data, 2) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame(
            [
                [1, 2],
                [1, 3],
                [2, 3],
            ],
            $result
        );
    }

    /**
     * @test combinations yields list-array tuples (0-indexed)
     */
    public function testOutputTuplesAreListArrays(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $first = null;
        foreach (Combinatorics::combinations($data, 2) as $tuple) {
            $first = $tuple;
            break;
        }

        // Then
        $this->assertIsArray($first);
        $this->assertSame([0, 1], \array_keys($first));
    }

    /**
     * @test combinations throws on negative $r
     */
    public function testNegativeRThrows(): void
    {
        // Given
        $data = [1, 2, 3];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (Combinatorics::combinations($data, -1) as $tuple) {
            // force iteration
        }
    }

    /**
     * @test combinations consumes generator input once (no rewind)
     */
    public function testInputGeneratorConsumedOnce(): void
    {
        // Given
        $gen = GeneratorFixture::getGenerator([1, 2, 3, 4]);

        // When
        $result = [];
        foreach (Combinatorics::combinations($gen, 2) as $tuple) {
            $result[] = $tuple;
        }

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
}

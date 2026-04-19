<?php

declare(strict_types=1);

namespace IterTools\Tests\Combinatorics;

use IterTools\Combinatorics;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class PermutationsTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test permutations example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = [];
        foreach (Combinatorics::permutations($data) as $tuple) {
            $result[] = $tuple;
        }

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
     * @test         permutations (array)
     * @dataProvider dataProviderForPermutations
     * @param        array<mixed>        $data
     * @param        int|null            $r
     * @param        array<array<mixed>> $expected
     */
    public function testPermutationsArray(array $data, ?int $r, array $expected): void
    {
        // When
        $result = [];
        foreach (Combinatorics::permutations($data, $r) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         permutations (Generator)
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
        $result = [];
        foreach (Combinatorics::permutations($gen, $r) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         permutations (Iterator)
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
        $result = [];
        foreach (Combinatorics::permutations($iter, $r) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         permutations (IteratorAggregate)
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
        $result = [];
        foreach (Combinatorics::permutations($agg, $r) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForPermutations(): array
    {
        return [
            // full-length permutations (null $r) of single element
            [
                ['a'],
                null,
                [['a']],
            ],
            // full-length permutations of two elements
            [
                [1, 2],
                null,
                [[1, 2], [2, 1]],
            ],
            // full-length permutations of three elements — Python itertools order
            [
                [1, 2, 3],
                null,
                [
                    [1, 2, 3],
                    [1, 3, 2],
                    [2, 1, 3],
                    [2, 3, 1],
                    [3, 1, 2],
                    [3, 2, 1],
                ],
            ],
            // explicit $r equal to length (same as null)
            [
                [1, 2, 3],
                3,
                [
                    [1, 2, 3],
                    [1, 3, 2],
                    [2, 1, 3],
                    [2, 3, 1],
                    [3, 1, 2],
                    [3, 2, 1],
                ],
            ],
            // $r less than length — pick 2 from 3
            [
                [1, 2, 3],
                2,
                [
                    [1, 2], [1, 3],
                    [2, 1], [2, 3],
                    [3, 1], [3, 2],
                ],
            ],
            // $r less than length — pick 1 from 3
            [
                [1, 2, 3],
                1,
                [[1], [2], [3]],
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
            // empty input with null $r (full-length of empty = one empty tuple)
            [
                [],
                null,
                [[]],
            ],
            // $r greater than length → empty
            [
                [1, 2],
                3,
                [],
            ],
            // $r greater than length → empty (r = length + 1)
            [
                [1, 2, 3],
                4,
                [],
            ],
            // duplicates are position-unique — [1,1] yields 2 tuples
            [
                [1, 1],
                null,
                [[1, 1], [1, 1]],
            ],
            // duplicates pick 2 from 3 — position-unique
            [
                ['a', 'a', 'b'],
                2,
                [
                    ['a', 'a'], ['a', 'b'],
                    ['a', 'a'], ['a', 'b'],
                    ['b', 'a'], ['b', 'a'],
                ],
            ],
            // mixed types
            [
                [1, 'two', null],
                2,
                [
                    [1, 'two'], [1, null],
                    ['two', 1], ['two', null],
                    [null, 1], [null, 'two'],
                ],
            ],
        ];
    }

    /**
     * @test         permutations with empty iterable and default $r (null)
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableDefaultR(iterable $data): void
    {
        // When
        $result = [];
        foreach (Combinatorics::permutations($data) as $tuple) {
            $result[] = $tuple;
        }

        // Then — full-length permutations of empty input is one empty tuple
        $this->assertSame([[]], $result);
    }

    /**
     * @test         permutations with empty iterable and positive $r yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterablePositiveR(iterable $data): void
    {
        // When
        $result = [];
        foreach (Combinatorics::permutations($data, 2) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test permutations discards associative input keys — tuples are list arrays
     */
    public function testAssociativeKeysDiscarded(): void
    {
        // Given
        $data = ['x' => 1, 'y' => 2, 'z' => 3];

        // When
        $result = [];
        foreach (Combinatorics::permutations($data, 2) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame(
            [
                [1, 2], [1, 3],
                [2, 1], [2, 3],
                [3, 1], [3, 2],
            ],
            $result
        );
    }

    /**
     * @test permutations yields list-array tuples (0-indexed)
     */
    public function testOutputTuplesAreListArrays(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $first = null;
        foreach (Combinatorics::permutations($data, 2) as $tuple) {
            $first = $tuple;
            break;
        }

        // Then
        $this->assertIsArray($first);
        $this->assertSame([0, 1], \array_keys($first));
    }

    /**
     * @test permutations throws on negative $r
     */
    public function testNegativeRThrows(): void
    {
        // Given
        $data = [1, 2, 3];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (Combinatorics::permutations($data, -1) as $tuple) {
            // force iteration
        }
    }

    /**
     * @test permutations consumes generator input once (no rewind)
     */
    public function testInputGeneratorConsumedOnce(): void
    {
        // Given
        $gen = GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $result = [];
        foreach (Combinatorics::permutations($gen, 2) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame(
            [
                [1, 2], [1, 3],
                [2, 1], [2, 3],
                [3, 1], [3, 2],
            ],
            $result
        );
    }
}

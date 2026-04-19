<?php

declare(strict_types=1);

namespace IterTools\Tests\Combinatorics;

use IterTools\Combinatorics;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class CombinationsWithReplacementTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test combinationsWithReplacement example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = [];
        foreach (Combinatorics::combinationsWithReplacement($data, 2) as $tuple) {
            $result[] = $tuple;
        }

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
     * @test         combinationsWithReplacement (array)
     * @dataProvider dataProviderForCombinationsWithReplacement
     * @param        array<mixed>        $data
     * @param        int                 $r
     * @param        array<array<mixed>> $expected
     */
    public function testCombinationsWithReplacementArray(array $data, int $r, array $expected): void
    {
        // When
        $result = [];
        foreach (Combinatorics::combinationsWithReplacement($data, $r) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         combinationsWithReplacement (Generator)
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
        $result = [];
        foreach (Combinatorics::combinationsWithReplacement($gen, $r) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         combinationsWithReplacement (Iterator)
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
        $result = [];
        foreach (Combinatorics::combinationsWithReplacement($iter, $r) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         combinationsWithReplacement (IteratorAggregate)
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
        $result = [];
        foreach (Combinatorics::combinationsWithReplacement($agg, $r) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForCombinationsWithReplacement(): array
    {
        return [
            // single element, r = 1
            [
                ['a'],
                1,
                [['a']],
            ],
            // single element, r = 2 — same element picked twice
            [
                ['a'],
                2,
                [['a', 'a']],
            ],
            // single element, r = 3
            [
                ['x'],
                3,
                [['x', 'x', 'x']],
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
                [
                    [1, 1],
                    [1, 2],
                    [2, 2],
                ],
            ],
            // two elements, r = 3
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
                    [1, 1],
                    [1, 2],
                    [1, 3],
                    [2, 2],
                    [2, 3],
                    [3, 3],
                ],
            ],
            // three elements, r = 3
            [
                [1, 2, 3],
                3,
                [
                    [1, 1, 1],
                    [1, 1, 2],
                    [1, 1, 3],
                    [1, 2, 2],
                    [1, 2, 3],
                    [1, 3, 3],
                    [2, 2, 2],
                    [2, 2, 3],
                    [2, 3, 3],
                    [3, 3, 3],
                ],
            ],
            // $r larger than count — still valid for with-replacement
            [
                [1, 2],
                4,
                [
                    [1, 1, 1, 1],
                    [1, 1, 1, 2],
                    [1, 1, 2, 2],
                    [1, 2, 2, 2],
                    [2, 2, 2, 2],
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
            // duplicate input values are position-unique — duplicate output tuples result
            [
                [1, 1],
                2,
                [
                    [1, 1],
                    [1, 1],
                    [1, 1],
                ],
            ],
            // duplicates: pick 2 from [a, a, b] — position-unique
            [
                ['a', 'a', 'b'],
                2,
                [
                    ['a', 'a'],
                    ['a', 'a'],
                    ['a', 'b'],
                    ['a', 'a'],
                    ['a', 'b'],
                    ['b', 'b'],
                ],
            ],
            // mixed types
            [
                [1, 'two', null],
                2,
                [
                    [1, 1],
                    [1, 'two'],
                    [1, null],
                    ['two', 'two'],
                    ['two', null],
                    [null, null],
                ],
            ],
        ];
    }

    /**
     * @test         combinationsWithReplacement with empty iterable and $r = 0 yields one empty tuple
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableRZero(iterable $data): void
    {
        // When
        $result = [];
        foreach (Combinatorics::combinationsWithReplacement($data, 0) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame([[]], $result);
    }

    /**
     * @test         combinationsWithReplacement with empty iterable and positive $r yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterablePositiveR(iterable $data): void
    {
        // When
        $result = [];
        foreach (Combinatorics::combinationsWithReplacement($data, 2) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test combinationsWithReplacement discards associative input keys — tuples are list arrays
     */
    public function testAssociativeKeysDiscarded(): void
    {
        // Given
        $data = ['x' => 1, 'y' => 2, 'z' => 3];

        // When
        $result = [];
        foreach (Combinatorics::combinationsWithReplacement($data, 2) as $tuple) {
            $result[] = $tuple;
        }

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
     * @test combinationsWithReplacement yields list-array tuples (0-indexed)
     */
    public function testOutputTuplesAreListArrays(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $first = null;
        foreach (Combinatorics::combinationsWithReplacement($data, 2) as $tuple) {
            $first = $tuple;
            break;
        }

        // Then
        $this->assertIsArray($first);
        $this->assertSame([0, 1], \array_keys($first));
    }

    /**
     * @test combinationsWithReplacement throws on negative $r
     */
    public function testNegativeRThrows(): void
    {
        // Given
        $data = [1, 2, 3];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (Combinatorics::combinationsWithReplacement($data, -1) as $tuple) {
            // force iteration
        }
    }

    /**
     * @test combinationsWithReplacement consumes generator input once (no rewind)
     */
    public function testInputGeneratorConsumedOnce(): void
    {
        // Given
        $gen = GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $result = [];
        foreach (Combinatorics::combinationsWithReplacement($gen, 2) as $tuple) {
            $result[] = $tuple;
        }

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
}

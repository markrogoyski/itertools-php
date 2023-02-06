<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class SortTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test sort example usage without custom comparator
     */
    public function testSortWithoutCustomComparator(): void
    {
        // Given
        $data     = [5, 4, 1, 9, 3, 3, 5, 10, 2];
        $expected = [1, 2, 3, 3, 4, 5, 5, 9, 10];

        // When
        $sorted = [];
        foreach (Single::sort($data) as $datum) {
            $sorted[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $sorted);
    }

    /**
     * @test sort example usage using custom comparator
     */
    public function testSortUsingCustomComparator(): void
    {
        // Given
        $chessPieces = [
            [
                'name' => 'bishop',
                'value' => 3,
            ],
            [
                'name' => 'knight',
                'value' => 3,
            ],
            [
                'name' => 'pawn',
                'value' => 1,
            ],
            [
                'name' => 'queen',
                'value' => 9,
            ],
            [
                'name' => 'rook',
                'value' => 5,
            ],
        ];
        $comparator = fn ($lhs, $rhs) => $lhs['value'] <=> $rhs['value'];

        // When
        $sortedByValue = [];
        foreach (Single::sort($chessPieces, $comparator) as $chessPiece) {
            $sortedByValue[] = $chessPiece;
        }

        // Then
        $expected = [
            [
                'name' => 'pawn',
                'value' => 1,
            ],
            [
                'name' => 'bishop',
                'value' => 3,
            ],
            [
                'name' => 'knight',
                'value' => 3,
            ],
            [
                'name' => 'rook',
                'value' => 5,
            ],
            [
                'name' => 'queen',
                'value' => 9,
            ],
        ];
        $this->assertEquals($expected, $sortedByValue);
    }

    /**
     * @dataProvider dataProviderForArray
     * @param array $data
     * @param callable|null $comparator
     * @param array $expected
     */
    public function testArray(array $data, ?callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                null,
                [],
            ],
            [
                [],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                [],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                [1],
                null,
                [1],
            ],
            [
                [1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1],
            ],
            [
                [1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1],
            ],
            [
                [1, 1],
                null,
                [1, 1],
            ],
            [
                [1, 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 1],
            ],
            [
                [1, 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 1],
            ],
            [
                [1, 2],
                null,
                [1, 2],
            ],
            [
                [1, 2],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                [1, 2],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                [2, 1],
                null,
                [1, 2],
            ],
            [
                [2, 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                [2, 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                [2, 1, 1],
                null,
                [1, 1, 2],
            ],
            [
                [2, 1, 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 1, 2],
            ],
            [
                [2, 1, 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1, 1],
            ],
            [
                [2, 1, 3],
                null,
                [1, 2, 3],
            ],
            [
                [2, 1, 3],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2, 3],
            ],
            [
                [2, 1, 3],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [3, 2, 1],
            ],
            [
                [1, 3, 2, 5, -3, -6, 10, 11, 1],
                null,
                [-6, -3, 1, 1, 2, 3, 5, 10, 11],
            ],
            [
                [1, 3, 2, 5, -3, -6, 10, 11, 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [-6, -3, 1, 1, 2, 3, 5, 10, 11],
            ],
            [
                [1, 3, 2, 5, -3, -6, 10, 11, 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [11, 10, 5, 3, 2, 1, 1, -3, -6],
            ],
            [
                [1, 3.3, 2, 5, -3.1, -6, '10', 11, 1],
                null,
                [-6, -3.1, 1, 1, 2, 3.3, 5, '10', 11],
            ],
            [
                [1, 3.3, 2, 5, -3.1, -6, '10', 11, 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [-6, -3.1, 1, 1, 2, 3.3, 5, '10', 11],
            ],
            [
                [1, 3.3, 2, 5, -3.1, -6, '10', 11, 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [11, '10', 5, 3.3, 2, 1, 1, -3.1, -6]
            ],
            [
                [true, false, false, true, false],
                null,
                [false, false, false, true, true],
            ],
            [
                [true, false, false, true, false],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [false, false, false, true, true],
            ],
            [
                [true, false, false, true, false],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [true, true, false, false, false],
            ],
            [
                [[1], [3], [-2], [5.1], [2, 3], [1, 1], [0], []],
                null,
                [[], [-2], [0], [1], [3], [5.1], [1, 1], [2, 3]],
            ],
            [
                [[1], [3], [-2], [5.1], [2, 3], [1, 1], [0], []],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [[], [-2], [0], [1], [3], [5.1], [1, 1], [2, 3]],
            ],
            [
                [[1], [3], [-2], [5.1], [2, 3], [1, 1], [0], []],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [[2, 3], [1, 1], [5.1], [3], [1], [0], [-2], []],
            ],
            [
                [[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]],
                null,
                [[0, 3], [1, 2], [2, 1], [2, 2], [2, 5], [3, 0], [5, 2]],
            ],
            [
                [[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [[0, 3], [1, 2], [2, 1], [2, 2], [2, 5], [3, 0], [5, 2]],
            ],
            [
                [[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [[5, 2], [3, 0], [2, 5], [2, 2], [2, 1], [1, 2], [0, 3]],
            ],
            [
                [[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]],
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                [[3, 0], [2, 1], [1, 2], [2, 2], [5, 2], [0, 3], [2, 5]],
            ],
            [
                [[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]],
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
                [[2, 5], [0, 3], [1, 2], [2, 2], [5, 2], [2, 1], [3, 0]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $data
     * @param callable|null $comparator
     * @param array $expected
     */
    public function testGenerators(\Generator $data, ?callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                null,
                [],
            ],
            [
                $gen([]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $gen([]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $gen([1]),
                null,
                [1],
            ],
            [
                $gen([1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1],
            ],
            [
                $gen([1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1],
            ],
            [
                $gen([1, 1]),
                null,
                [1, 1],
            ],
            [
                $gen([1, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 1],
            ],
            [
                $gen([1, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 1],
            ],
            [
                $gen([1, 2]),
                null,
                [1, 2],
            ],
            [
                $gen([1, 2]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                $gen([1, 2]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                $gen([2, 1]),
                null,
                [1, 2],
            ],
            [
                $gen([2, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                $gen([2, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                $gen([2, 1, 1]),
                null,
                [1, 1, 2],
            ],
            [
                $gen([2, 1, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 1, 2],
            ],
            [
                $gen([2, 1, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1, 1],
            ],
            [
                $gen([2, 1, 3]),
                null,
                [1, 2, 3],
            ],
            [
                $gen([2, 1, 3]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2, 3],
            ],
            [
                $gen([2, 1, 3]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [3, 2, 1],
            ],
            [
                $gen([1, 3, 2, 5, -3, -6, 10, 11, 1]),
                null,
                [-6, -3, 1, 1, 2, 3, 5, 10, 11],
            ],
            [
                $gen([1, 3, 2, 5, -3, -6, 10, 11, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [-6, -3, 1, 1, 2, 3, 5, 10, 11],
            ],
            [
                $gen([1, 3, 2, 5, -3, -6, 10, 11, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [11, 10, 5, 3, 2, 1, 1, -3, -6],
            ],
            [
                $gen([1, 3.3, 2, 5, -3.1, -6, '10', 11, 1]),
                null,
                [-6, -3.1, 1, 1, 2, 3.3, 5, '10', 11],
            ],
            [
                $gen([1, 3.3, 2, 5, -3.1, -6, '10', 11, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [-6, -3.1, 1, 1, 2, 3.3, 5, '10', 11],
            ],
            [
                $gen([1, 3.3, 2, 5, -3.1, -6, '10', 11, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [11, '10', 5, 3.3, 2, 1, 1, -3.1, -6]
            ],
            [
                $gen([true, false, false, true, false]),
                null,
                [false, false, false, true, true],
            ],
            [
                $gen([true, false, false, true, false]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [false, false, false, true, true],
            ],
            [
                $gen([true, false, false, true, false]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [true, true, false, false, false],
            ],
            [
                $gen([[1], [3], [-2], [5.1], [2, 3], [1, 1], [0], []]),
                null,
                [[], [-2], [0], [1], [3], [5.1], [1, 1], [2, 3]],
            ],
            [
                $gen([[1], [3], [-2], [5.1], [2, 3], [1, 1], [0], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [[], [-2], [0], [1], [3], [5.1], [1, 1], [2, 3]],
            ],
            [
                $gen([[1], [3], [-2], [5.1], [2, 3], [1, 1], [0], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [[2, 3], [1, 1], [5.1], [3], [1], [0], [-2], []],
            ],
            [
                $gen([[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]]),
                null,
                [[0, 3], [1, 2], [2, 1], [2, 2], [2, 5], [3, 0], [5, 2]],
            ],
            [
                $gen([[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [[0, 3], [1, 2], [2, 1], [2, 2], [2, 5], [3, 0], [5, 2]],
            ],
            [
                $gen([[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [[5, 2], [3, 0], [2, 5], [2, 2], [2, 1], [1, 2], [0, 3]],
            ],
            [
                $gen([[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]]),
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                [[3, 0], [2, 1], [1, 2], [2, 2], [5, 2], [0, 3], [2, 5]],
            ],
            [
                $gen([[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]]),
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
                [[2, 5], [0, 3], [1, 2], [2, 2], [5, 2], [2, 1], [3, 0]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $data
     * @param callable|null $comparator
     * @param array $expected
     */
    public function testIterators(\Iterator $data, ?callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                null,
                [],
            ],
            [
                $iter([]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $iter([]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $iter([1]),
                null,
                [1],
            ],
            [
                $iter([1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1],
            ],
            [
                $iter([1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1],
            ],
            [
                $iter([1, 1]),
                null,
                [1, 1],
            ],
            [
                $iter([1, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 1],
            ],
            [
                $iter([1, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 1],
            ],
            [
                $iter([1, 2]),
                null,
                [1, 2],
            ],
            [
                $iter([1, 2]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                $iter([1, 2]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                $iter([2, 1]),
                null,
                [1, 2],
            ],
            [
                $iter([2, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                $iter([2, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                $iter([2, 1, 1]),
                null,
                [1, 1, 2],
            ],
            [
                $iter([2, 1, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 1, 2],
            ],
            [
                $iter([2, 1, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1, 1],
            ],
            [
                $iter([2, 1, 3]),
                null,
                [1, 2, 3],
            ],
            [
                $iter([2, 1, 3]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2, 3],
            ],
            [
                $iter([2, 1, 3]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [3, 2, 1],
            ],
            [
                $iter([1, 3, 2, 5, -3, -6, 10, 11, 1]),
                null,
                [-6, -3, 1, 1, 2, 3, 5, 10, 11],
            ],
            [
                $iter([1, 3, 2, 5, -3, -6, 10, 11, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [-6, -3, 1, 1, 2, 3, 5, 10, 11],
            ],
            [
                $iter([1, 3, 2, 5, -3, -6, 10, 11, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [11, 10, 5, 3, 2, 1, 1, -3, -6],
            ],
            [
                $iter([1, 3.3, 2, 5, -3.1, -6, '10', 11, 1]),
                null,
                [-6, -3.1, 1, 1, 2, 3.3, 5, '10', 11],
            ],
            [
                $iter([1, 3.3, 2, 5, -3.1, -6, '10', 11, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [-6, -3.1, 1, 1, 2, 3.3, 5, '10', 11],
            ],
            [
                $iter([1, 3.3, 2, 5, -3.1, -6, '10', 11, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [11, '10', 5, 3.3, 2, 1, 1, -3.1, -6]
            ],
            [
                $iter([true, false, false, true, false]),
                null,
                [false, false, false, true, true],
            ],
            [
                $iter([true, false, false, true, false]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [false, false, false, true, true],
            ],
            [
                $iter([true, false, false, true, false]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [true, true, false, false, false],
            ],
            [
                $iter([[1], [3], [-2], [5.1], [2, 3], [1, 1], [0], []]),
                null,
                [[], [-2], [0], [1], [3], [5.1], [1, 1], [2, 3]],
            ],
            [
                $iter([[1], [3], [-2], [5.1], [2, 3], [1, 1], [0], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [[], [-2], [0], [1], [3], [5.1], [1, 1], [2, 3]],
            ],
            [
                $iter([[1], [3], [-2], [5.1], [2, 3], [1, 1], [0], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [[2, 3], [1, 1], [5.1], [3], [1], [0], [-2], []],
            ],
            [
                $iter([[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]]),
                null,
                [[0, 3], [1, 2], [2, 1], [2, 2], [2, 5], [3, 0], [5, 2]],
            ],
            [
                $iter([[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [[0, 3], [1, 2], [2, 1], [2, 2], [2, 5], [3, 0], [5, 2]],
            ],
            [
                $iter([[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [[5, 2], [3, 0], [2, 5], [2, 2], [2, 1], [1, 2], [0, 3]],
            ],
            [
                $iter([[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]]),
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                [[3, 0], [2, 1], [1, 2], [2, 2], [5, 2], [0, 3], [2, 5]],
            ],
            [
                $iter([[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]]),
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
                [[2, 5], [0, 3], [1, 2], [2, 2], [5, 2], [2, 1], [3, 0]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $data
     * @param callable|null $comparator
     * @param array $expected
     */
    public function testTraversables(\Traversable $data, ?callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                null,
                [],
            ],
            [
                $trav([]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $trav([]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $trav([1]),
                null,
                [1],
            ],
            [
                $trav([1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1],
            ],
            [
                $trav([1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1],
            ],
            [
                $trav([1, 1]),
                null,
                [1, 1],
            ],
            [
                $trav([1, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 1],
            ],
            [
                $trav([1, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 1],
            ],
            [
                $trav([1, 2]),
                null,
                [1, 2],
            ],
            [
                $trav([1, 2]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                $trav([1, 2]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                $trav([2, 1]),
                null,
                [1, 2],
            ],
            [
                $trav([2, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                $trav([2, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                $trav([2, 1, 1]),
                null,
                [1, 1, 2],
            ],
            [
                $trav([2, 1, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 1, 2],
            ],
            [
                $trav([2, 1, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1, 1],
            ],
            [
                $trav([2, 1, 3]),
                null,
                [1, 2, 3],
            ],
            [
                $trav([2, 1, 3]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2, 3],
            ],
            [
                $trav([2, 1, 3]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [3, 2, 1],
            ],
            [
                $trav([1, 3, 2, 5, -3, -6, 10, 11, 1]),
                null,
                [-6, -3, 1, 1, 2, 3, 5, 10, 11],
            ],
            [
                $trav([1, 3, 2, 5, -3, -6, 10, 11, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [-6, -3, 1, 1, 2, 3, 5, 10, 11],
            ],
            [
                $trav([1, 3, 2, 5, -3, -6, 10, 11, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [11, 10, 5, 3, 2, 1, 1, -3, -6],
            ],
            [
                $trav([1, 3.3, 2, 5, -3.1, -6, '10', 11, 1]),
                null,
                [-6, -3.1, 1, 1, 2, 3.3, 5, '10', 11],
            ],
            [
                $trav([1, 3.3, 2, 5, -3.1, -6, '10', 11, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [-6, -3.1, 1, 1, 2, 3.3, 5, '10', 11],
            ],
            [
                $trav([1, 3.3, 2, 5, -3.1, -6, '10', 11, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [11, '10', 5, 3.3, 2, 1, 1, -3.1, -6]
            ],
            [
                $trav([true, false, false, true, false]),
                null,
                [false, false, false, true, true],
            ],
            [
                $trav([true, false, false, true, false]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [false, false, false, true, true],
            ],
            [
                $trav([true, false, false, true, false]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [true, true, false, false, false],
            ],
            [
                $trav([[1], [3], [-2], [5.1], [2, 3], [1, 1], [0], []]),
                null,
                [[], [-2], [0], [1], [3], [5.1], [1, 1], [2, 3]],
            ],
            [
                $trav([[1], [3], [-2], [5.1], [2, 3], [1, 1], [0], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [[], [-2], [0], [1], [3], [5.1], [1, 1], [2, 3]],
            ],
            [
                $trav([[1], [3], [-2], [5.1], [2, 3], [1, 1], [0], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [[2, 3], [1, 1], [5.1], [3], [1], [0], [-2], []],
            ],
            [
                $trav([[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]]),
                null,
                [[0, 3], [1, 2], [2, 1], [2, 2], [2, 5], [3, 0], [5, 2]],
            ],
            [
                $trav([[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [[0, 3], [1, 2], [2, 1], [2, 2], [2, 5], [3, 0], [5, 2]],
            ],
            [
                $trav([[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [[5, 2], [3, 0], [2, 5], [2, 2], [2, 1], [1, 2], [0, 3]],
            ],
            [
                $trav([[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]]),
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                [[3, 0], [2, 1], [1, 2], [2, 2], [5, 2], [0, 3], [2, 5]],
            ],
            [
                $trav([[1, 2], [2, 1], [2, 2], [3, 0], [0, 3], [5, 2], [2, 5]]),
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
                [[2, 5], [0, 3], [1, 2], [2, 2], [5, 2], [2, 1], [3, 0]],
            ],
        ];
    }
}

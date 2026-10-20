<?php

declare(strict_types=1);

namespace IterTools\Tests\Sort;

use IterTools\Sort;
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
        foreach (Sort::sort($data) as $datum) {
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
        foreach (Sort::sort($chessPieces, $comparator) as $chessPiece) {
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
        foreach (Sort::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
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
        foreach (Sort::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForGenerators(): array
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
        foreach (Sort::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForIterators(): array
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
        foreach (Sort::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForTraversables(): array
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

    /**
     * @test sort preserves all elements from generator with duplicate keys
     */
    public function testSortGeneratorWithDuplicateKeys(): void
    {
        // Given: generator yielding keys 0, 1, 0, 1 (duplicates)
        $gen = GeneratorFixture::getCombined([0, 1, 0, 1], ['c', 'a', 'b', 'z']);

        // When
        $result = [];
        foreach (Sort::sort($gen) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertCount(4, $result);
        $this->assertEquals(['a', 'b', 'c', 'z'], $result);
    }

    /**
     * @test sort with custom comparator preserves all elements from generator with duplicate keys
     */
    public function testSortGeneratorWithDuplicateKeysCustomComparator(): void
    {
        // Given: generator yielding keys 0, 1, 0, 1 (duplicates)
        $gen = GeneratorFixture::getCombined([0, 1, 0, 1], ['c', 'a', 'b', 'z']);

        // When
        $result = [];
        foreach (Sort::sort($gen, fn ($lhs, $rhs) => $rhs <=> $lhs) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertCount(4, $result);
        $this->assertEquals(['z', 'c', 'b', 'a'], $result);
    }

    /**
     * @test         sort array with NAN sorted last by the default comparison
     * @dataProvider dataProviderForNanSortsLastArray
     * @param        array $data
     * @param        array $expected
     */
    public function testNanSortsLastArray(array $data, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::sort($data) as $datum) {
            $result[] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     */
    public static function dataProviderForNanSortsLastArray(): array
    {
        return [
            'NAN in the middle' => [
                [2, 4, \NAN, 5, 6, 1, 3],
                [1, 2, 3, 4, 5, 6, \NAN],
            ],
            'NAN first' => [
                [\NAN, 3, 1, 2],
                [1, 2, 3, \NAN],
            ],
            'NAN already last' => [
                [3, 1, 2, \NAN],
                [1, 2, 3, \NAN],
            ],
            'several NANs' => [
                [\NAN, 3, \NAN, 1, \NAN, 2],
                [1, 2, 3, \NAN, \NAN, \NAN],
            ],
            'only NANs' => [
                [\NAN, \NAN],
                [\NAN, \NAN],
            ],
            'single NAN' => [
                [\NAN],
                [\NAN],
            ],
            'NAN after INF' => [
                [\INF, \NAN, -\INF, 0.5, -1.5],
                [-\INF, -1.5, 0.5, \INF, \NAN],
            ],
            'NAN among ints and floats, int/float tie kept in input order' => [
                [2.5, \NAN, 1.0, -0.5, 1, 2],
                [-0.5, 1.0, 1, 2, 2.5, \NAN],
            ],
        ];
    }

    /**
     * @test         sort generators with NAN sorted last by the default comparison
     * @dataProvider dataProviderForNanSortsLastGenerators
     * @param        \Generator $data
     * @param        array $expected
     */
    public function testNanSortsLastGenerators(\Generator $data, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::sort($data) as $datum) {
            $result[] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     */
    public static function dataProviderForNanSortsLastGenerators(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            'NAN in the middle' => [
                $gen([2, 4, \NAN, 5, 6, 1, 3]),
                [1, 2, 3, 4, 5, 6, \NAN],
            ],
            'NAN first' => [
                $gen([\NAN, 3, 1, 2]),
                [1, 2, 3, \NAN],
            ],
            'NAN already last' => [
                $gen([3, 1, 2, \NAN]),
                [1, 2, 3, \NAN],
            ],
            'several NANs' => [
                $gen([\NAN, 3, \NAN, 1, \NAN, 2]),
                [1, 2, 3, \NAN, \NAN, \NAN],
            ],
            'only NANs' => [
                $gen([\NAN, \NAN]),
                [\NAN, \NAN],
            ],
            'single NAN' => [
                $gen([\NAN]),
                [\NAN],
            ],
            'NAN after INF' => [
                $gen([\INF, \NAN, -\INF, 0.5, -1.5]),
                [-\INF, -1.5, 0.5, \INF, \NAN],
            ],
            'NAN among ints and floats, int/float tie kept in input order' => [
                $gen([2.5, \NAN, 1.0, -0.5, 1, 2]),
                [-0.5, 1.0, 1, 2, 2.5, \NAN],
            ],
        ];
    }

    /**
     * @test         sort iterators with NAN sorted last by the default comparison
     * @dataProvider dataProviderForNanSortsLastIterators
     * @param        \Iterator $data
     * @param        array $expected
     */
    public function testNanSortsLastIterators(\Iterator $data, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::sort($data) as $datum) {
            $result[] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     */
    public static function dataProviderForNanSortsLastIterators(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            'NAN in the middle' => [
                $iter([2, 4, \NAN, 5, 6, 1, 3]),
                [1, 2, 3, 4, 5, 6, \NAN],
            ],
            'NAN first' => [
                $iter([\NAN, 3, 1, 2]),
                [1, 2, 3, \NAN],
            ],
            'NAN already last' => [
                $iter([3, 1, 2, \NAN]),
                [1, 2, 3, \NAN],
            ],
            'several NANs' => [
                $iter([\NAN, 3, \NAN, 1, \NAN, 2]),
                [1, 2, 3, \NAN, \NAN, \NAN],
            ],
            'only NANs' => [
                $iter([\NAN, \NAN]),
                [\NAN, \NAN],
            ],
            'single NAN' => [
                $iter([\NAN]),
                [\NAN],
            ],
            'NAN after INF' => [
                $iter([\INF, \NAN, -\INF, 0.5, -1.5]),
                [-\INF, -1.5, 0.5, \INF, \NAN],
            ],
            'NAN among ints and floats, int/float tie kept in input order' => [
                $iter([2.5, \NAN, 1.0, -0.5, 1, 2]),
                [-0.5, 1.0, 1, 2, 2.5, \NAN],
            ],
        ];
    }

    /**
     * @test         sort traversables with NAN sorted last by the default comparison
     * @dataProvider dataProviderForNanSortsLastTraversables
     * @param        \Traversable $data
     * @param        array $expected
     */
    public function testNanSortsLastTraversables(\Traversable $data, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::sort($data) as $datum) {
            $result[] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     */
    public static function dataProviderForNanSortsLastTraversables(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            'NAN in the middle' => [
                $trav([2, 4, \NAN, 5, 6, 1, 3]),
                [1, 2, 3, 4, 5, 6, \NAN],
            ],
            'NAN first' => [
                $trav([\NAN, 3, 1, 2]),
                [1, 2, 3, \NAN],
            ],
            'NAN already last' => [
                $trav([3, 1, 2, \NAN]),
                [1, 2, 3, \NAN],
            ],
            'several NANs' => [
                $trav([\NAN, 3, \NAN, 1, \NAN, 2]),
                [1, 2, 3, \NAN, \NAN, \NAN],
            ],
            'only NANs' => [
                $trav([\NAN, \NAN]),
                [\NAN, \NAN],
            ],
            'single NAN' => [
                $trav([\NAN]),
                [\NAN],
            ],
            'NAN after INF' => [
                $trav([\INF, \NAN, -\INF, 0.5, -1.5]),
                [-\INF, -1.5, 0.5, \INF, \NAN],
            ],
            'NAN among ints and floats, int/float tie kept in input order' => [
                $trav([2.5, \NAN, 1.0, -0.5, 1, 2]),
                [-0.5, 1.0, 1, 2, 2.5, \NAN],
            ],
        ];
    }

    /**
     * @test         sort array of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionArray
     * @param        array $data
     * @param        array $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionArray(array $data, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::sort($data) as $datum) {
            $result[] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionArray(): array
    {
        return [
            'int 2^53 + 1 before float 2.0^53, equal as floats' => [
                [9007199254740993, 9007199254740992.0],
                [9007199254740992.0, 9007199254740993],
            ],
            'float 2.0^53 after ints that equal it as floats' => [
                [9007199254740994, 9007199254740993, 9007199254740992.0],
                [9007199254740992.0, 9007199254740993, 9007199254740994],
            ],
            'float 2.0^53 + 2 after int 2^53 + 1' => [
                [9007199254740994.0, 9007199254740993],
                [9007199254740993, 9007199254740994.0],
            ],
            'negative float -2.0^53 before int -(2^53 + 1)' => [
                [-9007199254740992.0, -9007199254740993],
                [-9007199254740993, -9007199254740992.0],
            ],
            'float 2.0^63 before int PHP_INT_MAX' => [
                [9.223372036854776e+18, 9223372036854775807],
                [9223372036854775807, 9.223372036854776e+18],
            ],
            'exact tie 2^53 and 2.0^53 kept in input order' => [
                [9007199254740992, 9007199254740992.0, 9007199254740991],
                [9007199254740991, 9007199254740992, 9007199254740992.0],
            ],
            'NAN after an int beyond 2^53 and a float' => [
                [9007199254740993, \NAN, 9007199254740992.0],
                [9007199254740992.0, 9007199254740993, \NAN],
            ],
        ];
    }

    /**
     * @test         sort generators of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionGenerators
     * @param        \Generator $data
     * @param        array $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionGenerators(\Generator $data, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::sort($data) as $datum) {
            $result[] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionGenerators(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            'int 2^53 + 1 before float 2.0^53, equal as floats' => [
                $gen([9007199254740993, 9007199254740992.0]),
                [9007199254740992.0, 9007199254740993],
            ],
            'float 2.0^53 after ints that equal it as floats' => [
                $gen([9007199254740994, 9007199254740993, 9007199254740992.0]),
                [9007199254740992.0, 9007199254740993, 9007199254740994],
            ],
            'float 2.0^53 + 2 after int 2^53 + 1' => [
                $gen([9007199254740994.0, 9007199254740993]),
                [9007199254740993, 9007199254740994.0],
            ],
            'negative float -2.0^53 before int -(2^53 + 1)' => [
                $gen([-9007199254740992.0, -9007199254740993]),
                [-9007199254740993, -9007199254740992.0],
            ],
            'float 2.0^63 before int PHP_INT_MAX' => [
                $gen([9.223372036854776e+18, 9223372036854775807]),
                [9223372036854775807, 9.223372036854776e+18],
            ],
            'exact tie 2^53 and 2.0^53 kept in input order' => [
                $gen([9007199254740992, 9007199254740992.0, 9007199254740991]),
                [9007199254740991, 9007199254740992, 9007199254740992.0],
            ],
            'NAN after an int beyond 2^53 and a float' => [
                $gen([9007199254740993, \NAN, 9007199254740992.0]),
                [9007199254740992.0, 9007199254740993, \NAN],
            ],
        ];
    }

    /**
     * @test         sort iterators of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionIterators
     * @param        \Iterator $data
     * @param        array $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionIterators(\Iterator $data, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::sort($data) as $datum) {
            $result[] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionIterators(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            'int 2^53 + 1 before float 2.0^53, equal as floats' => [
                $iter([9007199254740993, 9007199254740992.0]),
                [9007199254740992.0, 9007199254740993],
            ],
            'float 2.0^53 after ints that equal it as floats' => [
                $iter([9007199254740994, 9007199254740993, 9007199254740992.0]),
                [9007199254740992.0, 9007199254740993, 9007199254740994],
            ],
            'float 2.0^53 + 2 after int 2^53 + 1' => [
                $iter([9007199254740994.0, 9007199254740993]),
                [9007199254740993, 9007199254740994.0],
            ],
            'negative float -2.0^53 before int -(2^53 + 1)' => [
                $iter([-9007199254740992.0, -9007199254740993]),
                [-9007199254740993, -9007199254740992.0],
            ],
            'float 2.0^63 before int PHP_INT_MAX' => [
                $iter([9.223372036854776e+18, 9223372036854775807]),
                [9223372036854775807, 9.223372036854776e+18],
            ],
            'exact tie 2^53 and 2.0^53 kept in input order' => [
                $iter([9007199254740992, 9007199254740992.0, 9007199254740991]),
                [9007199254740991, 9007199254740992, 9007199254740992.0],
            ],
            'NAN after an int beyond 2^53 and a float' => [
                $iter([9007199254740993, \NAN, 9007199254740992.0]),
                [9007199254740992.0, 9007199254740993, \NAN],
            ],
        ];
    }

    /**
     * @test         sort traversables of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionTraversables
     * @param        \Traversable $data
     * @param        array $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionTraversables(\Traversable $data, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::sort($data) as $datum) {
            $result[] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionTraversables(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            'int 2^53 + 1 before float 2.0^53, equal as floats' => [
                $trav([9007199254740993, 9007199254740992.0]),
                [9007199254740992.0, 9007199254740993],
            ],
            'float 2.0^53 after ints that equal it as floats' => [
                $trav([9007199254740994, 9007199254740993, 9007199254740992.0]),
                [9007199254740992.0, 9007199254740993, 9007199254740994],
            ],
            'float 2.0^53 + 2 after int 2^53 + 1' => [
                $trav([9007199254740994.0, 9007199254740993]),
                [9007199254740993, 9007199254740994.0],
            ],
            'negative float -2.0^53 before int -(2^53 + 1)' => [
                $trav([-9007199254740992.0, -9007199254740993]),
                [-9007199254740993, -9007199254740992.0],
            ],
            'float 2.0^63 before int PHP_INT_MAX' => [
                $trav([9.223372036854776e+18, 9223372036854775807]),
                [9223372036854775807, 9.223372036854776e+18],
            ],
            'exact tie 2^53 and 2.0^53 kept in input order' => [
                $trav([9007199254740992, 9007199254740992.0, 9007199254740991]),
                [9007199254740991, 9007199254740992, 9007199254740992.0],
            ],
            'NAN after an int beyond 2^53 and a float' => [
                $trav([9007199254740993, \NAN, 9007199254740992.0]),
                [9007199254740992.0, 9007199254740993, \NAN],
            ],
        ];
    }

    /**
     * @test         sort array with a custom comparator, which is used as given when a value is NAN
     * @dataProvider dataProviderForCustomComparatorWithNanArray
     * @param        array $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithNanArray(array $data, callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort and the same NAN-first comparator:
     *   sorted(data, key=cmp_to_key(nan_first))
     * where nan_first(a, b) puts a NAN before every other value and compares other values with a <=> b.
     */
    public static function dataProviderForCustomComparatorWithNanArray(): array
    {
        return [
            'NAN-first comparator' => [
                [2, \NAN, 1],
                fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs,
                [\NAN, 1, 2],
            ],
            'NAN-first comparator, several NANs' => [
                [\NAN, 3, \NAN, 1],
                fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs,
                [\NAN, \NAN, 1, 3],
            ],
        ];
    }

    /**
     * @test         sort array with a custom comparator, which is used as given for an int beyond 2^53 and a float
     * @dataProvider dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionArray
     * @param        array $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithIntAndFloatBeyondFloatPrecisionArray(array $data, callable $comparator, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's stable sort and the comparison PHP's <=> makes, which compares two
     * ints exactly and an int and a float as floats, so it ties 2^53 + 1 with 2.0^53:
     *   sorted(data, key=cmp_to_key(php_spaceship))
     */
    public static function dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionArray(): array
    {
        return [
            '<=> ties int 2^53 + 1 with float 2.0^53, kept in input order' => [
                [9007199254740993, 9007199254740992.0],
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                [9007199254740993, 9007199254740992.0],
            ],
            '<=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                [9007199254740994, 9007199254740993, 9007199254740992.0],
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                [9007199254740993, 9007199254740992.0, 9007199254740994],
            ],
        ];
    }

    /**
     * @test         sort generators with a custom comparator, which is used as given when a value is NAN
     * @dataProvider dataProviderForCustomComparatorWithNanGenerators
     * @param        \Generator $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithNanGenerators(\Generator $data, callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort and the same NAN-first comparator:
     *   sorted(data, key=cmp_to_key(nan_first))
     * where nan_first(a, b) puts a NAN before every other value and compares other values with a <=> b.
     */
    public static function dataProviderForCustomComparatorWithNanGenerators(): array
    {
        return [
            'NAN-first comparator' => [
                GeneratorFixture::getGenerator([2, \NAN, 1]),
                fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs,
                [\NAN, 1, 2],
            ],
            'NAN-first comparator, several NANs' => [
                GeneratorFixture::getGenerator([\NAN, 3, \NAN, 1]),
                fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs,
                [\NAN, \NAN, 1, 3],
            ],
        ];
    }

    /**
     * @test         sort generators with a custom comparator, which is used as given for an int beyond 2^53 and a float
     * @dataProvider dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionGenerators
     * @param        \Generator $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithIntAndFloatBeyondFloatPrecisionGenerators(\Generator $data, callable $comparator, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's stable sort and the comparison PHP's <=> makes, which compares two
     * ints exactly and an int and a float as floats, so it ties 2^53 + 1 with 2.0^53:
     *   sorted(data, key=cmp_to_key(php_spaceship))
     */
    public static function dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionGenerators(): array
    {
        return [
            '<=> ties int 2^53 + 1 with float 2.0^53, kept in input order' => [
                GeneratorFixture::getGenerator([9007199254740993, 9007199254740992.0]),
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                [9007199254740993, 9007199254740992.0],
            ],
            '<=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                GeneratorFixture::getGenerator([9007199254740994, 9007199254740993, 9007199254740992.0]),
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                [9007199254740993, 9007199254740992.0, 9007199254740994],
            ],
        ];
    }

    /**
     * @test         sort iterators with a custom comparator, which is used as given when a value is NAN
     * @dataProvider dataProviderForCustomComparatorWithNanIterators
     * @param        \Iterator $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithNanIterators(\Iterator $data, callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort and the same NAN-first comparator:
     *   sorted(data, key=cmp_to_key(nan_first))
     * where nan_first(a, b) puts a NAN before every other value and compares other values with a <=> b.
     */
    public static function dataProviderForCustomComparatorWithNanIterators(): array
    {
        return [
            'NAN-first comparator' => [
                new ArrayIteratorFixture([2, \NAN, 1]),
                fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs,
                [\NAN, 1, 2],
            ],
            'NAN-first comparator, several NANs' => [
                new ArrayIteratorFixture([\NAN, 3, \NAN, 1]),
                fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs,
                [\NAN, \NAN, 1, 3],
            ],
        ];
    }

    /**
     * @test         sort iterators with a custom comparator, which is used as given for an int beyond 2^53 and a float
     * @dataProvider dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionIterators
     * @param        \Iterator $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithIntAndFloatBeyondFloatPrecisionIterators(\Iterator $data, callable $comparator, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's stable sort and the comparison PHP's <=> makes, which compares two
     * ints exactly and an int and a float as floats, so it ties 2^53 + 1 with 2.0^53:
     *   sorted(data, key=cmp_to_key(php_spaceship))
     */
    public static function dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionIterators(): array
    {
        return [
            '<=> ties int 2^53 + 1 with float 2.0^53, kept in input order' => [
                new ArrayIteratorFixture([9007199254740993, 9007199254740992.0]),
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                [9007199254740993, 9007199254740992.0],
            ],
            '<=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                new ArrayIteratorFixture([9007199254740994, 9007199254740993, 9007199254740992.0]),
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                [9007199254740993, 9007199254740992.0, 9007199254740994],
            ],
        ];
    }

    /**
     * @test         sort traversables with a custom comparator, which is used as given when a value is NAN
     * @dataProvider dataProviderForCustomComparatorWithNanTraversables
     * @param        \Traversable $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithNanTraversables(\Traversable $data, callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort and the same NAN-first comparator:
     *   sorted(data, key=cmp_to_key(nan_first))
     * where nan_first(a, b) puts a NAN before every other value and compares other values with a <=> b.
     */
    public static function dataProviderForCustomComparatorWithNanTraversables(): array
    {
        return [
            'NAN-first comparator' => [
                new IteratorAggregateFixture([2, \NAN, 1]),
                fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs,
                [\NAN, 1, 2],
            ],
            'NAN-first comparator, several NANs' => [
                new IteratorAggregateFixture([\NAN, 3, \NAN, 1]),
                fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs,
                [\NAN, \NAN, 1, 3],
            ],
        ];
    }

    /**
     * @test         sort traversables with a custom comparator, which is used as given for an int beyond 2^53 and a float
     * @dataProvider dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionTraversables
     * @param        \Traversable $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithIntAndFloatBeyondFloatPrecisionTraversables(\Traversable $data, callable $comparator, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::sort($data, $comparator) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's stable sort and the comparison PHP's <=> makes, which compares two
     * ints exactly and an int and a float as floats, so it ties 2^53 + 1 with 2.0^53:
     *   sorted(data, key=cmp_to_key(php_spaceship))
     */
    public static function dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionTraversables(): array
    {
        return [
            '<=> ties int 2^53 + 1 with float 2.0^53, kept in input order' => [
                new IteratorAggregateFixture([9007199254740993, 9007199254740992.0]),
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                [9007199254740993, 9007199254740992.0],
            ],
            '<=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                new IteratorAggregateFixture([9007199254740994, 9007199254740993, 9007199254740992.0]),
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                [9007199254740993, 9007199254740992.0, 9007199254740994],
            ],
        ];
    }
}

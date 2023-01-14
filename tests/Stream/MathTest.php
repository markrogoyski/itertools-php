<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class MathTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array $input
     * @param callable $chainMaker
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForArray
     */
    public function testArray(array $input, callable $chainMaker, array $expected): void
    {
        // Given
        // When
        $result = $chainMaker($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1],
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                [3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                [1, 3, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 6],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->runningTotal()
                    ->toArray(),
                [1, 2, 4, 6, 9, 12],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->toArray(),
                [1, -1, -1, -2, -2, -3],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->toArray(),
                [-1, -3, -6, -10, -15],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [-1, -1, -1, -1, -1],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->runningTotal()
                    ->toArray(),
                [1, 0, -1, -3, -5, -8],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->toArray(),
                [1, 2, 6, 24, 120],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [1, 1, 1, 1, 1],
            ],
        ];
    }

    /**
     * @param \Generator $input
     * @param callable $chainMaker
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForGenerator
     */
    public function testGenerator(\Generator $input, callable $chainMaker, array $expected): void
    {
        // Given
        // When
        $result = $chainMaker($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGenerator(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1],
            ],
            [
                $gen([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                $gen([3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                $gen([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 6],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->runningTotal()
                    ->toArray(),
                [1, 2, 4, 6, 9, 12],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->toArray(),
                [1, -1, -1, -2, -2, -3],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->toArray(),
                [-1, -3, -6, -10, -15],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [-1, -1, -1, -1, -1],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->runningTotal()
                    ->toArray(),
                [1, 0, -1, -3, -5, -8],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->toArray(),
                [1, 2, 6, 24, 120],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [1, 1, 1, 1, 1],
            ],
        ];
    }

    /**
     * @param \Iterator $input
     * @param callable $chainMaker
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForIterator
     */
    public function testIterator(\Iterator $input, callable $chainMaker, array $expected): void
    {
        // Given
        // When
        $result = $chainMaker($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIterator(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1],
            ],
            [
                $iter([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                $iter([3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                $iter([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 6],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->runningTotal()
                    ->toArray(),
                [1, 2, 4, 6, 9, 12],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->toArray(),
                [1, -1, -1, -2, -2, -3],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->toArray(),
                [-1, -3, -6, -10, -15],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [-1, -1, -1, -1, -1],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->runningTotal()
                    ->toArray(),
                [1, 0, -1, -3, -5, -8],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->toArray(),
                [1, 2, 6, 24, 120],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [1, 1, 1, 1, 1],
            ],
        ];
    }

    /**
     * @param \Traversable $input
     * @param callable $chainMaker
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForTraversable
     */
    public function testTraversable(\Traversable $input, callable $chainMaker, array $expected): void
    {
        // Given
        // When
        $result = $chainMaker($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversable(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1],
            ],
            [
                $trav([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                $trav([3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage(1)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                $trav([1, 3, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningAverage()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 6],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMax()
                    ->runningTotal()
                    ->toArray(),
                [1, 2, 4, 6, 9, 12],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->toArray(),
                [1, -1, -1, -2, -2, -3],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->toArray(),
                [-1, -3, -6, -10, -15],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [-1, -4, -10, -20, -35],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningDifference()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [-1, -1, -1, -1, -1],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningMin()
                    ->runningTotal()
                    ->toArray(),
                [1, 0, -1, -3, -5, -8],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)->runningProduct()
                    ->toArray(),
                [1, 2, 6, 24, 120],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMax()
                    ->toArray(),
                [1, 3, 9, 33, 153],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->runningProduct()
                    ->runningTotal()
                    ->runningMin()
                    ->toArray(),
                [1, 1, 1, 1, 1],
            ],
        ];
    }
}

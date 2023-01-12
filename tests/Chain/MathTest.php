<?php

declare(strict_types=1);

namespace IterTools\Tests\Chain;

use IterTools\Chain;
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
        $result = [];

        // When
        $chain = $chainMaker($input);
        foreach ($chain as $key => $value) {
            $result[$key] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage();
                },
                [],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage(1);
                },
                [1],
            ],
            [
                [1, 3, 5],
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage();
                },
                [1, 2, 3],
            ],
            [
                [3, 5],
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage(1);
                },
                [1, 2, 3],
            ],
            [
                [1, 3, 5],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningAverage()
                        ->runningTotal();
                },
                [1, 3, 6],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningMax();
                },
                [1, 1, 2, 2, 3, 3],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningMax()
                        ->runningTotal();
                },
                [1, 2, 4, 6, 9, 12],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningMin();
                },
                [1, -1, -1, -2, -2, -3],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningDifference();
                },
                [-1, -3, -6, -10, -15],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningDifference()
                        ->runningTotal();
                },
                [-1, -4, -10, -20, -35],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningDifference()
                        ->runningTotal()
                        ->runningMin();
                },
                [-1, -4, -10, -20, -35],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningDifference()
                        ->runningTotal()
                        ->runningMax();
                },
                [-1, -1, -1, -1, -1],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningMin()
                        ->runningTotal();
                },
                [1, 0, -1, -3, -5, -8],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningProduct();
                },
                [1, 2, 6, 24, 120],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal();
                },
                [1, 3, 9, 33, 153],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal()
                        ->runningMax();
                },
                [1, 3, 9, 33, 153],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal()
                        ->runningMin();
                },
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
        $result = [];

        // When
        $chain = $chainMaker($input);
        foreach ($chain as $key => $value) {
            $result[$key] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGenerator(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage();
                },
                [],
            ],
            [
                $gen([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage(1);
                },
                [1],
            ],
            [
                $gen([1, 3, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage();
                },
                [1, 2, 3],
            ],
            [
                $gen([3, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage(1);
                },
                [1, 2, 3],
            ],
            [
                $gen([1, 3, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningAverage()
                        ->runningTotal();
                },
                [1, 3, 6],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningMax();
                },
                [1, 1, 2, 2, 3, 3],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningMax()
                        ->runningTotal();
                },
                [1, 2, 4, 6, 9, 12],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningMin();
                },
                [1, -1, -1, -2, -2, -3],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningDifference();
                },
                [-1, -3, -6, -10, -15],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningDifference()
                        ->runningTotal();
                },
                [-1, -4, -10, -20, -35],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningDifference()
                        ->runningTotal()
                        ->runningMin();
                },
                [-1, -4, -10, -20, -35],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningDifference()
                        ->runningTotal()
                        ->runningMax();
                },
                [-1, -1, -1, -1, -1],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningMin()
                        ->runningTotal();
                },
                [1, 0, -1, -3, -5, -8],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningProduct();
                },
                [1, 2, 6, 24, 120],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal();
                },
                [1, 3, 9, 33, 153],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal()
                        ->runningMax();
                },
                [1, 3, 9, 33, 153],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal()
                        ->runningMin();
                },
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
        $result = [];

        // When
        $chain = $chainMaker($input);
        foreach ($chain as $key => $value) {
            $result[$key] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIterator(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage();
                },
                [],
            ],
            [
                $iter([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage(1);
                },
                [1],
            ],
            [
                $iter([1, 3, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage();
                },
                [1, 2, 3],
            ],
            [
                $iter([3, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage(1);
                },
                [1, 2, 3],
            ],
            [
                $iter([1, 3, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningAverage()
                        ->runningTotal();
                },
                [1, 3, 6],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningMax();
                },
                [1, 1, 2, 2, 3, 3],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningMax()
                        ->runningTotal();
                },
                [1, 2, 4, 6, 9, 12],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningMin();
                },
                [1, -1, -1, -2, -2, -3],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningDifference();
                },
                [-1, -3, -6, -10, -15],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningDifference()
                        ->runningTotal();
                },
                [-1, -4, -10, -20, -35],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningDifference()
                        ->runningTotal()
                        ->runningMin();
                },
                [-1, -4, -10, -20, -35],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningDifference()
                        ->runningTotal()
                        ->runningMax();
                },
                [-1, -1, -1, -1, -1],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningMin()
                        ->runningTotal();
                },
                [1, 0, -1, -3, -5, -8],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningProduct();
                },
                [1, 2, 6, 24, 120],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal();
                },
                [1, 3, 9, 33, 153],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal()
                        ->runningMax();
                },
                [1, 3, 9, 33, 153],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal()
                        ->runningMin();
                },
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
        $result = [];

        // When
        $chain = $chainMaker($input);
        foreach ($chain as $key => $value) {
            $result[$key] = $value;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversable(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage();
                },
                [],
            ],
            [
                $trav([]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage(1);
                },
                [1],
            ],
            [
                $trav([1, 3, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage();
                },
                [1, 2, 3],
            ],
            [
                $trav([3, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningAverage(1);
                },
                [1, 2, 3],
            ],
            [
                $trav([1, 3, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningAverage()
                        ->runningTotal();
                },
                [1, 3, 6],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningMax();
                },
                [1, 1, 2, 2, 3, 3],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningMax()
                        ->runningTotal();
                },
                [1, 2, 4, 6, 9, 12],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningMin();
                },
                [1, -1, -1, -2, -2, -3],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningDifference();
                },
                [-1, -3, -6, -10, -15],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningDifference()
                        ->runningTotal();
                },
                [-1, -4, -10, -20, -35],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningDifference()
                        ->runningTotal()
                        ->runningMin();
                },
                [-1, -4, -10, -20, -35],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningDifference()
                        ->runningTotal()
                        ->runningMax();
                },
                [-1, -1, -1, -1, -1],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningMin()
                        ->runningTotal();
                },
                [1, 0, -1, -3, -5, -8],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)->runningProduct();
                },
                [1, 2, 6, 24, 120],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal();
                },
                [1, 3, 9, 33, 153],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal()
                        ->runningMax();
                },
                [1, 3, 9, 33, 153],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->runningProduct()
                        ->runningTotal()
                        ->runningMin();
                },
                [1, 1, 1, 1, 1],
            ],
        ];
    }
}

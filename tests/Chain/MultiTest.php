<?php

declare(strict_types=1);

namespace IterTools\Tests\Chain;

use IterTools\Chain;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class MultiTest extends \PHPUnit\Framework\TestCase
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
        foreach ($chain as $value) {
            $result[] = $value;
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
                    return Chain::create($iterable)->zipWith([], []);
                },
                [],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)->zipEqualWith([], []);
                },
                [],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)->zipLongestWith([], []);
                },
                [],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->zipWith(
                            [11, 22, 33],
                            [111, 222, 333, 444]
                        );
                },
                [],
            ],
            [
                [],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->zipLongestWith(
                            [11, 22, 33],
                            [111, 222, 333, 444]
                        );
                },
                [
                    [null, 11, 111],
                    [null, 22, 222],
                    [null, 33, 333],
                    [null, null, 444],
                ],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->zipWith(
                            [11, 22, 33],
                            [111, 222, 333, 444]
                        );
                },
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                [1, 2, 3],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->zipEqualWith(
                            [11, 22, 33],
                            [111, 222, 333]
                        );
                },
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                [1, 2, 3, 4, 5],
                static function (iterable $iterable) {
                    return Chain::create($iterable)
                        ->zipLongestWith(
                            [11, 22, 33],
                            [111, 222, 333, 444]
                        );
                },
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                    [4, null, 444],
                    [5, null, null],
                ],
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
        foreach ($chain as $value) {
            $result[] = $value;
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
                static function (iterable $iterable) use ($gen) {
                    return Chain::create($iterable)->zipWith($gen([]), $gen([]));
                },
                [],
            ],
            [
                $gen([]),
                static function (iterable $iterable) use ($gen) {
                    return Chain::create($iterable)->zipEqualWith($gen([]), $gen([]));
                },
                [],
            ],
            [
                $gen([]),
                static function (iterable $iterable) use ($gen) {
                    return Chain::create($iterable)->zipLongestWith($gen([]), $gen([]));
                },
                [],
            ],
            [
                $gen([]),
                static function (iterable $iterable) use ($gen) {
                    return Chain::create($iterable)
                        ->zipWith(
                            $gen([11, 22, 33]),
                            $gen([111, 222, 333, 444])
                        );
                },
                [],
            ],
            [
                $gen([]),
                static function (iterable $iterable) use ($gen) {
                    return Chain::create($iterable)
                        ->zipLongestWith(
                            $gen([11, 22, 33]),
                            $gen([111, 222, 333, 444])
                        );
                },
                [
                    [null, 11, 111],
                    [null, 22, 222],
                    [null, 33, 333],
                    [null, null, 444],
                ],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) use ($gen) {
                    return Chain::create($iterable)
                        ->zipWith(
                            $gen([11, 22, 33]),
                            $gen([111, 222, 333, 444])
                        );
                },
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                $gen([1, 2, 3]),
                static function (iterable $iterable) use ($gen) {
                    return Chain::create($iterable)
                        ->zipEqualWith(
                            $gen([11, 22, 33]),
                            $gen([111, 222, 333])
                        );
                },
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                static function (iterable $iterable) use ($gen) {
                    return Chain::create($iterable)
                        ->zipLongestWith(
                            $gen([11, 22, 33]),
                            $gen([111, 222, 333, 444])
                        );
                },
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                    [4, null, 444],
                    [5, null, null],
                ],
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
        foreach ($chain as $value) {
            $result[] = $value;
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
                static function (iterable $iterable) use ($iter) {
                    return Chain::create($iterable)->zipWith($iter([]), $iter([]));
                },
                [],
            ],
            [
                $iter([]),
                static function (iterable $iterable) use ($iter) {
                    return Chain::create($iterable)->zipEqualWith($iter([]), $iter([]));
                },
                [],
            ],
            [
                $iter([]),
                static function (iterable $iterable) use ($iter) {
                    return Chain::create($iterable)->zipLongestWith($iter([]), $iter([]));
                },
                [],
            ],
            [
                $iter([]),
                static function (iterable $iterable) use ($iter) {
                    return Chain::create($iterable)
                        ->zipWith(
                            $iter([11, 22, 33]),
                            $iter([111, 222, 333, 444])
                        );
                },
                [],
            ],
            [
                $iter([]),
                static function (iterable $iterable) use ($iter) {
                    return Chain::create($iterable)
                        ->zipLongestWith(
                            $iter([11, 22, 33]),
                            $iter([111, 222, 333, 444])
                        );
                },
                [
                    [null, 11, 111],
                    [null, 22, 222],
                    [null, 33, 333],
                    [null, null, 444],
                ],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) use ($iter) {
                    return Chain::create($iterable)
                        ->zipWith(
                            $iter([11, 22, 33]),
                            $iter([111, 222, 333, 444])
                        );
                },
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                $iter([1, 2, 3]),
                static function (iterable $iterable) use ($iter) {
                    return Chain::create($iterable)
                        ->zipEqualWith(
                            $iter([11, 22, 33]),
                            $iter([111, 222, 333])
                        );
                },
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                static function (iterable $iterable) use ($iter) {
                    return Chain::create($iterable)
                        ->zipLongestWith(
                            $iter([11, 22, 33]),
                            $iter([111, 222, 333, 444])
                        );
                },
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                    [4, null, 444],
                    [5, null, null],
                ],
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
        foreach ($chain as $value) {
            $result[] = $value;
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
                static function (iterable $iterable) use ($trav) {
                    return Chain::create($iterable)->zipWith($trav([]), $trav([]));
                },
                [],
            ],
            [
                $trav([]),
                static function (iterable $iterable) use ($trav) {
                    return Chain::create($iterable)->zipEqualWith($trav([]), $trav([]));
                },
                [],
            ],
            [
                $trav([]),
                static function (iterable $iterable) use ($trav) {
                    return Chain::create($iterable)->zipLongestWith($trav([]), $trav([]));
                },
                [],
            ],
            [
                $trav([]),
                static function (iterable $iterable) use ($trav) {
                    return Chain::create($iterable)
                        ->zipWith(
                            $trav([11, 22, 33]),
                            $trav([111, 222, 333, 444])
                        );
                },
                [],
            ],
            [
                $trav([]),
                static function (iterable $iterable) use ($trav) {
                    return Chain::create($iterable)
                        ->zipLongestWith(
                            $trav([11, 22, 33]),
                            $trav([111, 222, 333, 444])
                        );
                },
                [
                    [null, 11, 111],
                    [null, 22, 222],
                    [null, 33, 333],
                    [null, null, 444],
                ],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) use ($trav) {
                    return Chain::create($iterable)
                        ->zipWith(
                            $trav([11, 22, 33]),
                            $trav([111, 222, 333, 444])
                        );
                },
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                $trav([1, 2, 3]),
                static function (iterable $iterable) use ($trav) {
                    return Chain::create($iterable)
                        ->zipEqualWith(
                            $trav([11, 22, 33]),
                            $trav([111, 222, 333])
                        );
                },
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                static function (iterable $iterable) use ($trav) {
                    return Chain::create($iterable)
                        ->zipLongestWith(
                            $trav([11, 22, 33]),
                            $trav([111, 222, 333, 444])
                        );
                },
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                    [4, null, 444],
                    [5, null, null],
                ],
            ],
        ];
    }
}

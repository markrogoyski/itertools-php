<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class MultiTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array    $input
     * @param callable $streamFactoryFunc
     * @param array    $expected
     * @return void
     * @dataProvider dataProviderForArray
     */
    public function testArray(array $input, callable $streamFactoryFunc, array $expected): void
    {
        // When
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith([], [])
                    ->toArray(),
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipEqualWith([], [])
                    ->toArray(),
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith([], [])
                    ->toArray(),
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        [11, 22, 33],
                        [111, 222, 333, 444]
                    )
                    ->toArray(),
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith(
                        [11, 22, 33],
                        [111, 222, 333, 444]
                    )
                    ->toArray(),
                [
                    [null, 11, 111],
                    [null, 22, 222],
                    [null, 33, 333],
                    [null, null, 444],
                ],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        [11, 22, 33],
                        [111, 222, 333, 444]
                    )
                    ->toArray(),
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipEqualWith(
                        [11, 22, 33],
                        [111, 222, 333]
                    )
                    ->toArray(),
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith(
                        [11, 22, 33],
                        [111, 222, 333, 444]
                    )
                    ->toArray(),
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
     * @param callable   $streamFactoryFunc
     * @param array      $expected
     * @return void
     * @dataProvider dataProviderForGenerator
     */
    public function testGenerator(\Generator $input, callable $streamFactoryFunc, array $expected): void
    {
        // When
        $result = $streamFactoryFunc($input);

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
                    ->zipWith($gen([]), $gen([]))
                    ->toArray(),
                [],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipEqualWith($gen([]), $gen([]))
                    ->toArray(),
                [],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith($gen([]), $gen([]))
                    ->toArray(),
                [],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        $gen([11, 22, 33]),
                        $gen([111, 222, 333, 444])
                    )
                    ->toArray(),
                [],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith(
                        $gen([11, 22, 33]),
                        $gen([111, 222, 333, 444])
                    )
                    ->toArray(),
                [
                    [null, 11, 111],
                    [null, 22, 222],
                    [null, 33, 333],
                    [null, null, 444],
                ],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        $gen([11, 22, 33]),
                        $gen([111, 222, 333, 444])
                    )
                    ->toArray(),
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipEqualWith(
                        $gen([11, 22, 33]),
                        $gen([111, 222, 333])
                    )
                    ->toArray(),
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith(
                        $gen([11, 22, 33]),
                        $gen([111, 222, 333, 444])
                    )
                    ->toArray(),
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
     * @param callable  $streamFactoryFunc
     * @param array     $expected
     * @return void
     * @dataProvider dataProviderForIterator
     */
    public function testIterator(\Iterator $input, callable $streamFactoryFunc, array $expected): void
    {
        // When
        $result = $streamFactoryFunc($input);

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
                    ->zipWith($iter([]), $iter([]))
                    ->toArray(),
                [],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipEqualWith($iter([]), $iter([]))
                    ->toArray(),
                [],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith($iter([]), $iter([]))
                    ->toArray(),
                [],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        $iter([11, 22, 33]),
                        $iter([111, 222, 333, 444])
                    )
                    ->toArray(),
                [],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith(
                        $iter([11, 22, 33]),
                        $iter([111, 222, 333, 444])
                    )
                    ->toArray(),
                [
                    [null, 11, 111],
                    [null, 22, 222],
                    [null, 33, 333],
                    [null, null, 444],
                ],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        $iter([11, 22, 33]),
                        $iter([111, 222, 333, 444])
                    )
                    ->toArray(),
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipEqualWith(
                        $iter([11, 22, 33]),
                        $iter([111, 222, 333])
                    )
                    ->toArray(),
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith(
                        $iter([11, 22, 33]),
                        $iter([111, 222, 333, 444])
                    )
                    ->toArray(),
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
     * @param callable     $streamFactoryFunc
     * @param array        $expected
     * @return void
     * @dataProvider dataProviderForTraversable
     */
    public function testTraversable(\Traversable $input, callable $streamFactoryFunc, array $expected): void
    {
        // When
        $result = $streamFactoryFunc($input);

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
                    ->zipWith($trav([]), $trav([]))
                    ->toArray(),
                [],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipEqualWith($trav([]), $trav([]))
                    ->toArray(),
                [],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith($trav([]), $trav([]))
                    ->toArray(),
                [],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        $trav([11, 22, 33]),
                        $trav([111, 222, 333, 444])
                    )
                    ->toArray(),
                [],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith(
                        $trav([11, 22, 33]),
                        $trav([111, 222, 333, 444])
                    )
                    ->toArray(),
                [
                    [null, 11, 111],
                    [null, 22, 222],
                    [null, 33, 333],
                    [null, null, 444],
                ],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        $trav([11, 22, 33]),
                        $trav([111, 222, 333, 444])
                    )
                    ->toArray(),
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipEqualWith(
                        $trav([11, 22, 33]),
                        $trav([111, 222, 333])
                    )
                    ->toArray(),
                [
                    [1, 11, 111],
                    [2, 22, 222],
                    [3, 33, 333],
                ],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith(
                        $trav([11, 22, 33]),
                        $trav([111, 222, 333, 444])
                    )
                    ->toArray(),
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

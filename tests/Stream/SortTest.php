<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class SortTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array $input
     * @param callable $streamFactoryFunc
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForArray
     */
    public function testArray(array $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [-3, -2, 1, 2, 2, 3, 3, 5, 7],
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toArray(),
                [-3, -2, 1, 2, 2, 3, 3, 5, 7],
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toArray(),
                [7, 5, 3, 3, 2, 2, 1, -2, -3],
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toArray(),
                [-3, -2, 1, 2, 2, 3, 3, 5, 7],
            ],
            [
                [2, 3, 1, 2.5, -3, -2, 5, 7, 3.5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                [4 => -3, 5 => -2, 2 => 1, 0 => 2, 3 =>  2.5, 1 => 3, 8 => 3.5, 6 => 5, 7 => 7],
            ],
            [
                ['a' => 2, 'b' => 3, 'c' => 1, 'd' => 2.5, 'e' => -3, 'f' => -2, 'g' => 5, 'h' => 7, 'i' => 3.5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['e' => -3, 'f' => -2, 'c' => 1, 'a' => 2, 'd' =>  2.5, 'b' => 3, 'i' => 3.5, 'g' => 5, 'h' => 7],
            ],
            [
                [2, 3, 1, 2.5, -3, -2, 5, 7, 3.5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toArray(),
                [-3, -2, 1, 2, 2.5, 3, 3.5, 5, 7],
            ],
            [
                [2, 3, 1, 2.5, -3, -2, 5, 7, 3.5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toAssociativeArray(),
                [4 => -3, 5 => -2, 2 => 1, 0 => 2, 3 => 2.5, 1 => 3, 8 => 3.5, 6 => 5, 7 => 7],
            ],
            [
                ['a' => 2, 'b' => 3, 'c' => 1, 'd' => 2.5, 'e' => -3, 'f' => -2, 'g' => 5, 'h' => 7, 'i' => 3.5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toAssociativeArray(),
                ['e' => -3, 'f' => -2, 'c' => 1, 'a' => 2, 'd' =>  2.5, 'b' => 3, 'i' => 3.5, 'g' => 5, 'h' => 7],
            ],
            [
                [2, 3, 1, 2.5, -3, -2, 5, 7, 3.5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toArray(),
                [7, 5, 3.5, 3, 2.5, 2, 1, -2, -3],
            ],
            [
                [2, 3, 1, 2.5, -3, -2, 5, 7, 3.5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toAssociativeArray(),
                [7 => 7, 6 => 5, 8 => 3.5, 1 => 3, 3 => 2.5, 0 => 2, 2 => 1, 5 => -2, 4 => -3],
            ],
            [
                ['a' => 2, 'b' => 3, 'c' => 1, 'd' => 2.5, 'e' => -3, 'f' => -2, 'g' => 5, 'h' => 7, 'i' => 3.5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toAssociativeArray(),
                ['h' => 7, 'g' => 5, 'i' => 3.5, 'b' => 3, 'd' => 2.5, 'a' => 2, 'c' => 1, 'f' => -2, 'e' => -3],
            ],
        ];
    }

    /**
     * @param \Generator $input
     * @param callable $streamFactoryFunc
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForGenerator
     */
    public function testGenerator(\Generator $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGenerator(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [-3, -2, 1, 2, 2, 3, 3, 5, 7],
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toArray(),
                [-3, -2, 1, 2, 2, 3, 3, 5, 7],
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toArray(),
                [7, 5, 3, 3, 2, 2, 1, -2, -3],
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toArray(),
                [-3, -2, 1, 2, 2, 3, 3, 5, 7],
            ],
            [
                $gen([2, 3, 1, 2.5, -3, -2, 5, 7, 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                [4 => -3, 5 => -2, 2 => 1, 0 => 2, 3 =>  2.5, 1 => 3, 8 => 3.5, 6 => 5, 7 => 7],
            ],
            [
                $gen(['a' => 2, 'b' => 3, 'c' => 1, 'd' => 2.5, 'e' => -3, 'f' => -2, 'g' => 5, 'h' => 7, 'i' => 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['e' => -3, 'f' => -2, 'c' => 1, 'a' => 2, 'd' =>  2.5, 'b' => 3, 'i' => 3.5, 'g' => 5, 'h' => 7],
            ],
            [
                $gen([2, 3, 1, 2.5, -3, -2, 5, 7, 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toArray(),
                [-3, -2, 1, 2, 2.5, 3, 3.5, 5, 7],
            ],
            [
                $gen([2, 3, 1, 2.5, -3, -2, 5, 7, 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toAssociativeArray(),
                [4 => -3, 5 => -2, 2 => 1, 0 => 2, 3 => 2.5, 1 => 3, 8 => 3.5, 6 => 5, 7 => 7],
            ],
            [
                $gen(['a' => 2, 'b' => 3, 'c' => 1, 'd' => 2.5, 'e' => -3, 'f' => -2, 'g' => 5, 'h' => 7, 'i' => 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toAssociativeArray(),
                ['e' => -3, 'f' => -2, 'c' => 1, 'a' => 2, 'd' =>  2.5, 'b' => 3, 'i' => 3.5, 'g' => 5, 'h' => 7],
            ],
            [
                $gen([2, 3, 1, 2.5, -3, -2, 5, 7, 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toArray(),
                [7, 5, 3.5, 3, 2.5, 2, 1, -2, -3],
            ],
            [
                $gen([2, 3, 1, 2.5, -3, -2, 5, 7, 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toAssociativeArray(),
                [7 => 7, 6 => 5, 8 => 3.5, 1 => 3, 3 => 2.5, 0 => 2, 2 => 1, 5 => -2, 4 => -3],
            ],
            [
                $gen(['a' => 2, 'b' => 3, 'c' => 1, 'd' => 2.5, 'e' => -3, 'f' => -2, 'g' => 5, 'h' => 7, 'i' => 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toAssociativeArray(),
                ['h' => 7, 'g' => 5, 'i' => 3.5, 'b' => 3, 'd' => 2.5, 'a' => 2, 'c' => 1, 'f' => -2, 'e' => -3],
            ],
        ];
    }

    /**
     * @param \Iterator $input
     * @param callable $streamFactoryFunc
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForIterator
     */
    public function testIterator(\Iterator $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIterator(): array
    {
        $iter = fn (array $data) => new \ArrayIterator($data);

        return [
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [-3, -2, 1, 2, 2, 3, 3, 5, 7],
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toArray(),
                [-3, -2, 1, 2, 2, 3, 3, 5, 7],
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toArray(),
                [7, 5, 3, 3, 2, 2, 1, -2, -3],
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toArray(),
                [-3, -2, 1, 2, 2, 3, 3, 5, 7],
            ],
            [
                $iter([2, 3, 1, 2.5, -3, -2, 5, 7, 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                [4 => -3, 5 => -2, 2 => 1, 0 => 2, 3 =>  2.5, 1 => 3, 8 => 3.5, 6 => 5, 7 => 7],
            ],
            [
                $iter(['a' => 2, 'b' => 3, 'c' => 1, 'd' => 2.5, 'e' => -3, 'f' => -2, 'g' => 5, 'h' => 7, 'i' => 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['e' => -3, 'f' => -2, 'c' => 1, 'a' => 2, 'd' =>  2.5, 'b' => 3, 'i' => 3.5, 'g' => 5, 'h' => 7],
            ],
            [
                $iter([2, 3, 1, 2.5, -3, -2, 5, 7, 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toArray(),
                [-3, -2, 1, 2, 2.5, 3, 3.5, 5, 7],
            ],
            [
                $iter([2, 3, 1, 2.5, -3, -2, 5, 7, 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toAssociativeArray(),
                [4 => -3, 5 => -2, 2 => 1, 0 => 2, 3 => 2.5, 1 => 3, 8 => 3.5, 6 => 5, 7 => 7],
            ],
            [
                $iter(['a' => 2, 'b' => 3, 'c' => 1, 'd' => 2.5, 'e' => -3, 'f' => -2, 'g' => 5, 'h' => 7, 'i' => 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toAssociativeArray(),
                ['e' => -3, 'f' => -2, 'c' => 1, 'a' => 2, 'd' =>  2.5, 'b' => 3, 'i' => 3.5, 'g' => 5, 'h' => 7],
            ],
            [
                $iter([2, 3, 1, 2.5, -3, -2, 5, 7, 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toArray(),
                [7, 5, 3.5, 3, 2.5, 2, 1, -2, -3],
            ],
            [
                $iter([2, 3, 1, 2.5, -3, -2, 5, 7, 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toAssociativeArray(),
                [7 => 7, 6 => 5, 8 => 3.5, 1 => 3, 3 => 2.5, 0 => 2, 2 => 1, 5 => -2, 4 => -3],
            ],
            [
                $iter(['a' => 2, 'b' => 3, 'c' => 1, 'd' => 2.5, 'e' => -3, 'f' => -2, 'g' => 5, 'h' => 7, 'i' => 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toAssociativeArray(),
                ['h' => 7, 'g' => 5, 'i' => 3.5, 'b' => 3, 'd' => 2.5, 'a' => 2, 'c' => 1, 'f' => -2, 'e' => -3],
            ],
        ];
    }

    /**
     * @param \Traversable $input
     * @param callable $streamFactoryFunc
     * @param array $expected
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
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [-3, -2, 1, 2, 2, 3, 3, 5, 7],
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toArray(),
                [-3, -2, 1, 2, 2, 3, 3, 5, 7],
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toArray(),
                [7, 5, 3, 3, 2, 2, 1, -2, -3],
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toArray(),
                [-3, -2, 1, 2, 2, 3, 3, 5, 7],
            ],
            [
                $trav([2, 3, 1, 2.5, -3, -2, 5, 7, 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                [4 => -3, 5 => -2, 2 => 1, 0 => 2, 3 =>  2.5, 1 => 3, 8 => 3.5, 6 => 5, 7 => 7],
            ],
            [
                $trav(['a' => 2, 'b' => 3, 'c' => 1, 'd' => 2.5, 'e' => -3, 'f' => -2, 'g' => 5, 'h' => 7, 'i' => 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['e' => -3, 'f' => -2, 'c' => 1, 'a' => 2, 'd' =>  2.5, 'b' => 3, 'i' => 3.5, 'g' => 5, 'h' => 7],
            ],
            [
                $trav([2, 3, 1, 2.5, -3, -2, 5, 7, 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toArray(),
                [-3, -2, 1, 2, 2.5, 3, 3.5, 5, 7],
            ],
            [
                $trav([2, 3, 1, 2.5, -3, -2, 5, 7, 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toAssociativeArray(),
                [4 => -3, 5 => -2, 2 => 1, 0 => 2, 3 => 2.5, 1 => 3, 8 => 3.5, 6 => 5, 7 => 7],
            ],
            [
                $trav(['a' => 2, 'b' => 3, 'c' => 1, 'd' => 2.5, 'e' => -3, 'f' => -2, 'g' => 5, 'h' => 7, 'i' => 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toAssociativeArray(),
                ['e' => -3, 'f' => -2, 'c' => 1, 'a' => 2, 'd' =>  2.5, 'b' => 3, 'i' => 3.5, 'g' => 5, 'h' => 7],
            ],
            [
                $trav([2, 3, 1, 2.5, -3, -2, 5, 7, 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toArray(),
                [7, 5, 3.5, 3, 2.5, 2, 1, -2, -3],
            ],
            [
                $trav([2, 3, 1, 2.5, -3, -2, 5, 7, 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toAssociativeArray(),
                [7 => 7, 6 => 5, 8 => 3.5, 1 => 3, 3 => 2.5, 0 => 2, 2 => 1, 5 => -2, 4 => -3],
            ],
            [
                $trav(['a' => 2, 'b' => 3, 'c' => 1, 'd' => 2.5, 'e' => -3, 'f' => -2, 'g' => 5, 'h' => 7, 'i' => 3.5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toAssociativeArray(),
                ['h' => 7, 'g' => 5, 'i' => 3.5, 'b' => 3, 'd' => 2.5, 'a' => 2, 'c' => 1, 'f' => -2, 'e' => -3],
            ],
        ];
    }
}

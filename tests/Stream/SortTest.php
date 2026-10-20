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

    public static function dataProviderForArray(): array
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

    public static function dataProviderForGenerator(): array
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

    public static function dataProviderForIterator(): array
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

    public static function dataProviderForTraversable(): array
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

    /**
     * @test sort preserves all elements from generator with duplicate keys
     */
    public function testSortGeneratorWithDuplicateKeys(): void
    {
        // Given: generator yielding keys 0, 1, 0, 1 (duplicates)
        $gen = GeneratorFixture::getCombined([0, 1, 0, 1], ['c', 'a', 'b', 'z']);

        // When
        $result = Stream::of($gen)->sort()->toArray();

        // Then
        $this->assertCount(4, $result);
        $this->assertSame(['a', 'b', 'c', 'z'], $result);
    }

    /**
     * @test sort with custom comparator preserves all elements from generator with duplicate keys
     */
    public function testSortGeneratorWithDuplicateKeysCustomComparator(): void
    {
        // Given: generator yielding keys 0, 1, 0, 1 (duplicates)
        $gen = GeneratorFixture::getCombined([0, 1, 0, 1], ['c', 'a', 'b', 'z']);

        // When
        $result = Stream::of($gen)
            ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
            ->toArray();

        // Then
        $this->assertCount(4, $result);
        $this->assertSame(['z', 'c', 'b', 'a'], $result);
    }

    /**
     * @test         Stream sort array with NAN sorted last by the default comparison
     * @dataProvider dataProviderForNanSortsLastArray
     * @param        array $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testNanSortsLastArray(array $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given

        // When
        $result = $streamFactoryFunc($input);

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     *   sorted(rows, key=lambda r: (isnan(r['v']), 0 if isnan(r['v']) else r['v']))
     *   sorted(enumerate(rows), key=lambda kv: (isnan(kv[1]['v']), 0 if isnan(kv[1]['v']) else kv[1]['v']))
     */
    public static function dataProviderForNanSortsLastArray(): array
    {
        return [
            'sort: NAN in the middle' => [
                [2, 4, \NAN, 5, 6, 1, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, \NAN],
            ],
            'sort: NAN first' => [
                [\NAN, 3, 1, 2],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [1, 2, 3, \NAN],
            ],
            'sort: NAN already last' => [
                [3, 1, 2, \NAN],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [1, 2, 3, \NAN],
            ],
            'asort: NANs keep input order' => [
                ['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN, 'e' => 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['c' => 1, 'a' => 2, 'e' => 3, 'b' => \NAN, 'd' => \NAN],
            ],
            'sortBy: NAN keys last in input order' => [
                [['id' => 'a', 'v' => 2], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 1], ['id' => 'd', 'v' => \NAN], ['id' => 'e', 'v' => 3]],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'c', 'v' => 1], ['id' => 'a', 'v' => 2], ['id' => 'e', 'v' => 3], ['id' => 'b', 'v' => \NAN], ['id' => 'd', 'v' => \NAN]],
            ],
            'asortBy: NAN keys last in input order' => [
                [['id' => 'a', 'v' => 2], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 1], ['id' => 'd', 'v' => \NAN], ['id' => 'e', 'v' => 3]],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [2 => ['id' => 'c', 'v' => 1], 0 => ['id' => 'a', 'v' => 2], 4 => ['id' => 'e', 'v' => 3], 1 => ['id' => 'b', 'v' => \NAN], 3 => ['id' => 'd', 'v' => \NAN]],
            ],
        ];
    }

    /**
     * @test         Stream sort generator with NAN sorted last by the default comparison
     * @dataProvider dataProviderForNanSortsLastGenerator
     * @param        \Generator $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testNanSortsLastGenerator(\Generator $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given

        // When
        $result = $streamFactoryFunc($input);

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     *   sorted(rows, key=lambda r: (isnan(r['v']), 0 if isnan(r['v']) else r['v']))
     *   sorted(enumerate(rows), key=lambda kv: (isnan(kv[1]['v']), 0 if isnan(kv[1]['v']) else kv[1]['v']))
     */
    public static function dataProviderForNanSortsLastGenerator(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            'sort: NAN in the middle' => [
                $gen([2, 4, \NAN, 5, 6, 1, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, \NAN],
            ],
            'sort: NAN first' => [
                $gen([\NAN, 3, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [1, 2, 3, \NAN],
            ],
            'sort: NAN already last' => [
                $gen([3, 1, 2, \NAN]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [1, 2, 3, \NAN],
            ],
            'asort: NANs keep input order' => [
                $gen(['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN, 'e' => 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['c' => 1, 'a' => 2, 'e' => 3, 'b' => \NAN, 'd' => \NAN],
            ],
            'sortBy: NAN keys last in input order' => [
                $gen([['id' => 'a', 'v' => 2], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 1], ['id' => 'd', 'v' => \NAN], ['id' => 'e', 'v' => 3]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'c', 'v' => 1], ['id' => 'a', 'v' => 2], ['id' => 'e', 'v' => 3], ['id' => 'b', 'v' => \NAN], ['id' => 'd', 'v' => \NAN]],
            ],
            'asortBy: NAN keys last in input order' => [
                $gen([['id' => 'a', 'v' => 2], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 1], ['id' => 'd', 'v' => \NAN], ['id' => 'e', 'v' => 3]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [2 => ['id' => 'c', 'v' => 1], 0 => ['id' => 'a', 'v' => 2], 4 => ['id' => 'e', 'v' => 3], 1 => ['id' => 'b', 'v' => \NAN], 3 => ['id' => 'd', 'v' => \NAN]],
            ],
        ];
    }

    /**
     * @test         Stream sort iterator with NAN sorted last by the default comparison
     * @dataProvider dataProviderForNanSortsLastIterator
     * @param        \Iterator $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testNanSortsLastIterator(\Iterator $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given

        // When
        $result = $streamFactoryFunc($input);

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     *   sorted(rows, key=lambda r: (isnan(r['v']), 0 if isnan(r['v']) else r['v']))
     *   sorted(enumerate(rows), key=lambda kv: (isnan(kv[1]['v']), 0 if isnan(kv[1]['v']) else kv[1]['v']))
     */
    public static function dataProviderForNanSortsLastIterator(): array
    {
        $iter = fn (array $data) => new \ArrayIterator($data);

        return [
            'sort: NAN in the middle' => [
                $iter([2, 4, \NAN, 5, 6, 1, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, \NAN],
            ],
            'sort: NAN first' => [
                $iter([\NAN, 3, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [1, 2, 3, \NAN],
            ],
            'sort: NAN already last' => [
                $iter([3, 1, 2, \NAN]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [1, 2, 3, \NAN],
            ],
            'asort: NANs keep input order' => [
                $iter(['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN, 'e' => 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['c' => 1, 'a' => 2, 'e' => 3, 'b' => \NAN, 'd' => \NAN],
            ],
            'sortBy: NAN keys last in input order' => [
                $iter([['id' => 'a', 'v' => 2], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 1], ['id' => 'd', 'v' => \NAN], ['id' => 'e', 'v' => 3]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'c', 'v' => 1], ['id' => 'a', 'v' => 2], ['id' => 'e', 'v' => 3], ['id' => 'b', 'v' => \NAN], ['id' => 'd', 'v' => \NAN]],
            ],
            'asortBy: NAN keys last in input order' => [
                $iter([['id' => 'a', 'v' => 2], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 1], ['id' => 'd', 'v' => \NAN], ['id' => 'e', 'v' => 3]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [2 => ['id' => 'c', 'v' => 1], 0 => ['id' => 'a', 'v' => 2], 4 => ['id' => 'e', 'v' => 3], 1 => ['id' => 'b', 'v' => \NAN], 3 => ['id' => 'd', 'v' => \NAN]],
            ],
        ];
    }

    /**
     * @test         Stream sort traversable with NAN sorted last by the default comparison
     * @dataProvider dataProviderForNanSortsLastTraversable
     * @param        \Traversable $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testNanSortsLastTraversable(\Traversable $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given

        // When
        $result = $streamFactoryFunc($input);

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     *   sorted(rows, key=lambda r: (isnan(r['v']), 0 if isnan(r['v']) else r['v']))
     *   sorted(enumerate(rows), key=lambda kv: (isnan(kv[1]['v']), 0 if isnan(kv[1]['v']) else kv[1]['v']))
     */
    public static function dataProviderForNanSortsLastTraversable(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            'sort: NAN in the middle' => [
                $trav([2, 4, \NAN, 5, 6, 1, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, \NAN],
            ],
            'sort: NAN first' => [
                $trav([\NAN, 3, 1, 2]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [1, 2, 3, \NAN],
            ],
            'sort: NAN already last' => [
                $trav([3, 1, 2, \NAN]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [1, 2, 3, \NAN],
            ],
            'asort: NANs keep input order' => [
                $trav(['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN, 'e' => 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['c' => 1, 'a' => 2, 'e' => 3, 'b' => \NAN, 'd' => \NAN],
            ],
            'sortBy: NAN keys last in input order' => [
                $trav([['id' => 'a', 'v' => 2], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 1], ['id' => 'd', 'v' => \NAN], ['id' => 'e', 'v' => 3]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'c', 'v' => 1], ['id' => 'a', 'v' => 2], ['id' => 'e', 'v' => 3], ['id' => 'b', 'v' => \NAN], ['id' => 'd', 'v' => \NAN]],
            ],
            'asortBy: NAN keys last in input order' => [
                $trav([['id' => 'a', 'v' => 2], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 1], ['id' => 'd', 'v' => \NAN], ['id' => 'e', 'v' => 3]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [2 => ['id' => 'c', 'v' => 1], 0 => ['id' => 'a', 'v' => 2], 4 => ['id' => 'e', 'v' => 3], 1 => ['id' => 'b', 'v' => \NAN], 3 => ['id' => 'd', 'v' => \NAN]],
            ],
        ];
    }

    /**
     * @test         Stream sort array of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionArray
     * @param        array $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionArray(array $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = $streamFactoryFunc($input);

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     *   sorted(rows, key=lambda r: (isnan(r['v']), 0 if isnan(r['v']) else r['v']))
     *   sorted(enumerate(rows), key=lambda kv: (isnan(kv[1]['v']), 0 if isnan(kv[1]['v']) else kv[1]['v']))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionArray(): array
    {
        return [
            'sort: int 2^53 + 1 before float 2.0^53, equal as floats' => [
                [9007199254740993, 9007199254740992.0],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9007199254740992.0, 9007199254740993],
            ],
            'sort: float 2.0^53 after ints that equal it as floats' => [
                [9007199254740994, 9007199254740993, 9007199254740992.0],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9007199254740992.0, 9007199254740993, 9007199254740994],
            ],
            'sort: float 2.0^63 before int PHP_INT_MAX' => [
                [9.223372036854776e+18, 9223372036854775807],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9223372036854775807, 9.223372036854776e+18],
            ],
            'sort: NAN after an int beyond 2^53 and a float' => [
                [9007199254740993, \NAN, 9007199254740992.0],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9007199254740992.0, 9007199254740993, \NAN],
            ],
            'asort: int 2^53 + 1 before float 2.0^53, equal as floats' => [
                ['a' => 9007199254740993, 'b' => 9007199254740992.0],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['b' => 9007199254740992.0, 'a' => 9007199254740993],
            ],
            'asort: float 2.0^63 before int PHP_INT_MAX' => [
                ['a' => 9.223372036854776e+18, 'b' => 9223372036854775807],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['b' => 9223372036854775807, 'a' => 9.223372036854776e+18],
            ],
            'asort: NAN after an int beyond 2^53 and a float' => [
                ['a' => 9007199254740993, 'b' => \NAN, 'c' => 9007199254740992.0],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['c' => 9007199254740992.0, 'a' => 9007199254740993, 'b' => \NAN],
            ],
            'sortBy: int key 2^53 + 1 before float key 2.0^53, equal as floats' => [
                [['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => 9007199254740992.0]],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'b', 'v' => 9007199254740992.0], ['id' => 'a', 'v' => 9007199254740993]],
            ],
            'sortBy: float key 2.0^63 before int key PHP_INT_MAX' => [
                [['id' => 'a', 'v' => 9.223372036854776e+18], ['id' => 'b', 'v' => 9223372036854775807]],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'b', 'v' => 9223372036854775807], ['id' => 'a', 'v' => 9.223372036854776e+18]],
            ],
            'sortBy: NAN key after an int key beyond 2^53 and a float key' => [
                [['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 9007199254740992.0]],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'c', 'v' => 9007199254740992.0], ['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => \NAN]],
            ],
            'asortBy: int key 2^53 + 1 before float key 2.0^53, equal as floats' => [
                [['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => 9007199254740992.0]],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [1 => ['id' => 'b', 'v' => 9007199254740992.0], 0 => ['id' => 'a', 'v' => 9007199254740993]],
            ],
            'asortBy: float key 2.0^63 before int key PHP_INT_MAX' => [
                [['id' => 'a', 'v' => 9.223372036854776e+18], ['id' => 'b', 'v' => 9223372036854775807]],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [1 => ['id' => 'b', 'v' => 9223372036854775807], 0 => ['id' => 'a', 'v' => 9.223372036854776e+18]],
            ],
            'asortBy: NAN key after an int key beyond 2^53 and a float key' => [
                [['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 9007199254740992.0]],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [2 => ['id' => 'c', 'v' => 9007199254740992.0], 0 => ['id' => 'a', 'v' => 9007199254740993], 1 => ['id' => 'b', 'v' => \NAN]],
            ],
        ];
    }

    /**
     * @test         Stream sort generator of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionGenerator
     * @param        \Generator $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionGenerator(\Generator $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = $streamFactoryFunc($input);

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     *   sorted(rows, key=lambda r: (isnan(r['v']), 0 if isnan(r['v']) else r['v']))
     *   sorted(enumerate(rows), key=lambda kv: (isnan(kv[1]['v']), 0 if isnan(kv[1]['v']) else kv[1]['v']))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionGenerator(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            'sort: int 2^53 + 1 before float 2.0^53, equal as floats' => [
                $gen([9007199254740993, 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9007199254740992.0, 9007199254740993],
            ],
            'sort: float 2.0^53 after ints that equal it as floats' => [
                $gen([9007199254740994, 9007199254740993, 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9007199254740992.0, 9007199254740993, 9007199254740994],
            ],
            'sort: float 2.0^63 before int PHP_INT_MAX' => [
                $gen([9.223372036854776e+18, 9223372036854775807]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9223372036854775807, 9.223372036854776e+18],
            ],
            'sort: NAN after an int beyond 2^53 and a float' => [
                $gen([9007199254740993, \NAN, 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9007199254740992.0, 9007199254740993, \NAN],
            ],
            'asort: int 2^53 + 1 before float 2.0^53, equal as floats' => [
                $gen(['a' => 9007199254740993, 'b' => 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['b' => 9007199254740992.0, 'a' => 9007199254740993],
            ],
            'asort: float 2.0^63 before int PHP_INT_MAX' => [
                $gen(['a' => 9.223372036854776e+18, 'b' => 9223372036854775807]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['b' => 9223372036854775807, 'a' => 9.223372036854776e+18],
            ],
            'asort: NAN after an int beyond 2^53 and a float' => [
                $gen(['a' => 9007199254740993, 'b' => \NAN, 'c' => 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['c' => 9007199254740992.0, 'a' => 9007199254740993, 'b' => \NAN],
            ],
            'sortBy: int key 2^53 + 1 before float key 2.0^53, equal as floats' => [
                $gen([['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => 9007199254740992.0]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'b', 'v' => 9007199254740992.0], ['id' => 'a', 'v' => 9007199254740993]],
            ],
            'sortBy: float key 2.0^63 before int key PHP_INT_MAX' => [
                $gen([['id' => 'a', 'v' => 9.223372036854776e+18], ['id' => 'b', 'v' => 9223372036854775807]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'b', 'v' => 9223372036854775807], ['id' => 'a', 'v' => 9.223372036854776e+18]],
            ],
            'sortBy: NAN key after an int key beyond 2^53 and a float key' => [
                $gen([['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 9007199254740992.0]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'c', 'v' => 9007199254740992.0], ['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => \NAN]],
            ],
            'asortBy: int key 2^53 + 1 before float key 2.0^53, equal as floats' => [
                $gen([['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => 9007199254740992.0]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [1 => ['id' => 'b', 'v' => 9007199254740992.0], 0 => ['id' => 'a', 'v' => 9007199254740993]],
            ],
            'asortBy: float key 2.0^63 before int key PHP_INT_MAX' => [
                $gen([['id' => 'a', 'v' => 9.223372036854776e+18], ['id' => 'b', 'v' => 9223372036854775807]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [1 => ['id' => 'b', 'v' => 9223372036854775807], 0 => ['id' => 'a', 'v' => 9.223372036854776e+18]],
            ],
            'asortBy: NAN key after an int key beyond 2^53 and a float key' => [
                $gen([['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 9007199254740992.0]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [2 => ['id' => 'c', 'v' => 9007199254740992.0], 0 => ['id' => 'a', 'v' => 9007199254740993], 1 => ['id' => 'b', 'v' => \NAN]],
            ],
        ];
    }

    /**
     * @test         Stream sort iterator of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionIterator
     * @param        \Iterator $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionIterator(\Iterator $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = $streamFactoryFunc($input);

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     *   sorted(rows, key=lambda r: (isnan(r['v']), 0 if isnan(r['v']) else r['v']))
     *   sorted(enumerate(rows), key=lambda kv: (isnan(kv[1]['v']), 0 if isnan(kv[1]['v']) else kv[1]['v']))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionIterator(): array
    {
        $iter = fn (array $data) => new \ArrayIterator($data);

        return [
            'sort: int 2^53 + 1 before float 2.0^53, equal as floats' => [
                $iter([9007199254740993, 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9007199254740992.0, 9007199254740993],
            ],
            'sort: float 2.0^53 after ints that equal it as floats' => [
                $iter([9007199254740994, 9007199254740993, 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9007199254740992.0, 9007199254740993, 9007199254740994],
            ],
            'sort: float 2.0^63 before int PHP_INT_MAX' => [
                $iter([9.223372036854776e+18, 9223372036854775807]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9223372036854775807, 9.223372036854776e+18],
            ],
            'sort: NAN after an int beyond 2^53 and a float' => [
                $iter([9007199254740993, \NAN, 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9007199254740992.0, 9007199254740993, \NAN],
            ],
            'asort: int 2^53 + 1 before float 2.0^53, equal as floats' => [
                $iter(['a' => 9007199254740993, 'b' => 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['b' => 9007199254740992.0, 'a' => 9007199254740993],
            ],
            'asort: float 2.0^63 before int PHP_INT_MAX' => [
                $iter(['a' => 9.223372036854776e+18, 'b' => 9223372036854775807]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['b' => 9223372036854775807, 'a' => 9.223372036854776e+18],
            ],
            'asort: NAN after an int beyond 2^53 and a float' => [
                $iter(['a' => 9007199254740993, 'b' => \NAN, 'c' => 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['c' => 9007199254740992.0, 'a' => 9007199254740993, 'b' => \NAN],
            ],
            'sortBy: int key 2^53 + 1 before float key 2.0^53, equal as floats' => [
                $iter([['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => 9007199254740992.0]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'b', 'v' => 9007199254740992.0], ['id' => 'a', 'v' => 9007199254740993]],
            ],
            'sortBy: float key 2.0^63 before int key PHP_INT_MAX' => [
                $iter([['id' => 'a', 'v' => 9.223372036854776e+18], ['id' => 'b', 'v' => 9223372036854775807]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'b', 'v' => 9223372036854775807], ['id' => 'a', 'v' => 9.223372036854776e+18]],
            ],
            'sortBy: NAN key after an int key beyond 2^53 and a float key' => [
                $iter([['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 9007199254740992.0]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'c', 'v' => 9007199254740992.0], ['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => \NAN]],
            ],
            'asortBy: int key 2^53 + 1 before float key 2.0^53, equal as floats' => [
                $iter([['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => 9007199254740992.0]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [1 => ['id' => 'b', 'v' => 9007199254740992.0], 0 => ['id' => 'a', 'v' => 9007199254740993]],
            ],
            'asortBy: float key 2.0^63 before int key PHP_INT_MAX' => [
                $iter([['id' => 'a', 'v' => 9.223372036854776e+18], ['id' => 'b', 'v' => 9223372036854775807]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [1 => ['id' => 'b', 'v' => 9223372036854775807], 0 => ['id' => 'a', 'v' => 9.223372036854776e+18]],
            ],
            'asortBy: NAN key after an int key beyond 2^53 and a float key' => [
                $iter([['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 9007199254740992.0]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [2 => ['id' => 'c', 'v' => 9007199254740992.0], 0 => ['id' => 'a', 'v' => 9007199254740993], 1 => ['id' => 'b', 'v' => \NAN]],
            ],
        ];
    }

    /**
     * @test         Stream sort traversable of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionTraversable
     * @param        \Traversable $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionTraversable(\Traversable $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = $streamFactoryFunc($input);

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data, key=lambda x: (isnan(x), 0 if isnan(x) else x))
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     *   sorted(rows, key=lambda r: (isnan(r['v']), 0 if isnan(r['v']) else r['v']))
     *   sorted(enumerate(rows), key=lambda kv: (isnan(kv[1]['v']), 0 if isnan(kv[1]['v']) else kv[1]['v']))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionTraversable(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            'sort: int 2^53 + 1 before float 2.0^53, equal as floats' => [
                $trav([9007199254740993, 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9007199254740992.0, 9007199254740993],
            ],
            'sort: float 2.0^53 after ints that equal it as floats' => [
                $trav([9007199254740994, 9007199254740993, 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9007199254740992.0, 9007199254740993, 9007199254740994],
            ],
            'sort: float 2.0^63 before int PHP_INT_MAX' => [
                $trav([9.223372036854776e+18, 9223372036854775807]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9223372036854775807, 9.223372036854776e+18],
            ],
            'sort: NAN after an int beyond 2^53 and a float' => [
                $trav([9007199254740993, \NAN, 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toArray(),
                [9007199254740992.0, 9007199254740993, \NAN],
            ],
            'asort: int 2^53 + 1 before float 2.0^53, equal as floats' => [
                $trav(['a' => 9007199254740993, 'b' => 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['b' => 9007199254740992.0, 'a' => 9007199254740993],
            ],
            'asort: float 2.0^63 before int PHP_INT_MAX' => [
                $trav(['a' => 9.223372036854776e+18, 'b' => 9223372036854775807]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['b' => 9223372036854775807, 'a' => 9.223372036854776e+18],
            ],
            'asort: NAN after an int beyond 2^53 and a float' => [
                $trav(['a' => 9007199254740993, 'b' => \NAN, 'c' => 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort()
                    ->toAssociativeArray(),
                ['c' => 9007199254740992.0, 'a' => 9007199254740993, 'b' => \NAN],
            ],
            'sortBy: int key 2^53 + 1 before float key 2.0^53, equal as floats' => [
                $trav([['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => 9007199254740992.0]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'b', 'v' => 9007199254740992.0], ['id' => 'a', 'v' => 9007199254740993]],
            ],
            'sortBy: float key 2.0^63 before int key PHP_INT_MAX' => [
                $trav([['id' => 'a', 'v' => 9.223372036854776e+18], ['id' => 'b', 'v' => 9223372036854775807]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'b', 'v' => 9223372036854775807], ['id' => 'a', 'v' => 9.223372036854776e+18]],
            ],
            'sortBy: NAN key after an int key beyond 2^53 and a float key' => [
                $trav([['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 9007199254740992.0]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sortBy(fn (array $row) => $row['v'])
                    ->toArray(),
                [['id' => 'c', 'v' => 9007199254740992.0], ['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => \NAN]],
            ],
            'asortBy: int key 2^53 + 1 before float key 2.0^53, equal as floats' => [
                $trav([['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => 9007199254740992.0]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [1 => ['id' => 'b', 'v' => 9007199254740992.0], 0 => ['id' => 'a', 'v' => 9007199254740993]],
            ],
            'asortBy: float key 2.0^63 before int key PHP_INT_MAX' => [
                $trav([['id' => 'a', 'v' => 9.223372036854776e+18], ['id' => 'b', 'v' => 9223372036854775807]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [1 => ['id' => 'b', 'v' => 9223372036854775807], 0 => ['id' => 'a', 'v' => 9.223372036854776e+18]],
            ],
            'asortBy: NAN key after an int key beyond 2^53 and a float key' => [
                $trav([['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 9007199254740992.0]]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asortBy(fn (array $row) => $row['v'])
                    ->toAssociativeArray(),
                [2 => ['id' => 'c', 'v' => 9007199254740992.0], 0 => ['id' => 'a', 'v' => 9007199254740993], 1 => ['id' => 'b', 'v' => \NAN]],
            ],
        ];
    }

    /**
     * @test         Stream sort array with a custom comparator, which is used as given when a value is NAN
     * @dataProvider dataProviderForCustomComparatorWithNanArray
     * @param        array $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testCustomComparatorWithNanArray(array $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given

        // When
        $result = $streamFactoryFunc($input);

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort and the same NAN-first comparator:
     *   sorted(data, key=cmp_to_key(nan_first))
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: nan_first(x[1], y[1])))
     * where nan_first(a, b) puts a NAN before every other value and compares other values with a <=> b.
     */
    public static function dataProviderForCustomComparatorWithNanArray(): array
    {
        return [
            'sort: NAN-first comparator' => [
                [2, \NAN, 1],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs)
                    ->toArray(),
                [\NAN, 1, 2],
            ],
            'asort: NAN-first comparator, NANs keep input order' => [
                ['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs)
                    ->toAssociativeArray(),
                ['b' => \NAN, 'd' => \NAN, 'c' => 1, 'a' => 2],
            ],
        ];
    }

    /**
     * @test         Stream sort array with a custom comparator, which is used as given for an int beyond 2^53 and a float
     * @dataProvider dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionArray
     * @param        array $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testCustomComparatorWithIntAndFloatBeyondFloatPrecisionArray(array $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's stable sort and the comparison PHP's <=> makes, which compares two
     * ints exactly and an int and a float as floats, so it ties 2^53 + 1 with 2.0^53:
     *   sorted(data, key=cmp_to_key(php_spaceship))
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: php_spaceship(x[1], y[1])))
     */
    public static function dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionArray(): array
    {
        return [
            'sort: <=> ties int 2^53 + 1 with float 2.0^53, kept in input order' => [
                [9007199254740993, 9007199254740992.0],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs): int => $lhs <=> $rhs)
                    ->toArray(),
                [9007199254740993, 9007199254740992.0],
            ],
            'sort: <=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                [9007199254740994, 9007199254740993, 9007199254740992.0],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs): int => $lhs <=> $rhs)
                    ->toArray(),
                [9007199254740993, 9007199254740992.0, 9007199254740994],
            ],
            'asort: <=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                ['a' => 9007199254740994, 'b' => 9007199254740993, 'c' => 9007199254740992.0],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs): int => $lhs <=> $rhs)
                    ->toAssociativeArray(),
                ['b' => 9007199254740993, 'c' => 9007199254740992.0, 'a' => 9007199254740994],
            ],
        ];
    }

    /**
     * @test         Stream sort generator with a custom comparator, which is used as given when a value is NAN
     * @dataProvider dataProviderForCustomComparatorWithNanGenerator
     * @param        \Generator $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testCustomComparatorWithNanGenerator(\Generator $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given

        // When
        $result = $streamFactoryFunc($input);

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort and the same NAN-first comparator:
     *   sorted(data, key=cmp_to_key(nan_first))
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: nan_first(x[1], y[1])))
     * where nan_first(a, b) puts a NAN before every other value and compares other values with a <=> b.
     */
    public static function dataProviderForCustomComparatorWithNanGenerator(): array
    {
        return [
            'sort: NAN-first comparator' => [
                GeneratorFixture::getKeyValueGenerator([2, \NAN, 1]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs)
                    ->toArray(),
                [\NAN, 1, 2],
            ],
            'asort: NAN-first comparator, NANs keep input order' => [
                GeneratorFixture::getKeyValueGenerator(['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs)
                    ->toAssociativeArray(),
                ['b' => \NAN, 'd' => \NAN, 'c' => 1, 'a' => 2],
            ],
        ];
    }

    /**
     * @test         Stream sort generator with a custom comparator, which is used as given for an int beyond 2^53 and a float
     * @dataProvider dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionGenerator
     * @param        \Generator $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testCustomComparatorWithIntAndFloatBeyondFloatPrecisionGenerator(\Generator $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's stable sort and the comparison PHP's <=> makes, which compares two
     * ints exactly and an int and a float as floats, so it ties 2^53 + 1 with 2.0^53:
     *   sorted(data, key=cmp_to_key(php_spaceship))
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: php_spaceship(x[1], y[1])))
     */
    public static function dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionGenerator(): array
    {
        return [
            'sort: <=> ties int 2^53 + 1 with float 2.0^53, kept in input order' => [
                GeneratorFixture::getKeyValueGenerator([9007199254740993, 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs): int => $lhs <=> $rhs)
                    ->toArray(),
                [9007199254740993, 9007199254740992.0],
            ],
            'sort: <=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                GeneratorFixture::getKeyValueGenerator([9007199254740994, 9007199254740993, 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs): int => $lhs <=> $rhs)
                    ->toArray(),
                [9007199254740993, 9007199254740992.0, 9007199254740994],
            ],
            'asort: <=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                GeneratorFixture::getKeyValueGenerator(['a' => 9007199254740994, 'b' => 9007199254740993, 'c' => 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs): int => $lhs <=> $rhs)
                    ->toAssociativeArray(),
                ['b' => 9007199254740993, 'c' => 9007199254740992.0, 'a' => 9007199254740994],
            ],
        ];
    }

    /**
     * @test         Stream sort iterator with a custom comparator, which is used as given when a value is NAN
     * @dataProvider dataProviderForCustomComparatorWithNanIterator
     * @param        \Iterator $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testCustomComparatorWithNanIterator(\Iterator $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given

        // When
        $result = $streamFactoryFunc($input);

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort and the same NAN-first comparator:
     *   sorted(data, key=cmp_to_key(nan_first))
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: nan_first(x[1], y[1])))
     * where nan_first(a, b) puts a NAN before every other value and compares other values with a <=> b.
     */
    public static function dataProviderForCustomComparatorWithNanIterator(): array
    {
        return [
            'sort: NAN-first comparator' => [
                new ArrayIteratorFixture([2, \NAN, 1]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs)
                    ->toArray(),
                [\NAN, 1, 2],
            ],
            'asort: NAN-first comparator, NANs keep input order' => [
                new \ArrayIterator(['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs)
                    ->toAssociativeArray(),
                ['b' => \NAN, 'd' => \NAN, 'c' => 1, 'a' => 2],
            ],
        ];
    }

    /**
     * @test         Stream sort iterator with a custom comparator, which is used as given for an int beyond 2^53 and a float
     * @dataProvider dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionIterator
     * @param        \Iterator $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testCustomComparatorWithIntAndFloatBeyondFloatPrecisionIterator(\Iterator $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's stable sort and the comparison PHP's <=> makes, which compares two
     * ints exactly and an int and a float as floats, so it ties 2^53 + 1 with 2.0^53:
     *   sorted(data, key=cmp_to_key(php_spaceship))
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: php_spaceship(x[1], y[1])))
     */
    public static function dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionIterator(): array
    {
        return [
            'sort: <=> ties int 2^53 + 1 with float 2.0^53, kept in input order' => [
                new ArrayIteratorFixture([9007199254740993, 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs): int => $lhs <=> $rhs)
                    ->toArray(),
                [9007199254740993, 9007199254740992.0],
            ],
            'sort: <=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                new ArrayIteratorFixture([9007199254740994, 9007199254740993, 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs): int => $lhs <=> $rhs)
                    ->toArray(),
                [9007199254740993, 9007199254740992.0, 9007199254740994],
            ],
            'asort: <=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                new \ArrayIterator(['a' => 9007199254740994, 'b' => 9007199254740993, 'c' => 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs): int => $lhs <=> $rhs)
                    ->toAssociativeArray(),
                ['b' => 9007199254740993, 'c' => 9007199254740992.0, 'a' => 9007199254740994],
            ],
        ];
    }

    /**
     * @test         Stream sort traversable with a custom comparator, which is used as given when a value is NAN
     * @dataProvider dataProviderForCustomComparatorWithNanTraversable
     * @param        \Traversable $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testCustomComparatorWithNanTraversable(\Traversable $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given

        // When
        $result = $streamFactoryFunc($input);

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort and the same NAN-first comparator:
     *   sorted(data, key=cmp_to_key(nan_first))
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: nan_first(x[1], y[1])))
     * where nan_first(a, b) puts a NAN before every other value and compares other values with a <=> b.
     */
    public static function dataProviderForCustomComparatorWithNanTraversable(): array
    {
        return [
            'sort: NAN-first comparator' => [
                new IteratorAggregateFixture([2, \NAN, 1]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs)
                    ->toArray(),
                [\NAN, 1, 2],
            ],
            'asort: NAN-first comparator, NANs keep input order' => [
                new IteratorAggregateFixture(['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs)
                    ->toAssociativeArray(),
                ['b' => \NAN, 'd' => \NAN, 'c' => 1, 'a' => 2],
            ],
        ];
    }

    /**
     * @test         Stream sort traversable with a custom comparator, which is used as given for an int beyond 2^53 and a float
     * @dataProvider dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionTraversable
     * @param        \Traversable $input
     * @param        callable $streamFactoryFunc
     * @param        array $expected
     */
    public function testCustomComparatorWithIntAndFloatBeyondFloatPrecisionTraversable(\Traversable $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's stable sort and the comparison PHP's <=> makes, which compares two
     * ints exactly and an int and a float as floats, so it ties 2^53 + 1 with 2.0^53:
     *   sorted(data, key=cmp_to_key(php_spaceship))
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: php_spaceship(x[1], y[1])))
     */
    public static function dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionTraversable(): array
    {
        return [
            'sort: <=> ties int 2^53 + 1 with float 2.0^53, kept in input order' => [
                new IteratorAggregateFixture([9007199254740993, 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs): int => $lhs <=> $rhs)
                    ->toArray(),
                [9007199254740993, 9007199254740992.0],
            ],
            'sort: <=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                new IteratorAggregateFixture([9007199254740994, 9007199254740993, 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs): int => $lhs <=> $rhs)
                    ->toArray(),
                [9007199254740993, 9007199254740992.0, 9007199254740994],
            ],
            'asort: <=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                new IteratorAggregateFixture(['a' => 9007199254740994, 'b' => 9007199254740993, 'c' => 9007199254740992.0]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->asort(fn ($lhs, $rhs): int => $lhs <=> $rhs)
                    ->toAssociativeArray(),
                ['b' => 9007199254740993, 'c' => 9007199254740992.0, 'a' => 9007199254740994],
            ],
        ];
    }
}

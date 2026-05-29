<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class MapWithKeysTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         mapWithKeys array
     * @dataProvider dataProviderForArray
     * @param        array    $iterable
     * @param        callable $func
     * @param        array    $expected
     */
    public function testArray(array $iterable, callable $func, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::mapWithKeys($iterable, $func) as $key => $item) {
            $result[$key] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [
                [],
                fn ($value, $key) => $value,
                [],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                fn ($value, $key) => "$key=$value",
                ['a' => 'a=1', 'b' => 'b=2', 'c' => 'c=3'],
            ],
            [
                [10, 20, 30],
                fn ($value, $key) => $value + $key,
                [0 => 10, 1 => 21, 2 => 32],
            ],
            [
                ['x' => 5, 'y' => 6],
                fn ($value, $key) => $value * 2,
                ['x' => 10, 'y' => 12],
            ],
            [
                [1, 2, 3, 4, 5],
                fn ($value, $key) => $key,
                [0, 1, 2, 3, 4],
            ],
        ];
    }

    /**
     * @test         mapWithKeys generator
     * @dataProvider dataProviderForGenerator
     * @param        \Generator $iterable
     * @param        callable   $func
     * @param        array      $expected
     */
    public function testGenerator(\Generator $iterable, callable $func, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::mapWithKeys($iterable, $func) as $key => $item) {
            $result[$key] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForGenerator(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [
                $gen([]),
                fn ($value, $key) => $value,
                [],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                fn ($value, $key) => "$key=$value",
                ['a' => 'a=1', 'b' => 'b=2', 'c' => 'c=3'],
            ],
            [
                $gen([10, 20, 30]),
                fn ($value, $key) => $value + $key,
                [0 => 10, 1 => 21, 2 => 32],
            ],
            [
                $gen(['x' => 5, 'y' => 6]),
                fn ($value, $key) => $value * 2,
                ['x' => 10, 'y' => 12],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn ($value, $key) => $key,
                [0, 1, 2, 3, 4],
            ],
        ];
    }

    /**
     * @test         mapWithKeys iterator
     * @dataProvider dataProviderForIterator
     * @param        \Iterator $iterable
     * @param        callable  $func
     * @param        array     $expected
     */
    public function testIterator(\Iterator $iterable, callable $func, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::mapWithKeys($iterable, $func) as $key => $item) {
            $result[$key] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForIterator(): array
    {
        $iter = fn (array $data) => new \ArrayIterator($data);

        return [
            [
                $iter([]),
                fn ($value, $key) => $value,
                [],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                fn ($value, $key) => "$key=$value",
                ['a' => 'a=1', 'b' => 'b=2', 'c' => 'c=3'],
            ],
            [
                $iter([10, 20, 30]),
                fn ($value, $key) => $value + $key,
                [0 => 10, 1 => 21, 2 => 32],
            ],
            [
                $iter(['x' => 5, 'y' => 6]),
                fn ($value, $key) => $value * 2,
                ['x' => 10, 'y' => 12],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn ($value, $key) => $key,
                [0, 1, 2, 3, 4],
            ],
        ];
    }

    /**
     * @test         mapWithKeys traversable
     * @dataProvider dataProviderForTraversable
     * @param        \Traversable $iterable
     * @param        callable     $func
     * @param        array        $expected
     */
    public function testTraversable(\Traversable $iterable, callable $func, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::mapWithKeys($iterable, $func) as $key => $item) {
            $result[$key] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForTraversable(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                fn ($value, $key) => $value,
                [],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                fn ($value, $key) => "$key=$value",
                ['a' => 'a=1', 'b' => 'b=2', 'c' => 'c=3'],
            ],
            [
                $trav([10, 20, 30]),
                fn ($value, $key) => $value + $key,
                [0 => 10, 1 => 21, 2 => 32],
            ],
            [
                $trav(['x' => 5, 'y' => 6]),
                fn ($value, $key) => $value * 2,
                ['x' => 10, 'y' => 12],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn ($value, $key) => $key,
                [0, 1, 2, 3, 4],
            ],
        ];
    }

    /**
     * @test mapWithKeys callback receives value then key
     */
    public function testCallbackArgumentOrder(): void
    {
        // Given
        $iterable = ['first' => 'a', 'second' => 'b'];
        $received = [];

        // When
        foreach (
            Single::mapWithKeys($iterable, function ($value, $key) use (&$received) {
                $received[] = [$value, $key];
                return $value;
            }) as $_
        ) {
            // consume
        }

        // Then
        $this->assertEquals([['a', 'first'], ['b', 'second']], $received);
    }

    /**
     * @test mapWithKeys is lazy
     */
    public function testLaziness(): void
    {
        // Given
        $count = 0;
        $iterable = GeneratorFixture::getGenerator([1, 2, 3, 4, 5]);

        // When
        $result = [];
        foreach (
            Single::mapWithKeys($iterable, function ($value, $key) use (&$count) {
                $count++;
                return $value;
            }) as $item
        ) {
            $result[] = $item;
            if (\count($result) === 2) {
                break;
            }
        }

        // Then
        $this->assertSame(2, $count);
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class FilterWithKeysTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         filterWithKeys array
     * @dataProvider dataProviderForArray
     * @param        array    $iterable
     * @param        callable $predicate
     * @param        array    $expected
     */
    public function testArray(array $iterable, callable $predicate, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::filterWithKeys($iterable, $predicate) as $key => $item) {
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
                fn ($value, $key) => true,
                [],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
                fn ($value, $key) => $value % 2 === 0,
                ['b' => 2, 'd' => 4],
            ],
            [
                ['apple' => 1, 'banana' => 2, 'avocado' => 3],
                fn ($value, $key) => \str_starts_with($key, 'a'),
                ['apple' => 1, 'avocado' => 3],
            ],
            [
                [10, 20, 30, 40],
                fn ($value, $key) => $key >= 2,
                [2 => 30, 3 => 40],
            ],
            [
                ['x' => 'truthy', 'y' => '', 'z' => '0', 'w' => 'a'],
                fn ($value, $key) => $value,
                ['x' => 'truthy', 'w' => 'a'],
            ],
            [
                ['a' => 1, 'b' => 0, 'c' => null, 'd' => [], 'e' => 5],
                fn ($value, $key) => $value,
                ['a' => 1, 'e' => 5],
            ],
        ];
    }

    /**
     * @test         filterWithKeys generator
     * @dataProvider dataProviderForGenerator
     * @param        \Generator $iterable
     * @param        callable   $predicate
     * @param        array      $expected
     */
    public function testGenerator(\Generator $iterable, callable $predicate, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::filterWithKeys($iterable, $predicate) as $key => $item) {
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
                fn ($value, $key) => true,
                [],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]),
                fn ($value, $key) => $value % 2 === 0,
                ['b' => 2, 'd' => 4],
            ],
            [
                $gen(['apple' => 1, 'banana' => 2, 'avocado' => 3]),
                fn ($value, $key) => \str_starts_with($key, 'a'),
                ['apple' => 1, 'avocado' => 3],
            ],
            [
                $gen([10, 20, 30, 40]),
                fn ($value, $key) => $key >= 2,
                [2 => 30, 3 => 40],
            ],
            [
                $gen(['x' => 'truthy', 'y' => '', 'z' => '0', 'w' => 'a']),
                fn ($value, $key) => $value,
                ['x' => 'truthy', 'w' => 'a'],
            ],
            [
                $gen(['a' => 1, 'b' => 0, 'c' => null, 'd' => [], 'e' => 5]),
                fn ($value, $key) => $value,
                ['a' => 1, 'e' => 5],
            ],
        ];
    }

    /**
     * @test         filterWithKeys iterator
     * @dataProvider dataProviderForIterator
     * @param        \Iterator $iterable
     * @param        callable  $predicate
     * @param        array     $expected
     */
    public function testIterator(\Iterator $iterable, callable $predicate, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::filterWithKeys($iterable, $predicate) as $key => $item) {
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
                fn ($value, $key) => true,
                [],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]),
                fn ($value, $key) => $value % 2 === 0,
                ['b' => 2, 'd' => 4],
            ],
            [
                $iter(['apple' => 1, 'banana' => 2, 'avocado' => 3]),
                fn ($value, $key) => \str_starts_with($key, 'a'),
                ['apple' => 1, 'avocado' => 3],
            ],
            [
                $iter([10, 20, 30, 40]),
                fn ($value, $key) => $key >= 2,
                [2 => 30, 3 => 40],
            ],
            [
                $iter(['x' => 'truthy', 'y' => '', 'z' => '0', 'w' => 'a']),
                fn ($value, $key) => $value,
                ['x' => 'truthy', 'w' => 'a'],
            ],
            [
                $iter(['a' => 1, 'b' => 0, 'c' => null, 'd' => [], 'e' => 5]),
                fn ($value, $key) => $value,
                ['a' => 1, 'e' => 5],
            ],
        ];
    }

    /**
     * @test         filterWithKeys traversable
     * @dataProvider dataProviderForTraversable
     * @param        \Traversable $iterable
     * @param        callable     $predicate
     * @param        array        $expected
     */
    public function testTraversable(\Traversable $iterable, callable $predicate, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::filterWithKeys($iterable, $predicate) as $key => $item) {
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
                fn ($value, $key) => true,
                [],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]),
                fn ($value, $key) => $value % 2 === 0,
                ['b' => 2, 'd' => 4],
            ],
            [
                $trav(['apple' => 1, 'banana' => 2, 'avocado' => 3]),
                fn ($value, $key) => \str_starts_with($key, 'a'),
                ['apple' => 1, 'avocado' => 3],
            ],
            [
                $trav([10, 20, 30, 40]),
                fn ($value, $key) => $key >= 2,
                [2 => 30, 3 => 40],
            ],
            [
                $trav(['x' => 'truthy', 'y' => '', 'z' => '0', 'w' => 'a']),
                fn ($value, $key) => $value,
                ['x' => 'truthy', 'w' => 'a'],
            ],
            [
                $trav(['a' => 1, 'b' => 0, 'c' => null, 'd' => [], 'e' => 5]),
                fn ($value, $key) => $value,
                ['a' => 1, 'e' => 5],
            ],
        ];
    }

    /**
     * @test filterWithKeys callback receives value then key
     */
    public function testCallbackArgumentOrder(): void
    {
        // Given
        $iterable = ['first' => 'a', 'second' => 'b'];
        $received = [];

        // When
        foreach (
            Single::filterWithKeys($iterable, function ($value, $key) use (&$received) {
                $received[] = [$value, $key];
                return true;
            }) as $_
        ) {
            // consume
        }

        // Then
        $this->assertEquals([['a', 'first'], ['b', 'second']], $received);
    }

    /**
     * @test filterWithKeys coerces predicate via (bool) cast
     */
    public function testPredicateCoercion(): void
    {
        // Given
        $iterable = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        // When  - non-boolean truthy/falsy return values
        $result = [];
        foreach (Single::filterWithKeys($iterable, fn ($value, $key) => $value % 2) as $key => $item) {
            $result[$key] = $item;
        }

        // Then  - 1 and 3 are truthy (odd), 0 (even) is falsy
        $this->assertEquals(['a' => 1, 'c' => 3], $result);
    }

    /**
     * @test filterWithKeys is lazy
     */
    public function testLaziness(): void
    {
        // Given
        $count = 0;
        $iterable = GeneratorFixture::getGenerator([1, 2, 3, 4, 5, 6]);

        // When
        $result = [];
        foreach (
            Single::filterWithKeys($iterable, function ($value, $key) use (&$count) {
                $count++;
                return true;
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

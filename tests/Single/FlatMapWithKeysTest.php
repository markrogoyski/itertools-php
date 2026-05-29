<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class FlatMapWithKeysTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         flatMapWithKeys array
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
        foreach (Single::flatMapWithKeys($iterable, $func) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [
                [],
                fn ($value, $key) => [$value],
                [],
            ],
            // callback returning scalar
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                fn ($value, $key) => $value + 1,
                [2, 3, 4],
            ],
            // callback returning array
            [
                ['a' => 1, 'b' => 2],
                fn ($value, $key) => [$key, $value],
                ['a', 1, 'b', 2],
            ],
            // callback returning Generator
            [
                ['x' => 2, 'y' => 3],
                fn ($value, $key) => Single::repeat($key, $value),
                ['x', 'x', 'y', 'y', 'y'],
            ],
            // callback returning empty iterable
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                fn ($value, $key) => [],
                [],
            ],
            // key used in output
            [
                ['one' => 1, 'two' => 2],
                fn ($value, $key) => ["$key:$value"],
                ['one:1', 'two:2'],
            ],
        ];
    }

    /**
     * @test         flatMapWithKeys generator
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
        foreach (Single::flatMapWithKeys($iterable, $func) as $item) {
            $result[] = $item;
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
                fn ($value, $key) => [$value],
                [],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                fn ($value, $key) => $value + 1,
                [2, 3, 4],
            ],
            [
                $gen(['a' => 1, 'b' => 2]),
                fn ($value, $key) => [$key, $value],
                ['a', 1, 'b', 2],
            ],
            [
                $gen(['x' => 2, 'y' => 3]),
                fn ($value, $key) => Single::repeat($key, $value),
                ['x', 'x', 'y', 'y', 'y'],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                fn ($value, $key) => [],
                [],
            ],
            [
                $gen(['one' => 1, 'two' => 2]),
                fn ($value, $key) => ["$key:$value"],
                ['one:1', 'two:2'],
            ],
        ];
    }

    /**
     * @test         flatMapWithKeys iterator
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
        foreach (Single::flatMapWithKeys($iterable, $func) as $item) {
            $result[] = $item;
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
                fn ($value, $key) => [$value],
                [],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                fn ($value, $key) => $value + 1,
                [2, 3, 4],
            ],
            [
                $iter(['a' => 1, 'b' => 2]),
                fn ($value, $key) => [$key, $value],
                ['a', 1, 'b', 2],
            ],
            [
                $iter(['x' => 2, 'y' => 3]),
                fn ($value, $key) => Single::repeat($key, $value),
                ['x', 'x', 'y', 'y', 'y'],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                fn ($value, $key) => [],
                [],
            ],
            [
                $iter(['one' => 1, 'two' => 2]),
                fn ($value, $key) => ["$key:$value"],
                ['one:1', 'two:2'],
            ],
        ];
    }

    /**
     * @test         flatMapWithKeys traversable
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
        foreach (Single::flatMapWithKeys($iterable, $func) as $item) {
            $result[] = $item;
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
                fn ($value, $key) => [$value],
                [],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                fn ($value, $key) => $value + 1,
                [2, 3, 4],
            ],
            [
                $trav(['a' => 1, 'b' => 2]),
                fn ($value, $key) => [$key, $value],
                ['a', 1, 'b', 2],
            ],
            [
                $trav(['x' => 2, 'y' => 3]),
                fn ($value, $key) => Single::repeat($key, $value),
                ['x', 'x', 'y', 'y', 'y'],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                fn ($value, $key) => [],
                [],
            ],
            [
                $trav(['one' => 1, 'two' => 2]),
                fn ($value, $key) => ["$key:$value"],
                ['one:1', 'two:2'],
            ],
        ];
    }

    /**
     * @test flatMapWithKeys callback receives value, key, self
     */
    public function testCallbackArgumentOrder(): void
    {
        // Given
        $iterable = ['first' => 'a', 'second' => 'b'];
        $received = [];

        // When
        foreach (
            Single::flatMapWithKeys($iterable, function ($value, $key, $self) use (&$received) {
                $received[] = [$value, $key, \is_callable($self)];
                return [$value];
            }) as $_
        ) {
            // consume
        }

        // Then
        $this->assertEquals([['a', 'first', true], ['b', 'second', true]], $received);
    }

    /**
     * @test flatMapWithKeys recursive flattening via the third $self argument
     */
    public function testRecursiveFlattening(): void
    {
        // Given
        $iterable = [
            'a' => [1, 2, ['x' => 3, 'y' => [4, 5]], 6],
            'b' => [7],
            'c' => 8,
        ];

        // When
        $result = [];
        $func = fn ($value, $key, $self) => \is_iterable($value)
            ? Single::flatMapWithKeys($value, $self)
            : [$value];
        foreach (Single::flatMapWithKeys($iterable, $func) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8], $result);
    }

    /**
     * @test flatMapWithKeys discards outer and inner keys (auto-generated numeric keys)
     */
    public function testKeysAreDiscarded(): void
    {
        // Given
        $iterable = ['outer1' => ['inner1' => 10], 'outer2' => ['inner2' => 20, 'inner3' => 30]];

        // When
        $result = [];
        foreach (Single::flatMapWithKeys($iterable, fn ($value, $key) => $value) as $key => $item) {
            $result[$key] = $item;
        }

        // Then  - keys are sequential integers, not the original string keys
        $this->assertEquals([0 => 10, 1 => 20, 2 => 30], $result);
        $this->assertSame([0, 1, 2], \array_keys($result));
    }

    /**
     * @test flatMapWithKeys is lazy
     */
    public function testLaziness(): void
    {
        // Given
        $count = 0;
        $iterable = GeneratorFixture::getGenerator([1, 2, 3, 4, 5]);

        // When
        $result = [];
        foreach (
            Single::flatMapWithKeys($iterable, function ($value, $key, $self) use (&$count) {
                $count++;
                return [$value];
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

<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture;

class TakeLastTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         takeLast array
     * @dataProvider dataProviderForArray
     * @param        array $iterable
     * @param        int   $count
     * @param        array $expected
     */
    public function testArray(array $iterable, int $count, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::takeLast($iterable, $count) as $item) {
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
                0,
                [],
            ],
            [
                [],
                3,
                [],
            ],
            [
                [1, 2, 3, 4, 5],
                0,
                [],
            ],
            [
                [1, 2, 3, 4, 5],
                1,
                [5],
            ],
            [
                [1, 2, 3, 4, 5],
                2,
                [4, 5],
            ],
            [
                [1, 2, 3, 4, 5],
                3,
                [3, 4, 5],
            ],
            [
                [1, 2, 3, 4, 5],
                5,
                [1, 2, 3, 4, 5],
            ],
            [
                [1, 2, 3, 4, 5],
                6,
                [1, 2, 3, 4, 5],
            ],
            [
                [1, 2, 3, 4, 5],
                100,
                [1, 2, 3, 4, 5],
            ],
            [
                [42],
                1,
                [42],
            ],
            [
                ['a', 'b', 'c'],
                2,
                ['b', 'c'],
            ],
        ];
    }

    /**
     * @test         takeLast generator
     * @dataProvider dataProviderForGenerator
     * @param        \Generator $iterable
     * @param        int        $count
     * @param        array      $expected
     */
    public function testGenerator(\Generator $iterable, int $count, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::takeLast($iterable, $count) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForGenerator(): array
    {
        $gen = fn (array $data) => Fixture\GeneratorFixture::getGenerator($data);

        return [
            [$gen([]), 0, []],
            [$gen([]), 3, []],
            [$gen([1, 2, 3, 4, 5]), 0, []],
            [$gen([1, 2, 3, 4, 5]), 1, [5]],
            [$gen([1, 2, 3, 4, 5]), 2, [4, 5]],
            [$gen([1, 2, 3, 4, 5]), 3, [3, 4, 5]],
            [$gen([1, 2, 3, 4, 5]), 5, [1, 2, 3, 4, 5]],
            [$gen([1, 2, 3, 4, 5]), 6, [1, 2, 3, 4, 5]],
            [$gen([1, 2, 3, 4, 5]), 100, [1, 2, 3, 4, 5]],
            [$gen([42]), 1, [42]],
            [$gen(['a', 'b', 'c']), 2, ['b', 'c']],
        ];
    }

    /**
     * @test         takeLast iterator
     * @dataProvider dataProviderForIterator
     * @param        \Iterator $iterable
     * @param        int       $count
     * @param        array     $expected
     */
    public function testIterator(\Iterator $iterable, int $count, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::takeLast($iterable, $count) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForIterator(): array
    {
        $iter = fn (array $data) => new Fixture\ArrayIteratorFixture($data);

        return [
            [$iter([]), 0, []],
            [$iter([]), 3, []],
            [$iter([1, 2, 3, 4, 5]), 0, []],
            [$iter([1, 2, 3, 4, 5]), 1, [5]],
            [$iter([1, 2, 3, 4, 5]), 2, [4, 5]],
            [$iter([1, 2, 3, 4, 5]), 3, [3, 4, 5]],
            [$iter([1, 2, 3, 4, 5]), 5, [1, 2, 3, 4, 5]],
            [$iter([1, 2, 3, 4, 5]), 6, [1, 2, 3, 4, 5]],
            [$iter([1, 2, 3, 4, 5]), 100, [1, 2, 3, 4, 5]],
            [$iter([42]), 1, [42]],
            [$iter(['a', 'b', 'c']), 2, ['b', 'c']],
        ];
    }

    /**
     * @test         takeLast traversable
     * @dataProvider dataProviderForTraversable
     * @param        \Traversable $iterable
     * @param        int          $count
     * @param        array        $expected
     */
    public function testTraversable(\Traversable $iterable, int $count, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::takeLast($iterable, $count) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForTraversable(): array
    {
        $trav = fn (array $data) => new Fixture\IteratorAggregateFixture($data);

        return [
            [$trav([]), 0, []],
            [$trav([]), 3, []],
            [$trav([1, 2, 3, 4, 5]), 0, []],
            [$trav([1, 2, 3, 4, 5]), 1, [5]],
            [$trav([1, 2, 3, 4, 5]), 2, [4, 5]],
            [$trav([1, 2, 3, 4, 5]), 3, [3, 4, 5]],
            [$trav([1, 2, 3, 4, 5]), 5, [1, 2, 3, 4, 5]],
            [$trav([1, 2, 3, 4, 5]), 6, [1, 2, 3, 4, 5]],
            [$trav([1, 2, 3, 4, 5]), 100, [1, 2, 3, 4, 5]],
            [$trav([42]), 1, [42]],
            [$trav(['a', 'b', 'c']), 2, ['b', 'c']],
        ];
    }

    /**
     * @test takeLast preserves keys
     */
    public function testPreservesKeys(): void
    {
        // Given
        $iterable = ['a' => 50, 'b' => 60, 'c' => 70, 'd' => 85, 'e' => 65, 'f' => 90];
        $count    = 2;

        // And
        $expected = ['e' => 65, 'f' => 90];

        // When
        $result = [];
        foreach (Single::takeLast($iterable, $count) as $key => $item) {
            $result[$key] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test takeLast with negative count throws
     */
    public function testNegativeCountThrows(): void
    {
        // Given
        $data         = [1, 2, 3];
        $invalidCount = -1;

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (Single::takeLast($data, $invalidCount) as $_) {
            break;
        }
    }

    /**
     * @test takeLast over a large iterable with a small count does not materialize the whole input
     */
    public function testLargeIterableSmallCount(): void
    {
        // Given a huge lazy source that would exhaust memory if materialized
        $count   = 3;
        $largeGen = (function () {
            for ($i = 1; $i <= 1_000_000; $i++) {
                yield $i;
            }
        })();

        // When
        $result = [];
        foreach (Single::takeLast($largeGen, $count) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals([999_998, 999_999, 1_000_000], $result);
    }
}

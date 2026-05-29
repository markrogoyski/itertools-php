<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture;

class DropLastTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         dropLast array
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
        foreach (Single::dropLast($iterable, $count) as $item) {
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
                [1, 2, 3, 4, 5],
            ],
            [
                [1, 2, 3, 4, 5],
                1,
                [1, 2, 3, 4],
            ],
            [
                [1, 2, 3, 4, 5],
                2,
                [1, 2, 3],
            ],
            [
                [1, 2, 3, 4, 5],
                3,
                [1, 2],
            ],
            [
                [1, 2, 3, 4, 5],
                5,
                [],
            ],
            [
                [1, 2, 3, 4, 5],
                6,
                [],
            ],
            [
                [1, 2, 3, 4, 5],
                100,
                [],
            ],
            [
                [42],
                1,
                [],
            ],
            [
                ['a', 'b', 'c'],
                1,
                ['a', 'b'],
            ],
        ];
    }

    /**
     * @test         dropLast generator
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
        foreach (Single::dropLast($iterable, $count) as $item) {
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
            [$gen([1, 2, 3, 4, 5]), 0, [1, 2, 3, 4, 5]],
            [$gen([1, 2, 3, 4, 5]), 1, [1, 2, 3, 4]],
            [$gen([1, 2, 3, 4, 5]), 2, [1, 2, 3]],
            [$gen([1, 2, 3, 4, 5]), 3, [1, 2]],
            [$gen([1, 2, 3, 4, 5]), 5, []],
            [$gen([1, 2, 3, 4, 5]), 6, []],
            [$gen([1, 2, 3, 4, 5]), 100, []],
            [$gen([42]), 1, []],
            [$gen(['a', 'b', 'c']), 1, ['a', 'b']],
        ];
    }

    /**
     * @test         dropLast iterator
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
        foreach (Single::dropLast($iterable, $count) as $item) {
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
            [$iter([1, 2, 3, 4, 5]), 0, [1, 2, 3, 4, 5]],
            [$iter([1, 2, 3, 4, 5]), 1, [1, 2, 3, 4]],
            [$iter([1, 2, 3, 4, 5]), 2, [1, 2, 3]],
            [$iter([1, 2, 3, 4, 5]), 3, [1, 2]],
            [$iter([1, 2, 3, 4, 5]), 5, []],
            [$iter([1, 2, 3, 4, 5]), 6, []],
            [$iter([1, 2, 3, 4, 5]), 100, []],
            [$iter([42]), 1, []],
            [$iter(['a', 'b', 'c']), 1, ['a', 'b']],
        ];
    }

    /**
     * @test         dropLast traversable
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
        foreach (Single::dropLast($iterable, $count) as $item) {
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
            [$trav([1, 2, 3, 4, 5]), 0, [1, 2, 3, 4, 5]],
            [$trav([1, 2, 3, 4, 5]), 1, [1, 2, 3, 4]],
            [$trav([1, 2, 3, 4, 5]), 2, [1, 2, 3]],
            [$trav([1, 2, 3, 4, 5]), 3, [1, 2]],
            [$trav([1, 2, 3, 4, 5]), 5, []],
            [$trav([1, 2, 3, 4, 5]), 6, []],
            [$trav([1, 2, 3, 4, 5]), 100, []],
            [$trav([42]), 1, []],
            [$trav(['a', 'b', 'c']), 1, ['a', 'b']],
        ];
    }

    /**
     * @test dropLast preserves keys
     */
    public function testPreservesKeys(): void
    {
        // Given
        $iterable = ['a' => 50, 'b' => 60, 'c' => 70, 'd' => 85, 'e' => 65, 'f' => 90];
        $count    = 2;

        // And
        $expected = ['a' => 50, 'b' => 60, 'c' => 70, 'd' => 85];

        // When
        $result = [];
        foreach (Single::dropLast($iterable, $count) as $key => $item) {
            $result[$key] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test dropLast with negative count throws
     */
    public function testNegativeCountThrows(): void
    {
        // Given
        $data         = [1, 2, 3];
        $invalidCount = -1;

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (Single::dropLast($data, $invalidCount) as $_) {
            break;
        }
    }

    /**
     * @test dropLast over a large iterable yields lazily without materializing it all
     */
    public function testLargeIterableSmallCount(): void
    {
        // Given a huge lazy source
        $count    = 3;
        $largeGen = (function () {
            for ($i = 1; $i <= 1_000_000; $i++) {
                yield $i;
            }
        })();

        // When we only consume the first few elements
        $result = [];
        foreach (Single::dropLast($largeGen, $count) as $item) {
            $result[] = $item;
            if (\count($result) === 3) {
                break;
            }
        }

        // Then the first elements are produced without exhausting the source
        $this->assertEquals([1, 2, 3], $result);
    }
}

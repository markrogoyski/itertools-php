<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture;
use IterTools\Tests\Fixture\DataProvider;

class WithLastTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test         withLast array
     * @dataProvider dataProviderForArray
     * @param        array $iterable
     * @param        array $expected
     */
    public function testArray(array $iterable, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::withLast($iterable) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [[], []],
            [[42], [[true, 42]]],
            [[1, 2], [[false, 1], [true, 2]]],
            [[1, 2, 3], [[false, 1], [false, 2], [true, 3]]],
            [['a', 'b', 'c'], [[false, 'a'], [false, 'b'], [true, 'c']]],
        ];
    }

    /**
     * @test         withLast generator
     * @dataProvider dataProviderForGenerator
     * @param        \Generator $iterable
     * @param        array      $expected
     */
    public function testGenerator(\Generator $iterable, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::withLast($iterable) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForGenerator(): array
    {
        $gen = fn (array $data) => Fixture\GeneratorFixture::getGenerator($data);

        return [
            [$gen([]), []],
            [$gen([42]), [[true, 42]]],
            [$gen([1, 2]), [[false, 1], [true, 2]]],
            [$gen([1, 2, 3]), [[false, 1], [false, 2], [true, 3]]],
            [$gen(['a', 'b', 'c']), [[false, 'a'], [false, 'b'], [true, 'c']]],
        ];
    }

    /**
     * @test         withLast iterator
     * @dataProvider dataProviderForIterator
     * @param        \Iterator $iterable
     * @param        array     $expected
     */
    public function testIterator(\Iterator $iterable, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::withLast($iterable) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForIterator(): array
    {
        $iter = fn (array $data) => new Fixture\ArrayIteratorFixture($data);

        return [
            [$iter([]), []],
            [$iter([42]), [[true, 42]]],
            [$iter([1, 2]), [[false, 1], [true, 2]]],
            [$iter([1, 2, 3]), [[false, 1], [false, 2], [true, 3]]],
            [$iter(['a', 'b', 'c']), [[false, 'a'], [false, 'b'], [true, 'c']]],
        ];
    }

    /**
     * @test         withLast traversable
     * @dataProvider dataProviderForTraversable
     * @param        \Traversable $iterable
     * @param        array        $expected
     */
    public function testTraversable(\Traversable $iterable, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::withLast($iterable) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForTraversable(): array
    {
        $trav = fn (array $data) => new Fixture\IteratorAggregateFixture($data);

        return [
            [$trav([]), []],
            [$trav([42]), [[true, 42]]],
            [$trav([1, 2]), [[false, 1], [true, 2]]],
            [$trav([1, 2, 3]), [[false, 1], [false, 2], [true, 3]]],
            [$trav(['a', 'b', 'c']), [[false, 'a'], [false, 'b'], [true, 'c']]],
        ];
    }

    /**
     * @test         withLast on empty iterable yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $iterable
     */
    public function testEmpty(iterable $iterable): void
    {
        // When
        $result = [];
        foreach (Single::withLast($iterable) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test withLast discards source keys and yields sequential 0-indexed keys
     */
    public function testSequentialKeys(): void
    {
        // Given string-keyed input
        $iterable = ['x' => 1, 'y' => 2, 'z' => 3];

        // When
        $keys = [];
        foreach (Single::withLast($iterable) as $key => $_) {
            $keys[] = $key;
        }

        // Then
        $this->assertSame([0, 1, 2], $keys);
    }

    /**
     * @test withLast over a large iterable is lazy (does not materialize the whole input)
     */
    public function testLargeIterableLazy(): void
    {
        // Given a huge lazy source that would exhaust memory if materialized
        $largeGen = (function () {
            for ($i = 1; $i <= 1_000_000; $i++) {
                yield $i;
            }
        })();

        // When taking only the first 3 marked elements
        $result = [];
        foreach (Single::withLast($largeGen) as $item) {
            $result[] = $item;
            if (\count($result) === 3) {
                break;
            }
        }

        // Then (single-element lookahead means each element is yielded once its successor is seen)
        $this->assertEquals([[false, 1], [false, 2], [false, 3]], $result);
    }
}

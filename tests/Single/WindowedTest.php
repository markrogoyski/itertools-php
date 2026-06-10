<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture;
use IterTools\Tests\Fixture\DataProvider;

class WindowedTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test         windowed array
     * @dataProvider dataProviderForWindowed
     * @param        array $data
     * @param        int   $size
     * @param        int   $step
     * @param        bool  $partial
     * @param        array $expected
     */
    public function testArray(array $data, int $size, int $step, bool $partial, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::windowed($data, $size, $step, $partial) as $window) {
            $result[] = $window;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         windowed generator
     * @dataProvider dataProviderForWindowed
     * @param        array $data
     * @param        int   $size
     * @param        int   $step
     * @param        bool  $partial
     * @param        array $expected
     */
    public function testGenerator(array $data, int $size, int $step, bool $partial, array $expected): void
    {
        // Given
        $iterable = Fixture\GeneratorFixture::getGenerator($data);
        $result = [];

        // When
        foreach (Single::windowed($iterable, $size, $step, $partial) as $window) {
            $result[] = $window;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         windowed iterator
     * @dataProvider dataProviderForWindowed
     * @param        array $data
     * @param        int   $size
     * @param        int   $step
     * @param        bool  $partial
     * @param        array $expected
     */
    public function testIterator(array $data, int $size, int $step, bool $partial, array $expected): void
    {
        // Given
        $iterable = new Fixture\ArrayIteratorFixture($data);
        $result = [];

        // When
        foreach (Single::windowed($iterable, $size, $step, $partial) as $window) {
            $result[] = $window;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         windowed traversable
     * @dataProvider dataProviderForWindowed
     * @param        array $data
     * @param        int   $size
     * @param        int   $step
     * @param        bool  $partial
     * @param        array $expected
     */
    public function testTraversable(array $data, int $size, int $step, bool $partial, array $expected): void
    {
        // Given
        $iterable = new Fixture\IteratorAggregateFixture($data);
        $result = [];

        // When
        foreach (Single::windowed($iterable, $size, $step, $partial) as $window) {
            $result[] = $window;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForWindowed(): array
    {
        return [
            // empty
            [[], 2, 1, true, []],
            [[], 2, 1, false, []],
            // single element
            [[5], 1, 1, true, [[5]]],
            [[5], 1, 1, false, [[5]]],
            [[5], 2, 1, true, [[5]]],
            [[5], 2, 1, false, []],
            // size 1, step 1
            [[1, 2, 3], 1, 1, false, [[1], [2], [3]]],
            [[1, 2, 3], 1, 1, true, [[1], [2], [3]]],
            // step < size (overlap); no spurious trailing partial when last element completes a window
            [[1, 2, 3, 4, 5], 3, 1, true, [[1, 2, 3], [2, 3, 4], [3, 4, 5]]],
            [[1, 2, 3, 4, 5], 3, 1, false, [[1, 2, 3], [2, 3, 4], [3, 4, 5]]],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                3,
                1,
                true,
                [[1, 2, 3], [2, 3, 4], [3, 4, 5], [4, 5, 6], [5, 6, 7], [6, 7, 8], [7, 8, 9], [8, 9, 10]],
            ],
            // step == size (tiling); partial governs the incomplete tail
            [[1, 2, 3, 4, 5], 2, 2, true, [[1, 2], [3, 4], [5]]],
            [[1, 2, 3, 4, 5], 2, 2, false, [[1, 2], [3, 4]]],
            [[1, 2, 3, 4], 2, 2, true, [[1, 2], [3, 4]]],
            [[1, 2, 3, 4], 2, 2, false, [[1, 2], [3, 4]]],
            // step > size (gapped windows; dropped elements between windows)
            [[1, 2, 3, 4, 5, 6], 2, 5, true, [[1, 2], [6]]],
            [[1, 2, 3, 4, 5, 6, 7], 2, 5, true, [[1, 2], [6, 7]]],
            [[1, 2, 3, 4, 5, 6, 7, 8], 2, 5, true, [[1, 2], [6, 7]]],
            [[1, 2, 3, 4, 5, 6], 2, 5, false, [[1, 2]]],
            [[1, 2, 3, 4, 5, 6, 7], 2, 5, false, [[1, 2], [6, 7]]],
            // size > input length
            [[1, 2, 3], 5, 1, false, []],
            [[1, 2, 3], 5, 1, true, [[1, 2, 3]]],
            // negative values pass through unchanged
            [[-1, -2, -3, -4], 2, 1, false, [[-1, -2], [-2, -3], [-3, -4]]],
        ];
    }

    /**
     * @test windowed produces 0-indexed list windows and discards source keys
     */
    public function testZeroIndexedWindowKeys(): void
    {
        // Given string-keyed input
        $iterable = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        // When
        $result = [];
        foreach (Single::windowed($iterable, 2, 1) as $key => $window) {
            $result[$key] = $window;
        }

        // Then both the outer keys and the inner window arrays are 0-indexed (assertSame is key-strict)
        $this->assertSame([[1, 2], [2, 3], [3, 4]], $result);
    }

    /**
     * @test         windowed is elementwise-equivalent to chunkwiseOverlap for 1 <= step <= size
     * @dataProvider dataProviderForEquivalence
     * @param        int  $size
     * @param        int  $step
     * @param        bool $partial
     */
    public function testEquivalenceWithChunkwiseOverlap(int $size, int $step, bool $partial): void
    {
        // Given
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        // When
        $windowed = \iterator_to_array(Single::windowed($data, $size, $step, $partial), false);
        $chunked  = \iterator_to_array(
            Single::chunkwiseOverlap($data, $size, $size - $step, $partial),
            false
        );

        // Then
        $this->assertEquals($chunked, $windowed);
    }

    public static function dataProviderForEquivalence(): array
    {
        return [
            [1, 1, true],
            [1, 1, false],
            [2, 1, true],
            [2, 1, false],
            [2, 2, true],
            [2, 2, false],
            [3, 1, true],
            [3, 1, false],
            [3, 2, true],
            [3, 2, false],
            [3, 3, true],
            [3, 3, false],
            [4, 1, true],
            [4, 1, false],
            [4, 2, true],
            [4, 2, false],
            [4, 3, true],
            [4, 3, false],
            [4, 4, true],
            [4, 4, false],
        ];
    }

    /**
     * @test         windowed throws on invalid size
     * @dataProvider dataProviderForInvalidSize
     * @param        int $size
     */
    public function testInvalidSizeThrows(int $size): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Window size must be ≥ 1. Got {$size}");

        // When
        foreach (Single::windowed([1, 2, 3], $size, 1) as $_) {
            break;
        }
    }

    public static function dataProviderForInvalidSize(): array
    {
        return [
            [0],
            [-1],
        ];
    }

    /**
     * @test         windowed throws on invalid step
     * @dataProvider dataProviderForInvalidStep
     * @param        int $step
     */
    public function testInvalidStepThrows(int $step): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Step must be ≥ 1. Got {$step}");

        // When
        foreach (Single::windowed([1, 2, 3], 2, $step) as $_) {
            break;
        }
    }

    public static function dataProviderForInvalidStep(): array
    {
        return [
            [0],
            [-1],
        ];
    }

    /**
     * @test         windowed on empty iterable yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $iterable
     */
    public function testEmpty(iterable $iterable): void
    {
        // When
        $result = [];
        foreach (Single::windowed($iterable, 2, 1, true) as $window) {
            $result[] = $window;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test windowed over a large iterable is memory-bounded (does not materialize the whole input)
     */
    public function testLargeIterableMemoryBounded(): void
    {
        // Given a huge lazy source that would exhaust memory if materialized
        $largeGen = (function () {
            for ($i = 1; $i <= 1_000_000; $i++) {
                yield $i;
            }
        })();

        // When taking only the first 3 windows
        $result = [];
        foreach (Single::windowed($largeGen, 3, 1) as $window) {
            $result[] = $window;
            if (\count($result) === 3) {
                break;
            }
        }

        // Then
        $this->assertEquals([[1, 2, 3], [2, 3, 4], [3, 4, 5]], $result);
    }
}

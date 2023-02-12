<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Single;
use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class SingleTest extends \PHPUnit\Framework\TestCase
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
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toArray(),
                [1, -1, 2, -2, 3, -3],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($value) => $value > 0)
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($value) => $value > 0)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue()
                    ->toArray(),
                [1, -1, 2, -2, 3, -3],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->toArray(),
                [1, -1, 2, -2],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1, 0, 1])
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1, 0, 1])
                    ->toArray(),
                [-1, -2],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->dropWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1])
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->dropWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1])
                    ->toArray(),
                [-3],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [2, 3],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filter(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [2, 3],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [2, 3],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [2, 3],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toArray(),
                [-1, -2, -3],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse()
                    ->toArray(),
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [[1, 2], [2, 3]],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [[-1, -2], [-2, -3]],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwise(2)
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwise(2)
                    ->toArray(),
                [[-1, -2], [-3]],
            ],
            [
                [-3, -2, -1, 0, 1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwise(2)
                    ->toArray(),
                [[1, 2], [3, 4], [5]],
            ],
            [
                [-3, -2, -1, 0, 1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwise(2)
                    ->toArray(),
                [[0, 1], [2, 3], [4, 5]],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [[-1, -2], [-3]],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [[-1, -2]],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [[-1, -2], [-2, -3]],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [[-1, -2], [-2, -3]],
            ],
            [
                [-3, -2, -1, 0, 1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [[1, 2], [3, 4], [5]],
            ],
            [
                [-3, -2, -1, 0, 1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [[1, 2], [3, 4]],
            ],
            [
                [-3, -2, -1, 0, 1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [[1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                [-3, -2, -1, 0, 1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [[1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                [-3, -2, -1, 0, 1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [[0, 1], [2, 3], [4, 5]],
            ],
            [
                [-3, -2, -1, 0, 1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [[0, 1], [2, 3], [4, 5]],
            ],
            [
                [-3, -2, -1, 0, 1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                [-3, -2, -1, 0, 1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                [-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 1)
                    ->toArray(),
                [[0, 1, 2], [2, 3, 4], [4, 5, 6], [6, 7, 8], [8, 9]],
            ],
            [
                [-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 1, false)
                    ->toArray(),
                [[0, 1, 2], [2, 3, 4], [4, 5, 6], [6, 7, 8]],
            ],
            [
                [-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 2)
                    ->toArray(),
                [[0, 1, 2], [1, 2, 3], [2, 3, 4], [3, 4, 5], [4, 5, 6], [5, 6, 7], [6, 7, 8], [7, 8, 9]],
            ],
            [
                [-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 2, false)
                    ->toArray(),
                [[0, 1, 2], [1, 2, 3], [2, 3, 4], [3, 4, 5], [4, 5, 6], [5, 6, 7], [6, 7, 8], [7, 8, 9]],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value % 2 === 0)
                    ->groupBy(fn ($item) => $item > 0 ? 'pos' : 'neg')
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value % 2 === 0)
                    ->groupBy(fn ($item) => $item > 0 ? 'pos' : 'neg')
                    ->toArray(),
                [[1, 3], [-1, -3]], // ['pos' => [1, 3], 'neg' => [-1, -3]],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(0)
                    ->toArray(),
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(5)
                    ->toArray(),
                [],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(5)
                    ->toArray(),
                [1, 2, 3, 4, 5],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(10)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, 7],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($x) => $x < 5)
                    ->limit(2)
                    ->toArray(),
                [1, 2],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($x) => $x < 5)
                    ->limit(10)
                    ->toArray(),
                [1, 2, 3, 4],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(0)
                    ->chainWith([1, 2, 3])
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->infiniteCycle()
                    ->limit(10)
                    ->toArray(),
                [1, 2, 3, 1, 2, 3, 1, 2, 3, 1],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->map(fn ($x) => $x * 2)
                    ->toArray(),
                [],
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->map('sqrt')
                    ->toArray(),
                [],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->map(fn ($x) => $x * 2)
                    ->toArray(),
                [2, -2, 4, -4, 6, -6],
            ],
            [
                [1, 4, 9, 16, 25],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->map('sqrt')
                    ->toArray(),
                [1.0, 2.0, 3.0, 4.0, 5.0],
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->map(fn ($x) => $x + 1)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterKeys(fn ($key) => in_array($key, ['a', 'c', 'e', 'g']))
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->reindex(fn ($value, $key) => "{$key}_{$value}")
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                ['0_1' => 1, '1_2' => 2, '2_3' => 3, '3_4' => 4, '4_5' => 5],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->compressAssociative(['a', 'c', 'e', 'g'])
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                [1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->compressAssociative([0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14])
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                [0 => 1, '1' => 2, 2 => 3, '3' => 4, 4 => 5, 'a' => 11, 'b' => 22],
            ],
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
                [-5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->reverse()
                    ->toArray(),
                [5, 4, 3, 2, 1],
            ],
            [
                [-5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->reverse()
                    ->toAssociativeArray(),
                [10 => 5, 9 => 4, 8 => 3, 7 => 2, 6 => 1],
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->flatMap(fn ($item) => Single::repeat($item, $item + 1))
                    ->toArray(),
                [1, 1, 2, 2, 2, 3, 3, 3, 3],
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
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toArray(),
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toArray(),
                [1, -1, 2, -2, 3, -3],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toArray(),
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->toArray(),
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->toArray(),
                [1, -1, 2, -2],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1, 0, 1])
                    ->toArray(),
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1, 0, 1])
                    ->toArray(),
                [-1, -2],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->dropWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1])
                    ->toArray(),
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->dropWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1])
                    ->toArray(),
                [-3],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [2, 3],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [2, 3],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toArray(),
                [-1, -2, -3],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [[1, 2], [2, 3]],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [[-1, -2], [-2, -3]],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwise(2)
                    ->toArray(),
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwise(2)
                    ->toArray(),
                [[-1, -2], [-3]],
            ],
            [
                $gen([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwise(2)
                    ->toArray(),
                [[1, 2], [3, 4], [5]],
            ],
            [
                $gen([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwise(2)
                    ->toArray(),
                [[0, 1], [2, 3], [4, 5]],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [[-1, -2], [-3]],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [[-1, -2]],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [[-1, -2], [-2, -3]],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [[-1, -2], [-2, -3]],
            ],
            [
                $gen([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [[1, 2], [3, 4], [5]],
            ],
            [
                $gen([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [[1, 2], [3, 4]],
            ],
            [
                $gen([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [[1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                $gen([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [[1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                $gen([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [[0, 1], [2, 3], [4, 5]],
            ],
            [
                $gen([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [[0, 1], [2, 3], [4, 5]],
            ],
            [
                $gen([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                $gen([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                $gen([-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 1)
                    ->toArray(),
                [[0, 1, 2], [2, 3, 4], [4, 5, 6], [6, 7, 8], [8, 9]],
            ],
            [
                $gen([-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 1, false)
                    ->toArray(),
                [[0, 1, 2], [2, 3, 4], [4, 5, 6], [6, 7, 8]],
            ],
            [
                $gen([-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 2)
                    ->toArray(),
                [[0, 1, 2], [1, 2, 3], [2, 3, 4], [3, 4, 5], [4, 5, 6], [5, 6, 7], [6, 7, 8], [7, 8, 9]],
            ],
            [
                $gen([-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 2, false)
                    ->toArray(),
                [[0, 1, 2], [1, 2, 3], [2, 3, 4], [3, 4, 5], [4, 5, 6], [5, 6, 7], [6, 7, 8], [7, 8, 9]],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value % 2 === 0)
                    ->groupBy(fn ($item) => $item > 0 ? 'pos' : 'neg')
                    ->toArray(),
                [],
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value % 2 === 0)
                    ->groupBy(fn ($item) => $item > 0 ? 'pos' : 'neg')
                    ->toArray(),
                [[1, 3], [-1, -3]], // ['pos' => [1, 3], 'neg' => [-1, -3]],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(0)
                    ->toArray(),
                [],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(5)
                    ->toArray(),
                [],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(5)
                    ->toArray(),
                [1, 2, 3, 4, 5],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(10)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, 7],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($x) => $x < 5)
                    ->limit(2)
                    ->toArray(),
                [1, 2],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($x) => $x < 5)
                    ->limit(10)
                    ->toArray(),
                [1, 2, 3, 4],
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(0)
                    ->chainWith($gen([1, 2, 3]))
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->infiniteCycle()
                    ->limit(10)
                    ->toArray(),
                [1, 2, 3, 1, 2, 3, 1, 2, 3, 1],
            ],
            [
                GeneratorFixture::getKeyValueGenerator(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterKeys(fn ($key) => in_array($key, ['a', 'c', 'e', 'g']))
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                GeneratorFixture::getKeyValueGenerator([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->reindex(fn ($value, $key) => "{$key}_{$value}")
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                ['0_1' => 1, '1_2' => 2, '2_3' => 3, '3_4' => 4, '4_5' => 5],
            ],
            [
                GeneratorFixture::getKeyValueGenerator(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->compressAssociative(['a', 'c', 'e', 'g'])
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                GeneratorFixture::getKeyValueGenerator([1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->compressAssociative([0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14])
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                [0 => 1, '1' => 2, 2 => 3, '3' => 4, 4 => 5, 'a' => 11, 'b' => 22],
            ],
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
                $gen([-5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->reverse()
                    ->toArray(),
                [5, 4, 3, 2, 1],
            ],
            [
                $gen([-5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->reverse()
                    ->toAssociativeArray(),
                [10 => 5, 9 => 4, 8 => 3, 7 => 2, 6 => 1],
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->flatMap(fn ($item) => Single::repeat($item, $item + 1))
                    ->toArray(),
                [1, 1, 2, 2, 2, 3, 3, 3, 3],
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
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toArray(),
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toArray(),
                [1, -1, 2, -2, 3, -3],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toArray(),
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->toArray(),
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->toArray(),
                [1, -1, 2, -2],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1, 0, 1])
                    ->toArray(),
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1, 0, 1])
                    ->toArray(),
                [-1, -2],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->dropWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1])
                    ->toArray(),
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->dropWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1])
                    ->toArray(),
                [-3],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [2, 3],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [2, 3],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toArray(),
                [-1, -2, -3],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [[1, 2], [2, 3]],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [[-1, -2], [-2, -3]],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwise(2)
                    ->toArray(),
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwise(2)
                    ->toArray(),
                [[-1, -2], [-3]],
            ],
            [
                $iter([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwise(2)
                    ->toArray(),
                [[1, 2], [3, 4], [5]],
            ],
            [
                $iter([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwise(2)
                    ->toArray(),
                [[0, 1], [2, 3], [4, 5]],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [[-1, -2], [-3]],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [[-1, -2]],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [[-1, -2], [-2, -3]],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [[-1, -2], [-2, -3]],
            ],
            [
                $iter([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [[1, 2], [3, 4], [5]],
            ],
            [
                $iter([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [[1, 2], [3, 4]],
            ],
            [
                $iter([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [[1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                $iter([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [[1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                $iter([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [[0, 1], [2, 3], [4, 5]],
            ],
            [
                $iter([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [[0, 1], [2, 3], [4, 5]],
            ],
            [
                $iter([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                $iter([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                $iter([-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 1)
                    ->toArray(),
                [[0, 1, 2], [2, 3, 4], [4, 5, 6], [6, 7, 8], [8, 9]],
            ],
            [
                $iter([-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 1, false)
                    ->toArray(),
                [[0, 1, 2], [2, 3, 4], [4, 5, 6], [6, 7, 8]],
            ],
            [
                $iter([-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 2)
                    ->toArray(),
                [[0, 1, 2], [1, 2, 3], [2, 3, 4], [3, 4, 5], [4, 5, 6], [5, 6, 7], [6, 7, 8], [7, 8, 9]],
            ],
            [
                $iter([-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 2, false)
                    ->toArray(),
                [[0, 1, 2], [1, 2, 3], [2, 3, 4], [3, 4, 5], [4, 5, 6], [5, 6, 7], [6, 7, 8], [7, 8, 9]],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value % 2 === 0)
                    ->groupBy(fn ($item) => $item > 0 ? 'pos' : 'neg')
                    ->toArray(),
                [],
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value % 2 === 0)
                    ->groupBy(fn ($item) => $item > 0 ? 'pos' : 'neg')
                    ->toArray(),
                [[1, 3], [-1, -3]], // ['pos' => [1, 3], 'neg' => [-1, -3]],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(0)
                    ->toArray(),
                [],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(5)
                    ->toArray(),
                [],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(5)
                    ->toArray(),
                [1, 2, 3, 4, 5],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(10)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, 7],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($x) => $x < 5)
                    ->limit(2)
                    ->toArray(),
                [1, 2],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($x) => $x < 5)
                    ->limit(10)
                    ->toArray(),
                [1, 2, 3, 4],
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(0)
                    ->chainWith($iter([1, 2, 3]))
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->infiniteCycle()
                    ->limit(10)
                    ->toArray(),
                [1, 2, 3, 1, 2, 3, 1, 2, 3, 1],
            ],
            [
                new \ArrayIterator(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterKeys(fn ($key) => in_array($key, ['a', 'c', 'e', 'g']))
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                new \ArrayIterator([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->reindex(fn ($value, $key) => "{$key}_{$value}")
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                ['0_1' => 1, '1_2' => 2, '2_3' => 3, '3_4' => 4, '4_5' => 5],
            ],
            [
                new \ArrayIterator(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->compressAssociative(['a', 'c', 'e', 'g'])
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                new \ArrayIterator([1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->compressAssociative([0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14])
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                [0 => 1, '1' => 2, 2 => 3, '3' => 4, 4 => 5, 'a' => 11, 'b' => 22],
            ],
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
                $iter([-5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->reverse()
                    ->toArray(),
                [5, 4, 3, 2, 1],
            ],
            [
                $iter([-5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->reverse()
                    ->toAssociativeArray(),
                [10 => 5, 9 => 4, 8 => 3, 7 => 2, 6 => 1],
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->flatMap(fn ($item) => Single::repeat($item, $item + 1))
                    ->toArray(),
                [1, 1, 2, 2, 2, 3, 3, 3, 3],
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
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toArray(),
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toArray(),
                [1, -1, 2, -2, 3, -3],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toArray(),
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->toArray(),
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->toArray(),
                [1, -1, 2, -2],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1, 0, 1])
                    ->toArray(),
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->takeWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1, 0, 1])
                    ->toArray(),
                [-1, -2],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->dropWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1])
                    ->toArray(),
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->dropWhile(fn ($value) => abs($value) < 3)
                    ->compress([0, 1])
                    ->toArray(),
                [-3],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [2, 3],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->compress([0, 1, 1])
                    ->toArray(),
                [2, 3],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toArray(),
                [-1, -2, -3],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [[1, 2], [2, 3]],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toArray(),
                [[-1, -2], [-2, -3]],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwise(2)
                    ->toArray(),
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwise(2)
                    ->toArray(),
                [[-1, -2], [-3]],
            ],
            [
                $trav([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwise(2)
                    ->toArray(),
                [[1, 2], [3, 4], [5]],
            ],
            [
                $trav([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwise(2)
                    ->toArray(),
                [[0, 1], [2, 3], [4, 5]],
            ],
            [
                $trav([]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [],
            ],
            [
                $trav([]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [],
            ],
            [
                $trav([]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [],
            ],
            [
                $trav([]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [[-1, -2], [-3]],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [[-1, -2]],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [[-1, -2], [-2, -3]],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [[-1, -2], [-2, -3]],
            ],
            [
                $trav([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [[1, 2], [3, 4], [5]],
            ],
            [
                $trav([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [[1, 2], [3, 4]],
            ],
            [
                $trav([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [[1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                $trav([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [[1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                $trav([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 0)
                    ->toArray(),
                [[0, 1], [2, 3], [4, 5]],
            ],
            [
                $trav([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 0, false)
                    ->toArray(),
                [[0, 1], [2, 3], [4, 5]],
            ],
            [
                $trav([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 1)
                    ->toArray(),
                [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                $trav([-3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(2, 1, false)
                    ->toArray(),
                [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5]],
            ],
            [
                $trav([-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 1)
                    ->toArray(),
                [[0, 1, 2], [2, 3, 4], [4, 5, 6], [6, 7, 8], [8, 9]],
            ],
            [
                $trav([-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 1, false)
                    ->toArray(),
                [[0, 1, 2], [2, 3, 4], [4, 5, 6], [6, 7, 8]],
            ],
            [
                $trav([-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 2)
                    ->toArray(),
                [[0, 1, 2], [1, 2, 3], [2, 3, 4], [3, 4, 5], [4, 5, 6], [5, 6, 7], [6, 7, 8], [7, 8, 9]],
            ],
            [
                $trav([-3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9]),
                fn (iterable $travable) => Stream::of($travable)
                    ->filterTrue(fn ($value) => $value >= 0)
                    ->chunkwiseOverlap(3, 2, false)
                    ->toArray(),
                [[0, 1, 2], [1, 2, 3], [2, 3, 4], [3, 4, 5], [4, 5, 6], [5, 6, 7], [6, 7, 8], [7, 8, 9]],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value % 2 === 0)
                    ->groupBy(fn ($item) => $item > 0 ? 'pos' : 'neg')
                    ->toArray(),
                [],
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value % 2 === 0)
                    ->groupBy(fn ($item) => $item > 0 ? 'pos' : 'neg')
                    ->toArray(),
                [[1, 3], [-1, -3]], // ['pos' => [1, 3], 'neg' => [-1, -3]],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(0)
                    ->toArray(),
                [],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(5)
                    ->toArray(),
                [],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(5)
                    ->toArray(),
                [1, 2, 3, 4, 5],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(10)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, 7],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($x) => $x < 5)
                    ->limit(2)
                    ->toArray(),
                [1, 2],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($x) => $x < 5)
                    ->limit(10)
                    ->toArray(),
                [1, 2, 3, 4],
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->limit(0)
                    ->chainWith($trav([1, 2, 3]))
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->infiniteCycle()
                    ->limit(10)
                    ->toArray(),
                [1, 2, 3, 1, 2, 3, 1, 2, 3, 1],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterKeys(fn ($key) => in_array($key, ['a', 'c', 'e', 'g']))
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->reindex(fn ($value, $key) => "{$key}_{$value}")
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                ['0_1' => 1, '1_2' => 2, '2_3' => 3, '3_4' => 4, '4_5' => 5],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->compressAssociative(['a', 'c', 'e', 'g'])
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                $trav([1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->compressAssociative([0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14])
                    ->toAssociativeArray(fn ($value, $key) => $key, fn ($value) => $value),
                [0 => 1, '1' => 2, 2 => 3, '3' => 4, 4 => 5, 'a' => 11, 'b' => 22],
            ],
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
                $trav([-5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->reverse()
                    ->toArray(),
                [5, 4, 3, 2, 1],
            ],
            [
                $trav([-5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->reverse()
                    ->toAssociativeArray(),
                [10 => 5, 9 => 4, 8 => 3, 7 => 2, 6 => 1],
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->flatMap(fn ($item) => Single::repeat($item, $item + 1))
                    ->toArray(),
                [1, 1, 2, 2, 2, 3, 3, 3, 3],
            ],
        ];
    }

    public function testGroupByOnItsOwn(): void
    {
        // Given
        $data = [1, -1, 2, -2, 3, -3];

        // And
        $expected = ['pos' => [1, 2, 3], 'neg' => [-1, -2, -3]];
        $result = [];

        // When
        foreach (Stream::of($data)->groupBy(fn ($item) => $item > 0 ? 'pos' : 'neg') as $groupKey => $groupData) {
            $result[$groupKey] = $groupData;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public function testGroupByAsLastFunction(): void
    {
        // Given
        $data = [1, -1, 2, -2, 3, -3];

        // And
        $expected = ['pos' => [1, 3], 'neg' => [-1, -3]];
        $result = [];

        // When
        foreach (
            Stream::of($data)
                ->filterFalse(fn ($value) => $value % 2 === 0)
                ->groupBy(fn ($item) => $item > 0 ? 'pos' : 'neg') as $groupKey => $groupData
        ) {
            $result[$groupKey] = $groupData;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }
}

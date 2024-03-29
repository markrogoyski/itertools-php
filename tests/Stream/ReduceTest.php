<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ReduceTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @param array    $input
     * @param callable $reducingStreamFactoryFunc
     * @param mixed    $expected
     * @return void
     * @dataProvider dataProviderForArray
     */
    public function testArray(array $input, callable $reducingStreamFactoryFunc, $expected): void
    {
        // When
        $result = $reducingStreamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->toCount(),
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)->toCount(),
                6,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toCount(),
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toCount(),
                3,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                2,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toCount(),
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toCount(),
                3,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                2,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->toAverage(),
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)->toAverage(),
                0,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toAverage(),
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toAverage(),
                2,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toAverage(),
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toAverage(),
                -2,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->toMax(),
                null,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => $item),
                null,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => -$item),
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => $item),
                3,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => -$item),
                -3,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)->toMax(),
                3,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMax(),
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMax(),
                3,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMax(),
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMax(),
                -1,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->toMin(),
                null,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => $item),
                null,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => -$item),
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)->toMin(),
                -3,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => $item),
                -3,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => -$item),
                3,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMin(),
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMin(),
                1,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMin(),
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMin(),
                -3,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->toProduct(),
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)->toProduct(),
                -36,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toProduct(),
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toProduct(),
                6,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toProduct(),
                null,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toProduct(),
                -6,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->toSum(),
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)->toSum(),
                0,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toSum(),
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toSum(),
                6,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toSum(),
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toSum(),
                -6,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }),
                null,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }, 1),
                1,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }),
                0,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }, 1),
                1,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }),
                null,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                1,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item),
                6,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                7,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item),
                null,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                1,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item),
                -6,
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                -5,
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                666,
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipEqualWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                666,
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                666,
            ],
            [
                [1, 2, 3, 4, 5],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                675,
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith(
                        [4, 5, 6],
                        [7, 8, 9]
                    )
                    ->toSum(),
                45,
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith(
                        [4, 5, 6],
                        [7, 8, 9]
                    )
                    ->zipEqualWith([1, 2, 3, 4, 5, 6, 7, 8, 9])
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item)),
                90,
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->toString(),
                '',
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)->toString(', ', 'pre:', '_suf'),
                'pre:_suf',
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)->toString(),
                '1-12-23-3',
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)->toString(', '),
                '1, -1, 2, -2, 3, -3',
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)->toString(', ', 'pre:', '_suf'),
                'pre:1, -1, 2, -2, 3, -3_suf',
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toString(),
                '',
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toString(),
                '123',
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toString(),
                '',
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toString('', 'BEGIN--', '--END'),
                'BEGIN----END',
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toString(),
                '-1-2-3',
            ],
            [
                [1, -1, 2, -2, 3, -3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toString(' ... '),
                '-1 ... -2 ... -3',
            ],
            [
                [],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toString(),
                '',
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toMinMax(),
                [null, null],
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toMinMax(fn ($item) => $item),
                [null, null],
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toMinMax(fn ($item) => -$item),
                [null, null],
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toMinMax(),
                [7, 7],
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toMinMax(fn ($item) => $item),
                [7, 7],
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toMinMax(fn ($item) => -$item),
                [7, 7],
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toMinMax(),
                [-3, 10],
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toMinMax(fn ($item) => $item),
                [-3, 10],
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toMinMax(fn ($item) => -$item),
                [10, -3],
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toRange(),
                0,
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toRange(),
                0,
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toRange(),
                13,
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toFirst(),
                -3,
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toFirst(),
                -3,
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toFirst(),
                7,
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toLast(),
                7,
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toLast(),
                7,
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toLast(),
                -3,
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toFirstAndLast(),
                [-3, 7],
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toFirstAndLast(),
                [-3, 7],
            ],
            [
                [2, 3, 1, 2, -3, -2, 5, 7, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toFirstAndLast(),
                [7, -3],
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toNth(0),
                1,
            ],
            [
                [1, 2, 3],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toNth(2),
                3,
            ],
        ];
    }

    /**
     * @param \Generator $input
     * @param callable   $reducingStreamFactoryFunc
     * @param mixed      $expected
     * @return void
     * @dataProvider dataProviderForGenerator
     */
    public function testGenerator(\Generator $input, callable $reducingStreamFactoryFunc, $expected): void
    {
        // When
        $result = $reducingStreamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGenerator(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->toCount(),
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toCount(),
                6,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toCount(),
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toCount(),
                3,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                2,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toCount(),
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toCount(),
                3,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                2,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->toAverage(),
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toAverage(),
                0,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toAverage(),
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toAverage(),
                2,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toAverage(),
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toAverage(),
                -2,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(),
                null,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => $item),
                null,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => -$item),
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => $item),
                3,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => -$item),
                -3,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(),
                3,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMax(),
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMax(),
                3,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMax(),
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMax(),
                -1,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(),
                null,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => $item),
                null,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => -$item),
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(),
                -3,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => $item),
                -3,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => -$item),
                3,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMin(),
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMin(),
                1,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMin(),
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMin(),
                -3,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->toProduct(),
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toProduct(),
                -36,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toProduct(),
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toProduct(),
                6,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toProduct(),
                null,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toProduct(),
                -6,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)->toSum(),
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toSum(),
                0,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toSum(),
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toSum(),
                6,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toSum(),
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toSum(),
                -6,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }),
                null,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }, 1),
                1,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }),
                0,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }, 1),
                1,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }),
                null,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                1,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item),
                6,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                7,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item),
                null,
            ],
            [
                $gen([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                1,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item),
                -6,
            ],
            [
                $gen([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                -5,
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                666,
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipEqualWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                666,
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                666,
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                675,
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith(
                        [4, 5, 6],
                        [7, 8, 9]
                    )
                    ->toSum(),
                45,
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith(
                        [4, 5, 6],
                        [7, 8, 9]
                    )
                    ->zipEqualWith([1, 2, 3, 4, 5, 6, 7, 8, 9])
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item)),
                90,
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toMinMax(),
                [null, null],
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toMinMax(fn ($item) => $item),
                [null, null],
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toMinMax(fn ($item) => -$item),
                [null, null],
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toMinMax(),
                [7, 7],
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toMinMax(fn ($item) => $item),
                [7, 7],
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toMinMax(fn ($item) => -$item),
                [7, 7],
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toMinMax(),
                [-3, 10],
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toMinMax(fn ($item) => $item),
                [-3, 10],
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toMinMax(fn ($item) => -$item),
                [10, -3],
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toRange(),
                0,
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toRange(),
                0,
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toRange(),
                13,
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toFirst(),
                -3,
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toFirst(),
                -3,
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toFirst(),
                7,
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toLast(),
                7,
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toLast(),
                7,
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toLast(),
                -3,
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toFirstAndLast(),
                [-3, 7],
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toFirstAndLast(),
                [-3, 7],
            ],
            [
                $gen([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toFirstAndLast(),
                [7, -3],
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toNth(0),
                1,
            ],
            [
                $gen([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toNth(2),
                3,
            ],
        ];
    }

    /**
     * @param \Iterator $input
     * @param callable  $reducingStreamFactoryFunc
     * @param mixed     $expected
     * @return void
     * @dataProvider dataProviderForIterator
     */
    public function testIterator(\Iterator $input, callable $reducingStreamFactoryFunc, $expected): void
    {
        // When
        $result = $reducingStreamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIterator(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->toCount(),
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toCount(),
                6,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toCount(),
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toCount(),
                3,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                2,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toCount(),
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toCount(),
                3,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                2,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->toAverage(),
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toAverage(),
                0,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toAverage(),
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toAverage(),
                2,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toAverage(),
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toAverage(),
                -2,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(),
                null,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => $item),
                null,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => -$item),
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => $item),
                3,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => -$item),
                -3,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(),
                3,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMax(),
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMax(),
                3,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMax(),
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMax(),
                -1,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(),
                null,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => $item),
                null,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => -$item),
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(),
                -3,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => $item),
                -3,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => -$item),
                3,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMin(),
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMin(),
                1,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMin(),
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMin(),
                -3,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->toProduct(),
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toProduct(),
                -36,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toProduct(),
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toProduct(),
                6,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toProduct(),
                null,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toProduct(),
                -6,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)->toSum(),
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toSum(),
                0,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toSum(),
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toSum(),
                6,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toSum(),
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toSum(),
                -6,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }),
                null,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }, 1),
                1,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }),
                0,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }, 1),
                1,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }),
                null,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                1,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item),
                6,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                7,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item),
                null,
            ],
            [
                $iter([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                1,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item),
                -6,
            ],
            [
                $iter([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                -5,
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                666,
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipEqualWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                666,
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                666,
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                675,
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith(
                        [4, 5, 6],
                        [7, 8, 9]
                    )
                    ->toSum(),
                45,
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith(
                        [4, 5, 6],
                        [7, 8, 9]
                    )
                    ->zipEqualWith([1, 2, 3, 4, 5, 6, 7, 8, 9])
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item)),
                90,
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toMinMax(),
                [null, null],
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toMinMax(fn ($item) => $item),
                [null, null],
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toMinMax(fn ($item) => -$item),
                [null, null],
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toMinMax(),
                [7, 7],
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toMinMax(fn ($item) => $item),
                [7, 7],
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toMinMax(fn ($item) => -$item),
                [7, 7],
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toMinMax(),
                [-3, 10],
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toMinMax(fn ($item) => $item),
                [-3, 10],
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toMinMax(fn ($item) => -$item),
                [10, -3],
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toRange(),
                0,
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toRange(),
                0,
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toRange(),
                13,
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toFirst(),
                -3,
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toFirst(),
                -3,
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toFirst(),
                7,
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toLast(),
                7,
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toLast(),
                7,
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toLast(),
                -3,
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toFirstAndLast(),
                [-3, 7],
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toFirstAndLast(),
                [-3, 7],
            ],
            [
                $iter([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toFirstAndLast(),
                [7, -3],
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toNth(0),
                1,
            ],
            [
                $iter([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toNth(2),
                3,
            ],
        ];
    }

    /**
     * @param \Traversable $input
     * @param callable     $reducingStreamFactoryFunc
     * @param mixed        $expected
     * @return void
     * @dataProvider dataProviderForTraversable
     */
    public function testTraversable(\Traversable $input, callable $reducingStreamFactoryFunc, $expected): void
    {
        // When
        $result = $reducingStreamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversable(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->toCount(),
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toCount(),
                6,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toCount(),
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toCount(),
                3,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                2,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toCount(),
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toCount(),
                3,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->pairwise()
                    ->toCount(),
                2,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->toAverage(),
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toAverage(),
                0,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toAverage(),
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toAverage(),
                2,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toAverage(),
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toAverage(),
                -2,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(),
                null,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => $item),
                null,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => -$item),
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => $item),
                3,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(fn ($item) => -$item),
                -3,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMax(),
                3,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMax(),
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMax(),
                3,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMax(),
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMax(),
                -1,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(),
                null,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => $item),
                null,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => -$item),
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(),
                -3,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => $item),
                -3,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toMin(fn ($item) => -$item),
                3,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMin(),
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toMin(),
                1,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMin(),
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toMin(),
                -3,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->toProduct(),
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toProduct(),
                -36,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toProduct(),
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toProduct(),
                6,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toProduct(),
                null,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toProduct(),
                -6,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)->toSum(),
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)->toSum(),
                0,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toSum(),
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toSum(),
                6,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toSum(),
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toSum(),
                -6,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }),
                null,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }, 1),
                1,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }),
                0,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }, 1),
                1,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(function ($carry, $item) {
                        return $carry + $item;
                    }),
                null,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                1,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item),
                6,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                7,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item),
                null,
            ],
            [
                $trav([]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                1,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item),
                -6,
            ],
            [
                $trav([1, -1, 2, -2, 3, -3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterFalse(fn ($value) => $value > 0)
                    ->toValue(fn ($carry, $item) => $carry + $item, 1),
                -5,
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                666,
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipEqualWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                666,
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                666,
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->zipLongestWith(
                        [10, 20, 30],
                        [100, 200, 300]
                    )
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item), 0),
                675,
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith(
                        [4, 5, 6],
                        [7, 8, 9]
                    )
                    ->toSum(),
                45,
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith(
                        [4, 5, 6],
                        [7, 8, 9]
                    )
                    ->zipEqualWith([1, 2, 3, 4, 5, 6, 7, 8, 9])
                    ->toValue(fn ($carry, $item) => $carry + array_sum($item)),
                90,
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toMinMax(),
                [null, null],
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toMinMax(fn ($item) => $item),
                [null, null],
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toMinMax(fn ($item) => -$item),
                [null, null],
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toMinMax(),
                [7, 7],
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toMinMax(fn ($item) => $item),
                [7, 7],
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toMinMax(fn ($item) => -$item),
                [7, 7],
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toMinMax(),
                [-3, 10],
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toMinMax(fn ($item) => $item),
                [-3, 10],
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toMinMax(fn ($item) => -$item),
                [10, -3],
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn () => false)
                    ->toRange(),
                0,
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->filterTrue(fn ($number) => $number > 6)
                    ->toRange(),
                0,
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->chainWith([6, 10, 4])
                    ->toRange(),
                13,
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toFirst(),
                -3,
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toFirst(),
                -3,
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toFirst(),
                7,
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toLast(),
                7,
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toLast(),
                7,
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toLast(),
                -3,
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort()
                    ->toFirstAndLast(),
                [-3, 7],
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $lhs <=> $rhs)
                    ->toFirstAndLast(),
                [-3, 7],
            ],
            [
                $trav([2, 3, 1, 2, -3, -2, 5, 7, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
                    ->toFirstAndLast(),
                [7, -3],
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toNth(0),
                1,
            ],
            [
                $trav([1, 2, 3]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->toNth(2),
                3,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForToRandomValue
     * @param iterable $data
     * @param array $expected
     * @return void
     */
    public function testToRandomValue(iterable $data, array $expected): void
    {
        // Given
        $result = Stream::of($data)->toRandomValue();

        // Then
        $this->assertContains($result, $expected);
    }

    public function dataProviderForToRandomValue(): array
    {
        $gen = fn ($data) => GeneratorFixture::getKeyValueGenerator($data);
        $iter = fn ($data) => new \ArrayIterator($data);
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                [1, 2, 3],
                [1, 2, 3],
            ],
            [
                $gen([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                $iter([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                $trav([1, 2, 3]),
                [1, 2, 3],
            ],
            [
                ['a' => 'aa', 'b' => 'bb', 'c' => 'cc'],
                ['aa', 'bb', 'cc'],
            ],
            [
                $gen(['a' => 'aa', 'b' => 'bb', 'c' => 'cc']),
                ['aa', 'bb', 'cc'],
            ],
            [
                $iter(['a' => 'aa', 'b' => 'bb', 'c' => 'cc']),
                ['aa', 'bb', 'cc'],
            ],
            [
                $trav(['a' => 'aa', 'b' => 'bb', 'c' => 'cc']),
                ['aa', 'bb', 'cc'],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForEmptyIterable
     * @param iterable $data
     * @return void
     */
    public function testToRandomValueErrorOnEmpty(iterable $data): void
    {
        // Then
        $this->expectException(\LengthException::class);
        $this->expectExceptionMessage('Given iterable must be non-empty');

        // When
        Stream::of($data)->toRandomValue();
    }
}

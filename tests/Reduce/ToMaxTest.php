<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToMaxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         toMax array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        callable|null $comparator
     * @param        int|float $expected
     */
    public function testArray(array $data, ?callable $comparator, $expected): void
    {
        // When
        $result = Reduce::toMax($data, $comparator);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                null,
                null,
            ],
            [
                [],
                fn ($item) => $item,
                null,
            ],
            [
                [],
                fn ($item) => -$item,
                null,
            ],
            [
                [0],
                null,
                0,
            ],
            [
                [0],
                fn ($item) => $item,
                0,
            ],
            [
                [0],
                fn ($item) => -$item,
                0,
            ],
            [
                [INF],
                null,
                INF,
            ],
            [
                [INF],
                fn ($item) => $item,
                INF,
            ],
            [
                [INF],
                fn ($item) => -$item,
                INF,
            ],
            [
                [-INF],
                null,
                -INF,
            ],
            [
                [-INF],
                fn ($item) => $item,
                -INF,
            ],
            [
                [-INF],
                fn ($item) => -$item,
                -INF,
            ],
            [
                [INF, -INF],
                null,
                INF,
            ],
            [
                [INF, -INF],
                fn ($item) => $item,
                INF,
            ],
            [
                [INF, -INF],
                fn ($item) => -$item,
                -INF,
            ],
            [
                [INF, -INF, 10, -1],
                null,
                INF,
            ],
            [
                [INF, -INF, 10, -1],
                fn ($item) => $item,
                INF,
            ],
            [
                [INF, -INF, 10, -1],
                fn ($item) => -$item,
                -INF,
            ],
            [
                [1, 2, 3],
                null,
                3,
            ],
            [
                [1, 2, 3],
                fn ($item) => $item,
                3,
            ],
            [
                [1, 2, 3],
                fn ($item) => -$item,
                1,
            ],
            [
                [3, 2, 1],
                null,
                3,
            ],
            [
                [3, 2, 1],
                fn ($item) => $item,
                3,
            ],
            [
                [3, 2, 1],
                fn ($item) => -$item,
                1,
            ],
            [
                [2, 3, 1],
                null,
                3,
            ],
            [
                [2, 3, 1],
                fn ($item) => $item,
                3,
            ],
            [
                [2, 3, 1],
                fn ($item) => -$item,
                1,
            ],
            [
                [1, 2.1],
                null,
                2.1,
            ],
            [
                [1, 2.1],
                fn ($item) => $item,
                2.1,
            ],
            [
                [1, 2.1],
                fn ($item) => -$item,
                1,
            ],
            [
                [2.1, 1],
                null,
                2.1,
            ],
            [
                [2.1, 1],
                fn ($item) => $item,
                2.1,
            ],
            [
                [2.1, 1],
                fn ($item) => -$item,
                1,
            ],
            [
                [2, 1.1],
                null,
                2,
            ],
            [
                [2, 1.1],
                fn ($item) => $item,
                2,
            ],
            [
                [2, 1.1],
                fn ($item) => -$item,
                1.1,
            ],
            [
                [2.2, 1.1],
                null,
                2.2,
            ],
            [
                [2.2, 1.1],
                fn ($item) => $item,
                2.2,
            ],
            [
                [2.2, 1.1],
                fn ($item) => -$item,
                1.1,
            ],
            [
                [1.1, 2.2],
                null,
                2.2,
            ],
            [
                [1.1, 2.2],
                fn ($item) => $item,
                2.2,
            ],
            [
                [1.1, 2.2],
                fn ($item) => -$item,
                1.1,
            ],
            [
                ['a', 'b', 'c'],
                null,
                'c',
            ],
            [
                ['a', 'b', 'c'],
                fn ($item) => $item,
                'c',
            ],
            [
                ['a', 'b', 'c'],
                fn ($item) => -ord($item),
                'a',
            ],
            [
                ['b', 'c', 'a'],
                null,
                'c',
            ],
            [
                ['b', 'c', 'a'],
                fn ($item) => $item,
                'c',
            ],
            [
                ['b', 'c', 'a'],
                fn ($item) => -ord($item),
                'a',
            ],
            [
                ['c', 'b', 'a'],
                null,
                'c',
            ],
            [
                ['c', 'b', 'a'],
                fn ($item) => $item,
                'c',
            ],
            [
                ['c', 'b', 'a'],
                fn ($item) => -ord($item),
                'a',
            ],
            [
                ['ab', 'ba', 'b'],
                null,
                'ba',
            ],
            [
                ['ab', 'ba', 'b'],
                fn ($item) => $item,
                'ba',
            ],
            [
                ['ba', 'b', 'ab'],
                null,
                'ba',
            ],
            [
                ['ba', 'b', 'ab'],
                fn ($item) => $item,
                'ba',
            ],
            [
                [[]],
                null,
                [],
            ],
            [
                [[]],
                fn ($item) => $item,
                [],
            ],
            [
                [[2]],
                null,
                [2],
            ],
            [
                [[2]],
                fn ($item) => $item,
                [2],
            ],
            [
                [[], []],
                null,
                [],
            ],
            [
                [[], []],
                fn ($item) => $item,
                [],
            ],
            [
                [[], [2]],
                null,
                [2],
            ],
            [
                [[], [2]],
                fn ($item) => $item,
                [2],
            ],
            [
                [[2], []],
                null,
                [2],
            ],
            [
                [[2], []],
                fn ($item) => $item,
                [2],
            ],
            [
                [[], [null]],
                null,
                [null],
            ],
            [
                [[], [null]],
                fn ($item) => $item,
                [null],
            ],
            [
                [[null], []],
                null,
                [null],
            ],
            [
                [[null], []],
                fn ($item) => $item,
                [null],
            ],
            [
                [[null], [null]],
                null,
                [null],
            ],
            [
                [[null], [null]],
                fn ($item) => $item,
                [null],
            ],
            [
                [[1, 2], [2]],
                null,
                [1, 2],
            ],
            [
                [[1, 2], [2]],
                fn ($item) => $item,
                [1, 2],
            ],
            [
                [[3, 2], [2]],
                null,
                [3, 2],
            ],
            [
                [[3, 2], [2]],
                fn ($item) => $item,
                [3, 2],
            ],
            [
                [[1, 2], [2, 1]],
                null,
                [2, 1],
            ],
            [
                [[1, 2], [2, 1]],
                fn ($item) => $item,
                [2, 1],
            ],
            [
                [[2, 1], [1, 2]],
                null,
                [2, 1],
            ],
            [
                [[2, 1], [1, 2]],
                fn ($item) => $item,
                [2, 1],
            ],
            [
                [['a'], ['b']],
                null,
                ['b'],
            ],
            [
                [['a'], ['b']],
                fn ($item) => $item,
                ['b'],
            ],
            [
                [['a', 'a'], ['b']],
                null,
                ['a', 'a'],
            ],
            [
                [['a', 'a'], ['b']],
                fn ($item) => $item,
                ['a', 'a'],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                null,
                [2, 1, 3],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($item) => $item,
                [2, 1, 3],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($item) => $item[1],
                [1, 2, 3],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($item) => -$item[1],
                [2, 0, 3],
            ],
        ];
    }

    /**
     * @test         toMax generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        callable|null $comparator
     * @param        mixed $expected
     */
    public function testGenerators(\Generator $data, ?callable $comparator, $expected): void
    {
        // When
        $result = Reduce::toMax($data, $comparator);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([]),
                null,
                null,
            ],
            [
                $gen([]),
                fn ($item) => $item,
                null,
            ],
            [
                $gen([]),
                fn ($item) => -$item,
                null,
            ],
            [
                $gen([0]),
                null,
                0,
            ],
            [
                $gen([0]),
                fn ($item) => $item,
                0,
            ],
            [
                $gen([0]),
                fn ($item) => -$item,
                0,
            ],
            [
                $gen([INF]),
                null,
                INF,
            ],
            [
                $gen([INF]),
                fn ($item) => $item,
                INF,
            ],
            [
                $gen([INF]),
                fn ($item) => -$item,
                INF,
            ],
            [
                $gen([-INF]),
                null,
                -INF,
            ],
            [
                $gen([-INF]),
                fn ($item) => $item,
                -INF,
            ],
            [
                $gen([-INF]),
                fn ($item) => -$item,
                -INF,
            ],
            [
                $gen([INF, -INF]),
                null,
                INF,
            ],
            [
                $gen([INF, -INF]),
                fn ($item) => $item,
                INF,
            ],
            [
                $gen([INF, -INF]),
                fn ($item) => -$item,
                -INF,
            ],
            [
                $gen([INF, -INF, 10, -1]),
                null,
                INF,
            ],
            [
                $gen([INF, -INF, 10, -1]),
                fn ($item) => $item,
                INF,
            ],
            [
                $gen([INF, -INF, 10, -1]),
                fn ($item) => -$item,
                -INF,
            ],
            [
                $gen([1, 2, 3]),
                null,
                3,
            ],
            [
                $gen([1, 2, 3]),
                fn ($item) => $item,
                3,
            ],
            [
                $gen([1, 2, 3]),
                fn ($item) => -$item,
                1,
            ],
            [
                $gen([3, 2, 1]),
                null,
                3,
            ],
            [
                $gen([3, 2, 1]),
                fn ($item) => $item,
                3,
            ],
            [
                $gen([3, 2, 1]),
                fn ($item) => -$item,
                1,
            ],
            [
                $gen([2, 3, 1]),
                null,
                3,
            ],
            [
                $gen([2, 3, 1]),
                fn ($item) => $item,
                3,
            ],
            [
                $gen([2, 3, 1]),
                fn ($item) => -$item,
                1,
            ],
            [
                $gen([1, 2.1]),
                null,
                2.1,
            ],
            [
                $gen([1, 2.1]),
                fn ($item) => $item,
                2.1,
            ],
            [
                $gen([1, 2.1]),
                fn ($item) => -$item,
                1,
            ],
            [
                $gen([2.1, 1]),
                null,
                2.1,
            ],
            [
                $gen([2.1, 1]),
                fn ($item) => $item,
                2.1,
            ],
            [
                $gen([2.1, 1]),
                fn ($item) => -$item,
                1,
            ],
            [
                $gen([2, 1.1]),
                null,
                2,
            ],
            [
                $gen([2, 1.1]),
                fn ($item) => $item,
                2,
            ],
            [
                $gen([2, 1.1]),
                fn ($item) => -$item,
                1.1,
            ],
            [
                $gen([2.2, 1.1]),
                null,
                2.2,
            ],
            [
                $gen([2.2, 1.1]),
                fn ($item) => $item,
                2.2,
            ],
            [
                $gen([2.2, 1.1]),
                fn ($item) => -$item,
                1.1,
            ],
            [
                $gen([1.1, 2.2]),
                null,
                2.2,
            ],
            [
                $gen([1.1, 2.2]),
                fn ($item) => $item,
                2.2,
            ],
            [
                $gen([1.1, 2.2]),
                fn ($item) => -$item,
                1.1,
            ],
            [
                $gen(['a', 'b', 'c']),
                null,
                'c',
            ],
            [
                $gen(['a', 'b', 'c']),
                fn ($item) => $item,
                'c',
            ],
            [
                $gen(['a', 'b', 'c']),
                fn ($item) => -ord($item),
                'a',
            ],
            [
                $gen(['b', 'c', 'a']),
                null,
                'c',
            ],
            [
                $gen(['b', 'c', 'a']),
                fn ($item) => $item,
                'c',
            ],
            [
                $gen(['b', 'c', 'a']),
                fn ($item) => -ord($item),
                'a',
            ],
            [
                $gen(['c', 'b', 'a']),
                null,
                'c',
            ],
            [
                $gen(['c', 'b', 'a']),
                fn ($item) => $item,
                'c',
            ],
            [
                $gen(['c', 'b', 'a']),
                fn ($item) => -ord($item),
                'a',
            ],
            [
                $gen(['ab', 'ba', 'b']),
                null,
                'ba',
            ],
            [
                $gen(['ab', 'ba', 'b']),
                fn ($item) => $item,
                'ba',
            ],
            [
                $gen(['ba', 'b', 'ab']),
                null,
                'ba',
            ],
            [
                $gen(['ba', 'b', 'ab']),
                fn ($item) => $item,
                'ba',
            ],
            [
                $gen([[]]),
                null,
                [],
            ],
            [
                $gen([[]]),
                fn ($item) => $item,
                [],
            ],
            [
                $gen([[2]]),
                null,
                [2],
            ],
            [
                $gen([[2]]),
                fn ($item) => $item,
                [2],
            ],
            [
                $gen([[], []]),
                null,
                [],
            ],
            [
                $gen([[], []]),
                fn ($item) => $item,
                [],
            ],
            [
                $gen([[], [2]]),
                null,
                [2],
            ],
            [
                $gen([[], [2]]),
                fn ($item) => $item,
                [2],
            ],
            [
                $gen([[2], []]),
                null,
                [2],
            ],
            [
                $gen([[2], []]),
                fn ($item) => $item,
                [2],
            ],
            [
                $gen([[], [null]]),
                null,
                [null],
            ],
            [
                $gen([[], [null]]),
                fn ($item) => $item,
                [null],
            ],
            [
                $gen([[null], []]),
                null,
                [null],
            ],
            [
                $gen([[null], []]),
                fn ($item) => $item,
                [null],
            ],
            [
                $gen([[null], [null]]),
                null,
                [null],
            ],
            [
                $gen([[null], [null]]),
                fn ($item) => $item,
                [null],
            ],
            [
                $gen([[1, 2], [2]]),
                null,
                [1, 2],
            ],
            [
                $gen([[1, 2], [2]]),
                fn ($item) => $item,
                [1, 2],
            ],
            [
                $gen([[3, 2], [2]]),
                null,
                [3, 2],
            ],
            [
                $gen([[3, 2], [2]]),
                fn ($item) => $item,
                [3, 2],
            ],
            [
                $gen([[1, 2], [2, 1]]),
                null,
                [2, 1],
            ],
            [
                $gen([[1, 2], [2, 1]]),
                fn ($item) => $item,
                [2, 1],
            ],
            [
                $gen([[2, 1], [1, 2]]),
                null,
                [2, 1],
            ],
            [
                $gen([[2, 1], [1, 2]]),
                fn ($item) => $item,
                [2, 1],
            ],
            [
                $gen([['a'], ['b']]),
                null,
                ['b'],
            ],
            [
                $gen([['a'], ['b']]),
                fn ($item) => $item,
                ['b'],
            ],
            [
                $gen([['a', 'a'], ['b']]),
                null,
                ['a', 'a'],
            ],
            [
                $gen([['a', 'a'], ['b']]),
                fn ($item) => $item,
                ['a', 'a'],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                null,
                [2, 1, 3],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item,
                [2, 1, 3],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item[1],
                [1, 2, 3],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => -$item[1],
                [2, 0, 3],
            ],
        ];
    }

    /**
     * @test         toMax iterators
     * @dataProvider dataProviderForIterators
     * @param        \Generator $data
     * @param        callable|null $comparator
     * @param        mixed $expected
     */
    public function testIterators(\Iterator $data, ?callable $comparator, $expected): void
    {
        // When
        $result = Reduce::toMax($data, $comparator);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $iter([]),
                null,
                null,
            ],
            [
                $iter([]),
                fn ($item) => $item,
                null,
            ],
            [
                $iter([]),
                fn ($item) => -$item,
                null,
            ],
            [
                $iter([0]),
                null,
                0,
            ],
            [
                $iter([0]),
                fn ($item) => $item,
                0,
            ],
            [
                $iter([0]),
                fn ($item) => -$item,
                0,
            ],
            [
                $iter([INF]),
                null,
                INF,
            ],
            [
                $iter([INF]),
                fn ($item) => $item,
                INF,
            ],
            [
                $iter([INF]),
                fn ($item) => -$item,
                INF,
            ],
            [
                $iter([-INF]),
                null,
                -INF,
            ],
            [
                $iter([-INF]),
                fn ($item) => $item,
                -INF,
            ],
            [
                $iter([-INF]),
                fn ($item) => -$item,
                -INF,
            ],
            [
                $iter([INF, -INF]),
                null,
                INF,
            ],
            [
                $iter([INF, -INF]),
                fn ($item) => $item,
                INF,
            ],
            [
                $iter([INF, -INF]),
                fn ($item) => -$item,
                -INF,
            ],
            [
                $iter([INF, -INF, 10, -1]),
                null,
                INF,
            ],
            [
                $iter([INF, -INF, 10, -1]),
                fn ($item) => $item,
                INF,
            ],
            [
                $iter([INF, -INF, 10, -1]),
                fn ($item) => -$item,
                -INF,
            ],
            [
                $iter([1, 2, 3]),
                null,
                3,
            ],
            [
                $iter([1, 2, 3]),
                fn ($item) => $item,
                3,
            ],
            [
                $iter([1, 2, 3]),
                fn ($item) => -$item,
                1,
            ],
            [
                $iter([3, 2, 1]),
                null,
                3,
            ],
            [
                $iter([3, 2, 1]),
                fn ($item) => $item,
                3,
            ],
            [
                $iter([3, 2, 1]),
                fn ($item) => -$item,
                1,
            ],
            [
                $iter([2, 3, 1]),
                null,
                3,
            ],
            [
                $iter([2, 3, 1]),
                fn ($item) => $item,
                3,
            ],
            [
                $iter([2, 3, 1]),
                fn ($item) => -$item,
                1,
            ],
            [
                $iter([1, 2.1]),
                null,
                2.1,
            ],
            [
                $iter([1, 2.1]),
                fn ($item) => $item,
                2.1,
            ],
            [
                $iter([1, 2.1]),
                fn ($item) => -$item,
                1,
            ],
            [
                $iter([2.1, 1]),
                null,
                2.1,
            ],
            [
                $iter([2.1, 1]),
                fn ($item) => $item,
                2.1,
            ],
            [
                $iter([2.1, 1]),
                fn ($item) => -$item,
                1,
            ],
            [
                $iter([2, 1.1]),
                null,
                2,
            ],
            [
                $iter([2, 1.1]),
                fn ($item) => $item,
                2,
            ],
            [
                $iter([2, 1.1]),
                fn ($item) => -$item,
                1.1,
            ],
            [
                $iter([2.2, 1.1]),
                null,
                2.2,
            ],
            [
                $iter([2.2, 1.1]),
                fn ($item) => $item,
                2.2,
            ],
            [
                $iter([2.2, 1.1]),
                fn ($item) => -$item,
                1.1,
            ],
            [
                $iter([1.1, 2.2]),
                null,
                2.2,
            ],
            [
                $iter([1.1, 2.2]),
                fn ($item) => $item,
                2.2,
            ],
            [
                $iter([1.1, 2.2]),
                fn ($item) => -$item,
                1.1,
            ],
            [
                $iter(['a', 'b', 'c']),
                null,
                'c',
            ],
            [
                $iter(['a', 'b', 'c']),
                fn ($item) => $item,
                'c',
            ],
            [
                $iter(['a', 'b', 'c']),
                fn ($item) => -ord($item),
                'a',
            ],
            [
                $iter(['b', 'c', 'a']),
                null,
                'c',
            ],
            [
                $iter(['b', 'c', 'a']),
                fn ($item) => $item,
                'c',
            ],
            [
                $iter(['b', 'c', 'a']),
                fn ($item) => -ord($item),
                'a',
            ],
            [
                $iter(['c', 'b', 'a']),
                null,
                'c',
            ],
            [
                $iter(['c', 'b', 'a']),
                fn ($item) => $item,
                'c',
            ],
            [
                $iter(['c', 'b', 'a']),
                fn ($item) => -ord($item),
                'a',
            ],
            [
                $iter(['ab', 'ba', 'b']),
                null,
                'ba',
            ],
            [
                $iter(['ab', 'ba', 'b']),
                fn ($item) => $item,
                'ba',
            ],
            [
                $iter(['ba', 'b', 'ab']),
                null,
                'ba',
            ],
            [
                $iter(['ba', 'b', 'ab']),
                fn ($item) => $item,
                'ba',
            ],
            [
                $iter([[]]),
                null,
                [],
            ],
            [
                $iter([[]]),
                fn ($item) => $item,
                [],
            ],
            [
                $iter([[2]]),
                null,
                [2],
            ],
            [
                $iter([[2]]),
                fn ($item) => $item,
                [2],
            ],
            [
                $iter([[], []]),
                null,
                [],
            ],
            [
                $iter([[], []]),
                fn ($item) => $item,
                [],
            ],
            [
                $iter([[], [2]]),
                null,
                [2],
            ],
            [
                $iter([[], [2]]),
                fn ($item) => $item,
                [2],
            ],
            [
                $iter([[2], []]),
                null,
                [2],
            ],
            [
                $iter([[2], []]),
                fn ($item) => $item,
                [2],
            ],
            [
                $iter([[], [null]]),
                null,
                [null],
            ],
            [
                $iter([[], [null]]),
                fn ($item) => $item,
                [null],
            ],
            [
                $iter([[null], []]),
                null,
                [null],
            ],
            [
                $iter([[null], []]),
                fn ($item) => $item,
                [null],
            ],
            [
                $iter([[null], [null]]),
                null,
                [null],
            ],
            [
                $iter([[null], [null]]),
                fn ($item) => $item,
                [null],
            ],
            [
                $iter([[1, 2], [2]]),
                null,
                [1, 2],
            ],
            [
                $iter([[1, 2], [2]]),
                fn ($item) => $item,
                [1, 2],
            ],
            [
                $iter([[3, 2], [2]]),
                null,
                [3, 2],
            ],
            [
                $iter([[3, 2], [2]]),
                fn ($item) => $item,
                [3, 2],
            ],
            [
                $iter([[1, 2], [2, 1]]),
                null,
                [2, 1],
            ],
            [
                $iter([[1, 2], [2, 1]]),
                fn ($item) => $item,
                [2, 1],
            ],
            [
                $iter([[2, 1], [1, 2]]),
                null,
                [2, 1],
            ],
            [
                $iter([[2, 1], [1, 2]]),
                fn ($item) => $item,
                [2, 1],
            ],
            [
                $iter([['a'], ['b']]),
                null,
                ['b'],
            ],
            [
                $iter([['a'], ['b']]),
                fn ($item) => $item,
                ['b'],
            ],
            [
                $iter([['a', 'a'], ['b']]),
                null,
                ['a', 'a'],
            ],
            [
                $iter([['a', 'a'], ['b']]),
                fn ($item) => $item,
                ['a', 'a'],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                null,
                [2, 1, 3],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item,
                [2, 1, 3],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item[1],
                [1, 2, 3],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => -$item[1],
                [2, 0, 3],
            ],
        ];
    }

    /**
     * @test         toMax traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        callable|null $comparator
     * @param        mixed $expected
     */
    public function testTraversables(\Traversable $data, ?callable $comparator, $expected): void
    {
        // When
        $result = Reduce::toMax($data, $comparator);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([]),
                null,
                null,
            ],
            [
                $trav([]),
                fn ($item) => $item,
                null,
            ],
            [
                $trav([]),
                fn ($item) => -$item,
                null,
            ],
            [
                $trav([0]),
                null,
                0,
            ],
            [
                $trav([0]),
                fn ($item) => $item,
                0,
            ],
            [
                $trav([0]),
                fn ($item) => -$item,
                0,
            ],
            [
                $trav([INF]),
                null,
                INF,
            ],
            [
                $trav([INF]),
                fn ($item) => $item,
                INF,
            ],
            [
                $trav([INF]),
                fn ($item) => -$item,
                INF,
            ],
            [
                $trav([-INF]),
                null,
                -INF,
            ],
            [
                $trav([-INF]),
                fn ($item) => $item,
                -INF,
            ],
            [
                $trav([-INF]),
                fn ($item) => -$item,
                -INF,
            ],
            [
                $trav([INF, -INF]),
                null,
                INF,
            ],
            [
                $trav([INF, -INF]),
                fn ($item) => $item,
                INF,
            ],
            [
                $trav([INF, -INF]),
                fn ($item) => -$item,
                -INF,
            ],
            [
                $trav([INF, -INF, 10, -1]),
                null,
                INF,
            ],
            [
                $trav([INF, -INF, 10, -1]),
                fn ($item) => $item,
                INF,
            ],
            [
                $trav([INF, -INF, 10, -1]),
                fn ($item) => -$item,
                -INF,
            ],
            [
                $trav([1, 2, 3]),
                null,
                3,
            ],
            [
                $trav([1, 2, 3]),
                fn ($item) => $item,
                3,
            ],
            [
                $trav([1, 2, 3]),
                fn ($item) => -$item,
                1,
            ],
            [
                $trav([3, 2, 1]),
                null,
                3,
            ],
            [
                $trav([3, 2, 1]),
                fn ($item) => $item,
                3,
            ],
            [
                $trav([3, 2, 1]),
                fn ($item) => -$item,
                1,
            ],
            [
                $trav([2, 3, 1]),
                null,
                3,
            ],
            [
                $trav([2, 3, 1]),
                fn ($item) => $item,
                3,
            ],
            [
                $trav([2, 3, 1]),
                fn ($item) => -$item,
                1,
            ],
            [
                $trav([1, 2.1]),
                null,
                2.1,
            ],
            [
                $trav([1, 2.1]),
                fn ($item) => $item,
                2.1,
            ],
            [
                $trav([1, 2.1]),
                fn ($item) => -$item,
                1,
            ],
            [
                $trav([2.1, 1]),
                null,
                2.1,
            ],
            [
                $trav([2.1, 1]),
                fn ($item) => $item,
                2.1,
            ],
            [
                $trav([2.1, 1]),
                fn ($item) => -$item,
                1,
            ],
            [
                $trav([2, 1.1]),
                null,
                2,
            ],
            [
                $trav([2, 1.1]),
                fn ($item) => $item,
                2,
            ],
            [
                $trav([2, 1.1]),
                fn ($item) => -$item,
                1.1,
            ],
            [
                $trav([2.2, 1.1]),
                null,
                2.2,
            ],
            [
                $trav([2.2, 1.1]),
                fn ($item) => $item,
                2.2,
            ],
            [
                $trav([2.2, 1.1]),
                fn ($item) => -$item,
                1.1,
            ],
            [
                $trav([1.1, 2.2]),
                null,
                2.2,
            ],
            [
                $trav([1.1, 2.2]),
                fn ($item) => $item,
                2.2,
            ],
            [
                $trav([1.1, 2.2]),
                fn ($item) => -$item,
                1.1,
            ],
            [
                $trav(['a', 'b', 'c']),
                null,
                'c',
            ],
            [
                $trav(['a', 'b', 'c']),
                fn ($item) => $item,
                'c',
            ],
            [
                $trav(['a', 'b', 'c']),
                fn ($item) => -ord($item),
                'a',
            ],
            [
                $trav(['b', 'c', 'a']),
                null,
                'c',
            ],
            [
                $trav(['b', 'c', 'a']),
                fn ($item) => $item,
                'c',
            ],
            [
                $trav(['b', 'c', 'a']),
                fn ($item) => -ord($item),
                'a',
            ],
            [
                $trav(['c', 'b', 'a']),
                null,
                'c',
            ],
            [
                $trav(['c', 'b', 'a']),
                fn ($item) => $item,
                'c',
            ],
            [
                $trav(['c', 'b', 'a']),
                fn ($item) => -ord($item),
                'a',
            ],
            [
                $trav(['ab', 'ba', 'b']),
                null,
                'ba',
            ],
            [
                $trav(['ab', 'ba', 'b']),
                fn ($item) => -ord($item),
                'ab',
            ],
            [
                $trav(['ba', 'b', 'ab']),
                null,
                'ba',
            ],
            [
                $trav(['ba', 'b', 'ab']),
                fn ($item) => $item,
                'ba',
            ],
            [
                $trav([[]]),
                null,
                [],
            ],
            [
                $trav([[]]),
                fn ($item) => $item,
                [],
            ],
            [
                $trav([[2]]),
                null,
                [2],
            ],
            [
                $trav([[2]]),
                fn ($item) => $item,
                [2],
            ],
            [
                $trav([[], []]),
                null,
                [],
            ],
            [
                $trav([[], []]),
                fn ($item) => $item,
                [],
            ],
            [
                $trav([[], [2]]),
                null,
                [2],
            ],
            [
                $trav([[], [2]]),
                fn ($item) => $item,
                [2],
            ],
            [
                $trav([[2], []]),
                null,
                [2],
            ],
            [
                $trav([[2], []]),
                fn ($item) => $item,
                [2],
            ],
            [
                $trav([[], [null]]),
                null,
                [null],
            ],
            [
                $trav([[], [null]]),
                fn ($item) => $item,
                [null],
            ],
            [
                $trav([[null], []]),
                null,
                [null],
            ],
            [
                $trav([[null], []]),
                fn ($item) => $item,
                [null],
            ],
            [
                $trav([[null], [null]]),
                null,
                [null],
            ],
            [
                $trav([[null], [null]]),
                fn ($item) => $item,
                [null],
            ],
            [
                $trav([[1, 2], [2]]),
                null,
                [1, 2],
            ],
            [
                $trav([[1, 2], [2]]),
                fn ($item) => $item,
                [1, 2],
            ],
            [
                $trav([[3, 2], [2]]),
                null,
                [3, 2],
            ],
            [
                $trav([[3, 2], [2]]),
                fn ($item) => $item,
                [3, 2],
            ],
            [
                $trav([[1, 2], [2, 1]]),
                null,
                [2, 1],
            ],
            [
                $trav([[1, 2], [2, 1]]),
                fn ($item) => $item,
                [2, 1],
            ],
            [
                $trav([[2, 1], [1, 2]]),
                null,
                [2, 1],
            ],
            [
                $trav([[2, 1], [1, 2]]),
                fn ($item) => $item,
                [2, 1],
            ],
            [
                $trav([['a'], ['b']]),
                null,
                ['b'],
            ],
            [
                $trav([['a'], ['b']]),
                fn ($item) => $item,
                ['b'],
            ],
            [
                $trav([['a', 'a'], ['b']]),
                null,
                ['a', 'a'],
            ],
            [
                $trav([['a', 'a'], ['b']]),
                fn ($item) => $item,
                ['a', 'a'],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                null,
                [2, 1, 3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item,
                [2, 1, 3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item[1],
                [1, 2, 3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => -$item[1],
                [2, 0, 3],
            ],
        ];
    }
}

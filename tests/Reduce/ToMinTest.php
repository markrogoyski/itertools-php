<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToMinTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         toMin array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        callable|null $comparator
     * @param        int|float $expected
     */
    public function testArray(array $data, ?callable $comparator, $expected)
    {
        // When
        $result = Reduce::toMin($data, $comparator);

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
                -INF,
            ],
            [
                [INF, -INF],
                fn ($item) => $item,
                -INF,
            ],
            [
                [INF, -INF],
                fn ($item) => -$item,
                INF,
            ],
            [
                [INF, -INF, 10, -1],
                null,
                -INF,
            ],
            [
                [INF, -INF, 10, -1],
                fn ($item) => $item,
                -INF,
            ],
            [
                [INF, -INF, 10, -1],
                fn ($item) => -$item,
                INF,
            ],
            [
                [1, 2, 3],
                null,
                1,
            ],
            [
                [1, 2, 3],
                fn ($item) => $item,
                1,
            ],
            [
                [1, 2, 3],
                fn ($item) => -$item,
                3,
            ],
            [
                [3, 2, 1],
                null,
                1,
            ],
            [
                [3, 2, 1],
                fn ($item) => $item,
                1,
            ],
            [
                [3, 2, 1],
                fn ($item) => -$item,
                3,
            ],
            [
                [3, 2, 1],
                null,
                1,
            ],
            [
                [3, 2, 1],
                fn ($item) => $item,
                1,
            ],
            [
                [3, 2, 1],
                fn ($item) => -$item,
                3,
            ],
            [
                [2.1, 1],
                null,
                1,
            ],
            [
                [2.1, 1],
                fn ($item) => $item,
                1,
            ],
            [
                [2.1, 1],
                fn ($item) => -$item,
                2.1,
            ],
            [
                [2, 1.1],
                null,
                1.1,
            ],
            [
                [2, 1.1],
                fn ($item) => $item,
                1.1,
            ],
            [
                [2, 1.1],
                fn ($item) => -$item,
                2,
            ],
            [
                [2.2, 1.1],
                null,
                1.1,
            ],
            [
                [2.2, 1.1],
                fn ($item) => $item,
                1.1,
            ],
            [
                [2.2, 1.1],
                fn ($item) => -$item,
                2.2,
            ],
            [
                [1.1, 2.2],
                null,
                1.1,
            ],
            [
                [1.1, 2.2],
                fn ($item) => $item,
                1.1,
            ],
            [
                [1.1, 2.2],
                fn ($item) => -$item,
                2.2,
            ],
            [
                ['a', 'b', 'c'],
                null,
                'a',
            ],
            [
                ['a', 'b', 'c'],
                fn ($item) => $item,
                'a',
            ],
            [
                ['a', 'b', 'c'],
                fn ($item) => -ord($item),
                'c',
            ],
            [
                ['b', 'c', 'a'],
                null,
                'a',
            ],
            [
                ['b', 'c', 'a'],
                fn ($item) => $item,
                'a',
            ],
            [
                ['b', 'c', 'a'],
                fn ($item) => -ord($item),
                'c',
            ],
            [
                ['c', 'b', 'a'],
                null,
                'a',
            ],
            [
                ['c', 'b', 'a'],
                fn ($item) => $item,
                'a',
            ],
            [
                ['c', 'b', 'a'],
                fn ($item) => -ord($item),
                'c',
            ],
            [
                ['ab', 'ba', 'b'],
                null,
                'ab',
            ],
            [
                ['ab', 'ba', 'b'],
                fn ($item) => $item,
                'ab',
            ],
            [
                ['ba', 'b', 'ab'],
                null,
                'ab',
            ],
            [
                ['ba', 'b', 'ab'],
                fn ($item) => $item,
                'ab',
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
                [],
            ],
            [
                [[], [2]],
                fn ($item) => $item,
                [],
            ],
            [
                [[2], []],
                null,
                [],
            ],
            [
                [[2], []],
                fn ($item) => $item,
                [],
            ],
            [
                [[], [null]],
                null,
                [],
            ],
            [
                [[], [null]],
                fn ($item) => $item,
                [],
            ],
            [
                [[null], []],
                null,
                [],
            ],
            [
                [[null], []],
                fn ($item) => $item,
                [],
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
                [2],
            ],
            [
                [[1, 2], [2]],
                fn ($item) => $item,
                [2],
            ],
            [
                [[3, 2], [2]],
                null,
                [2],
            ],
            [
                [[3, 2], [2]],
                fn ($item) => $item,
                [2],
            ],
            [
                [[1, 2], [2, 1]],
                null,
                [1, 2],
            ],
            [
                [[1, 2], [2, 1]],
                fn ($item) => $item,
                [1, 2],
            ],
            [
                [[2, 1], [1, 2]],
                null,
                [1, 2],
            ],
            [
                [[2, 1], [1, 2]],
                fn ($item) => $item,
                [1, 2],
            ],
            [
                [['a'], ['b']],
                null,
                ['a'],
            ],
            [
                [['a'], ['b']],
                fn ($item) => $item,
                ['a'],
            ],
            [
                [['a', 'a'], ['b']],
                null,
                ['b'],
            ],
            [
                [['a', 'a'], ['b']],
                fn ($item) => $item,
                ['b'],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                null,
                [1, 2, 3],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($item) => $item,
                [1, 2, 3],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($item) => $item[1],
                [2, 0, 3],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($item) => -$item[1],
                [1, 2, 3],
            ],
        ];
    }

    /**
     * @test         toMin generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        callable|null $comparator
     * @param        mixed $expected
     */
    public function testGenerators(\Generator $data, ?callable $comparator, $expected)
    {
        // When
        $result = Reduce::toMin($data, $comparator);

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
                -INF,
            ],
            [
                $gen([INF, -INF]),
                fn ($item) => $item,
                -INF,
            ],
            [
                $gen([INF, -INF]),
                fn ($item) => -$item,
                INF,
            ],
            [
                $gen([INF, -INF, 10, -1]),
                null,
                -INF,
            ],
            [
                $gen([INF, -INF, 10, -1]),
                fn ($item) => $item,
                -INF,
            ],
            [
                $gen([INF, -INF, 10, -1]),
                fn ($item) => -$item,
                INF,
            ],
            [
                $gen([1, 2, 3]),
                null,
                1,
            ],
            [
                $gen([1, 2, 3]),
                fn ($item) => $item,
                1,
            ],
            [
                $gen([1, 2, 3]),
                fn ($item) => -$item,
                3,
            ],
            [
                $gen([3, 2, 1]),
                null,
                1,
            ],
            [
                $gen([3, 2, 1]),
                fn ($item) => $item,
                1,
            ],
            [
                $gen([3, 2, 1]),
                fn ($item) => -$item,
                3,
            ],
            [
                $gen([3, 2, 1]),
                null,
                1,
            ],
            [
                $gen([3, 2, 1]),
                fn ($item) => $item,
                1,
            ],
            [
                $gen([3, 2, 1]),
                fn ($item) => -$item,
                3,
            ],
            [
                $gen([2.1, 1]),
                null,
                1,
            ],
            [
                $gen([2.1, 1]),
                fn ($item) => $item,
                1,
            ],
            [
                $gen([2.1, 1]),
                fn ($item) => -$item,
                2.1,
            ],
            [
                $gen([2, 1.1]),
                null,
                1.1,
            ],
            [
                $gen([2, 1.1]),
                fn ($item) => $item,
                1.1,
            ],
            [
                $gen([2, 1.1]),
                fn ($item) => -$item,
                2,
            ],
            [
                $gen([2.2, 1.1]),
                null,
                1.1,
            ],
            [
                $gen([2.2, 1.1]),
                fn ($item) => $item,
                1.1,
            ],
            [
                $gen([2.2, 1.1]),
                fn ($item) => -$item,
                2.2,
            ],
            [
                $gen([1.1, 2.2]),
                null,
                1.1,
            ],
            [
                $gen([1.1, 2.2]),
                fn ($item) => $item,
                1.1,
            ],
            [
                $gen([1.1, 2.2]),
                fn ($item) => -$item,
                2.2,
            ],
            [
                $gen(['a', 'b', 'c']),
                null,
                'a',
            ],
            [
                $gen(['a', 'b', 'c']),
                fn ($item) => $item,
                'a',
            ],
            [
                $gen(['a', 'b', 'c']),
                fn ($item) => -ord($item),
                'c',
            ],
            [
                $gen(['b', 'c', 'a']),
                null,
                'a',
            ],
            [
                $gen(['b', 'c', 'a']),
                fn ($item) => $item,
                'a',
            ],
            [
                $gen(['b', 'c', 'a']),
                fn ($item) => -ord($item),
                'c',
            ],
            [
                $gen(['c', 'b', 'a']),
                null,
                'a',
            ],
            [
                $gen(['c', 'b', 'a']),
                fn ($item) => $item,
                'a',
            ],
            [
                $gen(['c', 'b', 'a']),
                fn ($item) => -ord($item),
                'c',
            ],
            [
                $gen(['ab', 'ba', 'b']),
                null,
                'ab',
            ],
            [
                $gen(['ab', 'ba', 'b']),
                fn ($item) => $item,
                'ab',
            ],
            [
                $gen(['ba', 'b', 'ab']),
                null,
                'ab',
            ],
            [
                $gen(['ba', 'b', 'ab']),
                fn ($item) => $item,
                'ab',
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
                [],
            ],
            [
                $gen([[], [2]]),
                fn ($item) => $item,
                [],
            ],
            [
                $gen([[2], []]),
                null,
                [],
            ],
            [
                $gen([[2], []]),
                fn ($item) => $item,
                [],
            ],
            [
                $gen([[], [null]]),
                null,
                [],
            ],
            [
                $gen([[], [null]]),
                fn ($item) => $item,
                [],
            ],
            [
                $gen([[null], []]),
                null,
                [],
            ],
            [
                $gen([[null], []]),
                fn ($item) => $item,
                [],
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
                [2],
            ],
            [
                $gen([[1, 2], [2]]),
                fn ($item) => $item,
                [2],
            ],
            [
                $gen([[3, 2], [2]]),
                null,
                [2],
            ],
            [
                $gen([[3, 2], [2]]),
                fn ($item) => $item,
                [2],
            ],
            [
                $gen([[1, 2], [2, 1]]),
                null,
                [1, 2],
            ],
            [
                $gen([[1, 2], [2, 1]]),
                fn ($item) => $item,
                [1, 2],
            ],
            [
                $gen([[2, 1], [1, 2]]),
                null,
                [1, 2],
            ],
            [
                $gen([[2, 1], [1, 2]]),
                fn ($item) => $item,
                [1, 2],
            ],
            [
                $gen([['a'], ['b']]),
                null,
                ['a'],
            ],
            [
                $gen([['a'], ['b']]),
                fn ($item) => $item,
                ['a'],
            ],
            [
                $gen([['a', 'a'], ['b']]),
                null,
                ['b'],
            ],
            [
                $gen([['a', 'a'], ['b']]),
                fn ($item) => $item,
                ['b'],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                null,
                [1, 2, 3],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item,
                [1, 2, 3],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item[1],
                [2, 0, 3],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => -$item[1],
                [1, 2, 3],
            ],
        ];
    }

    /**
     * @test         toMin iterators
     * @dataProvider dataProviderForIterators
     * @param        \Generator $data
     * @param        callable|null $comparator
     * @param        mixed $expected
     */
    public function testIterators(\Iterator $data, ?callable $comparator, $expected)
    {
        // When
        $result = Reduce::toMin($data, $comparator);

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
                -INF,
            ],
            [
                $iter([INF, -INF]),
                fn ($item) => $item,
                -INF,
            ],
            [
                $iter([INF, -INF]),
                fn ($item) => -$item,
                INF,
            ],
            [
                $iter([INF, -INF, 10, -1]),
                null,
                -INF,
            ],
            [
                $iter([INF, -INF, 10, -1]),
                fn ($item) => $item,
                -INF,
            ],
            [
                $iter([INF, -INF, 10, -1]),
                fn ($item) => -$item,
                INF,
            ],
            [
                $iter([1, 2, 3]),
                null,
                1,
            ],
            [
                $iter([1, 2, 3]),
                fn ($item) => $item,
                1,
            ],
            [
                $iter([1, 2, 3]),
                fn ($item) => -$item,
                3,
            ],
            [
                $iter([3, 2, 1]),
                null,
                1,
            ],
            [
                $iter([3, 2, 1]),
                fn ($item) => $item,
                1,
            ],
            [
                $iter([3, 2, 1]),
                fn ($item) => -$item,
                3,
            ],
            [
                $iter([3, 2, 1]),
                null,
                1,
            ],
            [
                $iter([3, 2, 1]),
                fn ($item) => $item,
                1,
            ],
            [
                $iter([3, 2, 1]),
                fn ($item) => -$item,
                3,
            ],
            [
                $iter([2.1, 1]),
                null,
                1,
            ],
            [
                $iter([2.1, 1]),
                fn ($item) => $item,
                1,
            ],
            [
                $iter([2.1, 1]),
                fn ($item) => -$item,
                2.1,
            ],
            [
                $iter([2, 1.1]),
                null,
                1.1,
            ],
            [
                $iter([2, 1.1]),
                fn ($item) => $item,
                1.1,
            ],
            [
                $iter([2, 1.1]),
                fn ($item) => -$item,
                2,
            ],
            [
                $iter([2.2, 1.1]),
                null,
                1.1,
            ],
            [
                $iter([2.2, 1.1]),
                fn ($item) => $item,
                1.1,
            ],
            [
                $iter([2.2, 1.1]),
                fn ($item) => -$item,
                2.2,
            ],
            [
                $iter([1.1, 2.2]),
                null,
                1.1,
            ],
            [
                $iter([1.1, 2.2]),
                fn ($item) => $item,
                1.1,
            ],
            [
                $iter([1.1, 2.2]),
                fn ($item) => -$item,
                2.2,
            ],
            [
                $iter(['a', 'b', 'c']),
                null,
                'a',
            ],
            [
                $iter(['a', 'b', 'c']),
                fn ($item) => $item,
                'a',
            ],
            [
                $iter(['a', 'b', 'c']),
                fn ($item) => -ord($item),
                'c',
            ],
            [
                $iter(['b', 'c', 'a']),
                null,
                'a',
            ],
            [
                $iter(['b', 'c', 'a']),
                fn ($item) => $item,
                'a',
            ],
            [
                $iter(['b', 'c', 'a']),
                fn ($item) => -ord($item),
                'c',
            ],
            [
                $iter(['c', 'b', 'a']),
                null,
                'a',
            ],
            [
                $iter(['c', 'b', 'a']),
                fn ($item) => $item,
                'a',
            ],
            [
                $iter(['c', 'b', 'a']),
                fn ($item) => -ord($item),
                'c',
            ],
            [
                $iter(['ab', 'ba', 'b']),
                null,
                'ab',
            ],
            [
                $iter(['ab', 'ba', 'b']),
                fn ($item) => $item,
                'ab',
            ],
            [
                $iter(['ba', 'b', 'ab']),
                null,
                'ab',
            ],
            [
                $iter(['ba', 'b', 'ab']),
                fn ($item) => $item,
                'ab',
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
                [],
            ],
            [
                $iter([[], [2]]),
                fn ($item) => $item,
                [],
            ],
            [
                $iter([[2], []]),
                null,
                [],
            ],
            [
                $iter([[2], []]),
                fn ($item) => $item,
                [],
            ],
            [
                $iter([[], [null]]),
                null,
                [],
            ],
            [
                $iter([[], [null]]),
                fn ($item) => $item,
                [],
            ],
            [
                $iter([[null], []]),
                null,
                [],
            ],
            [
                $iter([[null], []]),
                fn ($item) => $item,
                [],
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
                [2],
            ],
            [
                $iter([[1, 2], [2]]),
                fn ($item) => $item,
                [2],
            ],
            [
                $iter([[3, 2], [2]]),
                null,
                [2],
            ],
            [
                $iter([[3, 2], [2]]),
                fn ($item) => $item,
                [2],
            ],
            [
                $iter([[1, 2], [2, 1]]),
                null,
                [1, 2],
            ],
            [
                $iter([[1, 2], [2, 1]]),
                fn ($item) => $item,
                [1, 2],
            ],
            [
                $iter([[2, 1], [1, 2]]),
                null,
                [1, 2],
            ],
            [
                $iter([[2, 1], [1, 2]]),
                fn ($item) => $item,
                [1, 2],
            ],
            [
                $iter([['a'], ['b']]),
                null,
                ['a'],
            ],
            [
                $iter([['a'], ['b']]),
                fn ($item) => $item,
                ['a'],
            ],
            [
                $iter([['a', 'a'], ['b']]),
                null,
                ['b'],
            ],
            [
                $iter([['a', 'a'], ['b']]),
                fn ($item) => $item,
                ['b'],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                null,
                [1, 2, 3],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item,
                [1, 2, 3],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item[1],
                [2, 0, 3],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => -$item[1],
                [1, 2, 3],
            ],
        ];
    }

    /**
     * @test         toMin traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        callable|null $comparator
     * @param        mixed $expected
     */
    public function testTraversables(\Traversable $data, ?callable $comparator, $expected)
    {
        // When
        $result = Reduce::toMin($data, $comparator);

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
                -INF,
            ],
            [
                $trav([INF, -INF]),
                fn ($item) => $item,
                -INF,
            ],
            [
                $trav([INF, -INF]),
                fn ($item) => -$item,
                INF,
            ],
            [
                $trav([INF, -INF, 10, -1]),
                null,
                -INF,
            ],
            [
                $trav([INF, -INF, 10, -1]),
                fn ($item) => $item,
                -INF,
            ],
            [
                $trav([INF, -INF, 10, -1]),
                fn ($item) => -$item,
                INF,
            ],
            [
                $trav([1, 2, 3]),
                null,
                1,
            ],
            [
                $trav([1, 2, 3]),
                fn ($item) => $item,
                1,
            ],
            [
                $trav([1, 2, 3]),
                fn ($item) => -$item,
                3,
            ],
            [
                $trav([3, 2, 1]),
                null,
                1,
            ],
            [
                $trav([3, 2, 1]),
                fn ($item) => $item,
                1,
            ],
            [
                $trav([3, 2, 1]),
                fn ($item) => -$item,
                3,
            ],
            [
                $trav([3, 2, 1]),
                null,
                1,
            ],
            [
                $trav([3, 2, 1]),
                fn ($item) => $item,
                1,
            ],
            [
                $trav([3, 2, 1]),
                fn ($item) => -$item,
                3,
            ],
            [
                $trav([2.1, 1]),
                null,
                1,
            ],
            [
                $trav([2.1, 1]),
                fn ($item) => $item,
                1,
            ],
            [
                $trav([2.1, 1]),
                fn ($item) => -$item,
                2.1,
            ],
            [
                $trav([2, 1.1]),
                null,
                1.1,
            ],
            [
                $trav([2, 1.1]),
                fn ($item) => $item,
                1.1,
            ],
            [
                $trav([2, 1.1]),
                fn ($item) => -$item,
                2,
            ],
            [
                $trav([2.2, 1.1]),
                null,
                1.1,
            ],
            [
                $trav([2.2, 1.1]),
                fn ($item) => $item,
                1.1,
            ],
            [
                $trav([2.2, 1.1]),
                fn ($item) => -$item,
                2.2,
            ],
            [
                $trav([1.1, 2.2]),
                null,
                1.1,
            ],
            [
                $trav([1.1, 2.2]),
                fn ($item) => $item,
                1.1,
            ],
            [
                $trav([1.1, 2.2]),
                fn ($item) => -$item,
                2.2,
            ],
            [
                $trav(['a', 'b', 'c']),
                null,
                'a',
            ],
            [
                $trav(['a', 'b', 'c']),
                fn ($item) => $item,
                'a',
            ],
            [
                $trav(['a', 'b', 'c']),
                fn ($item) => -ord($item),
                'c',
            ],
            [
                $trav(['b', 'c', 'a']),
                null,
                'a',
            ],
            [
                $trav(['b', 'c', 'a']),
                fn ($item) => $item,
                'a',
            ],
            [
                $trav(['b', 'c', 'a']),
                fn ($item) => -ord($item),
                'c',
            ],
            [
                $trav(['c', 'b', 'a']),
                null,
                'a',
            ],
            [
                $trav(['c', 'b', 'a']),
                fn ($item) => $item,
                'a',
            ],
            [
                $trav(['c', 'b', 'a']),
                fn ($item) => -ord($item),
                'c',
            ],
            [
                $trav(['ab', 'ba', 'b']),
                null,
                'ab',
            ],
            [
                $trav(['ab', 'ba', 'b']),
                fn ($item) => $item,
                'ab',
            ],
            [
                $trav(['ba', 'b', 'ab']),
                null,
                'ab',
            ],
            [
                $trav(['ba', 'b', 'ab']),
                fn ($item) => $item,
                'ab',
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
                [],
            ],
            [
                $trav([[], [2]]),
                fn ($item) => $item,
                [],
            ],
            [
                $trav([[2], []]),
                null,
                [],
            ],
            [
                $trav([[2], []]),
                fn ($item) => $item,
                [],
            ],
            [
                $trav([[], [null]]),
                null,
                [],
            ],
            [
                $trav([[], [null]]),
                fn ($item) => $item,
                [],
            ],
            [
                $trav([[null], []]),
                null,
                [],
            ],
            [
                $trav([[null], []]),
                fn ($item) => $item,
                [],
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
                [2],
            ],
            [
                $trav([[1, 2], [2]]),
                fn ($item) => $item,
                [2],
            ],
            [
                $trav([[3, 2], [2]]),
                null,
                [2],
            ],
            [
                $trav([[3, 2], [2]]),
                fn ($item) => $item,
                [2],
            ],
            [
                $trav([[1, 2], [2, 1]]),
                null,
                [1, 2],
            ],
            [
                $trav([[1, 2], [2, 1]]),
                fn ($item) => $item,
                [1, 2],
            ],
            [
                $trav([[2, 1], [1, 2]]),
                null,
                [1, 2],
            ],
            [
                $trav([[2, 1], [1, 2]]),
                fn ($item) => $item,
                [1, 2],
            ],
            [
                $trav([['a'], ['b']]),
                null,
                ['a'],
            ],
            [
                $trav([['a'], ['b']]),
                fn ($item) => $item,
                ['a'],
            ],
            [
                $trav([['a', 'a'], ['b']]),
                null,
                ['b'],
            ],
            [
                $trav([['a', 'a'], ['b']]),
                fn ($item) => $item,
                ['b'],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                null,
                [1, 2, 3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item,
                [1, 2, 3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item[1],
                [2, 0, 3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => -$item[1],
                [1, 2, 3],
            ],
        ];
    }
}

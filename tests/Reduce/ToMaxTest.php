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
    public function testArray(array $data, ?callable $comparator, $expected)
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
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                null,
            ],
            [
                [],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                null,
            ],
            [
                [0],
                null,
                0,
            ],
            [
                [0],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                0,
            ],
            [
                [0],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                0,
            ],
            [
                [INF],
                null,
                INF,
            ],
            [
                [INF],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                INF,
            ],
            [
                [INF],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                INF,
            ],
            [
                [-INF],
                null,
                -INF,
            ],
            [
                [-INF],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                -INF,
            ],
            [
                [-INF],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                -INF,
            ],
            [
                [INF, -INF],
                null,
                INF,
            ],
            [
                [INF, -INF],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                INF,
            ],
            [
                [INF, -INF],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                -INF,
            ],
            [
                [INF, -INF, 10, -1],
                null,
                INF,
            ],
            [
                [INF, -INF, 10, -1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                INF,
            ],
            [
                [INF, -INF, 10, -1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                -INF,
            ],
            [
                [1, 2, 3],
                null,
                3,
            ],
            [
                [1, 2, 3],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                3,
            ],
            [
                [1, 2, 3],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                [3, 2, 1],
                null,
                3,
            ],
            [
                [3, 2, 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                3,
            ],
            [
                [3, 2, 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                [2, 3, 1],
                null,
                3,
            ],
            [
                [2, 3, 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                3,
            ],
            [
                [2, 3, 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                [1, 2.1],
                null,
                2.1,
            ],
            [
                [1, 2.1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.1,
            ],
            [
                [1, 2.1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                [2.1, 1],
                null,
                2.1,
            ],
            [
                [2.1, 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.1,
            ],
            [
                [2.1, 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                [2, 1.1],
                null,
                2,
            ],
            [
                [2, 1.1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2,
            ],
            [
                [2, 1.1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1.1,
            ],
            [
                [2.2, 1.1],
                null,
                2.2,
            ],
            [
                [2.2, 1.1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.2,
            ],
            [
                [2.2, 1.1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1.1,
            ],
            [
                [1.1, 2.2],
                null,
                2.2,
            ],
            [
                [1.1, 2.2],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.2,
            ],
            [
                [1.1, 2.2],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1.1,
            ],
            [
                ['a', 'b', 'c'],
                null,
                'c',
            ],
            [
                ['a', 'b', 'c'],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'c',
            ],
            [
                ['a', 'b', 'c'],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'a',
            ],
            [
                ['b', 'c', 'a'],
                null,
                'c',
            ],
            [
                ['b', 'c', 'a'],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'c',
            ],
            [
                ['b', 'c', 'a'],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'a',
            ],
            [
                ['c', 'b', 'a'],
                null,
                'c',
            ],
            [
                ['c', 'b', 'a'],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'c',
            ],
            [
                ['c', 'b', 'a'],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'a',
            ],
            [
                ['ab', 'ba', 'b'],
                null,
                'ba',
            ],
            [
                ['ab', 'ba', 'b'],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ba',
            ],
            [
                ['ab', 'ba', 'b'],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ab',
            ],
            [
                ['ba', 'b', 'ab'],
                null,
                'ba',
            ],
            [
                ['ba', 'b', 'ab'],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ba',
            ],
            [
                ['ba', 'b', 'ab'],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ab',
            ],
            [
                [[]],
                null,
                [],
            ],
            [
                [[]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                [[]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                [[2]],
                null,
                [2],
            ],
            [
                [[2]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                [[2]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                [[], []],
                null,
                [],
            ],
            [
                [[], []],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                [[], []],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                [[], [2]],
                null,
                [2],
            ],
            [
                [[], [2]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                [[], [2]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                [[2], []],
                null,
                [2],
            ],
            [
                [[2], []],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                [[2], []],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                [[], [null]],
                null,
                [null],
            ],
            [
                [[], [null]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [null],
            ],
            [
                [[], [null]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                [[null], []],
                null,
                [null],
            ],
            [
                [[null], []],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [null],
            ],
            [
                [[null], []],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                [[null], [null]],
                null,
                [null],
            ],
            [
                [[null], [null]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [null],
            ],
            [
                [[null], [null]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [null],
            ],
            [
                [[1, 2], [2]],
                null,
                [1, 2],
            ],
            [
                [[1, 2], [2]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                [[1, 2], [2]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                [[3, 2], [2]],
                null,
                [3, 2],
            ],
            [
                [[3, 2], [2]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [3, 2],
            ],
            [
                [[3, 2], [2]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                [[1, 2], [2, 1]],
                null,
                [2, 1],
            ],
            [
                [[1, 2], [2, 1]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2, 1],
            ],
            [
                [[1, 2], [2, 1]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2],
            ],
            [
                [[2, 1], [1, 2]],
                null,
                [2, 1],
            ],
            [
                [[2, 1], [1, 2]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2, 1],
            ],
            [
                [[2, 1], [1, 2]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2],
            ],
            [
                [['a'], ['b']],
                null,
                ['b'],
            ],
            [
                [['a'], ['b']],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b'],
            ],
            [
                [['a'], ['b']],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a'],
            ],
            [
                [['a', 'a'], ['b']],
                null,
                ['a', 'a'],
            ],
            [
                [['a', 'a'], ['b']],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a', 'a'],
            ],
            [
                [['a', 'a'], ['b']],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b'],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                null,
                [2, 1, 3],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2, 1, 3],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2, 3],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                [1, 2, 3],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
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
    public function testGenerators(\Generator $data, ?callable $comparator, $expected)
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
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                null,
            ],
            [
                $gen([]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                null,
            ],
            [
                $gen([0]),
                null,
                0,
            ],
            [
                $gen([0]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                0,
            ],
            [
                $gen([0]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                0,
            ],
            [
                $gen([INF]),
                null,
                INF,
            ],
            [
                $gen([INF]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                INF,
            ],
            [
                $gen([INF]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                INF,
            ],
            [
                $gen([-INF]),
                null,
                -INF,
            ],
            [
                $gen([-INF]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                -INF,
            ],
            [
                $gen([-INF]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                -INF,
            ],
            [
                $gen([INF, -INF]),
                null,
                INF,
            ],
            [
                $gen([INF, -INF]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                INF,
            ],
            [
                $gen([INF, -INF]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                -INF,
            ],
            [
                $gen([INF, -INF, 10, -1]),
                null,
                INF,
            ],
            [
                $gen([INF, -INF, 10, -1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                INF,
            ],
            [
                $gen([INF, -INF, 10, -1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                -INF,
            ],
            [
                $gen([1, 2, 3]),
                null,
                3,
            ],
            [
                $gen([1, 2, 3]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                3,
            ],
            [
                $gen([1, 2, 3]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                $gen([3, 2, 1]),
                null,
                3,
            ],
            [
                $gen([3, 2, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                3,
            ],
            [
                $gen([3, 2, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                $gen([2, 3, 1]),
                null,
                3,
            ],
            [
                $gen([2, 3, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                3,
            ],
            [
                $gen([2, 3, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                $gen([1, 2.1]),
                null,
                2.1,
            ],
            [
                $gen([1, 2.1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.1,
            ],
            [
                $gen([1, 2.1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                $gen([2.1, 1]),
                null,
                2.1,
            ],
            [
                $gen([2.1, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.1,
            ],
            [
                $gen([2.1, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                $gen([2, 1.1]),
                null,
                2,
            ],
            [
                $gen([2, 1.1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2,
            ],
            [
                $gen([2, 1.1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1.1,
            ],
            [
                $gen([2.2, 1.1]),
                null,
                2.2,
            ],
            [
                $gen([2.2, 1.1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.2,
            ],
            [
                $gen([2.2, 1.1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1.1,
            ],
            [
                $gen([1.1, 2.2]),
                null,
                2.2,
            ],
            [
                $gen([1.1, 2.2]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.2,
            ],
            [
                $gen([1.1, 2.2]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1.1,
            ],
            [
                $gen(['a', 'b', 'c']),
                null,
                'c',
            ],
            [
                $gen(['a', 'b', 'c']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'c',
            ],
            [
                $gen(['a', 'b', 'c']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'a',
            ],
            [
                $gen(['b', 'c', 'a']),
                null,
                'c',
            ],
            [
                $gen(['b', 'c', 'a']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'c',
            ],
            [
                $gen(['b', 'c', 'a']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'a',
            ],
            [
                $gen(['c', 'b', 'a']),
                null,
                'c',
            ],
            [
                $gen(['c', 'b', 'a']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'c',
            ],
            [
                $gen(['c', 'b', 'a']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'a',
            ],
            [
                $gen(['ab', 'ba', 'b']),
                null,
                'ba',
            ],
            [
                $gen(['ab', 'ba', 'b']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ba',
            ],
            [
                $gen(['ab', 'ba', 'b']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ab',
            ],
            [
                $gen(['ba', 'b', 'ab']),
                null,
                'ba',
            ],
            [
                $gen(['ba', 'b', 'ab']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ba',
            ],
            [
                $gen(['ba', 'b', 'ab']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ab',
            ],
            [
                $gen([[]]),
                null,
                [],
            ],
            [
                $gen([[]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $gen([[]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $gen([[2]]),
                null,
                [2],
            ],
            [
                $gen([[2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                $gen([[2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                $gen([[], []]),
                null,
                [],
            ],
            [
                $gen([[], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $gen([[], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $gen([[], [2]]),
                null,
                [2],
            ],
            [
                $gen([[], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                $gen([[], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $gen([[2], []]),
                null,
                [2],
            ],
            [
                $gen([[2], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                $gen([[2], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $gen([[], [null]]),
                null,
                [null],
            ],
            [
                $gen([[], [null]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [null],
            ],
            [
                $gen([[], [null]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $gen([[null], []]),
                null,
                [null],
            ],
            [
                $gen([[null], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [null],
            ],
            [
                $gen([[null], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $gen([[null], [null]]),
                null,
                [null],
            ],
            [
                $gen([[null], [null]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [null],
            ],
            [
                $gen([[null], [null]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [null],
            ],
            [
                $gen([[1, 2], [2]]),
                null,
                [1, 2],
            ],
            [
                $gen([[1, 2], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                $gen([[1, 2], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                $gen([[3, 2], [2]]),
                null,
                [3, 2],
            ],
            [
                $gen([[3, 2], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [3, 2],
            ],
            [
                $gen([[3, 2], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                $gen([[1, 2], [2, 1]]),
                null,
                [2, 1],
            ],
            [
                $gen([[1, 2], [2, 1]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2, 1],
            ],
            [
                $gen([[1, 2], [2, 1]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2],
            ],
            [
                $gen([[2, 1], [1, 2]]),
                null,
                [2, 1],
            ],
            [
                $gen([[2, 1], [1, 2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2, 1],
            ],
            [
                $gen([[2, 1], [1, 2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2],
            ],
            [
                $gen([['a'], ['b']]),
                null,
                ['b'],
            ],
            [
                $gen([['a'], ['b']]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b'],
            ],
            [
                $gen([['a'], ['b']]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a'],
            ],
            [
                $gen([['a', 'a'], ['b']]),
                null,
                ['a', 'a'],
            ],
            [
                $gen([['a', 'a'], ['b']]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a', 'a'],
            ],
            [
                $gen([['a', 'a'], ['b']]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b'],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                null,
                [2, 1, 3],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2, 1, 3],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2, 3],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                [1, 2, 3],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
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
    public function testIterators(\Iterator $data, ?callable $comparator, $expected)
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
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                null,
            ],
            [
                $iter([]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                null,
            ],
            [
                $iter([0]),
                null,
                0,
            ],
            [
                $iter([0]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                0,
            ],
            [
                $iter([0]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                0,
            ],
            [
                $iter([INF]),
                null,
                INF,
            ],
            [
                $iter([INF]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                INF,
            ],
            [
                $iter([INF]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                INF,
            ],
            [
                $iter([-INF]),
                null,
                -INF,
            ],
            [
                $iter([-INF]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                -INF,
            ],
            [
                $iter([-INF]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                -INF,
            ],
            [
                $iter([INF, -INF]),
                null,
                INF,
            ],
            [
                $iter([INF, -INF]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                INF,
            ],
            [
                $iter([INF, -INF]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                -INF,
            ],
            [
                $iter([INF, -INF, 10, -1]),
                null,
                INF,
            ],
            [
                $iter([INF, -INF, 10, -1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                INF,
            ],
            [
                $iter([INF, -INF, 10, -1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                -INF,
            ],
            [
                $iter([1, 2, 3]),
                null,
                3,
            ],
            [
                $iter([1, 2, 3]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                3,
            ],
            [
                $iter([1, 2, 3]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                $iter([3, 2, 1]),
                null,
                3,
            ],
            [
                $iter([3, 2, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                3,
            ],
            [
                $iter([3, 2, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                $iter([2, 3, 1]),
                null,
                3,
            ],
            [
                $iter([2, 3, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                3,
            ],
            [
                $iter([2, 3, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                $iter([1, 2.1]),
                null,
                2.1,
            ],
            [
                $iter([1, 2.1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.1,
            ],
            [
                $iter([1, 2.1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                $iter([2.1, 1]),
                null,
                2.1,
            ],
            [
                $iter([2.1, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.1,
            ],
            [
                $iter([2.1, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                $iter([2, 1.1]),
                null,
                2,
            ],
            [
                $iter([2, 1.1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2,
            ],
            [
                $iter([2, 1.1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1.1,
            ],
            [
                $iter([2.2, 1.1]),
                null,
                2.2,
            ],
            [
                $iter([2.2, 1.1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.2,
            ],
            [
                $iter([2.2, 1.1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1.1,
            ],
            [
                $iter([1.1, 2.2]),
                null,
                2.2,
            ],
            [
                $iter([1.1, 2.2]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.2,
            ],
            [
                $iter([1.1, 2.2]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1.1,
            ],
            [
                $iter(['a', 'b', 'c']),
                null,
                'c',
            ],
            [
                $iter(['a', 'b', 'c']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'c',
            ],
            [
                $iter(['a', 'b', 'c']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'a',
            ],
            [
                $iter(['b', 'c', 'a']),
                null,
                'c',
            ],
            [
                $iter(['b', 'c', 'a']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'c',
            ],
            [
                $iter(['b', 'c', 'a']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'a',
            ],
            [
                $iter(['c', 'b', 'a']),
                null,
                'c',
            ],
            [
                $iter(['c', 'b', 'a']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'c',
            ],
            [
                $iter(['c', 'b', 'a']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'a',
            ],
            [
                $iter(['ab', 'ba', 'b']),
                null,
                'ba',
            ],
            [
                $iter(['ab', 'ba', 'b']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ba',
            ],
            [
                $iter(['ab', 'ba', 'b']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ab',
            ],
            [
                $iter(['ba', 'b', 'ab']),
                null,
                'ba',
            ],
            [
                $iter(['ba', 'b', 'ab']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ba',
            ],
            [
                $iter(['ba', 'b', 'ab']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ab',
            ],
            [
                $iter([[]]),
                null,
                [],
            ],
            [
                $iter([[]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $iter([[]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $iter([[2]]),
                null,
                [2],
            ],
            [
                $iter([[2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                $iter([[2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                $iter([[], []]),
                null,
                [],
            ],
            [
                $iter([[], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $iter([[], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $iter([[], [2]]),
                null,
                [2],
            ],
            [
                $iter([[], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                $iter([[], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $iter([[2], []]),
                null,
                [2],
            ],
            [
                $iter([[2], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                $iter([[2], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $iter([[], [null]]),
                null,
                [null],
            ],
            [
                $iter([[], [null]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [null],
            ],
            [
                $iter([[], [null]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $iter([[null], []]),
                null,
                [null],
            ],
            [
                $iter([[null], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [null],
            ],
            [
                $iter([[null], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $iter([[null], [null]]),
                null,
                [null],
            ],
            [
                $iter([[null], [null]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [null],
            ],
            [
                $iter([[null], [null]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [null],
            ],
            [
                $iter([[1, 2], [2]]),
                null,
                [1, 2],
            ],
            [
                $iter([[1, 2], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                $iter([[1, 2], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                $iter([[3, 2], [2]]),
                null,
                [3, 2],
            ],
            [
                $iter([[3, 2], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [3, 2],
            ],
            [
                $iter([[3, 2], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                $iter([[1, 2], [2, 1]]),
                null,
                [2, 1],
            ],
            [
                $iter([[1, 2], [2, 1]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2, 1],
            ],
            [
                $iter([[1, 2], [2, 1]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2],
            ],
            [
                $iter([[2, 1], [1, 2]]),
                null,
                [2, 1],
            ],
            [
                $iter([[2, 1], [1, 2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2, 1],
            ],
            [
                $iter([[2, 1], [1, 2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2],
            ],
            [
                $iter([['a'], ['b']]),
                null,
                ['b'],
            ],
            [
                $iter([['a'], ['b']]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b'],
            ],
            [
                $iter([['a'], ['b']]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a'],
            ],
            [
                $iter([['a', 'a'], ['b']]),
                null,
                ['a', 'a'],
            ],
            [
                $iter([['a', 'a'], ['b']]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a', 'a'],
            ],
            [
                $iter([['a', 'a'], ['b']]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b'],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                null,
                [2, 1, 3],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2, 1, 3],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2, 3],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                [1, 2, 3],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
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
    public function testTraversables(\Traversable $data, ?callable $comparator, $expected)
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
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                null,
            ],
            [
                $trav([]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                null,
            ],
            [
                $trav([0]),
                null,
                0,
            ],
            [
                $trav([0]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                0,
            ],
            [
                $trav([0]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                0,
            ],
            [
                $trav([INF]),
                null,
                INF,
            ],
            [
                $trav([INF]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                INF,
            ],
            [
                $trav([INF]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                INF,
            ],
            [
                $trav([-INF]),
                null,
                -INF,
            ],
            [
                $trav([-INF]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                -INF,
            ],
            [
                $trav([-INF]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                -INF,
            ],
            [
                $trav([INF, -INF]),
                null,
                INF,
            ],
            [
                $trav([INF, -INF]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                INF,
            ],
            [
                $trav([INF, -INF]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                -INF,
            ],
            [
                $trav([INF, -INF, 10, -1]),
                null,
                INF,
            ],
            [
                $trav([INF, -INF, 10, -1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                INF,
            ],
            [
                $trav([INF, -INF, 10, -1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                -INF,
            ],
            [
                $trav([1, 2, 3]),
                null,
                3,
            ],
            [
                $trav([1, 2, 3]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                3,
            ],
            [
                $trav([1, 2, 3]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                $trav([3, 2, 1]),
                null,
                3,
            ],
            [
                $trav([3, 2, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                3,
            ],
            [
                $trav([3, 2, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                $trav([2, 3, 1]),
                null,
                3,
            ],
            [
                $trav([2, 3, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                3,
            ],
            [
                $trav([2, 3, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                $trav([1, 2.1]),
                null,
                2.1,
            ],
            [
                $trav([1, 2.1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.1,
            ],
            [
                $trav([1, 2.1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                $trav([2.1, 1]),
                null,
                2.1,
            ],
            [
                $trav([2.1, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.1,
            ],
            [
                $trav([2.1, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1,
            ],
            [
                $trav([2, 1.1]),
                null,
                2,
            ],
            [
                $trav([2, 1.1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2,
            ],
            [
                $trav([2, 1.1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1.1,
            ],
            [
                $trav([2.2, 1.1]),
                null,
                2.2,
            ],
            [
                $trav([2.2, 1.1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.2,
            ],
            [
                $trav([2.2, 1.1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1.1,
            ],
            [
                $trav([1.1, 2.2]),
                null,
                2.2,
            ],
            [
                $trav([1.1, 2.2]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                2.2,
            ],
            [
                $trav([1.1, 2.2]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                1.1,
            ],
            [
                $trav(['a', 'b', 'c']),
                null,
                'c',
            ],
            [
                $trav(['a', 'b', 'c']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'c',
            ],
            [
                $trav(['a', 'b', 'c']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'a',
            ],
            [
                $trav(['b', 'c', 'a']),
                null,
                'c',
            ],
            [
                $trav(['b', 'c', 'a']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'c',
            ],
            [
                $trav(['b', 'c', 'a']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'a',
            ],
            [
                $trav(['c', 'b', 'a']),
                null,
                'c',
            ],
            [
                $trav(['c', 'b', 'a']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'c',
            ],
            [
                $trav(['c', 'b', 'a']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'a',
            ],
            [
                $trav(['ab', 'ba', 'b']),
                null,
                'ba',
            ],
            [
                $trav(['ab', 'ba', 'b']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ba',
            ],
            [
                $trav(['ab', 'ba', 'b']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ab',
            ],
            [
                $trav(['ba', 'b', 'ab']),
                null,
                'ba',
            ],
            [
                $trav(['ba', 'b', 'ab']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ba',
            ],
            [
                $trav(['ba', 'b', 'ab']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ab',
            ],
            [
                $trav([[]]),
                null,
                [],
            ],
            [
                $trav([[]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $trav([[]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $trav([[2]]),
                null,
                [2],
            ],
            [
                $trav([[2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                $trav([[2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                $trav([[], []]),
                null,
                [],
            ],
            [
                $trav([[], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $trav([[], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $trav([[], [2]]),
                null,
                [2],
            ],
            [
                $trav([[], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                $trav([[], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $trav([[2], []]),
                null,
                [2],
            ],
            [
                $trav([[2], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                $trav([[2], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $trav([[], [null]]),
                null,
                [null],
            ],
            [
                $trav([[], [null]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [null],
            ],
            [
                $trav([[], [null]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $trav([[null], []]),
                null,
                [null],
            ],
            [
                $trav([[null], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [null],
            ],
            [
                $trav([[null], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $trav([[null], [null]]),
                null,
                [null],
            ],
            [
                $trav([[null], [null]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [null],
            ],
            [
                $trav([[null], [null]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [null],
            ],
            [
                $trav([[1, 2], [2]]),
                null,
                [1, 2],
            ],
            [
                $trav([[1, 2], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                $trav([[1, 2], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                $trav([[3, 2], [2]]),
                null,
                [3, 2],
            ],
            [
                $trav([[3, 2], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [3, 2],
            ],
            [
                $trav([[3, 2], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                $trav([[1, 2], [2, 1]]),
                null,
                [2, 1],
            ],
            [
                $trav([[1, 2], [2, 1]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2, 1],
            ],
            [
                $trav([[1, 2], [2, 1]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2],
            ],
            [
                $trav([[2, 1], [1, 2]]),
                null,
                [2, 1],
            ],
            [
                $trav([[2, 1], [1, 2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2, 1],
            ],
            [
                $trav([[2, 1], [1, 2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2],
            ],
            [
                $trav([['a'], ['b']]),
                null,
                ['b'],
            ],
            [
                $trav([['a'], ['b']]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b'],
            ],
            [
                $trav([['a'], ['b']]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a'],
            ],
            [
                $trav([['a', 'a'], ['b']]),
                null,
                ['a', 'a'],
            ],
            [
                $trav([['a', 'a'], ['b']]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a', 'a'],
            ],
            [
                $trav([['a', 'a'], ['b']]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b'],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                null,
                [2, 1, 3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2, 1, 3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2, 3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                [1, 2, 3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
                [2, 0, 3],
            ],
        ];
    }
}

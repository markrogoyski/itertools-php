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
                -INF,
            ],
            [
                [INF, -INF],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                -INF,
            ],
            [
                [INF, -INF],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                INF,
            ],
            [
                [INF, -INF, 10, -1],
                null,
                -INF,
            ],
            [
                [INF, -INF, 10, -1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                -INF,
            ],
            [
                [INF, -INF, 10, -1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                INF,
            ],
            [
                [1, 2, 3],
                null,
                1,
            ],
            [
                [1, 2, 3],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                [1, 2, 3],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                3,
            ],
            [
                [3, 2, 1],
                null,
                1,
            ],
            [
                [3, 2, 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                [3, 2, 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                3,
            ],
            [
                [3, 2, 1],
                null,
                1,
            ],
            [
                [3, 2, 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                [3, 2, 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                3,
            ],
            [
                [2.1, 1],
                null,
                1,
            ],
            [
                [2.1, 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                [2.1, 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2.1,
            ],
            [
                [2, 1.1],
                null,
                1.1,
            ],
            [
                [2, 1.1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1.1,
            ],
            [
                [2, 1.1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2,
            ],
            [
                [2.2, 1.1],
                null,
                1.1,
            ],
            [
                [2.2, 1.1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1.1,
            ],
            [
                [2.2, 1.1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2.2,
            ],
            [
                [1.1, 2.2],
                null,
                1.1,
            ],
            [
                [1.1, 2.2],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1.1,
            ],
            [
                [1.1, 2.2],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2.2,
            ],
            [
                ['a', 'b', 'c'],
                null,
                'a',
            ],
            [
                ['a', 'b', 'c'],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'a',
            ],
            [
                ['a', 'b', 'c'],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'c',
            ],
            [
                ['b', 'c', 'a'],
                null,
                'a',
            ],
            [
                ['b', 'c', 'a'],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'a',
            ],
            [
                ['b', 'c', 'a'],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'c',
            ],
            [
                ['c', 'b', 'a'],
                null,
                'a',
            ],
            [
                ['c', 'b', 'a'],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'a',
            ],
            [
                ['c', 'b', 'a'],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'c',
            ],
            [
                ['ab', 'ba', 'b'],
                null,
                'ab',
            ],
            [
                ['ab', 'ba', 'b'],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ab',
            ],
            [
                ['ab', 'ba', 'b'],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ba',
            ],
            [
                ['ba', 'b', 'ab'],
                null,
                'ab',
            ],
            [
                ['ba', 'b', 'ab'],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ab',
            ],
            [
                ['ba', 'b', 'ab'],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ba',
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
                [],
            ],
            [
                [[], [2]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                [[], [2]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                [[2], []],
                null,
                [],
            ],
            [
                [[2], []],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                [[2], []],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                [[], [null]],
                null,
                [],
            ],
            [
                [[], [null]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                [[], [null]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [null],
            ],
            [
                [[null], []],
                null,
                [],
            ],
            [
                [[null], []],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                [[null], []],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [null],
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
                [2],
            ],
            [
                [[1, 2], [2]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                [[1, 2], [2]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2],
            ],
            [
                [[3, 2], [2]],
                null,
                [2],
            ],
            [
                [[3, 2], [2]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                [[3, 2], [2]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [3, 2],
            ],
            [
                [[1, 2], [2, 1]],
                null,
                [1, 2],
            ],
            [
                [[1, 2], [2, 1]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                [[1, 2], [2, 1]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                [[2, 1], [1, 2]],
                null,
                [1, 2],
            ],
            [
                [[2, 1], [1, 2]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                [[2, 1], [1, 2]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                [['a'], ['b']],
                null,
                ['a'],
            ],
            [
                [['a'], ['b']],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a'],
            ],
            [
                [['a'], ['b']],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b'],
            ],
            [
                [['a', 'a'], ['b']],
                null,
                ['b'],
            ],
            [
                [['a', 'a'], ['b']],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b'],
            ],
            [
                [['a', 'a'], ['b']],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a', 'a'],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                null,
                [1, 2, 3],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2, 3],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1, 3],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                [2, 0, 3],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
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
                -INF,
            ],
            [
                $gen([INF, -INF]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                -INF,
            ],
            [
                $gen([INF, -INF]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                INF,
            ],
            [
                $gen([INF, -INF, 10, -1]),
                null,
                -INF,
            ],
            [
                $gen([INF, -INF, 10, -1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                -INF,
            ],
            [
                $gen([INF, -INF, 10, -1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                INF,
            ],
            [
                $gen([1, 2, 3]),
                null,
                1,
            ],
            [
                $gen([1, 2, 3]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                $gen([1, 2, 3]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                3,
            ],
            [
                $gen([3, 2, 1]),
                null,
                1,
            ],
            [
                $gen([3, 2, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                $gen([3, 2, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                3,
            ],
            [
                $gen([3, 2, 1]),
                null,
                1,
            ],
            [
                $gen([3, 2, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                $gen([3, 2, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                3,
            ],
            [
                $gen([2.1, 1]),
                null,
                1,
            ],
            [
                $gen([2.1, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                $gen([2.1, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2.1,
            ],
            [
                $gen([2, 1.1]),
                null,
                1.1,
            ],
            [
                $gen([2, 1.1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1.1,
            ],
            [
                $gen([2, 1.1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2,
            ],
            [
                $gen([2.2, 1.1]),
                null,
                1.1,
            ],
            [
                $gen([2.2, 1.1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1.1,
            ],
            [
                $gen([2.2, 1.1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2.2,
            ],
            [
                $gen([1.1, 2.2]),
                null,
                1.1,
            ],
            [
                $gen([1.1, 2.2]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1.1,
            ],
            [
                $gen([1.1, 2.2]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2.2,
            ],
            [
                $gen(['a', 'b', 'c']),
                null,
                'a',
            ],
            [
                $gen(['a', 'b', 'c']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'a',
            ],
            [
                $gen(['a', 'b', 'c']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'c',
            ],
            [
                $gen(['b', 'c', 'a']),
                null,
                'a',
            ],
            [
                $gen(['b', 'c', 'a']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'a',
            ],
            [
                $gen(['b', 'c', 'a']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'c',
            ],
            [
                $gen(['c', 'b', 'a']),
                null,
                'a',
            ],
            [
                $gen(['c', 'b', 'a']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'a',
            ],
            [
                $gen(['c', 'b', 'a']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'c',
            ],
            [
                $gen(['ab', 'ba', 'b']),
                null,
                'ab',
            ],
            [
                $gen(['ab', 'ba', 'b']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ab',
            ],
            [
                $gen(['ab', 'ba', 'b']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ba',
            ],
            [
                $gen(['ba', 'b', 'ab']),
                null,
                'ab',
            ],
            [
                $gen(['ba', 'b', 'ab']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ab',
            ],
            [
                $gen(['ba', 'b', 'ab']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ba',
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
                [],
            ],
            [
                $gen([[], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $gen([[], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                $gen([[2], []]),
                null,
                [],
            ],
            [
                $gen([[2], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $gen([[2], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                $gen([[], [null]]),
                null,
                [],
            ],
            [
                $gen([[], [null]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $gen([[], [null]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [null],
            ],
            [
                $gen([[null], []]),
                null,
                [],
            ],
            [
                $gen([[null], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $gen([[null], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [null],
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
                [2],
            ],
            [
                $gen([[1, 2], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                $gen([[1, 2], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2],
            ],
            [
                $gen([[3, 2], [2]]),
                null,
                [2],
            ],
            [
                $gen([[3, 2], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                $gen([[3, 2], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [3, 2],
            ],
            [
                $gen([[1, 2], [2, 1]]),
                null,
                [1, 2],
            ],
            [
                $gen([[1, 2], [2, 1]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                $gen([[1, 2], [2, 1]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                $gen([[2, 1], [1, 2]]),
                null,
                [1, 2],
            ],
            [
                $gen([[2, 1], [1, 2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                $gen([[2, 1], [1, 2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                $gen([['a'], ['b']]),
                null,
                ['a'],
            ],
            [
                $gen([['a'], ['b']]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a'],
            ],
            [
                $gen([['a'], ['b']]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b'],
            ],
            [
                $gen([['a', 'a'], ['b']]),
                null,
                ['b'],
            ],
            [
                $gen([['a', 'a'], ['b']]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b'],
            ],
            [
                $gen([['a', 'a'], ['b']]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a', 'a'],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                null,
                [1, 2, 3],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2, 3],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1, 3],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                [2, 0, 3],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
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
                -INF,
            ],
            [
                $iter([INF, -INF]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                -INF,
            ],
            [
                $iter([INF, -INF]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                INF,
            ],
            [
                $iter([INF, -INF, 10, -1]),
                null,
                -INF,
            ],
            [
                $iter([INF, -INF, 10, -1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                -INF,
            ],
            [
                $iter([INF, -INF, 10, -1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                INF,
            ],
            [
                $iter([1, 2, 3]),
                null,
                1,
            ],
            [
                $iter([1, 2, 3]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                $iter([1, 2, 3]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                3,
            ],
            [
                $iter([3, 2, 1]),
                null,
                1,
            ],
            [
                $iter([3, 2, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                $iter([3, 2, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                3,
            ],
            [
                $iter([3, 2, 1]),
                null,
                1,
            ],
            [
                $iter([3, 2, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                $iter([3, 2, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                3,
            ],
            [
                $iter([2.1, 1]),
                null,
                1,
            ],
            [
                $iter([2.1, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                $iter([2.1, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2.1,
            ],
            [
                $iter([2, 1.1]),
                null,
                1.1,
            ],
            [
                $iter([2, 1.1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1.1,
            ],
            [
                $iter([2, 1.1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2,
            ],
            [
                $iter([2.2, 1.1]),
                null,
                1.1,
            ],
            [
                $iter([2.2, 1.1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1.1,
            ],
            [
                $iter([2.2, 1.1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2.2,
            ],
            [
                $iter([1.1, 2.2]),
                null,
                1.1,
            ],
            [
                $iter([1.1, 2.2]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1.1,
            ],
            [
                $iter([1.1, 2.2]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2.2,
            ],
            [
                $iter(['a', 'b', 'c']),
                null,
                'a',
            ],
            [
                $iter(['a', 'b', 'c']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'a',
            ],
            [
                $iter(['a', 'b', 'c']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'c',
            ],
            [
                $iter(['b', 'c', 'a']),
                null,
                'a',
            ],
            [
                $iter(['b', 'c', 'a']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'a',
            ],
            [
                $iter(['b', 'c', 'a']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'c',
            ],
            [
                $iter(['c', 'b', 'a']),
                null,
                'a',
            ],
            [
                $iter(['c', 'b', 'a']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'a',
            ],
            [
                $iter(['c', 'b', 'a']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'c',
            ],
            [
                $iter(['ab', 'ba', 'b']),
                null,
                'ab',
            ],
            [
                $iter(['ab', 'ba', 'b']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ab',
            ],
            [
                $iter(['ab', 'ba', 'b']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ba',
            ],
            [
                $iter(['ba', 'b', 'ab']),
                null,
                'ab',
            ],
            [
                $iter(['ba', 'b', 'ab']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ab',
            ],
            [
                $iter(['ba', 'b', 'ab']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ba',
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
                [],
            ],
            [
                $iter([[], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $iter([[], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                $iter([[2], []]),
                null,
                [],
            ],
            [
                $iter([[2], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $iter([[2], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                $iter([[], [null]]),
                null,
                [],
            ],
            [
                $iter([[], [null]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $iter([[], [null]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [null],
            ],
            [
                $iter([[null], []]),
                null,
                [],
            ],
            [
                $iter([[null], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $iter([[null], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [null],
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
                [2],
            ],
            [
                $iter([[1, 2], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                $iter([[1, 2], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2],
            ],
            [
                $iter([[3, 2], [2]]),
                null,
                [2],
            ],
            [
                $iter([[3, 2], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                $iter([[3, 2], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [3, 2],
            ],
            [
                $iter([[1, 2], [2, 1]]),
                null,
                [1, 2],
            ],
            [
                $iter([[1, 2], [2, 1]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                $iter([[1, 2], [2, 1]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                $iter([[2, 1], [1, 2]]),
                null,
                [1, 2],
            ],
            [
                $iter([[2, 1], [1, 2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                $iter([[2, 1], [1, 2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                $iter([['a'], ['b']]),
                null,
                ['a'],
            ],
            [
                $iter([['a'], ['b']]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a'],
            ],
            [
                $iter([['a'], ['b']]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b'],
            ],
            [
                $iter([['a', 'a'], ['b']]),
                null,
                ['b'],
            ],
            [
                $iter([['a', 'a'], ['b']]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b'],
            ],
            [
                $iter([['a', 'a'], ['b']]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a', 'a'],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                null,
                [1, 2, 3],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2, 3],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1, 3],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                [2, 0, 3],
            ],
            [
                $iter([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
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
                -INF,
            ],
            [
                $trav([INF, -INF]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                -INF,
            ],
            [
                $trav([INF, -INF]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                INF,
            ],
            [
                $trav([INF, -INF, 10, -1]),
                null,
                -INF,
            ],
            [
                $trav([INF, -INF, 10, -1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                -INF,
            ],
            [
                $trav([INF, -INF, 10, -1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                INF,
            ],
            [
                $trav([1, 2, 3]),
                null,
                1,
            ],
            [
                $trav([1, 2, 3]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                $trav([1, 2, 3]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                3,
            ],
            [
                $trav([3, 2, 1]),
                null,
                1,
            ],
            [
                $trav([3, 2, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                $trav([3, 2, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                3,
            ],
            [
                $trav([3, 2, 1]),
                null,
                1,
            ],
            [
                $trav([3, 2, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                $trav([3, 2, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                3,
            ],
            [
                $trav([2.1, 1]),
                null,
                1,
            ],
            [
                $trav([2.1, 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1,
            ],
            [
                $trav([2.1, 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2.1,
            ],
            [
                $trav([2, 1.1]),
                null,
                1.1,
            ],
            [
                $trav([2, 1.1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1.1,
            ],
            [
                $trav([2, 1.1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2,
            ],
            [
                $trav([2.2, 1.1]),
                null,
                1.1,
            ],
            [
                $trav([2.2, 1.1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1.1,
            ],
            [
                $trav([2.2, 1.1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2.2,
            ],
            [
                $trav([1.1, 2.2]),
                null,
                1.1,
            ],
            [
                $trav([1.1, 2.2]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                1.1,
            ],
            [
                $trav([1.1, 2.2]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                2.2,
            ],
            [
                $trav(['a', 'b', 'c']),
                null,
                'a',
            ],
            [
                $trav(['a', 'b', 'c']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'a',
            ],
            [
                $trav(['a', 'b', 'c']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'c',
            ],
            [
                $trav(['b', 'c', 'a']),
                null,
                'a',
            ],
            [
                $trav(['b', 'c', 'a']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'a',
            ],
            [
                $trav(['b', 'c', 'a']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'c',
            ],
            [
                $trav(['c', 'b', 'a']),
                null,
                'a',
            ],
            [
                $trav(['c', 'b', 'a']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'a',
            ],
            [
                $trav(['c', 'b', 'a']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'c',
            ],
            [
                $trav(['ab', 'ba', 'b']),
                null,
                'ab',
            ],
            [
                $trav(['ab', 'ba', 'b']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ab',
            ],
            [
                $trav(['ab', 'ba', 'b']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ba',
            ],
            [
                $trav(['ba', 'b', 'ab']),
                null,
                'ab',
            ],
            [
                $trav(['ba', 'b', 'ab']),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                'ab',
            ],
            [
                $trav(['ba', 'b', 'ab']),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                'ba',
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
                [],
            ],
            [
                $trav([[], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $trav([[], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                $trav([[2], []]),
                null,
                [],
            ],
            [
                $trav([[2], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $trav([[2], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2],
            ],
            [
                $trav([[], [null]]),
                null,
                [],
            ],
            [
                $trav([[], [null]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $trav([[], [null]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [null],
            ],
            [
                $trav([[null], []]),
                null,
                [],
            ],
            [
                $trav([[null], []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $trav([[null], []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [null],
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
                [2],
            ],
            [
                $trav([[1, 2], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                $trav([[1, 2], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [1, 2],
            ],
            [
                $trav([[3, 2], [2]]),
                null,
                [2],
            ],
            [
                $trav([[3, 2], [2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [2],
            ],
            [
                $trav([[3, 2], [2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [3, 2],
            ],
            [
                $trav([[1, 2], [2, 1]]),
                null,
                [1, 2],
            ],
            [
                $trav([[1, 2], [2, 1]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                $trav([[1, 2], [2, 1]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                $trav([[2, 1], [1, 2]]),
                null,
                [1, 2],
            ],
            [
                $trav([[2, 1], [1, 2]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2],
            ],
            [
                $trav([[2, 1], [1, 2]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1],
            ],
            [
                $trav([['a'], ['b']]),
                null,
                ['a'],
            ],
            [
                $trav([['a'], ['b']]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a'],
            ],
            [
                $trav([['a'], ['b']]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b'],
            ],
            [
                $trav([['a', 'a'], ['b']]),
                null,
                ['b'],
            ],
            [
                $trav([['a', 'a'], ['b']]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b'],
            ],
            [
                $trav([['a', 'a'], ['b']]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a', 'a'],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                null,
                [1, 2, 3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [1, 2, 3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [2, 1, 3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                [2, 0, 3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
                [1, 2, 3],
            ],
        ];
    }
}

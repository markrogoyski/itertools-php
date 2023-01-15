<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToBoundsTest extends \PHPUnit\Framework\TestCase
{
    protected const ROUND_PRECISION = 0.0001;

    /**
     * @test         toBounds array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        array $expected
     */
    public function testArray(array $data, array $expected)
    {
        // When
        $result = Reduce::toBounds($data);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::ROUND_PRECISION);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                [null, null],
            ],
            [
                [0],
                [0, 0],
            ],
            [
                [1],
                [1, 1],
            ],
            [
                [-1],
                [-1, -1],
            ],
            [
                [-1, -3, -5],
                [-5, -1],
            ],
            [
                [3, 1, 2, -3, -1, -2],
                [-3, 3],
            ],
            [
                [2.2, 3.3, 1.1],
                [1.1, 3.3],
            ],
            [
                [2, 3.3, 1.1],
                [1.1, 3.3],
            ],
            [
                [2.2, -3.3, -1.1, 2.2, 5.5],
                [-3.3, 5.5],
            ],
            [
                ['2.2', '-3.3', '-1.1', '2.2', '5.5'],
                [-3.3, 5.5],
            ],
            [
                ['3', '4', '1'],
                [1, 4],
            ],
            [
                [2, -3.3, '-1.1', 2.2, '5'],
                [-3.3, 5],
            ],
        ];
    }

    /**
     * @test         toBounds generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        array $expected
     */
    public function testGenerators(\Generator $data, array $expected)
    {
        // When
        $result = Reduce::toBounds($data);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::ROUND_PRECISION);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([]),
                [null, null],
            ],
            [
                $gen([0]),
                [0, 0],
            ],
            [
                $gen([1]),
                [1, 1],
            ],
            [
                $gen([-1]),
                [-1, -1],
            ],
            [
                $gen([-1, -3, -5]),
                [-5, -1],
            ],
            [
                $gen([3, 1, 2, -3, -1, -2]),
                [-3, 3],
            ],
            [
                $gen([2.2, 3.3, 1.1]),
                [1.1, 3.3],
            ],
            [
                $gen([2, 3.3, 1.1]),
                [1.1, 3.3],
            ],
            [
                $gen([2.2, -3.3, -1.1, 2.2, 5.5]),
                [-3.3, 5.5],
            ],
            [
                $gen(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                [-3.3, 5.5],
            ],
            [
                $gen(['3', '4', '1']),
                [1, 4],
            ],
            [
                $gen([2, -3.3, '-1.1', 2.2, '5']),
                [-3.3, 5],
            ],
        ];
    }

    /**
     * @test         toBounds iterators
     * @dataProvider dataProviderForIterators
     * @param        \Generator $data
     * @param        array $expected
     */
    public function testIterators(\Iterator $data, array $expected)
    {
        // When
        $result = Reduce::toBounds($data);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::ROUND_PRECISION);
    }

    public function dataProviderForIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $iter([]),
                [null, null],
            ],
            [
                $iter([0]),
                [0, 0],
            ],
            [
                $iter([1]),
                [1, 1],
            ],
            [
                $iter([-1]),
                [-1, -1],
            ],
            [
                $iter([-1, -3, -5]),
                [-5, -1],
            ],
            [
                $iter([3, 1, 2, -3, -1, -2]),
                [-3, 3],
            ],
            [
                $iter([2.2, 3.3, 1.1]),
                [1.1, 3.3],
            ],
            [
                $iter([2, 3.3, 1.1]),
                [1.1, 3.3],
            ],
            [
                $iter([2.2, -3.3, -1.1, 2.2, 5.5]),
                [-3.3, 5.5],
            ],
            [
                $iter(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                [-3.3, 5.5],
            ],
            [
                $iter(['3', '4', '1']),
                [1, 4],
            ],
            [
                $iter([2, -3.3, '-1.1', 2.2, '5']),
                [-3.3, 5],
            ],
        ];
    }

    /**
     * @test         toBounds traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        array $expected
     */
    public function testTraversables(\Traversable $data, array $expected)
    {
        // When
        $result = Reduce::toBounds($data);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::ROUND_PRECISION);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([]),
                [null, null],
            ],
            [
                $trav([0]),
                [0, 0],
            ],
            [
                $trav([1]),
                [1, 1],
            ],
            [
                $trav([-1]),
                [-1, -1],
            ],
            [
                $trav([-1, -3, -5]),
                [-5, -1],
            ],
            [
                $trav([3, 1, 2, -3, -1, -2]),
                [-3, 3],
            ],
            [
                $trav([2.2, 3.3, 1.1]),
                [1.1, 3.3],
            ],
            [
                $trav([2, 3.3, 1.1]),
                [1.1, 3.3],
            ],
            [
                $trav([2.2, -3.3, -1.1, 2.2, 5.5]),
                [-3.3, 5.5],
            ],
            [
                $trav(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                [-3.3, 5.5],
            ],
            [
                $trav(['3', '4', '1']),
                [1, 4],
            ],
            [
                $trav([2, -3.3, '-1.1', 2.2, '5']),
                [-3.3, 5],
            ],
        ];
    }
}

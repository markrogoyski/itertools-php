<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToAmplitudeTest extends \PHPUnit\Framework\TestCase
{
    protected const ROUND_PRECISION = 0.0001;

    /**
     * @test         toAmplitude array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        int|float $expected
     */
    public function testArray(array $data, $expected)
    {
        // When
        $result = Reduce::toAmplitude($data);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::ROUND_PRECISION);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                0,
            ],
            [
                [0],
                0,
            ],
            [
                [1],
                0,
            ],
            [
                [-1],
                0,
            ],
            [
                [-1, -3, -5],
                4,
            ],
            [
                [3, 1, 2, -3, -1, -2],
                6,
            ],
            [
                [2.2, 3.3, 1.1],
                2.2,
            ],
            [
                [2, 3.3, 1.1],
                2.2,
            ],
            [
                [2.2, -3.3, -1.1, 2.2, 5.5],
                8.8,
            ],
            [
                ['2.2', '-3.3', '-1.1', '2.2', '5.5'],
                8.8,
            ],
            [
                ['3', '4', '1'],
                3,
            ],
            [
                [2, -3.3, '-1.1', 2.2, '5'],
                8.3,
            ],
        ];
    }

    /**
     * @test         toAmplitude generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        int|float $expected
     */
    public function testGenerators(\Generator $data, $expected)
    {
        // When
        $result = Reduce::toAmplitude($data);

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
                0,
            ],
            [
                $gen([0]),
                0,
            ],
            [
                $gen([1]),
                0,
            ],
            [
                $gen([-1]),
                0,
            ],
            [
                $gen([-1, -3, -5]),
                4,
            ],
            [
                $gen([3, 1, 2, -3, -1, -2]),
                6,
            ],
            [
                $gen([2.2, 3.3, 1.1]),
                2.2,
            ],
            [
                $gen([2, 3.3, 1.1]),
                2.2,
            ],
            [
                $gen([2.2, -3.3, -1.1, 2.2, 5.5]),
                8.8,
            ],
            [
                $gen(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                8.8,
            ],
            [
                $gen(['3', '4', '1']),
                3,
            ],
            [
                $gen([2, -3.3, '-1.1', 2.2, '5']),
                8.3,
            ],
        ];
    }

    /**
     * @test         toAmplitude iterators
     * @dataProvider dataProviderForIterators
     * @param        \Generator $data
     * @param        int|float $expected
     */
    public function testIterators(\Iterator $data, $expected)
    {
        // When
        $result = Reduce::toAmplitude($data);

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
                0,
            ],
            [
                $iter([0]),
                0,
            ],
            [
                $iter([1]),
                0,
            ],
            [
                $iter([-1]),
                0,
            ],
            [
                $iter([-1, -3, -5]),
                4,
            ],
            [
                $iter([3, 1, 2, -3, -1, -2]),
                6,
            ],
            [
                $iter([2.2, 3.3, 1.1]),
                2.2,
            ],
            [
                $iter([2, 3.3, 1.1]),
                2.2,
            ],
            [
                $iter([2.2, -3.3, -1.1, 2.2, 5.5]),
                8.8,
            ],
            [
                $iter(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                8.8,
            ],
            [
                $iter(['3', '4', '1']),
                3,
            ],
            [
                $iter([2, -3.3, '-1.1', 2.2, '5']),
                8.3,
            ],
        ];
    }

    /**
     * @test         toAmplitude traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        int|float $expected
     */
    public function testTraversables(\Traversable $data, $expected)
    {
        // When
        $result = Reduce::toAmplitude($data);

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
                0,
            ],
            [
                $trav([0]),
                0,
            ],
            [
                $trav([1]),
                0,
            ],
            [
                $trav([-1]),
                0,
            ],
            [
                $trav([-1, -3, -5]),
                4,
            ],
            [
                $trav([3, 1, 2, -3, -1, -2]),
                6,
            ],
            [
                $trav([2.2, 3.3, 1.1]),
                2.2,
            ],
            [
                $trav([2, 3.3, 1.1]),
                2.2,
            ],
            [
                $trav([2.2, -3.3, -1.1, 2.2, 5.5]),
                8.8,
            ],
            [
                $trav(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                8.8,
            ],
            [
                $trav(['3', '4', '1']),
                3,
            ],
            [
                $trav([2, -3.3, '-1.1', 2.2, '5']),
                8.3,
            ],
        ];
    }
}

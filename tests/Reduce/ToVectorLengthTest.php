<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToVectorLengthTest extends \PHPUnit\Framework\TestCase
{
    protected const ROUND_PRECISION = 0.0001;

    /**
     * @test         toVectorLength array
     * @dataProvider dataProviderForArray
     * @param        array $vector
     * @param        int|float $expected
     */
    public function testArray(array $vector, $expected)
    {
        // When
        $result = Reduce::toVectorLength($vector);

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
                1,
            ],
            [
                [2],
                2,
            ],
            [
                [1, 1],
                sqrt(2),
            ],
            [
                [1, 1, 1],
                sqrt(3),
            ],
            [
                [1, 1, 1, 1],
                2,
            ],
            [
                [3, 4],
                5,
            ],
            [
                [1, 2, 3, 4],
                sqrt(30),
            ],
            [
                ['1', '2', '3', '4'],
                sqrt(30),
            ],
            [
                [1, 2, '3', '4'],
                sqrt(30),
            ],
            [
                [1.1, 2.2, 3.3, 4.4],
                sqrt(36.3),
            ],
            [
                ['1.1', '2.2', 3.3, 4.4],
                sqrt(36.3),
            ],
            [
                [1, 2, 3.3, 4.4],
                sqrt(35.25),
            ],
            [
                [1, '2', 3.3, '4.4'],
                sqrt(35.25),
            ],
        ];
    }

    /**
     * @test         toVectorLength generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $vector
     * @param        mixed $expected
     */
    public function testGenerators(\Generator $vector, $expected)
    {
        // When
        $result = Reduce::toVectorLength($vector);

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
                1,
            ],
            [
                $gen([2]),
                2,
            ],
            [
                $gen([1, 1]),
                sqrt(2),
            ],
            [
                $gen([1, 1, 1]),
                sqrt(3),
            ],
            [
                $gen([1, 1, 1, 1]),
                2,
            ],
            [
                $gen([3, 4]),
                5,
            ],
            [
                $gen([1, 2, 3, 4]),
                sqrt(30),
            ],
            [
                $gen(['1', '2', '3', '4']),
                sqrt(30),
            ],
            [
                $gen([1, 2, '3', '4']),
                sqrt(30),
            ],
            [
                $gen([1.1, 2.2, 3.3, 4.4]),
                sqrt(36.3),
            ],
            [
                $gen(['1.1', '2.2', 3.3, 4.4]),
                sqrt(36.3),
            ],
            [
                $gen([1, 2, 3.3, 4.4]),
                sqrt(35.25),
            ],
            [
                $gen([1, '2', 3.3, '4.4']),
                sqrt(35.25),
            ],
        ];
    }

    /**
     * @test         toVectorLength iterators
     * @dataProvider dataProviderForIterators
     * @param        \Generator $vector
     * @param        mixed $expected
     */
    public function testIterators(\Iterator $vector, $expected)
    {
        // When
        $result = Reduce::toVectorLength($vector);

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
                1,
            ],
            [
                $iter([2]),
                2,
            ],
            [
                $iter([1, 1]),
                sqrt(2),
            ],
            [
                $iter([1, 1, 1]),
                sqrt(3),
            ],
            [
                $iter([1, 1, 1, 1]),
                2,
            ],
            [
                $iter([3, 4]),
                5,
            ],
            [
                $iter([1, 2, 3, 4]),
                sqrt(30),
            ],
            [
                $iter(['1', '2', '3', '4']),
                sqrt(30),
            ],
            [
                $iter([1, 2, '3', '4']),
                sqrt(30),
            ],
            [
                $iter([1.1, 2.2, 3.3, 4.4]),
                sqrt(36.3),
            ],
            [
                $iter(['1.1', '2.2', 3.3, 4.4]),
                sqrt(36.3),
            ],
            [
                $iter([1, 2, 3.3, 4.4]),
                sqrt(35.25),
            ],
            [
                $iter([1, '2', 3.3, '4.4']),
                sqrt(35.25),
            ],
        ];
    }

    /**
     * @test         toVectorLength traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $vector
     * @param        mixed $expected
     */
    public function testTraversables(\Traversable $vector, $expected)
    {
        // When
        $result = Reduce::toVectorLength($vector);

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
                1,
            ],
            [
                $trav([2]),
                2,
            ],
            [
                $trav([1, 1]),
                sqrt(2),
            ],
            [
                $trav([1, 1, 1]),
                sqrt(3),
            ],
            [
                $trav([1, 1, 1, 1]),
                2,
            ],
            [
                $trav([3, 4]),
                5,
            ],
            [
                $trav([1, 2, 3, 4]),
                sqrt(30),
            ],
            [
                $trav(['1', '2', '3', '4']),
                sqrt(30),
            ],
            [
                $trav([1, 2, '3', '4']),
                sqrt(30),
            ],
            [
                $trav([1.1, 2.2, 3.3, 4.4]),
                sqrt(36.3),
            ],
            [
                $trav(['1.1', '2.2', 3.3, 4.4]),
                sqrt(36.3),
            ],
            [
                $trav([1, 2, 3.3, 4.4]),
                sqrt(35.25),
            ],
            [
                $trav([1, '2', 3.3, '4.4']),
                sqrt(35.25),
            ],
        ];
    }
}

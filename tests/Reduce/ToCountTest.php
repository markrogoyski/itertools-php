<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\CountableIteratorAggregateFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToCountTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         toCount array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        int|float $expected
     */
    public function testArray(array $data, $expected)
    {
        // When
        $result = Reduce::toCount($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                0
            ],
            [
                [0],
                1
            ],
            [
                [null],
                1
            ],
            [
                [''],
                1
            ],
            [
                ['', null],
                2
            ],
            [
                [1, 2, 3],
                3
            ],
            [
                [[1], '2', 3],
                3
            ],
        ];
    }

    /**
     * @test         toCount generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testGenerators(\Generator $data, $expected)
    {
        // When
        $result = Reduce::toCount($data);

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
                0
            ],
            [
                $gen([0]),
                1
            ],
            [
                $gen([null]),
                1
            ],
            [
                $gen(['']),
                1
            ],
            [
                $gen(['', null]),
                2
            ],
            [
                $gen([1, 2, 3]),
                3
            ],
            [
                $gen([[1], '2', 3]),
                3
            ],
        ];
    }

    /**
     * @test         toCount iterators
     * @dataProvider dataProviderForIterators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testIterators(\Iterator $data, $expected)
    {
        // When
        $result = Reduce::toCount($data);

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
                0
            ],
            [
                $iter([0]),
                1
            ],
            [
                $iter([null]),
                1
            ],
            [
                $iter(['']),
                1
            ],
            [
                $iter(['', null]),
                2
            ],
            [
                $iter([1, 2, 3]),
                3
            ],
            [
                $iter([[1], '2', 3]),
                3
            ],
        ];
    }

    /**
     * @test         toCount traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        mixed $expected
     */
    public function testTraversables(\Traversable $data, $expected)
    {
        // When
        $result = Reduce::toCount($data);

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
                0
            ],
            [
                $trav([0]),
                1
            ],
            [
                $trav([null]),
                1
            ],
            [
                $trav(['']),
                1
            ],
            [
                $trav(['', null]),
                2
            ],
            [
                $trav([1, 2, 3]),
                3
            ],
            [
                $trav([[1], '2', 3]),
                3
            ],
        ];
    }

    /**
     * @test         toCount countables
     * @dataProvider dataProviderForCountables
     * @param        CountableIteratorAggregateFixture $data
     * @param        int|float $expected
     */
    public function testCountables(CountableIteratorAggregateFixture $data, $expected)
    {
        // When
        $result = Reduce::toCount($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForCountables(): array
    {
        $trav = static function (array $data) {
            return new CountableIteratorAggregateFixture($data);
        };

        return [
            [
                $trav([]),
                0
            ],
            [
                $trav([0]),
                1
            ],
            [
                $trav([null]),
                1
            ],
            [
                $trav(['']),
                1
            ],
            [
                $trav(['', null]),
                2
            ],
            [
                $trav([1, 2, 3]),
                3
            ],
            [
                $trav([[1], '2', 3]),
                3
            ],
        ];
    }
}

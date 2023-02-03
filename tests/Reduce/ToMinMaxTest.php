<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToMinMaxTest extends \PHPUnit\Framework\TestCase
{
    protected const ROUND_PRECISION = 0.0001;

    /**
     * @test         toMinMax array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        callable|null $comparator
     * @param        array $expected
     */
    public function testArray(array $data, ?callable $comparator, array $expected)
    {
        // When
        $result = Reduce::toMinMax($data, $comparator);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::ROUND_PRECISION);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                null,
                [null, null],
            ],
            [
                [],
                fn ($item) => $item,
                [null, null],
            ],
            [
                [],
                fn ($item) => -$item,
                [null, null],
            ],
            [
                [0],
                null,
                [0, 0],
            ],
            [
                [0],
                fn ($item) => $item,
                [0, 0],
            ],
            [
                [0],
                fn ($item) => -$item,
                [0, 0],
            ],
            [
                [1],
                null,
                [1, 1],
            ],
            [
                [1],
                fn ($item) => $item,
                [1, 1],
            ],
            [
                [1],
                fn ($item) => -$item,
                [1, 1],
            ],
            [
                [-1],
                null,
                [-1, -1],
            ],
            [
                [-1],
                fn ($item) => $item,
                [-1, -1],
            ],
            [
                [-1],
                fn ($item) => -$item,
                [-1, -1],
            ],
            [
                [-1, -3, -5],
                null,
                [-5, -1],
            ],
            [
                [-1, -3, -5],
                fn ($item) => $item,
                [-5, -1],
            ],
            [
                [-1, -3, -5],
                fn ($item) => -$item,
                [-1, -5],
            ],
            [
                [3, 1, 2, -3, -1, -2],
                null,
                [-3, 3],
            ],
            [
                [3, 1, 2, -3, -1, -2],
                fn ($item) => $item,
                [-3, 3],
            ],
            [
                [3, 1, 2, -3, -1, -2],
                fn ($item) => -$item,
                [3, -3],
            ],
            [
                [2.2, 3.3, 1.1],
                null,
                [1.1, 3.3],
            ],
            [
                [2.2, 3.3, 1.1],
                fn ($item) => $item,
                [1.1, 3.3],
            ],
            [
                [2.2, 3.3, 1.1],
                fn ($item) => -$item,
                [3.3, 1.1],
            ],
            [
                [2, 3.3, 1.1],
                null,
                [1.1, 3.3],
            ],
            [
                [2, 3.3, 1.1],
                fn ($item) => $item,
                [1.1, 3.3],
            ],
            [
                [2, 3.3, 1.1],
                fn ($item) => -$item,
                [3.3, 1.1],
            ],
            [
                [2.2, -3.3, -1.1, 2.2, 5.5],
                null,
                [-3.3, 5.5],
            ],
            [
                [2.2, -3.3, -1.1, 2.2, 5.5],
                fn ($item) => $item,
                [-3.3, 5.5],
            ],
            [
                [2.2, -3.3, -1.1, 2.2, 5.5],
                fn ($item) => -$item,
                [5.5, -3.3],
            ],
            [
                ['2.2', '-3.3', '-1.1', '2.2', '5.5'],
                null,
                [-3.3, 5.5],
            ],
            [
                ['2.2', '-3.3', '-1.1', '2.2', '5.5'],
                fn ($item) => $item,
                [-3.3, 5.5],
            ],
            [
                ['2.2', '-3.3', '-1.1', '2.2', '5.5'],
                fn ($item) => -$item,
                [5.5, -3.3],
            ],
            [
                ['3', '4', '1'],
                null,
                [1, 4],
            ],
            [
                ['3', '4', '1'],
                fn ($item) => $item,
                [1, 4],
            ],
            [
                ['3', '4', '1'],
                fn ($item) => -$item,
                [4, 1],
            ],
            [
                [2, -3.3, '-1.1', 2.2, '5'],
                null,
                [-3.3, 5],
            ],
            [
                [2, -3.3, '-1.1', 2.2, '5'],
                fn ($item) => $item,
                [-3.3, 5],
            ],
            [
                [2, -3.3, '-1.1', 2.2, '5'],
                fn ($item) => -$item,
                [5, -3.3],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                null,
                [[1, 2, 3], [2, 1, 3]],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($item) => $item,
                [[1, 2, 3], [2, 1, 3]],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($item) => $item[1],
                [[2, 0, 3], [1, 2, 3]],
            ],
            [
                [[1, 2, 3], [2, 0, 3], [2, 1, 3]],
                fn ($item) => -$item[1],
                [[1, 2, 3], [2, 0, 3]],
            ],
        ];
    }

    /**
     * @test         toMinMax generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        callable|null $comparator
     * @param        array $expected
     */
    public function testGenerators(\Generator $data, ?callable $comparator, array $expected)
    {
        // When
        $result = Reduce::toMinMax($data, $comparator);

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
                null,
                [null, null],
            ],
            [
                $gen([]),
                fn ($item) => $item,
                [null, null],
            ],
            [
                $gen([]),
                fn ($item) => -$item,
                [null, null],
            ],
            [
                $gen([0]),
                null,
                [0, 0],
            ],
            [
                $gen([0]),
                fn ($item) => $item,
                [0, 0],
            ],
            [
                $gen([0]),
                fn ($item) => -$item,
                [0, 0],
            ],
            [
                $gen([1]),
                null,
                [1, 1],
            ],
            [
                $gen([1]),
                fn ($item) => $item,
                [1, 1],
            ],
            [
                $gen([1]),
                fn ($item) => -$item,
                [1, 1],
            ],
            [
                $gen([-1]),
                null,
                [-1, -1],
            ],
            [
                $gen([-1]),
                fn ($item) => $item,
                [-1, -1],
            ],
            [
                $gen([-1]),
                fn ($item) => -$item,
                [-1, -1],
            ],
            [
                $gen([-1, -3, -5]),
                null,
                [-5, -1],
            ],
            [
                $gen([-1, -3, -5]),
                fn ($item) => $item,
                [-5, -1],
            ],
            [
                $gen([-1, -3, -5]),
                fn ($item) => -$item,
                [-1, -5],
            ],
            [
                $gen([3, 1, 2, -3, -1, -2]),
                null,
                [-3, 3],
            ],
            [
                $gen([3, 1, 2, -3, -1, -2]),
                fn ($item) => $item,
                [-3, 3],
            ],
            [
                $gen([3, 1, 2, -3, -1, -2]),
                fn ($item) => -$item,
                [3, -3],
            ],
            [
                $gen([2.2, 3.3, 1.1]),
                null,
                [1.1, 3.3],
            ],
            [
                $gen([2.2, 3.3, 1.1]),
                fn ($item) => $item,
                [1.1, 3.3],
            ],
            [
                $gen([2.2, 3.3, 1.1]),
                fn ($item) => -$item,
                [3.3, 1.1],
            ],
            [
                $gen([2, 3.3, 1.1]),
                null,
                [1.1, 3.3],
            ],
            [
                $gen([2, 3.3, 1.1]),
                fn ($item) => $item,
                [1.1, 3.3],
            ],
            [
                $gen([2, 3.3, 1.1]),
                fn ($item) => -$item,
                [3.3, 1.1],
            ],
            [
                $gen([2.2, -3.3, -1.1, 2.2, 5.5]),
                null,
                [-3.3, 5.5],
            ],
            [
                $gen([2.2, -3.3, -1.1, 2.2, 5.5]),
                fn ($item) => $item,
                [-3.3, 5.5],
            ],
            [
                $gen([2.2, -3.3, -1.1, 2.2, 5.5]),
                fn ($item) => -$item,
                [5.5, -3.3],
            ],
            [
                $gen(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                null,
                [-3.3, 5.5],
            ],
            [
                $gen(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                fn ($item) => $item,
                [-3.3, 5.5],
            ],
            [
                $gen(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                fn ($item) => -$item,
                [5.5, -3.3],
            ],
            [
                $gen(['3', '4', '1']),
                null,
                [1, 4],
            ],
            [
                $gen(['3', '4', '1']),
                fn ($item) => $item,
                [1, 4],
            ],
            [
                $gen(['3', '4', '1']),
                fn ($item) => -$item,
                [4, 1],
            ],
            [
                $gen([2, -3.3, '-1.1', 2.2, '5']),
                null,
                [-3.3, 5],
            ],
            [
                $gen([2, -3.3, '-1.1', 2.2, '5']),
                fn ($item) => $item,
                [-3.3, 5],
            ],
            [
                $gen([2, -3.3, '-1.1', 2.2, '5']),
                fn ($item) => -$item,
                [5, -3.3],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                null,
                [[1, 2, 3], [2, 1, 3]],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item,
                [[1, 2, 3], [2, 1, 3]],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item[1],
                [[2, 0, 3], [1, 2, 3]],
            ],
            [
                $gen([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => -$item[1],
                [[1, 2, 3], [2, 0, 3]],
            ],
        ];
    }

    /**
     * @test         toMinMax iterators
     * @dataProvider dataProviderForIterators
     * @param        \Generator $data
     * @param        callable|null $comparator
     * @param        array $expected
     */
    public function testIterators(\Iterator $data, ?callable $comparator, array $expected)
    {
        // When
        $result = Reduce::toMinMax($data, $comparator);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::ROUND_PRECISION);
    }

    public function dataProviderForIterators(): array
    {
        $trav = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $trav([]),
                null,
                [null, null],
            ],
            [
                $trav([]),
                fn ($item) => $item,
                [null, null],
            ],
            [
                $trav([]),
                fn ($item) => -$item,
                [null, null],
            ],
            [
                $trav([0]),
                null,
                [0, 0],
            ],
            [
                $trav([0]),
                fn ($item) => $item,
                [0, 0],
            ],
            [
                $trav([0]),
                fn ($item) => -$item,
                [0, 0],
            ],
            [
                $trav([1]),
                null,
                [1, 1],
            ],
            [
                $trav([1]),
                fn ($item) => $item,
                [1, 1],
            ],
            [
                $trav([1]),
                fn ($item) => -$item,
                [1, 1],
            ],
            [
                $trav([-1]),
                null,
                [-1, -1],
            ],
            [
                $trav([-1]),
                fn ($item) => $item,
                [-1, -1],
            ],
            [
                $trav([-1]),
                fn ($item) => -$item,
                [-1, -1],
            ],
            [
                $trav([-1, -3, -5]),
                null,
                [-5, -1],
            ],
            [
                $trav([-1, -3, -5]),
                fn ($item) => $item,
                [-5, -1],
            ],
            [
                $trav([-1, -3, -5]),
                fn ($item) => -$item,
                [-1, -5],
            ],
            [
                $trav([3, 1, 2, -3, -1, -2]),
                null,
                [-3, 3],
            ],
            [
                $trav([3, 1, 2, -3, -1, -2]),
                fn ($item) => $item,
                [-3, 3],
            ],
            [
                $trav([3, 1, 2, -3, -1, -2]),
                fn ($item) => -$item,
                [3, -3],
            ],
            [
                $trav([2.2, 3.3, 1.1]),
                null,
                [1.1, 3.3],
            ],
            [
                $trav([2.2, 3.3, 1.1]),
                fn ($item) => $item,
                [1.1, 3.3],
            ],
            [
                $trav([2.2, 3.3, 1.1]),
                fn ($item) => -$item,
                [3.3, 1.1],
            ],
            [
                $trav([2, 3.3, 1.1]),
                null,
                [1.1, 3.3],
            ],
            [
                $trav([2, 3.3, 1.1]),
                fn ($item) => $item,
                [1.1, 3.3],
            ],
            [
                $trav([2, 3.3, 1.1]),
                fn ($item) => -$item,
                [3.3, 1.1],
            ],
            [
                $trav([2.2, -3.3, -1.1, 2.2, 5.5]),
                null,
                [-3.3, 5.5],
            ],
            [
                $trav([2.2, -3.3, -1.1, 2.2, 5.5]),
                fn ($item) => $item,
                [-3.3, 5.5],
            ],
            [
                $trav([2.2, -3.3, -1.1, 2.2, 5.5]),
                fn ($item) => -$item,
                [5.5, -3.3],
            ],
            [
                $trav(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                null,
                [-3.3, 5.5],
            ],
            [
                $trav(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                fn ($item) => $item,
                [-3.3, 5.5],
            ],
            [
                $trav(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                fn ($item) => -$item,
                [5.5, -3.3],
            ],
            [
                $trav(['3', '4', '1']),
                null,
                [1, 4],
            ],
            [
                $trav(['3', '4', '1']),
                fn ($item) => $item,
                [1, 4],
            ],
            [
                $trav(['3', '4', '1']),
                fn ($item) => -$item,
                [4, 1],
            ],
            [
                $trav([2, -3.3, '-1.1', 2.2, '5']),
                null,
                [-3.3, 5],
            ],
            [
                $trav([2, -3.3, '-1.1', 2.2, '5']),
                fn ($item) => $item,
                [-3.3, 5],
            ],
            [
                $trav([2, -3.3, '-1.1', 2.2, '5']),
                fn ($item) => -$item,
                [5, -3.3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                null,
                [[1, 2, 3], [2, 1, 3]],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item,
                [[1, 2, 3], [2, 1, 3]],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item[1],
                [[2, 0, 3], [1, 2, 3]],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => -$item[1],
                [[1, 2, 3], [2, 0, 3]],
            ],
        ];
    }

    /**
     * @test         toMinMax traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        callable|null $comparator
     * @param        array $expected
     */
    public function testTraversables(\Traversable $data, ?callable $comparator, array $expected)
    {
        // When
        $result = Reduce::toMinMax($data, $comparator);

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
                null,
                [null, null],
            ],
            [
                $trav([]),
                fn ($item) => $item,
                [null, null],
            ],
            [
                $trav([]),
                fn ($item) => -$item,
                [null, null],
            ],
            [
                $trav([0]),
                null,
                [0, 0],
            ],
            [
                $trav([0]),
                fn ($item) => $item,
                [0, 0],
            ],
            [
                $trav([0]),
                fn ($item) => -$item,
                [0, 0],
            ],
            [
                $trav([1]),
                null,
                [1, 1],
            ],
            [
                $trav([1]),
                fn ($item) => $item,
                [1, 1],
            ],
            [
                $trav([1]),
                fn ($item) => -$item,
                [1, 1],
            ],
            [
                $trav([-1]),
                null,
                [-1, -1],
            ],
            [
                $trav([-1]),
                fn ($item) => $item,
                [-1, -1],
            ],
            [
                $trav([-1]),
                fn ($item) => -$item,
                [-1, -1],
            ],
            [
                $trav([-1, -3, -5]),
                null,
                [-5, -1],
            ],
            [
                $trav([-1, -3, -5]),
                fn ($item) => $item,
                [-5, -1],
            ],
            [
                $trav([-1, -3, -5]),
                fn ($item) => -$item,
                [-1, -5],
            ],
            [
                $trav([3, 1, 2, -3, -1, -2]),
                null,
                [-3, 3],
            ],
            [
                $trav([3, 1, 2, -3, -1, -2]),
                fn ($item) => $item,
                [-3, 3],
            ],
            [
                $trav([3, 1, 2, -3, -1, -2]),
                fn ($item) => -$item,
                [3, -3],
            ],
            [
                $trav([2.2, 3.3, 1.1]),
                null,
                [1.1, 3.3],
            ],
            [
                $trav([2.2, 3.3, 1.1]),
                fn ($item) => $item,
                [1.1, 3.3],
            ],
            [
                $trav([2.2, 3.3, 1.1]),
                fn ($item) => -$item,
                [3.3, 1.1],
            ],
            [
                $trav([2, 3.3, 1.1]),
                null,
                [1.1, 3.3],
            ],
            [
                $trav([2, 3.3, 1.1]),
                fn ($item) => $item,
                [1.1, 3.3],
            ],
            [
                $trav([2, 3.3, 1.1]),
                fn ($item) => -$item,
                [3.3, 1.1],
            ],
            [
                $trav([2.2, -3.3, -1.1, 2.2, 5.5]),
                null,
                [-3.3, 5.5],
            ],
            [
                $trav([2.2, -3.3, -1.1, 2.2, 5.5]),
                fn ($item) => $item,
                [-3.3, 5.5],
            ],
            [
                $trav([2.2, -3.3, -1.1, 2.2, 5.5]),
                fn ($item) => -$item,
                [5.5, -3.3],
            ],
            [
                $trav(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                null,
                [-3.3, 5.5],
            ],
            [
                $trav(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                fn ($item) => $item,
                [-3.3, 5.5],
            ],
            [
                $trav(['2.2', '-3.3', '-1.1', '2.2', '5.5']),
                fn ($item) => -$item,
                [5.5, -3.3],
            ],
            [
                $trav(['3', '4', '1']),
                null,
                [1, 4],
            ],
            [
                $trav(['3', '4', '1']),
                fn ($item) => $item,
                [1, 4],
            ],
            [
                $trav(['3', '4', '1']),
                fn ($item) => -$item,
                [4, 1],
            ],
            [
                $trav([2, -3.3, '-1.1', 2.2, '5']),
                null,
                [-3.3, 5],
            ],
            [
                $trav([2, -3.3, '-1.1', 2.2, '5']),
                fn ($item) => $item,
                [-3.3, 5],
            ],
            [
                $trav([2, -3.3, '-1.1', 2.2, '5']),
                fn ($item) => -$item,
                [5, -3.3],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                null,
                [[1, 2, 3], [2, 1, 3]],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item,
                [[1, 2, 3], [2, 1, 3]],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => $item[1],
                [[2, 0, 3], [1, 2, 3]],
            ],
            [
                $trav([[1, 2, 3], [2, 0, 3], [2, 1, 3]]),
                fn ($item) => -$item[1],
                [[1, 2, 3], [2, 0, 3]],
            ],
        ];
    }
}

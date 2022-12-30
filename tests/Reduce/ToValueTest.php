<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToValueTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         toValue array - sum reducer
     * @dataProvider dataProviderForArrayWithSumReducer
     * @param        array    $data
     * @param        mixed    $initialValue
     * @param        mixed    $expected
     */
    public function testArrayWithSumReducer(array $data, $initialValue, $expected)
    {
        // Given
        $sum = static function ($carry, $datum) {
            return $carry + $datum;
        };

        // When
        $result = Reduce::toValue($data, $sum, $initialValue);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArrayWithSumReducer(): array
    {
        return [
            [
                [],
                null,
                null
            ],
            [
                [],
                0,
                0
            ],
            [
                [],
                1,
                1
            ],
            [
                [],
                'a',
                'a'
            ],
            [
                [0],
                null,
                0
            ],
            [
                [null],
                null,
                0
            ],
            [
                [null, 1, 2],
                null,
                3
            ],
            [
                [1, 2, 3],
                null,
                6
            ],
        ];
    }

    /**
     * @test         toValue array - concat reducer
     * @dataProvider dataProviderForArrayWithConcatReducer
     * @param        array    $data
     * @param        mixed    $initialValue
     * @param        mixed    $expected
     */
    public function testArrayWithConcatReducer(array $data, $initialValue, $expected)
    {
        // Given
        $concat = static function ($carry, $datum) {
            return $carry . $datum;
        };

        // When
        $result = Reduce::toValue($data, $concat, $initialValue);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArrayWithConcatReducer(): array
    {
        return [
            [
                [],
                null,
                null
            ],
            [
                [],
                0,
                0
            ],
            [
                [],
                1,
                1
            ],
            [
                [],
                'a',
                'a'
            ],
            [
                [0],
                null,
                '0'
            ],
            [
                [null],
                null,
                ''
            ],
            [
                ['a', 'b'],
                null,
                'ab'
            ],
            [
                ['one', 'TWO', '3', '四', 'cinco'],
                null,
                'oneTWO3四cinco'
            ],
            [
                ['one', 'TWO', '3', '四', 'cinco'],
                '0',
                '0oneTWO3四cinco'
            ],
            [
                [null, 2, 3],
                null,
                '23',
            ],
        ];
    }

    /**
     * @test         toValue generators - sum reducer
     * @dataProvider dataProviderForGeneratorsWithSumReducer
     * @param        \Generator $data
     * @param        mixed      $initialValue
     * @param        mixed      $expected
     */
    public function testGeneratorsWithSumReducer(\Generator $data, $initialValue, $expected)
    {
        // Given
        $sum = static function ($carry, $datum) {
            return $carry + $datum;
        };

        // When
        $result = Reduce::toValue($data, $sum, $initialValue);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGeneratorsWithSumReducer(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([]),
                null,
                null
            ],
            [
                $gen([]),
                0,
                0
            ],
            [
                $gen([]),
                1,
                1
            ],
            [
                $gen([]),
                'a',
                'a'
            ],
            [
                $gen([0]),
                null,
                0
            ],
            [
                $gen([null]),
                null,
                0
            ],
            [
                $gen([null, 1, 2]),
                null,
                3
            ],
            [
                $gen([1, 2, 3]),
                null,
                6
            ],
        ];
    }

    /**
     * @test         toValue generators - concat reducer
     * @dataProvider dataProviderForGeneratorsWithConcatReducer
     * @param        \Generator $data
     * @param        mixed      $initialValue
     * @param        mixed      $expected
     */
    public function testGeneratorsWithConcatReducer(\Generator $data, $initialValue, $expected)
    {
        // Given
        $concat = static function ($carry, $datum) {
            return $carry . $datum;
        };

        // When
        $result = Reduce::toValue($data, $concat, $initialValue);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGeneratorsWithConcatReducer(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([]),
                null,
                null
            ],
            [
                $gen([]),
                0,
                0
            ],
            [
                $gen([]),
                1,
                1
            ],
            [
                $gen([]),
                'a',
                'a'
            ],
            [
                $gen([0]),
                null,
                '0'
            ],
            [
                $gen([null]),
                null,
                ''
            ],
            [
                $gen(['a', 'b']),
                null,
                'ab'
            ],
            [
                $gen(['one', 'TWO', '3', '四', 'cinco']),
                null,
                'oneTWO3四cinco'
            ],
            [
                $gen(['one', 'TWO', '3', '四', 'cinco']),
                '0',
                '0oneTWO3四cinco'
            ],
            [
                $gen([null, 2, 3]),
                null,
                '23',
            ],
        ];
    }

    /**
     * @test         toValue iterators - sum reducer
     * @dataProvider dataProviderForIteratorsWithSumReducer
     * @param        \Generator $data
     * @param        mixed      $initialValue
     * @param        mixed      $expected
     */
    public function testIteratorsWithSumReducer(\Iterator $data, $initialValue, $expected)
    {
        // Given
        $sum = static function ($carry, $datum) {
            return $carry + $datum;
        };

        // When
        $result = Reduce::toValue($data, $sum, $initialValue);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIteratorsWithSumReducer(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $iter([]),
                null,
                null
            ],
            [
                $iter([]),
                0,
                0
            ],
            [
                $iter([]),
                1,
                1
            ],
            [
                $iter([]),
                'a',
                'a'
            ],
            [
                $iter([0]),
                null,
                0
            ],
            [
                $iter([null]),
                null,
                0
            ],
            [
                $iter([null, 1, 2]),
                null,
                3
            ],
            [
                $iter([1, 2, 3]),
                null,
                6
            ],
        ];
    }

    /**
     * @test         toValue iterators - concat reducer
     * @dataProvider dataProviderForIteratorsWithConcatReducer
     * @param        \Generator $data
     * @param        mixed      $initialValue
     * @param        mixed      $expected
     */
    public function testIteratorsWithConcatReducer(\Iterator $data, $initialValue, $expected)
    {
        // Given
        $concat = static function ($carry, $datum) {
            return $carry . $datum;
        };

        // When
        $result = Reduce::toValue($data, $concat, $initialValue);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIteratorsWithConcatReducer(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $iter([]),
                null,
                null
            ],
            [
                $iter([]),
                0,
                0
            ],
            [
                $iter([]),
                1,
                1
            ],
            [
                $iter([]),
                'a',
                'a'
            ],
            [
                $iter([0]),
                null,
                '0'
            ],
            [
                $iter([null]),
                null,
                ''
            ],
            [
                $iter(['a', 'b']),
                null,
                'ab'
            ],
            [
                $iter(['one', 'TWO', '3', '四', 'cinco']),
                null,
                'oneTWO3四cinco'
            ],
            [
                $iter(['one', 'TWO', '3', '四', 'cinco']),
                '0',
                '0oneTWO3四cinco'
            ],
            [
                $iter([null, 2, 3]),
                null,
                '23',
            ],
        ];
    }

    /**
     * @test         toValue traversables - sum reducer
     * @dataProvider dataProviderForTraversablesWithSumReducer
     * @param        \Traversable $data
     * @param        mixed        $initialValue
     * @param        mixed        $expected
     */
    public function testTraversablesWithSumReducer(\Traversable $data, $initialValue, $expected)
    {
        // Given
        $sum = static function ($carry, $datum) {
            return $carry + $datum;
        };

        // When
        $result = Reduce::toValue($data, $sum, $initialValue);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversablesWithSumReducer(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([]),
                null,
                null
            ],
            [
                $trav([]),
                0,
                0
            ],
            [
                $trav([]),
                1,
                1
            ],
            [
                $trav([]),
                'a',
                'a'
            ],
            [
                $trav([0]),
                null,
                0
            ],
            [
                $trav([null]),
                null,
                0
            ],
            [
                $trav([null, 1, 2]),
                null,
                3
            ],
            [
                $trav([1, 2, 3]),
                null,
                6
            ],
        ];
    }

    /**
     * @test         toValue traversables - concat reducer
     * @dataProvider dataProviderForTraversablesWithConcatReducer
     * @param        \Traversable $data
     * @param        mixed        $initialValue
     * @param        mixed        $expected
     */
    public function testTraversablesWithConcatReducer(\Traversable $data, $initialValue, $expected)
    {
        // Given
        $concat = static function ($carry, $datum) {
            return $carry . $datum;
        };

        // When
        $result = Reduce::toValue($data, $concat, $initialValue);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversablesWithConcatReducer(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([]),
                null,
                null
            ],
            [
                $trav([]),
                0,
                0
            ],
            [
                $trav([]),
                1,
                1
            ],
            [
                $trav([]),
                'a',
                'a'
            ],
            [
                $trav([0]),
                null,
                '0'
            ],
            [
                $trav([null]),
                null,
                ''
            ],
            [
                $trav(['a', 'b']),
                null,
                'ab'
            ],
            [
                $trav(['one', 'TWO', '3', '四', 'cinco']),
                null,
                'oneTWO3四cinco'
            ],
            [
                $trav(['one', 'TWO', '3', '四', 'cinco']),
                '0',
                '0oneTWO3四cinco'
            ],
            [
                $trav([null, 2, 3]),
                null,
                '23',
            ],
        ];
    }
}

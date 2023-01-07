<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class SameTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testEmpty(): void
    {
        // Given: $data

        // When
        $result = Summary::same();

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         Same array true
     * @dataProvider dataProviderForArrayTrue
     * @param        iterable ...$iterables
     */
    public function testArrayTrue(iterable ...$iterables)
    {
        // Given: $data

        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForArrayTrue(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [[]],
            [[1]],
            [[1, 2, 3]],
            [[], []],
            [[], $gen([])],
            [[], $iter([])],
            [[], $trav([])],

            [[], [], []],
            [[], $gen([]), []],
            [[], $iter([]), []],
            [[], $trav([]), []],
            [[], [], $gen([])],
            [[], [], $iter([])],
            [[], [], $trav([])],
            [[], $gen([]), $iter([])],
            [[], $iter([]), $trav([])],

            [[], [], [], [], []],
            [[], $gen([]), $iter([]), $trav([]), []],
            [[], [], $gen([]), $iter([]), $trav([])],

            [[1], [1]],
            [[1], $gen([1])],
            [[1], $iter([1])],
            [[1], $trav([1])],

            [[1], [1], [1]],
            [[1], $gen([1]), [1]],
            [[1], $iter([1]), [1]],
            [[1], $trav([1]), [1]],
            [[1], [1], $gen([1])],
            [[1], [1], $iter([1])],
            [[1], [1], $trav([1])],
            [[1], $gen([1]), $iter([1])],
            [[1], $iter([1]), $trav([1])],

            [[1, 2], [1, 2], [1, 2], [1, 2]],
            [[1, 2], [1, 2], $gen([1, 2]), [1, 2]],
            [[1, 2], [1, 2], $iter([1, 2]), [1, 2]],
            [[1, 2], [1, 2], $trav([1, 2]), [1, 2]],
            [[1, 2], [1, 2], $gen([1, 2]), $iter([1, 2])],
            [[1, 2], [1, 2], $iter([1, 2]), $trav([1, 2])],

            [[1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 3]],
            [[1, 2, 3], [1, 2, 3], $gen([1, 2, 3]), [1, 2, 3]],
            [[1, 2, 3], [1, 2, 3], $iter([1, 2, 3]), [1, 2, 3]],
            [[1, 2, 3], [1, 2, 3], $trav([1, 2, 3]), [1, 2, 3]],

            [['a', 2], ['a', 2], ['a', 2], ['a', 2]],
            [['a', 2], $gen(['a', 2]), ['a', 2], ['a', 2]],
            [['a', 2], $iter(['a', 2]), ['a', 2], ['a', 2]],
            [['a', 2], $trav(['a', 2]), ['a', 2], ['a', 2]],

            [[1, null], [1, null], [1, null], [1, null]],
            [[1, null], [1, null], [1, null], $gen([1, null])],
            [[1, null], [1, null], [1, null], $iter([1, null])],
            [[1, null], [1, null], [1, null], $trav([1, null])],
        ];
    }

    /**
     * @test         Same array true
     * @dataProvider dataProviderForArrayFalse
     * @param        iterable ...$iterables
     */
    public function testArrayFalse(iterable ...$iterables)
    {
        // Given: $data

        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForArrayFalse(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [[], [1]],
            [[], $gen([1])],
            [[], $iter([1])],
            [[], $trav([1])],

            [[1], []],
            [[1], $gen([])],
            [[1], $iter([])],
            [[1], $trav([])],

            [['1'], [1], [1], [1]],
            [['1'], $gen([1]), [1], [1]],
            [['1'], [1], $iter([1]), [1]],
            [['1'], [1], [1], $trav([1])],

            [[1], ['1']],
            [[1], $gen(['1'])],
            [[1], $iter(['1'])],
            [[1], $trav(['1'])],

            [[1], [2]],
            [[1], $gen([2])],
            [[1], $iter([2])],
            [[1], $trav([2])],

            [[1], ['1'], [1]],
            [[1], $gen(['1']), [1]],
            [[1], $iter(['1']), [1]],
            [[1], $trav(['1']), [1]],
            [[1], ['1'], $gen([1])],
            [[1], ['1'], $iter([1])],
            [[1], ['1'], $trav([1])],
            [[1], $iter(['1']), $gen([1])],
            [[1], $trav(['1']), $iter([1])],
            [[1], $gen(['1']), $trav([1])],

            [[1], [1], ['1']],
            [[1], [1], [2], [1]],
            [[1], [1], ['1'], [1]],
            [[1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, '3']],
            [[1, 2, 3], [1, 2, 3], [1, 2, 3], [1, 2, 1]],
            [[1], [1, null], [1, null], [1]],
            [[1], [1, null], [1, null], [1, null]],
            [[1, null], [1], [1], [1]],
            [[1, null], [1], [1, null], [1]],
        ];
    }

    /**
     * @test         Same generators true
     * @dataProvider dataProviderForGeneratorsTrue
     * @param        array<\Generator> $iterables
     */
    public function testGeneratorsTrue(iterable ...$iterables)
    {
        // Given: $data

        // When
        $result = Summary::same($iterables);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForGeneratorsTrue(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [$gen([]), []],
            [$gen([]), $gen([])],
            [$gen([]), $iter([])],
            [$gen([]), $trav([])],

            [$gen([]), [], []],
            [$gen([]), $gen([]), []],
            [$gen([]), $iter([]), []],
            [$gen([]), $trav([]), []],
            [$gen([]), [], $gen([])],
            [$gen([]), [], $iter([])],
            [$gen([]), [], $trav([])],
            [$gen([]), $gen([]), $iter([])],
            [$gen([]), $iter([]), $trav([])],

            [$gen([]), [], [], [], []],
            [$gen([]), $gen([]), $iter([]), $trav([]), []],
            [$gen([]), [], $gen([]), $iter([]), $trav([])],

            [$gen([1]), [1]],
            [$gen([1]), $gen([1])],
            [$gen([1]), $iter([1])],
            [$gen([1]), $trav([1])],

            [$gen([1]), [1], [1]],
            [$gen([1]), $gen([1]), [1]],
            [$gen([1]), $iter([1]), [1]],
            [$gen([1]), $trav([1]), [1]],
            [$gen([1]), [1], $gen([1])],
            [$gen([1]), [1], $iter([1])],
            [$gen([1]), [1], $trav([1])],
            [$gen([1]), $gen([1]), $iter([1])],
            [$gen([1]), $iter([1]), $trav([1])],

            [$gen([1, 2]), [1, 2], [1, 2], [1, 2]],
            [$gen([1, 2]), [1, 2], $gen([1, 2]), [1, 2]],
            [$gen([1, 2]), [1, 2], $iter([1, 2]), [1, 2]],
            [$gen([1, 2]), [1, 2], $trav([1, 2]), [1, 2]],
            [$gen([1, 2]), [1, 2], $gen([1, 2]), $iter([1, 2])],
            [$gen([1, 2]), [1, 2], $iter([1, 2]), $trav([1, 2])],

            [$gen([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, 3]],
            [$gen([1, 2, 3]), [1, 2, 3], $gen([1, 2, 3]), [1, 2, 3]],
            [$gen([1, 2, 3]), [1, 2, 3], $iter([1, 2, 3]), [1, 2, 3]],
            [$gen([1, 2, 3]), [1, 2, 3], $trav([1, 2, 3]), [1, 2, 3]],

            [$gen(['a', 2]), ['a', 2], ['a', 2], ['a', 2]],
            [$gen(['a', 2]), $gen(['a', 2]), ['a', 2], ['a', 2]],
            [$gen(['a', 2]), $iter(['a', 2]), ['a', 2], ['a', 2]],
            [$gen(['a', 2]), $trav(['a', 2]), ['a', 2], ['a', 2]],

            [$gen([1, null]), [1, null], [1, null], [1, null]],
            [$gen([1, null]), [1, null], [1, null], $gen([1, null])],
            [$gen([1, null]), [1, null], [1, null], $iter([1, null])],
            [$gen([1, null]), [1, null], [1, null], $trav([1, null])],
        ];
    }

    /**
     * @test         Same generators true
     * @dataProvider dataProviderForGeneratorsFalse
     * @param        iterable ...$iterables
     */
    public function testGeneratorsFalse(iterable ...$iterables)
    {
        // Given: $data

        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForGeneratorsFalse(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [$gen([]), [1]],
            [$gen([]), $gen([1])],
            [$gen([]), $iter([1])],
            [$gen([]), $trav([1])],

            [$gen([1]), []],
            [$gen([1]), $gen([])],
            [$gen([1]), $iter([])],
            [$gen([1]), $trav([])],

            [$gen(['1']), [1], [1], [1]],
            [$gen(['1']), $gen([1]), [1], [1]],
            [$gen(['1']), [1], $iter([1]), [1]],
            [$gen(['1']), [1], [1], $trav([1])],

            [$gen([1]), ['1']],
            [$gen([1]), $gen(['1'])],
            [$gen([1]), $iter(['1'])],
            [$gen([1]), $trav(['1'])],

            [$gen([1]), [2]],
            [$gen([1]), $gen([2])],
            [$gen([1]), $iter([2])],
            [$gen([1]), $trav([2])],

            [$gen([1]), ['1'], [1]],
            [$gen([1]), $gen(['1']), [1]],
            [$gen([1]), $iter(['1']), [1]],
            [$gen([1]), $trav(['1']), [1]],
            [$gen([1]), ['1'], $gen([1])],
            [$gen([1]), ['1'], $iter([1])],
            [$gen([1]), ['1'], $trav([1])],
            [$gen([1]), $iter(['1']), $gen([1])],
            [$gen([1]), $trav(['1']), $iter([1])],
            [$gen([1]), $gen(['1']), $trav([1])],

            [$gen([1]), [1], ['1']],
            [$gen([1]), [1], [2], [1]],
            [$gen([1]), [1], ['1'], [1]],
            [$gen([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, '3']],
            [$gen([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, 1]],
            [$gen([1]), [1, null], [1, null], [1]],
            [$gen([1]), [1, null], [1, null], [1, null]],
            [$gen([1, null]), [1], [1], [1]],
            [$gen([1, null]), [1], [1, null], [1]],
        ];
    }

    /**
     * @test         Same iterators true
     * @dataProvider dataProviderForIteratorsTrue
     * @param        iterable ...$iterables
     */
    public function testIteratorsTrue(iterable ...$iterables)
    {
        // Given: $data

        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForIteratorsTrue(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [$iter([]), []],
            [$iter([]), $gen([])],
            [$iter([]), $iter([])],
            [$iter([]), $trav([])],

            [$iter([]), [], []],
            [$iter([]), $gen([]), []],
            [$iter([]), $iter([]), []],
            [$iter([]), $trav([]), []],
            [$iter([]), [], $gen([])],
            [$iter([]), [], $iter([])],
            [$iter([]), [], $trav([])],
            [$iter([]), $gen([]), $iter([])],
            [$iter([]), $iter([]), $trav([])],

            [$iter([]), [], [], [], []],
            [$iter([]), $gen([]), $iter([]), $trav([]), []],
            [$iter([]), [], $gen([]), $iter([]), $trav([])],

            [$iter([1]), [1]],
            [$iter([1]), $gen([1])],
            [$iter([1]), $iter([1])],
            [$iter([1]), $trav([1])],

            [$iter([1]), [1], [1]],
            [$iter([1]), $gen([1]), [1]],
            [$iter([1]), $iter([1]), [1]],
            [$iter([1]), $trav([1]), [1]],
            [$iter([1]), [1], $gen([1])],
            [$iter([1]), [1], $iter([1])],
            [$iter([1]), [1], $trav([1])],
            [$iter([1]), $gen([1]), $iter([1])],
            [$iter([1]), $iter([1]), $trav([1])],

            [$iter([1, 2]), [1, 2], [1, 2], [1, 2]],
            [$iter([1, 2]), [1, 2], $gen([1, 2]), [1, 2]],
            [$iter([1, 2]), [1, 2], $iter([1, 2]), [1, 2]],
            [$iter([1, 2]), [1, 2], $trav([1, 2]), [1, 2]],
            [$iter([1, 2]), [1, 2], $gen([1, 2]), $iter([1, 2])],
            [$iter([1, 2]), [1, 2], $iter([1, 2]), $trav([1, 2])],

            [$iter([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, 3]],
            [$iter([1, 2, 3]), [1, 2, 3], $gen([1, 2, 3]), [1, 2, 3]],
            [$iter([1, 2, 3]), [1, 2, 3], $iter([1, 2, 3]), [1, 2, 3]],
            [$iter([1, 2, 3]), [1, 2, 3], $trav([1, 2, 3]), [1, 2, 3]],

            [$iter(['a', 2]), ['a', 2], ['a', 2], ['a', 2]],
            [$iter(['a', 2]), $gen(['a', 2]), ['a', 2], ['a', 2]],
            [$iter(['a', 2]), $iter(['a', 2]), ['a', 2], ['a', 2]],
            [$iter(['a', 2]), $trav(['a', 2]), ['a', 2], ['a', 2]],

            [$iter([1, null]), [1, null], [1, null], [1, null]],
            [$iter([1, null]), [1, null], [1, null], $gen([1, null])],
            [$iter([1, null]), [1, null], [1, null], $iter([1, null])],
            [$iter([1, null]), [1, null], [1, null], $trav([1, null])],
        ];
    }

    /**
     * @test         Same iterators true
     * @dataProvider dataProviderForGeneratorsFalse
     * @param        iterable ...$iterables
     */
    public function testIteratorsFalse(iterable ...$iterables)
    {
        // Given: $data

        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForIteratorsFalse(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [$iter([]), [1]],
            [$iter([]), $gen([1])],
            [$iter([]), $iter([1])],
            [$iter([]), $trav([1])],

            [$iter([1]), []],
            [$iter([1]), $gen([])],
            [$iter([1]), $iter([])],
            [$iter([1]), $trav([])],

            [$iter(['1']), [1], [1], [1]],
            [$iter(['1']), $gen([1]), [1], [1]],
            [$iter(['1']), [1], $iter([1]), [1]],
            [$iter(['1']), [1], [1], $trav([1])],

            [$iter([1]), ['1']],
            [$iter([1]), $gen(['1'])],
            [$iter([1]), $iter(['1'])],
            [$iter([1]), $trav(['1'])],

            [$iter([1]), [2]],
            [$iter([1]), $gen([2])],
            [$iter([1]), $iter([2])],
            [$iter([1]), $trav([2])],

            [$iter([1]), ['1'], [1]],
            [$iter([1]), $gen(['1']), [1]],
            [$iter([1]), $iter(['1']), [1]],
            [$iter([1]), $trav(['1']), [1]],
            [$iter([1]), ['1'], $gen([1])],
            [$iter([1]), ['1'], $iter([1])],
            [$iter([1]), ['1'], $trav([1])],
            [$iter([1]), $iter(['1']), $gen([1])],
            [$iter([1]), $trav(['1']), $iter([1])],
            [$iter([1]), $gen(['1']), $trav([1])],

            [$iter([1]), [1], ['1']],
            [$iter([1]), [1], [2], [1]],
            [$iter([1]), [1], ['1'], [1]],
            [$iter([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, '3']],
            [$iter([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, 1]],
            [$iter([1]), [1, null], [1, null], [1]],
            [$iter([1]), [1, null], [1, null], [1, null]],
            [$iter([1, null]), [1], [1], [1]],
            [$iter([1, null]), [1], [1, null], [1]],
        ];
    }

    /**
     * @test         Same iterators true
     * @dataProvider dataProviderForTraversablesTrue
     * @param        iterable ...$iterables
     */
    public function testTraversablesTrue(iterable ...$iterables)
    {
        // Given: $data

        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForTraversablesTrue(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [$trav([]),        []],
            [$trav([]),        $gen([])],
            [$trav([]),        $iter([])],
            [$trav([]),        $trav([])],

            [$trav([]),        [], []],
            [$trav([]),        $gen([]), []],
            [$trav([]),        $iter([]), []],
            [$trav([]),        $trav([]), []],
            [$trav([]),        [], $gen([])],
            [$trav([]),        [], $iter([])],
            [$trav([]),        [], $trav([])],
            [$trav([]),        $gen([]), $iter([])],
            [$trav([]),        $iter([]), $trav([])],

            [$trav([]),        [], [], [], []],
            [$trav([]),        $gen([]), $iter([]), $trav([]), []],
            [$trav([]),        [], $gen([]), $iter([]), $trav([])],

            [$trav([1]),       [1]],
            [$trav([1]),       $gen([1])],
            [$trav([1]),       $iter([1])],
            [$trav([1]),       $trav([1])],

            [$trav([1]),       [1], [1]],
            [$trav([1]),       $gen([1]), [1]],
            [$trav([1]),       $iter([1]), [1]],
            [$trav([1]),       $trav([1]), [1]],
            [$trav([1]),       [1], $gen([1])],
            [$trav([1]),       [1], $iter([1])],
            [$trav([1]),       [1], $trav([1])],
            [$trav([1]),       $gen([1]), $iter([1])],
            [$trav([1]),       $iter([1]), $trav([1])],

            [$trav([1, 2]),    [1, 2], [1, 2], [1, 2]],
            [$trav([1, 2]),    [1, 2], $gen([1, 2]), [1, 2]],
            [$trav([1, 2]),    [1, 2], $iter([1, 2]), [1, 2]],
            [$trav([1, 2]),    [1, 2], $trav([1, 2]), [1, 2]],
            [$trav([1, 2]),    [1, 2], $gen([1, 2]), $iter([1, 2])],
            [$trav([1, 2]),    [1, 2], $iter([1, 2]), $trav([1, 2])],

            [$trav([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, 3]],
            [$trav([1, 2, 3]), [1, 2, 3], $gen([1, 2, 3]), [1, 2, 3]],
            [$trav([1, 2, 3]), [1, 2, 3], $iter([1, 2, 3]), [1, 2, 3]],
            [$trav([1, 2, 3]), [1, 2, 3], $trav([1, 2, 3]), [1, 2, 3]],

            [$trav(['a', 2]),  ['a', 2], ['a', 2], ['a', 2]],
            [$trav(['a', 2]),  $gen(['a', 2]), ['a', 2], ['a', 2]],
            [$trav(['a', 2]),  $iter(['a', 2]), ['a', 2], ['a', 2]],
            [$trav(['a', 2]),  $trav(['a', 2]), ['a', 2], ['a', 2]],

            [$trav([1, null]), [1, null], [1, null], [1, null]],
            [$trav([1, null]), [1, null], [1, null], $gen([1, null])],
            [$trav([1, null]), [1, null], [1, null], $iter([1, null])],
            [$trav([1, null]), [1, null], [1, null], $trav([1, null])],
        ];
    }

    /**
     * @test         Same iterators true
     * @dataProvider dataProviderForTraversablesFalse
     * @param        iterable ...$iterables
     */
    public function testTraversablesFalse(iterable ...$iterables)
    {
        // Given: $data

        // When
        $result = Summary::same(...$iterables);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForTraversablesFalse(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [$trav([]), [1]],
            [$trav([]), $gen([1])],
            [$trav([]), $iter([1])],
            [$trav([]), $trav([1])],

            [$trav([1]), []],
            [$trav([1]), $gen([])],
            [$trav([1]), $iter([])],
            [$trav([1]), $trav([])],

            [$trav(['1']), [1], [1], [1]],
            [$trav(['1']), $gen([1]), [1], [1]],
            [$trav(['1']), [1], $iter([1]), [1]],
            [$trav(['1']), [1], [1], $trav([1])],

            [$trav([1]), ['1']],
            [$trav([1]), $gen(['1'])],
            [$trav([1]), $iter(['1'])],
            [$trav([1]), $trav(['1'])],

            [$trav([1]), [2]],
            [$trav([1]), $gen([2])],
            [$trav([1]), $iter([2])],
            [$trav([1]), $trav([2])],

            [$trav([1]), ['1'], [1]],
            [$trav([1]), $gen(['1']), [1]],
            [$trav([1]), $iter(['1']), [1]],
            [$trav([1]), $trav(['1']), [1]],
            [$trav([1]), ['1'], $gen([1])],
            [$trav([1]), ['1'], $iter([1])],
            [$trav([1]), ['1'], $trav([1])],
            [$trav([1]), $iter(['1']), $gen([1])],
            [$trav([1]), $trav(['1']), $iter([1])],
            [$trav([1]), $gen(['1']), $trav([1])],

            [$trav([1]), [1], ['1']],
            [$trav([1]), [1], [2], [1]],
            [$trav([1]), [1], ['1'], [1]],
            [$trav([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, '3']],
            [$trav([1, 2, 3]), [1, 2, 3], [1, 2, 3], [1, 2, 1]],
            [$trav([1]), [1, null], [1, null], [1]],
            [$trav([1]), [1, null], [1, null], [1, null]],
            [$trav([1, null]), [1], [1], [1]],
            [$trav([1, null]), [1], [1, null], [1]],
        ];
    }
}

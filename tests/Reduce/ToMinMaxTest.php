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
     * @test toMinMax example usage without custom comparator
     */
    public function testToMinMaxWithoutCustomComparator(): void
    {
        // Given
        $data     = [5, 4, 1, 9, 3];
        $expected = [1, 9];

        // When
        $minMax = Reduce::toMinMax($data);

        // Then
        $this->assertEquals($expected, $minMax);
    }

    /**
     * @test toMinMax example usage custom comparator
     */
    public function testToMinMaxUsingCustomComparator(): void
    {
        // Given
        $reportCard = [
            [
                'subject' => 'history',
                'grade' => 90
            ],
            [
                'subject' => 'math',
                'grade' => 98
            ],
            [
                'subject' => 'science',
                'grade' => 92
            ],
            [
                'subject' => 'english',
                'grade' => 85
            ],
            [
                'subject' => 'programming',
                'grade' => 100
            ],
        ];
        $compareBy = fn ($class) => $class['grade'];

        // When
        $bestAndWorstSubject = Reduce::toMinMax($reportCard, $compareBy);

        // Then
        $expected = [
            [
                'subject' => 'english',
                'grade' => 85
            ],
            [
                'subject' => 'programming',
                'grade' => 100
            ],
        ];
        $this->assertEquals($expected, $bestAndWorstSubject);
    }

    /**
     * @test         toMinMax array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        callable|null $compareBy
     * @param        array $expected
     */
    public function testArray(array $data, ?callable $compareBy, array $expected)
    {
        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::ROUND_PRECISION);
    }

    public static function dataProviderForArray(): array
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
            [
                [1, null, 2],
                null,
                [null, 2],
            ],
            [
                [1, null, 2],
                fn ($item) => $item,
                [null, 2],
            ],
        ];
    }

    /**
     * @test         toMinMax generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        callable|null $compareBy
     * @param        array $expected
     */
    public function testGenerators(\Generator $data, ?callable $compareBy, array $expected)
    {
        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::ROUND_PRECISION);
    }

    public static function dataProviderForGenerators(): array
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
     * @param        callable|null $compareBy
     * @param        array $expected
     */
    public function testIterators(\Iterator $data, ?callable $compareBy, array $expected)
    {
        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::ROUND_PRECISION);
    }

    public static function dataProviderForIterators(): array
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
     * @param        callable|null $compareBy
     * @param        array $expected
     */
    public function testTraversables(\Traversable $data, ?callable $compareBy, array $expected)
    {
        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::ROUND_PRECISION);
    }

    public static function dataProviderForTraversables(): array
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

    /**
     * @dataProvider dataProviderForUsingClassMethodToCompare
     * @param iterable $data
     * @param callable|null $compareBy
     * @param $expected
     * @return void
     */
    public function testUsingClassMethodToCompare(iterable $data, ?callable $compareBy, $expected): void
    {
        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForUsingClassMethodToCompare(): array
    {
        $helper = new class () {
            public function direct(int $value): int
            {
                return $value;
            }

            public function reverse(int $value): int
            {
                return -$value;
            }
        };

        return [
            [
                [1, 3, 2, 5, 0],
                fn ($item) => $helper->direct($item),
                [0, 5],
            ],
            [
                [1, 3, 2, 5, 0],
                fn ($item) => $helper->reverse($item),
                [5, 0],
            ],
            [
                [1, 3, 2, 5, 0],
                [$helper, 'direct'],
                [0, 5],
            ],
            [
                [1, 3, 2, 5, 0],
                [$helper, 'reverse'],
                [5, 0],
            ],
        ];
    }

    /**
     * @test toMinMax skips NaN values array
     * @dataProvider dataProviderForNanArray
     * @param        array $data
     * @param        callable|null $compareBy
     * @param        array $expected
     */
    public function testNanArray(array $data, ?callable $compareBy, array $expected): void
    {
        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test toMinMax skips NaN values generators
     * @dataProvider dataProviderForNanGenerators
     * @param        \Generator $data
     * @param        callable|null $compareBy
     * @param        array $expected
     */
    public function testNanGenerators(\Generator $data, ?callable $compareBy, array $expected): void
    {
        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test toMinMax skips NaN values iterators
     * @dataProvider dataProviderForNanIterators
     * @param        \Iterator $data
     * @param        callable|null $compareBy
     * @param        array $expected
     */
    public function testNanIterators(\Iterator $data, ?callable $compareBy, array $expected): void
    {
        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test toMinMax skips NaN values traversables
     * @dataProvider dataProviderForNanTraversables
     * @param        \Traversable $data
     * @param        callable|null $compareBy
     * @param        array $expected
     */
    public function testNanTraversables(\Traversable $data, ?callable $compareBy, array $expected): void
    {
        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForNanArray(): array
    {
        return [
            [[NAN], null, [null, null]],
            [[NAN, NAN, NAN], null, [null, null]],
            [[3, NAN, 1], null, [1, 3]],
            [[1, 3, NAN], null, [1, 3]],
            [[NAN, 1, 3], null, [1, 3]],
            [[NAN, 3, NAN, 1, NAN], null, [1, 3]],
            [[1.5, NAN, 2.5, NAN, 0.5], null, [0.5, 2.5]],
            [[NAN, 5], null, [5, 5]],
            [[5, NAN], null, [5, 5]],
            [[['v' => NAN]], fn ($item) => $item['v'], [null, null]],
            [[['v' => NAN], ['v' => NAN]], fn ($item) => $item['v'], [null, null]],
            [[['v' => 3], ['v' => NAN], ['v' => 1]], fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
            [[['v' => NAN], ['v' => 1], ['v' => 3]], fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
            [[['v' => 1], ['v' => 3], ['v' => NAN]], fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
            [[['v' => NAN], ['v' => 3], ['v' => NAN], ['v' => 1], ['v' => NAN]], fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
        ];
    }

    public static function dataProviderForNanGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [$gen([NAN]), null, [null, null]],
            [$gen([NAN, NAN, NAN]), null, [null, null]],
            [$gen([3, NAN, 1]), null, [1, 3]],
            [$gen([1, 3, NAN]), null, [1, 3]],
            [$gen([NAN, 1, 3]), null, [1, 3]],
            [$gen([NAN, 3, NAN, 1, NAN]), null, [1, 3]],
            [$gen([1.5, NAN, 2.5, NAN, 0.5]), null, [0.5, 2.5]],
            [$gen([NAN, 5]), null, [5, 5]],
            [$gen([5, NAN]), null, [5, 5]],
            [$gen([['v' => NAN]]), fn ($item) => $item['v'], [null, null]],
            [$gen([['v' => NAN], ['v' => NAN]]), fn ($item) => $item['v'], [null, null]],
            [$gen([['v' => 3], ['v' => NAN], ['v' => 1]]), fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
            [$gen([['v' => NAN], ['v' => 1], ['v' => 3]]), fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
            [$gen([['v' => 1], ['v' => 3], ['v' => NAN]]), fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
            [$gen([['v' => NAN], ['v' => 3], ['v' => NAN], ['v' => 1], ['v' => NAN]]), fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
        ];
    }

    public static function dataProviderForNanIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [$iter([NAN]), null, [null, null]],
            [$iter([NAN, NAN, NAN]), null, [null, null]],
            [$iter([3, NAN, 1]), null, [1, 3]],
            [$iter([1, 3, NAN]), null, [1, 3]],
            [$iter([NAN, 1, 3]), null, [1, 3]],
            [$iter([NAN, 3, NAN, 1, NAN]), null, [1, 3]],
            [$iter([1.5, NAN, 2.5, NAN, 0.5]), null, [0.5, 2.5]],
            [$iter([NAN, 5]), null, [5, 5]],
            [$iter([5, NAN]), null, [5, 5]],
            [$iter([['v' => NAN]]), fn ($item) => $item['v'], [null, null]],
            [$iter([['v' => NAN], ['v' => NAN]]), fn ($item) => $item['v'], [null, null]],
            [$iter([['v' => 3], ['v' => NAN], ['v' => 1]]), fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
            [$iter([['v' => NAN], ['v' => 1], ['v' => 3]]), fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
            [$iter([['v' => 1], ['v' => 3], ['v' => NAN]]), fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
            [$iter([['v' => NAN], ['v' => 3], ['v' => NAN], ['v' => 1], ['v' => NAN]]), fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
        ];
    }

    public static function dataProviderForNanTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [$trav([NAN]), null, [null, null]],
            [$trav([NAN, NAN, NAN]), null, [null, null]],
            [$trav([3, NAN, 1]), null, [1, 3]],
            [$trav([1, 3, NAN]), null, [1, 3]],
            [$trav([NAN, 1, 3]), null, [1, 3]],
            [$trav([NAN, 3, NAN, 1, NAN]), null, [1, 3]],
            [$trav([1.5, NAN, 2.5, NAN, 0.5]), null, [0.5, 2.5]],
            [$trav([NAN, 5]), null, [5, 5]],
            [$trav([5, NAN]), null, [5, 5]],
            [$trav([['v' => NAN]]), fn ($item) => $item['v'], [null, null]],
            [$trav([['v' => NAN], ['v' => NAN]]), fn ($item) => $item['v'], [null, null]],
            [$trav([['v' => 3], ['v' => NAN], ['v' => 1]]), fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
            [$trav([['v' => NAN], ['v' => 1], ['v' => 3]]), fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
            [$trav([['v' => 1], ['v' => 3], ['v' => NAN]]), fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
            [$trav([['v' => NAN], ['v' => 3], ['v' => NAN], ['v' => 1], ['v' => NAN]]), fn ($item) => $item['v'], [['v' => 1], ['v' => 3]]],
        ];
    }

    /**
     * @test         toMinMax array of an int beyond 2^53 and a float, which compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionArray
     * @param        array        $data
     * @param        callable|null $compareBy
     * @param        array        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionArray(array $data, ?callable $compareBy, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which compares int and float by exact value:
     *   [min(clean), max(clean)] where clean = [x for x in data if not nan(x)]
     * The data has no exact int/float tie, so the tie choice doesn't decide any result.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionArray(): array
    {
        return [
            'minimum is the float that equals the last int as floats' => [
                [9007199254740994, 9007199254740992.0, 9007199254740993],
                null,
                [9007199254740992.0, 9007199254740994],
            ],
            'maximum is the int that equals the first float as floats' => [
                [9007199254740992.0, 9007199254740993],
                null,
                [9007199254740992.0, 9007199254740993],
            ],
            'int and float 2^53 apart by 1 in both orders' => [
                [9007199254740993, 9007199254740992.0, 9007199254740994.0, 9007199254740995],
                null,
                [9007199254740992.0, 9007199254740995],
            ],
            'int PHP_INT_MAX below float 2.0^63' => [
                [9.223372036854776e+18, 9223372036854775807],
                null,
                [9223372036854775807, 9.223372036854776e+18],
            ],
            'NAN skipped' => [
                [9007199254740994, \NAN, 9007199254740992.0, 9007199254740993],
                null,
                [9007199254740992.0, 9007199254740994],
            ],
            'compareBy: minimum is the float that equals the last int as floats' => [
                [['v' => 9007199254740994], ['v' => 9007199254740992.0], ['v' => 9007199254740993]],
                fn (array $item) => $item['v'],
                [['v' => 9007199254740992.0], ['v' => 9007199254740994]],
            ],
            'compareBy: maximum is the int that equals the first float as floats' => [
                [['v' => 9007199254740992.0], ['v' => 9007199254740993]],
                fn (array $item) => $item['v'],
                [['v' => 9007199254740992.0], ['v' => 9007199254740993]],
            ],
            'compareBy: int and float 2^53 apart by 1 in both orders' => [
                [['v' => 9007199254740993], ['v' => 9007199254740992.0], ['v' => 9007199254740994.0], ['v' => 9007199254740995]],
                fn (array $item) => $item['v'],
                [['v' => 9007199254740992.0], ['v' => 9007199254740995]],
            ],
            'compareBy: int PHP_INT_MAX below float 2.0^63' => [
                [['v' => 9.223372036854776e+18], ['v' => 9223372036854775807]],
                fn (array $item) => $item['v'],
                [['v' => 9223372036854775807], ['v' => 9.223372036854776e+18]],
            ],
        ];
    }

    /**
     * @test         toMinMax generators of an int beyond 2^53 and a float, which compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionGenerators
     * @param        \Generator   $data
     * @param        callable|null $compareBy
     * @param        array        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionGenerators(\Generator $data, ?callable $compareBy, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which compares int and float by exact value:
     *   [min(clean), max(clean)] where clean = [x for x in data if not nan(x)]
     * The data has no exact int/float tie, so the tie choice doesn't decide any result.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            'minimum is the float that equals the last int as floats' => [
                $gen([9007199254740994, 9007199254740992.0, 9007199254740993]),
                null,
                [9007199254740992.0, 9007199254740994],
            ],
            'maximum is the int that equals the first float as floats' => [
                $gen([9007199254740992.0, 9007199254740993]),
                null,
                [9007199254740992.0, 9007199254740993],
            ],
            'int and float 2^53 apart by 1 in both orders' => [
                $gen([9007199254740993, 9007199254740992.0, 9007199254740994.0, 9007199254740995]),
                null,
                [9007199254740992.0, 9007199254740995],
            ],
            'int PHP_INT_MAX below float 2.0^63' => [
                $gen([9.223372036854776e+18, 9223372036854775807]),
                null,
                [9223372036854775807, 9.223372036854776e+18],
            ],
            'NAN skipped' => [
                $gen([9007199254740994, \NAN, 9007199254740992.0, 9007199254740993]),
                null,
                [9007199254740992.0, 9007199254740994],
            ],
            'compareBy: minimum is the float that equals the last int as floats' => [
                $gen([['v' => 9007199254740994], ['v' => 9007199254740992.0], ['v' => 9007199254740993]]),
                fn (array $item) => $item['v'],
                [['v' => 9007199254740992.0], ['v' => 9007199254740994]],
            ],
            'compareBy: maximum is the int that equals the first float as floats' => [
                $gen([['v' => 9007199254740992.0], ['v' => 9007199254740993]]),
                fn (array $item) => $item['v'],
                [['v' => 9007199254740992.0], ['v' => 9007199254740993]],
            ],
            'compareBy: int and float 2^53 apart by 1 in both orders' => [
                $gen([['v' => 9007199254740993], ['v' => 9007199254740992.0], ['v' => 9007199254740994.0], ['v' => 9007199254740995]]),
                fn (array $item) => $item['v'],
                [['v' => 9007199254740992.0], ['v' => 9007199254740995]],
            ],
            'compareBy: int PHP_INT_MAX below float 2.0^63' => [
                $gen([['v' => 9.223372036854776e+18], ['v' => 9223372036854775807]]),
                fn (array $item) => $item['v'],
                [['v' => 9223372036854775807], ['v' => 9.223372036854776e+18]],
            ],
        ];
    }

    /**
     * @test         toMinMax iterators of an int beyond 2^53 and a float, which compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionIterators
     * @param        \Iterator    $data
     * @param        callable|null $compareBy
     * @param        array        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionIterators(\Iterator $data, ?callable $compareBy, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which compares int and float by exact value:
     *   [min(clean), max(clean)] where clean = [x for x in data if not nan(x)]
     * The data has no exact int/float tie, so the tie choice doesn't decide any result.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            'minimum is the float that equals the last int as floats' => [
                $iter([9007199254740994, 9007199254740992.0, 9007199254740993]),
                null,
                [9007199254740992.0, 9007199254740994],
            ],
            'maximum is the int that equals the first float as floats' => [
                $iter([9007199254740992.0, 9007199254740993]),
                null,
                [9007199254740992.0, 9007199254740993],
            ],
            'int and float 2^53 apart by 1 in both orders' => [
                $iter([9007199254740993, 9007199254740992.0, 9007199254740994.0, 9007199254740995]),
                null,
                [9007199254740992.0, 9007199254740995],
            ],
            'int PHP_INT_MAX below float 2.0^63' => [
                $iter([9.223372036854776e+18, 9223372036854775807]),
                null,
                [9223372036854775807, 9.223372036854776e+18],
            ],
            'NAN skipped' => [
                $iter([9007199254740994, \NAN, 9007199254740992.0, 9007199254740993]),
                null,
                [9007199254740992.0, 9007199254740994],
            ],
            'compareBy: minimum is the float that equals the last int as floats' => [
                $iter([['v' => 9007199254740994], ['v' => 9007199254740992.0], ['v' => 9007199254740993]]),
                fn (array $item) => $item['v'],
                [['v' => 9007199254740992.0], ['v' => 9007199254740994]],
            ],
            'compareBy: maximum is the int that equals the first float as floats' => [
                $iter([['v' => 9007199254740992.0], ['v' => 9007199254740993]]),
                fn (array $item) => $item['v'],
                [['v' => 9007199254740992.0], ['v' => 9007199254740993]],
            ],
            'compareBy: int and float 2^53 apart by 1 in both orders' => [
                $iter([['v' => 9007199254740993], ['v' => 9007199254740992.0], ['v' => 9007199254740994.0], ['v' => 9007199254740995]]),
                fn (array $item) => $item['v'],
                [['v' => 9007199254740992.0], ['v' => 9007199254740995]],
            ],
            'compareBy: int PHP_INT_MAX below float 2.0^63' => [
                $iter([['v' => 9.223372036854776e+18], ['v' => 9223372036854775807]]),
                fn (array $item) => $item['v'],
                [['v' => 9223372036854775807], ['v' => 9.223372036854776e+18]],
            ],
        ];
    }

    /**
     * @test         toMinMax traversables of an int beyond 2^53 and a float, which compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionTraversables
     * @param        \Traversable $data
     * @param        callable|null $compareBy
     * @param        array        $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionTraversables(\Traversable $data, ?callable $compareBy, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which compares int and float by exact value:
     *   [min(clean), max(clean)] where clean = [x for x in data if not nan(x)]
     * The data has no exact int/float tie, so the tie choice doesn't decide any result.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            'minimum is the float that equals the last int as floats' => [
                $trav([9007199254740994, 9007199254740992.0, 9007199254740993]),
                null,
                [9007199254740992.0, 9007199254740994],
            ],
            'maximum is the int that equals the first float as floats' => [
                $trav([9007199254740992.0, 9007199254740993]),
                null,
                [9007199254740992.0, 9007199254740993],
            ],
            'int and float 2^53 apart by 1 in both orders' => [
                $trav([9007199254740993, 9007199254740992.0, 9007199254740994.0, 9007199254740995]),
                null,
                [9007199254740992.0, 9007199254740995],
            ],
            'int PHP_INT_MAX below float 2.0^63' => [
                $trav([9.223372036854776e+18, 9223372036854775807]),
                null,
                [9223372036854775807, 9.223372036854776e+18],
            ],
            'NAN skipped' => [
                $trav([9007199254740994, \NAN, 9007199254740992.0, 9007199254740993]),
                null,
                [9007199254740992.0, 9007199254740994],
            ],
            'compareBy: minimum is the float that equals the last int as floats' => [
                $trav([['v' => 9007199254740994], ['v' => 9007199254740992.0], ['v' => 9007199254740993]]),
                fn (array $item) => $item['v'],
                [['v' => 9007199254740992.0], ['v' => 9007199254740994]],
            ],
            'compareBy: maximum is the int that equals the first float as floats' => [
                $trav([['v' => 9007199254740992.0], ['v' => 9007199254740993]]),
                fn (array $item) => $item['v'],
                [['v' => 9007199254740992.0], ['v' => 9007199254740993]],
            ],
            'compareBy: int and float 2^53 apart by 1 in both orders' => [
                $trav([['v' => 9007199254740993], ['v' => 9007199254740992.0], ['v' => 9007199254740994.0], ['v' => 9007199254740995]]),
                fn (array $item) => $item['v'],
                [['v' => 9007199254740992.0], ['v' => 9007199254740995]],
            ],
            'compareBy: int PHP_INT_MAX below float 2.0^63' => [
                $trav([['v' => 9.223372036854776e+18], ['v' => 9223372036854775807]]),
                fn (array $item) => $item['v'],
                [['v' => 9223372036854775807], ['v' => 9.223372036854776e+18]],
            ],
        ];
    }

    /**
     * @test         toMinMax array with compareBy keeps the first of tied elements
     * @dataProvider dataProviderForTieBreakingWithCompareByArray
     * @param        array        $data
     * @param        callable     $compareBy
     * @param        array        $expected
     */
    public function testTieBreakingWithCompareByArray(array $data, callable $compareBy, array $expected): void
    {
        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which returns the first of tied elements:
     *   [min(clean, key=f), max(clean, key=f)] where clean = [e for e in data if not nan(f(e))]
     *   and f = lambda e: e['key'], or lambda e: -e['key'] for the negated rows
     */
    public static function dataProviderForTieBreakingWithCompareByArray(): array
    {
        return [
            'all keys tied' => [
                [['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 1, 'id' => 'c']],
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'a']],
            ],
            'minimum and maximum tied at several positions' => [
                [['key' => 2, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 3, 'id' => 'c'], ['key' => 1, 'id' => 'd'], ['key' => 3, 'id' => 'e'], ['key' => 2, 'id' => 'f']],
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'b'], ['key' => 3, 'id' => 'c']],
            ],
            'minimum and maximum tied at several positions, negated key' => [
                [['key' => 2, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 3, 'id' => 'c'], ['key' => 1, 'id' => 'd'], ['key' => 3, 'id' => 'e'], ['key' => 2, 'id' => 'f']],
                fn (array $item) => -$item['key'],
                [['key' => 3, 'id' => 'c'], ['key' => 1, 'id' => 'b']],
            ],
            'whole input is one tied pair' => [
                [['key' => 5, 'id' => 'a'], ['key' => 5, 'id' => 'b']],
                fn (array $item) => $item['key'],
                [['key' => 5, 'id' => 'a'], ['key' => 5, 'id' => 'a']],
            ],
            'int key then equal float key' => [
                [['key' => 1, 'id' => 'a'], ['key' => 1.0, 'id' => 'b']],
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'a']],
            ],
            'float key then equal int key' => [
                [['key' => 1.0, 'id' => 'a'], ['key' => 1, 'id' => 'b']],
                fn (array $item) => $item['key'],
                [['key' => 1.0, 'id' => 'a'], ['key' => 1.0, 'id' => 'a']],
            ],
            'string keys tied' => [
                [['key' => 'b', 'id' => 'a'], ['key' => 'a', 'id' => 'b'], ['key' => 'b', 'id' => 'c'], ['key' => 'a', 'id' => 'd']],
                fn (array $item) => $item['key'],
                [['key' => 'a', 'id' => 'b'], ['key' => 'b', 'id' => 'a']],
            ],
            'NAN key skipped before tied keys' => [
                [['key' => \NAN, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 1, 'id' => 'c']],
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'b'], ['key' => 1, 'id' => 'b']],
            ],
            'NAN key between tied keys' => [
                [['key' => 1, 'id' => 'a'], ['key' => \NAN, 'id' => 'b'], ['key' => 1, 'id' => 'c']],
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'a']],
            ],
        ];
    }

    /**
     * @test         toMinMax generators with compareBy keeps the first of tied elements
     * @dataProvider dataProviderForTieBreakingWithCompareByGenerators
     * @param        \Generator   $data
     * @param        callable     $compareBy
     * @param        array        $expected
     */
    public function testTieBreakingWithCompareByGenerators(\Generator $data, callable $compareBy, array $expected): void
    {
        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which returns the first of tied elements:
     *   [min(clean, key=f), max(clean, key=f)] where clean = [e for e in data if not nan(f(e))]
     *   and f = lambda e: e['key'], or lambda e: -e['key'] for the negated rows
     */
    public static function dataProviderForTieBreakingWithCompareByGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            'all keys tied' => [
                $gen([['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 1, 'id' => 'c']]),
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'a']],
            ],
            'minimum and maximum tied at several positions' => [
                $gen([['key' => 2, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 3, 'id' => 'c'], ['key' => 1, 'id' => 'd'], ['key' => 3, 'id' => 'e'], ['key' => 2, 'id' => 'f']]),
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'b'], ['key' => 3, 'id' => 'c']],
            ],
            'minimum and maximum tied at several positions, negated key' => [
                $gen([['key' => 2, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 3, 'id' => 'c'], ['key' => 1, 'id' => 'd'], ['key' => 3, 'id' => 'e'], ['key' => 2, 'id' => 'f']]),
                fn (array $item) => -$item['key'],
                [['key' => 3, 'id' => 'c'], ['key' => 1, 'id' => 'b']],
            ],
            'whole input is one tied pair' => [
                $gen([['key' => 5, 'id' => 'a'], ['key' => 5, 'id' => 'b']]),
                fn (array $item) => $item['key'],
                [['key' => 5, 'id' => 'a'], ['key' => 5, 'id' => 'a']],
            ],
            'int key then equal float key' => [
                $gen([['key' => 1, 'id' => 'a'], ['key' => 1.0, 'id' => 'b']]),
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'a']],
            ],
            'float key then equal int key' => [
                $gen([['key' => 1.0, 'id' => 'a'], ['key' => 1, 'id' => 'b']]),
                fn (array $item) => $item['key'],
                [['key' => 1.0, 'id' => 'a'], ['key' => 1.0, 'id' => 'a']],
            ],
            'string keys tied' => [
                $gen([['key' => 'b', 'id' => 'a'], ['key' => 'a', 'id' => 'b'], ['key' => 'b', 'id' => 'c'], ['key' => 'a', 'id' => 'd']]),
                fn (array $item) => $item['key'],
                [['key' => 'a', 'id' => 'b'], ['key' => 'b', 'id' => 'a']],
            ],
            'NAN key skipped before tied keys' => [
                $gen([['key' => \NAN, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 1, 'id' => 'c']]),
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'b'], ['key' => 1, 'id' => 'b']],
            ],
            'NAN key between tied keys' => [
                $gen([['key' => 1, 'id' => 'a'], ['key' => \NAN, 'id' => 'b'], ['key' => 1, 'id' => 'c']]),
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'a']],
            ],
        ];
    }

    /**
     * @test         toMinMax iterators with compareBy keeps the first of tied elements
     * @dataProvider dataProviderForTieBreakingWithCompareByIterators
     * @param        \Iterator    $data
     * @param        callable     $compareBy
     * @param        array        $expected
     */
    public function testTieBreakingWithCompareByIterators(\Iterator $data, callable $compareBy, array $expected): void
    {
        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which returns the first of tied elements:
     *   [min(clean, key=f), max(clean, key=f)] where clean = [e for e in data if not nan(f(e))]
     *   and f = lambda e: e['key'], or lambda e: -e['key'] for the negated rows
     */
    public static function dataProviderForTieBreakingWithCompareByIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            'all keys tied' => [
                $iter([['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 1, 'id' => 'c']]),
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'a']],
            ],
            'minimum and maximum tied at several positions' => [
                $iter([['key' => 2, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 3, 'id' => 'c'], ['key' => 1, 'id' => 'd'], ['key' => 3, 'id' => 'e'], ['key' => 2, 'id' => 'f']]),
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'b'], ['key' => 3, 'id' => 'c']],
            ],
            'minimum and maximum tied at several positions, negated key' => [
                $iter([['key' => 2, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 3, 'id' => 'c'], ['key' => 1, 'id' => 'd'], ['key' => 3, 'id' => 'e'], ['key' => 2, 'id' => 'f']]),
                fn (array $item) => -$item['key'],
                [['key' => 3, 'id' => 'c'], ['key' => 1, 'id' => 'b']],
            ],
            'whole input is one tied pair' => [
                $iter([['key' => 5, 'id' => 'a'], ['key' => 5, 'id' => 'b']]),
                fn (array $item) => $item['key'],
                [['key' => 5, 'id' => 'a'], ['key' => 5, 'id' => 'a']],
            ],
            'int key then equal float key' => [
                $iter([['key' => 1, 'id' => 'a'], ['key' => 1.0, 'id' => 'b']]),
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'a']],
            ],
            'float key then equal int key' => [
                $iter([['key' => 1.0, 'id' => 'a'], ['key' => 1, 'id' => 'b']]),
                fn (array $item) => $item['key'],
                [['key' => 1.0, 'id' => 'a'], ['key' => 1.0, 'id' => 'a']],
            ],
            'string keys tied' => [
                $iter([['key' => 'b', 'id' => 'a'], ['key' => 'a', 'id' => 'b'], ['key' => 'b', 'id' => 'c'], ['key' => 'a', 'id' => 'd']]),
                fn (array $item) => $item['key'],
                [['key' => 'a', 'id' => 'b'], ['key' => 'b', 'id' => 'a']],
            ],
            'NAN key skipped before tied keys' => [
                $iter([['key' => \NAN, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 1, 'id' => 'c']]),
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'b'], ['key' => 1, 'id' => 'b']],
            ],
            'NAN key between tied keys' => [
                $iter([['key' => 1, 'id' => 'a'], ['key' => \NAN, 'id' => 'b'], ['key' => 1, 'id' => 'c']]),
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'a']],
            ],
        ];
    }

    /**
     * @test         toMinMax traversables with compareBy keeps the first of tied elements
     * @dataProvider dataProviderForTieBreakingWithCompareByTraversables
     * @param        \Traversable $data
     * @param        callable     $compareBy
     * @param        array        $expected
     */
    public function testTieBreakingWithCompareByTraversables(\Traversable $data, callable $compareBy, array $expected): void
    {
        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which returns the first of tied elements:
     *   [min(clean, key=f), max(clean, key=f)] where clean = [e for e in data if not nan(f(e))]
     *   and f = lambda e: e['key'], or lambda e: -e['key'] for the negated rows
     */
    public static function dataProviderForTieBreakingWithCompareByTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            'all keys tied' => [
                $trav([['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 1, 'id' => 'c']]),
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'a']],
            ],
            'minimum and maximum tied at several positions' => [
                $trav([['key' => 2, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 3, 'id' => 'c'], ['key' => 1, 'id' => 'd'], ['key' => 3, 'id' => 'e'], ['key' => 2, 'id' => 'f']]),
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'b'], ['key' => 3, 'id' => 'c']],
            ],
            'minimum and maximum tied at several positions, negated key' => [
                $trav([['key' => 2, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 3, 'id' => 'c'], ['key' => 1, 'id' => 'd'], ['key' => 3, 'id' => 'e'], ['key' => 2, 'id' => 'f']]),
                fn (array $item) => -$item['key'],
                [['key' => 3, 'id' => 'c'], ['key' => 1, 'id' => 'b']],
            ],
            'whole input is one tied pair' => [
                $trav([['key' => 5, 'id' => 'a'], ['key' => 5, 'id' => 'b']]),
                fn (array $item) => $item['key'],
                [['key' => 5, 'id' => 'a'], ['key' => 5, 'id' => 'a']],
            ],
            'int key then equal float key' => [
                $trav([['key' => 1, 'id' => 'a'], ['key' => 1.0, 'id' => 'b']]),
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'a']],
            ],
            'float key then equal int key' => [
                $trav([['key' => 1.0, 'id' => 'a'], ['key' => 1, 'id' => 'b']]),
                fn (array $item) => $item['key'],
                [['key' => 1.0, 'id' => 'a'], ['key' => 1.0, 'id' => 'a']],
            ],
            'string keys tied' => [
                $trav([['key' => 'b', 'id' => 'a'], ['key' => 'a', 'id' => 'b'], ['key' => 'b', 'id' => 'c'], ['key' => 'a', 'id' => 'd']]),
                fn (array $item) => $item['key'],
                [['key' => 'a', 'id' => 'b'], ['key' => 'b', 'id' => 'a']],
            ],
            'NAN key skipped before tied keys' => [
                $trav([['key' => \NAN, 'id' => 'a'], ['key' => 1, 'id' => 'b'], ['key' => 1, 'id' => 'c']]),
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'b'], ['key' => 1, 'id' => 'b']],
            ],
            'NAN key between tied keys' => [
                $trav([['key' => 1, 'id' => 'a'], ['key' => \NAN, 'id' => 'b'], ['key' => 1, 'id' => 'c']]),
                fn (array $item) => $item['key'],
                [['key' => 1, 'id' => 'a'], ['key' => 1, 'id' => 'a']],
            ],
        ];
    }

    /**
     * @test         toMinMax array with compareBy keeps the first of tied elements, comparing an int beyond 2^53 and a float exactly
     * @dataProvider dataProviderForTieBreakingWithCompareByBeyondFloatPrecisionArray
     * @param        array        $data
     * @param        callable     $compareBy
     * @param        array        $expected
     */
    public function testTieBreakingWithCompareByBeyondFloatPrecisionArray(array $data, callable $compareBy, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which returns the first of tied elements:
     *   [min(clean, key=f), max(clean, key=f)] where clean = [e for e in data if not nan(f(e))]
     *   and f = lambda e: e['key'], or lambda e: -e['key'] for the negated rows
     */
    public static function dataProviderForTieBreakingWithCompareByBeyondFloatPrecisionArray(): array
    {
        return [
            'int 2^53 then equal float 2.0^53' => [
                [['key' => 9007199254740992, 'id' => 'a'], ['key' => 9007199254740992.0, 'id' => 'b']],
                fn (array $item) => $item['key'],
                [['key' => 9007199254740992, 'id' => 'a'], ['key' => 9007199254740992, 'id' => 'a']],
            ],
            'float 2.0^53 then equal int 2^53' => [
                [['key' => 9007199254740992.0, 'id' => 'a'], ['key' => 9007199254740992, 'id' => 'b']],
                fn (array $item) => $item['key'],
                [['key' => 9007199254740992.0, 'id' => 'a'], ['key' => 9007199254740992.0, 'id' => 'a']],
            ],
            'tied ints above tied floats that are below them exactly' => [
                [['key' => 9007199254740993, 'id' => 'a'], ['key' => 9007199254740993, 'id' => 'b'], ['key' => 9007199254740992.0, 'id' => 'c'], ['key' => 9007199254740992.0, 'id' => 'd']],
                fn (array $item) => $item['key'],
                [['key' => 9007199254740992.0, 'id' => 'c'], ['key' => 9007199254740993, 'id' => 'a']],
            ],
            'tied floats below tied ints that are above them exactly, negated key' => [
                [['key' => 9007199254740992.0, 'id' => 'a'], ['key' => 9007199254740992.0, 'id' => 'b'], ['key' => 9007199254740993, 'id' => 'c'], ['key' => 9007199254740993, 'id' => 'd']],
                fn (array $item) => -$item['key'],
                [['key' => 9007199254740993, 'id' => 'c'], ['key' => 9007199254740992.0, 'id' => 'a']],
            ],
        ];
    }

    /**
     * @test         toMinMax generators with compareBy keeps the first of tied elements, comparing an int beyond 2^53 and a float exactly
     * @dataProvider dataProviderForTieBreakingWithCompareByBeyondFloatPrecisionGenerators
     * @param        \Generator   $data
     * @param        callable     $compareBy
     * @param        array        $expected
     */
    public function testTieBreakingWithCompareByBeyondFloatPrecisionGenerators(\Generator $data, callable $compareBy, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which returns the first of tied elements:
     *   [min(clean, key=f), max(clean, key=f)] where clean = [e for e in data if not nan(f(e))]
     *   and f = lambda e: e['key'], or lambda e: -e['key'] for the negated rows
     */
    public static function dataProviderForTieBreakingWithCompareByBeyondFloatPrecisionGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            'int 2^53 then equal float 2.0^53' => [
                $gen([['key' => 9007199254740992, 'id' => 'a'], ['key' => 9007199254740992.0, 'id' => 'b']]),
                fn (array $item) => $item['key'],
                [['key' => 9007199254740992, 'id' => 'a'], ['key' => 9007199254740992, 'id' => 'a']],
            ],
            'float 2.0^53 then equal int 2^53' => [
                $gen([['key' => 9007199254740992.0, 'id' => 'a'], ['key' => 9007199254740992, 'id' => 'b']]),
                fn (array $item) => $item['key'],
                [['key' => 9007199254740992.0, 'id' => 'a'], ['key' => 9007199254740992.0, 'id' => 'a']],
            ],
            'tied ints above tied floats that are below them exactly' => [
                $gen([['key' => 9007199254740993, 'id' => 'a'], ['key' => 9007199254740993, 'id' => 'b'], ['key' => 9007199254740992.0, 'id' => 'c'], ['key' => 9007199254740992.0, 'id' => 'd']]),
                fn (array $item) => $item['key'],
                [['key' => 9007199254740992.0, 'id' => 'c'], ['key' => 9007199254740993, 'id' => 'a']],
            ],
            'tied floats below tied ints that are above them exactly, negated key' => [
                $gen([['key' => 9007199254740992.0, 'id' => 'a'], ['key' => 9007199254740992.0, 'id' => 'b'], ['key' => 9007199254740993, 'id' => 'c'], ['key' => 9007199254740993, 'id' => 'd']]),
                fn (array $item) => -$item['key'],
                [['key' => 9007199254740993, 'id' => 'c'], ['key' => 9007199254740992.0, 'id' => 'a']],
            ],
        ];
    }

    /**
     * @test         toMinMax iterators with compareBy keeps the first of tied elements, comparing an int beyond 2^53 and a float exactly
     * @dataProvider dataProviderForTieBreakingWithCompareByBeyondFloatPrecisionIterators
     * @param        \Iterator    $data
     * @param        callable     $compareBy
     * @param        array        $expected
     */
    public function testTieBreakingWithCompareByBeyondFloatPrecisionIterators(\Iterator $data, callable $compareBy, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which returns the first of tied elements:
     *   [min(clean, key=f), max(clean, key=f)] where clean = [e for e in data if not nan(f(e))]
     *   and f = lambda e: e['key'], or lambda e: -e['key'] for the negated rows
     */
    public static function dataProviderForTieBreakingWithCompareByBeyondFloatPrecisionIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            'int 2^53 then equal float 2.0^53' => [
                $iter([['key' => 9007199254740992, 'id' => 'a'], ['key' => 9007199254740992.0, 'id' => 'b']]),
                fn (array $item) => $item['key'],
                [['key' => 9007199254740992, 'id' => 'a'], ['key' => 9007199254740992, 'id' => 'a']],
            ],
            'float 2.0^53 then equal int 2^53' => [
                $iter([['key' => 9007199254740992.0, 'id' => 'a'], ['key' => 9007199254740992, 'id' => 'b']]),
                fn (array $item) => $item['key'],
                [['key' => 9007199254740992.0, 'id' => 'a'], ['key' => 9007199254740992.0, 'id' => 'a']],
            ],
            'tied ints above tied floats that are below them exactly' => [
                $iter([['key' => 9007199254740993, 'id' => 'a'], ['key' => 9007199254740993, 'id' => 'b'], ['key' => 9007199254740992.0, 'id' => 'c'], ['key' => 9007199254740992.0, 'id' => 'd']]),
                fn (array $item) => $item['key'],
                [['key' => 9007199254740992.0, 'id' => 'c'], ['key' => 9007199254740993, 'id' => 'a']],
            ],
            'tied floats below tied ints that are above them exactly, negated key' => [
                $iter([['key' => 9007199254740992.0, 'id' => 'a'], ['key' => 9007199254740992.0, 'id' => 'b'], ['key' => 9007199254740993, 'id' => 'c'], ['key' => 9007199254740993, 'id' => 'd']]),
                fn (array $item) => -$item['key'],
                [['key' => 9007199254740993, 'id' => 'c'], ['key' => 9007199254740992.0, 'id' => 'a']],
            ],
        ];
    }

    /**
     * @test         toMinMax traversables with compareBy keeps the first of tied elements, comparing an int beyond 2^53 and a float exactly
     * @dataProvider dataProviderForTieBreakingWithCompareByBeyondFloatPrecisionTraversables
     * @param        \Traversable $data
     * @param        callable     $compareBy
     * @param        array        $expected
     */
    public function testTieBreakingWithCompareByBeyondFloatPrecisionTraversables(\Traversable $data, callable $compareBy, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toMinMax($data, $compareBy);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which returns the first of tied elements:
     *   [min(clean, key=f), max(clean, key=f)] where clean = [e for e in data if not nan(f(e))]
     *   and f = lambda e: e['key'], or lambda e: -e['key'] for the negated rows
     */
    public static function dataProviderForTieBreakingWithCompareByBeyondFloatPrecisionTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            'int 2^53 then equal float 2.0^53' => [
                $trav([['key' => 9007199254740992, 'id' => 'a'], ['key' => 9007199254740992.0, 'id' => 'b']]),
                fn (array $item) => $item['key'],
                [['key' => 9007199254740992, 'id' => 'a'], ['key' => 9007199254740992, 'id' => 'a']],
            ],
            'float 2.0^53 then equal int 2^53' => [
                $trav([['key' => 9007199254740992.0, 'id' => 'a'], ['key' => 9007199254740992, 'id' => 'b']]),
                fn (array $item) => $item['key'],
                [['key' => 9007199254740992.0, 'id' => 'a'], ['key' => 9007199254740992.0, 'id' => 'a']],
            ],
            'tied ints above tied floats that are below them exactly' => [
                $trav([['key' => 9007199254740993, 'id' => 'a'], ['key' => 9007199254740993, 'id' => 'b'], ['key' => 9007199254740992.0, 'id' => 'c'], ['key' => 9007199254740992.0, 'id' => 'd']]),
                fn (array $item) => $item['key'],
                [['key' => 9007199254740992.0, 'id' => 'c'], ['key' => 9007199254740993, 'id' => 'a']],
            ],
            'tied floats below tied ints that are above them exactly, negated key' => [
                $trav([['key' => 9007199254740992.0, 'id' => 'a'], ['key' => 9007199254740992.0, 'id' => 'b'], ['key' => 9007199254740993, 'id' => 'c'], ['key' => 9007199254740993, 'id' => 'd']]),
                fn (array $item) => -$item['key'],
                [['key' => 9007199254740993, 'id' => 'c'], ['key' => 9007199254740992.0, 'id' => 'a']],
            ],
        ];
    }
}

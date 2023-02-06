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

    public function dataProviderForUsingClassMethodToCompare(): array
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
}

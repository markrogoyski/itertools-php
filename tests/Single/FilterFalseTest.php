<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture;

class FilterFalseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test filterFalse example usage
     */
    public function testFilterFalseExampleUsage(): void
    {
        // Given
        $reportCardGrades = [100, 0, 95, 85, 0, 94, 0];
        $expected         = [0, 0, 0];

        // When
        $allZeros = [];
        foreach (Single::filterFalse($reportCardGrades) as $zeroGrade) {
            $allZeros[] = $zeroGrade;
        }

        // Then
        $this->assertEquals($expected, $allZeros);
    }

    /**
     * @test filterFalse example usage with keys
     */
    public function testFilterFalseExampleUsageWithKeys(): void
    {
        // Given
        $weeklyAlerts = [
            'Sunday'    => 0,
            'Monday'    => 1,
            'Tuesday'   => 1,
            'Wednesday' => 2,
            'Thursday'  => 1,
            'Friday'    => 2,
            'Saturday'  => 0,
        ];
        $expected = [
            'Sunday'   => 0,
            'Saturday' => 0,
        ];

        // When
        $daysWithoutAlerts = [];
        foreach (Single::filterFalse($weeklyAlerts) as $day => $alerts) {
            $daysWithoutAlerts[$day] = $alerts;
        }

        // Then
        $this->assertEquals($expected, $daysWithoutAlerts);
    }

    /**
     * @test         filterFalse array
     * @dataProvider dataProviderForArray
     * @param        array    $iterable
     * @param        callable $predicate
     * @param        array    $expected
     */
    public function testArray(array $iterable, callable $predicate, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::filterFalse($iterable, $predicate) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 0,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 1,
                [1, 2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 2,
                [2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 3,
                [3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 4,
                [4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 5,
                [5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 6,
                [],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x > 0,
                [0],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x > 1,
                [0, 1],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x > -1,
                [],
            ],
            [
                [5, 4, 3, 2, 1, 0],
                fn ($x) => $x > 2,
                [2, 1, 0],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => true,
                [],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => false,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [1, 4, 6, 4, 1],
                fn ($x) => $x < 5,
                [6],
            ],
            [
                [50, 60, 70, 85, 65, 90],
                fn ($x) => $x < 70,
                [70, 85, 90],
            ],
            [
                [50, 60, 70, 85, 65, 90],
                fn ($x) => $x <= 70,
                [85, 90],
            ],
        ];
    }

    /**
     * @test filterFalse with no predicate
     */
    public function testNoPredicate()
    {
        // Given
        $data = [-1, 0, 1, 2, 3, true, false, null, [], [0], [1], 'a', ''];

        // And
        $result   = [];
        $expected = [0, false, null, [], ''];

        // When
        foreach (Single::filterFalse($data) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         filterFalse generator
     * @dataProvider dataProviderForGenerator
     * @param        \Generator $iterable
     * @param        callable   $predicate
     * @param        array      $expected
     */
    public function testGenerator(\Generator $iterable, callable $predicate, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::filterFalse($iterable, $predicate) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForGenerator(): array
    {
        return [
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 0,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 1,
                [1, 2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 2,
                [2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 3,
                [3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 4,
                [4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 5,
                [5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 6,
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 0,
                [0],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 1,
                [0, 1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > -1,
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([5, 4, 3, 2, 1, 0]),
                fn ($x) => $x > 2,
                [2, 1, 0],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => true,
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => false,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 4, 6, 4, 1]),
                fn ($x) => $x < 5,
                [6],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([50, 60, 70, 85, 65, 90]),
                fn ($x) => $x < 70,
                [70, 85, 90],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([50, 60, 70, 85, 65, 90]),
                fn ($x) => $x <= 70,
                [85, 90],
            ],
        ];
    }

    /**
     * @test         filterFalse iterator
     * @dataProvider dataProviderForIterator
     * @param        \Iterator $iterable
     * @param        callable  $predicate
     * @param        array     $expected
     */
    public function testIterator(\Iterator $iterable, callable $predicate, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::filterFalse($iterable, $predicate) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForIterator(): array
    {
        return [
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 0,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 1,
                [1, 2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 2,
                [2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 3,
                [3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 4,
                [4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 5,
                [5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 6,
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 0,
                [0],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 1,
                [0, 1],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > -1,
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([5, 4, 3, 2, 1, 0]),
                fn ($x) => $x > 2,
                [2, 1, 0],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => true,
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => false,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 4, 6, 4, 1]),
                fn ($x) => $x < 5,
                [6],
            ],
            [
                new Fixture\ArrayIteratorFixture([50, 60, 70, 85, 65, 90]),
                fn ($x) => $x < 70,
                [70, 85, 90],
            ],
            [
                new Fixture\ArrayIteratorFixture([50, 60, 70, 85, 65, 90]),
                fn ($x) => $x <= 70,
                [85, 90],
            ],
        ];
    }

    /**
     * @test         filterFalse traversable
     * @dataProvider dataProviderForTraversable
     * @param        \Traversable $iterable
     * @param        callable  $predicate
     * @param        array     $expected
     */
    public function testTraversable(\Traversable $iterable, callable $predicate, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::filterFalse($iterable, $predicate) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForTraversable(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 0,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 1,
                [1, 2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 2,
                [2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 3,
                [3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 4,
                [4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 5,
                [5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 6,
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 0,
                [0],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 1,
                [0, 1],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > -1,
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([5, 4, 3, 2, 1, 0]),
                fn ($x) => $x > 2,
                [2, 1, 0],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => true,
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => false,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 4, 6, 4, 1]),
                fn ($x) => $x < 5,
                [6],
            ],
            [
                new Fixture\IteratorAggregateFixture([50, 60, 70, 85, 65, 90]),
                fn ($x) => $x < 70,
                [70, 85, 90],
            ],
            [
                new Fixture\IteratorAggregateFixture([50, 60, 70, 85, 65, 90]),
                fn ($x) => $x <= 70,
                [85, 90],
            ],
        ];
    }

    /**
     * @test filterFalse preserves keys with associative array
     */
    public function testFilterFalsePreservesKeysArray(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 0, 'c' => 3, 'd' => 0, 'e' => 5];

        // When
        $result = \iterator_to_array(Single::filterFalse($data));

        // Then
        $expected = ['b' => 0, 'd' => 0];
        $this->assertSame($expected, $result);
    }

    /**
     * @test filterFalse preserves keys with predicate and associative array
     */
    public function testFilterFalsePreservesKeysWithPredicateArray(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];

        // When
        $result = \iterator_to_array(Single::filterFalse($data, fn ($x) => $x > 2));

        // Then
        $expected = ['a' => 1, 'b' => 2];
        $this->assertSame($expected, $result);
    }

    /**
     * @test filterFalse preserves keys with associative generator
     */
    public function testFilterFalsePreservesKeysGenerator(): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getKeyValueGenerator(['a' => 1, 'b' => 0, 'c' => 3, 'd' => 0, 'e' => 5]);

        // When
        $result = \iterator_to_array(Single::filterFalse($data));

        // Then
        $expected = ['b' => 0, 'd' => 0];
        $this->assertSame($expected, $result);
    }

    /**
     * @test filterFalse preserves keys with associative iterator
     */
    public function testFilterFalsePreservesKeysIterator(): void
    {
        // Given
        $data = new \ArrayIterator(['a' => 1, 'b' => 0, 'c' => 3, 'd' => 0, 'e' => 5]);

        // When
        $result = \iterator_to_array(Single::filterFalse($data));

        // Then
        $expected = ['b' => 0, 'd' => 0];
        $this->assertSame($expected, $result);
    }

    /**
     * @test filterFalse preserves keys with associative traversable
     */
    public function testFilterFalsePreservesKeysTraversable(): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture(['a' => 1, 'b' => 0, 'c' => 3, 'd' => 0, 'e' => 5]);

        // When
        $result = \iterator_to_array(Single::filterFalse($data));

        // Then
        $expected = ['b' => 0, 'd' => 0];
        $this->assertSame($expected, $result);
    }

    /**
     * @test         filterFalse iterator_to_array
     * @dataProvider dataProviderForArray
     * @param        array    $iterable
     * @param        callable $predicate
     * @param        array    $expected
     */
    public function testIteratorToArray(array $iterable, callable $predicate, array $expected): void
    {
        // Given
        $iterator = Single::filterFalse($iterable, $predicate);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, \array_values($result));
    }
}

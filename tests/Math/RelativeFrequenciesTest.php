<?php

declare(strict_types=1);

namespace IterTools\Tests\Math;

use IterTools\Math;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class RelativeFrequenciesTest extends \PHPUnit\Framework\TestCase
{
    private const Δ = 0.0001;

    /**
     * @dataProvider dataProviderForArray
     * @dataProvider dataProviderForArrayStrict
     * @dataProvider dataProviderForArrayNonScalarValues
     * @param array $data
     * @param array $expectedValues
     * @param array $expectedrelativeFrequencies
     */
    public function testArrayStrict(array $data, array $expectedValues, array $expectedrelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0;

        // When
        foreach (Math::relativeFrequencies($data) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEqualsWithDelta($expectedrelativeFrequencies, $relativeFrequencies, self::Δ);

        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1, $sum, self::Δ);
        }
    }

    /**
     * @dataProvider dataProviderForArray
     * @dataProvider dataProviderForArrayCoercive
     * @param array $data
     * @param array $expectedValues
     * @param array $expectedrelativeFrequencies
     */
    public function testArrayCoercive(array $data, array $expectedValues, array $expectedrelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0;

        // When
        foreach (Math::relativeFrequencies($data, false) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEqualsWithDelta($expectedrelativeFrequencies, $relativeFrequencies, self::Δ);

        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1, $sum, self::Δ);
        }
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                [],
                [],
            ],
            [
                [0],
                [0],
                [1],
            ],
            [
                [1],
                [1],
                [1],
            ],
            [
                ['a'],
                ['a'],
                [1],
            ],
            [
                [0, 0],
                [0],
                [1],
            ],
            [
                ['1', '1'],
                ['1'],
                [1],
            ],
            [
                [0, 1],
                [0, 1],
                [0.5, 0.5],
            ],
            [
                [0, 1, 1],
                [0, 1],
                [0.3333, 0.6667],
            ],
            [
                [0, 0, 1, 1],
                [0, 1],
                [0.5, 0.5],
            ],
            [
                [0, 0, 0, 1],
                [0, 1],
                [0.75, 0.25],
            ],
            [
                [0, 1, 0, 0],
                [0, 1],
                [0.75, 0.25],
            ],
            [
                [1, 0, 0, 0],
                [1, 0],
                [0.25, 0.75],
            ],
            [
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                [0.3333, 0.3333, 0.3333],
            ],
            [
                ['a', 'c', 'b', 'c'],
                ['a', 'c', 'b'],
                [0.25, 0.5, 0.25],
            ],
            [
                [1, 'a', 1, 'b', 1, 'a', 1, 'b', 1, 'a'],
                [1, 'a', 'b'],
                [0.5, 0.3, 0.2],
            ],
        ];
    }

    public function dataProviderForArrayNonScalarValues(): array
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        return [
            [
                [[1, 2, 3], [1], [1, 2, 3], [2]],
                [[1, 2, 3], [1], [2]],
                [0.5, 0.25, 0.25],
            ],
            [
                [$obj1, $obj1, $obj2, $obj2, $obj2],
                [$obj1, $obj2],
                [0.4, 0.6],
            ],
        ];
    }

    public function dataProviderForArrayStrict(): array
    {
        return [
            [
                [0, '0', null, 0.0, false],
                [0, '0', null, 0.0, false],
                [0.2, 0.2, 0.2, 0.2, 0.2],
            ],
            [
                [1, 1.0, true, '1'],
                [1, 1.0, true, '1'],
                [0.25, 0.25, 0.25, 0.25],
            ],
            [
                ['1', 1, '2', 2],
                ['1', 1, '2', 2],
                [0.25, 0.25, 0.25, 0.25],
            ],
        ];
    }

    public function dataProviderForArrayCoercive(): array
    {
        return [
            [
                [0, '0', null, 0.0, false],
                [0],
                [1],
            ],
            [
                ['1', 1, '2', 2],
                ['1', '2'],
                [0.5, 0.5],
            ],
            [
                [0, '0', null, 0.0, false, '1', 2, 1, '2', 2.0],
                [0, '1', 2],
                [0.5, 0.2, 0.3],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @dataProvider dataProviderForGeneratorsStrict
     * @param \Generator $data
     * @param array $expectedValues
     * @param array $expectedrelativeFrequencies
     */
    public function testGeneratorsStrict(\Generator $data, array $expectedValues, array $expectedrelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0;

        // When
        foreach (Math::relativeFrequencies($data) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEqualsWithDelta($expectedrelativeFrequencies, $relativeFrequencies, self::Δ);

        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1, $sum, self::Δ);
        }
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @dataProvider dataProviderForGeneratorsCoercive
     * @param \Generator $data
     * @param array $expectedValues
     * @param array $expectedrelativeFrequencies
     */
    public function testGeneratorsCoercive(\Generator $data, array $expectedValues, array $expectedrelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0;

        // When
        foreach (Math::relativeFrequencies($data, false) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEqualsWithDelta($expectedrelativeFrequencies, $relativeFrequencies, self::Δ);

        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1, $sum, self::Δ);
        }
    }

    public function dataProviderForGenerators(): array
    {
        $gen = fn ($data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                [],
                [],
            ],
            [
                $gen([0]),
                [0],
                [1],
            ],
            [
                $gen([1]),
                [1],
                [1],
            ],
            [
                $gen(['a']),
                ['a'],
                [1],
            ],
            [
                $gen([0, 0]),
                [0],
                [1],
            ],
            [
                $gen(['1', '1']),
                ['1'],
                [1],
            ],
            [
                $gen([0, 1]),
                [0, 1],
                [0.5, 0.5],
            ],
            [
                $gen([0, 1, 1]),
                [0, 1],
                [0.3333, 0.6667],
            ],
            [
                $gen([0, 0, 1, 1]),
                [0, 1],
                [0.5, 0.5],
            ],
            [
                $gen([0, 0, 0, 1]),
                [0, 1],
                [0.75, 0.25],
            ],
            [
                $gen([0, 1, 0, 0]),
                [0, 1],
                [0.75, 0.25],
            ],
            [
                $gen([1, 0, 0, 0]),
                [1, 0],
                [0.25, 0.75],
            ],
            [
                $gen(['a', 'b', 'c']),
                ['a', 'b', 'c'],
                [0.3333, 0.3333, 0.3333],
            ],
            [
                $gen(['a', 'c', 'b', 'c']),
                ['a', 'c', 'b'],
                [0.25, 0.5, 0.25],
            ],
            [
                $gen([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [0.5, 0.25, 0.25],
            ],
            [
                $gen([1, 'a', 1, 'b', 1, 'a', 1, 'b', 1, 'a']),
                [1, 'a', 'b'],
                [0.5, 0.3, 0.2],
            ],
        ];
    }

    public function dataProviderForGeneratorsStrict(): array
    {
        $gen = fn ($data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([0, '0', null, 0.0, false]),
                [0, '0', null, 0.0, false],
                [0.2, 0.2, 0.2, 0.2, 0.2],
            ],
            [
                $gen([1, 1.0, true, '1']),
                [1, 1.0, true, '1'],
                [0.25, 0.25, 0.25, 0.25],
            ],
            [
                $gen(['1', 1, '2', 2]),
                ['1', 1, '2', 2],
                [0.25, 0.25, 0.25, 0.25],
            ],
        ];
    }

    public function dataProviderForGeneratorsCoercive(): array
    {
        $gen = fn ($data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([0, '0', null, 0.0, false]),
                [0],
                [1],
            ],
            [
                $gen(['1', 1, '2', 2]),
                ['1', '2'],
                [0.5, 0.5],
            ],
            [
                $gen([0, '0', null, 0.0, false, '1', 2, 1, '2', 2.0]),
                [0, '1', 2],
                [0.5, 0.2, 0.3],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @dataProvider dataProviderForIteratorsStrict
     * @param \Iterator $data
     * @param array $expectedValues
     * @param array $expectedrelativeFrequencies
     */
    public function testIteratorsStrict(\Iterator $data, array $expectedValues, array $expectedrelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0;

        // When
        foreach (Math::relativeFrequencies($data) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEqualsWithDelta($expectedrelativeFrequencies, $relativeFrequencies, self::Δ);

        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1, $sum, self::Δ);
        }
    }

    /**
     * @dataProvider dataProviderForIterators
     * @dataProvider dataProviderForIteratorsCoercive
     * @param \Iterator $data
     * @param array $expectedValues
     * @param array $expectedrelativeFrequencies
     */
    public function testIteratorsCoercive(\Iterator $data, array $expectedValues, array $expectedrelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0;

        // When
        foreach (Math::relativeFrequencies($data, false) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEqualsWithDelta($expectedrelativeFrequencies, $relativeFrequencies, self::Δ);

        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1, $sum, self::Δ);
        }
    }

    public function dataProviderForIterators(): array
    {
        $iter = fn ($data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                [],
                [],
            ],
            [
                $iter([0]),
                [0],
                [1],
            ],
            [
                $iter([1]),
                [1],
                [1],
            ],
            [
                $iter(['a']),
                ['a'],
                [1],
            ],
            [
                $iter([0, 0]),
                [0],
                [1],
            ],
            [
                $iter(['1', '1']),
                ['1'],
                [1],
            ],
            [
                $iter([0, 1]),
                [0, 1],
                [0.5, 0.5],
            ],
            [
                $iter([0, 1, 1]),
                [0, 1],
                [0.3333, 0.6667],
            ],
            [
                $iter([0, 0, 1, 1]),
                [0, 1],
                [0.5, 0.5],
            ],
            [
                $iter([0, 0, 0, 1]),
                [0, 1],
                [0.75, 0.25],
            ],
            [
                $iter([0, 1, 0, 0]),
                [0, 1],
                [0.75, 0.25],
            ],
            [
                $iter([1, 0, 0, 0]),
                [1, 0],
                [0.25, 0.75],
            ],
            [
                $iter(['a', 'b', 'c']),
                ['a', 'b', 'c'],
                [0.3333, 0.3333, 0.3333],
            ],
            [
                $iter(['a', 'c', 'b', 'c']),
                ['a', 'c', 'b'],
                [0.25, 0.5, 0.25],
            ],
            [
                $iter([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [0.5, 0.25, 0.25],
            ],
            [
                $iter([1, 'a', 1, 'b', 1, 'a', 1, 'b', 1, 'a']),
                [1, 'a', 'b'],
                [0.5, 0.3, 0.2],
            ],
        ];
    }

    public function dataProviderForIteratorsStrict(): array
    {
        $iter = fn ($data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([0, '0', null, 0.0, false]),
                [0, '0', null, 0.0, false],
                [0.2, 0.2, 0.2, 0.2, 0.2],
            ],
            [
                $iter([1, 1.0, true, '1']),
                [1, 1.0, true, '1'],
                [0.25, 0.25, 0.25, 0.25],
            ],
            [
                $iter(['1', 1, '2', 2]),
                ['1', 1, '2', 2],
                [0.25, 0.25, 0.25, 0.25],
            ],
        ];
    }

    public function dataProviderForIteratorsCoercive(): array
    {
        $iter = fn ($data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([0, '0', null, 0.0, false]),
                [0],
                [1],
            ],
            [
                $iter(['1', 1, '2', 2]),
                ['1', '2'],
                [0.5, 0.5],
            ],
            [
                $iter([0, '0', null, 0.0, false, '1', 2, 1, '2', 2.0]),
                [0, '1', 2],
                [0.5, 0.2, 0.3],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @dataProvider dataProviderForTraversablesStrict
     * @param \Traversable $data
     * @param array $expectedValues
     * @param array $expectedrelativeFrequencies
     */
    public function testTraversablesStrict(\Traversable $data, array $expectedValues, array $expectedrelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0;

        // When
        foreach (Math::relativeFrequencies($data) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEqualsWithDelta($expectedrelativeFrequencies, $relativeFrequencies, self::Δ);

        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1, $sum, self::Δ);
        }
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @dataProvider dataProviderForTraversablesCoercive
     * @param \Traversable $data
     * @param array $expectedValues
     * @param array $expectedrelativeFrequencies
     */
    public function testTraversablesCoercive(\Traversable $data, array $expectedValues, array $expectedrelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0;

        // When
        foreach (Math::relativeFrequencies($data, false) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEqualsWithDelta($expectedrelativeFrequencies, $relativeFrequencies, self::Δ);

        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1, $sum, self::Δ);
        }
    }

    public function dataProviderForTraversables(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                [],
                [],
            ],
            [
                $trav([0]),
                [0],
                [1],
            ],
            [
                $trav([1]),
                [1],
                [1],
            ],
            [
                $trav(['a']),
                ['a'],
                [1],
            ],
            [
                $trav([0, 0]),
                [0],
                [1],
            ],
            [
                $trav(['1', '1']),
                ['1'],
                [1],
            ],
            [
                $trav([0, 1]),
                [0, 1],
                [0.5, 0.5],
            ],
            [
                $trav([0, 1, 1]),
                [0, 1],
                [0.3333, 0.6667],
            ],
            [
                $trav([0, 0, 1, 1]),
                [0, 1],
                [0.5, 0.5],
            ],
            [
                $trav([0, 0, 0, 1]),
                [0, 1],
                [0.75, 0.25],
            ],
            [
                $trav([0, 1, 0, 0]),
                [0, 1],
                [0.75, 0.25],
            ],
            [
                $trav([1, 0, 0, 0]),
                [1, 0],
                [0.25, 0.75],
            ],
            [
                $trav(['a', 'b', 'c']),
                ['a', 'b', 'c'],
                [0.3333, 0.3333, 0.3333],
            ],
            [
                $trav(['a', 'c', 'b', 'c']),
                ['a', 'c', 'b'],
                [0.25, 0.5, 0.25],
            ],
            [
                $trav([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [0.5, 0.25, 0.25],
            ],
            [
                $trav([1, 'a', 1, 'b', 1, 'a', 1, 'b', 1, 'a']),
                [1, 'a', 'b'],
                [0.5, 0.3, 0.2],
            ],
        ];
    }

    public function dataProviderForTraversablesStrict(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([0, '0', null, 0.0, false]),
                [0, '0', null, 0.0, false],
                [0.2, 0.2, 0.2, 0.2, 0.2],
            ],
            [
                $trav([1, 1.0, true, '1']),
                [1, 1.0, true, '1'],
                [0.25, 0.25, 0.25, 0.25],
            ],
            [
                $trav(['1', 1, '2', 2]),
                ['1', 1, '2', 2],
                [0.25, 0.25, 0.25, 0.25],
            ],
        ];
    }

    public function dataProviderForTraversablesCoercive(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([0, '0', null, 0.0, false]),
                [0],
                [1],
            ],
            [
                $trav(['1', 1, '2', 2]),
                ['1', '2'],
                [0.5, 0.5],
            ],
            [
                $trav([0, '0', null, 0.0, false, '1', 2, 1, '2', 2.0]),
                [0, '1', 2],
                [0.5, 0.2, 0.3],
            ],
        ];
    }

    /**
     * @test         frequencies iterator_to_array
     * @dataProvider dataProviderForArray
     * @param array $data
     * @param array $expectedValues
     * @param array $expectedFrequencies
     */
    public function testIteratorToArray(array $data, array $expectedValues, array $expectedFrequencies): void
    {
        // Given
        $iterator = Math::relativeFrequencies($data);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertEqualsWithDelta($expectedValues, \array_keys($result), self::Δ);
        $this->assertEqualsWithDelta($expectedFrequencies, \array_values($result), self::Δ);
    }
}

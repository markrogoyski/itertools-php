<?php

declare(strict_types=1);

namespace IterTools\Tests\Math;

use IterTools\Math;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class FrequenciesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArray
     * @dataProvider dataProviderForArrayStrict
     * @dataProvider dataProviderForArrayNonScalarValues
     * @param array $data
     * @param array $expectedValues
     * @param array $expectedFrequencies
     */
    public function testArrayStrict(array $data, array $expectedValues, array $expectedFrequencies): void
    {
        // Given
        $values = [];
        $frequencies = [];

        // When
        foreach (Math::frequencies($data) as $value => $frequency) {
            $values[] = $value;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEquals($expectedFrequencies, $frequencies);
    }

    /**
     * @dataProvider dataProviderForArray
     * @dataProvider dataProviderForArrayCoercive
     * @param array $data
     * @param array $expectedValues
     * @param array $expectedFrequencies
     */
    public function testArrayCoercive(array $data, array $expectedValues, array $expectedFrequencies): void
    {
        // Given
        $values = [];
        $frequencies = [];

        // When
        foreach (Math::frequencies($data, false) as $value => $frequency) {
            $values[] = $value;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEquals($expectedFrequencies, $frequencies);
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
                [2],
            ],
            [
                ['1', '1'],
                ['1'],
                [2],
            ],
            [
                [0, 1],
                [0, 1],
                [1, 1],
            ],
            [
                [0, 1, 1],
                [0, 1],
                [1, 2],
            ],
            [
                [0, 0, 1, 1],
                [0, 1],
                [2, 2],
            ],
            [
                [0, 0, 0, 1],
                [0, 1],
                [3, 1],
            ],
            [
                [0, 1, 0, 0],
                [0, 1],
                [3, 1],
            ],
            [
                [1, 0, 0, 0],
                [1, 0],
                [1, 3],
            ],
            [
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                [1, 1, 1],
            ],
            [
                ['a', 'c', 'b', 'c'],
                ['a', 'c', 'b'],
                [1, 2, 1],
            ],
            [
                [1, 'a', 1, 'b', 1, 'a', 1, 'b', 1, 'a'],
                [1, 'a', 'b'],
                [5, 3, 2],
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
                [2, 1, 1],
            ],
            [
                [$obj1, $obj1, $obj2, $obj2, $obj2],
                [$obj1, $obj2],
                [2, 3],
            ],
        ];
    }

    public function dataProviderForArrayStrict(): array
    {
        return [
            [
                [0, '0', null, 0.0, false],
                [0, '0', null, 0.0, false],
                [1, 1, 1, 1, 1],
            ],
            [
                [1, 1.0, true, '1'],
                [1, 1.0, true, '1'],
                [1, 1, 1, 1],
            ],
            [
                ['1', 1, '2', 2],
                ['1', 1, '2', 2],
                [1, 1, 1, 1],
            ],
        ];
    }

    public function dataProviderForArrayCoercive(): array
    {
        return [
            [
                [0, '0', null, 0.0, false],
                [0],
                [5],
            ],
            [
                ['1', 1, '2', 2],
                ['1', '2'],
                [2, 2],
            ],
            [
                [0, '0', null, 0.0, false, '1', 2, 1, '2', 2.0],
                [0, '1', 2],
                [5, 2, 3],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @dataProvider dataProviderForGeneratorsStrict
     * @param \Generator $data
     * @param array $expectedValues
     * @param array $expectedFrequencies
     */
    public function testGeneratorsStrict(\Generator $data, array $expectedValues, array $expectedFrequencies): void
    {
        // Given
        $values = [];
        $frequencies = [];

        // When
        foreach (Math::frequencies($data) as $value => $frequency) {
            $values[] = $value;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEquals($expectedFrequencies, $frequencies);
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @dataProvider dataProviderForGeneratorsCoercive
     * @param \Generator $data
     * @param array $expectedValues
     * @param array $expectedFrequencies
     */
    public function testGeneratorsCoercive(\Generator $data, array $expectedValues, array $expectedFrequencies): void
    {
        // Given
        $values = [];
        $frequencies = [];

        // When
        foreach (Math::frequencies($data, false) as $value => $frequency) {
            $values[] = $value;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEquals($expectedFrequencies, $frequencies);
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
                [2],
            ],
            [
                $gen(['1', '1']),
                ['1'],
                [2],
            ],
            [
                $gen([0, 1]),
                [0, 1],
                [1, 1],
            ],
            [
                $gen([0, 1, 1]),
                [0, 1],
                [1, 2],
            ],
            [
                $gen([0, 0, 1, 1]),
                [0, 1],
                [2, 2],
            ],
            [
                $gen([0, 0, 0, 1]),
                [0, 1],
                [3, 1],
            ],
            [
                $gen([0, 1, 0, 0]),
                [0, 1],
                [3, 1],
            ],
            [
                $gen([1, 0, 0, 0]),
                [1, 0],
                [1, 3],
            ],
            [
                $gen(['a', 'b', 'c']),
                ['a', 'b', 'c'],
                [1, 1, 1],
            ],
            [
                $gen(['a', 'c', 'b', 'c']),
                ['a', 'c', 'b'],
                [1, 2, 1],
            ],
            [
                $gen([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [2, 1, 1],
            ],
            [
                $gen([1, 'a', 1, 'b', 1, 'a', 1, 'b', 1, 'a']),
                [1, 'a', 'b'],
                [5, 3, 2],
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
                [1, 1, 1, 1, 1],
            ],
            [
                $gen([1, 1.0, true, '1']),
                [1, 1.0, true, '1'],
                [1, 1, 1, 1],
            ],
            [
                $gen(['1', 1, '2', 2]),
                ['1', 1, '2', 2],
                [1, 1, 1, 1],
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
                [5],
            ],
            [
                $gen(['1', 1, '2', 2]),
                ['1', '2'],
                [2, 2],
            ],
            [
                $gen([0, '0', null, 0.0, false, '1', 2, 1, '2', 2.0]),
                [0, '1', 2],
                [5, 2, 3],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @dataProvider dataProviderForIteratorsStrict
     * @param \Iterator $data
     * @param array $expectedValues
     * @param array $expectedFrequencies
     */
    public function testIteratorsStrict(\Iterator $data, array $expectedValues, array $expectedFrequencies): void
    {
        // Given
        $values = [];
        $frequencies = [];

        // When
        foreach (Math::frequencies($data) as $value => $frequency) {
            $values[] = $value;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEquals($expectedFrequencies, $frequencies);
    }

    /**
     * @dataProvider dataProviderForIterators
     * @dataProvider dataProviderForIteratorsCoercive
     * @param \Iterator $data
     * @param array $expectedValues
     * @param array $expectedFrequencies
     */
    public function testIteratorsCoercive(\Iterator $data, array $expectedValues, array $expectedFrequencies): void
    {
        // Given
        $values = [];
        $frequencies = [];

        // When
        foreach (Math::frequencies($data, false) as $value => $frequency) {
            $values[] = $value;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEquals($expectedFrequencies, $frequencies);
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
                [2],
            ],
            [
                $iter(['1', '1']),
                ['1'],
                [2],
            ],
            [
                $iter([0, 1]),
                [0, 1],
                [1, 1],
            ],
            [
                $iter([0, 1, 1]),
                [0, 1],
                [1, 2],
            ],
            [
                $iter([0, 0, 1, 1]),
                [0, 1],
                [2, 2],
            ],
            [
                $iter([0, 0, 0, 1]),
                [0, 1],
                [3, 1],
            ],
            [
                $iter([0, 1, 0, 0]),
                [0, 1],
                [3, 1],
            ],
            [
                $iter([1, 0, 0, 0]),
                [1, 0],
                [1, 3],
            ],
            [
                $iter(['a', 'b', 'c']),
                ['a', 'b', 'c'],
                [1, 1, 1],
            ],
            [
                $iter(['a', 'c', 'b', 'c']),
                ['a', 'c', 'b'],
                [1, 2, 1],
            ],
            [
                $iter([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [2, 1, 1],
            ],
            [
                $iter([1, 'a', 1, 'b', 1, 'a', 1, 'b', 1, 'a']),
                [1, 'a', 'b'],
                [5, 3, 2],
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
                [1, 1, 1, 1, 1],
            ],
            [
                $iter([1, 1.0, true, '1']),
                [1, 1.0, true, '1'],
                [1, 1, 1, 1],
            ],
            [
                $iter(['1', 1, '2', 2]),
                ['1', 1, '2', 2],
                [1, 1, 1, 1],
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
                [5],
            ],
            [
                $iter(['1', 1, '2', 2]),
                ['1', '2'],
                [2, 2],
            ],
            [
                $iter([0, '0', null, 0.0, false, '1', 2, 1, '2', 2.0]),
                [0, '1', 2],
                [5, 2, 3],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @dataProvider dataProviderForTraversablesStrict
     * @param \Traversable $data
     * @param array $expectedValues
     * @param array $expectedFrequencies
     */
    public function testTraversablesStrict(\Traversable $data, array $expectedValues, array $expectedFrequencies): void
    {
        // Given
        $values = [];
        $frequencies = [];

        // When
        foreach (Math::frequencies($data) as $value => $frequency) {
            $values[] = $value;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEquals($expectedFrequencies, $frequencies);
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @dataProvider dataProviderForTraversablesCoercive
     * @param \Traversable $data
     * @param array $expectedValues
     * @param array $expectedFrequencies
     */
    public function testTraversablesCoercive(\Traversable $data, array $expectedValues, array $expectedFrequencies): void
    {
        // Given
        $values = [];
        $frequencies = [];

        // When
        foreach (Math::frequencies($data, false) as $value => $frequency) {
            $values[] = $value;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedValues, $values);
        $this->assertEquals($expectedFrequencies, $frequencies);
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
                [2],
            ],
            [
                $trav(['1', '1']),
                ['1'],
                [2],
            ],
            [
                $trav([0, 1]),
                [0, 1],
                [1, 1],
            ],
            [
                $trav([0, 1, 1]),
                [0, 1],
                [1, 2],
            ],
            [
                $trav([0, 0, 1, 1]),
                [0, 1],
                [2, 2],
            ],
            [
                $trav([0, 0, 0, 1]),
                [0, 1],
                [3, 1],
            ],
            [
                $trav([0, 1, 0, 0]),
                [0, 1],
                [3, 1],
            ],
            [
                $trav([1, 0, 0, 0]),
                [1, 0],
                [1, 3],
            ],
            [
                $trav(['a', 'b', 'c']),
                ['a', 'b', 'c'],
                [1, 1, 1],
            ],
            [
                $trav(['a', 'c', 'b', 'c']),
                ['a', 'c', 'b'],
                [1, 2, 1],
            ],
            [
                $trav([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [2, 1, 1],
            ],
            [
                $trav([1, 'a', 1, 'b', 1, 'a', 1, 'b', 1, 'a']),
                [1, 'a', 'b'],
                [5, 3, 2],
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
                [1, 1, 1, 1, 1],
            ],
            [
                $trav([1, 1.0, true, '1']),
                [1, 1.0, true, '1'],
                [1, 1, 1, 1],
            ],
            [
                $trav(['1', 1, '2', 2]),
                ['1', 1, '2', 2],
                [1, 1, 1, 1],
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
                [5],
            ],
            [
                $trav(['1', 1, '2', 2]),
                ['1', '2'],
                [2, 2],
            ],
            [
                $trav([0, '0', null, 0.0, false, '1', 2, 1, '2', 2.0]),
                [0, '1', 2],
                [5, 2, 3],
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
        $iterator = Math::frequencies($data);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertEquals($expectedValues, \array_keys($result));
        $this->assertEquals($expectedFrequencies, \array_values($result));
    }
}

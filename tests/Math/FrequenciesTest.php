<?php

declare(strict_types=1);

namespace IterTools\Tests\Math;

use IterTools\Math;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;
use IterTools\Tests\Fixture\NonSerializableFixture;

class FrequenciesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test frequencies example usage
     */
    public function testFrequenciesExampleUsage(): void
    {
        // Given
        $grades = ['A', 'A', 'B', 'B', 'B', 'C'];

        // And
        $frequencies = [];

        // When
        foreach (Math::frequencies($grades) as $grade => $frequency) {
            $frequencies[$grade] = $frequency;
        }

        // Then
        self::assertEqualsCanonicalizing(['A' => 2, 'B' => 3, 'C' => 1], $frequencies);
    }

    /**
     * @test
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
     * @test
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

    public static function dataProviderForArray(): array
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

    public static function dataProviderForArrayNonScalarValues(): array
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

    public static function dataProviderForArrayStrict(): array
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

    public static function dataProviderForArrayCoercive(): array
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
     * @test
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
     * @test
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

    public static function dataProviderForGenerators(): array
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

    public static function dataProviderForGeneratorsStrict(): array
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

    public static function dataProviderForGeneratorsCoercive(): array
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
     * @test
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
     * @test
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

    public static function dataProviderForIterators(): array
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

    public static function dataProviderForIteratorsStrict(): array
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

    public static function dataProviderForIteratorsCoercive(): array
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
     * @test
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
     * @test
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

    public static function dataProviderForTraversables(): array
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

    public static function dataProviderForTraversablesStrict(): array
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

    public static function dataProviderForTraversablesCoercive(): array
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

    /**
     * @test frequencies throws InvalidArgumentException for non-serializable objects in non-strict mode
     */
    public function testNonSerializableObjectNonStrictThrowsException(): void
    {
        // Given
        $obj = new NonSerializableFixture(1);

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (Math::frequencies([$obj], false) as $_) {
        }
    }

    /**
     * @test frequencies works with non-serializable objects in strict mode
     */
    public function testNonSerializableObjectStrictMode(): void
    {
        // Given
        $obj1 = new NonSerializableFixture(1);
        $obj2 = new NonSerializableFixture(1);

        // When
        $frequencies = [];
        foreach (Math::frequencies([$obj1, $obj2, $obj1]) as $value => $frequency) {
            $frequencies[] = [$value, $frequency];
        }

        // Then
        $this->assertCount(2, $frequencies);
        $this->assertSame($obj1, $frequencies[0][0]);
        $this->assertEquals(2, $frequencies[0][1]);
        $this->assertSame($obj2, $frequencies[1][0]);
        $this->assertEquals(1, $frequencies[1][1]);
    }
}

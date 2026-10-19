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
     * Example data generated with Python:
     *   c = collections.Counter(['A', 'A', 'B', 'B', 'B', 'C'])
     *
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
        $this->assertSame(['A' => 2, 'B' => 3, 'C' => 1], $frequencies);
    }

    /**
     * @test frequencies array strict
     * @dataProvider dataProviderForArray
     * @dataProvider dataProviderForArrayFloats
     * @dataProvider dataProviderForArrayStrict
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
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedFrequencies, $frequencies);
    }

    /**
     * @test frequencies array coercive
     * @dataProvider dataProviderForArray
     * @dataProvider dataProviderForArrayFloats
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
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedFrequencies, $frequencies);
    }

    /**
     * Test data generated with Python (first-seen order; ints and strings only, which Python
     * never equates across types):
     *   c = collections.Counter(data); values = list(c); frequencies = list(c.values())
     */
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

    /**
     * Test data generated with Python (floats only, equal under Python ==, strict and coercive
     * comparison alike, so -0.0 and 0.0 share one entry keyed by the first-seen -0.0):
     *   c = collections.Counter(data); values = list(c); frequencies = list(c.values())
     */
    public static function dataProviderForArrayFloats(): array
    {
        return [
            [
                [0.5, 0.25, 0.5],
                [0.5, 0.25],
                [2, 1],
            ],
            [
                [0.1, 0.1, 0.2],
                [0.1, 0.2],
                [2, 1],
            ],
            [
                [0.30000000000000004, 0.3],
                [0.30000000000000004, 0.3],
                [1, 1],
            ],
            [
                [-0.0, 0.0],
                [-0.0],
                [2],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): two values are one iff ===, so
     * 0, '0', null, 0.0 and false are five values; nested arrays compare by key and value,
     * objects by instance.
     * Frequencies are the size of each group.
     */
    public static function dataProviderForArrayStrict(): array
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
                [['a' => 1], ['b' => 1]],
                [['a' => 1], ['b' => 1]],
                [1, 1],
            ],
            [
                [$obj1, $obj1, $obj2, $obj2, $obj2],
                [$obj1, $obj2],
                [2, 3],
            ],
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

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): numeric equivalence, so
     * 0, '0', null, 0.0 and false are one value, 1, '1' and 1.0 are one value; objects
     * compare by serialized state, so two empty stdClass instances are one value.
     * Frequencies are the size of each group.
     */
    public static function dataProviderForArrayCoercive(): array
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
                [['a' => 1], ['b' => 1]],
                [['a' => 1], ['b' => 1]],
                [1, 1],
            ],
            [
                [$obj1, $obj1, $obj2, $obj2, $obj2],
                [$obj1],
                [5],
            ],
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
     * @test frequencies generators strict
     * @dataProvider dataProviderForGenerators
     * @dataProvider dataProviderForGeneratorsFloats
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
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedFrequencies, $frequencies);
    }

    /**
     * @test frequencies generators coercive
     * @dataProvider dataProviderForGenerators
     * @dataProvider dataProviderForGeneratorsFloats
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
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedFrequencies, $frequencies);
    }

    /**
     * Test data generated with Python (first-seen order; ints and strings only, which Python
     * never equates across types):
     *   c = collections.Counter(data); values = list(c); frequencies = list(c.values())
     */
    public static function dataProviderForGenerators(): array
    {
        return [
            [
                GeneratorFixture::getGenerator([]),
                [],
                [],
            ],
            [
                GeneratorFixture::getGenerator([0]),
                [0],
                [1],
            ],
            [
                GeneratorFixture::getGenerator([1]),
                [1],
                [1],
            ],
            [
                GeneratorFixture::getGenerator(['a']),
                ['a'],
                [1],
            ],
            [
                GeneratorFixture::getGenerator([0, 0]),
                [0],
                [2],
            ],
            [
                GeneratorFixture::getGenerator(['1', '1']),
                ['1'],
                [2],
            ],
            [
                GeneratorFixture::getGenerator([0, 1]),
                [0, 1],
                [1, 1],
            ],
            [
                GeneratorFixture::getGenerator([0, 1, 1]),
                [0, 1],
                [1, 2],
            ],
            [
                GeneratorFixture::getGenerator([0, 0, 1, 1]),
                [0, 1],
                [2, 2],
            ],
            [
                GeneratorFixture::getGenerator([0, 0, 0, 1]),
                [0, 1],
                [3, 1],
            ],
            [
                GeneratorFixture::getGenerator([0, 1, 0, 0]),
                [0, 1],
                [3, 1],
            ],
            [
                GeneratorFixture::getGenerator([1, 0, 0, 0]),
                [1, 0],
                [1, 3],
            ],
            [
                GeneratorFixture::getGenerator(['a', 'b', 'c']),
                ['a', 'b', 'c'],
                [1, 1, 1],
            ],
            [
                GeneratorFixture::getGenerator(['a', 'c', 'b', 'c']),
                ['a', 'c', 'b'],
                [1, 2, 1],
            ],
            [
                GeneratorFixture::getGenerator([1, 'a', 1, 'b', 1, 'a', 1, 'b', 1, 'a']),
                [1, 'a', 'b'],
                [5, 3, 2],
            ],
        ];
    }

    /**
     * Test data generated with Python (floats only, equal under Python ==, strict and coercive
     * comparison alike, so -0.0 and 0.0 share one entry keyed by the first-seen -0.0):
     *   c = collections.Counter(data); values = list(c); frequencies = list(c.values())
     */
    public static function dataProviderForGeneratorsFloats(): array
    {
        return [
            [
                GeneratorFixture::getGenerator([0.5, 0.25, 0.5]),
                [0.5, 0.25],
                [2, 1],
            ],
            [
                GeneratorFixture::getGenerator([0.1, 0.1, 0.2]),
                [0.1, 0.2],
                [2, 1],
            ],
            [
                GeneratorFixture::getGenerator([0.30000000000000004, 0.3]),
                [0.30000000000000004, 0.3],
                [1, 1],
            ],
            [
                GeneratorFixture::getGenerator([-0.0, 0.0]),
                [-0.0],
                [2],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): two values are one iff ===, so
     * 0, '0', null, 0.0 and false are five values; nested arrays compare by key and value,
     * objects by instance.
     * Frequencies are the size of each group.
     */
    public static function dataProviderForGeneratorsStrict(): array
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        return [
            [
                GeneratorFixture::getGenerator([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [2, 1, 1],
            ],
            [
                GeneratorFixture::getGenerator([['a' => 1], ['b' => 1]]),
                [['a' => 1], ['b' => 1]],
                [1, 1],
            ],
            [
                GeneratorFixture::getGenerator([$obj1, $obj1, $obj2, $obj2, $obj2]),
                [$obj1, $obj2],
                [2, 3],
            ],
            [
                GeneratorFixture::getGenerator([0, '0', null, 0.0, false]),
                [0, '0', null, 0.0, false],
                [1, 1, 1, 1, 1],
            ],
            [
                GeneratorFixture::getGenerator([1, 1.0, true, '1']),
                [1, 1.0, true, '1'],
                [1, 1, 1, 1],
            ],
            [
                GeneratorFixture::getGenerator(['1', 1, '2', 2]),
                ['1', 1, '2', 2],
                [1, 1, 1, 1],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): numeric equivalence, so
     * 0, '0', null, 0.0 and false are one value, 1, '1' and 1.0 are one value; objects
     * compare by serialized state, so two empty stdClass instances are one value.
     * Frequencies are the size of each group.
     */
    public static function dataProviderForGeneratorsCoercive(): array
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        return [
            [
                GeneratorFixture::getGenerator([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [2, 1, 1],
            ],
            [
                GeneratorFixture::getGenerator([['a' => 1], ['b' => 1]]),
                [['a' => 1], ['b' => 1]],
                [1, 1],
            ],
            [
                GeneratorFixture::getGenerator([$obj1, $obj1, $obj2, $obj2, $obj2]),
                [$obj1],
                [5],
            ],
            [
                GeneratorFixture::getGenerator([0, '0', null, 0.0, false]),
                [0],
                [5],
            ],
            [
                GeneratorFixture::getGenerator(['1', 1, '2', 2]),
                ['1', '2'],
                [2, 2],
            ],
            [
                GeneratorFixture::getGenerator([0, '0', null, 0.0, false, '1', 2, 1, '2', 2.0]),
                [0, '1', 2],
                [5, 2, 3],
            ],
        ];
    }

    /**
     * @test frequencies iterators strict
     * @dataProvider dataProviderForIterators
     * @dataProvider dataProviderForIteratorsFloats
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
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedFrequencies, $frequencies);
    }

    /**
     * @test frequencies iterators coercive
     * @dataProvider dataProviderForIterators
     * @dataProvider dataProviderForIteratorsFloats
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
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedFrequencies, $frequencies);
    }

    /**
     * Test data generated with Python (first-seen order; ints and strings only, which Python
     * never equates across types):
     *   c = collections.Counter(data); values = list(c); frequencies = list(c.values())
     */
    public static function dataProviderForIterators(): array
    {
        return [
            [
                new ArrayIteratorFixture([]),
                [],
                [],
            ],
            [
                new ArrayIteratorFixture([0]),
                [0],
                [1],
            ],
            [
                new ArrayIteratorFixture([1]),
                [1],
                [1],
            ],
            [
                new ArrayIteratorFixture(['a']),
                ['a'],
                [1],
            ],
            [
                new ArrayIteratorFixture([0, 0]),
                [0],
                [2],
            ],
            [
                new ArrayIteratorFixture(['1', '1']),
                ['1'],
                [2],
            ],
            [
                new ArrayIteratorFixture([0, 1]),
                [0, 1],
                [1, 1],
            ],
            [
                new ArrayIteratorFixture([0, 1, 1]),
                [0, 1],
                [1, 2],
            ],
            [
                new ArrayIteratorFixture([0, 0, 1, 1]),
                [0, 1],
                [2, 2],
            ],
            [
                new ArrayIteratorFixture([0, 0, 0, 1]),
                [0, 1],
                [3, 1],
            ],
            [
                new ArrayIteratorFixture([0, 1, 0, 0]),
                [0, 1],
                [3, 1],
            ],
            [
                new ArrayIteratorFixture([1, 0, 0, 0]),
                [1, 0],
                [1, 3],
            ],
            [
                new ArrayIteratorFixture(['a', 'b', 'c']),
                ['a', 'b', 'c'],
                [1, 1, 1],
            ],
            [
                new ArrayIteratorFixture(['a', 'c', 'b', 'c']),
                ['a', 'c', 'b'],
                [1, 2, 1],
            ],
            [
                new ArrayIteratorFixture([1, 'a', 1, 'b', 1, 'a', 1, 'b', 1, 'a']),
                [1, 'a', 'b'],
                [5, 3, 2],
            ],
        ];
    }

    /**
     * Test data generated with Python (floats only, equal under Python ==, strict and coercive
     * comparison alike, so -0.0 and 0.0 share one entry keyed by the first-seen -0.0):
     *   c = collections.Counter(data); values = list(c); frequencies = list(c.values())
     */
    public static function dataProviderForIteratorsFloats(): array
    {
        return [
            [
                new ArrayIteratorFixture([0.5, 0.25, 0.5]),
                [0.5, 0.25],
                [2, 1],
            ],
            [
                new ArrayIteratorFixture([0.1, 0.1, 0.2]),
                [0.1, 0.2],
                [2, 1],
            ],
            [
                new ArrayIteratorFixture([0.30000000000000004, 0.3]),
                [0.30000000000000004, 0.3],
                [1, 1],
            ],
            [
                new ArrayIteratorFixture([-0.0, 0.0]),
                [-0.0],
                [2],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): two values are one iff ===, so
     * 0, '0', null, 0.0 and false are five values; nested arrays compare by key and value,
     * objects by instance.
     * Frequencies are the size of each group.
     */
    public static function dataProviderForIteratorsStrict(): array
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        return [
            [
                new ArrayIteratorFixture([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [2, 1, 1],
            ],
            [
                new ArrayIteratorFixture([['a' => 1], ['b' => 1]]),
                [['a' => 1], ['b' => 1]],
                [1, 1],
            ],
            [
                new ArrayIteratorFixture([$obj1, $obj1, $obj2, $obj2, $obj2]),
                [$obj1, $obj2],
                [2, 3],
            ],
            [
                new ArrayIteratorFixture([0, '0', null, 0.0, false]),
                [0, '0', null, 0.0, false],
                [1, 1, 1, 1, 1],
            ],
            [
                new ArrayIteratorFixture([1, 1.0, true, '1']),
                [1, 1.0, true, '1'],
                [1, 1, 1, 1],
            ],
            [
                new ArrayIteratorFixture(['1', 1, '2', 2]),
                ['1', 1, '2', 2],
                [1, 1, 1, 1],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): numeric equivalence, so
     * 0, '0', null, 0.0 and false are one value, 1, '1' and 1.0 are one value; objects
     * compare by serialized state, so two empty stdClass instances are one value.
     * Frequencies are the size of each group.
     */
    public static function dataProviderForIteratorsCoercive(): array
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        return [
            [
                new ArrayIteratorFixture([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [2, 1, 1],
            ],
            [
                new ArrayIteratorFixture([['a' => 1], ['b' => 1]]),
                [['a' => 1], ['b' => 1]],
                [1, 1],
            ],
            [
                new ArrayIteratorFixture([$obj1, $obj1, $obj2, $obj2, $obj2]),
                [$obj1],
                [5],
            ],
            [
                new ArrayIteratorFixture([0, '0', null, 0.0, false]),
                [0],
                [5],
            ],
            [
                new ArrayIteratorFixture(['1', 1, '2', 2]),
                ['1', '2'],
                [2, 2],
            ],
            [
                new ArrayIteratorFixture([0, '0', null, 0.0, false, '1', 2, 1, '2', 2.0]),
                [0, '1', 2],
                [5, 2, 3],
            ],
        ];
    }

    /**
     * @test frequencies traversables strict
     * @dataProvider dataProviderForTraversables
     * @dataProvider dataProviderForTraversablesFloats
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
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedFrequencies, $frequencies);
    }

    /**
     * @test frequencies traversables coercive
     * @dataProvider dataProviderForTraversables
     * @dataProvider dataProviderForTraversablesFloats
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
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedFrequencies, $frequencies);
    }

    /**
     * Test data generated with Python (first-seen order; ints and strings only, which Python
     * never equates across types):
     *   c = collections.Counter(data); values = list(c); frequencies = list(c.values())
     */
    public static function dataProviderForTraversables(): array
    {
        return [
            [
                new IteratorAggregateFixture([]),
                [],
                [],
            ],
            [
                new IteratorAggregateFixture([0]),
                [0],
                [1],
            ],
            [
                new IteratorAggregateFixture([1]),
                [1],
                [1],
            ],
            [
                new IteratorAggregateFixture(['a']),
                ['a'],
                [1],
            ],
            [
                new IteratorAggregateFixture([0, 0]),
                [0],
                [2],
            ],
            [
                new IteratorAggregateFixture(['1', '1']),
                ['1'],
                [2],
            ],
            [
                new IteratorAggregateFixture([0, 1]),
                [0, 1],
                [1, 1],
            ],
            [
                new IteratorAggregateFixture([0, 1, 1]),
                [0, 1],
                [1, 2],
            ],
            [
                new IteratorAggregateFixture([0, 0, 1, 1]),
                [0, 1],
                [2, 2],
            ],
            [
                new IteratorAggregateFixture([0, 0, 0, 1]),
                [0, 1],
                [3, 1],
            ],
            [
                new IteratorAggregateFixture([0, 1, 0, 0]),
                [0, 1],
                [3, 1],
            ],
            [
                new IteratorAggregateFixture([1, 0, 0, 0]),
                [1, 0],
                [1, 3],
            ],
            [
                new IteratorAggregateFixture(['a', 'b', 'c']),
                ['a', 'b', 'c'],
                [1, 1, 1],
            ],
            [
                new IteratorAggregateFixture(['a', 'c', 'b', 'c']),
                ['a', 'c', 'b'],
                [1, 2, 1],
            ],
            [
                new IteratorAggregateFixture([1, 'a', 1, 'b', 1, 'a', 1, 'b', 1, 'a']),
                [1, 'a', 'b'],
                [5, 3, 2],
            ],
        ];
    }

    /**
     * Test data generated with Python (floats only, equal under Python ==, strict and coercive
     * comparison alike, so -0.0 and 0.0 share one entry keyed by the first-seen -0.0):
     *   c = collections.Counter(data); values = list(c); frequencies = list(c.values())
     */
    public static function dataProviderForTraversablesFloats(): array
    {
        return [
            [
                new IteratorAggregateFixture([0.5, 0.25, 0.5]),
                [0.5, 0.25],
                [2, 1],
            ],
            [
                new IteratorAggregateFixture([0.1, 0.1, 0.2]),
                [0.1, 0.2],
                [2, 1],
            ],
            [
                new IteratorAggregateFixture([0.30000000000000004, 0.3]),
                [0.30000000000000004, 0.3],
                [1, 1],
            ],
            [
                new IteratorAggregateFixture([-0.0, 0.0]),
                [-0.0],
                [2],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): two values are one iff ===, so
     * 0, '0', null, 0.0 and false are five values; nested arrays compare by key and value,
     * objects by instance.
     * Frequencies are the size of each group.
     */
    public static function dataProviderForTraversablesStrict(): array
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        return [
            [
                new IteratorAggregateFixture([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [2, 1, 1],
            ],
            [
                new IteratorAggregateFixture([['a' => 1], ['b' => 1]]),
                [['a' => 1], ['b' => 1]],
                [1, 1],
            ],
            [
                new IteratorAggregateFixture([$obj1, $obj1, $obj2, $obj2, $obj2]),
                [$obj1, $obj2],
                [2, 3],
            ],
            [
                new IteratorAggregateFixture([0, '0', null, 0.0, false]),
                [0, '0', null, 0.0, false],
                [1, 1, 1, 1, 1],
            ],
            [
                new IteratorAggregateFixture([1, 1.0, true, '1']),
                [1, 1.0, true, '1'],
                [1, 1, 1, 1],
            ],
            [
                new IteratorAggregateFixture(['1', 1, '2', 2]),
                ['1', 1, '2', 2],
                [1, 1, 1, 1],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): numeric equivalence, so
     * 0, '0', null, 0.0 and false are one value, 1, '1' and 1.0 are one value; objects
     * compare by serialized state, so two empty stdClass instances are one value.
     * Frequencies are the size of each group.
     */
    public static function dataProviderForTraversablesCoercive(): array
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        return [
            [
                new IteratorAggregateFixture([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [2, 1, 1],
            ],
            [
                new IteratorAggregateFixture([['a' => 1], ['b' => 1]]),
                [['a' => 1], ['b' => 1]],
                [1, 1],
            ],
            [
                new IteratorAggregateFixture([$obj1, $obj1, $obj2, $obj2, $obj2]),
                [$obj1],
                [5],
            ],
            [
                new IteratorAggregateFixture([0, '0', null, 0.0, false]),
                [0],
                [5],
            ],
            [
                new IteratorAggregateFixture(['1', 1, '2', 2]),
                ['1', '2'],
                [2, 2],
            ],
            [
                new IteratorAggregateFixture([0, '0', null, 0.0, false, '1', 2, 1, '2', 2.0]),
                [0, '1', 2],
                [5, 2, 3],
            ],
        ];
    }

    /**
     * PHP array keys canonicalize numeric strings ('1' becomes int 1), so the keys are compared
     * loosely; the frequencies are compared strictly.
     *
     * @test         frequencies iterator_to_array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        array $expectedValues
     * @param        array $expectedFrequencies
     */
    public function testIteratorToArray(array $data, array $expectedValues, array $expectedFrequencies): void
    {
        // Given
        $iterator = Math::frequencies($data);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        // Loose: PHP array keys turn the string '1' into the int 1
        $this->assertEquals($expectedValues, \array_keys($result));
        $this->assertSame($expectedFrequencies, \array_values($result));
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
        $this->assertSame(2, $frequencies[0][1]);
        $this->assertSame($obj2, $frequencies[1][0]);
        $this->assertSame(1, $frequencies[1][1]);
    }

    /**
     * Regression for defect 8: a freed object's spl_object_id can be reissued to a later,
     * unrelated object. Each yielded object is dropped by the consumer before the next is
     * produced, so this can only be reproduced through a generator, not the four fixtures.
     *
     * @test frequencies retains identity anchors so ephemeral objects are not falsely merged
     */
    public function testEphemeralObjectsStrict(): void
    {
        // Given
        $data = self::freshObjectGenerator();

        // When
        $frequencies = [];
        foreach (Math::frequencies($data, true) as $value => $frequency) {
            $frequencies[] = [$value, $frequency];
        }

        // Then
        $this->assertCount(3, $frequencies);
        $this->assertSame(1, $frequencies[0][1]);
        $this->assertSame(1, $frequencies[1][1]);
        $this->assertSame(1, $frequencies[2][1]);
        $this->assertNotSame($frequencies[0][0], $frequencies[1][0]);
        $this->assertNotSame($frequencies[0][0], $frequencies[2][0]);
        $this->assertNotSame($frequencies[1][0], $frequencies[2][0]);
    }

    /**
     * @test frequencies retains identity anchors for closures, which hash by instance in both modes
     */
    public function testEphemeralClosuresCoercive(): void
    {
        // Given
        $data = self::freshClosureGenerator();

        // When
        $frequencies = [];
        foreach (Math::frequencies($data, false) as $value => $frequency) {
            $frequencies[] = [$value, $frequency];
        }

        // Then
        $this->assertCount(3, $frequencies);
        $this->assertSame(1, $frequencies[0][1]);
        $this->assertSame(1, $frequencies[1][1]);
        $this->assertSame(1, $frequencies[2][1]);
        $this->assertNotSame($frequencies[0][0], $frequencies[1][0]);
        $this->assertNotSame($frequencies[0][0], $frequencies[2][0]);
        $this->assertNotSame($frequencies[1][0], $frequencies[2][0]);
    }

    private static function freshObjectGenerator(): \Generator
    {
        for ($i = 0; $i < 3; $i++) {
            yield new \stdClass();
        }
    }

    private static function freshClosureGenerator(): \Generator
    {
        for ($i = 0; $i < 3; $i++) {
            yield fn () => $i;
        }
    }

    /**
     * Regression for defect 11: arrays that all share one reference slot collapse without an
     * identity anchor, because retaining the array does not retain the object it held when it
     * was yielded. Only a generator can reproduce this, not the four fixtures.
     *
     * @test frequencies reports five entries of count one for reference-slot arrays
     */
    public function testReferenceSlotArraysReportFiveEntriesOfCountOne(): void
    {
        // Given
        $data = self::referenceSlotArrays();

        // When
        $frequencies = [];
        foreach (Math::frequencies($data) as $value => $frequency) {
            $frequencies[] = [$value, $frequency];
        }

        // Then
        $this->assertCount(5, $frequencies);
        foreach ($frequencies as [, $frequency]) {
            $this->assertSame(1, $frequency);
        }
    }

    /**
     * Yields arrays that all share one reference slot: each yielded [&$slot] follows the next
     * assignment to $slot, so retaining the array does not retain the object it held when it was
     * yielded.
     *
     * @return \Generator<int, array{0: \stdClass}>
     */
    private static function referenceSlotArrays(): \Generator
    {
        $slot = null;

        for ($i = 0; $i < 5; $i++) {
            $slot = new \stdClass();

            yield [&$slot];
        }
    }
}

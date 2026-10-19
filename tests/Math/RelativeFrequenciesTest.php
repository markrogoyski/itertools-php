<?php

declare(strict_types=1);

namespace IterTools\Tests\Math;

use IterTools\Math;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class RelativeFrequenciesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Bound for the sum of the relative frequencies against 1. Each term is within 0.5 ulp
     * (about 1.1e-16) of its exact value, so the sum of the at most ten terms in these rows is
     * within about 2e-15 of 1.
     */
    private const SUM_DELTA = 1e-12;

    /**
     * Example data generated with Python:
     *   c = collections.Counter(['A', 'A', 'B', 'B', 'B', 'C'])
     *   [float(Fraction(n, 6)) for n in c.values()]
     *
     * @test relativeFrequencies example usage
     */
    public function testRelativeFrequenciesExampleUsage(): void
    {
        // Given
        $grades = ['A', 'A', 'B', 'B', 'B', 'C'];

        // And
        $frequencies = [];

        // When
        foreach (Math::relativeFrequencies($grades) as $grade => $frequency) {
            $frequencies[$grade] = $frequency;
        }

        // Then
        $this->assertSame(['A' => 0.3333333333333333, 'B' => 0.5, 'C' => 0.16666666666666666], $frequencies);
    }

    /**
     * @test relativeFrequencies array strict
     * @dataProvider dataProviderForArray
     * @dataProvider dataProviderForArrayFloats
     * @dataProvider dataProviderForArrayStrict
     * @param array $data
     * @param array $expectedValues
     * @param array $expectedRelativeFrequencies
     */
    public function testArrayStrict(array $data, array $expectedValues, array $expectedRelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0.0;

        // When
        foreach (Math::relativeFrequencies($data) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedRelativeFrequencies, $relativeFrequencies);

        // And (the sum is a derived property; see SUM_DELTA for the tolerance)
        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1.0, $sum, self::SUM_DELTA);
        }
    }

    /**
     * @test relativeFrequencies array coercive
     * @dataProvider dataProviderForArray
     * @dataProvider dataProviderForArrayFloats
     * @dataProvider dataProviderForArrayCoercive
     * @param array $data
     * @param array $expectedValues
     * @param array $expectedRelativeFrequencies
     */
    public function testArrayCoercive(array $data, array $expectedValues, array $expectedRelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0.0;

        // When
        foreach (Math::relativeFrequencies($data, false) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedRelativeFrequencies, $relativeFrequencies);

        // And (the sum is a derived property; see SUM_DELTA for the tolerance)
        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1.0, $sum, self::SUM_DELTA);
        }
    }

    /**
     * Test data generated with Python (first-seen order; ints and strings only, which Python
     * never equates across types):
     *   c = collections.Counter(data)
     *   values = list(c); frequencies = [float(Fraction(n, len(data))) for n in c.values()]
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
                [1.0],
            ],
            [
                [1],
                [1],
                [1.0],
            ],
            [
                ['a'],
                ['a'],
                [1.0],
            ],
            [
                [0, 0],
                [0],
                [1.0],
            ],
            [
                ['1', '1'],
                ['1'],
                [1.0],
            ],
            [
                [0, 1],
                [0, 1],
                [0.5, 0.5],
            ],
            [
                [0, 1, 1],
                [0, 1],
                [0.3333333333333333, 0.6666666666666666],
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
                [0.3333333333333333, 0.3333333333333333, 0.3333333333333333],
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

    /**
     * Test data generated with Python (floats only, equal under Python ==, strict and coercive
     * comparison alike, so -0.0 and 0.0 share one entry keyed by the first-seen -0.0):
     *   c = collections.Counter(data)
     *   values = list(c); frequencies = [float(Fraction(n, len(data))) for n in c.values()]
     */
    public static function dataProviderForArrayFloats(): array
    {
        return [
            [
                [0.5, 0.25, 0.5],
                [0.5, 0.25],
                [0.6666666666666666, 0.3333333333333333],
            ],
            [
                [0.1, 0.1, 0.2],
                [0.1, 0.2],
                [0.6666666666666666, 0.3333333333333333],
            ],
            [
                [0.30000000000000004, 0.3],
                [0.30000000000000004, 0.3],
                [0.5, 0.5],
            ],
            [
                [-0.0, 0.0],
                [-0.0],
                [1.0],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): two values are one iff ===, so
     * 0, '0', null, 0.0 and false are five values; nested arrays compare by key and value,
     * objects by instance.
     * Relative frequencies are float(Fraction(group size, len(data))).
     */
    public static function dataProviderForArrayStrict(): array
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
                [['a' => 1], ['b' => 1]],
                [['a' => 1], ['b' => 1]],
                [0.5, 0.5],
            ],
            [
                [$obj1, $obj1, $obj2, $obj2, $obj2],
                [$obj1, $obj2],
                [0.4, 0.6],
            ],
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

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): numeric equivalence, so
     * 0, '0', null, 0.0 and false are one value, 1, '1' and 1.0 are one value; objects
     * compare by serialized state, so two empty stdClass instances are one value.
     * Relative frequencies are float(Fraction(group size, len(data))).
     */
    public static function dataProviderForArrayCoercive(): array
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
                [['a' => 1], ['b' => 1]],
                [['a' => 1], ['b' => 1]],
                [0.5, 0.5],
            ],
            [
                [$obj1, $obj1, $obj2, $obj2, $obj2],
                [$obj1],
                [1.0],
            ],
            [
                [0, '0', null, 0.0, false],
                [0],
                [1.0],
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
     * @test relativeFrequencies generators strict
     * @dataProvider dataProviderForGenerators
     * @dataProvider dataProviderForGeneratorsFloats
     * @dataProvider dataProviderForGeneratorsStrict
     * @param \Generator $data
     * @param array $expectedValues
     * @param array $expectedRelativeFrequencies
     */
    public function testGeneratorsStrict(\Generator $data, array $expectedValues, array $expectedRelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0.0;

        // When
        foreach (Math::relativeFrequencies($data) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedRelativeFrequencies, $relativeFrequencies);

        // And (the sum is a derived property; see SUM_DELTA for the tolerance)
        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1.0, $sum, self::SUM_DELTA);
        }
    }

    /**
     * @test relativeFrequencies generators coercive
     * @dataProvider dataProviderForGenerators
     * @dataProvider dataProviderForGeneratorsFloats
     * @dataProvider dataProviderForGeneratorsCoercive
     * @param \Generator $data
     * @param array $expectedValues
     * @param array $expectedRelativeFrequencies
     */
    public function testGeneratorsCoercive(\Generator $data, array $expectedValues, array $expectedRelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0.0;

        // When
        foreach (Math::relativeFrequencies($data, false) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedRelativeFrequencies, $relativeFrequencies);

        // And (the sum is a derived property; see SUM_DELTA for the tolerance)
        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1.0, $sum, self::SUM_DELTA);
        }
    }

    /**
     * Test data generated with Python (first-seen order; ints and strings only, which Python
     * never equates across types):
     *   c = collections.Counter(data)
     *   values = list(c); frequencies = [float(Fraction(n, len(data))) for n in c.values()]
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
                [1.0],
            ],
            [
                GeneratorFixture::getGenerator([1]),
                [1],
                [1.0],
            ],
            [
                GeneratorFixture::getGenerator(['a']),
                ['a'],
                [1.0],
            ],
            [
                GeneratorFixture::getGenerator([0, 0]),
                [0],
                [1.0],
            ],
            [
                GeneratorFixture::getGenerator(['1', '1']),
                ['1'],
                [1.0],
            ],
            [
                GeneratorFixture::getGenerator([0, 1]),
                [0, 1],
                [0.5, 0.5],
            ],
            [
                GeneratorFixture::getGenerator([0, 1, 1]),
                [0, 1],
                [0.3333333333333333, 0.6666666666666666],
            ],
            [
                GeneratorFixture::getGenerator([0, 0, 1, 1]),
                [0, 1],
                [0.5, 0.5],
            ],
            [
                GeneratorFixture::getGenerator([0, 0, 0, 1]),
                [0, 1],
                [0.75, 0.25],
            ],
            [
                GeneratorFixture::getGenerator([0, 1, 0, 0]),
                [0, 1],
                [0.75, 0.25],
            ],
            [
                GeneratorFixture::getGenerator([1, 0, 0, 0]),
                [1, 0],
                [0.25, 0.75],
            ],
            [
                GeneratorFixture::getGenerator(['a', 'b', 'c']),
                ['a', 'b', 'c'],
                [0.3333333333333333, 0.3333333333333333, 0.3333333333333333],
            ],
            [
                GeneratorFixture::getGenerator(['a', 'c', 'b', 'c']),
                ['a', 'c', 'b'],
                [0.25, 0.5, 0.25],
            ],
            [
                GeneratorFixture::getGenerator([1, 'a', 1, 'b', 1, 'a', 1, 'b', 1, 'a']),
                [1, 'a', 'b'],
                [0.5, 0.3, 0.2],
            ],
        ];
    }

    /**
     * Test data generated with Python (floats only, equal under Python ==, strict and coercive
     * comparison alike, so -0.0 and 0.0 share one entry keyed by the first-seen -0.0):
     *   c = collections.Counter(data)
     *   values = list(c); frequencies = [float(Fraction(n, len(data))) for n in c.values()]
     */
    public static function dataProviderForGeneratorsFloats(): array
    {
        return [
            [
                GeneratorFixture::getGenerator([0.5, 0.25, 0.5]),
                [0.5, 0.25],
                [0.6666666666666666, 0.3333333333333333],
            ],
            [
                GeneratorFixture::getGenerator([0.1, 0.1, 0.2]),
                [0.1, 0.2],
                [0.6666666666666666, 0.3333333333333333],
            ],
            [
                GeneratorFixture::getGenerator([0.30000000000000004, 0.3]),
                [0.30000000000000004, 0.3],
                [0.5, 0.5],
            ],
            [
                GeneratorFixture::getGenerator([-0.0, 0.0]),
                [-0.0],
                [1.0],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): two values are one iff ===, so
     * 0, '0', null, 0.0 and false are five values; nested arrays compare by key and value,
     * objects by instance.
     * Relative frequencies are float(Fraction(group size, len(data))).
     */
    public static function dataProviderForGeneratorsStrict(): array
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        return [
            [
                GeneratorFixture::getGenerator([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [0.5, 0.25, 0.25],
            ],
            [
                GeneratorFixture::getGenerator([['a' => 1], ['b' => 1]]),
                [['a' => 1], ['b' => 1]],
                [0.5, 0.5],
            ],
            [
                GeneratorFixture::getGenerator([$obj1, $obj1, $obj2, $obj2, $obj2]),
                [$obj1, $obj2],
                [0.4, 0.6],
            ],
            [
                GeneratorFixture::getGenerator([0, '0', null, 0.0, false]),
                [0, '0', null, 0.0, false],
                [0.2, 0.2, 0.2, 0.2, 0.2],
            ],
            [
                GeneratorFixture::getGenerator([1, 1.0, true, '1']),
                [1, 1.0, true, '1'],
                [0.25, 0.25, 0.25, 0.25],
            ],
            [
                GeneratorFixture::getGenerator(['1', 1, '2', 2]),
                ['1', 1, '2', 2],
                [0.25, 0.25, 0.25, 0.25],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): numeric equivalence, so
     * 0, '0', null, 0.0 and false are one value, 1, '1' and 1.0 are one value; objects
     * compare by serialized state, so two empty stdClass instances are one value.
     * Relative frequencies are float(Fraction(group size, len(data))).
     */
    public static function dataProviderForGeneratorsCoercive(): array
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        return [
            [
                GeneratorFixture::getGenerator([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [0.5, 0.25, 0.25],
            ],
            [
                GeneratorFixture::getGenerator([['a' => 1], ['b' => 1]]),
                [['a' => 1], ['b' => 1]],
                [0.5, 0.5],
            ],
            [
                GeneratorFixture::getGenerator([$obj1, $obj1, $obj2, $obj2, $obj2]),
                [$obj1],
                [1.0],
            ],
            [
                GeneratorFixture::getGenerator([0, '0', null, 0.0, false]),
                [0],
                [1.0],
            ],
            [
                GeneratorFixture::getGenerator(['1', 1, '2', 2]),
                ['1', '2'],
                [0.5, 0.5],
            ],
            [
                GeneratorFixture::getGenerator([0, '0', null, 0.0, false, '1', 2, 1, '2', 2.0]),
                [0, '1', 2],
                [0.5, 0.2, 0.3],
            ],
        ];
    }

    /**
     * @test relativeFrequencies iterators strict
     * @dataProvider dataProviderForIterators
     * @dataProvider dataProviderForIteratorsFloats
     * @dataProvider dataProviderForIteratorsStrict
     * @param \Iterator $data
     * @param array $expectedValues
     * @param array $expectedRelativeFrequencies
     */
    public function testIteratorsStrict(\Iterator $data, array $expectedValues, array $expectedRelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0.0;

        // When
        foreach (Math::relativeFrequencies($data) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedRelativeFrequencies, $relativeFrequencies);

        // And (the sum is a derived property; see SUM_DELTA for the tolerance)
        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1.0, $sum, self::SUM_DELTA);
        }
    }

    /**
     * @test relativeFrequencies iterators coercive
     * @dataProvider dataProviderForIterators
     * @dataProvider dataProviderForIteratorsFloats
     * @dataProvider dataProviderForIteratorsCoercive
     * @param \Iterator $data
     * @param array $expectedValues
     * @param array $expectedRelativeFrequencies
     */
    public function testIteratorsCoercive(\Iterator $data, array $expectedValues, array $expectedRelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0.0;

        // When
        foreach (Math::relativeFrequencies($data, false) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedRelativeFrequencies, $relativeFrequencies);

        // And (the sum is a derived property; see SUM_DELTA for the tolerance)
        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1.0, $sum, self::SUM_DELTA);
        }
    }

    /**
     * Test data generated with Python (first-seen order; ints and strings only, which Python
     * never equates across types):
     *   c = collections.Counter(data)
     *   values = list(c); frequencies = [float(Fraction(n, len(data))) for n in c.values()]
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
                [1.0],
            ],
            [
                new ArrayIteratorFixture([1]),
                [1],
                [1.0],
            ],
            [
                new ArrayIteratorFixture(['a']),
                ['a'],
                [1.0],
            ],
            [
                new ArrayIteratorFixture([0, 0]),
                [0],
                [1.0],
            ],
            [
                new ArrayIteratorFixture(['1', '1']),
                ['1'],
                [1.0],
            ],
            [
                new ArrayIteratorFixture([0, 1]),
                [0, 1],
                [0.5, 0.5],
            ],
            [
                new ArrayIteratorFixture([0, 1, 1]),
                [0, 1],
                [0.3333333333333333, 0.6666666666666666],
            ],
            [
                new ArrayIteratorFixture([0, 0, 1, 1]),
                [0, 1],
                [0.5, 0.5],
            ],
            [
                new ArrayIteratorFixture([0, 0, 0, 1]),
                [0, 1],
                [0.75, 0.25],
            ],
            [
                new ArrayIteratorFixture([0, 1, 0, 0]),
                [0, 1],
                [0.75, 0.25],
            ],
            [
                new ArrayIteratorFixture([1, 0, 0, 0]),
                [1, 0],
                [0.25, 0.75],
            ],
            [
                new ArrayIteratorFixture(['a', 'b', 'c']),
                ['a', 'b', 'c'],
                [0.3333333333333333, 0.3333333333333333, 0.3333333333333333],
            ],
            [
                new ArrayIteratorFixture(['a', 'c', 'b', 'c']),
                ['a', 'c', 'b'],
                [0.25, 0.5, 0.25],
            ],
            [
                new ArrayIteratorFixture([1, 'a', 1, 'b', 1, 'a', 1, 'b', 1, 'a']),
                [1, 'a', 'b'],
                [0.5, 0.3, 0.2],
            ],
        ];
    }

    /**
     * Test data generated with Python (floats only, equal under Python ==, strict and coercive
     * comparison alike, so -0.0 and 0.0 share one entry keyed by the first-seen -0.0):
     *   c = collections.Counter(data)
     *   values = list(c); frequencies = [float(Fraction(n, len(data))) for n in c.values()]
     */
    public static function dataProviderForIteratorsFloats(): array
    {
        return [
            [
                new ArrayIteratorFixture([0.5, 0.25, 0.5]),
                [0.5, 0.25],
                [0.6666666666666666, 0.3333333333333333],
            ],
            [
                new ArrayIteratorFixture([0.1, 0.1, 0.2]),
                [0.1, 0.2],
                [0.6666666666666666, 0.3333333333333333],
            ],
            [
                new ArrayIteratorFixture([0.30000000000000004, 0.3]),
                [0.30000000000000004, 0.3],
                [0.5, 0.5],
            ],
            [
                new ArrayIteratorFixture([-0.0, 0.0]),
                [-0.0],
                [1.0],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): two values are one iff ===, so
     * 0, '0', null, 0.0 and false are five values; nested arrays compare by key and value,
     * objects by instance.
     * Relative frequencies are float(Fraction(group size, len(data))).
     */
    public static function dataProviderForIteratorsStrict(): array
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        return [
            [
                new ArrayIteratorFixture([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [0.5, 0.25, 0.25],
            ],
            [
                new ArrayIteratorFixture([['a' => 1], ['b' => 1]]),
                [['a' => 1], ['b' => 1]],
                [0.5, 0.5],
            ],
            [
                new ArrayIteratorFixture([$obj1, $obj1, $obj2, $obj2, $obj2]),
                [$obj1, $obj2],
                [0.4, 0.6],
            ],
            [
                new ArrayIteratorFixture([0, '0', null, 0.0, false]),
                [0, '0', null, 0.0, false],
                [0.2, 0.2, 0.2, 0.2, 0.2],
            ],
            [
                new ArrayIteratorFixture([1, 1.0, true, '1']),
                [1, 1.0, true, '1'],
                [0.25, 0.25, 0.25, 0.25],
            ],
            [
                new ArrayIteratorFixture(['1', 1, '2', 2]),
                ['1', 1, '2', 2],
                [0.25, 0.25, 0.25, 0.25],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): numeric equivalence, so
     * 0, '0', null, 0.0 and false are one value, 1, '1' and 1.0 are one value; objects
     * compare by serialized state, so two empty stdClass instances are one value.
     * Relative frequencies are float(Fraction(group size, len(data))).
     */
    public static function dataProviderForIteratorsCoercive(): array
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        return [
            [
                new ArrayIteratorFixture([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [0.5, 0.25, 0.25],
            ],
            [
                new ArrayIteratorFixture([['a' => 1], ['b' => 1]]),
                [['a' => 1], ['b' => 1]],
                [0.5, 0.5],
            ],
            [
                new ArrayIteratorFixture([$obj1, $obj1, $obj2, $obj2, $obj2]),
                [$obj1],
                [1.0],
            ],
            [
                new ArrayIteratorFixture([0, '0', null, 0.0, false]),
                [0],
                [1.0],
            ],
            [
                new ArrayIteratorFixture(['1', 1, '2', 2]),
                ['1', '2'],
                [0.5, 0.5],
            ],
            [
                new ArrayIteratorFixture([0, '0', null, 0.0, false, '1', 2, 1, '2', 2.0]),
                [0, '1', 2],
                [0.5, 0.2, 0.3],
            ],
        ];
    }

    /**
     * @test relativeFrequencies traversables strict
     * @dataProvider dataProviderForTraversables
     * @dataProvider dataProviderForTraversablesFloats
     * @dataProvider dataProviderForTraversablesStrict
     * @param \Traversable $data
     * @param array $expectedValues
     * @param array $expectedRelativeFrequencies
     */
    public function testTraversablesStrict(\Traversable $data, array $expectedValues, array $expectedRelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0.0;

        // When
        foreach (Math::relativeFrequencies($data) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedRelativeFrequencies, $relativeFrequencies);

        // And (the sum is a derived property; see SUM_DELTA for the tolerance)
        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1.0, $sum, self::SUM_DELTA);
        }
    }

    /**
     * @test relativeFrequencies traversables coercive
     * @dataProvider dataProviderForTraversables
     * @dataProvider dataProviderForTraversablesFloats
     * @dataProvider dataProviderForTraversablesCoercive
     * @param \Traversable $data
     * @param array $expectedValues
     * @param array $expectedRelativeFrequencies
     */
    public function testTraversablesCoercive(\Traversable $data, array $expectedValues, array $expectedRelativeFrequencies): void
    {
        // Given
        $values = [];
        $relativeFrequencies = [];
        $sum = 0.0;

        // When
        foreach (Math::relativeFrequencies($data, false) as $value => $frequency) {
            $values[] = $value;
            $relativeFrequencies[] = $frequency;
            $sum += $frequency;
        }

        // Then
        $this->assertSame($expectedValues, $values);
        $this->assertSame($expectedRelativeFrequencies, $relativeFrequencies);

        // And (the sum is a derived property; see SUM_DELTA for the tolerance)
        if (\count($values) > 0) {
            $this->assertEqualsWithDelta(1.0, $sum, self::SUM_DELTA);
        }
    }

    /**
     * Test data generated with Python (first-seen order; ints and strings only, which Python
     * never equates across types):
     *   c = collections.Counter(data)
     *   values = list(c); frequencies = [float(Fraction(n, len(data))) for n in c.values()]
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
                [1.0],
            ],
            [
                new IteratorAggregateFixture([1]),
                [1],
                [1.0],
            ],
            [
                new IteratorAggregateFixture(['a']),
                ['a'],
                [1.0],
            ],
            [
                new IteratorAggregateFixture([0, 0]),
                [0],
                [1.0],
            ],
            [
                new IteratorAggregateFixture(['1', '1']),
                ['1'],
                [1.0],
            ],
            [
                new IteratorAggregateFixture([0, 1]),
                [0, 1],
                [0.5, 0.5],
            ],
            [
                new IteratorAggregateFixture([0, 1, 1]),
                [0, 1],
                [0.3333333333333333, 0.6666666666666666],
            ],
            [
                new IteratorAggregateFixture([0, 0, 1, 1]),
                [0, 1],
                [0.5, 0.5],
            ],
            [
                new IteratorAggregateFixture([0, 0, 0, 1]),
                [0, 1],
                [0.75, 0.25],
            ],
            [
                new IteratorAggregateFixture([0, 1, 0, 0]),
                [0, 1],
                [0.75, 0.25],
            ],
            [
                new IteratorAggregateFixture([1, 0, 0, 0]),
                [1, 0],
                [0.25, 0.75],
            ],
            [
                new IteratorAggregateFixture(['a', 'b', 'c']),
                ['a', 'b', 'c'],
                [0.3333333333333333, 0.3333333333333333, 0.3333333333333333],
            ],
            [
                new IteratorAggregateFixture(['a', 'c', 'b', 'c']),
                ['a', 'c', 'b'],
                [0.25, 0.5, 0.25],
            ],
            [
                new IteratorAggregateFixture([1, 'a', 1, 'b', 1, 'a', 1, 'b', 1, 'a']),
                [1, 'a', 'b'],
                [0.5, 0.3, 0.2],
            ],
        ];
    }

    /**
     * Test data generated with Python (floats only, equal under Python ==, strict and coercive
     * comparison alike, so -0.0 and 0.0 share one entry keyed by the first-seen -0.0):
     *   c = collections.Counter(data)
     *   values = list(c); frequencies = [float(Fraction(n, len(data))) for n in c.values()]
     */
    public static function dataProviderForTraversablesFloats(): array
    {
        return [
            [
                new IteratorAggregateFixture([0.5, 0.25, 0.5]),
                [0.5, 0.25],
                [0.6666666666666666, 0.3333333333333333],
            ],
            [
                new IteratorAggregateFixture([0.1, 0.1, 0.2]),
                [0.1, 0.2],
                [0.6666666666666666, 0.3333333333333333],
            ],
            [
                new IteratorAggregateFixture([0.30000000000000004, 0.3]),
                [0.30000000000000004, 0.3],
                [0.5, 0.5],
            ],
            [
                new IteratorAggregateFixture([-0.0, 0.0]),
                [-0.0],
                [1.0],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): two values are one iff ===, so
     * 0, '0', null, 0.0 and false are five values; nested arrays compare by key and value,
     * objects by instance.
     * Relative frequencies are float(Fraction(group size, len(data))).
     */
    public static function dataProviderForTraversablesStrict(): array
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        return [
            [
                new IteratorAggregateFixture([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [0.5, 0.25, 0.25],
            ],
            [
                new IteratorAggregateFixture([['a' => 1], ['b' => 1]]),
                [['a' => 1], ['b' => 1]],
                [0.5, 0.5],
            ],
            [
                new IteratorAggregateFixture([$obj1, $obj1, $obj2, $obj2, $obj2]),
                [$obj1, $obj2],
                [0.4, 0.6],
            ],
            [
                new IteratorAggregateFixture([0, '0', null, 0.0, false]),
                [0, '0', null, 0.0, false],
                [0.2, 0.2, 0.2, 0.2, 0.2],
            ],
            [
                new IteratorAggregateFixture([1, 1.0, true, '1']),
                [1, 1.0, true, '1'],
                [0.25, 0.25, 0.25, 0.25],
            ],
            [
                new IteratorAggregateFixture(['1', 1, '2', 2]),
                ['1', 1, '2', 2],
                [0.25, 0.25, 0.25, 0.25],
            ],
        ];
    }

    /**
     * Textbook definition, README "Strict and Coercive Types" (Python cannot express it: it
     * equates 1, 1.0 and True, and keeps '1' apart): numeric equivalence, so
     * 0, '0', null, 0.0 and false are one value, 1, '1' and 1.0 are one value; objects
     * compare by serialized state, so two empty stdClass instances are one value.
     * Relative frequencies are float(Fraction(group size, len(data))).
     */
    public static function dataProviderForTraversablesCoercive(): array
    {
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        return [
            [
                new IteratorAggregateFixture([[1, 2, 3], [1], [1, 2, 3], [2]]),
                [[1, 2, 3], [1], [2]],
                [0.5, 0.25, 0.25],
            ],
            [
                new IteratorAggregateFixture([['a' => 1], ['b' => 1]]),
                [['a' => 1], ['b' => 1]],
                [0.5, 0.5],
            ],
            [
                new IteratorAggregateFixture([$obj1, $obj1, $obj2, $obj2, $obj2]),
                [$obj1],
                [1.0],
            ],
            [
                new IteratorAggregateFixture([0, '0', null, 0.0, false]),
                [0],
                [1.0],
            ],
            [
                new IteratorAggregateFixture(['1', 1, '2', 2]),
                ['1', '2'],
                [0.5, 0.5],
            ],
            [
                new IteratorAggregateFixture([0, '0', null, 0.0, false, '1', 2, 1, '2', 2.0]),
                [0, '1', 2],
                [0.5, 0.2, 0.3],
            ],
        ];
    }

    /**
     * PHP array keys canonicalize numeric strings ('1' becomes int 1), so the keys are compared
     * loosely; the relative frequencies are compared strictly.
     *
     * @test         relativeFrequencies iterator_to_array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        array $expectedValues
     * @param        array $expectedRelativeFrequencies
     */
    public function testIteratorToArray(array $data, array $expectedValues, array $expectedRelativeFrequencies): void
    {
        // Given
        $iterator = Math::relativeFrequencies($data);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        // Loose: PHP array keys turn the string '1' into the int 1
        $this->assertEquals($expectedValues, \array_keys($result));
        $this->assertSame($expectedRelativeFrequencies, \array_values($result));
    }

    /**
     * @test         relativeFrequencies yields a float when there is only one distinct value
     * @dataProvider dataProviderForSingleDistinctValue
     * @param        iterable $data
     * @param        bool     $strict
     * @param        array    $expected
     */
    public function testSingleDistinctValueYieldsFloat(iterable $data, bool $strict, array $expected): void
    {
        // When
        $result = \iterator_to_array(Math::relativeFrequencies($data, $strict));

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForSingleDistinctValue(): array
    {
        return [
            'array one element'               => [['a'], true, ['a' => 1.0]],
            'array repeated'                  => [['a', 'a', 'a'], true, ['a' => 1.0]],
            'array coercive collapse'         => [[1, '1', 1.0, true], false, [1 => 1.0]],
            'generator one element'           => [GeneratorFixture::getGenerator(['a']), true, ['a' => 1.0]],
            'generator repeated'              => [GeneratorFixture::getGenerator(['a', 'a', 'a']), true, ['a' => 1.0]],
            'generator coercive collapse'     => [GeneratorFixture::getGenerator([1, '1', 1.0, true]), false, [1 => 1.0]],
            'iterator one element'            => [new ArrayIteratorFixture(['a']), true, ['a' => 1.0]],
            'iterator repeated'               => [new ArrayIteratorFixture(['a', 'a', 'a']), true, ['a' => 1.0]],
            'iterator coercive collapse'      => [new ArrayIteratorFixture([1, '1', 1.0, true]), false, [1 => 1.0]],
            'aggregate one element'           => [new IteratorAggregateFixture(['a']), true, ['a' => 1.0]],
            'aggregate repeated'              => [new IteratorAggregateFixture(['a', 'a', 'a']), true, ['a' => 1.0]],
            'aggregate coercive collapse'     => [new IteratorAggregateFixture([1, '1', 1.0, true]), false, [1 => 1.0]],
        ];
    }
}

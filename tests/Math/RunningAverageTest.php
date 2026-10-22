<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

use IterTools\Math;
use IterTools\Tests\Fixture;

class RunningAverageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         runningAverage array
     * @dataProvider dataProviderForArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testArray(array $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (the exact mean of the first n terms, rounded once):
     *   [float(t / n) for n, t in enumerate(itertools.accumulate(map(Fraction, data)), 1)]
     * The int-vs-float result type is the library's contract, not the oracle's: an average is an
     * int when the int running total divides evenly by the count, otherwise a float (a total past
     * the int64 range is already a float).
     * Float rows whose running total rounds away from the exact mean are in
     * dataProviderForFloatsWithinRounding.
     */
    public static function dataProviderForArray(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                [0],
                [0],
            ],
            [
                [1],
                [1],
            ],
            [
                [2],
                [2],
            ],
            [
                [1, 1, 1],
                [1, 1, 1],
            ],
            [
                [1, 2, 3],
                [1, 1.5, 2],
            ],
            [
                [2, 2, 2],
                [2, 2, 2],
            ],
            [
                [1, 3, 5],
                [1, 2, 3],
            ],
            [
                [1, 2, 3, 4, 5],
                [1, 1.5, 2, 2.5, 3],
            ],
            [
                [1, 2, 3, -4, 5],
                [1, 1.5, 2, 0.5, 1.4],
            ],
            [
                [1, 2, 3, -4, -5],
                [1, 1.5, 2, 0.5, -0.6],
            ],
            [
                [0.5],
                [0.5],
            ],
            [
                [1.5, 2.5, -4.0],
                [1.5, 2.0, 0.0],
            ],
            [
                [1, 2.5, 3],
                [1, 1.75, 2.1666666666666665],
            ],
            [
                [1.0, 3.0],
                [1.0, 2.0],
            ],
        ];
    }

    /**
     * @test         runningAverage array of integers beyond 2^31
     * @dataProvider dataProviderForArray64Bit
     * @param        array $numbers
     * @param        array $expected
     */
    public function testArray64Bit(array $numbers, array $expected): void
    {
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers.');
        }

        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (the exact mean of the first n terms, rounded once):
     *   [float(t / n) for n, t in enumerate(itertools.accumulate(map(Fraction, data)), 1)]
     * The int-vs-float result type is the library's contract, not the oracle's: an average is an
     * int when the int running total divides evenly by the count, otherwise a float (a total past
     * the int64 range is already a float).
     */
    public static function dataProviderForArray64Bit(): array
    {
        return [
            [
                [4294967296, 4294967298],
                [4294967296, 4294967297],
            ],
            [
                [4294967296, 4294967297],
                [4294967296, 4294967296.5],
            ],
            [
                [\PHP_INT_MAX, 1],
                [\PHP_INT_MAX, 4.611686018427388E+18],
            ],
            [
                [\PHP_INT_MIN, -1],
                [\PHP_INT_MIN, -4.611686018427388E+18],
            ],
        ];
    }

    /**
     * @test         runningAverage array with initial value
     * @dataProvider dataProviderForArrayWithInitialValue
     * @param        array     $numbers
     * @param        int|float $initialValue
     * @param        array     $expected
     */
    public function testArrayWithInitialValue(array $numbers, int|float $initialValue, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers, $initialValue) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (the exact mean of the first n terms, rounded once):
     *   [float(t / n) for n, t in enumerate(itertools.accumulate(map(Fraction, [initial] + data)), 1)]
     * The int-vs-float result type is the library's contract, not the oracle's: an average is an
     * int when the int running total divides evenly by the count, otherwise a float (a total past
     * the int64 range is already a float).
     * Float rows whose running total rounds away from the exact mean are in
     * dataProviderForFloatsWithinRounding.
     */
    public static function dataProviderForArrayWithInitialValue(): array
    {
        return [
            [
                [],
                0,
                [0],
            ],
            [
                [1, 2, 3],
                0,
                [0, 0.5, 1, 1.5],
            ],
            [
                [],
                5,
                [5],
            ],
            [
                [0],
                5,
                [5, 2.5],
            ],
            [
                [1],
                5,
                [5, 3],
            ],
            [
                [2],
                5,
                [5, 3.5],
            ],
            [
                [1, 1, 1],
                5,
                [5, 3, 2.3333333333333335, 2],
            ],
            [
                [1, 2, 3],
                5,
                [5, 3, 2.6666666666666665, 2.75],
            ],
            [
                [2, 2, 2],
                5,
                [5, 3.5, 3, 2.75],
            ],
            [
                [1, 3, 5],
                5,
                [5, 3, 3, 3.5],
            ],
            [
                [1, 2, 3, 4, 5],
                5,
                [5, 3, 2.6666666666666665, 2.75, 3, 3.3333333333333335],
            ],
            [
                [1, 2, 3, -4, 5],
                5,
                [5, 3, 2.6666666666666665, 2.75, 1.4, 2],
            ],
            [
                [1, 2, 3, -4, -5],
                5,
                [5, 3, 2.6666666666666665, 2.75, 1.4, 0.3333333333333333],
            ],
            [
                [1, 2],
                0.5,
                [0.5, 0.75, 1.1666666666666667],
            ],
            [
                [1.5, -2.5],
                -5,
                [-5, -1.75, -2.0],
            ],
        ];
    }

    /**
     * @test         runningAverage array of integers beyond 2^31 with initial value
     * @dataProvider dataProviderForArrayWithInitialValue64Bit
     * @param        array $numbers
     * @param        int   $initialValue
     * @param        array $expected
     */
    public function testArrayWithInitialValue64Bit(array $numbers, int $initialValue, array $expected): void
    {
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers.');
        }

        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers, $initialValue) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (the exact mean of the first n terms, rounded once):
     *   [float(t / n) for n, t in enumerate(itertools.accumulate(map(Fraction, [initial] + data)), 1)]
     * The int-vs-float result type is the library's contract, not the oracle's: an average is an
     * int when the int running total divides evenly by the count, otherwise a float (a total past
     * the int64 range is already a float).
     */
    public static function dataProviderForArrayWithInitialValue64Bit(): array
    {
        return [
            [
                [2, 4],
                4294967296,
                [4294967296, 2147483649, 1431655767.3333333],
            ],
            [
                [1],
                \PHP_INT_MAX,
                [\PHP_INT_MAX, 4.611686018427388E+18],
            ],
            [
                [-1],
                \PHP_INT_MIN,
                [\PHP_INT_MIN, -4.611686018427388E+18],
            ],
        ];
    }

    /**
     * @test         runningAverage of floats within the floating-point summation error bound
     * @dataProvider dataProviderForFloatsWithinRounding
     * @param        array           $numbers
     * @param        int|float|null  $initialValue
     * @param        array<float>    $expected
     * @param        array<float>    $deltas
     */
    public function testFloatsWithinRounding(
        array $numbers,
        int|float|null $initialValue,
        array $expected,
        array $deltas
    ): void {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers, $initialValue) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertCount(\count($expected), $result);
        foreach ($expected as $i => $value) {
            $this->assertIsFloat($result[$i]);
            $this->assertEqualsWithDelta($value, $result[$i], $deltas[$i]);
        }
    }

    /**
     * Test data generated with Python (the exact mean of the first n terms, rounded once):
     *   [float(t / n) for n, t in enumerate(itertools.accumulate(map(Fraction, seq)), 1)]
     * where seq is [initial] + data when there is an initial value.
     * The library divides a left-to-right floating-point running total, so its result may differ
     * from the correctly rounded mean, e.g. [0.1, 0.2, 0.3] ends 0.20000000000000004, not 0.2.
     * Each element's delta is the a-priori bound on that error over its first k terms,
     *   sys.float_info.epsilon * math.fsum(map(abs, seq[:k]))
     * since |error| <= (k-1)·u·Σ|x|/k + u·|mean| < ε·Σ|x|, with unit roundoff u = ε/2
     * (Higham, Accuracy and Stability of Numerical Algorithms, 2nd ed., §4.2).
     */
    public static function dataProviderForFloatsWithinRounding(): array
    {
        return [
            [
                [0.1, 0.2, 0.3],
                null,
                [0.1, 0.15000000000000002, 0.2],
                [2.2204460492503132E-17, 6.66133814775094E-17, 1.3322676295501878E-16],
            ],
            [
                [0.2, 0.3],
                0.1,
                [0.1, 0.15000000000000002, 0.2],
                [2.2204460492503132E-17, 6.66133814775094E-17, 1.3322676295501878E-16],
            ],
        ];
    }

    /**
     * @test         runningAverage generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $numbers
     * @param        array      $expected
     */
    public function testGenerators(\Generator $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (the exact mean of the first n terms, rounded once):
     *   [float(t / n) for n, t in enumerate(itertools.accumulate(map(Fraction, data)), 1)]
     * The int-vs-float result type is the library's contract, not the oracle's: an average is an
     * int when the int running total divides evenly by the count, otherwise a float (a total past
     * the int64 range is already a float).
     * Float rows whose running total rounds away from the exact mean are in
     * dataProviderForFloatsWithinRounding.
     */
    public static function dataProviderForGenerators(): array
    {
        return [
            [
                Fixture\GeneratorFixture::getGenerator([]),
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0]),
                [0],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1]),
                [1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([2]),
                [2],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 1, 1]),
                [1, 1, 1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                [1, 1.5, 2],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([2, 2, 2]),
                [2, 2, 2],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 3, 5]),
                [1, 2, 3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5]),
                [1, 1.5, 2, 2.5, 3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, -4, 5]),
                [1, 1.5, 2, 0.5, 1.4],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3, -4, -5]),
                [1, 1.5, 2, 0.5, -0.6],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0.5]),
                [0.5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1.5, 2.5, -4.0]),
                [1.5, 2.0, 0.0],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2.5, 3]),
                [1, 1.75, 2.1666666666666665],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1.0, 3.0]),
                [1.0, 2.0],
            ],
        ];
    }

    /**
     * @test         runningAverage iterators
     * @dataProvider dataProviderForIterators
     * @param        iterable $numbers
     * @param        array    $expected
     */
    public function testIterators(iterable $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (the exact mean of the first n terms, rounded once):
     *   [float(t / n) for n, t in enumerate(itertools.accumulate(map(Fraction, data)), 1)]
     * The int-vs-float result type is the library's contract, not the oracle's: an average is an
     * int when the int running total divides evenly by the count, otherwise a float (a total past
     * the int64 range is already a float).
     * Float rows whose running total rounds away from the exact mean are in
     * dataProviderForFloatsWithinRounding.
     */
    public static function dataProviderForIterators(): array
    {
        return [
            [
                new Fixture\ArrayIteratorFixture([]),
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([0]),
                [0],
            ],
            [
                new Fixture\ArrayIteratorFixture([1]),
                [1],
            ],
            [
                new Fixture\ArrayIteratorFixture([2]),
                [2],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 1, 1]),
                [1, 1, 1],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                [1, 1.5, 2],
            ],
            [
                new Fixture\ArrayIteratorFixture([2, 2, 2]),
                [2, 2, 2],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 3, 5]),
                [1, 2, 3],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5]),
                [1, 1.5, 2, 2.5, 3],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, -4, 5]),
                [1, 1.5, 2, 0.5, 1.4],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3, -4, -5]),
                [1, 1.5, 2, 0.5, -0.6],
            ],
            [
                new Fixture\ArrayIteratorFixture([0.5]),
                [0.5],
            ],
            [
                new Fixture\ArrayIteratorFixture([1.5, 2.5, -4.0]),
                [1.5, 2.0, 0.0],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2.5, 3]),
                [1, 1.75, 2.1666666666666665],
            ],
            [
                new Fixture\ArrayIteratorFixture([1.0, 3.0]),
                [1.0, 2.0],
            ],
        ];
    }

    /**
     * @test         runningAverage traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $numbers
     * @param        array        $expected
     */
    public function testTraversables(\Traversable $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (the exact mean of the first n terms, rounded once):
     *   [float(t / n) for n, t in enumerate(itertools.accumulate(map(Fraction, data)), 1)]
     * The int-vs-float result type is the library's contract, not the oracle's: an average is an
     * int when the int running total divides evenly by the count, otherwise a float (a total past
     * the int64 range is already a float).
     * Float rows whose running total rounds away from the exact mean are in
     * dataProviderForFloatsWithinRounding.
     */
    public static function dataProviderForTraversables(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([]),
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([0]),
                [0],
            ],
            [
                new Fixture\IteratorAggregateFixture([1]),
                [1],
            ],
            [
                new Fixture\IteratorAggregateFixture([2]),
                [2],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 1, 1]),
                [1, 1, 1],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                [1, 1.5, 2],
            ],
            [
                new Fixture\IteratorAggregateFixture([2, 2, 2]),
                [2, 2, 2],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 3, 5]),
                [1, 2, 3],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, 4, 5]),
                [1, 1.5, 2, 2.5, 3],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, -4, 5]),
                [1, 1.5, 2, 0.5, 1.4],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3, -4, -5]),
                [1, 1.5, 2, 0.5, -0.6],
            ],
            [
                new Fixture\IteratorAggregateFixture([0.5]),
                [0.5],
            ],
            [
                new Fixture\IteratorAggregateFixture([1.5, 2.5, -4.0]),
                [1.5, 2.0, 0.0],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2.5, 3]),
                [1, 1.75, 2.1666666666666665],
            ],
            [
                new Fixture\IteratorAggregateFixture([1.0, 3.0]),
                [1.0, 2.0],
            ],
        ];
    }

    /**
     * @test         runningAverage array whose float running total overflows although the means are finite
     * @dataProvider dataProviderForSumOverflowArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testSumOverflowArray(array $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (the exact mean of the first n terms, rounded once):
     *   [float(t / n) for n, t in enumerate(itertools.accumulate(map(Fraction, data)), 1)]
     */
    public static function dataProviderForSumOverflowArray(): array
    {
        return [
            [
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX],
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX],
            ],
            [
                [-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX],
                [-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX],
            ],
            [
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX],
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307],
            ],
            [
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX],
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307, 0.0],
            ],
            [
                [\PHP_FLOAT_MAX, 1, \PHP_FLOAT_MAX],
                [\PHP_FLOAT_MAX, 8.988465674311579E+307, 1.1984620899082105E+308],
            ],
            [
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, 1e-310],
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307, 0.0, 2.0E-311],
            ],
            [
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \PHP_FLOAT_MAX],
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307, 0.0, 3.5953862697246315E+307, 5.992310449541053E+307],
            ],
        ];
    }

    /**
     * @test         runningAverage generators whose float running total overflows although the means are finite
     * @dataProvider dataProviderForSumOverflowGenerators
     * @param        \Generator $numbers
     * @param        array $expected
     */
    public function testSumOverflowGenerators(\Generator $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (the exact mean of the first n terms, rounded once):
     *   [float(t / n) for n, t in enumerate(itertools.accumulate(map(Fraction, data)), 1)]
     */
    public static function dataProviderForSumOverflowGenerators(): array
    {
        return [
            [
                Fixture\GeneratorFixture::getGenerator([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX]),
                [-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307, 0.0],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([\PHP_FLOAT_MAX, 1, \PHP_FLOAT_MAX]),
                [\PHP_FLOAT_MAX, 8.988465674311579E+307, 1.1984620899082105E+308],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, 1e-310]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307, 0.0, 2.0E-311],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \PHP_FLOAT_MAX]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307, 0.0, 3.5953862697246315E+307, 5.992310449541053E+307],
            ],
        ];
    }

    /**
     * @test         runningAverage iterators whose float running total overflows although the means are finite
     * @dataProvider dataProviderForSumOverflowIterators
     * @param        \Iterator $numbers
     * @param        array $expected
     */
    public function testSumOverflowIterators(\Iterator $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (the exact mean of the first n terms, rounded once):
     *   [float(t / n) for n, t in enumerate(itertools.accumulate(map(Fraction, data)), 1)]
     */
    public static function dataProviderForSumOverflowIterators(): array
    {
        return [
            [
                new Fixture\ArrayIteratorFixture([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX],
            ],
            [
                new Fixture\ArrayIteratorFixture([-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX]),
                [-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX],
            ],
            [
                new Fixture\ArrayIteratorFixture([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307],
            ],
            [
                new Fixture\ArrayIteratorFixture([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307, 0.0],
            ],
            [
                new Fixture\ArrayIteratorFixture([\PHP_FLOAT_MAX, 1, \PHP_FLOAT_MAX]),
                [\PHP_FLOAT_MAX, 8.988465674311579E+307, 1.1984620899082105E+308],
            ],
            [
                new Fixture\ArrayIteratorFixture([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, 1e-310]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307, 0.0, 2.0E-311],
            ],
            [
                new Fixture\ArrayIteratorFixture([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \PHP_FLOAT_MAX]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307, 0.0, 3.5953862697246315E+307, 5.992310449541053E+307],
            ],
        ];
    }

    /**
     * @test         runningAverage traversables whose float running total overflows although the means are finite
     * @dataProvider dataProviderForSumOverflowTraversables
     * @param        \Traversable $numbers
     * @param        array $expected
     */
    public function testSumOverflowTraversables(\Traversable $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (the exact mean of the first n terms, rounded once):
     *   [float(t / n) for n, t in enumerate(itertools.accumulate(map(Fraction, data)), 1)]
     */
    public static function dataProviderForSumOverflowTraversables(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX],
            ],
            [
                new Fixture\IteratorAggregateFixture([-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX]),
                [-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX],
            ],
            [
                new Fixture\IteratorAggregateFixture([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307],
            ],
            [
                new Fixture\IteratorAggregateFixture([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307, 0.0],
            ],
            [
                new Fixture\IteratorAggregateFixture([\PHP_FLOAT_MAX, 1, \PHP_FLOAT_MAX]),
                [\PHP_FLOAT_MAX, 8.988465674311579E+307, 1.1984620899082105E+308],
            ],
            [
                new Fixture\IteratorAggregateFixture([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, 1e-310]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307, 0.0, 2.0E-311],
            ],
            [
                new Fixture\IteratorAggregateFixture([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \PHP_FLOAT_MAX]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307, 0.0, 3.5953862697246315E+307, 5.992310449541053E+307],
            ],
        ];
    }

    /**
     * @test         runningAverage with an initial value whose float running total overflows
     * @dataProvider dataProviderForSumOverflowWithInitialValue
     * @param        array $numbers
     * @param        float $initialValue
     * @param        array $expected
     */
    public function testSumOverflowWithInitialValue(array $numbers, float $initialValue, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers, $initialValue) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (the exact mean of the first n terms, rounded once):
     *   [float(t / n) for n, t in enumerate(itertools.accumulate(map(Fraction, [initial] + data)), 1)]
     */
    public static function dataProviderForSumOverflowWithInitialValue(): array
    {
        return [
            [
                [\PHP_FLOAT_MAX],
                \PHP_FLOAT_MAX,
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX],
            ],
            [
                [\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX],
                \PHP_FLOAT_MAX,
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 5.992310449541053E+307],
            ],
        ];
    }

    /**
     * @test         runningAverage keeps the sign of a zero initial value
     * @dataProvider dataProviderForSignedZeroInitialValue
     * @param        array $numbers
     * @param        float $initialValue
     * @param        array $expected
     */
    public function testSignedZeroInitialValue(array $numbers, float $initialValue, array $expected): void
    {
        // When
        $result = \iterator_to_array(Math::runningAverage($numbers, $initialValue));

        // Then
        // -0.0 === 0.0, so compare the exported forms, which differ in the sign.
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with NumPy (Fraction has no signed zero; cumsum starts from the first
     * element, as IEEE addition does):
     *   numpy.cumsum([initial] + data) / numpy.arange(1, len(data) + 2)
     */
    public static function dataProviderForSignedZeroInitialValue(): array
    {
        return [
            [
                [],
                -0.0,
                [-0.0],
            ],
            [
                [-0.0],
                -0.0,
                [-0.0, -0.0],
            ],
            [
                [0.0],
                -0.0,
                [-0.0, 0.0],
            ],
        ];
    }

    /**
     * @test         runningAverage with an infinity after the float running total has overflowed
     * @dataProvider dataProviderForInfinityAfterSumOverflow
     * @param        array $numbers
     * @param        array $expected
     */
    public function testInfinityAfterSumOverflow(array $numbers, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Math::runningAverage($numbers) as $runningValue) {
            $result[] = $runningValue;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python mpmath (Fraction can't hold an infinity, and math.fsum raises
     * on the intermediate overflow), with mpmath.mp.prec = 2000:
     *   [float(mpmath.fsum(map(mpmath.mpf, data[:k])) / k) for k in range(1, len(data) + 1)]
     */
    public static function dataProviderForInfinityAfterSumOverflow(): array
    {
        return [
            [
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \INF],
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \INF],
            ],
            [
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\INF],
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\INF],
            ],
        ];
    }

    /**
     * @test         runningAverage of opposite infinities after the float running total has overflowed
     *
     * Expected values generated with Python mpmath, with mpmath.mp.prec = 2000:
     *   [float(mpmath.fsum(map(mpmath.mpf, data[:k])) / k) for k in range(1, len(data) + 1)]
     */
    public function testOppositeInfinitiesAfterSumOverflow(): void
    {
        // Given
        $numbers = [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \INF, -\INF];

        // When
        $result = \iterator_to_array(Math::runningAverage($numbers));

        // Then
        $this->assertSame([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \INF], \array_slice($result, 0, 3));
        $this->assertNan($result[3]);
    }

    /**
     * @test         runningAverage array with INF, -INF or NAN values
     * @dataProvider dataProviderForNonFiniteArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testNonFiniteArray(array $numbers, array $expected): void
    {
        // When
        $result = \iterator_to_array(Math::runningAverage($numbers));

        // Then
        $this->assertSameOrNan($expected, $result);
    }

    /**
     * Test data generated with NumPy float64:
     *   numpy.cumsum(numpy.array(x, dtype=float)) / numpy.arange(1, len(x) + 1)
     * The int-vs-float result type is the library's contract, not the oracle's: an average is an
     * int when the int running total divides evenly by the count, otherwise a float.
     */
    public static function dataProviderForNonFiniteArray(): array
    {
        return [
            [
                [\INF, 1],
                [\INF, \INF],
            ],
            [
                [1, \INF],
                [1, \INF],
            ],
            [
                [-\INF, 1],
                [-\INF, -\INF],
            ],
            [
                [\INF, -\INF],
                [\INF, \NAN],
            ],
            [
                [\NAN, 1],
                [\NAN, \NAN],
            ],
            [
                [1, \NAN],
                [1, \NAN],
            ],
            [
                [1.5, 2.5, \INF, 4.0],
                [1.5, 2.0, \INF, \INF],
            ],
            [
                [1.5, 2.5, -\INF, \NAN],
                [1.5, 2.0, -\INF, \NAN],
            ],
            [
                [\INF, \PHP_FLOAT_MAX, \PHP_FLOAT_MAX],
                [\INF, \INF, \INF],
            ],
            [
                [-\INF, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX],
                [-\INF, -\INF, -\INF],
            ],
            [
                [1.0, \INF, \PHP_FLOAT_MAX, \PHP_FLOAT_MAX],
                [1.0, \INF, \INF, \INF],
            ],
            [
                [2, 4, 1.5, 3],
                [2, 3, 2.5, 2.625],
            ],
            [
                [1, 2, 3.5, 4, -\INF, 5],
                [1, 1.5, 2.1666666666666665, 2.625, -\INF, -\INF],
            ],
        ];
    }

    /**
     * @test         runningAverage generators with INF, -INF or NAN values
     * @dataProvider dataProviderForNonFiniteGenerators
     * @param        \Generator $numbers
     * @param        array $expected
     */
    public function testNonFiniteGenerators(\Generator $numbers, array $expected): void
    {
        // When
        $result = \iterator_to_array(Math::runningAverage($numbers));

        // Then
        $this->assertSameOrNan($expected, $result);
    }

    /**
     * Test data generated with NumPy float64:
     *   numpy.cumsum(numpy.array(x, dtype=float)) / numpy.arange(1, len(x) + 1)
     * The int-vs-float result type is the library's contract, not the oracle's: an average is an
     * int when the int running total divides evenly by the count, otherwise a float.
     */
    public static function dataProviderForNonFiniteGenerators(): array
    {
        return [
            [
                Fixture\GeneratorFixture::getGenerator([\INF, 1]),
                [\INF, \INF],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, \INF]),
                [1, \INF],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([-\INF, 1]),
                [-\INF, -\INF],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([\INF, -\INF]),
                [\INF, \NAN],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([\NAN, 1]),
                [\NAN, \NAN],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, \NAN]),
                [1, \NAN],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1.5, 2.5, \INF, 4.0]),
                [1.5, 2.0, \INF, \INF],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1.5, 2.5, -\INF, \NAN]),
                [1.5, 2.0, -\INF, \NAN],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([\INF, \PHP_FLOAT_MAX, \PHP_FLOAT_MAX]),
                [\INF, \INF, \INF],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([-\INF, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX]),
                [-\INF, -\INF, -\INF],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1.0, \INF, \PHP_FLOAT_MAX, \PHP_FLOAT_MAX]),
                [1.0, \INF, \INF, \INF],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([2, 4, 1.5, 3]),
                [2, 3, 2.5, 2.625],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3.5, 4, -\INF, 5]),
                [1, 1.5, 2.1666666666666665, 2.625, -\INF, -\INF],
            ],
        ];
    }

    /**
     * @test         runningAverage iterators with INF, -INF or NAN values
     * @dataProvider dataProviderForNonFiniteIterators
     * @param        \Iterator $numbers
     * @param        array $expected
     */
    public function testNonFiniteIterators(\Iterator $numbers, array $expected): void
    {
        // When
        $result = \iterator_to_array(Math::runningAverage($numbers));

        // Then
        $this->assertSameOrNan($expected, $result);
    }

    /**
     * Test data generated with NumPy float64:
     *   numpy.cumsum(numpy.array(x, dtype=float)) / numpy.arange(1, len(x) + 1)
     * The int-vs-float result type is the library's contract, not the oracle's: an average is an
     * int when the int running total divides evenly by the count, otherwise a float.
     */
    public static function dataProviderForNonFiniteIterators(): array
    {
        return [
            [
                new Fixture\ArrayIteratorFixture([\INF, 1]),
                [\INF, \INF],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, \INF]),
                [1, \INF],
            ],
            [
                new Fixture\ArrayIteratorFixture([-\INF, 1]),
                [-\INF, -\INF],
            ],
            [
                new Fixture\ArrayIteratorFixture([\INF, -\INF]),
                [\INF, \NAN],
            ],
            [
                new Fixture\ArrayIteratorFixture([\NAN, 1]),
                [\NAN, \NAN],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, \NAN]),
                [1, \NAN],
            ],
            [
                new Fixture\ArrayIteratorFixture([1.5, 2.5, \INF, 4.0]),
                [1.5, 2.0, \INF, \INF],
            ],
            [
                new Fixture\ArrayIteratorFixture([1.5, 2.5, -\INF, \NAN]),
                [1.5, 2.0, -\INF, \NAN],
            ],
            [
                new Fixture\ArrayIteratorFixture([\INF, \PHP_FLOAT_MAX, \PHP_FLOAT_MAX]),
                [\INF, \INF, \INF],
            ],
            [
                new Fixture\ArrayIteratorFixture([-\INF, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX]),
                [-\INF, -\INF, -\INF],
            ],
            [
                new Fixture\ArrayIteratorFixture([1.0, \INF, \PHP_FLOAT_MAX, \PHP_FLOAT_MAX]),
                [1.0, \INF, \INF, \INF],
            ],
            [
                new Fixture\ArrayIteratorFixture([2, 4, 1.5, 3]),
                [2, 3, 2.5, 2.625],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 2, 3.5, 4, -\INF, 5]),
                [1, 1.5, 2.1666666666666665, 2.625, -\INF, -\INF],
            ],
        ];
    }

    /**
     * @test         runningAverage traversables with INF, -INF or NAN values
     * @dataProvider dataProviderForNonFiniteTraversables
     * @param        \Traversable $numbers
     * @param        array $expected
     */
    public function testNonFiniteTraversables(\Traversable $numbers, array $expected): void
    {
        // When
        $result = \iterator_to_array(Math::runningAverage($numbers));

        // Then
        $this->assertSameOrNan($expected, $result);
    }

    /**
     * Test data generated with NumPy float64:
     *   numpy.cumsum(numpy.array(x, dtype=float)) / numpy.arange(1, len(x) + 1)
     * The int-vs-float result type is the library's contract, not the oracle's: an average is an
     * int when the int running total divides evenly by the count, otherwise a float.
     */
    public static function dataProviderForNonFiniteTraversables(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([\INF, 1]),
                [\INF, \INF],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, \INF]),
                [1, \INF],
            ],
            [
                new Fixture\IteratorAggregateFixture([-\INF, 1]),
                [-\INF, -\INF],
            ],
            [
                new Fixture\IteratorAggregateFixture([\INF, -\INF]),
                [\INF, \NAN],
            ],
            [
                new Fixture\IteratorAggregateFixture([\NAN, 1]),
                [\NAN, \NAN],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, \NAN]),
                [1, \NAN],
            ],
            [
                new Fixture\IteratorAggregateFixture([1.5, 2.5, \INF, 4.0]),
                [1.5, 2.0, \INF, \INF],
            ],
            [
                new Fixture\IteratorAggregateFixture([1.5, 2.5, -\INF, \NAN]),
                [1.5, 2.0, -\INF, \NAN],
            ],
            [
                new Fixture\IteratorAggregateFixture([\INF, \PHP_FLOAT_MAX, \PHP_FLOAT_MAX]),
                [\INF, \INF, \INF],
            ],
            [
                new Fixture\IteratorAggregateFixture([-\INF, -\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX]),
                [-\INF, -\INF, -\INF],
            ],
            [
                new Fixture\IteratorAggregateFixture([1.0, \INF, \PHP_FLOAT_MAX, \PHP_FLOAT_MAX]),
                [1.0, \INF, \INF, \INF],
            ],
            [
                new Fixture\IteratorAggregateFixture([2, 4, 1.5, 3]),
                [2, 3, 2.5, 2.625],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 2, 3.5, 4, -\INF, 5]),
                [1, 1.5, 2.1666666666666665, 2.625, -\INF, -\INF],
            ],
        ];
    }

    /**
     * @test         runningAverage array with an INF or NAN after the float running total has overflowed
     * @dataProvider dataProviderForNonFiniteAfterSumOverflowArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testNonFiniteAfterSumOverflowArray(array $numbers, array $expected): void
    {
        // When
        $result = \iterator_to_array(Math::runningAverage($numbers));

        // Then
        $this->assertSameOrNan($expected, $result);
    }

    /**
     * Test data generated with Python mpmath (NumPy's float64 cumsum overflows to INF where
     * runningAverage rescales, and Fraction can't hold an infinity or NAN), with mpmath.mp.prec = 2000:
     *   [float(mpmath.fsum(map(mpmath.mpf, x[:k])) / k) for k in range(1, len(x) + 1)]
     */
    public static function dataProviderForNonFiniteAfterSumOverflowArray(): array
    {
        return [
            [
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \INF, 1.0],
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \INF, \INF],
            ],
            [
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \NAN],
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \NAN],
            ],
            [
                [-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\INF, 1.0],
                [-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\INF, -\INF],
            ],
        ];
    }

    /**
     * @test         runningAverage generators with an INF or NAN after the float running total has overflowed
     * @dataProvider dataProviderForNonFiniteAfterSumOverflowGenerators
     * @param        \Generator $numbers
     * @param        array $expected
     */
    public function testNonFiniteAfterSumOverflowGenerators(\Generator $numbers, array $expected): void
    {
        // When
        $result = \iterator_to_array(Math::runningAverage($numbers));

        // Then
        $this->assertSameOrNan($expected, $result);
    }

    /**
     * Test data generated with Python mpmath (NumPy's float64 cumsum overflows to INF where
     * runningAverage rescales, and Fraction can't hold an infinity or NAN), with mpmath.mp.prec = 2000:
     *   [float(mpmath.fsum(map(mpmath.mpf, x[:k])) / k) for k in range(1, len(x) + 1)]
     */
    public static function dataProviderForNonFiniteAfterSumOverflowGenerators(): array
    {
        return [
            [
                Fixture\GeneratorFixture::getGenerator([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \INF, 1.0]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \INF, \INF],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \NAN]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \NAN],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\INF, 1.0]),
                [-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\INF, -\INF],
            ],
        ];
    }

    /**
     * @test         runningAverage iterators with an INF or NAN after the float running total has overflowed
     * @dataProvider dataProviderForNonFiniteAfterSumOverflowIterators
     * @param        \Iterator $numbers
     * @param        array $expected
     */
    public function testNonFiniteAfterSumOverflowIterators(\Iterator $numbers, array $expected): void
    {
        // When
        $result = \iterator_to_array(Math::runningAverage($numbers));

        // Then
        $this->assertSameOrNan($expected, $result);
    }

    /**
     * Test data generated with Python mpmath (NumPy's float64 cumsum overflows to INF where
     * runningAverage rescales, and Fraction can't hold an infinity or NAN), with mpmath.mp.prec = 2000:
     *   [float(mpmath.fsum(map(mpmath.mpf, x[:k])) / k) for k in range(1, len(x) + 1)]
     */
    public static function dataProviderForNonFiniteAfterSumOverflowIterators(): array
    {
        return [
            [
                new Fixture\ArrayIteratorFixture([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \INF, 1.0]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \INF, \INF],
            ],
            [
                new Fixture\ArrayIteratorFixture([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \NAN]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \NAN],
            ],
            [
                new Fixture\ArrayIteratorFixture([-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\INF, 1.0]),
                [-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\INF, -\INF],
            ],
        ];
    }

    /**
     * @test         runningAverage traversables with an INF or NAN after the float running total has overflowed
     * @dataProvider dataProviderForNonFiniteAfterSumOverflowTraversables
     * @param        \Traversable $numbers
     * @param        array $expected
     */
    public function testNonFiniteAfterSumOverflowTraversables(\Traversable $numbers, array $expected): void
    {
        // When
        $result = \iterator_to_array(Math::runningAverage($numbers));

        // Then
        $this->assertSameOrNan($expected, $result);
    }

    /**
     * Test data generated with Python mpmath (NumPy's float64 cumsum overflows to INF where
     * runningAverage rescales, and Fraction can't hold an infinity or NAN), with mpmath.mp.prec = 2000:
     *   [float(mpmath.fsum(map(mpmath.mpf, x[:k])) / k) for k in range(1, len(x) + 1)]
     */
    public static function dataProviderForNonFiniteAfterSumOverflowTraversables(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \INF, 1.0]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \INF, \INF],
            ],
            [
                new Fixture\IteratorAggregateFixture([\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \NAN]),
                [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, \NAN],
            ],
            [
                new Fixture\IteratorAggregateFixture([-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\INF, 1.0]),
                [-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, -\INF, -\INF],
            ],
        ];
    }

    /**
     * @test         runningAverage with an initial value and INF, -INF or NAN values
     * @dataProvider dataProviderForNonFiniteWithInitialValue
     * @param        array     $numbers
     * @param        int|float $initialValue
     * @param        array     $expected
     */
    public function testNonFiniteWithInitialValue(array $numbers, int|float $initialValue, array $expected): void
    {
        // When
        $result = \iterator_to_array(Math::runningAverage($numbers, $initialValue));

        // Then
        $this->assertSameOrNan($expected, $result);
    }

    /**
     * Test data generated with NumPy float64:
     *   x = [initial] + data
     *   numpy.cumsum(numpy.array(x, dtype=float)) / numpy.arange(1, len(x) + 1)
     */
    public static function dataProviderForNonFiniteWithInitialValue(): array
    {
        return [
            [
                [1],
                \INF,
                [\INF, \INF],
            ],
            [
                [\INF],
                1.0,
                [1.0, \INF],
            ],
            [
                [1],
                \NAN,
                [\NAN, \NAN],
            ],
        ];
    }

    /**
     * @test         runningAverage iterator_to_array
     * @dataProvider dataProviderForArray
     * @param        array $numbers
     * @param        array $expected
     */
    public function testIteratorToArray(array $numbers, array $expected): void
    {
        // Given
        $iterator = Math::runningAverage($numbers);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * assertSame for each element, but assertNan where NAN is expected, since NAN !== NAN.
     */
    private function assertSameOrNan(array $expected, array $result): void
    {
        $this->assertCount(\count($expected), $result);
        foreach ($expected as $i => $value) {
            if (\is_float($value) && \is_nan($value)) {
                $this->assertNan($result[$i]);
            } else {
                $this->assertSame($value, $result[$i]);
            }
        }
    }
}

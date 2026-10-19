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
}

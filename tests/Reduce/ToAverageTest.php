<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToAverageTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test         toAverage array
     * @dataProvider dataProviderForArray
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testArray(array $data, int|float $expected): void
    {
        // When
        $result = Reduce::toAverage($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational mean, correctly rounded to a float):
     *   float(sum(map(Fraction, data)) / len(data))
     * null and false are coerced to 0 and true to 1, as PHP arithmetic does.
     * The int-vs-float result type is the library's contract, not the oracle's.
     */
    public static function dataProviderForArray(): array
    {
        return [
            [
                [0],
                0,
            ],
            [
                [null],
                0,
            ],
            [
                [false],
                0,
            ],
            [
                [null, null],
                0,
            ],
            [
                [null, false],
                0,
            ],
            [
                [true, false],
                0.5,
            ],
            [
                [false, true],
                0.5,
            ],
            [
                [0, null, false],
                0,
            ],
            [
                [1, null, false],
                0.3333333333333333,
            ],
            [
                [1, null, true],
                0.6666666666666666,
            ],
            [
                [1, 2, 3],
                2,
            ],
            [
                [2, 2, 2],
                2,
            ],
            [
                [5, 6, 1],
                4,
            ],
            [
                [6, 7],
                6.5,
            ],
            [
                [0.5, 1, 1.5],
                1.0,
            ],
            [
                [-0.5, 0, 0.5],
                0.0,
            ],
            [
                [-2, -4, -3],
                -3,
            ],
        ];
    }

    /**
     * @test         toAverage generators
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        int|float  $expected
     */
    public function testGenerators(\Generator $data, int|float $expected): void
    {
        // When
        $result = Reduce::toAverage($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational mean, correctly rounded to a float):
     *   float(sum(map(Fraction, data)) / len(data))
     * null and false are coerced to 0 and true to 1, as PHP arithmetic does.
     * The int-vs-float result type is the library's contract, not the oracle's.
     */
    public static function dataProviderForGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([0]),
                0,
            ],
            [
                $gen([null]),
                0,
            ],
            [
                $gen([false]),
                0,
            ],
            [
                $gen([null, null]),
                0,
            ],
            [
                $gen([null, false]),
                0,
            ],
            [
                $gen([true, false]),
                0.5,
            ],
            [
                $gen([false, true]),
                0.5,
            ],
            [
                $gen([0, null, false]),
                0,
            ],
            [
                $gen([1, null, false]),
                0.3333333333333333,
            ],
            [
                $gen([1, null, true]),
                0.6666666666666666,
            ],
            [
                $gen([1, 2, 3]),
                2,
            ],
            [
                $gen([2, 2, 2]),
                2,
            ],
            [
                $gen([5, 6, 1]),
                4,
            ],
            [
                $gen([6, 7]),
                6.5,
            ],
            [
                $gen([0.5, 1, 1.5]),
                1.0,
            ],
            [
                $gen([-0.5, 0, 0.5]),
                0.0,
            ],
            [
                $gen([-2, -4, -3]),
                -3,
            ],
        ];
    }

    /**
     * @test         toAverage iterators
     * @dataProvider dataProviderForIterators
     * @param        \Iterator $data
     * @param        int|float $expected
     */
    public function testIterators(\Iterator $data, int|float $expected): void
    {
        // When
        $result = Reduce::toAverage($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational mean, correctly rounded to a float):
     *   float(sum(map(Fraction, data)) / len(data))
     * null and false are coerced to 0 and true to 1, as PHP arithmetic does.
     * The int-vs-float result type is the library's contract, not the oracle's.
     */
    public static function dataProviderForIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $iter([0]),
                0,
            ],
            [
                $iter([null]),
                0,
            ],
            [
                $iter([false]),
                0,
            ],
            [
                $iter([null, null]),
                0,
            ],
            [
                $iter([null, false]),
                0,
            ],
            [
                $iter([true, false]),
                0.5,
            ],
            [
                $iter([false, true]),
                0.5,
            ],
            [
                $iter([0, null, false]),
                0,
            ],
            [
                $iter([1, null, false]),
                0.3333333333333333,
            ],
            [
                $iter([1, null, true]),
                0.6666666666666666,
            ],
            [
                $iter([1, 2, 3]),
                2,
            ],
            [
                $iter([2, 2, 2]),
                2,
            ],
            [
                $iter([5, 6, 1]),
                4,
            ],
            [
                $iter([6, 7]),
                6.5,
            ],
            [
                $iter([0.5, 1, 1.5]),
                1.0,
            ],
            [
                $iter([-0.5, 0, 0.5]),
                0.0,
            ],
            [
                $iter([-2, -4, -3]),
                -3,
            ],
        ];
    }

    /**
     * @test         toAverage traversables
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        int|float    $expected
     */
    public function testTraversables(\Traversable $data, int|float $expected): void
    {
        // When
        $result = Reduce::toAverage($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational mean, correctly rounded to a float):
     *   float(sum(map(Fraction, data)) / len(data))
     * null and false are coerced to 0 and true to 1, as PHP arithmetic does.
     * The int-vs-float result type is the library's contract, not the oracle's.
     */
    public static function dataProviderForTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([0]),
                0,
            ],
            [
                $trav([null]),
                0,
            ],
            [
                $trav([false]),
                0,
            ],
            [
                $trav([null, null]),
                0,
            ],
            [
                $trav([null, false]),
                0,
            ],
            [
                $trav([true, false]),
                0.5,
            ],
            [
                $trav([false, true]),
                0.5,
            ],
            [
                $trav([0, null, false]),
                0,
            ],
            [
                $trav([1, null, false]),
                0.3333333333333333,
            ],
            [
                $trav([1, null, true]),
                0.6666666666666666,
            ],
            [
                $trav([1, 2, 3]),
                2,
            ],
            [
                $trav([2, 2, 2]),
                2,
            ],
            [
                $trav([5, 6, 1]),
                4,
            ],
            [
                $trav([6, 7]),
                6.5,
            ],
            [
                $trav([0.5, 1, 1.5]),
                1.0,
            ],
            [
                $trav([-0.5, 0, 0.5]),
                0.0,
            ],
            [
                $trav([-2, -4, -3]),
                -3,
            ],
        ];
    }

    /**
     * @test         toAverage array within the floating-point summation error bound
     * @dataProvider dataProviderForNaiveSummationArray
     * @param        array     $data
     * @param        float     $expected
     * @param        float     $delta
     */
    public function testNaiveSummationArray(array $data, float $expected, float $delta): void
    {
        // When
        $result = Reduce::toAverage($data);

        // Then
        $this->assertIsFloat($result);
        $this->assertEqualsWithDelta($expected, $result, $delta);
    }

    /**
     * Test data generated with Python (exact rational mean, correctly rounded to a float):
     *   float(sum(map(Fraction, data)) / len(data))
     * The library sums left to right in floating point and then divides, so its result may differ
     * from the correctly rounded mean. Each row's delta is the a-priori bound on that error,
     *   sys.float_info.epsilon * math.fsum(map(abs, data))
     * since |error| <= (n-1)·u·Σ|x|/n + u·|mean| < ε·Σ|x|, with unit roundoff u = ε/2
     * (Higham, Accuracy and Stability of Numerical Algorithms, 2nd ed., §4.2).
     */
    public static function dataProviderForNaiveSummationArray(): array
    {
        return [
            [
                [1.1, 2.2, 3.3],
                2.2,
                1.4654943925052066E-15,
            ],
        ];
    }

    /**
     * @test         toAverage generators within the floating-point summation error bound
     * @dataProvider dataProviderForNaiveSummationGenerators
     * @param        \Generator $data
     * @param        float      $expected
     * @param        float      $delta
     */
    public function testNaiveSummationGenerators(\Generator $data, float $expected, float $delta): void
    {
        // When
        $result = Reduce::toAverage($data);

        // Then
        $this->assertIsFloat($result);
        $this->assertEqualsWithDelta($expected, $result, $delta);
    }

    /**
     * Test data generated with Python (exact rational mean, correctly rounded to a float):
     *   float(sum(map(Fraction, data)) / len(data))
     * The library sums left to right in floating point and then divides, so its result may differ
     * from the correctly rounded mean. Each row's delta is the a-priori bound on that error,
     *   sys.float_info.epsilon * math.fsum(map(abs, data))
     * since |error| <= (n-1)·u·Σ|x|/n + u·|mean| < ε·Σ|x|, with unit roundoff u = ε/2
     * (Higham, Accuracy and Stability of Numerical Algorithms, 2nd ed., §4.2).
     */
    public static function dataProviderForNaiveSummationGenerators(): array
    {
        $gen = static function (array $data) {
            return GeneratorFixture::getGenerator($data);
        };

        return [
            [
                $gen([1.1, 2.2, 3.3]),
                2.2,
                1.4654943925052066E-15,
            ],
        ];
    }

    /**
     * @test         toAverage iterators within the floating-point summation error bound
     * @dataProvider dataProviderForNaiveSummationIterators
     * @param        \Iterator $data
     * @param        float     $expected
     * @param        float     $delta
     */
    public function testNaiveSummationIterators(\Iterator $data, float $expected, float $delta): void
    {
        // When
        $result = Reduce::toAverage($data);

        // Then
        $this->assertIsFloat($result);
        $this->assertEqualsWithDelta($expected, $result, $delta);
    }

    /**
     * Test data generated with Python (exact rational mean, correctly rounded to a float):
     *   float(sum(map(Fraction, data)) / len(data))
     * The library sums left to right in floating point and then divides, so its result may differ
     * from the correctly rounded mean. Each row's delta is the a-priori bound on that error,
     *   sys.float_info.epsilon * math.fsum(map(abs, data))
     * since |error| <= (n-1)·u·Σ|x|/n + u·|mean| < ε·Σ|x|, with unit roundoff u = ε/2
     * (Higham, Accuracy and Stability of Numerical Algorithms, 2nd ed., §4.2).
     */
    public static function dataProviderForNaiveSummationIterators(): array
    {
        $iter = static function (array $data) {
            return new ArrayIteratorFixture($data);
        };

        return [
            [
                $iter([1.1, 2.2, 3.3]),
                2.2,
                1.4654943925052066E-15,
            ],
        ];
    }

    /**
     * @test         toAverage traversables within the floating-point summation error bound
     * @dataProvider dataProviderForNaiveSummationTraversables
     * @param        \Traversable $data
     * @param        float        $expected
     * @param        float        $delta
     */
    public function testNaiveSummationTraversables(\Traversable $data, float $expected, float $delta): void
    {
        // When
        $result = Reduce::toAverage($data);

        // Then
        $this->assertIsFloat($result);
        $this->assertEqualsWithDelta($expected, $result, $delta);
    }

    /**
     * Test data generated with Python (exact rational mean, correctly rounded to a float):
     *   float(sum(map(Fraction, data)) / len(data))
     * The library sums left to right in floating point and then divides, so its result may differ
     * from the correctly rounded mean. Each row's delta is the a-priori bound on that error,
     *   sys.float_info.epsilon * math.fsum(map(abs, data))
     * since |error| <= (n-1)·u·Σ|x|/n + u·|mean| < ε·Σ|x|, with unit roundoff u = ε/2
     * (Higham, Accuracy and Stability of Numerical Algorithms, 2nd ed., §4.2).
     */
    public static function dataProviderForNaiveSummationTraversables(): array
    {
        $trav = static function (array $data) {
            return new IteratorAggregateFixture($data);
        };

        return [
            [
                $trav([1.1, 2.2, 3.3]),
                2.2,
                1.4654943925052066E-15,
            ],
        ];
    }

    /**
     * @test         toAverage returns null on empty
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptyReturnsNull(iterable $data): void
    {
        // When
        $result = Reduce::toAverage($data);

        // Then
        $this->assertNull($result);
    }
}

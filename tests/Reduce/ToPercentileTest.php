<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToPercentileTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test toPercentile example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $scores = [10, 20, 30, 40, 50];

        // When
        $p75 = Reduce::toPercentile($scores, 75);

        // Then (numpy.percentile([10, 20, 30, 40, 50], 75, method='linear'), landing on the element 40)
        $this->assertSame(40, $p75);
    }

    /**
     * @test         toPercentile array
     * @dataProvider dataProviderForPercentile
     * @param        array     $data
     * @param        float     $percentile
     * @param        int|float $expected
     */
    public function testArray(array $data, float $percentile, $expected): void
    {
        // When
        $result = Reduce::toPercentile($data, $percentile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toPercentile generator
     * @dataProvider dataProviderForPercentile
     * @param        array     $data
     * @param        float     $percentile
     * @param        int|float $expected
     */
    public function testGenerator(array $data, float $percentile, $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toPercentile($generator, $percentile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toPercentile iterator
     * @dataProvider dataProviderForPercentile
     * @param        array     $data
     * @param        float     $percentile
     * @param        int|float $expected
     */
    public function testIterator(array $data, float $percentile, $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toPercentile($iterator, $percentile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toPercentile traversable
     * @dataProvider dataProviderForPercentile
     * @param        array     $data
     * @param        float     $percentile
     * @param        int|float $expected
     */
    public function testTraversable(array $data, float $percentile, $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toPercentile($traversable, $percentile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   numpy.percentile(data, p, method='linear')
     * The int-vs-float result type is the library's contract, not the oracle's: a rank that lands on
     * an element returns that element, and the midpoint of two integers with an even sum is an integer.
     */
    public static function dataProviderForPercentile(): array
    {
        return [
            // [1,2,3,4] — R-7 / linear interpolation (NumPy default)
            [[1, 2, 3, 4], 0, 1],
            [[1, 2, 3, 4], 25, 1.75],
            [[1, 2, 3, 4], 50, 2.5],
            [[1, 2, 3, 4], 75, 3.25],
            [[1, 2, 3, 4], 100, 4],
            // [1,2,3,4,5] — exact-rank landings
            [[1, 2, 3, 4, 5], 0, 1],
            [[1, 2, 3, 4, 5], 25, 2],
            [[1, 2, 3, 4, 5], 50, 3],
            [[1, 2, 3, 4, 5], 75, 4],
            [[1, 2, 3, 4, 5], 100, 5],
            // Fractional percentile requiring interpolation
            [[1, 2, 3, 4, 5], 10, 1.4],
            // Unsorted input (output invariant to order)
            [[4, 2, 5, 1, 3], 50, 3],
            // Textbook interpolation cases
            [[15, 20, 35, 40, 50], 40, 29.0],
            [[15, 20, 35, 40, 50], 5, 16.0],
            // Single element — every percentile is the element
            [[42], 0, 42],
            [[42], 50, 42],
            [[42], 100, 42],
            // Negative numbers
            [[-10, -5, 0, 5, 10], 50, 0],
            // Floats
            [[1.5, 2.5, 3.5], 50, 2.5],
        ];
    }

    /**
     * @test         toPercentile returns null on empty
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptyReturnsNull(iterable $data): void
    {
        // When
        $result = Reduce::toPercentile($data, 50);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test         toPercentile throws on out-of-range percentile
     * @dataProvider dataProviderForOutOfRange
     * @param        float $percentile
     */
    public function testOutOfRangeThrows(float $percentile): void
    {
        // Given
        $data = [1, 2, 3, 4, 5];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Reduce::toPercentile($data, $percentile);
    }

    public static function dataProviderForOutOfRange(): array
    {
        return [
            [-1],
            [-0.0001],
            [100.0001],
            [101],
            [1000],
            [\NAN],
        ];
    }

    /**
     * @test toPercentile boundary 0 and 100 are min and max
     */
    public function testBoundariesAreMinAndMax(): void
    {
        // Given
        $data = [5, 1, 9, 3, 7];

        // When
        $min = Reduce::toPercentile($data, 0);
        $max = Reduce::toPercentile($data, 100);

        // Then (numpy.percentile([5, 1, 9, 3, 7], [0, 100], method='linear'), landing on the elements)
        $this->assertSame(1, $min);
        $this->assertSame(9, $max);
    }

    /**
     * @test toPercentile of a very large collection
     */
    public function testVeryLargeN(): void
    {
        // Given the sequence 1..10000
        $data = \range(1, 10000);

        // When
        $p0   = Reduce::toPercentile($data, 0);
        $p50  = Reduce::toPercentile($data, 50);
        $p100 = Reduce::toPercentile($data, 100);

        // Then (numpy.percentile(range(1, 10001), [0, 50, 100], method='linear'))
        $this->assertSame(1, $p0);
        $this->assertSame(5000.5, $p50);
        $this->assertSame(10000, $p100);
    }

    /**
     * @test         toPercentile does not overflow when the interpolated span exceeds PHP_FLOAT_MAX
     * @dataProvider dataProviderForHugeSpan
     * @param        array $data
     * @param        float $percentile
     * @param        float $expected
     */
    public function testInterpolationOverHugeSpanDoesNotOverflow(array $data, float $percentile, float $expected): void
    {
        // When
        $result = Reduce::toPercentile($data, $percentile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact R-7 interpolation, correctly rounded to a float):
     *   h = (len(x) - 1) * Fraction(p) / 100, j = floor(h)
     *   float(Fraction(x[j]) + (h - j) * (Fraction(x[j + 1]) - Fraction(x[j])))
     * numpy.percentile is not the oracle here: its span x[j + 1] - x[j] overflows to INF.
     */
    public static function dataProviderForHugeSpan(): array
    {
        return [
            [[-1e308, 1e308], 50, 0.0],
            [[-1e308, 1e308], 75, 5e307],
            [[-1e308, 1e308], 25, -5e307],
            [[-1.5e308, 1.5e308], 50, 0.0],
        ];
    }

    /**
     * @test toPercentile at the 50th percentile of the two integer extremes keeps the exact half-unit result
     */
    public function testMedianPercentileOfIntegerExtremesIsExact(): void
    {
        // Given the widest possible integer span, whose midpoint is exactly -0.5
        $data = [\PHP_INT_MIN, \PHP_INT_MAX];

        // When
        $result = Reduce::toPercentile($data, 50);

        // Then (exact R-7: float((Fraction(-2**63) + Fraction(2**63 - 1)) / 2))
        $this->assertSame(-0.5, $result);
    }

    /**
     * @test toPercentile at the 50th percentile of two integers past 2^53 is the correctly rounded midpoint
     */
    public function testMedianPercentileOfLargeIntegersIsCorrectlyRounded(): void
    {
        // Given middle values whose true midpoint …993.5 lies between the floats …992 and …994
        // (on 32-bit PHP these literals parse as floats, so the expected value does not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $data = [9007199254740993, 9007199254740994];

        // When
        $result = Reduce::toPercentile($data, 50);

        // Then (exact R-7: float((Fraction(9007199254740993) + Fraction(9007199254740994)) / 2))
        $this->assertSame(9007199254740994.0, $result);
    }

    /**
     * @test         toPercentile at the 50th percentile agrees with toMedian
     * @dataProvider dataProviderForMedianAgreement
     * @param        array $data
     */
    public function testFiftiethPercentileAgreesWithMedian(array $data): void
    {
        // When
        $percentile = Reduce::toPercentile($data, 50);
        $median     = Reduce::toMedian($data);

        // Then
        $this->assertSame($median, $percentile);
    }

    public static function dataProviderForMedianAgreement(): array
    {
        return [
            [[1, 2]],
            [[2, 4]],
            [[1, 2, 3]],
            [[1, 2, 3, 4]],
            [[3, 1, 4, 1, 5, 9]],
            [[\PHP_INT_MIN, \PHP_INT_MAX]],
            [[-1e308, 1e308]],
            [[1e308, 1e308]],
            [[0.1, 0.1]],
        ];
    }

    /**
     * @test         toPercentile between two identical infinities is that infinity
     * @dataProvider dataProviderForIdenticalInfiniteNeighbours
     * @param        array $data
     * @param        float $percentile
     * @param        float $expected
     */
    public function testInterpolationBetweenIdenticalInfinitiesIsThatInfinity(
        array $data,
        float $percentile,
        float $expected
    ): void {
        // When
        $result = Reduce::toPercentile($data, $percentile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's R-7 quantiles (NumPy gives NAN, from INF - INF in its span):
     *   statistics.quantiles(data, n=100, method='inclusive')[p - 1]
     */
    public static function dataProviderForIdenticalInfiniteNeighbours(): array
    {
        return [
            [[\INF, \INF], 50, \INF],
            [[\INF, \INF], 25, \INF],
            [[-\INF, -\INF], 50, -\INF],
            [[1, \INF, \INF, \INF], 50, \INF],
        ];
    }

    /**
     * @test         toPercentile between an infinity and a finite value is that infinity
     * @dataProvider dataProviderForInfiniteAndFiniteNeighbours
     * @param        array $data
     * @param        float $percentile
     * @param        float $expected
     */
    public function testInterpolationBetweenInfinityAndFiniteValueIsThatInfinity(
        array $data,
        float $percentile,
        float $expected
    ): void {
        // When
        $result = Reduce::toPercentile($data, $percentile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's R-7 quantiles (NumPy gives NAN, from INF - INF in its span):
     *   statistics.quantiles(data, n=100, method='inclusive')[p - 1]
     */
    public static function dataProviderForInfiniteAndFiniteNeighbours(): array
    {
        return [
            [[-\INF, -1], 50, -\INF],
            [[-\INF, -1], 25, -\INF],
            [[-\INF, -1], 75, -\INF],
            [[1, \INF], 50, \INF],
            [[1, \INF], 25, \INF],
            [[1, \INF], 75, \INF],
            [[-\INF, 1], 50, -\INF],
            [[-1, \INF], 50, \INF],
        ];
    }

    /**
     * @test toPercentile interpolating between two identical neighbours returns that value exactly
     */
    public function testInterpolationBetweenIdenticalNeighboursIsExact(): void
    {
        // Given a value whose weighted recombination is not exact in binary floating point
        $data = [0.1, 0.1, 0.1, 0.1];

        // When (a percentile that lands strictly between two ranks)
        $result = Reduce::toPercentile($data, 30);

        // Then (numpy.percentile([0.1, 0.1, 0.1, 0.1], 30, method='linear'))
        $this->assertSame(0.1, $result);
    }

    /**
     * @test         toPercentile of data holding NAN is NAN, array
     * @dataProvider dataProviderForNan
     * @param        array $data
     * @param        float $percentile
     */
    public function testNanArray(array $data, float $percentile): void
    {
        // When
        $result = Reduce::toPercentile($data, $percentile);

        // Then
        $this->assertNan($result);
    }

    /**
     * @test         toPercentile of data holding NAN is NAN, generator
     * @dataProvider dataProviderForNan
     * @param        array $data
     * @param        float $percentile
     */
    public function testNanGenerator(array $data, float $percentile): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toPercentile($generator, $percentile);

        // Then
        $this->assertNan($result);
    }

    /**
     * @test         toPercentile of data holding NAN is NAN, iterator
     * @dataProvider dataProviderForNan
     * @param        array $data
     * @param        float $percentile
     */
    public function testNanIterator(array $data, float $percentile): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toPercentile($iterator, $percentile);

        // Then
        $this->assertNan($result);
    }

    /**
     * @test         toPercentile of data holding NAN is NAN, traversable
     * @dataProvider dataProviderForNan
     * @param        array $data
     * @param        float $percentile
     */
    public function testNanTraversable(array $data, float $percentile): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toPercentile($traversable, $percentile);

        // Then
        $this->assertNan($result);
    }

    /**
     * Test data generated with:
     *   numpy.percentile(data, p, method='linear')
     * which is nan for every row, wherever the nan sits (scripts: tools/oracles/nan_median_percentile_quantile.py).
     * The rows cover odd and even lengths, a NAN first, in the middle and last, and a NAN among infinities.
     */
    public static function dataProviderForNan(): array
    {
        return [
            [[\NAN, 1, 2], 0],
            [[\NAN, 1, 2], 25],
            [[\NAN, 1, 2], 50],
            [[\NAN, 1, 2], 100],
            [[1, \NAN, 2], 0],
            [[1, \NAN, 2], 25],
            [[1, \NAN, 2], 50],
            [[1, \NAN, 2], 100],
            [[1, 2, \NAN], 0],
            [[1, 2, \NAN], 25],
            [[1, 2, \NAN], 50],
            [[1, 2, \NAN], 100],
            [[\NAN], 0],
            [[\NAN], 25],
            [[\NAN], 50],
            [[\NAN], 100],
            [[\NAN, \NAN], 0],
            [[\NAN, \NAN], 25],
            [[\NAN, \NAN], 50],
            [[\NAN, \NAN], 100],
            [[1, 2, 3, \NAN], 0],
            [[1, 2, 3, \NAN], 25],
            [[1, 2, 3, \NAN], 50],
            [[1, 2, 3, \NAN], 100],
            [[\NAN, 4, 3, 2, 1], 0],
            [[\NAN, 4, 3, 2, 1], 25],
            [[\NAN, 4, 3, 2, 1], 50],
            [[\NAN, 4, 3, 2, 1], 100],
            [[1.5, \NAN, 2.5, 4.5], 0],
            [[1.5, \NAN, 2.5, 4.5], 25],
            [[1.5, \NAN, 2.5, 4.5], 50],
            [[1.5, \NAN, 2.5, 4.5], 100],
            [[-\INF, \NAN, \INF], 0],
            [[-\INF, \NAN, \INF], 25],
            [[-\INF, \NAN, \INF], 50],
            [[-\INF, \NAN, \INF], 100],
        ];
    }

    /**
     * @test toPercentile still rejects an out-of-range percentile when the data holds NAN
     */
    public function testOutOfRangePercentileThrowsForDataHoldingNan(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Reduce::toPercentile([1, \NAN, 3], 150);
    }

    /**
     * @test         toPercentile array of ints beyond 2^53 and floats, ordered by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array     $data
     * @param        float     $percentile
     * @param        int|float $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionArray(array $data, float $percentile, int|float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toPercentile($data, $percentile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toPercentile generator of ints beyond 2^53 and floats, ordered by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array     $data
     * @param        float     $percentile
     * @param        int|float $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionGenerator(array $data, float $percentile, int|float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toPercentile($iterable, $percentile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toPercentile iterator of ints beyond 2^53 and floats, ordered by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array     $data
     * @param        float     $percentile
     * @param        int|float $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionIterator(array $data, float $percentile, int|float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toPercentile($iterable, $percentile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toPercentile traversable of ints beyond 2^53 and floats, ordered by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array     $data
     * @param        float     $percentile
     * @param        int|float $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionTraversable(array $data, float $percentile, int|float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toPercentile($iterable, $percentile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which orders an int and a float by exact value
     * (tools/oracles/exact_order_percentile.py):
     *   s = sorted(data), h = (len(s) - 1) * Fraction(p) / 100, j = floor(h)
     *   s[j] if h == j else float(Fraction(s[j]) + (h - j) * (Fraction(s[j + 1]) - Fraction(s[j])))
     * Every rank lands on an element, so the result is that element.
     * NumPy is not the oracle here: it converts the ints to float64, which can't tell them from the floats.
     * Each int compares equal as a float to a float it is not equal to, so <=> orders them wrongly.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecision(): array
    {
        return [
            '2^62 + 500, 2.0^62, 2^62 + 1 at 0: the float is the minimum' => [
                [4611686018427388404, 4611686018427387904.0, 4611686018427387905],
                0,
                4611686018427387904.0,
            ],
            '2^62 + 500, 2.0^62, 2^62 + 1 at 50: the int 2^62 + 1 is the middle' => [
                [4611686018427388404, 4611686018427387904.0, 4611686018427387905],
                50,
                4611686018427387905,
            ],
            '2^62 + 500, 2.0^62, 2^62 + 1 at 100: the int 2^62 + 500 is the maximum' => [
                [4611686018427388404, 4611686018427387904.0, 4611686018427387905],
                100,
                4611686018427388404,
            ],
            '2^53 + 1, 2.0^53 at 0' => [
                [9007199254740993, 9007199254740992.0],
                0,
                9007199254740992.0,
            ],
            '2^53 + 1, 2.0^53 at 100' => [
                [9007199254740993, 9007199254740992.0],
                100,
                9007199254740993,
            ],
            '-2.0^53, -(2^53 + 1) at 0' => [
                [-9007199254740992.0, -9007199254740993],
                0,
                -9007199254740993,
            ],
            '-2.0^53, -(2^53 + 1) at 100' => [
                [-9007199254740992.0, -9007199254740993],
                100,
                -9007199254740992.0,
            ],
        ];
    }
}

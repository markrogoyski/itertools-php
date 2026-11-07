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
     * @test         toPercentile at a percentile whose rank is an exact integer returns that element, without interpolating
     * @dataProvider dataProviderForExactIntegerRank
     * @param        array     $data
     * @param        float     $percentile
     * @param        int|float $expected
     */
    public function testExactIntegerRankReturnsElement(array $data, float $percentile, int|float $expected): void
    {
        // When
        $result = Reduce::toPercentile($data, $percentile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact R-7 rank, the element itself when the rank is an integer):
     *   h = (len(x) - 1) * Fraction(p) / 100, j = floor(h)
     *   x[j] if h == j else float(Fraction(x[j]) + (h - j) * (Fraction(x[j + 1]) - Fraction(x[j])))
     * Each rank is exact in rationals but not when p / 100 is rounded first (e.g. 25 * 0.28 = 7.000000000000001).
     */
    public static function dataProviderForExactIntegerRank(): array
    {
        return [
            'rank 7 of 0..25 at 28' => [\range(0, 25), 28, 7],
            'rank 7 of 0..50 at 14' => [\range(0, 50), 14, 7],
            'rank 7 of 0.0..2.5 at 28' => [
                [0.0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0, 1.1, 1.2,
                 1.3, 1.4, 1.5, 1.6, 1.7, 1.8, 1.9, 2.0, 2.1, 2.2, 2.3, 2.4, 2.5],
                28,
                0.7,
            ],
            'rank 7 of eight zeros and eighteen INF at 28: zero, not INF' => [
                [0, 0, 0, 0, 0, 0, 0, 0, \INF, \INF, \INF, \INF, \INF, \INF, \INF, \INF, \INF,
                 \INF, \INF, \INF, \INF, \INF, \INF, \INF, \INF, \INF],
                28,
                0,
            ],
            'rank 18 of eighteen -INF and eight zeros at 72: zero, not -INF' => [
                [-\INF, -\INF, -\INF, -\INF, -\INF, -\INF, -\INF, -\INF, -\INF, -\INF, -\INF, -\INF,
                 -\INF, -\INF, -\INF, -\INF, -\INF, -\INF, 0, 0, 0, 0, 0, 0, 0, 0],
                72,
                0,
            ],
        ];
    }

    /**
     * @test         toPercentile at a rank just off an integer interpolates by the true, tiny weight
     * @dataProvider dataProviderForRankNextToInteger
     * @param        array $data
     * @param        float $percentile
     * @param        float $expected
     */
    public function testRankNextToIntegerInterpolates(array $data, float $percentile, float $expected): void
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
     * Each rank is within a rounding of an integer: (n - 1) * p or p / 100 rounds onto it in float, or
     * the weight h - j, or 1 - (h - j), underflows or loses its digits when divided by 100.
     */
    public static function dataProviderForRankNextToInteger(): array
    {
        return [
            'rank just above 1: 7 * p rounds to 100' => [[0, 0, 1, 1, 1, 1, 1, 1], 14.285714285714286, 5.329070518200751e-17],
            'rank just below 1' => [[0, 1, 1, 1, 1, 1, 1, 1], 14.285714285714285, 0.9999999999999999],
            'weight 5e-326 underflows: not inflated' => [[0, 1e308], 5e-324, 4.9406564584124655e-18],
            'weight 1e-302 is tiny but normal' => [[0, 1e308], 1e-300, 1000000.0],
            'weight 5e-326 on a span past PHP_FLOAT_MAX' => [[-1e308, 1e308], 5e-324, -1e308],
            'weight 1 - 1.4e-16 keeps the digits of its complement' => [[-1e308, 0], 99.99999999999999, -1.4210854715202004e+292],
        ];
    }

    /**
     * @test         toPercentile at a rank just off an integer gives an infinite neighbour's infinity
     * @dataProvider dataProviderForRankNextToIntegerWithInfinity
     * @param        array $data
     * @param        float $percentile
     * @param        float $expected
     */
    public function testRankNextToIntegerWithInfiniteNeighbour(array $data, float $percentile, float $expected): void
    {
        // When
        $result = Reduce::toPercentile($data, $percentile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data from the R-7 definition x[j] + (h - j) * (x[j + 1] - x[j]), h = (len(x) - 1) * Fraction(p) / 100:
     * each h is not an integer, so a positive weight lands on the infinite neighbour and gives its infinity.
     * Python's Fraction can't hold an infinity, and NumPy rounds each h onto an integer.
     */
    public static function dataProviderForRankNextToIntegerWithInfinity(): array
    {
        return [
            'rank just above 1, next to INF' => [[0, 0, \INF, \INF, \INF, \INF, \INF, \INF], 14.285714285714286, \INF],
            'rank just below 1, next to -INF' => [[-\INF, 0, 0, 0, 0, 0, 0, 0], 14.285714285714285, -\INF],
            'weight 5e-326 on INF' => [[0, \INF], 5e-324, \INF],
            'weight 1.4e-16 on -INF' => [[-\INF, 0], 99.99999999999999, -\INF],
        ];
    }

    /**
     * @test         toPercentile interpolating between integers beyond 2^53 adds the offset before rounding to float
     * @dataProvider dataProviderForLargeIntegerInterpolation
     * @param        array $data
     * @param        float $percentile
     * @param        float $expected
     */
    public function testLargeIntegerInterpolation(array $data, float $percentile, float $expected): void
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
     * Test data generated with Python (exact R-7 interpolation, correctly rounded to a float):
     *   h = (len(x) - 1) * Fraction(p) / 100, j = floor(h)
     *   float(Fraction(x[j]) + (h - j) * (Fraction(x[j + 1]) - Fraction(x[j])))
     */
    public static function dataProviderForLargeIntegerInterpolation(): array
    {
        return [
            '2^53, 2^53 + 3 at 75: from the exact upper end' => [[9007199254740992, 9007199254740995], 75, 9007199254740994.0],
            '2^53 + 1, 2^53 + 4 at 25: from the inexact lower end' => [[9007199254740993, 9007199254740996], 25, 9007199254740994.0],
            '2^53 + 1, 2^53 + 4 at 75' => [[9007199254740993, 9007199254740996], 75, 9007199254740996.0],
            '-(2^53 + 4), -(2^53 + 1) at 25' => [[-9007199254740996, -9007199254740993], 25, -9007199254740996.0],
            '-(2^53 + 4), -(2^53 + 1) at 75' => [[-9007199254740996, -9007199254740993], 75, -9007199254740994.0],
            '2^62 + 1, 2^62 + 1001 at 30' => [[4611686018427387905, 4611686018427388905], 30, 4611686018427387904.0],
            '2.0^53, 2^53 + 3 at 75: an integral float' => [[9007199254740992.0, 9007199254740995], 75, 9007199254740994.0],
            '2^53 + 1, 2.0^53 + 4 at 25: an integral float' => [[9007199254740993, 9007199254740996.0], 25, 9007199254740994.0],
            'PHP_INT_MAX - 3, PHP_INT_MAX at 75: rounds to 2^63' => [[\PHP_INT_MAX - 3, \PHP_INT_MAX], 75, 9223372036854775808.0],
            'PHP_INT_MIN, PHP_INT_MIN + 3 at 25' => [[\PHP_INT_MIN, \PHP_INT_MIN + 3], 25, -9223372036854775808.0],
            'PHP_INT_MIN, PHP_INT_MAX at 25: the span overflows' => [[\PHP_INT_MIN, \PHP_INT_MAX], 25, -4611686018427387904.0],
            '-(2^53 + 1), 3 * (2^53 + 1) at 25: the span 2^55 + 4 rounds as a float' => [[-9007199254740993, 27021597764222979], 25, 0.0],
            '-(2^53 + 1), 3 * (2^53 + 1) at 30: so does the weight 0.3' => [[-9007199254740993, 27021597764222979], 30, 1801439850948198.5],
            '-(2^53 + 1), 3 * (2^53 + 1) at 70' => [[-9007199254740993, 27021597764222979], 70, 1.6212958658533788e+16],
            '-(2^62 + 7), 2^62 + 3 at 33' => [[-4611686018427387911, 4611686018427387907], 33, -1.567973246265312e+18],
            '-(2^61 + 1), 2^62 + 2^60 + 5 at 61' => [[-2305843009213693953, 5764607523034234885], 61, 2.6171318154575427e+18],
            '2^53 + 1, PHP_INT_MAX at 99' => [[9007199254740993, \PHP_INT_MAX], 99, 9.131228388478775e+18],
            '2^53 + 2, 2^53 + 3 (x 11) just below rank 1: a tie broken by the fraction' => [
                [
                    9007199254740994, 9007199254740995, 9007199254740995, 9007199254740995,
                    9007199254740995, 9007199254740995, 9007199254740995, 9007199254740995,
                    9007199254740995, 9007199254740995, 9007199254740995, 9007199254740995,
                ],
                9.09090909090909,
                9007199254740994.0,
            ],
            '2^53 + 9, 2^53 + 11 at 1e-323: a subnormal fraction breaks the tie' => [
                [9007199254741001, 9007199254741003],
                1e-323,
                9007199254741002.0,
            ],
            '0, 2^53 + 1 (x 7) just above rank 0.5: not the midpoint' => [
                [
                    0, 9007199254740993, 9007199254740993, 9007199254740993,
                    9007199254740993, 9007199254740993, 9007199254740993, 9007199254740993,
                ],
                7.142857142857143,
                4503599627370497.0,
            ],
            '2^53 + 1, 2^53 + 10 at 22.22222222222222: an offset just below 2 does not carry' => [
                [9007199254740993, 9007199254741002],
                22.22222222222222,
                9007199254740994.0,
            ],
            'PHP_INT_MIN, PHP_INT_MAX (x 3) just above rank 0.5: the nearer endpoint from the exact weight' => [
                [\PHP_INT_MIN, \PHP_INT_MAX, \PHP_INT_MAX, \PHP_INT_MAX],
                16.666666666666668,
                654.86,
            ],
            '-10^16, 9 * 10^16 - 1 at 10: a result near 0 is rounded once, after the cancellation' => [
                [-10000000000000000, 89999999999999999],
                10,
                -0.1,
            ],
            '-k, 9k at 10 for k = 10^18 + 4: the split-span fractions carry exactly' => [
                [-1000000000000000004, 9000000000000000036],
                10,
                0.0,
            ],
        ];
    }

    /**
     * @test toPercentile of a span past PHP_INT_MAX at a weight just below 50 does not overflow its offset
     */
    public function testLargeIntegerOffsetJustBelowHalfTheSpan(): void
    {
        // Given (on 32-bit PHP these integers are floats, so the expected value does not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $data    = \array_fill(0, 1788, \PHP_INT_MAX);
        $data[0] = \PHP_INT_MIN;

        // When
        $result = Reduce::toPercentile($data, 0.027979854504756575);

        // Then (Python, exact R-7 interpolation correctly rounded, as for the large-integer data provider:
        //   h = 1787 * Fraction(0.027979854504756575) / 100
        //   float(Fraction(-2**63) + h * (Fraction(2**63 - 1) - Fraction(-2**63))))
        $this->assertSame(-1.78, $result);
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
     * @test         toPercentile(50) of middle values -0.0 and 0.0 is 0.0 in either order, and -0.0 only when both are -0.0
     * @dataProvider dataProviderForSignedZeroMiddleValues
     * @param        array $data
     * @param        float $expectedReciprocal
     */
    public function testFiftiethPercentileOfSignedZeros(array $data, float $expectedReciprocal): void
    {
        // When
        $result = Reduce::toPercentile($data, 50);

        // Then (assertSame() takes -0.0 and 0.0 as the same, so the sign is checked through 1 / ±0.0 = ±INF)
        $this->assertSame(0.0, $result);
        $this->assertSame($expectedReciprocal, \fdiv(1.0, $result));
    }

    /**
     * Test data generated with NumPy (the sign of the zero result, as the reciprocal ±INF):
     *   math.copysign(math.inf, numpy.percentile(data, 50, method='linear'))
     */
    public static function dataProviderForSignedZeroMiddleValues(): array
    {
        return [
            [[-0.0, 0.0], \INF],
            [[0.0, -0.0], \INF],
            [[-0.0, -0.0], -\INF],
            [[0.0, 0.0], \INF],
            [[-0.0, 0], \INF],
            [[0, -0.0], \INF],
            [[1.0, -0.0, 0.0, -1.0], \INF],
            [[-1.0, 0.0, -0.0, 1.0], \INF],
            [[-0.0, -0.0, 1.0, -1.0], -\INF],
        ];
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

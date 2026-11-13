<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToQuantileTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test toQuantile example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $scores = [10, 20, 30, 40, 50];

        // When
        $q3 = Reduce::toQuantile($scores, 0.75);

        // Then (numpy.quantile([10, 20, 30, 40, 50], 0.75, method='linear'), landing on the element 40)
        $this->assertSame(40, $q3);
    }

    /**
     * @test         toQuantile array
     * @dataProvider dataProviderForQuantile
     * @param        array     $data
     * @param        float     $quantile
     * @param        int|float $expected
     */
    public function testArray(array $data, float $quantile, $expected): void
    {
        // When
        $result = Reduce::toQuantile($data, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toQuantile generator
     * @dataProvider dataProviderForQuantile
     * @param        array     $data
     * @param        float     $quantile
     * @param        int|float $expected
     */
    public function testGenerator(array $data, float $quantile, $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toQuantile($generator, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toQuantile iterator
     * @dataProvider dataProviderForQuantile
     * @param        array     $data
     * @param        float     $quantile
     * @param        int|float $expected
     */
    public function testIterator(array $data, float $quantile, $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toQuantile($iterator, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toQuantile traversable
     * @dataProvider dataProviderForQuantile
     * @param        array     $data
     * @param        float     $quantile
     * @param        int|float $expected
     */
    public function testTraversable(array $data, float $quantile, $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toQuantile($traversable, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   numpy.quantile(data, q, method='linear')
     * The int-vs-float result type is the library's contract, not the oracle's: a rank that lands on
     * an element returns that element, and the midpoint of two integers with an even sum is an integer.
     */
    public static function dataProviderForQuantile(): array
    {
        return [
            // Quantile maps [0, 1] onto the percentile [0, 100]
            [[1, 2, 3, 4], 0, 1],
            [[1, 2, 3, 4], 0.25, 1.75],
            [[1, 2, 3, 4], 0.5, 2.5],
            [[1, 2, 3, 4], 0.75, 3.25],
            [[1, 2, 3, 4], 1, 4],
            // Exact-rank landings
            [[1, 2, 3, 4, 5], 0.5, 3],
            // Unsorted input (output invariant to order)
            [[4, 2, 5, 1, 3], 0.5, 3],
            // Single element
            [[42], 0.5, 42],
            // Negatives
            [[-10, -5, 0, 5, 10], 0.5, 0],
            // Floats
            [[1.5, 2.5, 3.5], 0.5, 2.5],
        ];
    }

    /**
     * @test         toQuantile returns null on empty
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptyReturnsNull(iterable $data): void
    {
        // When
        $result = Reduce::toQuantile($data, 0.5);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test         toQuantile throws on out-of-range quantile
     * @dataProvider dataProviderForOutOfRange
     * @param        float $quantile
     */
    public function testOutOfRangeThrows(float $quantile): void
    {
        // Given
        $data = [1, 2, 3, 4, 5];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Reduce::toQuantile($data, $quantile);
    }

    public static function dataProviderForOutOfRange(): array
    {
        return [
            [-0.0001],
            [-1],
            [1.0001],
            [2],
            [\NAN],
        ];
    }

    /**
     * @test toQuantile agrees with toPercentile scaled by 100
     */
    public function testAgreesWithPercentile(): void
    {
        // Given
        $data = [3, 1, 4, 1, 5, 9, 2, 6];

        // When
        $quantile   = Reduce::toQuantile($data, 0.3);
        $percentile = Reduce::toPercentile($data, 30);

        // Then (0.3 * 100.0 is exactly 30.0, so both compute the same rank)
        $this->assertSame($percentile, $quantile);
    }

    /**
     * @test         toQuantile does not overflow when the interpolated span exceeds PHP_FLOAT_MAX
     * @dataProvider dataProviderForHugeSpan
     * @param        array $data
     * @param        float $quantile
     * @param        float $expected
     */
    public function testInterpolationOverHugeSpanDoesNotOverflow(array $data, float $quantile, float $expected): void
    {
        // When
        $result = Reduce::toQuantile($data, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test toQuantile at the median quantile of the two integer extremes keeps the exact half-unit result
     */
    public function testMedianQuantileOfIntegerExtremesIsExact(): void
    {
        // Given the widest possible integer span, whose midpoint is exactly -0.5
        $data = [\PHP_INT_MIN, \PHP_INT_MAX];

        // When
        $result = Reduce::toQuantile($data, 0.5);

        // Then (exact R-7: float((Fraction(-2**63) + Fraction(2**63 - 1)) / 2))
        $this->assertSame(-0.5, $result);
    }

    /**
     * @test toQuantile at the median quantile of two integers past 2^53 is the correctly rounded midpoint
     */
    public function testMedianQuantileOfLargeIntegersIsCorrectlyRounded(): void
    {
        // Given middle values whose true midpoint …993.5 lies between the floats …992 and …994
        // (on 32-bit PHP these literals parse as floats, so the expected value does not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $data = [9007199254740993, 9007199254740994];

        // When
        $result = Reduce::toQuantile($data, 0.5);

        // Then (exact R-7: float((Fraction(9007199254740993) + Fraction(9007199254740994)) / 2))
        $this->assertSame(9007199254740994.0, $result);
    }

    /**
     * @test toQuantile between two identical infinities is that infinity
     */
    public function testInterpolationBetweenIdenticalInfinitiesIsThatInfinity(): void
    {
        // When
        $result = Reduce::toQuantile([\INF, \INF], 0.5);

        // Then (statistics.quantiles([INF, INF], n=2, method='inclusive'); NumPy gives NAN from INF - INF)
        $this->assertSame(\INF, $result);
    }

    /**
     * @test         toQuantile(0.5) of middle values -0.0 and 0.0 is 0.0 in either order, and -0.0 only when both are -0.0
     * @dataProvider dataProviderForSignedZeroMiddleValues
     * @param        array $data
     * @param        float $expectedReciprocal
     */
    public function testHalfQuantileOfSignedZeros(array $data, float $expectedReciprocal): void
    {
        // When
        $result = Reduce::toQuantile($data, 0.5);

        // Then (assertSame() takes -0.0 and 0.0 as the same, so the sign is checked through 1 / ±0.0 = ±INF)
        $this->assertSame(0.0, $result);
        $this->assertSame($expectedReciprocal, \fdiv(1.0, $result));
    }

    /**
     * Test data generated with NumPy (the sign of the zero result, as the reciprocal ±INF):
     *   math.copysign(math.inf, numpy.quantile(data, 0.5, method='linear'))
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
     * Test data generated with Python (exact R-7 interpolation at p = 100q, correctly rounded to a float):
     *   h = (len(x) - 1) * Fraction(q), j = floor(h)
     *   float(Fraction(x[j]) + (h - j) * (Fraction(x[j + 1]) - Fraction(x[j])))
     * numpy.quantile is not the oracle here: its span x[j + 1] - x[j] overflows to INF.
     */
    public static function dataProviderForHugeSpan(): array
    {
        return [
            [[-1e308, 1e308], 0.5, 0.0],
            [[-1e308, 1e308], 0.75, 5e307],
            [[-1e308, 1e308], 0.25, -5e307],
            [[-1.5e308, 1.5e308], 0.5, 0.0],
        ];
    }

    /**
     * @test         toQuantile of data holding NAN is NAN, array
     * @dataProvider dataProviderForNan
     * @param        array $data
     * @param        float $quantile
     */
    public function testNanArray(array $data, float $quantile): void
    {
        // When
        $result = Reduce::toQuantile($data, $quantile);

        // Then
        $this->assertNan($result);
    }

    /**
     * @test         toQuantile of data holding NAN is NAN, generator
     * @dataProvider dataProviderForNan
     * @param        array $data
     * @param        float $quantile
     */
    public function testNanGenerator(array $data, float $quantile): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toQuantile($generator, $quantile);

        // Then
        $this->assertNan($result);
    }

    /**
     * @test         toQuantile of data holding NAN is NAN, iterator
     * @dataProvider dataProviderForNan
     * @param        array $data
     * @param        float $quantile
     */
    public function testNanIterator(array $data, float $quantile): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toQuantile($iterator, $quantile);

        // Then
        $this->assertNan($result);
    }

    /**
     * @test         toQuantile of data holding NAN is NAN, traversable
     * @dataProvider dataProviderForNan
     * @param        array $data
     * @param        float $quantile
     */
    public function testNanTraversable(array $data, float $quantile): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toQuantile($traversable, $quantile);

        // Then
        $this->assertNan($result);
    }

    /**
     * Test data generated with:
     *   numpy.quantile(data, q, method='linear')
     * which is nan for every row, wherever the nan sits (scripts: tools/oracles/nan_median_percentile_quantile.py).
     * The rows cover odd and even lengths, a NAN first, in the middle and last, and a NAN among infinities.
     */
    public static function dataProviderForNan(): array
    {
        return [
            [[\NAN, 1, 2], 0.0],
            [[\NAN, 1, 2], 0.25],
            [[\NAN, 1, 2], 0.5],
            [[\NAN, 1, 2], 1.0],
            [[1, \NAN, 2], 0.0],
            [[1, \NAN, 2], 0.25],
            [[1, \NAN, 2], 0.5],
            [[1, \NAN, 2], 1.0],
            [[1, 2, \NAN], 0.0],
            [[1, 2, \NAN], 0.25],
            [[1, 2, \NAN], 0.5],
            [[1, 2, \NAN], 1.0],
            [[\NAN], 0.0],
            [[\NAN], 0.25],
            [[\NAN], 0.5],
            [[\NAN], 1.0],
            [[\NAN, \NAN], 0.0],
            [[\NAN, \NAN], 0.25],
            [[\NAN, \NAN], 0.5],
            [[\NAN, \NAN], 1.0],
            [[1, 2, 3, \NAN], 0.0],
            [[1, 2, 3, \NAN], 0.25],
            [[1, 2, 3, \NAN], 0.5],
            [[1, 2, 3, \NAN], 1.0],
            [[\NAN, 4, 3, 2, 1], 0.0],
            [[\NAN, 4, 3, 2, 1], 0.25],
            [[\NAN, 4, 3, 2, 1], 0.5],
            [[\NAN, 4, 3, 2, 1], 1.0],
            [[1.5, \NAN, 2.5, 4.5], 0.0],
            [[1.5, \NAN, 2.5, 4.5], 0.25],
            [[1.5, \NAN, 2.5, 4.5], 0.5],
            [[1.5, \NAN, 2.5, 4.5], 1.0],
            [[-\INF, \NAN, \INF], 0.0],
            [[-\INF, \NAN, \INF], 0.25],
            [[-\INF, \NAN, \INF], 0.5],
            [[-\INF, \NAN, \INF], 1.0],
        ];
    }

    /**
     * @test toQuantile still rejects an out-of-range quantile when the data holds NAN
     */
    public function testOutOfRangeQuantileThrowsForDataHoldingNan(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Reduce::toQuantile([1, \NAN, 3], 1.5);
    }

    /**
     * @test         toQuantile array of ints beyond 2^53 and floats, ordered by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array     $data
     * @param        float     $quantile
     * @param        int|float $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionArray(array $data, float $quantile, int|float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toQuantile($data, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toQuantile generator of ints beyond 2^53 and floats, ordered by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array     $data
     * @param        float     $quantile
     * @param        int|float $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionGenerator(array $data, float $quantile, int|float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toQuantile($iterable, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toQuantile iterator of ints beyond 2^53 and floats, ordered by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array     $data
     * @param        float     $quantile
     * @param        int|float $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionIterator(array $data, float $quantile, int|float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toQuantile($iterable, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toQuantile traversable of ints beyond 2^53 and floats, ordered by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array     $data
     * @param        float     $quantile
     * @param        int|float $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionTraversable(array $data, float $quantile, int|float $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toQuantile($iterable, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which orders an int and a float by exact value
     * (tools/oracles/exact_order_percentile.py, with q = p / 100):
     *   s = sorted(data), h = (len(s) - 1) * Fraction(q), j = floor(h)
     *   s[j] if h == j else float(Fraction(s[j]) + (h - j) * (Fraction(s[j + 1]) - Fraction(s[j])))
     * Every rank lands on an element, so the result is that element.
     * NumPy is not the oracle here: it converts the ints to float64, which can't tell them from the floats.
     * Each int compares equal as a float to a float it is not equal to, so <=> orders them wrongly.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecision(): array
    {
        return [
            '2^62 + 500, 2.0^62, 2^62 + 1 at 0.0: the float is the minimum' => [
                [4611686018427388404, 4611686018427387904.0, 4611686018427387905],
                0.0,
                4611686018427387904.0,
            ],
            '2^62 + 500, 2.0^62, 2^62 + 1 at 0.5: the int 2^62 + 1 is the middle' => [
                [4611686018427388404, 4611686018427387904.0, 4611686018427387905],
                0.5,
                4611686018427387905,
            ],
            '2^62 + 500, 2.0^62, 2^62 + 1 at 1.0: the int 2^62 + 500 is the maximum' => [
                [4611686018427388404, 4611686018427387904.0, 4611686018427387905],
                1.0,
                4611686018427388404,
            ],
            '2^53 + 1, 2.0^53 at 0.0' => [
                [9007199254740993, 9007199254740992.0],
                0.0,
                9007199254740992.0,
            ],
            '2^53 + 1, 2.0^53 at 1.0' => [
                [9007199254740993, 9007199254740992.0],
                1.0,
                9007199254740993,
            ],
            '-2.0^53, -(2^53 + 1) at 0.0' => [
                [-9007199254740992.0, -9007199254740993],
                0.0,
                -9007199254740993,
            ],
            '-2.0^53, -(2^53 + 1) at 1.0' => [
                [-9007199254740992.0, -9007199254740993],
                1.0,
                -9007199254740992.0,
            ],
        ];
    }

    /**
     * @test         toQuantile locates a rank just off an integer next to an infinity exactly (array)
     * @dataProvider dataProviderForExactRankWithInfiniteNeighbour
     * @param        array $data
     * @param        float $quantile
     * @param        float $expected
     */
    public function testExactRankWithInfiniteNeighbourArray(array $data, float $quantile, float $expected): void
    {
        // When
        $result = Reduce::toQuantile($data, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toQuantile locates a rank just off an integer next to an infinity exactly (generator)
     * @dataProvider dataProviderForExactRankWithInfiniteNeighbour
     * @param        array $data
     * @param        float $quantile
     * @param        float $expected
     */
    public function testExactRankWithInfiniteNeighbourGenerator(array $data, float $quantile, float $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toQuantile($generator, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toQuantile locates a rank just off an integer next to an infinity exactly (iterator)
     * @dataProvider dataProviderForExactRankWithInfiniteNeighbour
     * @param        array $data
     * @param        float $quantile
     * @param        float $expected
     */
    public function testExactRankWithInfiniteNeighbourIterator(array $data, float $quantile, float $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toQuantile($iterator, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toQuantile locates a rank just off an integer next to an infinity exactly (traversable)
     * @dataProvider dataProviderForExactRankWithInfiniteNeighbour
     * @param        array $data
     * @param        float $quantile
     * @param        float $expected
     */
    public function testExactRankWithInfiniteNeighbourTraversable(array $data, float $quantile, float $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toQuantile($traversable, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (the exact rank, then the limit of linear interpolation strictly
     * between a finite value and an infinity, which is that infinity: textbook definition):
     *   s = sorted(data), h = (len(s) - 1) * Fraction(q), j = floor(h)
     *   with j < h < j + 1, and s[j] = -INF or s[j + 1] = INF
     * Each q is not exactly representable: 100 * q rounds onto a value whose rank is an integer or lies
     * past one, which picks the wrong neighbours. NumPy is not the oracle here: it rounds (len - 1) * q.
     */
    public static function dataProviderForExactRankWithInfiniteNeighbour(): array
    {
        return [
            'rank 1 + 2^-54 between 0 and INF' => [
                [0, 0, \INF, \INF, \INF, \INF],
                0.2,
                \INF,
            ],
            'rank 2 - 2^-55 between -INF and 0 (q = 2 / 15)' => [
                [-\INF, -\INF, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                0.13333333333333333,
                -\INF,
            ],
            'rank just below 3 between -INF and 0' => [
                [-\INF, -\INF, -\INF, 0, 0, 0],
                0.6,
                -\INF,
            ],
            'rank just above 3 between 0.0 and INF' => [
                [0.0, 0.0, 0.0, 0.0, \INF, \INF, \INF, \INF, \INF, \INF, \INF, \INF, \INF, \INF, \INF, \INF],
                0.2,
                \INF,
            ],
            'rank just above 3 between 4 and INF (q = 3 / 13)' => [
                [1, 2, 3, 4, \INF, \INF, \INF, \INF, \INF, \INF, \INF, \INF, \INF, \INF],
                0.23076923076923078,
                \INF,
            ],
            'rank just below 7 between -INF and 1 (q = 7 / 13)' => [
                [-\INF, -\INF, -\INF, -\INF, -\INF, -\INF, -\INF, 1, 2, 3, 4, 5, 6, 7],
                0.5384615384615384,
                -\INF,
            ],
        ];
    }

    /**
     * @test         toQuantile interpolates at the exact rank of a quantile that is not exactly representable (array)
     * @dataProvider dataProviderForExactRankWithFiniteNeighbours
     * @param        array $data
     * @param        float $quantile
     * @param        float $expected
     */
    public function testExactRankWithFiniteNeighboursArray(array $data, float $quantile, float $expected): void
    {
        // When
        $result = Reduce::toQuantile($data, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toQuantile interpolates at the exact rank of a quantile that is not exactly representable (generator)
     * @dataProvider dataProviderForExactRankWithFiniteNeighbours
     * @param        array $data
     * @param        float $quantile
     * @param        float $expected
     */
    public function testExactRankWithFiniteNeighboursGenerator(array $data, float $quantile, float $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toQuantile($generator, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toQuantile interpolates at the exact rank of a quantile that is not exactly representable (iterator)
     * @dataProvider dataProviderForExactRankWithFiniteNeighbours
     * @param        array $data
     * @param        float $quantile
     * @param        float $expected
     */
    public function testExactRankWithFiniteNeighboursIterator(array $data, float $quantile, float $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toQuantile($iterator, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toQuantile interpolates at the exact rank of a quantile that is not exactly representable (traversable)
     * @dataProvider dataProviderForExactRankWithFiniteNeighbours
     * @param        array $data
     * @param        float $quantile
     * @param        float $expected
     */
    public function testExactRankWithFiniteNeighboursTraversable(array $data, float $quantile, float $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toQuantile($traversable, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact R-7 interpolation at the exact rank, correctly rounded):
     *   s = sorted(data), h = (len(s) - 1) * Fraction(q), j = floor(h)
     *   float(Fraction(s[j]) + (h - j) * (Fraction(s[j + 1]) - Fraction(s[j])))
     * NumPy is not the oracle here: it rounds (len - 1) * q, and gives 7.449999999999998 for the 0.7 row.
     */
    public static function dataProviderForExactRankWithFiniteNeighbours(): array
    {
        return [
            'rank 1 + 2^-54 between 0 and 1e300' => [
                [0, 0, 1e300, 1e300, 1e300, 1e300],
                0.2,
                5.551115123125783e+283,
            ],
            'rank just below 3 between -1e300 and 0' => [
                [-1e300, -1e300, -1e300, 0, 0, 0],
                0.6,
                -1.1102230246251566e+284,
            ],
            'rank just below 2.1' => [
                [1.5, 2.25, 7.0, 11.5],
                0.7,
                7.449999999999999,
            ],
            'rank just below 1.2 between 0 and 1e300' => [
                [0, 0, 1e300],
                0.6,
                1.9999999999999997e+299,
            ],
        ];
    }

    /**
     * @test         toQuantile of large integers interpolates at the exact rank when 100 * q is not exact
     * @dataProvider dataProviderForExactRankOfLargeIntegerInterpolation
     * @param        array $data
     * @param        float $quantile
     * @param        float $expected
     */
    public function testExactRankOfLargeIntegerInterpolation(array $data, float $quantile, float $expected): void
    {
        // Given (on 32-bit PHP these literals parse as floats, so the expected value does not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toQuantile($data, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact R-7 interpolation at the exact rank, correctly rounded):
     *   s = sorted(data), h = (len(s) - 1) * Fraction(q), j = floor(h)
     *   float(Fraction(s[j]) + (h - j) * (Fraction(s[j + 1]) - Fraction(s[j])))
     * NumPy is not the oracle here: it converts the ints to float64.
     */
    public static function dataProviderForExactRankOfLargeIntegerInterpolation(): array
    {
        return [
            'PHP_INT_MIN, PHP_INT_MAX at 0.5 + 2^-53' => [[\PHP_INT_MIN, \PHP_INT_MAX], 0.5000000000000001, 2047.5],
            'PHP_INT_MIN, PHP_INT_MAX at 0.7'         => [[\PHP_INT_MIN, \PHP_INT_MAX], 0.7, 3.6893488147419095e+18],
            'PHP_INT_MIN, PHP_INT_MAX at 1 / 3'       => [[\PHP_INT_MIN, \PHP_INT_MAX], 0.3333333333333333, -3.074457345618259e+18],
            '-2^62, 2^62 + 7 at 0.6'                  => [[-4611686018427387904, 4611686018427387911], 0.6, 9.223372036854774e+17],
            'PHP_INT_MIN, PHP_INT_MAX at 0.1'         => [[\PHP_INT_MIN, \PHP_INT_MAX], 0.1, -7.378697629483821e+18],
            '2^60 + 1, 2^62 + 3 at 0.1'               => [[1152921504606846977, 4611686018427387907], 0.1, 1.498797955988901e+18],
        ];
    }

    /**
     * @test         toQuantile keeps a rank that is correctly rounded as a float
     * @dataProvider dataProviderForRankCorrectlyRounded
     * @param        array $data
     * @param        float $quantile
     * @param        float $expected
     */
    public function testRankCorrectlyRounded(array $data, float $quantile, float $expected): void
    {
        // When
        $result = Reduce::toQuantile($data, $quantile);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact R-7 interpolation at the exact rank, correctly rounded):
     *   s = sorted(data), h = (len(s) - 1) * Fraction(q), j = floor(h)
     *   float(Fraction(s[j]) + (h - j) * (Fraction(s[j + 1]) - Fraction(s[j])))
     * Cross-checked with numpy.quantile(data, q, method='linear'), which agrees on every row.
     * The binary 0.28 is above 0.28, so the 25th value 7 is passed: 7.000000000000001 is correct.
     */
    public static function dataProviderForRankCorrectlyRounded(): array
    {
        return [
            'range(0, 25) at 0.28' => [
                [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25],
                0.28,
                7.000000000000001,
            ],
            '1, 2, 3, 4 at 0.1'                 => [[1, 2, 3, 4], 0.1, 1.3],
            '10, 20, 30, 40, 50 at 0.3'         => [[10, 20, 30, 40, 50], 0.3, 22.0],
            'unsorted at 0.9'                   => [[-3, 8, 1, 5, 2, 9, 4], 0.9, 8.4],
            '0.1, 0.2, 0.3 at 0.35'             => [[0.1, 0.2, 0.3], 0.35, 0.17],
        ];
    }

    /**
     * @test         toQuantile(0.5) is identical to toMedian and toPercentile(50)
     * @dataProvider dataProviderForHalfQuantileFinite
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testHalfQuantileMatchesMedianAndPercentile(array $data, int|float $expected): void
    {
        // When
        $result = Reduce::toQuantile($data, 0.5);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame(Reduce::toMedian($data), $result);
        $this->assertSame(Reduce::toPercentile($data, 50), $result);
    }

    /**
     * Test data generated with Python (the element at an exact rank, else the exact midpoint):
     *   s = sorted(data), h = (len(s) - 1) * Fraction(1, 2), j = floor(h)
     *   v = s[j] if h == j else (Fraction(s[j]) + Fraction(s[j + 1])) / 2
     * The oracle gives the exact value v; its int-vs-float type is the library's contract, not the oracle's.
     * A rank that lands on an element returns that element, and the midpoint of two integers with an even
     * sum is the integer v. Any other result is float(v), v rounded once.
     */
    public static function dataProviderForHalfQuantileFinite(): array
    {
        return [
            'odd count'                      => [[3, 1, 2], 2],
            'even count'                     => [[4, 1, 3, 2], 2.5],
            'odd count of floats'            => [[1.5, 2.5, 3.5, 4.5, 10.0], 3.5],
            'even count, even sum'           => [[5, -2, 7, 1], 3],
            'infinite extremes, finite pair' => [[-\INF, 1, 2, \INF], 1.5],
        ];
    }

    /**
     * @test         toQuantile(0.5) of integers past 2^53 is identical to toMedian and toPercentile(50)
     * @dataProvider dataProviderForHalfQuantileLargeIntegers
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testHalfQuantileMatchesMedianAndPercentileForLargeIntegers(array $data, int|float $expected): void
    {
        // Given (on 32-bit PHP these literals parse as floats, so the expected value does not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toQuantile($data, 0.5);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame(Reduce::toMedian($data), $result);
        $this->assertSame(Reduce::toPercentile($data, 50), $result);
    }

    /**
     * Test data generated with Python (the exact midpoint):
     *   v = (Fraction(a) + Fraction(b)) / 2
     * The oracle gives the exact value v; its int-vs-float type is the library's contract, not the oracle's.
     * The midpoint of two integers with an even sum is the integer v. Any other result is float(v), v
     * rounded once.
     */
    public static function dataProviderForHalfQuantileLargeIntegers(): array
    {
        return [
            '2^53 + 1, 2^53 + 2: midpoint rounds to 2^53 + 2'  => [[9007199254740993, 9007199254740994], 9007199254740994.0],
            '2^53 + 1, 2^53 + 3: exact integer midpoint'       => [[9007199254740993, 9007199254740995], 9007199254740994],
            '-(2^53 + 2), -(2^53 + 1): midpoint rounds'        => [[-9007199254740994, -9007199254740993], -9007199254740994.0],
        ];
    }

    /**
     * @test         toQuantile(0.5) next to an infinity is identical to toMedian and toPercentile(50)
     * @dataProvider dataProviderForHalfQuantileInfiniteNeighbour
     * @param        array $data
     * @param        float $expected
     */
    public function testHalfQuantileWithInfiniteNeighbourMatchesMedianAndPercentile(array $data, float $expected): void
    {
        // When
        $result = Reduce::toQuantile($data, 0.5);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame(Reduce::toMedian($data), $result);
        $this->assertSame(Reduce::toPercentile($data, 50), $result);
    }

    /**
     * Test data from the textbook definition: the limit of linear interpolation strictly between a finite
     * value and an infinity is that infinity. NumPy is not the oracle here: -INF + 0.5 * INF gives NAN.
     */
    public static function dataProviderForHalfQuantileInfiniteNeighbour(): array
    {
        return [
            '-INF, -1.0'        => [[-\INF, -1.0], -\INF],
            '1.0, INF'          => [[1.0, \INF], \INF],
            '-INF, -INF, 0, 1'  => [[-\INF, -\INF, 0, 1], -\INF],
            '0, 1, INF, INF'    => [[0, 1, \INF, \INF], \INF],
        ];
    }

    /**
     * @test         toQuantile between integers past 2^53 is correctly rounded and identical to toPercentile
     * @dataProvider dataProviderForLargeIntegerInterpolation
     * @param        array $data
     * @param        float $quantile
     * @param        float $expected
     */
    public function testLargeIntegerInterpolationIsCorrectlyRounded(array $data, float $quantile, float $expected): void
    {
        // Given (on 32-bit PHP these literals parse as floats, so the expected value does not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = Reduce::toQuantile($data, $quantile);

        // Then (100 * $quantile is exact for every row, so the percentile has the same rank)
        $this->assertSame($expected, $result);
        $this->assertSame(Reduce::toPercentile($data, $quantile * 100.0), $result);
    }

    /**
     * Test data generated with Python (exact R-7 interpolation at the exact rank, correctly rounded):
     *   s = sorted(data), h = (len(s) - 1) * Fraction(q), j = floor(h)
     *   float(Fraction(s[j]) + (h - j) * (Fraction(s[j + 1]) - Fraction(s[j])))
     * NumPy is not the oracle here: it converts the ints to float64.
     */
    public static function dataProviderForLargeIntegerInterpolation(): array
    {
        return [
            '2^53 + 1, 2^53 + 4 at 0.25'               => [[9007199254740993, 9007199254740996], 0.25, 9007199254740994.0],
            '2^53 + 1, 2^53 + 4 at 0.75'               => [[9007199254740993, 9007199254740996], 0.75, 9007199254740996.0],
            'PHP_INT_MIN, PHP_INT_MAX at 0.25'         => [[\PHP_INT_MIN, \PHP_INT_MAX], 0.25, -4.611686018427388e+18],
            'PHP_INT_MIN, PHP_INT_MAX at 0.75'         => [[\PHP_INT_MIN, \PHP_INT_MAX], 0.75, 4.611686018427388e+18],
            'PHP_INT_MIN, PHP_INT_MAX at 0.125'        => [[\PHP_INT_MIN, \PHP_INT_MAX], 0.125, -6.917529027641082e+18],
            '0, 2^53 + 1 at the smallest subnormal'    => [[0, 9007199254740993], 5e-324, 4.450147717014403e-308],
            '-(2^53 + 1), 0 at the smallest subnormal' => [[-9007199254740993, 0], 5e-324, -9007199254740992.0],
            '0, 2^53 + 3 at 2^-1060'                   => [[0, 9007199254740995], 8.095e-320, 7.2911220195564e-304],
        ];
    }

    /**
     * @test toQuantile between integers past 2^53 whose interpolation is exactly zero gives 0.0
     */
    public function testLargeIntegerInterpolationIsCorrectlyRoundedToPositiveZero(): void
    {
        // Given (on 32-bit PHP these literals parse as floats, so the expected value does not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $data = [-18014398509481984, 54043195528445952];

        // When
        $result = Reduce::toQuantile($data, 0.25);

        // Then (Python: h = Fraction(1, 4), Fraction(-2**54) + h * (Fraction(3 * 2**54) - Fraction(-2**54)) == 0;
        // math.copysign(math.inf, float(0)) is INF. assertSame() takes -0.0 and 0.0 as the same.)
        $this->assertSame(0.0, $result);
        $this->assertSame(\INF, \fdiv(1.0, $result));
        $this->assertSame(\INF, \fdiv(1.0, Reduce::toPercentile($data, 25)));
    }

    /**
     * @test         toQuantile on ints returns the same int-or-float type as toPercentile at the same rank
     * @dataProvider dataProviderForResultType
     * @param        array     $data
     * @param        float     $quantile
     * @param        int|float $expected
     */
    public function testResultTypeMatchesPercentile(array $data, float $quantile, int|float $expected): void
    {
        // When
        $result = Reduce::toQuantile($data, $quantile);

        // Then (100 * $quantile is exact for every row, so the percentile has the same rank)
        $this->assertSame($expected, $result);
        $this->assertSame(Reduce::toPercentile($data, $quantile * 100.0), $result);
    }

    /**
     * Test data generated with Python (the element at an exact rank, else exact R-7 interpolation):
     *   s = sorted(data), h = (len(s) - 1) * Fraction(q), j = floor(h)
     *   v = s[j] if h == j else Fraction(s[j]) + (h - j) * (Fraction(s[j + 1]) - Fraction(s[j]))
     * The oracle gives the exact value v; its int-vs-float type is the library's contract, not the oracle's.
     * A rank that lands on an element returns that element, and the midpoint of two integers with an even
     * sum is the integer v. Any other result is float(v), v rounded once.
     */
    public static function dataProviderForResultType(): array
    {
        return [
            'exact rank on an int'              => [[1, 2, 3, 4, 5], 0.25, 2],
            'interpolation between ints'        => [[1, 2, 3, 4], 0.25, 1.75],
            'midpoint of ints with an even sum' => [[1, 3], 0.5, 2],
        ];
    }

    /**
     * @test         toQuantile keeps the sign of a zero element its rank lands on, as toPercentile does
     * @dataProvider dataProviderForSignedZeroExactRank
     * @param        array $data
     * @param        float $quantile
     * @param        float $expectedReciprocal
     */
    public function testSignedZeroExactRankMatchesPercentile(array $data, float $quantile, float $expectedReciprocal): void
    {
        // When
        $result     = Reduce::toQuantile($data, $quantile);
        $percentile = Reduce::toPercentile($data, $quantile * 100.0);

        // Then (assertSame() takes -0.0 and 0.0 as the same, so the sign is checked through 1 / ±0.0 = ±INF)
        $this->assertSame(0.0, $result);
        $this->assertSame($expectedReciprocal, \fdiv(1.0, $result));
        $this->assertSame($expectedReciprocal, \fdiv(1.0, $percentile));
    }

    /**
     * Test data generated with Python (the sign of the element at the exact rank, as the reciprocal ±INF):
     *   s = sorted(data), h = (len(s) - 1) * Fraction(q), math.copysign(math.inf, s[int(h)]) with h an integer
     */
    public static function dataProviderForSignedZeroExactRank(): array
    {
        return [
            'minimum -0.0'          => [[-0.0, 1.0], 0.0, -\INF],
            'middle -0.0'           => [[-0.0, -0.0, 5.0], 0.5, -\INF],
            'maximum -0.0'          => [[-1.0, -0.0], 1.0, -\INF],
            'first quartile -0.0'   => [[-0.0, -0.0, -0.0, 2.0, 3.0], 0.25, -\INF],
        ];
    }

    /**
     * @test         toQuantile between signed zeros gives the zero toPercentile gives
     * @dataProvider dataProviderForSignedZeroInterpolation
     * @param        array $data
     * @param        float $quantile
     * @param        float $expectedReciprocal
     */
    public function testSignedZeroInterpolationMatchesPercentile(array $data, float $quantile, float $expectedReciprocal): void
    {
        // When
        $result     = Reduce::toQuantile($data, $quantile);
        $percentile = Reduce::toPercentile($data, $quantile * 100.0);

        // Then (assertSame() takes -0.0 and 0.0 as the same, so the sign is checked through 1 / ±0.0 = ±INF)
        $this->assertSame(0.0, $result);
        $this->assertSame($expectedReciprocal, \fdiv(1.0, $result));
        $this->assertSame($expectedReciprocal, \fdiv(1.0, $percentile));
    }

    /**
     * Test data generated with NumPy (the sign of the zero result, as the reciprocal ±INF):
     *   math.copysign(math.inf, numpy.quantile(data, q, method='linear'))
     */
    public static function dataProviderForSignedZeroInterpolation(): array
    {
        return [
            '-0.0, -0.0 at their midpoint'        => [[-0.0, -0.0, 5.0], 0.25, -\INF],
            '-0.0, -0.0 nearer the upper'         => [[-0.0, -0.0, 5.0], 0.375, -\INF],
            '-0.0, -0.0 nearer the lower'         => [[-0.0, -0.0, 5.0], 0.125, \INF],
            '-0.0, 0.0 at their midpoint'         => [[-0.0, 0.0, 1.0], 0.25, \INF],
            'upper -0.0, -0.0 at their midpoint'  => [[-1.0, -0.0, -0.0], 0.75, -\INF],
            'upper -0.0, -0.0 nearer the upper'   => [[-1.0, -0.0, -0.0], 0.875, -\INF],
        ];
    }

    /**
     * @test rounding an expansion that is not nonadjacent throws rather than looping without end
     */
    public function testNonConvergingRoundingThrows(): void
    {
        // Given nonoverlapping components whose float sum 2^-52 is far from their value 2^-54 + 2^-53
        $method    = new \ReflectionMethod(Reduce::class, 'roundExpansionDividedBy');
        $expansion = [2 ** -54, -(1 - 2 ** -53), 1.0];

        // When
        try {
            $method->invoke(null, $expansion, 1.0);
            $this->fail('Expected a LogicException');
        } catch (\LogicException $e) {
            // Then
            $this->assertSame('Rounding an interpolated value did not converge.', $e->getMessage());
        }
    }
}

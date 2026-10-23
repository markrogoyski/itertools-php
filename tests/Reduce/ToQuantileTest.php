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
}

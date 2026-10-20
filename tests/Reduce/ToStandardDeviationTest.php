<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToStandardDeviationTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test toStandardDeviation example usage (population)
     */
    public function testExampleUsage(): void
    {
        // Given
        $numbers = [2, 4, 4, 4, 5, 5, 7, 9];

        // When
        $stddev = Reduce::toStandardDeviation($numbers);

        // Then (expected value from numpy.std([2, 4, 4, 4, 5, 5, 7, 9], ddof=0))
        $this->assertSame(2.0, $stddev);
    }

    /**
     * @test         toStandardDeviation array
     * @dataProvider dataProviderForStandardDeviation
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testArray(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toStandardDeviation($data, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toStandardDeviation generator
     * @dataProvider dataProviderForStandardDeviation
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testGenerator(array $data, bool $sample, float $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toStandardDeviation($generator, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toStandardDeviation iterator
     * @dataProvider dataProviderForStandardDeviation
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testIterator(array $data, bool $sample, float $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toStandardDeviation($iterator, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toStandardDeviation traversable
     * @dataProvider dataProviderForStandardDeviation
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testTraversable(array $data, bool $sample, float $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toStandardDeviation($traversable, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   numpy.std(data, ddof=1 if sample else 0)
     */
    public static function dataProviderForStandardDeviation(): array
    {
        return [
            // Population standard deviation
            [[1, 2, 3, 4, 5], false, 1.4142135623730951],
            [[2, 4, 4, 4, 5, 5, 7, 9], false, 2.0],
            [[10, 20], false, 5.0],
            [[7, 7, 7], false, 0.0],
            // Sample standard deviation (Bessel's correction)
            [[1, 2, 3, 4, 5], true, 1.5811388300841898],
            [[2, 4, 4, 4, 5, 5, 7, 9], true, 2.138089935299395],
            [[10, 20], true, 7.0710678118654755],
            [[7, 7, 7], true, 0.0],
            // Floats
            [[1.5, 2.5, 3.5], false, 0.816496580927726],
            // Order invariance
            [[9, 5, 4, 4, 7, 4, 2, 5], false, 2.0],
        ];
    }

    /**
     * @test         toStandardDeviation returns null on empty (population)
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptyPopulationReturnsNull(iterable $data): void
    {
        // When
        $result = Reduce::toStandardDeviation($data);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test         toStandardDeviation returns null on empty (sample)
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptySampleReturnsNull(iterable $data): void
    {
        // When
        $result = Reduce::toStandardDeviation($data, true);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test toStandardDeviation of a single value is 0.0 for population
     */
    public function testSingleValuePopulationIsZero(): void
    {
        // When
        $result = Reduce::toStandardDeviation([5]);

        // Then
        $this->assertSame(0.0, $result);
    }

    /**
     * @test toStandardDeviation of a single value is null for sample (undefined)
     */
    public function testSingleValueSampleIsNull(): void
    {
        // When
        $result = Reduce::toStandardDeviation([5], true);

        // Then
        $this->assertNull($result);
    }


    /**
     * @test toStandardDeviation of a very large collection (population)
     */
    public function testVeryLargeNPopulation(): void
    {
        // Given the uniform sequence 1..10000, with the expected value generated with Python as the
        // correctly rounded square root of the exact variance (see dataProviderForHugeValues)
        $n        = 10000;
        $data     = \range(1, $n);
        $expected = 2886.751331514372;

        // When
        $result = Reduce::toStandardDeviation($data);

        // Then (the online update rounds once per value, and n independent roundings accumulate like
        // a random walk, so the relative error grows as about sqrt(n) * epsilon rather than staying
        // within an ulp; the worst-case bound n * epsilon is far looser)
        $this->assertEqualsWithDelta($expected, $result, \sqrt($n) * \PHP_FLOAT_EPSILON * $expected);
    }

    /**
     * @test         toStandardDeviation does not overflow for values whose sum exceeds PHP_FLOAT_MAX
     * @dataProvider dataProviderForHugeValues
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testHugeValuesDoNotOverflow(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toStandardDeviation($data, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational variance, then its correctly rounded square root):
     *   mean = sum(map(Fraction, data)) / n
     *   v    = sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof)
     *   then sqrt(v) correctly rounded: math.isqrt of v scaled by an even power of two to ~128 bits,
     *   a sticky bit for an inexact root, and one correctly rounded int / int division to a float
     * NumPy cannot serve here: its float64 sum overflows to INF.
     */
    public static function dataProviderForHugeValues(): array
    {
        return [
            [[1e308, 1e308], false, 0.0],
            [[1e308, 1e308], true, 0.0],
            [[1e308, 1e308, 1e308], false, 0.0],
            [[-1e308, -1e308], false, 0.0],
            [[-1e308, -1e308], true, 0.0],
        ];
    }

    /**
     * @test         toStandardDeviation is finite when the result is representable but the squared deviations are not
     * @dataProvider dataProviderForRepresentableStdDevOfHugeValues
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testRepresentableStdDevOfHugeValuesIsFinite(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toStandardDeviation($data, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational variance, then its correctly rounded square root):
     *   mean = sum(map(Fraction, data)) / n
     *   v    = sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof)
     *   then sqrt(v) correctly rounded: math.isqrt of v scaled by an even power of two to ~128 bits,
     *   a sticky bit for an inexact root, and one correctly rounded int / int division to a float
     */
    public static function dataProviderForRepresentableStdDevOfHugeValues(): array
    {
        return [
            [[-1e154, 1e154], false, 1.0E+154],
            [[-1e154, 0, 1e154], false, 8.164965809277261E+153],
            [[0, 1e154], false, 5.0E+153],
        ];
    }

    /**
     * @test         toStandardDeviation returns null for the sample standard deviation of a single non-finite value
     * @dataProvider dataProviderForNonFiniteSingleton
     * @param        array $data
     */
    public function testNonFiniteSingletonSampleStdDevIsNull(array $data): void
    {
        // When
        $result = Reduce::toStandardDeviation($data, true);

        // Then (the undefined N - 1 = 0 divisor wins over NAN propagation)
        $this->assertNull($result);
    }

    /**
     * @test         toStandardDeviation of a single non-finite value is NAN for the population standard deviation
     * @dataProvider dataProviderForNonFiniteSingleton
     * @param        array $data
     */
    public function testNonFiniteSingletonPopulationStdDevIsNan(array $data): void
    {
        // When
        $result = Reduce::toStandardDeviation($data);

        // Then
        $this->assertNan($result);
    }

    public static function dataProviderForNonFiniteSingleton(): array
    {
        return [
            [[\INF]],
            [[-\INF]],
            [[\NAN]],
        ];
    }

    /**
     * @test         toStandardDeviation is finite when an intermediate prefix variance is not representable
     * @dataProvider dataProviderForPrefixOverflow
     * @param        array $data
     * @param        float $expected
     */
    public function testPrefixOverflowStillYieldsRepresentableResult(array $data, float $expected): void
    {
        // When
        $result = Reduce::toStandardDeviation($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational variance, then its correctly rounded square root):
     *   mean = sum(map(Fraction, data)) / n
     *   v    = sum((Fraction(x) - mean) ** 2 for x in data) / n
     *   then sqrt(v) correctly rounded: math.isqrt of v scaled by an even power of two to ~128 bits,
     *   a sticky bit for an inexact root, and one correctly rounded int / int division to a float
     */
    public static function dataProviderForPrefixOverflow(): array
    {
        return [
            // The variance of the leading pair is ~1.96e308, beyond PHP_FLOAT_MAX
            [[-1.4e154, 1.4e154, 0], 1.1430952132988164E+154],
        ];
    }

    /**
     * @test         toStandardDeviation is finite, to within rounding, when an intermediate prefix variance is not representable
     * @dataProvider dataProviderForPrefixOverflowWithinRounding
     * @param        array $data
     * @param        float $expected
     */
    public function testPrefixOverflowStillYieldsRepresentableResultWithinRounding(array $data, float $expected): void
    {
        // When
        $result = Reduce::toStandardDeviation($data);

        // Then (the result is not correctly rounded: it passes through a handful of rounded steps — the
        // per-value updates and the final square root and unscaling — so it may sit a few ulps from the
        // oracle)
        $this->assertEqualsWithDelta($expected, $result, 4 * \PHP_FLOAT_EPSILON * \abs($expected));
    }

    /**
     * Test data generated with Python (exact rational variance, then its correctly rounded square root):
     *   mean = sum(map(Fraction, data)) / n
     *   v    = sum((Fraction(x) - mean) ** 2 for x in data) / n
     *   then sqrt(v) correctly rounded: math.isqrt of v scaled by an even power of two to ~128 bits,
     *   a sticky bit for an inexact root, and one correctly rounded int / int division to a float
     */
    public static function dataProviderForPrefixOverflowWithinRounding(): array
    {
        return [
            // The variance of the leading pair is ~2.25e308, beyond PHP_FLOAT_MAX
            [[-1.5e154, 1.5e154, 0, 0], 1.0606601717798214E+154],
        ];
    }

    /**
     * @test         toStandardDeviation is representable when the variance itself overflows or underflows
     * @dataProvider dataProviderForUnrepresentableVariance
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testRepresentableStdDevOfUnrepresentableVariance(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toStandardDeviation($data, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational variance, then its correctly rounded square root):
     *   mean = sum(map(Fraction, data)) / n
     *   v    = sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof)
     *   then sqrt(v) correctly rounded: math.isqrt of v scaled by an even power of two to ~128 bits,
     *   a sticky bit for an inexact root, and one correctly rounded int / int division to a float
     */
    public static function dataProviderForUnrepresentableVariance(): array
    {
        return [
            // Variance 1e400 overflows
            [[-1e200, 1e200], false, 1.0E+200],
            [[-1e200, 1e200], true, 1.414213562373095E+200],
            [[-1e200, 0, 1e200], false, 8.16496580927726E+199],
            // Values 2 ** 666 apart, so the standard deviation is exactly 2 ** 665
            [[2.0 ** 700, 2.0 ** 700 + 2.0 ** 666], false, 1.5309010345804195E+200],
            // Variance 1e-400 underflows
            [[-1e-200, 1e-200], false, 1.0E-200],
            [[-1e-200, 1e-200], true, 1.414213562373095E-200],
            [[-1e-200, 0, 1e-200], false, 8.16496580927726E-201],
        ];
    }

    /**
     * @test         toStandardDeviation array is finite when a deviation step overflows
     * @dataProvider dataProviderForDeviationOverflow
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testDeviationOverflowArray(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toStandardDeviation($data, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toStandardDeviation generator is finite when a deviation step overflows
     * @dataProvider dataProviderForDeviationOverflow
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testDeviationOverflowGenerator(array $data, bool $sample, float $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toStandardDeviation($generator, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toStandardDeviation iterator is finite when a deviation step overflows
     * @dataProvider dataProviderForDeviationOverflow
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testDeviationOverflowIterator(array $data, bool $sample, float $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toStandardDeviation($iterator, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toStandardDeviation traversable is finite when a deviation step overflows
     * @dataProvider dataProviderForDeviationOverflow
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testDeviationOverflowTraversable(array $data, bool $sample, float $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toStandardDeviation($traversable, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * The difference between a value and the running mean exceeds PHP_FLOAT_MAX at some step, although
     * no deviation from the true mean does. The rows marked INF have a standard deviation that is itself
     * beyond PHP_FLOAT_MAX.
     *
     * Test data generated with Python (exact rational variance, then its correctly rounded square root):
     *   mean = sum(map(Fraction, data)) / n
     *   v    = sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof)
     *   then sqrt(v) correctly rounded: math.isqrt of v scaled by an even power of two to ~128 bits,
     *   a sticky bit for an inexact root, and one correctly rounded int / int division to a float,
     *   which is INF at or beyond 2 ** 1024 - 2 ** 970
     */
    public static function dataProviderForDeviationOverflow(): array
    {
        return [
            // The second value is 2e308 from the first
            [[-1e308, 1e308], false, 1.0E+308],
            [[-1e308, 1e308], true, 1.4142135623730951E+308],
            [[1e308, -1e308], false, 1.0E+308],
            [[1e308, -1e308], true, 1.4142135623730951E+308],
            // The same overflowing step, followed by a value that shifts the mean
            [[-1e308, 1e308, 1e308], false, 9.428090415820633E+307],
            [[-1e308, 1e308, 1e308], true, 1.1547005383792515E+308],
            // The overflow comes on the last value, after the running mean has settled at 1e308
            [[1e308, 1e308, 1e308, -1e308], false, 8.660254037844386E+307],
            [[1e308, 1e308, 1e308, -1e308], true, 1.0E+308],
            // A small value after the overflowing step
            [[-1e308, 1e308, 5], false, 8.16496580927726E+307],
            [[-1e308, 1e308, 5], true, 1.0E+308],
            [[-1e308, 1e308, 1], false, 8.16496580927726E+307],
            [[-1e308, 1e308, 1], true, 1.0E+308],
            // The second value is 2.5e308 from the first, and the third sits between them
            [[-1.5e308, 1e308, 2.5e307], false, 1.0474837574980445E+308],
            // Values straddling zero at PHP_FLOAT_MAX
            [[-\PHP_FLOAT_MAX, \PHP_FLOAT_MAX], false, \PHP_FLOAT_MAX],
            [[\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX], false, \PHP_FLOAT_MAX],
            [[-\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 0], false, 1.4678102981723262E+308],
            [[-\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, 0], true, \PHP_FLOAT_MAX],
            [[-\PHP_FLOAT_MAX, 0, \PHP_FLOAT_MAX], false, 1.4678102981723262E+308],
            [[-\PHP_FLOAT_MAX, 0, \PHP_FLOAT_MAX], true, \PHP_FLOAT_MAX],
            [[0, -\PHP_FLOAT_MAX, \PHP_FLOAT_MAX], false, 1.4678102981723262E+308],
            [[0, -\PHP_FLOAT_MAX, \PHP_FLOAT_MAX], true, \PHP_FLOAT_MAX],
            [[-\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, \PHP_FLOAT_MAX], false, \PHP_FLOAT_MAX],
            [[\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, \PHP_FLOAT_MAX], false, 1.6948813415381948E+308],
            // The standard deviation itself is beyond PHP_FLOAT_MAX (PHP_FLOAT_MAX * sqrt(2) and the like)
            [[-\PHP_FLOAT_MAX, \PHP_FLOAT_MAX], true, \INF],
            [[\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX], true, \INF],
            [[-\PHP_FLOAT_MAX, \PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, \PHP_FLOAT_MAX], true, \INF],
            [[\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX, \PHP_FLOAT_MAX], true, \INF],
        ];
    }

    /**
     * @test         toStandardDeviation array is finite, to within rounding, when a deviation step overflows
     * @dataProvider dataProviderForDeviationOverflowWithinRounding
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testDeviationOverflowWithinRoundingArray(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toStandardDeviation($data, $sample);

        // Then (not correctly rounded: the result passes through a handful of rounded steps — the
        // per-value updates, Bessel's ratio, the square root and the unscaling — so it may sit a
        // few ulps from the oracle)
        $this->assertEqualsWithDelta($expected, $result, 4 * \PHP_FLOAT_EPSILON * \abs($expected));
    }

    /**
     * @test         toStandardDeviation generator is finite, to within rounding, when a deviation step overflows
     * @dataProvider dataProviderForDeviationOverflowWithinRounding
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testDeviationOverflowWithinRoundingGenerator(array $data, bool $sample, float $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toStandardDeviation($generator, $sample);

        // Then (not correctly rounded: the result passes through a handful of rounded steps — the
        // per-value updates, Bessel's ratio, the square root and the unscaling — so it may sit a
        // few ulps from the oracle)
        $this->assertEqualsWithDelta($expected, $result, 4 * \PHP_FLOAT_EPSILON * \abs($expected));
    }

    /**
     * @test         toStandardDeviation iterator is finite, to within rounding, when a deviation step overflows
     * @dataProvider dataProviderForDeviationOverflowWithinRounding
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testDeviationOverflowWithinRoundingIterator(array $data, bool $sample, float $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toStandardDeviation($iterator, $sample);

        // Then (not correctly rounded: the result passes through a handful of rounded steps — the
        // per-value updates, Bessel's ratio, the square root and the unscaling — so it may sit a
        // few ulps from the oracle)
        $this->assertEqualsWithDelta($expected, $result, 4 * \PHP_FLOAT_EPSILON * \abs($expected));
    }

    /**
     * @test         toStandardDeviation traversable is finite, to within rounding, when a deviation step overflows
     * @dataProvider dataProviderForDeviationOverflowWithinRounding
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testDeviationOverflowWithinRoundingTraversable(array $data, bool $sample, float $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toStandardDeviation($traversable, $sample);

        // Then (not correctly rounded: the result passes through a handful of rounded steps — the
        // per-value updates, Bessel's ratio, the square root and the unscaling — so it may sit a
        // few ulps from the oracle)
        $this->assertEqualsWithDelta($expected, $result, 4 * \PHP_FLOAT_EPSILON * \abs($expected));
    }

    /**
     * Test data generated with Python (exact rational variance, then its correctly rounded square root):
     *   mean = sum(map(Fraction, data)) / n
     *   v    = sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof)
     *   then sqrt(v) correctly rounded: math.isqrt of v scaled by an even power of two to ~128 bits,
     *   a sticky bit for an inexact root, and one correctly rounded int / int division to a float
     */
    public static function dataProviderForDeviationOverflowWithinRounding(): array
    {
        return [
            // The sample row of [-1.5e308, 1e308, 2.5e307]; IterTools gives 1.2829003598617208E+308, 1 ulp low
            [[-1.5e308, 1e308, 2.5e307], true, 1.282900359861721E+308],
        ];
    }

    /**
     * @test         toStandardDeviation array of subnormal values keeps their low bit when no step overflows
     * @dataProvider dataProviderForSubnormalWithoutOverflow
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testSubnormalWithoutOverflowArray(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toStandardDeviation($data, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toStandardDeviation generator of subnormal values keeps their low bit when no step overflows
     * @dataProvider dataProviderForSubnormalWithoutOverflow
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testSubnormalWithoutOverflowGenerator(array $data, bool $sample, float $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toStandardDeviation($generator, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toStandardDeviation iterator of subnormal values keeps their low bit when no step overflows
     * @dataProvider dataProviderForSubnormalWithoutOverflow
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testSubnormalWithoutOverflowIterator(array $data, bool $sample, float $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toStandardDeviation($iterator, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toStandardDeviation traversable of subnormal values keeps their low bit when no step overflows
     * @dataProvider dataProviderForSubnormalWithoutOverflow
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testSubnormalWithoutOverflowTraversable(array $data, bool $sample, float $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toStandardDeviation($traversable, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * No deviation step overflows here, so the halved-operand branch for an overflowing step must not
     * run: halving an odd multiple of the smallest subnormal rounds its low bit away, which would turn
     * 5e-324 into 0.0 and 1.5e-323 into 2e-323.
     *
     * Test data generated with Python (exact rational variance, then its correctly rounded square root):
     *   mean = sum(map(Fraction, data)) / n
     *   v    = sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof)
     *   then sqrt(v) correctly rounded: math.isqrt of v scaled by an even power of two to ~128 bits,
     *   a sticky bit for an inexact root, and one correctly rounded int / int division to a float
     */
    public static function dataProviderForSubnormalWithoutOverflow(): array
    {
        return [
            [[-5e-324, 5e-324], false, 5.0E-324],
            [[-5e-324, 5e-324], true, 5.0E-324],
            [[5e-324, -5e-324], false, 5.0E-324],
            [[5e-324, -5e-324], true, 5.0E-324],
            [[1.5e-323, -1.5e-323], false, 1.5E-323],
            [[1.5e-323, -1.5e-323], true, 2.0E-323],
            [[-5e-324, 0, 5e-324], false, 5.0E-324],
        ];
    }

    /**
     * @test         toStandardDeviation array of subnormal values keeps its precision when a step underflows
     * @dataProvider dataProviderForStepUnderflow
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testStepUnderflowArray(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toStandardDeviation($data, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toStandardDeviation generator of subnormal values keeps its precision when a step underflows
     * @dataProvider dataProviderForStepUnderflow
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testStepUnderflowGenerator(array $data, bool $sample, float $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toStandardDeviation($generator, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toStandardDeviation iterator of subnormal values keeps its precision when a step underflows
     * @dataProvider dataProviderForStepUnderflow
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testStepUnderflowIterator(array $data, bool $sample, float $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toStandardDeviation($iterator, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toStandardDeviation traversable of subnormal values keeps its precision when a step underflows
     * @dataProvider dataProviderForStepUnderflow
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testStepUnderflowTraversable(array $data, bool $sample, float $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toStandardDeviation($traversable, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * A Welford step (the gap to the running mean, divided by the count) falls below PHP_FLOAT_MIN and
     * rounds to the coarse subnormal grid, which for [0, 5e-324] turns the step 2.5e-324 into 0.0.
     *
     * Test data generated with Python (exact rational variance, then its correctly rounded square root):
     *   mean = sum(map(Fraction, data)) / n
     *   v    = sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof)
     *   then sqrt(v) correctly rounded: math.isqrt of v scaled by an even power of two to ~128 bits,
     *   a sticky bit for an inexact root, and one correctly rounded int / int division to a float
     */
    public static function dataProviderForStepUnderflow(): array
    {
        return [
            // Two values one subnormal step apart
            [[0, 5e-324], false, 0.0],
            [[0, 5e-324], true, 5.0E-324],
            [[5e-324, 0], false, 0.0],
            [[5e-324, 0], true, 5.0E-324],
            [[0, 0, 5e-324], false, 0.0],
            [[0, 0, 5e-324], true, 5.0E-324],
            [[-5e-324, 0, 5e-324], false, 5.0E-324],
            [[-5e-324, 0, 5e-324], true, 5.0E-324],
            [[5e-324, 0, -5e-324], false, 5.0E-324],
            [[5e-324, 0, -5e-324], true, 5.0E-324],
            // Wider subnormal spreads
            [[1.5e-323, -5e-324, 0, 3.5e-323], false, 1.5E-323],
            [[1.5e-323, -5e-324, 0, 3.5e-323], true, 2.0E-323],
            [[1e-320, -3e-321, 2e-321, 7e-322], false, 4.743E-321],
            [[1e-320, -3e-321, 2e-321, 7e-322], true, 5.474E-321],
            [[1e-310, 0, 5e-324], false, 4.7140452079104E-311],
            [[1e-310, 0, 5e-324], true, 5.773502691896E-311],
            // Subnormal values one step apart
            [[1e-310, 1e-310 + 5e-324], false, 0.0],
            [[1e-310, 1e-310 + 5e-324], true, 5.0E-324],
            // Normal values one subnormal step apart, at the subnormal/normal boundary
            [[\PHP_FLOAT_MIN, \PHP_FLOAT_MIN + 5e-324], false, 0.0],
            [[\PHP_FLOAT_MIN, \PHP_FLOAT_MIN + 5e-324], true, 5.0E-324],
            [[\PHP_FLOAT_MIN, \PHP_FLOAT_MIN + 5e-324, \PHP_FLOAT_MIN], false, 0.0],
            [[\PHP_FLOAT_MIN, \PHP_FLOAT_MIN + 5e-324, \PHP_FLOAT_MIN], true, 5.0E-324],
            [[\PHP_FLOAT_MIN - 5e-324, \PHP_FLOAT_MIN, \PHP_FLOAT_MIN + 5e-324], false, 5.0E-324],
            [[\PHP_FLOAT_MIN - 5e-324, \PHP_FLOAT_MIN, \PHP_FLOAT_MIN + 5e-324], true, 5.0E-324],
            // A subnormal step followed by a value that dominates the spread
            [[0, 5e-324, 1.0], false, 0.4714045207910317],
            [[0, 5e-324, 1.0], true, 0.5773502691896257],
            // A subnormal step followed by values too large to rescale
            [[0, 5e-324, 1e300, 1e300], false, 5.0E+299],
            [[0, 5e-324, -\PHP_FLOAT_MAX, \PHP_FLOAT_MAX], false, 1.2711610061536462E+308],
            [[0, 5e-324, -\PHP_FLOAT_MAX, \PHP_FLOAT_MAX], true, 1.4678102981723262E+308],
            // An underflowing step after a wide spread, too large to rescale and unaffected by the rounding
            [[-1e300, 1e300, 0, 5e-324], false, 7.071067811865476E+299],
            [[-1e300, 1e300, 0, 5e-324], true, 8.164965809277261E+299],
            [[-1e308, 1e308, 0, 5e-324], false, 7.071067811865476E+307],
            [[-1e308, 1e308, 0, 5e-324], true, 8.16496580927726E+307],
        ];
    }

    /**
     * @test toStandardDeviation is invariant to input order
     */
    public function testOrderInvariance(): void
    {
        // Given a large offset with a spread at the ulp scale
        $data      = [1e16, 1e16 + 2, 1e16 + 4];
        $reordered = [1e16, 1e16 + 4, 1e16 + 2];

        // When
        $fromData      = Reduce::toStandardDeviation($data);
        $fromReordered = Reduce::toStandardDeviation($reordered);

        // Then
        $this->assertSame($fromData, $fromReordered);
    }

    /**
     * @test         toStandardDeviation of a large-offset collection matches the exact mathematical value
     * @dataProvider dataProviderForLargeOffsetSmallSpread
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testLargeOffsetSmallSpreadIsExact(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toStandardDeviation($data, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational variance, then its correctly rounded square root):
     *   mean = sum(map(Fraction, data)) / n
     *   v    = sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof)
     *   then sqrt(v) correctly rounded: math.isqrt of v scaled by an even power of two to ~128 bits,
     *   a sticky bit for an inexact root, and one correctly rounded int / int division to a float
     * NumPy's float64 mean loses the spread at 1e18 (it gives 245.10133958562255 and 300.1866086286995).
     */
    public static function dataProviderForLargeOffsetSmallSpread(): array
    {
        return [
            // Values one ulp step apart at 1e16
            [[1e16, 1e16 + 2, 1e16 + 4], false, 1.632993161855452],
            [[1e16, 1e16 + 2, 1e16 + 4], true, 2.0],
            // Values two ulp steps apart at 1e18
            [[1e18, 1e18 + 256, 1e18 + 512], false, 209.02312471749786],
            [[1e18, 1e18 + 256, 1e18 + 512], true, 256.0],
        ];
    }

    /**
     * @test toStandardDeviation over a large lazy iterable does not materialize the input
     */
    public function testLargeIterableMemoryBounded(): void
    {
        // Given a huge lazy source that costs ~16 MB to materialize, with the expected value generated
        // with Python as the correctly rounded square root of the exact variance (see
        // dataProviderForHugeValues)
        $n = 1_000_000;
        $largeGen = (function () use ($n) {
            for ($i = 1; $i <= $n; $i++) {
                yield $i;
            }
        })();
        $expected = 288675.1345946685;

        // When
        \memory_reset_peak_usage();
        $before = \memory_get_usage();
        $result = Reduce::toStandardDeviation($largeGen);
        $peakIncrease = \memory_get_peak_usage() - $before;

        // Then (relative error of about sqrt(n) * epsilon from n per-value roundings, as above)
        $this->assertEqualsWithDelta($expected, $result, \sqrt($n) * \PHP_FLOAT_EPSILON * $expected);
        $this->assertLessThan(4 * 1024 * 1024, $peakIncrease);
    }
}

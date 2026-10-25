<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToVarianceTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test toVariance example usage (population)
     */
    public function testExampleUsage(): void
    {
        // Given
        $numbers = [1, 2, 3, 4, 5];

        // When
        $variance = Reduce::toVariance($numbers);

        // Then (expected value from numpy.var([1, 2, 3, 4, 5], ddof=0))
        $this->assertSame(2.0, $variance);
    }

    /**
     * @test         toVariance array
     * @dataProvider dataProviderForVariance
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testArray(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toVariance($data, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toVariance generator
     * @dataProvider dataProviderForVariance
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testGenerator(array $data, bool $sample, float $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toVariance($generator, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toVariance iterator
     * @dataProvider dataProviderForVariance
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testIterator(array $data, bool $sample, float $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toVariance($iterator, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toVariance traversable
     * @dataProvider dataProviderForVariance
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testTraversable(array $data, bool $sample, float $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toVariance($traversable, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   numpy.var(data, ddof=1 if sample else 0)
     */
    public static function dataProviderForVariance(): array
    {
        return [
            // Population variance
            [[1, 2, 3, 4, 5], false, 2.0],
            [[2, 4, 4, 4, 5, 5, 7, 9], false, 4.0],
            [[10, 20], false, 25.0],
            // Population variance — all equal is 0
            [[7, 7, 7], false, 0.0],
            // Population variance — negatives
            [[-2, -4, -6], false, 2.6666666666666665],
            // Population variance — floats
            [[1.5, 2.5, 3.5], false, 0.6666666666666666],
            // Sample variance (Bessel's correction)
            [[1, 2, 3, 4, 5], true, 2.5],
            [[2, 4, 4, 4, 5, 5, 7, 9], true, 4.571428571428571],
            [[10, 20], true, 50.0],
            [[1, 2], true, 0.5],
            // Sample variance — all equal is 0
            [[7, 7, 7], true, 0.0],
            // Order invariance
            [[5, 1, 3, 2, 4], false, 2.0],
            [[5, 1, 3, 2, 4], true, 2.5],
        ];
    }

    /**
     * @test         toVariance returns null on empty (population)
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptyPopulationReturnsNull(iterable $data): void
    {
        // When
        $result = Reduce::toVariance($data);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test         toVariance returns null on empty (sample)
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptySampleReturnsNull(iterable $data): void
    {
        // When
        $result = Reduce::toVariance($data, true);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test         toVariance returns null for the sample variance of a single non-finite value
     * @dataProvider dataProviderForNonFiniteSingleton
     * @param        array $data
     */
    public function testNonFiniteSingletonSampleVarianceIsNull(array $data): void
    {
        // When
        $result = Reduce::toVariance($data, true);

        // Then the undefined N - 1 = 0 divisor wins over NAN propagation: with a single observation
        // there is no sample variance to compute at all, whatever that observation happens to be.
        $this->assertNull($result);
    }

    /**
     * @test         toVariance of a single non-finite value is NAN for the population variance
     * @dataProvider dataProviderForNonFiniteSingleton
     * @param        array $data
     */
    public function testNonFiniteSingletonPopulationVarianceIsNan(array $data): void
    {
        // When
        $result = Reduce::toVariance($data);

        // Then (the population variance of one value is defined, so the value's NAN-ness carries)
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
     * @test toVariance of a single value is 0.0 for population
     */
    public function testSingleValuePopulationIsZero(): void
    {
        // When
        $result = Reduce::toVariance([5]);

        // Then
        $this->assertSame(0.0, $result);
    }

    /**
     * @test toVariance of a single value is null for sample (undefined, N-1 = 0)
     */
    public function testSingleValueSampleIsNull(): void
    {
        // When
        $result = Reduce::toVariance([5], true);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test toVariance of a very large collection (population)
     */
    public function testVeryLargeNPopulation(): void
    {
        // Given the uniform sequence 1..10000, with the expected value generated with Python:
        //   float(sum((Fraction(x) - mean) ** 2 for x in data) / n), mean = Fraction(sum(data), n)
        $n        = 10000;
        $data     = \range(1, $n);
        $expected = 8333333.25;

        // When
        $result = Reduce::toVariance($data);

        // Then (the online update rounds once per value, and n independent roundings accumulate like
        // a random walk, so the relative error grows as about sqrt(n) * epsilon rather than staying
        // within an ulp; the worst-case bound n * epsilon is far looser)
        $this->assertEqualsWithDelta($expected, $result, \sqrt($n) * \PHP_FLOAT_EPSILON * $expected);
    }

    /**
     * @test         toVariance does not overflow for values whose sum exceeds PHP_FLOAT_MAX
     * @dataProvider dataProviderForHugeValues
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testHugeValuesDoNotOverflow(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toVariance($data, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational variance, correctly rounded to a float):
     *   mean = sum(map(Fraction, data)) / n
     *   float(sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof))
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
     * @test         toVariance reports a genuinely unrepresentable variance as INF, never as a negative number
     * @dataProvider dataProviderForUnrepresentableVariance
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testUnrepresentableVarianceIsPositiveInfinity(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toVariance($data, $sample);

        // Then (documented policy is INF for an unrepresentable variance, not NAN)
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational variance, correctly rounded to a float, which is
     * INF at or beyond 2 ** 1024 - 2 ** 970):
     *   mean = sum(map(Fraction, data)) / n
     *   float(sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof))
     */
    public static function dataProviderForUnrepresentableVariance(): array
    {
        return [
            // The true variance (~1e616) exceeds PHP_FLOAT_MAX
            [[-1e308, 1e308], false, \INF],
            [[-1e308, 1e308], true, \INF],
            // The mean also overflows during accumulation
            [[-1e308, 1e308, 1e308], false, \INF],
            [[-1e308, 1e308, 1e308], true, \INF],
            // A deviation step overflows, so the variance is at least about PHP_FLOAT_MAX ** 2 / n
            [[1e308, 1e308, 1e308, -1e308], false, \INF],
            [[1e308, 1e308, 1e308, -1e308], true, \INF],
            [[-\PHP_FLOAT_MAX, \PHP_FLOAT_MAX], false, \INF],
            [[-\PHP_FLOAT_MAX, \PHP_FLOAT_MAX], true, \INF],
            [[-\PHP_FLOAT_MAX, 0, \PHP_FLOAT_MAX], false, \INF],
            [[-\PHP_FLOAT_MAX, 0, \PHP_FLOAT_MAX], true, \INF],
        ];
    }

    /**
     * @test         toVariance of subnormal values is unscaled correctly when a step underflows
     * @dataProvider dataProviderForStepUnderflow
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testStepUnderflow(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toVariance($data, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * A Welford step (the gap to the running mean, divided by the count) falls below PHP_FLOAT_MIN. The
     * variance of subnormal values is far below the smallest subnormal and so is 0.0; a larger value
     * after the underflowing step gives a representable variance.
     *
     * Test data generated with Python (exact rational variance, correctly rounded to a float):
     *   mean = sum(map(Fraction, data)) / n
     *   float(sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof))
     */
    public static function dataProviderForStepUnderflow(): array
    {
        return [
            [[0, 5e-324], false, 0.0],
            [[0, 5e-324], true, 0.0],
            [[-5e-324, 0, 5e-324], false, 0.0],
            [[-5e-324, 0, 5e-324], true, 0.0],
            [[0, 5e-324, 1.0], false, 0.2222222222222222],
            [[0, 5e-324, 1.0], true, 0.3333333333333333],
            [[0, 5e-324, 1e-150], false, 2.2222222222222223E-301],
            [[0, 5e-324, 1e-150], true, 3.3333333333333334E-301],
        ];
    }

    /**
     * @test toVariance over a large lazy iterable does not materialize the input
     */
    public function testLargeIterableMemoryBounded(): void
    {
        // Given a huge lazy source that costs ~16 MB to materialize, with the expected value generated
        // with Python (NumPy's float64 result is an ulp low here):
        //   float(sum((Fraction(x) - mean) ** 2 for x in data) / n), mean = Fraction(sum(data), n)
        $n = 1_000_000;
        $largeGen = (function () use ($n) {
            for ($i = 1; $i <= $n; $i++) {
                yield $i;
            }
        })();
        $expected = 83333333333.25;

        // When
        \memory_reset_peak_usage();
        $before = \memory_get_usage();
        $result = Reduce::toVariance($largeGen);
        $peakIncrease = \memory_get_peak_usage() - $before;

        // Then (relative error of about sqrt(n) * epsilon from n per-value roundings, as above)
        $this->assertEqualsWithDelta($expected, $result, \sqrt($n) * \PHP_FLOAT_EPSILON * $expected);
        $this->assertLessThan(4 * 1024 * 1024, $peakIncrease);
    }

    /**
     * @test         toVariance is finite when the variance is representable but the squared deviations are not
     * @dataProvider dataProviderForRepresentableVarianceOfHugeValues
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testRepresentableVarianceOfHugeValuesIsFinite(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toVariance($data, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational variance, correctly rounded to a float):
     *   mean = sum(map(Fraction, data)) / n
     *   float(sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof))
     */
    public static function dataProviderForRepresentableVarianceOfHugeValues(): array
    {
        return [
            // Sum of squared deviations is 2e308 (unrepresentable), but the variance itself is not.
            [[-1e154, 1e154], false, 1.0E+308],
            [[-1e154, 0, 1e154], false, 6.666666666666667E+307],
            [[-1e154, 0, 1e154], true, 1.0E+308],
            [[0, 1e154], false, 2.5E+307],
        ];
    }

    /**
     * @test         toVariance is finite when an intermediate prefix variance is not representable
     * @dataProvider dataProviderForPrefixOverflow
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testPrefixOverflowStillYieldsRepresentableResult(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toVariance($data, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational variance, correctly rounded to a float):
     *   mean = sum(map(Fraction, data)) / n
     *   float(sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof))
     */
    public static function dataProviderForPrefixOverflow(): array
    {
        return [
            // The variance of the leading pair is ~2.25e308 — beyond PHP_FLOAT_MAX — while the
            // variance of the whole collection is representable. An accumulator that stores the
            // variance directly cannot come back down from INF.
            [[-1.5e154, 1.5e154, 0, 0], false, 1.1250000000000002E+308],
            [[-1.5e154, 1.5e154, 0, 0], true, 1.5000000000000002E+308],
        ];
    }

    /**
     * @test         toVariance is finite, to within rounding, when an intermediate prefix variance is not representable
     * @dataProvider dataProviderForPrefixOverflowWithinRounding
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testPrefixOverflowStillYieldsRepresentableResultWithinRounding(
        array $data,
        bool $sample,
        float $expected
    ): void {
        // When
        $result = Reduce::toVariance($data, $sample);

        // Then (the result is not correctly rounded: it passes through a handful of rounded steps — the
        // per-value updates and the final unscaling — so it may sit a few ulps from the oracle)
        $this->assertEqualsWithDelta($expected, $result, 4 * \PHP_FLOAT_EPSILON * \abs($expected));
    }

    /**
     * Test data generated with Python (exact rational variance, correctly rounded to a float):
     *   mean = sum(map(Fraction, data)) / n
     *   float(sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof))
     */
    public static function dataProviderForPrefixOverflowWithinRounding(): array
    {
        return [
            // The variance of the leading pair is ~1.96e308 — beyond PHP_FLOAT_MAX — while the
            // variance of the whole collection is representable.
            [[-1.4e154, 1.4e154, 0], false, 1.3066666666666667E+308],
        ];
    }

    /**
     * @test         toVariance is order-stable to within floating-point rounding
     * @dataProvider dataProviderForApproximateOrderStability
     * @param        array $data
     * @param        array $reordered
     */
    public function testOrderStabilityWithinRounding(array $data, array $reordered): void
    {
        // When
        $fromData      = Reduce::toVariance($data);
        $fromReordered = Reduce::toVariance($reordered);

        // Then (floating-point accumulation is not bit-reproducible across orderings, but each ordering
        // is within a few ulps of the exact variance, so the two are too)
        $this->assertEqualsWithDelta($fromData, $fromReordered, 4 * \PHP_FLOAT_EPSILON * \abs($fromData));
    }

    public static function dataProviderForApproximateOrderStability(): array
    {
        return [
            [[401.57, 989.165, -640.942], [989.165, 401.57, -640.942]],
            [[401.57, 989.165, -640.942], [-640.942, 989.165, 401.57]],
            [[2, 4, 4, 4, 5, 5, 7, 9], [4, 9, 5, 2, 7, 4, 5, 4]],
            [[0.1, 0.2, 0.3, 0.4, 0.5], [0.5, 0.3, 0.1, 0.4, 0.2]],
        ];
    }

    /**
     * @test         toVariance resolves the large-offset ordering pathology exactly
     * @dataProvider dataProviderForOrderInvariance
     * @param        array $data
     * @param        array $reordered
     */
    public function testLargeOffsetOrderingIsExact(array $data, array $reordered): void
    {
        // When
        $fromData      = Reduce::toVariance($data);
        $fromReordered = Reduce::toVariance($reordered);

        // Then
        $this->assertSame($fromData, $fromReordered);
    }

    public static function dataProviderForOrderInvariance(): array
    {
        return [
            // Large offset with a spread at the ulp scale: the running mean cannot be represented
            // exactly, so an uncompensated online algorithm gives a different answer per ordering.
            [[1e16, 1e16 + 2, 1e16 + 4], [1e16, 1e16 + 4, 1e16 + 2]],
            [[1e16, 1e16 + 2, 1e16 + 4], [1e16 + 2, 1e16, 1e16 + 4]],
            [[1e16, 1e16 + 2, 1e16 + 4], [1e16 + 2, 1e16 + 4, 1e16]],
            [[1e16, 1e16 + 2, 1e16 + 4], [1e16 + 4, 1e16, 1e16 + 2]],
            [[1e16, 1e16 + 2, 1e16 + 4], [1e16 + 4, 1e16 + 2, 1e16]],
            [[1e18, 1e18 + 256, 1e18 + 512], [1e18 + 512, 1e18, 1e18 + 256]],
            [[2, 4, 4, 4, 5, 5, 7, 9], [9, 7, 5, 5, 4, 4, 4, 2]],
        ];
    }

    /**
     * @test         toVariance of a large-offset collection matches the exact mathematical variance
     * @dataProvider dataProviderForLargeOffsetSmallSpread
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testLargeOffsetSmallSpreadIsExact(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toVariance($data, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational variance, correctly rounded to a float):
     *   mean = sum(map(Fraction, data)) / n
     *   float(sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof))
     * NumPy's float64 mean loses the spread at 1e18 (it gives 60074.666666666664 and 90112.0).
     */
    public static function dataProviderForLargeOffsetSmallSpread(): array
    {
        return [
            // Values one ulp step apart at 1e16
            [[1e16, 1e16 + 2, 1e16 + 4], false, 2.6666666666666665],
            [[1e16, 1e16 + 2, 1e16 + 4], true, 4.0],
            // Values two ulp steps apart at 1e18
            [[1e18, 1e18 + 256, 1e18 + 512], false, 43690.666666666664],
            [[1e18, 1e18 + 256, 1e18 + 512], true, 65536.0],
        ];
    }

    /**
     * @test         toVariance of ints beyond ±2^53 keeps the differences their conversion to float would round away
     * @dataProvider dataProviderForLargeInts
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testLargeIntsAreExact(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toVariance($data, $sample);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python (exact rational variance, correctly rounded to a float):
     *   mean = sum(map(Fraction, data)) / n
     *   float(sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof))
     */
    public static function dataProviderForLargeInts(): array
    {
        return [
            [[2 ** 53, 2 ** 53 + 1], false, 0.25],
            [[2 ** 53, 2 ** 53 + 1], true, 0.5],
            [[2 ** 62, 2 ** 62 + 1, 2 ** 62 + 2], false, 0.6666666666666666],
            [[2 ** 62, 2 ** 62 + 1, 2 ** 62 + 2], true, 1.0],
            [[2 ** 62 + 3000, 2 ** 62], false, 2250000.0],
            [[2 ** 62 + 3000, 2 ** 62], true, 4500000.0],
            [[\PHP_INT_MIN, \PHP_INT_MIN + 1], false, 0.25],
            [[\PHP_INT_MIN, \PHP_INT_MIN + 1], true, 0.5],
            // Low 11 bits near 2047, which leave the compensation term little room beside them
            [[2 ** 53 + 2044, 2 ** 53 + 2044, 2 ** 53 + 2046, 2 ** 53 + 2046], false, 1.0],
            [[2 ** 53 + 2044, 2 ** 53 + 2044, 2 ** 53 + 2046, 2 ** 53 + 2046], true, 1.3333333333333333],
            [[-(2 ** 53) - 2044, -(2 ** 53) - 2044, -(2 ** 53) - 2046, -(2 ** 53) - 2046], false, 1.0],
            [[-(2 ** 53) - 2044, -(2 ** 53) - 2044, -(2 ** 53) - 2046, -(2 ** 53) - 2046], true, 1.3333333333333333],
            [[2 ** 62 + 2047, 2 ** 62 + 2046, 2 ** 62 + 2047, 2 ** 62 + 2046], false, 0.25],
            [[2 ** 62 + 2047, 2 ** 62 + 2046, 2 ** 62 + 2047, 2 ** 62 + 2046], true, 0.3333333333333333],
            // Mixed with floats
            [[2.0 ** 53, 2 ** 53 + 1], false, 0.25],
            [[2.0 ** 53, 2 ** 53 + 1], true, 0.5],
            [[2 ** 53 + 1, 9007199254740994.0, 2 ** 53 + 3], false, 0.6666666666666666],
            [[2 ** 53 + 1, 9007199254740994.0, 2 ** 53 + 3], true, 1.0],
        ];
    }

    /**
     * @test         toVariance of ints beyond ±2^53 keeps their differences, to within rounding
     * @dataProvider dataProviderForLargeIntsWithinRounding
     * @param        array $data
     * @param        bool  $sample
     * @param        float $expected
     */
    public function testLargeIntsWithinRounding(array $data, bool $sample, float $expected): void
    {
        // When
        $result = Reduce::toVariance($data, $sample);

        // Then (the result is not correctly rounded: the per-value updates round as they do for any spread,
        // and toVariance([1, 2, 4]) is likewise 1.5555555555555558, an ulp from the oracle)
        $this->assertEqualsWithDelta($expected, $result, 4 * \PHP_FLOAT_EPSILON * \abs($expected));
    }

    /**
     * Test data generated with Python (exact rational variance, correctly rounded to a float):
     *   mean = sum(map(Fraction, data)) / n
     *   float(sum((Fraction(x) - mean) ** 2 for x in data) / (n - ddof))
     */
    public static function dataProviderForLargeIntsWithinRounding(): array
    {
        return [
            [[2 ** 53 + 1, 2 ** 53 + 2, 2 ** 53 + 4], false, 1.5555555555555556],
            [[2 ** 53 + 1, 2 ** 53 + 2, 2 ** 53 + 4], true, 2.3333333333333335],
            [[-(2 ** 53) - 1, -(2 ** 53) - 2, -(2 ** 53) - 4], false, 1.5555555555555556],
            [[-(2 ** 53) - 1, -(2 ** 53) - 2, -(2 ** 53) - 4], true, 2.3333333333333335],
            [[\PHP_INT_MAX, \PHP_INT_MAX - 1, \PHP_INT_MAX - 3], false, 1.5555555555555556],
            [[\PHP_INT_MAX, \PHP_INT_MAX - 1, \PHP_INT_MAX - 3], true, 2.3333333333333335],
            [[2 ** 53 + 2047, 2 ** 53 + 2045, 2 ** 53 + 2047, 2 ** 53 + 2045, 2 ** 53 + 2047], false, 0.96],
            [[2 ** 53 + 2047, 2 ** 53 + 2045, 2 ** 53 + 2047, 2 ** 53 + 2045, 2 ** 53 + 2047], true, 1.2],
        ];
    }

    /**
     * @test toVariance propagates NAN when the input contains NAN
     */
    public function testNanInputPropagates(): void
    {
        // When
        $result = Reduce::toVariance([1, 2, \NAN]);

        // Then
        $this->assertNan($result);
    }

    /**
     * @test         toVariance of a collection containing a non-finite value is NAN
     * @dataProvider dataProviderForNonFiniteInput
     * @param        array $data
     */
    public function testNonFiniteInputIsNan(array $data): void
    {
        // When
        $result = Reduce::toVariance($data);

        // Then (deviations from an infinite mean are INF - INF, which is undefined)
        $this->assertNan($result);
    }

    public static function dataProviderForNonFiniteInput(): array
    {
        return [
            [[\INF, \INF]],
            [[-\INF, -\INF]],
            [[1, 2, \INF]],
            [[1, 2, -\INF]],
            // All-equal values leave the spread at zero, so NAN must be tracked from the input
            // itself rather than inferred from the accumulator.
            [[5, 5, \NAN]],
            [[5, 5, \INF]],
            [[\NAN]],
        ];
    }
}

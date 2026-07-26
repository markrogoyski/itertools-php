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

    private const DELTA = 0.0000000001;

    /**
     * @test toVariance example usage (population)
     */
    public function testExampleUsage(): void
    {
        // Given
        $numbers = [1, 2, 3, 4, 5];

        // When
        $variance = Reduce::toVariance($numbers);

        // Then
        $this->assertEqualsWithDelta(2.0, $variance, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
    }

    public static function dataProviderForVariance(): array
    {
        return [
            // Population variance — textbook examples
            [[1, 2, 3, 4, 5], false, 2.0],
            [[2, 4, 4, 4, 5, 5, 7, 9], false, 4.0],
            [[10, 20], false, 25.0],
            // Population variance — all equal is 0
            [[7, 7, 7], false, 0.0],
            // Population variance — negatives
            [[-2, -4, -6], false, 8 / 3],
            // Population variance — floats
            [[1.5, 2.5, 3.5], false, 2 / 3],
            // Sample variance — textbook examples (Bessel's correction)
            [[1, 2, 3, 4, 5], true, 2.5],
            [[2, 4, 4, 4, 5, 5, 7, 9], true, 32 / 7],
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
        // Given the uniform sequence 1..10000, whose population variance is (N^2 - 1) / 12
        $n        = 10000;
        $data     = \range(1, $n);
        $expected = (($n ** 2) - 1) / 12;

        // When
        $result = Reduce::toVariance($data);

        // Then (loosened delta accounts for floating-point accumulation over 10000 terms)
        $this->assertEqualsWithDelta($expected, $result, 0.0001);
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
     * @test toVariance reports a genuinely unrepresentable variance as INF, never as a negative number
     */
    public function testUnrepresentableVarianceIsPositiveInfinity(): void
    {
        // Given values whose true variance (~1e616) exceeds PHP_FLOAT_MAX and cannot be represented
        $data = [-1e308, 1e308];

        // When
        $result = Reduce::toVariance($data);

        // Then
        $this->assertSame(\INF, $result);
    }

    /**
     * @test toVariance over a large lazy iterable does not materialize the input
     */
    public function testLargeIterableMemoryBounded(): void
    {
        // Given a huge lazy source that costs ~16 MB to materialize
        $n = 1_000_000;
        $largeGen = (function () use ($n) {
            for ($i = 1; $i <= $n; $i++) {
                yield $i;
            }
        })();
        $expected = (($n ** 2) - 1) / 12;

        // When
        \memory_reset_peak_usage();
        $before = \memory_get_usage();
        $result = Reduce::toVariance($largeGen);
        $peakIncrease = \memory_get_peak_usage() - $before;

        // Then
        $this->assertEqualsWithDelta($expected, $result, 1.0);
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
        $this->assertEqualsWithDelta($expected, $result, \abs($expected) * 1e-12);
    }

    public static function dataProviderForRepresentableVarianceOfHugeValues(): array
    {
        return [
            // Sum of squared deviations is 2e308 (unrepresentable), but the variance itself is not.
            [[-1e154, 1e154], false, 1e308],
            [[-1e154, 0, 1e154], false, 6.666666666666667e307],
            [[-1e154, 0, 1e154], true, 1e308],
            [[0, 1e154], false, 2.5e307],
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
        $this->assertEqualsWithDelta($expected, $result, \abs($expected) * 1e-12);
    }

    public static function dataProviderForPrefixOverflow(): array
    {
        return [
            // The variance of the leading pair is ~1.96e308 and ~2.25e308 respectively — both beyond
            // PHP_FLOAT_MAX — while the variance of the whole collection is representable. An
            // accumulator that stores the variance directly cannot come back down from INF.
            [[-1.4e154, 1.4e154, 0], false, 1.3066666666666665e308],
            [[-1.5e154, 1.5e154, 0, 0], false, 1.1250000000000002e308],
            [[-1.5e154, 1.5e154, 0, 0], true, 1.5000000000000002e308],
        ];
    }

    /**
     * @test toVariance reports a genuinely unrepresentable variance as INF even when the mean overflows
     */
    public function testUnrepresentableVarianceWithOverflowingMeanIsInfinite(): void
    {
        // Given finite values whose mean overflows during accumulation and whose variance is ~1e616
        $data = [-1e308, 1e308, 1e308];

        // When
        $result = Reduce::toVariance($data);

        // Then (documented policy is INF for an unrepresentable variance, not NAN)
        $this->assertSame(\INF, $result);
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

        // Then (floating-point accumulation is not bit-reproducible across orderings)
        $this->assertEqualsWithDelta($fromData, $fromReordered, \abs($fromData) * 1e-12);
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
     * @test toVariance of a large-offset collection matches the exact mathematical variance
     */
    public function testLargeOffsetSmallSpreadIsExact(): void
    {
        // Given values 1e16 apart from each other by exactly one ulp step (the true variance is 8/3)
        $data = [1e16, 1e16 + 2, 1e16 + 4];

        // When
        $result = Reduce::toVariance($data);

        // Then
        $this->assertEqualsWithDelta(8 / 3, $result, self::DELTA);
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

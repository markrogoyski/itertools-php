<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture;

class StreamReduceStatisticsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::toMedian example usage
     */
    public function testToMedianExampleUsage(): void
    {
        // Given
        $grades = [100, 90, 95, 85, 94];

        // When
        $median = Stream::of($grades)->toMedian();

        // Then (statistics.median([100, 90, 95, 85, 94]))
        $this->assertSame(94, $median);
    }

    /**
     * @test         Stream::toMedian across iterable types
     * @dataProvider dataProviderForIterableTypes
     * @param        iterable $data
     */
    public function testToMedian(iterable $data): void
    {
        // When
        $result = Stream::of($data)->toMedian();

        // Then (statistics.median([1, 2, 2, 3, 4]))
        $this->assertSame(2, $result);
    }

    /**
     * @test Stream::toMedian on empty stream is null
     */
    public function testToMedianEmpty(): void
    {
        // When
        $result = Stream::of([])->toMedian();

        // Then
        $this->assertNull($result);
    }

    /**
     * @test Stream::toMode example usage
     */
    public function testToModeExampleUsage(): void
    {
        // Given
        $votes = ['red', 'blue', 'red', 'green', 'blue', 'red'];

        // When
        $modes = Stream::of($votes)->toMode();

        // Then (statistics.multimode(['red', 'blue', 'red', 'green', 'blue', 'red']))
        $this->assertSame(['red'], $modes);
    }

    /**
     * @test         Stream::toMode across iterable types
     * @dataProvider dataProviderForIterableTypes
     * @param        iterable $data
     */
    public function testToMode(iterable $data): void
    {
        // When
        $result = Stream::of($data)->toMode();

        // Then (statistics.multimode([1, 2, 2, 3, 4]))
        $this->assertSame([2], $result);
    }

    /**
     * @test Stream::toMode on empty stream is empty array
     */
    public function testToModeEmpty(): void
    {
        // When
        $result = Stream::of([])->toMode();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::toVariance example usage (population)
     */
    public function testToVarianceExampleUsage(): void
    {
        // Given
        $numbers = [1, 2, 3, 4, 5];

        // When
        $variance = Stream::of($numbers)->toVariance();

        // Then (exact population variance, rounded once: float(Fraction variance of [1, 2, 3, 4, 5]))
        $this->assertSame(2.0, $variance);
    }

    /**
     * @test         Stream::toVariance across iterable types
     * @dataProvider dataProviderForIterableTypes
     * @param        iterable $data
     */
    public function testToVariance(iterable $data): void
    {
        // When
        $result = Stream::of($data)->toVariance();

        // Then (exact population variance, rounded once: float(Fraction variance of [1, 2, 2, 3, 4]))
        $this->assertSame(1.04, $result);
    }

    /**
     * @test Stream::toVariance sample uses Bessel's correction
     */
    public function testToVarianceSample(): void
    {
        // Given
        $numbers = [1, 2, 3, 4, 5];

        // When
        $variance = Stream::of($numbers)->toVariance(true);

        // Then (exact sample variance, rounded once: float(Fraction sample variance of [1, 2, 3, 4, 5]))
        $this->assertSame(2.5, $variance);
    }

    /**
     * @test Stream::toVariance on empty stream is null
     */
    public function testToVarianceEmpty(): void
    {
        // When
        $result = Stream::of([])->toVariance();

        // Then
        $this->assertNull($result);
    }

    /**
     * @test Stream::toStandardDeviation example usage (population)
     */
    public function testToStandardDeviationExampleUsage(): void
    {
        // Given
        $numbers = [2, 4, 4, 4, 5, 5, 7, 9];

        // When
        $stddev = Stream::of($numbers)->toStandardDeviation();

        // Then (correctly rounded square root of the exact population variance, which is 4)
        $this->assertSame(2.0, $stddev);
    }

    /**
     * @test         Stream::toStandardDeviation across iterable types
     * @dataProvider dataProviderForIterableTypes
     * @param        iterable $data
     */
    public function testToStandardDeviation(iterable $data): void
    {
        // When
        $result = Stream::of($data)->toStandardDeviation();

        // Then (correctly rounded square root of the exact population variance, 26/25)
        $this->assertSame(1.019803902718557, $result);
    }

    /**
     * @test Stream::toStandardDeviation sample uses Bessel's correction
     */
    public function testToStandardDeviationSample(): void
    {
        // Given
        $numbers = [1, 2, 3, 4, 5];

        // When
        $stddev = Stream::of($numbers)->toStandardDeviation(true);

        // Then (correctly rounded square root of the exact sample variance, 5/2)
        $this->assertSame(1.5811388300841898, $stddev);
    }

    /**
     * @test Stream::toStandardDeviation is representable when the variance itself overflows or underflows
     */
    public function testToStandardDeviationOfUnrepresentableVariance(): void
    {
        // When
        $overflowed  = Stream::of([-1e200, 1e200])->toStandardDeviation();
        $underflowed = Stream::of([-1e-200, 1e-200])->toStandardDeviation();

        // Then (the exact population variance of [-x, x] is x², so the correctly rounded square root is x)
        $this->assertSame(1e200, $overflowed);
        $this->assertSame(1e-200, $underflowed);
    }

    /**
     * @test Stream::toStandardDeviation on empty stream is null
     */
    public function testToStandardDeviationEmpty(): void
    {
        // When
        $result = Stream::of([])->toStandardDeviation();

        // Then
        $this->assertNull($result);
    }

    /**
     * @test Stream::toPercentile example usage
     */
    public function testToPercentileExampleUsage(): void
    {
        // Given
        $scores = [10, 20, 30, 40, 50];

        // When
        $p75 = Stream::of($scores)->toPercentile(75);

        // Then (numpy.percentile([10, 20, 30, 40, 50], 75, method='linear'), landing on the element 40)
        $this->assertSame(40, $p75);
    }

    /**
     * @test         Stream::toPercentile across iterable types
     * @dataProvider dataProviderForIterableTypes
     * @param        iterable $data
     */
    public function testToPercentile(iterable $data): void
    {
        // When
        $result = Stream::of($data)->toPercentile(50);

        // Then (numpy.percentile([1, 2, 2, 3, 4], 50, method='linear'), landing on the element 2)
        $this->assertSame(2, $result);
    }

    /**
     * @test Stream::toPercentile out-of-range throws
     */
    public function testToPercentileOutOfRangeThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of([1, 2, 3])->toPercentile(150);
    }

    /**
     * @test Stream::toPercentile NAN throws
     */
    public function testToPercentileNanThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of([1, 2, 3])->toPercentile(\NAN);
    }

    /**
     * @test Stream::toPercentile on empty stream is null
     */
    public function testToPercentileEmpty(): void
    {
        // When
        $result = Stream::of([])->toPercentile(50);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test Stream::toQuantile example usage
     */
    public function testToQuantileExampleUsage(): void
    {
        // Given
        $scores = [10, 20, 30, 40, 50];

        // When
        $q3 = Stream::of($scores)->toQuantile(0.75);

        // Then (numpy.quantile([10, 20, 30, 40, 50], 0.75, method='linear'), landing on the element 40)
        $this->assertSame(40, $q3);
    }

    /**
     * @test         Stream::toQuantile across iterable types
     * @dataProvider dataProviderForIterableTypes
     * @param        iterable $data
     */
    public function testToQuantile(iterable $data): void
    {
        // When
        $result = Stream::of($data)->toQuantile(0.5);

        // Then (numpy.quantile([1, 2, 2, 3, 4], 0.5, method='linear'), landing on the element 2)
        $this->assertSame(2, $result);
    }

    /**
     * @test Stream::toQuantile NAN throws
     */
    public function testToQuantileNanThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of([1, 2, 3])->toQuantile(\NAN);
    }

    /**
     * @test Stream::toQuantile out-of-range throws
     */
    public function testToQuantileOutOfRangeThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of([1, 2, 3])->toQuantile(1.5);
    }

    /**
     * @test Stream::toQuantile on empty stream is null
     */
    public function testToQuantileEmpty(): void
    {
        // When
        $result = Stream::of([])->toQuantile(0.5);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test Stream statistics compose with upstream operations
     */
    public function testComposesWithUpstreamOperations(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        // When (keep evens: [2,4,6,8,10], median is 6)
        $median = Stream::of($data)
            ->filter(fn ($x) => $x % 2 === 0)
            ->toMedian();

        // Then (statistics.median([2, 4, 6, 8, 10]))
        $this->assertSame(6, $median);
    }

    /**
     * The same data, [1, 2, 2, 3, 4], as each iterable type. The tests using it compute different
     * statistics, so each cites its own oracle where it asserts the expected value.
     */
    public static function dataProviderForIterableTypes(): array
    {
        return [
            'array'             => [[1, 2, 2, 3, 4]],
            'generator'         => [Fixture\GeneratorFixture::getGenerator([1, 2, 2, 3, 4])],
            'iterator'          => [new Fixture\ArrayIteratorFixture([1, 2, 2, 3, 4])],
            'iteratorAggregate' => [new Fixture\IteratorAggregateFixture([1, 2, 2, 3, 4])],
        ];
    }

    /**
     * @test         Stream::toMedian of a source holding NAN is NAN
     * @dataProvider dataProviderForIterableTypesHoldingNan
     * @param        iterable $data
     */
    public function testToMedianNan(iterable $data): void
    {
        // When
        $result = Stream::of($data)->toMedian();

        // Then (numpy.median([1, nan, 2]) is nan)
        $this->assertNan($result);
    }

    /**
     * @test         Stream::toPercentile of a source holding NAN is NAN
     * @dataProvider dataProviderForIterableTypesHoldingNan
     * @param        iterable $data
     */
    public function testToPercentileNan(iterable $data): void
    {
        // When
        $result = Stream::of($data)->toPercentile(25);

        // Then (numpy.percentile([1, nan, 2], 25, method='linear') is nan)
        $this->assertNan($result);
    }

    /**
     * @test         Stream::toQuantile of a source holding NAN is NAN
     * @dataProvider dataProviderForIterableTypesHoldingNan
     * @param        iterable $data
     */
    public function testToQuantileNan(iterable $data): void
    {
        // When
        $result = Stream::of($data)->toQuantile(0.25);

        // Then (numpy.quantile([1, nan, 2], 0.25, method='linear') is nan)
        $this->assertNan($result);
    }

    /**
     * The same data, [1, NAN, 2], as each iterable type.
     */
    public static function dataProviderForIterableTypesHoldingNan(): array
    {
        return [
            'array'             => [[1, \NAN, 2]],
            'generator'         => [Fixture\GeneratorFixture::getGenerator([1, \NAN, 2])],
            'iterator'          => [new Fixture\ArrayIteratorFixture([1, \NAN, 2])],
            'iteratorAggregate' => [new Fixture\IteratorAggregateFixture([1, \NAN, 2])],
        ];
    }
}

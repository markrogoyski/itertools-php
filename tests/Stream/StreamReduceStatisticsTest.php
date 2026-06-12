<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture;

class StreamReduceStatisticsTest extends \PHPUnit\Framework\TestCase
{
    private const DELTA = 0.0000000001;

    /**
     * @test Stream::toMedian example usage
     */
    public function testToMedianExampleUsage(): void
    {
        // Given
        $grades = [100, 90, 95, 85, 94];

        // When
        $median = Stream::of($grades)->toMedian();

        // Then
        $this->assertEqualsWithDelta(94, $median, self::DELTA);
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

        // Then (sorted [1,2,2,3,4] → middle is 2)
        $this->assertEqualsWithDelta(2, $result, self::DELTA);
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

        // Then
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

        // Then (2 appears twice in [1,2,2,3,4])
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

        // Then
        $this->assertEqualsWithDelta(2.0, $variance, self::DELTA);
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

        // Then (population variance of [1,2,2,3,4])
        $this->assertEqualsWithDelta(1.04, $result, self::DELTA);
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

        // Then
        $this->assertEqualsWithDelta(2.5, $variance, self::DELTA);
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

        // Then
        $this->assertEqualsWithDelta(2.0, $stddev, self::DELTA);
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

        // Then (population stddev of [1,2,2,3,4])
        $this->assertEqualsWithDelta(\sqrt(1.04), $result, self::DELTA);
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

        // Then
        $this->assertEqualsWithDelta(\sqrt(2.5), $stddev, self::DELTA);
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

        // Then
        $this->assertEqualsWithDelta(40.0, $p75, self::DELTA);
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

        // Then (50th percentile of [1,2,2,3,4] is 2)
        $this->assertEqualsWithDelta(2, $result, self::DELTA);
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

        // Then
        $this->assertEqualsWithDelta(40.0, $q3, self::DELTA);
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

        // Then (0.5 quantile of [1,2,2,3,4] is 2)
        $this->assertEqualsWithDelta(2, $result, self::DELTA);
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

        // Then
        $this->assertEqualsWithDelta(6, $median, self::DELTA);
    }

    public static function dataProviderForIterableTypes(): array
    {
        $data = [1, 2, 2, 3, 4];

        return [
            'array'             => [$data],
            'generator'         => [Fixture\GeneratorFixture::getGenerator($data)],
            'iterator'          => [new Fixture\ArrayIteratorFixture($data)],
            'iteratorAggregate' => [new Fixture\IteratorAggregateFixture($data)],
        ];
    }
}

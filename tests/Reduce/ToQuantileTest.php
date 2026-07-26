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

    private const DELTA = 0.0000000001;

    /**
     * @test toQuantile example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $scores = [10, 20, 30, 40, 50];

        // When
        $q3 = Reduce::toQuantile($scores, 0.75);

        // Then
        $this->assertEqualsWithDelta(40.0, $q3, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
    }

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
            [[1, 2, 3, 4, 5], 0.5, 3.0],
            // Unsorted input (output invariant to order)
            [[4, 2, 5, 1, 3], 0.5, 3.0],
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

        // Then
        $this->assertEqualsWithDelta($percentile, $quantile, self::DELTA);
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

        // Then
        $this->assertSame(-0.5, $result);
    }

    /**
     * @test toQuantile between two identical infinities is that infinity
     */
    public function testInterpolationBetweenIdenticalInfinitiesIsThatInfinity(): void
    {
        // When
        $result = Reduce::toQuantile([\INF, \INF], 0.5);

        // Then
        $this->assertSame(\INF, $result);
    }

    public static function dataProviderForHugeSpan(): array
    {
        return [
            [[-1e308, 1e308], 0.5, 0.0],
            [[-1e308, 1e308], 0.75, 5e307],
            [[-1e308, 1e308], 0.25, -5e307],
            [[-1.5e308, 1.5e308], 0.5, 0.0],
        ];
    }
}

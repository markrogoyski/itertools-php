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

    private const DELTA = 0.0000000001;

    /**
     * @test toPercentile example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $scores = [10, 20, 30, 40, 50];

        // When
        $p75 = Reduce::toPercentile($scores, 75);

        // Then
        $this->assertEqualsWithDelta(40.0, $p75, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
    }

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
            [[1, 2, 3, 4, 5], 25, 2.0],
            [[1, 2, 3, 4, 5], 50, 3.0],
            [[1, 2, 3, 4, 5], 75, 4.0],
            [[1, 2, 3, 4, 5], 100, 5],
            // Fractional percentile requiring interpolation
            [[1, 2, 3, 4, 5], 10, 1.4],
            // Unsorted input (output invariant to order)
            [[4, 2, 5, 1, 3], 50, 3.0],
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

        // Then
        $this->assertEqualsWithDelta(1, $min, self::DELTA);
        $this->assertEqualsWithDelta(9, $max, self::DELTA);
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

        // Then (R-7: median of 1..10000 is the mean of the two middle values)
        $this->assertEqualsWithDelta(1, $p0, self::DELTA);
        $this->assertEqualsWithDelta(5000.5, $p50, self::DELTA);
        $this->assertEqualsWithDelta(10000, $p100, self::DELTA);
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

        // Then
        $this->assertSame(-0.5, $result);
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
     * @test toPercentile interpolating between two identical neighbours returns that value exactly
     */
    public function testInterpolationBetweenIdenticalNeighboursIsExact(): void
    {
        // Given a value whose weighted recombination is not exact in binary floating point
        $data = [0.1, 0.1, 0.1, 0.1];

        // When (a percentile that lands strictly between two ranks)
        $result = Reduce::toPercentile($data, 30);

        // Then
        $this->assertSame(0.1, $result);
    }
}

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

    private const DELTA = 0.0000000001;

    /**
     * @test toStandardDeviation example usage (population)
     */
    public function testExampleUsage(): void
    {
        // Given
        $numbers = [2, 4, 4, 4, 5, 5, 7, 9];

        // When
        $stddev = Reduce::toStandardDeviation($numbers);

        // Then (population variance is 4.0 → stddev 2.0)
        $this->assertEqualsWithDelta(2.0, $stddev, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
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
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
    }

    public static function dataProviderForStandardDeviation(): array
    {
        return [
            // Population stddev = sqrt(population variance)
            [[1, 2, 3, 4, 5], false, \sqrt(2.0)],
            [[2, 4, 4, 4, 5, 5, 7, 9], false, 2.0],
            [[10, 20], false, 5.0],
            [[7, 7, 7], false, 0.0],
            // Sample stddev = sqrt(sample variance)
            [[1, 2, 3, 4, 5], true, \sqrt(2.5)],
            [[2, 4, 4, 4, 5, 5, 7, 9], true, \sqrt(32 / 7)],
            [[10, 20], true, \sqrt(50.0)],
            [[7, 7, 7], true, 0.0],
            // Floats
            [[1.5, 2.5, 3.5], false, \sqrt(2 / 3)],
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
        // Given the uniform sequence 1..10000; population stddev is sqrt((N^2 - 1) / 12)
        $n        = 10000;
        $data     = \range(1, $n);
        $expected = \sqrt((($n ** 2) - 1) / 12);

        // When
        $result = Reduce::toStandardDeviation($data);

        // Then (loosened delta accounts for floating-point accumulation over 10000 terms)
        $this->assertEqualsWithDelta($expected, $result, 0.0001);
    }
}

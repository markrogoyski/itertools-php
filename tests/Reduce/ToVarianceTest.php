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
}

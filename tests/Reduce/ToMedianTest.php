<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToMedianTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    private const DELTA = 0.0000000001;

    /**
     * @test toMedian example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $grades = [100, 90, 95, 85, 94];

        // When
        $median = Reduce::toMedian($grades);

        // Then (sorted: 85, 90, 94, 95, 100 — middle is 94)
        $this->assertEquals(94, $median);
    }

    /**
     * @test         toMedian array
     * @dataProvider dataProviderForMedian
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testArray(array $data, $expected): void
    {
        // When
        $result = Reduce::toMedian($data);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
    }

    /**
     * @test         toMedian generator
     * @dataProvider dataProviderForMedian
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testGenerator(array $data, $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toMedian($generator);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
    }

    /**
     * @test         toMedian iterator
     * @dataProvider dataProviderForMedian
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testIterator(array $data, $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toMedian($iterator);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
    }

    /**
     * @test         toMedian traversable
     * @dataProvider dataProviderForMedian
     * @param        array     $data
     * @param        int|float $expected
     */
    public function testTraversable(array $data, $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toMedian($traversable);

        // Then
        $this->assertEqualsWithDelta($expected, $result, self::DELTA);
    }

    public static function dataProviderForMedian(): array
    {
        return [
            // Single element
            [[42], 42],
            // Two elements
            [[10, 20], 15],
            [[20, 10], 15],
            // Odd length
            [[1, 2, 3], 2],
            // Odd length, unsorted (output invariant to order)
            [[3, 1, 2], 2],
            // Even length
            [[1, 2, 3, 4], 2.5],
            // Even length, unsorted (output invariant to order)
            [[4, 3, 2, 1], 2.5],
            // All-equal values
            [[5, 5, 5], 5],
            [[7, 7, 7, 7], 7],
            // Negative numbers
            [[-5, -1, -3], -3],
            [[-10, -20, -30, -40], -25],
            // Floats
            [[1.5, 2.5, 3.5], 2.5],
            [[0.1, 0.2, 0.3, 0.4], 0.25],
            // Mixed int and float
            [[1, 2.5, 4], 2.5],
            // Large N (1..1000 → median is average of 500 and 501)
            [\range(1, 1000), 500.5],
        ];
    }

    /**
     * @test         toMedian returns null on empty
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptyReturnsNull(iterable $data): void
    {
        // When
        $result = Reduce::toMedian($data);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test toMedian is invariant to input order
     */
    public function testOrderInvariance(): void
    {
        // Given
        $sorted   = [1, 2, 3, 4, 5, 6];
        $unsorted = [4, 1, 6, 2, 5, 3];

        // When
        $fromSorted   = Reduce::toMedian($sorted);
        $fromUnsorted = Reduce::toMedian($unsorted);

        // Then
        $this->assertEqualsWithDelta(3.5, $fromSorted, self::DELTA);
        $this->assertEqualsWithDelta($fromSorted, $fromUnsorted, self::DELTA);
    }
}

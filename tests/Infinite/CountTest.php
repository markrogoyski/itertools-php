<?php

declare(strict_types=1);

namespace IterTools\Tests\Infinite;

use IterTools\Infinite;

class CountTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test count defaults
     */
    public function testCountDefaults(): void
    {
        // Given
        $result = [];
        $expected = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        // When
        foreach (Infinite::count() as $i) {
            $result[] = $i;
            if ($i === 10) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test          count default step
     * @dataProvider  dataProviderForDefaultStep
     * @param         int   $start
     * @param         array $expected
     */
    public function testCountDefaultStep(int $start, array $expected): void
    {
        // Given
        $result = [];
        $count  = 0;

        // When
        foreach (Infinite::count($start) as $i) {
            $result[] = $i;
            $count++;
            if ($count === 10) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForDefaultStep(): array
    {
        return [
            [0, [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]],
            [1, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]],
            [2, [2, 3, 4, 5, 6, 7, 8, 9, 10, 11]],
            [95, [95, 96, 97, 98, 99, 100, 101, 102, 103, 104]],
            [-1, [-1, 0, 1, 2, 3, 4, 5, 6, 7, 8]],
            [-20, [-20, -19, -18, -17, -16, -15, -14, -13, -12, -11]]
        ];
    }

    /**
     * @test          count custom step
     * @dataProvider  dataProviderForCustomStep
     * @param         int   $start
     * @param         int   $step
     * @param         array $expected
     */
    public function testCountCustomStep(int $start, int $step, array $expected): void
    {
        // Given
        $result = [];
        $count  = 0;

        // When
        foreach (Infinite::count($start, $step) as $i) {
            $result[] = $i;
            $count++;
            if ($count === 10) {
                break;
            }
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForCustomStep(): array
    {
        return [
            [0, 2, [0, 2, 4, 6, 8, 10, 12, 14, 16, 18]],
            [1, 3, [1, 4, 7, 10, 13, 16, 19, 22, 25, 28]],
            [2, -1, [2, 1, 0, -1, -2, -3, -4, -5, -6, -7]],
            [80, -10, [80, 70, 60, 50, 40, 30, 20, 10, 0, -10]]
        ];
    }

    /**
     * @test count for a long time
     */
    public function testCountForLongTime(): void
    {
        // Given
        $result = [];
        $expected = 100_000;

        // When
        foreach (Infinite::count() as $i) {
            $result[] = $i;
            if ($i === 100_000) {
                break;
            }
        }

        // Then
        $this->assertCount($expected, $result);
    }
}

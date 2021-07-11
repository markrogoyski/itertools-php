<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Random;

class NumberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         number
     * @dataProvider dataProviderForNumber
     * @param        int $min
     * @param        int $max
     * @param        int $repetitions
     */
    public function testNumber(int $min, int $max, int $repetitions): void
    {
        // Given
        $result = [];

        // When
        foreach (Random::number($min, $max, $repetitions) as $number) {
            $result[] = $number;
        }

        // Then
        $this->assertCount($repetitions, $result);

        // And
        foreach ($result as $number) {
            $this->assertIsInt($number);
            $this->assertTrue($number >= $min);
            $this->assertTrue($number <= $max);
        }
    }

    public function dataProviderForNumber(): array
    {
        return [
            [0, 0, 0],
            [0, 1, 0],
            [0, 10, 0],
            [0, 10, 1],
            [0, 10, 2],
            [0, 10, 10],
            [0, 10, 50],
            [-5, 5, 100],
            [-928739, 9872937492, 9482],
        ];
    }

    /**
     * @test number exception when repetitions is negative
     */
    public function testNumberRepetitionsException(): void
    {
        // Given
        $repetitions = -1;

        // And
        $min = 0;
        $max = 10;

        // Then
        $this->expectException(\RangeException::class);

        // When
        foreach (Random::number($min, $max, $repetitions) as $number) {
            continue;
        }

        // Fail
        $this->fail('Expected \RangeException');
    }

    /**
     * @test number exception when max is less than min
     */
    public function testNumberMaxLessThanMinException(): void
    {
        // Given
        $min = 10;
        $max = 0;

        // And
        $repetitions = -1;

        // Then
        $this->expectException(\RangeException::class);

        // When
        foreach (Random::number($min, $max, $repetitions) as $number) {
            continue;
        }

        // Fail
        $this->fail('Expected \RangeException');
    }
}

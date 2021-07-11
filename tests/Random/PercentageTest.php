<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Random;

class PercentageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         percentage
     * @dataProvider dataProviderForPercentage
     * @param        int $repetitions
     */
    public function testPercentage(int $repetitions): void
    {
        // Given
        $result = [];

        // When
        foreach (Random::percentage($repetitions) as $percentage) {
            $result[] = $percentage;
        }

        // Then
        $this->assertCount($repetitions, $result);

        // And
        foreach ($result as $percentage) {
            $this->assertIsFloat($percentage);
            $this->assertTrue($percentage >= 0);
            $this->assertTrue($percentage <= 1);
        }
    }

    public function dataProviderForPercentage(): array
    {
        return [
            [0],
            [1],
            [2],
            [10],
            [50],
            [9873],
        ];
    }

    /**
     * @test percentage exception when repetitions is negative
     */
    public function testPercentageRepetitionsException(): void
    {
        // Given
        $repetitions = -1;

        // Then
        $this->expectException(\RangeException::class);

        // When
        foreach (Random::percentage($repetitions) as $percentage) {
            continue;
        }

        // Fail
        $this->fail('Expected \RangeException');
    }
}

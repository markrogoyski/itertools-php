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

    public static function dataProviderForPercentage(): array
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

    /**
     * @test percentage with seeded engine produces deterministic results
     */
    public function testPercentageWithSeededEngine(): void
    {
        // Given
        $engine = new \Random\Engine\Mt19937(42);

        // When
        $result = iterator_to_array(Random::percentage(3, $engine));

        // Then
        $this->assertCount(3, $result);
        $this->assertEqualsWithDelta(0.59308596857569, $result[0], 1e-10);
        $this->assertEqualsWithDelta(0.36686957578674, $result[1], 1e-10);
        $this->assertEqualsWithDelta(0.55938199522532, $result[2], 1e-10);
    }

    /**
     * @test percentage with seeded engine is reproducible
     */
    public function testPercentageWithSeededEngineIsReproducible(): void
    {
        // Given
        $result1 = iterator_to_array(Random::percentage(10, new \Random\Engine\Mt19937(77)));
        $result2 = iterator_to_array(Random::percentage(10, new \Random\Engine\Mt19937(77)));

        // Then
        $this->assertEquals($result1, $result2);
    }

    /**
     * @test         percentage iterator_to_array
     * @dataProvider dataProviderForPercentage
     * @param        int $repetitions
     */
    public function testPercentageIteratorToArray(int $repetitions): void
    {
        // Given
        $iterator = Random::percentage($repetitions);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertCount($repetitions, $result);

        // And
        foreach ($result as $percentage) {
            $this->assertIsFloat($percentage);
            $this->assertTrue($percentage >= 0);
            $this->assertTrue($percentage <= 1);
        }
    }
}

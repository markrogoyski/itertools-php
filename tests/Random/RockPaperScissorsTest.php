<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Random;

class RockPaperScissorsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         rockPaperScissors
     * @dataProvider dataProviderForRockPaperScissors
     * @param        int $repetitions
     */
    public function testRockPaperScissors(int $repetitions): void
    {
        // Given
        $result = [];

        // When
        foreach (Random::rockPaperScissors($repetitions) as $rockPaperScissors) {
            $result[] = $rockPaperScissors;
        }

        // Then
        $this->assertCount($repetitions, $result);

        // And
        foreach ($result as $rockPaperScissors) {
            $this->assertIsString($rockPaperScissors);
            $this->assertThat(
                $rockPaperScissors,
                $this->logicalOr(
                    $this->equalTo(Random::RPS_ROCK),
                    $this->equalTo(Random::RPS_PAPER),
                    $this->equalTo(Random::RPS_SCISSORS)
                )
            );
        }
    }

    public static function dataProviderForRockPaperScissors(): array
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
     * @test rockPaperScissors with seeded engine produces deterministic results
     */
    public function testRockPaperScissorsWithSeededEngine(): void
    {
        // Given
        $engine = new \Random\Engine\Mt19937(42);

        // When
        $result = \iterator_to_array(Random::rockPaperScissors(5, $engine));

        // Then
        $this->assertEquals(['rock', 'scissors', 'paper', 'paper', 'rock'], $result);
    }

    /**
     * @test rockPaperScissors with seeded engine is reproducible
     */
    public function testRockPaperScissorsWithSeededEngineIsReproducible(): void
    {
        // Given
        $result1 = \iterator_to_array(Random::rockPaperScissors(10, new \Random\Engine\Mt19937(55)));
        $result2 = \iterator_to_array(Random::rockPaperScissors(10, new \Random\Engine\Mt19937(55)));

        // Then
        $this->assertEquals($result1, $result2);
    }

    /**
     * @test rockPaperScissors exception when repetitions is negative
     */
    public function testRockPaperScissorsRepetitionsException(): void
    {
        // Given
        $repetitions = -1;

        // Then
        $this->expectException(\RangeException::class);

        // When
        foreach (Random::rockPaperScissors($repetitions) as $rockPaperScissors) {
            continue;
        }

        // Fail
        $this->fail('Expected \RangeException');
    }

    /**
     * @test         rockPaperScissors iterator_to_array
     * @dataProvider dataProviderForRockPaperScissors
     * @param        int $repetitions
     */
    public function testRockPaperScissorsIteratorToArray(int $repetitions): void
    {
        // Given
        $iterator = Random::rockPaperScissors($repetitions);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertCount($repetitions, $result);

        // And
        foreach ($result as $rockPaperScissors) {
            $this->assertIsString($rockPaperScissors);
            $this->assertThat(
                $rockPaperScissors,
                $this->logicalOr(
                    $this->equalTo(Random::RPS_ROCK),
                    $this->equalTo(Random::RPS_PAPER),
                    $this->equalTo(Random::RPS_SCISSORS)
                )
            );
        }
    }
}

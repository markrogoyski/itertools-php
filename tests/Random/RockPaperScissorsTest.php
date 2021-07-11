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

    public function dataProviderForRockPaperScissors(): array
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
}

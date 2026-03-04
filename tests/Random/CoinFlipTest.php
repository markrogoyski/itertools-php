<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Random;

class CoinFlipTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         coinFlip
     * @dataProvider dataProviderForCoinFlip
     * @param        int $repetitions
     */
    public function testCoinFlip(int $repetitions): void
    {
        // Given
        $result = [];

        // When
        foreach (Random::coinFlip($repetitions) as $coinFlip) {
            $result[] = $coinFlip;
        }

        // Then
        $this->assertCount($repetitions, $result);

        // And
        foreach ($result as $coinFlip) {
            $this->assertIsInt($coinFlip);
            $this->assertThat(
                $coinFlip,
                $this->logicalOr(
                    $this->equalTo(0),
                    $this->equalTo(1)
                )
            );
        }
    }

    public static function dataProviderForCoinFlip(): array
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
     * @test coinFlip exception when repetitions is negative
     */
    public function testCoinFlipRepetitionsException(): void
    {
        // Given
        $repetitions = -1;

        // Then
        $this->expectException(\RangeException::class);

        // When
        foreach (Random::coinFlip($repetitions) as $coinFlip) {
            continue;
        }

        // Fail
        $this->fail('Expected \RangeException');
    }

    /**
     * @test coinFlip with seeded engine produces deterministic results
     */
    public function testCoinFlipWithSeededEngine(): void
    {
        // Given
        $engine = new \Random\Engine\Mt19937(42);

        // When
        $result = iterator_to_array(Random::coinFlip(5, $engine));

        // Then
        $this->assertEquals([0, 1, 0, 0, 0], $result);
    }

    /**
     * @test coinFlip with seeded engine is reproducible
     */
    public function testCoinFlipWithSeededEngineIsReproducible(): void
    {
        // Given
        $result1 = iterator_to_array(Random::coinFlip(10, new \Random\Engine\Mt19937(123)));
        $result2 = iterator_to_array(Random::coinFlip(10, new \Random\Engine\Mt19937(123)));

        // Then
        $this->assertEquals($result1, $result2);
    }

    /**
     * @test         coinFlip iterator_to_array
     * @dataProvider dataProviderForCoinFlip
     * @param        int $repetitions
     */
    public function testCoinFlipIteratorToArray(int $repetitions): void
    {
        // Given
        $iterator = Random::coinFlip($repetitions);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertCount($repetitions, $result);

        // And
        foreach ($result as $coinFlip) {
            $this->assertIsInt($coinFlip);
            $this->assertThat(
                $coinFlip,
                $this->logicalOr(
                    $this->equalTo(0),
                    $this->equalTo(1)
                )
            );
        }
    }
}

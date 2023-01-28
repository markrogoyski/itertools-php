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

    public function dataProviderForCoinFlip(): array
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

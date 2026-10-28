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
            $this->assertTrue($percentage < 1);
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
        $result = \iterator_to_array(Random::percentage(3, $engine));

        // Then
        // Generated with: $r = new \Random\Randomizer(new \Random\Engine\Mt19937(42)); per value,
        //   ($r->getInt(0, 2 ** 26 - 1) * 2 ** 27 + $r->getInt(0, 2 ** 27 - 1)) / 2 ** 53
        $this->assertSame([0.9705673231091424, 0.8457159529613436, 0.8476120673136699], $result);
    }

    /**
     * @test percentage with seeded engine is reproducible
     */
    public function testPercentageWithSeededEngineIsReproducible(): void
    {
        // Given
        $result1 = \iterator_to_array(Random::percentage(10, new \Random\Engine\Mt19937(77)));
        $result2 = \iterator_to_array(Random::percentage(10, new \Random\Engine\Mt19937(77)));

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
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertCount($repetitions, $result);

        // And
        foreach ($result as $percentage) {
            $this->assertIsFloat($percentage);
            $this->assertTrue($percentage >= 0);
            $this->assertTrue($percentage < 1);
        }
    }

    /**
     * @test percentage never reaches 1: the largest value an engine can produce is the largest float below 1
     *       on the 2^-53 grid
     */
    public function testPercentageUpperBoundIsExclusive(): void
    {
        // Given
        $engine = new class implements \Random\Engine {
            public function generate(): string
            {
                return \str_repeat("\xFF", 8);
            }
        };

        // When
        $result = \iterator_to_array(Random::percentage(3, $engine));

        // Then
        $this->assertSame([1 - 2 ** -53, 1 - 2 ** -53, 1 - 2 ** -53], $result);
    }

    /**
     * @test percentage lower bound is 0, inclusive
     */
    public function testPercentageLowerBoundIsInclusive(): void
    {
        // Given
        $engine = new class implements \Random\Engine {
            public function generate(): string
            {
                return \str_repeat("\x00", 8);
            }
        };

        // When
        $result = \iterator_to_array(Random::percentage(3, $engine));

        // Then
        $this->assertSame([0.0, 0.0, 0.0], $result);
    }
}

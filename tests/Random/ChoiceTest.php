<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Random;

class ChoiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         choice int
     * @dataProvider dataProviderForChoiceInt
     * @param        int[] $items
     * @param        int   $repetitions
     */
    public function testChoiceInt(array $items, int $repetitions): void
    {
        // Given
        $result = [];

        // When
        foreach (Random::choice($items, $repetitions) as $choice) {
            $result[] = $choice;
        }

        // Then
        $this->assertCount($repetitions, $result);

        // And
        foreach ($result as $choice) {
            $this->assertIsInt($choice);
            $this->assertTrue(in_array($choice, $items));
        }
    }

    public function dataProviderForChoiceInt(): array
    {
        return [
            [[-5, -1, 0, 1, 7, 4, 10, 8847], 0],
            [[-5, -1, 0, 1, 7, 4, 10, 8847], 1],
            [[-5, -1, 0, 1, 7, 4, 10, 8847], 2],
            [[-5, -1, 0, 1, 7, 4, 10, 8847], 10],
            [[-5, -1, 0, 1, 7, 4, 10, 8847], 50],
            [[-5, -1, 0, 1, 7, 4, 10, 8847], 9873],
        ];
    }

    /**
     * @test         choice float
     * @dataProvider dataProviderForChoiceFloat
     * @param        int[] $items
     * @param        int   $repetitions
     */
    public function testChoiceFloat(array $items, int $repetitions): void
    {
        // Given
        $result = [];

        // When
        foreach (Random::choice($items, $repetitions) as $choice) {
            $result[] = $choice;
        }

        // Then
        $this->assertCount($repetitions, $result);

        // And
        foreach ($result as $choice) {
            $this->assertIsFloat($choice);
            $this->assertTrue(in_array($choice, $items));
        }
    }

    public function dataProviderForChoiceFloat(): array
    {
        return [
            [[-5.0, -1.2, 0.0, 1.2, 7.65, 4.339, 10.10, 8847.00001, 0.00005], 0],
            [[-5.0, -1.2, 0.0, 1.2, 7.65, 4.339, 10.10, 8847.00001, 0.00005], 1],
            [[-5.0, -1.2, 0.0, 1.2, 7.65, 4.339, 10.10, 8847.00001, 0.00005], 2],
            [[-5.0, -1.2, 0.0, 1.2, 7.65, 4.339, 10.10, 8847.00001, 0.00005], 10],
            [[-5.0, -1.2, 0.0, 1.2, 7.65, 4.339, 10.10, 8847.00001, 0.00005], 50],
            [[-5.0, -1.2, 0.0, 1.2, 7.65, 4.339, 10.10, 8847.00001, 0.00005], 9873],
        ];
    }

    /**
     * @test         choice string
     * @dataProvider dataProviderForChoiceString
     * @param        int[] $items
     * @param        int   $repetitions
     */
    public function testChoiceString(array $items, int $repetitions): void
    {
        // Given
        $result = [];

        // When
        foreach (Random::choice($items, $repetitions) as $choice) {
            $result[] = $choice;
        }

        // Then
        $this->assertCount($repetitions, $result);

        // And
        foreach ($result as $choice) {
            $this->assertIsString($choice);
            $this->assertTrue(in_array($choice, $items));
        }
    }

    public function dataProviderForChoiceString(): array
    {
        return [
            [['php', 'go', 'python', 'java', 'c++', 'lisp', 'ruby', 'perl'], 0],
            [['php', 'go', 'python', 'java', 'c++', 'lisp', 'ruby', 'perl'], 1],
            [['php', 'go', 'python', 'java', 'c++', 'lisp', 'ruby', 'perl'], 2],
            [['php', 'go', 'python', 'java', 'c++', 'lisp', 'ruby', 'perl'], 10],
            [['php', 'go', 'python', 'java', 'c++', 'lisp', 'ruby', 'perl'], 50],
            [['php', 'go', 'python', 'java', 'c++', 'lisp', 'ruby', 'perl'], 9873],
        ];
    }

    /**
     * @test choice mixed data
     */
    public function testChoiceMixed(): void
    {
        // Given
        $items       = [1, 2.2, 'three', [4, 5, 6], [[1, 2, 3], [5, 6, 7]], true, false, null, \INF, \M_PI];
        $repetitions = 200;

        // And
        $result = [];

        // When
        foreach (Random::choice($items, $repetitions) as $choice) {
            $result[] = $choice;
        }

        // Then
        $this->assertCount($repetitions, $result);

        // And
        foreach ($result as $choice) {
            $this->assertTrue(in_array($choice, $items, true));
        }
    }

    /**
     * @test choice exception when repetitions is negative
     */
    public function testChoiceRepetitionsException(): void
    {
        // Given
        $repetitions = -1;

        // And
        $items = [1, 2, 3];

        // Then
        $this->expectException(\RangeException::class);

        // When
        foreach (Random::choice($items, $repetitions) as $choice) {
            continue;
        }

        // Fail
        $this->fail('Expected \RangeException');
    }
}

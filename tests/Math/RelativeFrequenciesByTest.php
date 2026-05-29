<?php

declare(strict_types=1);

namespace IterTools\Tests\Math;

use IterTools\Math;
use IterTools\Tests\Fixture;

class RelativeFrequenciesByTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test relativeFrequenciesBy example usage
     */
    public function testRelativeFrequenciesByExampleUsage(): void
    {
        // Given
        $words = ['apple', 'pear', 'kiwi', 'plum'];

        // And
        $frequencies = [];

        // When (group by length: apple=5, pear=4, kiwi=4, plum=4)
        foreach (Math::relativeFrequenciesBy($words, fn ($word) => \strlen($word)) as $length => $frequency) {
            $frequencies[$length] = $frequency;
        }

        // Then
        $this->assertEquals([5 => 0.25, 4 => 0.75], $frequencies);
    }

    /**
     * @test         relativeFrequenciesBy array
     * @dataProvider dataProviderForArray
     * @param        array    $data
     * @param        callable $keyFunc
     * @param        array    $expectedKeys
     * @param        array    $expectedFrequencies
     */
    public function testArray(array $data, callable $keyFunc, array $expectedKeys, array $expectedFrequencies): void
    {
        // Given
        $keys        = [];
        $frequencies = [];

        // When
        foreach (Math::relativeFrequenciesBy($data, $keyFunc) as $key => $frequency) {
            $keys[]        = $key;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedKeys, $keys);
        $this->assertEqualsWithDelta($expectedFrequencies, $frequencies, 0.0000001);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [[], fn ($x) => $x, [], []],
            [[5], fn ($x) => $x, [5], [1.0]],
            [
                [1, 2, 3, 4],
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd', 'even'],
                [0.5, 0.5],
            ],
            [
                ['apple', 'pear', 'kiwi', 'plum'],
                fn ($x) => \strlen($x),
                [5, 4],
                [0.25, 0.75],
            ],
        ];
    }

    /**
     * @test         relativeFrequenciesBy generator
     * @dataProvider dataProviderForGenerator
     * @param        \Generator $data
     * @param        callable   $keyFunc
     * @param        array      $expectedKeys
     * @param        array      $expectedFrequencies
     */
    public function testGenerator(\Generator $data, callable $keyFunc, array $expectedKeys, array $expectedFrequencies): void
    {
        // Given
        $keys        = [];
        $frequencies = [];

        // When
        foreach (Math::relativeFrequenciesBy($data, $keyFunc) as $key => $frequency) {
            $keys[]        = $key;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedKeys, $keys);
        $this->assertEqualsWithDelta($expectedFrequencies, $frequencies, 0.0000001);
    }

    public static function dataProviderForGenerator(): array
    {
        $gen = fn (array $data) => Fixture\GeneratorFixture::getGenerator($data);

        return [
            [$gen([]), fn ($x) => $x, [], []],
            [$gen([5]), fn ($x) => $x, [5], [1.0]],
            [
                $gen([1, 2, 3, 4]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd', 'even'],
                [0.5, 0.5],
            ],
            [
                $gen(['apple', 'pear', 'kiwi', 'plum']),
                fn ($x) => \strlen($x),
                [5, 4],
                [0.25, 0.75],
            ],
        ];
    }

    /**
     * @test         relativeFrequenciesBy iterator
     * @dataProvider dataProviderForIterator
     * @param        \Iterator $data
     * @param        callable  $keyFunc
     * @param        array     $expectedKeys
     * @param        array     $expectedFrequencies
     */
    public function testIterator(\Iterator $data, callable $keyFunc, array $expectedKeys, array $expectedFrequencies): void
    {
        // Given
        $keys        = [];
        $frequencies = [];

        // When
        foreach (Math::relativeFrequenciesBy($data, $keyFunc) as $key => $frequency) {
            $keys[]        = $key;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedKeys, $keys);
        $this->assertEqualsWithDelta($expectedFrequencies, $frequencies, 0.0000001);
    }

    public static function dataProviderForIterator(): array
    {
        $iter = fn (array $data) => new Fixture\ArrayIteratorFixture($data);

        return [
            [$iter([]), fn ($x) => $x, [], []],
            [$iter([5]), fn ($x) => $x, [5], [1.0]],
            [
                $iter([1, 2, 3, 4]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd', 'even'],
                [0.5, 0.5],
            ],
            [
                $iter(['apple', 'pear', 'kiwi', 'plum']),
                fn ($x) => \strlen($x),
                [5, 4],
                [0.25, 0.75],
            ],
        ];
    }

    /**
     * @test         relativeFrequenciesBy traversable
     * @dataProvider dataProviderForTraversable
     * @param        \Traversable $data
     * @param        callable     $keyFunc
     * @param        array        $expectedKeys
     * @param        array        $expectedFrequencies
     */
    public function testTraversable(\Traversable $data, callable $keyFunc, array $expectedKeys, array $expectedFrequencies): void
    {
        // Given
        $keys        = [];
        $frequencies = [];

        // When
        foreach (Math::relativeFrequenciesBy($data, $keyFunc) as $key => $frequency) {
            $keys[]        = $key;
            $frequencies[] = $frequency;
        }

        // Then
        $this->assertEquals($expectedKeys, $keys);
        $this->assertEqualsWithDelta($expectedFrequencies, $frequencies, 0.0000001);
    }

    public static function dataProviderForTraversable(): array
    {
        $trav = fn (array $data) => new Fixture\IteratorAggregateFixture($data);

        return [
            [$trav([]), fn ($x) => $x, [], []],
            [$trav([5]), fn ($x) => $x, [5], [1.0]],
            [
                $trav([1, 2, 3, 4]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd', 'even'],
                [0.5, 0.5],
            ],
            [
                $trav(['apple', 'pear', 'kiwi', 'plum']),
                fn ($x) => \strlen($x),
                [5, 4],
                [0.25, 0.75],
            ],
        ];
    }

    /**
     * @test relativeFrequenciesBy frequencies sum to 1
     */
    public function testFrequenciesSumToOne(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        // When
        $sum = 0.0;
        foreach (Math::relativeFrequenciesBy($data, fn ($x) => $x % 3) as $frequency) {
            $sum += $frequency;
        }

        // Then
        $this->assertEqualsWithDelta(1.0, $sum, 0.0000001);
    }

    /**
     * @test relativeFrequenciesBy throws TypeError when keyFunc returns a non-int|string
     */
    public function testInvalidKeyTypeThrows(): void
    {
        // Given
        $data = [1, 2, 3];

        // Then
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('double');

        // When
        foreach (Math::relativeFrequenciesBy($data, fn ($x) => 1.5) as $_) {
            // drain
        }
    }
}

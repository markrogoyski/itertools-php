<?php

declare(strict_types=1);

namespace IterTools\Tests\Combinatorics;

use IterTools\Combinatorics;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class PowersetTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test powerset example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = [];
        foreach (Combinatorics::powerset($data) as $subset) {
            $result[] = $subset;
        }

        // Then
        $this->assertSame(
            [
                [],
                [1],
                [2],
                [3],
                [1, 2],
                [1, 3],
                [2, 3],
                [1, 2, 3],
            ],
            $result
        );
    }

    /**
     * @test         powerset (array)
     * @dataProvider dataProviderForPowerset
     * @param        array<mixed>        $data
     * @param        array<array<mixed>> $expected
     */
    public function testPowersetArray(array $data, array $expected): void
    {
        // When
        $result = [];
        foreach (Combinatorics::powerset($data) as $subset) {
            $result[] = $subset;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         powerset (Generator)
     * @dataProvider dataProviderForPowerset
     * @param        array<mixed>        $data
     * @param        array<array<mixed>> $expected
     */
    public function testPowersetGenerator(array $data, array $expected): void
    {
        // Given
        $gen = GeneratorFixture::getGenerator($data);

        // When
        $result = [];
        foreach (Combinatorics::powerset($gen) as $subset) {
            $result[] = $subset;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         powerset (Iterator)
     * @dataProvider dataProviderForPowerset
     * @param        array<mixed>        $data
     * @param        array<array<mixed>> $expected
     */
    public function testPowersetIterator(array $data, array $expected): void
    {
        // Given
        $iter = new ArrayIteratorFixture($data);

        // When
        $result = [];
        foreach (Combinatorics::powerset($iter) as $subset) {
            $result[] = $subset;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         powerset (IteratorAggregate)
     * @dataProvider dataProviderForPowerset
     * @param        array<mixed>        $data
     * @param        array<array<mixed>> $expected
     */
    public function testPowersetIteratorAggregate(array $data, array $expected): void
    {
        // Given
        $agg = new IteratorAggregateFixture($data);

        // When
        $result = [];
        foreach (Combinatorics::powerset($agg) as $subset) {
            $result[] = $subset;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForPowerset(): array
    {
        return [
            // empty input — yields one empty subset
            [
                [],
                [[]],
            ],
            // single element
            [
                [1],
                [[], [1]],
            ],
            // two elements
            [
                [1, 2],
                [[], [1], [2], [1, 2]],
            ],
            // three elements — exact length-then-lexicographic order
            [
                [1, 2, 3],
                [
                    [],
                    [1],
                    [2],
                    [3],
                    [1, 2],
                    [1, 3],
                    [2, 3],
                    [1, 2, 3],
                ],
            ],
            // string values
            [
                ['a', 'b', 'c'],
                [
                    [],
                    ['a'],
                    ['b'],
                    ['c'],
                    ['a', 'b'],
                    ['a', 'c'],
                    ['b', 'c'],
                    ['a', 'b', 'c'],
                ],
            ],
            // duplicates are position-unique (matches combinations semantics)
            [
                [1, 1],
                [
                    [],
                    [1],
                    [1],
                    [1, 1],
                ],
            ],
        ];
    }

    /**
     * @test         powerset of empty iterable yields one empty subset
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterable(iterable $data): void
    {
        // When
        $result = [];
        foreach (Combinatorics::powerset($data) as $subset) {
            $result[] = $subset;
        }

        // Then
        $this->assertSame([[]], $result);
    }

    /**
     * @test powerset count equals 2^n
     */
    public function testCountIsTwoToTheN(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5, 6];

        // When
        $count = 0;
        foreach (Combinatorics::powerset($data) as $subset) {
            $count++;
        }

        // Then
        $this->assertSame(2 ** \count($data), $count);
    }

    /**
     * @test powerset count equals 2^n for n=10
     */
    public function testCountIsTwoToTheNForTen(): void
    {
        // Given
        $data = \range(1, 10);

        // When
        $count = 0;
        foreach (Combinatorics::powerset($data) as $subset) {
            $count++;
        }

        // Then
        $this->assertSame(1024, $count);
    }

    /**
     * @test powerset discards associative input keys — subsets are list arrays
     */
    public function testAssociativeKeysDiscarded(): void
    {
        // Given
        $data = ['x' => 1, 'y' => 2];

        // When
        $result = [];
        foreach (Combinatorics::powerset($data) as $subset) {
            $result[] = $subset;
        }

        // Then
        $this->assertSame(
            [
                [],
                [1],
                [2],
                [1, 2],
            ],
            $result
        );
    }

    /**
     * @test powerset yields list-array subsets (0-indexed)
     */
    public function testOutputSubsetsAreListArrays(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $second = null;
        $i = 0;
        foreach (Combinatorics::powerset($data) as $subset) {
            if ($i === 4) {
                // pick the first 2-element subset
                $second = $subset;
                break;
            }
            $i++;
        }

        // Then
        $this->assertIsArray($second);
        $this->assertSame([0, 1], \array_keys($second));
    }

    /**
     * @test powerset consumes generator input once (no rewind)
     */
    public function testInputGeneratorConsumedOnce(): void
    {
        // Given
        $gen = GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $result = [];
        foreach (Combinatorics::powerset($gen) as $subset) {
            $result[] = $subset;
        }

        // Then
        $this->assertSame(
            [
                [],
                [1],
                [2],
                [3],
                [1, 2],
                [1, 3],
                [2, 3],
                [1, 2, 3],
            ],
            $result
        );
    }
}

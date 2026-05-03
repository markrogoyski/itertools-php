<?php

declare(strict_types=1);

namespace IterTools\Tests\Sort;

use IterTools\Sort;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ShuffleTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test         shuffle multiset preservation (array)
     * @dataProvider dataProviderForShuffle
     * @param        array<mixed> $data
     */
    public function testShuffleArray(array $data): void
    {
        // When
        $result = \iterator_to_array(Sort::shuffle($data), false);

        // Then
        $this->assertCount(\count($data), $result);
        $sortedInput = $data;
        \sort($sortedInput);
        $sortedOutput = $result;
        \sort($sortedOutput);
        $this->assertSame($sortedInput, $sortedOutput);
    }

    /**
     * @test         shuffle multiset preservation (Generator)
     * @dataProvider dataProviderForShuffle
     * @param        array<mixed> $data
     */
    public function testShuffleGenerator(array $data): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = \iterator_to_array(Sort::shuffle($iterable), false);

        // Then
        $this->assertCount(\count($data), $result);
        $sortedInput = $data;
        \sort($sortedInput);
        $sortedOutput = $result;
        \sort($sortedOutput);
        $this->assertSame($sortedInput, $sortedOutput);
    }

    /**
     * @test         shuffle multiset preservation (Iterator)
     * @dataProvider dataProviderForShuffle
     * @param        array<mixed> $data
     */
    public function testShuffleIterator(array $data): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = \iterator_to_array(Sort::shuffle($iterable), false);

        // Then
        $this->assertCount(\count($data), $result);
        $sortedInput = $data;
        \sort($sortedInput);
        $sortedOutput = $result;
        \sort($sortedOutput);
        $this->assertSame($sortedInput, $sortedOutput);
    }

    /**
     * @test         shuffle multiset preservation (IteratorAggregate)
     * @dataProvider dataProviderForShuffle
     * @param        array<mixed> $data
     */
    public function testShuffleIteratorAggregate(array $data): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = \iterator_to_array(Sort::shuffle($iterable), false);

        // Then
        $this->assertCount(\count($data), $result);
        $sortedInput = $data;
        \sort($sortedInput);
        $sortedOutput = $result;
        \sort($sortedOutput);
        $this->assertSame($sortedInput, $sortedOutput);
    }

    public static function dataProviderForShuffle(): array
    {
        return [
            [[1, 2, 3, 4, 5]],
            [[1]],
            [[1, 1, 2, 2, 3, 3]],
            [['a', 'b', 'c', 'd']],
            [\range(1, 50)],
        ];
    }

    /**
     * @test         shuffle empty iterable yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterable(iterable $data): void
    {
        // When
        $result = \iterator_to_array(Sort::shuffle($data), false);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test shuffle output keys are sequential 0-indexed
     */
    public function testKeysSequential(): void
    {
        // Given
        $data = ['x' => 'a', 'y' => 'b', 'z' => 'c'];

        // When
        $keys = [];
        foreach (Sort::shuffle($data) as $key => $value) {
            $keys[] = $key;
        }

        // Then
        $this->assertSame([0, 1, 2], $keys);
    }

    /**
     * @test shuffle determinism with same engine seed
     */
    public function testDeterministicWithSeed(): void
    {
        // Given
        $data = \range(1, 20);
        $engineA = new \Random\Engine\Mt19937(12345);
        $engineB = new \Random\Engine\Mt19937(12345);

        // When
        $a = \iterator_to_array(Sort::shuffle($data, $engineA), false);
        $b = \iterator_to_array(Sort::shuffle($data, $engineB), false);

        // Then
        $this->assertSame($a, $b);
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Random;

use IterTools\Random;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class SampleTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test         sample (array)
     * @dataProvider dataProviderForSample
     * @param        array<mixed> $data
     * @param        int          $size
     */
    public function testSampleArray(array $data, int $size): void
    {
        // When
        $result = \iterator_to_array(Random::sample($data, $size), false);

        // Then
        $this->assertCount($size, $result);
        // every output element appears in the input
        foreach ($result as $item) {
            $this->assertContains($item, $data);
        }
    }

    /**
     * @test         sample (Generator)
     * @dataProvider dataProviderForSample
     * @param        array<mixed> $data
     * @param        int          $size
     */
    public function testSampleGenerator(array $data, int $size): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = \iterator_to_array(Random::sample($iterable, $size), false);

        // Then
        $this->assertCount($size, $result);
        foreach ($result as $item) {
            $this->assertContains($item, $data);
        }
    }

    /**
     * @test         sample (Iterator)
     * @dataProvider dataProviderForSample
     * @param        array<mixed> $data
     * @param        int          $size
     */
    public function testSampleIterator(array $data, int $size): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = \iterator_to_array(Random::sample($iterable, $size), false);

        // Then
        $this->assertCount($size, $result);
        foreach ($result as $item) {
            $this->assertContains($item, $data);
        }
    }

    /**
     * @test         sample (IteratorAggregate)
     * @dataProvider dataProviderForSample
     * @param        array<mixed> $data
     * @param        int          $size
     */
    public function testSampleIteratorAggregate(array $data, int $size): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = \iterator_to_array(Random::sample($iterable, $size), false);

        // Then
        $this->assertCount($size, $result);
        foreach ($result as $item) {
            $this->assertContains($item, $data);
        }
    }

    public static function dataProviderForSample(): array
    {
        return [
            [[1, 2, 3, 4, 5], 0],
            [[1, 2, 3, 4, 5], 1],
            [[1, 2, 3, 4, 5], 3],
            [[1, 2, 3, 4, 5], 5],
            [['a', 'b', 'c', 'd', 'e', 'f'], 4],
            [\range(1, 100), 25],
        ];
    }

    /**
     * @test sample size = count is a permutation (multiset preservation)
     */
    public function testSizeEqualsCountIsPermutation(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5, 6, 7];

        // When
        $result = \iterator_to_array(Random::sample($data, \count($data)), false);

        // Then
        $a = $data;
        \sort($a);
        $b = $result;
        \sort($b);
        $this->assertSame($a, $b);
    }

    /**
     * @test sample size = 0 yields empty
     */
    public function testSizeZero(): void
    {
        // When
        $result = \iterator_to_array(Random::sample([1, 2, 3], 0), false);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test sample throws InvalidArgumentException on negative size
     */
    public function testNegativeSizeThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        \iterator_to_array(Random::sample([1, 2, 3], -1));
    }

    /**
     * @test sample throws LengthException when size exceeds count
     */
    public function testOversizedThrows(): void
    {
        // Then
        $this->expectException(\LengthException::class);

        // When
        \iterator_to_array(Random::sample([1, 2, 3], 4));
    }

    /**
     * @test sample preserves duplicate values when input has duplicates
     */
    public function testDuplicateValuesAllowedWhenSizeFits(): void
    {
        // Given
        $data = [1, 1, 1, 1];

        // When
        $result = \iterator_to_array(Random::sample($data, 3), false);

        // Then
        $this->assertCount(3, $result);
        foreach ($result as $item) {
            $this->assertSame(1, $item);
        }
    }

    /**
     * @test sample without replacement uses each input position at most once
     */
    public function testWithoutReplacement(): void
    {
        // Given a population where each value is unique to a position
        $data = ['a', 'b', 'c', 'd', 'e'];

        // When sampling all 5
        $result = \iterator_to_array(Random::sample($data, 5), false);

        // Then each input value appears exactly once (no replacement)
        $sorted = $result;
        \sort($sorted);
        $expected = $data;
        \sort($expected);
        $this->assertSame($expected, $sorted);
    }

    /**
     * @test sample output keys are sequential 0-indexed
     */
    public function testKeysSequential(): void
    {
        // When
        $keys = [];
        foreach (Random::sample(['x' => 1, 'y' => 2, 'z' => 3], 3) as $key => $value) {
            $keys[] = $key;
        }

        // Then
        $this->assertSame([0, 1, 2], $keys);
    }

    /**
     * @test sample determinism with same engine seed
     */
    public function testDeterministicWithSeed(): void
    {
        // Given
        $data = \range(1, 20);
        $engineA = new \Random\Engine\Mt19937(54321);
        $engineB = new \Random\Engine\Mt19937(54321);

        // When
        $a = \iterator_to_array(Random::sample($data, 7, $engineA), false);
        $b = \iterator_to_array(Random::sample($data, 7, $engineB), false);

        // Then
        $this->assertSame($a, $b);
    }

    /**
     * @test         sample size 0 on empty input yields empty
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyInputSizeZero(iterable $data): void
    {
        // When
        $result = \iterator_to_array(Random::sample($data, 0), false);

        // Then
        $this->assertSame([], $result);
    }
}

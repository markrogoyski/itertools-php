<?php

declare(strict_types=1);

namespace IterTools\Tests\Random;

use IterTools\Random;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ReservoirSampleTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test         reservoirSample behavioral invariants (array)
     * @dataProvider dataProviderForReservoirSample
     * @param        array<mixed> $data
     * @param        int          $size
     */
    public function testInvariantsArray(array $data, int $size): void
    {
        // When
        $result = Random::reservoirSample($data, $size);

        // Then
        $this->assertCount(\min($size, \count($data)), $result);
        foreach ($result as $item) {
            $this->assertContains($item, $data);
        }
    }

    /**
     * @test         reservoirSample behavioral invariants (Generator)
     * @dataProvider dataProviderForReservoirSample
     * @param        array<mixed> $data
     * @param        int          $size
     */
    public function testInvariantsGenerator(array $data, int $size): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Random::reservoirSample($iterable, $size);

        // Then
        $this->assertCount(\min($size, \count($data)), $result);
        foreach ($result as $item) {
            $this->assertContains($item, $data);
        }
    }

    /**
     * @test         reservoirSample behavioral invariants (Iterator)
     * @dataProvider dataProviderForReservoirSample
     * @param        array<mixed> $data
     * @param        int          $size
     */
    public function testInvariantsIterator(array $data, int $size): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Random::reservoirSample($iterable, $size);

        // Then
        $this->assertCount(\min($size, \count($data)), $result);
        foreach ($result as $item) {
            $this->assertContains($item, $data);
        }
    }

    /**
     * @test         reservoirSample behavioral invariants (IteratorAggregate)
     * @dataProvider dataProviderForReservoirSample
     * @param        array<mixed> $data
     * @param        int          $size
     */
    public function testInvariantsIteratorAggregate(array $data, int $size): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Random::reservoirSample($iterable, $size);

        // Then
        $this->assertCount(\min($size, \count($data)), $result);
        foreach ($result as $item) {
            $this->assertContains($item, $data);
        }
    }

    public static function dataProviderForReservoirSample(): array
    {
        return [
            [[1, 2, 3, 4, 5], 0],
            [[1, 2, 3, 4, 5], 1],
            [[1, 2, 3, 4, 5], 3],
            [[1, 2, 3, 4, 5], 5],
            [[1, 2, 3, 4, 5], 10],
            [['a', 'b', 'c', 'd', 'e', 'f'], 4],
            [\range(1, 100), 25],
        ];
    }

    /**
     * @test         reservoirSample on empty input returns empty array
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyInput(iterable $data): void
    {
        // When
        $result = Random::reservoirSample($data, 3);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test reservoirSample with size 0 returns empty array
     */
    public function testSizeZero(): void
    {
        // When
        $result = Random::reservoirSample([1, 2, 3], 0);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test reservoirSample with size greater than count returns the entire input in original order
     */
    public function testSizeGreaterThanCountReturnsAllInOriginalOrder(): void
    {
        // Given
        $data = [10, 20, 30];

        // When (no engine — this regime performs zero random draws)
        $result = Random::reservoirSample($data, 5);

        // Then
        $this->assertSame([10, 20, 30], $result);
    }

    /**
     * @test reservoirSample with size equal to count returns the entire input in original order
     */
    public function testSizeEqualsCountReturnsAllInOriginalOrder(): void
    {
        // Given
        $data = [10, 20, 30];

        // When (no engine — this regime performs zero random draws)
        $result = Random::reservoirSample($data, 3);

        // Then
        $this->assertSame([10, 20, 30], $result);
    }

    /**
     * @test reservoirSample throws InvalidArgumentException on negative size, before consuming the iterable
     */
    public function testNegativeSizeThrowsEagerly(): void
    {
        // Given a generator that records whether its body was ever entered
        $consumed = false;
        $gen = (function () use (&$consumed) {
            $consumed = true;
            yield 1;
        })();

        // When / Then
        try {
            Random::reservoirSample($gen, -1);
            $this->fail('Expected InvalidArgumentException was not thrown');
        } catch (\InvalidArgumentException $e) {
            $this->assertFalse($consumed, 'iterable must not be consumed before size validation');
            $this->assertSame('Sample size cannot be negative: -1', $e->getMessage());
        }
    }

    /**
     * @test reservoirSample with size 0 returns [] without consuming the iterable
     */
    public function testSizeZeroDoesNotConsumeIterable(): void
    {
        // Given a generator that counts how many elements have been pulled
        $consumedCount = 0;
        $gen = (function () use (&$consumedCount) {
            foreach ([1, 2, 3, 4, 5] as $value) {
                $consumedCount++;
                yield $value;
            }
        })();

        // When
        $result = Random::reservoirSample($gen, 0);

        // Then
        $this->assertSame([], $result);
        $this->assertSame(0, $consumedCount, 'size 0 must short-circuit without consuming the iterable');
    }

    /**
     * @test reservoirSample is deterministic for the same engine seed
     */
    public function testDeterministicWithSeed(): void
    {
        // Given
        $data = \range(1, 20);
        $engineA = new \Random\Engine\Mt19937(54321);
        $engineB = new \Random\Engine\Mt19937(54321);

        // When
        $a = Random::reservoirSample($data, 7, $engineA);
        $b = Random::reservoirSample($data, 7, $engineB);

        // Then
        $this->assertSame($a, $b);
    }

    /**
     * @test reservoirSample over a large lazy iterable holds only the reservoir (does not materialize the input)
     */
    public function testLargeIterableMemoryBounded(): void
    {
        // Given a huge lazy source that would exhaust memory if materialized
        $largeGen = (function () {
            for ($i = 1; $i <= 1_000_000; $i++) {
                yield $i;
            }
        })();

        // When
        $result = Random::reservoirSample($largeGen, 3);

        // Then
        $this->assertCount(3, $result);
        foreach ($result as $item) {
            $this->assertGreaterThanOrEqual(1, $item);
            $this->assertLessThanOrEqual(1_000_000, $item);
        }
    }

    /**
     * @test reservoirSample works on a single-pass (non-rewindable) generator
     */
    public function testWorksOnOneShotGenerator(): void
    {
        // Given a one-shot generator
        $gen = (function () {
            yield 1;
            yield 2;
            yield 3;
            yield 4;
            yield 5;
        })();

        // When
        $result = Random::reservoirSample($gen, 2);

        // Then
        $this->assertCount(2, $result);
        foreach ($result as $item) {
            $this->assertContains($item, [1, 2, 3, 4, 5]);
        }
    }
}

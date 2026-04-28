<?php

declare(strict_types=1);

namespace IterTools\Tests\Sort;

use IterTools\Sort;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class SmallestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test smallest example usage: bottom-3 from a list
     */
    public function testExampleUsageBottom3(): void
    {
        // Given
        $data = [3, 1, 4, 1, 5, 9, 2, 6];

        // When
        $result = \iterator_to_array(Sort::smallest($data, 3), false);

        // Then
        $this->assertSame([1, 1, 2], $result);
    }

    /**
     * @test         smallest (array)
     * @dataProvider dataProviderForSmallest
     * @param        array<mixed> $data
     * @param        int          $n
     * @param        array<mixed> $expected
     */
    public function testArray(array $data, int $n, array $expected): void
    {
        // When
        $result = \iterator_to_array(Sort::smallest($data, $n), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         smallest (Generator)
     * @dataProvider dataProviderForSmallest
     * @param        array<mixed> $data
     * @param        int          $n
     * @param        array<mixed> $expected
     */
    public function testGenerator(array $data, int $n, array $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = \iterator_to_array(Sort::smallest($generator, $n), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         smallest (Iterator)
     * @dataProvider dataProviderForSmallest
     * @param        array<mixed> $data
     * @param        int          $n
     * @param        array<mixed> $expected
     */
    public function testIterator(array $data, int $n, array $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = \iterator_to_array(Sort::smallest($iterator, $n), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         smallest (IteratorAggregate)
     * @dataProvider dataProviderForSmallest
     * @param        array<mixed> $data
     * @param        int          $n
     * @param        array<mixed> $expected
     */
    public function testIteratorAggregate(array $data, int $n, array $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = \iterator_to_array(Sort::smallest($traversable, $n), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @return array<array{0: array<mixed>, 1: int, 2: array<mixed>}>
     */
    public static function dataProviderForSmallest(): array
    {
        return [
            // Canonical
            [
                [3, 1, 4, 1, 5, 9, 2, 6],
                3,
                [1, 1, 2],
            ],
            // n = 0 -> empty
            [
                [3, 1, 4, 1, 5, 9, 2, 6],
                0,
                [],
            ],
            // n > size -> entire input sorted ascending
            [
                [3, 1, 4, 1, 5, 9, 2, 6],
                100,
                [1, 1, 2, 3, 4, 5, 6, 9],
            ],
            // n equals size
            [
                [3, 1, 2],
                3,
                [1, 2, 3],
            ],
            // Duplicates
            [
                [5, 5, 5, 5],
                2,
                [5, 5],
            ],
            // Empty input, n > 0
            [
                [],
                3,
                [],
            ],
            // Empty input, n = 0
            [
                [],
                0,
                [],
            ],
            // Single element, n = 1
            [
                [42],
                1,
                [42],
            ],
            // n = 1 from many
            [
                [3, 1, 4, 1, 5, 9, 2, 6],
                1,
                [1],
            ],
            // Strings
            [
                ['banana', 'apple', 'cherry', 'fig'],
                2,
                ['apple', 'banana'],
            ],
        ];
    }

    /**
     * @test smallest with n = 0 does not consume input (lazy early-return)
     */
    public function testZeroNDoesNotConsumeInput(): void
    {
        // Given
        $generator = (function () {
            throw new \RuntimeException('should not advance');
            yield 1; // unreachable
        })();

        // When
        $result = \iterator_to_array(Sort::smallest($generator, 0), false);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test smallest with negative n throws InvalidArgumentException
     */
    public function testNegativeNThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('non-negative');

        // When
        \iterator_to_array(Sort::smallest([1, 2, 3], -1), false);
    }

    /**
     * @test smallest tie stability: when more elements share a key than there are slots,
     *       the earliest insertions are retained, and final order is by insertion order among ties.
     */
    public function testTieStabilityMoreTiesThanSlots(): void
    {
        // Given
        $items = [
            (object)['id' => 'a', 'score' => 10],
            (object)['id' => 'b', 'score' => 10],
            (object)['id' => 'c', 'score' => 10],
        ];

        // When
        $result = [];
        foreach (Sort::smallest($items, 2, fn ($o) => $o->score) as $item) {
            $result[] = $item->id;
        }

        // Then
        $this->assertSame(['a', 'b'], $result);
    }

    /**
     * @test smallest tie stability with mixed scores: ties broken by insertion order
     */
    public function testTieStabilityMixedScores(): void
    {
        // Given
        $items = [
            (object)['id' => 'a', 'score' => 50],
            (object)['id' => 'b', 'score' => 10],
            (object)['id' => 'c', 'score' => 10],
            (object)['id' => 'd', 'score' => 30],
            (object)['id' => 'e', 'score' => 10],
        ];

        // When
        $result = [];
        foreach (Sort::smallest($items, 3, fn ($o) => $o->score) as $item) {
            $result[] = $item->id;
        }

        // Then: bottom 3 scores are all the 10s; retained by insertion order (b, c, e),
        // emitted in insertion order.
        $this->assertSame(['b', 'c', 'e'], $result);
    }

    /**
     * @test smallest with keyFn: smallest-N latencies by durationMs
     */
    public function testWithKeyFn(): void
    {
        // Given
        $requests = [
            (object)['id' => 'r1', 'durationMs' => 120],
            (object)['id' => 'r2', 'durationMs' => 50],
            (object)['id' => 'r3', 'durationMs' => 200],
            (object)['id' => 'r4', 'durationMs' => 80],
        ];

        // When
        $result = [];
        foreach (Sort::smallest($requests, 3, fn ($r) => $r->durationMs) as $r) {
            $result[] = $r->id;
        }

        // Then
        $this->assertSame(['r2', 'r4', 'r1'], $result);
    }

    /**
     * @test smallest skips NaN values (no keyFn)
     */
    public function testNanSkippedNoKeyFn(): void
    {
        // Given
        $data = [1.0, NAN, 2.0];

        // When
        $result = \iterator_to_array(Sort::smallest($data, 2), false);

        // Then
        $this->assertSame([1.0, 2.0], $result);
    }

    /**
     * @test smallest returns empty when every element is NaN
     */
    public function testAllNanInput(): void
    {
        // Given
        $data = [NAN, NAN];

        // When
        $result = \iterator_to_array(Sort::smallest($data, 2), false);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test smallest skips elements whose keyFn returns NaN
     */
    public function testNanViaKeyFn(): void
    {
        // Given
        $data = [
            (object)['id' => 'a', 'score' => 10.0],
            (object)['id' => 'b', 'score' => NAN],
            (object)['id' => 'c', 'score' => 5.0],
        ];

        // When
        $result = [];
        foreach (Sort::smallest($data, 3, fn ($o) => $o->score) as $item) {
            $result[] = $item->id;
        }

        // Then
        $this->assertSame(['c', 'a'], $result);
    }

    /**
     * @test smallest returns a Generator
     */
    public function testReturnsGenerator(): void
    {
        // When
        $result = Sort::smallest([3, 1, 2], 2);

        // Then
        $this->assertInstanceOf(\Generator::class, $result);
    }

    /**
     * @test smallest n equal to zero on empty input
     */
    public function testZeroNEmptyInput(): void
    {
        // When
        $result = \iterator_to_array(Sort::smallest([], 0), false);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test smallest n > size on empty input
     */
    public function testNGreaterThanSizeEmptyInput(): void
    {
        // When
        $result = \iterator_to_array(Sort::smallest([], 5), false);

        // Then
        $this->assertSame([], $result);
    }
}

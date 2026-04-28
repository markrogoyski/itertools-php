<?php

declare(strict_types=1);

namespace IterTools\Tests\Sort;

use IterTools\Sort;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class LargestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test largest example usage: top-3 from a list
     */
    public function testExampleUsageTop3(): void
    {
        // Given
        $data = [3, 1, 4, 1, 5, 9, 2, 6];

        // When
        $result = \iterator_to_array(Sort::largest($data, 3), false);

        // Then
        $this->assertSame([9, 6, 5], $result);
    }

    /**
     * @test         largest (array)
     * @dataProvider dataProviderForLargest
     * @param        array<mixed> $data
     * @param        int          $n
     * @param        array<mixed> $expected
     */
    public function testArray(array $data, int $n, array $expected): void
    {
        // When
        $result = \iterator_to_array(Sort::largest($data, $n), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         largest (Generator)
     * @dataProvider dataProviderForLargest
     * @param        array<mixed> $data
     * @param        int          $n
     * @param        array<mixed> $expected
     */
    public function testGenerator(array $data, int $n, array $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = \iterator_to_array(Sort::largest($generator, $n), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         largest (Iterator)
     * @dataProvider dataProviderForLargest
     * @param        array<mixed> $data
     * @param        int          $n
     * @param        array<mixed> $expected
     */
    public function testIterator(array $data, int $n, array $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = \iterator_to_array(Sort::largest($iterator, $n), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         largest (IteratorAggregate)
     * @dataProvider dataProviderForLargest
     * @param        array<mixed> $data
     * @param        int          $n
     * @param        array<mixed> $expected
     */
    public function testIteratorAggregate(array $data, int $n, array $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);

        // When
        $result = \iterator_to_array(Sort::largest($traversable, $n), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @return array<array{0: array<mixed>, 1: int, 2: array<mixed>}>
     */
    public static function dataProviderForLargest(): array
    {
        return [
            // Canonical
            [
                [3, 1, 4, 1, 5, 9, 2, 6],
                3,
                [9, 6, 5],
            ],
            // n = 0 -> empty
            [
                [3, 1, 4, 1, 5, 9, 2, 6],
                0,
                [],
            ],
            // n > size -> entire input sorted descending
            [
                [3, 1, 4, 1, 5, 9, 2, 6],
                100,
                [9, 6, 5, 4, 3, 2, 1, 1],
            ],
            // n equals size
            [
                [3, 1, 2],
                3,
                [3, 2, 1],
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
                [9],
            ],
            // Strings
            [
                ['banana', 'apple', 'cherry', 'fig'],
                2,
                ['fig', 'cherry'],
            ],
        ];
    }

    /**
     * @test largest with n = 0 does not consume input (lazy early-return)
     */
    public function testZeroNDoesNotConsumeInput(): void
    {
        // Given
        $generator = (function () {
            throw new \RuntimeException('should not advance');
            yield 1; // unreachable
        })();

        // When
        $result = \iterator_to_array(Sort::largest($generator, 0), false);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test largest with negative n throws InvalidArgumentException
     */
    public function testNegativeNThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('non-negative');

        // When
        \iterator_to_array(Sort::largest([1, 2, 3], -1), false);
    }

    /**
     * @test largest tie stability: when more elements share a key than there are slots,
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
        foreach (Sort::largest($items, 2, fn ($o) => $o->score) as $item) {
            $result[] = $item->id;
        }

        // Then
        $this->assertSame(['a', 'b'], $result);
    }

    /**
     * @test largest tie stability with mixed scores: ties broken by insertion order
     */
    public function testTieStabilityMixedScores(): void
    {
        // Given
        $items = [
            (object)['id' => 'a', 'score' => 5],
            (object)['id' => 'b', 'score' => 10],
            (object)['id' => 'c', 'score' => 10],
            (object)['id' => 'd', 'score' => 8],
            (object)['id' => 'e', 'score' => 10],
        ];

        // When
        $result = [];
        foreach (Sort::largest($items, 3, fn ($o) => $o->score) as $item) {
            $result[] = $item->id;
        }

        // Then: top 3 scores are all the 10s; among ties, retain by insertion order (b, c, e),
        // and emit them in insertion order.
        $this->assertSame(['b', 'c', 'e'], $result);
    }

    /**
     * @test largest with keyFn: top-N leaderboard by score
     */
    public function testWithKeyFn(): void
    {
        // Given
        $people = [
            (object)['name' => 'Alice', 'age' => 30],
            (object)['name' => 'Bob',   'age' => 20],
            (object)['name' => 'Carol', 'age' => 40],
            (object)['name' => 'Dave',  'age' => 35],
        ];

        // When
        $result = [];
        foreach (Sort::largest($people, 3, fn ($p) => $p->age) as $p) {
            $result[] = $p->name;
        }

        // Then
        $this->assertSame(['Carol', 'Dave', 'Alice'], $result);
    }

    /**
     * @test largest skips NaN values (no keyFn)
     */
    public function testNanSkippedNoKeyFn(): void
    {
        // Given
        $data = [1.0, NAN, 2.0];

        // When
        $result = \iterator_to_array(Sort::largest($data, 2), false);

        // Then
        $this->assertSame([2.0, 1.0], $result);
    }

    /**
     * @test largest returns empty when every element is NaN
     */
    public function testAllNanInput(): void
    {
        // Given
        $data = [NAN, NAN];

        // When
        $result = \iterator_to_array(Sort::largest($data, 2), false);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test largest skips elements whose keyFn returns NaN
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
        foreach (Sort::largest($data, 3, fn ($o) => $o->score) as $item) {
            $result[] = $item->id;
        }

        // Then
        $this->assertSame(['a', 'c'], $result);
    }

    /**
     * @test largest returns a Generator
     */
    public function testReturnsGenerator(): void
    {
        // When
        $result = Sort::largest([3, 1, 2], 2);

        // Then
        $this->assertInstanceOf(\Generator::class, $result);
    }

    /**
     * @test largest n equal to zero on empty input
     */
    public function testZeroNEmptyInput(): void
    {
        // When
        $result = \iterator_to_array(Sort::largest([], 0), false);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test largest n > size on empty input
     */
    public function testNGreaterThanSizeEmptyInput(): void
    {
        // When
        $result = \iterator_to_array(Sort::largest([], 5), false);

        // Then
        $this->assertSame([], $result);
    }
}

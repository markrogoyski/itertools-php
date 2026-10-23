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
     * @test largest only skips a key that is itself NaN: a NaN nested in a composite key is not detected
     *       and the element is kept (its position is unspecified; see README "NaN and Ordering")
     */
    public function testNanNestedInCompositeKeyIsNotSkipped(): void
    {
        // Given
        $data = [
            (object)['id' => 'a', 'key' => [2.0, 'a']],
            (object)['id' => 'b', 'key' => [NAN, 'b']],
            (object)['id' => 'c', 'key' => [1.0, 'c']],
        ];

        // When
        $result = [];
        foreach (Sort::largest($data, 3, fn ($o) => $o->key) as $item) {
            $result[] = $item->id;
        }

        // Then
        \sort($result);
        $this->assertSame(['a', 'b', 'c'], $result);
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

    /**
     * @test         largest (array) of ints beyond 2^53 and floats, compared by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array<mixed>  $data
     * @param        int           $n
     * @param        callable|null $keyFn
     * @param        array<mixed>  $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionArray(array $data, int $n, ?callable $keyFn, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }

        // When
        $result = \iterator_to_array(Sort::largest($data, $n, $keyFn), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         largest (Generator) of ints beyond 2^53 and floats, compared by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array<mixed>  $data
     * @param        int           $n
     * @param        callable|null $keyFn
     * @param        array<mixed>  $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionGenerator(array $data, int $n, ?callable $keyFn, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = \iterator_to_array(Sort::largest($iterable, $n, $keyFn), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         largest (Iterator) of ints beyond 2^53 and floats, compared by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array<mixed>  $data
     * @param        int           $n
     * @param        callable|null $keyFn
     * @param        array<mixed>  $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionIterator(array $data, int $n, ?callable $keyFn, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = \iterator_to_array(Sort::largest($iterable, $n, $keyFn), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         largest (IteratorAggregate) of ints beyond 2^53 and floats, compared by exact value
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecision
     * @param        array<mixed>  $data
     * @param        int           $n
     * @param        callable|null $keyFn
     * @param        array<mixed>  $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionIteratorAggregate(array $data, int $n, ?callable $keyFn, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = \iterator_to_array(Sort::largest($iterable, $n, $keyFn), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, which compares an int and a float by exact value
     * (tools/oracles/exact_order_heaps.py):
     *   heapq.nlargest(n, data, key=keyFn)
     * Each int compares equal as a float to a float it is not equal to, so <=> orders them wrongly.
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecision(): array
    {
        return [
            'float 2.0^53 first, below int 2^53 + 1' => [
                [9007199254740992.0, 9007199254740993],
                1,
                null,
                [9007199254740993],
            ],
            '2^62 + 500, 2.0^62, 2^62 + 1: two largest are the ints' => [
                [4611686018427388404, 4611686018427387904.0, 4611686018427387905],
                2,
                null,
                [4611686018427388404, 4611686018427387905],
            ],
            '2^62 + 500, 2.0^62, 2^62 + 1: all three, descending' => [
                [4611686018427388404, 4611686018427387904.0, 4611686018427387905],
                3,
                null,
                [4611686018427388404, 4611686018427387905, 4611686018427387904.0],
            ],
            'negative int -(2^53 + 1) first, below float -2.0^53' => [
                [-9007199254740993, -9007199254740992.0],
                1,
                null,
                [-9007199254740992.0],
            ],
            'keyFn: float 2.0^53 first, below int 2^53 + 1' => [
                [['v' => 9007199254740992.0], ['v' => 9007199254740993]],
                1,
                fn (array $item) => $item['v'],
                [['v' => 9007199254740993]],
            ],
        ];
    }
}

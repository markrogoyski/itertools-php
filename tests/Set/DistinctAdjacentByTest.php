<?php

declare(strict_types=1);

namespace IterTools\Tests\Set;

use IterTools\Set;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class DistinctAdjacentByTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test distinctAdjacentBy example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data    = ['apple', 'ant', 'banana', 'berry', 'apple'];
        $firstCh = static fn (string $s): string => $s[0];

        // When
        $result = [];
        foreach (Set::distinctAdjacentBy($data, $firstCh) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame(['apple', 'banana', 'apple'], $result);
    }

    /**
     * @test         distinctAdjacentBy (array)
     * @dataProvider dataProviderForDistinctAdjacentBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentByArray(array $data, callable $keyFn, array $expected): void
    {
        // When
        $result = [];
        foreach (Set::distinctAdjacentBy($data, $keyFn) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         distinctAdjacentBy (Generator)
     * @dataProvider dataProviderForDistinctAdjacentBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentByGenerator(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $gen = GeneratorFixture::getGenerator($data);

        // When
        $result = [];
        foreach (Set::distinctAdjacentBy($gen, $keyFn) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         distinctAdjacentBy (Iterator)
     * @dataProvider dataProviderForDistinctAdjacentBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentByIterator(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $iter = new ArrayIteratorFixture($data);

        // When
        $result = [];
        foreach (Set::distinctAdjacentBy($iter, $keyFn) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         distinctAdjacentBy (IteratorAggregate)
     * @dataProvider dataProviderForDistinctAdjacentBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentByIteratorAggregate(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $agg = new IteratorAggregateFixture($data);

        // When
        $result = [];
        foreach (Set::distinctAdjacentBy($agg, $keyFn) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForDistinctAdjacentBy(): array
    {
        $identity = static fn ($x) => $x;
        $firstCh  = static fn (string $s): string => $s[0];
        $modTwo   = static fn (int $x): int => $x % 2;

        return [
            // empty
            [
                [],
                $identity,
                [],
            ],
            // single element
            [
                [1],
                $identity,
                [1],
            ],
            // all same key
            [
                [1, 1, 1, 1],
                $identity,
                [1],
            ],
            // identity key — equivalent to distinctAdjacent
            [
                [1, 1, 2, 2, 3, 1, 1],
                $identity,
                [1, 2, 3, 1],
            ],
            // group by first letter
            [
                ['apple', 'ant', 'banana', 'berry', 'apple'],
                $firstCh,
                ['apple', 'banana', 'apple'],
            ],
            // group by parity
            [
                [2, 4, 6, 1, 3, 8, 10],
                $modTwo,
                [2, 1, 8],
            ],
            // strict comparison on extracted key: integer 1 vs string '1' both produce same string when cast,
            // but here keyFn returns the raw value so 1 !== '1'
            [
                [1, 1, '1', '1', 1],
                $identity,
                [1, '1', 1],
            ],
            // already distinct by key
            [
                [1, 2, 3],
                $identity,
                [1, 2, 3],
            ],
        ];
    }

    /**
     * @test         distinctAdjacentBy on empty iterable
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmpty(iterable $data): void
    {
        // When
        $result = [];
        foreach (Set::distinctAdjacentBy($data, fn ($x) => $x) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test distinctAdjacentBy collapses sensor readings rounded to the minute
     */
    public function testSensorReadingsRoundedByMinute(): void
    {
        // Given
        $readings = [
            ['ts' => 60, 'v' => 1],
            ['ts' => 65, 'v' => 2],
            ['ts' => 119, 'v' => 3],
            ['ts' => 120, 'v' => 4],
            ['ts' => 121, 'v' => 5],
            ['ts' => 60, 'v' => 6],
        ];
        $minuteKey = static fn (array $r): int => \intdiv($r['ts'], 60);

        // When
        $result = [];
        foreach (Set::distinctAdjacentBy($readings, $minuteKey) as $datum) {
            $result[] = $datum;
        }

        // Then — first of each adjacent run is kept
        $this->assertSame(
            [
                ['ts' => 60, 'v' => 1],
                ['ts' => 120, 'v' => 4],
                ['ts' => 60, 'v' => 6],
            ],
            $result
        );
    }

    /**
     * @test distinctAdjacentBy uses strict comparison on extracted keys
     */
    public function testStrictKeyComparison(): void
    {
        // Given
        $data  = [1, 2, 3, 4, 5, 6];
        // returns string vs int alternately; strict comparison treats them as different
        $keyFn = static fn (int $x): mixed => ($x % 2 === 0) ? '0' : 0;

        // When
        $result = [];
        foreach (Set::distinctAdjacentBy($data, $keyFn) as $datum) {
            $result[] = $datum;
        }

        // Then — every key alternates 0, '0', 0, '0', ... so nothing collapses
        $this->assertSame([1, 2, 3, 4, 5, 6], $result);
    }

    /**
     * @test distinctAdjacentBy discards source keys (output is sequentially re-indexed)
     */
    public function testKeysDiscarded(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 1, 'c' => 2, 'd' => 2, 'e' => 3];

        // When
        $result = \iterator_to_array(Set::distinctAdjacentBy($data, fn ($x) => $x), false);

        // Then
        $this->assertSame([1, 2, 3], $result);
        $this->assertSame([0, 1, 2], \array_keys($result));
    }

    /**
     * Regression: a key that shares a reference slot with later keys (here the row itself, filled
     * the PDO bindColumn way) is compared as it was when it was projected.
     *
     * @test distinctAdjacentBy keeps rows whose reference-slot keys differ when projected
     */
    public function testReferenceSlotKeys(): void
    {
        // Given
        $data = self::referenceSlotRows(1, 1, 2, 2, 1);
        $keyFn = fn (array $row): array => $row;

        // When
        $ids = [];
        foreach (Set::distinctAdjacentBy($data, $keyFn) as $row) {
            $ids[] = $row['id'];
        }

        // Then
        $this->assertSame([1, 2, 1], $ids);
    }

    /**
     * Regression: each key is captured for comparison before its element is yielded, so a consumer
     * that writes through the shared slot does not change what the next key is compared with.
     *
     * @test distinctAdjacentBy compares reference-slot keys as projected, whatever the consumer writes
     */
    public function testReferenceSlotKeysWrittenByConsumer(): void
    {
        // Given
        $data = self::referenceSlotRows(1, 1, 2, 2, 1);
        $keyFn = fn (array $row): array => $row;

        // When
        $ids = [];
        foreach (Set::distinctAdjacentBy($data, $keyFn) as $row) {
            $ids[] = $row['id'];
            $row['id'] = 100;
        }

        // Then
        $this->assertSame([1, 2, 1], $ids);
    }

    /**
     * Regression: each key is compared as it was when projected. Both keys refer to one cell, which held
     * a different array holding NAN at each projection, so they are distinct, though both refer to the
     * same array once the second is projected.
     *
     * @test distinctAdjacentBy keeps elements whose reference-slot keys held separate arrays holding NAN
     */
    public function testReferenceSlotKeysToSeparateArraysHoldingNan(): void
    {
        // Given
        $nan = NAN;
        $cell = null;
        $data = (function () use ($nan, &$cell): \Generator {
            $cell = [$nan];
            yield 1;
            $cell = [$nan];
            yield 2;
        })();
        $keyFn = function (int $datum) use (&$cell): array {
            return [&$cell];
        };

        // When
        $result = \iterator_to_array(Set::distinctAdjacentBy($data, $keyFn), false);

        // Then
        $this->assertSame([1, 2], $result);
    }

    /**
     * A key with a reference slot is compared through its copy, so the NAN it holds never matches, even
     * in the same unchanged key projected twice.
     *
     * @test distinctAdjacentBy keeps elements whose reference-slot key holding NAN is projected twice
     */
    public function testReferenceSlotKeyHoldingNanProjectedTwice(): void
    {
        // Given
        $id = 1;
        $key = ['id' => &$id, 'score' => NAN];
        $data = [1, 2];
        $keyFn = fn (int $datum): array => $key;

        // When
        $result = \iterator_to_array(Set::distinctAdjacentBy($data, $keyFn), false);

        // Then
        $this->assertSame([1, 2], $result);
    }

    /**
     * @test distinctAdjacentBy drops an element whose reference-slot key holds NAN in a subarray without reference slots
     */
    public function testReferenceSlotKeyHoldingNanInSubarrayProjectedTwice(): void
    {
        // Given
        $id = 1;
        $key = ['id' => &$id, 'scores' => [NAN]];
        $data = [1, 2];
        $keyFn = fn (int $datum): array => $key;

        // When
        $result = \iterator_to_array(Set::distinctAdjacentBy($data, $keyFn), false);

        // Then
        $this->assertSame([1], $result);
    }

    /**
     * Yields rows that all share one reference slot, as PDOStatement::bindColumn() fills them:
     * each yielded ['id' => &$id] follows the next assignment to $id.
     *
     * @return \Generator<int, array{id: int}>
     */
    private static function referenceSlotRows(int ...$ids): \Generator
    {
        $id = null;
        $row = ['id' => &$id];

        foreach ($ids as $value) {
            $id = $value;

            yield $row;
        }
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class GroupAdjacentByTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test groupAdjacentBy example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 1, 2, 2, 1, 3];

        // When
        $result = [];
        foreach (Single::groupAdjacentBy($data, fn (int $x): int => $x) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame([[1, [1, 1]], [2, [2, 2]], [1, [1]], [3, [3]]], $result);
    }

    /**
     * @test         groupAdjacentBy (array)
     * @dataProvider dataProviderForGroupAdjacentBy
     * @param        array<mixed>     $data
     * @param        callable         $keyFn
     * @param        array<array{mixed, array<mixed>}> $expected
     */
    public function testGroupAdjacentByArray(array $data, callable $keyFn, array $expected): void
    {
        // When
        $result = [];
        foreach (Single::groupAdjacentBy($data, $keyFn) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         groupAdjacentBy (Generator)
     * @dataProvider dataProviderForGroupAdjacentBy
     * @param        array<mixed>     $data
     * @param        callable         $keyFn
     * @param        array<array{mixed, array<mixed>}> $expected
     */
    public function testGroupAdjacentByGenerator(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = [];
        foreach (Single::groupAdjacentBy($iterable, $keyFn) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         groupAdjacentBy (Iterator)
     * @dataProvider dataProviderForGroupAdjacentBy
     * @param        array<mixed>     $data
     * @param        callable         $keyFn
     * @param        array<array{mixed, array<mixed>}> $expected
     */
    public function testGroupAdjacentByIterator(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = [];
        foreach (Single::groupAdjacentBy($iterable, $keyFn) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         groupAdjacentBy (IteratorAggregate)
     * @dataProvider dataProviderForGroupAdjacentBy
     * @param        array<mixed>     $data
     * @param        callable         $keyFn
     * @param        array<array{mixed, array<mixed>}> $expected
     */
    public function testGroupAdjacentByIteratorAggregate(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = [];
        foreach (Single::groupAdjacentBy($iterable, $keyFn) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForGroupAdjacentBy(): array
    {
        return [
            // repeated key in separate runs
            [[1, 1, 2, 2, 1, 3, 3], fn (int $x): int => $x, [[1, [1, 1]], [2, [2, 2]], [1, [1]], [3, [3, 3]]]],
            // single element
            [[42], fn (int $x): int => $x, [[42, [42]]]],
            // all same value
            [[7, 7, 7], fn (int $x): int => $x, [[7, [7, 7, 7]]]],
            // all unique
            [[1, 2, 3], fn (int $x): int => $x, [[1, [1]], [2, [2]], [3, [3]]]],
            // group by parity
            [[1, 3, 2, 4, 5], fn (int $x): int => $x % 2, [[1, [1, 3]], [0, [2, 4]], [1, [5]]]],
            // group by string key
            [
                [['type' => 'a', 'v' => 1], ['type' => 'a', 'v' => 2], ['type' => 'b', 'v' => 3]],
                fn (array $x): string => $x['type'],
                [
                    ['a', [['type' => 'a', 'v' => 1], ['type' => 'a', 'v' => 2]]],
                    ['b', [['type' => 'b', 'v' => 3]]],
                ],
            ],
        ];
    }

    /**
     * @test         groupAdjacentBy empty iterable yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterable(iterable $data): void
    {
        // When
        $result = [];
        foreach (Single::groupAdjacentBy($data, fn ($x) => $x) as $pair) {
            $result[] = $pair;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test groupAdjacentBy inner array is list-shaped
     */
    public function testInnerArrayIsList(): void
    {
        // Given
        $data = [1, 1, 2, 2, 1];

        // When
        $first = null;
        foreach (Single::groupAdjacentBy($data, fn (int $x): int => $x) as $pair) {
            $first = $pair;
            break;
        }

        // Then
        $this->assertIsArray($first);
        $this->assertSame([0, 1], \array_keys($first));
        $this->assertTrue(\array_is_list($first[1]));
    }

    /**
     * @test groupAdjacentBy outer keys are sequential 0-indexed
     */
    public function testOuterKeysSequential(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 1, 'c' => 2];

        // When
        $keys = [];
        foreach (Single::groupAdjacentBy($data, fn (int $x): int => $x) as $key => $pair) {
            $keys[] = $key;
        }

        // Then
        $this->assertSame([0, 1], $keys);
    }

    /**
     * Regression: a key that shares a reference slot with later keys (here the row itself, filled
     * the PDO bindColumn way) is compared, and yielded, as it was when it was projected.
     *
     * @test groupAdjacentBy splits rows whose reference-slot keys differ when projected
     */
    public function testReferenceSlotKeys(): void
    {
        // Given
        $data = self::referenceSlotRows(1, 1, 2, 2, 1);
        $keyFn = fn (array $row): array => $row;

        // When
        $keys = [];
        foreach (Single::groupAdjacentBy($data, $keyFn) as [$key, $group]) {
            $keys[] = $key;
        }

        // Then
        $this->assertSame([['id' => 1], ['id' => 2], ['id' => 1]], $keys);
    }

    /**
     * Regression: the next group's key is captured before the previous group is yielded, so a
     * consumer that writes through the shared slot does not change it.
     *
     * @test groupAdjacentBy compares and yields reference-slot keys as projected, whatever the consumer writes
     */
    public function testReferenceSlotKeysWrittenByConsumer(): void
    {
        // Given
        $data = self::referenceSlotRows(1, 1, 2, 2, 1);
        $keyFn = fn (array $row): array => $row;

        // When
        $keys = [];
        foreach (Single::groupAdjacentBy($data, $keyFn) as [$key, $group]) {
            $keys[] = $key;
            $group[0]['id'] = 100;
        }

        // Then
        $this->assertSame([['id' => 1], ['id' => 2], ['id' => 1]], $keys);
    }

    /**
     * Regression: each key is compared as it was when projected. Both keys refer to one cell, which held
     * a different array holding NAN at each projection, so they start separate groups, though both refer
     * to the same array once the second is projected.
     *
     * @test groupAdjacentBy splits elements whose reference-slot keys held separate arrays holding NAN
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
        $groups = [];
        foreach (Single::groupAdjacentBy($data, $keyFn) as [$key, $group]) {
            $groups[] = $group;
        }

        // Then
        $this->assertSame([[1], [2]], $groups);
    }

    /**
     * A key with a reference slot is compared through its copy, so the NAN it holds never matches, even
     * in the same unchanged key projected twice.
     *
     * @test groupAdjacentBy splits elements whose reference-slot key holding NAN is projected twice
     */
    public function testReferenceSlotKeyHoldingNanProjectedTwice(): void
    {
        // Given
        $id = 1;
        $key = ['id' => &$id, 'score' => NAN];
        $data = [1, 2];
        $keyFn = fn (int $datum): array => $key;

        // When
        $groups = [];
        foreach (Single::groupAdjacentBy($data, $keyFn) as [$groupKey, $group]) {
            $groups[] = $group;
        }

        // Then
        $this->assertSame([[1], [2]], $groups);
    }

    /**
     * @test groupAdjacentBy groups elements whose reference-slot key holds NAN in a subarray without reference slots
     */
    public function testReferenceSlotKeyHoldingNanInSubarrayProjectedTwice(): void
    {
        // Given
        $id = 1;
        $key = ['id' => &$id, 'scores' => [NAN]];
        $data = [1, 2];
        $keyFn = fn (int $datum): array => $key;

        // When
        $groups = [];
        foreach (Single::groupAdjacentBy($data, $keyFn) as [$groupKey, $group]) {
            $groups[] = $group;
        }

        // Then
        $this->assertSame([[1, 2]], $groups);
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

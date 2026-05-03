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
}

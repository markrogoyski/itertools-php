<?php

declare(strict_types=1);

namespace IterTools\Tests\Sort;

use IterTools\Sort;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class SortByTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test sortBy example usage with objects sorted by property
     */
    public function testExampleUsageSortObjectsByProperty(): void
    {
        // Given
        $people = [
            (object)['name' => 'Alice', 'age' => 30],
            (object)['name' => 'Bob',   'age' => 20],
            (object)['name' => 'Carol', 'age' => 40],
        ];

        // When
        $result = [];
        foreach (Sort::sortBy($people, fn ($p) => $p->age) as $person) {
            $result[] = $person->name;
        }

        // Then
        $this->assertSame(['Bob', 'Alice', 'Carol'], $result);
    }

    /**
     * @test         sortBy (array)
     * @dataProvider dataProviderForSortBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testArray(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::sortBy($data, $keyFn) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         sortBy (Generator)
     * @dataProvider dataProviderForSortBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testGenerator(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);
        $result = [];

        // When
        foreach (Sort::sortBy($generator, $keyFn) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         sortBy (Iterator)
     * @dataProvider dataProviderForSortBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testIterator(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);
        $result = [];

        // When
        foreach (Sort::sortBy($iterator, $keyFn) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         sortBy (IteratorAggregate)
     * @dataProvider dataProviderForSortBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testIteratorAggregate(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);
        $result = [];

        // When
        foreach (Sort::sortBy($traversable, $keyFn) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<array{0: array<mixed>, 1: callable, 2: array<mixed>}>
     */
    public static function dataProviderForSortBy(): array
    {
        return [
            // Empty
            [
                [],
                fn ($x) => $x,
                [],
            ],
            // Single element
            [
                [42],
                fn ($x) => $x,
                [42],
            ],
            // Strings sorted by length
            [
                ['banana', 'fig', 'cherry', 'apple'],
                fn (string $s) => \strlen($s),
                ['fig', 'apple', 'banana', 'cherry'],
            ],
            // Arrays sorted by first column
            [
                [[3, 'a'], [1, 'b'], [2, 'c']],
                fn (array $row) => $row[0],
                [[1, 'b'], [2, 'c'], [3, 'a']],
            ],
            // Sort by negation (descending)
            [
                [3, 1, 4, 1, 5, 9, 2, 6],
                fn (int $n) => -$n,
                [9, 6, 5, 4, 3, 2, 1, 1],
            ],
            // Sort with all equal keys (stable)
            [
                ['a', 'b', 'c', 'd'],
                fn (string $s) => 0,
                ['a', 'b', 'c', 'd'],
            ],
        ];
    }

    /**
     * @test sortBy is a stable sort: equal keys preserve original relative order
     */
    public function testStableSort(): void
    {
        // Given
        $items = [
            ['name' => 'one',   'group' => 'A'],
            ['name' => 'two',   'group' => 'B'],
            ['name' => 'three', 'group' => 'A'],
            ['name' => 'four',  'group' => 'B'],
            ['name' => 'five',  'group' => 'A'],
        ];

        // When
        $result = [];
        foreach (Sort::sortBy($items, fn (array $item) => $item['group']) as $item) {
            $result[] = $item['name'];
        }

        // Then: A's come before B's; within each group, insertion order is preserved.
        $this->assertSame(['one', 'three', 'five', 'two', 'four'], $result);
    }

    /**
     * @test sortBy invokes key function exactly once per element (Schwartzian transform)
     */
    public function testKeyFunctionCalledOncePerElement(): void
    {
        // Given
        $data = [5, 4, 3, 2, 1, 6, 7, 8, 9, 10];
        $callCount = 0;
        $keyFn = function (int $n) use (&$callCount): int {
            $callCount++;
            return $n;
        };

        // When
        $result = \iterator_to_array(Sort::sortBy($data, $keyFn), false);

        // Then
        $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], $result);
        $this->assertSame(\count($data), $callCount);
    }

    /**
     * @test sortBy returns a Generator
     */
    public function testReturnsGenerator(): void
    {
        // When
        $result = Sort::sortBy([3, 1, 2], fn ($x) => $x);

        // Then
        $this->assertInstanceOf(\Generator::class, $result);
    }

    /**
     * @test sortBy on associative array discards keys (re-indexed list output)
     */
    public function testAssociativeArrayDiscardsKeysArray(): void
    {
        // Given
        $data = ['c' => 3, 'a' => 1, 'b' => 2];

        // When
        $result = \iterator_to_array(Sort::sortBy($data, fn (int $x) => $x), false);

        // Then
        $this->assertSame([1, 2, 3], $result);
    }

    /**
     * @test sortBy on associative key/value generator discards keys (re-indexed list output)
     */
    public function testAssociativeArrayDiscardsKeysKeyValueGenerator(): void
    {
        // Given
        $data = GeneratorFixture::getKeyValueGenerator(['c' => 3, 'a' => 1, 'b' => 2]);

        // When
        $result = \iterator_to_array(Sort::sortBy($data, fn (int $x) => $x), false);

        // Then
        $this->assertSame([1, 2, 3], $result);
    }

    /**
     * @test sortBy on IteratorAggregate with associative input discards keys
     */
    public function testAssociativeArrayDiscardsKeysIteratorAggregate(): void
    {
        // Given
        $data = new IteratorAggregateFixture(['c' => 3, 'a' => 1, 'b' => 2]);

        // When
        $result = \iterator_to_array(Sort::sortBy($data, fn (int $x) => $x), false);

        // Then
        $this->assertSame([1, 2, 3], $result);
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Sort;

use IterTools\Sort;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class AsortByTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test asortBy example usage with associative score table
     */
    public function testExampleUsageAssociativeScoreTable(): void
    {
        // Given
        $scores = [
            'Alice' => 87,
            'Bob'   => 92,
            'Carol' => 75,
        ];

        // When
        $result = [];
        foreach (Sort::asortBy($scores, fn (int $score) => $score) as $name => $score) {
            $result[$name] = $score;
        }

        // Then
        $this->assertSame(['Carol' => 75, 'Alice' => 87, 'Bob' => 92], $result);
    }

    /**
     * @test         asortBy on lists (array)
     * @dataProvider dataProviderForAsortBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testArray(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::asortBy($data, $keyFn) as $key => $item) {
            $result[$key] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         asortBy on lists (Generator)
     * @dataProvider dataProviderForAsortBy
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
        foreach (Sort::asortBy($generator, $keyFn) as $key => $item) {
            $result[$key] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         asortBy on lists (Iterator)
     * @dataProvider dataProviderForAsortBy
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
        foreach (Sort::asortBy($iterator, $keyFn) as $key => $item) {
            $result[$key] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         asortBy on lists (IteratorAggregate)
     * @dataProvider dataProviderForAsortBy
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
        foreach (Sort::asortBy($traversable, $keyFn) as $key => $item) {
            $result[$key] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<array{0: array<mixed>, 1: callable, 2: array<mixed>}>
     */
    public static function dataProviderForAsortBy(): array
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
            // Strings sorted by length, list input → keys are 0..n
            [
                ['banana', 'fig', 'cherry', 'apple'],
                fn (string $s) => \strlen($s),
                [1 => 'fig', 3 => 'apple', 0 => 'banana', 2 => 'cherry'],
            ],
            // Arrays sorted by first column
            [
                [[3, 'a'], [1, 'b'], [2, 'c']],
                fn (array $row) => $row[0],
                [1 => [1, 'b'], 2 => [2, 'c'], 0 => [3, 'a']],
            ],
        ];
    }

    /**
     * @test asortBy preserves keys for associative array
     */
    public function testPreservesKeysAssociativeArray(): void
    {
        // Given
        $people = [
            'alice' => (object)['age' => 30],
            'bob'   => (object)['age' => 20],
            'carol' => (object)['age' => 40],
        ];

        // When
        $result = \iterator_to_array(Sort::asortBy($people, fn ($p) => $p->age), true);

        // Then
        $this->assertSame(['bob', 'alice', 'carol'], \array_keys($result));
    }

    /**
     * @test asortBy preserves keys for associative key/value generator
     */
    public function testPreservesKeysKeyValueGenerator(): void
    {
        // Given
        $generator = GeneratorFixture::getKeyValueGenerator([
            'alice' => 30,
            'bob'   => 20,
            'carol' => 40,
        ]);

        // When
        $result = \iterator_to_array(Sort::asortBy($generator, fn (int $age) => $age), true);

        // Then
        $this->assertSame(['bob' => 20, 'alice' => 30, 'carol' => 40], $result);
    }

    /**
     * @test asortBy preserves keys for IteratorAggregate with associative data
     */
    public function testPreservesKeysIteratorAggregate(): void
    {
        // Given
        $traversable = new IteratorAggregateFixture([
            'alice' => 30,
            'bob'   => 20,
            'carol' => 40,
        ]);

        // When
        $result = \iterator_to_array(Sort::asortBy($traversable, fn (int $age) => $age), true);

        // Then
        $this->assertSame(['bob' => 20, 'alice' => 30, 'carol' => 40], $result);
    }

    /**
     * @test asortBy is a stable sort: equal keys preserve original relative order (with key preservation)
     */
    public function testStableSort(): void
    {
        // Given
        $items = [
            'one'   => ['group' => 'A'],
            'two'   => ['group' => 'B'],
            'three' => ['group' => 'A'],
            'four'  => ['group' => 'B'],
            'five'  => ['group' => 'A'],
        ];

        // When
        $result = \iterator_to_array(
            Sort::asortBy($items, fn (array $item) => $item['group']),
            true
        );

        // Then
        $this->assertSame(['one', 'three', 'five', 'two', 'four'], \array_keys($result));
    }

    /**
     * @test asortBy invokes key function exactly once per element (Schwartzian transform)
     */
    public function testKeyFunctionCalledOncePerElement(): void
    {
        // Given
        $data = ['a' => 5, 'b' => 4, 'c' => 3, 'd' => 2, 'e' => 1, 'f' => 6, 'g' => 7, 'h' => 8, 'i' => 9, 'j' => 10];
        $callCount = 0;
        $keyFn = function (int $n) use (&$callCount): int {
            $callCount++;
            return $n;
        };

        // When
        $result = \iterator_to_array(Sort::asortBy($data, $keyFn), true);

        // Then
        $this->assertSame(['e', 'd', 'c', 'b', 'a', 'f', 'g', 'h', 'i', 'j'], \array_keys($result));
        $this->assertSame(\count($data), $callCount);
    }

    /**
     * @test asortBy returns a Generator
     */
    public function testReturnsGenerator(): void
    {
        // When
        $result = Sort::asortBy(['a' => 3, 'b' => 1, 'c' => 2], fn ($x) => $x);

        // Then
        $this->assertInstanceOf(\Generator::class, $result);
    }
}

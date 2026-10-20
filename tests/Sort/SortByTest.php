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

    /**
     * @test         sortBy (Array) with NAN keys sorted last
     * @dataProvider dataProviderForNanKeySortsLast
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testNanKeySortsLastArray(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::sortBy($data, $keyFn) as $item) {
            $result[] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         sortBy (Generator) with NAN keys sorted last
     * @dataProvider dataProviderForNanKeySortsLast
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testNanKeySortsLastGenerator(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);
        $result = [];

        // When
        foreach (Sort::sortBy($generator, $keyFn) as $item) {
            $result[] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         sortBy (Iterator) with NAN keys sorted last
     * @dataProvider dataProviderForNanKeySortsLast
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testNanKeySortsLastIterator(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);
        $result = [];

        // When
        foreach (Sort::sortBy($iterator, $keyFn) as $item) {
            $result[] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         sortBy (IteratorAggregate) with NAN keys sorted last
     * @dataProvider dataProviderForNanKeySortsLast
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testNanKeySortsLastIteratorAggregate(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $traversable = new IteratorAggregateFixture($data);
        $result = [];

        // When
        foreach (Sort::sortBy($traversable, $keyFn) as $item) {
            $result[] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(rows, key=lambda r: (isnan(r['v']), 0 if isnan(r['v']) else r['v']))
     */
    public static function dataProviderForNanKeySortsLast(): array
    {
        return [
            'NAN keys last in input order' => [
                [['id' => 'a', 'v' => 2], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 1], ['id' => 'd', 'v' => \NAN], ['id' => 'e', 'v' => 3]],
                fn (array $row) => $row['v'],
                [['id' => 'c', 'v' => 1], ['id' => 'a', 'v' => 2], ['id' => 'e', 'v' => 3], ['id' => 'b', 'v' => \NAN], ['id' => 'd', 'v' => \NAN]],
            ],
            'NAN key in the middle' => [
                [['id' => 'a', 'v' => 2], ['id' => 'b', 'v' => 4], ['id' => 'c', 'v' => \NAN], ['id' => 'd', 'v' => 5], ['id' => 'e', 'v' => 6], ['id' => 'f', 'v' => 1], ['id' => 'g', 'v' => 3]],
                fn (array $row) => $row['v'],
                [['id' => 'f', 'v' => 1], ['id' => 'a', 'v' => 2], ['id' => 'g', 'v' => 3], ['id' => 'b', 'v' => 4], ['id' => 'd', 'v' => 5], ['id' => 'e', 'v' => 6], ['id' => 'c', 'v' => \NAN]],
            ],
            'only NAN keys' => [
                [['id' => 'a', 'v' => \NAN], ['id' => 'b', 'v' => \NAN]],
                fn (array $row) => $row['v'],
                [['id' => 'a', 'v' => \NAN], ['id' => 'b', 'v' => \NAN]],
            ],
            'NAN key after INF' => [
                [['id' => 'a', 'v' => \INF], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => -\INF]],
                fn (array $row) => $row['v'],
                [['id' => 'c', 'v' => -\INF], ['id' => 'a', 'v' => \INF], ['id' => 'b', 'v' => \NAN]],
            ],
        ];
    }

    /**
     * @test         sortBy (Array) of int and float keys beyond 2^53, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatKeyBeyondFloatPrecision
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testIntAndFloatKeyBeyondFloatPrecisionArray(array $data, callable $keyFn, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::sortBy($data, $keyFn) as $item) {
            $result[] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         sortBy (Generator) of int and float keys beyond 2^53, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatKeyBeyondFloatPrecision
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testIntAndFloatKeyBeyondFloatPrecisionGenerator(array $data, callable $keyFn, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $generator = GeneratorFixture::getGenerator($data);
        $result = [];

        // When
        foreach (Sort::sortBy($generator, $keyFn) as $item) {
            $result[] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         sortBy (Iterator) of int and float keys beyond 2^53, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatKeyBeyondFloatPrecision
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testIntAndFloatKeyBeyondFloatPrecisionIterator(array $data, callable $keyFn, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $iterator = new ArrayIteratorFixture($data);
        $result = [];

        // When
        foreach (Sort::sortBy($iterator, $keyFn) as $item) {
            $result[] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         sortBy (IteratorAggregate) of int and float keys beyond 2^53, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatKeyBeyondFloatPrecision
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testIntAndFloatKeyBeyondFloatPrecisionIteratorAggregate(array $data, callable $keyFn, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $traversable = new IteratorAggregateFixture($data);
        $result = [];

        // When
        foreach (Sort::sortBy($traversable, $keyFn) as $item) {
            $result[] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(rows, key=lambda r: (isnan(r['v']), 0 if isnan(r['v']) else r['v']))
     */
    public static function dataProviderForIntAndFloatKeyBeyondFloatPrecision(): array
    {
        return [
            'int key 2^53 + 1 before float key 2.0^53, equal as floats' => [
                [['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => 9007199254740992.0]],
                fn (array $row) => $row['v'],
                [['id' => 'b', 'v' => 9007199254740992.0], ['id' => 'a', 'v' => 9007199254740993]],
            ],
            'float key 2.0^53 after int keys that equal it as floats' => [
                [['id' => 'a', 'v' => 9007199254740994], ['id' => 'b', 'v' => 9007199254740993], ['id' => 'c', 'v' => 9007199254740992.0]],
                fn (array $row) => $row['v'],
                [['id' => 'c', 'v' => 9007199254740992.0], ['id' => 'b', 'v' => 9007199254740993], ['id' => 'a', 'v' => 9007199254740994]],
            ],
            'negative float key -2.0^53 before int key -(2^53 + 1)' => [
                [['id' => 'a', 'v' => -9007199254740992.0], ['id' => 'b', 'v' => -9007199254740993]],
                fn (array $row) => $row['v'],
                [['id' => 'b', 'v' => -9007199254740993], ['id' => 'a', 'v' => -9007199254740992.0]],
            ],
            'float key 2.0^63 before int key PHP_INT_MAX' => [
                [['id' => 'a', 'v' => 9.223372036854776e+18], ['id' => 'b', 'v' => 9223372036854775807]],
                fn (array $row) => $row['v'],
                [['id' => 'b', 'v' => 9223372036854775807], ['id' => 'a', 'v' => 9.223372036854776e+18]],
            ],
            'NAN key after an int key beyond 2^53 and a float key' => [
                [['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 9007199254740992.0]],
                fn (array $row) => $row['v'],
                [['id' => 'c', 'v' => 9007199254740992.0], ['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => \NAN]],
            ],
        ];
    }
}

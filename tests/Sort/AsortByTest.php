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
        $this->assertSame($expected, $result);
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
        $this->assertSame($expected, $result);
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
        $this->assertSame($expected, $result);
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
        $this->assertSame($expected, $result);
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

    /**
     * @test         asortBy (Array) with NAN keys sorted last
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
        foreach (Sort::asortBy($data, $keyFn) as $key => $item) {
            $result[$key] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         asortBy (Generator) with NAN keys sorted last
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
        foreach (Sort::asortBy($generator, $keyFn) as $key => $item) {
            $result[$key] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         asortBy (Iterator) with NAN keys sorted last
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
        foreach (Sort::asortBy($iterator, $keyFn) as $key => $item) {
            $result[$key] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         asortBy (IteratorAggregate) with NAN keys sorted last
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
        foreach (Sort::asortBy($traversable, $keyFn) as $key => $item) {
            $result[$key] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(enumerate(rows), key=lambda kv: (isnan(kv[1]['v']), 0 if isnan(kv[1]['v']) else kv[1]['v']))
     */
    public static function dataProviderForNanKeySortsLast(): array
    {
        return [
            'NAN keys last in input order' => [
                [['id' => 'a', 'v' => 2], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 1], ['id' => 'd', 'v' => \NAN], ['id' => 'e', 'v' => 3]],
                fn (array $row) => $row['v'],
                [2 => ['id' => 'c', 'v' => 1], 0 => ['id' => 'a', 'v' => 2], 4 => ['id' => 'e', 'v' => 3], 1 => ['id' => 'b', 'v' => \NAN], 3 => ['id' => 'd', 'v' => \NAN]],
            ],
            'NAN key in the middle' => [
                [['id' => 'a', 'v' => 2], ['id' => 'b', 'v' => 4], ['id' => 'c', 'v' => \NAN], ['id' => 'd', 'v' => 5], ['id' => 'e', 'v' => 6], ['id' => 'f', 'v' => 1], ['id' => 'g', 'v' => 3]],
                fn (array $row) => $row['v'],
                [5 => ['id' => 'f', 'v' => 1], 0 => ['id' => 'a', 'v' => 2], 6 => ['id' => 'g', 'v' => 3], 1 => ['id' => 'b', 'v' => 4], 3 => ['id' => 'd', 'v' => 5], 4 => ['id' => 'e', 'v' => 6], 2 => ['id' => 'c', 'v' => \NAN]],
            ],
            'only NAN keys' => [
                [['id' => 'a', 'v' => \NAN], ['id' => 'b', 'v' => \NAN]],
                fn (array $row) => $row['v'],
                [['id' => 'a', 'v' => \NAN], ['id' => 'b', 'v' => \NAN]],
            ],
            'NAN key after INF' => [
                [['id' => 'a', 'v' => \INF], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => -\INF]],
                fn (array $row) => $row['v'],
                [2 => ['id' => 'c', 'v' => -\INF], 0 => ['id' => 'a', 'v' => \INF], 1 => ['id' => 'b', 'v' => \NAN]],
            ],
        ];
    }

    /**
     * @test         asortBy (Array) of int and float keys beyond 2^53, which may compare equal as floats
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
        foreach (Sort::asortBy($data, $keyFn) as $key => $item) {
            $result[$key] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         asortBy (Generator) of int and float keys beyond 2^53, which may compare equal as floats
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
        foreach (Sort::asortBy($generator, $keyFn) as $key => $item) {
            $result[$key] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         asortBy (Iterator) of int and float keys beyond 2^53, which may compare equal as floats
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
        foreach (Sort::asortBy($iterator, $keyFn) as $key => $item) {
            $result[$key] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * @test         asortBy (IteratorAggregate) of int and float keys beyond 2^53, which may compare equal as floats
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
        foreach (Sort::asortBy($traversable, $keyFn) as $key => $item) {
            $result[$key] = $item;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(enumerate(rows), key=lambda kv: (isnan(kv[1]['v']), 0 if isnan(kv[1]['v']) else kv[1]['v']))
     */
    public static function dataProviderForIntAndFloatKeyBeyondFloatPrecision(): array
    {
        return [
            'int key 2^53 + 1 before float key 2.0^53, equal as floats' => [
                [['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => 9007199254740992.0]],
                fn (array $row) => $row['v'],
                [1 => ['id' => 'b', 'v' => 9007199254740992.0], 0 => ['id' => 'a', 'v' => 9007199254740993]],
            ],
            'float key 2.0^53 after int keys that equal it as floats' => [
                [['id' => 'a', 'v' => 9007199254740994], ['id' => 'b', 'v' => 9007199254740993], ['id' => 'c', 'v' => 9007199254740992.0]],
                fn (array $row) => $row['v'],
                [2 => ['id' => 'c', 'v' => 9007199254740992.0], 1 => ['id' => 'b', 'v' => 9007199254740993], 0 => ['id' => 'a', 'v' => 9007199254740994]],
            ],
            'negative float key -2.0^53 before int key -(2^53 + 1)' => [
                [['id' => 'a', 'v' => -9007199254740992.0], ['id' => 'b', 'v' => -9007199254740993]],
                fn (array $row) => $row['v'],
                [1 => ['id' => 'b', 'v' => -9007199254740993], 0 => ['id' => 'a', 'v' => -9007199254740992.0]],
            ],
            'float key 2.0^63 before int key PHP_INT_MAX' => [
                [['id' => 'a', 'v' => 9.223372036854776e+18], ['id' => 'b', 'v' => 9223372036854775807]],
                fn (array $row) => $row['v'],
                [1 => ['id' => 'b', 'v' => 9223372036854775807], 0 => ['id' => 'a', 'v' => 9.223372036854776e+18]],
            ],
            'NAN key after an int key beyond 2^53 and a float key' => [
                [['id' => 'a', 'v' => 9007199254740993], ['id' => 'b', 'v' => \NAN], ['id' => 'c', 'v' => 9007199254740992.0]],
                fn (array $row) => $row['v'],
                [2 => ['id' => 'c', 'v' => 9007199254740992.0], 0 => ['id' => 'a', 'v' => 9007199254740993], 1 => ['id' => 'b', 'v' => \NAN]],
            ],
        ];
    }
}

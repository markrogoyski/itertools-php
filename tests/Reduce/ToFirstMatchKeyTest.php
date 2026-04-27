<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToFirstMatchKeyTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test toFirstMatchKey example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $users = ['alice' => 12, 'bob' => 17, 'carol' => 22, 'dan' => 30];

        // When
        $firstAdultKey = Reduce::toFirstMatchKey($users, fn (int $age): bool => $age >= 18);

        // Then
        $this->assertSame('carol', $firstAdultKey);
    }

    /**
     * @test         toFirstMatchKey returns key of first match (array, list-shape)
     * @dataProvider dataProviderForListMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $expected
     */
    public function testListMatchArray(array $data, callable $predicate, mixed $expected): void
    {
        // When
        $result = Reduce::toFirstMatchKey($data, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatchKey returns key of first match (Generator, list-shape)
     * @dataProvider dataProviderForListMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $expected
     */
    public function testListMatchGenerator(array $data, callable $predicate, mixed $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toFirstMatchKey($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatchKey returns key of first match (Iterator, list-shape)
     * @dataProvider dataProviderForListMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $expected
     */
    public function testListMatchIterator(array $data, callable $predicate, mixed $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toFirstMatchKey($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatchKey returns key of first match (IteratorAggregate, list-shape)
     * @dataProvider dataProviderForListMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $expected
     */
    public function testListMatchIteratorAggregate(array $data, callable $predicate, mixed $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toFirstMatchKey($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForListMatch(): array
    {
        $isEven   = fn (int $n): bool => $n % 2 === 0;
        $isOver25 = fn (int $n): bool => $n > 25;
        $isString = fn (mixed $v): bool => \is_string($v);

        return [
            // first element matches → key 0
            [[2, 4, 6], $isEven, 0],
            // last element matches → key 3
            [[1, 3, 5, 8], $isEven, 3],
            // middle element matches → key 2
            [[1, 3, 6, 7, 9], $isEven, 2],
            // single element matches
            [[4], $isEven, 0],
            // canonical example
            [[10, 20, 30, 40], $isOver25, 2],
            // string among mixed types
            [[1, 2.5, 'hello', true], $isString, 2],
        ];
    }

    /**
     * @test         toFirstMatchKey returns default when no element matches (array)
     * @dataProvider dataProviderForNoMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $default
     * @param        mixed        $expected
     */
    public function testNoMatchArray(array $data, callable $predicate, mixed $default, mixed $expected): void
    {
        // When
        $result = Reduce::toFirstMatchKey($data, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatchKey returns default when no element matches (Generator)
     * @dataProvider dataProviderForNoMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $default
     * @param        mixed        $expected
     */
    public function testNoMatchGenerator(array $data, callable $predicate, mixed $default, mixed $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toFirstMatchKey($iterable, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatchKey returns default when no element matches (Iterator)
     * @dataProvider dataProviderForNoMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $default
     * @param        mixed        $expected
     */
    public function testNoMatchIterator(array $data, callable $predicate, mixed $default, mixed $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toFirstMatchKey($iterable, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatchKey returns default when no element matches (IteratorAggregate)
     * @dataProvider dataProviderForNoMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $default
     * @param        mixed        $expected
     */
    public function testNoMatchIteratorAggregate(array $data, callable $predicate, mixed $default, mixed $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toFirstMatchKey($iterable, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForNoMatch(): array
    {
        $isEven     = fn (int $n): bool => $n % 2 === 0;
        $isNegative = fn (int $n): bool => $n < 0;

        return [
            // no match - null default (implicit)
            [[1, 3, 5, 7], $isEven, null, null],
            // no match - custom int default
            [[1, 3, 5, 7], $isEven, -1, -1],
            // no match - string default
            [[1, 2, 3], $isNegative, 'none', 'none'],
            // no match - false default (distinguishable from a matched key of false-equivalents)
            [[1, 2, 3], $isNegative, false, false],
        ];
    }

    /**
     * @test         toFirstMatchKey returns null on empty iterable when default omitted
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableReturnsNullDefault(iterable $data): void
    {
        // Given
        $alwaysTrue = fn (): bool => true;

        // When
        $result = Reduce::toFirstMatchKey($data, $alwaysTrue);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test         toFirstMatchKey returns custom default on empty iterable
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableReturnsCustomDefault(iterable $data): void
    {
        // Given
        $alwaysTrue = fn (): bool => true;

        // When
        $result = Reduce::toFirstMatchKey($data, $alwaysTrue, 'missing');

        // Then
        $this->assertSame('missing', $result);
    }

    /**
     * @test toFirstMatchKey coerces predicate return via (bool) cast
     */
    public function testPredicateCoercedViaBoolCast(): void
    {
        // Given
        $data = [0, 0, 1, 0];

        // When
        // Predicate returns the int itself; first truthy is at key 2.
        $result = Reduce::toFirstMatchKey($data, fn (int $n): int => $n);

        // Then
        $this->assertSame(2, $result);
    }

    /**
     * @test toFirstMatchKey short-circuits without exhausting the iterator
     */
    public function testShortCircuitsOnFirstMatch(): void
    {
        // Given
        $yielded   = [];
        $generator = (function () use (&$yielded): \Generator {
            foreach ([10, 20, 30, 40, 50] as $n) {
                $yielded[] = $n;
                yield $n;
            }
        })();

        // When
        $result = Reduce::toFirstMatchKey($generator, fn (int $n): bool => $n === 30);

        // Then
        $this->assertSame(2, $result);
        $this->assertSame([10, 20, 30], $yielded);
    }

    /**
     * @test toFirstMatchKey short-circuits before items that would throw
     */
    public function testShortCircuitsBeforeThrowingItem(): void
    {
        // Given a generator that throws after the matching element
        $generator = (function (): \Generator {
            yield 'a' => 1;
            yield 'b' => 2;
            throw new \RuntimeException('iterator advanced past match');
        })();

        // When
        $result = Reduce::toFirstMatchKey($generator, fn (int $n): bool => $n === 2);

        // Then
        $this->assertSame('b', $result);
    }

    /**
     * @test toFirstMatchKey returns string key for associative array input
     */
    public function testAssociativeArrayKey(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 3];

        // When
        $result = Reduce::toFirstMatchKey($data, fn (int $n): bool => $n > 1);

        // Then
        $this->assertSame('b', $result);
    }

    /**
     * @test toFirstMatchKey returns string key for associative key-value Generator
     */
    public function testAssociativeKeyValueGeneratorKey(): void
    {
        // Given
        $iterable = GeneratorFixture::getKeyValueGenerator(['a' => 1, 'b' => 2, 'c' => 3]);

        // When
        $result = Reduce::toFirstMatchKey($iterable, fn (int $n): bool => $n > 1);

        // Then
        $this->assertSame('b', $result);
    }

    /**
     * @test toFirstMatchKey returns string key for associative IteratorAggregate input
     */
    public function testAssociativeIteratorAggregateKey(): void
    {
        // Given
        $iterable = new IteratorAggregateFixture(['a' => 1, 'b' => 2, 'c' => 3]);

        // When
        $result = Reduce::toFirstMatchKey($iterable, fn (int $n): bool => $n > 1);

        // Then
        $this->assertSame('b', $result);
    }

    /**
     * @test toFirstMatchKey returns explicit default on associative no-match
     */
    public function testAssociativeNoMatchExplicitDefault(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 3];

        // When
        $result = Reduce::toFirstMatchKey($data, fn (int $n): bool => $n > 100, 'missing');

        // Then
        $this->assertSame('missing', $result);
    }
}

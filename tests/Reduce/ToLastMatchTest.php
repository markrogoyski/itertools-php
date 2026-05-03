<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToLastMatchTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test toLastMatch example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $numbers = [1, 3, 5, 6, 7, 8];

        // When
        $lastEven = Reduce::toLastMatch($numbers, fn (int $n): bool => $n % 2 === 0);

        // Then
        $this->assertSame(8, $lastEven);
    }

    /**
     * @test         toLastMatch returns last match (array)
     * @dataProvider dataProviderForMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $expected
     */
    public function testMatchArray(array $data, callable $predicate, mixed $expected): void
    {
        // When
        $result = Reduce::toLastMatch($data, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toLastMatch returns last match (Generator)
     * @dataProvider dataProviderForMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $expected
     */
    public function testMatchGenerator(array $data, callable $predicate, mixed $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toLastMatch($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toLastMatch returns last match (Iterator)
     * @dataProvider dataProviderForMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $expected
     */
    public function testMatchIterator(array $data, callable $predicate, mixed $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toLastMatch($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toLastMatch returns last match (IteratorAggregate)
     * @dataProvider dataProviderForMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $expected
     */
    public function testMatchIteratorAggregate(array $data, callable $predicate, mixed $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toLastMatch($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForMatch(): array
    {
        $isEven     = fn (int $n): bool => $n % 2 === 0;
        $isPositive = fn (int $n): bool => $n > 0;
        $isString   = fn (mixed $v): bool => \is_string($v);
        $truthy     = fn (mixed $v): bool => (bool) $v;

        return [
            // last element matches
            [[2, 4, 6], $isEven, 6],
            // last even is final element
            [[1, 3, 5, 8], $isEven, 8],
            // last even comes before non-matching tail
            [[1, 3, 6, 7, 9], $isEven, 6],
            // single element matches
            [[4], $isEven, 4],
            // multiple matches: pick the last
            [[2, 4, 1, 6, 3], $isEven, 6],
            // positive number in mixed list - last positive
            [[-2, -1, 0, 1, 2, -3], $isPositive, 2],
            // string among mixed types - last string
            [[1, 'first', 2.5, 'last', true], $isString, 'last'],
            // truthy coercion: last truthy value
            [[0, 'x', null, '0', 'y'], $truthy, 'y'],
            // truthy coercion: last non-empty value
            [[0, '', null, [], 42, 0], $truthy, 42],
        ];
    }

    /**
     * @test         toLastMatch returns default when no element matches (array)
     * @dataProvider dataProviderForNoMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $default
     * @param        mixed        $expected
     */
    public function testNoMatchArray(array $data, callable $predicate, mixed $default, mixed $expected): void
    {
        // When
        $result = Reduce::toLastMatch($data, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toLastMatch returns default when no element matches (Generator)
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
        $result = Reduce::toLastMatch($iterable, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toLastMatch returns default when no element matches (Iterator)
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
        $result = Reduce::toLastMatch($iterable, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toLastMatch returns default when no element matches (IteratorAggregate)
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
        $result = Reduce::toLastMatch($iterable, $predicate, $default);

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
            // no match - custom default
            [[1, 3, 5, 7], $isEven, -1, -1],
            // no match - string default
            [[1, 2, 3], $isNegative, 'none', 'none'],
            // no match - array default
            [[1, 2, 3], $isNegative, [], []],
            // no match - false default (distinguishable from matched false)
            [[1, 2, 3], $isNegative, false, false],
        ];
    }

    /**
     * @test         toLastMatch returns null on empty iterable when default omitted
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableReturnsNullDefault(iterable $data): void
    {
        // Given
        $alwaysTrue = fn (): bool => true;

        // When
        $result = Reduce::toLastMatch($data, $alwaysTrue);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test         toLastMatch returns custom default on empty iterable
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableReturnsCustomDefault(iterable $data): void
    {
        // Given
        $alwaysTrue = fn (): bool => true;

        // When
        $result = Reduce::toLastMatch($data, $alwaysTrue, 'fallback');

        // Then
        $this->assertSame('fallback', $result);
    }

    /**
     * @test toLastMatch distinguishes legitimate null match from default
     */
    public function testNullAsMatchValueVsDefault(): void
    {
        // Given
        $data   = [1, 2, null, 3, null, 4];
        $isNull = fn (mixed $v): bool => $v === null;

        // When
        $resultWithDefault = Reduce::toLastMatch($data, $isNull, 'default');
        $resultNullDefault = Reduce::toLastMatch($data, $isNull);

        // Then
        $this->assertNull($resultWithDefault);
        $this->assertNull($resultNullDefault);
    }

    /**
     * @test toLastMatch consumes the entire iterable
     */
    public function testConsumesEntireIterable(): void
    {
        // Given
        $yielded   = [];
        $generator = (function () use (&$yielded): \Generator {
            foreach ([1, 2, 3, 4, 5] as $n) {
                $yielded[] = $n;
                yield $n;
            }
        })();

        // When
        $result = Reduce::toLastMatch($generator, fn (int $n): bool => $n === 3);

        // Then
        $this->assertSame(3, $result);
        $this->assertSame([1, 2, 3, 4, 5], $yielded);
    }

    /**
     * @test toLastMatch coerces predicate return via (bool) cast
     */
    public function testPredicateCoercedViaBoolCast(): void
    {
        // Given
        $data = [0, 1, 2, 3, 0];

        // When
        // Predicate returns int; values 1+ are truthy; last truthy is 3.
        $result = Reduce::toLastMatch($data, fn (int $n): int => $n);

        // Then
        $this->assertSame(3, $result);
    }
}

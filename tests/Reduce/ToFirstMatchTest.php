<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToFirstMatchTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test toFirstMatch example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $numbers = [1, 3, 5, 6, 7, 8];

        // When
        $firstEven = Reduce::toFirstMatch($numbers, fn (int $n): bool => $n % 2 === 0);

        // Then
        $this->assertSame(6, $firstEven);
    }

    /**
     * @test         toFirstMatch returns first match (array)
     * @dataProvider dataProviderForMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $expected
     */
    public function testMatchArray(array $data, callable $predicate, mixed $expected): void
    {
        // When
        $result = Reduce::toFirstMatch($data, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatch returns first match (Generator)
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
        $result = Reduce::toFirstMatch($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatch returns first match (Iterator)
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
        $result = Reduce::toFirstMatch($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatch returns first match (IteratorAggregate)
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
        $result = Reduce::toFirstMatch($iterable, $predicate);

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
            // first element matches
            [[2, 4, 6], $isEven, 2],
            // last element matches
            [[1, 3, 5, 8], $isEven, 8],
            // middle element matches
            [[1, 3, 6, 7, 9], $isEven, 6],
            // single element matches
            [[4], $isEven, 4],
            // positive number in mixed list
            [[-2, -1, 0, 1, 2], $isPositive, 1],
            // string among mixed types
            [[1, 2.5, 'hello', true], $isString, 'hello'],
            // truthy coercion: '0' is falsy in PHP's (bool) cast, so 'x' is first truthy
            [[0, '', null, '0', 'x'], $truthy, 'x'],
            // truthy coercion: first non-empty value
            [[0, '', null, [], 42], $truthy, 42],
        ];
    }

    /**
     * @test         toFirstMatch returns default when no element matches (array)
     * @dataProvider dataProviderForNoMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $default
     * @param        mixed        $expected
     */
    public function testNoMatchArray(array $data, callable $predicate, mixed $default, mixed $expected): void
    {
        // When
        $result = Reduce::toFirstMatch($data, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatch returns default when no element matches (Generator)
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
        $result = Reduce::toFirstMatch($iterable, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatch returns default when no element matches (Iterator)
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
        $result = Reduce::toFirstMatch($iterable, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatch returns default when no element matches (IteratorAggregate)
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
        $result = Reduce::toFirstMatch($iterable, $predicate, $default);

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
     * @test         toFirstMatch returns null on empty iterable when default omitted
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableReturnsNullDefault(iterable $data): void
    {
        // Given
        $alwaysTrue = fn (): bool => true;

        // When
        $result = Reduce::toFirstMatch($data, $alwaysTrue);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test         toFirstMatch returns custom default on empty iterable
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableReturnsCustomDefault(iterable $data): void
    {
        // Given
        $alwaysTrue = fn (): bool => true;

        // When
        $result = Reduce::toFirstMatch($data, $alwaysTrue, 'fallback');

        // Then
        $this->assertSame('fallback', $result);
    }

    /**
     * @test toFirstMatch distinguishes legitimate null match from default
     */
    public function testNullAsMatchValueVsDefault(): void
    {
        // Given
        $data      = [1, 2, null, 3];
        $isNull    = fn (mixed $v): bool => $v === null;

        // When
        $resultWithDefault = Reduce::toFirstMatch($data, $isNull, 'default');
        $resultNullDefault = Reduce::toFirstMatch($data, $isNull);

        // Then
        $this->assertNull($resultWithDefault);
        $this->assertNull($resultNullDefault);
    }

    /**
     * @test toFirstMatch short-circuits without exhausting the iterator
     */
    public function testShortCircuitsOnFirstMatch(): void
    {
        // Given
        $yielded = [];
        $generator = (function () use (&$yielded): \Generator {
            foreach ([1, 2, 3, 4, 5] as $n) {
                $yielded[] = $n;
                yield $n;
            }
        })();

        // When
        $result = Reduce::toFirstMatch($generator, fn (int $n): bool => $n === 3);

        // Then
        $this->assertSame(3, $result);
        $this->assertSame([1, 2, 3], $yielded);
    }

    /**
     * @test toFirstMatch coerces predicate return via (bool) cast
     */
    public function testPredicateCoercedViaBoolCast(): void
    {
        // Given
        $data = [0, 1, 2, 3];

        // When
        // Predicate returns int; values 1+ are truthy; first truthy result is 1.
        $result = Reduce::toFirstMatch($data, fn (int $n): int => $n);

        // Then
        $this->assertSame(1, $result);
    }
}

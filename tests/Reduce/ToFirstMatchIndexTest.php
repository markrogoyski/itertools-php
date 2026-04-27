<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToFirstMatchIndexTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test toFirstMatchIndex example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $numbers = [10, 20, 30, 40];

        // When
        $index = Reduce::toFirstMatchIndex($numbers, fn (int $n): bool => $n > 25);

        // Then
        $this->assertSame(2, $index);
    }

    /**
     * @test         toFirstMatchIndex returns first match position (array)
     * @dataProvider dataProviderForMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $expected
     */
    public function testMatchArray(array $data, callable $predicate, mixed $expected): void
    {
        // When
        $result = Reduce::toFirstMatchIndex($data, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatchIndex returns first match position (Generator)
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
        $result = Reduce::toFirstMatchIndex($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatchIndex returns first match position (Iterator)
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
        $result = Reduce::toFirstMatchIndex($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatchIndex returns first match position (IteratorAggregate)
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
        $result = Reduce::toFirstMatchIndex($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForMatch(): array
    {
        $isEven      = fn (int $n): bool => $n % 2 === 0;
        $isOver25    = fn (int $n): bool => $n > 25;
        $isString    = fn (mixed $v): bool => \is_string($v);

        return [
            // first element matches → index 0
            [[2, 4, 6], $isEven, 0],
            // last element matches → index 3
            [[1, 3, 5, 8], $isEven, 3],
            // middle element matches → index 2
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
     * @test         toFirstMatchIndex returns default when no element matches (array)
     * @dataProvider dataProviderForNoMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $default
     * @param        mixed        $expected
     */
    public function testNoMatchArray(array $data, callable $predicate, mixed $default, mixed $expected): void
    {
        // When
        $result = Reduce::toFirstMatchIndex($data, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatchIndex returns default when no element matches (Generator)
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
        $result = Reduce::toFirstMatchIndex($iterable, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatchIndex returns default when no element matches (Iterator)
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
        $result = Reduce::toFirstMatchIndex($iterable, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toFirstMatchIndex returns default when no element matches (IteratorAggregate)
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
        $result = Reduce::toFirstMatchIndex($iterable, $predicate, $default);

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
            // no match - false default (distinguishable from a matched index of 0)
            [[1, 2, 3], $isNegative, false, false],
        ];
    }

    /**
     * @test         toFirstMatchIndex returns null on empty iterable when default omitted
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableReturnsNullDefault(iterable $data): void
    {
        // Given
        $alwaysTrue = fn (): bool => true;

        // When
        $result = Reduce::toFirstMatchIndex($data, $alwaysTrue);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test         toFirstMatchIndex returns custom default on empty iterable
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableReturnsCustomDefault(iterable $data): void
    {
        // Given
        $alwaysTrue = fn (): bool => true;

        // When
        $result = Reduce::toFirstMatchIndex($data, $alwaysTrue, -1);

        // Then
        $this->assertSame(-1, $result);
    }

    /**
     * @test toFirstMatchIndex coerces predicate return via (bool) cast
     */
    public function testPredicateCoercedViaBoolCast(): void
    {
        // Given
        $data = [0, 0, 1, 0];

        // When
        // Predicate returns the int itself; first truthy is at index 2.
        $result = Reduce::toFirstMatchIndex($data, fn (int $n): int => $n);

        // Then
        $this->assertSame(2, $result);
    }

    /**
     * @test toFirstMatchIndex coerces non-bool truthy values (string 'x', array [0])
     */
    public function testPredicateCoercedTruthyValues(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $resultString = Reduce::toFirstMatchIndex($data, fn (int $n): string => $n === 2 ? 'x' : '');
        $resultArray  = Reduce::toFirstMatchIndex($data, fn (int $n): array => $n === 3 ? [0] : []);

        // Then
        $this->assertSame(1, $resultString);
        $this->assertSame(2, $resultArray);
    }

    /**
     * @test toFirstMatchIndex short-circuits without exhausting the iterator
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
        $result = Reduce::toFirstMatchIndex($generator, fn (int $n): bool => $n === 30);

        // Then
        $this->assertSame(2, $result);
        $this->assertSame([10, 20, 30], $yielded);
    }

    /**
     * @test toFirstMatchIndex short-circuits before items that would throw
     */
    public function testShortCircuitsBeforeThrowingItem(): void
    {
        // Given a generator that throws after the matching element
        $generator = (function (): \Generator {
            yield 1;
            yield 2;
            throw new \RuntimeException('iterator advanced past match');
        })();

        // When
        $result = Reduce::toFirstMatchIndex($generator, fn (int $n): bool => $n === 2);

        // Then
        $this->assertSame(1, $result);
    }

    /**
     * @test toFirstMatchIndex returns position (not key) for associative array input
     */
    public function testAssociativeArrayPosition(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 3];

        // When
        $result = Reduce::toFirstMatchIndex($data, fn (int $n): bool => $n > 1);

        // Then
        $this->assertSame(1, $result);
    }

    /**
     * @test toFirstMatchIndex returns position for associative key-value Generator input
     */
    public function testAssociativeKeyValueGeneratorPosition(): void
    {
        // Given
        $data     = ['a' => 1, 'b' => 2, 'c' => 3];
        $iterable = GeneratorFixture::getKeyValueGenerator($data);

        // When
        $result = Reduce::toFirstMatchIndex($iterable, fn (int $n): bool => $n > 1);

        // Then
        $this->assertSame(1, $result);
    }

    /**
     * @test toFirstMatchIndex returns position for associative IteratorAggregate input
     */
    public function testAssociativeIteratorAggregatePosition(): void
    {
        // Given
        $iterable = new IteratorAggregateFixture(['a' => 1, 'b' => 2, 'c' => 3]);

        // When
        $result = Reduce::toFirstMatchIndex($iterable, fn (int $n): bool => $n > 1);

        // Then
        $this->assertSame(1, $result);
    }

    /**
     * @test toFirstMatchIndex returns explicit default on associative no-match
     */
    public function testAssociativeNoMatchExplicitDefault(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 3];

        // When
        $result = Reduce::toFirstMatchIndex($data, fn (int $n): bool => $n > 100, -1);

        // Then
        $this->assertSame(-1, $result);
    }
}

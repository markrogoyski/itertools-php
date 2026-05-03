<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToLastMatchIndexTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test toLastMatchIndex example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $numbers = [10, 20, 30, 40];

        // When
        $index = Reduce::toLastMatchIndex($numbers, fn (int $n): bool => $n > 15);

        // Then
        $this->assertSame(3, $index);
    }

    /**
     * @test         toLastMatchIndex returns last match position (array)
     * @dataProvider dataProviderForMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $expected
     */
    public function testMatchArray(array $data, callable $predicate, mixed $expected): void
    {
        // When
        $result = Reduce::toLastMatchIndex($data, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toLastMatchIndex returns last match position (Generator)
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
        $result = Reduce::toLastMatchIndex($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toLastMatchIndex returns last match position (Iterator)
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
        $result = Reduce::toLastMatchIndex($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toLastMatchIndex returns last match position (IteratorAggregate)
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
        $result = Reduce::toLastMatchIndex($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForMatch(): array
    {
        $isEven   = fn (int $n): bool => $n % 2 === 0;
        $isOver15 = fn (int $n): bool => $n > 15;
        $isString = fn (mixed $v): bool => \is_string($v);

        return [
            // last element matches → last index
            [[2, 4, 6], $isEven, 2],
            // last even is final element
            [[1, 3, 5, 8], $isEven, 3],
            // last even comes before non-matching tail
            [[1, 3, 6, 7, 9], $isEven, 2],
            // single element matches
            [[4], $isEven, 0],
            // multiple matches: pick last index
            [[2, 4, 1, 6, 3], $isEven, 3],
            // canonical example
            [[10, 20, 30, 40], $isOver15, 3],
            // last string in mixed types
            [[1, 'first', 2.5, 'last', true], $isString, 3],
        ];
    }

    /**
     * @test         toLastMatchIndex returns default when no element matches (array)
     * @dataProvider dataProviderForNoMatch
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        mixed        $default
     * @param        mixed        $expected
     */
    public function testNoMatchArray(array $data, callable $predicate, mixed $default, mixed $expected): void
    {
        // When
        $result = Reduce::toLastMatchIndex($data, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toLastMatchIndex returns default when no element matches (Generator)
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
        $result = Reduce::toLastMatchIndex($iterable, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toLastMatchIndex returns default when no element matches (Iterator)
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
        $result = Reduce::toLastMatchIndex($iterable, $predicate, $default);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toLastMatchIndex returns default when no element matches (IteratorAggregate)
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
        $result = Reduce::toLastMatchIndex($iterable, $predicate, $default);

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
            // no match - false default (distinguishable from matched index of 0)
            [[1, 2, 3], $isNegative, false, false],
        ];
    }

    /**
     * @test         toLastMatchIndex returns null on empty iterable when default omitted
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableReturnsNullDefault(iterable $data): void
    {
        // Given
        $alwaysTrue = fn (): bool => true;

        // When
        $result = Reduce::toLastMatchIndex($data, $alwaysTrue);

        // Then
        $this->assertNull($result);
    }

    /**
     * @test         toLastMatchIndex returns custom default on empty iterable
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableReturnsCustomDefault(iterable $data): void
    {
        // Given
        $alwaysTrue = fn (): bool => true;

        // When
        $result = Reduce::toLastMatchIndex($data, $alwaysTrue, -1);

        // Then
        $this->assertSame(-1, $result);
    }

    /**
     * @test toLastMatchIndex coerces predicate return via (bool) cast
     */
    public function testPredicateCoercedViaBoolCast(): void
    {
        // Given
        $data = [0, 0, 1, 0, 1, 0];

        // When
        // Predicate returns int; last truthy index is 4.
        $result = Reduce::toLastMatchIndex($data, fn (int $n): int => $n);

        // Then
        $this->assertSame(4, $result);
    }

    /**
     * @test toLastMatchIndex consumes the entire iterable
     */
    public function testConsumesEntireIterable(): void
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
        $result = Reduce::toLastMatchIndex($generator, fn (int $n): bool => $n === 30);

        // Then
        $this->assertSame(2, $result);
        $this->assertSame([10, 20, 30, 40, 50], $yielded);
    }

    /**
     * @test toLastMatchIndex returns position (not key) for associative array input
     */
    public function testAssociativeArrayPosition(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 0];

        // When
        $result = Reduce::toLastMatchIndex($data, fn (int $n): bool => $n > 0);

        // Then
        $this->assertSame(2, $result);
    }

    /**
     * @test toLastMatchIndex returns position for associative key-value Generator input
     */
    public function testAssociativeKeyValueGeneratorPosition(): void
    {
        // Given
        $data     = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 0];
        $iterable = GeneratorFixture::getKeyValueGenerator($data);

        // When
        $result = Reduce::toLastMatchIndex($iterable, fn (int $n): bool => $n > 0);

        // Then
        $this->assertSame(2, $result);
    }

    /**
     * @test toLastMatchIndex returns position for associative IteratorAggregate input
     */
    public function testAssociativeIteratorAggregatePosition(): void
    {
        // Given
        $iterable = new IteratorAggregateFixture(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 0]);

        // When
        $result = Reduce::toLastMatchIndex($iterable, fn (int $n): bool => $n > 0);

        // Then
        $this->assertSame(2, $result);
    }

    /**
     * @test toLastMatchIndex returns explicit default on associative no-match
     */
    public function testAssociativeNoMatchExplicitDefault(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 3];

        // When
        $result = Reduce::toLastMatchIndex($data, fn (int $n): bool => $n > 100, -1);

        // Then
        $this->assertSame(-1, $result);
    }
}

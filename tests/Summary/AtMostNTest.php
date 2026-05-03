<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Infinite;
use IterTools\Summary;
use IterTools\Tests\Fixture;

class AtMostNTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /**
     * @test         atMostN - empty iterable, default predicate, n=0
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableNZeroDefaultPredicate(iterable $data): void
    {
        // When
        $result = Summary::atMostN($data, 0);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         atMostN - empty iterable, default predicate, n=5
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableNFiveDefaultPredicate(iterable $data): void
    {
        // When
        $result = Summary::atMostN($data, 5);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         atMostN array - default predicate true cases
     * @dataProvider dataProviderForDefaultPredicateTrue
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateTrueArray(array $input, int $n): void
    {
        // When
        $result = Summary::atMostN($input, $n);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         atMostN generator - default predicate true cases
     * @dataProvider dataProviderForDefaultPredicateTrue
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateTrueGenerator(array $input, int $n): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($input);

        // When
        $result = Summary::atMostN($data, $n);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         atMostN iterator - default predicate true cases
     * @dataProvider dataProviderForDefaultPredicateTrue
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateTrueIterator(array $input, int $n): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($input);

        // When
        $result = Summary::atMostN($data, $n);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         atMostN traversable - default predicate true cases
     * @dataProvider dataProviderForDefaultPredicateTrue
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateTrueTraversable(array $input, int $n): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($input);

        // When
        $result = Summary::atMostN($data, $n);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForDefaultPredicateTrue(): array
    {
        return [
            // boundary: count equals n
            [[1], 1],
            [[1, 2], 2],
            [[1, 2, 3], 3],
            // count below n
            [[1], 5],
            [[1, 2], 10],
            // n = 0 with all-falsy data — zero matches under default predicate
            [[0, 0, 0], 0],
            [[false, false], 0],
            [[null, null], 0],
            // empty under any non-negative n
            [[], 0],
            [[], 1],
            [[], 100],
            // mixed truthy/falsy
            [[0, 1, 0], 1],
            [[0, 0, 1, 1, 0], 2],
        ];
    }

    /**
     * @test         atMostN array - default predicate false cases
     * @dataProvider dataProviderForDefaultPredicateFalse
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateFalseArray(array $input, int $n): void
    {
        // When
        $result = Summary::atMostN($input, $n);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         atMostN generator - default predicate false cases
     * @dataProvider dataProviderForDefaultPredicateFalse
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateFalseGenerator(array $input, int $n): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($input);

        // When
        $result = Summary::atMostN($data, $n);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         atMostN iterator - default predicate false cases
     * @dataProvider dataProviderForDefaultPredicateFalse
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateFalseIterator(array $input, int $n): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($input);

        // When
        $result = Summary::atMostN($data, $n);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         atMostN traversable - default predicate false cases
     * @dataProvider dataProviderForDefaultPredicateFalse
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateFalseTraversable(array $input, int $n): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($input);

        // When
        $result = Summary::atMostN($data, $n);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForDefaultPredicateFalse(): array
    {
        return [
            // count exceeds n
            [[1], 0],
            [[1, 2], 1],
            [[1, 2, 3], 2],
            [[1, 1, 1, 1, 1], 3],
            // n negative always false (mirrors exactlyN(-1))
            [[], -1],
            [[1], -1],
            [[], -5],
        ];
    }

    /**
     * @test         atMostN array - with predicate true cases
     * @dataProvider dataProviderForPredicateTrue
     * @param        array<mixed> $input
     * @param        int          $n
     * @param        callable     $predicate
     */
    public function testPredicateTrueArray(array $input, int $n, callable $predicate): void
    {
        // When
        $result = Summary::atMostN($input, $n, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         atMostN generator - with predicate true cases
     * @dataProvider dataProviderForPredicateTrue
     * @param        array<mixed> $input
     * @param        int          $n
     * @param        callable     $predicate
     */
    public function testPredicateTrueGenerator(array $input, int $n, callable $predicate): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($input);

        // When
        $result = Summary::atMostN($data, $n, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         atMostN iterator - with predicate true cases
     * @dataProvider dataProviderForPredicateTrue
     * @param        array<mixed> $input
     * @param        int          $n
     * @param        callable     $predicate
     */
    public function testPredicateTrueIterator(array $input, int $n, callable $predicate): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($input);

        // When
        $result = Summary::atMostN($data, $n, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         atMostN traversable - with predicate true cases
     * @dataProvider dataProviderForPredicateTrue
     * @param        array<mixed> $input
     * @param        int          $n
     * @param        callable     $predicate
     */
    public function testPredicateTrueTraversable(array $input, int $n, callable $predicate): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($input);

        // When
        $result = Summary::atMostN($data, $n, $predicate);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForPredicateTrue(): array
    {
        return [
            [[1, 2, 3, 4, 5], 3, fn ($x) => $x >= 3],
            [[1, 2, 3, 4, 5], 5, fn ($x) => true],
            [[1, 2, 3, 4, 5], 0, fn ($x) => false],
            [[1, 1, 1, 2, 2], 3, fn ($x) => $x === 1],
            [[1, 2, 3, 4, 5], 10, fn ($x) => true],
        ];
    }

    /**
     * @test         atMostN array - with predicate false cases
     * @dataProvider dataProviderForPredicateFalse
     * @param        array<mixed> $input
     * @param        int          $n
     * @param        callable     $predicate
     */
    public function testPredicateFalseArray(array $input, int $n, callable $predicate): void
    {
        // When
        $result = Summary::atMostN($input, $n, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         atMostN generator - with predicate false cases
     * @dataProvider dataProviderForPredicateFalse
     * @param        array<mixed> $input
     * @param        int          $n
     * @param        callable     $predicate
     */
    public function testPredicateFalseGenerator(array $input, int $n, callable $predicate): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($input);

        // When
        $result = Summary::atMostN($data, $n, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         atMostN iterator - with predicate false cases
     * @dataProvider dataProviderForPredicateFalse
     * @param        array<mixed> $input
     * @param        int          $n
     * @param        callable     $predicate
     */
    public function testPredicateFalseIterator(array $input, int $n, callable $predicate): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($input);

        // When
        $result = Summary::atMostN($data, $n, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         atMostN traversable - with predicate false cases
     * @dataProvider dataProviderForPredicateFalse
     * @param        array<mixed> $input
     * @param        int          $n
     * @param        callable     $predicate
     */
    public function testPredicateFalseTraversable(array $input, int $n, callable $predicate): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($input);

        // When
        $result = Summary::atMostN($data, $n, $predicate);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForPredicateFalse(): array
    {
        return [
            [[1, 2, 3, 4, 5], 2, fn ($x) => $x >= 3],
            [[1, 2, 3, 4, 5], 4, fn ($x) => true],
            [[1, 1, 1, 2, 2], 2, fn ($x) => $x === 1],
        ];
    }

    /**
     * @test atMostN early-terminates as soon as count exceeds n.
     */
    public function testEarlyTerminationWithInfiniteIterable(): void
    {
        // Given
        $infinite = Infinite::repeat(true);

        // When
        $result = Summary::atMostN($infinite, 100);

        // Then
        $this->assertFalse($result);
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Infinite;
use IterTools\Summary;
use IterTools\Tests\Fixture;

class AtLeastNTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /**
     * @test         atLeastN - empty iterable, default predicate, n=0
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableNZeroDefaultPredicate(iterable $data): void
    {
        // When
        $result = Summary::atLeastN($data, 0);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test atLeastN - empty array, default predicate, n=1
     */
    public function testEmptyArrayNOneDefaultPredicate(): void
    {
        // Given
        $data = [];

        // When
        $result = Summary::atLeastN($data, 1);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test atLeastN - empty generator, default predicate, n=1
     */
    public function testEmptyGeneratorNOneDefaultPredicate(): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator([]);

        // When
        $result = Summary::atLeastN($data, 1);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test atLeastN - empty iterator, default predicate, n=1
     */
    public function testEmptyIteratorNOneDefaultPredicate(): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture([]);

        // When
        $result = Summary::atLeastN($data, 1);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test atLeastN - empty traversable, default predicate, n=1
     */
    public function testEmptyTraversableNOneDefaultPredicate(): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture([]);

        // When
        $result = Summary::atLeastN($data, 1);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         atLeastN array - default predicate true cases
     * @dataProvider dataProviderForDefaultPredicateTrue
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateTrueArray(array $input, int $n): void
    {
        // Given
        $data = $input;

        // When
        $result = Summary::atLeastN($data, $n);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         atLeastN generator - default predicate true cases
     * @dataProvider dataProviderForDefaultPredicateTrue
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateTrueGenerator(array $input, int $n): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($input);

        // When
        $result = Summary::atLeastN($data, $n);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         atLeastN iterator - default predicate true cases
     * @dataProvider dataProviderForDefaultPredicateTrue
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateTrueIterator(array $input, int $n): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($input);

        // When
        $result = Summary::atLeastN($data, $n);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         atLeastN traversable - default predicate true cases
     * @dataProvider dataProviderForDefaultPredicateTrue
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateTrueTraversable(array $input, int $n): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($input);

        // When
        $result = Summary::atLeastN($data, $n);

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
            // count exceeds n
            [[1, 2, 3], 1],
            [[1, 2, 3], 2],
            [[1, 1, 1, 1, 1], 3],
            // n = 0 always true
            [[], 0],
            [[1], 0],
            [[1, 2, 3], 0],
            // n negative always true
            [[], -1],
            [[1, 2], -5],
            // truthy values count
            [[true, true, true], 3],
            [[1, 2.2, 'a', [5]], 4],
        ];
    }

    /**
     * @test         atLeastN array - default predicate false cases
     * @dataProvider dataProviderForDefaultPredicateFalse
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateFalseArray(array $input, int $n): void
    {
        // Given
        $data = $input;

        // When
        $result = Summary::atLeastN($data, $n);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         atLeastN generator - default predicate false cases
     * @dataProvider dataProviderForDefaultPredicateFalse
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateFalseGenerator(array $input, int $n): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($input);

        // When
        $result = Summary::atLeastN($data, $n);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         atLeastN iterator - default predicate false cases
     * @dataProvider dataProviderForDefaultPredicateFalse
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateFalseIterator(array $input, int $n): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($input);

        // When
        $result = Summary::atLeastN($data, $n);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         atLeastN traversable - default predicate false cases
     * @dataProvider dataProviderForDefaultPredicateFalse
     * @param        array<mixed> $input
     * @param        int          $n
     */
    public function testDefaultPredicateFalseTraversable(array $input, int $n): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($input);

        // When
        $result = Summary::atLeastN($data, $n);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForDefaultPredicateFalse(): array
    {
        return [
            // count below n
            [[], 1],
            [[1], 2],
            [[1, 2], 3],
            [[1, 2, 3], 4],
            // falsy values don't count under default predicate
            [[0, 0, 0], 1],
            [[false, false], 1],
            [[null, null, null], 1],
            // mixed truthy/falsy
            [[0, 1, 0], 2],
        ];
    }

    /**
     * @test         atLeastN array - with predicate true cases
     * @dataProvider dataProviderForPredicateTrue
     * @param        array<mixed> $input
     * @param        int          $n
     * @param        callable     $predicate
     */
    public function testPredicateTrueArray(array $input, int $n, callable $predicate): void
    {
        // When
        $result = Summary::atLeastN($input, $n, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         atLeastN generator - with predicate true cases
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
        $result = Summary::atLeastN($data, $n, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         atLeastN iterator - with predicate true cases
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
        $result = Summary::atLeastN($data, $n, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         atLeastN traversable - with predicate true cases
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
        $result = Summary::atLeastN($data, $n, $predicate);

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
            [[0, 0, 0, 0], 4, fn ($x) => $x === 0],
        ];
    }

    /**
     * @test         atLeastN array - with predicate false cases
     * @dataProvider dataProviderForPredicateFalse
     * @param        array<mixed> $input
     * @param        int          $n
     * @param        callable     $predicate
     */
    public function testPredicateFalseArray(array $input, int $n, callable $predicate): void
    {
        // When
        $result = Summary::atLeastN($input, $n, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         atLeastN generator - with predicate false cases
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
        $result = Summary::atLeastN($data, $n, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         atLeastN iterator - with predicate false cases
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
        $result = Summary::atLeastN($data, $n, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         atLeastN traversable - with predicate false cases
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
        $result = Summary::atLeastN($data, $n, $predicate);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForPredicateFalse(): array
    {
        return [
            [[1, 2, 3, 4, 5], 4, fn ($x) => $x >= 3],
            [[1, 2, 3, 4, 5], 6, fn ($x) => true],
            [[1, 2, 3, 4, 5], 1, fn ($x) => false],
            [[1, 1, 1, 2, 2], 4, fn ($x) => $x === 1],
            [[], 1, fn ($x) => true],
        ];
    }

    /**
     * @test atLeastN early-terminates against an infinite iterable.
     */
    public function testEarlyTerminationWithInfiniteIterable(): void
    {
        // Given
        $infinite = Infinite::repeat(true);

        // When
        $result = Summary::atLeastN($infinite, 100);

        // Then
        $this->assertTrue($result);
    }
}

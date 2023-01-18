<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Infinite;
use IterTools\Summary;
use IterTools\Tests\Fixture;

class ExactlyNTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /**
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptyIterableWithDefaultPredicateZero(iterable $data): void
    {
        // Given
        $n = 0;

        // When
        $result = Summary::exactlyN($data, $n);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @dataProvider dataProviderForNonZero
     * @param        int $n
     */
    public function testEmptyArrayWithDefaultPredicateNonZeroNAlwaysFalse(int $n): void
    {
        // Given
        $data = [];

        // When
        $result = Summary::exactlyN($data, $n);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @dataProvider dataProviderForNonZero
     * @param        int $n
     */
    public function testEmptyGeneratorWithDefaultPredicateNonZeroNAlwaysFalse(int $n): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator([]);

        // When
        $result = Summary::exactlyN($data, $n);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @dataProvider dataProviderForNonZero
     * @param        int $n
     */
    public function testEmptyIteratorWithDefaultPredicateNonZeroNAlwaysFalse(int $n): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture([]);

        // When
        $result = Summary::exactlyN($data, $n);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @dataProvider dataProviderForNonZero
     * @param        int $n
     */
    public function testEmptyTraversableWithDefaultPredicateNonZeroNAlwaysFalse(int $n): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture([]);

        // When
        $result = Summary::exactlyN($data, $n);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForNonZero(): array
    {
        return [
            [1],
            [2],
            [5],
            [10],
            [100],
            [98237498],
        ];
    }

    /**
     * @dataProvider dataProviderForExactlyN
     * @param        array $input
     * @param        int   $n
     */
    public function testArrayWithDefaultPredicateNonZeroNWhenNMatches(array $input, int $n): void
    {
        // Given
        $data = $input;

        // When
        $result = Summary::exactlyN($data, $n);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @dataProvider dataProviderForExactlyN
     * @param        array $input
     * @param        int   $n
     */
    public function testGeneratorWithDefaultPredicateNonZeroNWhenNMatches(array $input, int $n): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($input);

        // When
        $result = Summary::exactlyN($data, $n);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @dataProvider dataProviderForExactlyN
     * @param        array $input
     * @param        int   $n
     */
    public function testIteratorWithDefaultPredicateNonZeroNWhenNMatches(array $input, int $n): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($input);

        // When
        $result = Summary::exactlyN($data, $n);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @dataProvider dataProviderForExactlyN
     * @param        array $input
     * @param        int   $n
     */
    public function testTraversableWithDefaultPredicateNonZeroNWhenNMatches(array $input, int $n): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($input);

        // When
        $result = Summary::exactlyN($data, $n);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForExactlyN(): array
    {
        return [
            [
                [1],
                1,
            ],
            [
                [2],
                1,
            ],
            [
                ['a'],
                1,
            ],
            [
                [[5]],
                1,
            ],
            [
                [new \stdClass()],
                1,
            ],
            [
                [1, 2],
                2,
            ],
            [
                [1, 1, 1],
                3,
            ],
            [
                [1, 2.2, '3', 'four'],
                4,
            ],
            [
                [0],
                0,
            ],
            [
                [0, 0, 0, 34],
                1,
            ],
            [
                [true, true, false],
                2,
            ],
            [
                [false, false, true],
                1,
            ],
            [
                [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
                24,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForExactlyNNotN
     * @param        array $input
     * @param        int   $n
     */
    public function testArrayWithDefaultPredicateNonZeroNWhenNDoesNotMatch(array $input, int $n): void
    {
        // Given
        $data = $input;

        // When
        $result = Summary::exactlyN($data, $n);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @dataProvider dataProviderForExactlyNNotN
     * @param        array $input
     * @param        int   $n
     */
    public function testGeneratorWithDefaultPredicateNonZeroNWhenNDoesNotMatch(array $input, int $n): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($input);

        // When
        $result = Summary::exactlyN($data, $n);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @dataProvider dataProviderForExactlyNNotN
     * @param        array $input
     * @param        int   $n
     */
    public function testIteratorWithDefaultPredicateNonZeroNWhenNDoesNotMatch(array $input, int $n): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($input);

        // When
        $result = Summary::exactlyN($data, $n);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @dataProvider dataProviderForExactlyNNotN
     * @param        array $input
     * @param        int   $n
     */
    public function testTraversableWithDefaultPredicateNonZeroNWhenNDoesNotMatch(array $input, int $n): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($input);

        // When
        $result = Summary::exactlyN($data, $n);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForExactlyNNotN(): array
    {
        return [
            [
                [1],
                0,
            ],
            [
                [1],
                2,
            ],
            [
                ['a'],
                2,
            ],
            [
                [[5]],
                6,
            ],
            [
                [new \stdClass()],
                0,
            ],
            [
                [1, 2],
                1,
            ],
            [
                [1, 2],
                3,
            ],
            [
                [1, 1, 1],
                2,
            ],
            [
                [1, 1, 1],
                4,
            ],
            [
                [1, 2.2, '3', 'four'],
                3,
            ],
            [
                [0],
                1,
            ],
            [
                [0, 0, 0, 34],
                4,
            ],
            [
                [true, true, false],
                3,
            ],
            [
                [false, false, true],
                2,
            ],
            [
                [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
                10,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForExactlyNWithPredicate
     * @param        array $input
     * @param        int   $n
     * @param        callable $predicate
     */
    public function testArrayWithPredicateNonZeroNWhenNMatches(array $input, int $n, callable $predicate): void
    {
        // Given
        $data = $input;

        // When
        $result = Summary::exactlyN($data, $n, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @dataProvider dataProviderForExactlyNWithPredicate
     * @param        array $input
     * @param        int   $n
     * @param        callable $predicate
     */
    public function testGeneratorWithPredicateNonZeroNWhenNMatches(array $input, int $n, callable $predicate): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($input);

        // When
        $result = Summary::exactlyN($data, $n, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @dataProvider dataProviderForExactlyNWithPredicate
     * @param        array $input
     * @param        int   $n
     * @param        callable $predicate
     */
    public function testIteratorWithPredicateNonZeroNWhenNMatches(array $input, int $n, callable $predicate): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($input);

        // When
        $result = Summary::exactlyN($data, $n, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @dataProvider dataProviderForExactlyNWithPredicate
     * @param        array $input
     * @param        int   $n
     * @param        callable $predicate
     */
    public function testTraversableWithPredicateNonZeroNWhenNMatches(array $input, int $n, callable $predicate): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($input);

        // When
        $result = Summary::exactlyN($data, $n, $predicate);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForExactlyNWithPredicate(): array
    {
        return [
            [
                [1],
                1,
                fn ($x) => $x === 1,
            ],
            [
                [1],
                1,
                fn ($x) => $x >= 1,
            ],
            [
                [2],
                1,
                fn ($x) => $x >= 1,
            ],
            [
                ['a'],
                1,
                fn ($x) => is_string($x),
            ],
            [
                [[5]],
                1,
                fn ($x) => is_array($x),
            ],
            [
                [new \stdClass()],
                1,
                fn ($x) => is_object($x),
            ],
            [
                [1, 2],
                2,
                fn ($x) => $x < 3,
            ],
            [
                [1, 1, 1],
                3,
                fn ($x) => $x === 1,
            ],
            [
                [1, 2.2, '3', 'four'],
                4,
                fn ($x) => $x < 3 || is_string($x),
            ],
            [
                [1, 2.2, '3', 'four', 5, ['what'], [2]],
                4,
                fn ($x) => $x < 3 || is_string($x),
            ],
            [
                [1, 2.2, '3', 'four', 5, ['what'], [2], 2.9, -5],
                6,
                fn ($x) => $x < 3 || is_string($x),
            ],
            [
                [0],
                1,
                fn ($x) => $x === 0,
            ],
            [
                [0],
                0,
                fn ($x) => $x !== 0,
            ],
            [
                [0, 0, 0, 34],
                1,
                fn ($x) => $x > 0,
            ],
            [
                [1, 1, 1, 2, 3, 3, 3, 4, 5, 5, 5],
                9,
                fn ($x) => $x % 2 === 1,
            ],
            [
                [1, 1, 1, 2, 3, 3, 3, 4, 5, 5, 5],
                2,
                fn ($x) => $x % 2 === 0,
            ],
            [
                [false, false, true],
                2,
                fn ($x) => !$x,
            ],
            [
                [false, true, true],
                1,
                fn ($x) => !$x,
            ],
            [
                [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
                24,
                fn ($x) => $x === 1,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForExactlyNWithPredicateWhenNDoesNotMatch
     * @param        array $input
     * @param        int   $n
     * @param        callable $predicate
     */
    public function testArrayWithPredicateNonZeroNWhenNDoesNotMatch(array $input, int $n, callable $predicate): void
    {
        // Given
        $data = $input;

        // When
        $result = Summary::exactlyN($data, $n, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @dataProvider dataProviderForExactlyNWithPredicateWhenNDoesNotMatch
     * @param        array $input
     * @param        int   $n
     * @param        callable $predicate
     */
    public function testGeneratorWithPredicateNonZeroNWhenNDoesNotMatch(array $input, int $n, callable $predicate): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($input);

        // When
        $result = Summary::exactlyN($data, $n, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @dataProvider dataProviderForExactlyNWithPredicateWhenNDoesNotMatch
     * @param        array $input
     * @param        int   $n
     * @param        callable $predicate
     */
    public function testIteratorWithPredicateNonZeroNWhenNDoesNotMatch(array $input, int $n, callable $predicate): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($input);

        // When
        $result = Summary::exactlyN($data, $n, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @dataProvider dataProviderForExactlyNWithPredicateWhenNDoesNotMatch
     * @param        array $input
     * @param        int   $n
     * @param        callable $predicate
     */
    public function testTraversableWithPredicateNonZeroNWhenNDoesNotMatch(array $input, int $n, callable $predicate): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($input);

        // When
        $result = Summary::exactlyN($data, $n, $predicate);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForExactlyNWithPredicateWhenNDoesNotMatch(): array
    {
        return [
            [
                [1],
                0,
                fn ($x) => $x === 1,
            ],
            [
                [1],
                2,
                fn ($x) => $x >= 1,
            ],
            [
                [2],
                3,
                fn ($x) => $x >= 1,
            ],
            [
                ['a'],
                0,
                fn ($x) => is_string($x),
            ],
            [
                [[5]],
                2,
                fn ($x) => is_array($x),
            ],
            [
                [new \stdClass()],
                0,
                fn ($x) => is_object($x),
            ],
            [
                [1, 2],
                1,
                fn ($x) => $x < 3,
            ],
            [
                [1, 1, 1],
                2,
                fn ($x) => $x === 1,
            ],
            [
                [1, 2.2, '3', 'four'],
                5,
                fn ($x) => $x < 3 || is_string($x),
            ],
            [
                [1, 2.2, '3', 'four', 5, ['what'], [2]],
                6,
                fn ($x) => $x < 3 || is_string($x),
            ],
            [
                [1, 2.2, '3', 'four', 5, ['what'], [2], 2.9, -5],
                4,
                fn ($x) => $x < 3 || is_string($x),
            ],
            [
                [0],
                0,
                fn ($x) => $x === 0,
            ],
            [
                [0],
                1,
                fn ($x) => $x !== 0,
            ],
            [
                [0, 0, 0, 34],
                4,
                fn ($x) => $x > 0,
            ],
            [
                [1, 1, 1, 2, 3, 3, 3, 4, 5, 5, 5],
                8,
                fn ($x) => $x % 2 === 1,
            ],
            [
                [1, 1, 1, 2, 3, 3, 3, 4, 5, 5, 5],
                3,
                fn ($x) => $x % 2 === 0,
            ],
            [
                [false, false, true],
                1,
                fn ($x) => !$x,
            ],
            [
                [false, true, true],
                2,
                fn ($x) => !$x,
            ],
            [
                [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
                10,
                fn ($x) => $x === 1,
            ],
        ];
    }

    public function testInfiniteIterationShortCircuitsWhenExceedingN(): void
    {
        // Given
        $infiniteIterator = Infinite::repeat(true);

        // When
        $result = Summary::exactlyN($infiniteIterator, 100);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testWithNegativeN(iterable $data): void
    {
        // Given
        $n = -1;

        // When
        $result = Summary::exactlyN($data, $n);

        // Then
        $this->assertFalse($result);
    }
}

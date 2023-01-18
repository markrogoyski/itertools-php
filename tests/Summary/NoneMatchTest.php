<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture;

class NoneMatchTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /**
     * @test         noneMatch - empty iterable
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptyIterableWith(iterable $data): void
    {
        // Given
        $predicate = fn ($x) => $x === 5;

        // When
        $result = Summary::noneMatch($data, $predicate);

        // Then
        $this->assertTrue($result);
    }


    /**
     * @test         noneMatch - true
     * @dataProvider dataProviderForNoneMatchWhenTrueArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testNoneMatchWhenTrueArray(array $data, callable $predicate): void
    {
        // When
        $result = Summary::noneMatch($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         noneMatch - true
     * @dataProvider dataProviderForNoneMatchWhenTrueArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testNoneMatchWhenTrueGenerator(array $data, callable $predicate): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Summary::noneMatch($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         noneMatch - true
     * @dataProvider dataProviderForNoneMatchWhenTrueArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testNoneMatchWhenTrueIterator(array $data, callable $predicate): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Summary::noneMatch($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         noneMatch - true
     * @dataProvider dataProviderForNoneMatchWhenTrueArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testNoneMatchWhenTrueTraversable(array $data, callable $predicate): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Summary::noneMatch($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForNoneMatchWhenTrueArray(): array
    {
        return [
            [
                [1],
                fn ($x) => $x === 2,
            ],
            [
                [1, 2, 3],
                fn ($x) => $x < 1,
            ],
            [
                [1, 2, 3],
                fn ($x) => $x > 4,
            ],
            [
                ['a'],
                fn ($x) => $x !== 'a',
            ],
            [
                ['A', 'B', 'C'],
                '\ctype_lower',
            ],
            [
                ['a', 'b', 'c'],
                '\ctype_upper',
            ],
            [
                ['OS', 'PHP', 'COBOL'],
                '\ctype_lower',
            ],
            [
                [[1, 2], ['a'], [1.1, 2.2, 3.3, 4.4]],
                fn ($x) => \count($x) === 3,
            ],
        ];
    }

    /**
     * @test         noneMatch - false
     * @dataProvider dataProviderForNoneMatchWhenFalseArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testNoneMatchWhenFalseArray(array $data, callable $predicate): void
    {
        // When
        $result = Summary::noneMatch($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         noneMatch - false
     * @dataProvider dataProviderForNoneMatchWhenFalseArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testNoneMatchWhenFalseGenerator(array $data, callable $predicate): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Summary::noneMatch($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         noneMatch - false
     * @dataProvider dataProviderForNoneMatchWhenFalseArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testNoneMatchWhenFalseIterator(array $data, callable $predicate): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Summary::noneMatch($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         noneMatch - false
     * @dataProvider dataProviderForNoneMatchWhenFalseArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testNoneMatchWhenFalseTraversable(array $data, callable $predicate): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Summary::noneMatch($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForNoneMatchWhenFalseArray(): array
    {
        return [
            [
                [1],
                fn ($x) => $x === 1,
            ],
            [
                [1, 2, 3],
                fn ($x) => $x > 2,
            ],
            [
                [1, 2, 3],
                fn ($x) => $x < 3,
            ],
            [
                ['a'],
                fn ($x) => $x === 'a',
            ],
            [
                ['a', 'B', 'C'],
                '\ctype_lower',
            ],
            [
                ['a', 'B', 'C'],
                '\ctype_upper',
            ],
            [
                ['OS', 'PHP', 'python'],
                '\ctype_upper',
            ],
            [
                [[1], ['a', 'b'], [1.1, 2.2, 3.3]],
                fn ($x) => \count($x) === 3,
            ],
        ];
    }
}

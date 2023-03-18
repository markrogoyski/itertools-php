<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture;

class AllMatchTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /**
     * @test         allMatch - empty iterable
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable $data
     */
    public function testEmptyIterableWith(iterable $data): void
    {
        // Given
        $predicate = fn ($x) => $x === 5;

        // When
        $result = Summary::allMatch($data, $predicate);

        // Then
        $this->assertTrue($result);
    }


    /**
     * @test         allMatch - true
     * @dataProvider dataProviderForAllMatchWhenTrueArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAllMatchWhenTrueArray(array $data, callable $predicate): void
    {
        // When
        $result = Summary::allMatch($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         allMatch - true
     * @dataProvider dataProviderForAllMatchWhenTrueArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAllMatchWhenTrueGenerator(array $data, callable $predicate): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Summary::allMatch($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         allMatch - true
     * @dataProvider dataProviderForAllMatchWhenTrueArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAllMatchWhenTrueIterator(array $data, callable $predicate): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Summary::allMatch($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         allMatch - true
     * @dataProvider dataProviderForAllMatchWhenTrueArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAllMatchWhenTrueTraversable(array $data, callable $predicate): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Summary::allMatch($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForAllMatchWhenTrueArray(): array
    {
        return [
            [
                [1],
                fn ($x) => $x === 1,
            ],
            [
                [1, 2, 3],
                fn ($x) => $x >= 1,
            ],
            [
                [1, 2, 3],
                fn ($x) => $x < 4,
            ],
            [
                ['a'],
                fn ($x) => $x === 'a',
            ],
            [
                ['A', 'B', 'C'],
                '\ctype_upper',
            ],
            [
                ['a', 'b', 'c'],
                '\ctype_lower',
            ],
            [
                ['OS', 'PHP', 'COBOL'],
                '\ctype_upper',
            ],
            [
                [[1, 2, 3], ['a', 'b', 'c'], [1.1, 2.2, 3.3]],
                fn ($x) => \count($x) === 3,
            ],
        ];
    }

    /**
     * @test         allMatch - false
     * @dataProvider dataProviderForAllMatchWhenFalseArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAllMatchWhenFalseArray(array $data, callable $predicate): void
    {
        // When
        $result = Summary::allMatch($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         allMatch - false
     * @dataProvider dataProviderForAllMatchWhenFalseArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAllMatchWhenFalseGenerator(array $data, callable $predicate): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Summary::allMatch($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         allMatch - false
     * @dataProvider dataProviderForAllMatchWhenFalseArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAllMatchWhenFalseIterator(array $data, callable $predicate): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Summary::allMatch($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         allMatch - false
     * @dataProvider dataProviderForAllMatchWhenFalseArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAllMatchWhenFalseTraversable(array $data, callable $predicate): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Summary::allMatch($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForAllMatchWhenFalseArray(): array
    {
        return [
            [
                [1],
                fn ($x) => $x === 2,
            ],
            [
                [1, 2, 3],
                fn ($x) => $x >= 4,
            ],
            [
                [1, 2, 3],
                fn ($x) => $x < 3,
            ],
            [
                ['a'],
                fn ($x) => $x === 'b',
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
                [[1, 2, 3], ['a', 'b'], [1.1, 2.2, 3.3]],
                fn ($x) => \count($x) === 3,
            ],
        ];
    }
}

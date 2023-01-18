<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture;

class AnyMatchTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /**
     * @test         anyMatch - empty array
     * @dataProvider dataProviderForEmptyIterable
     * @param        array $data
     */
    public function testEmptyIterableWith(iterable $data): void
    {
        // Given
        $predicate = fn ($x) => $x === 5;

        // When
        $result = Summary::anyMatch($data, $predicate);

        // Then
        $this->assertFalse($result);
    }


    /**
     * @test         anyMatch - true
     * @dataProvider dataProviderForAnyMatchWhenTrueArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAnyMatchWhenTrueArray(array $data, callable $predicate): void
    {
        // When
        $result = Summary::anyMatch($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         anyMatch - true
     * @dataProvider dataProviderForAnyMatchWhenTrueArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAnyMatchWhenTrueGenerator(array $data, callable $predicate): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Summary::anyMatch($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         anyMatch - true
     * @dataProvider dataProviderForAnyMatchWhenTrueArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAnyMatchWhenTrueIterator(array $data, callable $predicate): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Summary::anyMatch($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         anyMatch - true
     * @dataProvider dataProviderForAnyMatchWhenTrueArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAnyMatchWhenTrueTraversable(array $data, callable $predicate): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Summary::anyMatch($data, $predicate);

        // Then
        $this->assertTrue($result);
    }

    public function dataProviderForAnyMatchWhenTrueArray(): array
    {
        return [
            [
                [1],
                fn ($x) => $x === 1,
            ],
            [
                [0, 1, 2, 3],
                fn ($x) => $x >= 1,
            ],
            [
                [1, 2, 3, 4, 5],
                fn ($x) => $x < 4,
            ],
            [
                ['a'],
                fn ($x) => $x === 'a',
            ],
            [
                ['n', 'a'],
                fn ($x) => $x === 'a',
            ],
            [
                ['A', 'b', 'C'],
                '\ctype_upper',
            ],
            [
                ['A', 'b', 'c'],
                '\ctype_lower',
            ],
            [
                ['OS', 'PHP', 'linux'],
                '\ctype_upper',
            ],
            [
                [[1, 3], ['a'], [1.1, 2.2, 3.3]],
                fn ($x) => \count($x) === 3,
            ],
        ];
    }

    /**
     * @test         anyMatch - false
     * @dataProvider dataProviderForAnyMatchWhenFalseArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAnyMatchWhenFalseArray(array $data, callable $predicate): void
    {
        // When
        $result = Summary::anyMatch($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         anyMatch - false
     * @dataProvider dataProviderForAnyMatchWhenFalseArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAnyMatchWhenFalseGenerator(array $data, callable $predicate): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Summary::anyMatch($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         anyMatch - false
     * @dataProvider dataProviderForAnyMatchWhenFalseArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAnyMatchWhenFalseIterator(array $data, callable $predicate): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Summary::anyMatch($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         anyMatch - false
     * @dataProvider dataProviderForAnyMatchWhenFalseArray
     * @param        array $data
     * @param        callable $predicate
     */
    public function testAnyMatchWhenFalseTraversable(array $data, callable $predicate): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Summary::anyMatch($data, $predicate);

        // Then
        $this->assertFalse($result);
    }

    public function dataProviderForAnyMatchWhenFalseArray(): array
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
                fn ($x) => $x < 1,
            ],
            [
                ['a'],
                fn ($x) => $x === 'b',
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
                [[1, 2], ['a', 'b'], [1.1, 2.2, 3.3, 4.4]],
                fn ($x) => \count($x) === 3,
            ],
        ];
    }
}

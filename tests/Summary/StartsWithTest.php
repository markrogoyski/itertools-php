<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture;

class StartsWithTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /**
     * @test         startsWith - empty prefix is always true
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $prefix
     */
    public function testEmptyPrefixIsAlwaysTrue(iterable $prefix): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = Summary::startsWith($data, $prefix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         startsWith - empty source with empty prefix is true
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptySourceEmptyPrefix(iterable $data): void
    {
        // Given
        $prefix = [];

        // When
        $result = Summary::startsWith($data, $prefix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         startsWith - empty source with non-empty prefix is false
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptySourceNonEmptyPrefix(iterable $data): void
    {
        // Given
        $prefix = [1];

        // When
        $result = Summary::startsWith($data, $prefix);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         startsWith - true for arrays
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testTrueArray(array $data, array $prefix): void
    {
        // When
        $result = Summary::startsWith($data, $prefix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         startsWith - true for generators
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testTrueGenerator(array $data, array $prefix): void
    {
        // Given
        $dataIt = Fixture\GeneratorFixture::getGenerator($data);
        $prefixIt = Fixture\GeneratorFixture::getGenerator($prefix);

        // When
        $result = Summary::startsWith($dataIt, $prefixIt);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         startsWith - true for iterators
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testTrueIterator(array $data, array $prefix): void
    {
        // Given
        $dataIt = new Fixture\ArrayIteratorFixture($data);
        $prefixIt = new Fixture\ArrayIteratorFixture($prefix);

        // When
        $result = Summary::startsWith($dataIt, $prefixIt);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         startsWith - true for traversables
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testTrueTraversable(array $data, array $prefix): void
    {
        // Given
        $dataIt = new Fixture\IteratorAggregateFixture($data);
        $prefixIt = new Fixture\IteratorAggregateFixture($prefix);

        // When
        $result = Summary::startsWith($dataIt, $prefixIt);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForTrue(): array
    {
        return [
            // exact match
            [[1, 2, 3], [1, 2, 3]],
            // proper prefix
            [[1, 2, 3, 4, 5], [1, 2]],
            [[1, 2, 3, 4, 5], [1]],
            // single element source/prefix
            [[7], [7]],
            // string values
            [['a', 'b', 'c'], ['a', 'b']],
            // mixed types — strict matches identical types
            [[1, 'a', 2.5], [1, 'a']],
        ];
    }

    /**
     * @test         startsWith - false for arrays
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testFalseArray(array $data, array $prefix): void
    {
        // When
        $result = Summary::startsWith($data, $prefix);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         startsWith - false for generators
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testFalseGenerator(array $data, array $prefix): void
    {
        // Given
        $dataIt = Fixture\GeneratorFixture::getGenerator($data);
        $prefixIt = Fixture\GeneratorFixture::getGenerator($prefix);

        // When
        $result = Summary::startsWith($dataIt, $prefixIt);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         startsWith - false for iterators
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testFalseIterator(array $data, array $prefix): void
    {
        // Given
        $dataIt = new Fixture\ArrayIteratorFixture($data);
        $prefixIt = new Fixture\ArrayIteratorFixture($prefix);

        // When
        $result = Summary::startsWith($dataIt, $prefixIt);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         startsWith - false for traversables
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $prefix
     */
    public function testFalseTraversable(array $data, array $prefix): void
    {
        // Given
        $dataIt = new Fixture\IteratorAggregateFixture($data);
        $prefixIt = new Fixture\IteratorAggregateFixture($prefix);

        // When
        $result = Summary::startsWith($dataIt, $prefixIt);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForFalse(): array
    {
        return [
            // mismatch at first
            [[1, 2, 3], [2]],
            // mismatch later
            [[1, 2, 3], [1, 3]],
            // source shorter than prefix
            [[1, 2], [1, 2, 3]],
            [[], [1]],
            // strict type mismatch
            [[1, 2], ['1', '2']],
            [[0, 1], [false, true]],
            [[1.0], [1]],
        ];
    }

    /**
     * @test startsWith - empty prefix does not consume source generator.
     */
    public function testEmptyPrefixDoesNotConsumeSource(): void
    {
        // Given
        $consumed = 0;
        $source = (function () use (&$consumed) {
            while (true) {
                $consumed++;
                yield 1;
            }
        })();

        // When
        $result = Summary::startsWith($source, []);

        // Then
        $this->assertTrue($result);
        $this->assertSame(0, $consumed);
    }
}

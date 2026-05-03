<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture;

class EndsWithTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /**
     * @test         endsWith - empty suffix is always true
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $suffix
     */
    public function testEmptySuffixIsAlwaysTrue(iterable $suffix): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = Summary::endsWith($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         endsWith - empty source with empty suffix is true
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptySourceEmptySuffix(iterable $data): void
    {
        // Given
        $suffix = [];

        // When
        $result = Summary::endsWith($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         endsWith - empty source with non-empty suffix is false
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptySourceNonEmptySuffix(iterable $data): void
    {
        // Given
        $suffix = [1];

        // When
        $result = Summary::endsWith($data, $suffix);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         endsWith - true for arrays
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testTrueArray(array $data, array $suffix): void
    {
        // When
        $result = Summary::endsWith($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         endsWith - true for generators
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testTrueGenerator(array $data, array $suffix): void
    {
        // Given
        $dataIt = Fixture\GeneratorFixture::getGenerator($data);
        $suffixIt = Fixture\GeneratorFixture::getGenerator($suffix);

        // When
        $result = Summary::endsWith($dataIt, $suffixIt);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         endsWith - true for iterators
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testTrueIterator(array $data, array $suffix): void
    {
        // Given
        $dataIt = new Fixture\ArrayIteratorFixture($data);
        $suffixIt = new Fixture\ArrayIteratorFixture($suffix);

        // When
        $result = Summary::endsWith($dataIt, $suffixIt);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         endsWith - true for traversables
     * @dataProvider dataProviderForTrue
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testTrueTraversable(array $data, array $suffix): void
    {
        // Given
        $dataIt = new Fixture\IteratorAggregateFixture($data);
        $suffixIt = new Fixture\IteratorAggregateFixture($suffix);

        // When
        $result = Summary::endsWith($dataIt, $suffixIt);

        // Then
        $this->assertTrue($result);
    }

    public static function dataProviderForTrue(): array
    {
        return [
            // exact match (suffix length == source length)
            [[1, 2, 3], [1, 2, 3]],
            // proper suffix
            [[1, 2, 3, 4, 5], [4, 5]],
            [[1, 2, 3, 4, 5], [5]],
            // single element source/suffix
            [[7], [7]],
            // string values
            [['a', 'b', 'c'], ['b', 'c']],
            // mixed types — strict matches identical types
            [[1, 'a', 2.5], ['a', 2.5]],
        ];
    }

    /**
     * @test         endsWith - false for arrays
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testFalseArray(array $data, array $suffix): void
    {
        // When
        $result = Summary::endsWith($data, $suffix);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         endsWith - false for generators
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testFalseGenerator(array $data, array $suffix): void
    {
        // Given
        $dataIt = Fixture\GeneratorFixture::getGenerator($data);
        $suffixIt = Fixture\GeneratorFixture::getGenerator($suffix);

        // When
        $result = Summary::endsWith($dataIt, $suffixIt);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         endsWith - false for iterators
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testFalseIterator(array $data, array $suffix): void
    {
        // Given
        $dataIt = new Fixture\ArrayIteratorFixture($data);
        $suffixIt = new Fixture\ArrayIteratorFixture($suffix);

        // When
        $result = Summary::endsWith($dataIt, $suffixIt);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         endsWith - false for traversables
     * @dataProvider dataProviderForFalse
     * @param        array<mixed> $data
     * @param        array<mixed> $suffix
     */
    public function testFalseTraversable(array $data, array $suffix): void
    {
        // Given
        $dataIt = new Fixture\IteratorAggregateFixture($data);
        $suffixIt = new Fixture\IteratorAggregateFixture($suffix);

        // When
        $result = Summary::endsWith($dataIt, $suffixIt);

        // Then
        $this->assertFalse($result);
    }

    public static function dataProviderForFalse(): array
    {
        return [
            // mismatch at last
            [[1, 2, 3], [2]],
            // mismatch deeper in suffix
            [[1, 2, 3], [2, 4]],
            // source shorter than suffix
            [[1, 2], [1, 2, 3]],
            // strict type mismatch
            [[1, 2], ['1', '2']],
            [[0, 1], [false, true]],
            [[1.0], [1]],
        ];
    }
}

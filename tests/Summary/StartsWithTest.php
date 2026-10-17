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

    /**
     * @test         startsWith - never advances a generator source past the last element compared
     * @dataProvider dataProviderForNoOverRead
     * @param        array<mixed> $prefix
     * @param        bool         $expected
     * @param        array<mixed> $expectedPulled
     */
    public function testDoesNotAdvancePastLastComparedElementGenerator(
        array $prefix,
        bool $expected,
        array $expectedPulled
    ): void {
        // Given a source that records every element pulled from it
        $pulled = [];
        $data = Fixture\GeneratorFixture::getRecordingGenerator([1, 2, 3, 4, 5], $pulled);

        // When
        $result = Summary::startsWith($data, $prefix);

        // Then
        $this->assertSame($expected, $result);

        // And the source was never advanced beyond the last element compared
        $this->assertSame($expectedPulled, $pulled);
    }

    /**
     * @test         startsWith - never advances an iterator source past the last element compared
     * @dataProvider dataProviderForNoOverRead
     * @param        array<mixed> $prefix
     * @param        bool         $expected
     * @param        array<mixed> $expectedPulled
     */
    public function testDoesNotAdvancePastLastComparedElementIterator(
        array $prefix,
        bool $expected,
        array $expectedPulled
    ): void {
        // Given a source that records every element pulled from it
        $pulled = [];
        $data = new Fixture\RecordingIteratorFixture([1, 2, 3, 4, 5], $pulled);

        // When
        $result = Summary::startsWith($data, $prefix);

        // Then
        $this->assertSame($expected, $result);

        // And the source was never advanced beyond the last element compared
        $this->assertSame($expectedPulled, $pulled);
    }

    /**
     * @test         startsWith - never advances an IteratorAggregate source past the last element compared
     * @dataProvider dataProviderForNoOverRead
     * @param        array<mixed> $prefix
     * @param        bool         $expected
     * @param        array<mixed> $expectedPulled
     */
    public function testDoesNotAdvancePastLastComparedElementIteratorAggregate(
        array $prefix,
        bool $expected,
        array $expectedPulled
    ): void {
        // Given a source that records every element pulled from it
        $pulled = [];
        $data = new Fixture\RecordingIteratorAggregateFixture([1, 2, 3, 4, 5], $pulled);

        // When
        $result = Summary::startsWith($data, $prefix);

        // Then
        $this->assertSame($expected, $result);

        // And the source was never advanced beyond the last element compared
        $this->assertSame($expectedPulled, $pulled);
    }

    public static function dataProviderForNoOverRead(): array
    {
        return [
            // full match: stops at the last prefix element
            [[1], true, [1]],
            [[1, 2], true, [1, 2]],
            [[1, 2, 3, 4], true, [1, 2, 3, 4]],
            [[1, 2, 3, 4, 5], true, [1, 2, 3, 4, 5]],
            // mismatch: stops at the mismatching element
            [[9], false, [1]],
            [[1, 9], false, [1, 2]],
            [[1, 2, 3, 9], false, [1, 2, 3, 4]],
            // source shorter than prefix
            [[1, 2, 3, 4, 5, 6], false, [1, 2, 3, 4, 5]],
        ];
    }

    /**
     * @test startsWith - a generator that throws after yielding the prefix still matches
     */
    public function testMatchesWhenSourceThrowsAfterPrefixGenerator(): void
    {
        // Given
        $data = (static function (): \Generator {
            yield 1;
            yield 2;
            throw new \LogicException('Source advanced past the prefix');
        })();

        // When
        $result = Summary::startsWith($data, [1, 2]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test         startsWith - leaves a generator source positioned on the last element compared
     * @dataProvider dataProviderForSourcePosition
     * @param        array<mixed> $prefix
     * @param        bool         $expected
     * @param        int          $expectedKey
     */
    public function testLeavesSourceOnLastComparedElementGenerator(array $prefix, bool $expected, int $expectedKey): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator([1, 2, 3, 4, 5]);

        // When
        $result = Summary::startsWith($data, $prefix);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame($expectedKey, $data->key());
    }

    /**
     * @test         startsWith - leaves an iterator source positioned on the last element compared
     * @dataProvider dataProviderForSourcePosition
     * @param        array<mixed> $prefix
     * @param        bool         $expected
     * @param        int          $expectedKey
     */
    public function testLeavesSourceOnLastComparedElementIterator(array $prefix, bool $expected, int $expectedKey): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture([1, 2, 3, 4, 5]);

        // When
        $result = Summary::startsWith($data, $prefix);

        // Then
        $this->assertSame($expected, $result);
        $this->assertSame($expectedKey, $data->key());
    }

    public static function dataProviderForSourcePosition(): array
    {
        return [
            // full match: on the last prefix element
            [[1], true, 0],
            [[1, 2], true, 1],
            [[1, 2, 3, 4, 5], true, 4],
            // mismatch: on the mismatching element
            [[9], false, 0],
            [[1, 2, 9], false, 2],
        ];
    }

    /**
     * @test startsWith - leaves a generator source exhausted when it is shorter than the prefix
     */
    public function testLeavesSourceExhaustedWhenShorterThanPrefixGenerator(): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator([1, 2]);

        // When
        $result = Summary::startsWith($data, [1, 2, 3]);

        // Then
        $this->assertFalse($result);
        $this->assertFalse($data->valid());
    }

    /**
     * @test startsWith - leaves an iterator source exhausted when it is shorter than the prefix
     */
    public function testLeavesSourceExhaustedWhenShorterThanPrefixIterator(): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture([1, 2]);

        // When
        $result = Summary::startsWith($data, [1, 2, 3]);

        // Then
        $this->assertFalse($result);
        $this->assertFalse($data->valid());
    }
}

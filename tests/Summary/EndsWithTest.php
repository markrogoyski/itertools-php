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
            // source one longer than suffix
            [[1, 2, 3, 4], [2, 3, 4]],
            // source length not a multiple of suffix length
            [[1, 2, 3, 4, 5, 6, 7], [5, 6, 7]],
            [[1, 2, 3, 4, 5, 6, 7, 8], [6, 7, 8]],
            // source length a multiple of suffix length
            [[1, 2, 3, 4, 5, 6, 7, 8, 9], [7, 8, 9]],
            // repeated values across the source
            [[1, 2, 1, 2, 1, 2, 1], [2, 1, 2, 1]],
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
            // source one longer than suffix
            [[1, 2, 3, 4], [1, 2, 3]],
            // mismatch at the oldest window element, source length not a multiple of suffix length
            [[1, 2, 3, 4, 5, 6, 7], [4, 6, 7]],
            [[1, 2, 3, 4, 5, 6, 7, 8], [5, 7, 8]],
            // mismatch at the newest window element
            [[1, 2, 3, 4, 5, 6, 7], [5, 6, 8]],
            // suffix matches a window rotated from the end
            [[1, 2, 3, 4, 5, 6, 7], [7, 5, 6]],
            [[1, 2, 3, 4, 5, 6, 7], [6, 7, 5]],
        ];
    }

    /**
     * @test endsWith - empty suffix does not consume source generator.
     */
    public function testEmptySuffixDoesNotConsumeSource(): void
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
        $result = Summary::endsWith($source, []);

        // Then
        $this->assertTrue($result);
        $this->assertSame(0, $consumed);
    }

    /**
     * Regression: a source whose rows share one reference slot (the PDO bindColumn pattern) is
     * compared as each row was when it was read, not as the slot holds after later rows.
     *
     * @test endsWith - reference-slot source rows ending with the suffix
     */
    public function testReferenceSlotSourceTrue(): void
    {
        // Given
        $data = self::referenceSlotRows(1, 2);
        $suffix = [['id' => 1], ['id' => 2]];

        // When
        $result = Summary::endsWith($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWith - reference-slot source rows not ending with the suffix
     */
    public function testReferenceSlotSourceFalse(): void
    {
        // Given
        $data = self::referenceSlotRows(1, 2);
        $suffix = [['id' => 2], ['id' => 2]];

        // When
        $result = Summary::endsWith($data, $suffix);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test endsWith - reference-slot suffix rows are compared as read
     */
    public function testReferenceSlotSuffix(): void
    {
        // Given
        $data = [['id' => 1], ['id' => 2]];
        $suffix = self::referenceSlotRows(1, 2);

        // When
        $result = Summary::endsWith($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWith - a reference slot nested inside a row is compared as read
     */
    public function testNestedReferenceSlotSource(): void
    {
        // Given
        $data = self::nestedReferenceSlotRows(1, 2);
        $suffix = [['row' => ['id' => 1]], ['row' => ['id' => 2]]];

        // When
        $result = Summary::endsWith($data, $suffix);

        // Then
        $this->assertTrue($result);
    }

    /**
     * Yields rows that all share one reference slot, as PDOStatement::bindColumn() fills them:
     * each yielded ['id' => &$id] follows the next assignment to $id.
     *
     * @return \Generator<int, array{id: int}>
     */
    private static function referenceSlotRows(int ...$ids): \Generator
    {
        $id = null;
        $row = ['id' => &$id];

        foreach ($ids as $value) {
            $id = $value;

            yield $row;
        }
    }

    /**
     * Yields rows holding a nested array that shares one reference slot.
     *
     * @return \Generator<int, array{row: array{id: int}}>
     */
    private static function nestedReferenceSlotRows(int ...$ids): \Generator
    {
        $id = null;
        $row = ['row' => ['id' => &$id]];

        foreach ($ids as $value) {
            $id = $value;

            yield $row;
        }
    }

    /**
     * @test endsWith - a generator passed as its own suffix is true
     */
    public function testOwnSourceGenerator(): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $result = Summary::endsWith($data, $data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWith - an iterator passed as its own suffix is true without being read
     */
    public function testOwnSourceIteratorIsNotRead(): void
    {
        // Given
        $pulled = [];
        $data = new Fixture\RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $result = Summary::endsWith($data, $data);

        // Then
        $this->assertTrue($result);
        $this->assertSame([], $pulled);
    }

    /**
     * @test endsWith - a throwing generator passed as its own suffix is true without its exception being raised
     */
    public function testOwnSourceThrowingGenerator(): void
    {
        // Given
        $data = (static function (): \Generator {
            throw new \RuntimeException('must not be pulled');
            yield; // unreachable
        })();

        // When
        $result = Summary::endsWith($data, $data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWith - an IteratorAggregate passed as its own suffix is compared through separate iterators
     */
    public function testOwnSourceIteratorAggregate(): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture([1, 2, 3]);

        // When
        $result = Summary::endsWith($data, $data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWith - an iterator holding NAN passed as its own suffix is true, though NAN never matches NAN
     */
    public function testOwnSourceIteratorWithNanIsTrue(): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture([NAN]);

        // When
        $result = Summary::endsWith($data, $data);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test endsWith - a separate iterator holding NAN as the suffix is false, since NAN never matches NAN
     */
    public function testSeparateIteratorWithNanIsFalse(): void
    {
        // Given
        $data  = new Fixture\ArrayIteratorFixture([NAN]);
        $suffix = new Fixture\ArrayIteratorFixture([NAN]);

        // When
        $result = Summary::endsWith($data, $suffix);

        // Then
        $this->assertFalse($result);
    }
}

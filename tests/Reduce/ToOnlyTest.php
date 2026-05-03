<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToOnlyTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test toOnly example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [42];

        // When
        $result = Reduce::toOnly($data);

        // Then
        $this->assertSame(42, $result);
    }

    /**
     * @test         toOnly returns the single element (array)
     * @dataProvider dataProviderForSingle
     * @param        array<mixed> $data
     * @param        mixed        $expected
     */
    public function testSingleArray(array $data, mixed $expected): void
    {
        // When
        $result = Reduce::toOnly($data);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toOnly returns the single element (Generator)
     * @dataProvider dataProviderForSingle
     * @param        array<mixed> $data
     * @param        mixed        $expected
     */
    public function testSingleGenerator(array $data, mixed $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Reduce::toOnly($iterable);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toOnly returns the single element (Iterator)
     * @dataProvider dataProviderForSingle
     * @param        array<mixed> $data
     * @param        mixed        $expected
     */
    public function testSingleIterator(array $data, mixed $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Reduce::toOnly($iterable);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         toOnly returns the single element (IteratorAggregate)
     * @dataProvider dataProviderForSingle
     * @param        array<mixed> $data
     * @param        mixed        $expected
     */
    public function testSingleIteratorAggregate(array $data, mixed $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Reduce::toOnly($iterable);

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForSingle(): array
    {
        return [
            [[1], 1],
            [[0], 0],
            [['only'], 'only'],
            [[null], null],
            [[false], false],
            [[[1, 2, 3]], [1, 2, 3]],
        ];
    }

    /**
     * @test         toOnly throws LengthException on empty iterable
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterableThrows(iterable $data): void
    {
        // Then
        $this->expectException(\LengthException::class);

        // When
        Reduce::toOnly($data);
    }

    /**
     * @test         toOnly throws LengthException on multi-element iterable (array)
     * @dataProvider dataProviderForMultiple
     * @param        array<mixed> $data
     */
    public function testMultipleArrayThrows(array $data): void
    {
        // Then
        $this->expectException(\LengthException::class);

        // When
        Reduce::toOnly($data);
    }

    /**
     * @test         toOnly throws LengthException on multi-element iterable (Generator)
     * @dataProvider dataProviderForMultiple
     * @param        array<mixed> $data
     */
    public function testMultipleGeneratorThrows(array $data): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // Then
        $this->expectException(\LengthException::class);

        // When
        Reduce::toOnly($iterable);
    }

    /**
     * @test         toOnly throws LengthException on multi-element iterable (Iterator)
     * @dataProvider dataProviderForMultiple
     * @param        array<mixed> $data
     */
    public function testMultipleIteratorThrows(array $data): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // Then
        $this->expectException(\LengthException::class);

        // When
        Reduce::toOnly($iterable);
    }

    /**
     * @test         toOnly throws LengthException on multi-element iterable (IteratorAggregate)
     * @dataProvider dataProviderForMultiple
     * @param        array<mixed> $data
     */
    public function testMultipleIteratorAggregateThrows(array $data): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // Then
        $this->expectException(\LengthException::class);

        // When
        Reduce::toOnly($iterable);
    }

    public static function dataProviderForMultiple(): array
    {
        return [
            [[1, 2]],
            [[1, 2, 3]],
            [[null, null]],
            [['a', 'b', 'c', 'd']],
        ];
    }

    /**
     * @test toOnly returns value (not key) for single-element associative iterable
     */
    public function testAssociativeSingleElementReturnsValue(): void
    {
        // Given
        $data = ['key' => 'value'];

        // When
        $result = Reduce::toOnly($data);

        // Then
        $this->assertSame('value', $result);
    }

    /**
     * @test toOnly returns value for single-element associative key-value Generator
     */
    public function testAssociativeKeyValueGeneratorReturnsValue(): void
    {
        // Given
        $iterable = GeneratorFixture::getKeyValueGenerator(['only' => 7]);

        // When
        $result = Reduce::toOnly($iterable);

        // Then
        $this->assertSame(7, $result);
    }

    /**
     * @test toOnly does not consume past the second element
     */
    public function testThrowsBeforeExhaustingIterator(): void
    {
        // Given a generator that throws after the second element
        $generator = (function (): \Generator {
            yield 1;
            yield 2;
            throw new \RuntimeException('iterator advanced past second element');
        })();

        // Then
        $this->expectException(\LengthException::class);

        // When
        Reduce::toOnly($generator);
    }
}

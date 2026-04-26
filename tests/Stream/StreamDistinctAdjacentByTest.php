<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamDistinctAdjacentByTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test Stream::distinctAdjacentBy example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data    = ['apple', 'ant', 'banana', 'berry', 'apple'];
        $firstCh = static fn (string $s): string => $s[0];

        // When
        $result = Stream::of($data)
            ->distinctAdjacentBy($firstCh)
            ->toArray();

        // Then
        $this->assertSame(['apple', 'banana', 'apple'], $result);
    }

    /**
     * @test         Stream::distinctAdjacentBy (array)
     * @dataProvider dataProviderForDistinctAdjacentBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentByArray(array $data, callable $keyFn, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->distinctAdjacentBy($keyFn)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::distinctAdjacentBy (Generator)
     * @dataProvider dataProviderForDistinctAdjacentBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentByGenerator(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($iterable)
            ->distinctAdjacentBy($keyFn)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::distinctAdjacentBy (Iterator)
     * @dataProvider dataProviderForDistinctAdjacentBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentByIterator(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iterable)
            ->distinctAdjacentBy($keyFn)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::distinctAdjacentBy (IteratorAggregate)
     * @dataProvider dataProviderForDistinctAdjacentBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentByIteratorAggregate(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)
            ->distinctAdjacentBy($keyFn)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForDistinctAdjacentBy(): array
    {
        $identity = static fn ($x) => $x;
        $firstCh  = static fn (string $s): string => $s[0];

        return [
            [[], $identity, []],
            [[1], $identity, [1]],
            [[1, 1, 2, 2, 3, 1, 1], $identity, [1, 2, 3, 1]],
            [['apple', 'ant', 'banana', 'berry', 'apple'], $firstCh, ['apple', 'banana', 'apple']],
        ];
    }

    /**
     * @test         Stream::distinctAdjacentBy on empty input
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmpty(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->distinctAdjacentBy(fn ($x) => $x)
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::distinctAdjacentBy is chainable with other stream operations
     */
    public function testChainableWithOtherOperations(): void
    {
        // Given
        $data = ['apple', 'ant', 'banana', 'berry', 'cherry'];

        // When
        $result = Stream::of($data)
            ->distinctAdjacentBy(fn (string $s): string => $s[0])
            ->map(fn (string $s): string => \strtoupper($s))
            ->toArray();

        // Then
        $this->assertSame(['APPLE', 'BANANA', 'CHERRY'], $result);
    }
}

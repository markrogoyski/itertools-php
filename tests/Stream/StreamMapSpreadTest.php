<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamMapSpreadTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test Stream::mapSpread example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [[1, 2], [3, 4]];

        // When
        $result = Stream::of($data)
            ->mapSpread(fn ($a, $b) => $a * $b)
            ->toArray();

        // Then
        $this->assertSame([2, 12], $result);
    }

    /**
     * @test         Stream::mapSpread (array)
     * @dataProvider dataProviderForMapSpread
     * @param        array<mixed> $data
     * @param        callable     $func
     * @param        array<mixed> $expected
     */
    public function testMapSpreadArray(array $data, callable $func, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->mapSpread($func)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::mapSpread (Generator)
     * @dataProvider dataProviderForMapSpread
     * @param        array<mixed> $data
     * @param        callable     $func
     * @param        array<mixed> $expected
     */
    public function testMapSpreadGenerator(array $data, callable $func, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($iterable)
            ->mapSpread($func)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::mapSpread (Iterator)
     * @dataProvider dataProviderForMapSpread
     * @param        array<mixed> $data
     * @param        callable     $func
     * @param        array<mixed> $expected
     */
    public function testMapSpreadIterator(array $data, callable $func, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iterable)
            ->mapSpread($func)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::mapSpread (IteratorAggregate)
     * @dataProvider dataProviderForMapSpread
     * @param        array<mixed> $data
     * @param        callable     $func
     * @param        array<mixed> $expected
     */
    public function testMapSpreadIteratorAggregate(array $data, callable $func, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)
            ->mapSpread($func)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForMapSpread(): array
    {
        return [
            [[], fn ($a, $b) => $a + $b, []],
            [[[1, 2], [3, 4]], fn ($a, $b) => $a + $b, [3, 7]],
            [[[1, 2], [3, 4], [5, 6]], fn ($a, $b) => $a * $b, [2, 12, 30]],
            [[[1, 2, 3], [4, 5, 6]], fn ($a, $b, $c) => $a + $b + $c, [6, 15]],
        ];
    }

    /**
     * @test         Stream::mapSpread on empty input
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmpty(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->mapSpread(fn ($a, $b) => $a + $b)
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::mapSpread is chainable with other stream operations
     */
    public function testChainableWithOtherOperations(): void
    {
        // Given
        $data = [[1, 2], [3, 4], [5, 6]];

        // When
        $result = Stream::of($data)
            ->mapSpread(fn ($a, $b) => $a + $b)
            ->filter(fn ($n) => $n > 3)
            ->toArray();

        // Then
        $this->assertSame([7, 11], $result);
    }

    /**
     * @test Stream::mapSpread chains with zip output
     */
    public function testChainsWithZip(): void
    {
        // Given
        $left  = [1, 2, 3];
        $right = [10, 20, 30];

        // When
        $result = Stream::of($left)
            ->zipWith($right)
            ->mapSpread(fn ($a, $b) => $a + $b)
            ->toArray();

        // Then
        $this->assertSame([11, 22, 33], $result);
    }
}

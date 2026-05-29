<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture;

class StreamTakeLastTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::takeLast example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $logLines = ['line1', 'line2', 'line3', 'line4', 'line5'];

        // When
        $result = Stream::of($logLines)
            ->takeLast(2)
            ->toArray();

        // Then
        $this->assertEquals(['line4', 'line5'], $result);
    }

    /**
     * @test         Stream::takeLast array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        int   $count
     * @param        array $expected
     */
    public function testArray(array $data, int $count, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->takeLast($count)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [[], 0, []],
            [[], 3, []],
            [[1, 2, 3, 4, 5], 0, []],
            [[1, 2, 3, 4, 5], 1, [5]],
            [[1, 2, 3, 4, 5], 3, [3, 4, 5]],
            [[1, 2, 3, 4, 5], 5, [1, 2, 3, 4, 5]],
            [[1, 2, 3, 4, 5], 100, [1, 2, 3, 4, 5]],
        ];
    }

    /**
     * @test         Stream::takeLast generator
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        int   $count
     * @param        array $expected
     */
    public function testGenerator(array $data, int $count, array $expected): void
    {
        // Given
        $iterable = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($iterable)
            ->takeLast($count)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::takeLast iterator
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        int   $count
     * @param        array $expected
     */
    public function testIterator(array $data, int $count, array $expected): void
    {
        // Given
        $iterable = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iterable)
            ->takeLast($count)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::takeLast traversable
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        int   $count
     * @param        array $expected
     */
    public function testTraversable(array $data, int $count, array $expected): void
    {
        // Given
        $iterable = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)
            ->takeLast($count)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test Stream::takeLast chains with other operations
     */
    public function testChaining(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5, 6, 7, 8];

        // When
        $result = Stream::of($data)
            ->filterTrue(fn ($value) => $value % 2 === 0)
            ->takeLast(2)
            ->map(fn ($value) => $value * 10)
            ->toArray();

        // Then
        $this->assertEquals([60, 80], $result);
    }

    /**
     * @test Stream::takeLast preserves keys
     */
    public function testPreservesKeys(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        // When
        $result = Stream::of($data)
            ->takeLast(2)
            ->toAssociativeArray();

        // Then
        $this->assertEquals(['c' => 3, 'd' => 4], $result);
    }

    /**
     * @test Stream::takeLast with negative count throws
     */
    public function testNegativeCountThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of([1, 2, 3])
            ->takeLast(-1)
            ->toArray();
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture;

class StreamDropLastTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::dropLast example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $rows = ['row1', 'row2', 'row3', 'summary'];

        // When
        $result = Stream::of($rows)
            ->dropLast(1)
            ->toArray();

        // Then
        $this->assertEquals(['row1', 'row2', 'row3'], $result);
    }

    /**
     * @test         Stream::dropLast array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        int   $count
     * @param        array $expected
     */
    public function testArray(array $data, int $count, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->dropLast($count)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [[], 0, []],
            [[], 3, []],
            [[1, 2, 3, 4, 5], 0, [1, 2, 3, 4, 5]],
            [[1, 2, 3, 4, 5], 1, [1, 2, 3, 4]],
            [[1, 2, 3, 4, 5], 3, [1, 2]],
            [[1, 2, 3, 4, 5], 5, []],
            [[1, 2, 3, 4, 5], 100, []],
        ];
    }

    /**
     * @test         Stream::dropLast generator
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
            ->dropLast($count)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::dropLast iterator
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
            ->dropLast($count)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::dropLast traversable
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
            ->dropLast($count)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test Stream::dropLast chains with other operations
     */
    public function testChaining(): void
    {
        // Given
        $data = [1, 2, 3, 4, 5, 6, 7, 8];

        // When
        $result = Stream::of($data)
            ->filterTrue(fn ($value) => $value % 2 === 0)
            ->dropLast(1)
            ->map(fn ($value) => $value * 10)
            ->toArray();

        // Then
        $this->assertEquals([20, 40, 60], $result);
    }

    /**
     * @test Stream::dropLast preserves keys
     */
    public function testPreservesKeys(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        // When
        $result = Stream::of($data)
            ->dropLast(2)
            ->toAssociativeArray();

        // Then
        $this->assertEquals(['a' => 1, 'b' => 2], $result);
    }

    /**
     * @test Stream::dropLast with negative count throws
     */
    public function testNegativeCountThrows(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of([1, 2, 3])
            ->dropLast(-1)
            ->toArray();
    }
}

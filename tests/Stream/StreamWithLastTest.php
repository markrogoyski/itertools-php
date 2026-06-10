<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture;

class StreamWithLastTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::withLast example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = ['a', 'b', 'c'];

        // When
        $result = Stream::of($data)
            ->withLast()
            ->toArray();

        // Then
        $this->assertEquals([[false, 'a'], [false, 'b'], [true, 'c']], $result);
    }

    /**
     * @test         Stream::withLast array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        array $expected
     */
    public function testArray(array $data, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->withLast()
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [[], []],
            [[42], [[true, 42]]],
            [[1, 2], [[false, 1], [true, 2]]],
            [[1, 2, 3], [[false, 1], [false, 2], [true, 3]]],
        ];
    }

    /**
     * @test         Stream::withLast generator
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        array $expected
     */
    public function testGenerator(array $data, array $expected): void
    {
        // Given
        $iterable = Fixture\GeneratorFixture::getGenerator($data);

        // When
        $result = Stream::of($iterable)
            ->withLast()
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::withLast iterator
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        array $expected
     */
    public function testIterator(array $data, array $expected): void
    {
        // Given
        $iterable = new Fixture\ArrayIteratorFixture($data);

        // When
        $result = Stream::of($iterable)
            ->withLast()
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::withLast traversable
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        array $expected
     */
    public function testTraversable(array $data, array $expected): void
    {
        // Given
        $iterable = new Fixture\IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)
            ->withLast()
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test Stream::withLast chains with other operations
     */
    public function testChaining(): void
    {
        // Given
        $data = [10, 20, 30];

        // When
        $result = Stream::of($data)
            ->withLast()
            ->map(fn (array $pair) => $pair[0] ? "last:{$pair[1]}" : "rest:{$pair[1]}")
            ->toArray();

        // Then
        $this->assertEquals(['rest:10', 'rest:20', 'last:30'], $result);
    }

    /**
     * @test Stream::withLast yields sequential 0-indexed keys
     */
    public function testSequentialKeys(): void
    {
        // Given string-keyed input
        $data = ['x' => 1, 'y' => 2, 'z' => 3];

        // When
        $keys = [];
        foreach (Stream::of($data)->withLast() as $key => $_) {
            $keys[] = $key;
        }

        // Then
        $this->assertSame([0, 1, 2], $keys);
    }
}

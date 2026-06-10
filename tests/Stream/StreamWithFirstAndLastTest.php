<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture;

class StreamWithFirstAndLastTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::withFirstAndLast example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = ['a', 'b', 'c'];

        // When
        $result = Stream::of($data)
            ->withFirstAndLast()
            ->toArray();

        // Then
        $this->assertEquals(
            [[true, false, 'a'], [false, false, 'b'], [false, true, 'c']],
            $result
        );
    }

    /**
     * @test         Stream::withFirstAndLast array
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        array $expected
     */
    public function testArray(array $data, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->withFirstAndLast()
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [[], []],
            [[42], [[true, true, 42]]],
            [[1, 2], [[true, false, 1], [false, true, 2]]],
            [[1, 2, 3], [[true, false, 1], [false, false, 2], [false, true, 3]]],
        ];
    }

    /**
     * @test         Stream::withFirstAndLast generator
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
            ->withFirstAndLast()
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::withFirstAndLast iterator
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
            ->withFirstAndLast()
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::withFirstAndLast traversable
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
            ->withFirstAndLast()
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test Stream::withFirstAndLast chains with other operations
     */
    public function testChaining(): void
    {
        // Given
        $data = [10, 20, 30];

        // When
        $result = Stream::of($data)
            ->withFirstAndLast()
            ->map(function (array $triple) {
                [$isFirst, $isLast, $value] = $triple;
                $tag = $isFirst ? 'first' : ($isLast ? 'last' : 'mid');
                return "{$tag}:{$value}";
            })
            ->toArray();

        // Then
        $this->assertEquals(['first:10', 'mid:20', 'last:30'], $result);
    }

    /**
     * @test Stream::withFirstAndLast yields sequential 0-indexed keys
     */
    public function testSequentialKeys(): void
    {
        // Given string-keyed input
        $data = ['x' => 1, 'y' => 2, 'z' => 3];

        // When
        $keys = [];
        foreach (Stream::of($data)->withFirstAndLast() as $key => $_) {
            $keys[] = $key;
        }

        // Then
        $this->assertSame([0, 1, 2], $keys);
    }
}

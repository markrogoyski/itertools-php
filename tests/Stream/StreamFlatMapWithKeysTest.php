<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Single;
use IterTools\Stream;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamFlatMapWithKeysTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::flatMapWithKeys example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2];

        // When
        $result = Stream::of($data)
            ->flatMapWithKeys(fn ($value, $key) => [$key, $value])
            ->toArray();

        // Then
        $this->assertEquals(['a', 1, 'b', 2], $result);
    }

    /**
     * @test         Stream::flatMapWithKeys array
     * @dataProvider dataProviderForArray
     * @param        array    $data
     * @param        callable $func
     * @param        array    $expected
     */
    public function testArray(array $data, callable $func, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->flatMapWithKeys($func)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [
                [],
                fn ($value, $key) => [$value],
                [],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                fn ($value, $key) => $value + 1,
                [2, 3, 4],
            ],
            [
                ['a' => 1, 'b' => 2],
                fn ($value, $key) => [$key, $value],
                ['a', 1, 'b', 2],
            ],
            [
                ['x' => 2, 'y' => 3],
                fn ($value, $key) => Single::repeat($key, $value),
                ['x', 'x', 'y', 'y', 'y'],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                fn ($value, $key) => [],
                [],
            ],
        ];
    }

    /**
     * @test         Stream::flatMapWithKeys generator
     * @dataProvider dataProviderForArray
     * @param        array    $data
     * @param        callable $func
     * @param        array    $expected
     */
    public function testGenerator(array $data, callable $func, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getKeyValueGenerator($data);

        // When
        $result = Stream::of($iterable)
            ->flatMapWithKeys($func)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::flatMapWithKeys iterator
     * @dataProvider dataProviderForArray
     * @param        array    $data
     * @param        callable $func
     * @param        array    $expected
     */
    public function testIterator(array $data, callable $func, array $expected): void
    {
        // Given
        $iterable = new \ArrayIterator($data);

        // When
        $result = Stream::of($iterable)
            ->flatMapWithKeys($func)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::flatMapWithKeys traversable
     * @dataProvider dataProviderForArray
     * @param        array    $data
     * @param        callable $func
     * @param        array    $expected
     */
    public function testTraversable(array $data, callable $func, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)
            ->flatMapWithKeys($func)
            ->toArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test Stream::flatMapWithKeys recursive flattening via the third $self argument
     */
    public function testRecursiveFlattening(): void
    {
        // Given
        $data = [
            'a' => [1, 2, ['x' => 3, 'y' => [4, 5]], 6],
            'b' => [7],
            'c' => 8,
        ];

        // When
        $result = Stream::of($data)
            ->flatMapWithKeys(fn ($value, $key, $self) => \is_iterable($value)
                ? Single::flatMapWithKeys($value, $self)
                : [$value])
            ->toArray();

        // Then
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8], $result);
    }

    /**
     * @test Stream::flatMapWithKeys chains with other operations
     */
    public function testChaining(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 3];

        // When
        $result = Stream::of($data)
            ->flatMapWithKeys(fn ($value, $key) => [$value, $value])
            ->map(fn ($value) => $value * 10)
            ->toArray();

        // Then
        $this->assertEquals([10, 10, 20, 20, 30, 30], $result);
    }
}

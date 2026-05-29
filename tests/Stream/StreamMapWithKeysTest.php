<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamMapWithKeysTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::mapWithKeys example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 3];

        // When
        $result = Stream::of($data)
            ->mapWithKeys(fn ($value, $key) => "$key=$value")
            ->toAssociativeArray();

        // Then
        $this->assertEquals(['a' => 'a=1', 'b' => 'b=2', 'c' => 'c=3'], $result);
    }

    /**
     * @test         Stream::mapWithKeys array
     * @dataProvider dataProviderForArray
     * @param        array    $data
     * @param        callable $func
     * @param        array    $expected
     */
    public function testArray(array $data, callable $func, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->mapWithKeys($func)
            ->toAssociativeArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [
                [],
                fn ($value, $key) => $value,
                [],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                fn ($value, $key) => "$key=$value",
                ['a' => 'a=1', 'b' => 'b=2', 'c' => 'c=3'],
            ],
            [
                [10, 20, 30],
                fn ($value, $key) => $value + $key,
                [0 => 10, 1 => 21, 2 => 32],
            ],
        ];
    }

    /**
     * @test         Stream::mapWithKeys generator
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
            ->mapWithKeys($func)
            ->toAssociativeArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::mapWithKeys iterator
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
            ->mapWithKeys($func)
            ->toAssociativeArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::mapWithKeys traversable
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
            ->mapWithKeys($func)
            ->toAssociativeArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test Stream::mapWithKeys chains with other operations
     */
    public function testChaining(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        // When
        $result = Stream::of($data)
            ->mapWithKeys(fn ($value, $key) => $value * 10)
            ->filterTrue(fn ($value) => $value > 15)
            ->toArray();

        // Then
        $this->assertEquals([20, 30, 40], $result);
    }
}

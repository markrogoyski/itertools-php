<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamFilterWithKeysTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test Stream::filterWithKeys example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = ['apple' => 1, 'banana' => 2, 'avocado' => 3];

        // When
        $result = Stream::of($data)
            ->filterWithKeys(fn ($value, $key) => \str_starts_with($key, 'a'))
            ->toAssociativeArray();

        // Then
        $this->assertEquals(['apple' => 1, 'avocado' => 3], $result);
    }

    /**
     * @test         Stream::filterWithKeys array
     * @dataProvider dataProviderForArray
     * @param        array    $data
     * @param        callable $predicate
     * @param        array    $expected
     */
    public function testArray(array $data, callable $predicate, array $expected): void
    {
        // When
        $result = Stream::of($data)
            ->filterWithKeys($predicate)
            ->toAssociativeArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [
                [],
                fn ($value, $key) => true,
                [],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
                fn ($value, $key) => $value % 2 === 0,
                ['b' => 2, 'd' => 4],
            ],
            [
                ['apple' => 1, 'banana' => 2, 'avocado' => 3],
                fn ($value, $key) => \str_starts_with($key, 'a'),
                ['apple' => 1, 'avocado' => 3],
            ],
            [
                [10, 20, 30, 40],
                fn ($value, $key) => $key >= 2,
                [2 => 30, 3 => 40],
            ],
        ];
    }

    /**
     * @test         Stream::filterWithKeys generator
     * @dataProvider dataProviderForArray
     * @param        array    $data
     * @param        callable $predicate
     * @param        array    $expected
     */
    public function testGenerator(array $data, callable $predicate, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getKeyValueGenerator($data);

        // When
        $result = Stream::of($iterable)
            ->filterWithKeys($predicate)
            ->toAssociativeArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::filterWithKeys iterator
     * @dataProvider dataProviderForArray
     * @param        array    $data
     * @param        callable $predicate
     * @param        array    $expected
     */
    public function testIterator(array $data, callable $predicate, array $expected): void
    {
        // Given
        $iterable = new \ArrayIterator($data);

        // When
        $result = Stream::of($iterable)
            ->filterWithKeys($predicate)
            ->toAssociativeArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         Stream::filterWithKeys traversable
     * @dataProvider dataProviderForArray
     * @param        array    $data
     * @param        callable $predicate
     * @param        array    $expected
     */
    public function testTraversable(array $data, callable $predicate, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Stream::of($iterable)
            ->filterWithKeys($predicate)
            ->toAssociativeArray();

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test Stream::filterWithKeys chains with other operations
     */
    public function testChaining(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];

        // When
        $result = Stream::of($data)
            ->filterWithKeys(fn ($value, $key) => $value % 2 === 1)
            ->map(fn ($value) => $value * 10)
            ->toArray();

        // Then
        $this->assertEquals([10, 30, 50], $result);
    }
}

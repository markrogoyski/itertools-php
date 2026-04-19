<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class StreamProductWithTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test Stream::productWith example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $numbers = [1, 2];
        $letters = ['a', 'b'];

        // When
        $result = Stream::of($numbers)
            ->productWith($letters)
            ->toArray();

        // Then
        $this->assertSame(
            [
                [1, 'a'],
                [1, 'b'],
                [2, 'a'],
                [2, 'b'],
            ],
            $result
        );
    }

    /**
     * @test         Stream::productWith (array)
     * @dataProvider dataProviderForProductWith
     * @param        array<mixed>        $source
     * @param        array<array<mixed>> $extras
     * @param        array<array<mixed>> $expected
     */
    public function testProductWithArray(array $source, array $extras, array $expected): void
    {
        // When
        $result = Stream::of($source)
            ->productWith(...$extras)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::productWith (Generator)
     * @dataProvider dataProviderForProductWith
     * @param        array<mixed>        $source
     * @param        array<array<mixed>> $extras
     * @param        array<array<mixed>> $expected
     */
    public function testProductWithGenerator(array $source, array $extras, array $expected): void
    {
        // Given
        $src = GeneratorFixture::getGenerator($source);
        $generatorExtras = [];
        foreach ($extras as $extra) {
            $generatorExtras[] = GeneratorFixture::getGenerator($extra);
        }

        // When
        $result = Stream::of($src)
            ->productWith(...$generatorExtras)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::productWith (Iterator)
     * @dataProvider dataProviderForProductWith
     * @param        array<mixed>        $source
     * @param        array<array<mixed>> $extras
     * @param        array<array<mixed>> $expected
     */
    public function testProductWithIterator(array $source, array $extras, array $expected): void
    {
        // Given
        $src = new ArrayIteratorFixture($source);
        $iteratorExtras = [];
        foreach ($extras as $extra) {
            $iteratorExtras[] = new ArrayIteratorFixture($extra);
        }

        // When
        $result = Stream::of($src)
            ->productWith(...$iteratorExtras)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         Stream::productWith (IteratorAggregate)
     * @dataProvider dataProviderForProductWith
     * @param        array<mixed>        $source
     * @param        array<array<mixed>> $extras
     * @param        array<array<mixed>> $expected
     */
    public function testProductWithIteratorAggregate(array $source, array $extras, array $expected): void
    {
        // Given
        $src = new IteratorAggregateFixture($source);
        $aggregateExtras = [];
        foreach ($extras as $extra) {
            $aggregateExtras[] = new IteratorAggregateFixture($extra);
        }

        // When
        $result = Stream::of($src)
            ->productWith(...$aggregateExtras)
            ->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForProductWith(): array
    {
        return [
            // zero extra iterables — one-element tuples from source
            [
                [1, 2],
                [],
                [[1], [2]],
            ],
            [
                ['only'],
                [],
                [['only']],
            ],
            // one extra iterable
            [
                [1, 2],
                [[3, 4]],
                [[1, 3], [1, 4], [2, 3], [2, 4]],
            ],
            // two extra iterables
            [
                [0, 1],
                [[0, 1], [0, 1]],
                [
                    [0, 0, 0], [0, 0, 1], [0, 1, 0], [0, 1, 1],
                    [1, 0, 0], [1, 0, 1], [1, 1, 0], [1, 1, 1],
                ],
            ],
            // mixed sizes
            [
                [1, 2, 3],
                [['a']],
                [[1, 'a'], [2, 'a'], [3, 'a']],
            ],
        ];
    }

    /**
     * @test         Stream::productWith with empty source yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptySource(iterable $data): void
    {
        // When
        $result = Stream::of($data)
            ->productWith([1, 2, 3])
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::productWith with empty extra iterable yields nothing
     */
    public function testEmptyExtraIterable(): void
    {
        // When
        $result = Stream::of([1, 2, 3])
            ->productWith([])
            ->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test Stream::productWith chains with other stream operations
     */
    public function testChainableWithOtherOperations(): void
    {
        // Given
        $source = [1, 2, 3, 4];

        // When
        $result = Stream::of($source)
            ->filter(fn (int $n): bool => $n % 2 === 0)
            ->productWith(['a', 'b'])
            ->toArray();

        // Then
        $this->assertSame(
            [
                [2, 'a'],
                [2, 'b'],
                [4, 'a'],
                [4, 'b'],
            ],
            $result
        );
    }
}

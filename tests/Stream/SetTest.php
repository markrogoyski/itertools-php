<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class SetTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array    $input
     * @param callable $streamFactoryFunc
     * @param array    $expected
     * @return void
     * @dataProvider dataProviderForArray
     */
    public function testArray(array $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [1, 2, 3, '1', '2', '3'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinct(false)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                [1, 2, 3, '1', '2', '3'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinct()
                    ->toArray(),
                [1, 2, 3, '1', '2', '3'],
            ],
            [
                [1, 2, 3, '1', '2', '3', 1, '1'],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinct()
                    ->toArray(),
                [1, 2, 3, '1', '2', '3'],
            ],
        ];
    }

    /**
     * @param \Generator $input
     * @param callable   $streamFactoryFunc
     * @param array      $expected
     * @return void
     * @dataProvider dataProviderForGenerator
     */
    public function testGenerator(\Generator $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGenerator(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([1, 2, 3, '1', '2', '3']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinct(false)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                $gen([1, 2, 3, '1', '2', '3']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinct()
                    ->toArray(),
                [1, 2, 3, '1', '2', '3'],
            ],
            [
                $gen([1, 2, 3, '1', '2', '3', 1, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinct()
                    ->toArray(),
                [1, 2, 3, '1', '2', '3'],
            ],
        ];
    }

    /**
     * @param \Iterator $input
     * @param callable  $streamFactoryFunc
     * @param array     $expected
     * @return void
     * @dataProvider dataProviderForIterator
     */
    public function testIterator(\Iterator $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIterator(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([1, 2, 3, '1', '2', '3']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinct(false)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                $iter([1, 2, 3, '1', '2', '3']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinct()
                    ->toArray(),
                [1, 2, 3, '1', '2', '3'],
            ],
            [
                $iter([1, 2, 3, '1', '2', '3', 1, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinct()
                    ->toArray(),
                [1, 2, 3, '1', '2', '3'],
            ],
        ];
    }

    /**
     * @param \Traversable $input
     * @param callable     $streamFactoryFunc
     * @param array        $expected
     * @return void
     * @dataProvider dataProviderForTraversable
     */
    public function testTraversable(\Traversable $input, callable $streamFactoryFunc, array $expected): void
    {
        // When
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversable(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([1, 2, 3, '1', '2', '3']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinct(false)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                $trav([1, 2, 3, '1', '2', '3']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinct()
                    ->toArray(),
                [1, 2, 3, '1', '2', '3'],
            ],
            [
                $trav([1, 2, 3, '1', '2', '3', 1, '1']),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinct()
                    ->toArray(),
                [1, 2, 3, '1', '2', '3'],
            ],
        ];
    }
}

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
            [
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6, 7],
                    [3, 4, 5, 6, 7, 8, 9],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6, 7],
                    ['3', 4, 5, 6, 7, 8, 9],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6, 7],
                    [3, 4, 5, 6, 7, 8, 9],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionStrictWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6, 7],
                    ['3', 4, 5, 6, 7, 8, 9],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionStrictWith(...$iterables)
                    ->toArray(),
                [4, 5],
            ],
            [
                [
                    [1, 2],
                    [2, 3, 4],
                    [2, 3, 4, 5, 6],
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    [1, 2],
                    [2, 3, 4],
                    [2, 3, 4, 5, 6],
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    [1, 2],
                    [2, 3, 4],
                    [2, 3, 4, 5, 6],
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    [1, 2],
                    ['2', 3, 4],
                    [2, 3, 4, 5, 6],
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, '2', 3, 4, 5, 6],
            ],
            [
                [
                    [1, 2],
                    ['2', 3, 4],
                    [2, 3, 4, 5, 6],
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    [1, 2],
                    ['2', 3, 4],
                    [2, 3, 4, 5, 6],
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    [1, 2],
                    [2, 3, 4],
                    [2, 3, 4, 5, 6],
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    [1, 2],
                    [2, 3, 4],
                    [2, 3, 4, 5, 6],
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    [1, 2],
                    [2, 3, 4],
                    [2, 3, 4, 5, 6],
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    [1, 2],
                    [2, '3', 4],
                    [2, 3, 4, 5, 6],
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, 2, '3', 3, 4, 5, 6],
            ],
            [
                [
                    [1, 2],
                    [2, '3', 4],
                    [2, 3, 4, 5, 6],
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 4],
            ],
            [
                [
                    [1, 2, 3],
                    [2, '3', 4],
                    [2, 3, 4, 5, 6],
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
        ];
    }

    /**
     * @param iterable $input
     * @param callable   $streamFactoryFunc
     * @param array      $expected
     * @return void
     * @dataProvider dataProviderForGenerator
     */
    public function testGenerator(iterable $input, callable $streamFactoryFunc, array $expected): void
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
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([2, 3, 4, 5, 6, 7]),
                    $gen([3, 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([2, 3, 4, 5, 6, 7]),
                    $gen(['3', 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([2, 3, 4, 5, 6, 7]),
                    $gen([3, 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionStrictWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([2, 3, 4, 5, 6, 7]),
                    $gen(['3', 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionStrictWith(...$iterables)
                    ->toArray(),
                [4, 5],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([2, 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([2, 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([2, 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen(['2', 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, '2', 3, 4, 5, 6],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen(['2', 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen(['2', 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([2, 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([2, 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([2, 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([2, '3', 4]),
                    $gen([2, 3, 4, 5, 6]),
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, 2, '3', 3, 4, 5, 6],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([2, '3', 4]),
                    $gen([2, 3, 4, 5, 6]),
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 4],
            ],
            [
                [
                    $gen([1, 2, 3]),
                    $gen([2, '3', 4]),
                    $gen([2, 3, 4, 5, 6]),
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
        ];
    }

    /**
     * @param iterable $input
     * @param callable  $streamFactoryFunc
     * @param array     $expected
     * @return void
     * @dataProvider dataProviderForIterator
     */
    public function testIterator(iterable $input, callable $streamFactoryFunc, array $expected): void
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
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([2, 3, 4, 5, 6, 7]),
                    $iter([3, 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([2, 3, 4, 5, 6, 7]),
                    $iter(['3', 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([2, 3, 4, 5, 6, 7]),
                    $iter([3, 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionStrictWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([2, 3, 4, 5, 6, 7]),
                    $iter(['3', 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionStrictWith(...$iterables)
                    ->toArray(),
                [4, 5],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([2, 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([2, 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([2, 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter(['2', 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, '2', 3, 4, 5, 6],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter(['2', 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter(['2', 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([2, 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([2, 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([2, 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([2, '3', 4]),
                    $iter([2, 3, 4, 5, 6]),
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, 2, '3', 3, 4, 5, 6],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([2, '3', 4]),
                    $iter([2, 3, 4, 5, 6]),
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 4],
            ],
            [
                [
                    $iter([1, 2, 3]),
                    $iter([2, '3', 4]),
                    $iter([2, 3, 4, 5, 6]),
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
        ];
    }

    /**
     * @param iterable $input
     * @param callable     $streamFactoryFunc
     * @param array        $expected
     * @return void
     * @dataProvider dataProviderForTraversable
     */
    public function testTraversable(iterable $input, callable $streamFactoryFunc, array $expected): void
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
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([2, 3, 4, 5, 6, 7]),
                    $trav([3, 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([2, 3, 4, 5, 6, 7]),
                    $trav(['3', 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([2, 3, 4, 5, 6, 7]),
                    $trav([3, 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionStrictWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([2, 3, 4, 5, 6, 7]),
                    $trav(['3', 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionStrictWith(...$iterables)
                    ->toArray(),
                [4, 5],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([2, 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([2, 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([2, 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav(['2', 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, '2', 3, 4, 5, 6],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav(['2', 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav(['2', 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([2, 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([2, 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([2, 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([2, '3', 4]),
                    $trav([2, 3, 4, 5, 6]),
                    1,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [1, 2, '3', 3, 4, 5, 6],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([2, '3', 4]),
                    $trav([2, 3, 4, 5, 6]),
                    2,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2, 4],
            ],
            [
                [
                    $trav([1, 2, 3]),
                    $trav([2, '3', 4]),
                    $trav([2, 3, 4, 5, 6]),
                    3,
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionStrictWith(array_pop($iterables), ...$iterables)
                    ->toArray(),
                [2],
            ],
        ];
    }
}

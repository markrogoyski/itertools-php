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
     * @param array $input
     * @param callable $streamFactoryFunc
     * @param array $expected
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
                    ->intersectionNonStrictWith(...$iterables)
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
                    ->intersectionNonStrictWith(...$iterables)
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
                [4, 5],
            ],
            [
                [
                    [1, 2, 3, 4, 5, 6, 7, 8, 9],
                    ['1', '2', 3, 4, 5, 6, 7, '8', '9'],
                    [1, 3, 5, 7, 9, 11],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [3, 5, 7],
            ],
            [
                [
                    [1, 2, 3],
                    [1, 1, 1],
                    [[1, 1], [2, 1], [3, 1], [1, 2], [1, 3]],
                    [[1, 3], [1, 1], [1, 4], [2, 1]],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->zipWith(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [[1, 1], [2, 1]],
            ],
            [
                [
                    [1, 2, 3],
                    ['a', 'b', 'c'],
                    [[1, 'a'], [2, 'b'], [3, 'c'], ['a', 2], ['a', 3]],
                    [['c', 3], [1, 'a'], ['d', 4], [2, 'b']],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->zipWith(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [[1, 'a'], [2, 'b']],
            ],
        ];
    }

    /**
     * @param array $input
     * @param int $minIntersectionCount
     * @param callable $streamFactoryFunc
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForPartialIntersectionArray
     */
    public function testPartialIntersectionArray(
        array $input,
        int $minIntersectionCount,
        callable $streamFactoryFunc,
        array $expected
    ): void {
        // Given
        $result = $streamFactoryFunc($minIntersectionCount, $input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForPartialIntersectionArray(): array
    {
        return [
            [
                [
                    [1, 2],
                    [2, 3, 4],
                    [2, 3, 4, 5, 6],
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    [1, 2],
                    [2, 3, 4],
                    [2, 3, 4, 5, 6],
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    [1, 2],
                    [2, 3, 4],
                    [2, 3, 4, 5, 6],
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    [1, 2],
                    ['2', 3, 4],
                    [2, 3, 4, 5, 6],
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, '2', 3, 4, 5, 6],
            ],
            [
                [
                    [1, 2],
                    ['2', 3, 4],
                    [2, 3, 4, 5, 6],
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    [1, 2],
                    ['2', 3, 4],
                    [2, 3, 4, 5, 6],
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    [1, 2],
                    [2, 3, 4],
                    [2, 3, 4, 5, 6],
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    [1, 2],
                    [2, 3, 4],
                    [2, 3, 4, 5, 6],
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    [1, 2],
                    [2, 3, 4],
                    [2, 3, 4, 5, 6],
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    [1, 2],
                    [2, '3', 4],
                    [2, 3, 4, 5, 6],
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 2, '3', 3, 4, 5, 6],
            ],
            [
                [
                    [1, 2],
                    [2, '3', 4],
                    [2, 3, 4, 5, 6],
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 4],
            ],
            [
                [
                    [1, 2, 3],
                    [2, '3', 4],
                    [2, 3, 4, 5, 6],
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    ['php', 'python', 'c++', 'java', 'c#', 'javascript', 'typescript'],
                    ['php', 'python', 'javascript', 'typescript'],
                    ['php', 'java', 'c#', 'typescript'],
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                ['php', 'python', 'java', 'typescript', 'c#', 'javascript'],
            ],
            [
                [
                    [1, 2, 3, 4, 5, 6, 7, 8, 9],
                    ['1', '2', 3, 4, 5, 6, 7, '8', '9'],
                    [1, 3, 5, 7, 9, 11],
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 3, 4, 5, 6, 7, 9],
            ],
        ];
    }

    /**
     * @param iterable $input
     * @param callable $streamFactoryFunc
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForGenerators
     */
    public function testGenerators(iterable $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGenerators(): array
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
                    ->intersectionNonStrictWith(...$iterables)
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
                    ->intersectionNonStrictWith(...$iterables)
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
                [4, 5],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                    $gen(['1', '2', 3, 4, 5, 6, 7, '8', '9']),
                    $gen([1, 3, 5, 7, 9, 11]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [3, 5, 7],
            ],
            [
                [
                    $gen([1, 2, 3]),
                    $gen([1, 1, 1]),
                    $gen([[1, 1], [2, 1], [3, 1], [1, 2], [1, 3]]),
                    $gen([[1, 3], [1, 1], [1, 4], [2, 1]]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->zipWith(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [[1, 1], [2, 1]],
            ],
            [
                [
                    $gen([1, 2, 3]),
                    $gen(['a', 'b', 'c']),
                    $gen([[1, 'a'], [2, 'b'], [3, 'c'], ['a', 2], ['a', 3]]),
                    $gen([['c', 3], [1, 'a'], ['d', 4], [2, 'b']]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->zipWith(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [[1, 'a'], [2, 'b']],
            ],
        ];
    }

    /**
     * @param array $input
     * @param int $minIntersectionCount
     * @param callable $streamFactoryFunc
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForPartialIntersectionGenerators
     */
    public function testPartialIntersectionGenerators(
        array $input,
        int $minIntersectionCount,
        callable $streamFactoryFunc,
        array $expected
    ): void {
        // Given
        $result = $streamFactoryFunc($minIntersectionCount, $input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForPartialIntersectionGenerators(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                [
                    $gen([1, 2]),
                    $gen([2, 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([2, 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([2, 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen(['2', 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, '2', 3, 4, 5, 6],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen(['2', 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen(['2', 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([2, 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([2, 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([2, 3, 4]),
                    $gen([2, 3, 4, 5, 6]),
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([2, '3', 4]),
                    $gen([2, 3, 4, 5, 6]),
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 2, '3', 3, 4, 5, 6],
            ],
            [
                [
                    $gen([1, 2]),
                    $gen([2, '3', 4]),
                    $gen([2, 3, 4, 5, 6]),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 4],
            ],
            [
                [
                    $gen([1, 2, 3]),
                    $gen([2, '3', 4]),
                    $gen([2, 3, 4, 5, 6]),
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $gen(['php', 'python', 'c++', 'java', 'c#', 'javascript', 'typescript']),
                    $gen(['php', 'python', 'javascript', 'typescript']),
                    $gen(['php', 'java', 'c#', 'typescript']),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                ['php', 'python', 'java', 'typescript', 'c#', 'javascript'],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                    $gen(['1', '2', 3, 4, 5, 6, 7, '8', '9']),
                    $gen([1, 3, 5, 7, 9, 11]),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 3, 4, 5, 6, 7, 9],
            ],
        ];
    }

    /**
     * @param iterable $input
     * @param callable $streamFactoryFunc
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForIterators
     */
    public function testIterators(iterable $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIterators(): array
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
                    ->intersectionNonStrictWith(...$iterables)
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
                    ->intersectionNonStrictWith(...$iterables)
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
                [4, 5],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                    $iter(['1', '2', 3, 4, 5, 6, 7, '8', '9']),
                    $iter([1, 3, 5, 7, 9, 11]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [3, 5, 7],
            ],
            [
                [
                    $iter([1, 2, 3]),
                    $iter([1, 1, 1]),
                    $iter([[1, 1], [2, 1], [3, 1], [1, 2], [1, 3]]),
                    $iter([[1, 3], [1, 1], [1, 4], [2, 1]]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->zipWith(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [[1, 1], [2, 1]],
            ],
            [
                [
                    $iter([1, 2, 3]),
                    $iter(['a', 'b', 'c']),
                    $iter([[1, 'a'], [2, 'b'], [3, 'c'], ['a', 2], ['a', 3]]),
                    $iter([['c', 3], [1, 'a'], ['d', 4], [2, 'b']]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->zipWith(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [[1, 'a'], [2, 'b']],
            ],
        ];
    }

    /**
     * @param array $input
     * @param int $minIntersectionCount
     * @param callable $streamFactoryFunc
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForPartialIntersectionIterators
     */
    public function testPartialIntersectionIterators(
        array $input,
        int $minIntersectionCount,
        callable $streamFactoryFunc,
        array $expected
    ): void {
        // Given
        $result = $streamFactoryFunc($minIntersectionCount, $input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForPartialIntersectionIterators(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                [
                    $iter([1, 2]),
                    $iter([2, 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([2, 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([2, 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter(['2', 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, '2', 3, 4, 5, 6],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter(['2', 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter(['2', 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([2, 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([2, 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([2, 3, 4]),
                    $iter([2, 3, 4, 5, 6]),
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([2, '3', 4]),
                    $iter([2, 3, 4, 5, 6]),
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 2, '3', 3, 4, 5, 6],
            ],
            [
                [
                    $iter([1, 2]),
                    $iter([2, '3', 4]),
                    $iter([2, 3, 4, 5, 6]),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 4],
            ],
            [
                [
                    $iter([1, 2, 3]),
                    $iter([2, '3', 4]),
                    $iter([2, 3, 4, 5, 6]),
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $iter(['php', 'python', 'c++', 'java', 'c#', 'javascript', 'typescript']),
                    $iter(['php', 'python', 'javascript', 'typescript']),
                    $iter(['php', 'java', 'c#', 'typescript']),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                ['php', 'python', 'java', 'typescript', 'c#', 'javascript'],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                    $iter(['1', '2', 3, 4, 5, 6, 7, '8', '9']),
                    $iter([1, 3, 5, 7, 9, 11]),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 3, 4, 5, 6, 7, 9],
            ],
        ];
    }

    /**
     * @param iterable $input
     * @param callable $streamFactoryFunc
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForTraversables
     */
    public function testTraversables(iterable $input, callable $streamFactoryFunc, array $expected): void
    {
        // Given
        $result = $streamFactoryFunc($input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversables(): array
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
                    ->intersectionNonStrictWith(...$iterables)
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
                    ->intersectionNonStrictWith(...$iterables)
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
                [4, 5],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                    $trav(['1', '2', 3, 4, 5, 6, 7, '8', '9']),
                    $trav([1, 3, 5, 7, 9, 11]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [3, 5, 7],
            ],
            [
                [
                    $trav([1, 2, 3]),
                    $trav([1, 1, 1]),
                    $trav([[1, 1], [2, 1], [3, 1], [1, 2], [1, 3]]),
                    $trav([[1, 3], [1, 1], [1, 4], [2, 1]]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->zipWith(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [[1, 1], [2, 1]],
            ],
            [
                [
                    $trav([1, 2, 3]),
                    $trav(['a', 'b', 'c']),
                    $trav([[1, 'a'], [2, 'b'], [3, 'c'], ['a', 2], ['a', 3]]),
                    $trav([['c', 3], [1, 'a'], ['d', 4], [2, 'b']]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->zipWith(array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [[1, 'a'], [2, 'b']],
            ],
        ];
    }

    /**
     * @param array $input
     * @param int $minIntersectionCount
     * @param callable $streamFactoryFunc
     * @param array $expected
     * @return void
     * @dataProvider dataProviderForPartialIntersectionTraversables
     */
    public function testPartialIntersectionTraversables(
        array $input,
        int $minIntersectionCount,
        callable $streamFactoryFunc,
        array $expected
    ): void {
        // Given
        $result = $streamFactoryFunc($minIntersectionCount, $input);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForPartialIntersectionTraversables(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                [
                    $trav([1, 2]),
                    $trav([2, 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([2, 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([2, 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav(['2', 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, '2', 3, 4, 5, 6],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav(['2', 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav(['2', 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([2, 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([2, 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 3, 4],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([2, 3, 4]),
                    $trav([2, 3, 4, 5, 6]),
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([2, '3', 4]),
                    $trav([2, 3, 4, 5, 6]),
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 2, '3', 3, 4, 5, 6],
            ],
            [
                [
                    $trav([1, 2]),
                    $trav([2, '3', 4]),
                    $trav([2, 3, 4, 5, 6]),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2, 4],
            ],
            [
                [
                    $trav([1, 2, 3]),
                    $trav([2, '3', 4]),
                    $trav([2, 3, 4, 5, 6]),
                ],
                3,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [2],
            ],
            [
                [
                    $trav(['php', 'python', 'c++', 'java', 'c#', 'javascript', 'typescript']),
                    $trav(['php', 'python', 'javascript', 'typescript']),
                    $trav(['php', 'java', 'c#', 'typescript']),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionNonStrictWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                ['php', 'python', 'java', 'typescript', 'c#', 'javascript'],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                    $trav(['1', '2', 3, 4, 5, 6, 7, '8', '9']),
                    $trav([1, 3, 5, 7, 9, 11]),
                ],
                2,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 3, 4, 5, 6, 7, 9],
            ],
        ];
    }
}

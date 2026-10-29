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

    public static function dataProviderForArray(): array
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
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ],
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinctBy(fn ($item) => $item['name'])
                    ->toArray(),
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6, 7],
                    [3, 4, 5, 6, 7, 8, 9],
                ],
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->intersectionCoerciveWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6, 7],
                    ['3', 4, 5, 6, 7, 8, 9],
                ],
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->intersectionCoerciveWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6, 7],
                    [3, 4, 5, 6, 7, 8, 9],
                ],
                fn (array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->zipWith(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->zipWith(\array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [[1, 'a'], [2, 'b']],
            ],
            [
                [
                    [1, 2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7, 8],
                    [5, 6, 7, 8, 9, 10],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceWith(...$iterables)
                    ->toArray(),
                [1, 2, 9, 10],
            ],
            [
                [
                    [1, 2, '3', 4, 5, 6],
                    [3, 4, 5, 6, 7, 8],
                    [5, 6, 7, 8, 9, 10],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceWith(...$iterables)
                    ->toArray(),
                [1, 3, 2, '3', 9, 10],
            ],
            [
                [
                    [1, 2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7, 8],
                    [5, 6, 7, 8, 9, 10],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2, 9, 10],
            ],
            [
                [
                    [1, 2, '3', 4, 5, 6],
                    [3, 4, 5, 6, 7, 8],
                    [5, 6, 7, 8, 9, 10],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2, 9, 10],
            ],
            [
                [
                    [1, 2, 3, 4, 7],
                    ['1', 2, 3, 5, 8],
                    [1, 2, 3, 6, 9],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceWith(...$iterables)
                    ->toArray(),
                ['1', 4, 5, 6, 7, 8, 9],
            ],
            [
                [
                    [1, 2, 3, 4, 7],
                    ['1', 2, 3, 5, 8],
                    [1, 2, 3, 6, 9],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceCoerciveWith(...$iterables)
                    ->toArray(),
                [4, 5, 6, 7, 8, 9],
            ],
            [
                [
                    [1, 2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7, 8],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 2],
            ],
            [
                [
                    [1, 2, '3', 4, 5, 6],
                    [3, 4, 5, 6, 7, 8],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 2, '3'],
            ],
            [
                [
                    [1, 2, '3', 4, 5, 6],
                    [3, 4, 5, 6, 7, 8],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [2, 4],
                    [1, 3],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [5],
            ],
            [
                [
                    [1, 1, 2, 2, 3],
                    [1, 2],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                [
                    [1, 2, 3, 4, 7],
                    ['1', 2, 3, 5, 8],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 4, 7],
            ],
            [
                [
                    [1, 2, 3, 4, 7],
                    ['1', 2, 3, 5, 8],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceCoerciveWith(...$iterables)
                    ->toArray(),
                [4, 7],
            ],
            [
                [
                    [],
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionWith(...$iterables)
                    ->toArray(),
                [2, 3, 4, 5, 6, 7],
            ],
            [
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionWith(...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, 7],
            ],
            [
                [
                    [],
                    [2, 3, '4', 5, '6'],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionCoerciveWith(...$iterables)
                    ->toArray(),
                [2, 3, 4, 5, 6, 7],
            ],
            [
                [
                    [1, '2', 3, '4', 5],
                    [2, '3', 4, '5', '6'],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, 7],
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

    public static function dataProviderForPartialIntersectionArray(): array
    {
        return [
            [
                [
                    [1, 2],
                    [2, 3, 4],
                    [2, 3, 4, 5, 6],
                ],
                1,
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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

    public static function dataProviderForGenerators(): array
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
                $gen([
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinctBy(fn ($item) => $item['name'])
                    ->toArray(),
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([2, 3, 4, 5, 6, 7]),
                    $gen([3, 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->intersectionCoerciveWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([2, 3, 4, 5, 6, 7]),
                    $gen(['3', 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->intersectionCoerciveWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([2, 3, 4, 5, 6, 7]),
                    $gen([3, 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->zipWith(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->zipWith(\array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [[1, 'a'], [2, 'b']],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5, 6]),
                    $gen([3, 4, 5, 6, 7, 8]),
                    $gen([5, 6, 7, 8, 9, 10]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceWith(...$iterables)
                    ->toArray(),
                [1, 2, 9, 10],
            ],
            [
                [
                    $gen([1, 2, '3', 4, 5, 6]),
                    $gen([3, 4, 5, 6, 7, 8]),
                    $gen([5, 6, 7, 8, 9, 10]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceWith(...$iterables)
                    ->toArray(),
                [1, 3, 2, '3', 9, 10],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5, 6]),
                    $gen([3, 4, 5, 6, 7, 8]),
                    $gen([5, 6, 7, 8, 9, 10]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2, 9, 10],
            ],
            [
                [
                    $gen([1, 2, '3', 4, 5, 6]),
                    $gen([3, 4, 5, 6, 7, 8]),
                    $gen([5, 6, 7, 8, 9, 10]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2, 9, 10],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 7]),
                    $gen(['1', 2, 3, 5, 8]),
                    $gen([1, 2, 3, 6, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceWith(...$iterables)
                    ->toArray(),
                ['1', 4, 5, 6, 7, 8, 9],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 7]),
                    $gen(['1', 2, 3, 5, 8]),
                    $gen([1, 2, 3, 6, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceCoerciveWith(...$iterables)
                    ->toArray(),
                [4, 5, 6, 7, 8, 9],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5, 6]),
                    $gen([3, 4, 5, 6, 7, 8]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 2],
            ],
            [
                [
                    $gen([1, 2, '3', 4, 5, 6]),
                    $gen([3, 4, 5, 6, 7, 8]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 2, '3'],
            ],
            [
                [
                    $gen([1, 2, '3', 4, 5, 6]),
                    $gen([3, 4, 5, 6, 7, 8]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 7]),
                    $gen(['1', 2, 3, 5, 8]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 4, 7],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 7]),
                    $gen(['1', 2, 3, 5, 8]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceCoerciveWith(...$iterables)
                    ->toArray(),
                [4, 7],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    $gen([2, 4]),
                    $gen([1, 3]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [5],
            ],
            [
                [
                    $gen([1, 1, 2, 2, 3]),
                    $gen([1, 2]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                [
                    $gen([]),
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionWith(...$iterables)
                    ->toArray(),
                [2, 3, 4, 5, 6, 7],
            ],
            [
                [
                    $gen([1, 2, 3, 4, 5]),
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionWith(...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, 7],
            ],
            [
                [
                    $gen([]),
                    [2, 3, '4', 5, '6'],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionCoerciveWith(...$iterables)
                    ->toArray(),
                [2, 3, 4, 5, 6, 7],
            ],
            [
                [
                    $gen([1, '2', 3, '4', 5]),
                    [2, '3', 4, '5', '6'],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, 7],
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

    public static function dataProviderForPartialIntersectionGenerators(): array
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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

    public static function dataProviderForIterators(): array
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
                $iter([
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinctBy(fn ($item) => $item['name'])
                    ->toArray(),
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([2, 3, 4, 5, 6, 7]),
                    $iter([3, 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->intersectionCoerciveWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([2, 3, 4, 5, 6, 7]),
                    $iter(['3', 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->intersectionCoerciveWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([2, 3, 4, 5, 6, 7]),
                    $iter([3, 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->zipWith(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->zipWith(\array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [[1, 'a'], [2, 'b']],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5, 6]),
                    $iter([3, 4, 5, 6, 7, 8]),
                    $iter([5, 6, 7, 8, 9, 10]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceWith(...$iterables)
                    ->toArray(),
                [1, 2, 9, 10],
            ],
            [
                [
                    $iter([1, 2, '3', 4, 5, 6]),
                    $iter([3, 4, 5, 6, 7, 8]),
                    $iter([5, 6, 7, 8, 9, 10]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceWith(...$iterables)
                    ->toArray(),
                [1, 3, 2, '3', 9, 10],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5, 6]),
                    $iter([3, 4, 5, 6, 7, 8]),
                    $iter([5, 6, 7, 8, 9, 10]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2, 9, 10],
            ],
            [
                [
                    $iter([1, 2, '3', 4, 5, 6]),
                    $iter([3, 4, 5, 6, 7, 8]),
                    $iter([5, 6, 7, 8, 9, 10]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2, 9, 10],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 7]),
                    $iter(['1', 2, 3, 5, 8]),
                    $iter([1, 2, 3, 6, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceWith(...$iterables)
                    ->toArray(),
                ['1', 4, 5, 6, 7, 8, 9],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 7]),
                    $iter(['1', 2, 3, 5, 8]),
                    $iter([1, 2, 3, 6, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceCoerciveWith(...$iterables)
                    ->toArray(),
                [4, 5, 6, 7, 8, 9],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5, 6]),
                    $iter([3, 4, 5, 6, 7, 8]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 2],
            ],
            [
                [
                    $iter([1, 2, '3', 4, 5, 6]),
                    $iter([3, 4, 5, 6, 7, 8]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 2, '3'],
            ],
            [
                [
                    $iter([1, 2, '3', 4, 5, 6]),
                    $iter([3, 4, 5, 6, 7, 8]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 7]),
                    $iter(['1', 2, 3, 5, 8]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 4, 7],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 7]),
                    $iter(['1', 2, 3, 5, 8]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceCoerciveWith(...$iterables)
                    ->toArray(),
                [4, 7],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    $iter([2, 4]),
                    $iter([1, 3]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [5],
            ],
            [
                [
                    $iter([1, 1, 2, 2, 3]),
                    $iter([1, 2]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                [
                    $iter([]),
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionWith(...$iterables)
                    ->toArray(),
                [2, 3, 4, 5, 6, 7],
            ],
            [
                [
                    $iter([1, 2, 3, 4, 5]),
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionWith(...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, 7],
            ],
            [
                [
                    $iter([]),
                    [2, 3, '4', 5, '6'],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionCoerciveWith(...$iterables)
                    ->toArray(),
                [2, 3, 4, 5, 6, 7],
            ],
            [
                [
                    $iter([1, '2', 3, '4', 5]),
                    [2, '3', 4, '5', '6'],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, 7],
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

    public static function dataProviderForPartialIntersectionIterators(): array
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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

    public static function dataProviderForTraversables(): array
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
                $trav([
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Mary', 'id' => 3],
                    ['name' => 'John', 'id' => 4],
                    ['name' => 'Jane', 'id' => 5],
                ]),
                fn (iterable $iterable) => Stream::of($iterable)
                    ->distinctBy(fn ($item) => $item['name'])
                    ->toArray(),
                [
                    ['name' => 'John', 'id' => 1],
                    ['name' => 'Mary', 'id' => 2],
                    ['name' => 'Jane', 'id' => 5],
                ],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([2, 3, 4, 5, 6, 7]),
                    $trav([3, 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->intersectionCoerciveWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([2, 3, 4, 5, 6, 7]),
                    $trav(['3', 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->intersectionCoerciveWith(...$iterables)
                    ->toArray(),
                [3, 4, 5],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([2, 3, 4, 5, 6, 7]),
                    $trav([3, 4, 5, 6, 7, 8, 9]),
                ],
                fn (array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->zipWith(\array_shift($iterables))
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
                fn (array $iterables) => Stream::of(\array_shift($iterables))
                    ->zipWith(\array_shift($iterables))
                    ->intersectionWith(...$iterables)
                    ->toArray(),
                [[1, 'a'], [2, 'b']],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5, 6]),
                    $trav([3, 4, 5, 6, 7, 8]),
                    $trav([5, 6, 7, 8, 9, 10]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceWith(...$iterables)
                    ->toArray(),
                [1, 2, 9, 10],
            ],
            [
                [
                    $trav([1, 2, '3', 4, 5, 6]),
                    $trav([3, 4, 5, 6, 7, 8]),
                    $trav([5, 6, 7, 8, 9, 10]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceWith(...$iterables)
                    ->toArray(),
                [1, 3, 2, '3', 9, 10],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5, 6]),
                    $trav([3, 4, 5, 6, 7, 8]),
                    $trav([5, 6, 7, 8, 9, 10]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2, 9, 10],
            ],
            [
                [
                    $trav([1, 2, '3', 4, 5, 6]),
                    $trav([3, 4, 5, 6, 7, 8]),
                    $trav([5, 6, 7, 8, 9, 10]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2, 9, 10],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 7]),
                    $trav(['1', 2, 3, 5, 8]),
                    $trav([1, 2, 3, 6, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceWith(...$iterables)
                    ->toArray(),
                ['1', 4, 5, 6, 7, 8, 9],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 7]),
                    $trav(['1', 2, 3, 5, 8]),
                    $trav([1, 2, 3, 6, 9]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->symmetricDifferenceCoerciveWith(...$iterables)
                    ->toArray(),
                [4, 5, 6, 7, 8, 9],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5, 6]),
                    $trav([3, 4, 5, 6, 7, 8]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 2],
            ],
            [
                [
                    $trav([1, 2, '3', 4, 5, 6]),
                    $trav([3, 4, 5, 6, 7, 8]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 2, '3'],
            ],
            [
                [
                    $trav([1, 2, '3', 4, 5, 6]),
                    $trav([3, 4, 5, 6, 7, 8]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 7]),
                    $trav(['1', 2, 3, 5, 8]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 4, 7],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 7]),
                    $trav(['1', 2, 3, 5, 8]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceCoerciveWith(...$iterables)
                    ->toArray(),
                [4, 7],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    $trav([2, 4]),
                    $trav([1, 3]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [5],
            ],
            [
                [
                    $trav([1, 1, 2, 2, 3]),
                    $trav([1, 2]),
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->differenceWith(...$iterables)
                    ->toArray(),
                [1, 2, 3],
            ],
            [
                [
                    $trav([]),
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionWith(...$iterables)
                    ->toArray(),
                [2, 3, 4, 5, 6, 7],
            ],
            [
                [
                    $trav([1, 2, 3, 4, 5]),
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionWith(...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, 7],
            ],
            [
                [
                    $trav([]),
                    [2, 3, '4', 5, '6'],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionCoerciveWith(...$iterables)
                    ->toArray(),
                [2, 3, 4, 5, 6, 7],
            ],
            [
                [
                    $trav([1, '2', 3, '4', 5]),
                    [2, '3', 4, '5', '6'],
                    [3, 4, 5, 6, 7],
                ],
                fn (array $iterables) => Stream::of(array_shift($iterables))
                    ->unionCoerciveWith(...$iterables)
                    ->toArray(),
                [1, 2, 3, 4, 5, 6, 7],
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

    public static function dataProviderForPartialIntersectionTraversables(): array
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables)
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
                fn (int $minIntersectionCount, array $iterables) => Stream::of(\array_shift($iterables))
                    ->partialIntersectionWith($minIntersectionCount, ...$iterables)
                    ->toArray(),
                [1, 3, 4, 5, 6, 7, 9],
            ],
        ];
    }

    /**
     * @test intersectionWith accepts named arguments that match its internal parameter names
     */
    public function testIntersectionWithAcceptsNamedArgumentsMatchingInternalParameterNames(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4])->intersectionWith(
            strict: [2, 3, 4, 5],
            filler: [3, 4, 5, 6],
            minIntersectionCount: [3, 4],
        )->toArray();

        // Then
        $this->assertSame([3, 4], $result);
    }

    /**
     * @test intersectionCoerciveWith accepts named arguments that match its internal parameter names
     */
    public function testIntersectionCoerciveWithAcceptsNamedArgumentsMatchingInternalParameterNames(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4])->intersectionCoerciveWith(
            strict: ['2', '3', '4'],
            filler: [3.0, 4],
            minIntersectionCount: [3, 4],
        )->toArray();

        // Then
        $this->assertSame([3, 4], $result);
    }

    /**
     * @test partialIntersectionWith accepts named arguments that match its internal parameter names
     */
    public function testPartialIntersectionWithAcceptsNamedArgumentsMatchingInternalParameterNames(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4])->partialIntersectionWith(
            2,
            strict: [2, 3, 5],
            filler: [5],
        )->toArray();

        // Then
        $this->assertSame([2, 3, 5], $result);
    }

    /**
     * @test partialIntersectionCoerciveWith accepts named arguments that match its internal parameter names
     */
    public function testPartialIntersectionCoerciveWithAcceptsNamedArgumentsMatchingInternalParameterNames(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4])->partialIntersectionCoerciveWith(
            2,
            strict: ['2', '3', '5'],
            filler: [5],
        )->toArray();

        // Then
        $this->assertSame([2, 3, '5'], $result);
    }

    /**
     * @test unionWith accepts named arguments that match its internal parameter names
     */
    public function testUnionWithAcceptsNamedArgumentsMatchingInternalParameterNames(): void
    {
        // When
        $result = Stream::of([1, 2, 3])->unionWith(
            strict: [4, 5],
            filler: [5, 6],
            minIntersectionCount: [6, 7],
        )->toArray();

        // Then
        $this->assertSame([1, 4, 5, 6, 2, 7, 3], $result);
    }

    /**
     * @test unionCoerciveWith accepts named arguments that match its internal parameter names
     */
    public function testUnionCoerciveWithAcceptsNamedArgumentsMatchingInternalParameterNames(): void
    {
        // When
        $result = Stream::of([1, 2, 3])->unionCoerciveWith(
            strict: ['4'],
            filler: [5.0],
            minIntersectionCount: [6],
        )->toArray();

        // Then
        $this->assertSame([1, '4', 5.0, 6, 2, 3], $result);
    }

    /**
     * @test differenceWith accepts named arguments that match its internal parameter names
     */
    public function testDifferenceWithAcceptsNamedArgumentsMatchingInternalParameterNames(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4])->differenceWith(
            a: [2],
            strict: [3],
        )->toArray();

        // Then
        $this->assertSame([1, 4], $result);
    }

    /**
     * @test differenceCoerciveWith accepts named arguments that match its internal parameter names
     */
    public function testDifferenceCoerciveWithAcceptsNamedArgumentsMatchingInternalParameterNames(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4])->differenceCoerciveWith(
            a: ['2'],
            strict: [3.0],
        )->toArray();

        // Then
        $this->assertSame([1, 4], $result);
    }

    /**
     * @test symmetricDifferenceWith accepts named arguments that match its internal parameter names
     */
    public function testSymmetricDifferenceWithAcceptsNamedArgumentsMatchingInternalParameterNames(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4])->symmetricDifferenceWith(
            strict: [2, 3, 4, 5],
            filler: [2, 3, 4],
        )->toArray();

        // Then
        $this->assertSame([1, 5], $result);
    }

    /**
     * @test symmetricDifferenceCoerciveWith accepts named arguments that match its internal parameter names
     */
    public function testSymmetricDifferenceCoerciveWithAcceptsNamedArgumentsMatchingInternalParameterNames(): void
    {
        // When
        $result = Stream::of([1, 2, 3, 4])->symmetricDifferenceCoerciveWith(
            strict: ['2', '3', '4', '5'],
            filler: [2.0, 3, 4],
        )->toArray();

        // Then
        $this->assertSame([1, '5'], $result);
    }

    /**
     * @test         symmetricDifferenceWith and symmetricDifferenceCoerciveWith without extra iterables yield the elements of the source
     * @dataProvider dataProviderForNoExtraIterables
     * @param        iterable<mixed> $source
     * @param        callable $streamFunc
     * @param        array<mixed> $expected
     */
    public function testSymmetricDifferenceWithNoExtraIterablesYieldsSource(
        iterable $source,
        callable $streamFunc,
        array $expected
    ): void {
        // Given
        $stream = Stream::of($source);

        // When
        $result = $streamFunc($stream);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with the two-argument form, which the no-extra-iterables form must match:
     *   Set::symmetricDifference($data, []) and Set::symmetricDifferenceCoercive($data, [])
     */
    public static function dataProviderForNoExtraIterables(): array
    {
        return [
            'strict array' => [
                [1, '1', 2, 1, 2],
                static fn (Stream $stream): array => $stream->symmetricDifferenceWith()->toArray(),
                [1, 1, '1', 2, 2],
            ],
            'strict generator' => [
                GeneratorFixture::getGenerator([1, '1', 2, 1, 2]),
                static fn (Stream $stream): array => $stream->symmetricDifferenceWith()->toArray(),
                [1, 1, '1', 2, 2],
            ],
            'strict iterator' => [
                new ArrayIteratorFixture([1, '1', 2, 1, 2]),
                static fn (Stream $stream): array => $stream->symmetricDifferenceWith()->toArray(),
                [1, 1, '1', 2, 2],
            ],
            'strict iterator aggregate' => [
                new IteratorAggregateFixture([1, '1', 2, 1, 2]),
                static fn (Stream $stream): array => $stream->symmetricDifferenceWith()->toArray(),
                [1, 1, '1', 2, 2],
            ],
            'coercive array' => [
                [1, '1', 2, 1, 2],
                static fn (Stream $stream): array => $stream->symmetricDifferenceCoerciveWith()->toArray(),
                [1, 1, 1, 2, 2],
            ],
            'coercive generator' => [
                GeneratorFixture::getGenerator([1, '1', 2, 1, 2]),
                static fn (Stream $stream): array => $stream->symmetricDifferenceCoerciveWith()->toArray(),
                [1, 1, 1, 2, 2],
            ],
            'coercive iterator' => [
                new ArrayIteratorFixture([1, '1', 2, 1, 2]),
                static fn (Stream $stream): array => $stream->symmetricDifferenceCoerciveWith()->toArray(),
                [1, 1, 1, 2, 2],
            ],
            'coercive iterator aggregate' => [
                new IteratorAggregateFixture([1, '1', 2, 1, 2]),
                static fn (Stream $stream): array => $stream->symmetricDifferenceCoerciveWith()->toArray(),
                [1, 1, 1, 2, 2],
            ],
        ];
    }

    /**
     * @test partialIntersectionWith throws for a minimum intersection count below 1
     * @dataProvider dataProviderForNonPositiveMinIntersectionCount
     * @param int $minIntersectionCount
     * @param array<iterable> $iterables
     */
    public function testPartialIntersectionWithNonPositiveMinIntersectionCountThrows(int $minIntersectionCount, array $iterables): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Minimum intersection count must be ≥ 1. Got {$minIntersectionCount}");

        // When
        foreach (Stream::of([1, 2, 3])->partialIntersectionWith($minIntersectionCount, ...$iterables) as $_) {
            break;
        }
    }

    /**
     * @test partialIntersectionCoerciveWith throws for a minimum intersection count below 1
     * @dataProvider dataProviderForNonPositiveMinIntersectionCount
     * @param int $minIntersectionCount
     * @param array<iterable> $iterables
     */
    public function testPartialIntersectionCoerciveWithNonPositiveMinIntersectionCountThrows(int $minIntersectionCount, array $iterables): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Minimum intersection count must be ≥ 1. Got {$minIntersectionCount}");

        // When
        foreach (Stream::of([1, 2, 3])->partialIntersectionCoerciveWith($minIntersectionCount, ...$iterables) as $_) {
            break;
        }
    }

    public static function dataProviderForNonPositiveMinIntersectionCount(): array
    {
        return [
            [0, [[1, 2, 3], [2, 3, 4]]],
            [-1, [[1, 2, 3], [2, 3, 4]]],
            [\PHP_INT_MIN, [[1, 2, 3], [2, 3, 4]]],
            [0, [GeneratorFixture::getGenerator([1, 2, 3]), GeneratorFixture::getGenerator([2, 3, 4])]],
            [-1, [GeneratorFixture::getGenerator([1, 2, 3]), GeneratorFixture::getGenerator([2, 3, 4])]],
            [0, [new ArrayIteratorFixture([1, 2, 3]), new ArrayIteratorFixture([2, 3, 4])]],
            [-1, [new ArrayIteratorFixture([1, 2, 3]), new ArrayIteratorFixture([2, 3, 4])]],
            [0, [new IteratorAggregateFixture([1, 2, 3]), new IteratorAggregateFixture([2, 3, 4])]],
            [-1, [new IteratorAggregateFixture([1, 2, 3]), new IteratorAggregateFixture([2, 3, 4])]],
            [0, [[], []]],
            [0, [[1, 2, 3]]],
            [0, []],
        ];
    }

    /**
     * @test intersectionWith reads a stream of the generator it holds as independent copies of the generator
     */
    public function testIntersectionWithStreamOfSameGenerator(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 2, 3]);

        // When
        $result = Stream::of($generator)
            ->intersectionWith(Stream::of($generator))
            ->toArray();

        // Then
        $this->assertSame([1, 2, 2, 3], $result);
    }

    /**
     * @test unionWith reads a stream of the generator it holds as independent copies of the generator
     */
    public function testUnionWithStreamOfSameGenerator(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 2, 3]);

        // When
        $result = Stream::of($generator)
            ->unionWith(Stream::of($generator))
            ->toArray();

        // Then
        $this->assertSame([1, 2, 2, 3], $result);
    }
}

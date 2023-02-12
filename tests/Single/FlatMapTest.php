<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class FlatMapTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArray
     * @param array $iterable
     * @param callable $func
     * @param array $expected
     */
    public function testArray(array $iterable, callable $func, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::flatMap($iterable, $func) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                fn ($item) => [$item],
                [],
            ],
            [
                [0],
                fn ($item) => [$item],
                [0],
            ],
            [
                [1],
                fn ($item) => [$item],
                [1],
            ],
            [
                [2],
                fn ($item) => [$item],
                [2],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($item) => [$item],
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [2],
                fn ($item) => [$item, $item],
                [2, 2],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($item) => [$item, $item],
                [0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($item) => [$item, -$item],
                [0, 0, 1, -1, 2, -2, 3, -3, 4, -4, 5, -5],
            ],
            [
                [],
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                [0],
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                [1],
                fn ($item) => Single::repeat($item, $item),
                [1],
            ],
            [
                [2],
                fn ($item) => Single::repeat($item, $item),
                [2, 2],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($item) => Single::repeat($item, $item),
                [1, 2, 2, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 5],
            ],
            [
                [
                    ['name' => 'bird', 'eggs' => 2],
                    ['name' => 'lizard', 'eggs' => 3],
                    ['name' => 'echidna', 'eggs' => 1],
                    ['name' => 'tyrannosaur', 'eggs' => 0],
                ],
                fn ($animal) => Single::repeat($animal['name'], $animal['eggs']),
                ['bird', 'bird', 'lizard', 'lizard', 'lizard', 'echidna'],
            ],
            [
                [[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10],
                fn ($item, $func) => is_iterable($item)
                    ? Single::flatMap($item, $func)
                    : [$item],
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            ],
            [
                [[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10],
                fn ($item, $func) => is_iterable($item)
                    ? Single::flatMap($item, $func)
                    : $item,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            ],
            [
                [1, 2, 3, 4, 5],
                fn ($item) => $item + 1,
                [2, 3, 4, 5, 6]
            ],
            [
                [1, 2, 3, 4, 5],
                fn ($item) => ($item % 2 === 0) ? [$item, $item] : $item,
                [1, 2, 2, 3, 4, 4, 5]
            ],
            [
                [1, 2, [3], [4, 5], 6, []],
                fn ($x) => $x,
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($item) => [$item, $item, [$item]],
                [0, 0, [0], 1, 1, [1], 2, 2, [2], 3, 3, [3], 4, 4, [4], 5, 5, [5]],
            ],
            [
                ["it's Sunny in", "", "California"],
                fn ($words) => \explode(' ', $words),
                ["it's","Sunny","in", "", "California"],
            ],
            [
                [5, 4, -3, 20, 17, -33, -4, 18],
                fn ($x) => $x < 0 ? [] : ($x % 2 === 0 ? [$x] : [$x - 1, 1]),
                [4, 1, 4, 20, 16, 1, 18],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $iterable
     * @param callable $func
     * @param array $expected
     */
    public function testGenerators(\Generator $iterable, callable $func, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::flatMap($iterable, $func) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = fn ($data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                fn ($item) => [$item],
                [],
            ],
            [
                $gen([0]),
                fn ($item) => [$item],
                [0],
            ],
            [
                $gen([1]),
                fn ($item) => [$item],
                [1],
            ],
            [
                $gen([2]),
                fn ($item) => [$item],
                [2],
            ],
            [
                $gen([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item],
                [0, 1, 2, 3, 4, 5],
            ],
            [
                $gen([2]),
                fn ($item) => [$item, $item],
                [2, 2],
            ],
            [
                $gen([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item, $item],
                [0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
            ],
            [
                $gen([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item, -$item],
                [0, 0, 1, -1, 2, -2, 3, -3, 4, -4, 5, -5],
            ],
            [
                $gen([]),
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                $gen([0]),
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                $gen([1]),
                fn ($item) => Single::repeat($item, $item),
                [1],
            ],
            [
                $gen([2]),
                fn ($item) => Single::repeat($item, $item),
                [2, 2],
            ],
            [
                $gen([0, 1, 2, 3, 4, 5]),
                fn ($item) => Single::repeat($item, $item),
                [1, 2, 2, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 5],
            ],
            [
                $gen([
                    ['name' => 'bird', 'eggs' => 2],
                    ['name' => 'lizard', 'eggs' => 3],
                    ['name' => 'echidna', 'eggs' => 1],
                    ['name' => 'tyrannosaur', 'eggs' => 0],
                ]),
                fn ($animal) => Single::repeat($animal['name'], $animal['eggs']),
                ['bird', 'bird', 'lizard', 'lizard', 'lizard', 'echidna'],
            ],
            [
                $gen([[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10]),
                fn ($item, $func) => is_iterable($item)
                    ? Single::flatMap($item, $func)
                    : [$item],
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            ],
            [
                $gen([[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10]),
                fn ($item, $func) => is_iterable($item)
                    ? Single::flatMap($item, $func)
                    : $item,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn ($item) => $item + 1,
                [2, 3, 4, 5, 6]
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn ($item) => ($item % 2 === 0) ? [$item, $item] : $item,
                [1, 2, 2, 3, 4, 4, 5]
            ],
            [
                $gen([1, 2, [3], [4, 5], 6, []]),
                fn ($x) => $x,
                [1, 2, 3, 4, 5, 6],
            ],
            [
                $gen([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item, $item, [$item]],
                [0, 0, [0], 1, 1, [1], 2, 2, [2], 3, 3, [3], 4, 4, [4], 5, 5, [5]],
            ],
            [
                $gen(["it's Sunny in", "", "California"]),
                fn ($words) => \explode(' ', $words),
                ["it's","Sunny","in", "", "California"],
            ],
            [
                $gen([5, 4, -3, 20, 17, -33, -4, 18]),
                fn ($x) => $x < 0 ? [] : ($x % 2 === 0 ? [$x] : [$x - 1, 1]),
                [4, 1, 4, 20, 16, 1, 18],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $iterable
     * @param callable $func
     * @param array $expected
     */
    public function testIterators(\Iterator $iterable, callable $func, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::flatMap($iterable, $func) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = fn ($data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                fn ($item) => [$item],
                [],
            ],
            [
                $iter([0]),
                fn ($item) => [$item],
                [0],
            ],
            [
                $iter([1]),
                fn ($item) => [$item],
                [1],
            ],
            [
                $iter([2]),
                fn ($item) => [$item],
                [2],
            ],
            [
                $iter([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item],
                [0, 1, 2, 3, 4, 5],
            ],
            [
                $iter([2]),
                fn ($item) => [$item, $item],
                [2, 2],
            ],
            [
                $iter([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item, $item],
                [0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
            ],
            [
                $iter([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item, -$item],
                [0, 0, 1, -1, 2, -2, 3, -3, 4, -4, 5, -5],
            ],
            [
                $iter([]),
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                $iter([0]),
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                $iter([1]),
                fn ($item) => Single::repeat($item, $item),
                [1],
            ],
            [
                $iter([2]),
                fn ($item) => Single::repeat($item, $item),
                [2, 2],
            ],
            [
                $iter([0, 1, 2, 3, 4, 5]),
                fn ($item) => Single::repeat($item, $item),
                [1, 2, 2, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 5],
            ],
            [
                $iter([
                    ['name' => 'bird', 'eggs' => 2],
                    ['name' => 'lizard', 'eggs' => 3],
                    ['name' => 'echidna', 'eggs' => 1],
                    ['name' => 'tyrannosaur', 'eggs' => 0],
                ]),
                fn ($animal) => Single::repeat($animal['name'], $animal['eggs']),
                ['bird', 'bird', 'lizard', 'lizard', 'lizard', 'echidna'],
            ],
            [
                $iter([[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10]),
                fn ($item, $func) => is_iterable($item)
                    ? Single::flatMap($item, $func)
                    : [$item],
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            ],
            [
                $iter([[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10]),
                fn ($item, $func) => is_iterable($item)
                    ? Single::flatMap($item, $func)
                    : $item,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn ($item) => $item + 1,
                [2, 3, 4, 5, 6]
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn ($item) => ($item % 2 === 0) ? [$item, $item] : $item,
                [1, 2, 2, 3, 4, 4, 5]
            ],
            [
                $iter([1, 2, [3], [4, 5], 6, []]),
                fn ($x) => $x,
                [1, 2, 3, 4, 5, 6],
            ],
            [
                $iter([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item, $item, [$item]],
                [0, 0, [0], 1, 1, [1], 2, 2, [2], 3, 3, [3], 4, 4, [4], 5, 5, [5]],
            ],
            [
                $iter(["it's Sunny in", "", "California"]),
                fn ($words) => \explode(' ', $words),
                ["it's","Sunny","in", "", "California"],
            ],
            [
                $iter([5, 4, -3, 20, 17, -33, -4, 18]),
                fn ($x) => $x < 0 ? [] : ($x % 2 === 0 ? [$x] : [$x - 1, 1]),
                [4, 1, 4, 20, 16, 1, 18],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $iterable
     * @param callable $func
     * @param array $expected
     */
    public function testTraversables(\Traversable $iterable, callable $func, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::flatMap($iterable, $func) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                fn ($item) => [$item],
                [],
            ],
            [
                $trav([0]),
                fn ($item) => [$item],
                [0],
            ],
            [
                $trav([1]),
                fn ($item) => [$item],
                [1],
            ],
            [
                $trav([2]),
                fn ($item) => [$item],
                [2],
            ],
            [
                $trav([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item],
                [0, 1, 2, 3, 4, 5],
            ],
            [
                $trav([2]),
                fn ($item) => [$item, $item],
                [2, 2],
            ],
            [
                $trav([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item, $item],
                [0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
            ],
            [
                $trav([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item, -$item],
                [0, 0, 1, -1, 2, -2, 3, -3, 4, -4, 5, -5],
            ],
            [
                $trav([]),
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                $trav([0]),
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                $trav([1]),
                fn ($item) => Single::repeat($item, $item),
                [1],
            ],
            [
                $trav([2]),
                fn ($item) => Single::repeat($item, $item),
                [2, 2],
            ],
            [
                $trav([0, 1, 2, 3, 4, 5]),
                fn ($item) => Single::repeat($item, $item),
                [1, 2, 2, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 5],
            ],
            [
                $trav([
                    ['name' => 'bird', 'eggs' => 2],
                    ['name' => 'lizard', 'eggs' => 3],
                    ['name' => 'echidna', 'eggs' => 1],
                    ['name' => 'tyrannosaur', 'eggs' => 0],
                ]),
                fn ($animal) => Single::repeat($animal['name'], $animal['eggs']),
                ['bird', 'bird', 'lizard', 'lizard', 'lizard', 'echidna'],
            ],
            [
                $trav([[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10]),
                fn ($item, $func) => is_iterable($item)
                    ? Single::flatMap($item, $func)
                    : [$item],
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            ],
            [
                $trav([[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10]),
                fn ($item, $func) => is_iterable($item)
                    ? Single::flatMap($item, $func)
                    : $item,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn ($item) => $item + 1,
                [2, 3, 4, 5, 6]
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn ($item) => ($item % 2 === 0) ? [$item, $item] : $item,
                [1, 2, 2, 3, 4, 4, 5]
            ],
            [
                $trav([1, 2, [3], [4, 5], 6, []]),
                fn ($x) => $x,
                [1, 2, 3, 4, 5, 6],
            ],
            [
                $trav([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item, $item, [$item]],
                [0, 0, [0], 1, 1, [1], 2, 2, [2], 3, 3, [3], 4, 4, [4], 5, 5, [5]],
            ],
            [
                $trav(["it's Sunny in", "", "California"]),
                fn ($words) => \explode(' ', $words),
                ["it's","Sunny","in", "", "California"],
            ],
            [
                $trav([5, 4, -3, 20, 17, -33, -4, 18]),
                fn ($x) => $x < 0 ? [] : ($x % 2 === 0 ? [$x] : [$x - 1, 1]),
                [4, 1, 4, 20, 16, 1, 18],
            ],
        ];
    }

    public function testForAllTheTypes(): void
    {
        $gen = fn ($data) => GeneratorFixture::getGenerator($data);
        $iter = fn ($data) => new ArrayIteratorFixture($data);
        $trav = fn ($data) => new IteratorAggregateFixture($data);
        $stdObj = new \stdClass();
        $anonObj = new class () {
        };
        $resource = \fopen('php://input', 'r');
        $closure = fn ($x) => $x + 1;

        // Given
        $input = [
            1, 1.1, '1', 'abc', true, false, null, \INF, -\INF, $stdObj, $anonObj, $resource, $closure,
            [1, 1.1, '1', 'abc', true, false, null, \INF, -\INF, $stdObj, $anonObj, $resource, $closure],
            $gen([1, 1.1, '1', 'abc', true, false, null, \INF, -\INF, $stdObj, $anonObj, $resource, $closure]),
            $iter([1, 1.1, '1', 'abc', true, false, null, \INF, -\INF, $stdObj, $anonObj, $resource, $closure]),
            $trav([1, 1.1, '1', 'abc', true, false, null, \INF, -\INF, $stdObj, $anonObj, $resource, $closure]),
            [[1, 1.1, '1', 'abc', true, false, null, \INF, -\INF, $stdObj, $anonObj, $resource, $closure]],
            [[1], $gen([1]), $iter([1]), $trav([1])],
        ];
        $result = [];

        // When
        foreach (Single::flatMap($input, fn ($item) => $item) as $datum) {
            $result[] = $datum;
        }

        // Then
        $expected = [
            1, 1.1, '1', 'abc', true, false, null, \INF, -\INF, $stdObj, $anonObj, $resource, $closure,
            1, 1.1, '1', 'abc', true, false, null, \INF, -\INF, $stdObj, $anonObj, $resource, $closure,
            1, 1.1, '1', 'abc', true, false, null, \INF, -\INF, $stdObj, $anonObj, $resource, $closure,
            1, 1.1, '1', 'abc', true, false, null, \INF, -\INF, $stdObj, $anonObj, $resource, $closure,
            1, 1.1, '1', 'abc', true, false, null, \INF, -\INF, $stdObj, $anonObj, $resource, $closure,
            [1, 1.1, '1', 'abc', true, false, null, \INF, -\INF, $stdObj, $anonObj, $resource, $closure],
            [1], $gen([1]), $iter([1]), $trav([1]),
        ];
        $this->assertEquals($expected, $result);
    }
}

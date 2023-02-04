<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class FilterKeysTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArray
     * @param array $iterable
     * @param callable $filter
     * @param array $expected
     * @return void
     */
    public function testArray(array $iterable, callable $filter, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::filterKeys($iterable, $filter) as $key => $value) {
            $result[$key] = $value;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [],
                fn (int $key): bool => $key > 2,
                [],
            ],
            [
                [1, 2, 3],
                fn (int $key): bool => $key > 2,
                [],
            ],
            [
                [1, 2, 3, 4, 5, 6],
                fn (int $key): bool => $key > 2,
                [3 => 4, 4 => 5, 5 => 6],
            ],
            [
                [1, 2, 3, 4, 5, 6],
                fn (int $key): bool => $key > 2 && $key < 5,
                [3 => 4, 4 => 5],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
                fn (string $key): bool => in_array($key, ['a', 'c', 'e']),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                [1, 2, 3, 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
                fn ($key): bool => in_array((string)$key, ['a', 'c', 'e']),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                [1, 2, 3, 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
                fn ($key): bool => in_array((string)$key, [1, 'a', 'c', 'e']),
                [1 => 2, 'a' => 1, 'c' => 3, 'e' => 5],
            ],
        ];
    }
    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $iterable
     * @param callable $filter
     * @param array $expected
     * @return void
     */
    public function testGenerators(\Generator $iterable, callable $filter, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::filterKeys($iterable, $filter) as $key => $value) {
            $result[$key] = $value;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [
                $gen([]),
                fn (int $key): bool => $key > 2,
                [],
            ],
            [
                $gen([1, 2, 3]),
                fn (int $key): bool => $key > 2,
                [],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6]),
                fn (int $key): bool => $key > 2,
                [3 => 4, 4 => 5, 5 => 6],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6]),
                fn (int $key): bool => $key > 2 && $key < 5,
                [3 => 4, 4 => 5],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn (string $key): bool => in_array($key, ['a', 'c', 'e']),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                $gen([1, 2, 3, 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn ($key): bool => in_array((string)$key, ['a', 'c', 'e']),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                $gen([1, 2, 3, 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn ($key): bool => in_array((string)$key, [1, 'a', 'c', 'e']),
                [1 => 2, 'a' => 1, 'c' => 3, 'e' => 5],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $iterable
     * @param callable $filter
     * @param array $expected
     * @return void
     */
    public function testIterators(\Iterator $iterable, callable $filter, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::filterKeys($iterable, $filter) as $key => $value) {
            $result[$key] = $value;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = fn (array $data) => new \ArrayIterator($data);

        return [
            [
                $iter([]),
                fn (int $key): bool => $key > 2,
                [],
            ],
            [
                $iter([1, 2, 3]),
                fn (int $key): bool => $key > 2,
                [],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6]),
                fn (int $key): bool => $key > 2,
                [3 => 4, 4 => 5, 5 => 6],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6]),
                fn (int $key): bool => $key > 2 && $key < 5,
                [3 => 4, 4 => 5],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn (string $key): bool => in_array($key, ['a', 'c', 'e']),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                $iter([1, 2, 3, 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn ($key): bool => in_array((string)$key, ['a', 'c', 'e']),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                $iter([1, 2, 3, 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn ($key): bool => in_array((string)$key, [1, 'a', 'c', 'e']),
                [1 => 2, 'a' => 1, 'c' => 3, 'e' => 5],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $iterable
     * @param callable $filter
     * @param array $expected
     * @return void
     */
    public function testTraversables(\Traversable $iterable, callable $filter, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::filterKeys($iterable, $filter) as $key => $value) {
            $result[$key] = $value;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                fn (int $key): bool => $key > 2,
                [],
            ],
            [
                $trav([1, 2, 3]),
                fn (int $key): bool => $key > 2,
                [],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6]),
                fn (int $key): bool => $key > 2,
                [3 => 4, 4 => 5, 5 => 6],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6]),
                fn (int $key): bool => $key > 2 && $key < 5,
                [3 => 4, 4 => 5],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn (string $key): bool => in_array($key, ['a', 'c', 'e']),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                $trav([1, 2, 3, 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn ($key): bool => in_array((string)$key, ['a', 'c', 'e']),
                ['a' => 1, 'c' => 3, 'e' => 5],
            ],
            [
                $trav([1, 2, 3, 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn ($key): bool => in_array((string)$key, [1, 'a', 'c', 'e']),
                [1 => 2, 'a' => 1, 'c' => 3, 'e' => 5],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForComposite
     * @param iterable $iterable
     * @param callable $filter
     * @param array $expected
     * @return void
     */
    public function testComposite(iterable $iterable, callable $filter, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::filterKeys($iterable, $filter) as $key => $value) {
            $resultKeys[] = $key;
            $resultValues[] = $value;
        }

        // Then
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }

    public function dataProviderForComposite(): array
    {
        $composite = fn (array $keys, array $values) => GeneratorFixture::getCombined($keys, $values);

        return [
            [
                $composite(
                    [[1], [2, 3], [4, 5, 6], [7, 8, 9, 10], [11, 12, 13, 14, 15]],
                    [[11], [22], [33], [44], [55]],
                ),
                fn (array $key): bool => count($key) < 4,
                [[1], [2, 3], [4, 5, 6]],
                [[11], [22], [33]],
            ],
            [
                $composite(
                    [[1], [2, 3], [4, 5, 6], [7, 8, 9, 10], [11, 12, 13, 14, 15]],
                    [[11], [22], [33], [44], [55]],
                ),
                fn (array $key): bool => ($key[0] ?? 0) > 7,
                [[11, 12, 13, 14, 15]],
                [[55]],
            ],
            [
                $composite(
                    [[1], [2, 3], [4, 5, 6], [7, 8, 9, 10], [11, 12, 13, 14, 15]],
                    [[11], [22], [33], [44], [55]],
                ),
                fn (array $key): bool => array_sum($key) === 15,
                [[4, 5, 6]],
                [[33]],
            ],
        ];
    }
}

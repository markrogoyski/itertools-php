<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ReindexTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArray
     * @param array $iterable
     * @param callable $indexer
     * @param array $expected
     * @return void
     */
    public function testArray(array $iterable, callable $indexer, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::reindex($iterable, $indexer) as $key => $value) {
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
                fn (int $value): int => $value + 1,
                [],
            ],
            [
                [1],
                fn (int $value): int => $value + 1,
                [2 => 1],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (int $value): int => $value + 1,
                [2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (int $value): string => "_{$value}_",
                ["_1_" => 1, "_2_" => 2, "_3_" => 3, "_4_" => 4, "_5_" => 5],
            ],
            [
                [1, 2, 3, 4, 5],
                fn (int $value, int $key): string => "{$key}_{$value}",
                ["0_1" => 1, "1_2" => 2, "2_3" => 3, "3_4" => 4, "4_5" => 5],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5],
                fn (int $value, string $key): string => "{$key}_{$value}",
                ["a_1" => 1, "b_2" => 2, "c_3" => 3, "d_4" => 4, "e_5" => 5],
            ],
            [
                ['a' => [1], 'b' => [2], 'c' => [3], 'd' => [4], 'e' => [5]],
                fn (array $value, string $key): string => "{$key}_{$value[0]}",
                ["a_1" => [1], "b_2" => [2], "c_3" => [3], "d_4" => [4], "e_5" => [5]],
            ],
            [
                [1, 2.0, [3], 'a' => [4], 'b' => [5], 'c' => [6], 'd' => []],
                fn ($value, $key): string => is_array($value) ? "{$key}_".($value[0] ?? 'null') : "{$key}_{$value}",
                ["0_1" => 1, "1_2" => 2.0, "2_3" => [3], "a_4" => [4], "b_5" => [5], "c_6" => [6], "d_null" => []],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $iterable
     * @param callable $indexer
     * @param array $expected
     * @return void
     */
    public function testGenerators(\Generator $iterable, callable $indexer, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::reindex($iterable, $indexer) as $key => $value) {
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
                fn (int $value): int => $value + 1,
                [],
            ],
            [
                $gen([1]),
                fn (int $value): int => $value + 1,
                [2 => 1],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (int $value): int => $value + 1,
                [2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (int $value): string => "_{$value}_",
                ["_1_" => 1, "_2_" => 2, "_3_" => 3, "_4_" => 4, "_5_" => 5],
            ],
            [
                $gen([1, 2, 3, 4, 5]),
                fn (int $value, int $key): string => "{$key}_{$value}",
                ["0_1" => 1, "1_2" => 2, "2_3" => 3, "3_4" => 4, "4_5" => 5],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn (int $value, string $key): string => "{$key}_{$value}",
                ["a_1" => 1, "b_2" => 2, "c_3" => 3, "d_4" => 4, "e_5" => 5],
            ],
            [
                $gen(['a' => [1], 'b' => [2], 'c' => [3], 'd' => [4], 'e' => [5]]),
                fn (array $value, string $key): string => "{$key}_{$value[0]}",
                ["a_1" => [1], "b_2" => [2], "c_3" => [3], "d_4" => [4], "e_5" => [5]],
            ],
            [
                $gen([1, 2.0, [3], 'a' => [4], 'b' => [5], 'c' => [6], 'd' => []]),
                fn ($value, $key): string => is_array($value) ? "{$key}_".($value[0] ?? 'null') : "{$key}_{$value}",
                ["0_1" => 1, "1_2" => 2.0, "2_3" => [3], "a_4" => [4], "b_5" => [5], "c_6" => [6], "d_null" => []],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $iterable
     * @param callable $indexer
     * @param array $expected
     * @return void
     */
    public function testIterators(\Iterator $iterable, callable $indexer, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::reindex($iterable, $indexer) as $key => $value) {
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
                fn (int $value): int => $value + 1,
                [],
            ],
            [
                $iter([1]),
                fn (int $value): int => $value + 1,
                [2 => 1],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (int $value): int => $value + 1,
                [2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (int $value): string => "_{$value}_",
                ["_1_" => 1, "_2_" => 2, "_3_" => 3, "_4_" => 4, "_5_" => 5],
            ],
            [
                $iter([1, 2, 3, 4, 5]),
                fn (int $value, int $key): string => "{$key}_{$value}",
                ["0_1" => 1, "1_2" => 2, "2_3" => 3, "3_4" => 4, "4_5" => 5],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn (int $value, string $key): string => "{$key}_{$value}",
                ["a_1" => 1, "b_2" => 2, "c_3" => 3, "d_4" => 4, "e_5" => 5],
            ],
            [
                $iter(['a' => [1], 'b' => [2], 'c' => [3], 'd' => [4], 'e' => [5]]),
                fn (array $value, string $key): string => "{$key}_{$value[0]}",
                ["a_1" => [1], "b_2" => [2], "c_3" => [3], "d_4" => [4], "e_5" => [5]],
            ],
            [
                $iter([1, 2.0, [3], 'a' => [4], 'b' => [5], 'c' => [6], 'd' => []]),
                fn ($value, $key): string => is_array($value) ? "{$key}_".($value[0] ?? 'null') : "{$key}_{$value}",
                ["0_1" => 1, "1_2" => 2.0, "2_3" => [3], "a_4" => [4], "b_5" => [5], "c_6" => [6], "d_null" => []],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $iterable
     * @param callable $indexer
     * @param array $expected
     * @return void
     */
    public function testTraversables(\Traversable $iterable, callable $indexer, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::reindex($iterable, $indexer) as $key => $value) {
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
                fn (int $value): int => $value + 1,
                [],
            ],
            [
                $trav([1]),
                fn (int $value): int => $value + 1,
                [2 => 1],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (int $value): int => $value + 1,
                [2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (int $value): string => "_{$value}_",
                ["_1_" => 1, "_2_" => 2, "_3_" => 3, "_4_" => 4, "_5_" => 5],
            ],
            [
                $trav([1, 2, 3, 4, 5]),
                fn (int $value, int $key): string => "{$key}_{$value}",
                ["0_1" => 1, "1_2" => 2, "2_3" => 3, "3_4" => 4, "4_5" => 5],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]),
                fn (int $value, string $key): string => "{$key}_{$value}",
                ["a_1" => 1, "b_2" => 2, "c_3" => 3, "d_4" => 4, "e_5" => 5],
            ],
            [
                $trav(['a' => [1], 'b' => [2], 'c' => [3], 'd' => [4], 'e' => [5]]),
                fn (array $value, string $key): string => "{$key}_{$value[0]}",
                ["a_1" => [1], "b_2" => [2], "c_3" => [3], "d_4" => [4], "e_5" => [5]],
            ],
            [
                $trav([1, 2.0, [3], 'a' => [4], 'b' => [5], 'c' => [6], 'd' => []]),
                fn ($value, $key): string => is_array($value) ? "{$key}_".($value[0] ?? 'null') : "{$key}_{$value}",
                ["0_1" => 1, "1_2" => 2.0, "2_3" => [3], "a_4" => [4], "b_5" => [5], "c_6" => [6], "d_null" => []],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForComposite
     * @param iterable $iterable
     * @param callable $indexer
     * @param array $expected
     * @return void
     */
    public function testComposite(iterable $iterable, callable $indexer, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::reindex($iterable, $indexer) as $key => $value) {
            $result[$key] = $value;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForComposite(): array
    {
        $composite = fn (array $keys, array $values) => GeneratorFixture::getCombined($keys, $values);

        return [
            [
                $composite(
                    [[1], [2], [3], [4], [5]],
                    [[11], [22], [33], [44], [55]],
                ),
                fn (array $value, array $key): string => "{$key[0]}_{$value[0]}",
                ["1_11" => [11], "2_22" => [22], "3_33" => [33], "4_44" => [44], "5_55" => [55]],
            ],
            [
                $composite(
                    [1, [2], (object)['a' => 3], "4", 5.0],
                    [[11], [22], [33], [44], [55]],
                ),
                static function (array $value, $key): string {
                    switch (true) {
                        case is_array($key):
                            return "[{$key[0]}]({$value[0]})";
                        case is_object($key):
                            return "[{$key->a}]({$value[0]})";
                        default:
                            return "[{$key}]({$value[0]})";
                    }
                },
                ["[1](11)" => [11], "[2](22)" => [22], "[3](33)" => [33], "[4](44)" => [44], "[5](55)" => [55]],
            ],
            [
                $composite(
                    [[11], [22], [33], [44], [55]],
                    [1, [2], (object)['a' => 3], "4", 5.0],
                ),
                static function ($value, array $key): string {
                    switch (true) {
                        case is_array($value):
                            return "[{$key[0]}]({$value[0]})";
                        case is_object($value):
                            return "[{$key[0]}]({$value->a})";
                        default:
                            return "[{$key[0]}]({$value})";
                    }
                },
                ["[11](1)" => 1, "[22](2)" => [2], "[33](3)" => (object)['a' => 3], "[44](4)" => "4", "[55](5)" => 5.0],
            ],
            [
                $composite(
                    [1, [2], (object)['a' => 3], "4", 5.0],
                    [11, [22], (object)['a' => 33], "44", 55.0],
                ),
                static function ($value, $key): string {
                    switch (true) {
                        case is_array($key):
                            $key = "{$key[0]}";
                            break;
                        case is_object($key):
                            $key = "{$key->a}";
                            break;
                        default:
                            $key = "{$key}";
                    }
                    switch (true) {
                        case is_array($value):
                            $value = "{$value[0]}";
                            break;
                        case is_object($value):
                            $value = "{$value->a}";
                            break;
                        default:
                            $value = "{$value}";
                    }

                    return "[{$key}]({$value})";
                },
                ["[1](11)" => 11, "[2](22)" => [22], "[3](33)" => (object)['a' => 33], "[4](44)" => "44", "[5](55)" => 55.0],
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Transform;

use IterTools\Tests\Fixture;
use IterTools\Transform;

class ToAssociativeArrayTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $messages = ['message 1', 'message 2', 'message 3'];

        // And
        $keyFunc   = fn ($msg) => \md5($msg);
        $valueFunc = fn ($msg) => \strtoupper($msg);

        // When
        $associativeArray = Transform::toAssociativeArray($messages, $keyFunc, $valueFunc);

        // Then
        $expected = [
            '1db65a6a0a818fd39655b95e33ada11d' => 'MESSAGE 1',
            '83b2330607fe8f817ce6d24249dea373' => 'MESSAGE 2',
            '037805d3ad7b10c5b8425427b516b5ce' => 'MESSAGE 3',
        ];
        $this->assertEquals($expected, $associativeArray);
    }

    /**
     * @dataProvider dataProviderForArray
     * @param array $input
     * @param callable|null $keyFunc
     * @param callable|null $valueFunc
     * @param array $expected
     * @return void
     */
    public function testArray(array $input, ?callable $keyFunc, ?callable $valueFunc, array $expected): void
    {
        // When
        $result = Transform::toAssociativeArray($input, $keyFunc, $valueFunc);

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [
                [],
                null,
                null,
                [],
            ],
            [
                [],
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                [],
            ],
            [
                [1],
                null,
                null,
                [1],
            ],
            [
                [1],
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0'],
            ],
            [
                [1, 1],
                null,
                null,
                [1, 1],
            ],
            [
                [1, 1],
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_1' => '1_1'],
            ],
            [
                [1, 1],
                fn ($value, $key) => "{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['1' => '1_1'],
            ],
            [
                [1, 2],
                null,
                null,
                [1, 2],
            ],
            [
                [1, 2],
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_2' => '2_1'],
            ],
            [
                [1, 1, 2],
                null,
                null,
                [1, 1, 2],
            ],
            [
                [1, 1, 2],
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_1' => '1_1', '2_2' => '2_2'],
            ],
            [
                [1, 1, 2],
                fn ($value, $key) => "{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['1' => '1_1', '2' => '2_2'],
            ],
            [
                [1, 2, 3],
                null,
                null,
                [1, 2, 3],
            ],
            [
                [1, 2, 3],
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_2' => '2_1', '2_3' => '3_2'],
            ],
            [
                [1 => 1, 2 => 2, 3 => 3],
                null,
                null,
                [1 => 1, 2 => 2, 3 => 3],
            ],
            [
                [1 => 1, 2 => 2, 3 => 3],
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['1_1' => '1_1', '2_2' => '2_2', '3_3' => '3_3'],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                null,
                null,
                ['a' => 1, 'b' => 2, 'c' => 3],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['a_1' => '1_a', 'b_2' => '2_b', 'c_3' => '3_c'],
            ],
            [
                [1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 'a' => 1, 'b' => 2, 'c' => 3],
                null,
                null,
                [1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 'a' => 1, 'b' => 2, 'c' => 3],
            ],
            [
                [1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', 'a' => 1, 'b' => 2, 'c' => 3],
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                [
                    '0_1' => '1_0',
                    '1_2' => '2_1',
                    '2_3' => '3_2',
                    '3_1.1' => '1.1_3',
                    '4_2.2' => '2.2_4',
                    '5_3.3' => '3.3_5',
                    '6_1' => '1_6',
                    '7_2' => '2_7',
                    '8_3' => '3_8',
                    '9_a' => 'a_9',
                    '10_b' => 'b_10',
                    '11_c' => 'c_11',
                    'a_1' => '1_a',
                    'b_2' => '2_b',
                    'c_3' => '3_c',
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $input
     * @param callable|null $keyFunc
     * @param callable|null $valueFunc
     * @param array $expected
     * @return void
     */
    public function testGenerators(\Generator $input, ?callable $keyFunc, ?callable $valueFunc, array $expected): void
    {
        // When
        $result = Transform::toAssociativeArray($input, $keyFunc, $valueFunc);

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForGenerators(): array
    {
        $gen = fn (array $input) => Fixture\GeneratorFixture::getKeyValueGenerator($input);

        return [
            [
                $gen([]),
                null,
                null,
                [],
            ],
            [
                $gen([]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                [],
            ],
            [
                $gen([1]),
                null,
                null,
                [1],
            ],
            [
                $gen([1]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0'],
            ],
            [
                $gen([1, 1]),
                null,
                null,
                [1, 1],
            ],
            [
                $gen([1, 1]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_1' => '1_1'],
            ],
            [
                $gen([1, 1]),
                fn ($value, $key) => "{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['1' => '1_1'],
            ],
            [
                $gen([1, 2]),
                null,
                null,
                [1, 2],
            ],
            [
                $gen([1, 2]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_2' => '2_1'],
            ],
            [
                $gen([1, 1, 2]),
                null,
                null,
                [1, 1, 2],
            ],
            [
                $gen([1, 1, 2]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_1' => '1_1', '2_2' => '2_2'],
            ],
            [
                $gen([1, 1, 2]),
                fn ($value, $key) => "{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['1' => '1_1', '2' => '2_2'],
            ],
            [
                $gen([1, 2, 3]),
                null,
                null,
                [1, 2, 3],
            ],
            [
                $gen([1, 2, 3]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_2' => '2_1', '2_3' => '3_2'],
            ],
            [
                $gen([1 => 1, 2 => 2, 3 => 3]),
                null,
                null,
                [1 => 1, 2 => 2, 3 => 3],
            ],
            [
                $gen([1 => 1, 2 => 2, 3 => 3]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['1_1' => '1_1', '2_2' => '2_2', '3_3' => '3_3'],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                null,
                null,
                ['a' => 1, 'b' => 2, 'c' => 3],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['a_1' => '1_a', 'b_2' => '2_b', 'c_3' => '3_c'],
            ],
            [
                $gen([1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 'a' => 1, 'b' => 2, 'c' => 3]),
                null,
                null,
                [1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 'a' => 1, 'b' => 2, 'c' => 3],
            ],
            [
                $gen([1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', 'a' => 1, 'b' => 2, 'c' => 3]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                [
                    '0_1' => '1_0',
                    '1_2' => '2_1',
                    '2_3' => '3_2',
                    '3_1.1' => '1.1_3',
                    '4_2.2' => '2.2_4',
                    '5_3.3' => '3.3_5',
                    '6_1' => '1_6',
                    '7_2' => '2_7',
                    '8_3' => '3_8',
                    '9_a' => 'a_9',
                    '10_b' => 'b_10',
                    '11_c' => 'c_11',
                    'a_1' => '1_a',
                    'b_2' => '2_b',
                    'c_3' => '3_c',
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $input
     * @param callable|null $keyFunc
     * @param callable|null $valueFunc
     * @param array $expected
     * @return void
     */
    public function testIterators(\Iterator $input, ?callable $keyFunc, ?callable $valueFunc, array $expected): void
    {
        // When
        $result = Transform::toAssociativeArray($input, $keyFunc, $valueFunc);

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForIterators(): array
    {
        $iter = fn (array $input) => new \ArrayIterator($input);

        return [
            [
                $iter([]),
                null,
                null,
                [],
            ],
            [
                $iter([]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                [],
            ],
            [
                $iter([1]),
                null,
                null,
                [1],
            ],
            [
                $iter([1]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0'],
            ],
            [
                $iter([1, 1]),
                null,
                null,
                [1, 1],
            ],
            [
                $iter([1, 1]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_1' => '1_1'],
            ],
            [
                $iter([1, 1]),
                fn ($value, $key) => "{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['1' => '1_1'],
            ],
            [
                $iter([1, 2]),
                null,
                null,
                [1, 2],
            ],
            [
                $iter([1, 2]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_2' => '2_1'],
            ],
            [
                $iter([1, 1, 2]),
                null,
                null,
                [1, 1, 2],
            ],
            [
                $iter([1, 1, 2]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_1' => '1_1', '2_2' => '2_2'],
            ],
            [
                $iter([1, 1, 2]),
                fn ($value, $key) => "{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['1' => '1_1', '2' => '2_2'],
            ],
            [
                $iter([1, 2, 3]),
                null,
                null,
                [1, 2, 3],
            ],
            [
                $iter([1, 2, 3]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_2' => '2_1', '2_3' => '3_2'],
            ],
            [
                $iter([1 => 1, 2 => 2, 3 => 3]),
                null,
                null,
                [1 => 1, 2 => 2, 3 => 3],
            ],
            [
                $iter([1 => 1, 2 => 2, 3 => 3]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['1_1' => '1_1', '2_2' => '2_2', '3_3' => '3_3'],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                null,
                null,
                ['a' => 1, 'b' => 2, 'c' => 3],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['a_1' => '1_a', 'b_2' => '2_b', 'c_3' => '3_c'],
            ],
            [
                $iter([1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 'a' => 1, 'b' => 2, 'c' => 3]),
                null,
                null,
                [1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 'a' => 1, 'b' => 2, 'c' => 3],
            ],
            [
                $iter([1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', 'a' => 1, 'b' => 2, 'c' => 3]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                [
                    '0_1' => '1_0',
                    '1_2' => '2_1',
                    '2_3' => '3_2',
                    '3_1.1' => '1.1_3',
                    '4_2.2' => '2.2_4',
                    '5_3.3' => '3.3_5',
                    '6_1' => '1_6',
                    '7_2' => '2_7',
                    '8_3' => '3_8',
                    '9_a' => 'a_9',
                    '10_b' => 'b_10',
                    '11_c' => 'c_11',
                    'a_1' => '1_a',
                    'b_2' => '2_b',
                    'c_3' => '3_c',
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $input
     * @param callable|null $keyFunc
     * @param callable|null $valueFunc
     * @param array $expected
     * @return void
     */
    public function testTraversables(\Traversable $input, ?callable $keyFunc, ?callable $valueFunc, array $expected): void
    {
        // When
        $result = Transform::toAssociativeArray($input, $keyFunc, $valueFunc);

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForTraversables(): array
    {
        $trav = fn (array $input) => new Fixture\IteratorAggregateFixture($input);

        return [
            [
                $trav([]),
                null,
                null,
                [],
            ],
            [
                $trav([]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                [],
            ],
            [
                $trav([1]),
                null,
                null,
                [1],
            ],
            [
                $trav([1]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0'],
            ],
            [
                $trav([1, 1]),
                null,
                null,
                [1, 1],
            ],
            [
                $trav([1, 1]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_1' => '1_1'],
            ],
            [
                $trav([1, 1]),
                fn ($value, $key) => "{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['1' => '1_1'],
            ],
            [
                $trav([1, 2]),
                null,
                null,
                [1, 2],
            ],
            [
                $trav([1, 2]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_2' => '2_1'],
            ],
            [
                $trav([1, 1, 2]),
                null,
                null,
                [1, 1, 2],
            ],
            [
                $trav([1, 1, 2]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_1' => '1_1', '2_2' => '2_2'],
            ],
            [
                $trav([1, 1, 2]),
                fn ($value, $key) => "{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['1' => '1_1', '2' => '2_2'],
            ],
            [
                $trav([1, 2, 3]),
                null,
                null,
                [1, 2, 3],
            ],
            [
                $trav([1, 2, 3]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['0_1' => '1_0', '1_2' => '2_1', '2_3' => '3_2'],
            ],
            [
                $trav([1 => 1, 2 => 2, 3 => 3]),
                null,
                null,
                [1 => 1, 2 => 2, 3 => 3],
            ],
            [
                $trav([1 => 1, 2 => 2, 3 => 3]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['1_1' => '1_1', '2_2' => '2_2', '3_3' => '3_3'],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                null,
                null,
                ['a' => 1, 'b' => 2, 'c' => 3],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                ['a_1' => '1_a', 'b_2' => '2_b', 'c_3' => '3_c'],
            ],
            [
                $trav([1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 'a' => 1, 'b' => 2, 'c' => 3]),
                null,
                null,
                [1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', true, false, null, [1, 2, 3], (object)[1, 2, 3], 'a' => 1, 'b' => 2, 'c' => 3],
            ],
            [
                $trav([1, 2, 3, 1.1, 2.2, 3.3, '1', '2', '3', 'a', 'b', 'c', 'a' => 1, 'b' => 2, 'c' => 3]),
                fn ($value, $key) => "{$key}_{$value}",
                fn ($value, $key) => "{$value}_{$key}",
                [
                    '0_1' => '1_0',
                    '1_2' => '2_1',
                    '2_3' => '3_2',
                    '3_1.1' => '1.1_3',
                    '4_2.2' => '2.2_4',
                    '5_3.3' => '3.3_5',
                    '6_1' => '1_6',
                    '7_2' => '2_7',
                    '8_3' => '3_8',
                    '9_a' => 'a_9',
                    '10_b' => 'b_10',
                    '11_c' => 'c_11',
                    'a_1' => '1_a',
                    'b_2' => '2_b',
                    'c_3' => '3_c',
                ],
            ],
        ];
    }

    /**
     * @test         toAssociativeArray throws InvalidArgumentException when keyFunc returns a key that is not int|string
     * @dataProvider dataProviderForInvalidKeys
     * @param        mixed $key
     */
    public function testInvalidComputedKeyThrowsArray(mixed $key): void
    {
        // Given
        $data = [1, 2, 3];

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Transform::toAssociativeArray($data, fn ($value) => $value === 2 ? $key : $value);
    }

    /**
     * @test         toAssociativeArray throws InvalidArgumentException when keyFunc returns a key that is not int|string
     * @dataProvider dataProviderForInvalidKeys
     * @param        mixed $key
     */
    public function testInvalidComputedKeyThrowsGenerator(mixed $key): void
    {
        // Given
        $data = Fixture\GeneratorFixture::getGenerator([1, 2, 3]);

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Transform::toAssociativeArray($data, fn ($value) => $value === 2 ? $key : $value);
    }

    /**
     * @test         toAssociativeArray throws InvalidArgumentException when keyFunc returns a key that is not int|string
     * @dataProvider dataProviderForInvalidKeys
     * @param        mixed $key
     */
    public function testInvalidComputedKeyThrowsIterator(mixed $key): void
    {
        // Given
        $data = new Fixture\ArrayIteratorFixture([1, 2, 3]);

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Transform::toAssociativeArray($data, fn ($value) => $value === 2 ? $key : $value);
    }

    /**
     * @test         toAssociativeArray throws InvalidArgumentException when keyFunc returns a key that is not int|string
     * @dataProvider dataProviderForInvalidKeys
     * @param        mixed $key
     */
    public function testInvalidComputedKeyThrowsIteratorAggregate(mixed $key): void
    {
        // Given
        $data = new Fixture\IteratorAggregateFixture([1, 2, 3]);

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Transform::toAssociativeArray($data, fn ($value) => $value === 2 ? $key : $value);
    }

    /**
     * @test         toAssociativeArray throws InvalidArgumentException when a source key is not int|string and no
     *               keyFunc is given
     * @dataProvider dataProviderForInvalidKeys
     * @param        mixed $key
     */
    public function testInvalidSourceKeyThrows(mixed $key): void
    {
        // Given
        $data = (function () use ($key) {
            yield 'a' => 1;
            yield $key => 2;
        })();

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Transform::toAssociativeArray($data);
    }

    public static function dataProviderForInvalidKeys(): array
    {
        return [
            'fractional float' => [1.5],
            'integral float'   => [2.0],
            'negative zero'    => [-0.0],
            'INF'              => [\INF],
            'NAN'              => [\NAN],
            'null'             => [null],
            'true'             => [true],
            'false'            => [false],
            'array'            => [[1]],
            'object'           => [new \stdClass()],
            'Stringable'       => [new class implements \Stringable {
                public function __toString(): string
                {
                    return 'key';
                }
            }],
        ];
    }

    /**
     * @test toAssociativeArray keeps the last value for a repeated key, at the position of its first occurrence
     */
    public function testRepeatedKeyKeepsLastValue(): void
    {
        // Given
        $data = ['apple', 'avocado', 'banana', 'apricot'];

        // When
        $result = Transform::toAssociativeArray($data, fn (string $fruit) => $fruit[0]);

        // Then
        $this->assertSame(['a' => 'apricot', 'b' => 'banana'], $result);
    }

    /**
     * @test toAssociativeArray stores a decimal integer string key as an int, as PHP arrays do, so '1' and 1 collide
     */
    public function testIntegerStringKeyBecomesInt(): void
    {
        // Given
        $data = [['1', 'string'], [1, 'int'], ['01', 'padded']];

        // When
        $result = Transform::toAssociativeArray($data, fn (array $pair) => $pair[0], fn (array $pair) => $pair[1]);

        // Then
        $this->assertSame([1 => 'int', '01' => 'padded'], $result);
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;

class KeysOnlyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArrayCommon
     * @dataProvider dataProviderForArrayStrict
     * @param iterable $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testArrayStrict(iterable $iterable, array $keys, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::keysOnly($iterable, $keys) as $key => $value) {
            $resultKeys[] = $key;
            $resultValues[] = $value;
        }

        // Then
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }

    /**
     * @dataProvider dataProviderForArrayCommon
     * @dataProvider dataProviderForArrayNonStrict
     * @param iterable $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testArrayNonStrict(iterable $iterable, array $keys, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::keysOnly($iterable, $keys, false) as $key => $value) {
            $resultKeys[] = $key;
            $resultValues[] = $value;
        }

        // Then
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }

    public function dataProviderForArrayCommon(): array
    {
        return [
            [
                [],
                [],
                [],
                [],
            ],
            [
                [1, 2, 3],
                [],
                [],
                [],
            ],
            [
                [],
                [1, 2, 3],
                [],
                [],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                [-2, -4, 1000],
                [],
                [],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                [-2, 1, -4, 3, 1000],
                [1, 3],
                [2, 4],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                [0, 2, 4, 6, 8],
                [0, 2, 4, 6, 8],
                [1, 3, 5, 7, 9],
            ],
            [
                ['a' => 11, 'b' => 22, 'c' => 33],
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                [11, 22, 33],
            ],
            [
                [1, 2, 3, 'a' => 11, 'b' => 22, 'c' => 33],
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                [11, 22, 33],
            ],
            [
                [1, 2, 3, 'a' => 11, 'b' => 22, 'c' => 33],
                ['a', 'b', 0, 2],
                [0, 2, 'a', 'b'],
                [1, 3, 11, 22],
            ],
            [
                [1, 2, 3, 'a' => 11, 'b' => 22, 'c' => 33],
                ['a', 'b', 'd', 0, 2, 4],
                [0, 2, 'a', 'b'],
                [1, 3, 11, 22],
            ],
        ];
    }

    public function dataProviderForArrayStrict(): array
    {
        return [
            [
                [1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33],
                [0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14],
                [0, 2, 4, 'a', 'b'],
                [1, 3, 5, 11, 22],
            ],
        ];
    }

    public function dataProviderForArrayNonStrict(): array
    {
        return [
            [
                [1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33],
                [0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14],
                [0, '1', 2, '3', 4, 'a', 'b'],
                [1, 2, 3, 4, 5, 11, 22],
            ],
        ];
    }
}

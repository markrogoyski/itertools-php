<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class CompressAssociativeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArray
     * @param array $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testArray(array $iterable, array $keys, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::compressAssociative($iterable, $keys) as $key => $value) {
            $resultKeys[] = $key;
            $resultValues[] = $value;
        }

        // Then
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }

    public function dataProviderForArray(): array
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
            [
                [1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33],
                [0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14],
                [0, '1', 2, '3', 4, 'a', 'b'],
                [1, 2, 3, 4, 5, 11, 22],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testGenerators(\Generator $iterable, array $keys, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::compressAssociative($iterable, $keys) as $key => $value) {
            $resultKeys[] = $key;
            $resultValues[] = $value;
        }

        // Then
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [
                $gen([]),
                [],
                [],
                [],
            ],
            [
                $gen([1, 2, 3]),
                [],
                [],
                [],
            ],
            [
                $gen([]),
                [1, 2, 3],
                [],
                [],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                [-2, -4, 1000],
                [],
                [],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                [-2, 1, -4, 3, 1000],
                [1, 3],
                [2, 4],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                [0, 2, 4, 6, 8],
                [0, 2, 4, 6, 8],
                [1, 3, 5, 7, 9],
            ],
            [
                $gen(['a' => 11, 'b' => 22, 'c' => 33]),
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                [11, 22, 33],
            ],
            [
                $gen([1, 2, 3, 'a' => 11, 'b' => 22, 'c' => 33]),
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                [11, 22, 33],
            ],
            [
                $gen([1, 2, 3, 'a' => 11, 'b' => 22, 'c' => 33]),
                ['a', 'b', 0, 2],
                [0, 2, 'a', 'b'],
                [1, 3, 11, 22],
            ],
            [
                $gen([1, 2, 3, 'a' => 11, 'b' => 22, 'c' => 33]),
                ['a', 'b', 'd', 0, 2, 4],
                [0, 2, 'a', 'b'],
                [1, 3, 11, 22],
            ],
            [
                $gen([1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33]),
                [0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14],
                [0, '1', 2, '3', 4, 'a', 'b'],
                [1, 2, 3, 4, 5, 11, 22],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testIterators(\Iterator $iterable, array $keys, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::compressAssociative($iterable, $keys) as $key => $value) {
            $resultKeys[] = $key;
            $resultValues[] = $value;
        }

        // Then
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }
    public function dataProviderForIterators(): array
    {
        $iter = fn (array $data) => new \ArrayIterator($data);

        return [
            [
                $iter([]),
                [],
                [],
                [],
            ],
            [
                $iter([1, 2, 3]),
                [],
                [],
                [],
            ],
            [
                $iter([]),
                [1, 2, 3],
                [],
                [],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                [-2, -4, 1000],
                [],
                [],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                [-2, 1, -4, 3, 1000],
                [1, 3],
                [2, 4],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                [0, 2, 4, 6, 8],
                [0, 2, 4, 6, 8],
                [1, 3, 5, 7, 9],
            ],
            [
                $iter(['a' => 11, 'b' => 22, 'c' => 33]),
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                [11, 22, 33],
            ],
            [
                $iter([1, 2, 3, 'a' => 11, 'b' => 22, 'c' => 33]),
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                [11, 22, 33],
            ],
            [
                $iter([1, 2, 3, 'a' => 11, 'b' => 22, 'c' => 33]),
                ['a', 'b', 0, 2],
                [0, 2, 'a', 'b'],
                [1, 3, 11, 22],
            ],
            [
                $iter([1, 2, 3, 'a' => 11, 'b' => 22, 'c' => 33]),
                ['a', 'b', 'd', 0, 2, 4],
                [0, 2, 'a', 'b'],
                [1, 3, 11, 22],
            ],
            [
                $iter([1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33]),
                [0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14],
                [0, '1', 2, '3', 4, 'a', 'b'],
                [1, 2, 3, 4, 5, 11, 22],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testTraversables(\Traversable $iterable, array $keys, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::compressAssociative($iterable, $keys) as $key => $value) {
            $resultKeys[] = $key;
            $resultValues[] = $value;
        }

        // Then
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                [],
                [],
                [],
            ],
            [
                $trav([1, 2, 3]),
                [],
                [],
                [],
            ],
            [
                $trav([]),
                [1, 2, 3],
                [],
                [],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                [-2, -4, 1000],
                [],
                [],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                [-2, 1, -4, 3, 1000],
                [1, 3],
                [2, 4],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                [0, 2, 4, 6, 8],
                [0, 2, 4, 6, 8],
                [1, 3, 5, 7, 9],
            ],
            [
                $trav(['a' => 11, 'b' => 22, 'c' => 33]),
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                [11, 22, 33],
            ],
            [
                $trav([1, 2, 3, 'a' => 11, 'b' => 22, 'c' => 33]),
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
                [11, 22, 33],
            ],
            [
                $trav([1, 2, 3, 'a' => 11, 'b' => 22, 'c' => 33]),
                ['a', 'b', 0, 2],
                [0, 2, 'a', 'b'],
                [1, 3, 11, 22],
            ],
            [
                $trav([1, 2, 3, 'a' => 11, 'b' => 22, 'c' => 33]),
                ['a', 'b', 'd', 0, 2, 4],
                [0, 2, 'a', 'b'],
                [1, 3, 11, 22],
            ],
            [
                $trav([1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33]),
                [0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14],
                [0, '1', 2, '3', 4, 'a', 'b'],
                [1, 2, 3, 4, 5, 11, 22],
            ],
        ];
    }
}

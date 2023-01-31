<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class CompressAssociativeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArrayCommon
     * @dataProvider dataProviderForArrayStrict
     * @param array $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testArrayStrict(array $iterable, array $keys, array $expectedKeys, array $expectedValues): void
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

    /**
     * @dataProvider dataProviderForArrayCommon
     * @dataProvider dataProviderForArrayNonStrict
     * @param array $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testArrayNonStrict(array $iterable, array $keys, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::compressAssociative($iterable, $keys, false) as $key => $value) {
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

    /**
     * @dataProvider dataProviderForGeneratorsCommon
     * @dataProvider dataProviderForGeneratorsStrict
     * @param \Generator $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testGeneratorsStrict(\Generator $iterable, array $keys, array $expectedKeys, array $expectedValues): void
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

    /**
     * @dataProvider dataProviderForGeneratorsCommon
     * @dataProvider dataProviderForGeneratorsNonStrict
     * @param \Generator $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testGeneratorsNonStrict(\Generator $iterable, array $keys, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::compressAssociative($iterable, $keys, false) as $key => $value) {
            $resultKeys[] = $key;
            $resultValues[] = $value;
        }

        // Then
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }

    public function dataProviderForGeneratorsCommon(): array
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
        ];
    }

    public function dataProviderForGeneratorsStrict(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [
                $gen([1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33]),
                [0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14],
                [0, 2, 4, 'a', 'b'],
                [1, 3, 5, 11, 22],
            ],
        ];
    }

    public function dataProviderForGeneratorsNonStrict(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [
                $gen([1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33]),
                [0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14],
                [0, '1', 2, '3', 4, 'a', 'b'],
                [1, 2, 3, 4, 5, 11, 22],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIteratorsCommon
     * @dataProvider dataProviderForIteratorsStrict
     * @param \Iterator $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testIteratorsStrict(\Iterator $iterable, array $keys, array $expectedKeys, array $expectedValues): void
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

    /**
     * @dataProvider dataProviderForIteratorsCommon
     * @dataProvider dataProviderForIteratorsNonStrict
     * @param \Iterator $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testIteratorsNonStrict(\Iterator $iterable, array $keys, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::compressAssociative($iterable, $keys, false) as $key => $value) {
            $resultKeys[] = $key;
            $resultValues[] = $value;
        }

        // Then
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }

    public function dataProviderForIteratorsCommon(): array
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
        ];
    }

    public function dataProviderForIteratorsStrict(): array
    {
        $iter = fn (array $data) => new \ArrayIterator($data);

        return [
            [
                $iter([1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33]),
                [0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14],
                [0, 2, 4, 'a', 'b'],
                [1, 3, 5, 11, 22],
            ],
        ];
    }

    public function dataProviderForIteratorsNonStrict(): array
    {
        $iter = fn (array $data) => new \ArrayIterator($data);

        return [
            [
                $iter([1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33]),
                [0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14],
                [0, '1', 2, '3', 4, 'a', 'b'],
                [1, 2, 3, 4, 5, 11, 22],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversablesCommon
     * @dataProvider dataProviderForTraversablesStrict
     * @param \Traversable $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testTraversablesStrict(\Traversable $iterable, array $keys, array $expectedKeys, array $expectedValues): void
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

    /**
     * @dataProvider dataProviderForTraversablesCommon
     * @dataProvider dataProviderForTraversablesNonStrict
     * @param \Traversable $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testTraversablesNonStrict(\Traversable $iterable, array $keys, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::compressAssociative($iterable, $keys, false) as $key => $value) {
            $resultKeys[] = $key;
            $resultValues[] = $value;
        }

        // Then
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }

    public function dataProviderForTraversablesCommon(): array
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
        ];
    }

    public function dataProviderForTraversablesStrict(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33]),
                [0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14],
                [0, 2, 4, 'a', 'b'],
                [1, 3, 5, 11, 22],
            ],
        ];
    }

    public function dataProviderForTraversablesNonStrict(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([1, 2, 3, 4, 5, 'a' => 11, 'b' => 22, 'c' => 33]),
                [0, '1', 2, '3', 4, 'a', 10, 'b', 12, 'd', 14],
                [0, '1', 2, '3', 4, 'a', 'b'],
                [1, 2, 3, 4, 5, 11, 22],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForWithCompositeKeysCommon
     * @dataProvider dataProviderForWithCompositeKeysStrict
     * @param iterable $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testWithCompositeKeysStrict(iterable $iterable, array $keys, array $expectedKeys, array $expectedValues): void
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

    /**
     * @dataProvider dataProviderForWithCompositeKeysCommon
     * @dataProvider dataProviderForWithCompositeKeysNonStrict
     * @param iterable $iterable
     * @param array $keys
     * @param array $expectedKeys
     * @param array $expectedValues
     * @return void
     */
    public function testWithCompositeKeysNonStrict(iterable $iterable, array $keys, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::compressAssociative($iterable, $keys, false) as $key => $value) {
            $resultKeys[] = $key;
            $resultValues[] = $value;
        }

        // Then
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }

    public function dataProviderForWithCompositeKeysCommon(): array
    {
        $gen = fn ($keys, $values) => GeneratorFixture::getCombined($keys, $values);

        return [
            [
                $gen(
                    [[1], [2], [3]],
                    [1, 2, 3],
                ),
                [],
                [],
                [],
            ],
            [
                $gen(
                    [[1], [2], [3]],
                    [1, 2, 3],
                ),
                [[1], [3]],
                [[1], [3]],
                [1, 3],
            ],
            [
                $gen(
                    [[1], [2], [3]],
                    [1, 2, 3],
                ),
                [[1], [2], [3]],
                [[1], [2], [3]],
                [1, 2, 3],
            ],
            [
                $gen(
                    [[1], [2], [3]],
                    [1, 2, 3],
                ),
                [1, [1], 2, [2], 3, [3]],
                [[1], [2], [3]],
                [1, 2, 3],
            ],
        ];
    }

    public function dataProviderForWithCompositeKeysStrict(): array
    {
        $gen = fn ($keys, $values) => GeneratorFixture::getCombined($keys, $values);

        return [
            [
                $gen(
                    [$o1 = (object)[1], $o2 = (object)[2], (object)[3]],
                    [1, 2, 3],
                ),
                [$o1, $o2, (object)[3]],
                [$o1, $o2],
                [1, 2],
            ],
        ];
    }

    public function dataProviderForWithCompositeKeysNonStrict(): array
    {
        $gen = fn ($keys, $values) => GeneratorFixture::getCombined($keys, $values);

        return [
            [
                $gen(
                    [$o1 = (object)[1], $o2 = (object)[2], $o3 = (object)[3]],
                    [1, 2, 3],
                ),
                [$o1, $o2, (object)[3]],
                [$o1, $o2, $o3],
                [1, 2, 3],
            ],
        ];
    }
}

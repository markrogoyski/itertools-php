<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class RetroTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArray
     * @param array $data
     * @param array $expectedKeys
     * @param array $expectedValues
     */
    public function testArray(array $data, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::retro($data) as $key => $value) {
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
            ],
            [
                [1],
                [0],
                [1],
            ],
            [
                [1, 2, 3],
                [2, 1, 0],
                [3, 2, 1],
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => 3],
                ['c', 'b', 'a'],
                [3, 2, 1],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $data
     * @param array $expectedKeys
     * @param array $expectedValues
     */
    public function testGenerators(\Generator $data, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::retro($data) as $key => $value) {
            $resultKeys[] = $key;
            $resultValues[] = $value;
        }

        // Then
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = fn ($data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [
                $gen([]),
                [],
                [],
            ],
            [
                $gen([1]),
                [0],
                [1],
            ],
            [
                $gen([1, 2, 3]),
                [2, 1, 0],
                [3, 2, 1],
            ],
            [
                $gen(['a' => 1, 'b' => 2, 'c' => 3]),
                ['c', 'b', 'a'],
                [3, 2, 1],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $data
     * @param array $expectedKeys
     * @param array $expectedValues
     */
    public function testIterators(\Iterator $data, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::retro($data) as $key => $value) {
            $resultKeys[] = $key;
            $resultValues[] = $value;
        }

        // Then
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }

    public function dataProviderForIterators(): array
    {
        $iter = fn ($data) => new \ArrayIterator($data);

        return [
            [
                $iter([]),
                [],
                [],
            ],
            [
                $iter([1]),
                [0],
                [1],
            ],
            [
                $iter([1, 2, 3]),
                [2, 1, 0],
                [3, 2, 1],
            ],
            [
                $iter(['a' => 1, 'b' => 2, 'c' => 3]),
                ['c', 'b', 'a'],
                [3, 2, 1],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $data
     * @param array $expectedKeys
     * @param array $expectedValues
     */
    public function testTraversables(\Traversable $data, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::retro($data) as $key => $value) {
            $resultKeys[] = $key;
            $resultValues[] = $value;
        }

        // Then
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                [],
                [],
            ],
            [
                $trav([1]),
                [0],
                [1],
            ],
            [
                $trav([1, 2, 3]),
                [2, 1, 0],
                [3, 2, 1],
            ],
            [
                $trav(['a' => 1, 'b' => 2, 'c' => 3]),
                ['c', 'b', 'a'],
                [3, 2, 1],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForComposite
     * @param iterable $data
     * @param array $expectedKeys
     * @param array $expectedValues
     */
    public function testComposite(iterable $data, array $expectedKeys, array $expectedValues): void
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::retro($data) as $key => $value) {
            $resultKeys[] = $key;
            $resultValues[] = $value;
        }

        // Then
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }

    public function dataProviderForComposite(): array
    {
        $composite = fn ($keys, $values) => GeneratorFixture::getCombined($keys, $values);

        return [
            [
                $composite(
                    [[1], [2], [3]],
                    [['a'], ['b'], ['c']],
                ),
                [[3], [2], [1]],
                [['c'], ['b'], ['a']],
            ],
            [
                $composite(
                    [[1], [1], [1]],
                    [['a'], ['b'], ['c']],
                ),
                [[1], [1], [1]],
                [['c'], ['b'], ['a']],
            ],
            [
                $composite(
                    [1, 1.1, 'abc', true, false, null, [1, 2, 3], $o1 = (object)[2, 3, 4]],
                    [11, 11.1, 'bcd', false, true, null, [11, 22, 33], $o2 = (object)[22, 33, 44]],
                ),
                [$o1, [1, 2, 3], null, false, true, 'abc', 1.1, 1],
                [$o2, [11, 22, 33], null, true, false, 'bcd', 11.1, 11],
            ],
        ];
    }
}

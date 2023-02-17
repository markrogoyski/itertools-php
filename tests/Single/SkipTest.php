<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class SkipTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArray
     * @param array $data
     * @param array $params
     * @param array $expectedKeys
     * @param array $expectedValues
     */
    public function testArray(array $data, array $params, array $expectedKeys, array $expectedValues)
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::skip($data, ...$params) as $key => $datum) {
            $resultKeys[] = $key;
            $resultValues[] = $datum;
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
                [0],
                [],
                [],
            ],
            [
                [],
                [0, 1],
                [],
                [],
            ],
            [
                [],
                [0, 2],
                [],
                [],
            ],
            [
                [],
                [3, 5],
                [],
                [],
            ],
            [
                [1],
                [0],
                [0],
                [1],
            ],
            [
                [1],
                [0, 0],
                [0],
                [1],
            ],
            [
                [1],
                [0, 1],
                [0],
                [1],
            ],
            [
                [1],
                [0, 2],
                [0],
                [1],
            ],
            [
                [1],
                [1],
                [],
                [],
            ],
            [
                [1],
                [1, 0],
                [],
                [],
            ],
            [
                [1],
                [1, 1],
                [0],
                [1],
            ],
            [
                [1],
                [1, 2],
                [0],
                [1],
            ],
            [
                [1],
                [2, 0],
                [],
                [],
            ],
            [
                [1],
                [2, 1],
                [0],
                [1],
            ],
            [
                [1],
                [2, 2],
                [0],
                [1],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [0],
                [0, 1, 2, 3, 4, 5, 6, 7, 8],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [0, 0],
                [0, 1, 2, 3, 4, 5, 6, 7, 8],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [1, 0],
                [1, 2, 3, 4, 5, 6, 7, 8],
                [2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [2, 0],
                [2, 3, 4, 5, 6, 7, 8],
                [3, 4, 5, 6, 7, 8, 9],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [8, 0],
                [8],
                [9],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [9, 0],
                [],
                [],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [10, 0],
                [],
                [],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [1, 1],
                [0, 2, 3, 4, 5, 6, 7, 8],
                [1, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [1, 2],
                [0, 1, 3, 4, 5, 6, 7, 8],
                [1, 2, 4, 5, 6, 7, 8, 9],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [1, 7],
                [0, 1, 2, 3, 4, 5, 6, 8],
                [1, 2, 3, 4, 5, 6, 7, 9],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [1, 8],
                [0, 1, 2, 3, 4, 5, 6, 7],
                [1, 2, 3, 4, 5, 6, 7, 8],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [1, 9],
                [0, 1, 2, 3, 4, 5, 6, 7, 8],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [2, 1],
                [0, 3, 4, 5, 6, 7, 8],
                [1, 4, 5, 6, 7, 8, 9],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [2, 2],
                [0, 1, 4, 5, 6, 7, 8],
                [1, 2, 5, 6, 7, 8, 9],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [2, 7],
                [0, 1, 2, 3, 4, 5, 6],
                [1, 2, 3, 4, 5, 6, 7],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [5, 2],
                [0, 1, 7, 8],
                [1, 2, 8, 9],
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [10, 2],
                [0, 1],
                [1, 2],
            ],
            [
                [1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null],
                [0],
                [0, 1, 2, 'a', 'b', 'c', 'd', 'e', 3],
                [1, 2.2, '3', 4, [5], (object)[6], true, false, null],
            ],
            [
                [1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null],
                [0, 0],
                [0, 1, 2, 'a', 'b', 'c', 'd', 'e', 3],
                [1, 2.2, '3', 4, [5], (object)[6], true, false, null],
            ],
            [
                [1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null],
                [1, 0],
                [1, 2, 'a', 'b', 'c', 'd', 'e', 3],
                [2.2, '3', 4, [5], (object)[6], true, false, null],
            ],
            [
                [1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null],
                [3, 0],
                ['a', 'b', 'c', 'd', 'e', 3],
                [4, [5], (object)[6], true, false, null],
            ],
            [
                [1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null],
                [3, 2],
                [0, 1, 'c', 'd', 'e', 3],
                [1, 2.2, (object)[6], true, false, null],
            ],
            [
                [1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null],
                [10, 2],
                [0, 1],
                [1, 2.2],
            ],
            [
                [1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null],
                [10, 0],
                [],
                [],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $data
     * @param array $params
     * @param array $expectedKeys
     * @param array $expectedValues
     */
    public function testGenerators(\Generator $data, array $params, array $expectedKeys, array $expectedValues)
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::skip($data, ...$params) as $key => $datum) {
            $resultKeys[] = $key;
            $resultValues[] = $datum;
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
                [0],
                [],
                [],
            ],
            [
                $gen([]),
                [0, 1],
                [],
                [],
            ],
            [
                $gen([]),
                [0, 2],
                [],
                [],
            ],
            [
                $gen([]),
                [3, 5],
                [],
                [],
            ],
            [
                $gen([1]),
                [0],
                [0],
                [1],
            ],
            [
                $gen([1]),
                [0, 0],
                [0],
                [1],
            ],
            [
                $gen([1]),
                [0, 1],
                [0],
                [1],
            ],
            [
                $gen([1]),
                [0, 2],
                [0],
                [1],
            ],
            [
                $gen([1]),
                [1],
                [],
                [],
            ],
            [
                $gen([1]),
                [1, 0],
                [],
                [],
            ],
            [
                $gen([1]),
                [1, 1],
                [0],
                [1],
            ],
            [
                $gen([1]),
                [1, 2],
                [0],
                [1],
            ],
            [
                $gen([1]),
                [2, 0],
                [],
                [],
            ],
            [
                $gen([1]),
                [2, 1],
                [0],
                [1],
            ],
            [
                $gen([1]),
                [2, 2],
                [0],
                [1],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [0],
                [0, 1, 2, 3, 4, 5, 6, 7, 8],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [0, 0],
                [0, 1, 2, 3, 4, 5, 6, 7, 8],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 0],
                [1, 2, 3, 4, 5, 6, 7, 8],
                [2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [2, 0],
                [2, 3, 4, 5, 6, 7, 8],
                [3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [8, 0],
                [8],
                [9],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [9, 0],
                [],
                [],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [10, 0],
                [],
                [],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 1],
                [0, 2, 3, 4, 5, 6, 7, 8],
                [1, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 2],
                [0, 1, 3, 4, 5, 6, 7, 8],
                [1, 2, 4, 5, 6, 7, 8, 9],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 7],
                [0, 1, 2, 3, 4, 5, 6, 8],
                [1, 2, 3, 4, 5, 6, 7, 9],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 8],
                [0, 1, 2, 3, 4, 5, 6, 7],
                [1, 2, 3, 4, 5, 6, 7, 8],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 9],
                [0, 1, 2, 3, 4, 5, 6, 7, 8],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [2, 1],
                [0, 3, 4, 5, 6, 7, 8],
                [1, 4, 5, 6, 7, 8, 9],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [2, 2],
                [0, 1, 4, 5, 6, 7, 8],
                [1, 2, 5, 6, 7, 8, 9],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [2, 7],
                [0, 1, 2, 3, 4, 5, 6],
                [1, 2, 3, 4, 5, 6, 7],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [5, 2],
                [0, 1, 7, 8],
                [1, 2, 8, 9],
            ],
            [
                $gen([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [10, 2],
                [0, 1],
                [1, 2],
            ],
            [
                $gen([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [0],
                [0, 1, 2, 'a', 'b', 'c', 'd', 'e', 3],
                [1, 2.2, '3', 4, [5], (object)[6], true, false, null],
            ],
            [
                $gen([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [0, 0],
                [0, 1, 2, 'a', 'b', 'c', 'd', 'e', 3],
                [1, 2.2, '3', 4, [5], (object)[6], true, false, null],
            ],
            [
                $gen([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [1, 0],
                [1, 2, 'a', 'b', 'c', 'd', 'e', 3],
                [2.2, '3', 4, [5], (object)[6], true, false, null],
            ],
            [
                $gen([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [3, 0],
                ['a', 'b', 'c', 'd', 'e', 3],
                [4, [5], (object)[6], true, false, null],
            ],
            [
                $gen([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [3, 2],
                [0, 1, 'c', 'd', 'e', 3],
                [1, 2.2, (object)[6], true, false, null],
            ],
            [
                $gen([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [10, 2],
                [0, 1],
                [1, 2.2],
            ],
            [
                $gen([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [10, 0],
                [],
                [],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $data
     * @param array $params
     * @param array $expectedKeys
     * @param array $expectedValues
     */
    public function testIterators(\Iterator $data, array $params, array $expectedKeys, array $expectedValues)
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::skip($data, ...$params) as $key => $datum) {
            $resultKeys[] = $key;
            $resultValues[] = $datum;
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
                [0],
                [],
                [],
            ],
            [
                $iter([]),
                [0, 1],
                [],
                [],
            ],
            [
                $iter([]),
                [0, 2],
                [],
                [],
            ],
            [
                $iter([]),
                [3, 5],
                [],
                [],
            ],
            [
                $iter([1]),
                [0],
                [0],
                [1],
            ],
            [
                $iter([1]),
                [0, 0],
                [0],
                [1],
            ],
            [
                $iter([1]),
                [0, 1],
                [0],
                [1],
            ],
            [
                $iter([1]),
                [0, 2],
                [0],
                [1],
            ],
            [
                $iter([1]),
                [1],
                [],
                [],
            ],
            [
                $iter([1]),
                [1, 0],
                [],
                [],
            ],
            [
                $iter([1]),
                [1, 1],
                [0],
                [1],
            ],
            [
                $iter([1]),
                [1, 2],
                [0],
                [1],
            ],
            [
                $iter([1]),
                [2, 0],
                [],
                [],
            ],
            [
                $iter([1]),
                [2, 1],
                [0],
                [1],
            ],
            [
                $iter([1]),
                [2, 2],
                [0],
                [1],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [0],
                [0, 1, 2, 3, 4, 5, 6, 7, 8],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [0, 0],
                [0, 1, 2, 3, 4, 5, 6, 7, 8],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 0],
                [1, 2, 3, 4, 5, 6, 7, 8],
                [2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [2, 0],
                [2, 3, 4, 5, 6, 7, 8],
                [3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [8, 0],
                [8],
                [9],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [9, 0],
                [],
                [],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [10, 0],
                [],
                [],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 1],
                [0, 2, 3, 4, 5, 6, 7, 8],
                [1, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 2],
                [0, 1, 3, 4, 5, 6, 7, 8],
                [1, 2, 4, 5, 6, 7, 8, 9],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 7],
                [0, 1, 2, 3, 4, 5, 6, 8],
                [1, 2, 3, 4, 5, 6, 7, 9],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 8],
                [0, 1, 2, 3, 4, 5, 6, 7],
                [1, 2, 3, 4, 5, 6, 7, 8],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 9],
                [0, 1, 2, 3, 4, 5, 6, 7, 8],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [2, 1],
                [0, 3, 4, 5, 6, 7, 8],
                [1, 4, 5, 6, 7, 8, 9],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [2, 2],
                [0, 1, 4, 5, 6, 7, 8],
                [1, 2, 5, 6, 7, 8, 9],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [2, 7],
                [0, 1, 2, 3, 4, 5, 6],
                [1, 2, 3, 4, 5, 6, 7],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [5, 2],
                [0, 1, 7, 8],
                [1, 2, 8, 9],
            ],
            [
                $iter([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [10, 2],
                [0, 1],
                [1, 2],
            ],
            [
                $iter([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [0],
                [0, 1, 2, 'a', 'b', 'c', 'd', 'e', 3],
                [1, 2.2, '3', 4, [5], (object)[6], true, false, null],
            ],
            [
                $iter([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [0, 0],
                [0, 1, 2, 'a', 'b', 'c', 'd', 'e', 3],
                [1, 2.2, '3', 4, [5], (object)[6], true, false, null],
            ],
            [
                $iter([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [1, 0],
                [1, 2, 'a', 'b', 'c', 'd', 'e', 3],
                [2.2, '3', 4, [5], (object)[6], true, false, null],
            ],
            [
                $iter([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [3, 0],
                ['a', 'b', 'c', 'd', 'e', 3],
                [4, [5], (object)[6], true, false, null],
            ],
            [
                $iter([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [3, 2],
                [0, 1, 'c', 'd', 'e', 3],
                [1, 2.2, (object)[6], true, false, null],
            ],
            [
                $iter([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [10, 2],
                [0, 1],
                [1, 2.2],
            ],
            [
                $iter([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [10, 0],
                [],
                [],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $data
     * @param array $params
     * @param array $expectedKeys
     * @param array $expectedValues
     */
    public function testTraversables(\Traversable $data, array $params, array $expectedKeys, array $expectedValues)
    {
        // Given
        $resultKeys = [];
        $resultValues = [];

        // When
        foreach (Single::skip($data, ...$params) as $key => $datum) {
            $resultKeys[] = $key;
            $resultValues[] = $datum;
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
                [0],
                [],
                [],
            ],
            [
                $trav([]),
                [0, 1],
                [],
                [],
            ],
            [
                $trav([]),
                [0, 2],
                [],
                [],
            ],
            [
                $trav([]),
                [3, 5],
                [],
                [],
            ],
            [
                $trav([1]),
                [0],
                [0],
                [1],
            ],
            [
                $trav([1]),
                [0, 0],
                [0],
                [1],
            ],
            [
                $trav([1]),
                [0, 1],
                [0],
                [1],
            ],
            [
                $trav([1]),
                [0, 2],
                [0],
                [1],
            ],
            [
                $trav([1]),
                [1],
                [],
                [],
            ],
            [
                $trav([1]),
                [1, 0],
                [],
                [],
            ],
            [
                $trav([1]),
                [1, 1],
                [0],
                [1],
            ],
            [
                $trav([1]),
                [1, 2],
                [0],
                [1],
            ],
            [
                $trav([1]),
                [2, 0],
                [],
                [],
            ],
            [
                $trav([1]),
                [2, 1],
                [0],
                [1],
            ],
            [
                $trav([1]),
                [2, 2],
                [0],
                [1],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [0],
                [0, 1, 2, 3, 4, 5, 6, 7, 8],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [0, 0],
                [0, 1, 2, 3, 4, 5, 6, 7, 8],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 0],
                [1, 2, 3, 4, 5, 6, 7, 8],
                [2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [2, 0],
                [2, 3, 4, 5, 6, 7, 8],
                [3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [8, 0],
                [8],
                [9],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [9, 0],
                [],
                [],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [10, 0],
                [],
                [],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 1],
                [0, 2, 3, 4, 5, 6, 7, 8],
                [1, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 2],
                [0, 1, 3, 4, 5, 6, 7, 8],
                [1, 2, 4, 5, 6, 7, 8, 9],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 7],
                [0, 1, 2, 3, 4, 5, 6, 8],
                [1, 2, 3, 4, 5, 6, 7, 9],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 8],
                [0, 1, 2, 3, 4, 5, 6, 7],
                [1, 2, 3, 4, 5, 6, 7, 8],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [1, 9],
                [0, 1, 2, 3, 4, 5, 6, 7, 8],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [2, 1],
                [0, 3, 4, 5, 6, 7, 8],
                [1, 4, 5, 6, 7, 8, 9],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [2, 2],
                [0, 1, 4, 5, 6, 7, 8],
                [1, 2, 5, 6, 7, 8, 9],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [2, 7],
                [0, 1, 2, 3, 4, 5, 6],
                [1, 2, 3, 4, 5, 6, 7],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [5, 2],
                [0, 1, 7, 8],
                [1, 2, 8, 9],
            ],
            [
                $trav([1, 2, 3, 4, 5, 6, 7, 8, 9]),
                [10, 2],
                [0, 1],
                [1, 2],
            ],
            [
                $trav([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [0],
                [0, 1, 2, 'a', 'b', 'c', 'd', 'e', 3],
                [1, 2.2, '3', 4, [5], (object)[6], true, false, null],
            ],
            [
                $trav([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [0, 0],
                [0, 1, 2, 'a', 'b', 'c', 'd', 'e', 3],
                [1, 2.2, '3', 4, [5], (object)[6], true, false, null],
            ],
            [
                $trav([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [1, 0],
                [1, 2, 'a', 'b', 'c', 'd', 'e', 3],
                [2.2, '3', 4, [5], (object)[6], true, false, null],
            ],
            [
                $trav([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [3, 0],
                ['a', 'b', 'c', 'd', 'e', 3],
                [4, [5], (object)[6], true, false, null],
            ],
            [
                $trav([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [3, 2],
                [0, 1, 'c', 'd', 'e', 3],
                [1, 2.2, (object)[6], true, false, null],
            ],
            [
                $trav([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [10, 2],
                [0, 1],
                [1, 2.2],
            ],
            [
                $trav([1, 2.2, '3', 'a' => 4, 'b' => [5], 'c' => (object)[6], 'd' => true, 'e' => false, null]),
                [10, 0],
                [],
                [],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForInvalidParams
     * @param array $params
     * @return void
     */
    public function testInvalidParams(array $params): void
    {
        // Given
        $data = Single::skip([1, 2, 3], ...$params);

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach ($data as $_) {
            break;
        }
    }

    public function dataProviderForInvalidParams(): array
    {
        return [
            [[-1]],
            [[-1]],
            [[0, -1]],
            [[0, -2]],
            [[-1, -1]],
            [[-2, -1]],
            [[-1, -2]],
            [[-2, -2]],
        ];
    }
}

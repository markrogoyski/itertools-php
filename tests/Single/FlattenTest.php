<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class FlattenTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArray
     * @dataProvider dataProviderForGenerator
     * @dataProvider dataProviderForIterator
     * @dataProvider dataProviderForTraversable
     * @param iterable $iterable
     * @param int     $dimensions
     * @param array   $expected
     */
    public function testArray(iterable $iterable, int $dimensions, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::flatten($iterable, $dimensions) as $item) {
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
                0,
                [],
            ],
            [
                [],
                1,
                [],
            ],
            [
                [0],
                0,
                [0],
            ],
            [
                [0],
                1,
                [0],
            ],
            [
                [1],
                1,
                [1],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                0,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                1,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [0, [1, 2], 3, [4, 5]],
                0,
                [0, [1, 2], 3, [4, 5]],
            ],
            [
                [0, [1, 2], 3, [4, 5]],
                1,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [1, 2, [3], [4, 5], 6, []],
                0,
                [1, 2, [3], [4, 5], 6, []],
            ],
            [
                [1, 2, [3], [4, 5], 6, []],
                1,
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10],
                0,
                [[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10],
            ],
            [
                [[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10],
                1,
                [1, 2, [3, [4, 5]], 6, 7, 8, 9, 10],
            ],
            [
                [[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10],
                2,
                [1, 2, 3, [4, 5], 6, 7, 8, 9, 10],
            ],
            [
                [[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10],
                3,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            ],
            [
                [[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10],
                4,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            ],
            [
                [[1, 2, 3], [4, 5, 6], [7, 8, 9]],
                0,
                [[1, 2, 3], [4, 5, 6], [7, 8, 9]],
            ],
            [
                [[1, 2, 3], [4, 5, 6], [7, 8, 9]],
                1,
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                [1, [2], [[3]], [[[4]]], [[[[5]]]]],
                0,
                [1, [2], [[3]], [[[4]]], [[[[5]]]]],
            ],
            [
                [1, [2], [[3]], [[[4]]], [[[[5]]]]],
                1,
                [1, 2, [3], [[4]], [[[5]]]],
            ],
            [
                [1, [2], [[3]], [[[4]]], [[[[5]]]]],
                2,
                [1, 2, 3, [4], [[5]]],
            ],
            [
                [1, [2], [[3]], [[[4]]], [[[[5]]]]],
                3,
                [1, 2, 3, 4, [5]],
            ],
            [
                [1, [2], [[3]], [[[4]]], [[[[5]]]]],
                4,
                [1, 2, 3, 4, 5],
            ],
            [
                [1, [2], [[3]], [[[4]]], [[[[5]]]]],
                5,
                [1, 2, 3, 4, 5],
            ],
            [
                ['PHP', ['IterTools', 'MathPHP', 'SubnetCalculator'], 'Perl', ['SubnetCalculator']],
                0,
                ['PHP', ['IterTools', 'MathPHP', 'SubnetCalculator'], 'Perl', ['SubnetCalculator']],
            ],
            [
                ['PHP', ['IterTools', 'MathPHP', 'SubnetCalculator'], 'Perl', ['SubnetCalculator']],
                1,
                ['PHP', 'IterTools', 'MathPHP', 'SubnetCalculator', 'Perl', 'SubnetCalculator'],
            ],
            [
                [1, 2.2, 'three', true, false, null, new \stdClass(), [1, 2, 3], fn ($x) => $x + 1],
                0,
                [1, 2.2, 'three', true, false, null, new \stdClass(), [1, 2, 3], fn ($x) => $x + 1],
            ],
            [
                [1, 2.2, 'three', true, false, null, new \stdClass(), [1, 2, 3], fn ($x) => $x + 1],
                1,
                [1, 2.2, 'three', true, false, null, new \stdClass(), 1, 2, 3, fn ($x) => $x + 1],
            ],
            [
                [[1, 2.2, 'three'], [true, false], [null, new \stdClass()], [1, 2, 3], [fn ($x) => $x + 1]],
                0,
                [[1, 2.2, 'three'], [true, false], [null, new \stdClass()], [1, 2, 3], [fn ($x) => $x + 1]],
            ],
            [
                [[1, 2.2, 'three'], [true, false], [null, new \stdClass()], [1, 2, 3], [fn ($x) => $x + 1]],
                1,
                [1, 2.2, 'three', true, false, null, new \stdClass(), 1, 2, 3, fn ($x) => $x + 1],
            ],
            [
                [new \ArrayIterator([1, 2, 3]), GeneratorFixture::getGenerator([4, 5, 6]), new IteratorAggregateFixture([7, 8, 9])],
                0,
                [new \ArrayIterator([1, 2, 3]), GeneratorFixture::getGenerator([4, 5, 6]), new IteratorAggregateFixture([7, 8, 9])],
            ],
            [
                [new \ArrayIterator([1, 2, 3]), GeneratorFixture::getGenerator([4, 5, 6]), new IteratorAggregateFixture([7, 8, 9])],
                1,
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                [0, new \ArrayIterator([1, 2, 3]), new \ArrayIterator([new \ArrayIterator([4, 5, 6]), 7, 8]), 9],
                0,
                [0, new \ArrayIterator([1, 2, 3]), new \ArrayIterator([new \ArrayIterator([4, 5, 6]), 7, 8]), 9],
            ],
            [
                [0, new \ArrayIterator([1, 2, 3]), new \ArrayIterator([new \ArrayIterator([4, 5, 6]), 7, 8]), 9],
                1,
                [0, 1, 2, 3, new \ArrayIterator([4, 5, 6]), 7, 8, 9],
            ],
            [
                [0, new \ArrayIterator([1, 2, 3]), new \ArrayIterator([new \ArrayIterator([4, 5, 6]), 7, 8]), 9],
                2,
                [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            [
                [0, new \ArrayIterator([1, 2, 3]), new \ArrayIterator([new \ArrayIterator([4, 5, 6]), 7, 8]), 9],
                3,
                [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
        ];
    }

    public function dataProviderForGenerator(): \Generator
    {
        foreach ($this->dataProviderForArray() as [$iterable, $dimensions, $expected]) {
            yield [
                GeneratorFixture::getGenerator($iterable),
                $dimensions,
                $expected
            ];
        }
    }

    public function dataProviderForIterator(): \Generator
    {
        foreach ($this->dataProviderForArray() as [$iterable, $dimensions, $expected]) {
            yield [
                new ArrayIteratorFixture($iterable),
                $dimensions,
                $expected
            ];
        }
    }

    public function dataProviderForTraversable(): \Generator
    {
        foreach ($this->dataProviderForArray() as [$iterable, $dimensions, $expected]) {
            yield [
                new IteratorAggregateFixture($iterable),
                $dimensions,
                $expected
            ];
        }
    }

    /**
     * @test         flatten iterator_to_array
     * @dataProvider dataProviderForArray
     * @param        iterable $iterable
     * @param        int      $dimensions
     * @param        array    $expected
     */
    public function testIteratorToArray(iterable $iterable, int $dimensions, array $expected): void
    {
        // Given
        $iterator = Single::flatten($iterable, $dimensions);

        // When
        $result = \iterator_to_array($iterator, false);

        // Then
        $this->assertEquals($expected, \array_values($result));
    }

    /**
     * @test flatten with default dimensions
     */
    public function testFlattenWithDefaultDimensions(): void
    {
        // Given
        $data = [1, [2, 3], [4, 5], [[6, 7]], [8], 9];

        // And
        $expected = [1, 2, 3, 4, 5, [6, 7], 8, 9];

        // When
        $result = [];
        foreach (Single::flatten($data) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }
}

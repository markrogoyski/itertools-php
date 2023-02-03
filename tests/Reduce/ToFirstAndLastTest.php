<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToFirstAndLastTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        mixed $expected
     */
    public function testArray(array $data, $expected)
    {
        // When
        $result = Reduce::toFirstAndLast($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [0],
                [0, 0],
            ],
            [
                [null],
                [null, null],
            ],
            [
                [''],
                ['', ''],
            ],
            [
                ['', null],
                ['', null],
            ],
            [
                [3, 2],
                [3, 2],
            ],
            [
                [1, 2, 3],
                [1, 3],
            ],
            [
                [1.1, 1.1, 2.1, 2.1, 3.1, 3.1],
                [1.1, 3.1],
            ],
            [
                [[1], '2', 3],
                [[1], 3],
            ],
            [
                [false, [1], '2', 3],
                [false, 3],
            ],
            [
                [true, [1], '2', 3],
                [true, 3],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testGenerators(\Generator $data, $expected)
    {
        // When
        $result = Reduce::toFirstAndLast($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([0]),
                [0, 0],
            ],
            [
                $gen([null]),
                [null, null],
            ],
            [
                $gen(['']),
                ['', ''],
            ],
            [
                $gen(['', null]),
                ['', null],
            ],
            [
                $gen([3, 2]),
                [3, 2],
            ],
            [
                $gen([1, 2, 3]),
                [1, 3],
            ],
            [
                $gen([1.1, 1.1, 2.1, 2.1, 3.1, 3.1]),
                [1.1, 3.1],
            ],
            [
                $gen([[1], '2', 3]),
                [[1], 3],
            ],
            [
                $gen([false, [1], '2', 3]),
                [false, 3],
            ],
            [
                $gen([true, [1], '2', 3]),
                [true, 3],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param        \Iterator $data
     * @param        mixed $expected
     */
    public function testIterators(\Iterator $data, $expected)
    {
        // When
        $result = Reduce::toFirstAndLast($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([0]),
                [0, 0],
            ],
            [
                $iter([null]),
                [null, null],
            ],
            [
                $iter(['']),
                ['', ''],
            ],
            [
                $iter(['', null]),
                ['', null],
            ],
            [
                $iter([3, 2]),
                [3, 2],
            ],
            [
                $iter([1, 2, 3]),
                [1, 3],
            ],
            [
                $iter([1.1, 1.1, 2.1, 2.1, 3.1, 3.1]),
                [1.1, 3.1],
            ],
            [
                $iter([[1], '2', 3]),
                [[1], 3],
            ],
            [
                $iter([false, [1], '2', 3]),
                [false, 3],
            ],
            [
                $iter([true, [1], '2', 3]),
                [true, 3],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        mixed $expected
     */
    public function testTraversables(\Traversable $data, $expected)
    {
        // When
        $result = Reduce::toFirstAndLast($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([0]),
                [0, 0],
            ],
            [
                $trav([null]),
                [null, null],
            ],
            [
                $trav(['']),
                ['', ''],
            ],
            [
                $trav(['', null]),
                ['', null],
            ],
            [
                $trav([3, 2]),
                [3, 2],
            ],
            [
                $trav([1, 2, 3]),
                [1, 3],
            ],
            [
                $trav([1.1, 1.1, 2.1, 2.1, 3.1, 3.1]),
                [1.1, 3.1],
            ],
            [
                $trav([[1], '2', 3]),
                [[1], 3],
            ],
            [
                $trav([false, [1], '2', 3]),
                [false, 3],
            ],
            [
                $trav([true, [1], '2', 3]),
                [true, 3],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForEmptyIterable
     * @param iterable $data
     * @return void
     */
    public function testErrorOnEmptyCollection(iterable $data): void
    {
        $this->expectException(\LengthException::class);
        Reduce::toFirstAndLast($data);
    }

    /**
     * @dataProvider dataProviderForRewindableIterators
     * @param \NoRewindIterator $data
     * @param array $expected
     * @return void
     */
    public function testRewindableIterators(\NoRewindIterator $data, array $expected): void
    {
        // When
        $result = Reduce::toFirstAndLast($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForRewindableIterators(): array
    {
        $iter = fn (array $data) => new \NoRewindIterator(new \ArrayIterator($data));

        return [
            [
                $iter([1]),
                [1, 1],
            ],
            [
                $iter([1.1]),
                [1.1, 1.1],
            ],
            [
                $iter([null]),
                [null, null],
            ],
            [
                $iter(['a']),
                ['a', 'a'],
            ],
            [
                $iter(['abc']),
                ['abc', 'abc'],
            ],
        ];
    }
}

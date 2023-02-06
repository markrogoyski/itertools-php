<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToLastTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test toLast example usage
     */
    public function testToFirstAndLastExampleUsage(): void
    {
        // Given
        $data     = ['a', 'b', 'c', 'd', 'e', 'f', 'g'];
        $expected = 'g';

        // When
        $firstAndLast = Reduce::toLast($data);

        // Then
        $this->assertEquals($expected, $firstAndLast);
    }

    /**
     * @dataProvider dataProviderForArray
     * @param        array $data
     * @param        mixed $expected
     */
    public function testArray(array $data, $expected): void
    {
        // When
        $result = Reduce::toLast($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [0],
                0,
            ],
            [
                [null],
                null,
            ],
            [
                [''],
                '',
            ],
            [
                ['', null],
                null,
            ],
            [
                [3, 2],
                2,
            ],
            [
                [1, 2, 3],
                3,
            ],
            [
                [1.1, 1.1, 2.1, 2.1, 3.1, 3.1],
                3.1,
            ],
            [
                [[1], '2', 3],
                3,
            ],
            [
                [false, [1], '2', 3],
                3,
            ],
            [
                [true, [1], '2', 3],
                3,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param        \Generator $data
     * @param        mixed $expected
     */
    public function testGenerators(\Generator $data, $expected): void
    {
        // When
        $result = Reduce::toLast($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([0]),
                0,
            ],
            [
                $gen([null]),
                null,
            ],
            [
                $gen(['']),
                '',
            ],
            [
                $gen(['', null]),
                null,
            ],
            [
                $gen([3, 2]),
                2,
            ],
            [
                $gen([1, 2, 3]),
                3,
            ],
            [
                $gen([1.1, 1.1, 2.1, 2.1, 3.1, 3.1]),
                3.1,
            ],
            [
                $gen([[1], '2', 3]),
                3,
            ],
            [
                $gen([false, [1], '2', 3]),
                3,
            ],
            [
                $gen([true, [1], '2', 3]),
                3,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param        \Iterator $data
     * @param        mixed $expected
     */
    public function testIterators(\Iterator $data, $expected): void
    {
        // When
        $result = Reduce::toLast($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = fn (array $data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([0]),
                0,
            ],
            [
                $iter([null]),
                null,
            ],
            [
                $iter(['']),
                '',
            ],
            [
                $iter(['', null]),
                null,
            ],
            [
                $iter([3, 2]),
                2,
            ],
            [
                $iter([1, 2, 3]),
                3,
            ],
            [
                $iter([1.1, 1.1, 2.1, 2.1, 3.1, 3.1]),
                3.1,
            ],
            [
                $iter([[1], '2', 3]),
                3,
            ],
            [
                $iter([false, [1], '2', 3]),
                3,
            ],
            [
                $iter([true, [1], '2', 3]),
                3,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param        \Traversable $data
     * @param        mixed $expected
     */
    public function testTraversables(\Traversable $data, $expected): void
    {
        // When
        $result = Reduce::toLast($data);

        // Then
        $this->assertSame($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([0]),
                0,
            ],
            [
                $trav([null]),
                null,
            ],
            [
                $trav(['']),
                '',
            ],
            [
                $trav(['', null]),
                null,
            ],
            [
                $trav([3, 2]),
                2,
            ],
            [
                $trav([1, 2, 3]),
                3,
            ],
            [
                $trav([1.1, 1.1, 2.1, 2.1, 3.1, 3.1]),
                3.1,
            ],
            [
                $trav([[1], '2', 3]),
                3,
            ],
            [
                $trav([false, [1], '2', 3]),
                3,
            ],
            [
                $trav([true, [1], '2', 3]),
                3,
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
        // Then
        $this->expectException(\LengthException::class);

        // When
        Reduce::toLast($data);
    }
}

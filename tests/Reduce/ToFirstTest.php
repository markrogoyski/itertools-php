<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class ToFirstTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test toFirst example usage
     */
    public function testToFirstAndLastExampleUsage(): void
    {
        // Given
        $data     = ['a', 'b', 'c', 'd', 'e', 'f', 'g'];
        $expected = 'a';

        // When
        $firstAndLast = Reduce::toFirst($data);

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
        $result = Reduce::toFirst($data);

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
                '',
            ],
            [
                [3, 2],
                3,
            ],
            [
                [1, 2, 3],
                1,
            ],
            [
                [1.1, 1.1, 2.1, 2.1, 3.1, 3.1],
                1.1,
            ],
            [
                [[1], '2', 3],
                [1],
            ],
            [
                [false, [1], '2', 3],
                false,
            ],
            [
                [true, [1], '2', 3],
                true,
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
        $result = Reduce::toFirst($data);

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
                '',
            ],
            [
                $gen([3, 2]),
                3,
            ],
            [
                $gen([1, 2, 3]),
                1,
            ],
            [
                $gen([1.1, 1.1, 2.1, 2.1, 3.1, 3.1]),
                1.1,
            ],
            [
                $gen([[1], '2', 3]),
                [1],
            ],
            [
                $gen([false, [1], '2', 3]),
                false,
            ],
            [
                $gen([true, [1], '2', 3]),
                true,
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
        $result = Reduce::toFirst($data);

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
                '',
            ],
            [
                $iter([3, 2]),
                3,
            ],
            [
                $iter([1, 2, 3]),
                1,
            ],
            [
                $iter([1.1, 1.1, 2.1, 2.1, 3.1, 3.1]),
                1.1,
            ],
            [
                $iter([[1], '2', 3]),
                [1],
            ],
            [
                $iter([false, [1], '2', 3]),
                false,
            ],
            [
                $iter([true, [1], '2', 3]),
                true,
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
        $result = Reduce::toFirst($data);

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
                '',
            ],
            [
                $trav([3, 2]),
                3,
            ],
            [
                $trav([1, 2, 3]),
                1,
            ],
            [
                $trav([1.1, 1.1, 2.1, 2.1, 3.1, 3.1]),
                1.1,
            ],
            [
                $trav([[1], '2', 3]),
                [1],
            ],
            [
                $trav([false, [1], '2', 3]),
                false,
            ],
            [
                $trav([true, [1], '2', 3]),
                true,
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
        Reduce::toFirst($data);
    }
}

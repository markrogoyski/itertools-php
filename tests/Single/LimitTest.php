<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture;

class LimitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         limit array
     * @dataProvider dataProviderForArray
     * @param        array    $iterable
     * @param        int      $limit
     * @param        array    $expected
     */
    public function testArray(array $iterable, int $limit, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::limit($iterable, $limit) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [0, 1, 2, 3, 4, 5],
                0,
                [],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                1,
                [0],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                2,
                [0, 1],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                3,
                [0, 1, 2],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                4,
                [0, 1, 2, 3],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                5,
                [0, 1, 2, 3, 4],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                6,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                7,
                [0, 1, 2, 3, 4, 5],
            ],
        ];
    }


    /**
     * @test         limit generator
     * @dataProvider dataProviderForGenerator
     * @param        \Generator $iterable
     * @param        int        $limit
     * @param        array      $expected
     */
    public function testGenerator(\Generator $iterable, int $limit, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::limit($iterable, $limit) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForGenerator(): array
    {
        return [
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                0,
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                1,
                [0],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                2,
                [0, 1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                3,
                [0, 1, 2],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                4,
                [0, 1, 2, 3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                5,
                [0, 1, 2, 3, 4],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                6,
                [0, 1, 2, 3, 4, 5],
            ],
        ];
    }

    /**
     * @test         limit iterator
     * @dataProvider dataProviderForIterator
     * @param        \Iterator $iterable
     * @param        int       $limit
     * @param        array     $expected
     */
    public function testIterator(\Iterator $iterable, int $limit, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::limit($iterable, $limit) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForIterator(): array
    {
        return [
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                0,
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                1,
                [0],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                2,
                [0, 1],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                3,
                [0, 1, 2],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                4,
                [0, 1, 2, 3],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                5,
                [0, 1, 2, 3, 4],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                6,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                7,
                [0, 1, 2, 3, 4, 5],
            ],
        ];
    }

    /**
     * @test         limit traversable
     * @dataProvider dataProviderForTraversable
     * @param        \Traversable $iterable
     * @param        int          $limit
     * @param        array        $expected
     */
    public function testTraversable(\Traversable $iterable, int $limit, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::limit($iterable, $limit) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForTraversable(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                0,
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                1,
                [0],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                2,
                [0, 1],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                3,
                [0, 1, 2],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                4,
                [0, 1, 2, 3],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                5,
                [0, 1, 2, 3, 4],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                6,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                7,
                [0, 1, 2, 3, 4, 5],
            ],
        ];
    }

    /**
     * @test invalid limit
     */
    public function testInvalidLimit(): void
    {
        // Given
        $data         = [1, 2, 3];
        $invalidLimit = -1;

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (Single::limit($data, $invalidLimit) as $_) {
            break;
        }
    }

    /**
     * @test         limit iterator_to_array
     * @dataProvider dataProviderForArray
     * @param        array    $iterable
     * @param        int      $limit
     * @param        array    $expected
     */
    public function testIteratorToArray(array $iterable, int $limit, array $expected): void
    {
        // Given
        $iterator = Single::limit($iterable, $limit);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }
}

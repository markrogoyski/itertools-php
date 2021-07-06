<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture;

class DropWhileTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         dropWhile array
     * @dataProvider dataProviderForArray
     * @param        array    $iterable
     * @param        callable $predicate
     * @param        array    $expected
     */
    public function testArray(array $iterable, callable $predicate, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::dropWhile($iterable, $predicate) as $item) {
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
                fn ($x) => $x < 0,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 1,
                [1, 2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 2,
                [2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 3,
                [3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 4,
                [4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 5,
                [5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 6,
                [],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x > 0,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x > 1,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x > -1,
                [],
            ],
            [
                [5, 4, 3, 2, 1, 0],
                fn ($x) => $x > 2,
                [2, 1, 0],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => true,
                [],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => false,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [1, 4, 6, 4, 1],
                fn ($x) => $x < 5,
                [6, 4, 1],
            ],
            [
                [50, 60, 70, 85, 65, 90],
                fn ($x) => $x < 70,
                [70, 85, 65, 90],
            ],
        ];
    }

    /**
     * @test         dropWhile generator
     * @dataProvider dataProviderForGenerator
     * @param        \Generator $iterable
     * @param        callable   $predicate
     * @param        array      $expected
     */
    public function testGenerator(\Generator $iterable, callable $predicate, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::dropWhile($iterable, $predicate) as $item) {
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
                fn ($x) => $x < 0,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 1,
                [1, 2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 2,
                [2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 3,
                [3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 4,
                [4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 5,
                [5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 6,
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 0,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 1,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > -1,
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([5, 4, 3, 2, 1, 0]),
                fn ($x) => $x > 2,
                [2, 1, 0],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => true,
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => false,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 4, 6, 4, 1]),
                fn ($x) => $x < 5,
                [6, 4, 1],
            ],
        ];
    }

    /**
     * @test         dropWhile iterator
     * @dataProvider dataProviderForIterator
     * @param        \Iterator $iterable
     * @param        callable  $predicate
     * @param        array     $expected
     */
    public function testIterator(\Iterator $iterable, callable $predicate, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::dropWhile($iterable, $predicate) as $item) {
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
                fn ($x) => $x < 0,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 1,
                [1, 2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 2,
                [2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 3,
                [3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 4,
                [4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 5,
                [5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 6,
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 0,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 1,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > -1,
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([5, 4, 3, 2, 1, 0]),
                fn ($x) => $x > 2,
                [2, 1, 0],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => true,
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => false,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 4, 6, 4, 1]),
                fn ($x) => $x < 5,
                [6, 4, 1],
            ],
        ];
    }

    /**
     * @test         dropWhile traversable
     * @dataProvider dataProviderForTraversable
     * @param        \Traversable $iterable
     * @param        callable  $predicate
     * @param        array     $expected
     */
    public function testTraversable(\Traversable $iterable, callable $predicate, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::dropWhile($iterable, $predicate) as $item) {
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
                fn ($x) => $x < 0,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 1,
                [1, 2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 2,
                [2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 3,
                [3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 4,
                [4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 5,
                [5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 6,
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 0,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 1,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > -1,
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([5, 4, 3, 2, 1, 0]),
                fn ($x) => $x > 2,
                [2, 1, 0],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => true,
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => false,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 4, 6, 4, 1]),
                fn ($x) => $x < 5,
                [6, 4, 1],
            ],
        ];
    }
}

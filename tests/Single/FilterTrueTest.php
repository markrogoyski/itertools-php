<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture;

class FilterTrueTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         filterTrue array
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
        foreach (Single::filterTrue($iterable, $predicate) as $item) {
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
                [],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 1,
                [0],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 2,
                [0, 1],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 3,
                [0, 1, 2],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 4,
                [0, 1, 2, 3],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 5,
                [0, 1, 2, 3, 4],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x < 6,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x > 0,
                [1, 2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x > 1,
                [2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => $x > -1,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [5, 4, 3, 2, 1, 0],
                fn ($x) => $x > 2,
                [5, 4, 3],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => true,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($x) => false,
                [],
            ],
            [
                [1, 4, 6, 4, 1],
                fn ($x) => $x < 5,
                [1, 4, 4, 1],
            ],
            [
                [50, 60, 70, 85, 65, 90],
                fn ($x) => $x < 65,
                [50, 60],
            ],
            [
                [50, 60, 70, 85, 65, 90],
                fn ($x) => $x <= 65,
                [50, 60, 65],
            ],
        ];
    }

    /**
     * @test filterTrue with no predicate
     */
    public function testNoPredicate()
    {
        // Given
        $data = [-1, 0, 1, 2, 3, true, false, null, [], [0], [1], 'a', ''];

        // And
        $result   = [];
        $expected = [-1, 1, 2, 3, true, [0], [1], 'a'];

        // When
        foreach (Single::filterTrue($data) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         filterTrue generator
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
        foreach (Single::filterTrue($iterable, $predicate) as $item) {
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
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 1,
                [0],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 2,
                [0, 1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 3,
                [0, 1, 2],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 4,
                [0, 1, 2, 3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 5,
                [0, 1, 2, 3, 4],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 6,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 0,
                [1, 2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 1,
                [2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > -1,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([5, 4, 3, 2, 1, 0]),
                fn ($x) => $x > 2,
                [5, 4, 3],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => true,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([0, 1, 2, 3, 4, 5]),
                fn ($x) => false,
                [],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([1, 4, 6, 4, 1]),
                fn ($x) => $x < 5,
                [1, 4, 4, 1],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([50, 60, 70, 85, 65, 90]),
                fn ($x) => $x < 65,
                [50, 60],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([50, 60, 70, 85, 65, 90]),
                fn ($x) => $x <= 65,
                [50, 60, 65],
            ],
        ];
    }

    /**
     * @test         filterTrue iterator
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
        foreach (Single::filterTrue($iterable, $predicate) as $item) {
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
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 1,
                [0],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 2,
                [0, 1],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 3,
                [0, 1, 2],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 4,
                [0, 1, 2, 3],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 5,
                [0, 1, 2, 3, 4],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 6,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 0,
                [1, 2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 1,
                [2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > -1,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([5, 4, 3, 2, 1, 0]),
                fn ($x) => $x > 2,
                [5, 4, 3],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => true,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\ArrayIteratorFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => false,
                [],
            ],
            [
                new Fixture\ArrayIteratorFixture([1, 4, 6, 4, 1]),
                fn ($x) => $x < 5,
                [1, 4, 4, 1],
            ],
            [
                new Fixture\ArrayIteratorFixture([50, 60, 70, 85, 65, 90]),
                fn ($x) => $x < 65,
                [50, 60],
            ],
            [
                new Fixture\ArrayIteratorFixture([50, 60, 70, 85, 65, 90]),
                fn ($x) => $x <= 65,
                [50, 60, 65],
            ],
        ];
    }

    /**
     * @test         filterTrue traversable
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
        foreach (Single::filterTrue($iterable, $predicate) as $item) {
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
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 1,
                [0],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 2,
                [0, 1],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 3,
                [0, 1, 2],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 4,
                [0, 1, 2, 3],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 5,
                [0, 1, 2, 3, 4],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x < 6,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 0,
                [1, 2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > 1,
                [2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => $x > -1,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([5, 4, 3, 2, 1, 0]),
                fn ($x) => $x > 2,
                [5, 4, 3],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => true,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                new Fixture\IteratorAggregateFixture([0, 1, 2, 3, 4, 5]),
                fn ($x) => false,
                [],
            ],
            [
                new Fixture\IteratorAggregateFixture([1, 4, 6, 4, 1]),
                fn ($x) => $x < 5,
                [1, 4, 4, 1],
            ],
            [
                new Fixture\IteratorAggregateFixture([50, 60, 70, 85, 65, 90]),
                fn ($x) => $x < 65,
                [50, 60],
            ],
            [
                new Fixture\IteratorAggregateFixture([50, 60, 70, 85, 65, 90]),
                fn ($x) => $x <= 65,
                [50, 60, 65],
            ],
        ];
    }

    /**
     * @test         filterTrue iterator_to_array
     * @dataProvider dataProviderForArray
     * @param        array    $iterable
     * @param        callable $predicate
     * @param        array    $expected
     */
    public function testIteratorToArray(array $iterable, callable $predicate, array $expected): void
    {
        // Given
        $iterator = Single::filterTrue($iterable, $predicate);

        // When
        $result = iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }
}

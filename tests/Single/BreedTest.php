<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class BreedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForArray
     * @param array $iterable
     * @param callable $breeder
     * @param array $expected
     */
    public function testArray(array $iterable, callable $breeder, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::breed($iterable, $breeder) as $item) {
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
                fn ($item) => [$item],
                [],
            ],
            [
                [0],
                fn ($item) => [$item],
                [0],
            ],
            [
                [1],
                fn ($item) => [$item],
                [1],
            ],
            [
                [2],
                fn ($item) => [$item],
                [2],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($item) => [$item],
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [2],
                fn ($item) => [$item, $item],
                [2, 2],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($item) => [$item, $item],
                [0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($item) => [$item, -$item],
                [0, 0, 1, -1, 2, -2, 3, -3, 4, -4, 5, -5],
            ],
            [
                [],
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                [0],
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                [1],
                fn ($item) => Single::repeat($item, $item),
                [1],
            ],
            [
                [2],
                fn ($item) => Single::repeat($item, $item),
                [2, 2],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn ($item) => Single::repeat($item, $item),
                [1, 2, 2, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 5],
            ],
            [
                [
                    ['name' => 'bird', 'eggs' => 2],
                    ['name' => 'lizard', 'eggs' => 3],
                    ['name' => 'echidna', 'eggs' => 1],
                    ['name' => 'tyrannosaur', 'eggs' => 0],
                ],
                fn ($animal) => Single::repeat($animal['name'], $animal['eggs']),
                ['bird', 'bird', 'lizard', 'lizard', 'lizard', 'echidna'],
            ],
            [
                [[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10],
                fn ($item, $breeder) => is_iterable($item)
                    ? Single::breed($item, $breeder)
                    : [$item],
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $iterable
     * @param callable $breeder
     * @param array $expected
     */
    public function testGenerators(\Generator $iterable, callable $breeder, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::breed($iterable, $breeder) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForGenerators(): array
    {
        $gen = fn ($data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                fn ($item) => [$item],
                [],
            ],
            [
                $gen([0]),
                fn ($item) => [$item],
                [0],
            ],
            [
                $gen([1]),
                fn ($item) => [$item],
                [1],
            ],
            [
                $gen([2]),
                fn ($item) => [$item],
                [2],
            ],
            [
                $gen([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item],
                [0, 1, 2, 3, 4, 5],
            ],
            [
                $gen([2]),
                fn ($item) => [$item, $item],
                [2, 2],
            ],
            [
                $gen([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item, $item],
                [0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
            ],
            [
                $gen([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item, -$item],
                [0, 0, 1, -1, 2, -2, 3, -3, 4, -4, 5, -5],
            ],
            [
                $gen([]),
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                $gen([0]),
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                $gen([1]),
                fn ($item) => Single::repeat($item, $item),
                [1],
            ],
            [
                $gen([2]),
                fn ($item) => Single::repeat($item, $item),
                [2, 2],
            ],
            [
                $gen([0, 1, 2, 3, 4, 5]),
                fn ($item) => Single::repeat($item, $item),
                [1, 2, 2, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 5],
            ],
            [
                $gen([
                    ['name' => 'bird', 'eggs' => 2],
                    ['name' => 'lizard', 'eggs' => 3],
                    ['name' => 'echidna', 'eggs' => 1],
                    ['name' => 'tyrannosaur', 'eggs' => 0],
                ]),
                fn ($animal) => Single::repeat($animal['name'], $animal['eggs']),
                ['bird', 'bird', 'lizard', 'lizard', 'lizard', 'echidna'],
            ],
            [
                $gen([[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10]),
                fn ($item, $breeder) => is_iterable($item)
                    ? Single::breed($item, $breeder)
                    : [$item],
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $iterable
     * @param callable $breeder
     * @param array $expected
     */
    public function testIterators(\Iterator $iterable, callable $breeder, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::breed($iterable, $breeder) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForIterators(): array
    {
        $iter = fn ($data) => new ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                fn ($item) => [$item],
                [],
            ],
            [
                $iter([0]),
                fn ($item) => [$item],
                [0],
            ],
            [
                $iter([1]),
                fn ($item) => [$item],
                [1],
            ],
            [
                $iter([2]),
                fn ($item) => [$item],
                [2],
            ],
            [
                $iter([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item],
                [0, 1, 2, 3, 4, 5],
            ],
            [
                $iter([2]),
                fn ($item) => [$item, $item],
                [2, 2],
            ],
            [
                $iter([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item, $item],
                [0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
            ],
            [
                $iter([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item, -$item],
                [0, 0, 1, -1, 2, -2, 3, -3, 4, -4, 5, -5],
            ],
            [
                $iter([]),
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                $iter([0]),
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                $iter([1]),
                fn ($item) => Single::repeat($item, $item),
                [1],
            ],
            [
                $iter([2]),
                fn ($item) => Single::repeat($item, $item),
                [2, 2],
            ],
            [
                $iter([0, 1, 2, 3, 4, 5]),
                fn ($item) => Single::repeat($item, $item),
                [1, 2, 2, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 5],
            ],
            [
                $iter([
                    ['name' => 'bird', 'eggs' => 2],
                    ['name' => 'lizard', 'eggs' => 3],
                    ['name' => 'echidna', 'eggs' => 1],
                    ['name' => 'tyrannosaur', 'eggs' => 0],
                ]),
                fn ($animal) => Single::repeat($animal['name'], $animal['eggs']),
                ['bird', 'bird', 'lizard', 'lizard', 'lizard', 'echidna'],
            ],
            [
                $iter([[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10]),
                fn ($item, $breeder) => is_iterable($item)
                    ? Single::breed($item, $breeder)
                    : [$item],
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $iterable
     * @param callable $breeder
     * @param array $expected
     */
    public function testTraversables(\Traversable $iterable, callable $breeder, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::breed($iterable, $breeder) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForTraversables(): array
    {
        $trav = fn ($data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                fn ($item) => [$item],
                [],
            ],
            [
                $trav([0]),
                fn ($item) => [$item],
                [0],
            ],
            [
                $trav([1]),
                fn ($item) => [$item],
                [1],
            ],
            [
                $trav([2]),
                fn ($item) => [$item],
                [2],
            ],
            [
                $trav([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item],
                [0, 1, 2, 3, 4, 5],
            ],
            [
                $trav([2]),
                fn ($item) => [$item, $item],
                [2, 2],
            ],
            [
                $trav([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item, $item],
                [0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
            ],
            [
                $trav([0, 1, 2, 3, 4, 5]),
                fn ($item) => [$item, -$item],
                [0, 0, 1, -1, 2, -2, 3, -3, 4, -4, 5, -5],
            ],
            [
                $trav([]),
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                $trav([0]),
                fn ($item) => Single::repeat($item, $item),
                [],
            ],
            [
                $trav([1]),
                fn ($item) => Single::repeat($item, $item),
                [1],
            ],
            [
                $trav([2]),
                fn ($item) => Single::repeat($item, $item),
                [2, 2],
            ],
            [
                $trav([0, 1, 2, 3, 4, 5]),
                fn ($item) => Single::repeat($item, $item),
                [1, 2, 2, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 5],
            ],
            [
                $trav([
                    ['name' => 'bird', 'eggs' => 2],
                    ['name' => 'lizard', 'eggs' => 3],
                    ['name' => 'echidna', 'eggs' => 1],
                    ['name' => 'tyrannosaur', 'eggs' => 0],
                ]),
                fn ($animal) => Single::repeat($animal['name'], $animal['eggs']),
                ['bird', 'bird', 'lizard', 'lizard', 'lizard', 'echidna'],
            ],
            [
                $trav([[1, 2, [3, [4, 5]], 6], [7], [8, 9], 10]),
                fn ($item, $breeder) => is_iterable($item)
                    ? Single::breed($item, $breeder)
                    : [$item],
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            ],
        ];
    }
}

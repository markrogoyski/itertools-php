<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture;

class GroupByTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         groupBy array
     * @dataProvider dataProviderForArray
     * @param        array    $iterable
     * @param        callable $groupKeyFunction
     * @param        array    $expected
     */
    public function testArray(array $iterable, callable $groupKeyFunction, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::groupBy($iterable, $groupKeyFunction) as $groupKey => $groupData) {
            $result[$groupKey] = $groupData;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public function dataProviderForArray(): array
    {
        return [
            [
                [['a', 1], ['a', 2], ['b', 3], ['b', 4], ['c', 5], ['a', 6], ['c', 7]],
                fn ($x) => $x[0],
                [
                    'a' => [['a', 1], ['a', 2], ['a', 6]],
                    'b' => [['b', 3], ['b', 4]],
                    'c' => [['c', 5], ['c', 7]],
                ],
            ],
            [
                [['a', 1], ['a', 2], ['b', 3], ['b', 4], ['c', 5], ['a', 6], ['c', 7]],
                fn ($x) => $x[1],
                [
                    1 => [['a', 1]],
                    2 => [['a', 2]],
                    3 => [['b', 3]],
                    4 => [['b', 4]],
                    5 => [['c', 5]],
                    6 => [['a', 6]],
                    7 => [['c', 7]],
                ],
            ],
            [
                [[1, 'a'], [2, 'a'], [3, 'b'], [4, 'b'], [5, 'c'], [6, 'a'], [7, 'c']],
                fn ($x) => $x[1],
                [
                    'a' => [[1, 'a'], [2, 'a'], [6, 'a']],
                    'b' => [[3, 'b'], [4, 'b']],
                    'c' => [[5, 'c'], [7, 'c']],
                ],
            ],
            [
                [[1, 'a'], [2, 'a'], [3, 'b'], [4, 'b'], [5, 'c'], [6, 'a'], [7, 'c']],
                fn ($x) => $x[0],
                [
                    1 => [[1, 'a']],
                    2 => [[2, 'a']],
                    3 => [[3, 'b']],
                    4 => [[4, 'b']],
                    5 => [[5, 'c']],
                    6 => [[6, 'a']],
                    7 => [[7, 'c']],
                ],
            ],
            [
                [
                    ['Episode IV', "Luke"],
                    ['Episode IV', "Leia"],
                    ['Episode IV', "Chewie"],
                    ['Episode IV', "Han"],
                    ['Episode IV', "Obi-wan"],
                    ['Episode IV', "R2-D2"],
                    ['Episode IV', "C3P0"],
                    ['Episode IV', "Vader"],
                    ['Episode IV', "Tarkin"],
                    ['Episode V', "Luke"],
                    ['Episode V', "Leia"],
                    ['Episode V', "Chewie"],
                    ['Episode V', "Han"],
                    ['Episode V', "Obi-wan"],
                    ['Episode V', "R2-D2"],
                    ['Episode V', "C3P0"],
                    ['Episode V', "Lando"],
                    ['Episode V', "Han"],
                    ['Episode V', "Vader"],
                    ['Episode V', "Emperor"],
                    ['Episode V', "Yoda"],
                    ['Episode V', "Boba Fett"],
                    ['Episode VI', "Luke"],
                    ['Episode VI', "Leia"],
                    ['Episode VI', "Chewie"],
                    ['Episode VI', "Han"],
                    ['Episode VI', "Obi-wan"],
                    ['Episode VI', "R2-D2"],
                    ['Episode VI', "C3P0"],
                    ['Episode VI', "Lando"],
                    ['Episode VI', "Han"],
                    ['Episode VI', "Vader"],
                    ['Episode VI', "Emperor"],
                    ['Episode VI', "Yoda"],
                    ['Episode VI', "Boba Fett"],
                    ['Episode VI', "Jabba"],
                ],
                fn ($x) => $x[0],
                [
                    'Episode IV' => [
                        ['Episode IV', "Luke"],
                        ['Episode IV', "Leia"],
                        ['Episode IV', "Chewie"],
                        ['Episode IV', "Han"],
                        ['Episode IV', "Obi-wan"],
                        ['Episode IV', "R2-D2"],
                        ['Episode IV', "C3P0"],
                        ['Episode IV', "Vader"],
                        ['Episode IV', "Tarkin"],
                    ],
                    'Episode V' => [
                        ['Episode V', "Luke"],
                        ['Episode V', "Leia"],
                        ['Episode V', "Chewie"],
                        ['Episode V', "Han"],
                        ['Episode V', "Obi-wan"],
                        ['Episode V', "R2-D2"],
                        ['Episode V', "C3P0"],
                        ['Episode V', "Lando"],
                        ['Episode V', "Han"],
                        ['Episode V', "Vader"],
                        ['Episode V', "Emperor"],
                        ['Episode V', "Yoda"],
                        ['Episode V', "Boba Fett"],
                    ],
                    'Episode VI' => [
                        ['Episode VI', "Luke"],
                        ['Episode VI', "Leia"],
                        ['Episode VI', "Chewie"],
                        ['Episode VI', "Han"],
                        ['Episode VI', "Obi-wan"],
                        ['Episode VI', "R2-D2"],
                        ['Episode VI', "C3P0"],
                        ['Episode VI', "Lando"],
                        ['Episode VI', "Han"],
                        ['Episode VI', "Vader"],
                        ['Episode VI', "Emperor"],
                        ['Episode VI', "Yoda"],
                        ['Episode VI', "Boba Fett"],
                        ['Episode VI', "Jabba"],
                    ]
                ],
            ],
            [
                [
                    ['Episode IV', "Luke"],
                    ['Episode IV', "Leia"],
                    ['Episode IV', "Chewie"],
                    ['Episode IV', "Han"],
                    ['Episode IV', "Obi-wan"],
                    ['Episode IV', "R2-D2"],
                    ['Episode IV', "C3P0"],
                    ['Episode IV', "Vader"],
                    ['Episode IV', "Tarkin"],
                    ['Episode V', "Luke"],
                    ['Episode V', "Leia"],
                    ['Episode V', "Chewie"],
                    ['Episode V', "Han"],
                    ['Episode V', "Obi-wan"],
                    ['Episode V', "R2-D2"],
                    ['Episode V', "C3P0"],
                    ['Episode V', "Lando"],
                    ['Episode V', "Vader"],
                    ['Episode V', "Emperor"],
                    ['Episode V', "Yoda"],
                    ['Episode V', "Boba Fett"],
                    ['Episode VI', "Luke"],
                    ['Episode VI', "Leia"],
                    ['Episode VI', "Chewie"],
                    ['Episode VI', "Han"],
                    ['Episode VI', "Obi-wan"],
                    ['Episode VI', "R2-D2"],
                    ['Episode VI', "C3P0"],
                    ['Episode VI', "Lando"],
                    ['Episode VI', "Vader"],
                    ['Episode VI', "Emperor"],
                    ['Episode VI', "Yoda"],
                    ['Episode VI', "Boba Fett"],
                    ['Episode VI', "Jabba"],
                ],
                fn ($x) => $x[1],
                [
                    'Luke' => [
                        ['Episode IV', "Luke"],
                        ['Episode V', "Luke"],
                        ['Episode VI', "Luke"],
                    ],
                    'Leia' => [
                        ['Episode IV', "Leia"],
                        ['Episode V', "Leia"],
                        ['Episode VI', "Leia"],
                    ],
                    'Chewie' => [
                        ['Episode IV', "Chewie"],
                        ['Episode V', "Chewie"],
                        ['Episode VI', "Chewie"],
                    ],
                    'Han' => [
                        ['Episode IV', "Han"],
                        ['Episode V', "Han"],
                        ['Episode VI', "Han"],
                    ],
                    'Obi-wan' => [
                        ['Episode IV', "Obi-wan"],
                        ['Episode V', "Obi-wan"],
                        ['Episode VI', "Obi-wan"],
                    ],
                    'R2-D2' => [
                        ['Episode IV', "R2-D2"],
                        ['Episode V', "R2-D2"],
                        ['Episode VI', "R2-D2"],
                    ],
                    'C3P0' => [
                        ['Episode IV', "C3P0"],
                        ['Episode V', "C3P0"],
                        ['Episode VI', "C3P0"],
                    ],
                    'Vader' => [
                        ['Episode IV', "Vader"],
                        ['Episode V', "Vader"],
                        ['Episode VI', "Vader"],
                    ],
                    'Tarkin' => [
                        ['Episode IV', "Tarkin"],
                    ],
                    'Lando' => [
                        ['Episode V', "Lando"],
                        ['Episode VI', "Lando"],
                    ],
                    'Yoda' => [
                        ['Episode V', "Yoda"],
                        ['Episode VI', "Yoda"],
                    ],
                    'Boba Fett' => [
                        ['Episode V', "Boba Fett"],
                        ['Episode VI', "Boba Fett"],
                    ],
                    'Emperor' => [
                        ['Episode V', "Emperor"],
                        ['Episode VI', "Emperor"],
                    ],
                    'Jabba' => [
                        ['Episode VI', "Jabba"],
                    ],
                ],
            ],
            [
                [
                    ['Garfield', 'cat'],
                    ['Tom', 'cat'],
                    ['Felix', 'cat'],
                    ['Heathcliff', 'cat'],
                    ['Snoopy', 'dog'],
                    ['Scooby-Doo', 'dog'],
                    ['Odie', 'dog'],
                    ['Donald', 'duck'],
                    ['Daffy', 'duck'],
                ],
                fn ($x) => $x[1],
                [
                    'cat' => [
                        ['Garfield', 'cat'],
                        ['Tom', 'cat'],
                        ['Felix', 'cat'],
                        ['Heathcliff', 'cat'],
                    ],
                    'dog' => [
                        ['Snoopy', 'dog'],
                        ['Scooby-Doo', 'dog'],
                        ['Odie', 'dog'],
                    ],
                    'duck' => [
                        ['Donald', 'duck'],
                        ['Daffy', 'duck'],
                    ]
                ]
            ],
        ];
    }

    /**
     * @test         groupBy generator
     * @dataProvider dataProviderForGenerator
     * @param        \Generator $generator
     * @param        callable   $groupKeyFunction
     * @param        array      $expected
     */
    public function testGenerator(\Generator $generator, callable $groupKeyFunction, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::groupBy($generator, $groupKeyFunction) as $groupKey => $groupData) {
            $result[$groupKey] = $groupData;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public function dataProviderForGenerator(): array
    {
        return [
            [
                Fixture\GeneratorFixture::getGenerator([['a', 1], ['a', 2], ['b', 3], ['b', 4], ['c', 5], ['a', 6], ['c', 7]]),
                fn ($x) => $x[0],
                [
                    'a' => [['a', 1], ['a', 2], ['a', 6]],
                    'b' => [['b', 3], ['b', 4]],
                    'c' => [['c', 5], ['c', 7]],
                ],
            ],
        ];
    }

    /**
     * @test         groupBy iterator
     * @dataProvider dataProviderForIterator
     * @param        \Iterator $iterator
     * @param        callable  $groupKeyFunction
     * @param        array     $expected
     */
    public function testIterator(\Iterator $iterator, callable $groupKeyFunction, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::groupBy($iterator, $groupKeyFunction) as $groupKey => $groupData) {
            $result[$groupKey] = $groupData;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public function dataProviderForIterator(): array
    {
        return [
            [
                new Fixture\ArrayIteratorFixture([['a', 1], ['a', 2], ['b', 3], ['b', 4], ['c', 5], ['a', 6], ['c', 7]]),
                fn ($x) => $x[0],
                [
                    'a' => [['a', 1], ['a', 2], ['a', 6]],
                    'b' => [['b', 3], ['b', 4]],
                    'c' => [['c', 5], ['c', 7]],
                ],
            ],
        ];
    }

    /**
     * @test         groupBy Traversable
     * @dataProvider dataProviderForTraversable
     * @param        \Traversable $traversable
     * @param        callable     $groupKeyFunction
     * @param        array        $expected
     */
    public function testTraversable(\Traversable $traversable, callable $groupKeyFunction, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::groupBy($traversable, $groupKeyFunction) as $groupKey => $groupData) {
            $result[$groupKey] = $groupData;
        }

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public function dataProviderForTraversable(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([['a', 1], ['a', 2], ['b', 3], ['b', 4], ['c', 5], ['a', 6], ['c', 7]]),
                fn ($x) => $x[0],
                [
                    'a' => [['a', 1], ['a', 2], ['a', 6]],
                    'b' => [['b', 3], ['b', 4]],
                    'c' => [['c', 5], ['c', 7]],
                ],
            ],
        ];
    }

    /**
     * @test         groupBy iterator_to_array
     * @dataProvider dataProviderForArray
     * @param        array    $iterable
     * @param        callable $groupKeyFunction
     * @param        array    $expected
     */
    public function testIteratorToArray(array $iterable, callable $groupKeyFunction, array $expected): void
    {
        // Given
        $iterator = Single::groupBy($iterable, $groupKeyFunction);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertEqualsCanonicalizing($expected, $result);
    }
}

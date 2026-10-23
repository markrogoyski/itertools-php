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
     * @param array $iterable
     * @param callable $groupKeyFunction
     * @param callable|null $itemKeyFunction
     * @param array $expected
     */
    public function testArray(array $iterable, callable $groupKeyFunction, ?callable $itemKeyFunction, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::groupBy($iterable, $groupKeyFunction, $itemKeyFunction) as $groupKey => $groupData) {
            $result[$groupKey] = $groupData;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [
                [['a', 1], ['a', 2], ['b', 3], ['b', 4], ['c', 5], ['a', 6], ['c', 7]],
                fn ($x) => $x[0],
                null,
                [
                    'a' => [['a', 1], ['a', 2], ['a', 6]],
                    'b' => [['b', 3], ['b', 4]],
                    'c' => [['c', 5], ['c', 7]],
                ],
            ],
            [
                [['a', 1], ['a', 2], ['b', 3], ['b', 4], ['c', 5], ['a', 6], ['c', 7]],
                fn ($x) => $x[1],
                null,
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
                null,
                [
                    'a' => [[1, 'a'], [2, 'a'], [6, 'a']],
                    'b' => [[3, 'b'], [4, 'b']],
                    'c' => [[5, 'c'], [7, 'c']],
                ],
            ],
            [
                [[1, 'a'], [2, 'a'], [3, 'b'], [4, 'b'], [5, 'c'], [6, 'a'], [7, 'c']],
                fn ($x) => $x[0],
                null,
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
                null,
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
                null,
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
                null,
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
            [
                [
                    ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ['name' => 'Anonymous', 'interests' => []],
                ],
                fn ($x) => $x['interests'],
                null,
                [
                    'programming' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'books' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'slacking' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'music' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'math' => [
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                    'fantasy' => [
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'wine' => [
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                ],
            ],
            [
                [
                    ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ['name' => 'Anonymous', 'interests' => []],
                ],
                fn ($x) => $x['interests'],
                fn ($x) => $x['name'],
                [
                    'programming' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        'Alice' => ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'books' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'slacking' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'music' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        'Alice' => ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'math' => [
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                    'fantasy' => [
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        'Alice' => ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'wine' => [
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                ],
            ],
            [
                [
                    ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                    ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                ],
                fn ($x) => $x['interests'],
                null,
                [
                    'programming' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                    ],
                    'books' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                    ],
                    'music' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                    ],
                ],
            ],
            [
                [
                    ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                    ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                ],
                fn ($x) => $x['interests'],
                fn ($x) => $x['name'],
                [
                    'programming' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                    ],
                    'books' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        'Laura' => ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                    ],
                    'music' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        'Laura' => ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                    ],
                ],
            ],
        ];
    }

    /**
     * @test         groupBy generator
     * @dataProvider dataProviderForGenerator
     * @param \Generator $generator
     * @param callable $groupKeyFunction
     * @param callable|null $itemKeyFunction
     * @param array $expected
     */
    public function testGenerator(\Generator $generator, callable $groupKeyFunction, ?callable $itemKeyFunction, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::groupBy($generator, $groupKeyFunction, $itemKeyFunction) as $groupKey => $groupData) {
            $result[$groupKey] = $groupData;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForGenerator(): array
    {
        return [
            [
                Fixture\GeneratorFixture::getGenerator([['a', 1], ['a', 2], ['b', 3], ['b', 4], ['c', 5], ['a', 6], ['c', 7]]),
                fn ($x) => $x[0],
                null,
                [
                    'a' => [['a', 1], ['a', 2], ['a', 6]],
                    'b' => [['b', 3], ['b', 4]],
                    'c' => [['c', 5], ['c', 7]],
                ],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([
                    ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ['name' => 'Anonymous', 'interests' => []],
                ]),
                fn ($x) => $x['interests'],
                null,
                [
                    'programming' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'books' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'slacking' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'music' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'math' => [
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                    'fantasy' => [
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'wine' => [
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                ],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([
                    ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ['name' => 'Anonymous', 'interests' => []],
                ]),
                fn ($x) => $x['interests'],
                fn ($x) => $x['name'],
                [
                    'programming' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        'Alice' => ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'books' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'slacking' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'music' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        'Alice' => ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'math' => [
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                    'fantasy' => [
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        'Alice' => ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'wine' => [
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                ],
                [
                    Fixture\GeneratorFixture::getGenerator([
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                    ]),
                    fn ($x) => $x['interests'],
                    null,
                    [
                        'programming' => [
                            ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        ],
                        'books' => [
                            ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                            ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                        ],
                        'music' => [
                            ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                            ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                        ],
                    ],
                ],
                [
                    Fixture\GeneratorFixture::getGenerator([
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                    ]),
                    fn ($x) => $x['interests'],
                    fn ($x) => $x['name'],
                    [
                        'programming' => [
                            'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        ],
                        'books' => [
                            'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                            'Laura' => ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                        ],
                        'music' => [
                            'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                            'Laura' => ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @test         groupBy iterator
     * @dataProvider dataProviderForIterator
     * @param \Iterator $iterator
     * @param callable $groupKeyFunction
     * @param callable|null $itemKeyFunction
     * @param array $expected
     */
    public function testIterator(\Iterator $iterator, callable $groupKeyFunction, ?callable $itemKeyFunction, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::groupBy($iterator, $groupKeyFunction, $itemKeyFunction) as $groupKey => $groupData) {
            $result[$groupKey] = $groupData;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForIterator(): array
    {
        return [
            [
                new Fixture\ArrayIteratorFixture([['a', 1], ['a', 2], ['b', 3], ['b', 4], ['c', 5], ['a', 6], ['c', 7]]),
                fn ($x) => $x[0],
                null,
                [
                    'a' => [['a', 1], ['a', 2], ['a', 6]],
                    'b' => [['b', 3], ['b', 4]],
                    'c' => [['c', 5], ['c', 7]],
                ],
            ],
            [
                new Fixture\ArrayIteratorFixture([
                    ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ['name' => 'Anonymous', 'interests' => []],
                ]),
                fn ($x) => $x['interests'],
                null,
                [
                    'programming' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'books' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'slacking' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'music' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'math' => [
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                    'fantasy' => [
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'wine' => [
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                ],
            ],
            [
                new Fixture\ArrayIteratorFixture([
                    ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ['name' => 'Anonymous', 'interests' => []],
                ]),
                fn ($x) => $x['interests'],
                fn ($x) => $x['name'],
                [
                    'programming' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        'Alice' => ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'books' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'slacking' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'music' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        'Alice' => ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'math' => [
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                    'fantasy' => [
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        'Alice' => ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'wine' => [
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                ],
                [
                    new Fixture\ArrayIteratorFixture([
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                    ]),
                    fn ($x) => $x['interests'],
                    null,
                    [
                        'programming' => [
                            ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        ],
                        'books' => [
                            ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                            ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                        ],
                        'music' => [
                            ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                            ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                        ],
                    ],
                ],
                [
                    new Fixture\ArrayIteratorFixture([
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                    ]),
                    fn ($x) => $x['interests'],
                    fn ($x) => $x['name'],
                    [
                        'programming' => [
                            'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        ],
                        'books' => [
                            'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                            'Laura' => ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                        ],
                        'music' => [
                            'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                            'Laura' => ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @test         groupBy Traversable
     * @dataProvider dataProviderForTraversable
     * @param \Traversable $traversable
     * @param callable $groupKeyFunction
     * @param callable|null $itemKeyFunction
     * @param array $expected
     */
    public function testTraversable(\Traversable $traversable, callable $groupKeyFunction, ?callable $itemKeyFunction, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::groupBy($traversable, $groupKeyFunction, $itemKeyFunction) as $groupKey => $groupData) {
            $result[$groupKey] = $groupData;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public static function dataProviderForTraversable(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([['a', 1], ['a', 2], ['b', 3], ['b', 4], ['c', 5], ['a', 6], ['c', 7]]),
                fn ($x) => $x[0],
                null,
                [
                    'a' => [['a', 1], ['a', 2], ['a', 6]],
                    'b' => [['b', 3], ['b', 4]],
                    'c' => [['c', 5], ['c', 7]],
                ],
            ],
            [
                new Fixture\IteratorAggregateFixture([
                    ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ['name' => 'Anonymous', 'interests' => []],
                ]),
                fn ($x) => $x['interests'],
                null,
                [
                    'programming' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'books' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'slacking' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'music' => [
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'math' => [
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                    'fantasy' => [
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'wine' => [
                        ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                ],
            ],
            [
                new Fixture\IteratorAggregateFixture([
                    ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ['name' => 'Anonymous', 'interests' => []],
                ]),
                fn ($x) => $x['interests'],
                fn ($x) => $x['name'],
                [
                    'programming' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        'Alice' => ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'books' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'slacking' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                    ],
                    'music' => [
                        'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'slacking', 'music']],
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        'Alice' => ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'math' => [
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                    'fantasy' => [
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                        'Alice' => ['name' => 'Alice', 'interests' => ['music', 'programming', 'fantasy']],
                    ],
                    'wine' => [
                        'Laura' => ['name' => 'Laura', 'interests' => ['math', 'fantasy', 'wine', 'music']],
                    ],
                ],
                [
                    new Fixture\IteratorAggregateFixture([
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                    ]),
                    fn ($x) => $x['interests'],
                    null,
                    [
                        'programming' => [
                            ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        ],
                        'books' => [
                            ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                            ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                        ],
                        'music' => [
                            ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                            ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                        ],
                    ],
                ],
                [
                    new Fixture\IteratorAggregateFixture([
                        ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                    ]),
                    fn ($x) => $x['interests'],
                    fn ($x) => $x['name'],
                    [
                        'programming' => [
                            'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                        ],
                        'books' => [
                            'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                            'Laura' => ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                        ],
                        'music' => [
                            'Sam' => ['name' => 'Sam', 'interests' => ['programming', 'books', 'music']],
                            'Laura' => ['name' => 'Laura', 'interests' => ['books', 'music', 'music']],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @test         groupBy iterator_to_array
     * @dataProvider dataProviderForArray
     * @param array $iterable
     * @param callable $groupKeyFunction
     * @param callable|null $itemKeyFunction
     * @param array $expected
     */
    public function testIteratorToArray(array $iterable, callable $groupKeyFunction, ?callable $itemKeyFunction, array $expected): void
    {
        // Given
        $iterator = Single::groupBy($iterable, $groupKeyFunction, $itemKeyFunction);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         groupBy adds an item to a group once when its group names normalize to the same array key
     * @dataProvider dataProviderForEquivalentGroupNamesArray
     * @param array $iterable
     * @param callable $groupKeyFunction
     * @param array $expected
     */
    public function testEquivalentGroupNamesArray(array $iterable, callable $groupKeyFunction, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::groupBy($iterable, $groupKeyFunction));

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForEquivalentGroupNamesArray(): array
    {
        return [
            [
                [['x', [2020, '2020']]],
                fn ($x) => $x[1],
                [2020 => [['x', [2020, '2020']]]],
            ],
            [
                [['x', [1, true, '1']]],
                fn ($x) => $x[1],
                [1 => [['x', [1, true, '1']]]],
            ],
            [
                [['x', ['a', 2020, 'b', '2020']], ['y', ['2020', 'b']]],
                fn ($x) => $x[1],
                [
                    'a' => [['x', ['a', 2020, 'b', '2020']]],
                    2020 => [['x', ['a', 2020, 'b', '2020']], ['y', ['2020', 'b']]],
                    'b' => [['x', ['a', 2020, 'b', '2020']], ['y', ['2020', 'b']]],
                ],
            ],
            [
                [['x', [1, '1.0']], ['y', ['1.0']]],
                fn ($x) => $x[1],
                [
                    1 => [['x', [1, '1.0']]],
                    '1.0' => [['x', [1, '1.0']], ['y', ['1.0']]],
                ],
            ],
            [
                [['x', ['', false]], ['y', [false]]],
                fn ($x) => $x[1],
                [
                    '' => [['x', ['', false]]],
                    0 => [['x', ['', false]], ['y', [false]]],
                ],
            ],
        ];
    }

    /**
     * @test         groupBy generator adds an item to a group once when its group names normalize to the same array key
     * @dataProvider dataProviderForEquivalentGroupNamesGenerator
     * @param \Generator $iterable
     * @param callable $groupKeyFunction
     * @param array $expected
     */
    public function testEquivalentGroupNamesGenerator(\Generator $iterable, callable $groupKeyFunction, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::groupBy($iterable, $groupKeyFunction));

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForEquivalentGroupNamesGenerator(): array
    {
        return [
            [
                Fixture\GeneratorFixture::getGenerator([['x', [2020, '2020']]]),
                fn ($x) => $x[1],
                [2020 => [['x', [2020, '2020']]]],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([['x', [1, true, '1']]]),
                fn ($x) => $x[1],
                [1 => [['x', [1, true, '1']]]],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([['x', ['a', 2020, 'b', '2020']], ['y', ['2020', 'b']]]),
                fn ($x) => $x[1],
                [
                    'a' => [['x', ['a', 2020, 'b', '2020']]],
                    2020 => [['x', ['a', 2020, 'b', '2020']], ['y', ['2020', 'b']]],
                    'b' => [['x', ['a', 2020, 'b', '2020']], ['y', ['2020', 'b']]],
                ],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([['x', [1, '1.0']], ['y', ['1.0']]]),
                fn ($x) => $x[1],
                [
                    1 => [['x', [1, '1.0']]],
                    '1.0' => [['x', [1, '1.0']], ['y', ['1.0']]],
                ],
            ],
            [
                Fixture\GeneratorFixture::getGenerator([['x', ['', false]], ['y', [false]]]),
                fn ($x) => $x[1],
                [
                    '' => [['x', ['', false]]],
                    0 => [['x', ['', false]], ['y', [false]]],
                ],
            ],
        ];
    }

    /**
     * @test         groupBy iterator adds an item to a group once when its group names normalize to the same array key
     * @dataProvider dataProviderForEquivalentGroupNamesIterator
     * @param \Iterator $iterable
     * @param callable $groupKeyFunction
     * @param array $expected
     */
    public function testEquivalentGroupNamesIterator(\Iterator $iterable, callable $groupKeyFunction, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::groupBy($iterable, $groupKeyFunction));

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForEquivalentGroupNamesIterator(): array
    {
        return [
            [
                new Fixture\ArrayIteratorFixture([['x', [2020, '2020']]]),
                fn ($x) => $x[1],
                [2020 => [['x', [2020, '2020']]]],
            ],
            [
                new Fixture\ArrayIteratorFixture([['x', [1, true, '1']]]),
                fn ($x) => $x[1],
                [1 => [['x', [1, true, '1']]]],
            ],
            [
                new Fixture\ArrayIteratorFixture([['x', ['a', 2020, 'b', '2020']], ['y', ['2020', 'b']]]),
                fn ($x) => $x[1],
                [
                    'a' => [['x', ['a', 2020, 'b', '2020']]],
                    2020 => [['x', ['a', 2020, 'b', '2020']], ['y', ['2020', 'b']]],
                    'b' => [['x', ['a', 2020, 'b', '2020']], ['y', ['2020', 'b']]],
                ],
            ],
            [
                new Fixture\ArrayIteratorFixture([['x', [1, '1.0']], ['y', ['1.0']]]),
                fn ($x) => $x[1],
                [
                    1 => [['x', [1, '1.0']]],
                    '1.0' => [['x', [1, '1.0']], ['y', ['1.0']]],
                ],
            ],
            [
                new Fixture\ArrayIteratorFixture([['x', ['', false]], ['y', [false]]]),
                fn ($x) => $x[1],
                [
                    '' => [['x', ['', false]]],
                    0 => [['x', ['', false]], ['y', [false]]],
                ],
            ],
        ];
    }

    /**
     * @test         groupBy traversable adds an item to a group once when its group names normalize to the same array key
     * @dataProvider dataProviderForEquivalentGroupNamesTraversable
     * @param \Traversable $iterable
     * @param callable $groupKeyFunction
     * @param array $expected
     */
    public function testEquivalentGroupNamesTraversable(\Traversable $iterable, callable $groupKeyFunction, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::groupBy($iterable, $groupKeyFunction));

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForEquivalentGroupNamesTraversable(): array
    {
        return [
            [
                new Fixture\IteratorAggregateFixture([['x', [2020, '2020']]]),
                fn ($x) => $x[1],
                [2020 => [['x', [2020, '2020']]]],
            ],
            [
                new Fixture\IteratorAggregateFixture([['x', [1, true, '1']]]),
                fn ($x) => $x[1],
                [1 => [['x', [1, true, '1']]]],
            ],
            [
                new Fixture\IteratorAggregateFixture([['x', ['a', 2020, 'b', '2020']], ['y', ['2020', 'b']]]),
                fn ($x) => $x[1],
                [
                    'a' => [['x', ['a', 2020, 'b', '2020']]],
                    2020 => [['x', ['a', 2020, 'b', '2020']], ['y', ['2020', 'b']]],
                    'b' => [['x', ['a', 2020, 'b', '2020']], ['y', ['2020', 'b']]],
                ],
            ],
            [
                new Fixture\IteratorAggregateFixture([['x', [1, '1.0']], ['y', ['1.0']]]),
                fn ($x) => $x[1],
                [
                    1 => [['x', [1, '1.0']]],
                    '1.0' => [['x', [1, '1.0']], ['y', ['1.0']]],
                ],
            ],
            [
                new Fixture\IteratorAggregateFixture([['x', ['', false]], ['y', [false]]]),
                fn ($x) => $x[1],
                [
                    '' => [['x', ['', false]]],
                    0 => [['x', ['', false]], ['y', [false]]],
                ],
            ],
        ];
    }

    /**
     * @test         groupBy array throws TypeError for a group name that isn't int|string|bool
     * @dataProvider dataProviderForInvalidGroupNamesArray
     * @param        array $iterable
     * @param        callable $groupKeyFunction
     * @param        string $expectedType
     */
    public function testInvalidGroupNamesArray(array $iterable, callable $groupKeyFunction, string $expectedType): void
    {
        // Given
        $groups = Single::groupBy($iterable, $groupKeyFunction);

        // Then
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(\sprintf('Group name must be int|string|bool, got %s', $expectedType));

        // When
        \iterator_to_array($groups);
    }

    public static function dataProviderForInvalidGroupNamesArray(): array
    {
        return [
            'float' => [
                [1, 2, 3],
                fn ($x) => 4.5,
                'double',
            ],
            'integral float' => [
                [1, 2, 3],
                fn ($x) => 4.0,
                'double',
            ],
            'null' => [
                [1, 2, 3],
                fn ($x) => null,
                'NULL',
            ],
            'object' => [
                [1, 2, 3],
                fn ($x) => (object)['v' => $x],
                'object',
            ],
            'float in a list' => [
                [1, 2, 3],
                fn ($x) => ['a', 2.5],
                'double',
            ],
            'integral float in a list' => [
                [1, 2, 3],
                fn ($x) => [1, 4.0],
                'double',
            ],
            'null in a list' => [
                [1, 2, 3],
                fn ($x) => ['a', null],
                'NULL',
            ],
            'array in a list' => [
                [1, 2, 3],
                fn ($x) => ['a', [$x]],
                'array',
            ],
            'object in a list' => [
                [1, 2, 3],
                fn ($x) => ['a', (object)['v' => $x]],
                'object',
            ],
            'float for a later item only' => [
                [1, 2, 3],
                fn ($x) => $x === 1 ? 'a' : 4.5,
                'double',
            ],
        ];
    }

    /**
     * @test         groupBy array accepts int, string and bool group names, alone or in a list
     * @dataProvider dataProviderForValidGroupNamesArray
     * @param        array $iterable
     * @param        callable $groupKeyFunction
     * @param        array $expected
     */
    public function testValidGroupNamesArray(array $iterable, callable $groupKeyFunction, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::groupBy($iterable, $groupKeyFunction));

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForValidGroupNamesArray(): array
    {
        return [
            'int' => [
                [1, 2, 3],
                fn ($x) => $x % 2,
                [1 => [1, 3], 0 => [2]],
            ],
            'string' => [
                [1, 2, 3],
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd' => [1, 3], 'even' => [2]],
            ],
            'bool' => [
                [1, 2, 3],
                fn ($x) => $x > 1,
                [0 => [1], 1 => [2, 3]],
            ],
            'list of int, string and bool' => [
                [1, 2, 3],
                fn ($x) => [$x, 'all', $x > 2],
                [1 => [1, 3], 'all' => [1, 2, 3], 0 => [1, 2], 2 => [2], 3 => [3]],
            ],
        ];
    }

    /**
     * @test         groupBy generator throws TypeError for a group name that isn't int|string|bool
     * @dataProvider dataProviderForInvalidGroupNamesGenerator
     * @param        \Generator $iterable
     * @param        callable $groupKeyFunction
     * @param        string $expectedType
     */
    public function testInvalidGroupNamesGenerator(\Generator $iterable, callable $groupKeyFunction, string $expectedType): void
    {
        // Given
        $groups = Single::groupBy($iterable, $groupKeyFunction);

        // Then
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(\sprintf('Group name must be int|string|bool, got %s', $expectedType));

        // When
        \iterator_to_array($groups);
    }

    public static function dataProviderForInvalidGroupNamesGenerator(): array
    {
        return [
            'float' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => 4.5,
                'double',
            ],
            'integral float' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => 4.0,
                'double',
            ],
            'null' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => null,
                'NULL',
            ],
            'object' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => (object)['v' => $x],
                'object',
            ],
            'float in a list' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => ['a', 2.5],
                'double',
            ],
            'integral float in a list' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => [1, 4.0],
                'double',
            ],
            'null in a list' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => ['a', null],
                'NULL',
            ],
            'array in a list' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => ['a', [$x]],
                'array',
            ],
            'object in a list' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => ['a', (object)['v' => $x]],
                'object',
            ],
            'float for a later item only' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => $x === 1 ? 'a' : 4.5,
                'double',
            ],
        ];
    }

    /**
     * @test         groupBy generator accepts int, string and bool group names, alone or in a list
     * @dataProvider dataProviderForValidGroupNamesGenerator
     * @param        \Generator $iterable
     * @param        callable $groupKeyFunction
     * @param        array $expected
     */
    public function testValidGroupNamesGenerator(\Generator $iterable, callable $groupKeyFunction, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::groupBy($iterable, $groupKeyFunction));

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForValidGroupNamesGenerator(): array
    {
        return [
            'int' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => $x % 2,
                [1 => [1, 3], 0 => [2]],
            ],
            'string' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd' => [1, 3], 'even' => [2]],
            ],
            'bool' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => $x > 1,
                [0 => [1], 1 => [2, 3]],
            ],
            'list of int, string and bool' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => [$x, 'all', $x > 2],
                [1 => [1, 3], 'all' => [1, 2, 3], 0 => [1, 2], 2 => [2], 3 => [3]],
            ],
        ];
    }

    /**
     * @test         groupBy iterator throws TypeError for a group name that isn't int|string|bool
     * @dataProvider dataProviderForInvalidGroupNamesIterator
     * @param        \Iterator $iterable
     * @param        callable $groupKeyFunction
     * @param        string $expectedType
     */
    public function testInvalidGroupNamesIterator(\Iterator $iterable, callable $groupKeyFunction, string $expectedType): void
    {
        // Given
        $groups = Single::groupBy($iterable, $groupKeyFunction);

        // Then
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(\sprintf('Group name must be int|string|bool, got %s', $expectedType));

        // When
        \iterator_to_array($groups);
    }

    public static function dataProviderForInvalidGroupNamesIterator(): array
    {
        return [
            'float' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => 4.5,
                'double',
            ],
            'integral float' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => 4.0,
                'double',
            ],
            'null' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => null,
                'NULL',
            ],
            'object' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => (object)['v' => $x],
                'object',
            ],
            'float in a list' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => ['a', 2.5],
                'double',
            ],
            'integral float in a list' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => [1, 4.0],
                'double',
            ],
            'null in a list' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => ['a', null],
                'NULL',
            ],
            'array in a list' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => ['a', [$x]],
                'array',
            ],
            'object in a list' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => ['a', (object)['v' => $x]],
                'object',
            ],
            'float for a later item only' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => $x === 1 ? 'a' : 4.5,
                'double',
            ],
        ];
    }

    /**
     * @test         groupBy iterator accepts int, string and bool group names, alone or in a list
     * @dataProvider dataProviderForValidGroupNamesIterator
     * @param        \Iterator $iterable
     * @param        callable $groupKeyFunction
     * @param        array $expected
     */
    public function testValidGroupNamesIterator(\Iterator $iterable, callable $groupKeyFunction, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::groupBy($iterable, $groupKeyFunction));

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForValidGroupNamesIterator(): array
    {
        return [
            'int' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => $x % 2,
                [1 => [1, 3], 0 => [2]],
            ],
            'string' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd' => [1, 3], 'even' => [2]],
            ],
            'bool' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => $x > 1,
                [0 => [1], 1 => [2, 3]],
            ],
            'list of int, string and bool' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => [$x, 'all', $x > 2],
                [1 => [1, 3], 'all' => [1, 2, 3], 0 => [1, 2], 2 => [2], 3 => [3]],
            ],
        ];
    }

    /**
     * @test         groupBy traversable throws TypeError for a group name that isn't int|string|bool
     * @dataProvider dataProviderForInvalidGroupNamesTraversable
     * @param        \Traversable $iterable
     * @param        callable $groupKeyFunction
     * @param        string $expectedType
     */
    public function testInvalidGroupNamesTraversable(\Traversable $iterable, callable $groupKeyFunction, string $expectedType): void
    {
        // Given
        $groups = Single::groupBy($iterable, $groupKeyFunction);

        // Then
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(\sprintf('Group name must be int|string|bool, got %s', $expectedType));

        // When
        \iterator_to_array($groups);
    }

    public static function dataProviderForInvalidGroupNamesTraversable(): array
    {
        return [
            'float' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => 4.5,
                'double',
            ],
            'integral float' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => 4.0,
                'double',
            ],
            'null' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => null,
                'NULL',
            ],
            'object' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => (object)['v' => $x],
                'object',
            ],
            'float in a list' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => ['a', 2.5],
                'double',
            ],
            'integral float in a list' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => [1, 4.0],
                'double',
            ],
            'null in a list' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => ['a', null],
                'NULL',
            ],
            'array in a list' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => ['a', [$x]],
                'array',
            ],
            'object in a list' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => ['a', (object)['v' => $x]],
                'object',
            ],
            'float for a later item only' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => $x === 1 ? 'a' : 4.5,
                'double',
            ],
        ];
    }

    /**
     * @test         groupBy traversable accepts int, string and bool group names, alone or in a list
     * @dataProvider dataProviderForValidGroupNamesTraversable
     * @param        \Traversable $iterable
     * @param        callable $groupKeyFunction
     * @param        array $expected
     */
    public function testValidGroupNamesTraversable(\Traversable $iterable, callable $groupKeyFunction, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::groupBy($iterable, $groupKeyFunction));

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForValidGroupNamesTraversable(): array
    {
        return [
            'int' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => $x % 2,
                [1 => [1, 3], 0 => [2]],
            ],
            'string' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => $x % 2 === 0 ? 'even' : 'odd',
                ['odd' => [1, 3], 'even' => [2]],
            ],
            'bool' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => $x > 1,
                [0 => [1], 1 => [2, 3]],
            ],
            'list of int, string and bool' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => [$x, 'all', $x > 2],
                [1 => [1, 3], 'all' => [1, 2, 3], 0 => [1, 2], 2 => [2], 3 => [3]],
            ],
        ];
    }

    /**
     * @test         groupBy array throws TypeError for an item key that isn't int|string|bool|null
     * @dataProvider dataProviderForInvalidItemKeysArray
     * @param        array $iterable
     * @param        callable $itemKeyFunction
     * @param        string $expectedType
     */
    public function testInvalidItemKeysArray(array $iterable, callable $itemKeyFunction, string $expectedType): void
    {
        // Given
        $groups = Single::groupBy($iterable, fn ($x) => 'g', $itemKeyFunction);

        // Then
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(\sprintf('Item key must be int|string|bool|null, got %s', $expectedType));

        // When
        \iterator_to_array($groups);
    }

    public static function dataProviderForInvalidItemKeysArray(): array
    {
        return [
            'float' => [
                [1, 2, 3],
                fn ($x) => 4.5,
                'double',
            ],
            'integral float' => [
                [1, 2, 3],
                fn ($x) => 4.0,
                'double',
            ],
            'array' => [
                [1, 2, 3],
                fn ($x) => [$x],
                'array',
            ],
            'object' => [
                [1, 2, 3],
                fn ($x) => (object)['v' => $x],
                'object',
            ],
            'float for a later item only' => [
                [1, 2, 3],
                fn ($x) => $x === 1 ? 'a' : 4.5,
                'double',
            ],
        ];
    }

    /**
     * @test         groupBy array accepts int, string, bool and null item keys
     * @dataProvider dataProviderForValidItemKeysArray
     * @param        array $iterable
     * @param        callable $itemKeyFunction
     * @param        array $expected
     */
    public function testValidItemKeysArray(array $iterable, callable $itemKeyFunction, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::groupBy($iterable, fn ($x) => 'g', $itemKeyFunction));

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForValidItemKeysArray(): array
    {
        return [
            'int' => [
                [1, 2, 3],
                fn ($x) => $x * 10,
                ['g' => [10 => 1, 20 => 2, 30 => 3]],
            ],
            'string' => [
                [1, 2, 3],
                fn ($x) => "k{$x}",
                ['g' => ['k1' => 1, 'k2' => 2, 'k3' => 3]],
            ],
            'bool, becoming the key 0 or 1' => [
                [1, 2, 3],
                fn ($x) => $x > 1,
                ['g' => [0 => 1, 1 => 3]],
            ],
            'null appends the item' => [
                [1, 2, 3],
                fn ($x) => $x === 2 ? null : "k{$x}",
                ['g' => ['k1' => 1, 0 => 2, 'k3' => 3]],
            ],
        ];
    }

    /**
     * @test         groupBy generator throws TypeError for an item key that isn't int|string|bool|null
     * @dataProvider dataProviderForInvalidItemKeysGenerator
     * @param        \Generator $iterable
     * @param        callable $itemKeyFunction
     * @param        string $expectedType
     */
    public function testInvalidItemKeysGenerator(\Generator $iterable, callable $itemKeyFunction, string $expectedType): void
    {
        // Given
        $groups = Single::groupBy($iterable, fn ($x) => 'g', $itemKeyFunction);

        // Then
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(\sprintf('Item key must be int|string|bool|null, got %s', $expectedType));

        // When
        \iterator_to_array($groups);
    }

    public static function dataProviderForInvalidItemKeysGenerator(): array
    {
        return [
            'float' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => 4.5,
                'double',
            ],
            'integral float' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => 4.0,
                'double',
            ],
            'array' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => [$x],
                'array',
            ],
            'object' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => (object)['v' => $x],
                'object',
            ],
            'float for a later item only' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => $x === 1 ? 'a' : 4.5,
                'double',
            ],
        ];
    }

    /**
     * @test         groupBy generator accepts int, string, bool and null item keys
     * @dataProvider dataProviderForValidItemKeysGenerator
     * @param        \Generator $iterable
     * @param        callable $itemKeyFunction
     * @param        array $expected
     */
    public function testValidItemKeysGenerator(\Generator $iterable, callable $itemKeyFunction, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::groupBy($iterable, fn ($x) => 'g', $itemKeyFunction));

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForValidItemKeysGenerator(): array
    {
        return [
            'int' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => $x * 10,
                ['g' => [10 => 1, 20 => 2, 30 => 3]],
            ],
            'string' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => "k{$x}",
                ['g' => ['k1' => 1, 'k2' => 2, 'k3' => 3]],
            ],
            'bool, becoming the key 0 or 1' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => $x > 1,
                ['g' => [0 => 1, 1 => 3]],
            ],
            'null appends the item' => [
                Fixture\GeneratorFixture::getGenerator([1, 2, 3]),
                fn ($x) => $x === 2 ? null : "k{$x}",
                ['g' => ['k1' => 1, 0 => 2, 'k3' => 3]],
            ],
        ];
    }

    /**
     * @test         groupBy iterator throws TypeError for an item key that isn't int|string|bool|null
     * @dataProvider dataProviderForInvalidItemKeysIterator
     * @param        \Iterator $iterable
     * @param        callable $itemKeyFunction
     * @param        string $expectedType
     */
    public function testInvalidItemKeysIterator(\Iterator $iterable, callable $itemKeyFunction, string $expectedType): void
    {
        // Given
        $groups = Single::groupBy($iterable, fn ($x) => 'g', $itemKeyFunction);

        // Then
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(\sprintf('Item key must be int|string|bool|null, got %s', $expectedType));

        // When
        \iterator_to_array($groups);
    }

    public static function dataProviderForInvalidItemKeysIterator(): array
    {
        return [
            'float' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => 4.5,
                'double',
            ],
            'integral float' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => 4.0,
                'double',
            ],
            'array' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => [$x],
                'array',
            ],
            'object' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => (object)['v' => $x],
                'object',
            ],
            'float for a later item only' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => $x === 1 ? 'a' : 4.5,
                'double',
            ],
        ];
    }

    /**
     * @test         groupBy iterator accepts int, string, bool and null item keys
     * @dataProvider dataProviderForValidItemKeysIterator
     * @param        \Iterator $iterable
     * @param        callable $itemKeyFunction
     * @param        array $expected
     */
    public function testValidItemKeysIterator(\Iterator $iterable, callable $itemKeyFunction, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::groupBy($iterable, fn ($x) => 'g', $itemKeyFunction));

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForValidItemKeysIterator(): array
    {
        return [
            'int' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => $x * 10,
                ['g' => [10 => 1, 20 => 2, 30 => 3]],
            ],
            'string' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => "k{$x}",
                ['g' => ['k1' => 1, 'k2' => 2, 'k3' => 3]],
            ],
            'bool, becoming the key 0 or 1' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => $x > 1,
                ['g' => [0 => 1, 1 => 3]],
            ],
            'null appends the item' => [
                new Fixture\ArrayIteratorFixture([1, 2, 3]),
                fn ($x) => $x === 2 ? null : "k{$x}",
                ['g' => ['k1' => 1, 0 => 2, 'k3' => 3]],
            ],
        ];
    }

    /**
     * @test         groupBy traversable throws TypeError for an item key that isn't int|string|bool|null
     * @dataProvider dataProviderForInvalidItemKeysTraversable
     * @param        \Traversable $iterable
     * @param        callable $itemKeyFunction
     * @param        string $expectedType
     */
    public function testInvalidItemKeysTraversable(\Traversable $iterable, callable $itemKeyFunction, string $expectedType): void
    {
        // Given
        $groups = Single::groupBy($iterable, fn ($x) => 'g', $itemKeyFunction);

        // Then
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(\sprintf('Item key must be int|string|bool|null, got %s', $expectedType));

        // When
        \iterator_to_array($groups);
    }

    public static function dataProviderForInvalidItemKeysTraversable(): array
    {
        return [
            'float' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => 4.5,
                'double',
            ],
            'integral float' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => 4.0,
                'double',
            ],
            'array' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => [$x],
                'array',
            ],
            'object' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => (object)['v' => $x],
                'object',
            ],
            'float for a later item only' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => $x === 1 ? 'a' : 4.5,
                'double',
            ],
        ];
    }

    /**
     * @test         groupBy traversable accepts int, string, bool and null item keys
     * @dataProvider dataProviderForValidItemKeysTraversable
     * @param        \Traversable $iterable
     * @param        callable $itemKeyFunction
     * @param        array $expected
     */
    public function testValidItemKeysTraversable(\Traversable $iterable, callable $itemKeyFunction, array $expected): void
    {
        // When
        $result = \iterator_to_array(Single::groupBy($iterable, fn ($x) => 'g', $itemKeyFunction));

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForValidItemKeysTraversable(): array
    {
        return [
            'int' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => $x * 10,
                ['g' => [10 => 1, 20 => 2, 30 => 3]],
            ],
            'string' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => "k{$x}",
                ['g' => ['k1' => 1, 'k2' => 2, 'k3' => 3]],
            ],
            'bool, becoming the key 0 or 1' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => $x > 1,
                ['g' => [0 => 1, 1 => 3]],
            ],
            'null appends the item' => [
                new Fixture\IteratorAggregateFixture([1, 2, 3]),
                fn ($x) => $x === 2 ? null : "k{$x}",
                ['g' => ['k1' => 1, 0 => 2, 'k3' => 3]],
            ],
        ];
    }
}

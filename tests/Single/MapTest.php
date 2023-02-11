<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture;
use IterTools\Tests\Fixture\GeneratorFixture;

class MapTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         map array
     * @dataProvider dataProviderForArray
     * @dataProvider dataProviderForGenerator
     * @dataProvider dataProviderForIterator
     * @dataProvider dataProviderForTraversable
     * @param        iterable $iterable
     * @param        callable $mapper
     * @param        array    $expected
     */
    public function testArray(iterable $iterable, callable $mapper, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::map($iterable, $mapper) as $item) {
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
                fn($x) => $x + 1,
                [],
            ],
            [
                [],
                'sqrt',
                [],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn($x) => $x,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                [0, 1, 2, 3, 4, 5],
                fn($x) => $x + 1,
                [1, 2, 3, 4, 5, 6],
            ],
            [
                ["IterToolsPHP", "MathPHP", "SubnetCalculator"],
                fn($x) => $x . " is great!",
                ["IterToolsPHP is great!", "MathPHP is great!", "SubnetCalculator is great!"],
            ],
            [
                [1, 4, 9, 16, 25],
                'sqrt',
                [1, 2, 3, 4, 5],
            ],
            [
                [1, -2, 3, -4, 5],
                'abs',
                [1, 2, 3, 4, 5],
            ],
            [
                ['one', 'Two', 'ThReE', 'FOUR'],
                'strtoupper',
                ['ONE', 'TWO', 'THREE', 'FOUR'],
            ],
        ];
    }

    public function dataProviderForGenerator(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getGenerator($data);

        return [
            [
                $gen([]),
                fn($x) => $x + 1,
                [],
            ],
            [
                $gen([]),
                'sqrt',
                [],
            ],
            [
                $gen([0, 1, 2, 3, 4, 5]),
                fn($x) => $x,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                $gen([0, 1, 2, 3, 4, 5]),
                fn($x) => $x + 1,
                [1, 2, 3, 4, 5, 6],
            ],
            [
                $gen(["IterToolsPHP", "MathPHP", "SubnetCalculator"]),
                fn($x) => $x . " is great!",
                ["IterToolsPHP is great!", "MathPHP is great!", "SubnetCalculator is great!"],
            ],
            [
                $gen([1, 4, 9, 16, 25]),
                'sqrt',
                [1, 2, 3, 4, 5],
            ],
            [
                $gen([1, -2, 3, -4, 5]),
                'abs',
                [1, 2, 3, 4, 5],
            ],
            [
                $gen(['one', 'Two', 'ThReE', 'FOUR']),
                'strtoupper',
                ['ONE', 'TWO', 'THREE', 'FOUR'],
            ],
        ];
    }

    public function dataProviderForIterator(): array
    {
        $iter = fn (array $data) => new Fixture\ArrayIteratorFixture($data);

        return [
            [
                $iter([]),
                fn($x) => $x + 1,
                [],
            ],
            [
                $iter([]),
                'sqrt',
                [],
            ],
            [
                $iter([0, 1, 2, 3, 4, 5]),
                fn($x) => $x,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                $iter([0, 1, 2, 3, 4, 5]),
                fn($x) => $x + 1,
                [1, 2, 3, 4, 5, 6],
            ],
            [
                $iter(["IterToolsPHP", "MathPHP", "SubnetCalculator"]),
                fn($x) => $x . " is great!",
                ["IterToolsPHP is great!", "MathPHP is great!", "SubnetCalculator is great!"],
            ],
            [
                $iter([1, 4, 9, 16, 25]),
                'sqrt',
                [1, 2, 3, 4, 5],
            ],
            [
                $iter([1, -2, 3, -4, 5]),
                'abs',
                [1, 2, 3, 4, 5],
            ],
            [
                $iter(['one', 'Two', 'ThReE', 'FOUR']),
                'strtoupper',
                ['ONE', 'TWO', 'THREE', 'FOUR'],
            ],
        ];
    }

    public function dataProviderForTraversable(): array
    {
        $iter = fn (array $data) => new Fixture\IteratorAggregateFixture($data);

        return [
            [
                $iter([]),
                fn($x) => $x + 1,
                [],
            ],
            [
                $iter([]),
                'sqrt',
                [],
            ],
            [
                $iter([0, 1, 2, 3, 4, 5]),
                fn($x) => $x,
                [0, 1, 2, 3, 4, 5],
            ],
            [
                $iter([0, 1, 2, 3, 4, 5]),
                fn($x) => $x + 1,
                [1, 2, 3, 4, 5, 6],
            ],
            [
                $iter(["IterToolsPHP", "MathPHP", "SubnetCalculator"]),
                fn($x) => $x . " is great!",
                ["IterToolsPHP is great!", "MathPHP is great!", "SubnetCalculator is great!"],
            ],
            [
                $iter([1, 4, 9, 16, 25]),
                'sqrt',
                [1, 2, 3, 4, 5],
            ],
            [
                $iter([1, -2, 3, -4, 5]),
                'abs',
                [1, 2, 3, 4, 5],
            ],
            [
                $iter(['one', 'Two', 'ThReE', 'FOUR']),
                'strtoupper',
                ['ONE', 'TWO', 'THREE', 'FOUR'],
            ],
        ];
    }

    /**
     * @test map with associative array
     */
    public function testAssociativeArray(): void
    {
        // Given
        $iterable = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];
        $func     = fn ($x) => $x**2;

        // And
        $expected = ['a' => 1, 'b' => 4, 'c' => 9, 'd' => 16, 'e' => 25];

        // When
        $result = [];
        foreach (Single::map($iterable, $func) as $key => $item) {
            $result[$key] = $item;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test         map iterator_to_array
     * @dataProvider dataProviderForArray
     * @dataProvider dataProviderForGenerator
     * @dataProvider dataProviderForIterator
     * @dataProvider dataProviderForTraversable
     * @param        iterable $iterable
     * @param        callable $mapper
     * @param        array    $expected
     */
    public function testIteratorToArray(iterable $iterable, callable $mapper, array $expected): void
    {
        // Given
        $iterator = Single::map($iterable, $mapper);

        // When
        $result = \iterator_to_array($iterator);

        // Then
        $this->assertEquals($expected, $result);
    }
}

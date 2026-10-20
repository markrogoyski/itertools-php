<?php

declare(strict_types=1);

namespace IterTools\Tests\Sort;

use IterTools\Sort;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class AsortTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test asort example usage without custom comparator
     */
    public function testSortWithoutCustomComparator(): void
    {
        // Given
        $worldPopulations = [
            'Bangladesh' => 164_689_383,
            'Brazil'     => 212_559_417,
            'China'      => 1_439_323_776,
            'India'      => 1_380_004_385,
            'Indonesia'  => 273_523_615,
            'Mexico'     => 128_932_753,
            'Nigeria'    => 206_139_589,
            'Pakistan'   => 220_892_340,
            'Russia'     => 145_934_462,
            'USA'        => 331_002_651,
        ];

        // And
        $expected = [
            'Mexico'     => 128_932_753,
            'Russia'     => 145_934_462,
            'Bangladesh' => 164_689_383,
            'Nigeria'    => 206_139_589,
            'Brazil'     => 212_559_417,
            'Pakistan'   => 220_892_340,
            'Indonesia'  => 273_523_615,
            'USA'        => 331_002_651,
            'India'      => 1_380_004_385,
            'China'      => 1_439_323_776,
        ];

        // When
        $sortedAscending = [];
        foreach (Sort::asort($worldPopulations) as $country => $population) {
            $sortedAscending[$country] = $population;
        }

        // Then
        $this->assertSame($expected, $sortedAscending);
    }

    /**
     * @test asort example usage using custom comparator
     */
    public function testSortUsingCustomComparator(): void
    {
        // Given
        $worldPopulations = [
            'Bangladesh' => 164_689_383,
            'Brazil'     => 212_559_417,
            'China'      => 1_439_323_776,
            'India'      => 1_380_004_385,
            'Indonesia'  => 273_523_615,
            'Mexico'     => 128_932_753,
            'Nigeria'    => 206_139_589,
            'Pakistan'   => 220_892_340,
            'Russia'     => 145_934_462,
            'USA'        => 331_002_651,
        ];
        $comparator = fn ($lhs, $rhs) => $rhs <=> $lhs;

        // And
        $expected = [
            'China'      => 1_439_323_776,
            'India'      => 1_380_004_385,
            'USA'        => 331_002_651,
            'Indonesia'  => 273_523_615,
            'Pakistan'   => 220_892_340,
            'Brazil'     => 212_559_417,
            'Nigeria'    => 206_139_589,
            'Bangladesh' => 164_689_383,
            'Russia'     => 145_934_462,
            'Mexico'     => 128_932_753,
        ];

        // When
        $sortedDescending = [];
        foreach (Sort::asort($worldPopulations, $comparator) as $country => $population) {
            $sortedDescending[$country] = $population;
        }

        // Then
        $this->assertSame($expected, $sortedDescending);
    }

    /**
     * @dataProvider dataProviderForArray
     * @param array $data
     * @param callable|null $comparator
     * @param array $expected
     */
    public function testArray(array $data, ?callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::asort($data, $comparator) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForArray(): array
    {
        return [
            [
                [],
                null,
                [],
            ],
            [
                [],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                [],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                ['a' => 1],
                null,
                ['a' => 1],
            ],
            [
                ['a' => 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1],
            ],
            [
                [0 => 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [0 => 1],
            ],
            [
                ['a' => 1, 'b' => 1],
                null,
                ['a' => 1, 'b' => 1],
            ],
            [
                ['a' => 1, 'b' => 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1, 'b' => 1],
            ],
            [
                ['a' => 1, 'b' => 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a' => 1, 'b' => 1],
            ],
            [
                ['a' => 1, 'b' => 2],
                null,
                ['a' => 1, 'b' => 2],
            ],
            [
                ['a' => 1, 'b' => 2],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1, 'b' => 2],
            ],
            [
                ['a' => 1, 'b' => 2],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b' => 2, 'a' => 1],
            ],
            [
                ['b' => 2, 'a' => 1],
                null,
                ['a' => 1, 'b' => 2],
            ],
            [
                ['b' => 2, 'a' => 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1, 'b' => 2],
            ],
            [
                ['b' => 2, 'a' => 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b' => 2, 'a' => 1],
            ],
            [
                ['a' => 2, 'b' => 1, 'c' => 1],
                null,
                ['b' => 1, 'c' => 1, 'a' => 2],
            ],
            [
                ['a' => 2, 'b' => 1, 'c' => 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b' => 1, 'c' => 1, 'a' => 2],
            ],
            [
                ['a' => 2, 'b' => 1, 'c' => 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a' => 2, 'b' => 1, 'c' => 1],
            ],
            [
                ['a' => 2, 'b' => 1, 'c' => 3],
                null,
                ['b' => 1, 'a' => 2, 'c' => 3],
            ],
            [
                ['a' => 2, 'b' => 1, 'c' => 3],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b' => 1, 'a' => 2, 'c' => 3],
            ],
            [
                ['a' => 2, 'b' => 1, 'c' => 3],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['c' => 3, 'a' => 2, 'b' => 1],
            ],
            [
                ['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1],
                null,
                ['f' => -6, 'e' => -3, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3, 'd' => 5, 'g' => 10, 'h' => 11],
            ],
            [
                ['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['f' => -6, 'e' => -3, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3, 'd' => 5, 'g' => 10, 'h' => 11],
            ],
            [
                ['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['h' => 11, 'g' => 10, 'd' => 5, 'b' => 3, 'c' => 2, 'a' => 1, 'i' => 1, 'e' => -3, 'f' => -6],
            ],
            [
                ['a' => 1, 'b' => 3.3, 'c' => 2, 'd' => 5, 'e' => -3.1, 'f' => -6, 'g' => '10', 'h' => 11, 'i' => 1],
                null,
                ['f' => -6, 'e' => -3.1, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3.3, 'd' => 5, 'g' => '10', 'h' => 11],
            ],
            [
                ['a' => 1, 'b' => 3.3, 'c' => 2, 'd' => 5, 'e' => -3.1, 'f' => -6, 'g' => '10', 'h' => 11, 'i' => 1],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['f' => -6, 'e' => -3.1, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3.3, 'd' => 5, 'g' => '10', 'h' => 11],
            ],
            [
                ['a' => 1, 'b' => 3.3, 'c' => 2, 'd' => 5, 'e' => -3.1, 'f' => -6, 'g' => '10', 'h' => 11, 'i' => 1],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['h' => 11, 'g' => '10', 'd' => 5, 'b' => 3.3, 'c' => 2, 'a' => 1, 'i' => 1, 'e' => -3.1, 'f' => -6]
            ],
            [
                ['a' => true, 'b' => false, 'c' => false, 'd' => true, 'e' => false],
                null,
                ['b' => false, 'c' => false, 'e' => false, 'a' => true, 'd' => true],
            ],
            [
                ['a' => true, 'b' => false, 'c' => false, 'd' => true, 'e' => false],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b' => false, 'c' => false, 'e' => false, 'a' => true, 'd' => true],
            ],
            [
                ['a' => true, 'b' => false, 'c' => false, 'd' => true, 'e' => false],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a' => true, 'd' => true, 'b' => false, 'c' => false, 'e' => false],
            ],
            [
                ['a' => [1], 'b' => [3], 'c' => [-2], 'd' => [5.1], 'e' => [2, 3], 'f' => [1, 1], 'g' => [0], 'h' => []],
                null,
                ['h' => [], 'c' => [-2], 'g' => [0], 'a' => [1], 'b' => [3], 'd' => [5.1], 'f' => [1, 1], 'e' => [2, 3]],
            ],
            [
                ['a' => [1], 'b' => [3], 'c' => [-2], 'd' => [5.1], 'e' => [2, 3], 'f' => [1, 1], 'g' => [0], 'h' => []],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['h' => [], 'c' => [-2], 'g' => [0], 'a' => [1], 'b' => [3], 'd' => [5.1], 'f' => [1, 1], 'e' => [2, 3]],
            ],
            [
                ['a' => [1], 'b' => [3], 'c' => [-2], 'd' => [5.1], 'e' => [2, 3], 'f' => [1, 1], 'g' => [0], 'h' => []],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['e' => [2, 3], 'f' => [1, 1], 'd' => [5.1], 'b' => [3], 'a' => [1], 'g' => [0], 'c' => [-2], 'h' => []],
            ],
            [
                ['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]],
                null,
                ['e' => [0, 3], 'a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'g' => [2, 5], 'd' => [3, 0], 'f' => [5, 2]],
            ],
            [
                ['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]],
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['e' => [0, 3], 'a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'g' => [2, 5], 'd' => [3, 0], 'f' => [5, 2]],
            ],
            [
                ['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]],
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['f' => [5, 2], 'd' => [3, 0], 'g' => [2, 5], 'c' => [2, 2], 'b' => [2, 1], 'a' => [1, 2], 'e' => [0, 3]],
            ],
            [
                ['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]],
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                ['d' => [3, 0], 'b' => [2, 1], 'a' => [1, 2], 'c' => [2, 2], 'f' => [5, 2], 'e' => [0, 3], 'g' => [2, 5]],
            ],
            [
                ['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]],
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
                ['g' => [2, 5], 'e' => [0, 3], 'a' => [1, 2], 'c' => [2, 2], 'f' => [5, 2], 'b' => [2, 1], 'd' => [3, 0]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForGenerators
     * @param \Generator $data
     * @param callable|null $comparator
     * @param array $expected
     */
    public function testGenerators(\Generator $data, ?callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::asort($data, $comparator) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForGenerators(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            [
               $gen([]),
                null,
                [],
            ],
            [
                $gen([]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $gen([]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $gen(['a' => 1]),
                null,
                ['a' => 1],
            ],
            [
                $gen(['a' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1],
            ],
            [
                $gen([0 => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [0 => 1],
            ],
            [
                $gen(['a' => 1, 'b' => 1]),
                null,
                ['a' => 1, 'b' => 1],
            ],
            [
                $gen(['a' => 1, 'b' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1, 'b' => 1],
            ],
            [
                $gen(['a' => 1, 'b' => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a' => 1, 'b' => 1],
            ],
            [
                $gen(['a' => 1, 'b' => 2]),
                null,
                ['a' => 1, 'b' => 2],
            ],
            [
                $gen(['a' => 1, 'b' => 2]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1, 'b' => 2],
            ],
            [
                $gen(['a' => 1, 'b' => 2]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b' => 2, 'a' => 1],
            ],
            [
                $gen(['b' => 2, 'a' => 1]),
                null,
                ['a' => 1, 'b' => 2],
            ],
            [
                $gen(['b' => 2, 'a' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1, 'b' => 2],
            ],
            [
                $gen(['b' => 2, 'a' => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b' => 2, 'a' => 1],
            ],
            [
                $gen(['a' => 2, 'b' => 1, 'c' => 1]),
                null,
                ['b' => 1, 'c' => 1, 'a' => 2],
            ],
            [
                $gen(['a' => 2, 'b' => 1, 'c' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b' => 1, 'c' => 1, 'a' => 2],
            ],
            [
                $gen(['a' => 2, 'b' => 1, 'c' => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a' => 2, 'b' => 1, 'c' => 1],
            ],
            [
                $gen(['a' => 2, 'b' => 1, 'c' => 3]),
                null,
                ['b' => 1, 'a' => 2, 'c' => 3],
            ],
            [
                $gen(['a' => 2, 'b' => 1, 'c' => 3]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b' => 1, 'a' => 2, 'c' => 3],
            ],
            [
                $gen(['a' => 2, 'b' => 1, 'c' => 3]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['c' => 3, 'a' => 2, 'b' => 1],
            ],
            [
                $gen(['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1]),
                null,
                ['f' => -6, 'e' => -3, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3, 'd' => 5, 'g' => 10, 'h' => 11],
            ],
            [
                $gen(['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['f' => -6, 'e' => -3, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3, 'd' => 5, 'g' => 10, 'h' => 11],
            ],
            [
                $gen(['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['h' => 11, 'g' => 10, 'd' => 5, 'b' => 3, 'c' => 2, 'a' => 1, 'i' => 1, 'e' => -3, 'f' => -6],
            ],
            [
                $gen(['a' => 1, 'b' => 3.3, 'c' => 2, 'd' => 5, 'e' => -3.1, 'f' => -6, 'g' => '10', 'h' => 11, 'i' => 1]),
                null,
                ['f' => -6, 'e' => -3.1, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3.3, 'd' => 5, 'g' => '10', 'h' => 11],
            ],
            [
                $gen(['a' => 1, 'b' => 3.3, 'c' => 2, 'd' => 5, 'e' => -3.1, 'f' => -6, 'g' => '10', 'h' => 11, 'i' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['f' => -6, 'e' => -3.1, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3.3, 'd' => 5, 'g' => '10', 'h' => 11],
            ],
            [
                $gen(['a' => 1, 'b' => 3.3, 'c' => 2, 'd' => 5, 'e' => -3.1, 'f' => -6, 'g' => '10', 'h' => 11, 'i' => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['h' => 11, 'g' => '10', 'd' => 5, 'b' => 3.3, 'c' => 2, 'a' => 1, 'i' => 1, 'e' => -3.1, 'f' => -6]
            ],
            [
                $gen(['a' => true, 'b' => false, 'c' => false, 'd' => true, 'e' => false]),
                null,
                ['b' => false, 'c' => false, 'e' => false, 'a' => true, 'd' => true],
            ],
            [
                $gen(['a' => true, 'b' => false, 'c' => false, 'd' => true, 'e' => false]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b' => false, 'c' => false, 'e' => false, 'a' => true, 'd' => true],
            ],
            [
                $gen(['a' => true, 'b' => false, 'c' => false, 'd' => true, 'e' => false]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a' => true, 'd' => true, 'b' => false, 'c' => false, 'e' => false],
            ],
            [
                $gen(['a' => [1], 'b' => [3], 'c' => [-2], 'd' => [5.1], 'e' => [2, 3], 'f' => [1, 1], 'g' => [0], 'h' => []]),
                null,
                ['h' => [], 'c' => [-2], 'g' => [0], 'a' => [1], 'b' => [3], 'd' => [5.1], 'f' => [1, 1], 'e' => [2, 3]],
            ],
            [
                $gen(['a' => [1], 'b' => [3], 'c' => [-2], 'd' => [5.1], 'e' => [2, 3], 'f' => [1, 1], 'g' => [0], 'h' => []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['h' => [], 'c' => [-2], 'g' => [0], 'a' => [1], 'b' => [3], 'd' => [5.1], 'f' => [1, 1], 'e' => [2, 3]],
            ],
            [
                $gen(['a' => [1], 'b' => [3], 'c' => [-2], 'd' => [5.1], 'e' => [2, 3], 'f' => [1, 1], 'g' => [0], 'h' => []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['e' => [2, 3], 'f' => [1, 1], 'd' => [5.1], 'b' => [3], 'a' => [1], 'g' => [0], 'c' => [-2], 'h' => []],
            ],
            [
                $gen(['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]]),
                null,
                ['e' => [0, 3], 'a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'g' => [2, 5], 'd' => [3, 0], 'f' => [5, 2]],
            ],
            [
                $gen(['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['e' => [0, 3], 'a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'g' => [2, 5], 'd' => [3, 0], 'f' => [5, 2]],
            ],
            [
                $gen(['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['f' => [5, 2], 'd' => [3, 0], 'g' => [2, 5], 'c' => [2, 2], 'b' => [2, 1], 'a' => [1, 2], 'e' => [0, 3]],
            ],
            [
                $gen(['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]]),
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                ['d' => [3, 0], 'b' => [2, 1], 'a' => [1, 2], 'c' => [2, 2], 'f' => [5, 2], 'e' => [0, 3], 'g' => [2, 5]],
            ],
            [
                $gen(['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]]),
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
                ['g' => [2, 5], 'e' => [0, 3], 'a' => [1, 2], 'c' => [2, 2], 'f' => [5, 2], 'b' => [2, 1], 'd' => [3, 0]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForIterators
     * @param \Iterator $data
     * @param callable|null $comparator
     * @param array $expected
     */
    public function testIterators(\Iterator $data, ?callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::asort($data, $comparator) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForIterators(): array
    {
        $iter = fn (array $data) => new \ArrayIterator($data);

        return [
            [
                $iter([]),
                null,
                [],
            ],
            [
                $iter([]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $iter([]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $iter(['a' => 1]),
                null,
                ['a' => 1],
            ],
            [
                $iter(['a' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1],
            ],
            [
                $iter([0 => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [0 => 1],
            ],
            [
                $iter(['a' => 1, 'b' => 1]),
                null,
                ['a' => 1, 'b' => 1],
            ],
            [
                $iter(['a' => 1, 'b' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1, 'b' => 1],
            ],
            [
                $iter(['a' => 1, 'b' => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a' => 1, 'b' => 1],
            ],
            [
                $iter(['a' => 1, 'b' => 2]),
                null,
                ['a' => 1, 'b' => 2],
            ],
            [
                $iter(['a' => 1, 'b' => 2]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1, 'b' => 2],
            ],
            [
                $iter(['a' => 1, 'b' => 2]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b' => 2, 'a' => 1],
            ],
            [
                $iter(['b' => 2, 'a' => 1]),
                null,
                ['a' => 1, 'b' => 2],
            ],
            [
                $iter(['b' => 2, 'a' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1, 'b' => 2],
            ],
            [
                $iter(['b' => 2, 'a' => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b' => 2, 'a' => 1],
            ],
            [
                $iter(['a' => 2, 'b' => 1, 'c' => 1]),
                null,
                ['b' => 1, 'c' => 1, 'a' => 2],
            ],
            [
                $iter(['a' => 2, 'b' => 1, 'c' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b' => 1, 'c' => 1, 'a' => 2],
            ],
            [
                $iter(['a' => 2, 'b' => 1, 'c' => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a' => 2, 'b' => 1, 'c' => 1],
            ],
            [
                $iter(['a' => 2, 'b' => 1, 'c' => 3]),
                null,
                ['b' => 1, 'a' => 2, 'c' => 3],
            ],
            [
                $iter(['a' => 2, 'b' => 1, 'c' => 3]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b' => 1, 'a' => 2, 'c' => 3],
            ],
            [
                $iter(['a' => 2, 'b' => 1, 'c' => 3]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['c' => 3, 'a' => 2, 'b' => 1],
            ],
            [
                $iter(['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1]),
                null,
                ['f' => -6, 'e' => -3, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3, 'd' => 5, 'g' => 10, 'h' => 11],
            ],
            [
                $iter(['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['f' => -6, 'e' => -3, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3, 'd' => 5, 'g' => 10, 'h' => 11],
            ],
            [
                $iter(['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['h' => 11, 'g' => 10, 'd' => 5, 'b' => 3, 'c' => 2, 'a' => 1, 'i' => 1, 'e' => -3, 'f' => -6],
            ],
            [
                $iter(['a' => 1, 'b' => 3.3, 'c' => 2, 'd' => 5, 'e' => -3.1, 'f' => -6, 'g' => '10', 'h' => 11, 'i' => 1]),
                null,
                ['f' => -6, 'e' => -3.1, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3.3, 'd' => 5, 'g' => '10', 'h' => 11],
            ],
            [
                $iter(['a' => 1, 'b' => 3.3, 'c' => 2, 'd' => 5, 'e' => -3.1, 'f' => -6, 'g' => '10', 'h' => 11, 'i' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['f' => -6, 'e' => -3.1, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3.3, 'd' => 5, 'g' => '10', 'h' => 11],
            ],
            [
                $iter(['a' => 1, 'b' => 3.3, 'c' => 2, 'd' => 5, 'e' => -3.1, 'f' => -6, 'g' => '10', 'h' => 11, 'i' => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['h' => 11, 'g' => '10', 'd' => 5, 'b' => 3.3, 'c' => 2, 'a' => 1, 'i' => 1, 'e' => -3.1, 'f' => -6]
            ],
            [
                $iter(['a' => true, 'b' => false, 'c' => false, 'd' => true, 'e' => false]),
                null,
                ['b' => false, 'c' => false, 'e' => false, 'a' => true, 'd' => true],
            ],
            [
                $iter(['a' => true, 'b' => false, 'c' => false, 'd' => true, 'e' => false]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b' => false, 'c' => false, 'e' => false, 'a' => true, 'd' => true],
            ],
            [
                $iter(['a' => true, 'b' => false, 'c' => false, 'd' => true, 'e' => false]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a' => true, 'd' => true, 'b' => false, 'c' => false, 'e' => false],
            ],
            [
                $iter(['a' => [1], 'b' => [3], 'c' => [-2], 'd' => [5.1], 'e' => [2, 3], 'f' => [1, 1], 'g' => [0], 'h' => []]),
                null,
                ['h' => [], 'c' => [-2], 'g' => [0], 'a' => [1], 'b' => [3], 'd' => [5.1], 'f' => [1, 1], 'e' => [2, 3]],
            ],
            [
                $iter(['a' => [1], 'b' => [3], 'c' => [-2], 'd' => [5.1], 'e' => [2, 3], 'f' => [1, 1], 'g' => [0], 'h' => []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['h' => [], 'c' => [-2], 'g' => [0], 'a' => [1], 'b' => [3], 'd' => [5.1], 'f' => [1, 1], 'e' => [2, 3]],
            ],
            [
                $iter(['a' => [1], 'b' => [3], 'c' => [-2], 'd' => [5.1], 'e' => [2, 3], 'f' => [1, 1], 'g' => [0], 'h' => []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['e' => [2, 3], 'f' => [1, 1], 'd' => [5.1], 'b' => [3], 'a' => [1], 'g' => [0], 'c' => [-2], 'h' => []],
            ],
            [
                $iter(['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]]),
                null,
                ['e' => [0, 3], 'a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'g' => [2, 5], 'd' => [3, 0], 'f' => [5, 2]],
            ],
            [
                $iter(['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['e' => [0, 3], 'a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'g' => [2, 5], 'd' => [3, 0], 'f' => [5, 2]],
            ],
            [
                $iter(['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['f' => [5, 2], 'd' => [3, 0], 'g' => [2, 5], 'c' => [2, 2], 'b' => [2, 1], 'a' => [1, 2], 'e' => [0, 3]],
            ],
            [
                $iter(['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]]),
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                ['d' => [3, 0], 'b' => [2, 1], 'a' => [1, 2], 'c' => [2, 2], 'f' => [5, 2], 'e' => [0, 3], 'g' => [2, 5]],
            ],
            [
                $iter(['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]]),
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
                ['g' => [2, 5], 'e' => [0, 3], 'a' => [1, 2], 'c' => [2, 2], 'f' => [5, 2], 'b' => [2, 1], 'd' => [3, 0]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTraversables
     * @param \Traversable $data
     * @param callable|null $comparator
     * @param array $expected
     */
    public function testTraversables(\Traversable $data, ?callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::asort($data, $comparator) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForTraversables(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            [
                $trav([]),
                null,
                [],
            ],
            [
                $trav([]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                [],
            ],
            [
                $trav([]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [],
            ],
            [
                $trav(['a' => 1]),
                null,
                ['a' => 1],
            ],
            [
                $trav(['a' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1],
            ],
            [
                $trav([0 => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                [0 => 1],
            ],
            [
                $trav(['a' => 1, 'b' => 1]),
                null,
                ['a' => 1, 'b' => 1],
            ],
            [
                $trav(['a' => 1, 'b' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1, 'b' => 1],
            ],
            [
                $trav(['a' => 1, 'b' => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a' => 1, 'b' => 1],
            ],
            [
                $trav(['a' => 1, 'b' => 2]),
                null,
                ['a' => 1, 'b' => 2],
            ],
            [
                $trav(['a' => 1, 'b' => 2]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1, 'b' => 2],
            ],
            [
                $trav(['a' => 1, 'b' => 2]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b' => 2, 'a' => 1],
            ],
            [
                $trav(['b' => 2, 'a' => 1]),
                null,
                ['a' => 1, 'b' => 2],
            ],
            [
                $trav(['b' => 2, 'a' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['a' => 1, 'b' => 2],
            ],
            [
                $trav(['b' => 2, 'a' => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['b' => 2, 'a' => 1],
            ],
            [
                $trav(['a' => 2, 'b' => 1, 'c' => 1]),
                null,
                ['b' => 1, 'c' => 1, 'a' => 2],
            ],
            [
                $trav(['a' => 2, 'b' => 1, 'c' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b' => 1, 'c' => 1, 'a' => 2],
            ],
            [
                $trav(['a' => 2, 'b' => 1, 'c' => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a' => 2, 'b' => 1, 'c' => 1],
            ],
            [
                $trav(['a' => 2, 'b' => 1, 'c' => 3]),
                null,
                ['b' => 1, 'a' => 2, 'c' => 3],
            ],
            [
                $trav(['a' => 2, 'b' => 1, 'c' => 3]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b' => 1, 'a' => 2, 'c' => 3],
            ],
            [
                $trav(['a' => 2, 'b' => 1, 'c' => 3]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['c' => 3, 'a' => 2, 'b' => 1],
            ],
            [
                $trav(['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1]),
                null,
                ['f' => -6, 'e' => -3, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3, 'd' => 5, 'g' => 10, 'h' => 11],
            ],
            [
                $trav(['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['f' => -6, 'e' => -3, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3, 'd' => 5, 'g' => 10, 'h' => 11],
            ],
            [
                $trav(['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['h' => 11, 'g' => 10, 'd' => 5, 'b' => 3, 'c' => 2, 'a' => 1, 'i' => 1, 'e' => -3, 'f' => -6],
            ],
            [
                $trav(['a' => 1, 'b' => 3.3, 'c' => 2, 'd' => 5, 'e' => -3.1, 'f' => -6, 'g' => '10', 'h' => 11, 'i' => 1]),
                null,
                ['f' => -6, 'e' => -3.1, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3.3, 'd' => 5, 'g' => '10', 'h' => 11],
            ],
            [
                $trav(['a' => 1, 'b' => 3.3, 'c' => 2, 'd' => 5, 'e' => -3.1, 'f' => -6, 'g' => '10', 'h' => 11, 'i' => 1]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['f' => -6, 'e' => -3.1, 'a' => 1, 'i' => 1, 'c' => 2, 'b' => 3.3, 'd' => 5, 'g' => '10', 'h' => 11],
            ],
            [
                $trav(['a' => 1, 'b' => 3.3, 'c' => 2, 'd' => 5, 'e' => -3.1, 'f' => -6, 'g' => '10', 'h' => 11, 'i' => 1]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['h' => 11, 'g' => '10', 'd' => 5, 'b' => 3.3, 'c' => 2, 'a' => 1, 'i' => 1, 'e' => -3.1, 'f' => -6]
            ],
            [
                $trav(['a' => true, 'b' => false, 'c' => false, 'd' => true, 'e' => false]),
                null,
                ['b' => false, 'c' => false, 'e' => false, 'a' => true, 'd' => true],
            ],
            [
                $trav(['a' => true, 'b' => false, 'c' => false, 'd' => true, 'e' => false]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['b' => false, 'c' => false, 'e' => false, 'a' => true, 'd' => true],
            ],
            [
                $trav(['a' => true, 'b' => false, 'c' => false, 'd' => true, 'e' => false]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['a' => true, 'd' => true, 'b' => false, 'c' => false, 'e' => false],
            ],
            [
                $trav(['a' => [1], 'b' => [3], 'c' => [-2], 'd' => [5.1], 'e' => [2, 3], 'f' => [1, 1], 'g' => [0], 'h' => []]),
                null,
                ['h' => [], 'c' => [-2], 'g' => [0], 'a' => [1], 'b' => [3], 'd' => [5.1], 'f' => [1, 1], 'e' => [2, 3]],
            ],
            [
                $trav(['a' => [1], 'b' => [3], 'c' => [-2], 'd' => [5.1], 'e' => [2, 3], 'f' => [1, 1], 'g' => [0], 'h' => []]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['h' => [], 'c' => [-2], 'g' => [0], 'a' => [1], 'b' => [3], 'd' => [5.1], 'f' => [1, 1], 'e' => [2, 3]],
            ],
            [
                $trav(['a' => [1], 'b' => [3], 'c' => [-2], 'd' => [5.1], 'e' => [2, 3], 'f' => [1, 1], 'g' => [0], 'h' => []]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['e' => [2, 3], 'f' => [1, 1], 'd' => [5.1], 'b' => [3], 'a' => [1], 'g' => [0], 'c' => [-2], 'h' => []],
            ],
            [
                $trav(['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]]),
                null,
                ['e' => [0, 3], 'a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'g' => [2, 5], 'd' => [3, 0], 'f' => [5, 2]],
            ],
            [
                $trav(['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]]),
                fn ($lhs, $rhs) => $lhs <=> $rhs,
                ['e' => [0, 3], 'a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'g' => [2, 5], 'd' => [3, 0], 'f' => [5, 2]],
            ],
            [
                $trav(['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]]),
                fn ($lhs, $rhs) => $rhs <=> $lhs,
                ['f' => [5, 2], 'd' => [3, 0], 'g' => [2, 5], 'c' => [2, 2], 'b' => [2, 1], 'a' => [1, 2], 'e' => [0, 3]],
            ],
            [
                $trav(['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]]),
                fn ($lhs, $rhs) => $lhs[1] <=> $rhs[1],
                ['d' => [3, 0], 'b' => [2, 1], 'a' => [1, 2], 'c' => [2, 2], 'f' => [5, 2], 'e' => [0, 3], 'g' => [2, 5]],
            ],
            [
                $trav(['a' => [1, 2], 'b' => [2, 1], 'c' => [2, 2], 'd' => [3, 0], 'e' => [0, 3], 'f' => [5, 2], 'g' => [2, 5]]),
                fn ($lhs, $rhs) => $rhs[1] <=> $lhs[1],
                ['g' => [2, 5], 'e' => [0, 3], 'a' => [1, 2], 'c' => [2, 2], 'f' => [5, 2], 'b' => [2, 1], 'd' => [3, 0]],
            ],
        ];
    }

    /**
     * @test asort preserves all elements from generator with duplicate keys
     */
    public function testAsortGeneratorWithDuplicateKeys(): void
    {
        // Given: generator yielding keys 0, 1, 0, 1 (duplicates)
        $gen = GeneratorFixture::getCombined([0, 1, 0, 1], ['c', 'a', 'b', 'z']);

        // When
        $keys = [];
        $values = [];
        foreach (Sort::asort($gen) as $key => $datum) {
            $keys[] = $key;
            $values[] = $datum;
        }

        // Then
        $this->assertCount(4, $values);
        $this->assertSame(['a', 'b', 'c', 'z'], $values);
        $this->assertSame([1, 0, 0, 1], $keys);
    }

    /**
     * @test asort with custom comparator preserves all elements from generator with duplicate keys
     */
    public function testAsortGeneratorWithDuplicateKeysCustomComparator(): void
    {
        // Given: generator yielding keys 0, 1, 0, 1 (duplicates)
        $gen = GeneratorFixture::getCombined([0, 1, 0, 1], ['c', 'a', 'b', 'z']);

        // When
        $keys = [];
        $values = [];
        foreach (Sort::asort($gen, fn ($lhs, $rhs) => $rhs <=> $lhs) as $key => $datum) {
            $keys[] = $key;
            $values[] = $datum;
        }

        // Then
        $this->assertCount(4, $values);
        $this->assertSame(['z', 'c', 'b', 'a'], $values);
        $this->assertSame([1, 0, 0, 1], $keys);
    }

    /**
     * @test asort with duplicate keys and equal values preserves insertion order for ties
     */
    public function testAsortGeneratorWithDuplicateKeysAndEqualValues(): void
    {
        // Given: generator yielding duplicate keys with some equal values
        $gen = GeneratorFixture::getCombined([0, 1, 0, 1], ['a', 'a', 'b', 'b']);

        // When
        $keys = [];
        $values = [];
        foreach (Sort::asort($gen) as $key => $datum) {
            $keys[] = $key;
            $values[] = $datum;
        }

        // Then
        $this->assertCount(4, $values);
        $this->assertSame(['a', 'a', 'b', 'b'], $values);
        $this->assertSame([0, 1, 0, 1], $keys);
    }

    /**
     * @test         asort array with NAN sorted last by the default comparison
     * @dataProvider dataProviderForNanSortsLastArray
     * @param        array $data
     * @param        array $expected
     */
    public function testNanSortsLastArray(array $data, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::asort($data) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     */
    public static function dataProviderForNanSortsLastArray(): array
    {
        return [
            'NAN in the middle, list keys' => [
                [2, 4, \NAN, 5, 6, 1, 3],
                [5 => 1, 0 => 2, 6 => 3, 1 => 4, 3 => 5, 4 => 6, 2 => \NAN],
            ],
            'NANs keep input order' => [
                ['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN, 'e' => 3],
                ['c' => 1, 'a' => 2, 'e' => 3, 'b' => \NAN, 'd' => \NAN],
            ],
            'only NANs' => [
                ['x' => \NAN, 'y' => \NAN],
                ['x' => \NAN, 'y' => \NAN],
            ],
            'NAN after INF' => [
                ['p' => \INF, 'q' => \NAN, 'r' => -\INF, 's' => 0.5],
                ['r' => -\INF, 's' => 0.5, 'p' => \INF, 'q' => \NAN],
            ],
        ];
    }

    /**
     * @test         asort generators with NAN sorted last by the default comparison
     * @dataProvider dataProviderForNanSortsLastGenerators
     * @param        \Generator $data
     * @param        array $expected
     */
    public function testNanSortsLastGenerators(\Generator $data, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::asort($data) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     */
    public static function dataProviderForNanSortsLastGenerators(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            'NAN in the middle, list keys' => [
                $gen([2, 4, \NAN, 5, 6, 1, 3]),
                [5 => 1, 0 => 2, 6 => 3, 1 => 4, 3 => 5, 4 => 6, 2 => \NAN],
            ],
            'NANs keep input order' => [
                $gen(['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN, 'e' => 3]),
                ['c' => 1, 'a' => 2, 'e' => 3, 'b' => \NAN, 'd' => \NAN],
            ],
            'only NANs' => [
                $gen(['x' => \NAN, 'y' => \NAN]),
                ['x' => \NAN, 'y' => \NAN],
            ],
            'NAN after INF' => [
                $gen(['p' => \INF, 'q' => \NAN, 'r' => -\INF, 's' => 0.5]),
                ['r' => -\INF, 's' => 0.5, 'p' => \INF, 'q' => \NAN],
            ],
        ];
    }

    /**
     * @test         asort iterators with NAN sorted last by the default comparison
     * @dataProvider dataProviderForNanSortsLastIterators
     * @param        \Iterator $data
     * @param        array $expected
     */
    public function testNanSortsLastIterators(\Iterator $data, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::asort($data) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     */
    public static function dataProviderForNanSortsLastIterators(): array
    {
        $iter = fn (array $data) => new \ArrayIterator($data);

        return [
            'NAN in the middle, list keys' => [
                $iter([2, 4, \NAN, 5, 6, 1, 3]),
                [5 => 1, 0 => 2, 6 => 3, 1 => 4, 3 => 5, 4 => 6, 2 => \NAN],
            ],
            'NANs keep input order' => [
                $iter(['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN, 'e' => 3]),
                ['c' => 1, 'a' => 2, 'e' => 3, 'b' => \NAN, 'd' => \NAN],
            ],
            'only NANs' => [
                $iter(['x' => \NAN, 'y' => \NAN]),
                ['x' => \NAN, 'y' => \NAN],
            ],
            'NAN after INF' => [
                $iter(['p' => \INF, 'q' => \NAN, 'r' => -\INF, 's' => 0.5]),
                ['r' => -\INF, 's' => 0.5, 'p' => \INF, 'q' => \NAN],
            ],
        ];
    }

    /**
     * @test         asort traversables with NAN sorted last by the default comparison
     * @dataProvider dataProviderForNanSortsLastTraversables
     * @param        \Traversable $data
     * @param        array $expected
     */
    public function testNanSortsLastTraversables(\Traversable $data, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::asort($data) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     */
    public static function dataProviderForNanSortsLastTraversables(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            'NAN in the middle, list keys' => [
                $trav([2, 4, \NAN, 5, 6, 1, 3]),
                [5 => 1, 0 => 2, 6 => 3, 1 => 4, 3 => 5, 4 => 6, 2 => \NAN],
            ],
            'NANs keep input order' => [
                $trav(['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN, 'e' => 3]),
                ['c' => 1, 'a' => 2, 'e' => 3, 'b' => \NAN, 'd' => \NAN],
            ],
            'only NANs' => [
                $trav(['x' => \NAN, 'y' => \NAN]),
                ['x' => \NAN, 'y' => \NAN],
            ],
            'NAN after INF' => [
                $trav(['p' => \INF, 'q' => \NAN, 'r' => -\INF, 's' => 0.5]),
                ['r' => -\INF, 's' => 0.5, 'p' => \INF, 'q' => \NAN],
            ],
        ];
    }

    /**
     * @test         asort array of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionArray
     * @param        array $data
     * @param        array $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionArray(array $data, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::asort($data) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionArray(): array
    {
        return [
            'int 2^53 + 1 before float 2.0^53, equal as floats' => [
                ['a' => 9007199254740993, 'b' => 9007199254740992.0],
                ['b' => 9007199254740992.0, 'a' => 9007199254740993],
            ],
            'float 2.0^53 after ints that equal it as floats' => [
                ['a' => 9007199254740994, 'b' => 9007199254740993, 'c' => 9007199254740992.0],
                ['c' => 9007199254740992.0, 'b' => 9007199254740993, 'a' => 9007199254740994],
            ],
            'negative float -2.0^53 before int -(2^53 + 1)' => [
                ['a' => -9007199254740992.0, 'b' => -9007199254740993],
                ['b' => -9007199254740993, 'a' => -9007199254740992.0],
            ],
            'float 2.0^63 before int PHP_INT_MAX' => [
                ['a' => 9.223372036854776e+18, 'b' => 9223372036854775807],
                ['b' => 9223372036854775807, 'a' => 9.223372036854776e+18],
            ],
            'NAN after an int beyond 2^53 and a float' => [
                ['a' => 9007199254740993, 'b' => \NAN, 'c' => 9007199254740992.0],
                ['c' => 9007199254740992.0, 'a' => 9007199254740993, 'b' => \NAN],
            ],
        ];
    }

    /**
     * @test         asort generators of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionGenerators
     * @param        \Generator $data
     * @param        array $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionGenerators(\Generator $data, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::asort($data) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionGenerators(): array
    {
        $gen = fn (array $data) => GeneratorFixture::getKeyValueGenerator($data);

        return [
            'int 2^53 + 1 before float 2.0^53, equal as floats' => [
                $gen(['a' => 9007199254740993, 'b' => 9007199254740992.0]),
                ['b' => 9007199254740992.0, 'a' => 9007199254740993],
            ],
            'float 2.0^53 after ints that equal it as floats' => [
                $gen(['a' => 9007199254740994, 'b' => 9007199254740993, 'c' => 9007199254740992.0]),
                ['c' => 9007199254740992.0, 'b' => 9007199254740993, 'a' => 9007199254740994],
            ],
            'negative float -2.0^53 before int -(2^53 + 1)' => [
                $gen(['a' => -9007199254740992.0, 'b' => -9007199254740993]),
                ['b' => -9007199254740993, 'a' => -9007199254740992.0],
            ],
            'float 2.0^63 before int PHP_INT_MAX' => [
                $gen(['a' => 9.223372036854776e+18, 'b' => 9223372036854775807]),
                ['b' => 9223372036854775807, 'a' => 9.223372036854776e+18],
            ],
            'NAN after an int beyond 2^53 and a float' => [
                $gen(['a' => 9007199254740993, 'b' => \NAN, 'c' => 9007199254740992.0]),
                ['c' => 9007199254740992.0, 'a' => 9007199254740993, 'b' => \NAN],
            ],
        ];
    }

    /**
     * @test         asort iterators of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionIterators
     * @param        \Iterator $data
     * @param        array $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionIterators(\Iterator $data, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::asort($data) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionIterators(): array
    {
        $iter = fn (array $data) => new \ArrayIterator($data);

        return [
            'int 2^53 + 1 before float 2.0^53, equal as floats' => [
                $iter(['a' => 9007199254740993, 'b' => 9007199254740992.0]),
                ['b' => 9007199254740992.0, 'a' => 9007199254740993],
            ],
            'float 2.0^53 after ints that equal it as floats' => [
                $iter(['a' => 9007199254740994, 'b' => 9007199254740993, 'c' => 9007199254740992.0]),
                ['c' => 9007199254740992.0, 'b' => 9007199254740993, 'a' => 9007199254740994],
            ],
            'negative float -2.0^53 before int -(2^53 + 1)' => [
                $iter(['a' => -9007199254740992.0, 'b' => -9007199254740993]),
                ['b' => -9007199254740993, 'a' => -9007199254740992.0],
            ],
            'float 2.0^63 before int PHP_INT_MAX' => [
                $iter(['a' => 9.223372036854776e+18, 'b' => 9223372036854775807]),
                ['b' => 9223372036854775807, 'a' => 9.223372036854776e+18],
            ],
            'NAN after an int beyond 2^53 and a float' => [
                $iter(['a' => 9007199254740993, 'b' => \NAN, 'c' => 9007199254740992.0]),
                ['c' => 9007199254740992.0, 'a' => 9007199254740993, 'b' => \NAN],
            ],
        ];
    }

    /**
     * @test         asort traversables of an int beyond 2^53 and a float, which may compare equal as floats
     * @dataProvider dataProviderForIntAndFloatBeyondFloatPrecisionTraversables
     * @param        \Traversable $data
     * @param        array $expected
     */
    public function testIntAndFloatBeyondFloatPrecisionTraversables(\Traversable $data, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::asort($data) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort, which compares int and float by exact value, with
     * NAN placed last by the key, since Python's own ordering misplaces NAN:
     *   sorted(data.items(), key=lambda kv: (isnan(kv[1]), 0 if isnan(kv[1]) else kv[1]))
     */
    public static function dataProviderForIntAndFloatBeyondFloatPrecisionTraversables(): array
    {
        $trav = fn (array $data) => new IteratorAggregateFixture($data);

        return [
            'int 2^53 + 1 before float 2.0^53, equal as floats' => [
                $trav(['a' => 9007199254740993, 'b' => 9007199254740992.0]),
                ['b' => 9007199254740992.0, 'a' => 9007199254740993],
            ],
            'float 2.0^53 after ints that equal it as floats' => [
                $trav(['a' => 9007199254740994, 'b' => 9007199254740993, 'c' => 9007199254740992.0]),
                ['c' => 9007199254740992.0, 'b' => 9007199254740993, 'a' => 9007199254740994],
            ],
            'negative float -2.0^53 before int -(2^53 + 1)' => [
                $trav(['a' => -9007199254740992.0, 'b' => -9007199254740993]),
                ['b' => -9007199254740993, 'a' => -9007199254740992.0],
            ],
            'float 2.0^63 before int PHP_INT_MAX' => [
                $trav(['a' => 9.223372036854776e+18, 'b' => 9223372036854775807]),
                ['b' => 9223372036854775807, 'a' => 9.223372036854776e+18],
            ],
            'NAN after an int beyond 2^53 and a float' => [
                $trav(['a' => 9007199254740993, 'b' => \NAN, 'c' => 9007199254740992.0]),
                ['c' => 9007199254740992.0, 'a' => 9007199254740993, 'b' => \NAN],
            ],
        ];
    }

    /**
     * @test         asort array with a custom comparator, which is used as given when a value is NAN
     * @dataProvider dataProviderForCustomComparatorWithNanArray
     * @param        array $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithNanArray(array $data, callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::asort($data, $comparator) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort and the same NAN-first comparator:
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: nan_first(x[1], y[1])))
     * where nan_first(a, b) puts a NAN before every other value and compares other values with a <=> b.
     */
    public static function dataProviderForCustomComparatorWithNanArray(): array
    {
        return [
            'NAN-first comparator, NANs keep input order' => [
                ['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN],
                fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs,
                ['b' => \NAN, 'd' => \NAN, 'c' => 1, 'a' => 2],
            ],
        ];
    }

    /**
     * @test         asort array with a custom comparator, which is used as given for an int beyond 2^53 and a float
     * @dataProvider dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionArray
     * @param        array $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithIntAndFloatBeyondFloatPrecisionArray(array $data, callable $comparator, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::asort($data, $comparator) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's stable sort and the comparison PHP's <=> makes, which compares two
     * ints exactly and an int and a float as floats, so it ties 2^53 + 1 with 2.0^53:
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: php_spaceship(x[1], y[1])))
     */
    public static function dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionArray(): array
    {
        return [
            '<=> ties int 2^53 + 1 with float 2.0^53, kept in input order' => [
                ['a' => 9007199254740993, 'b' => 9007199254740992.0],
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                ['a' => 9007199254740993, 'b' => 9007199254740992.0],
            ],
            '<=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                ['a' => 9007199254740994, 'b' => 9007199254740993, 'c' => 9007199254740992.0],
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                ['b' => 9007199254740993, 'c' => 9007199254740992.0, 'a' => 9007199254740994],
            ],
        ];
    }

    /**
     * @test         asort generators with a custom comparator, which is used as given when a value is NAN
     * @dataProvider dataProviderForCustomComparatorWithNanGenerators
     * @param        \Generator $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithNanGenerators(\Generator $data, callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::asort($data, $comparator) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort and the same NAN-first comparator:
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: nan_first(x[1], y[1])))
     * where nan_first(a, b) puts a NAN before every other value and compares other values with a <=> b.
     */
    public static function dataProviderForCustomComparatorWithNanGenerators(): array
    {
        return [
            'NAN-first comparator, NANs keep input order' => [
                GeneratorFixture::getKeyValueGenerator(['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN]),
                fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs,
                ['b' => \NAN, 'd' => \NAN, 'c' => 1, 'a' => 2],
            ],
        ];
    }

    /**
     * @test         asort generators with a custom comparator, which is used as given for an int beyond 2^53 and a float
     * @dataProvider dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionGenerators
     * @param        \Generator $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithIntAndFloatBeyondFloatPrecisionGenerators(\Generator $data, callable $comparator, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::asort($data, $comparator) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's stable sort and the comparison PHP's <=> makes, which compares two
     * ints exactly and an int and a float as floats, so it ties 2^53 + 1 with 2.0^53:
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: php_spaceship(x[1], y[1])))
     */
    public static function dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionGenerators(): array
    {
        return [
            '<=> ties int 2^53 + 1 with float 2.0^53, kept in input order' => [
                GeneratorFixture::getKeyValueGenerator(['a' => 9007199254740993, 'b' => 9007199254740992.0]),
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                ['a' => 9007199254740993, 'b' => 9007199254740992.0],
            ],
            '<=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                GeneratorFixture::getKeyValueGenerator(['a' => 9007199254740994, 'b' => 9007199254740993, 'c' => 9007199254740992.0]),
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                ['b' => 9007199254740993, 'c' => 9007199254740992.0, 'a' => 9007199254740994],
            ],
        ];
    }

    /**
     * @test         asort iterators with a custom comparator, which is used as given when a value is NAN
     * @dataProvider dataProviderForCustomComparatorWithNanIterators
     * @param        \Iterator $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithNanIterators(\Iterator $data, callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::asort($data, $comparator) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort and the same NAN-first comparator:
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: nan_first(x[1], y[1])))
     * where nan_first(a, b) puts a NAN before every other value and compares other values with a <=> b.
     */
    public static function dataProviderForCustomComparatorWithNanIterators(): array
    {
        return [
            'NAN-first comparator, NANs keep input order' => [
                new \ArrayIterator(['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN]),
                fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs,
                ['b' => \NAN, 'd' => \NAN, 'c' => 1, 'a' => 2],
            ],
        ];
    }

    /**
     * @test         asort iterators with a custom comparator, which is used as given for an int beyond 2^53 and a float
     * @dataProvider dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionIterators
     * @param        \Iterator $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithIntAndFloatBeyondFloatPrecisionIterators(\Iterator $data, callable $comparator, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::asort($data, $comparator) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's stable sort and the comparison PHP's <=> makes, which compares two
     * ints exactly and an int and a float as floats, so it ties 2^53 + 1 with 2.0^53:
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: php_spaceship(x[1], y[1])))
     */
    public static function dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionIterators(): array
    {
        return [
            '<=> ties int 2^53 + 1 with float 2.0^53, kept in input order' => [
                new \ArrayIterator(['a' => 9007199254740993, 'b' => 9007199254740992.0]),
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                ['a' => 9007199254740993, 'b' => 9007199254740992.0],
            ],
            '<=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                new \ArrayIterator(['a' => 9007199254740994, 'b' => 9007199254740993, 'c' => 9007199254740992.0]),
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                ['b' => 9007199254740993, 'c' => 9007199254740992.0, 'a' => 9007199254740994],
            ],
        ];
    }

    /**
     * @test         asort traversables with a custom comparator, which is used as given when a value is NAN
     * @dataProvider dataProviderForCustomComparatorWithNanTraversables
     * @param        \Traversable $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithNanTraversables(\Traversable $data, callable $comparator, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Sort::asort($data, $comparator) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then (compared through var_export, since NAN !== NAN makes assertSame fail on equal arrays)
        $this->assertSame(\var_export($expected, true), \var_export($result, true));
    }

    /**
     * Test data generated with Python's stable sort and the same NAN-first comparator:
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: nan_first(x[1], y[1])))
     * where nan_first(a, b) puts a NAN before every other value and compares other values with a <=> b.
     */
    public static function dataProviderForCustomComparatorWithNanTraversables(): array
    {
        return [
            'NAN-first comparator, NANs keep input order' => [
                new IteratorAggregateFixture(['a' => 2, 'b' => \NAN, 'c' => 1, 'd' => \NAN]),
                fn ($lhs, $rhs): int => (\is_nan($lhs) || \is_nan($rhs)) ? \is_nan($rhs) <=> \is_nan($lhs) : $lhs <=> $rhs,
                ['b' => \NAN, 'd' => \NAN, 'c' => 1, 'a' => 2],
            ],
        ];
    }

    /**
     * @test         asort traversables with a custom comparator, which is used as given for an int beyond 2^53 and a float
     * @dataProvider dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionTraversables
     * @param        \Traversable $data
     * @param        callable $comparator
     * @param        array $expected
     */
    public function testCustomComparatorWithIntAndFloatBeyondFloatPrecisionTraversables(\Traversable $data, callable $comparator, array $expected): void
    {
        // Given (on 32-bit PHP these integer literals parse as floats, so the expected values do not apply)
        if (\PHP_INT_SIZE < 8) {
            $this->markTestSkipped('Requires 64-bit integers');
        }
        $result = [];

        // When
        foreach (Sort::asort($data, $comparator) as $key => $datum) {
            $result[$key] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python's stable sort and the comparison PHP's <=> makes, which compares two
     * ints exactly and an int and a float as floats, so it ties 2^53 + 1 with 2.0^53:
     *   sorted(data.items(), key=cmp_to_key(lambda x, y: php_spaceship(x[1], y[1])))
     */
    public static function dataProviderForCustomComparatorWithIntAndFloatBeyondFloatPrecisionTraversables(): array
    {
        return [
            '<=> ties int 2^53 + 1 with float 2.0^53, kept in input order' => [
                new IteratorAggregateFixture(['a' => 9007199254740993, 'b' => 9007199254740992.0]),
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                ['a' => 9007199254740993, 'b' => 9007199254740992.0],
            ],
            '<=> ties int 2^53 + 1 with float 2.0^53, both before int 2^53 + 2' => [
                new IteratorAggregateFixture(['a' => 9007199254740994, 'b' => 9007199254740993, 'c' => 9007199254740992.0]),
                fn ($lhs, $rhs): int => $lhs <=> $rhs,
                ['b' => 9007199254740993, 'c' => 9007199254740992.0, 'a' => 9007199254740994],
            ],
        ];
    }
}

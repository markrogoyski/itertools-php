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
        $this->assertEquals($expected, $sortedAscending);
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
        $this->assertEquals($expected, $sortedDescending);
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
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForArray(): array
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
                ['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1],
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
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForGenerators(): array
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
                ['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1],
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
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForIterators(): array
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
                ['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1],
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
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForTraversables(): array
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
                ['a' => 1, 'b' => 3, 'c' => 2, 'd' => 5, 'e' => -3, 'f' => -6, 'g' => 10, 'h' => 11, 'i' => 1],
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
}

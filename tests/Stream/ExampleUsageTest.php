<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class ExampleUsageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test compressAssociative example usage
     */
    public function compressAssociativeExampleUsage(): void
    {
        // Given
        $starWarsEpisodes = [
            'I'    => 'The Phantom Menace',
            'II'   => 'Attack of the Clones',
            'III'  => 'Revenge of the Sith',
            'IV'   => 'A New Hope',
            'V'    => 'The Empire Strikes Back',
            'VI'   => 'Return of the Jedi',
            'VII'  => 'The Force Awakens',
            'VIII' => 'The Last Jedi',
            'IX'   => 'The Rise of Skywalker',
        ];
        $sequelTrilogyNumbers = ['VII', 'VIII', 'IX'];

        // When
        $sequelTrilogy = Stream::of($starWarsEpisodes)
            ->compressAssociative($sequelTrilogyNumbers)
            ->toAssociativeArray();

        // Then
        $expected = [
            'VII'  => 'The Force Awakens',
            'VIII' => 'The Last Jedi',
            'IX'   => 'The Rise of Skywalker',
        ];
        $this->assertEquals($expected, $sequelTrilogy);
    }

    /**
     * @test filterKeys example usage
     */
    public function filterKeysExampleUsage(): void
    {
        // Given
        $olympics = [
            2000 => 'Sydney',
            2002 => 'Salt Lake City',
            2004 => 'Athens',
            2006 => 'Turin',
            2008 => 'Beijing',
            2010 => 'Vancouver',
            2012 => 'London',
            2014 => 'Sochi',
            2016 => 'Rio de Janeiro',
            2018 => 'Pyeongchang',
            2020 => 'Tokyo',
            2022 => 'Beijing',
        ];

        // And
        $winterFilter = fn ($year) => $year % 4 === 2;

        // When
        $winterOlympics = Stream::of($olympics)
            ->filterKeys($winterFilter)
            ->toArray();

        // Then
        $expected = [
            'Salt Lake City',
            'Turin',
            'Vancouver',
            'Sochi',
            'Pyeongchang',
            'Beijing',
        ];
        $this->assertEquals($expected, $winterOlympics);
    }

    /**
     * @test reindex example usage
     */
    public function reindexExampleUsage(): void
    {
        // Given
        $dbResult = [
            [
                'title'   => 'Star Wars: Episode IV – A New Hope',
                'episode' => 'IV',
                'year'    => 1977,
            ],
            [
                'title'   => 'Star Wars: Episode V – The Empire Strikes Back',
                'episode' => 'V',
                'year'    => 1980,
            ],
            [
                'title' => 'Star Wars: Episode VI – Return of the Jedi',
                'episode' => 'VI',
                'year' => 1983,
            ],
        ];

        // And
        $reindexFunc = fn (array $swFilm) => $swFilm['episode'];

        // When
        $reindexResult = Stream::of($dbResult)
            ->reindex($reindexFunc)
            ->toAssociativeArray();

        // Then
        $expected = [
            'IV' => [
                'title'   => 'Star Wars: Episode IV – A New Hope',
                'episode' => 'IV',
                'year'    => 1977,
            ],
            'V' => [
                'title'   => 'Star Wars: Episode V – The Empire Strikes Back',
                'episode' => 'V',
                'year'    => 1980,
            ],
            'VI' => [
                'title' => 'Star Wars: Episode VI – Return of the Jedi',
                'episode' => 'VI',
                'year' => 1983,
            ],
        ];
        $this->assertEquals($expected, $reindexResult);
    }

    /**
     * @test filter / asort example usage
     */
    public function filterAsortExampleUsage(): void
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

        // When
        $sortedAndFiltered = Stream::of($worldPopulations)
            ->filterTrue(fn ($pop) => $pop > 300_000_000)
            ->asort()
            ->toAssociativeArray();

        // Then
        $expected = [
            'USA'   => 331_002_651,
            'India' => 1_380_004_385,
            'China' => 1_439_323_776,
        ];
        $this->assertEquals($expected, $sortedAndFiltered);
    }

    /**
     * @test map / filter / sort / reduce example usage
     */
    public function mapFilterSortReduceExampleUsage(): void
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

        // When
        $sortedAndFiltered = Stream::of($worldPopulations)
            ->map(fn ($pop) => \round($pop, -6))
            ->map('intval')
            ->filterTrue(fn ($pop) => $pop > 300_000_000)
            ->sort(fn ($lhs, $rhs) => $rhs <=> $lhs)
            ->toAssociativeArray();

        // Then
        $expected = [
            1_439_000_000,
            1_380_000_000,
            331_000_000,
        ];
        $this->assertEquals($expected, $sortedAndFiltered);
    }

    /**
     * @test reverse / toString example usage
     */
    public function reverseExampleUsage(): void
    {
        // Given
        $words = ['are', 'you', 'as' ,'bored', 'as', 'I', 'am'];

        // When
        $reversed = Stream::of($words)
            ->reverse()
            ->toString(' ');

        // Then
        $this->assertEquals('am I as bored as you are', $reversed);
    }
}

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
}

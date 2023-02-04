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
}

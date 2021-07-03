<?php

declare(strict_types=1);

namespace IterTools\Tests\Infinite;

use IterTools\Multi;
use IterTools\Single;

class StringTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test zip strings
     */
    public function testZipStrings(): void
    {
        // Given
        $letters = 'abc';
        $numbers = '123';

        // And
        $result   = [];
        $expected = ['a1', 'b2', 'c3'];

        // When
        foreach (Multi::zip(Single::string($letters), Single::string($numbers)) as [$letter, $number]) {
            $result[] = "{$letter}{$number}";
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    /**
     * @test chain strings
     */
    public function testChainStrings(): void
    {
        // Given
        $letters = 'abc';
        $numbers = '123';

        // And
        $result   = [];
        $expected = ['a', 'b', 'c', '1', '2', '3'];

        // When
        foreach (Multi::chain(Single::string($letters), Single::string($numbers)) as $character) {
            $result[] = $character;
        }

        // Then
        $this->assertEquals($expected, $result);
    }
}

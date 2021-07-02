<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;

class StringTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForAsciiStrings
     * @param        string $string
     * @param        array  $expected
     */
    public function testStringAscii(string $string, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::string($string) as $character) {
            $result[] = $character;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForAsciiStrings(): array
    {
        return [
            ['', []],
            ['a', ['a']],
            ['ab', ['a', 'b']],
            ['abc', ['a', 'b', 'c']],
            ['a"b"c', ['a', '"', 'b', '"', 'c']],
            ['abcdefghijklmnopqrstuvwxyz', ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z']],
        ];
    }

    /**
     * @dataProvider dataProviderForMultiByteStrings
     * @param        string $string
     * @param        array  $expected
     */
    public function testStringMultiByte(string $string, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (Single::string($string) as $character) {
            $result[] = $character;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForMultiByteStrings(): array
    {
        return [
            ['　', ['　']],
            ['日本語も大丈夫ですよね！', ['日', '本', '語', 'も', '大', '丈', '夫', 'で', 'す', 'よ', 'ね', '！']],
            ['English 日本語', ['E', 'n', 'g', 'l', 'i', 's', 'h', ' ', '日', '本', '語']],
        ];
    }
}

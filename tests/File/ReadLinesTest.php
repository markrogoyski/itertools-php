<?php

declare(strict_types=1);

namespace IterTools\Tests\File;

use IterTools\File;
use IterTools\Tests\Fixture\FileFixture;

class ReadLinesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForCommon
     * @param        resource $file
     * @param        array $expected
     */
    public function testCommon($file, array $expected): void
    {
        // Given
        $result = [];

        // When
        foreach (File::readLines($file) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForCommon(): array
    {
        $file = fn (array $lines) => FileFixture::createFromLines($lines);

        return [
            [
                $file([]),
                [],
            ],
            [
                $file(['']),
                [],
            ],
            [
                $file(['123']),
                ['123'],
            ],
            [
                $file(['123', '456', '78']),
                ["123\n", "456\n", '78'],
            ],
            [
                $file(['123', '456', '']),
                ["123\n", "456\n"],
            ],
            [
                $file(['123', '', '']),
                ["123\n", "\n"],
            ],
            [
                $file(['123', '', '456']),
                ["123\n", "\n", "456"],
            ],
            [
                $file(['', '', '456']),
                ["\n", "\n", "456"],
            ],
            [
                $file(['', '', '']),
                ["\n", "\n"],
            ],
        ];
    }

    /**
     * @return void
     */
    public function testError(): void
    {
        // Given
        $file = FileFixture::createFromLines([]);

        // When
        fclose($file);

        // Then
        $this->expectException(\UnexpectedValueException::class);
        foreach (File::readLines($file) as $_) {
            break;
        }
    }
}

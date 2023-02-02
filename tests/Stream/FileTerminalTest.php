<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class FileTerminalTest extends \PHPUnit\Framework\TestCase
{
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('test');
    }

    /**
     * @dataProvider dataProviderForToFileWithDefaultConfig
     * @param array $lines
     * @param string $expected
     * @return void
     */
    public function testToFileWithDefaultConfig(array $lines, string $expected): void
    {
        // Given
        $stream = Stream::of($lines);
        $filePath = $this->root->url().'/'.uniqid();
        $file = \fopen($filePath, 'w');

        // When
        $stream->toFile($file, "\n");
        $fileContents = \file_get_contents($filePath);

        // Then
        $this->assertEquals($expected, $fileContents);
    }

    public function dataProviderForToFileWithDefaultConfig(): array
    {
        return [
            [
                [],
                "",
            ],
            [
                [123],
                "123",
            ],
            [
                ['123'],
                "123",
            ],
            [
                [123, 456, 789],
                "123\n456\n789",
            ],
            [
                ['123', '456', '789'],
                "123\n456\n789",
            ],
            [
                ["123\n", "45\n6", "\n789"],
                "123\n\n45\n6\n\n789",
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForToFileWithCustomConfig
     * @param array $lines
     * @param array $config
     * @param string $expected
     * @return void
     */
    public function testToFileWithCustomConfig(array $lines, array $config, string $expected): void
    {
        // Given
        $stream = Stream::of($lines);
        $filePath = $this->root->url().'/'.uniqid();
        $file = \fopen($filePath, 'w');

        // When
        $stream->toFile($file, ...$config);
        $fileContents = \file_get_contents($filePath);

        // Then
        $this->assertEquals($expected, $fileContents);
    }

    public function dataProviderForToFileWithCustomConfig(): array
    {
        return [
            [
                [],
                ["\n"],
                "",
            ],
            [
                [],
                ["\n", null, null],
                "",
            ],
            [
                [],
                ["\n", "a", null],
                "a\n",
            ],
            [
                [],
                ["\n", null, "b"],
                "\nb",
            ],
            [
                [],
                ["\n", "hel", "lo"],
                "hel\n\nlo",
            ],
            [
                [],
                ["\t"],
                "",
            ],
            [
                [],
                ["\t", "good ", "bye"],
                "good \t\tbye",
            ],
            [
                [123],
                ["\n"],
                "123",
            ],
            [
                [123],
                ["\n", null, null],
                "123",
            ],
            [
                [123],
                ["\n", "a", null],
                "a\n123",
            ],
            [
                [123],
                ["\n", null, "b"],
                "123\nb",
            ],
            [
                [123],
                ["\n", "a", "b"],
                "a\n123\nb",
            ],
            [
                [123],
                ["\n", null, "\n"],
                "123\n\n",
            ],
            [
                [123],
                ["\t"],
                "123",
            ],
            [
                [123],
                ["\t", "a", "b"],
                "a\t123\tb",
            ],
            [
                ['123'],
                ["\n"],
                "123",
            ],
            [
                ['123'],
                ["\n", "\n", "\n"],
                "\n\n123\n\n",
            ],
            [
                ['123'],
                ["\t"],
                "123",
            ],
            [
                ['123'],
                ["\t", "\n", "\n"],
                "\n\t123\t\n",
            ],
            [
                [123, 456, 789],
                ["\n"],
                "123\n456\n789",
            ],
            [
                [123, 456, 789],
                ["\n", "\t", "--"],
                "\t\n123\n456\n789\n--",
            ],
            [
                [123, 456, 789],
                ["\t"],
                "123\t456\t789",
            ],
            [
                [123, 456, 789],
                ["\t", "\t", "\t"],
                "\t\t123\t456\t789\t\t",
            ],
            [
                ['123', '456', '789'],
                ["\n"],
                "123\n456\n789",
            ],
            [
                ['123', '456', '789'],
                ["\n", "\t", "\t"],
                "\t\n123\n456\n789\n\t",
            ],
            [
                ['123', '456', '789'],
                ["\n", "<<", ">>"],
                "<<\n123\n456\n789\n>>",
            ],
            [
                ['123', '456', '789'],
                ["\t"],
                "123\t456\t789",
            ],
            [
                ['123', '456', '789'],
                ["\t", "(", ")"],
                "(\t123\t456\t789\t)",
            ],
            [
                ["123\n", "45\n6", "\n789"],
                ["\n"],
                "123\n\n45\n6\n\n789",
            ],
            [
                ["123\n", "45\n6", "\n789"],
                ["\n", "\n", "\n"],
                "\n\n123\n\n45\n6\n\n789\n\n",
            ],
            [
                ["123\n", "45\n6", "\n789"],
                ["\t", "\n", "\n"],
                "\n\t123\n\t45\n6\t\n789\t\n",
            ],
            [
                ["123\t", "45\t6", "\t789"],
                ["\n"],
                "123\t\n45\t6\n\t789",
            ],
            [
                ["123\t", "45\t6", "\t789"],
                ["\t"],
                "123\t\t45\t6\t\t789",
            ],
            [
                ["123\t", "45\t6", "\t789"],
                ["\t", "\t", "\t"],
                "\t\t123\t\t45\t6\t\t789\t\t",
            ],
            [
                ['a', 'b', 'c'],
                ["-"],
                "a-b-c",
            ],
            [
                ['a', 'b', 'c'],
                ["\n", null, "\n"],
                "a\nb\nc\n\n",
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForToCsvFileWithDefaultConfig
     * @param array $rows
     * @param string $expected
     * @return void
     */
    public function testToCsvFileWithDefaultConfig(array $rows, string $expected): void
    {
        // Given
        $stream = Stream::of($rows);
        $filePath = $this->root->url().'/'.uniqid();
        $file = \fopen($filePath, 'w');

        // When
        $stream->toCsvFile($file);
        $fileContents = \file_get_contents($filePath);

        // Then
        $this->assertEquals($expected, $fileContents);
    }

    public function dataProviderForToCsvFileWithDefaultConfig(): array
    {
        return [
            [
                [],
                "",
            ],
            [
                [
                    [],
                ],
                "\n",
            ],
            [
                [
                    [1, 2, 3],
                ],
                "1,2,3\n",
            ],
            [
                [
                    [1, 2, 3],
                    [4, 5, 6, 7],
                    [8, 9, 10, 11, 12],
                ],
                "1,2,3\n4,5,6,7\n8,9,10,11,12\n",
            ],
            [
                [
                    [1, 2, 3],
                    [],
                    [4, 5, 6, 7],
                    [8, 9, 10, 11, 12],
                ],
                "1,2,3\n\n4,5,6,7\n8,9,10,11,12\n",
            ],
            [
                [
                    [],
                    [1, 2, 3],
                    [4, 5, 6, 7],
                    [8, 9, 10, 11, 12],
                ],
                "\n1,2,3\n4,5,6,7\n8,9,10,11,12\n",
            ],
            [
                [
                    [1, 2, 3],
                    [4, 5, 6, 7],
                    [8, 9, 10, 11, 12],
                    [],
                ],
                "1,2,3\n4,5,6,7\n8,9,10,11,12\n\n",
            ],
            [
                [
                    [],
                    [1, 2, 3],
                    [4, 5, 6, 7],
                    [],
                    [8, 9, 10, 11, 12],
                    [],
                ],
                "\n1,2,3\n4,5,6,7\n\n8,9,10,11,12\n\n",
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForToCsvFileWithCustomConfig
     * @param array $rows
     * @param array|null $header
     * @param array{string, string, string} $config
     * @param string $expected
     * @return void
     */
    public function testToCsvFileWithCustomConfig(array $rows, ?array $header, array $config, string $expected): void
    {
        // Given
        $stream = Stream::of($rows);
        $filePath = $this->root->url().'/'.uniqid();
        $file = \fopen($filePath, 'w');

        // When
        $stream->toCsvFile($file, $header, ...$config);
        $fileContents = \file_get_contents($filePath);

        // Then
        $this->assertEquals($expected, $fileContents);
    }

    public function dataProviderForToCsvFileWithCustomConfig(): array
    {
        return [
            [
                [],
                null,
                [',', '"', '\\'],
                "",
            ],
            [
                [],
                [1, 2, 3],
                [',', '"', '\\'],
                "1,2,3\n",
            ],
            [
                [],
                null,
                [';', '"', '\\'],
                "",
            ],
            [
                [],
                [1, 2, 3],
                [';', '"', '\\'],
                "1;2;3\n",
            ],
            [
                [],
                null,
                [';', "'", '\\'],
                "",
            ],
            [
                [],
                [1, 2, 3],
                [';', "'", '\\'],
                "1;2;3\n",
            ],
            [
                [
                    [],
                ],
                null,
                [',', '"', '\\'],
                "\n",
            ],
            [
                [
                    [],
                ],
                [1, 2, 3],
                [',', '"', '\\'],
                "1,2,3\n\n",
            ],
            [
                [
                    [],
                ],
                null,
                [';', '"', '\\'],
                "\n",
            ],
            [
                [
                    [],
                ],
                [1, 2, 3],
                [';', '"', '\\'],
                "1;2;3\n\n",
            ],
            [
                [
                    [],
                ],
                null,
                [';', "'", '\\'],
                "\n",
            ],
            [
                [
                    [],
                ],
                [1, 2, 3],
                [';', "'", '\\'],
                "1;2;3\n\n",
            ],
            [
                [
                    [1, 2, 3],
                ],
                null,
                [',', '"', '\\'],
                "1,2,3\n",
            ],
            [
                [
                    [1, 2, 3],
                ],
                [1, 2, 3],
                [',', '"', '\\'],
                "1,2,3\n1,2,3\n",
            ],
            [
                [
                    [1, 2, 3],
                ],
                null,
                [';', '"', '\\'],
                "1;2;3\n",
            ],
            [
                [
                    [1, 2, 3],
                ],
                [1, 2, 3],
                [';', '"', '\\'],
                "1;2;3\n1;2;3\n",
            ],
            [
                [
                    [1, 2, 3],
                ],
                null,
                [';', "'", '\\'],
                "1;2;3\n",
            ],
            [
                [
                    [1, 2, 3],
                ],
                [1, 2, 3],
                [';', "'", '\\'],
                "1;2;3\n1;2;3\n",
            ],
            [
                [
                    [1, 2, 3],
                    [4, 5, 6, 7],
                    [8, 9, 10, 11, 12],
                ],
                null,
                [',', '"', '\\'],
                "1,2,3\n4,5,6,7\n8,9,10,11,12\n",
            ],
            [
                [
                    [1, 2, 3],
                    [4, 5, 6, 7],
                    [8, 9, 10, 11, 12],
                ],
                [100, 200, 300],
                [',', '"', '\\'],
                "100,200,300\n1,2,3\n4,5,6,7\n8,9,10,11,12\n",
            ],
            [
                [
                    [1, 2, 3],
                    [4, 5, 6, 7],
                    [8, 9, 10, 11, 12],
                ],
                [100, 200, 300],
                [';', '"', '\\'],
                "100;200;300\n1;2;3\n4;5;6;7\n8;9;10;11;12\n",
            ],
            [
                [
                    [1, 2, 3],
                    [4, 5, 6, 7],
                    [8, 9, 10, 11, 12],
                ],
                null,
                [';', "'", '\\'],
                "1;2;3\n4;5;6;7\n8;9;10;11;12\n",
            ],
            [
                [
                    [1, 2, 3],
                    [4, 5, 6, 7],
                    [8, 9, 10, 11, 12],
                ],
                [100, 200, 300],
                [';', "'", '\\'],
                "100;200;300\n1;2;3\n4;5;6;7\n8;9;10;11;12\n",
            ],
            [
                [
                    ["a, b, c"],
                    ["d, e, f", "g, h, i"],
                    ["j, k, l", "m, n, o", "p, q, r"],
                ],
                ['x, y, z', '1, 2, 3'],
                [',', '"', '\\'],
                "\"x, y, z\",\"1, 2, 3\"\n\"a, b, c\"\n\"d, e, f\",\"g, h, i\"\n\"j, k, l\",\"m, n, o\",\"p, q, r\"\n",
            ],
            [
                [
                    ["a, b, c"],
                    ["d, e, f", "g, h, i"],
                    ["j, k, l", "m, n, o", "p, q, r"],
                ],
                null,
                [';', '"', '\\'],
                "\"a, b, c\"\n\"d, e, f\";\"g, h, i\"\n\"j, k, l\";\"m, n, o\";\"p, q, r\"\n",
            ],
            [
                [
                    ["a, b, c"],
                    ["d, e, f", "g, h, i"],
                    ["j, k, l", "m, n, o", "p, q, r"],
                ],
                ['x, y, z', '1, 2, 3'],
                [';', '"', '\\'],
                "\"x, y, z\";\"1, 2, 3\"\n\"a, b, c\"\n\"d, e, f\";\"g, h, i\"\n\"j, k, l\";\"m, n, o\";\"p, q, r\"\n",
            ],
            [
                [
                    ["a, b, c"],
                    ["d, e, f", "g, h, i"],
                    ["j, k, l", "m, n, o", "p, q, r"],
                ],
                null,
                [';', "'", '\\'],
                "'a, b, c'\n'd, e, f';'g, h, i'\n'j, k, l';'m, n, o';'p, q, r'\n",
            ],
            [
                [
                    ["a, b, c"],
                    ["d, e, f", "g, h, i"],
                    ["j, k, l", "m, n, o", "p, q, r"],
                ],
                ['x, y, z', '1, 2, 3'],
                [';', "'", '\\'],
                "'x, y, z';'1, 2, 3'\n'a, b, c'\n'd, e, f';'g, h, i'\n'j, k, l';'m, n, o';'p, q, r'\n",
            ],
            [
                [
                    ["a, b, c"],
                    ["d, e, f", "g, h, i"],
                    ["j, k, l", "m, n, o", "p, q, r"],
                    [1, 2, 3],
                ],
                null,
                [',', '"', '\\'],
                "\"a, b, c\"\n\"d, e, f\",\"g, h, i\"\n\"j, k, l\",\"m, n, o\",\"p, q, r\"\n1,2,3\n",
            ],
            [
                [
                    ["a, b, c"],
                    ["d, e, f", "g, h, i"],
                    ["j, k, l", "m, n, o", "p, q, r"],
                    [1, 2, 3],
                ],
                ['x, y, z', '1, 2, 3'],
                [',', '"', '\\'],
                "\"x, y, z\",\"1, 2, 3\"\n\"a, b, c\"\n\"d, e, f\",\"g, h, i\"\n\"j, k, l\",\"m, n, o\",\"p, q, r\"\n1,2,3\n",
            ],
            [
                [
                    ["a, b, c"],
                    ["d, e, f", "g, h, i"],
                    ["j, k, l", "m, n, o", "p, q, r"],
                    [1, 2, 3],
                ],
                null,
                [';', '"', '\\'],
                "\"a, b, c\"\n\"d, e, f\";\"g, h, i\"\n\"j, k, l\";\"m, n, o\";\"p, q, r\"\n1;2;3\n",
            ],
            [
                [
                    ["a, b, c"],
                    ["d, e, f", "g, h, i"],
                    ["j, k, l", "m, n, o", "p, q, r"],
                    [1, 2, 3],
                ],
                ['x, y, z', '1, 2, 3'],
                [';', '"', '\\'],
                "\"x, y, z\";\"1, 2, 3\"\n\"a, b, c\"\n\"d, e, f\";\"g, h, i\"\n\"j, k, l\";\"m, n, o\";\"p, q, r\"\n1;2;3\n",
            ],
            [
                [
                    ["a, b, c"],
                    ["d, e, f", "g, h, i"],
                    ["j, k, l", "m, n, o", "p, q, r"],
                    [1, 2, 3],
                ],
                null,
                [';', "'", '\\'],
                "'a, b, c'\n'd, e, f';'g, h, i'\n'j, k, l';'m, n, o';'p, q, r'\n1;2;3\n",
            ],
            [
                [
                    ["a, b, c"],
                    ["d, e, f", "g, h, i"],
                    ["j, k, l", "m, n, o", "p, q, r"],
                    [1, 2, 3],
                ],
                ['x, y, z', '1, 2, 3'],
                [';', "'", '\\'],
                "'x, y, z';'1, 2, 3'\n'a, b, c'\n'd, e, f';'g, h, i'\n'j, k, l';'m, n, o';'p, q, r'\n1;2;3\n",
            ],
        ];
    }

    public function testToFileError(): void
    {
        // Given
        $stream = Stream::of([1, 2, 3]);
        $filePath = $this->root->url().'/'.uniqid();
        $file = \fopen($filePath, 'w');

        // When
        \fclose($file);

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $stream->toFile($file);
    }

    public function testToCsvFileError(): void
    {
        // Given
        $stream = Stream::of([1, 2, 3]);
        $filePath = $this->root->url().'/'.uniqid();
        $file = \fopen($filePath, 'w');

        // When
        \fclose($file);

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $stream->toCsvFile($file);
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Exceptions\FileException;
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
     * @throws FileException
     */
    public function testToFileWithDefaultConfig(array $lines, string $expected): void
    {
        // Given
        $stream = Stream::of($lines);
        $filePath = $this->root->url().'/'.uniqid();

        // When
        $stream->toFile($filePath);
        $fileContents = file_get_contents($filePath);

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
     * @throws FileException
     */
    public function testToFileWithCustomConfig(array $lines, array $config, string $expected): void
    {
        // Given
        $stream = Stream::of($lines);
        $filePath = $this->root->url().'/'.uniqid();

        // When
        $stream->toFile($filePath, ...$config);
        $fileContents = file_get_contents($filePath);

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
                ["\n", "", ""],
                "",
            ],
            [
                [],
                ["\n", "a", ""],
                "a",
            ],
            [
                [],
                ["\n", "", "b"],
                "b",
            ],
            [
                [],
                ["\n", "hel", "lo"],
                "hello",
            ],
            [
                [],
                ["\t"],
                "",
            ],
            [
                [],
                ["\t", "good ", "bie"],
                "good bie",
            ],
            [
                [123],
                ["\n"],
                "123",
            ],
            [
                [123],
                ["\n", "", ""],
                "123",
            ],
            [
                [123],
                ["\n", "a", ""],
                "a123",
            ],
            [
                [123],
                ["\n", "", "b"],
                "123b",
            ],
            [
                [123],
                ["\n", "a", "b"],
                "a123b",
            ],
            [
                [123],
                ["\n", "", "\n"],
                "123\n",
            ],
            [
                [123],
                ["\t"],
                "123",
            ],
            [
                [123],
                ["\t", "a", "b"],
                "a123b",
            ],
            [
                ['123'],
                ["\n"],
                "123",
            ],
            [
                ['123'],
                ["\n", "\n", "\n"],
                "\n123\n",
            ],
            [
                ['123'],
                ["\t"],
                "123",
            ],
            [
                ['123'],
                ["\t", "\n", "\n"],
                "\n123\n",
            ],
            [
                [123, 456, 789],
                ["\n"],
                "123\n456\n789",
            ],
            [
                [123, 456, 789],
                ["\n", "\t", "--"],
                "\t123\n456\n789--",
            ],
            [
                [123, 456, 789],
                ["\t"],
                "123\t456\t789",
            ],
            [
                [123, 456, 789],
                ["\t", "\t", "\t"],
                "\t123\t456\t789\t",
            ],
            [
                ['123', '456', '789'],
                ["\n"],
                "123\n456\n789",
            ],
            [
                ['123', '456', '789'],
                ["\n", "\t", "\t"],
                "\t123\n456\n789\t",
            ],
            [
                ['123', '456', '789'],
                ["\n", "<<", ">>"],
                "<<123\n456\n789>>",
            ],
            [
                ['123', '456', '789'],
                ["\t"],
                "123\t456\t789",
            ],
            [
                ['123', '456', '789'],
                ["\t", "(", ")"],
                "(123\t456\t789)",
            ],
            [
                ["123\n", "45\n6", "\n789"],
                ["\n"],
                "123\n\n45\n6\n\n789",
            ],
            [
                ["123\n", "45\n6", "\n789"],
                ["\n", "\n", "\n"],
                "\n123\n\n45\n6\n\n789\n",
            ],
            [
                ["123\n", "45\n6", "\n789"],
                ["\t", "\n", "\n"],
                "\n123\n\t45\n6\t\n789\n",
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
                "\t123\t\t45\t6\t\t789\t",
            ],
            [
                ['a', 'b', 'c'],
                ["-"],
                "a-b-c",
            ],
            [
                ['a', 'b', 'c'],
                ["\n", "", "\n"],
                "a\nb\nc\n",
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForToCsvFileWithDefaultConfig
     * @param array $rows
     * @param string $expected
     * @return void
     * @throws FileException
     */
    public function testToCsvFileWithDefaultConfig(array $rows, string $expected): void
    {
        // Given
        $stream = Stream::of($rows);
        $filePath = $this->root->url().'/'.uniqid();

        // When
        $stream->toCsvFile($filePath);
        $fileContents = file_get_contents($filePath);

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
     * @param array{string, string, string} $config
     * @param string $expected
     * @return void
     * @throws FileException
     */
    public function testToCsvFileWithCustomConfig(array $rows, array $config, string $expected): void
    {
        // Given
        $stream = Stream::of($rows);
        $filePath = $this->root->url().'/'.uniqid();

        // When
        $stream->toCsvFile($filePath, ...$config);
        $fileContents = file_get_contents($filePath);

        // Then
        $this->assertEquals($expected, $fileContents);
    }

    public function dataProviderForToCsvFileWithCustomConfig(): array
    {
        return [
            [
                [],
                [',', '"', '\\'],
                "",
            ],
            [
                [],
                [';', '"', '\\'],
                "",
            ],
            [
                [],
                [';', "'", '\\'],
                "",
            ],
            [
                [
                    [],
                ],
                [',', '"', '\\'],
                "\n",
            ],
            [
                [
                    [],
                ],
                [';', '"', '\\'],
                "\n",
            ],
            [
                [
                    [],
                ],
                [';', "'", '\\'],
                "\n",
            ],
            [
                [
                    [1, 2, 3],
                ],
                [',', '"', '\\'],
                "1,2,3\n",
            ],
            [
                [
                    [1, 2, 3],
                ],
                [';', '"', '\\'],
                "1;2;3\n",
            ],
            [
                [
                    [1, 2, 3],
                ],
                [';', "'", '\\'],
                "1;2;3\n",
            ],
            [
                [
                    [1, 2, 3],
                    [4, 5, 6, 7],
                    [8, 9, 10, 11, 12],
                ],
                [',', '"', '\\'],
                "1,2,3\n4,5,6,7\n8,9,10,11,12\n",
            ],
            [
                [
                    [1, 2, 3],
                    [4, 5, 6, 7],
                    [8, 9, 10, 11, 12],
                ],
                [';', '"', '\\'],
                "1;2;3\n4;5;6;7\n8;9;10;11;12\n",
            ],
            [
                [
                    [1, 2, 3],
                    [4, 5, 6, 7],
                    [8, 9, 10, 11, 12],
                ],
                [';', "'", '\\'],
                "1;2;3\n4;5;6;7\n8;9;10;11;12\n",
            ],
            [
                [
                    ["a, b, c"],
                    ["d, e, f", "g, h, i"],
                    ["j, k, l", "m, n, o", "p, q, r"],
                ],
                [',', '"', '\\'],
                "\"a, b, c\"\n\"d, e, f\",\"g, h, i\"\n\"j, k, l\",\"m, n, o\",\"p, q, r\"\n",
            ],
            [
                [
                    ["a, b, c"],
                    ["d, e, f", "g, h, i"],
                    ["j, k, l", "m, n, o", "p, q, r"],
                ],
                [';', '"', '\\'],
                "\"a, b, c\"\n\"d, e, f\";\"g, h, i\"\n\"j, k, l\";\"m, n, o\";\"p, q, r\"\n",
            ],
            [
                [
                    ["a, b, c"],
                    ["d, e, f", "g, h, i"],
                    ["j, k, l", "m, n, o", "p, q, r"],
                ],
                [';', "'", '\\'],
                "'a, b, c'\n'd, e, f';'g, h, i'\n'j, k, l';'m, n, o';'p, q, r'\n",
            ],
            [
                [
                    ["a, b, c"],
                    ["d, e, f", "g, h, i"],
                    ["j, k, l", "m, n, o", "p, q, r"],
                    [1, 2, 3],
                ],
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
                [';', "'", '\\'],
                "'a, b, c'\n'd, e, f';'g, h, i'\n'j, k, l';'m, n, o';'p, q, r'\n1;2;3\n",
            ],
        ];
    }
}

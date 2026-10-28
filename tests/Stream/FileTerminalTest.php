<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;
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
     * @test toFile example usage
     */
    public function testToFileExampleUsage(): void
    {
        // Given
        $data = ['item1', 'item2', 'item3'];

        // And
        $header = '<ul>';
        $footer = '</ul>';

        // And
        $filePath = $this->root->url().'/'.uniqid();
        $fh       = \fopen($filePath, 'w');

        // When
        Stream::of($data)
            ->map(fn ($item) => "  <li>$item</li>")
            ->toFile($fh, \PHP_EOL, $header, $footer);
        $resultWrittenToFile = \file_get_contents($filePath);

        // Then
        $expected = <<<'END_OF_EXPECTED'
<ul>
  <li>item1</li>
  <li>item2</li>
  <li>item3</li>
</ul>
END_OF_EXPECTED;

        $this->assertEquals($expected, $resultWrittenToFile);
    }

    /**
     * @test toCsvFile example usage
     */
    public function testToCsvFileExampleUsage(): void
    {
        // Given
        $data = [
            ['Star Wars: Episode IV – A New Hope', 'IV', 1977],
            ['Star Wars: Episode V – The Empire Strikes Back', 'V', 1980],
            ['Star Wars: Episode VI – Return of the Jedi', 'VI', 1983],
        ];

        // And
        $header = ['title', 'episode', 'year'];

        // And
        $filePath = $this->root->url().'/'.uniqid();
        $fh       = \fopen($filePath, 'w');

        // When
        Stream::of($data)
            ->toCsvFile($fh, $header);
        $resultWrittenToFile = \file_get_contents($filePath);

        // Then
        $expected = <<<'END_OF_EXPECTED'
title,episode,year
"Star Wars: Episode IV – A New Hope",IV,1977
"Star Wars: Episode V – The Empire Strikes Back",V,1980
"Star Wars: Episode VI – Return of the Jedi",VI,1983

END_OF_EXPECTED;

        $this->assertEquals($expected, $resultWrittenToFile);
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

    public static function dataProviderForToFileWithDefaultConfig(): array
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
            [
                [1, 2.2, 'words', \NAN, \INF, true, false, null, 'the end'],
                "1\n2.2\nwords\nNAN\nINF\n1\n\n\nthe end",
            ]
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

    public static function dataProviderForToFileWithCustomConfig(): array
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

    /**
     * @test toCsvFile throws TypeError for a row that isn't an array, without a warning first
     */
    public function testToCsvFileRowNotArray(): void
    {
        // Given
        $stream   = Stream::of([[1, 2], 5]);
        $file     = \fopen($this->root->url() . '/' . \uniqid(), 'w');
        $raised   = [];
        $previous = \set_error_handler(static function (int $errno, string $errstr) use (&$raised): bool {
            $raised[] = $errstr;
            return true;
        });

        // When
        $thrown = null;
        try {
            $stream->toCsvFile($file);
        } catch (\TypeError $e) {
            $thrown = $e;
        } finally {
            \set_error_handler($previous);
        }

        // Then
        $this->assertInstanceOf(\TypeError::class, $thrown);
        $this->assertSame([], $raised);
    }

    public static function dataProviderForToCsvFileWithDefaultConfig(): array
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

    public static function dataProviderForToCsvFileWithCustomConfig(): array
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

    /**
     * @test toFile throws when the write is truncated
     */
    public function testToFileTruncatedWriteThrows(): void
    {
        // Given
        $file = \fopen($this->root->url() . '/' . \uniqid(), 'w');
        vfsStream::setQuota(3);

        // Then
        $this->expectException(\RuntimeException::class);

        // When
        Stream::of(['abcdef'])->toFile($file);
    }

    /**
     * @test toFile throws when the header write is truncated
     */
    public function testToFileTruncatedHeaderThrows(): void
    {
        // Given
        $file = \fopen($this->root->url() . '/' . \uniqid(), 'w');
        vfsStream::setQuota(3);

        // Then
        $this->expectException(\RuntimeException::class);

        // When
        Stream::of([])->toFile($file, "\n", 'abcdef');
    }

    /**
     * @test toFile throws when the footer write is truncated
     */
    public function testToFileTruncatedFooterThrows(): void
    {
        // Given
        $file = \fopen($this->root->url() . '/' . \uniqid(), 'w');
        vfsStream::setQuota(3);

        // Then
        $this->expectException(\RuntimeException::class);

        // When
        Stream::of([])->toFile($file, "\n", null, 'abcdef');
    }

    /**
     * @test toCsvFile throws when a row write is truncated
     */
    public function testToCsvFileTruncatedRowThrows(): void
    {
        // Given
        $file = \fopen($this->root->url() . '/' . \uniqid(), 'w');
        vfsStream::setQuota(3);

        // Then
        $this->expectException(\RuntimeException::class);

        // When
        Stream::of([['abcdef', 'x']])->toCsvFile($file);
    }

    /**
     * @test toCsvFile throws when the header write is truncated
     */
    public function testToCsvFileTruncatedHeaderThrows(): void
    {
        // Given
        $file = \fopen($this->root->url() . '/' . \uniqid(), 'w');
        vfsStream::setQuota(3);

        // Then
        $this->expectException(\RuntimeException::class);

        // When
        Stream::of([])->toCsvFile($file, ['abcdef', 'x']);
    }

    /**
     * @test         toCsvFile formats NAN fields as 'NAN' without raising PHP 8.5's NAN-to-string warning
     * @dataProvider dataProviderForToCsvFileNan
     * @param        iterable<array<scalar>> $rows
     */
    public function testToCsvFileNan(iterable $rows, string $expected): void
    {
        // Given
        $stream   = Stream::of($rows);
        $filePath = $this->root->url() . '/' . uniqid();
        $file     = \fopen($filePath, 'w');
        $raised   = [];
        $previous = \set_error_handler(static function (int $errno, string $errstr) use (&$raised): bool {
            $raised[] = $errstr;
            return true;
        });

        try {
            // When
            $stream->toCsvFile($file);
        } finally {
            \set_error_handler($previous);
        }

        // Then
        $this->assertSame([], $raised);
        $this->assertSame($expected, \file_get_contents($filePath));
    }

    public static function dataProviderForToCsvFileNan(): array
    {
        return [
            [
                [[1, \NAN, 2.5], [\NAN, 'a', \INF]],
                "1,NAN,2.5\nNAN,a,INF\n",
            ],
            [
                GeneratorFixture::getGenerator([[1, \NAN, 2.5], [\NAN, 'a', \INF]]),
                "1,NAN,2.5\nNAN,a,INF\n",
            ],
            [
                new ArrayIteratorFixture([[1, \NAN, 2.5], [\NAN, 'a', \INF]]),
                "1,NAN,2.5\nNAN,a,INF\n",
            ],
            [
                new IteratorAggregateFixture([[1, \NAN, 2.5], [\NAN, 'a', \INF]]),
                "1,NAN,2.5\nNAN,a,INF\n",
            ],
        ];
    }

    /**
     * @test toFile rejects a resource that is not a stream, such as a stream context, even with nothing to write
     */
    public function testToFileStreamContextThrows(): void
    {
        // Given
        $context = \stream_context_create();

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of([])->toFile($context);
    }

    /**
     * @test toCsvFile rejects a resource that is not a stream, such as a stream context, even with nothing to write
     */
    public function testToCsvFileStreamContextThrows(): void
    {
        // Given
        $context = \stream_context_create();

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        Stream::of([])->toCsvFile($context);
    }
}

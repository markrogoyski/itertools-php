<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\FileFixture;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class FileSourceTest extends \PHPUnit\Framework\TestCase
{
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('test');
    }

    /**
     * @dataProvider dataProviderForOfCsvFileByDefault
     * @param        array $lines
     * @param        array $expected
     */
    public function testOfCsvFileByDefault(array $lines, array $expected): void
    {
        // Given
        $file = FileFixture::createFromLines($lines, $this->root->url());

        // When
        $stream = Stream::ofCsvFile($file);

        // Then
        $this->assertEquals($expected, $stream->toArray());
    }

    public function dataProviderForOfCsvFileByDefault(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                [''],
                [],
            ],
            [
                ['1'],
                [['1']],
            ],
            [
                ['1,2,3'],
                [['1', '2', '3']],
            ],
            [
                [
                    '1,2,3',
                    '',
                ],
                [['1', '2', '3']],
            ],
            [
                [
                    '1,2,3',
                    '',
                    '4,5,6',
                ],
                [
                    ['1', '2', '3'],
                    [null], // TODO WTF fgetcsv()? Is it the expected behavior? I do not like it!
                    ['4', '5', '6'],
                ],
            ],
            [
                [
                    '1,2,3',
                    ' ',
                ],
                [
                    ['1', '2', '3'],
                    [' '],
                ],
            ],
            [
                [
                    '1,2,3',
                    '2,3,4',
                ],
                [
                    ['1', '2', '3'],
                    ['2', '3', '4'],
                ],
            ],
            [
                [
                    '1,2,3,',
                    '2,3,4',
                ],
                [
                    ['1', '2', '3', ''],
                    ['2', '3', '4'],
                ],
            ],
            [
                [
                    '1,2,3,""',
                    '2,3,4',
                ],
                [
                    ['1', '2', '3', ''],
                    ['2', '3', '4'],
                ],
            ],
            [
                [
                    '1,2,3',
                    '2,3,4',
                    '5,6',
                ],
                [
                    ['1', '2', '3'],
                    ['2', '3', '4'],
                    ['5', '6'],
                ],
            ],
            [
                [
                    '1,2,"3"',
                    '2,"3",4',
                    '"5",6',
                ],
                [
                    ['1', '2', '3'],
                    ['2', '3', '4'],
                    ['5', '6'],
                ],
            ],
            [
                [
                    '1,2,"3"',
                    '2,"\n3",4',
                    '"5",6',
                ],
                [
                    ['1', '2', '3'],
                    ['2', '\n3', '4'],
                    ['5', '6'],
                ],
            ],
            [
                [
                    '1,2,"3"',
                    '2,"""3""",4',
                    '"5",6',
                ],
                [
                    ['1', '2', '3'],
                    ['2', '"3"', '4'],
                    ['5', '6'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForOfCsvFileWithConfig
     * @param        array $lines
     * @param        array $config
     * @param        array $expected
     */
    public function testOfCsvFileWithConfig(array $lines, array $config, array $expected): void
    {
        // Given
        $file = FileFixture::createFromLines($lines, $this->root->url());
        [$separator, $enclosure, $escape] = $config;

        // When
        $stream = Stream::ofCsvFile($file, $separator, $enclosure, $escape);

        // Then
        $this->assertEquals($expected, $stream->toArray());
    }

    public function dataProviderForOfCsvFileWithConfig(): array
    {
        return [
            [
                [],
                [",", "\"", "\\"],
                [],
            ],
            [
                [],
                [";", "\"", "\\"],
                [],
            ],
            [
                [],
                [",", "'", "\\"],
                [],
            ],
            [
                [],
                [",", "\"", "/"],
                [],
            ],
            [
                [],
                [",", "\"", ""],
                [],
            ],
            [
                ['1,2,3'],
                [",", "\"", "\\"],
                [['1', '2', '3']],
            ],
            [
                ['1;2;3'],
                [",", "\"", "\\"],
                [['1;2;3']],
            ],
            [
                ['1,2,3'],
                [";", "\"", "\\"],
                [['1,2,3']],
            ],
            [
                ['1;2;3'],
                [";", "\"", "\\"],
                [['1', '2', '3']],
            ],
            [
                ["1,'2',3"],
                [",", "'", "\\"],
                [['1', '2', '3']],
            ],
            [
                ["'1','2','3'"],
                [",", "'", "\\"],
                [['1', '2', '3']],
            ],
            [
                [
                    '1,2,3',
                    '4,5,6',
                ],
                [",", "\"", "\\"],
                [
                    ['1', '2', '3'],
                    ['4', '5', '6'],
                ],
            ],
            [
                [
                    '1;2;3',
                    '4;5;6',
                ],
                [",", "\"", "\\"],
                [
                    ['1;2;3'],
                    ['4;5;6'],
                ],
            ],
            [
                [
                    '1,2,3',
                    '4,5,6',
                ],
                [";", "\"", "\\"],
                [
                    ['1,2,3'],
                    ['4,5,6'],
                ],
            ],
            [
                [
                    '1;2;3',
                    '4;5;6',
                ],
                [";", "\"", "\\"],
                [
                    ['1', '2', '3'],
                    ['4', '5', '6'],
                ],
            ],
            [
                [
                    "1,'2',3",
                    "4,'5','6'",
                ],
                [",", "'", "\\"],
                [
                    ['1', '2', '3'],
                    ['4', '5', '6'],
                ],
            ],
            [
                [
                    "'1','2','3'",
                    "'4','5','6'",
                ],
                [",", "'", "\\"],
                [
                    ['1', '2', '3'],
                    ['4', '5', '6'],
                ],
            ],
            // FIXME I do not know how to test the $escape parameter :(
            [
                [
                    '\t,\n,\t',
                    '\n,\\,\t',
                ],
                [",", "'", "\\"],
                [
                    ['\t', '\n', '\t'],
                    ['\n', '\\', '\t'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForOfFileLines
     * @param        array $lines
     * @param        array $expected
     */
    public function testOfFileLines(array $lines, array $expected): void
    {
        // Given
        $file = FileFixture::createFromLines($lines, $this->root->url());

        // When
        $stream = Stream::ofFileLines($file);

        // Then
        $this->assertEquals($expected, $stream->toArray());
    }

    public function dataProviderForOfFileLines(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                [''],
                [],
            ],
            [
                ['123'],
                ['123'],
            ],
            [
                ['123', '456', '78'],
                ["123\n", "456\n", '78'],
            ],
            [
                ['123', '456', ''],
                ["123\n", "456\n"],
            ],
            [
                ['123', '', ''],
                ["123\n", "\n"],
            ],
            [
                ['123', '', '456'],
                ["123\n", "\n", "456"],
            ],
            [
                ['', '', '456'],
                ["\n", "\n", "456"],
            ],
            [
                ['', '', ''],
                ["\n", "\n"],
            ],
        ];
    }

    /**
     * @return void
     */
    public function testOfFileLinesError(): void
    {
        // Given
        $file = FileFixture::createFromLines([], $this->root->url());

        // When
        fclose($file);

        // Then
        $this->expectException(\UnexpectedValueException::class);

        Stream::ofFileLines($file)->toArray();
    }

    /**
     * @return void
     */
    public function testOfCsvFileError(): void
    {
        // Given
        $file = FileFixture::createFromLines([], $this->root->url());

        // When
        fclose($file);

        // Then
        $this->expectException(\UnexpectedValueException::class);

        Stream::ofCsvFile($file)->toArray();
    }
}

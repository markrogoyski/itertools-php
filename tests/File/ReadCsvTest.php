<?php

declare(strict_types=1);

namespace IterTools\Tests\File;

use IterTools\File;
use IterTools\Tests\Fixture\FileFixture;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class ReadCsvTest extends \PHPUnit\Framework\TestCase
{
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('test');
    }

    /**
     * @dataProvider dataProviderForByDefault
     * @param        array $lines
     * @param        array $expected
     */
    public function testByDefault(array $lines, array $expected): void
    {
        // Given
        $file = FileFixture::createFromLines($lines, $this->root->url());
        $result = [];

        // When
        foreach (File::readCsv($file) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForByDefault(): array
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
     * @dataProvider dataProviderForWithConfig
     * @param        array $lines
     * @param        array $config
     * @param        array $expected
     */
    public function testWithConfig(array $lines, array $config, array $expected): void
    {
        // Given
        $file = FileFixture::createFromLines($lines, $this->root->url());
        $result = [];
        [$separator, $enclosure, $escape] = $config;

        // When
        foreach (File::readCsv($file, $separator, $enclosure, $escape) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertEquals($expected, $result);
    }

    public function dataProviderForWithConfig(): array
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
            [
                [
                    "\\,\",a\040a",
                    "\\,\",a\040a",
                ],
                [",", "'", "\\"],
                [
                    ['\\', '"', 'a a'],
                    ['\\', '"', 'a a'],
                ],
            ],
        ];
    }

    /**
     * @return void
     */
    public function testError(): void
    {
        // Given
        $file = FileFixture::createFromLines([], $this->root->url());

        // When
        \fclose($file);

        // Then
        $this->expectException(\InvalidArgumentException::class);
        foreach (File::readCsv($file) as $_) {
            break;
        }
    }
}
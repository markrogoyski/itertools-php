<?php

declare(strict_types=1);

namespace IterTools\Tests\File;

use IterTools\File;
use IterTools\Tests\Fixture\FileFixture;

class ReadCsvTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderForByDefault
     * @param        resource $file
     * @param        array $expected
     */
    public function testByDefault($file, array $expected): void
    {
        // Given
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
                $file(['1']),
                [['1']],
            ],
            [
                $file(['1,2,3']),
                [['1', '2', '3']],
            ],
            [
                $file([
                    '1,2,3',
                    '',
                ]),
                [['1', '2', '3']],
            ],
            [
                $file([
                    '1,2,3',
                    '',
                    '4,5,6',
                ]),
                [
                    ['1', '2', '3'],
                    [null], // TODO WTF fgetcsv()? Is it the expected behavior? I do not like it!
                    ['4', '5', '6'],
                ],
            ],
            [
                $file([
                    '1,2,3',
                    ' ',
                ]),
                [
                    ['1', '2', '3'],
                    [' '],
                ],
            ],
            [
                $file([
                    '1,2,3',
                    '2,3,4',
                ]),
                [
                    ['1', '2', '3'],
                    ['2', '3', '4'],
                ],
            ],
            [
                $file([
                    '1,2,3,',
                    '2,3,4',
                ]),
                [
                    ['1', '2', '3', ''],
                    ['2', '3', '4'],
                ],
            ],
            [
                $file([
                    '1,2,3,""',
                    '2,3,4',
                ]),
                [
                    ['1', '2', '3', ''],
                    ['2', '3', '4'],
                ],
            ],
            [
                $file([
                    '1,2,3',
                    '2,3,4',
                    '5,6',
                ]),
                [
                    ['1', '2', '3'],
                    ['2', '3', '4'],
                    ['5', '6'],
                ],
            ],
            [
                $file([
                    '1,2,"3"',
                    '2,"3",4',
                    '"5",6',
                ]),
                [
                    ['1', '2', '3'],
                    ['2', '3', '4'],
                    ['5', '6'],
                ],
            ],
            [
                $file([
                    '1,2,"3"',
                    '2,"\n3",4',
                    '"5",6',
                ]),
                [
                    ['1', '2', '3'],
                    ['2', '\n3', '4'],
                    ['5', '6'],
                ],
            ],
            [
                $file([
                    '1,2,"3"',
                    '2,"""3""",4',
                    '"5",6',
                ]),
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
     * @param        resource $file
     * @param        array $config
     * @param        array $expected
     */
    public function testWithConfig($file, array $config, array $expected): void
    {
        // Given
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
        $file = fn (array $lines) => FileFixture::createFromLines($lines);

        return [
            [
                $file([]),
                [",", "\"", "\\"],
                [],
            ],
            [
                $file([]),
                [";", "\"", "\\"],
                [],
            ],
            [
                $file([]),
                [",", "'", "\\"],
                [],
            ],
            [
                $file([]),
                [",", "\"", "/"],
                [],
            ],
            [
                $file([]),
                [",", "\"", ""],
                [],
            ],
            [
                $file(['1,2,3']),
                [",", "\"", "\\"],
                [['1', '2', '3']],
            ],
            [
                $file(['1;2;3']),
                [",", "\"", "\\"],
                [['1;2;3']],
            ],
            [
                $file(['1,2,3']),
                [";", "\"", "\\"],
                [['1,2,3']],
            ],
            [
                $file(['1;2;3']),
                [";", "\"", "\\"],
                [['1', '2', '3']],
            ],
            [
                $file(["1,'2',3"]),
                [",", "'", "\\"],
                [['1', '2', '3']],
            ],
            [
                $file(["'1','2','3'"]),
                [",", "'", "\\"],
                [['1', '2', '3']],
            ],
            [
                $file([
                    '1,2,3',
                    '4,5,6',
                ]),
                [",", "\"", "\\"],
                [
                    ['1', '2', '3'],
                    ['4', '5', '6'],
                ],
            ],
            [
                $file([
                    '1;2;3',
                    '4;5;6',
                ]),
                [",", "\"", "\\"],
                [
                    ['1;2;3'],
                    ['4;5;6'],
                ],
            ],
            [
                $file([
                    '1,2,3',
                    '4,5,6',
                ]),
                [";", "\"", "\\"],
                [
                    ['1,2,3'],
                    ['4,5,6'],
                ],
            ],
            [
                $file([
                    '1;2;3',
                    '4;5;6',
                ]),
                [";", "\"", "\\"],
                [
                    ['1', '2', '3'],
                    ['4', '5', '6'],
                ],
            ],
            [
                $file([
                    "1,'2',3",
                    "4,'5','6'",
                ]),
                [",", "'", "\\"],
                [
                    ['1', '2', '3'],
                    ['4', '5', '6'],
                ],
            ],
            [
                $file([
                    "'1','2','3'",
                    "'4','5','6'",
                ]),
                [",", "'", "\\"],
                [
                    ['1', '2', '3'],
                    ['4', '5', '6'],
                ],
            ],
            // FIXME I do not know how to test the $escape parameter :(
            [
                $file([
                    '\t,\n,\t',
                    '\n,\\,\t',
                ]),
                [",", "'", "\\"],
                [
                    ['\t', '\n', '\t'],
                    ['\n', '\\', '\t'],
                ],
            ],
        ];
    }

    /**
     * @return void
     */
    public function testErrorOnStart(): void
    {
        // Given
        $file = FileFixture::createFromLines([]);

        // When
        fclose($file);

        // Then
        $this->expectException(\UnexpectedValueException::class);
        foreach (File::readCsv($file) as $_) {
            break;
        }
    }

    /**
     * @return void
     */
    public function testErrorOnProcess(): void
    {
        /// Given
        $file = FileFixture::createFromLines(['123', '456']);

        // Then
        $this->expectException(\UnexpectedValueException::class);
        foreach (File::readCsv($file) as $_) {
            // When
            fclose($file);
        }
    }
}

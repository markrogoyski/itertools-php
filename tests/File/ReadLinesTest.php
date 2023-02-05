<?php

declare(strict_types=1);

namespace IterTools\Tests\File;

use IterTools\File;
use IterTools\Tests\Fixture\FileFixture;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class ReadLinesTest extends \PHPUnit\Framework\TestCase
{
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('test');
    }

    /**
     * @test readLines example usage
     */
    public function testReadCsvExampleUseCase(): void
    {
        // Given
        $fileText = <<<'CSV_END'
This is the first line.
This is the second line.
Third line goes here.
四番目の行はここですよ。
And the last line.
CSV_END;
        $file = FileFixture::createFromString($fileText, $this->root->url());

        // When
        $result = [];
        foreach (File::readLines($file) as $line) {
            $result[] = $line;
        }

        // Then
        $expected = [
            "This is the first line.\n",
            "This is the second line.\n",
            "Third line goes here.\n",
            "四番目の行はここですよ。\n",
            "And the last line.",
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider dataProviderForCommon
     * @param        array $lines
     * @param        array $expected
     */
    public function testCommon(array $lines, array $expected): void
    {
        // Given
        $file = FileFixture::createFromLines($lines, $this->root->url());
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
    public function testErrorOnStart(): void
    {
        // Given
        $file = FileFixture::createFromLines([], $this->root->url());

        // When
        \fclose($file);

        // Then
        $this->expectException(\InvalidArgumentException::class);
        foreach (File::readLines($file) as $_) {
            break;
        }
    }
}

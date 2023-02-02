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
        fclose($file);

        // Then
        $this->expectException(\UnexpectedValueException::class);
        foreach (File::readLines($file) as $_) {
            break;
        }
    }
}

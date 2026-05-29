<?php

declare(strict_types=1);

namespace IterTools\Tests\File;

use IterTools\File;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class WriteLinesTest extends \PHPUnit\Framework\TestCase
{
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('test');
    }

    /**
     * @param resource $fileResource
     */
    private function readBack($fileResource): string
    {
        $meta = \stream_get_meta_data($fileResource);
        \fclose($fileResource);

        return \file_get_contents($meta['uri']);
    }

    /**
     * @return resource
     */
    private function writableFile()
    {
        return \fopen($this->root->url() . '/' . \uniqid(), 'w');
    }

    /**
     * @test writeLines example usage
     */
    public function testWriteLinesExampleUsage(): void
    {
        // Given
        $lines = ['The quick', 'brown fox', 'jumps over'];
        $file = $this->writableFile();

        // When
        File::writeLines($file, $lines, "\n");

        // Then
        $this->assertEquals("The quick\nbrown fox\njumps over", $this->readBack($file));
    }

    /**
     * @test         writeLines round-trips through readLines (array)
     * @dataProvider dataProviderForRoundTrip
     * @param        array<string> $lines
     */
    public function testWriteLinesArray(array $lines): void
    {
        // Given
        $file = $this->writableFile();

        // When
        File::writeLines($file, $lines, "\n");

        // Then
        $this->assertEquals($lines, $this->readLinesRtrimmed($file));
    }

    /**
     * @test         writeLines round-trips through readLines (Generator)
     * @dataProvider dataProviderForRoundTrip
     * @param        array<string> $lines
     */
    public function testWriteLinesGenerator(array $lines): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($lines);
        $file = $this->writableFile();

        // When
        File::writeLines($file, $iterable, "\n");

        // Then
        $this->assertEquals($lines, $this->readLinesRtrimmed($file));
    }

    /**
     * @test         writeLines round-trips through readLines (Iterator)
     * @dataProvider dataProviderForRoundTrip
     * @param        array<string> $lines
     */
    public function testWriteLinesIterator(array $lines): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($lines);
        $file = $this->writableFile();

        // When
        File::writeLines($file, $iterable, "\n");

        // Then
        $this->assertEquals($lines, $this->readLinesRtrimmed($file));
    }

    /**
     * @test         writeLines round-trips through readLines (IteratorAggregate)
     * @dataProvider dataProviderForRoundTrip
     * @param        array<string> $lines
     */
    public function testWriteLinesIteratorAggregate(array $lines): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($lines);
        $file = $this->writableFile();

        // When
        File::writeLines($file, $iterable, "\n");

        // Then
        $this->assertEquals($lines, $this->readLinesRtrimmed($file));
    }

    /**
     * Read back the lines via File::readLines and strip the trailing separator for round-trip comparison.
     *
     * @param resource $fileResource
     *
     * @return array<string>
     */
    private function readLinesRtrimmed($fileResource): array
    {
        $meta = \stream_get_meta_data($fileResource);
        \fclose($fileResource);

        $reader = \fopen($meta['uri'], 'r');
        $lines = [];
        foreach (File::readLines($reader) as $line) {
            $lines[] = \rtrim($line, "\n");
        }
        \fclose($reader);

        return $lines;
    }

    public static function dataProviderForRoundTrip(): array
    {
        return [
            [['single']],
            [['first', 'second']],
            [['a', 'b', 'c', 'd']],
            [['line with spaces', 'another line']],
        ];
    }

    /**
     * @test writeLines single line input has no trailing separator
     */
    public function testWriteLinesSingleLineNoTrailingSeparator(): void
    {
        // Given
        $file = $this->writableFile();

        // When
        File::writeLines($file, ['only line'], "\n");

        // Then
        $this->assertEquals('only line', $this->readBack($file));
    }

    /**
     * @test writeLines empty iterable produces a zero-byte file
     */
    public function testWriteLinesEmptyProducesZeroByteFile(): void
    {
        // Given
        $file = $this->writableFile();

        // When
        File::writeLines($file, []);

        // Then
        $this->assertEquals('', $this->readBack($file));
    }

    /**
     * @test         writeLines honors a custom line separator
     * @dataProvider dataProviderForCustomSeparator
     * @param        array<string> $lines
     * @param        string        $separator
     * @param        string        $expected
     */
    public function testWriteLinesCustomSeparator(array $lines, string $separator, string $expected): void
    {
        // Given
        $file = $this->writableFile();

        // When
        File::writeLines($file, $lines, $separator);

        // Then
        $this->assertEquals($expected, $this->readBack($file));
    }

    public static function dataProviderForCustomSeparator(): array
    {
        return [
            [['a', 'b', 'c'], "\t", "a\tb\tc"],
            [['a', 'b', 'c'], '-', 'a-b-c'],
            [['a', 'b', 'c'], '||', 'a||b||c'],
            [['x'], '-', 'x'],
        ];
    }

    /**
     * @test writeLines uses PHP_EOL as the default separator
     */
    public function testWriteLinesDefaultSeparator(): void
    {
        // Given
        $file = $this->writableFile();

        // When
        File::writeLines($file, ['a', 'b']);

        // Then
        $this->assertEquals('a' . \PHP_EOL . 'b', $this->readBack($file));
    }

    /**
     * @test writeLines rejects a non-resource argument
     */
    public function testWriteLinesError(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        File::writeLines('/not/a/resource', ['a', 'b']);
    }
}

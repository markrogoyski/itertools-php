<?php

declare(strict_types=1);

namespace IterTools\Tests\File;

use IterTools\File;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class WriteCsvTest extends \PHPUnit\Framework\TestCase
{
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('test');
    }

    /**
     * @return resource
     */
    private function writableFile()
    {
        return \fopen($this->root->url() . '/' . \uniqid(), 'w');
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
     * Read back the CSV rows via File::readCsv.
     *
     * @param resource $fileResource
     *
     * @return array<array<int, string|null>>
     */
    private function readCsvBack($fileResource): array
    {
        $meta = \stream_get_meta_data($fileResource);
        \fclose($fileResource);

        $reader = \fopen($meta['uri'], 'r');
        $rows = [];
        foreach (File::readCsv($reader) as $row) {
            $rows[] = $row;
        }
        \fclose($reader);

        return $rows;
    }

    /**
     * @test writeCsv example usage
     */
    public function testWriteCsvExampleUsage(): void
    {
        // Given
        $rows = [
            ['1', 'Star Wars', '1977'],
            ['2', 'The Empire Strikes Back', '1980'],
        ];
        $file = $this->writableFile();

        // When
        File::writeCsv($file, $rows, ['episode', 'title', 'year']);

        // Then
        $expected = "episode,title,year\n1,\"Star Wars\",1977\n2,\"The Empire Strikes Back\",1980\n";
        $this->assertEquals($expected, $this->readBack($file));
    }

    /**
     * @test         writeCsv round-trips through readCsv without header (array)
     * @dataProvider dataProviderForRoundTrip
     * @param        array<array<string>> $rows
     */
    public function testWriteCsvArray(array $rows): void
    {
        // Given
        $file = $this->writableFile();

        // When
        File::writeCsv($file, $rows);

        // Then
        $this->assertEquals($rows, $this->readCsvBack($file));
    }

    /**
     * @test         writeCsv round-trips through readCsv without header (Generator)
     * @dataProvider dataProviderForRoundTrip
     * @param        array<array<string>> $rows
     */
    public function testWriteCsvGenerator(array $rows): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($rows);
        $file = $this->writableFile();

        // When
        File::writeCsv($file, $iterable);

        // Then
        $this->assertEquals($rows, $this->readCsvBack($file));
    }

    /**
     * @test         writeCsv round-trips through readCsv without header (Iterator)
     * @dataProvider dataProviderForRoundTrip
     * @param        array<array<string>> $rows
     */
    public function testWriteCsvIterator(array $rows): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($rows);
        $file = $this->writableFile();

        // When
        File::writeCsv($file, $iterable);

        // Then
        $this->assertEquals($rows, $this->readCsvBack($file));
    }

    /**
     * @test         writeCsv round-trips through readCsv without header (IteratorAggregate)
     * @dataProvider dataProviderForRoundTrip
     * @param        array<array<string>> $rows
     */
    public function testWriteCsvIteratorAggregate(array $rows): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($rows);
        $file = $this->writableFile();

        // When
        File::writeCsv($file, $iterable);

        // Then
        $this->assertEquals($rows, $this->readCsvBack($file));
    }

    public static function dataProviderForRoundTrip(): array
    {
        return [
            [[]],
            [[['1']]],
            [[['1', '2', '3']]],
            [[['1', '2', '3'], ['4', '5', '6']]],
            [[['a', 'b'], ['c', 'd'], ['e', 'f']]],
        ];
    }

    /**
     * @test writeCsv round-trips through readCsv with a header row
     */
    public function testWriteCsvWithHeaderRoundTrip(): void
    {
        // Given
        $header = ['x', 'y', 'z'];
        $rows = [['1', '2', '3'], ['4', '5', '6']];
        $file = $this->writableFile();

        // When
        File::writeCsv($file, $rows, $header);

        // Then
        $expected = [$header, ['1', '2', '3'], ['4', '5', '6']];
        $this->assertEquals($expected, $this->readCsvBack($file));
    }

    /**
     * @test writeCsv omits the header row when header is null
     */
    public function testWriteCsvNoHeader(): void
    {
        // Given
        $rows = [['1', '2'], ['3', '4']];
        $file = $this->writableFile();

        // When
        File::writeCsv($file, $rows, null);

        // Then
        $this->assertEquals("1,2\n3,4\n", $this->readBack($file));
    }

    /**
     * @test writeCsv writes the header row when supplied
     */
    public function testWriteCsvWritesHeaderWhenSupplied(): void
    {
        // Given
        $rows = [['1', '2']];
        $file = $this->writableFile();

        // When
        File::writeCsv($file, $rows, ['a', 'b']);

        // Then
        $this->assertEquals("a,b\n1,2\n", $this->readBack($file));
    }

    /**
     * @test         writeCsv honors a custom separator, enclosure, and escape
     * @dataProvider dataProviderForCustomConfig
     * @param        array<array<string>> $rows
     * @param        array{string, string, string} $config
     * @param        string $expected
     */
    public function testWriteCsvCustomConfig(array $rows, array $config, string $expected): void
    {
        // Given
        [$separator, $enclosure, $escape] = $config;
        $file = $this->writableFile();

        // When
        File::writeCsv($file, $rows, null, $separator, $enclosure, $escape);

        // Then
        $this->assertEquals($expected, $this->readBack($file));
    }

    public static function dataProviderForCustomConfig(): array
    {
        return [
            [[['1', '2', '3']], [';', '"', '\\'], "1;2;3\n"],
            [[['1', '2', '3']], ['|', '"', '\\'], "1|2|3\n"],
        ];
    }

    /**
     * @test writeCsv then readCsvAssoc with explicit headers is identity
     */
    public function testWriteCsvThenReadCsvAssoc(): void
    {
        // Given
        $headers = ['name', 'age'];
        $rows = [['Alice', '30'], ['Bob', '25']];
        $file = $this->writableFile();

        // When
        File::writeCsv($file, $rows);

        // Then
        $meta = \stream_get_meta_data($file);
        \fclose($file);
        $reader = \fopen($meta['uri'], 'r');
        $result = [];
        foreach (File::readCsvAssoc($reader, $headers) as $assoc) {
            $result[] = $assoc;
        }
        \fclose($reader);

        $expected = [
            ['name' => 'Alice', 'age' => '30'],
            ['name' => 'Bob', 'age' => '25'],
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * @test writeCsv rejects a non-resource argument
     */
    public function testWriteCsvError(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        File::writeCsv('/not/a/resource', [['a', 'b']]);
    }
}

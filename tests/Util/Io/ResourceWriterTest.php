<?php

declare(strict_types=1);

namespace IterTools\Tests\Util\Io;

use IterTools\Util\Io\ResourceWriter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class ResourceWriterTest extends \PHPUnit\Framework\TestCase
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
     * @test write() writes the data in full
     */
    public function testWrite(): void
    {
        // Given
        $file   = $this->writableFile();
        $writer = new ResourceWriter($file);

        // When
        $writer->write('abc');
        $writer->write('def');

        // Then
        $this->assertSame('abcdef', $this->readBack($file));
    }

    /**
     * @test write() accepts an empty string
     */
    public function testWriteEmptyString(): void
    {
        // Given
        $file   = $this->writableFile();
        $writer = new ResourceWriter($file);

        // When
        $writer->write('');

        // Then
        $this->assertSame('', $this->readBack($file));
    }

    /**
     * @test write() throws when the write is truncated
     */
    public function testWriteTruncatedThrows(): void
    {
        // Given
        $file   = $this->writableFile();
        $writer = new ResourceWriter($file);
        vfsStream::setQuota(3);

        // Then
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('wrote 3 of 6 bytes');

        // When
        $writer->write('abcdef');
    }

    /**
     * @test write() throws when the write fails outright
     */
    public function testWriteFailureThrows(): void
    {
        // Given a handle opened read-only
        $file   = \fopen('php://memory', 'r');
        $writer = new ResourceWriter($file);

        // And PHP's own "write failed" notice is swallowed so only our exception surfaces
        \set_error_handler(static fn (): bool => true);

        // Then
        $this->expectException(\RuntimeException::class);

        // When
        try {
            $writer->write('abc');
        } finally {
            \restore_error_handler();
        }
    }

    /**
     * @test writeCsvRow() writes a row with the given separator, enclosure and escape
     */
    public function testWriteCsvRow(): void
    {
        // Given
        $file   = $this->writableFile();
        $writer = new ResourceWriter($file);

        // When
        $writer->writeCsvRow(['a', 'b c', 'd;e', 'f"g'], ';', '"', '\\');

        // Then
        $this->assertSame("a;\"b c\";\"d;e\";\"f\"\"g\"\n", $this->readBack($file));
    }

    /**
     * @test writeCsvRow() honours a custom enclosure
     */
    public function testWriteCsvRowCustomEnclosure(): void
    {
        // Given
        $file   = $this->writableFile();
        $writer = new ResourceWriter($file);

        // When
        $writer->writeCsvRow(['a b', 'c'], ',', "'", '\\');

        // Then
        $this->assertSame("'a b',c\n", $this->readBack($file));
    }

    /**
     * @test writeCsvRow() does not leak an earlier, longer row into a later, shorter one
     */
    public function testWriteCsvRowsDoNotLeakIntoEachOther(): void
    {
        // Given
        $file   = $this->writableFile();
        $writer = new ResourceWriter($file);

        // When
        $writer->writeCsvRow(['aaaaaaaaaa', 'bbbbbbbbbb'], ',', '"', '\\');
        $writer->writeCsvRow(['c'], ',', '"', '\\');
        $writer->writeCsvRow(['d', 'e'], ',', '"', '\\');

        // Then
        $this->assertSame("aaaaaaaaaa,bbbbbbbbbb\nc\nd,e\n", $this->readBack($file));
    }

    /**
     * @test writeCsvRow() interleaves with write() on the same resource
     */
    public function testWriteCsvRowInterleavesWithWrite(): void
    {
        // Given
        $file   = $this->writableFile();
        $writer = new ResourceWriter($file);

        // When
        $writer->write('# header' . "\n");
        $writer->writeCsvRow([1, 2], ',', '"', '\\');
        $writer->write('# footer');

        // Then
        $this->assertSame("# header\n1,2\n# footer", $this->readBack($file));
    }

    /**
     * @test writeCsvRow() throws when the row write is truncated
     */
    public function testWriteCsvRowTruncatedThrows(): void
    {
        // Given
        $file   = $this->writableFile();
        $writer = new ResourceWriter($file);
        vfsStream::setQuota(3);

        // Then
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('wrote 3 of 9 bytes');

        // When
        $writer->writeCsvRow(['abcdef', 'x'], ',', '"', '\\');
    }

    /**
     * @test writeCsvRow() reports a failure once the quota is already exhausted
     */
    public function testWriteCsvRowAfterQuotaExhaustedThrows(): void
    {
        // Given
        $file   = $this->writableFile();
        $writer = new ResourceWriter($file);
        vfsStream::setQuota(2);
        $writer->write('ab');

        // Then
        $this->expectException(\RuntimeException::class);

        // When
        $writer->writeCsvRow(['x'], ',', '"', '\\');
    }

    /**
     * @test writeCsvRows() writes each row with the given separator, enclosure and escape, an earlier, longer row
     *       not leaking into a later, shorter one
     */
    public function testWriteCsvRows(): void
    {
        // Given
        $file   = $this->writableFile();
        $writer = new ResourceWriter($file);

        // When
        $writer->writeCsvRows([['aaaaaaaaaa', 'b c'], ['d;e'], ['f"g', 1]], ';', '"', '\\');

        // Then
        $this->assertSame("aaaaaaaaaa;\"b c\"\n\"d;e\"\n\"f\"\"g\";1\n", $this->readBack($file));
    }

    /**
     * @test writeCsvRows() writes nothing for no rows
     */
    public function testWriteCsvRowsEmpty(): void
    {
        // Given
        $file   = $this->writableFile();
        $writer = new ResourceWriter($file);

        // When
        $writer->writeCsvRows([], ',', '"', '\\');

        // Then
        $this->assertSame('', $this->readBack($file));
    }

    /**
     * @test writeCsvRows() writes each row before it takes the next one
     */
    public function testWriteCsvRowsWritesEachRowBeforeTheNext(): void
    {
        // Given
        $file   = $this->writableFile();
        $uri    = \stream_get_meta_data($file)['uri'];
        $writer = new ResourceWriter($file);
        $seen   = [];
        $rows   = (static function () use ($uri, &$seen): \Generator {
            yield ['a', 1];
            $seen[] = \file_get_contents($uri);
            yield ['b', 2];
            $seen[] = \file_get_contents($uri);
        })();

        // When
        $writer->writeCsvRows($rows, ',', '"', '\\');

        // Then
        $this->assertSame(["a,1\n", "a,1\nb,2\n"], $seen);
    }

    /**
     * @test writeCsvRows() writes a NAN field as 'NAN'
     */
    public function testWriteCsvRowsNan(): void
    {
        // Given
        $file   = $this->writableFile();
        $writer = new ResourceWriter($file);

        // When
        $writer->writeCsvRows([[1, \NAN], [\NAN, 'a', \INF]], ',', '"', '\\');

        // Then
        $this->assertSame("1,NAN\nNAN,a,INF\n", $this->readBack($file));
    }

    /**
     * @test writeCsvRows() throws when a later row write is truncated
     */
    public function testWriteCsvRowsTruncatedThrows(): void
    {
        // Given
        $file   = $this->writableFile();
        $writer = new ResourceWriter($file);
        vfsStream::setQuota(6);

        // Then
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('wrote 2 of 5 bytes');

        // When
        $writer->writeCsvRows([['abc'], ['de', 'f']], ',', '"', '\\');
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

class FileFixtureTest extends \PHPUnit\Framework\TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = \sys_get_temp_dir() . '/itertools_test_' . \uniqid();
        \mkdir($this->tempDir);
    }

    protected function tearDown(): void
    {
        $files = \glob($this->tempDir . '/*');
        foreach ($files as $file) {
            \unlink($file);
        }
        \rmdir($this->tempDir);
    }

    // createFromString

    /**
     * @test createFromString returns resource
     */
    public function testCreateFromStringReturnsResource(): void
    {
        // When
        $resource = FileFixture::createFromString('hello', $this->tempDir);

        // Then
        $this->assertIsResource($resource);
        \fclose($resource);
    }

    /**
     * @test createFromString content
     */
    public function testCreateFromStringContent(): void
    {
        // Given
        $string = 'hello world';

        // When
        $resource = FileFixture::createFromString($string, $this->tempDir);
        $content = \stream_get_contents($resource);
        \fclose($resource);

        // Then
        $this->assertSame('hello world', $content);
    }

    /**
     * @test createFromString empty
     */
    public function testCreateFromStringEmpty(): void
    {
        // Given
        $string = '';

        // When
        $resource = FileFixture::createFromString($string, $this->tempDir);
        $content = \stream_get_contents($resource);
        \fclose($resource);

        // Then
        $this->assertSame('', $content);
    }

    // createFromLines

    /**
     * @test createFromLines returns resource
     */
    public function testCreateFromLinesReturnsResource(): void
    {
        // Given
        $lines = ['a', 'b'];

        // When
        $resource = FileFixture::createFromLines($lines, $this->tempDir);

        // Then
        $this->assertIsResource($resource);
        \fclose($resource);
    }

    /**
     * @test createFromLines content
     */
    public function testCreateFromLinesContent(): void
    {
        // Given
        $lines = ['line1', 'line2', 'line3'];

        // When
        $resource = FileFixture::createFromLines($lines, $this->tempDir);
        $content = \stream_get_contents($resource);
        \fclose($resource);

        // Then
        $this->assertSame(\implode(\PHP_EOL, $lines), $content);
    }

    /**
     * @test createFromLines empty
     */
    public function testCreateFromLinesEmpty(): void
    {
        // Given
        $lines = [];

        // When
        $resource = FileFixture::createFromLines($lines, $this->tempDir);
        $content = \stream_get_contents($resource);
        \fclose($resource);

        // Then
        $this->assertSame('', $content);
    }

    /**
     * @test createFromLines single line
     */
    public function testCreateFromLinesSingleLine(): void
    {
        // Given
        $lines = ['only line'];

        // When
        $resource = FileFixture::createFromLines($lines, $this->tempDir);
        $content = \stream_get_contents($resource);
        \fclose($resource);

        // Then
        $this->assertSame('only line', $content);
    }
}

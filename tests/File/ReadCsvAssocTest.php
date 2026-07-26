<?php

declare(strict_types=1);

namespace IterTools\Tests\File;

use IterTools\File;
use IterTools\Tests\Fixture\FileFixture;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class ReadCsvAssocTest extends \PHPUnit\Framework\TestCase
{
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('test');
    }

    /**
     * @test readCsvAssoc example usage
     */
    public function testReadCsvAssocExampleUsage(): void
    {
        // Given
        $csvText = <<<'CSV_END'
episode,title,year
I,The Phantom Menace,1999
II,Attack of the Clones,2002
III,Revenge of the Sith,2005
CSV_END;
        $file = FileFixture::createFromString($csvText, $this->root->url());

        // When
        $result = [];
        foreach (File::readCsvAssoc($file) as $row) {
            $result[] = $row;
        }

        // Then
        $expected = [
            ['episode' => 'I', 'title' => 'The Phantom Menace', 'year' => '1999'],
            ['episode' => 'II', 'title' => 'Attack of the Clones', 'year' => '2002'],
            ['episode' => 'III', 'title' => 'Revenge of the Sith', 'year' => '2005'],
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * @test readCsvAssoc infers headers from the first row
     */
    public function testReadCsvAssocInfersHeaders(): void
    {
        // Given
        $file = FileFixture::createFromLines(['a,b,c', '1,2,3', '4,5,6'], $this->root->url());

        // When
        $result = \iterator_to_array(File::readCsvAssoc($file), false);

        // Then
        $expected = [
            ['a' => '1', 'b' => '2', 'c' => '3'],
            ['a' => '4', 'b' => '5', 'c' => '6'],
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * @test readCsvAssoc uses explicit headers and treats every row as data
     */
    public function testReadCsvAssocExplicitHeaders(): void
    {
        // Given
        $file = FileFixture::createFromLines(['1,2,3', '4,5,6'], $this->root->url());

        // When
        $result = \iterator_to_array(File::readCsvAssoc($file, ['a', 'b', 'c']), false);

        // Then
        $expected = [
            ['a' => '1', 'b' => '2', 'c' => '3'],
            ['a' => '4', 'b' => '5', 'c' => '6'],
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * @test readCsvAssoc maps empty fields to empty strings
     */
    public function testReadCsvAssocEmptyFields(): void
    {
        // Given
        $file = FileFixture::createFromLines(['x,y,z', 'a,,c'], $this->root->url());

        // When
        $result = \iterator_to_array(File::readCsvAssoc($file), false);

        // Then
        $this->assertEquals([['x' => 'a', 'y' => '', 'z' => 'c']], $result);
    }

    /**
     * @test readCsvAssoc throws on a data row shorter than the headers
     */
    public function testReadCsvAssocMismatchedRowLength(): void
    {
        // Given
        $file = FileFixture::createFromLines(['a,b,c', '1,2,3', '4,5'], $this->root->url());

        // Then
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/2/');

        // When
        foreach (File::readCsvAssoc($file) as $_) {
            // drive the generator
        }
    }

    /**
     * @test readCsvAssoc throws on a blank line rather than silently skipping it
     */
    public function testReadCsvAssocBlankLineThrows(): void
    {
        // Given
        $file = FileFixture::createFromLines(['a,b,c', '1,2,3', '', '4,5,6'], $this->root->url());

        // Then
        $this->expectException(\RuntimeException::class);

        // When
        foreach (File::readCsvAssoc($file) as $_) {
            // drive the generator
        }
    }

    /**
     * @test readCsvAssoc on an empty file with inferred headers yields nothing
     */
    public function testReadCsvAssocEmptyFileInferredHeaders(): void
    {
        // Given
        $file = FileFixture::createFromLines([], $this->root->url());

        // When
        $result = \iterator_to_array(File::readCsvAssoc($file), false);

        // Then
        $this->assertEquals([], $result);
    }

    /**
     * @test readCsvAssoc on an empty file with explicit headers yields nothing
     */
    public function testReadCsvAssocEmptyFileExplicitHeaders(): void
    {
        // Given
        $file = FileFixture::createFromLines([], $this->root->url());

        // When
        $result = \iterator_to_array(File::readCsvAssoc($file, ['a', 'b']), false);

        // Then
        $this->assertEquals([], $result);
    }

    /**
     * @test readCsvAssoc validates explicit headers even when the file is empty
     */
    public function testReadCsvAssocValidatesExplicitHeadersOnEmptyFile(): void
    {
        // Given
        $file = FileFixture::createFromLines([], $this->root->url());

        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (File::readCsvAssoc($file, ['a', 'a']) as $_) {
            // drive the generator
        }
    }

    /**
     * @test readCsvAssoc rejects an empty inferred header
     */
    public function testReadCsvAssocInferredEmptyHeaderThrows(): void
    {
        // Given
        $file = FileFixture::createFromLines(['a,,c', '1,2,3'], $this->root->url());

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/empty/i');

        // When
        foreach (File::readCsvAssoc($file) as $_) {
            // drive the generator
        }
    }

    /**
     * @test readCsvAssoc rejects a duplicate inferred header
     */
    public function testReadCsvAssocInferredDuplicateHeaderThrows(): void
    {
        // Given
        $file = FileFixture::createFromLines(['a,b,a', '1,2,3'], $this->root->url());

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches("/duplicate.+'a'/i");

        // When
        foreach (File::readCsvAssoc($file) as $_) {
            // drive the generator
        }
    }

    /**
     * @test readCsvAssoc rejects an empty explicit header
     */
    public function testReadCsvAssocExplicitEmptyHeaderThrows(): void
    {
        // Given
        $file = FileFixture::createFromLines(['1,2,3'], $this->root->url());

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/empty/i');

        // When
        foreach (File::readCsvAssoc($file, ['a', '', 'c']) as $_) {
            // drive the generator
        }
    }

    /**
     * @test readCsvAssoc rejects a duplicate explicit header
     */
    public function testReadCsvAssocExplicitDuplicateHeaderThrows(): void
    {
        // Given
        $file = FileFixture::createFromLines(['1,2,3'], $this->root->url());

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches("/duplicate.+'b'/i");

        // When
        foreach (File::readCsvAssoc($file, ['b', 'b', 'c']) as $_) {
            // drive the generator
        }
    }

    /**
     * @test readCsvAssoc rejects a non-string explicit header
     */
    public function testReadCsvAssocExplicitNonStringHeaderThrows(): void
    {
        // Given
        $file = FileFixture::createFromLines(['1,2,3'], $this->root->url());

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/string/i');

        // When
        foreach (File::readCsvAssoc($file, ['a', 42, 'c']) as $_) {
            // drive the generator
        }
    }

    /**
     * @test readCsvAssoc honors a custom separator
     */
    public function testReadCsvAssocCustomSeparator(): void
    {
        // Given
        $file = FileFixture::createFromLines(['a;b;c', '1;2;3'], $this->root->url());

        // When
        $result = \iterator_to_array(File::readCsvAssoc($file, null, ';'), false);

        // Then
        $this->assertEquals([['a' => '1', 'b' => '2', 'c' => '3']], $result);
    }

    /**
     * @test readCsvAssoc keys rows by canonical numeric headers as integers (PHP array key coercion)
     */
    public function testReadCsvAssocNumericHeadersBecomeIntegerKeys(): void
    {
        // Given a CSV whose headers include a canonical numeric string
        $file = FileFixture::createFromLines(['1,name', 'alpha,beta'], $this->root->url());

        // When
        $result = \iterator_to_array(File::readCsvAssoc($file), false);

        // Then PHP coerces the canonical numeric header to an integer array key
        $this->assertEquals([[1 => 'alpha', 'name' => 'beta']], $result);
        $this->assertSame([1, 'name'], \array_keys($result[0]));
    }

    /**
     * @test readCsvAssoc keeps non-canonical numeric-looking headers as string keys
     */
    public function testReadCsvAssocNonCanonicalNumericHeadersStayStrings(): void
    {
        // Given headers that look numeric but are not canonical integer strings
        $file = FileFixture::createFromLines(['01,1.0, 1,+1', 'a,b,c,d'], $this->root->url());

        // When
        $result = \iterator_to_array(File::readCsvAssoc($file), false);

        // Then
        $this->assertSame(['01', '1.0', ' 1', '+1'], \array_keys($result[0]));
    }

    /**
     * @test readCsvAssoc does not lose columns when numeric headers coerce to integer keys
     */
    public function testReadCsvAssocNumericHeadersDoNotCollide(): void
    {
        // Given a common real-world shape: year columns
        $file = FileFixture::createFromLines(['2020,2021,2022', '10,20,30'], $this->root->url());

        // When
        $result = \iterator_to_array(File::readCsvAssoc($file), false);

        // Then every column survives
        $this->assertCount(3, $result[0]);
        $this->assertSame([2020, 2021, 2022], \array_keys($result[0]));
        $this->assertSame(['10', '20', '30'], \array_values($result[0]));
    }

    /**
     * @test readCsvAssoc rejects a non-resource argument
     */
    public function testReadCsvAssocError(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);

        // When
        foreach (File::readCsvAssoc('/not/a/resource') as $_) {
            // drive the generator
        }
    }
}

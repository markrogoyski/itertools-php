<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

class GeneratorFixtureTest extends \PHPUnit\Framework\TestCase
{
    // getGenerator

    /**
     * @test getGenerator returns Generator
     */
    public function testGetGeneratorReturnsGenerator(): void
    {
        // When
        $generator = GeneratorFixture::getGenerator([]);

        // Then
        $this->assertInstanceOf(\Generator::class, $generator);
    }

    /**
     * @test getGenerator empty
     */
    public function testGetGeneratorEmpty(): void
    {
        // Given
        $values = [];

        // When
        $result = \iterator_to_array(GeneratorFixture::getGenerator($values));

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test getGenerator yields values
     */
    public function testGetGeneratorYieldsValues(): void
    {
        // Given
        $values = [1, 2, 3];

        // When
        $result = \iterator_to_array(GeneratorFixture::getGenerator($values));

        // Then
        $this->assertSame($values, $result);
    }

    /**
     * @test getGenerator mixed types
     */
    public function testGetGeneratorMixedTypes(): void
    {
        // Given
        $values = [1, 'two', 3.0, null, true];

        // When
        $result = \iterator_to_array(GeneratorFixture::getGenerator($values));

        // Then
        $this->assertSame($values, $result);
    }

    // getRecordingGenerator

    /**
     * @test getRecordingGenerator records nothing until advanced
     */
    public function testGetRecordingGeneratorRecordsNothingUntilAdvanced(): void
    {
        // Given
        $pulled = [];

        // When
        $generator = GeneratorFixture::getRecordingGenerator([1, 2, 3], $pulled);

        // Then
        $this->assertInstanceOf(\Generator::class, $generator);
        $this->assertSame([], $pulled);
    }

    /**
     * @test getRecordingGenerator records each value as the generator advances onto it
     */
    public function testGetRecordingGeneratorRecordsEachValueAsAdvanced(): void
    {
        // Given
        $pulled = [];
        $generator = GeneratorFixture::getRecordingGenerator([1, 2, 3], $pulled);

        // When
        $generator->current();
        $afterFirst = $pulled;
        $generator->next();

        // Then
        $this->assertSame([1], $afterFirst);
        $this->assertSame([1, 2], $pulled);
    }

    /**
     * @test getRecordingGenerator yields values and records all of them
     */
    public function testGetRecordingGeneratorYieldsValues(): void
    {
        // Given
        $pulled = [];
        $values = [1, 'two', null];

        // When
        $result = \iterator_to_array(GeneratorFixture::getRecordingGenerator($values, $pulled));

        // Then
        $this->assertSame($values, $result);
        $this->assertSame($values, $pulled);
    }

    /**
     * @test getRecordingGenerator generators can share one log
     */
    public function testGetRecordingGeneratorSharedLog(): void
    {
        // Given
        $pulled = [];
        $a = GeneratorFixture::getRecordingGenerator(['a1', 'a2'], $pulled);
        $b = GeneratorFixture::getRecordingGenerator(['b1'], $pulled);

        // When
        $a->current();
        $b->current();
        $a->next();

        // Then
        $this->assertSame(['a1', 'b1', 'a2'], $pulled);
    }

    // getKeyValueGenerator

    /**
     * @test getKeyValueGenerator returns Generator
     */
    public function testGetKeyValueGeneratorReturnsGenerator(): void
    {
        // When
        $generator = GeneratorFixture::getKeyValueGenerator([]);

        // Then
        $this->assertInstanceOf(\Generator::class, $generator);
    }

    /**
     * @test getKeyValueGenerator empty
     */
    public function testGetKeyValueGeneratorEmpty(): void
    {
        // Given
        $values = [];

        // When
        $result = \iterator_to_array(GeneratorFixture::getKeyValueGenerator($values));

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test getKeyValueGenerator preserves keys
     */
    public function testGetKeyValueGeneratorPreservesKeys(): void
    {
        // Given
        $values = ['a' => 1, 'b' => 2, 'c' => 3];

        // When
        $result = \iterator_to_array(GeneratorFixture::getKeyValueGenerator($values));

        // Then
        $this->assertSame($values, $result);
    }

    /**
     * @test getKeyValueGenerator with integer keys
     */
    public function testGetKeyValueGeneratorWithIntegerKeys(): void
    {
        // Given
        $values = [10 => 'a', 20 => 'b'];

        // When
        $result = \iterator_to_array(GeneratorFixture::getKeyValueGenerator($values));

        // Then
        $this->assertSame($values, $result);
    }

    // getCombined

    /**
     * @test getCombined returns Generator
     */
    public function testGetCombinedReturnsGenerator(): void
    {
        // When
        $generator = GeneratorFixture::getCombined([], []);

        // Then
        $this->assertInstanceOf(\Generator::class, $generator);
    }

    /**
     * @test getCombined empty
     */
    public function testGetCombinedEmpty(): void
    {
        // Given
        $keys = [];
        $values = [];

        // When
        $result = \iterator_to_array(GeneratorFixture::getCombined($keys, $values));

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test getCombined yields key-value pairs
     */
    public function testGetCombinedYieldsKeyValuePairs(): void
    {
        // Given
        $keys = ['a', 'b', 'c'];
        $values = [1, 2, 3];

        // When
        $result = \iterator_to_array(GeneratorFixture::getCombined($keys, $values));

        // Then
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], $result);
    }
}

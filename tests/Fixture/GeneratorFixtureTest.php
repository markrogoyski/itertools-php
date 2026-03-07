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

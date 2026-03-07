<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

class IteratorAggregateFixtureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test implements IteratorAggregate interface
     */
    public function testImplementsIteratorAggregateInterface(): void
    {
        // Given
        $fixture = new IteratorAggregateFixture([]);

        // Then
        $this->assertInstanceOf(\IteratorAggregate::class, $fixture);
    }

    /**
     * @test getIterator returns ArrayIterator
     */
    public function testGetIteratorReturnsArrayIterator(): void
    {
        // Given
        $fixture = new IteratorAggregateFixture([]);

        // When
        $iterator = $fixture->getIterator();

        // Then
        $this->assertInstanceOf(\ArrayIterator::class, $iterator);
    }

    /**
     * @test empty array
     */
    public function testEmptyArray(): void
    {
        // Given
        $fixture = new IteratorAggregateFixture([]);

        // When
        $result = \iterator_to_array($fixture);

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test iterates values
     */
    public function testIteratesValues(): void
    {
        // Given
        $values = [10, 20, 30];
        $fixture = new IteratorAggregateFixture($values);

        // When
        $result = \iterator_to_array($fixture);

        // Then
        $this->assertSame($values, $result);
    }

    /**
     * @test preserves keys
     */
    public function testPreservesKeys(): void
    {
        // Given
        $values = ['a' => 1, 'b' => 2, 'c' => 3];
        $fixture = new IteratorAggregateFixture($values);

        // When
        $result = \iterator_to_array($fixture);

        // Then
        $this->assertSame($values, $result);
    }

    /**
     * @test is rewindable
     */
    public function testIsRewindable(): void
    {
        // Given
        $fixture = new IteratorAggregateFixture([1, 2, 3]);

        // When
        $first = \iterator_to_array($fixture);
        $second = \iterator_to_array($fixture);

        // Then
        $this->assertSame($first, $second);
    }

    /**
     * @test mixed value types
     */
    public function testMixedValueTypes(): void
    {
        // Given
        $values = [1, 'two', 3.0, null, true, [4, 5]];
        $fixture = new IteratorAggregateFixture($values);

        // When
        $result = \iterator_to_array($fixture);

        // Then
        $this->assertSame($values, $result);
    }
}

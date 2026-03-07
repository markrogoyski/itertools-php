<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

class CountableIteratorAggregateFixtureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test implements IteratorAggregate interface
     */
    public function testImplementsIteratorAggregateInterface(): void
    {
        // Given
        $fixture = new CountableIteratorAggregateFixture([]);

        // Then
        $this->assertInstanceOf(\IteratorAggregate::class, $fixture);
    }

    /**
     * @test implements Countable interface
     */
    public function testImplementsCountableInterface(): void
    {
        // Given
        $fixture = new CountableIteratorAggregateFixture([]);

        // Then
        $this->assertInstanceOf(\Countable::class, $fixture);
    }

    /**
     * @test getIterator returns ArrayIterator
     */
    public function testGetIteratorReturnsArrayIterator(): void
    {
        // Given
        $fixture = new CountableIteratorAggregateFixture([]);

        // When
        $iterator = $fixture->getIterator();

        // Then
        $this->assertInstanceOf(\ArrayIterator::class, $iterator);
    }

    /**
     * @test count empty
     */
    public function testCountEmpty(): void
    {
        // Given
        $fixture = new CountableIteratorAggregateFixture([]);

        // Then
        $this->assertCount(0, $fixture);
    }

    /**
     * @test count non-empty
     */
    public function testCountNonEmpty(): void
    {
        // Given
        $fixture = new CountableIteratorAggregateFixture([1, 2, 3]);

        // Then
        $this->assertCount(3, $fixture);
    }

    /**
     * @test empty iteration
     */
    public function testEmptyIteration(): void
    {
        // Given
        $fixture = new CountableIteratorAggregateFixture([]);

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
        $fixture = new CountableIteratorAggregateFixture($values);

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
        $fixture = new CountableIteratorAggregateFixture($values);

        // When
        $result = \iterator_to_array($fixture);

        // Then
        $this->assertSame($values, $result);
    }

    /**
     * @test count matches iterated elements
     */
    public function testCountMatchesIteratedElements(): void
    {
        // Given
        $values = [1, 2, 3, 4, 5];
        $fixture = new CountableIteratorAggregateFixture($values);

        // When
        $iteratedCount = 0;
        foreach ($fixture as $_) {
            $iteratedCount++;
        }

        // Then
        $this->assertSame(\count($fixture), $iteratedCount);
    }
}

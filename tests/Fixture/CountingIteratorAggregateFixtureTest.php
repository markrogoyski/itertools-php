<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

class CountingIteratorAggregateFixtureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test implements IteratorAggregate interface
     */
    public function testImplementsIteratorAggregateInterface(): void
    {
        // Given
        $fixture = new CountingIteratorAggregateFixture([]);

        // Then
        $this->assertInstanceOf(\IteratorAggregate::class, $fixture);
    }

    /**
     * @test call count starts at zero
     */
    public function testCallCountStartsAtZero(): void
    {
        // Given
        $fixture = new CountingIteratorAggregateFixture([1, 2, 3]);

        // Then
        $this->assertSame(0, $fixture->getIteratorCallCount());
    }

    /**
     * @test counts every getIterator call
     */
    public function testCountsEveryGetIteratorCall(): void
    {
        // Given
        $fixture = new CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $fixture->getIterator();
        $fixture->getIterator();

        // Then
        $this->assertSame(2, $fixture->getIteratorCallCount());
    }

    /**
     * @test iterates values
     */
    public function testIteratesValues(): void
    {
        // Given
        $values = [10, 20, 30];
        $fixture = new CountingIteratorAggregateFixture($values);

        // When
        $result = \iterator_to_array($fixture);

        // Then
        $this->assertSame($values, $result);
        $this->assertSame(1, $fixture->getIteratorCallCount());
    }

    /**
     * @test preserves keys
     */
    public function testPreservesKeys(): void
    {
        // Given
        $values = ['a' => 1, 'b' => 2, 'c' => 3];
        $fixture = new CountingIteratorAggregateFixture($values);

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
        $fixture = new CountingIteratorAggregateFixture([1, 2, 3]);

        // When
        $first = \iterator_to_array($fixture);
        $second = \iterator_to_array($fixture);

        // Then
        $this->assertSame($first, $second);
        $this->assertSame(2, $fixture->getIteratorCallCount());
    }
}

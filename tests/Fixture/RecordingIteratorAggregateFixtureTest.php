<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

class RecordingIteratorAggregateFixtureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test implements IteratorAggregate interface
     */
    public function testImplementsIteratorAggregateInterface(): void
    {
        // Given
        $pulled = [];

        // When
        $fixture = new RecordingIteratorAggregateFixture([], $pulled);

        // Then
        $this->assertInstanceOf(\IteratorAggregate::class, $fixture);
    }

    /**
     * @test getIterator records nothing until the iterator moves
     */
    public function testGetIteratorRecordsNothing(): void
    {
        // Given
        $pulled = [];
        $fixture = new RecordingIteratorAggregateFixture([1, 2, 3], $pulled);

        // When
        $fixture->getIterator();

        // Then
        $this->assertSame([], $pulled);
    }

    /**
     * @test iterator records each value it moves onto in the shared log
     */
    public function testIteratorRecordsInSharedLog(): void
    {
        // Given
        $pulled = [];
        $fixture = new RecordingIteratorAggregateFixture([1, 2, 3], $pulled);
        $iterator = $fixture->getIterator();

        // When
        $iterator->rewind();
        $iterator->next();

        // Then
        $this->assertSame([1, 2], $pulled);
    }

    /**
     * @test iterates values and records all of them
     */
    public function testIteratesValues(): void
    {
        // Given
        $pulled = [];
        $values = [10, 20, 30];
        $fixture = new RecordingIteratorAggregateFixture($values, $pulled);

        // When
        $result = \iterator_to_array($fixture);

        // Then
        $this->assertSame($values, $result);
        $this->assertSame($values, $pulled);
    }
}

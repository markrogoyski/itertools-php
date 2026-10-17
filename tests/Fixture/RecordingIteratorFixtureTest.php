<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

class RecordingIteratorFixtureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test implements Iterator interface
     */
    public function testImplementsIteratorInterface(): void
    {
        // Given
        $pulled = [];

        // When
        $fixture = new RecordingIteratorFixture([], $pulled);

        // Then
        $this->assertInstanceOf(\Iterator::class, $fixture);
    }

    /**
     * @test records nothing on construction
     */
    public function testRecordsNothingOnConstruction(): void
    {
        // Given
        $pulled = [];

        // When
        new RecordingIteratorFixture([1, 2, 3], $pulled);

        // Then
        $this->assertSame([], $pulled);
    }

    /**
     * @test records the value that rewind and next move onto
     */
    public function testRecordsValueOnRewindAndNext(): void
    {
        // Given
        $pulled = [];
        $fixture = new RecordingIteratorFixture([1, 2, 3], $pulled);

        // When
        $fixture->rewind();
        $afterRewind = $pulled;
        $fixture->next();

        // Then
        $this->assertSame([1], $afterRewind);
        $this->assertSame([1, 2], $pulled);
    }

    /**
     * @test current, key and valid record nothing
     */
    public function testAccessorsRecordNothing(): void
    {
        // Given
        $pulled = [];
        $fixture = new RecordingIteratorFixture([1, 2, 3], $pulled);
        $fixture->rewind();

        // When
        $fixture->current();
        $fixture->key();
        $fixture->valid();

        // Then
        $this->assertSame([1], $pulled);
    }

    /**
     * @test moving past the end records nothing
     */
    public function testMovingPastEndRecordsNothing(): void
    {
        // Given
        $pulled = [];
        $fixture = new RecordingIteratorFixture([1], $pulled);
        $fixture->rewind();

        // When
        $fixture->next();

        // Then
        $this->assertFalse($fixture->valid());
        $this->assertSame([1], $pulled);
    }

    /**
     * @test iterates values and records all of them
     */
    public function testIteratesValues(): void
    {
        // Given
        $pulled = [];
        $values = [10, 20, 30];
        $fixture = new RecordingIteratorFixture($values, $pulled);

        // When
        $result = \iterator_to_array($fixture);

        // Then
        $this->assertSame($values, $result);
        $this->assertSame($values, $pulled);
    }

    /**
     * @test rewinding records the first value again
     */
    public function testRewindRecordsAgain(): void
    {
        // Given
        $pulled = [];
        $fixture = new RecordingIteratorFixture([1, 2], $pulled);

        // When
        $fixture->rewind();
        $fixture->next();
        $fixture->rewind();

        // Then
        $this->assertSame(1, $fixture->current());
        $this->assertSame([1, 2, 1], $pulled);
    }

    /**
     * @test fixtures can share one log
     */
    public function testSharedLog(): void
    {
        // Given
        $pulled = [];
        $a = new RecordingIteratorFixture(['a1', 'a2'], $pulled);
        $b = new RecordingIteratorFixture(['b1'], $pulled);

        // When
        $a->rewind();
        $b->rewind();
        $a->next();

        // Then
        $this->assertSame(['a1', 'b1', 'a2'], $pulled);
    }
}

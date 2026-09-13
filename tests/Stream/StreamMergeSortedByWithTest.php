<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\CountingIteratorAggregateFixture;

class StreamMergeSortedByWithTest extends \PHPUnit\Framework\TestCase
{
    public function testMergesCurrentStreamByProjection(): void
    {
        // Given
        $stream = Stream::of(['a', 'bbb']);

        // When
        $result = $stream->mergeSortedByWith('\strlen', ['cc', 'dddd'])->toArray();

        // Then
        $this->assertSame(['a', 'cc', 'bbb', 'dddd'], $result);
    }

    public function testReturnsSameStreamForFluentChaining(): void
    {
        // Given
        $stream = Stream::of(['a']);

        // When
        $returned = $stream->mergeSortedByWith('\strlen', ['bb']);

        // Then
        $this->assertSame($stream, $returned);
    }

    public function testIsLazyAndDoesNotTouchSourcesBeforeTerminalOperation(): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture(['bb']);
        $stream = Stream::of(['a'])->mergeSortedByWith('\strlen', $aggregate);

        // Then
        $this->assertSame(0, $aggregate->getIteratorCallCount());

        // When
        $result = $stream->toArray();

        // Then
        $this->assertSame(['a', 'bb'], $result);
        $this->assertSame(1, $aggregate->getIteratorCallCount());
    }

    public function testPreservesStableTieBreakWithCurrentStreamFirst(): void
    {
        // Given
        $stream = Stream::of([(object) ['rank' => 1, 'id' => 'a']]);

        // When
        $result = $stream->mergeSortedByWith(
            static fn (object $item): int => $item->rank,
            [(object) ['rank' => 1, 'id' => 'b']],
        )->toArray();

        // Then
        $this->assertSame(['a', 'b'], \array_map(static fn (object $item): string => $item->id, $result));
    }
}

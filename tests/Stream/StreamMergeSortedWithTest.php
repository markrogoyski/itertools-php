<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\CountingIteratorAggregateFixture;
use IterTools\Tests\Fixture\GeneratorFixture;

class StreamMergeSortedWithTest extends \PHPUnit\Framework\TestCase
{
    public function testMergesCurrentStreamAsFirstStableSource(): void
    {
        // Given
        $stream = Stream::of([1, 2, 2]);

        // When
        $result = $stream->mergeSortedWith(GeneratorFixture::getGenerator([2, 3]))->toArray();

        // Then
        $this->assertSame([1, 2, 2, 2, 3], $result);
    }

    public function testReturnsSameStreamForFluentChaining(): void
    {
        // Given
        $stream = Stream::of([1]);

        // When
        $returned = $stream->mergeSortedWith([2]);

        // Then
        $this->assertSame($stream, $returned);
    }

    public function testIsLazyAndDoesNotTouchSourcesBeforeTerminalOperation(): void
    {
        // Given
        $aggregate = new CountingIteratorAggregateFixture([2]);
        $stream = Stream::of([1])->mergeSortedWith($aggregate);

        // Then
        $this->assertSame(0, $aggregate->getIteratorCallCount());

        // When
        $result = $stream->toArray();

        // Then
        $this->assertSame([1, 2], $result);
        $this->assertSame(1, $aggregate->getIteratorCallCount());
    }

    public function testPreservesStableTieBreakWithCurrentStreamFirst(): void
    {
        // Given
        $stream = Stream::of([0]);

        // When
        $result = $stream->mergeSortedWith([0.0])->toArray();

        // Then: equal under <=>, current stream's int must still precede the other source's float
        $this->assertSame(['integer', 'double'], \array_map('\gettype', $result));
    }
}

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

    /**
     * @test mergeSortedWith accepts named arguments that match its internal parameter names
     */
    public function testMergeSortedWithAcceptsNamedArgumentsMatchingInternalParameterNames(): void
    {
        // When
        $result = Stream::of([1, 4])->mergeSortedWith(
            keyFunc: [2, 5],
            nanMessage: [3, 6],
        )->toArray();

        // Then
        $this->assertSame([1, 2, 3, 4, 5, 6], $result);
    }

    /**
     * @test mergeSortedWith reads a stream of the generator it holds as independent copies of the generator
     */
    public function testMergeSortedWithStreamOfSameGenerator(): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator([1, 2, 3]);

        // When
        $result = Stream::of($generator)
            ->mergeSortedWith([2.0], Stream::of($generator))
            ->toArray();

        // Then
        $this->assertSame([1, 1, 2, 2.0, 2, 3, 3], $result);
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

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
}

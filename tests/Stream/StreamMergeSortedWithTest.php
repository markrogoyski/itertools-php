<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
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
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamAllEqualByTest extends \PHPUnit\Framework\TestCase
{
    public function testProjectsStreamValues(): void
    {
        // Given
        $stream = Stream::of([['group' => 1], ['group' => 1]]);

        // When
        $result = $stream->allEqualBy(static fn (array $item): int => $item['group']);

        // Then
        $this->assertTrue($result);
    }

    public function testDistinguishesFreshlyProjectedObjects(): void
    {
        // Given
        $stream = Stream::of([1, 2, 3]);

        // When
        $result = $stream->allEqualBy(static fn (int $value): object => (object) ['value' => $value]);

        // Then
        $this->assertFalse($result);
    }
}

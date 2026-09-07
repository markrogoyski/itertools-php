<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamPeekWithKeysTest extends \PHPUnit\Framework\TestCase
{
    public function testIsLazyPreservesItemsAndRunsBeforeDownstreamMap(): void
    {
        // Given
        $events = [];
        $stream = Stream::of(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4])
            ->peekWithKeys(static function (int $value, string $key) use (&$events): void {
                $events[] = "peek:$key:$value";
            });
        $this->assertSame([], $events);

        // When
        $result = $stream->map(static function (int $value) use (&$events): int {
            $events[] = "map:$value";
            return $value * 10;
        })->limit(3)->toAssociativeArray();

        // Then
        $this->assertSame(['a' => 10, 'b' => 20, 'c' => 30], $result);
        $this->assertSame(
            ['peek:a:1', 'map:1', 'peek:b:2', 'map:2', 'peek:c:3', 'map:3'],
            $events,
        );
    }
}

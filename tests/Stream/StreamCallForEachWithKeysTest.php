<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamCallForEachWithKeysTest extends \PHPUnit\Framework\TestCase
{
    public function testVisitsEveryValueAndKeyAndReturnsNull(): void
    {
        // Given
        $received = [];
        $stream = Stream::of(['a' => 1, 7 => 2]);

        // When
        $result = $stream->callForEachWithKeys(static function (int $value, mixed $key) use (&$received): void {
            $received[] = [$value, $key];
        });

        // Then
        $this->assertNull($result);
        $this->assertSame([[1, 'a'], [2, 7]], $received);
    }
}

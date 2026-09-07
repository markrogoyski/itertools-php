<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamMemoizeTest extends \PHPUnit\Framework\TestCase
{
    public function testReturnsSameStreamAndReplaysTerminalOperations(): void
    {
        // Given
        $pulls = 0;
        $source = (static function () use (&$pulls): \Generator {
            foreach ([1, 2, 3] as $value) {
                ++$pulls;
                yield $value;
            }
        })();
        $stream = Stream::of($source);

        // When
        $returned = $stream->memoize();
        $first = $stream->toArray();
        $second = $stream->toArray();

        // Then
        $this->assertSame($stream, $returned);
        $this->assertSame([1, 2, 3], $first);
        $this->assertSame($first, $second);
        $this->assertSame(3, $pulls);
    }

    public function testSecondMemoizationCachesLaterTransformations(): void
    {
        // Given
        $mapped = 0;
        $stream = Stream::of([1, 2, 3])->memoize()
            ->map(static function (int $value) use (&$mapped): int {
                ++$mapped;
                return $value * 2;
            })
            ->filter(static fn (int $value): bool => $value > 2)
            ->memoize();

        // When
        $first = $stream->toArray();
        $sum = $stream->toSum();

        // Then
        $this->assertSame([4, 6], $first);
        $this->assertSame(10, $sum);
        $this->assertSame(3, $mapped);
    }
}

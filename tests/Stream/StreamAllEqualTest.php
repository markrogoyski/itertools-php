<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamAllEqualTest extends \PHPUnit\Framework\TestCase
{
    public function testConsumesOnlyThroughFirstMismatchAfterTransformation(): void
    {
        // Given
        $source = (static function (): \Generator {
            yield 1;
            yield 2;
            throw new \RuntimeException('must not be pulled');
        })();

        // When
        $result = Stream::of($source)->map(static fn (int $value): int => $value * 10)->allEqual();

        // Then
        $this->assertFalse($result);
    }
}

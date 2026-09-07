<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamAllMatchWithKeysTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /** @dataProvider dataProviderForKeyAwareIterable */
    public function testDelegatesAfterLazyTransformation(iterable $data): void
    {
        // When
        $result = Stream::of($data)->map(static fn (int $value): int => $value + 1)
            ->allMatchWithKeys(static fn (int $value): bool => $value > 1);

        // Then
        $this->assertTrue($result);
    }
}

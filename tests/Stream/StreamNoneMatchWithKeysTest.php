<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamNoneMatchWithKeysTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /** @dataProvider dataProviderForKeyAwareIterable */
    public function testDelegatesAfterLazyTransformation(iterable $data): void
    {
        // When
        $result = Stream::of($data)->limit(3)
            ->noneMatchWithKeys(static fn (int $value): bool => $value > 3);

        // Then
        $this->assertTrue($result);
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamAnyMatchWithKeysTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /** @dataProvider dataProviderForKeyAwareIterable */
    public function testDelegatesAfterLazyTransformation(iterable $data): void
    {
        // When
        $result = Stream::of($data)->filter(static fn (int $value): bool => $value > 1)
            ->anyMatchWithKeys(static fn (int $value): bool => $value === 3);

        // Then
        $this->assertTrue($result);
    }
}

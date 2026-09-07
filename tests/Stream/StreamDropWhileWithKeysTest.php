<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamDropWhileWithKeysTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /** @dataProvider dataProviderForKeyAwareIterable */
    public function testDelegatesAndPreservesKeys(iterable $data, array $expectedInput): void
    {
        // When
        $result = Stream::of($data)->dropWhileWithKeys(static fn (int $value): bool => $value < 3)->toAssociativeArray();

        // Then
        $this->assertSame(\array_slice($expectedInput, 2, null, true), $result);
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamTakeWhileWithKeysTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /** @dataProvider dataProviderForKeyAwareIterable */
    public function testDelegatesAndPreservesKeys(iterable $data, array $expectedInput): void
    {
        // When
        $result = Stream::of($data)->takeWhileWithKeys(static fn (int $value): bool => $value < 3)->toAssociativeArray();

        // Then
        $this->assertSame(\array_slice($expectedInput, 0, 2, true), $result);
    }

    /** @dataProvider dataProviderForEmptyIterable */
    public function testEmptyIterableYieldsNothing(iterable $data): void
    {
        // When
        $result = Stream::of($data)->takeWhileWithKeys(static fn (mixed $value): bool => true)->toAssociativeArray();

        // Then
        $this->assertSame([], $result);
    }
}

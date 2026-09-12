<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamCallForEachWithKeysTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

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

    /** @dataProvider dataProviderForKeyAwareIterable */
    public function testVisitsEveryValueAndKeyAcrossIterableTypes(iterable $data, array $expectedInput): void
    {
        // Given
        $received = [];

        // When
        $result = Stream::of($data)->callForEachWithKeys(static function (int $value, mixed $key) use (&$received): void {
            $received[] = [$value, $key];
        });

        // Then
        $this->assertNull($result);
        $this->assertSame(\array_values($expectedInput), \array_column($received, 0));
        $this->assertSame(\array_keys($expectedInput), \array_column($received, 1));
    }
}

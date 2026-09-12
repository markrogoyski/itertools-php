<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;

class StreamPeekWithKeysTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /** @dataProvider dataProviderForKeyAwareIterable */
    public function testPreservesValuesAndKeysAcrossIterableTypes(iterable $data, array $expectedInput): void
    {
        // Given
        $received = [];

        // When
        $result = Stream::of($data)
            ->peekWithKeys(static function (int $value, mixed $key) use (&$received): void {
                $received[] = [$value, $key];
            })
            ->toAssociativeArray();

        // Then
        $this->assertSame($expectedInput, $result);
        $this->assertSame(\array_values($expectedInput), \array_column($received, 0));
        $this->assertSame(\array_keys($expectedInput), \array_column($received, 1));
    }

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

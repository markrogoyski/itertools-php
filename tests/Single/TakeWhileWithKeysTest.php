<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;

class TakeWhileWithKeysTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /** @dataProvider dataProviderForKeyAwareIterable */
    public function testPreservesKeysAndPassesValueThenKey(iterable $data, array $expectedInput): void
    {
        // Given
        $received = [];

        // When
        $result = \iterator_to_array(Single::takeWhileWithKeys(
            $data,
            static function (int $value, mixed $key) use (&$received): int {
                $received[] = [$value, $key];
                return 3 - $value;
            },
        ));

        // Then
        $this->assertSame(\array_slice($expectedInput, 0, 2, true), $result);
        $this->assertSame([1, 2, 3], \array_column($received, 0));
        $this->assertSame(\array_slice(\array_keys($expectedInput), 0, 3), \array_column($received, 1));
    }

    public function testStopsSourceAtFirstFailure(): void
    {
        // Given
        $pulled = 0;
        $source = (static function () use (&$pulled): \Generator {
            foreach ([1, 2, 3, 4] as $value) {
                ++$pulled;
                yield $value;
            }
        })();

        // When
        $result = \iterator_to_array(Single::takeWhileWithKeys($source, static fn (int $value): bool => $value < 3));

        // Then
        $this->assertSame([1, 2], $result);
        $this->assertSame(3, $pulled);
    }

    /** @dataProvider dataProviderForEmptyIterable */
    public function testEmptyIterableYieldsNothing(iterable $data): void
    {
        // When
        $result = \iterator_to_array(Single::takeWhileWithKeys($data, static fn (mixed $value): bool => true));

        // Then
        $this->assertSame([], $result);
    }
}

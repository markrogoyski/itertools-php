<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;

class DropWhileWithKeysTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /** @dataProvider dataProviderForKeyAwareIterable */
    public function testPreservesKeysAndStopsCallingPredicateAfterBoundary(iterable $data, array $expectedInput): void
    {
        // Given
        $received = [];

        // When
        $result = \iterator_to_array(Single::dropWhileWithKeys(
            $data,
            static function (int $value, mixed $key) use (&$received): int {
                $received[] = [$value, $key];
                return 3 - $value;
            },
        ));

        // Then
        $this->assertSame(\array_slice($expectedInput, 2, null, true), $result);
        $this->assertSame([1, 2, 3], \array_column($received, 0));
        $this->assertSame(\array_slice(\array_keys($expectedInput), 0, 3), \array_column($received, 1));
    }

    /** @dataProvider dataProviderForEmptyIterable */
    public function testEmptyIterableYieldsNothing(iterable $data): void
    {
        // When
        $result = \iterator_to_array(Single::dropWhileWithKeys($data, static fn (mixed $value): bool => true));

        // Then
        $this->assertSame([], $result);
    }
}

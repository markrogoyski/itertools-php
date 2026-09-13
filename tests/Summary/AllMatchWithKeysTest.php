<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;

class AllMatchWithKeysTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /** @dataProvider dataProviderForKeyAwareIterable */
    public function testReceivesValueThenKeyAndCoercesTruthyResults(iterable $data, array $expectedInput): void
    {
        // Given
        $received = [];

        // When
        $result = Summary::allMatchWithKeys($data, static function (int $value, mixed $key) use (&$received): int {
            $received[] = [$value, $key];
            return $value;
        });

        // Then
        $this->assertTrue($result);
        $this->assertSame([1, 2, 3, 4], \array_column($received, 0));
        $this->assertSame(\array_keys($expectedInput), \array_column($received, 1));
    }

    public function testShortCircuitsWithoutPullingFollowingValue(): void
    {
        // Given
        $source = (static function (): \Generator {
            yield 1;
            yield 0;
            throw new \RuntimeException('must not be pulled');
        })();

        // When / Then
        $this->assertFalse(Summary::allMatchWithKeys($source, static fn (int $value): int => $value));
    }

    /** @dataProvider dataProviderForEmptyIterable */
    public function testEmptyIterableIsTrue(iterable $data): void
    {
        // When / Then
        $this->assertTrue(Summary::allMatchWithKeys($data, static fn (mixed $value, mixed $key): bool => false));
    }
}

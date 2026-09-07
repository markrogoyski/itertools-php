<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;

class NoneMatchWithKeysTest extends \PHPUnit\Framework\TestCase
{
    use \IterTools\Tests\Fixture\DataProvider;

    /** @dataProvider dataProviderForKeyAwareIterable */
    public function testReceivesValueThenKeyAndStopsAtFirstMatch(iterable $data): void
    {
        // Given
        $received = [];

        // When
        $result = Summary::noneMatchWithKeys($data, static function (int $value, mixed $key) use (&$received): int {
            $received[] = [$value, $key];
            return $value === 3 ? 1 : 0;
        });

        // Then
        $this->assertFalse($result);
        $this->assertSame([1, 2, 3], \array_column($received, 0));
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Transform;

use IterTools\Transform;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

/**
 * A float NAN returned by a predicate is truthy as (bool) NAN is, and doesn't raise PHP 8.5's
 * "unexpected NAN value was coerced to bool" warning.
 */
class NanTruthinessTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         partition treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testPartitionNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;
        $raised    = [];
        $previous  = \set_error_handler(static function (int $errno, string $errstr) use (&$raised): bool {
            $raised[] = $errstr;
            return true;
        });

        try {
            // When
            $result = Transform::partition($data, $predicate);
        } finally {
            \set_error_handler($previous);
        }

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([[1, 2, 3], [0, 0]], $result);
    }

    /**
     * Elements for a predicate that returns NAN for each non-zero element and 0.0 for each zero.
     *
     * Expected results follow PHP's documented conversion to bool, under which NAN is true
     * (https://www.php.net/manual/en/language.types.boolean.php); Python agrees: bool(float('nan')) is True.
     *
     * @return array<array{iterable<int>}>
     */
    public static function dataProviderForNanPredicate(): array
    {
        return [
            [[1, 2, 0, 3, 0]],
            [GeneratorFixture::getGenerator([1, 2, 0, 3, 0])],
            [new ArrayIteratorFixture([1, 2, 0, 3, 0])],
            [new IteratorAggregateFixture([1, 2, 0, 3, 0])],
        ];
    }
}

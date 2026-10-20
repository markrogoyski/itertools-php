<?php

declare(strict_types=1);

namespace IterTools\Tests\Reduce;

use IterTools\Reduce;
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
     * @test         toFirstMatch treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testToFirstMatchNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(static fn (): mixed => Reduce::toFirstMatch($data, $predicate));

        // Then
        $this->assertSame([], $raised);
        $this->assertSame(5, $result);
    }

    /**
     * @test         toFirstMatchIndex treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testToFirstMatchIndexNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): mixed => Reduce::toFirstMatchIndex($data, $predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame(1, $result);
    }

    /**
     * @test         toFirstMatchKey treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testToFirstMatchKeyNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): mixed => Reduce::toFirstMatchKey($data, $predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame(1, $result);
    }

    /**
     * @test         toLastMatch treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testToLastMatchNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(static fn (): mixed => Reduce::toLastMatch($data, $predicate));

        // Then
        $this->assertSame([], $raised);
        $this->assertSame(7, $result);
    }

    /**
     * @test         toLastMatchIndex treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testToLastMatchIndexNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): mixed => Reduce::toLastMatchIndex($data, $predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame(3, $result);
    }

    /**
     * @test         toLastMatchKey treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testToLastMatchKeyNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): mixed => Reduce::toLastMatchKey($data, $predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame(3, $result);
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
            [[0, 5, 0, 7, 0]],
            [GeneratorFixture::getGenerator([0, 5, 0, 7, 0])],
            [new ArrayIteratorFixture([0, 5, 0, 7, 0])],
            [new IteratorAggregateFixture([0, 5, 0, 7, 0])],
        ];
    }

    /**
     * Runs $operation, recording the message of every PHP error it raises instead of reporting it.
     *
     * @param callable(): mixed $operation
     *
     * @return array{mixed, list<string>} the operation's result and the recorded messages
     */
    private static function recordingErrors(callable $operation): array
    {
        $raised   = [];
        $previous = \set_error_handler(static function (int $errno, string $errstr) use (&$raised): bool {
            $raised[] = $errstr;
            return true;
        });

        try {
            $result = $operation();
        } finally {
            \set_error_handler($previous);
        }

        return [$result, $raised];
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Summary;

use IterTools\Summary;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

/**
 * A float NAN that reaches the library's bool coercion, as an element or as a predicate's return value, is
 * truthy as (bool) NAN is, and doesn't raise PHP 8.5's "unexpected NAN value was coerced to bool" warning.
 */
class NanTruthinessTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         allMatch treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testAllMatchNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => \NAN;

        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Summary::allMatch($data, $predicate));

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         allMatchWithKeys treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testAllMatchWithKeysNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum, int $key): float => \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): bool => Summary::allMatchWithKeys($data, $predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         anyMatch treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testAnyMatchNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Summary::anyMatch($data, $predicate));

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         anyMatchWithKeys treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testAnyMatchWithKeysNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum, int $key): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): bool => Summary::anyMatchWithKeys($data, $predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         noneMatch treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testNoneMatchNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Summary::noneMatch($data, $predicate));

        // Then
        $this->assertSame([], $raised);
        $this->assertFalse($result);
    }

    /**
     * @test         noneMatchWithKeys treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testNoneMatchWithKeysNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum, int $key): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): bool => Summary::noneMatchWithKeys($data, $predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertFalse($result);
    }

    /**
     * @test         exactlyN with the default predicate counts NAN elements as true, without a warning
     * @dataProvider dataProviderForNanElements
     * @param        iterable<int|float> $data
     */
    public function testExactlyNDefaultPredicateNanElements(iterable $data): void
    {
        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Summary::exactlyN($data, 3));

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         exactlyN counts a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testExactlyNNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): bool => Summary::exactlyN($data, 3, $predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         atLeastN with the default predicate counts NAN elements as true, without a warning
     * @dataProvider dataProviderForNanElements
     * @param        iterable<int|float> $data
     */
    public function testAtLeastNDefaultPredicateNanElements(iterable $data): void
    {
        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Summary::atLeastN($data, 3));

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         atLeastN counts a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testAtLeastNNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): bool => Summary::atLeastN($data, 3, $predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         atMostN with the default predicate counts NAN elements as true, without a warning
     * @dataProvider dataProviderForNanElements
     * @param        iterable<int|float> $data
     */
    public function testAtMostNDefaultPredicateNanElements(iterable $data): void
    {
        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Summary::atMostN($data, 2));

        // Then
        $this->assertSame([], $raised);
        $this->assertFalse($result);
    }

    /**
     * @test         atMostN counts a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testAtMostNNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): bool => Summary::atMostN($data, 2, $predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertFalse($result);
    }

    /**
     * @test         isPartitioned with the default predicate treats NAN elements as true, without a warning
     * @dataProvider dataProviderForNanElements
     * @param        iterable<int|float> $data
     */
    public function testIsPartitionedDefaultPredicateNanElements(iterable $data): void
    {
        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Summary::isPartitioned($data));

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         isPartitioned treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testIsPartitionedNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): bool => Summary::isPartitioned($data, $predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertFalse($result);
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

    /**
     * Elements for the default predicate, the element's own truthiness.
     *
     * Expected results follow PHP's documented conversion to bool, under which NAN is true
     * (https://www.php.net/manual/en/language.types.boolean.php); Python agrees: bool(float('nan')) is True.
     *
     * @return array<array{iterable<int|float>}>
     */
    public static function dataProviderForNanElements(): array
    {
        return [
            [[\NAN, 1, \NAN, 0, 0.0]],
            [GeneratorFixture::getGenerator([\NAN, 1, \NAN, 0, 0.0])],
            [new ArrayIteratorFixture([\NAN, 1, \NAN, 0, 0.0])],
            [new IteratorAggregateFixture([\NAN, 1, \NAN, 0, 0.0])],
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

<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

/**
 * A float NAN that reaches the library's bool coercion, as an element or as a predicate's return value, is
 * truthy as (bool) NAN is, and doesn't raise PHP 8.5's "unexpected NAN value was coerced to bool" warning.
 */
class StreamNanTruthinessTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         Stream filter treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testFilterNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Stream::of($data)->filter($predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([0 => 1, 1 => 2, 3 => 3], $result);
    }

    /**
     * @test         Stream filterTrue with the default predicate keeps NAN elements, without a warning
     * @dataProvider dataProviderForNanElements
     * @param        iterable<int|float> $data
     */
    public function testFilterTrueDefaultPredicateNanElements(iterable $data): void
    {
        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Stream::of($data)->filterTrue())
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([0, 1, 2], \array_keys($result));
    }

    /**
     * @test         Stream filterTrue treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testFilterTrueNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Stream::of($data)->filterTrue($predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([0 => 1, 1 => 2, 3 => 3], $result);
    }

    /**
     * @test         Stream filterFalse with the default predicate drops NAN elements, without a warning
     * @dataProvider dataProviderForNanElements
     * @param        iterable<int|float> $data
     */
    public function testFilterFalseDefaultPredicateNanElements(iterable $data): void
    {
        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Stream::of($data)->filterFalse())
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([3 => 0, 4 => 0.0], $result);
    }

    /**
     * @test         Stream filterFalse treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testFilterFalseNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Stream::of($data)->filterFalse($predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([2 => 0, 4 => 0], $result);
    }

    /**
     * @test         Stream filterKeys treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testFilterKeysNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $key): float => $key % 2 === 0 ? \NAN : 0.0;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Stream::of($data)->filterKeys($predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([0 => 1, 2 => 0, 4 => 0], $result);
    }

    /**
     * @test         Stream filterWithKeys treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testFilterWithKeysNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum, int $key): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Stream::of($data)->filterWithKeys($predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([0 => 1, 1 => 2, 3 => 3], $result);
    }

    /**
     * @test         Stream dropWhile treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testDropWhileNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Stream::of($data)->dropWhile($predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([2 => 0, 3 => 3, 4 => 0], $result);
    }

    /**
     * @test         Stream dropWhileWithKeys treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testDropWhileWithKeysNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum, int $key): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Stream::of($data)->dropWhileWithKeys($predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([2 => 0, 3 => 3, 4 => 0], $result);
    }

    /**
     * @test         Stream takeWhile treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testTakeWhileNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Stream::of($data)->takeWhile($predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([0 => 1, 1 => 2], $result);
    }

    /**
     * @test         Stream takeWhileWithKeys treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testTakeWhileWithKeysNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum, int $key): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Stream::of($data)->takeWhileWithKeys($predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([0 => 1, 1 => 2], $result);
    }

    /**
     * @test         Stream splitWhen treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testSplitWhenNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => Stream::of($data)->splitWhen($predicate)->toArray()
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([[1], [2, 0], [3, 0]], $result);
    }

    /**
     * @test         Stream allMatch treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testAllMatchNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => \NAN;

        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Stream::of($data)->allMatch($predicate));

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         Stream allMatchWithKeys treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testAllMatchWithKeysNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum, int $key): float => \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): bool => Stream::of($data)->allMatchWithKeys($predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         Stream anyMatch treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testAnyMatchNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Stream::of($data)->anyMatch($predicate));

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         Stream anyMatchWithKeys treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testAnyMatchWithKeysNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum, int $key): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): bool => Stream::of($data)->anyMatchWithKeys($predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         Stream noneMatch treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testNoneMatchNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Stream::of($data)->noneMatch($predicate));

        // Then
        $this->assertSame([], $raised);
        $this->assertFalse($result);
    }

    /**
     * @test         Stream noneMatchWithKeys treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testNoneMatchWithKeysNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum, int $key): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): bool => Stream::of($data)->noneMatchWithKeys($predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertFalse($result);
    }

    /**
     * @test         Stream exactlyN with the default predicate counts NAN elements as true, without a warning
     * @dataProvider dataProviderForNanElements
     * @param        iterable<int|float> $data
     */
    public function testExactlyNDefaultPredicateNanElements(iterable $data): void
    {
        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Stream::of($data)->exactlyN(3));

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         Stream exactlyN counts a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testExactlyNNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Stream::of($data)->exactlyN(3, $predicate));

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         Stream atLeastN with the default predicate counts NAN elements as true, without a warning
     * @dataProvider dataProviderForNanElements
     * @param        iterable<int|float> $data
     */
    public function testAtLeastNDefaultPredicateNanElements(iterable $data): void
    {
        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Stream::of($data)->atLeastN(3));

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         Stream atLeastN counts a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testAtLeastNNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Stream::of($data)->atLeastN(3, $predicate));

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         Stream atMostN with the default predicate counts NAN elements as true, without a warning
     * @dataProvider dataProviderForNanElements
     * @param        iterable<int|float> $data
     */
    public function testAtMostNDefaultPredicateNanElements(iterable $data): void
    {
        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Stream::of($data)->atMostN(2));

        // Then
        $this->assertSame([], $raised);
        $this->assertFalse($result);
    }

    /**
     * @test         Stream atMostN counts a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testAtMostNNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Stream::of($data)->atMostN(2, $predicate));

        // Then
        $this->assertSame([], $raised);
        $this->assertFalse($result);
    }

    /**
     * @test         Stream isPartitioned with the default predicate treats NAN elements as true, without a warning
     * @dataProvider dataProviderForNanElements
     * @param        iterable<int|float> $data
     */
    public function testIsPartitionedDefaultPredicateNanElements(iterable $data): void
    {
        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Stream::of($data)->isPartitioned());

        // Then
        $this->assertSame([], $raised);
        $this->assertTrue($result);
    }

    /**
     * @test         Stream isPartitioned treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testIsPartitionedNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(static fn (): bool => Stream::of($data)->isPartitioned($predicate));

        // Then
        $this->assertSame([], $raised);
        $this->assertFalse($result);
    }

    /**
     * @test         Stream toFirstMatch treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testToFirstMatchNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(static fn (): mixed => Stream::of($data)->toFirstMatch($predicate));

        // Then
        $this->assertSame([], $raised);
        $this->assertSame(1, $result);
    }

    /**
     * @test         Stream toFirstMatchIndex treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testToFirstMatchIndexNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): mixed => Stream::of($data)->toFirstMatchIndex($predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame(0, $result);
    }

    /**
     * @test         Stream toFirstMatchKey treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testToFirstMatchKeyNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): mixed => Stream::of($data)->toFirstMatchKey($predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame(0, $result);
    }

    /**
     * @test         Stream toLastMatch treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testToLastMatchNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(static fn (): mixed => Stream::of($data)->toLastMatch($predicate));

        // Then
        $this->assertSame([], $raised);
        $this->assertSame(3, $result);
    }

    /**
     * @test         Stream toLastMatchIndex treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testToLastMatchIndexNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): mixed => Stream::of($data)->toLastMatchIndex($predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame(3, $result);
    }

    /**
     * @test         Stream toLastMatchKey treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testToLastMatchKeyNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): mixed => Stream::of($data)->toLastMatchKey($predicate)
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame(3, $result);
    }

    /**
     * @test         Stream toPartition treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testToPartitionNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(static fn (): array => Stream::of($data)->toPartition($predicate));

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([[1, 2, 3], [0, 0]], $result);
    }

    /**
     * @test         Stream compress treats a NAN selector as true, without a warning
     * @dataProvider dataProviderForNanSelectors
     * @param        iterable<string>    $data
     * @param        iterable<int|float> $selectors
     */
    public function testCompressNanSelectors(iterable $data, iterable $selectors): void
    {
        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => Stream::of($data)->compress($selectors)->toArray()
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame(['a', 'c', 'e'], $result);
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
     * Expected results follow PHP's documented conversion to bool, under which NAN is true
     * (https://www.php.net/manual/en/language.types.boolean.php); Python agrees: bool(float('nan')) is True.
     *
     * @return array<array{iterable<string>, iterable<int|float>}>
     */
    public static function dataProviderForNanSelectors(): array
    {
        return [
            [
                ['a', 'b', 'c', 'd', 'e'],
                [\NAN, 0, \NAN, 0.0, 1],
            ],
            [
                GeneratorFixture::getGenerator(['a', 'b', 'c', 'd', 'e']),
                GeneratorFixture::getGenerator([\NAN, 0, \NAN, 0.0, 1]),
            ],
            [
                new ArrayIteratorFixture(['a', 'b', 'c', 'd', 'e']),
                new ArrayIteratorFixture([\NAN, 0, \NAN, 0.0, 1]),
            ],
            [
                new IteratorAggregateFixture(['a', 'b', 'c', 'd', 'e']),
                new IteratorAggregateFixture([\NAN, 0, \NAN, 0.0, 1]),
            ],
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

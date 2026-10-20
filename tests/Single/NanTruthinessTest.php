<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
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
     * @test         filter treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testFilterNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Single::filter($data, $predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([0 => 1, 1 => 2, 3 => 3], $result);
    }

    /**
     * @test         filterTrue with the default predicate keeps NAN elements, without a warning
     * @dataProvider dataProviderForNanElements
     * @param        iterable<int|float> $data
     */
    public function testFilterTrueDefaultPredicateNanElements(iterable $data): void
    {
        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Single::filterTrue($data))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([0, 1, 2], \array_keys($result));
    }

    /**
     * @test         filterTrue treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testFilterTrueNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Single::filterTrue($data, $predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([0 => 1, 1 => 2, 3 => 3], $result);
    }

    /**
     * @test         filterFalse with the default predicate drops NAN elements, without a warning
     * @dataProvider dataProviderForNanElements
     * @param        iterable<int|float> $data
     */
    public function testFilterFalseDefaultPredicateNanElements(iterable $data): void
    {
        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Single::filterFalse($data))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([3 => 0, 4 => 0.0], $result);
    }

    /**
     * @test         filterFalse treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testFilterFalseNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Single::filterFalse($data, $predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([2 => 0, 4 => 0], $result);
    }

    /**
     * @test         filterKeys treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testFilterKeysNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $key): float => $key % 2 === 0 ? \NAN : 0.0;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Single::filterKeys($data, $predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([0 => 1, 2 => 0, 4 => 0], $result);
    }

    /**
     * @test         filterWithKeys treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testFilterWithKeysNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum, int $key): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Single::filterWithKeys($data, $predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([0 => 1, 1 => 2, 3 => 3], $result);
    }

    /**
     * @test         dropWhile treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testDropWhileNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Single::dropWhile($data, $predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([2 => 0, 3 => 3, 4 => 0], $result);
    }

    /**
     * @test         dropWhileWithKeys treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testDropWhileWithKeysNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum, int $key): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Single::dropWhileWithKeys($data, $predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([2 => 0, 3 => 3, 4 => 0], $result);
    }

    /**
     * @test         takeWhile treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testTakeWhileNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Single::takeWhile($data, $predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([0 => 1, 1 => 2], $result);
    }

    /**
     * @test         takeWhileWithKeys treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testTakeWhileWithKeysNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum, int $key): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Single::takeWhileWithKeys($data, $predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([0 => 1, 1 => 2], $result);
    }

    /**
     * @test         splitWhen treats a NAN predicate result as true, without a warning
     * @dataProvider dataProviderForNanPredicate
     * @param        iterable<int> $data
     */
    public function testSplitWhenNanPredicateResult(iterable $data): void
    {
        // Given
        $predicate = static fn (int $datum): float => $datum === 0 ? 0.0 : \NAN;

        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Single::splitWhen($data, $predicate))
        );

        // Then
        $this->assertSame([], $raised);
        $this->assertSame([[1], [2, 0], [3, 0]], $result);
    }

    /**
     * @test         compress treats a NAN selector as true, without a warning
     * @dataProvider dataProviderForNanSelectors
     * @param        iterable<string>    $data
     * @param        iterable<int|float> $selectors
     */
    public function testCompressNanSelectors(iterable $data, iterable $selectors): void
    {
        // When
        [$result, $raised] = self::recordingErrors(
            static fn (): array => \iterator_to_array(Single::compress($data, $selectors), false)
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

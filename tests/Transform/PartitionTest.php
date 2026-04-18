<?php

declare(strict_types=1);

namespace IterTools\Tests\Transform;

use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;
use IterTools\Transform;

class PartitionTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test partition example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $numbers = [1, 2, 3, 4, 5, 6];

        // When
        [$evens, $odds] = Transform::partition($numbers, fn (int $n): bool => $n % 2 === 0);

        // Then
        $this->assertSame([2, 4, 6], $evens);
        $this->assertSame([1, 3, 5], $odds);
    }

    /**
     * @test         partition (array)
     * @dataProvider dataProviderForPartition
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        array{0: array<mixed>, 1: array<mixed>} $expected
     */
    public function testPartitionArray(array $data, callable $predicate, array $expected): void
    {
        // When
        $result = Transform::partition($data, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         partition (Generator)
     * @dataProvider dataProviderForPartition
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        array{0: array<mixed>, 1: array<mixed>} $expected
     */
    public function testPartitionGenerator(array $data, callable $predicate, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = Transform::partition($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         partition (Iterator)
     * @dataProvider dataProviderForPartition
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        array{0: array<mixed>, 1: array<mixed>} $expected
     */
    public function testPartitionIterator(array $data, callable $predicate, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = Transform::partition($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         partition (IteratorAggregate)
     * @dataProvider dataProviderForPartition
     * @param        array<mixed> $data
     * @param        callable     $predicate
     * @param        array{0: array<mixed>, 1: array<mixed>} $expected
     */
    public function testPartitionIteratorAggregate(array $data, callable $predicate, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = Transform::partition($iterable, $predicate);

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForPartition(): array
    {
        $isEven     = fn (int $n): bool => $n % 2 === 0;
        $isPositive = fn (int $n): bool => $n > 0;
        $truthy     = fn (mixed $v): mixed => $v;

        return [
            // all truthy
            [[2, 4, 6], $isEven, [[2, 4, 6], []]],
            // all falsy
            [[1, 3, 5], $isEven, [[], [1, 3, 5]]],
            // mixed — preserves input order on each side
            [[1, 2, 3, 4, 5, 6], $isEven, [[2, 4, 6], [1, 3, 5]]],
            // single truthy element
            [[2], $isEven, [[2], []]],
            // single falsy element
            [[1], $isEven, [[], [1]]],
            // negatives and positives
            [[-2, -1, 0, 1, 2], $isPositive, [[1, 2], [-2, -1, 0]]],
            // coercion — 0, '', null, [] are falsy
            [[1, 0, 'x', '', 'y', null, [1], []], $truthy, [[1, 'x', 'y', [1]], [0, '', null, []]]],
            // predicate returning non-bool values (string, int) coerced via (bool)
            [[0, 1, 2, 3], fn (int $n): int => $n, [[1, 2, 3], [0]]],
            [['', 'a', 'b', ''], fn (string $s): string => $s, [['a', 'b'], ['', '']]],
        ];
    }

    /**
     * @test         partition empty iterable returns two empty arrays
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterable(iterable $data): void
    {
        // When
        $result = Transform::partition($data, fn (mixed $x): bool => (bool) $x);

        // Then
        $this->assertSame([[], []], $result);
    }

    /**
     * @test partition reindexes truthy output (discards source string keys)
     */
    public function testReindexesOutputWithStringKeys(): void
    {
        // Given
        $data = ['a' => 2, 'b' => 1, 'c' => 4, 'd' => 3];

        // When
        [$even, $odd] = Transform::partition($data, fn (int $n): bool => $n % 2 === 0);

        // Then
        $this->assertSame([2, 4], $even);
        $this->assertSame([0, 1], \array_keys($even));
        $this->assertSame([1, 3], $odd);
        $this->assertSame([0, 1], \array_keys($odd));
    }

    /**
     * @test partition reindexes output (discards source numeric keys)
     */
    public function testReindexesOutputWithNumericKeys(): void
    {
        // Given
        $data = [10 => 2, 20 => 1, 30 => 4, 40 => 3];

        // When
        [$even, $odd] = Transform::partition($data, fn (int $n): bool => $n % 2 === 0);

        // Then
        $this->assertSame([2, 4], $even);
        $this->assertSame([0, 1], \array_keys($even));
        $this->assertSame([1, 3], $odd);
        $this->assertSame([0, 1], \array_keys($odd));
    }

    /**
     * @test partition uses (bool) coercion — non-bool predicate return values
     */
    public function testPredicateReturnValueCoerced(): void
    {
        // Given
        $data = [0, 1, 2, 3, 0];

        // When — predicate returns int (0 → falsy, nonzero → truthy)
        [$truthy, $falsy] = Transform::partition($data, fn (int $n): int => $n);

        // Then
        $this->assertSame([1, 2, 3], $truthy);
        $this->assertSame([0, 0], $falsy);
    }

    /**
     * @test partition returns list array [truthy, falsy] with integer keys 0 and 1
     */
    public function testReturnsListArray(): void
    {
        // Given
        $data = [1, 2, 3];

        // When
        $result = Transform::partition($data, fn (int $n): bool => $n > 1);

        // Then
        $this->assertIsArray($result);
        $this->assertSame([0, 1], \array_keys($result));
    }
}

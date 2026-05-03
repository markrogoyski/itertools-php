<?php

declare(strict_types=1);

namespace IterTools\Tests\Single;

use IterTools\Single;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class SplitWhenTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test splitWhen example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2, 0, 3, 0, 4];

        // When
        $result = [];
        foreach (Single::splitWhen($data, fn (int $x): bool => $x === 0) as $group) {
            $result[] = $group;
        }

        // Then
        $this->assertSame([[1, 2], [0, 3], [0, 4]], $result);
    }

    /**
     * @test         splitWhen (array)
     * @dataProvider dataProviderForSplitWhen
     * @param        array<mixed>     $data
     * @param        callable         $predicate
     * @param        array<array<mixed>> $expected
     */
    public function testSplitWhenArray(array $data, callable $predicate, array $expected): void
    {
        // When
        $result = [];
        foreach (Single::splitWhen($data, $predicate) as $group) {
            $result[] = $group;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         splitWhen (Generator)
     * @dataProvider dataProviderForSplitWhen
     * @param        array<mixed>     $data
     * @param        callable         $predicate
     * @param        array<array<mixed>> $expected
     */
    public function testSplitWhenGenerator(array $data, callable $predicate, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = [];
        foreach (Single::splitWhen($iterable, $predicate) as $group) {
            $result[] = $group;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         splitWhen (Iterator)
     * @dataProvider dataProviderForSplitWhen
     * @param        array<mixed>     $data
     * @param        callable         $predicate
     * @param        array<array<mixed>> $expected
     */
    public function testSplitWhenIterator(array $data, callable $predicate, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = [];
        foreach (Single::splitWhen($iterable, $predicate) as $group) {
            $result[] = $group;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         splitWhen (IteratorAggregate)
     * @dataProvider dataProviderForSplitWhen
     * @param        array<mixed>     $data
     * @param        callable         $predicate
     * @param        array<array<mixed>> $expected
     */
    public function testSplitWhenIteratorAggregate(array $data, callable $predicate, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = [];
        foreach (Single::splitWhen($iterable, $predicate) as $group) {
            $result[] = $group;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForSplitWhen(): array
    {
        return [
            // canonical example
            [[1, 2, 0, 3, 0, 4], fn (int $x): bool => $x === 0, [[1, 2], [0, 3], [0, 4]]],
            // predicate matches first element — no leading empty group
            [[0, 1, 2, 0, 3], fn (int $x): bool => $x === 0, [[0, 1, 2], [0, 3]]],
            // predicate never matches — single group with all
            [[1, 2, 3, 4], fn (int $x): bool => $x === 0, [[1, 2, 3, 4]]],
            // every element matches — each is its own group
            [[0, 0, 0], fn (int $x): bool => $x === 0, [[0], [0], [0]]],
            // single element non-matching
            [[7], fn (int $x): bool => $x === 0, [[7]]],
            // single element matching
            [[0], fn (int $x): bool => $x === 0, [[0]]],
            // alternating
            [[1, 0, 1, 0, 1], fn (int $x): bool => $x === 0, [[1], [0, 1], [0, 1]]],
            // strings
            [['a', 'b', '|', 'c'], fn (string $x): bool => $x === '|', [['a', 'b'], ['|', 'c']]],
        ];
    }

    /**
     * @test         splitWhen empty iterable yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterable(iterable $data): void
    {
        // When
        $result = [];
        foreach (Single::splitWhen($data, fn ($_): bool => true) as $group) {
            $result[] = $group;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test splitWhen yields list arrays (sequentially indexed groups)
     */
    public function testYieldsListArrays(): void
    {
        // Given
        $data = [1, 2, 0, 3];

        // When
        $groups = [];
        foreach (Single::splitWhen($data, fn (int $x): bool => $x === 0) as $key => $group) {
            $groups[$key] = $group;
        }

        // Then
        $this->assertSame([0, 1], \array_keys($groups));
        foreach ($groups as $group) {
            $this->assertTrue(\array_is_list($group));
        }
    }

    /**
     * @test splitWhen discards source keys
     */
    public function testDiscardsSourceKeys(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 0, 'd' => 3];

        // When
        $result = [];
        foreach (Single::splitWhen($data, fn (int $x): bool => $x === 0) as $group) {
            $result[] = $group;
        }

        // Then
        $this->assertSame([[1, 2], [0, 3]], $result);
    }
}

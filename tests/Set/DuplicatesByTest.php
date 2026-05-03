<?php

declare(strict_types=1);

namespace IterTools\Tests\Set;

use IterTools\Set;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class DuplicatesByTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test duplicatesBy example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [
            ['id' => 1, 'name' => 'a'],
            ['id' => 2, 'name' => 'b'],
            ['id' => 1, 'name' => 'c'],
            ['id' => 3, 'name' => 'd'],
            ['id' => 2, 'name' => 'e'],
        ];

        // When
        $result = [];
        foreach (Set::duplicatesBy($data, fn (array $x): int => $x['id']) as $item) {
            $result[] = $item;
        }

        // Then
        $this->assertSame([
            ['id' => 1, 'name' => 'c'],
            ['id' => 2, 'name' => 'e'],
        ], $result);
    }

    /**
     * @test         duplicatesBy (array)
     * @dataProvider dataProviderForDuplicatesBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testDuplicatesByArray(array $data, callable $keyFn, array $expected): void
    {
        // When
        $result = [];
        foreach (Set::duplicatesBy($data, $keyFn) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         duplicatesBy (Generator)
     * @dataProvider dataProviderForDuplicatesBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testDuplicatesByGenerator(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = [];
        foreach (Set::duplicatesBy($iterable, $keyFn) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         duplicatesBy (Iterator)
     * @dataProvider dataProviderForDuplicatesBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testDuplicatesByIterator(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = [];
        foreach (Set::duplicatesBy($iterable, $keyFn) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         duplicatesBy (IteratorAggregate)
     * @dataProvider dataProviderForDuplicatesBy
     * @param        array<mixed> $data
     * @param        callable     $keyFn
     * @param        array<mixed> $expected
     */
    public function testDuplicatesByIteratorAggregate(array $data, callable $keyFn, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = [];
        foreach (Set::duplicatesBy($iterable, $keyFn) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForDuplicatesBy(): array
    {
        return [
            // identity key
            [[1, 2, 1, 1, 2, 3], fn (int $x): int => $x, [1, 2]],
            // empty input
            [[], fn (int $x): int => $x, []],
            // all unique under key
            [[1, 2, 3], fn (int $x): int => $x, []],
            // all collide under key — yielded once at second occurrence
            [[1, 11, 21], fn (int $x): int => $x % 10, [11]],
            // by length
            [['a', 'bb', 'cc', 'd', 'eee'], fn (string $x): int => \strlen($x), ['cc', 'd']],
            // single element
            [[42], fn (int $x): int => $x, []],
        ];
    }

    /**
     * @test         duplicatesBy empty iterable yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterable(iterable $data): void
    {
        // When
        $result = [];
        foreach (Set::duplicatesBy($data, fn ($x) => $x) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test duplicatesBy output keys are sequential 0-indexed
     */
    public function testKeysSequential(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 1, 'c' => 2, 'd' => 2];

        // When
        $keys = [];
        foreach (Set::duplicatesBy($data, fn ($x) => $x) as $key => $value) {
            $keys[] = $key;
        }

        // Then
        $this->assertSame([0, 1], $keys);
    }
}

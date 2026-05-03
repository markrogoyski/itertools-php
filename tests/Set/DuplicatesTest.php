<?php

declare(strict_types=1);

namespace IterTools\Tests\Set;

use IterTools\Set;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class DuplicatesTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test duplicates example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 2, 1, 1, 2, 3];

        // When
        $result = [];
        foreach (Set::duplicates($data) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame([1, 2], $result);
    }

    /**
     * @test         duplicates (array)
     * @dataProvider dataProviderForDuplicates
     * @param        array<mixed> $data
     * @param        bool         $strict
     * @param        array<mixed> $expected
     */
    public function testDuplicatesArray(array $data, bool $strict, array $expected): void
    {
        // When
        $result = [];
        foreach (Set::duplicates($data, $strict) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         duplicates (Generator)
     * @dataProvider dataProviderForDuplicates
     * @param        array<mixed> $data
     * @param        bool         $strict
     * @param        array<mixed> $expected
     */
    public function testDuplicatesGenerator(array $data, bool $strict, array $expected): void
    {
        // Given
        $iterable = GeneratorFixture::getGenerator($data);

        // When
        $result = [];
        foreach (Set::duplicates($iterable, $strict) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         duplicates (Iterator)
     * @dataProvider dataProviderForDuplicates
     * @param        array<mixed> $data
     * @param        bool         $strict
     * @param        array<mixed> $expected
     */
    public function testDuplicatesIterator(array $data, bool $strict, array $expected): void
    {
        // Given
        $iterable = new ArrayIteratorFixture($data);

        // When
        $result = [];
        foreach (Set::duplicates($iterable, $strict) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         duplicates (IteratorAggregate)
     * @dataProvider dataProviderForDuplicates
     * @param        array<mixed> $data
     * @param        bool         $strict
     * @param        array<mixed> $expected
     */
    public function testDuplicatesIteratorAggregate(array $data, bool $strict, array $expected): void
    {
        // Given
        $iterable = new IteratorAggregateFixture($data);

        // When
        $result = [];
        foreach (Set::duplicates($iterable, $strict) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForDuplicates(): array
    {
        return [
            // canonical example
            [[1, 2, 1, 1, 2, 3], true, [1, 2]],
            // all unique
            [[1, 2, 3, 4], true, []],
            // all same
            [[7, 7, 7], true, [7]],
            // single element
            [[42], true, []],
            // strict: '1' and 1 are different
            [[1, '1', 2], true, []],
            // coercive: '1' and 1 collide — yielded value is the second occurrence
            [[1, '1', 2], false, ['1']],
            // duplicate emitted once at second occurrence
            [[1, 1, 1, 1], true, [1]],
            // strings
            [['a', 'b', 'a', 'c', 'b'], true, ['a', 'b']],
        ];
    }

    /**
     * @test         duplicates empty iterable yields nothing
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmptyIterable(iterable $data): void
    {
        // When
        $result = [];
        foreach (Set::duplicates($data) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test duplicates output keys are sequential 0-indexed
     */
    public function testKeysSequential(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 2, 'c' => 1, 'd' => 2];

        // When
        $keys = [];
        foreach (Set::duplicates($data) as $key => $value) {
            $keys[] = $key;
        }

        // Then
        $this->assertSame([0, 1], $keys);
    }
}

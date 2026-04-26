<?php

declare(strict_types=1);

namespace IterTools\Tests\Set;

use IterTools\Set;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;

class DistinctAdjacentTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test distinctAdjacent example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $data = [1, 1, 2, 2, 3, 1, 1];

        // When
        $result = [];
        foreach (Set::distinctAdjacent($data) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame([1, 2, 3, 1], $result);
    }

    /**
     * @test         distinctAdjacent (array)
     * @dataProvider dataProviderForDistinctAdjacent
     * @param        array<mixed> $data
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentArray(array $data, array $expected): void
    {
        // When
        $result = [];
        foreach (Set::distinctAdjacent($data) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         distinctAdjacent (Generator)
     * @dataProvider dataProviderForDistinctAdjacent
     * @param        array<mixed> $data
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentGenerator(array $data, array $expected): void
    {
        // Given
        $gen = GeneratorFixture::getGenerator($data);

        // When
        $result = [];
        foreach (Set::distinctAdjacent($gen) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         distinctAdjacent (Iterator)
     * @dataProvider dataProviderForDistinctAdjacent
     * @param        array<mixed> $data
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentIterator(array $data, array $expected): void
    {
        // Given
        $iter = new ArrayIteratorFixture($data);

        // When
        $result = [];
        foreach (Set::distinctAdjacent($iter) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         distinctAdjacent (IteratorAggregate)
     * @dataProvider dataProviderForDistinctAdjacent
     * @param        array<mixed> $data
     * @param        array<mixed> $expected
     */
    public function testDistinctAdjacentIteratorAggregate(array $data, array $expected): void
    {
        // Given
        $agg = new IteratorAggregateFixture($data);

        // When
        $result = [];
        foreach (Set::distinctAdjacent($agg) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForDistinctAdjacent(): array
    {
        return [
            // empty
            [
                [],
                [],
            ],
            // single element
            [
                [1],
                [1],
            ],
            // all same
            [
                [1, 1, 1, 1],
                [1],
            ],
            // already distinct
            [
                [1, 2, 3],
                [1, 2, 3],
            ],
            // canonical: trailing 1 stays because not adjacent to earlier 1s
            [
                [1, 1, 2, 2, 3, 1, 1],
                [1, 2, 3, 1],
            ],
            // mixed runs
            [
                [1, 2, 2, 3, 3, 3, 4],
                [1, 2, 3, 4],
            ],
            // strings
            [
                ['a', 'a', 'b', 'a', 'a'],
                ['a', 'b', 'a'],
            ],
            // strict comparison: 1 and '1' are not equal
            [
                [1, '1'],
                [1, '1'],
            ],
            [
                [1, 1, '1', '1', 1],
                [1, '1', 1],
            ],
            // floats
            [
                [1.0, 1.0, 1.5, 1.5, 1.0],
                [1.0, 1.5, 1.0],
            ],
            // null and false strict
            [
                [null, null, false, false, 0, 0],
                [null, false, 0],
            ],
        ];
    }

    /**
     * @test         distinctAdjacent on empty iterable
     * @dataProvider dataProviderForEmptyIterable
     * @param        iterable<mixed> $data
     */
    public function testEmpty(iterable $data): void
    {
        // When
        $result = [];
        foreach (Set::distinctAdjacent($data) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test distinctAdjacent uses strict comparison (objects compared by identity)
     */
    public function testObjectsCompareByIdentity(): void
    {
        // Given
        $obj1 = (object) ['a' => 1];
        $obj2 = (object) ['a' => 1]; // structurally equal but different instance
        $data = [$obj1, $obj1, $obj2, $obj2, $obj1];

        // When
        $result = [];
        foreach (Set::distinctAdjacent($data) as $datum) {
            $result[] = $datum;
        }

        // Then
        $this->assertCount(3, $result);
        $this->assertSame($obj1, $result[0]);
        $this->assertSame($obj2, $result[1]);
        $this->assertSame($obj1, $result[2]);
    }

    /**
     * @test distinctAdjacent discards source keys (output is sequentially re-indexed)
     */
    public function testKeysDiscarded(): void
    {
        // Given
        $data = ['a' => 1, 'b' => 1, 'c' => 2, 'd' => 2, 'e' => 3];

        // When
        $result = \iterator_to_array(Set::distinctAdjacent($data), false);

        // Then
        $this->assertSame([1, 2, 3], $result);
        $this->assertSame([0, 1, 2], \array_keys($result));
    }
}

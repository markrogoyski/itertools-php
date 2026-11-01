<?php

declare(strict_types=1);

namespace IterTools\Tests\Combinatorics;

use IterTools\Combinatorics;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\CountingIteratorAggregateFixture;
use IterTools\Tests\Fixture\DataProvider;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;
use IterTools\Tests\Fixture\WrappingIteratorAggregateFixture;

class ProductTest extends \PHPUnit\Framework\TestCase
{
    use DataProvider;

    /**
     * @test product example usage
     */
    public function testExampleUsage(): void
    {
        // Given
        $a = [1, 2];
        $b = ['a', 'b'];

        // When
        $result = [];
        foreach (Combinatorics::product($a, $b) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame(
            [
                [1, 'a'],
                [1, 'b'],
                [2, 'a'],
                [2, 'b'],
            ],
            $result
        );
    }

    /**
     * @test         product (array)
     * @dataProvider dataProviderForProduct
     * @param        array<array<mixed>> $iterables
     * @param        array<array<mixed>> $expected
     */
    public function testProductArray(array $iterables, array $expected): void
    {
        // When
        $result = [];
        foreach (Combinatorics::product(...$iterables) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         product (Generator)
     * @dataProvider dataProviderForProduct
     * @param        array<array<mixed>> $iterables
     * @param        array<array<mixed>> $expected
     */
    public function testProductGenerator(array $iterables, array $expected): void
    {
        // Given
        $generators = [];
        foreach ($iterables as $iterable) {
            $generators[] = GeneratorFixture::getGenerator($iterable);
        }

        // When
        $result = [];
        foreach (Combinatorics::product(...$generators) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         product (Iterator)
     * @dataProvider dataProviderForProduct
     * @param        array<array<mixed>> $iterables
     * @param        array<array<mixed>> $expected
     */
    public function testProductIterator(array $iterables, array $expected): void
    {
        // Given
        $iterators = [];
        foreach ($iterables as $iterable) {
            $iterators[] = new ArrayIteratorFixture($iterable);
        }

        // When
        $result = [];
        foreach (Combinatorics::product(...$iterators) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         product (IteratorAggregate)
     * @dataProvider dataProviderForProduct
     * @param        array<array<mixed>> $iterables
     * @param        array<array<mixed>> $expected
     */
    public function testProductIteratorAggregate(array $iterables, array $expected): void
    {
        // Given
        $aggregates = [];
        foreach ($iterables as $iterable) {
            $aggregates[] = new IteratorAggregateFixture($iterable);
        }

        // When
        $result = [];
        foreach (Combinatorics::product(...$aggregates) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   list(itertools.product(*iterables))
     */
    public static function dataProviderForProduct(): array
    {
        return [
            // single iterable — one-element tuples preserving input order
            [
                [[1, 2, 3]],
                [[1], [2], [3]],
            ],
            // single iterable with one element
            [
                [['only']],
                [['only']],
            ],
            // two iterables, Python itertools lexicographic order
            [
                [[1, 2], ['a', 'b']],
                [[1, 'a'], [1, 'b'], [2, 'a'], [2, 'b']],
            ],
            // two iterables, one element each
            [
                [['x'], ['y']],
                [['x', 'y']],
            ],
            // three iterables — cartesian product
            [
                [[0, 1], [0, 1], [0, 1]],
                [
                    [0, 0, 0], [0, 0, 1], [0, 1, 0], [0, 1, 1],
                    [1, 0, 0], [1, 0, 1], [1, 1, 0], [1, 1, 1],
                ],
            ],
            // mixed-size iterables
            [
                [[1, 2, 3], ['a']],
                [[1, 'a'], [2, 'a'], [3, 'a']],
            ],
            [
                [['a'], [1, 2, 3]],
                [['a', 1], ['a', 2], ['a', 3]],
            ],
            // mixed types
            [
                [[1, 'two'], [true, null]],
                [[1, true], [1, null], ['two', true], ['two', null]],
            ],
            // duplicate values preserved (positional)
            [
                [[1, 1], ['a', 'a']],
                [[1, 'a'], [1, 'a'], [1, 'a'], [1, 'a']],
            ],
        ];
    }

    /**
     * @test product of zero iterables yields one empty tuple
     */
    public function testZeroIterablesYieldsSingleEmptyTuple(): void
    {
        // When
        $result = [];
        foreach (Combinatorics::product() as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame([[]], $result);
    }

    /**
     * @test         product with any empty iterable yields nothing (array)
     * @dataProvider dataProviderForEmptyInProduct
     * @param        array<array<mixed>> $iterables
     */
    public function testEmptyInProductArray(array $iterables): void
    {
        // When
        $result = [];
        foreach (Combinatorics::product(...$iterables) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame([], $result);
    }

    /**
     * Test data generated with:
     *   list(itertools.product(*iterables)), which is [] for every row
     */
    public static function dataProviderForEmptyInProduct(): array
    {
        return [
            [[[]]],
            [[[], [1, 2]]],
            [[[1, 2], []]],
            [[[1, 2], [], [3, 4]]],
            [[[1], [2], []]],
        ];
    }

    /**
     * @test product discards associative input keys — tuples are list arrays
     */
    public function testAssociativeKeysDiscarded(): void
    {
        // Given
        $a = ['x' => 1, 'y' => 2];
        $b = ['p' => 'a', 'q' => 'b'];

        // When
        $result = [];
        foreach (Combinatorics::product($a, $b) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame(
            [
                [1, 'a'],
                [1, 'b'],
                [2, 'a'],
                [2, 'b'],
            ],
            $result
        );
    }

    /**
     * @test product yields list-array tuples (0-indexed)
     */
    public function testOutputTuplesAreListArrays(): void
    {
        // Given
        $a = [1, 2];
        $b = ['a', 'b'];

        // When
        $first = null;
        foreach (Combinatorics::product($a, $b) as $tuple) {
            $first = $tuple;
            break;
        }

        // Then
        $this->assertIsArray($first);
        $this->assertSame([0, 1], \array_keys($first));
    }

    /**
     * @test         product reads a generator passed more than once as independent copies of it
     * @dataProvider dataProviderForRepeatedSourceTwice
     * @param        array<mixed>        $data
     * @param        array<array<mixed>> $expected
     */
    public function testRepeatedGenerator(array $data, array $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = \iterator_to_array(Combinatorics::product($generator, $generator), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         product reads an iterator passed more than once as independent copies of it
     * @dataProvider dataProviderForRepeatedSourceTwice
     * @param        array<mixed>        $data
     * @param        array<array<mixed>> $expected
     */
    public function testRepeatedIterator(array $data, array $expected): void
    {
        // Given
        $iterator = new ArrayIteratorFixture($data);

        // When
        $result = \iterator_to_array(Combinatorics::product($iterator, $iterator), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         product reads an aggregate over one generator, passed more than once, as independent copies of it
     * @dataProvider dataProviderForRepeatedSourceTwice
     * @param        array<mixed>        $data
     * @param        array<array<mixed>> $expected
     */
    public function testRepeatedAggregateOverGenerator(array $data, array $expected): void
    {
        // Given
        $aggregate = new WrappingIteratorAggregateFixture(GeneratorFixture::getGenerator($data));

        // When
        $result = \iterator_to_array(Combinatorics::product($aggregate, $aggregate), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * @test         product reads an aggregate and the generator it returns as independent copies of the generator
     * @dataProvider dataProviderForRepeatedSourceTwice
     * @param        array<mixed>        $data
     * @param        array<array<mixed>> $expected
     */
    public function testAggregateAndItsGenerator(array $data, array $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);
        $aggregate = new WrappingIteratorAggregateFixture($generator);

        // When
        $result = \iterator_to_array(Combinatorics::product($aggregate, $generator), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   [list(t) for t in itertools.product(data, data)]
     */
    public static function dataProviderForRepeatedSourceTwice(): array
    {
        return [
            [
                [1, 2],
                [[1, 1], [1, 2], [2, 1], [2, 2]],
            ],
            [
                [1, 2, 3],
                [[1, 1], [1, 2], [1, 3], [2, 1], [2, 2], [2, 3], [3, 1], [3, 2], [3, 3]],
            ],
            [
                [],
                [],
            ],
        ];
    }

    /**
     * @test         product gives a generator passed around another iterable its own position each time
     * @dataProvider dataProviderForRepeatedGeneratorAroundOtherIterable
     * @param        array<mixed>        $data
     * @param        array<mixed>        $other
     * @param        array<array<mixed>> $expected
     */
    public function testRepeatedGeneratorAroundOtherIterable(array $data, array $other, array $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);

        // When
        $result = \iterator_to_array(Combinatorics::product($generator, $other, $generator), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   [list(t) for t in itertools.product(data, other, data)]
     */
    public static function dataProviderForRepeatedGeneratorAroundOtherIterable(): array
    {
        return [
            [
                [1, 2],
                ['a', 'b'],
                [[1, 'a', 1], [1, 'a', 2], [1, 'b', 1], [1, 'b', 2], [2, 'a', 1], [2, 'a', 2], [2, 'b', 1], [2, 'b', 2]],
            ],
        ];
    }

    /**
     * @test         product reads a generator passed three times as independent copies of it
     * @dataProvider dataProviderForRepeatedGeneratorThreeTimes
     * @param        array<mixed>        $data
     * @param        array<array<mixed>> $expected
     */
    public function testRepeatedGeneratorThreeTimes(array $data, array $expected): void
    {
        // Given
        $generator = GeneratorFixture::getGenerator($data);
        $aggregate = new WrappingIteratorAggregateFixture(new WrappingIteratorAggregateFixture($generator));

        // When
        $result = \iterator_to_array(Combinatorics::product($generator, $aggregate, $generator), false);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with:
     *   [list(t) for t in itertools.product(data, data, data)]
     */
    public static function dataProviderForRepeatedGeneratorThreeTimes(): array
    {
        return [
            [
                [1, 2],
                [[1, 1, 1], [1, 1, 2], [1, 2, 1], [1, 2, 2], [2, 1, 1], [2, 1, 2], [2, 2, 1], [2, 2, 2]],
            ],
        ];
    }

    /**
     * @test product copies an array's values, so a referenced element changed during iteration does not change later tuples
     */
    public function testArrayElementReferenceIsCopied(): void
    {
        // Given
        $x      = 1;
        $values = [&$x];

        // When
        $result = [];
        foreach (Combinatorics::product($values, ['a', 'b']) as $tuple) {
            $result[] = $tuple;
            $x        = 99;
        }

        // Then
        $this->assertSame([[1, 'a'], [1, 'b']], $result);
    }

    /**
     * @test product releases the iterators it materialized before yielding tuples
     */
    public function testReleasesMaterializedIterators(): void
    {
        // Given a source whose iterator the aggregate creates, so only product holds it
        $aggregate = new CountingIteratorAggregateFixture([1, 2]);

        // When the first tuple is read, with tuples still to come
        $product = Combinatorics::product($aggregate, ['a', 'b']);
        $first   = $product->current();

        // Then
        $this->assertSame([1, 'a'], $first);
        $this->assertSame(1, $aggregate->getIteratorCallCount());
        $this->assertSame(0, $aggregate->liveIteratorCount());
    }

    /**
     * @test product consumes generator inputs once (no rewind)
     */
    public function testInputGeneratorsConsumedOnce(): void
    {
        // Given
        $a = GeneratorFixture::getGenerator([1, 2]);
        $b = GeneratorFixture::getGenerator(['a', 'b']);

        // When
        $result = [];
        foreach (Combinatorics::product($a, $b) as $tuple) {
            $result[] = $tuple;
        }

        // Then
        $this->assertSame(
            [
                [1, 'a'],
                [1, 'b'],
                [2, 'a'],
                [2, 'b'],
            ],
            $result
        );
    }
}

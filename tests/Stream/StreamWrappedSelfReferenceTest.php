<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Transform;
use IterTools\Tests\Fixture\ArrayIteratorFixture;
use IterTools\Tests\Fixture\CountingIteratorAggregateFixture;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\IteratorAggregateFixture;
use IterTools\Tests\Fixture\NonSerializableFixture;
use IterTools\Tests\Fixture\WrappingIteratorAggregateFixture;

/**
 * A stream wrapped in Stream::of() streams with no steps of their own or in unread Transform::memoize()
 * iterables, nested in any order, and passed to its own methods stands for its elements at that point, as
 * the stream passed directly does. Other wrappers, and memoized iterables already read, are not detected.
 */
class StreamWrappedSelfReferenceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         stream wrapped in Stream::of() and passed to its own method stands for its
     *               elements at that point (array)
     * @dataProvider dataProviderForWrappedSelfReferenceArray
     * @param        iterable<mixed>                          $input
     * @param        callable(Stream, iterable<mixed>): mixed $operation
     * @param        mixed                                    $expected
     */
    public function testStreamOfSelfReferenceArray(iterable $input, callable $operation, mixed $expected): void
    {
        // Given
        $stream  = Stream::of($input);
        $wrapper = Stream::of($stream);

        // When
        $result = $operation($stream, $wrapper);

        // Then
        $this->assertSame($expected, $result);
    }
    /**
     * @test         stream wrapped in an unread Transform::memoize() and passed to its own
     *               method stands for its elements at that point (array)
     * @dataProvider dataProviderForWrappedSelfReferenceArray
     * @param        iterable<mixed>                          $input
     * @param        callable(Stream, iterable<mixed>): mixed $operation
     * @param        mixed                                    $expected
     */
    public function testMemoizedSelfReferenceArray(iterable $input, callable $operation, mixed $expected): void
    {
        // Given
        $stream  = Stream::of($input);
        $wrapper = Transform::memoize($stream);

        // When
        $result = $operation($stream, $wrapper);

        // Then
        $this->assertSame($expected, $result);
    }
    /**
     * Test data generated with Python, for d = [1, 2, 3] (compress: d = [1, 0, 2]):
     *   list(itertools.compress(d, d)), list(itertools.chain(d, d)), list(heapq.merge(d, d)),
     *   [list(t) for t in itertools.product(d, d)], [list(t) for t in zip(d, d)],
     *   [list(t) for t in itertools.zip_longest(d, d, fillvalue=0)],
     *   sorted((Counter(d) & Counter(d)).elements()) for intersection and partialIntersection(2) of two,
     *   | for union, - for difference, and (a - b) + (b - a) for symmetricDifference;
     *   roundRobin by its definition, alternating d and d.
     */
    public static function dataProviderForWrappedSelfReferenceArray(): array
    {
        return [
            'compress' => [
                [1, 0, 2],
                static fn (Stream $s, iterable $w): array => $s->compress($w)->toArray(),
                [1, 2],
            ],
            'chainWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->chainWith($w)->toArray(),
                [1, 2, 3, 1, 2, 3],
            ],
            'mergeSortedWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->mergeSortedWith($w)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'mergeSortedByWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->mergeSortedByWith(static fn (int $x): int => $x, $w)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'roundRobinWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->roundRobinWith($w)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'productWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->productWith($w)->toArray(),
                [[1, 1], [1, 2], [1, 3], [2, 1], [2, 2], [2, 3], [3, 1], [3, 2], [3, 3]],
            ],
            'zipWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->zipWith($w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipLongestWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->zipLongestWith($w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipFilledWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->zipFilledWith(0, $w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipEqualWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->zipEqualWith($w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'intersectionWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->intersectionWith($w)->toArray(),
                [1, 2, 3],
            ],
            'intersectionCoerciveWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->intersectionCoerciveWith($w)->toArray(),
                [1, 2, 3],
            ],
            'symmetricDifferenceWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->symmetricDifferenceWith($w)->toArray(),
                [],
            ],
            'symmetricDifferenceCoerciveWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->symmetricDifferenceCoerciveWith($w)->toArray(),
                [],
            ],
            'differenceWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->differenceWith($w)->toArray(),
                [],
            ],
            'differenceCoerciveWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->differenceCoerciveWith($w)->toArray(),
                [],
            ],
            'partialIntersectionWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->partialIntersectionWith(2, $w)->toArray(),
                [1, 2, 3],
            ],
            'partialIntersectionCoerciveWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->partialIntersectionCoerciveWith(2, $w)->toArray(),
                [1, 2, 3],
            ],
            'unionWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->unionWith($w)->toArray(),
                [1, 2, 3],
            ],
            'unionCoerciveWith' => [
                [1, 2, 3],
                static fn (Stream $s, iterable $w): array => $s->unionCoerciveWith($w)->toArray(),
                [1, 2, 3],
            ],
        ];
    }

    /**
     * @test         stream wrapped in Stream::of() and passed to its own method stands for its
     *               elements at that point (generator)
     * @dataProvider dataProviderForWrappedSelfReferenceGenerator
     * @param        iterable<mixed>                          $input
     * @param        callable(Stream, iterable<mixed>): mixed $operation
     * @param        mixed                                    $expected
     */
    public function testStreamOfSelfReferenceGenerator(iterable $input, callable $operation, mixed $expected): void
    {
        // Given
        $stream  = Stream::of($input);
        $wrapper = Stream::of($stream);

        // When
        $result = $operation($stream, $wrapper);

        // Then
        $this->assertSame($expected, $result);
    }
    /**
     * @test         stream wrapped in an unread Transform::memoize() and passed to its own
     *               method stands for its elements at that point (generator)
     * @dataProvider dataProviderForWrappedSelfReferenceGenerator
     * @param        iterable<mixed>                          $input
     * @param        callable(Stream, iterable<mixed>): mixed $operation
     * @param        mixed                                    $expected
     */
    public function testMemoizedSelfReferenceGenerator(iterable $input, callable $operation, mixed $expected): void
    {
        // Given
        $stream  = Stream::of($input);
        $wrapper = Transform::memoize($stream);

        // When
        $result = $operation($stream, $wrapper);

        // Then
        $this->assertSame($expected, $result);
    }
    /**
     * Test data generated with Python, for d = [1, 2, 3] (compress: d = [1, 0, 2]):
     *   list(itertools.compress(d, d)), list(itertools.chain(d, d)), list(heapq.merge(d, d)),
     *   [list(t) for t in itertools.product(d, d)], [list(t) for t in zip(d, d)],
     *   [list(t) for t in itertools.zip_longest(d, d, fillvalue=0)],
     *   sorted((Counter(d) & Counter(d)).elements()) for intersection and partialIntersection(2) of two,
     *   | for union, - for difference, and (a - b) + (b - a) for symmetricDifference;
     *   roundRobin by its definition, alternating d and d. chainWith has no row here: over a generator it
     * is single-pass, as the direct form is.
     */
    public static function dataProviderForWrappedSelfReferenceGenerator(): array
    {
        return [
            'compress' => [
                GeneratorFixture::getGenerator([1, 0, 2]),
                static fn (Stream $s, iterable $w): array => $s->compress($w)->toArray(),
                [1, 2],
            ],
            'mergeSortedWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->mergeSortedWith($w)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'mergeSortedByWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->mergeSortedByWith(static fn (int $x): int => $x, $w)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'roundRobinWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->roundRobinWith($w)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'productWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->productWith($w)->toArray(),
                [[1, 1], [1, 2], [1, 3], [2, 1], [2, 2], [2, 3], [3, 1], [3, 2], [3, 3]],
            ],
            'zipWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->zipWith($w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipLongestWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->zipLongestWith($w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipFilledWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->zipFilledWith(0, $w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipEqualWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->zipEqualWith($w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'intersectionWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->intersectionWith($w)->toArray(),
                [1, 2, 3],
            ],
            'intersectionCoerciveWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->intersectionCoerciveWith($w)->toArray(),
                [1, 2, 3],
            ],
            'symmetricDifferenceWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->symmetricDifferenceWith($w)->toArray(),
                [],
            ],
            'symmetricDifferenceCoerciveWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->symmetricDifferenceCoerciveWith($w)->toArray(),
                [],
            ],
            'differenceWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->differenceWith($w)->toArray(),
                [],
            ],
            'differenceCoerciveWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->differenceCoerciveWith($w)->toArray(),
                [],
            ],
            'partialIntersectionWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->partialIntersectionWith(2, $w)->toArray(),
                [1, 2, 3],
            ],
            'partialIntersectionCoerciveWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->partialIntersectionCoerciveWith(2, $w)->toArray(),
                [1, 2, 3],
            ],
            'unionWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->unionWith($w)->toArray(),
                [1, 2, 3],
            ],
            'unionCoerciveWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->unionCoerciveWith($w)->toArray(),
                [1, 2, 3],
            ],
        ];
    }

    /**
     * @test         stream wrapped in Stream::of() and passed to its own method stands for its
     *               elements at that point (iterator)
     * @dataProvider dataProviderForWrappedSelfReferenceIterator
     * @param        iterable<mixed>                          $input
     * @param        callable(Stream, iterable<mixed>): mixed $operation
     * @param        mixed                                    $expected
     */
    public function testStreamOfSelfReferenceIterator(iterable $input, callable $operation, mixed $expected): void
    {
        // Given
        $stream  = Stream::of($input);
        $wrapper = Stream::of($stream);

        // When
        $result = $operation($stream, $wrapper);

        // Then
        $this->assertSame($expected, $result);
    }
    /**
     * @test         stream wrapped in an unread Transform::memoize() and passed to its own
     *               method stands for its elements at that point (iterator)
     * @dataProvider dataProviderForWrappedSelfReferenceIterator
     * @param        iterable<mixed>                          $input
     * @param        callable(Stream, iterable<mixed>): mixed $operation
     * @param        mixed                                    $expected
     */
    public function testMemoizedSelfReferenceIterator(iterable $input, callable $operation, mixed $expected): void
    {
        // Given
        $stream  = Stream::of($input);
        $wrapper = Transform::memoize($stream);

        // When
        $result = $operation($stream, $wrapper);

        // Then
        $this->assertSame($expected, $result);
    }
    /**
     * Test data generated with Python, for d = [1, 2, 3] (compress: d = [1, 0, 2]):
     *   list(itertools.compress(d, d)), list(itertools.chain(d, d)), list(heapq.merge(d, d)),
     *   [list(t) for t in itertools.product(d, d)], [list(t) for t in zip(d, d)],
     *   [list(t) for t in itertools.zip_longest(d, d, fillvalue=0)],
     *   sorted((Counter(d) & Counter(d)).elements()) for intersection and partialIntersection(2) of two,
     *   | for union, - for difference, and (a - b) + (b - a) for symmetricDifference;
     *   roundRobin by its definition, alternating d and d.
     */
    public static function dataProviderForWrappedSelfReferenceIterator(): array
    {
        return [
            'compress' => [
                new ArrayIteratorFixture([1, 0, 2]),
                static fn (Stream $s, iterable $w): array => $s->compress($w)->toArray(),
                [1, 2],
            ],
            'chainWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->chainWith($w)->toArray(),
                [1, 2, 3, 1, 2, 3],
            ],
            'mergeSortedWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->mergeSortedWith($w)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'mergeSortedByWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->mergeSortedByWith(static fn (int $x): int => $x, $w)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'roundRobinWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->roundRobinWith($w)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'productWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->productWith($w)->toArray(),
                [[1, 1], [1, 2], [1, 3], [2, 1], [2, 2], [2, 3], [3, 1], [3, 2], [3, 3]],
            ],
            'zipWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->zipWith($w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipLongestWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->zipLongestWith($w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipFilledWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->zipFilledWith(0, $w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipEqualWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->zipEqualWith($w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'intersectionWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->intersectionWith($w)->toArray(),
                [1, 2, 3],
            ],
            'intersectionCoerciveWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->intersectionCoerciveWith($w)->toArray(),
                [1, 2, 3],
            ],
            'symmetricDifferenceWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->symmetricDifferenceWith($w)->toArray(),
                [],
            ],
            'symmetricDifferenceCoerciveWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->symmetricDifferenceCoerciveWith($w)->toArray(),
                [],
            ],
            'differenceWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->differenceWith($w)->toArray(),
                [],
            ],
            'differenceCoerciveWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->differenceCoerciveWith($w)->toArray(),
                [],
            ],
            'partialIntersectionWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->partialIntersectionWith(2, $w)->toArray(),
                [1, 2, 3],
            ],
            'partialIntersectionCoerciveWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->partialIntersectionCoerciveWith(2, $w)->toArray(),
                [1, 2, 3],
            ],
            'unionWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->unionWith($w)->toArray(),
                [1, 2, 3],
            ],
            'unionCoerciveWith' => [
                new ArrayIteratorFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->unionCoerciveWith($w)->toArray(),
                [1, 2, 3],
            ],
        ];
    }

    /**
     * @test         stream wrapped in Stream::of() and passed to its own method stands for its
     *               elements at that point (aggregate)
     * @dataProvider dataProviderForWrappedSelfReferenceIteratorAggregate
     * @param        iterable<mixed>                          $input
     * @param        callable(Stream, iterable<mixed>): mixed $operation
     * @param        mixed                                    $expected
     */
    public function testStreamOfSelfReferenceIteratorAggregate(iterable $input, callable $operation, mixed $expected): void
    {
        // Given
        $stream  = Stream::of($input);
        $wrapper = Stream::of($stream);

        // When
        $result = $operation($stream, $wrapper);

        // Then
        $this->assertSame($expected, $result);
    }
    /**
     * @test         stream wrapped in an unread Transform::memoize() and passed to its own
     *               method stands for its elements at that point (aggregate)
     * @dataProvider dataProviderForWrappedSelfReferenceIteratorAggregate
     * @param        iterable<mixed>                          $input
     * @param        callable(Stream, iterable<mixed>): mixed $operation
     * @param        mixed                                    $expected
     */
    public function testMemoizedSelfReferenceIteratorAggregate(iterable $input, callable $operation, mixed $expected): void
    {
        // Given
        $stream  = Stream::of($input);
        $wrapper = Transform::memoize($stream);

        // When
        $result = $operation($stream, $wrapper);

        // Then
        $this->assertSame($expected, $result);
    }
    /**
     * Test data generated with Python, for d = [1, 2, 3] (compress: d = [1, 0, 2]):
     *   list(itertools.compress(d, d)), list(itertools.chain(d, d)), list(heapq.merge(d, d)),
     *   [list(t) for t in itertools.product(d, d)], [list(t) for t in zip(d, d)],
     *   [list(t) for t in itertools.zip_longest(d, d, fillvalue=0)],
     *   sorted((Counter(d) & Counter(d)).elements()) for intersection and partialIntersection(2) of two,
     *   | for union, - for difference, and (a - b) + (b - a) for symmetricDifference;
     *   roundRobin by its definition, alternating d and d.
     */
    public static function dataProviderForWrappedSelfReferenceIteratorAggregate(): array
    {
        return [
            'compress' => [
                new IteratorAggregateFixture([1, 0, 2]),
                static fn (Stream $s, iterable $w): array => $s->compress($w)->toArray(),
                [1, 2],
            ],
            'chainWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->chainWith($w)->toArray(),
                [1, 2, 3, 1, 2, 3],
            ],
            'mergeSortedWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->mergeSortedWith($w)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'mergeSortedByWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->mergeSortedByWith(static fn (int $x): int => $x, $w)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'roundRobinWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->roundRobinWith($w)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'productWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->productWith($w)->toArray(),
                [[1, 1], [1, 2], [1, 3], [2, 1], [2, 2], [2, 3], [3, 1], [3, 2], [3, 3]],
            ],
            'zipWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->zipWith($w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipLongestWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->zipLongestWith($w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipFilledWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->zipFilledWith(0, $w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipEqualWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->zipEqualWith($w)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'intersectionWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->intersectionWith($w)->toArray(),
                [1, 2, 3],
            ],
            'intersectionCoerciveWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->intersectionCoerciveWith($w)->toArray(),
                [1, 2, 3],
            ],
            'symmetricDifferenceWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->symmetricDifferenceWith($w)->toArray(),
                [],
            ],
            'symmetricDifferenceCoerciveWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->symmetricDifferenceCoerciveWith($w)->toArray(),
                [],
            ],
            'differenceWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->differenceWith($w)->toArray(),
                [],
            ],
            'differenceCoerciveWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->differenceCoerciveWith($w)->toArray(),
                [],
            ],
            'partialIntersectionWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->partialIntersectionWith(2, $w)->toArray(),
                [1, 2, 3],
            ],
            'partialIntersectionCoerciveWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->partialIntersectionCoerciveWith(2, $w)->toArray(),
                [1, 2, 3],
            ],
            'unionWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->unionWith($w)->toArray(),
                [1, 2, 3],
            ],
            'unionCoerciveWith' => [
                new IteratorAggregateFixture([1, 2, 3]),
                static fn (Stream $s, iterable $w): array => $s->unionCoerciveWith($w)->toArray(),
                [1, 2, 3],
            ],
        ];
    }

    /**
     * @test         stream nested in Stream::of() and unread Transform::memoize() wrappers, in any order, and
     *               passed to its own method stands for its elements at that point
     * @dataProvider dataProviderForNestedSelfWrappers
     * @param        callable(Stream): iterable<mixed> $wrap
     * @param        array<mixed>                      $expected
     */
    public function testNestedWrapperSelfReferenceArray(callable $wrap, array $expected): void
    {
        // Given
        $stream  = Stream::of([1, 2, 3]);
        $wrapper = $wrap($stream);

        // When
        $result = $stream->zipWith($wrapper)->toArray();

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, for d = [1, 2, 3]:
     *   [list(t) for t in zip(d, d)]
     */
    public static function dataProviderForNestedSelfWrappers(): array
    {
        return [
            'Stream::of(Stream::of)' => [
                static fn (Stream $s): iterable => Stream::of(Stream::of($s)),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'memoize(memoize)' => [
                static fn (Stream $s): iterable => Transform::memoize(Transform::memoize($s)),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'Stream::of(memoize)' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize($s)),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'memoize(Stream::of)' => [
                static fn (Stream $s): iterable => Transform::memoize(Stream::of($s)),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'Stream::of(memoize(Stream::of))' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                [[1, 1], [2, 2], [3, 3]],
            ],
        ];
    }

    /**
     * @test         a self-wrapper calls no getIterator() when the step is added, and as many as the direct
     *               form when the result is read; the result equals the direct form's
     * @dataProvider dataProviderForLazyWrappedSelfReference
     * @param        callable(Stream, iterable<mixed>): Stream $build
     * @param        callable(Stream): iterable<mixed>         $wrap
     */
    public function testWrappedSelfReferenceIsLazyAndMatchesDirectIteratorAggregate(callable $build, callable $wrap): void
    {
        // Given
        $directSource  = new CountingIteratorAggregateFixture([1, 2, 3]);
        $direct        = Stream::of($directSource);
        $wrappedSource = new CountingIteratorAggregateFixture([1, 2, 3]);
        $stream        = Stream::of($wrappedSource);
        $wrapper       = $wrap($stream);

        // When
        $built        = $build($stream, $wrapper);
        $callsAtBuild = $wrappedSource->getIteratorCallCount();
        $result       = $built->toArray();
        $expected     = $build($direct, $direct)->toArray();

        // Then
        $this->assertSame(0, $callsAtBuild);
        $this->assertSame($expected, $result);
        $this->assertSame($directSource->getIteratorCallCount(), $wrappedSource->getIteratorCallCount());
    }

    /**
     * Differential rows: the expected result and getIterator() count are the direct form's, $build($direct,
     * $direct) over an equal CountingIteratorAggregateFixture, read in the test. A lazy step calls no
     * getIterator() when it is added, by definition.
     */
    public static function dataProviderForLazyWrappedSelfReference(): array
    {
        return [
            'compress Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->compress($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'compress memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->compress($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'chainWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->chainWith($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'chainWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->chainWith($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'mergeSortedWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->mergeSortedWith($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'mergeSortedWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->mergeSortedWith($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'mergeSortedByWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->mergeSortedByWith(static fn (int $x): int => $x, $w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'mergeSortedByWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->mergeSortedByWith(static fn (int $x): int => $x, $w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'roundRobinWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->roundRobinWith($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'roundRobinWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->roundRobinWith($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'productWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->productWith($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'productWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->productWith($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'zipWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->zipWith($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'zipWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->zipWith($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'zipWith memoize(Stream::of)' => [
                static fn (Stream $s, iterable $w): Stream => $s->zipWith($w),
                static fn (Stream $s): iterable => Transform::memoize(Stream::of($s)),
            ],
            'zipWith Stream::of(memoize(Stream::of))' => [
                static fn (Stream $s, iterable $w): Stream => $s->zipWith($w),
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
            ],
            'zipLongestWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->zipLongestWith($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'zipLongestWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->zipLongestWith($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'zipFilledWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->zipFilledWith(null, $w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'zipFilledWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->zipFilledWith(null, $w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'zipEqualWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->zipEqualWith($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'zipEqualWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->zipEqualWith($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'intersectionWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->intersectionWith($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'intersectionWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->intersectionWith($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'intersectionCoerciveWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->intersectionCoerciveWith($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'intersectionCoerciveWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->intersectionCoerciveWith($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'symmetricDifferenceWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->symmetricDifferenceWith($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'symmetricDifferenceWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->symmetricDifferenceWith($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'symmetricDifferenceCoerciveWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->symmetricDifferenceCoerciveWith($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'symmetricDifferenceCoerciveWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->symmetricDifferenceCoerciveWith($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'differenceWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->differenceWith($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'differenceWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->differenceWith($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'differenceCoerciveWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->differenceCoerciveWith($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'differenceCoerciveWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->differenceCoerciveWith($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'partialIntersectionWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->partialIntersectionWith(2, $w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'partialIntersectionWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->partialIntersectionWith(2, $w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'partialIntersectionCoerciveWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->partialIntersectionCoerciveWith(2, $w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'partialIntersectionCoerciveWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->partialIntersectionCoerciveWith(2, $w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'unionWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->unionWith($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'unionWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->unionWith($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
            'unionCoerciveWith Stream::of' => [
                static fn (Stream $s, iterable $w): Stream => $s->unionCoerciveWith($w),
                static fn (Stream $s): iterable => Stream::of($s),
            ],
            'unionCoerciveWith memoize' => [
                static fn (Stream $s, iterable $w): Stream => $s->unionCoerciveWith($w),
                static fn (Stream $s): iterable => Transform::memoize($s),
            ],
        ];
    }

    /**
     * @test         a self-wrapper among other arguments of a mutating method gives what the stream passed
     *               directly in the same positions gives
     * @dataProvider dataProviderForWrappedSelfReferenceWithOtherArguments
     * @param        callable(Stream, iterable<mixed>): array<mixed> $operation
     * @param        callable(Stream): iterable<mixed>               $wrap
     * @param        array<mixed>                                    $expected
     */
    public function testWrappedSelfReferenceWithOtherArgumentsMatchesDirectArray(
        callable $operation,
        callable $wrap,
        array $expected
    ): void {
        // Given
        $direct  = Stream::of([1, 2, 3]);
        $stream  = Stream::of([1, 2, 3]);
        $wrapper = $wrap($stream);

        // When
        $result       = $operation($stream, $wrapper);
        $directResult = $operation($direct, $direct);

        // Then
        $this->assertSame($expected, $directResult);
        $this->assertSame($directResult, $result);
    }

    /**
     * Test data generated with Python, for d = [1, 2, 3]:
     *   [list(t) for t in zip(d, d, [7, 8, 9])], [list(t) for t in zip(d, [7, 8, 9], d)],
     *   [list(t) for t in zip(d, d, d)], list(itertools.chain(d, d, [4])),
     *   [list(t) for t in itertools.product(d, d, ['a'])],
     *   sorted((Counter(d) & Counter(d) & Counter([2, 3])).elements()),
     *   sorted((Counter(d) - Counter([1]) - Counter(d)).elements())
     * The direct form, the stream passed in the wrapper's positions, is asserted to give the same.
     */
    public static function dataProviderForWrappedSelfReferenceWithOtherArguments(): array
    {
        return [
            'zipWith wrapper first, Stream::of' => [
                static fn (Stream $s, iterable $w): array => $s->zipWith($w, [7, 8, 9])->toArray(),
                static fn (Stream $s): iterable => Stream::of($s),
                [[1, 1, 7], [2, 2, 8], [3, 3, 9]],
            ],
            'zipWith wrapper first, memoize' => [
                static fn (Stream $s, iterable $w): array => $s->zipWith($w, [7, 8, 9])->toArray(),
                static fn (Stream $s): iterable => Transform::memoize($s),
                [[1, 1, 7], [2, 2, 8], [3, 3, 9]],
            ],
            'zipWith wrapper last, Stream::of' => [
                static fn (Stream $s, iterable $w): array => $s->zipWith([7, 8, 9], $w)->toArray(),
                static fn (Stream $s): iterable => Stream::of($s),
                [[1, 7, 1], [2, 8, 2], [3, 9, 3]],
            ],
            'zipWith wrapper last, memoize' => [
                static fn (Stream $s, iterable $w): array => $s->zipWith([7, 8, 9], $w)->toArray(),
                static fn (Stream $s): iterable => Transform::memoize($s),
                [[1, 7, 1], [2, 8, 2], [3, 9, 3]],
            ],
            'zipWith wrapper twice, Stream::of' => [
                static fn (Stream $s, iterable $w): array => $s->zipWith($w, $w)->toArray(),
                static fn (Stream $s): iterable => Stream::of($s),
                [[1, 1, 1], [2, 2, 2], [3, 3, 3]],
            ],
            'zipWith wrapper twice, memoize' => [
                static fn (Stream $s, iterable $w): array => $s->zipWith($w, $w)->toArray(),
                static fn (Stream $s): iterable => Transform::memoize($s),
                [[1, 1, 1], [2, 2, 2], [3, 3, 3]],
            ],
            'chainWith, Stream::of' => [
                static fn (Stream $s, iterable $w): array => $s->chainWith($w, [4])->toArray(),
                static fn (Stream $s): iterable => Stream::of($s),
                [1, 2, 3, 1, 2, 3, 4],
            ],
            'chainWith, memoize' => [
                static fn (Stream $s, iterable $w): array => $s->chainWith($w, [4])->toArray(),
                static fn (Stream $s): iterable => Transform::memoize($s),
                [1, 2, 3, 1, 2, 3, 4],
            ],
            'productWith, Stream::of' => [
                static fn (Stream $s, iterable $w): array => $s->productWith($w, ['a'])->toArray(),
                static fn (Stream $s): iterable => Stream::of($s),
                [[1, 1, 'a'], [1, 2, 'a'], [1, 3, 'a'], [2, 1, 'a'], [2, 2, 'a'], [2, 3, 'a'], [3, 1, 'a'], [3, 2, 'a'], [3, 3, 'a']],
            ],
            'productWith, memoize' => [
                static fn (Stream $s, iterable $w): array => $s->productWith($w, ['a'])->toArray(),
                static fn (Stream $s): iterable => Transform::memoize($s),
                [[1, 1, 'a'], [1, 2, 'a'], [1, 3, 'a'], [2, 1, 'a'], [2, 2, 'a'], [2, 3, 'a'], [3, 1, 'a'], [3, 2, 'a'], [3, 3, 'a']],
            ],
            'intersectionWith, Stream::of' => [
                static fn (Stream $s, iterable $w): array => $s->intersectionWith($w, [2, 3])->toArray(),
                static fn (Stream $s): iterable => Stream::of($s),
                [2, 3],
            ],
            'intersectionWith, memoize' => [
                static fn (Stream $s, iterable $w): array => $s->intersectionWith($w, [2, 3])->toArray(),
                static fn (Stream $s): iterable => Transform::memoize($s),
                [2, 3],
            ],
            'differenceWith, Stream::of' => [
                static fn (Stream $s, iterable $w): array => $s->differenceWith([1], $w)->toArray(),
                static fn (Stream $s): iterable => Stream::of($s),
                [],
            ],
            'differenceWith, memoize' => [
                static fn (Stream $s, iterable $w): array => $s->differenceWith([1], $w)->toArray(),
                static fn (Stream $s): iterable => Transform::memoize($s),
                [],
            ],
        ];
    }

    /**
     * @test         a self-wrapper among other arguments of a comparison gives what the stream passed
     *               directly in the same positions gives
     * @dataProvider dataProviderForWrappedSelfComparisonWithOtherArguments
     * @param        callable(Stream, iterable<mixed>): bool $comparison
     * @param        callable(Stream): iterable<mixed>       $wrap
     */
    public function testWrappedSelfComparisonWithOtherArgumentsMatchesDirectArray(
        callable $comparison,
        callable $wrap,
        bool $expected
    ): void {
        // Given
        $direct  = Stream::of([1, 2, 3]);
        $stream  = Stream::of([1, 2, 3]);
        $wrapper = $wrap($stream);

        // When
        $result       = $comparison($stream, $wrapper);
        $directResult = $comparison($direct, $direct);

        // Then
        $this->assertSame($expected, $directResult);
        $this->assertSame($directResult, $result);
    }

    /**
     * Test data generated with Python, for d = [1, 2, 3]:
     *   d == d == [1, 2, 3], d == d == [1, 2, 4], d == d == d,
     *   Counter(d) == Counter(d) == Counter([3, 2, 1]),
     *   len(d) == len(d) == len([9, 9, 9])
     * The direct form, the stream passed in the wrapper's positions, is asserted to give the same.
     */
    public static function dataProviderForWrappedSelfComparisonWithOtherArguments(): array
    {
        return [
            'sameWith equal array, Stream::of' => [
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w, [1, 2, 3]),
                static fn (Stream $s): iterable => Stream::of($s),
                true,
            ],
            'sameWith equal array, memoize' => [
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w, [1, 2, 3]),
                static fn (Stream $s): iterable => Transform::memoize($s),
                true,
            ],
            'sameWith differing array, Stream::of' => [
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w, [1, 2, 4]),
                static fn (Stream $s): iterable => Stream::of($s),
                false,
            ],
            'sameWith differing array, memoize' => [
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w, [1, 2, 4]),
                static fn (Stream $s): iterable => Transform::memoize($s),
                false,
            ],
            'sameWith wrapper twice, Stream::of' => [
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w, $w),
                static fn (Stream $s): iterable => Stream::of($s),
                true,
            ],
            'sameWith wrapper twice, memoize' => [
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w, $w),
                static fn (Stream $s): iterable => Transform::memoize($s),
                true,
            ],
            'arePermutationsWith, Stream::of' => [
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsWith($w, [3, 2, 1]),
                static fn (Stream $s): iterable => Stream::of($s),
                true,
            ],
            'arePermutationsWith, memoize' => [
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsWith($w, [3, 2, 1]),
                static fn (Stream $s): iterable => Transform::memoize($s),
                true,
            ],
            'sameCountWith, Stream::of' => [
                static fn (Stream $s, iterable $w): bool => $s->sameCountWith($w, [9, 9, 9]),
                static fn (Stream $s): iterable => Stream::of($s),
                true,
            ],
            'sameCountWith, memoize' => [
                static fn (Stream $s, iterable $w): bool => $s->sameCountWith($w, [9, 9, 9]),
                static fn (Stream $s): iterable => Transform::memoize($s),
                true,
            ],
        ];
    }

    /**
     * @test         a self-comparison through a wrapper returns true without calling the source's
     *               getIterator(), as the direct form does
     * @dataProvider dataProviderForComparisonThroughWrapperIsLazy
     * @param        callable(Stream): iterable<mixed>       $wrap
     * @param        callable(Stream, iterable<mixed>): bool $comparison
     */
    public function testComparisonThroughWrapperIsLazyAndMatchesDirectIteratorAggregate(
        callable $wrap,
        callable $comparison
    ): void {
        // Given
        $directSource  = new CountingIteratorAggregateFixture([1, 2, 3]);
        $direct        = Stream::of($directSource);
        $wrappedSource = new CountingIteratorAggregateFixture([1, 2, 3]);
        $stream        = Stream::of($wrappedSource);
        $wrapper       = $wrap($stream);

        // When
        $result       = $comparison($stream, $wrapper);
        $directResult = $comparison($direct, $direct);

        // Then
        $this->assertTrue($result);
        $this->assertSame($directResult, $result);
        $this->assertSame(0, $wrappedSource->getIteratorCallCount());
        $this->assertSame($directSource->getIteratorCallCount(), $wrappedSource->getIteratorCallCount());
    }

    /**
     * Expected by definition: a sequence is the same as, starts with, ends with and is a permutation of
     * itself, which the direct form answers without reading it (asserted alongside).
     */
    public static function dataProviderForComparisonThroughWrapperIsLazy(): array
    {
        return [
            'sameWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w),
            ],
            'sameWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w),
            ],
            'startsWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->startsWith($w),
            ],
            'startsWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->startsWith($w),
            ],
            'startsWithCoercive Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->startsWithCoercive($w),
            ],
            'startsWithCoercive memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->startsWithCoercive($w),
            ],
            'endsWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->endsWith($w),
            ],
            'endsWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->endsWith($w),
            ],
            'endsWithCoercive Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->endsWithCoercive($w),
            ],
            'endsWithCoercive memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->endsWithCoercive($w),
            ],
            'arePermutationsWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsWith($w),
            ],
            'arePermutationsWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsWith($w),
            ],
            'arePermutationsCoerciveWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsCoerciveWith($w),
            ],
            'arePermutationsCoerciveWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsCoerciveWith($w),
            ],
        ];
    }

    /**
     * @test         sameCountWith through a wrapper returns true and reads the source as often as the direct
     *               form
     * @dataProvider dataProviderForSelfWrappers
     * @param        callable(Stream): iterable<mixed> $wrap
     */
    public function testSameCountWithThroughWrapperMatchesDirectIteratorAggregate(callable $wrap): void
    {
        // Given
        $directSource  = new CountingIteratorAggregateFixture([1, 2, 3]);
        $direct        = Stream::of($directSource);
        $wrappedSource = new CountingIteratorAggregateFixture([1, 2, 3]);
        $stream        = Stream::of($wrappedSource);
        $wrapper       = $wrap($stream);

        // When
        $result       = $stream->sameCountWith($wrapper);
        $directResult = $direct->sameCountWith($direct);

        // Then
        $this->assertTrue($result);
        $this->assertSame($directResult, $result);
        $this->assertSame($directSource->getIteratorCallCount(), $wrappedSource->getIteratorCallCount());
    }

    /**
     * @test sameCountWith through an unread memoized wrapper over a generator returns true (len(d) == len(d))
     */
    public function testSameCountWithThroughMemoizedWrapperGenerator(): void
    {
        // Given
        $stream  = Stream::of(GeneratorFixture::getGenerator([1, 2, 3]));
        $wrapper = Transform::memoize($stream);

        // When
        $result = $stream->sameCountWith($wrapper);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test sameCountWith through a Stream::of() wrapper over a generator returns true (len(d) == len(d))
     */
    public function testSameCountWithThroughStreamOfWrapperGenerator(): void
    {
        // Given
        $stream  = Stream::of(GeneratorFixture::getGenerator([1, 2, 3]));
        $wrapper = Stream::of($stream);

        // When
        $result = $stream->sameCountWith($wrapper);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @test sameCountWith neither reads nor fills an unread memoized self-wrapper: read later, it sees a step
     *       added later ([x * 10 for x in [1, 2]] in Python)
     */
    public function testSameCountWithLeavesUnreadMemoizedWrapperUnreadArray(): void
    {
        // Given
        $stream   = Stream::of([1, 2]);
        $memoized = Transform::memoize($stream);

        // When
        $counted = $stream->sameCountWith($memoized);
        $stream->map(static fn (int $x): int => $x * 10);
        $values = \iterator_to_array($memoized, false);

        // Then
        $this->assertTrue($counted);
        $this->assertSame([10, 20], $values);
    }

    /**
     * @test chainWith of a generator-backed stream with a self-wrapper yields one pass, then throws the
     *       direct form's exception
     */
    public function testChainWithWrappedSelfSinglePassGenerator(): void
    {
        // Given
        $stream = Stream::of(GeneratorFixture::getGenerator([1, 2]));
        $result = [];
        $caught = null;

        // When
        try {
            foreach ($stream->chainWith(Stream::of($stream)) as $item) {
                $result[] = $item;
            }
        } catch (\Throwable $e) {
            $caught = $e;
        }

        // Then
        $this->assertSame([1, 2], $result);
        $this->assertNotNull($caught);
        $this->assertSame(\Exception::class, $caught::class);
        $this->assertSame('Cannot traverse an already closed generator', $caught->getMessage());
    }

    /**
     * @test         array-backed stream holding NAN compared strictly with itself through a wrapper is the
     *               same as itself, as passed directly
     * @dataProvider dataProviderForNanSelfComparisonThroughWrapper
     * @param        callable(Stream): iterable<mixed>       $wrap
     * @param        callable(Stream, iterable<mixed>): bool $comparison
     */
    public function testNanSelfComparisonThroughWrapperArray(callable $wrap, callable $comparison): void
    {
        // Given
        $stream  = Stream::of([1.0, \NAN, 3.0]);
        $wrapper = $wrap($stream);

        // When
        $result = $comparison($stream, $wrapper);

        // Then
        $this->assertTrue($result);
    }

    /**
     * Expected by definition: a sequence is the same as, starts with and ends with itself, NAN included, as
     * the stream passed directly is.
     */
    public static function dataProviderForNanSelfComparisonThroughWrapper(): array
    {
        return [
            'sameWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w),
            ],
            'sameWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w),
            ],
            'sameWith nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w),
            ],
            'startsWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->startsWith($w),
            ],
            'startsWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->startsWith($w),
            ],
            'startsWith nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->startsWith($w),
            ],
            'endsWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->endsWith($w),
            ],
            'endsWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->endsWith($w),
            ],
            'endsWith nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->endsWith($w),
            ],
            'sameWith wrapper and stream, Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w, $s),
            ],
            'sameWith wrapper and stream, memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w, $s),
            ],
            'sameWith wrapper and stream, nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w, $s),
            ],
        ];
    }

    /**
     * @test         array-backed stream holding NAN compared with itself through a wrapper, by a comparison
     *               that already treats NAN as equal to itself, is the same as itself
     * @dataProvider dataProviderForNanValueEqualComparisonThroughWrapper
     * @param        callable(Stream): iterable<mixed>       $wrap
     * @param        callable(Stream, iterable<mixed>): bool $comparison
     */
    public function testNanValueEqualComparisonThroughWrapperArray(callable $wrap, callable $comparison): void
    {
        // Given
        $stream  = Stream::of([1.0, \NAN, 3.0]);
        $wrapper = $wrap($stream);

        // When
        $result = $comparison($stream, $wrapper);

        // Then
        $this->assertTrue($result);
    }

    /**
     * Expected by definition: a sequence starts with, ends with and is a permutation of itself, NAN included,
     * as the stream passed directly is.
     */
    public static function dataProviderForNanValueEqualComparisonThroughWrapper(): array
    {
        return [
            'startsWithCoercive Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->startsWithCoercive($w),
            ],
            'startsWithCoercive memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->startsWithCoercive($w),
            ],
            'startsWithCoercive nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->startsWithCoercive($w),
            ],
            'endsWithCoercive Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->endsWithCoercive($w),
            ],
            'endsWithCoercive memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->endsWithCoercive($w),
            ],
            'endsWithCoercive nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->endsWithCoercive($w),
            ],
            'arePermutationsWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsWith($w),
            ],
            'arePermutationsWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsWith($w),
            ],
            'arePermutationsWith nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsWith($w),
            ],
            'arePermutationsCoerciveWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsCoerciveWith($w),
            ],
            'arePermutationsCoerciveWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsCoerciveWith($w),
            ],
            'arePermutationsCoerciveWith nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsCoerciveWith($w),
            ],
        ];
    }

    /**
     * @test         stream holding NAN after a step, compared for a prefix or suffix with itself through a
     *               memoized wrapper, starts and ends with itself
     * @dataProvider dataProviderForNanPrefixSuffixThroughMemoizedWrapperAfterAStep
     * @param        callable(Stream): iterable<mixed>       $wrap
     * @param        callable(Stream, iterable<mixed>): bool $comparison
     */
    public function testNanPrefixSuffixThroughMemoizedWrapperAfterAStepArray(callable $wrap, callable $comparison): void
    {
        // Given
        $stream  = Stream::of([1.0, \NAN, 3.0])->map(static fn (float $x): float => $x * 10);
        $wrapper = $wrap($stream);

        // When
        $result = $comparison($stream, $wrapper);

        // Then
        $this->assertTrue($result);
    }

    /**
     * Expected by definition: a sequence starts and ends with itself, NAN included, as the stream passed
     * directly does.
     */
    public static function dataProviderForNanPrefixSuffixThroughMemoizedWrapperAfterAStep(): array
    {
        return [
            'startsWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->startsWith($w),
            ],
            'startsWith nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->startsWith($w),
            ],
            'startsWithCoercive memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->startsWithCoercive($w),
            ],
            'startsWithCoercive nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->startsWithCoercive($w),
            ],
            'endsWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->endsWith($w),
            ],
            'endsWith nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->endsWith($w),
            ],
            'endsWithCoercive memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->endsWithCoercive($w),
            ],
            'endsWithCoercive nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->endsWithCoercive($w),
            ],
        ];
    }

    /**
     * @test         stream holding NAN after a step, which makes its source one shared generator, compared
     *               with itself through a wrapper is the same as itself
     * @dataProvider dataProviderForNanComparisonThroughWrapperOverSharedStep
     * @param        callable(Stream): iterable<mixed>       $wrap
     * @param        callable(Stream, iterable<mixed>): bool $comparison
     */
    public function testNanComparisonThroughWrapperOverSharedStepArray(callable $wrap, callable $comparison): void
    {
        // Given
        $stream  = Stream::of([1.0, \NAN, 3.0])->map(static fn (float $x): float => $x * 10);
        $wrapper = $wrap($stream);

        // When
        $result = $comparison($stream, $wrapper);

        // Then
        $this->assertTrue($result);
    }

    /**
     * Expected by definition: a sequence is the same as, starts with, ends with and is a permutation of
     * itself, NAN included, as the stream passed directly is.
     */
    public static function dataProviderForNanComparisonThroughWrapperOverSharedStep(): array
    {
        return [
            'sameWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w),
            ],
            'startsWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->startsWith($w),
            ],
            'startsWithCoercive Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->startsWithCoercive($w),
            ],
            'endsWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->endsWith($w),
            ],
            'endsWithCoercive Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->endsWithCoercive($w),
            ],
            'arePermutationsWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsWith($w),
            ],
            'arePermutationsCoerciveWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsCoerciveWith($w),
            ],
            'sameWith wrapper and stream, Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w, $s),
            ],
            'sameWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w),
            ],
            'sameWith nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w),
            ],
            'arePermutationsWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsWith($w),
            ],
            'arePermutationsWith nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsWith($w),
            ],
            'arePermutationsCoerciveWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsCoerciveWith($w),
            ],
            'arePermutationsCoerciveWith nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsCoerciveWith($w),
            ],
            'sameWith wrapper and stream, memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w, $s),
            ],
            'sameWith wrapper and stream, nested' => [
                static fn (Stream $s): iterable => Stream::of(Transform::memoize(Stream::of($s))),
                static fn (Stream $s, iterable $w): bool => $s->sameWith($w, $s),
            ],
        ];
    }

    /**
     * @test         stream holding an object that cannot be serialized, compared coercively with itself
     *               through a wrapper, is the same as itself without serializing it
     * @dataProvider dataProviderForNonSerializableThroughWrapper
     * @param        callable(Stream): iterable<mixed>       $wrap
     * @param        callable(Stream, iterable<mixed>): bool $comparison
     */
    public function testNonSerializableComparisonThroughWrapperArray(callable $wrap, callable $comparison): void
    {
        // Given
        $stream  = Stream::of([new NonSerializableFixture(1)]);
        $wrapper = $wrap($stream);

        // When
        $result = $comparison($stream, $wrapper);

        // Then
        $this->assertTrue($result);
    }

    /**
     * Expected by definition: a sequence starts with, ends with and is a permutation of itself, as the stream
     * passed directly is, with nothing serialized.
     */
    public static function dataProviderForNonSerializableThroughWrapper(): array
    {
        return [
            'startsWithCoercive Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->startsWithCoercive($w),
            ],
            'startsWithCoercive memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->startsWithCoercive($w),
            ],
            'endsWithCoercive Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->endsWithCoercive($w),
            ],
            'endsWithCoercive memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->endsWithCoercive($w),
            ],
            'arePermutationsCoerciveWith Stream::of' => [
                static fn (Stream $s): iterable => Stream::of($s),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsCoerciveWith($w),
            ],
            'arePermutationsCoerciveWith memoize' => [
                static fn (Stream $s): iterable => Transform::memoize($s),
                static fn (Stream $s, iterable $w): bool => $s->arePermutationsCoerciveWith($w),
            ],
        ];
    }

    /**
     * @test an unread memoized self-wrapper stands for the stream: zipWith gives zip(d, d) over d = [1, 2, 3]
     */
    public function testMemoizedStateUnreadArray(): void
    {
        // Given
        $stream   = Stream::of([1, 2, 3]);
        $memoized = Transform::memoize($stream);

        // When
        $result = $stream->zipWith($memoized)->toArray();

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3]], $result);
    }

    /**
     * @test a memoized self-wrapper whose iterator was handed out but not advanced is still unread and stands
     *       for the stream: zipWith gives zip(d, d) over d = [1, 2, 3]
     */
    public function testMemoizedStateIteratorHandedOutArray(): void
    {
        // Given
        $stream   = Stream::of([1, 2, 3]);
        $memoized = Transform::memoize($stream);
        $memoized->getIterator();

        // When
        $result = $stream->zipWith($memoized)->toArray();

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3]], $result);
    }

    /**
     * @test a clone of an unread memoized self-wrapper shares its unread cache and stands for the stream:
     *       zipWith gives zip(d, d) over d = [1, 2, 3]
     */
    public function testMemoizedStateCloneOfUnreadHandleArray(): void
    {
        // Given
        $stream   = Stream::of([1, 2, 3]);
        $memoized = clone Transform::memoize($stream);

        // When
        $result = $stream->zipWith($memoized)->toArray();

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3]], $result);
    }

    /**
     * @test a partially read memoized wrapper over an array is not detected: it read a fresh iterator, so it
     *       is a copy, and zipWith gives zip(d, d) over d = [1, 2, 3]
     */
    public function testMemoizedStatePartiallyReadArray(): void
    {
        // Given
        $stream   = Stream::of([1, 2, 3]);
        $memoized = Transform::memoize($stream);
        foreach ($memoized as $ignored) {
            break;
        }

        // When
        $result = $stream->zipWith($memoized)->toArray();
        $values = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3]], $result);
        $this->assertSame([1, 2, 3], $values);
    }

    /**
     * @test a clone of a partially read memoized wrapper over an array shares its cache and is read as it:
     *       zipWith gives zip(d, d) over d = [1, 2, 3]
     */
    public function testMemoizedStateCloneOfPartiallyReadHandleArray(): void
    {
        // Given
        $stream   = Stream::of([1, 2, 3]);
        $original = Transform::memoize($stream);
        foreach ($original as $ignored) {
            break;
        }
        $memoized = clone $original;

        // When
        $result = $stream->zipWith($memoized)->toArray();
        $values = \iterator_to_array($original, false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3]], $result);
        $this->assertSame([1, 2, 3], $values);
    }

    /**
     * @test an exhausted memoized wrapper over an array is read as its cached values: zipWith gives zip(d, d)
     *       over d = [1, 2, 3]
     */
    public function testMemoizedStateExhaustedArray(): void
    {
        // Given
        $stream   = Stream::of([1, 2, 3]);
        $memoized = Transform::memoize($stream);
        foreach ($memoized as $ignored) {
        }

        // When
        $result = $stream->zipWith($memoized)->toArray();
        $values = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3]], $result);
        $this->assertSame([1, 2, 3], $values);
    }

    /**
     * @test a partially read memoized wrapper over an IteratorAggregate that returns a new iterator per call
     *       is a copy: zipWith gives zip(d, d) over d = [1, 2, 3]
     */
    public function testMemoizedStatePartiallyReadIteratorAggregate(): void
    {
        // Given
        $stream   = Stream::of(new IteratorAggregateFixture([1, 2, 3]));
        $memoized = Transform::memoize($stream);
        foreach ($memoized as $ignored) {
            break;
        }

        // When
        $result = $stream->zipWith($memoized)->toArray();
        $values = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3]], $result);
        $this->assertSame([1, 2, 3], $values);
    }

    /**
     * @test a partially read memoized wrapper over a stream whose memoize() step gives a fresh iterator per
     *       read is a copy: zipWith gives zip(d, d) over d = [1, 2, 3]
     */
    public function testMemoizedStatePartiallyReadAfterMemoizeStepGenerator(): void
    {
        // Given
        $stream   = Stream::of(GeneratorFixture::getGenerator([1, 2, 3]))->memoize();
        $memoized = Transform::memoize($stream);
        foreach ($memoized as $ignored) {
            break;
        }

        // When
        $result = $stream->zipWith($memoized)->toArray();
        $values = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3]], $result);
        $this->assertSame([1, 2, 3], $values);
    }

    /**
     * @test a partially read memoized wrapper over a generator shares its cursor with the pipeline, as any
     *       memo of a generator read alongside it does: zip pairs 1 with the cached 1, then the stream and
     *       the memo each advance the one generator, to 2 and then 3, and both read 3
     */
    public function testMemoizedStatePartiallyReadGenerator(): void
    {
        // Given
        $stream   = Stream::of(GeneratorFixture::getGenerator([1, 2, 3]));
        $memoized = Transform::memoize($stream);
        foreach ($memoized as $ignored) {
            break;
        }

        // When
        $result = $stream->zipWith($memoized)->toArray();
        $values = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([[1, 1], [3, 3]], $result);
        $this->assertSame([1, 3], $values);
    }

    /**
     * @test a partially read memoized wrapper over a stream whose map() step gives one shared generator
     *       shares its cursor: zip pairs 1 with the cached 1, then the stream and the memo each advance the
     *       generator, and both read 3
     */
    public function testMemoizedStatePartiallyReadAfterMapStepArray(): void
    {
        // Given
        $stream   = Stream::of([1, 2, 3])->map(static fn (int $x): int => $x);
        $memoized = Transform::memoize($stream);
        foreach ($memoized as $ignored) {
            break;
        }

        // When
        $result = $stream->zipWith($memoized)->toArray();
        $values = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([[1, 1], [3, 3]], $result);
        $this->assertSame([1, 3], $values);
    }

    /**
     * @test a partially read memoized wrapper over an Iterator shares its cursor: zip pairs 1 with the cached
     *       1, then the stream and the memo each advance the iterator, and both read 3
     */
    public function testMemoizedStatePartiallyReadIterator(): void
    {
        // Given
        $stream   = Stream::of(new ArrayIteratorFixture([1, 2, 3]));
        $memoized = Transform::memoize($stream);
        foreach ($memoized as $ignored) {
            break;
        }

        // When
        $result = $stream->zipWith($memoized)->toArray();
        $values = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([[1, 1], [3, 3]], $result);
        $this->assertSame([1, 3], $values);
    }

    /**
     * @test a partially read memoized wrapper over an IteratorAggregate that returns the same generator on
     *       every call shares its cursor: zip pairs 1 with the cached 1, then both advance the generator and
     *       read 3
     */
    public function testMemoizedStatePartiallyReadSharedIteratorAggregate(): void
    {
        // Given
        $stream   = Stream::of(new WrappingIteratorAggregateFixture(GeneratorFixture::getGenerator([1, 2, 3])));
        $memoized = Transform::memoize($stream);
        foreach ($memoized as $ignored) {
            break;
        }

        // When
        $result = $stream->zipWith($memoized)->toArray();
        $values = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([[1, 1], [3, 3]], $result);
        $this->assertSame([1, 3], $values);
    }

    /**
     * @test an exhausted memoized wrapper over an Iterator shares it, and the stream rewinds and reads it
     *       again from the start: zipWith gives zip(d, d) over d = [1, 2, 3]
     */
    public function testMemoizedStateExhaustedIterator(): void
    {
        // Given
        $stream   = Stream::of(new ArrayIteratorFixture([1, 2, 3]));
        $memoized = Transform::memoize($stream);
        foreach ($memoized as $ignored) {
        }

        // When
        $result = $stream->zipWith($memoized)->toArray();
        $values = \iterator_to_array($memoized, false);

        // Then
        $this->assertSame([[1, 1], [2, 2], [3, 3]], $result);
        $this->assertSame([1, 2, 3], $values);
    }

    /**
     * @test an exhausted memoized wrapper over a generator leaves the stream's generator spent, so the call
     *       throws PHP's rewind exception, while the memo still replays its values
     */
    public function testMemoizedStateExhaustedGenerator(): void
    {
        // Given
        $stream   = Stream::of(GeneratorFixture::getGenerator([1, 2, 3]));
        $memoized = Transform::memoize($stream);
        foreach ($memoized as $ignored) {
        }

        // When
        $caught = null;
        try {
            $stream->zipWith($memoized)->toArray();
        } catch (\Throwable $e) {
            $caught = $e;
        }
        $values = \iterator_to_array($memoized, false);

        // Then
        $this->assertNotNull($caught);
        $this->assertSame(\Exception::class, $caught::class);
        $this->assertSame('Cannot rewind a generator that was already run', $caught->getMessage());
        $this->assertSame([1, 2, 3], $values);
    }

    /**
     * @test an exhausted memoized wrapper over an IteratorAggregate that returns the same generator on every
     *       call leaves that generator spent, so the call throws PHP's rewind exception, while the memo still
     *       replays
     */
    public function testMemoizedStateExhaustedSharedIteratorAggregate(): void
    {
        // Given
        $stream   = Stream::of(new WrappingIteratorAggregateFixture(GeneratorFixture::getGenerator([1, 2, 3])));
        $memoized = Transform::memoize($stream);
        foreach ($memoized as $ignored) {
        }

        // When
        $caught = null;
        try {
            $stream->zipWith($memoized)->toArray();
        } catch (\Throwable $e) {
            $caught = $e;
        }
        $values = \iterator_to_array($memoized, false);

        // Then
        $this->assertNotNull($caught);
        $this->assertSame(\Exception::class, $caught::class);
        $this->assertSame('Cannot rewind a generator that was already run', $caught->getMessage());
        $this->assertSame([1, 2, 3], $values);
    }

    /**
     * @test a memoized wrapper holding a cached failure rethrows that same failure object; it is not rebound
     */
    public function testMemoizedStateCachedFailureIteratorAggregate(): void
    {
        // Given
        $source = new class implements \IteratorAggregate {
            private int $calls = 0;

            public function getIterator(): \Iterator
            {
                if ($this->calls++ === 0) {
                    throw new \RuntimeException('first');
                }
                return new \ArrayIterator([1, 2, 3]);
            }
        };
        $stream   = Stream::of($source);
        $memoized = Transform::memoize($stream);
        $failure  = null;
        try {
            foreach ($memoized as $ignored) {
            }
        } catch (\RuntimeException $e) {
            $failure = $e;
        }

        // When
        $caught = null;
        try {
            $stream->zipWith($memoized)->toArray();
        } catch (\Throwable $e) {
            $caught = $e;
        }

        // Then
        $this->assertInstanceOf(\RuntimeException::class, $failure);
        $this->assertSame($failure, $caught);
    }

    /**
     * @test         a self-wrapper does not make other operands equal: each is still compared by value
     * @dataProvider dataProviderForIdentityControls
     * @param        callable(Stream): bool $comparison
     */
    public function testIdentityControlsArray(callable $comparison, bool $expected): void
    {
        // Given
        $stream = Stream::of([1.0, \NAN]);

        // When
        $result = $comparison($stream);

        // Then
        $this->assertSame($expected, $result);
    }

    /**
     * Test data generated with Python, for d = [1.0, float('nan')], compared by value element-wise
     * (float('nan') == float('nan') is False):
     *   all(a == b for a, b in zip(d, [1.0, float('nan')])) for sameWith, startsWith and endsWith a distinct
     *   equal stream, and for sameWith a wrapper with its own identity step;
     *   d == [1.0, 2.0] for sameWith a self-wrapper and a differing argument;
     *   Counter(d) == Counter([1.0, 2.0]) for arePermutationsWith a self-wrapper and a differing argument
     */
    public static function dataProviderForIdentityControls(): array
    {
        return [
            'sameWith distinct stream of equal array' => [
                static fn (Stream $s): bool => $s->sameWith(Stream::of([1.0, \NAN])),
                false,
            ],
            'startsWith distinct stream of equal array' => [
                static fn (Stream $s): bool => $s->startsWith(Stream::of([1.0, \NAN])),
                false,
            ],
            'endsWith distinct stream of equal array' => [
                static fn (Stream $s): bool => $s->endsWith(Stream::of([1.0, \NAN])),
                false,
            ],
            'sameWith self-wrapper and a differing argument' => [
                static fn (Stream $s): bool => $s->sameWith(Stream::of($s), [1.0, 2.0]),
                false,
            ],
            'arePermutationsWith self-wrapper and a differing argument' => [
                static fn (Stream $s): bool => $s->arePermutationsWith(Stream::of($s), [1.0, 2.0]),
                false,
            ],
            'sameWith a wrapper with its own step' => [
                static fn (Stream $s): bool => $s->sameWith(Stream::of($s)->map(static fn (float $x): float => $x)),
                false,
            ],
        ];
    }

    /**
     * @test a memoized wrapper of an array-backed stream already read is a copy of the values it read: with
     *       NAN it is not the same (float('nan') == float('nan') is False in Python)
     */
    public function testSameWithMemoizedWrapperAlreadyReadArray(): void
    {
        // Given
        $stream   = Stream::of([1.0, \NAN]);
        $memoized = Transform::memoize($stream);
        foreach ($memoized as $ignored) {
            break;
        }

        // When
        $result = $stream->sameWith($memoized);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test a self-wrapper mixed with a differing argument still compares it ([1, 2] == [1, 3] is False in
     *       Python)
     */
    public function testSelfWrapperWithDifferingArgumentArray(): void
    {
        // Given
        $stream = Stream::of([1, 2]);

        // When
        $result = $stream->sameWith(Stream::of($stream), [1, 3]);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @test         a self-wrapper mixed with other arguments substitutes only its own slot: zipWith gives
     *               zip(d, d, [7, 8, 9]) over d = [1, 2, 3]
     * @dataProvider dataProviderForSelfWrappers
     * @param        callable(Stream): iterable<mixed> $wrap
     */
    public function testSelfWrapperMixedWithOtherArgumentsArray(callable $wrap): void
    {
        // Given
        $stream  = Stream::of([1, 2, 3]);
        $wrapper = $wrap($stream);

        // When
        $result = $stream->zipWith($wrapper, [7, 8, 9])->toArray();

        // Then
        $this->assertSame([[1, 1, 7], [2, 2, 8], [3, 3, 9]], $result);
    }

    /**
     * @test         a wrapper of one stream passed to another stream's method is not a self-reference:
     *               zipWith gives zip([4, 5, 6], [1, 2, 3])
     * @dataProvider dataProviderForSelfWrappers
     * @param        callable(Stream): iterable<mixed> $wrap
     */
    public function testWrapperOfAnotherStreamIsUnaffectedArray(callable $wrap): void
    {
        // Given
        $other   = Stream::of([1, 2, 3]);
        $stream  = Stream::of([4, 5, 6]);
        $wrapper = $wrap($other);

        // When
        $result = $stream->zipWith($wrapper)->toArray();

        // Then
        $this->assertSame([[4, 1], [5, 2], [6, 3]], $result);
    }

    /**
     * @test         a self-wrapper of an empty stream yields nothing, as zip([], []) does
     * @dataProvider dataProviderForSelfWrappers
     * @param        callable(Stream): iterable<mixed> $wrap
     */
    public function testSelfWrapperOfEmptyStreamArray(callable $wrap): void
    {
        // Given
        $stream  = Stream::of([]);
        $wrapper = $wrap($stream);

        // When
        $result = $stream->zipWith($wrapper)->toArray();

        // Then
        $this->assertSame([], $result);
    }

    /**
     * @test the wrapper keeps its meaning outside the call: a live view of the stream, whose own later steps
     *       do not reach the stream's pipeline ([a + b for a, b in zip(d, d)] over d = [1, 2] in Python)
     */
    public function testWrappersStillReadTheStreamAfterTheCallArray(): void
    {
        // Given
        $stream  = Stream::of([1, 2]);
        $wrapper = Stream::of($stream);
        $stream->zipWith($wrapper);
        $wrapper->map(static fn (array $pair): int => $pair[0] + $pair[1]);

        // When
        $result = $wrapper->toArray();

        // Then
        $this->assertSame([2, 4], $result);
    }

    /**
     * @test an unread memoized self-wrapper is neither read nor filled by the call; read later, it memoizes
     *       the pipeline ([list(t) for t in zip(d, d)] over d = [1, 2] in Python)
     */
    public function testUnreadMemoizedWrapperMemoizesThePipelineLaterArray(): void
    {
        // Given
        $stream   = Stream::of([1, 2]);
        $memoized = Transform::memoize($stream);
        $stream->zipWith($memoized);

        // When
        $result = \iterator_to_array($memoized);

        // Then
        $this->assertSame([[1, 1], [2, 2]], $result);
    }

    /**
     * @test an unread memoized self-wrapper read after the stream was consumed fails, as reading the stream
     *       again does: the call does not make the wrapper replay the stream
     */
    public function testMemoizedWrapperReadAfterStreamWasConsumedArray(): void
    {
        // Given
        $stream   = Stream::of([1, 2]);
        $memoized = Transform::memoize($stream);
        $stream->zipWith($memoized)->toArray();

        // When
        $caught = null;
        try {
            \iterator_to_array($memoized, false);
        } catch (\Throwable $e) {
            $caught = $e;
        }

        // Then
        $this->assertNotNull($caught);
        $this->assertSame(\Exception::class, $caught::class);
        $this->assertSame('Cannot rewind a generator that was already run', $caught->getMessage());
    }

    /**
     * @test a self-comparison through an unread memoized wrapper reads nothing: the generator body never runs
     */
    public function testStartsWithUnreadMemoizedWrapperReadsNothingGenerator(): void
    {
        // Given
        $ran    = false;
        $stream = Stream::of((static function () use (&$ran): \Generator {
            $ran = true;
            yield 1;
        })());

        // When
        $result = $stream->startsWith(Transform::memoize($stream));

        // Then
        $this->assertTrue($result);
        $this->assertFalse($ran);
    }

    /**
     * @test a step that stops early leaves an unread memoized self-wrapper, its clone and its handed-out
     *       iterator unread: read later, all three see the limited pipeline (list(itertools.islice(chain(d,
     *       d), 1)) over d = [1, 2] in Python)
     */
    public function testEarlyTerminationLeavesMemoizedHandlesUnreadArray(): void
    {
        // Given
        $stream   = Stream::of([1, 2]);
        $memoized = Transform::memoize($stream);
        $clone    = clone $memoized;
        $iterator = $memoized->getIterator();
        $stream->chainWith($memoized)->limit(1);

        // When
        $values      = \iterator_to_array($memoized, false);
        $cloneValues = \iterator_to_array($clone, false);
        $cursor      = \iterator_to_array($iterator, false);

        // Then
        $this->assertSame([1], $values);
        $this->assertSame([1], $cloneValues);
        $this->assertSame([1], $cursor);
    }

    /**
     * @test         wrappers the stream cannot see through still read the stream when iterated, which inside
     *               its own pipeline is a cycle that throws \Error
     * @dataProvider dataProviderForUndetectableWrappers
     * @param        callable(Stream): iterable<mixed> $wrap
     */
    public function testUndetectableWrappersStillCycleArray(callable $wrap): void
    {
        // Given
        $stream  = Stream::of([1, 2, 3]);
        $wrapper = $wrap($stream);

        // When
        $caught = null;
        try {
            $stream->zipWith($wrapper)->toArray();
        } catch (\Throwable $e) {
            $caught = $e;
        }

        // Then
        $this->assertNotNull($caught);
        $this->assertSame(\Error::class, $caught::class);
    }

    /**
     * Wrappers only: a step, a user IteratorAggregate and a tee copy hide the stream from the search. The
     * expected \Error is PHP's for resuming a generator that is running.
     */
    public static function dataProviderForUndetectableWrappers(): array
    {
        return [
            'wrapper with its own step' => [
                static fn (Stream $s): iterable => Stream::of($s)->map(static fn (int $x): int => $x * 10),
            ],
            'user aggregate returning the stream' => [
                static fn (Stream $s): iterable => new WrappingIteratorAggregateFixture($s),
            ],
            'tee copy' => [
                static fn (Stream $s): iterable => Transform::tee($s, 2)[0],
            ],
        ];
    }

    /**
     * Wrappers only, which stand for the stream: Stream::of() with no steps of its own, and an unread
     * Transform::memoize(). Each test states where its expected value comes from.
     */
    public static function dataProviderForSelfWrappers(): array
    {
        return [
            'Stream::of' => [static fn (Stream $s): iterable => Stream::of($s)],
            'memoize'    => [static fn (Stream $s): iterable => Transform::memoize($s)],
        ];
    }
}

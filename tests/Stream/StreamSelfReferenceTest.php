<?php

declare(strict_types=1);

namespace IterTools\Tests\Stream;

use IterTools\Stream;
use IterTools\Tests\Fixture\GeneratorFixture;
use IterTools\Tests\Fixture\NonSerializableFixture;

/**
 * A stream passed to its own methods stands for its current source, read as a repeated argument.
 */
class StreamSelfReferenceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test         stream passed to its own method on an array-backed stream
     * @dataProvider dataProviderForSelfReferenceArray
     * @param        array<mixed>                 $input
     * @param        callable(Stream): mixed       $operation
     * @param        mixed                        $expected
     */
    public function testSelfReferenceArray(array $input, callable $operation, mixed $expected): void
    {
        // Given
        $stream = Stream::of($input);

        // When
        $result = $operation($stream);

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForSelfReferenceArray(): array
    {
        return [
            'compress' => [
                [1, 0, 2],
                static fn (Stream $s): array => $s->compress($s)->toArray(),
                [1, 2],
            ],
            'compress after a step' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->map(static fn (int $x): int => $x - 1)->compress($s)->toArray(),
                [1, 2],
            ],
            'mergeSortedWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->mergeSortedWith($s)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'mergeSortedWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->mergeSortedWith($s, [2.5])->toArray(),
                [1, 1, 2, 2, 2.5, 3, 3],
            ],
            'mergeSortedByWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->mergeSortedByWith(static fn (int|float $x): int|float => $x, $s)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'mergeSortedByWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->mergeSortedByWith(static fn (int|float $x): int|float => $x, $s, [2.5])->toArray(),
                [1, 1, 2, 2, 2.5, 3, 3],
            ],
            'zipWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->zipWith($s)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->zipWith($s, ['a', 'b', 'c'])->toArray(),
                [[1, 1, 'a'], [2, 2, 'b'], [3, 3, 'c']],
            ],
            'zipWith other first' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->zipWith(['a', 'b', 'c'], $s)->toArray(),
                [[1, 'a', 1], [2, 'b', 2], [3, 'c', 3]],
            ],
            'zipWith after a step' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->map(static fn (int $x): int => $x * 10)->zipWith($s)->toArray(),
                [[10, 10], [20, 20], [30, 30]],
            ],
            'zipWith named' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->zipWith(other: $s)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipLongestWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->zipLongestWith($s)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipLongestWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->zipLongestWith($s, ['a'])->toArray(),
                [[1, 1, 'a'], [2, 2, null], [3, 3, null]],
            ],
            'zipFilledWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->zipFilledWith(0, $s)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipFilledWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->zipFilledWith(0, $s, ['a'])->toArray(),
                [[1, 1, 'a'], [2, 2, 0], [3, 3, 0]],
            ],
            'zipEqualWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->zipEqualWith($s)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipEqualWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->zipEqualWith($s, [4, 5, 6])->toArray(),
                [[1, 1, 4], [2, 2, 5], [3, 3, 6]],
            ],
            'intersectionWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->intersectionWith($s)->toArray(),
                [1, 2, 3],
            ],
            'intersectionWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->intersectionWith($s, [2, 3, 4])->toArray(),
                [2, 3],
            ],
            'intersectionCoerciveWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->intersectionCoerciveWith($s)->toArray(),
                [1, 2, 3],
            ],
            'intersectionCoerciveWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->intersectionCoerciveWith($s, ['2', '3', '4'])->toArray(),
                [2, 3],
            ],
            'symmetricDifferenceWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->symmetricDifferenceWith($s)->toArray(),
                [],
            ],
            'symmetricDifferenceWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->symmetricDifferenceWith($s, [1, 2, 3])->toArray(),
                [],
            ],
            'symmetricDifferenceCoerciveWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->symmetricDifferenceCoerciveWith($s)->toArray(),
                [],
            ],
            'symmetricDifferenceCoerciveWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->symmetricDifferenceCoerciveWith($s, ['1', '2', '3'])->toArray(),
                [],
            ],
            'differenceWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->differenceWith($s)->toArray(),
                [],
            ],
            'differenceWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->differenceWith([4], $s)->toArray(),
                [],
            ],
            'differenceCoerciveWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->differenceCoerciveWith($s)->toArray(),
                [],
            ],
            'differenceCoerciveWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->differenceCoerciveWith(['4'], $s)->toArray(),
                [],
            ],
            'partialIntersectionWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->partialIntersectionWith(2, $s)->toArray(),
                [1, 2, 3],
            ],
            'partialIntersectionWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->partialIntersectionWith(3, $s, [2, 3, 4])->toArray(),
                [2, 3],
            ],
            'partialIntersectionCoerciveWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->partialIntersectionCoerciveWith(2, $s)->toArray(),
                [1, 2, 3],
            ],
            'partialIntersectionCoerciveWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->partialIntersectionCoerciveWith(3, $s, ['2', '3', '4'])->toArray(),
                [2, 3],
            ],
            'unionWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->unionWith($s)->toArray(),
                [1, 2, 3],
            ],
            'unionWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->unionWith($s, [3, 4])->toArray(),
                [1, 3, 2, 4],
            ],
            'unionCoerciveWith' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->unionCoerciveWith($s)->toArray(),
                [1, 2, 3],
            ],
            'unionCoerciveWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): array => $s->unionCoerciveWith($s, [3, 4])->toArray(),
                [1, 3, 2, 4],
            ],
            'startsWith' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->startsWith($s),
                true,
            ],
            'startsWith after a step' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->map(static fn (int $x): int => $x * 10)->startsWith($s),
                true,
            ],
            'startsWithCoercive' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->startsWithCoercive($s),
                true,
            ],
            'startsWithCoercive after a step' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->map(static fn (int $x): int => $x * 10)->startsWithCoercive($s),
                true,
            ],
            'endsWith' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->endsWith($s),
                true,
            ],
            'endsWith after a step' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->map(static fn (int $x): int => $x * 10)->endsWith($s),
                true,
            ],
            'endsWithCoercive' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->endsWithCoercive($s),
                true,
            ],
            'endsWithCoercive after a step' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->map(static fn (int $x): int => $x * 10)->endsWithCoercive($s),
                true,
            ],
            'sameWith' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->sameWith($s),
                true,
            ],
            'sameWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->sameWith($s, [1, 2, 3]),
                true,
            ],
            'sameWith mixed different' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->sameWith($s, [1, 2, 4]),
                false,
            ],
            'sameCountWith' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->sameCountWith($s),
                true,
            ],
            'sameCountWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->sameCountWith($s, [7, 8, 9]),
                true,
            ],
            'sameCountWith mixed different' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->sameCountWith($s, [7, 8]),
                false,
            ],
            'arePermutationsWith' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->arePermutationsWith($s),
                true,
            ],
            'arePermutationsWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->arePermutationsWith($s, [3, 1, 2]),
                true,
            ],
            'arePermutationsWith mixed different' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->arePermutationsWith($s, ['3', '1', '2']),
                false,
            ],
            'arePermutationsCoerciveWith' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->arePermutationsCoerciveWith($s),
                true,
            ],
            'arePermutationsCoerciveWith mixed' => [
                [1, 2, 3],
                static fn (Stream $s): bool => $s->arePermutationsCoerciveWith($s, ['3', '1', '2']),
                true,
            ],
            'startsWith NAN' => [
                [NAN],
                static fn (Stream $s): bool => $s->startsWith($s),
                true,
            ],
            'startsWithCoercive unserializable' => [
                [new NonSerializableFixture(1)],
                static fn (Stream $s): bool => $s->startsWithCoercive($s),
                true,
            ],
            'endsWith NAN' => [
                [NAN],
                static fn (Stream $s): bool => $s->endsWith($s),
                true,
            ],
            'endsWithCoercive unserializable' => [
                [new NonSerializableFixture(1)],
                static fn (Stream $s): bool => $s->endsWithCoercive($s),
                true,
            ],
            'sameWith NAN' => [
                [NAN],
                static fn (Stream $s): bool => $s->sameWith($s),
                true,
            ],
            'sameWith NAN twice' => [
                [NAN],
                static fn (Stream $s): bool => $s->sameWith($s, $s),
                true,
            ],
            'sameWith NAN mixed' => [
                [NAN],
                static fn (Stream $s): bool => $s->sameWith($s, [NAN]),
                false,
            ],
        ];
    }

    /**
     * @test         stream passed to its own method on a generator-backed stream
     * @dataProvider dataProviderForSelfReferenceGenerator
     * @param        \Generator                   $input
     * @param        callable(Stream): mixed       $operation
     * @param        mixed                        $expected
     */
    public function testSelfReferenceGenerator(\Generator $input, callable $operation, mixed $expected): void
    {
        // Given
        $stream = Stream::of($input);

        // When
        $result = $operation($stream);

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForSelfReferenceGenerator(): array
    {
        return [
            'compress' => [
                GeneratorFixture::getGenerator([1, 0, 2]),
                static fn (Stream $s): array => $s->compress($s)->toArray(),
                [1, 2],
            ],
            'compress after a step' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->map(static fn (int $x): int => $x - 1)->compress($s)->toArray(),
                [1, 2],
            ],
            'mergeSortedWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->mergeSortedWith($s)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'mergeSortedWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->mergeSortedWith($s, [2.5])->toArray(),
                [1, 1, 2, 2, 2.5, 3, 3],
            ],
            'mergeSortedByWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->mergeSortedByWith(static fn (int|float $x): int|float => $x, $s)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'mergeSortedByWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->mergeSortedByWith(static fn (int|float $x): int|float => $x, $s, [2.5])->toArray(),
                [1, 1, 2, 2, 2.5, 3, 3],
            ],
            'zipWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->zipWith($s)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->zipWith($s, ['a', 'b', 'c'])->toArray(),
                [[1, 1, 'a'], [2, 2, 'b'], [3, 3, 'c']],
            ],
            'zipWith other first' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->zipWith(['a', 'b', 'c'], $s)->toArray(),
                [[1, 'a', 1], [2, 'b', 2], [3, 'c', 3]],
            ],
            'zipWith after a step' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->map(static fn (int $x): int => $x * 10)->zipWith($s)->toArray(),
                [[10, 10], [20, 20], [30, 30]],
            ],
            'zipWith named' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->zipWith(other: $s)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipLongestWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->zipLongestWith($s)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipLongestWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->zipLongestWith($s, ['a'])->toArray(),
                [[1, 1, 'a'], [2, 2, null], [3, 3, null]],
            ],
            'zipFilledWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->zipFilledWith(0, $s)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipFilledWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->zipFilledWith(0, $s, ['a'])->toArray(),
                [[1, 1, 'a'], [2, 2, 0], [3, 3, 0]],
            ],
            'zipEqualWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->zipEqualWith($s)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipEqualWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->zipEqualWith($s, [4, 5, 6])->toArray(),
                [[1, 1, 4], [2, 2, 5], [3, 3, 6]],
            ],
            'intersectionWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->intersectionWith($s)->toArray(),
                [1, 2, 3],
            ],
            'intersectionWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->intersectionWith($s, [2, 3, 4])->toArray(),
                [2, 3],
            ],
            'intersectionCoerciveWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->intersectionCoerciveWith($s)->toArray(),
                [1, 2, 3],
            ],
            'intersectionCoerciveWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->intersectionCoerciveWith($s, ['2', '3', '4'])->toArray(),
                [2, 3],
            ],
            'symmetricDifferenceWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->symmetricDifferenceWith($s)->toArray(),
                [],
            ],
            'symmetricDifferenceWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->symmetricDifferenceWith($s, [1, 2, 3])->toArray(),
                [],
            ],
            'symmetricDifferenceCoerciveWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->symmetricDifferenceCoerciveWith($s)->toArray(),
                [],
            ],
            'symmetricDifferenceCoerciveWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->symmetricDifferenceCoerciveWith($s, ['1', '2', '3'])->toArray(),
                [],
            ],
            'differenceWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->differenceWith($s)->toArray(),
                [],
            ],
            'differenceWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->differenceWith([4], $s)->toArray(),
                [],
            ],
            'differenceCoerciveWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->differenceCoerciveWith($s)->toArray(),
                [],
            ],
            'differenceCoerciveWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->differenceCoerciveWith(['4'], $s)->toArray(),
                [],
            ],
            'partialIntersectionWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->partialIntersectionWith(2, $s)->toArray(),
                [1, 2, 3],
            ],
            'partialIntersectionWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->partialIntersectionWith(3, $s, [2, 3, 4])->toArray(),
                [2, 3],
            ],
            'partialIntersectionCoerciveWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->partialIntersectionCoerciveWith(2, $s)->toArray(),
                [1, 2, 3],
            ],
            'partialIntersectionCoerciveWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->partialIntersectionCoerciveWith(3, $s, ['2', '3', '4'])->toArray(),
                [2, 3],
            ],
            'unionWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->unionWith($s)->toArray(),
                [1, 2, 3],
            ],
            'unionWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->unionWith($s, [3, 4])->toArray(),
                [1, 3, 2, 4],
            ],
            'unionCoerciveWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->unionCoerciveWith($s)->toArray(),
                [1, 2, 3],
            ],
            'unionCoerciveWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): array => $s->unionCoerciveWith($s, [3, 4])->toArray(),
                [1, 3, 2, 4],
            ],
            'startsWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->startsWith($s),
                true,
            ],
            'startsWith after a step' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->map(static fn (int $x): int => $x * 10)->startsWith($s),
                true,
            ],
            'startsWithCoercive' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->startsWithCoercive($s),
                true,
            ],
            'startsWithCoercive after a step' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->map(static fn (int $x): int => $x * 10)->startsWithCoercive($s),
                true,
            ],
            'endsWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->endsWith($s),
                true,
            ],
            'endsWith after a step' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->map(static fn (int $x): int => $x * 10)->endsWith($s),
                true,
            ],
            'endsWithCoercive' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->endsWithCoercive($s),
                true,
            ],
            'endsWithCoercive after a step' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->map(static fn (int $x): int => $x * 10)->endsWithCoercive($s),
                true,
            ],
            'sameWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->sameWith($s),
                true,
            ],
            'sameWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->sameWith($s, [1, 2, 3]),
                true,
            ],
            'sameWith mixed different' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->sameWith($s, [1, 2, 4]),
                false,
            ],
            'sameCountWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->sameCountWith($s),
                true,
            ],
            'sameCountWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->sameCountWith($s, [7, 8, 9]),
                true,
            ],
            'sameCountWith mixed different' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->sameCountWith($s, [7, 8]),
                false,
            ],
            'arePermutationsWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->arePermutationsWith($s),
                true,
            ],
            'arePermutationsWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->arePermutationsWith($s, [3, 1, 2]),
                true,
            ],
            'arePermutationsWith mixed different' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->arePermutationsWith($s, ['3', '1', '2']),
                false,
            ],
            'arePermutationsCoerciveWith' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->arePermutationsCoerciveWith($s),
                true,
            ],
            'arePermutationsCoerciveWith mixed' => [
                GeneratorFixture::getGenerator([1, 2, 3]),
                static fn (Stream $s): bool => $s->arePermutationsCoerciveWith($s, ['3', '1', '2']),
                true,
            ],
            'startsWith NAN' => [
                GeneratorFixture::getGenerator([NAN]),
                static fn (Stream $s): bool => $s->startsWith($s),
                true,
            ],
            'startsWithCoercive unserializable' => [
                GeneratorFixture::getGenerator([new NonSerializableFixture(1)]),
                static fn (Stream $s): bool => $s->startsWithCoercive($s),
                true,
            ],
            'endsWith NAN' => [
                GeneratorFixture::getGenerator([NAN]),
                static fn (Stream $s): bool => $s->endsWith($s),
                true,
            ],
            'endsWithCoercive unserializable' => [
                GeneratorFixture::getGenerator([new NonSerializableFixture(1)]),
                static fn (Stream $s): bool => $s->endsWithCoercive($s),
                true,
            ],
            'sameWith NAN' => [
                GeneratorFixture::getGenerator([NAN]),
                static fn (Stream $s): bool => $s->sameWith($s),
                true,
            ],
            'sameWith NAN twice' => [
                GeneratorFixture::getGenerator([NAN]),
                static fn (Stream $s): bool => $s->sameWith($s, $s),
                true,
            ],
            'sameWith NAN mixed' => [
                GeneratorFixture::getGenerator([NAN]),
                static fn (Stream $s): bool => $s->sameWith($s, [NAN]),
                false,
            ],
        ];
    }

    /**
     * @test         stream passed to its own method on a stream over an IteratorAggregate
     * @dataProvider dataProviderForSelfReferenceIteratorAggregate
     * @param        \IteratorAggregate          $input
     * @param        callable(Stream): mixed      $operation
     * @param        mixed                        $expected
     */
    public function testSelfReferenceIteratorAggregate(\IteratorAggregate $input, callable $operation, mixed $expected): void
    {
        // Given
        $stream = Stream::of($input);

        // When
        $result = $operation($stream);

        // Then
        $this->assertSame($expected, $result);
    }

    public static function dataProviderForSelfReferenceIteratorAggregate(): array
    {
        return [
            'compress' => [
                Stream::of(GeneratorFixture::getGenerator([1, 0, 2])),
                static fn (Stream $s): array => $s->compress($s)->toArray(),
                [1, 2],
            ],
            'compress after a step' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->map(static fn (int $x): int => $x - 1)->compress($s)->toArray(),
                [1, 2],
            ],
            'mergeSortedWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->mergeSortedWith($s)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'mergeSortedWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->mergeSortedWith($s, [2.5])->toArray(),
                [1, 1, 2, 2, 2.5, 3, 3],
            ],
            'mergeSortedByWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->mergeSortedByWith(static fn (int|float $x): int|float => $x, $s)->toArray(),
                [1, 1, 2, 2, 3, 3],
            ],
            'mergeSortedByWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->mergeSortedByWith(static fn (int|float $x): int|float => $x, $s, [2.5])->toArray(),
                [1, 1, 2, 2, 2.5, 3, 3],
            ],
            'zipWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->zipWith($s)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->zipWith($s, ['a', 'b', 'c'])->toArray(),
                [[1, 1, 'a'], [2, 2, 'b'], [3, 3, 'c']],
            ],
            'zipWith other first' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->zipWith(['a', 'b', 'c'], $s)->toArray(),
                [[1, 'a', 1], [2, 'b', 2], [3, 'c', 3]],
            ],
            'zipWith after a step' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->map(static fn (int $x): int => $x * 10)->zipWith($s)->toArray(),
                [[10, 10], [20, 20], [30, 30]],
            ],
            'zipWith named' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->zipWith(other: $s)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipLongestWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->zipLongestWith($s)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipLongestWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->zipLongestWith($s, ['a'])->toArray(),
                [[1, 1, 'a'], [2, 2, null], [3, 3, null]],
            ],
            'zipFilledWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->zipFilledWith(0, $s)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipFilledWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->zipFilledWith(0, $s, ['a'])->toArray(),
                [[1, 1, 'a'], [2, 2, 0], [3, 3, 0]],
            ],
            'zipEqualWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->zipEqualWith($s)->toArray(),
                [[1, 1], [2, 2], [3, 3]],
            ],
            'zipEqualWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->zipEqualWith($s, [4, 5, 6])->toArray(),
                [[1, 1, 4], [2, 2, 5], [3, 3, 6]],
            ],
            'intersectionWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->intersectionWith($s)->toArray(),
                [1, 2, 3],
            ],
            'intersectionWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->intersectionWith($s, [2, 3, 4])->toArray(),
                [2, 3],
            ],
            'intersectionCoerciveWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->intersectionCoerciveWith($s)->toArray(),
                [1, 2, 3],
            ],
            'intersectionCoerciveWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->intersectionCoerciveWith($s, ['2', '3', '4'])->toArray(),
                [2, 3],
            ],
            'symmetricDifferenceWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->symmetricDifferenceWith($s)->toArray(),
                [],
            ],
            'symmetricDifferenceWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->symmetricDifferenceWith($s, [1, 2, 3])->toArray(),
                [],
            ],
            'symmetricDifferenceCoerciveWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->symmetricDifferenceCoerciveWith($s)->toArray(),
                [],
            ],
            'symmetricDifferenceCoerciveWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->symmetricDifferenceCoerciveWith($s, ['1', '2', '3'])->toArray(),
                [],
            ],
            'differenceWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->differenceWith($s)->toArray(),
                [],
            ],
            'differenceWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->differenceWith([4], $s)->toArray(),
                [],
            ],
            'differenceCoerciveWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->differenceCoerciveWith($s)->toArray(),
                [],
            ],
            'differenceCoerciveWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->differenceCoerciveWith(['4'], $s)->toArray(),
                [],
            ],
            'partialIntersectionWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->partialIntersectionWith(2, $s)->toArray(),
                [1, 2, 3],
            ],
            'partialIntersectionWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->partialIntersectionWith(3, $s, [2, 3, 4])->toArray(),
                [2, 3],
            ],
            'partialIntersectionCoerciveWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->partialIntersectionCoerciveWith(2, $s)->toArray(),
                [1, 2, 3],
            ],
            'partialIntersectionCoerciveWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->partialIntersectionCoerciveWith(3, $s, ['2', '3', '4'])->toArray(),
                [2, 3],
            ],
            'unionWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->unionWith($s)->toArray(),
                [1, 2, 3],
            ],
            'unionWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->unionWith($s, [3, 4])->toArray(),
                [1, 3, 2, 4],
            ],
            'unionCoerciveWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->unionCoerciveWith($s)->toArray(),
                [1, 2, 3],
            ],
            'unionCoerciveWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): array => $s->unionCoerciveWith($s, [3, 4])->toArray(),
                [1, 3, 2, 4],
            ],
            'startsWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->startsWith($s),
                true,
            ],
            'startsWith after a step' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->map(static fn (int $x): int => $x * 10)->startsWith($s),
                true,
            ],
            'startsWithCoercive' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->startsWithCoercive($s),
                true,
            ],
            'startsWithCoercive after a step' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->map(static fn (int $x): int => $x * 10)->startsWithCoercive($s),
                true,
            ],
            'endsWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->endsWith($s),
                true,
            ],
            'endsWith after a step' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->map(static fn (int $x): int => $x * 10)->endsWith($s),
                true,
            ],
            'endsWithCoercive' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->endsWithCoercive($s),
                true,
            ],
            'endsWithCoercive after a step' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->map(static fn (int $x): int => $x * 10)->endsWithCoercive($s),
                true,
            ],
            'sameWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->sameWith($s),
                true,
            ],
            'sameWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->sameWith($s, [1, 2, 3]),
                true,
            ],
            'sameWith mixed different' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->sameWith($s, [1, 2, 4]),
                false,
            ],
            'sameCountWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->sameCountWith($s),
                true,
            ],
            'sameCountWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->sameCountWith($s, [7, 8, 9]),
                true,
            ],
            'sameCountWith mixed different' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->sameCountWith($s, [7, 8]),
                false,
            ],
            'arePermutationsWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->arePermutationsWith($s),
                true,
            ],
            'arePermutationsWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->arePermutationsWith($s, [3, 1, 2]),
                true,
            ],
            'arePermutationsWith mixed different' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->arePermutationsWith($s, ['3', '1', '2']),
                false,
            ],
            'arePermutationsCoerciveWith' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->arePermutationsCoerciveWith($s),
                true,
            ],
            'arePermutationsCoerciveWith mixed' => [
                Stream::of(GeneratorFixture::getGenerator([1, 2, 3])),
                static fn (Stream $s): bool => $s->arePermutationsCoerciveWith($s, ['3', '1', '2']),
                true,
            ],
            'startsWith NAN' => [
                Stream::of(GeneratorFixture::getGenerator([NAN])),
                static fn (Stream $s): bool => $s->startsWith($s),
                true,
            ],
            'startsWithCoercive unserializable' => [
                Stream::of(GeneratorFixture::getGenerator([new NonSerializableFixture(1)])),
                static fn (Stream $s): bool => $s->startsWithCoercive($s),
                true,
            ],
            'endsWith NAN' => [
                Stream::of(GeneratorFixture::getGenerator([NAN])),
                static fn (Stream $s): bool => $s->endsWith($s),
                true,
            ],
            'endsWithCoercive unserializable' => [
                Stream::of(GeneratorFixture::getGenerator([new NonSerializableFixture(1)])),
                static fn (Stream $s): bool => $s->endsWithCoercive($s),
                true,
            ],
            'sameWith NAN' => [
                Stream::of(GeneratorFixture::getGenerator([NAN])),
                static fn (Stream $s): bool => $s->sameWith($s),
                true,
            ],
            'sameWith NAN twice' => [
                Stream::of(GeneratorFixture::getGenerator([NAN])),
                static fn (Stream $s): bool => $s->sameWith($s, $s),
                true,
            ],
            'sameWith NAN mixed' => [
                Stream::of(GeneratorFixture::getGenerator([NAN])),
                static fn (Stream $s): bool => $s->sameWith($s, [NAN]),
                false,
            ],
        ];
    }
}

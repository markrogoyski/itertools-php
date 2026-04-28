<?php

namespace IterTools\Tests\Fixture;

use IterTools\Combinatorics;
use IterTools\Infinite;
use IterTools\Math;
use IterTools\Multi;
use IterTools\Random;
use IterTools\Set;
use IterTools\Single;
use IterTools\Sort;
use IterTools\Stream;
use IterTools\Tests\Fixture;
use IterTools\Transform;

trait DataProvider
{
    public static function dataProviderForEmptyIterable(): array
    {
        return [
            [[]],
            [Fixture\GeneratorFixture::getGenerator([])],
            [new Fixture\ArrayIteratorFixture([])],
            [new Fixture\IteratorAggregateFixture([])],
        ];
    }

    // LOOP TOOLS

    public static function dataProviderForIterableLoopTools(): \Generator
    {
        foreach (self::dataProviderForMultiLoopTools() as $loopTool) {
            yield $loopTool;
        }
        foreach (self::dataProviderForSingleLoopTools() as $loopTool) {
            yield $loopTool;
        }
        foreach (self::dataProviderForInfiniteLoopTools() as $loopTool) {
            yield $loopTool;
        }
        foreach (self::dataProviderForRandomLoopTools() as $loopTool) {
            yield $loopTool;
        }
        foreach (self::dataProviderForMathLoopTools() as $loopTool) {
            yield $loopTool;
        }
        foreach (self::dataProviderForSetLoopTools() as $loopTool) {
            yield $loopTool;
        }
        foreach (self::dataProviderForSortLoopTools() as $loopTool) {
            yield $loopTool;
        }
        foreach (self::dataProviderForTransformLoopTools() as $loopTool) {
            yield $loopTool;
        }
        foreach (self::dataProviderForCombinatoricsLoopTools() as $loopTool) {
            yield $loopTool;
        }
    }

    public static function dataProviderForMultiLoopTools(): array
    {
        return [
            [Multi::chain([1, 2, 3], [4, 5, 6])],
            [Multi::roundRobin([1, 2, 3], [4, 5, 6])],
            [Multi::zip([1, 2, 3], [4, 5, 6])],
            [Multi::zipEqual([1, 2, 3], [4, 5, 6])],
            [Multi::zipFilled([1, 2, 3], ['filler', 5, 6])],
            [Multi::zipLongest([1, 2, 3], [4, 5, 6])],
            [Multi::unzip([[1, 'a'], [2, 'b'], [3, 'c']])],
        ];
    }

    public static function dataProviderForSingleLoopTools(): array
    {
        return [
            [Single::accumulate([1, 2, 3, 4, 5], fn ($a, $b) => $a + $b)],
            [Single::chunkwise([1, 2, 3, 4, 5], 2)],
            [Single::chunkwiseOverlap([1, 2, 3, 4, 5], 2, 1)],
            [Single::compress([1, 2, 3, 4, 5], [1, 1, 0, 0, 1])],
            [Single::compressAssociative(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5], ['a', 'b', 'd'])],
            [Single::dropWhile([1, 2, 3, 4, 5], fn ($x) => $x < 2)],
            [Single::enumerate([1, 2, 3, 4, 5])],
            [Single::filter([1, 2, 3, 4, 5], fn ($x) => $x < 2)],
            [Single::filterTrue([1, 2, 3, 4, 5], fn ($x) => $x < 2)],
            [Single::filterTrue([1, 2, 3, 4, 5])],
            [Single::filterFalse([1, 2, 3, 4, 5], fn ($x) => $x < 2)],
            [Single::filterFalse([1, 2, 3, 4, 5])],
            [Single::filterKeys([1, 2, 3, 4, 5], fn ($x) => $x < 2)],
            [Single::flatMap([1, 2, 3, 4, 5], fn ($x) => [$x, $x])],
            [Single::flatten([1, 2, 3, [4, 5]], 1)],
            [Single::groupBy([1, 2, 3, 4, 5], fn ($x) => $x < 2)],
            [Single::intersperse([1, 2, 3, 4, 5], 0)],
            [Single::limit([1, 2, 3, 4, 5], 3)],
            [Single::map([1, 2, 3, 4, 5], fn ($x) => $x ** 2)],
            [Single::mapSpread([[1, 2], [3, 4]], fn ($a, $b) => $a + $b)],
            [Single::pairwise([1, 2, 3, 4, 5])],
            [Single::reindex([1, 2, 3, 4, 5], fn ($x) => $x)],
            [Single::repeat(10, 5)],
            [Single::reverse([1, 2, 3, 4, 5])],
            [Single::skip([1, 2, 3, 4, 5], 2)],
            [Single::slice([1, 2, 3, 4, 5], 1, 4)],
            [Single::string('abcdefg')],
            [Single::takeWhile([1, 2, 3, 4, 5], fn ($x) => $x < 2)],
        ];
    }

    public static function dataProviderForInfiniteLoopTools(): array
    {
        return [
            [Infinite::count(1, 1)],
            [Infinite::cycle([1, 2, 3])],
            [Infinite::repeat(5)],
            [Infinite::iterate(1, fn ($x) => $x + 1)],

        ];
    }

    public static function dataProviderForRandomLoopTools(): array
    {
        return [
            [Random::choice([1, 2, 3], 3)],
            [Random::coinFlip(5)],
            [Random::number(1, 10, 5)],
            [Random::percentage(5)],
            [Random::rockPaperScissors(5)],
        ];
    }

    public static function dataProviderForMathLoopTools(): array
    {
        return [
            [Math::frequencies([1, 2, 3, 4, 5])],
            [Math::relativeFrequencies([1, 2, 3, 4, 5])],
            [Math::runningAverage([1, 2, 3, 4, 5])],
            [Math::runningDifference([1, 2, 3, 4, 5])],
            [Math::runningMax([1, 2, 3, 4, 5])],
            [Math::runningMin([1, 2, 3, 4, 5])],
            [Math::runningProduct([1, 2, 3, 4, 5])],
            [Math::runningTotal([1, 2, 3, 4, 5])],
        ];
    }

    public static function dataProviderForSetLoopTools(): array
    {
        return [
            [Set::distinct([1, 2, 3, 4, 5])],
            [Set::distinctAdjacent([1, 1, 2, 2, 3, 3, 4, 4, 5, 5])],
            [Set::distinctAdjacentBy([1, 1, 2, 2, 3, 3, 4, 4, 5, 5], fn ($x) => $x)],
            [Set::intersection([1, 2, 3, 4, 5], [2, 3, 4])],
            [Set::intersectionCoercive([1, 2, 3, 4, 5], [2, 3, 4])],
            [Set::partialIntersection(2, [1, 2, 3, 4, 5], [2, 3, 4])],
            [Set::partialIntersectionCoercive(2, [1, 2, 3, 4, 5], [2, 3, 4])],
            [Set::symmetricDifference([1, 2, 3, 4, 5], [2, 3, 4])],
            [Set::symmetricDifferenceCoercive([1, 2, 3, 4, 5], [2, 3, 4])],
            [Set::union([1, 2, 3, 4, 5], [2, 3, 4])],
            [Set::unionCoercive([1, 2, 3, 4, 5], [2, 3, 4])],
        ];
    }

    public static function dataProviderForSortLoopTools(): array
    {
        return [
            [Sort::sort([5, 4, 1, 3, 2])],
            [Sort::asort([5, 4, 1, 3, 2])],
            [Sort::sortBy([5, 4, 1, 3, 2], fn ($x) => $x)],
            [Sort::asortBy([5, 4, 1, 3, 2], fn ($x) => $x)],
            [Sort::largest([5, 4, 1, 3, 2], 3)],
            [Sort::smallest([5, 4, 1, 3, 2], 3)],
        ];
    }

    public static function dataProviderForTransformLoopTools(): array
    {
        return [
            [Transform::partition([1, 2, 3, 4, 5], fn ($x) => $x % 2 === 0)],
            [Transform::tee([1, 2, 3, 4, 5], 2)],
            [Transform::toArray([1, 2, 3, 4, 5])],
            [Transform::toAssociativeArray([1, 2, 3, 4, 5])],
            [Transform::toIterator([1, 2, 3, 4, 5])],
        ];
    }

    public static function dataProviderForCombinatoricsLoopTools(): array
    {
        return [
            [Combinatorics::product([1, 2], ['a', 'b'])],
            [Combinatorics::permutations([1, 2, 3], 2)],
            [Combinatorics::combinations([1, 2, 3], 2)],
            [Combinatorics::combinationsWithReplacement([1, 2, 3], 2)],
            [Combinatorics::powerset([1, 2, 3])],
        ];
    }

    // STREAM TOOLS

    public static function dataProviderForIterableStreamTools(): \Generator
    {
        foreach (self::dataProviderForSourceStreamTools() as $loopTool) {
            yield $loopTool;
        }
        foreach (self::dataProviderForStreamOperations() as $loopTool) {
            yield $loopTool;
        }
    }

    public static function dataProviderForSourceStreamTools(): array
    {
        return [
            [Stream::of([1, 2, 3, 4, 5])],
            [Stream::ofCoinFlips(5)],
            [Stream::ofEmpty()],
            [Stream::ofRandomChoice([1, 2, 3, 4, 5], 5)],
            [Stream::ofRandomNumbers(1, 5, 5)],
            [Stream::ofRandomPercentage(5)],
            [Stream::ofRange(0, 5)],
            [Stream::ofRockPaperScissors(5)],
        ];
    }

    public static function dataProviderForStreamOperations(): array
    {
        return [
            [Stream::of([1, 2, 3, 4, 5])->accumulate(fn ($a, $b) => $a + $b)],
            [Stream::of([1, 2, 3, 4, 5])->asort()],
            [Stream::of([1, 2, 3, 4, 5])->chainWith([6, 7, 8, 9, 10])],
            [Stream::of([1, 2, 3, 4, 5])->compress([1, 1, 0, 0, 1])],
            [Stream::of([1, 2, 3, 4, 5])->compressAssociative(['0', '1', '4'])],
            [Stream::of([1, 2, 3, 4, 5])->chunkwise(2)],
            [Stream::of([1, 2, 3, 4, 5])->chunkwiseOverlap(2, 1)],
            [Stream::of([1, 2, 3, 4, 5])->distinct()],
            [Stream::of([1, 1, 2, 2, 3, 3, 4, 4, 5, 5])->distinctAdjacent()],
            [Stream::of([1, 1, 2, 2, 3, 3, 4, 4, 5, 5])->distinctAdjacentBy(fn ($x) => $x)],
            [Stream::of([1, 2, 3, 4, 5])->dropWhile(fn ($x) => $x < 2)],
            [Stream::of([1, 2, 3, 4, 5])->enumerate()],
            [Stream::of([1, 2, 3, 4, 5])->filter(fn ($x) => $x < 2)],
            [Stream::of([1, 2, 3, 4, 5])->filterTrue()],
            [Stream::of([1, 2, 3, 4, 5])->filterTrue(fn ($x) => $x < 2)],
            [Stream::of([1, 2, 3, 4, 5])->filterFalse(fn ($x) => $x < 2)],
            [Stream::of([1, 2, 3, 4, 5])->filterFalse()],
            [Stream::of([1, 2, 3, 4, 5])->filterKeys(fn ($x) => $x < 2)],
            [Stream::of([1, 2, 3, 4, 5])->flatMap(fn ($x) => [$x, $x])],
            [Stream::of([1, [2, 3], [4, 5]])->flatten()],
            [Stream::of([1, 2, 3, 4, 5])->groupBy(fn ($x) => $x < 2)],
            [Stream::of([1, 2, 3, 4, 5])->infiniteCycle()],
            [Stream::of([1, 2, 3, 4, 5])->intersperse(0)],
            [Stream::of([1, 2, 3, 4, 5])->intersectionWith([2, 3, 4])],
            [Stream::of([1, 2, 3, 4, 5])->intersectionCoerciveWith([2, 3, 4])],
            [Stream::of([1, 2, 3, 4, 5])->limit(3)],
            [Stream::of([1, 2, 3, 4, 5])->map(fn ($x) => $x**2)],
            [Stream::of([[1, 2], [3, 4]])->mapSpread(fn ($a, $b) => $a + $b)],
            [Stream::of([1, 2, 3, 4, 5])->pairwise()],
            [Stream::of([1, 2, 3, 4, 5])->partialIntersectionWith(1, [2, 3, 4])],
            [Stream::of([1, 2, 3, 4, 5])->partialIntersectionCoerciveWith(1, [2, 3, 4])],
            [Stream::of([1, 2, 3, 4, 5])->peek(fn ($x) => $x)],
            [Stream::of([1, 2, 3])->productWith(['a', 'b'])],
            [Stream::of([1, 2, 3])->permutations(2)],
            [Stream::of([1, 2, 3])->combinations(2)],
            [Stream::of([1, 2, 3])->combinationsWithReplacement(2)],
            [Stream::of([1, 2, 3])->powerset()],
            [Stream::of([1, 2, 3, 4, 5])->reindex(fn ($x) => $x)],
            [Stream::of([1, 2, 3])->roundRobinWith([4, 5, 6])],
            [Stream::of([1, 2, 3, 4, 5])->reverse()],
            [Stream::of([1, 2, 3, 4, 5])->runningAverage()],
            [Stream::of([1, 2, 3, 4, 5])->runningDifference()],
            [Stream::of([1, 2, 3, 4, 5])->runningMax()],
            [Stream::of([1, 2, 3, 4, 5])->runningMin()],
            [Stream::of([1, 2, 3, 4, 5])->runningProduct()],
            [Stream::of([1, 2, 3, 4, 5])->runningTotal()],
            [Stream::of([1, 2, 3, 4, 5])->skip(2)],
            [Stream::of([1, 2, 3, 4, 5])->slice(0, 3)],
            [Stream::of([1, 2, 3, 4, 5])->sort()],
            [Stream::of([1, 2, 3, 4, 5])->sortBy(fn ($x) => $x)],
            [Stream::of([1, 2, 3, 4, 5])->asortBy(fn ($x) => $x)],
            [Stream::of([1, 2, 3, 4, 5])->largest(3)],
            [Stream::of([1, 2, 3, 4, 5])->smallest(3)],
            [Stream::of([1, 2, 3, 4, 5])->symmetricDifferenceWith([2, 3, 4])],
            [Stream::of([1, 2, 3, 4, 5])->symmetricDifferenceCoerciveWith([2, 3, 4])],
            [Stream::of([1, 2, 3, 4, 5])->takeWhile(fn ($x) => $x < 2)],
            [Stream::of([1, 2, 3, 4, 5])->tee(2)],
            [Stream::of([1, 2, 3, 4, 5])->unionWith([2, 3, 4])],
            [Stream::of([1, 2, 3, 4, 5])->unionCoerciveWith([2, 3, 4])],
            [Stream::of([1, 2, 3, 4, 5])->zipWith([6, 7, 8, 9, 10])],
            [Stream::of([1, 2, 3, 4, 5])->zipEqualWith([6, 7, 8, 9, 10])],
            [Stream::of([1, 2, 3, 4, 5])->zipFilledWith('filler', [6, 7, 8, 9, 10])],
            [Stream::of([1, 2, 3, 4, 5])->zipLongestWith([6, 7, 8, 9, 10])],
            [Stream::of([[1, 2, 3], [4, 5, 6]])->zip()],
            [Stream::of([[1, 2, 3], [4, 5]])->zipLongest()],
            [Stream::of([[1, 2, 3], [4, 5]])->zipFilled('x')],
            [Stream::of([[1, 2, 3], [4, 5, 6]])->zipEqual()],
            [Stream::of([[1, 'a'], [2, 'b'], [3, 'c']])->unzip()],
        ];
    }
}

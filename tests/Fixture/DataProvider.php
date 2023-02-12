<?php

namespace IterTools\Tests\Fixture;

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
    public function dataProviderForEmptyIterable(): array
    {
        return [
            [[]],
            [Fixture\GeneratorFixture::getGenerator([])],
            [new Fixture\ArrayIteratorFixture([])],
            [new Fixture\IteratorAggregateFixture([])],
        ];
    }

    // LOOP TOOLS

    public function dataProviderForIterableLoopTools(): \Generator
    {
        foreach ($this->dataProviderForMultiLoopTools() as $loopTool) {
            yield $loopTool;
        }
        foreach ($this->dataProviderForSingleLoopTools() as $loopTool) {
            yield $loopTool;
        }
        foreach ($this->dataProviderForInfiniteLoopTools() as $loopTool) {
            yield $loopTool;
        }
        foreach ($this->dataProviderForRandomLoopTools() as $loopTool) {
            yield $loopTool;
        }
        foreach ($this->dataProviderForMathLoopTools() as $loopTool) {
            yield $loopTool;
        }
        foreach ($this->dataProviderForSetLoopTools() as $loopTool) {
            yield $loopTool;
        }
        foreach ($this->dataProviderForSortLoopTools() as $loopTool) {
            yield $loopTool;
        }
        foreach ($this->dataProviderForTransformLoopTools() as $loopTool) {
            yield $loopTool;
        }
    }

    public function dataProviderForMultiLoopTools(): array
    {
        return [
            [Multi::chain([1, 2, 3], [4, 5, 6])],
            [Multi::zip([1, 2, 3], [4, 5, 6])],
            [Multi::zipLongest([1, 2, 3], [4, 5, 6])],
            [Multi::zipEqual([1, 2, 3], [4, 5, 6])],
        ];
    }

    public function dataProviderForSingleLoopTools(): array
    {
        return [
            [Single::chunkwise([1, 2, 3, 4, 5], 2)],
            [Single::chunkwiseOverlap([1, 2, 3, 4, 5], 2, 1)],
            [Single::compress([1, 2, 3, 4, 5], [1, 1, 0, 0, 1])],
            [Single::compressAssociative(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5], ['a', 'b', 'd'])],
            [Single::dropWhile([1, 2, 3, 4, 5], fn ($x) => $x < 2)],
            [Single::filter([1, 2, 3, 4, 5], fn ($x) => $x < 2)],
            [Single::filterTrue([1, 2, 3, 4, 5], fn ($x) => $x < 2)],
            [Single::filterTrue([1, 2, 3, 4, 5])],
            [Single::filterFalse([1, 2, 3, 4, 5], fn ($x) => $x < 2)],
            [Single::filterFalse([1, 2, 3, 4, 5])],
            [Single::filterKeys([1, 2, 3, 4, 5], fn ($x) => $x < 2)],
            [Single::groupBy([1, 2, 3, 4, 5], fn ($x) => $x < 2)],
            [Single::limit([1, 2, 3, 4, 5], 3)],
            [Single::map([1, 2, 3, 4, 5], fn ($x) => $x ** 2)],
            [Single::pairwise([1, 2, 3, 4, 5])],
            [Single::reindex([1, 2, 3, 4, 5], fn ($x) => $x)],
            [Single::repeat(10, 5)],
            [Single::reverse([1, 2, 3, 4, 5])],
            [Single::string('abcdefg')],
            [Single::takeWhile([1, 2, 3, 4, 5], fn ($x) => $x < 2)],
        ];
    }

    public function dataProviderForInfiniteLoopTools(): array
    {
        return [
            [Infinite::count(1, 1)],
            [Infinite::cycle([1, 2, 3])],
            [Infinite::repeat(5)],

        ];
    }

    public function dataProviderForRandomLoopTools(): array
    {
        return [
            [Random::choice([1, 2, 3], 3)],
            [Random::coinFlip(5)],
            [Random::number(1, 10, 5)],
            [Random::percentage(5)],
            [Random::rockPaperScissors(5)],
        ];
    }

    public function dataProviderForMathLoopTools(): array
    {
        return [
            [Math::runningAverage([1, 2, 3, 4, 5])],
            [Math::runningDifference([1, 2, 3, 4, 5])],
            [Math::runningMax([1, 2, 3, 4, 5])],
            [Math::runningMin([1, 2, 3, 4, 5])],
            [Math::runningProduct([1, 2, 3, 4, 5])],
            [Math::runningTotal([1, 2, 3, 4, 5])],
        ];
    }

    public function dataProviderForSetLoopTools(): array
    {
        return [
            [Set::distinct([1, 2, 3, 4, 5])],
            [Set::intersection([1, 2, 3, 4, 5], [2, 3, 4])],
            [Set::intersectionCoercive([1, 2, 3, 4, 5], [2, 3, 4])],
            [Set::partialIntersection(2, [1, 2, 3, 4, 5], [2, 3, 4])],
            [Set::partialIntersectionCoercive(2, [1, 2, 3, 4, 5], [2, 3, 4])],
            [Set::symmetricDifference([1, 2, 3, 4, 5], [2, 3, 4])],
            [Set::symmetricDifferenceCoercive([1, 2, 3, 4, 5], [2, 3, 4])],
        ];
    }

    public function dataProviderForSortLoopTools(): array
    {
        return [
            [Sort::sort([5, 4, 1, 3, 2])],
            [Sort::asort([5, 4, 1, 3, 2])],
        ];
    }

    public function dataProviderForTransformLoopTools(): array
    {
        return [
            [Transform::tee([1, 2, 3, 4, 5], 2)],
            [Transform::toArray([1, 2, 3, 4, 5])],
            [Transform::toAssociativeArray([1, 2, 3, 4, 5])],
            [Transform::toIterator([1, 2, 3, 4, 5])],
        ];
    }

    // STREAM TOOLS

    public function dataProviderForIterableStreamTools(): \Generator
    {
        foreach ($this->dataProviderForSourceStreamTools() as $loopTool) {
            yield $loopTool;
        }
        foreach ($this->dataProviderForStreamOperations() as $loopTool) {
            yield $loopTool;
        }
    }

    public function dataProviderForSourceStreamTools(): array
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

    public function dataProviderForStreamOperations(): array
    {
        return [
            [Stream::of([1, 2, 3, 4, 5])->asort()],
            [Stream::of([1, 2, 3, 4, 5])->chainWith([6, 7, 8, 9, 10])],
            [Stream::of([1, 2, 3, 4, 5])->compress([1, 1, 0, 0, 1])],
            [Stream::of([1, 2, 3, 4, 5])->compressAssociative(['0', '1', '4'])],
            [Stream::of([1, 2, 3, 4, 5])->chunkwise(2)],
            [Stream::of([1, 2, 3, 4, 5])->chunkwiseOverlap(2, 1)],
            [Stream::of([1, 2, 3, 4, 5])->distinct()],
            [Stream::of([1, 2, 3, 4, 5])->dropWhile(fn ($x) => $x < 2)],
            [Stream::of([1, 2, 3, 4, 5])->filter(fn ($x) => $x < 2)],
            [Stream::of([1, 2, 3, 4, 5])->filterTrue()],
            [Stream::of([1, 2, 3, 4, 5])->filterTrue(fn ($x) => $x < 2)],
            [Stream::of([1, 2, 3, 4, 5])->filterFalse(fn ($x) => $x < 2)],
            [Stream::of([1, 2, 3, 4, 5])->filterFalse()],
            [Stream::of([1, 2, 3, 4, 5])->filterKeys(fn ($x) => $x < 2)],
            [Stream::of([1, 2, 3, 4, 5])->groupBy(fn ($x) => $x < 2)],
            [Stream::of([1, 2, 3, 4, 5])->infiniteCycle()],
            [Stream::of([1, 2, 3, 4, 5])->intersectionWith([2, 3, 4])],
            [Stream::of([1, 2, 3, 4, 5])->intersectionCoerciveWith([2, 3, 4])],
            [Stream::of([1, 2, 3, 4, 5])->limit(3)],
            [Stream::of([1, 2, 3, 4, 5])->map(fn ($x) => $x**2)],
            [Stream::of([1, 2, 3, 4, 5])->pairwise()],
            [Stream::of([1, 2, 3, 4, 5])->partialIntersectionWith(1, [2, 3, 4])],
            [Stream::of([1, 2, 3, 4, 5])->partialIntersectionCoerciveWith(1, [2, 3, 4])],
            [Stream::of([1, 2, 3, 4, 5])->reindex(fn ($x) => $x)],
            [Stream::of([1, 2, 3, 4, 5])->reverse()],
            [Stream::of([1, 2, 3, 4, 5])->runningAverage()],
            [Stream::of([1, 2, 3, 4, 5])->runningDifference()],
            [Stream::of([1, 2, 3, 4, 5])->runningMax()],
            [Stream::of([1, 2, 3, 4, 5])->runningMin()],
            [Stream::of([1, 2, 3, 4, 5])->runningProduct()],
            [Stream::of([1, 2, 3, 4, 5])->runningTotal()],
            [Stream::of([1, 2, 3, 4, 5])->sort()],
            [Stream::of([1, 2, 3, 4, 5])->symmetricDifferenceWith([2, 3, 4])],
            [Stream::of([1, 2, 3, 4, 5])->symmetricDifferenceCoerciveWith([2, 3, 4])],
            [Stream::of([1, 2, 3, 4, 5])->takeWhile(fn ($x) => $x < 2)],
            [Stream::of([1, 2, 3, 4, 5])->tee(2)],
            [Stream::of([1, 2, 3, 4, 5])->zipWith([6, 7, 8, 9, 10])],
            [Stream::of([1, 2, 3, 4, 5])->zipEqualWith([6, 7, 8, 9, 10])],
            [Stream::of([1, 2, 3, 4, 5])->zipLongestWith([6, 7, 8, 9, 10])],
        ];
    }
}

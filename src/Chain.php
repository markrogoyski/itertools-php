<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\IteratorFactory;

/**
 * Provides fluent interface for working with iterables.
 *
 * @implements \IteratorAggregate<mixed>
 */
class Chain implements \IteratorAggregate
{
    /**
     * @var iterable<mixed> iterable source
     */
    protected iterable $iterable;

    /**
     * Creates iterable instance with fluent interface.
     *
     * @param iterable<mixed> $iterable
     *
     * @return Chain<mixed>
     */
    public static function create(iterable $iterable): self
    {
        return new self($iterable);
    }

    /**
     * Compress an iterable source by filtering out data that is not selected.
     *
     * Selectors indicate which data. True value selects item. False value filters out data.
     *
     * @param iterable<bool> $selectors
     *
     * @return $this
     */
    public function compress(iterable $selectors): self
    {
        $this->iterable = Single::compress($this->iterable, $selectors);
        return $this;
    }

    /**
     * Drop elements from the iterable source while the predicate function is true.
     *
     * Once the predicate function returns false once, all remaining elements are returned.
     *
     * @param callable $predicate
     *
     * @return $this
     */
    public function dropWhile(callable $predicate): self
    {
        $this->iterable = Single::dropWhile($this->iterable, $predicate);
        return $this;
    }

    /**
     * Return elements from the iterable source as long as the predicate is true.
     *
     * If no predicate is provided, the boolean value of the data is used.
     *
     * @param callable $predicate
     *
     * @return $this
     */
    public function takeWhile(callable $predicate): self
    {
        $this->iterable = Single::takeWhile($this->iterable, $predicate);
        return $this;
    }

    /**
     * Filter out elements from the iterable source only returning elements where there predicate function is true.
     *
     * If no predicate is provided, the boolean value of the data is used.
     *
     * @param callable $predicate
     *
     * @return $this
     */
    public function filterTrue(callable $predicate): self
    {
        $this->iterable = Single::filterTrue($this->iterable, $predicate);
        return $this;
    }

    /**
     * Filter out elements from the iterable source only returning elements where the predicate function is false.
     *
     * If no predicate is provided, the boolean value of the data is used.
     *
     * @param callable $predicate
     *
     * @return $this
     */
    public function filterFalse(callable $predicate): self
    {
        $this->iterable = Single::filterFalse($this->iterable, $predicate);
        return $this;
    }

    /**
     * Group iterable source by a common data element.
     *
     * The groupKeyFunction determines the key to group elements by.
     *
     * @param callable $groupKeyFunction
     *
     * @return $this
     */
    public function groupBy(callable $groupKeyFunction): self
    {
        $this->iterable = Single::groupBy($this->iterable, $groupKeyFunction);
        return $this;
    }

    /**
     * Return pairs of elements from iterable source.
     *
     * Returns empty generator if given collection contains less than 2 elements.
     *
     * @return $this
     */
    public function pairwise(): self
    {
        $this->iterable = Single::pairwise($this->iterable);
        return $this;
    }

    /**
     * Chain iterable source withs given iterables together into a single iteration.
     *
     * Makes a single continuous sequence out of multiple sequences.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return $this
     */
    public function chainWith(iterable ...$iterables): self
    {
        $this->iterable = Multi::chain($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterate iterable source with another iterable collections simultaneously.
     *
     * Make an iterator that aggregates items from multiple iterators.
     * Similar to Python's zip function.
     *
     * For uneven lengths, iterations stops when the shortest iterable is exhausted.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return $this
     */
    public function zipWith(iterable ...$iterables): self
    {
        $this->iterable = Multi::zip($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterate iterable source with another iterable collections simultaneously.
     *
     * Make an iterator that aggregates items from multiple iterators.
     * Similar to Python's zip_longest function
     *
     * Iteration continues until the longest iterable is exhausted.
     * For uneven lengths, the exhausted iterables will produce null for the remaining iterations.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return $this
     */
    public function zipLongestWith(iterable ...$iterables): self
    {
        $this->iterable = Multi::zipLongest($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterate iterable source with another iterable collections of equal lengths simultaneously.
     *
     * Works like Multi::zip() method but throws \LengthException if lengths not equal,
     * i.e., at least one iterator ends before the others.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return $this
     */
    public function zipEqualWith(iterable ...$iterables): self
    {
        $this->iterable = Multi::zipEqual($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Cycle through the elements of iterable source sequentially forever
     *
     * @return $this
     */
    public function infiniteCycle(): self
    {
        $this->iterable = Infinite::cycle($this->iterable);
        return $this;
    }

    /**
     * Accumulate the running average (mean) over iterable source
     *
     * @param int|float|null $initialValue
     *
     * @return $this
     */
    public function runningAverage($initialValue = null): self
    {
        /** @var iterable<int|float> $iterable */
        $iterable = $this->iterable;
        $this->iterable = Math::runningAverage($iterable, $initialValue);
        return $this;
    }

    /**
     * Accumulate the running difference over iterable source
     *
     * @param int|float|null $initialValue
     *
     * @return $this
     */
    public function runningDifference($initialValue = null): self
    {
        /** @var iterable<int|float> $iterable */
        $iterable = $this->iterable;
        $this->iterable = Math::runningDifference($iterable, $initialValue);
        return $this;
    }

    /**
     * Accumulate the running max over iterable source
     *
     * @param int|float|null $initialValue
     *
     * @return $this
     */
    public function runningMax($initialValue = null): self
    {
        /** @var iterable<int|float> $iterable */
        $iterable = $this->iterable;
        $this->iterable = Math::runningMax($iterable, $initialValue);
        return $this;
    }

    /**
     * Accumulate the running min over iterable source
     *
     * @param int|float|null $initialValue
     *
     * @return $this
     */
    public function runningMin($initialValue = null): self
    {
        /** @var iterable<int|float> $iterable */
        $iterable = $this->iterable;
        $this->iterable = Math::runningMin($iterable, $initialValue);
        return $this;
    }

    /**
     * Accumulate the running product over iterable source
     *
     * @param int|float|null $initialValue
     *
     * @return $this
     */
    public function runningProduct($initialValue = null): self
    {
        /** @var iterable<int|float> $iterable */
        $iterable = $this->iterable;
        $this->iterable = Math::runningProduct($iterable, $initialValue);
        return $this;
    }

    /**
     * Accumulate the running total over iterable source
     *
     * @param int|float|null $initialValue
     *
     * @return $this
     */
    public function runningTotal($initialValue = null): self
    {
        /** @var iterable<int|float> $iterable */
        $iterable = $this->iterable;
        $this->iterable = Math::runningTotal($iterable, $initialValue);
        return $this;
    }

    /**
     * Returns true if iterable source is sorted in ascending order; otherwise false.
     *
     * Items of iterable source must be comparable.
     *
     * Returns true if iterable source is empty or has only one element.
     *
     * @return bool
     */
    public function isSorted(): bool
    {
        return Summary::isSorted($this->iterable);
    }

    /**
     * Returns true if iterable source is sorted in reverse descending order; otherwise false.
     *
     * Items of iterable source must be comparable.
     *
     * Returns true if iterable source is empty or has only one element.
     *
     * @return bool
     */
    public function isReversed(): bool
    {
        return Summary::isReversed($this->iterable);
    }

    /**
     * Returns true if iterable source and all given collections are the same.
     *
     * For single iterable or empty iterables list returns true.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     */
    public function sameWith(iterable ...$iterables): bool
    {
        return Summary::same($this->iterable, ...$iterables);
    }

    /**
     * Returns true if iterable source and all given collections have the same lengths.
     *
     * For single iterable or empty iterables list returns true.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     */
    public function sameCountWith(iterable ...$iterables): bool
    {
        return Summary::sameCount($this->iterable, ...$iterables);
    }

    /**
     * Reduces iterable source to the mean average of its items.
     *
     * Returns null if iterable source is empty.
     *
     * @return int|float|null
     */
    public function toAverage()
    {
        /** @var iterable<numeric> $iterable */
        $iterable = $this->iterable;
        return Reduce::toAverage($iterable);
    }

    /**
     * Reduces iterable source to its length.
     *
     * @return int
     */
    public function toCount(): int
    {
        return Reduce::toCount($this->iterable);
    }

    /**
     * Reduces iterable source to its max value.
     *
     * Items of iterable source must be comparable.
     *
     * Returns null if iterable source is empty.
     *
     * @return mixed
     */
    public function toMax()
    {
        return Reduce::toMax($this->iterable);
    }

    /**
     * Reduces iterable source to its min value.
     *
     * Items of iterable source must be comparable.
     *
     * Returns null if iterable source is empty.
     *
     * @return mixed
     */
    public function toMin()
    {
        return Reduce::toMin($this->iterable);
    }

    /**
     * Reduces iterable source to the product of its items.
     *
     * Returns null if iterable source is empty.
     *
     * @return int|float|null
     */
    public function toProduct()
    {
        /** @var iterable<numeric> $iterable */
        $iterable = $this->iterable;
        return Reduce::toProduct($iterable);
    }

    /**
     * Reduces iterable source to the sum of its items.
     *
     * @return int|float
     */
    public function toSum()
    {
        /** @var iterable<numeric> $iterable */
        $iterable = $this->iterable;
        return Reduce::toSum($iterable);
    }

    /**
     * Reduces iterable source like array_reduce() function.
     *
     * But unlike array_reduce(), it works with all iterable types.
     *
     * @param callable $reducer
     * @param mixed $initialValue
     *
     * @return mixed
     */
    public function toValue(callable $reducer, $initialValue = null)
    {
        return Reduce::toValue($this->iterable, $reducer, $initialValue);
    }

    /**
     * Chain constructor.
     *
     * @param iterable<mixed> $iterable
     */
    protected function __construct(iterable $iterable)
    {
        $this->iterable = $iterable;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Iterator<mixed>
     */
    public function getIterator(): \Iterator
    {
        return IteratorFactory::makeIterator($this->iterable);
    }
}

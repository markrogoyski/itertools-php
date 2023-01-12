<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\IteratorFactory;

/**
 * @implements \IteratorAggregate<mixed>
 */
class Chain implements \IteratorAggregate
{
    /**
     * @var iterable<mixed>
     */
    protected iterable $iterable;

    /**
     * @param iterable<mixed> $iterable
     *
     * @return Chain<mixed>
     */
    public static function create(iterable $iterable): self
    {
        return new self($iterable);
    }

    /**
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
     * @return $this
     */
    public function pairwise(): self
    {
        $this->iterable = Single::pairwise($this->iterable);
        return $this;
    }

    /**
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
     * @return $this
     */
    public function infiniteCycle(): self
    {
        $this->iterable = Infinite::cycle($this->iterable);
        return $this;
    }

    /**
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
     * @return bool
     */
    public function isSorted(): bool
    {
        return Summary::isSorted($this->iterable);
    }

    /**
     * @return bool
     */
    public function isReversed(): bool
    {
        return Summary::isReversed($this->iterable);
    }

    /**
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     */
    public function sameWith(iterable ...$iterables): bool
    {
        return Summary::same($this->iterable, ...$iterables);
    }

    /**
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     */
    public function sameCountWith(iterable ...$iterables): bool
    {
        return Summary::sameCount($this->iterable, ...$iterables);
    }

    /**
     * @return int|float|null
     */
    public function toAverage()
    {
        /** @var iterable<numeric> $iterable */
        $iterable = $this->iterable;
        return Reduce::toAverage($iterable);
    }

    /**
     * @return int
     */
    public function toCount(): int
    {
        return Reduce::toCount($this->iterable);
    }

    /**
     * @return mixed
     */
    public function toMax()
    {
        return Reduce::toMax($this->iterable);
    }

    /**
     * @return mixed
     */
    public function toMin()
    {
        return Reduce::toMin($this->iterable);
    }

    /**
     * @return int|float|null
     */
    public function toProduct()
    {
        /** @var iterable<numeric> $iterable */
        $iterable = $this->iterable;
        return Reduce::toProduct($iterable);
    }

    /**
     * @return numeric
     */
    public function toSum()
    {
        /** @var iterable<numeric> $iterable */
        $iterable = $this->iterable;
        return Reduce::toSum($iterable);
    }

    /**
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
     * @param iterable<mixed> $iterable
     */
    protected function __construct(iterable $iterable)
    {
        $this->iterable = $iterable;
    }

    /**
     * @return \Iterator<mixed>
     */
    public function getIterator(): \Iterator
    {
        return IteratorFactory::makeIterator($this->iterable);
    }
}

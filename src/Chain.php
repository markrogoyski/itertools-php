<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\IteratorFactory;

/**
 * @template mixed
 * @template mixed
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
     * @return $this
     */
    public function infiniteCycle(): self
    {
        $this->iterable = Infinite::cycle($this->iterable);
        return $this;
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

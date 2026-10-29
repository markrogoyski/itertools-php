<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

/**
 * IteratorAggregate fixture whose getIterator() returns the Traversable it wraps, the same object on every call.
 *
 * Used to pass one iterator through an aggregate or nested aggregates, and to build an aggregate that returns
 * itself (the default) or a cycle of aggregates.
 */
class WrappingIteratorAggregateFixture implements \IteratorAggregate
{
    private \Traversable $traversable;

    /**
     * @param \Traversable|null $traversable wrapped object; null wraps the fixture itself
     */
    public function __construct(?\Traversable $traversable = null)
    {
        $this->traversable = $traversable ?? $this;
    }

    public function wrap(\Traversable $traversable): void
    {
        $this->traversable = $traversable;
    }

    public function getIterator(): \Traversable
    {
        return $this->traversable;
    }
}

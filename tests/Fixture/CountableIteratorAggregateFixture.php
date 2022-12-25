<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

class CountableIteratorAggregateFixture implements \IteratorAggregate, \Countable
{
    private array $values;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->values);
    }

    public function count(): int
    {
        return count($this->values);
    }
}

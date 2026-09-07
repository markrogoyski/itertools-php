<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

/**
 * IteratorAggregate fixture that counts how many times getIterator() is called.
 *
 * Used to prove that lazy tools do not touch an IteratorAggregate source until
 * their result is first advanced.
 */
class CountingIteratorAggregateFixture implements \IteratorAggregate
{
    private array $values;

    private int $getIteratorCallCount = 0;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function getIterator(): \ArrayIterator
    {
        ++$this->getIteratorCallCount;

        return new \ArrayIterator($this->values);
    }

    public function getIteratorCallCount(): int
    {
        return $this->getIteratorCallCount;
    }
}

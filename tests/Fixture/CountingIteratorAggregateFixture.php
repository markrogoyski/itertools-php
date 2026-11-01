<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

/**
 * IteratorAggregate fixture that counts how many times getIterator() is called.
 *
 * Used to prove that lazy tools do not touch an IteratorAggregate source until
 * their result is first advanced, and that tools release the iterators it gives once they are done with them.
 */
class CountingIteratorAggregateFixture implements \IteratorAggregate
{
    private array $values;

    private int $getIteratorCallCount = 0;

    /**
     * @var array<\WeakReference<\ArrayIterator>>
     */
    private array $iterators = [];

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

        $iterator          = new \ArrayIterator($this->values);
        $this->iterators[] = \WeakReference::create($iterator);

        return $iterator;
    }

    public function getIteratorCallCount(): int
    {
        return $this->getIteratorCallCount;
    }

    /**
     * Number of iterators given by getIterator() that something still holds.
     */
    public function liveIteratorCount(): int
    {
        return \count(\array_filter(
            $this->iterators,
            static fn (\WeakReference $iterator): bool => $iterator->get() !== null
        ));
    }
}

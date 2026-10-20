<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

/**
 * An IteratorAggregate whose count() reports a size other than the number of elements it yields,
 * as a CachingIterator with FULL_CACHE over a source it has not read yet does (it reports 0).
 */
class MisreportingCountableFixture implements \IteratorAggregate, \Countable
{
    private array $values;
    private int $reportedCount;

    /**
     * @param array $values        the elements yielded on iteration
     * @param int   $reportedCount the size count() reports
     */
    public function __construct(array $values, int $reportedCount)
    {
        $this->values = $values;
        $this->reportedCount = $reportedCount;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->values);
    }

    public function count(): int
    {
        return $this->reportedCount;
    }
}

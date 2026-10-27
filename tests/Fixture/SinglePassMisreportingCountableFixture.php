<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

/**
 * A single-pass IteratorAggregate whose count() reports a size other than the number of elements it yields.
 *
 * getIterator() returns the same generator every time, so a second traversal throws once the first has
 * advanced it, as it does for any generator-backed source.
 */
class SinglePassMisreportingCountableFixture implements \IteratorAggregate, \Countable
{
    private \Generator $generator;
    private int $reportedCount;

    /**
     * @param array $values        the elements yielded on iteration
     * @param int   $reportedCount the size count() reports
     */
    public function __construct(array $values, int $reportedCount)
    {
        $this->generator = (static fn (): \Generator => yield from $values)();
        $this->reportedCount = $reportedCount;
    }

    public function getIterator(): \Generator
    {
        return $this->generator;
    }

    public function count(): int
    {
        return $this->reportedCount;
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

/**
 * IteratorAggregate fixture whose iterator appends each value to a shared log as it moves onto it.
 *
 * Used to prove that lazy tools never advance a source beyond the values they need.
 */
class RecordingIteratorAggregateFixture implements \IteratorAggregate
{
    private array $values;
    private array $pulled;

    /**
     * @param array $values
     * @param array $pulled log shared by reference; several fixtures may write to the same one
     */
    public function __construct(array $values, array &$pulled)
    {
        $this->values = $values;
        $this->pulled = &$pulled;
    }

    public function getIterator(): RecordingIteratorFixture
    {
        return new RecordingIteratorFixture($this->values, $this->pulled);
    }
}

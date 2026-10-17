<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

/**
 * Iterator fixture that appends each value to a shared log as rewind() or next() moves onto it.
 *
 * Used to prove that lazy tools never advance a source beyond the values they need.
 */
class RecordingIteratorFixture implements \Iterator
{
    private array $values;
    private array $pulled;
    private int $i;

    /**
     * @param array $values
     * @param array $pulled log shared by reference; several fixtures may write to the same one
     */
    public function __construct(array $values, array &$pulled)
    {
        $this->values = $values;
        $this->pulled = &$pulled;
        $this->i      = 0;
    }

    public function rewind(): void
    {
        $this->i = 0;
        $this->record();
    }

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->values[$this->i];
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->i;
    }

    public function next(): void
    {
        ++$this->i;
        $this->record();
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return \array_key_exists($this->i, $this->values);
    }

    private function record(): void
    {
        if ($this->valid()) {
            $this->pulled[] = $this->values[$this->i];
        }
    }
}

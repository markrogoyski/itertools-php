<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

class ArrayIteratorFixture implements \Iterator
{
    private array $values;
    private int $i;

    public function __construct(array $values)
    {
        $this->values = $values;
        $this->i      = 0;
    }

    public function rewind()
    {
        $this->i = 0;
    }

    /**
     * @return mixed
     */
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

    public function next()
    {
        ++$this->i;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->values[$this->i]);
    }
}

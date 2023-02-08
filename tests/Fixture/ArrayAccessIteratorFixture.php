<?php

namespace IterTools\Tests\Fixture;

class ArrayAccessIteratorFixture implements \ArrayAccess, \Iterator
{
    protected array $data;
    protected \ArrayIterator $iterator;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->iterator = new \ArrayIterator($this->data);
    }

    public function current()
    {
        return $this->iterator->current();
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function key()
    {
        return $this->iterator->key();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadFunctionCallException();
    }

    public function offsetUnset($offset)
    {
        throw new \BadFunctionCallException();
    }
}

<?php

namespace IterTools\Util\Iterators;

use IterTools\Stream;
use IterTools\Transform;
use IterTools\Util\NoValueMonad;

/**
 * @internal
 * @implements \Iterator<array<mixed>>
 */
class JustifyMultipleIterator implements \Iterator
{
    /**
     * @var array<\Iterator<mixed>>
     */
    protected array $iterators = [];
    /**
     * @var int
     */
    protected int $index = 0;
    /**
     * @var mixed
     */
    protected $filler;

    /**
     * @param mixed $filler
     * @param iterable<mixed> ...$iterables
     */
    public function __construct($filler, iterable ...$iterables)
    {
        $this->filler = $filler;

        foreach ($iterables as $iterable) {
            $this->iterators[] = Transform::toIterator($iterable);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return array<mixed>
     */
    public function current(): array
    {
        return Stream::of($this->iterators)
            ->map(function (\Iterator $iterator) {
                return $iterator->valid() ? $iterator->current() : $this->filler;
            })
            ->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function next(): void
    {
        foreach ($this->iterators as $iterator) {
            if ($iterator->valid()) {
                $iterator->next();
            }
        }
        $this->index++;
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function key(): int
    {
        return $this->index;
    }

    /**
     * {@inheritDoc}
     */
    public function valid(): bool
    {
        foreach ($this->iterators as $iterator) {
            if ($iterator->valid()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        foreach ($this->iterators as $iterator) {
            $iterator->rewind();
        }
        $this->index = 0;
    }
}

<?php

namespace IterTools\Util\Iterators;

use IterTools\Transform;
use IterTools\Util\NoValueMonad;

/**
 * @internal
 * @implements \Iterator<array<mixed>>
 */
final class JustifyMultipleIterator implements \Iterator
{
    /**
     * Each distinct iterator once, however many times it was passed.
     *
     * @var array<\Iterator<mixed>>
     */
    protected array $iterators = [];
    /**
     * For each position passed, the index in $iterators of its iterator.
     *
     * @var array<int>
     */
    protected array $iteratorIndexes = [];
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
    public function __construct(mixed $filler, iterable ...$iterables)
    {
        $this->filler = $filler;

        // An iterator passed more than once is advanced once per step and its value given to each of its
        // positions, so it reads as independent copies of itself; advancing it once per position would hand
        // each position a different element and skip values.
        $indexesById = [];
        foreach ($iterables as $iterable) {
            $iterator = Transform::toIterator($iterable);
            $id       = \spl_object_id($iterator);

            if (!isset($indexesById[$id])) {
                $indexesById[$id] = \count($this->iterators);
                $this->iterators[] = $iterator;
            }
            $this->iteratorIndexes[] = $indexesById[$id];
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return array<mixed>
     */
    public function current(): array
    {
        $values = \array_map(
            fn(\Iterator $iterator): mixed => $iterator->valid() ? $iterator->current() : $this->filler,
            $this->iterators,
        );

        return \array_map(static fn (int $index): mixed => $values[$index], $this->iteratorIndexes);
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

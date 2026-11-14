<?php

declare(strict_types=1);

namespace IterTools\Util\Iterators;

use IterTools\Transform;

/**
 * @internal
 * Iterates several iterables in step, giving a list of their current values in argument order.
 * Subclasses decide when iteration ends.
 *
 * @implements \Iterator<array<mixed>>
 */
abstract class AbstractMultipleIterator implements \Iterator
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
     * @param iterable<mixed> ...$iterables
     */
    public function __construct(iterable ...$iterables)
    {
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
            fn (\Iterator $iterator): mixed => $this->currentOf($iterator),
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
    public function rewind(): void
    {
        foreach ($this->iterators as $iterator) {
            $iterator->rewind();
        }
        $this->index = 0;
    }

    /**
     * The value an iterator gives each of its positions in the current list.
     *
     * @param \Iterator<mixed> $iterator
     *
     * @return mixed
     */
    protected function currentOf(\Iterator $iterator): mixed
    {
        return $iterator->current();
    }
}

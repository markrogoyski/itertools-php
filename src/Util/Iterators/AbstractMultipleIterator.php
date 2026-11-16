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
     * Whether no iterator was passed more than once, so $iteratorIndexes is 0, 1, 2, ... and the values of
     * $iterators are already in position order.
     */
    protected bool $allDistinct;
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

        $this->allDistinct = \count($this->iterators) === \count($this->iteratorIndexes);
    }

    /**
     * {@inheritDoc}
     *
     * @return array<mixed>
     */
    public function current(): array
    {
        // A plain loop rather than array_map with a closure, and no per-element hook: this runs once per
        // element of every row, and the calls cost several times the read itself.
        $values = [];
        foreach ($this->iterators as $iterator) {
            $values[] = $iterator->current();
        }

        return $this->allDistinct ? $values : $this->toPositions($values);
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
     * Spreads the current values of the distinct iterators, in the order of $iterators, over the positions
     * passed: an iterator passed more than once gives its value to each of its positions. Needed only when one
     * was; otherwise $values already are the positions, and current() returns them without this call.
     *
     * @param list<mixed> $values
     *
     * @return list<mixed>
     */
    protected function toPositions(array $values): array
    {
        $positions = [];
        foreach ($this->iteratorIndexes as $index) {
            $positions[] = $values[$index];
        }

        return $positions;
    }
}

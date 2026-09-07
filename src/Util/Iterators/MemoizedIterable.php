<?php

declare(strict_types=1);

namespace IterTools\Util\Iterators;

use IterTools\Transform;

/**
 * @internal
 *
 * @template TKey
 * @template TValue
 * @implements \IteratorAggregate<TKey, TValue>
 */
final class MemoizedIterable implements \IteratorAggregate
{
    /** @var iterable<mixed, mixed>|null */
    private ?iterable $iterable;

    /** @var \Iterator<mixed, mixed>|null */
    private ?\Iterator $source = null;

    /** @var list<array{0: TKey, 1: TValue}> */
    private array $cache = [];

    private bool $initialized = false;
    private bool $exhausted = false;
    private ?\Throwable $failure = null;
    private ?int $failureIndex = null;
    private bool $advancing = false;

    /** @param iterable<TKey, TValue> $iterable */
    public function __construct(iterable $iterable)
    {
        $this->iterable = $iterable;
    }

    /** @return \Generator<TKey, TValue> */
    public function getIterator(): \Generator
    {
        $position = 0;
        while (true) {
            if (isset($this->cache[$position])) {
                [$key, $value] = $this->cache[$position];
                ++$position;
                yield $key => $value;
                continue;
            }

            if ($this->failure !== null && $position === $this->failureIndex) {
                throw $this->failure;
            }
            if ($this->exhausted) {
                return;
            }

            $this->advanceFrontier();
        }
    }

    private function advanceFrontier(): void
    {
        if ($this->advancing) {
            throw new \LogicException('Memoized iterable cannot advance its source re-entrantly');
        }

        $this->advancing = true;
        try {
            if (!$this->initialized) {
                $this->initializeSource();
            } else {
                $this->advanceSource();
            }
        } catch (\Throwable $exception) {
            $this->failure = $exception;
            $this->failureIndex = \count($this->cache);
        } finally {
            $this->advancing = false;
        }
    }

    private function initializeSource(): void
    {
        $this->initialized = true;
        $iterable = $this->iterable;
        $this->iterable = null;
        if ($iterable === null) {
            throw new \LogicException('Memoized iterable source is unavailable');
        }

        $this->source = Transform::toIterator($iterable);
        $this->source->rewind();
        $this->cacheCurrentSourceValue();
    }

    private function advanceSource(): void
    {
        if ($this->source === null) {
            throw new \LogicException('Memoized iterable source is unavailable');
        }

        $this->source->next();
        $this->cacheCurrentSourceValue();
    }

    private function cacheCurrentSourceValue(): void
    {
        if ($this->source === null) {
            throw new \LogicException('Memoized iterable source is unavailable');
        }
        if (!$this->source->valid()) {
            $this->exhausted = true;
            return;
        }

        $this->cache[] = [$this->source->key(), $this->source->current()];
    }
}

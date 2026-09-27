<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 * Counts how often each distinct value has been registered.
 *
 * The registry behind the single-collection consumers: distinct, duplicates, allUnique, allEqual,
 * the frequency functions, toMode, and the subtracted side of difference. Equality is decided by
 * {@see UniqueExtractor} under the strict or coercive contract documented in README.md, section
 * "Strict and Coercive Types".
 *
 * First-seen semantics throughout: distinct values appear in {@see self::values()} and
 * {@see self::counts()} in the order they were first registered, and the representative kept for
 * each of them is the first value registered under its hash, not the last.
 *
 * Representatives are kept only when asked for at construction: the consumers that report them
 * (frequencies, toMode) need them, the ones that only count (distinct, allUnique, ...) do not, and
 * keeping them would hold every distinct value of a stream in memory.
 *
 * Registering a value also retains the identities its hash was derived from, for as long as the
 * counter lives. See {@see Identity} for why that is necessary — in short, an object or resource
 * id is unique only among the values alive at the moment it is read, so a key kept across
 * iterations must keep its anchors alive too.
 */
final class ValueCounter
{
    /**
     * First-seen representative per distinct value, keyed by hash, in first-seen order; empty
     * unless representatives are retained.
     *
     * @var array<string, mixed>
     */
    private array $values = [];
    /**
     * Occurrence count per distinct value, keyed by hash, in first-seen order.
     *
     * @var array<string, int<1, max>>
     */
    private array $counts = [];
    /**
     * Every anchor of every distinct registered value, keyed by {@see UniqueExtractor::anchorId()}.
     *
     * Keyed by {@see UniqueExtractor::anchorId()}, so the same object or resource nested in many
     * distinct registered values is held once instead of once per value — see {@see Identity}.
     *
     * @var array<string, object|resource|closed-resource>
     */
    // @phpstan-ignore property.onlyWritten
    private array $anchors = [];

    /**
     * @param bool $strict whether values compare under the strict or the coercive contract
     * @param bool $retainValues whether to keep a representative per distinct value for {@see self::values()}
     */
    public function __construct(private readonly bool $strict, private readonly bool $retainValues = false)
    {
    }

    /**
     * Registers one occurrence and returns the running count for this value.
     *
     * Returns 1 on first sight, 2 on the first repetition, and so on.
     *
     * @param mixed $value
     *
     * @return int<1, max>
     */
    public function add(mixed $value): int
    {
        $identity = UniqueExtractor::identify($value, $this->strict);

        $count = ($this->counts[$identity->key] ?? 0) + 1;

        if ($count === 1) {
            if ($this->retainValues) {
                $this->values[$identity->key] = $value;
            }

            foreach ($identity->anchors as $anchor) {
                $this->anchors[UniqueExtractor::anchorId($anchor)] = $anchor;
            }
        }

        $this->counts[$identity->key] = $count;

        return $count;
    }

    /**
     * Number of distinct values registered.
     *
     * @return int<0, max>
     */
    public function size(): int
    {
        return \count($this->counts);
    }

    /**
     * First-seen representative per distinct value, in first-seen order, keyed by hash.
     *
     * @return array<string, mixed>
     *
     * @throws \LogicException if the counter was constructed without retaining values
     */
    public function values(): array
    {
        if (!$this->retainValues) {
            throw new \LogicException('ValueCounter was constructed without retaining values');
        }

        return $this->values;
    }

    /**
     * Occurrence count per distinct value, in first-seen order, keyed by hash.
     *
     * The keys match those of {@see self::values()}, in the same order.
     *
     * @return array<string, int<1, max>>
     */
    public function counts(): array
    {
        return $this->counts;
    }
}

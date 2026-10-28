<?php

declare(strict_types=1);

namespace IterTools;

final class Random
{
    /**
     * Generate random selections from an array of values
     *
     * @param mixed[] $items
     * @param int     $repetitions
     * @param \Random\Engine|null $engine
     *
     * @return \Generator<mixed>
     */
    public static function choice(array $items, int $repetitions, ?\Random\Engine $engine = null): \Generator
    {
        if (\count($items) === 0) {
            throw new \RangeException('Array of items for choice cannot be empty');
        }

        $items = \array_values($items);
        $start = 0;
        $end   = \count($items) - 1;
        foreach (self::number($start, $end, $repetitions, $engine) as $i) {
            yield $items[$i];
        }
    }

    /**
     * Generate random coin flips
     *
     * @param int $repetitions
     * @param \Random\Engine|null $engine
     *
     * @return \Generator<int>
     */
    public static function coinFlip(int $repetitions, ?\Random\Engine $engine = null): \Generator
    {
        if ($repetitions < 0) {
            throw new \RangeException("Number of repetitions cannot be negative: {$repetitions}");
        }
        $randomizer = new \Random\Randomizer($engine);
        for ($i = $repetitions; $i > 0; $i--) {
            yield $randomizer->getInt(0, 1);
        }
    }

    /**
     * Generate random numbers (integers)
     *
     * @param int $min
     * @param int $max
     * @param int $repetitions
     * @param \Random\Engine|null $engine
     *
     * @return \Generator<int>
     */
    public static function number(int $min, int $max, int $repetitions, ?\Random\Engine $engine = null): \Generator
    {
        if ($repetitions < 0) {
            throw new \RangeException("Number of repetitions cannot be negative: {$repetitions}");
        }
        if ($max < $min) {
            throw new \RangeException("Max $max cannot be less than min $min");
        }
        $randomizer = new \Random\Randomizer($engine);
        for ($i = $repetitions; $i > 0; $i--) {
            yield $randomizer->getInt($min, $max);
        }
    }

    // A percentage's 53 random bits are drawn as 26 high and 27 low, so each draw fits a 32-bit PHP int.
    private const PERCENTAGE_HIGH_STEPS = 2 ** 26;
    private const PERCENTAGE_LOW_STEPS  = 2 ** 27;

    /**
     * Generate a random percentage in [0, 1): 0 inclusive, 1 exclusive
     *
     * @param int $repetitions
     * @param \Random\Engine|null $engine
     *
     * @return \Generator<float>
     */
    public static function percentage(int $repetitions, ?\Random\Engine $engine = null): \Generator
    {
        if ($repetitions < 0) {
            throw new \RangeException("Number of repetitions cannot be negative: {$repetitions}");
        }
        $randomizer = new \Random\Randomizer($engine);
        for ($i = $repetitions; $i > 0; $i--) {
            // 53 random bits over 2^53, as Randomizer::nextFloat() (PHP 8.3+) does: every value is exact, evenly
            // spaced, and below 1. Dividing a 63-bit int rounds the top ones up to 1.0. Dividing by powers of 2 is
            // exact, and the same two draws give the same values on 32- and 64-bit builds.
            $high = $randomizer->getInt(0, self::PERCENTAGE_HIGH_STEPS - 1);
            $low  = $randomizer->getInt(0, self::PERCENTAGE_LOW_STEPS - 1);
            yield ((float) $high + (float) $low / (float) self::PERCENTAGE_LOW_STEPS)
                / (float) self::PERCENTAGE_HIGH_STEPS;
        }
    }

    public const RPS_ROCK     = 'rock';
    public const RPS_PAPER    = 'paper';
    public const RPS_SCISSORS = 'scissors';
    private const RPS_HANDS   = [self::RPS_ROCK, self::RPS_PAPER, self::RPS_SCISSORS];

    /**
     * Generate random rock-paper-scissors hands
     *
     * @param int $repetitions
     * @param \Random\Engine|null $engine
     *
     * @return \Generator<string>
     */
    public static function rockPaperScissors(int $repetitions, ?\Random\Engine $engine = null): \Generator
    {
        if ($repetitions < 0) {
            throw new \RangeException("Number of repetitions cannot be negative: {$repetitions}");
        }
        $randomizer = new \Random\Randomizer($engine);
        for ($i = $repetitions; $i > 0; $i--) {
            yield self::RPS_HANDS[$randomizer->getInt(0, 2)];
        }
    }

    /**
     * Sample $size elements from the population without replacement.
     *
     * Every input position is used at most once; duplicate values in the population are
     * valid because positions, not values, are unique.
     *
     * Materializes the input. Output keys are sequential 0-indexed.
     *
     * @param iterable<mixed>     $data
     * @param int                 $size   number of elements to draw (0 ≤ $size ≤ count of population).
     * @param \Random\Engine|null $engine optional Random engine.
     *
     * @return \Generator<mixed>
     *
     * @throws \InvalidArgumentException if $size is negative.
     * @throws \LengthException          if $size exceeds the population size.
     */
    public static function sample(iterable $data, int $size, ?\Random\Engine $engine = null): \Generator
    {
        if ($size < 0) {
            throw new \InvalidArgumentException("Sample size cannot be negative: {$size}");
        }

        $array = \iterator_to_array(Transform::toIterator($data), false);
        $count = \count($array);

        if ($size > $count) {
            throw new \LengthException(
                "Sample size {$size} cannot exceed population size {$count}"
            );
        }

        if ($size === 0) {
            return;
        }

        $randomizer = new \Random\Randomizer($engine);
        $shuffled = $randomizer->shuffleArray($array);

        for ($i = 0; $i < $size; ++$i) {
            yield $shuffled[$i];
        }
    }

    /**
     * Sample up to $size elements uniformly at random in a single pass (Algorithm R).
     *
     * Algorithm R: the first $size elements fill the reservoir; each subsequent element at
     * 0-based index $i overwrites a uniformly random reservoir slot with probability
     * $size / ($i + 1). By induction this gives every element of an n-element input the same
     * $size / n probability of appearing in the result.
     *
     * Does not materialize the full input — only a reservoir of at most $size items is held in
     * memory, so it is safe over very large (but finite) inputs. An infinite source is invalid:
     * Algorithm R must consume the whole input.
     *
     * Returns up to $size items (fewer if the input is shorter). When $size >= count of the input,
     * the entire input is returned in original order and zero random draws occur — this differs
     * deliberately from {@see Random::sample()}, which throws \LengthException on oversize $size.
     *
     * Output keys are sequential 0-indexed.
     *
     * @param iterable<mixed>     $data
     * @param int                 $size   number of elements to draw (must be ≥ 0).
     * @param \Random\Engine|null $engine optional Random engine.
     *
     * @return array<mixed>
     *
     * @throws \InvalidArgumentException if $size is negative.
     */
    public static function reservoirSample(iterable $data, int $size, ?\Random\Engine $engine = null): array
    {
        if ($size < 0) {
            throw new \InvalidArgumentException("Sample size cannot be negative: {$size}");
        }

        if ($size === 0) {
            return [];
        }

        $randomizer = new \Random\Randomizer($engine);

        /** @var array<mixed> $reservoir */
        $reservoir = [];
        $i = 0;
        foreach ($data as $value) {
            if ($i < $size) {
                $reservoir[$i] = $value;
            } else {
                // getInt is inclusive on both ends: [0, $i] is $i + 1 values, the exact
                // $size / ($i + 1) acceptance probability Algorithm R requires (see doc block).
                // An upper bound of $i - 1 would over-accept, biasing toward later elements.
                $j = $randomizer->getInt(0, $i);
                if ($j < $size) {
                    $reservoir[$j] = $value;
                }
            }
            $i++;
        }

        return $reservoir;
    }
}

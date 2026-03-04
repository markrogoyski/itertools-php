<?php

declare(strict_types=1);

namespace IterTools;

class Random
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
        if (count($items) === 0) {
            throw new \RangeException('Array of items for choice cannot be empty');
        }

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

    /**
     * Generate a random percentage between 0 and 1
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
            yield $randomizer->getInt(0, \PHP_INT_MAX) / \PHP_INT_MAX;
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
}

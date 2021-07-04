<?php

declare(strict_types=1);

namespace IterTools;

class Single
{
    /**
     * Iterate the individual characters of a string
     *
     * @param string $string
     *
     * @return \Generator<string>
     */
    public static function string(string $string): \Generator
    {
        $characters = \mb_str_split($string);
        foreach ($characters as $character) {
            yield $character;
        }
    }

    /**
     * Repeat an item
     *
     * @param mixed $item
     * @param int   $repetitions
     *
     * @return \Generator<mixed>
     */
    public static function repeat($item, int $repetitions): \Generator
    {
        if ($repetitions < 0) {
            throw new \LogicException("Number of repetitions cannot be negative: {$repetitions}");
        }
        for ($i = $repetitions; $i > 0; $i--) {
            yield $item;
        }
    }

    /**
     * Compress an iterable by filtering out data that is not selected.
     *
     * Selectors indicates which data. True value selects item. False value filters out data.
     *
     * @param iterable<mixed> $data
     * @param iterable<bool> $selectors
     *
     * @return \Generator<mixed>
     */
    public static function compress(iterable $data, iterable $selectors): \Generator
    {
        foreach (Multi::zip($data, $selectors) as [$datum, $selector]) {
            if ($selector) {
                yield $datum;
            }
        }
    }
}

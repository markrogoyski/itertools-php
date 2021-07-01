<?php

declare(strict_types=1);

namespace IterTools;

class Infinite
{
    /**
     * Count sequentially forever
     *
     * @param int $start Starting point
     * @param int $step  How much to increment the count at each step
     *
     * @return \Generator<int>
     */
    public static function count(int $start = 1, int $step = 1): \Generator
    {
        for ($i = $start; $i < \INF; $i += $step) {
            yield $i;
        }
    }
}

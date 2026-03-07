<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

use IterTools\Util\UniqueExtractor;

class SetFixture
{
    /**
     * Returns true if multiset $a is a subset of multiset $b using strict comparison.
     *
     * Each element in $a must appear in $b at least as many times.
     */
    public static function isSubset(array $a, array $b): bool
    {
        $bCounts = [];
        foreach ($b as $item) {
            $hash = UniqueExtractor::getString($item, true);
            $bCounts[$hash] = ($bCounts[$hash] ?? 0) + 1;
        }

        foreach ($a as $item) {
            $hash = UniqueExtractor::getString($item, true);
            if (!isset($bCounts[$hash]) || $bCounts[$hash] <= 0) {
                return false;
            }
            $bCounts[$hash]--;
        }

        return true;
    }
}

<?php

declare(strict_types=1);

namespace IterTools\Tests\Fixture;

use IterTools\Multi;

class GeneratorFixture
{
    public static function getGenerator(array $values): \Generator
    {
        foreach ($values as $value) {
            yield $value;
        }
    }

    /**
     * Yield each value, appending it to $pulled as the generator advances onto it.
     *
     * Several generators may share one $pulled log to record the order values were pulled across sources.
     *
     * @param array $values
     * @param array $pulled
     */
    public static function getRecordingGenerator(array $values, array &$pulled): \Generator
    {
        foreach ($values as $value) {
            $pulled[] = $value;
            yield $value;
        }
    }

    public static function getKeyValueGenerator(array $values): \Generator
    {
        foreach ($values as $key => $value) {
            yield $key => $value;
        }
    }

    public static function getCombined(array $keys, array $values): \Generator
    {
        foreach (Multi::zip($keys, $values) as [$key, $value]) {
            yield $key => $value;
        }
    }
}

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

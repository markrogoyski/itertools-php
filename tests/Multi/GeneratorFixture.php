<?php

declare(strict_types=1);

namespace IterTools\Tests\Multi;

class GeneratorFixture
{
    public static function getGenerator(array $values): \Generator
    {
        foreach ($values as $value) {
            yield $value;
        }
    }
}

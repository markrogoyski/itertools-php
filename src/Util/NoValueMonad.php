<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 */
final class NoValueMonad
{
    private static ?self $instance = null;

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    private function __construct()
    {
    }
}

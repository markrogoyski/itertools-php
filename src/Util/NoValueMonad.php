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
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }
}

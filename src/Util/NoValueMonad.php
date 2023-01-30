<?php

namespace IterTools\Util;

/**
 * @internal
 */
class NoValueMonad
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

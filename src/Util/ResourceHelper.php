<?php

declare(strict_types=1);

namespace IterTools\Util;

class ResourceHelper
{
    /**
     * @param resource $resource
     * @return void
     */
    public static function checkIsValid($resource): void
    {
        if (!\is_resource($resource)) {
            throw new \InvalidArgumentException('Parameter is not a file resource: ' . \gettype($resource));
        }
    }
}

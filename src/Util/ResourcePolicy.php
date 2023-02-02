<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 */
class ResourcePolicy
{
    /**
     * @param resource $resource
     * @return void
     */
    public static function assertIsSatisfied($resource): void
    {
        if (!\is_resource($resource)) {
            throw new \InvalidArgumentException('Parameter is not a file resource: ' . \gettype($resource));
        }
    }
}

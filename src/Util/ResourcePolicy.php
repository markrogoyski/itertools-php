<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 */
final class ResourcePolicy
{
    /**
     * @param mixed $resource
     * @return void
     */
    public static function assertIsSatisfied(mixed $resource): void
    {
        if (!\is_resource($resource)) {
            throw new \InvalidArgumentException('Parameter is not a file resource: ' . \gettype($resource));
        }
    }
}

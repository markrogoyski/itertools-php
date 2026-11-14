<?php

declare(strict_types=1);

namespace IterTools\Util\Io;

/**
 * @internal
 */
final class ResourcePolicy
{
    /**
     * Resource types that the stream functions (fgets, fwrite, fgetcsv, ...) accept. is_resource alone also
     * passes other open resources, such as a stream context, which those functions reject with a TypeError.
     */
    private const STREAM_TYPES = ['stream', 'persistent stream'];

    /**
     * @param mixed $resource
     * @return void
     *
     * @throws \InvalidArgumentException if $resource is not an open stream resource
     */
    public static function assertIsSatisfied(mixed $resource): void
    {
        if (!\is_resource($resource)) {
            throw new \InvalidArgumentException('Parameter is not a file resource: ' . \gettype($resource));
        }

        $type = \get_resource_type($resource);
        if (!\in_array($type, self::STREAM_TYPES, true)) {
            throw new \InvalidArgumentException("Parameter is not a file resource: resource of type {$type}");
        }
    }
}

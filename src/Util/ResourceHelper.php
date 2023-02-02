<?php

declare(strict_types=1);

namespace IterTools\Util;

class ResourceHelper
{
    /**
     * @param resource $resource
     * @return bool
     */
    public static function isValid($resource): bool
    {
        return \is_resource($resource);
    }

    /**
     * @param resource $resource
     * @return void
     */
    public static function checkIsValid($resource): void
    {
        if (!self::isValid($resource)) {
            throw new \UnexpectedValueException('invalid resource');
        }
    }
}

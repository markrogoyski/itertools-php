<?php

declare(strict_types=1);

namespace IterTools\Exceptions;

class FileException extends \Exception
{
    public const CANNOT_CREATE_FILE = 1;
    public const FILE_IS_NOT_READABLE = 2;
    public const FILE_IS_NOT_WRITABLE = 3;
}

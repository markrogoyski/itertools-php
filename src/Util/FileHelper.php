<?php

declare(strict_types=1);

namespace IterTools\Util;

use IterTools\Exceptions\FileException;

class FileHelper
{
    /**
     * Opens file for writing.
     *
     * @param string $path file path
     *
     * @return resource file resource
     *
     * @throws FileException if file is not readable or if we cannot create file.
     */
    public static function openToWrite(string $path)
    {
        if (\file_exists($path) && !\is_writable($path)) {
            throw new FileException(
                "File '{$path}' is not writable",
                FileException::FILE_IS_NOT_WRITABLE
            );
        }

        $dir = \dirname($path);

        if (!\is_writable($dir)) {
            throw new FileException(
                "Cannot create file '{$path}'",
                FileException::CANNOT_CREATE_FILE
            );
        }

        /** @var resource */
        return \fopen($path, 'w');
    }
}

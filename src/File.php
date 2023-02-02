<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\ResourceHelper;

class File
{
    /**
     * Iterate the lines of a file, read in from a file handle stream resource.
     *
     * @param resource $file File handle stream opened for reading
     *
     * @return \Generator<string>
     *
     * @throws \UnexpectedValueException if file handle stream resource is invalid resource
     *
     * @see fgets()
     */
    public static function readLines($file): \Generator
    {
        ResourceHelper::checkIsValid($file);

        while (($line = \fgets($file)) !== false) {
            yield $line;
        }
    }

    /**
     * Iterate the lines of a CSV file, read in from a file handle stream resource.
     *
     * @param resource $file File handle stream opened for reading
     * @param string $separator
     * @param string $enclosure
     * @param string $escape
     *
     * @return \Generator<array<int, string|null>>
     *
     * @throws \UnexpectedValueException if invalid resource given
     *
     * @see fgetcsv()
     */
    public static function readCsv(
        $file,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ): \Generator {
        ResourceHelper::checkIsValid($file);

        // @phpstan-ignore-next-line (expects int<0, max>, null given.)
        while (($row = \fgetcsv($file, null, $separator, $enclosure, $escape)) !== false) {
            /** @var array<string|null> $row */
            yield $row;
        }
    }
}

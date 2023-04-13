<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\ResourcePolicy;

class File
{
    /**
     * Iterate the lines of a file, read in from a file handle stream resource.
     *
     * @param resource $fileResource File handle stream opened for reading
     *
     * @return \Generator<string>
     *
     * @throws \UnexpectedValueException if file handle stream resource is invalid resource
     *
     * @see fgets()
     */
    public static function readLines($fileResource): \Generator
    {
        ResourcePolicy::assertIsSatisfied($fileResource);

        while (($line = \fgets($fileResource)) !== false) {
            yield $line;
        }
    }

    /**
     * Iterate the lines of a CSV file, read in from a file handle stream resource.
     *
     * @param resource $fileResource File handle stream opened for reading
     * @param string   $separator
     * @param string   $enclosure
     * @param string   $escape
     *
     * @return \Generator<array<int, string|null>>
     *
     * @throws \UnexpectedValueException if invalid resource given
     *
     * @see fgetcsv()
     */
    public static function readCsv(
        $fileResource,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ): \Generator {
        ResourcePolicy::assertIsSatisfied($fileResource);

        /** @var int<0, max> $length */
        $length = null;

        while (($row = \fgetcsv($fileResource, $length, $separator, $enclosure, $escape)) !== false) {
            /** @var array<string|null> $row */
            yield $row;
        }
    }
}

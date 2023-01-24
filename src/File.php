<?php

declare(strict_types=1);

namespace IterTools;

class File
{
    /**
     * Reads file resource line by line.
     *
     * @param resource $fileResource
     *
     * @return \Generator<string>
     *
     * @see fgets()
     */
    public static function readByLine($fileResource): \Generator
    {
        while (($line = @\fgets($fileResource)) !== false) {
            yield $line;
        }
    }

    /**
     * Reads data from CSV file resource like fgetcsv() function.
     *
     * @param resource $fileResource
     * @param string $separator
     * @param string $enclosure
     * @param string $escape
     *
     * @return \Generator<array<int, string|null>>
     *
     * @throws \RuntimeException if read error occurred
     *
     * @see fgetcsv()
     */
    public static function readCsv(
        $fileResource,
        string $separator = ",",
        string $enclosure = "\"",
        string $escape = "\\"
    ): \Generator {
        // @phpstan-ignore-next-line (expects int<0, max>, int<0, max>|null given.)
        while (($row = @\fgetcsv($fileResource, null, $separator, $enclosure, $escape)) !== false) {
            if ($row === null) {
                throw new \RuntimeException('cannot read from file resource');
            }

            yield $row;
        }
    }
}

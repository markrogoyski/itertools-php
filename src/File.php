<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\ResourceHelper;

class File
{
    /**
     * Reads file resource line by line.
     *
     * @param resource $file
     *
     * @return \Generator<string>
     *
     * @throws \UnexpectedValueException if invalid resource given
     *
     * @see fgets()
     */
    public static function readLines($file): \Generator
    {
        ResourceHelper::checkIsValid($file);

        while (($line = \fgets($file)) !== false) {
            yield $line;
            ResourceHelper::checkIsValid($file);
        }
    }

    /**
     * Reads data from CSV file resource like fgetcsv() function.
     *
     * @param resource $file
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
        string $separator = ",",
        string $enclosure = "\"",
        string $escape = "\\"
    ): \Generator {
        ResourceHelper::checkIsValid($file);

        // @phpstan-ignore-next-line (expects int<0, max>, null given.)
        while (($row = \fgetcsv($file, null, $separator, $enclosure, $escape)) !== false) {
            /** @var array<string|null> $row */
            yield $row;
            ResourceHelper::checkIsValid($file);
        }
    }
}

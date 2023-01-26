<?php

declare(strict_types=1);

namespace IterTools;

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
        static::checkIsResourceValid($file);

        while (($line = \fgets($file)) !== false) {
            yield $line;
            static::checkIsResourceValid($file);
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
        static::checkIsResourceValid($file);

        // @phpstan-ignore-next-line (expects int<0, max>, null given.)
        while (($row = \fgetcsv($file, null, $separator, $enclosure, $escape)) !== false) {
            /** @var array<string|null> $row */
            yield $row;
            static::checkIsResourceValid($file);
        }
    }

    /**
     * @param resource $resource
     * @return void
     */
    public static function checkIsResourceValid($resource): void
    {
        if (!is_resource($resource)) {
            throw new \UnexpectedValueException('invalid resource');
        }
    }
}

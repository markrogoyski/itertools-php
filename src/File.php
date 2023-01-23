<?php

declare(strict_types=1);

namespace IterTools;

class File
{
    /**
     * Reads file resource line by line.
     *
     * @param resource $fileResource
     * @param int<0, max>|null $length
     *
     * @return \Generator
     *
     * @see fgets()
     */
    public static function readByLine($fileResource, ?int $length = null): \Generator
    {
        // @phpstan-ignore-next-line (expects int<0, max>, int<0, max>|null given.)
        while (($row = fgets($fileResource, $length)) !== false) {
            yield $row;
        }
    }

    /**
     * Reads data from CSV file resource like fgetcsv() function.
     *
     * @param resource $fileResource
     * @param int<0, max>|null $length
     * @param string $separator
     * @param string $enclosure
     * @param string $escape
     *
     * @return \Generator
     *
     * @see fgetcsv()
     */
    public static function readCsv(
        $fileResource,
        ?int $length = null,
        string $separator = ",",
        string $enclosure = "\"",
        string $escape = "\\"
    ): \Generator {
        // @phpstan-ignore-next-line (expects int<0, max>, int<0, max>|null given.)
        while (($row = fgetcsv($fileResource, $length, $separator, $enclosure, $escape)) !== false) {
            yield $row;
        }
    }

    /**
     * Writes data to the file resource.
     *
     * Data items must be stringifiable.
     *
     * Returns count of written bytes.
     *
     * @param resource $fileResource
     * @param iterable<mixed> $data
     * @param string $suffix
     * @param string $prefix
     * @param int<0, max>|null $length
     *
     * @return int
     *
     * @see fwrite()
     */
    public static function write(
        $fileResource,
        iterable $data,
        string $suffix = '',
        string $prefix = '',
        ?int $length = null
    ): int {
        $bytesWrote = 0;

        /** @var string $datum */
        foreach ($data as $datum) {
            $toWrite = "{$prefix}{$datum}{$suffix}";
            // @phpstan-ignore-next-line (expects int<0, max>, int<0, max>|null given.)
            fputs($fileResource, $toWrite, $length);
            $bytesWrote += mb_strlen($toWrite, '8bit');
        }

        return $bytesWrote;
    }
}

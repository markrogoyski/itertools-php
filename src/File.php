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
     * @return \Generator<string>
     *
     * @see fgets()
     */
    public static function readByLine($fileResource, ?int $length = null): \Generator
    {
        // @phpstan-ignore-next-line (expects int<0, max>, int<0, max>|null given.)
        while (($line = @\fgets($fileResource, $length)) !== false) {
            yield $line;
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
     * @return \Generator<array<int, string|null>>
     *
     * @throws \RuntimeException if read error occurred
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
        while (($row = @\fgetcsv($fileResource, $length, $separator, $enclosure, $escape)) !== false) {
            if ($row === null) {
                throw new \RuntimeException('cannot read from file resource');
            }

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
     * @throws \RuntimeException if write error occurred
     *
     * @see fputs()
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
            // @phpstan-ignore-next-line (expects int<0, max>, int<0, max>|null given.)
            $writeResult = @\fputs($fileResource, "{$prefix}{$datum}{$suffix}", $length);

            if ($writeResult === false) {
                throw new \RuntimeException('cannot write to file resource');
            }

            $bytesWrote += $writeResult;
        }

        return $bytesWrote;
    }
}

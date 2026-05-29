<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\ResourcePolicy;

final class File
{
    /**
     * Iterate the lines of a file, read in from a file handle stream resource.
     *
     * @param resource $fileResource File handle stream opened for reading
     *
     * @return \Generator<string>
     *
     * @throws \InvalidArgumentException if file handle stream resource is invalid resource
     *
     * @see fgets()
     */
    public static function readLines(mixed $fileResource): \Generator
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
     * @throws \InvalidArgumentException if invalid resource given
     *
     * @see fgetcsv()
     */
    public static function readCsv(
        mixed $fileResource,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ): \Generator {
        ResourcePolicy::assertIsSatisfied($fileResource);

        while (($row = \fgetcsv($fileResource, null, $separator, $enclosure, $escape)) !== false) {
            /** @var array<int, string|null> $row */
            yield $row;
        }
    }

    /**
     * Iterate the rows of a CSV file as associative arrays keyed by header.
     *
     * If $headers is null, the first row of the file is consumed and used as the header list.
     * Otherwise, the supplied $headers are used and every row of the file is treated as data.
     *
     * Headers (inferred or explicit) are validated before any data is yielded: each header must be a
     * non-empty string, and the list must be unique. A data row whose field count differs from the
     * header count throws a \RuntimeException naming the 1-based data row number. Blank lines (which
     * fgetcsv reports as a single [null] field) therefore trigger the same mismatch error rather than
     * being silently skipped. The exception is a single-column file: there a blank line cannot be
     * distinguished from an empty field, so its lone null matches the one-header count and is yielded
     * as a row with a null value rather than throwing.
     *
     * @param resource           $fileResource File handle stream opened for reading
     * @param array<string>|null $headers      (optional) explicit header list; null infers from the first row
     * @param string             $separator
     * @param string             $enclosure
     * @param string             $escape
     *
     * @return \Generator<array<string, string|null>>
     *
     * @throws \InvalidArgumentException if invalid resource given, or a header is empty, non-string, or duplicated
     * @throws \RuntimeException if a data row's field count does not match the header count
     *
     * @see fgetcsv()
     */
    public static function readCsvAssoc(
        mixed $fileResource,
        ?array $headers = null,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ): \Generator {
        ResourcePolicy::assertIsSatisfied($fileResource);

        if ($headers === null) {
            $firstRow = \fgetcsv($fileResource, null, $separator, $enclosure, $escape);
            if ($firstRow === false) {
                return;
            }
            /** @var array<int, string|null> $firstRow */
            $headers = self::validatedCsvHeaders($firstRow);
        } else {
            $headers = self::validatedCsvHeaders($headers);
        }

        $headerCount = \count($headers);
        $rowNumber = 0;

        while (($row = \fgetcsv($fileResource, null, $separator, $enclosure, $escape)) !== false) {
            /** @var array<int, string|null> $row */
            ++$rowNumber;

            if (\count($row) !== $headerCount) {
                throw new \RuntimeException(
                    "CSV data row {$rowNumber} has " . \count($row) . " fields; expected {$headerCount} to match headers"
                );
            }

            yield \array_combine($headers, $row);
        }
    }

    /**
     * Write an iterable of lines to a file handle stream resource.
     *
     * The $lineSeparator is inserted *between* lines, never after the last line (matching Stream::toFile).
     * An empty iterable writes nothing. Lines are consumed lazily and coerced to string.
     *
     * @param resource        $fileResource  File handle stream opened for writing
     * @param iterable<mixed> $lines         Stringable elements to write
     * @param string          $lineSeparator (optional) inserted between each line, typically the newline character
     *
     * @return void
     *
     * @throws \InvalidArgumentException if file handle stream resource is invalid resource
     *
     * @see fputs()
     */
    public static function writeLines(mixed $fileResource, iterable $lines, string $lineSeparator = \PHP_EOL): void
    {
        ResourcePolicy::assertIsSatisfied($fileResource);

        $firstIteration = true;

        foreach ($lines as $line) {
            /** @psalm-suppress MixedArgument */
            $line = \is_float($line) && \is_nan($line) ? 'NAN' : \strval($line); // @phpstan-ignore argument.type

            if ($firstIteration) {
                $firstIteration = false;
            } else {
                $line = $lineSeparator . $line;
            }

            \fputs($fileResource, $line);
        }
    }

    /**
     * Write an iterable of rows to a file handle stream resource as CSV.
     *
     * When $header is not null, it is written as the first row before the data rows.
     * Rows are consumed lazily. Each row must be an array of scalars.
     *
     * @param resource                   $fileResource File handle stream opened for writing
     * @param iterable<array<scalar>>    $rows         Rows to write
     * @param array<string>|null         $header       (optional) header row written before the data, labelling the columns
     * @param string                     $separator
     * @param string                     $enclosure
     * @param string                     $escape
     *
     * @return void
     *
     * @throws \InvalidArgumentException if file handle stream resource is invalid resource
     *
     * @see fputcsv()
     */
    public static function writeCsv(
        mixed $fileResource,
        iterable $rows,
        ?array $header = null,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ): void {
        ResourcePolicy::assertIsSatisfied($fileResource);

        if ($header !== null) {
            \fputcsv($fileResource, $header, $separator, $enclosure, $escape);
        }

        foreach ($rows as $row) {
            \fputcsv($fileResource, $row, $separator, $enclosure, $escape);
        }
    }

    /**
     * Validate a CSV header list and return it as a list of strings.
     *
     * Each header must be a non-empty string, and the list must be unique.
     *
     * @param array<mixed> $headers
     *
     * @return list<string>
     *
     * @throws \InvalidArgumentException if a header is empty, non-string, or duplicated
     */
    private static function validatedCsvHeaders(array $headers): array
    {
        $seen = [];
        $validated = [];

        $index = 0;
        foreach ($headers as $header) {
            if (!\is_string($header)) {
                throw new \InvalidArgumentException(
                    "CSV header at index {$index} is not a string: " . \gettype($header)
                );
            }
            if ($header === '') {
                throw new \InvalidArgumentException("CSV header is empty at index {$index}");
            }
            if (isset($seen[$header])) {
                throw new \InvalidArgumentException(
                    "Duplicate CSV header '{$header}' at indices {$seen[$header]} and {$index}"
                );
            }
            $seen[$header] = $index;
            $validated[] = $header;
            ++$index;
        }

        return $validated;
    }
}

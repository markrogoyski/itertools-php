<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * Writes to a stream resource and fails loudly on a failed or short write.
 *
 * @internal
 */
final class ResourceWriter
{
    /** @var resource|null */
    private $scratch = null;

    /**
     * @param resource $resource File handle stream opened for writing
     */
    public function __construct(private $resource)
    {
    }

    /**
     * @throws \RuntimeException if the data could not be written in full
     */
    public function write(string $data): void
    {
        $expected = \strlen($data);
        $written  = \fwrite($this->resource, $data);

        if ($written !== $expected) {
            throw self::shortWrite($written, $expected);
        }
    }

    /**
     * @param array<mixed> $row
     *
     * @throws \RuntimeException if the row could not be written in full
     */
    public function writeCsvRow(array $row, string $separator, string $enclosure, string $escape): void
    {
        $this->writeCsvRows([$row], $separator, $enclosure, $escape);
    }

    /**
     * Writes each row before it takes the next one from $rows.
     *
     * @param iterable<mixed> $rows arrays; a row that isn't one throws the \TypeError fputcsv() throws for it
     *
     * @throws \RuntimeException if a row could not be written in full
     */
    public function writeCsvRows(iterable $rows, string $separator, string $enclosure, string $escape): void
    {
        // Each row is formatted in a scratch stream first, as fputcsv() doesn't report the length of a line it
        // wrote short. The scratch stream is overwritten from the start and read only to the length written.
        // The loop is kept in here, with the write inlined, as the calls per row cost more than the formatting.
        $scratch = $this->scratch();

        foreach ($rows as $row) {
            // A row with a NAN is copied with it replaced, not changed in place: a field may be a reference, which
            // writing through would change the caller's variable, or fail for a typed float property.
            // A row that isn't an array is left to fputcsv(), which throws the TypeError, with no warning first.
            if (\is_array($row)) {
                foreach ($row as $field) {
                    if (\is_float($field) && \is_nan($field)) {
                        $row = \array_map(
                            static fn (mixed $field): mixed => \is_float($field) && \is_nan($field) ? 'NAN' : $field,
                            $row
                        );
                        break;
                    }
                }
            }

            \rewind($scratch);
            /** @var array<scalar|null> $row */
            $length = \fputcsv($scratch, $row, $separator, $enclosure, $escape);
            if ($length === false) {
                throw new \RuntimeException('Failed to format CSV row');
            }

            $written = \fwrite($this->resource, (string) \stream_get_contents($scratch, $length, 0));
            if ($written !== $length) {
                throw self::shortWrite($written, $length);
            }
        }
    }

    /**
     * The exception for a failed or short write of $expected bytes.
     */
    private static function shortWrite(int|false $written, int $expected): \RuntimeException
    {
        return new \RuntimeException(
            'Failed to write to file resource: wrote ' . \var_export($written, true) . " of {$expected} bytes"
        );
    }

    /**
     * @return resource
     */
    private function scratch()
    {
        if ($this->scratch === null) {
            $scratch = \fopen('php://memory', 'w+');
            if ($scratch === false) {
                throw new \RuntimeException('Failed to open scratch stream for CSV formatting');
            }
            $this->scratch = $scratch;
        }

        return $this->scratch;
    }

    public function __destruct()
    {
        if ($this->scratch !== null) {
            \fclose($this->scratch);
        }
    }
}

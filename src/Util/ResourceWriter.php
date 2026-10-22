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
            throw new \RuntimeException(
                'Failed to write to file resource: wrote ' . \var_export($written, true) . " of {$expected} bytes"
            );
        }
    }

    /**
     * @param array<mixed> $row
     *
     * @throws \RuntimeException if the row could not be written in full
     */
    public function writeCsvRow(array $row, string $separator, string $enclosure, string $escape): void
    {
        // A row with a NAN is copied with it replaced, not changed in place: a field may be a reference, which
        // writing through would change the caller's variable, or fail for a typed float property.
        foreach ($row as $field) {
            if (\is_float($field) && \is_nan($field)) {
                $row = \array_map(
                    static fn (mixed $field): mixed => \is_float($field) && \is_nan($field) ? 'NAN' : $field,
                    $row
                );
                break;
            }
        }

        // The row is formatted in a scratch stream first, as fputcsv() doesn't report the length of a line it
        // wrote short. The scratch stream is overwritten from the start and read only to the length written.
        $scratch = $this->scratch();
        \rewind($scratch);
        /** @var array<scalar|null> $row */
        $length = \fputcsv($scratch, $row, $separator, $enclosure, $escape);
        if ($length === false) {
            throw new \RuntimeException('Failed to format CSV row');
        }

        $this->write((string) \stream_get_contents($scratch, $length, 0));
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

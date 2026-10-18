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
        $scratch = $this->scratch();
        \ftruncate($scratch, 0);
        \rewind($scratch);

        $row = \array_map(
            static fn (mixed $field): mixed => \is_float($field) && \is_nan($field) ? 'NAN' : $field,
            $row
        );

        /** @var array<scalar|null> $row */
        \fputcsv($scratch, $row, $separator, $enclosure, $escape);
        \rewind($scratch);

        $this->write((string) \stream_get_contents($scratch));
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

# File Iteration

[Back to main README](../README.md)

Tools for iterating over file contents.

---

### Read CSV
Iterate the lines of a CSV file.

```File::readCsv(resource $fileHandle, string $separator = ',', string $enclosure = '"', string $escape = '\\')```

```php
use IterTools\File;

$fileHandle = \fopen('path/to/file.csv', 'r');

foreach (File::readCsv($fileHandle) as $row) {
    print_r($row);
}
// Each column field is an element of the array
```

### Read CSV Assoc
Iterate the rows of a CSV file as associative arrays keyed by header.

```File::readCsvAssoc(resource $fileHandle, ?array $headers = null, string $separator = ',', string $enclosure = '"', string $escape = '\\')```

If `$headers` is `null`, the first row of the file is consumed and used as the header list. Otherwise, the supplied `$headers` are used and every row is treated as data.

Headers (inferred or explicit) are validated before any data is yielded: each header must be a non-empty string, and the list must be unique. A data row whose field count differs from the header count throws a `\RuntimeException` naming the 1-based data row number (blank lines are reported by `fgetcsv` as a single field and so trigger this error rather than being silently skipped). The one exception is a single-column file: there a blank line cannot be distinguished from an empty field, so it is yielded as a row with a `null` value instead of throwing.

```php
use IterTools\File;

$fileHandle = \fopen('path/to/file.csv', 'r');

foreach (File::readCsvAssoc($fileHandle) as $row) {
    print_r($row);
}
// ['header1' => 'value1', 'header2' => 'value2', ...]
```

### Read Lines
Iterate the lines of a file.

```File::readLines(resource $fileHandle)```

```php
use IterTools\File;

$fileHandle = \fopen('path/to/file.txt', 'r');

foreach (File::readLines($fileHandle) as $line) {
    print($line);
}
```

### Write CSV
Write an iterable of rows to a file as CSV.

```File::writeCsv(resource $fileHandle, iterable $rows, ?array $header = null, string $separator = ',', string $enclosure = '"', string $escape = '\\')```

When `$header` is not `null`, it is written as the first row before the data rows. Each row must be an array of scalars.

```php
use IterTools\File;

$fileHandle = \fopen('path/to/file.csv', 'w');
$rows       = [
    ['1', 'The Phantom Menace', '1999'],
    ['2', 'Attack of the Clones', '2002'],
];

File::writeCsv($fileHandle, $rows, ['episode', 'title', 'year']);
```

### Write Lines
Write an iterable of lines to a file.

```File::writeLines(resource $fileHandle, iterable $lines, string $lineSeparator = \PHP_EOL)```

The `$lineSeparator` is inserted *between* lines, never after the last line. An empty iterable writes nothing.

```php
use IterTools\File;

$fileHandle = \fopen('path/to/file.txt', 'w');
$lines      = ['The quick', 'brown fox', 'jumps over'];

File::writeLines($fileHandle, $lines);
```

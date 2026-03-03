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

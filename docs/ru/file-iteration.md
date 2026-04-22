# Итерирование файлов

[Вернуться к главной странице](README.md)

Инструменты для итерирования по содержимому файлов.

---

### Read CSV
Итерирует коллекции ячеек CSV-файла построчно.

```File::readCsv(resource $fileHandle, string $separator = ',', string $enclosure = '"', string $escape = '\\')```

```php
use IterTools\File;
$fileHandle = \fopen('path/to/file.csv', 'r');
foreach (File::readCsv($fileHandle) as $row) {
    print_r($row);
}
// Каждое поле столбца — элемент массива
```

### Read Lines
Итерирует содержимое файла построчно.

```File::readLines(resource $fileHandle)```
```php
use IterTools\File;
$fileHandle = \fopen('path/to/file.txt', 'r');
foreach (File::readLines($fileHandle) as $line) {
    print($line);
}
```

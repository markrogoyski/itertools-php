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

### Read CSV Assoc
Итерирует строки CSV-файла как ассоциативные массивы с ключами из заголовков.

```File::readCsvAssoc(resource $fileHandle, ?array $headers = null, string $separator = ',', string $enclosure = '"', string $escape = '\\')```

Если `$headers` равен `null`, первая строка файла потребляется и используется как список заголовков. Иначе используются переданные `$headers`, а каждая строка считается данными.

Заголовки (вычисленные или переданные) проверяются до выдачи данных: каждый заголовок должен быть непустой строкой, а список — уникальным. Строка данных, число полей которой отличается от числа заголовков, выбрасывает `\RuntimeException` с указанием 1-индексированного номера строки данных (пустые строки `fgetcsv` возвращает как одно поле, поэтому они также вызывают эту ошибку, а не пропускаются молча). Единственное исключение — файл с одним столбцом: там пустую строку нельзя отличить от пустого поля, поэтому она выдаётся как строка со значением `null`, а не вызывает ошибку.

```php
use IterTools\File;
$fileHandle = \fopen('path/to/file.csv', 'r');
foreach (File::readCsvAssoc($fileHandle) as $row) {
    print_r($row);
}
// ['header1' => 'value1', 'header2' => 'value2', ...]
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

### Write CSV
Записывает коллекцию строк в файл в формате CSV.

```File::writeCsv(resource $fileHandle, iterable $rows, ?array $header = null, string $separator = ',', string $enclosure = '"', string $escape = '\\')```

Если `$header` не равен `null`, он записывается как первая строка перед строками данных. Каждая строка должна быть массивом скаляров.

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
Записывает коллекцию строк в файл.

```File::writeLines(resource $fileHandle, iterable $lines, string $lineSeparator = \PHP_EOL)```

Разделитель `$lineSeparator` вставляется *между* строками, но не после последней строки. Пустая коллекция ничего не записывает.

```php
use IterTools\File;
$fileHandle = \fopen('path/to/file.txt', 'w');
$lines      = ['The quick', 'brown fox', 'jumps over'];
File::writeLines($fileHandle, $lines);
```

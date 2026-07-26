# Математическое итерирование

[Вернуться к главной странице](README.md)

Инструменты для итерирования, связанного с математическими операциями.

---

### Frequencies
Возвращает генератор, при обходе которого ключами оказываются элементы поданной на вход последовательности,
а значениями — количества вхождений соответствующих элементов.

```Math::frequencies(iterable $data, bool $strict = true): \Generator```

По умолчанию выполняет сравнение в [режиме строгой типизации](README.md#режимы-типизации). Передайте значение `false` аргумента `$strict`, чтобы работать в режиме приведения типов.

```php
use IterTools\Math;

$grades = ['A', 'A', 'B', 'B', 'B', 'C'];

foreach (Math::frequencies($grades) as $grade => $frequency) {
    print("$grade: $frequency" . \PHP_EOL);
}
// A: 2, B: 3, C: 1
```

### Frequencies By
Возвращает распределение частот элементов, сгруппированных по значению, которое возвращает функция-ключ.

```Math::frequenciesBy(iterable $data, callable $keyFunc, bool $strict = true): \Generator```

Функция-ключ должна возвращать `int` или `string` (единственные допустимые типы ключей массива). При любом другом типе возвращаемого значения выбрасывается `\TypeError` с указанием некорректного типа. По умолчанию выполняет сравнение в [режиме строгой типизации](README.md#режимы-типизации); передайте `false` аргумента `$strict`, чтобы работать в режиме приведения типов (при котором числовая строка-ключ, например `"1"`, объединяется с целочисленным ключом `1`).

В строгом режиме такие ключи остаются отдельными группами, но ключи массивов в PHP канонизируют числовые строки — поэтому при материализации генератора (через `iterator_to_array` или терминальную операцию `Stream`, которая строит массив) группы сливаются, и остаётся только одно из двух значений счётчика. Чтобы увидеть все группы, итерируйте генератор напрямую или используйте [`Reduce::toCountBy`](reduce.md#to-count-by), который вместо этого суммирует такие ключи в один счётчик.

```php
use IterTools\Math;

$words = ['apple', 'pear', 'banana', 'kiwi'];

foreach (Math::frequenciesBy($words, fn ($word) => \strlen($word)) as $length => $frequency) {
    print("$length: $frequency" . \PHP_EOL);
}
// 5: 1, 4: 2, 6: 1
```

### Relative Frequencies
Возвращает генератор, при обходе которого ключами оказываются элементы поданной на вход последовательности,
а значениями — относительные частоты вхождений соответствующих элементов.

```Math::relativeFrequencies(iterable $data, bool $strict = true): \Generator```

По умолчанию выполняет сравнение в [режиме строгой типизации](README.md#режимы-типизации). Передайте значение `false` аргумента `$strict`, чтобы работать в режиме приведения типов.

```php
use IterTools\Math;

$grades = ['A', 'A', 'B', 'B', 'B', 'C'];

foreach (Math::relativeFrequencies($grades) as $grade => $frequency) {
    print("$grade: $frequency" . \PHP_EOL);
}
// A: 0.33, B: 0.5, C: 0.166
```

### Relative Frequencies By
Возвращает распределение относительных частот элементов, сгруппированных по значению, которое возвращает функция-ключ, нормированное к диапазону [0, 1].

```Math::relativeFrequenciesBy(iterable $data, callable $keyFunc, bool $strict = true): \Generator```

Использует тот же контракт функции-ключа (`int|string`) и семантику `$strict`, что и [`frequenciesBy`](#frequencies-by).

```php
use IterTools\Math;

$words = ['apple', 'pear', 'kiwi', 'plum'];

foreach (Math::relativeFrequenciesBy($words, fn ($word) => \strlen($word)) as $length => $frequency) {
    print("$length: $frequency" . \PHP_EOL);
}
// 5: 0.25, 4: 0.75
```

### Running Average
Накопление среднего арифметического элементов коллекции в процессе итерирования.

```Math::runningAverage(iterable $numbers, int|float $initialValue = null)```

```php
use IterTools\Math;

$grades = [100, 80, 80, 90, 85];

foreach (Math::runningAverage($grades) as $runningAverage) {
    print($runningAverage);
}
// 100, 90, 86.667, 87.5, 87
```

### Running Difference
Накопление разности элементов коллекции в процессе итерирования.

```Math::runningDifference(iterable $numbers, int|float $initialValue = null)```

```php
use IterTools\Math;

$credits = [1, 2, 3, 4, 5];

foreach (Math::runningDifference($credits) as $runningDifference) {
    print($runningDifference);
}
// -1, -3, -6, -10, -15
```
Опционально позволяет начать вычисления с заданного значения.
```php
use IterTools\Math;

$dartsScores   = [50, 50, 25, 50];
$startingScore = 501;

foreach (Math::runningDifference($dartsScores, $startingScore) as $runningScore) {
    print($runningScore);
}
// 501, 451, 401, 376, 326
```

### Running Max
Поиск максимального значения в процессе итерирования.

```Math::runningMax(iterable $numbers, int|float $initialValue = null)```

```php
use IterTools\Math;

$numbers = [1, 2, 1, 3, 5];

foreach (Math::runningMax($numbers) as $runningMax) {
    print($runningMax);
}
// 1, 2, 2, 3, 5
```

### Running Min
Поиск минимального значения в процессе итерирования.

```Math::runningMin(iterable $numbers, int|float $initialValue = null)```

```php
use IterTools\Math;

$numbers = [3, 4, 2, 5, 1];

foreach (Math::runningMin($numbers) as $runningMin) {
    print($runningMin);
}
// 3, 3, 2, 2, 1
```

### Running Product
Накопление произведения элементов коллекции в процессе итерирования.

```Math::runningProduct(iterable $numbers, int|float $initialValue = null)```

```php
use IterTools\Math;

$numbers = [1, 2, 3, 4, 5];

foreach (Math::runningProduct($numbers) as $runningProduct) {
    print($runningProduct);
}
// 1, 2, 6, 24, 120
```

Опционально позволяет начать вычисления с заданного значения.
```php
use IterTools\Math;

$numbers      = [1, 2, 3, 4, 5];
$initialValue = 5;

foreach (Math::runningProduct($numbers, $initialValue) as $runningProduct) {
    print($runningProduct);
}
// 5, 5, 10, 30, 120, 600
```

### Running Total
Накопление суммы элементов коллекции в процессе итерирования.

```Math::runningTotal(iterable $numbers, int|float $initialValue = null)```

```php
use IterTools\Math;

$prices = [1, 2, 3, 4, 5];

foreach (Math::runningTotal($prices) as $runningTotal) {
    print($runningTotal);
}
// 1, 3, 6, 10, 15
```

Опционально позволяет начать вычисления с заданного значения.
```php
use IterTools\Math;

$prices       = [1, 2, 3, 4, 5];
$initialValue = 5;

foreach (Math::runningTotal($prices, $initialValue) as $runningTotal) {
    print($runningTotal);
}
// 5, 6, 8, 11, 15, 20
```

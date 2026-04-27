# Цепочечный вызов итераторов

[Вернуться к главной странице](README.md)

Предоставляет гибкий текучий интерфейс для преобразования массивов и других итерируемых сущностей с помощью конвейера операций.

Данный функционал содержит в себе:

1. Фабричные методы для создания объекта, предоставляющего текучий интерфейс для работы с итерируемыми сущностями.
2. Методы для преобразования текущего состояние потока в новый поток.
3. Способы завершения потока преобразований:
   * Методы, преобразующие поток в скалярное значение или в структуру данных.
   ```php
   $result = Stream::of([1, 1, 2, 2, 3, 4, 5])
      ->distinct()                      // [1, 2, 3, 4, 5]
      ->map(fn ($x) => $x**2)           // [1, 4, 9, 16, 25]
      ->filterTrue(fn ($x) => $x < 10)  // [1, 4, 9]
      ->toSum();                        // 14
   ```
   * Возможность проитерировать результат потока на любом этапе с использованием цикла `foreach`.
   ```php
   $result = Stream::of([1, 1, 2, 2, 3, 4, 5])
      ->distinct()                      // [1, 2, 3, 4, 5]
      ->map(fn ($x) => $x**2)           // [1, 4, 9, 16, 25]
      ->filterTrue(fn ($x) => $x < 10); // [1, 4, 9]
   
   foreach ($result as $item) {
       // 1, 4, 9
   }
   ```

### Фабричные методы

#### Of
Создает поток из данной коллекции.

```Stream::of(iterable $iterable): Stream```

```php
use IterTools\Stream;

$iterable = [1, 2, 3];

$result = Stream::of($iterable)
    ->chainWith([4, 5, 6], [7, 8, 9])
    ->zipEqualWith([1, 2, 3, 4, 5, 6, 7, 8, 9])
    ->toValue(fn ($carry, $item) => $carry + array_sum($item));
// 90
```

#### Of Coin Flips
Создает поток из бесконечных случайных бросков монеты.

```Stream::ofCoinFlips(int $repetitions): Stream```

```php
use IterTools\Stream;

$result = Stream::ofCoinFlips(10)
    ->filterTrue()
    ->toCount();
// 5 (random)
```

#### Of CSV File
Создает поток из строк CSV-файла.

```Stream::ofCsvFile(resource $fileHandle, string $separator = ',', string $enclosure = '"', string = $escape = '\\'): Stream```

```php
use IterTools\Stream;
$fileHandle = \fopen('path/to/file.csv', 'r');
$result = Stream::of($fileHandle)
    ->toArray();
```

#### Of Empty
Создает поток из пустой коллекции.

```Stream::ofEmpty(): Stream```

```php
use IterTools\Stream;

$result = Stream::ofEmpty()
    ->chainWith([1, 2, 3])
    ->toArray();
// 1, 2, 3
```

#### Of File Lines
Создает поток из строк файла.

```Stream::ofFileLines(resource $fileHandle): Stream```
```php
use IterTools\Stream;
$fileHandle = \fopen('path/to/file.txt', 'r');
$result = Stream::of($fileHandle)
    ->map('strtoupper');
    ->toArray();
```

#### Of Random Choice
Создает поток из бесконечных случайных выборов элемента из списка.

```Stream::ofRandomChoice(array $items, int $repetitions): Stream```

```php
use IterTools\Stream;

$languages = ['PHP', 'Go', 'Python'];

$languages = Stream::ofRandomChoice($languages, 5)
    ->toArray();
// 'Go', 'PHP', 'Python', 'PHP', 'PHP' (random)
```

#### Of Random Numbers
Создает поток из бесконечного набора случайных целых чисел.

```Stream::ofRandomNumbers(int $min, int $max, int $repetitions): Stream```

```php
use IterTools\Stream;

$min  = 1;
$max  = 3;
$reps = 7;

$result = Stream::ofRandomNumbers($min, $max, $reps)
    ->toArray();
// 1, 2, 2, 1, 3, 2, 1 (random)
```

#### Of Random Percentage
Создает поток из бесконечного набора случайных вещественных чисел между 0 и 1.

```Stream::ofRandomPercentage(int $repetitions): Stream```

```php
use IterTools\Stream;

$stream = Stream::ofRandomPercentage(3)
    ->toArray();
// 0.8012566976245, 0.81237281724151, 0.61676896329459 [random]
```

#### Of Range
Создает поток для цепочечных вызовов из арифметической прогрессии.

```Stream::ofRange(int|float $start, int|float $end, int|float $step = 1): Stream```

```php
use IterTools\Stream;
$numbers = Stream::ofRange(0, 5)
    ->toArray();
// 0, 1, 2, 3, 4, 5
```

#### Of Rock Paper Scissors
Создает поток из бесконечных случайных выборов "камень-ножницы-бумага".

```Stream::ofRockPaperScissors(int $repetitions): Stream```

```php
use IterTools\Stream;

$rps = Stream::ofRockPaperScissors(5)
    ->toArray();
// 'paper', 'rock', 'rock', 'scissors', 'paper' [random]
```

### Цепочечные операции

#### Accumulate
Накапливает результат применения бинарного оператора по элементам потока.

```$stream->accumulate(callable $op, mixed ...$initial): Stream```

* Без начального значения: первый элемент результата — первый элемент коллекции без изменений, каждый следующий — `$op(аккумулятор, следующий_элемент)`.
* С начальным значением: первый элемент результата — начальное значение, каждый следующий — `$op(аккумулятор, следующий_элемент)`.
* Явный `null` является допустимым начальным значением.
* Выбрасывает `\InvalidArgumentException`, если передано более одного начального значения.

```php
use IterTools\Stream;

$numbers = [1, 2, 3, 4, 5];

$runningSums = Stream::of($numbers)
    ->accumulate(fn ($a, $b) => $a + $b)
    ->toArray();
// [1, 3, 6, 10, 15]

$withInitial = Stream::of($numbers)
    ->accumulate(fn ($a, $b) => $a + $b, 100)
    ->toArray();
// [100, 101, 103, 106, 110, 115]
```

#### ASort
Сортирует коллекцию в потоке с сохранением ключей.

```$stream->asort(callable $comparator = null)```

Если `$comparator` не передан, элементы хранимой коллекции должны быть сравнимы.

```php
use IterTools\Stream;
$worldPopulations = [
    'China'     => 1_439_323_776,
    'India'     => 1_380_004_385,
    'Indonesia' => 273_523_615,
    'USA'       => 331_002_651,
];
$result = Stream::of($worldPopulations)
    ->filter(fn ($pop) => $pop > 300_000_000)
    ->asort()
    ->toAssociativeArray();
// USA   => 331_002_651,
// India => 1_380_004_385,
// China => 1_439_323_776,
```

#### Chain With
Добавляет в конец потокового итератора другие коллекции для последовательного итерирования.

```$stream->chainWith(iterable ...$iterables): Stream```

Создает одну длинную последовательность из последовательности в потоке и нескольких данных последовательностей.

```php
use IterTools\Stream;

$input = [1, 2, 3];

$result = Stream::of($input)
    ->chainWith([4, 5, 6])
    ->chainWith([7, 8, 9])
    ->toArray();
// 1, 2, 3, 4, 5, 6, 7, 8, 9
```

#### Product With
Декартово произведение коллекции потока с другими коллекциями.

```$stream->productWith(iterable ...$iterables): Stream```

Выходные кортежи — массивы-списки (индексы с нуля, в порядке входов). Без дополнительных коллекций каждый элемент потока оборачивается в одноэлементный кортеж. Если хотя бы одна коллекция (поток или аргумент) пуста, результат пуст.

```php
use IterTools\Stream;

$numbers = [1, 2];
$letters = ['a', 'b'];

$result = Stream::of($numbers)
    ->productWith($letters)
    ->toArray();
// [[1, 'a'], [1, 'b'], [2, 'a'], [2, 'b']]
```

#### Permutations
Перестановки элементов коллекции потока.

```$stream->permutations(?int $r = null): Stream```

Выходные кортежи — массивы-списки (индексы с нуля, в порядке входа). Порядок соответствует `itertools.permutations` из Python (лексикографический по позиции): одинаковые значения различаются по позиции. `$r = 0` даёт один пустой кортеж; если `$r` больше длины потока, результат пуст; `$r = null` означает перестановки полной длины.

Выбрасывает `\InvalidArgumentException`, если `$r` отрицательное.

```php
use IterTools\Stream;

$data = [1, 2, 3];

$result = Stream::of($data)
    ->permutations(2)
    ->toArray();
// [[1, 2], [1, 3], [2, 1], [2, 3], [3, 1], [3, 2]]
```

#### Combinations
Сочетания (без повторений) элементов коллекции потока.

```$stream->combinations(int $r): Stream```

Выходные кортежи — массивы-списки (индексы с нуля, в порядке входа). Порядок соответствует `itertools.combinations` из Python (лексикографический по позиции): одинаковые значения различаются по позиции. `$r = 0` даёт один пустой кортеж; если `$r` больше длины потока, результат пуст.

Выбрасывает `\InvalidArgumentException`, если `$r` отрицательное.

```php
use IterTools\Stream;

$data = [1, 2, 3, 4];

$result = Stream::of($data)
    ->combinations(2)
    ->toArray();
// [[1, 2], [1, 3], [1, 4], [2, 3], [2, 4], [3, 4]]
```

#### Combinations With Replacement
Сочетания с повторениями элементов коллекции потока.

```$stream->combinationsWithReplacement(int $r): Stream```

Выходные кортежи — массивы-списки (индексы с нуля, в порядке входа). Порядок соответствует `itertools.combinations_with_replacement` из Python (лексикографический по позиции): одинаковые значения различаются по позиции и могут давать дублирующиеся выходные кортежи. В отличие от `combinations()`, `$r` может превышать длину потока — элементы повторяются. `$r = 0` даёт один пустой кортеж.

Выбрасывает `\InvalidArgumentException`, если `$r` отрицательное.

```php
use IterTools\Stream;

$data = [1, 2, 3];

$result = Stream::of($data)
    ->combinationsWithReplacement(2)
    ->toArray();
// [[1, 1], [1, 2], [1, 3], [2, 2], [2, 3], [3, 3]]
```

#### Powerset
Все подмножества элементов потока, упорядоченные по длине, а внутри каждой длины — по позиции во входе.

```$stream->powerset(): Stream```

Подмножества — массивы-списки (индексы с нуля, в порядке входа); ключи источника отбрасываются. Подмножества отдаются в порядке возрастания длины; внутри каждой длины порядок совпадает с `Stream::combinations` (лексикографический по позиции): дублирующиеся значения уникальны по позиции. Пустой поток даёт одно пустое подмножество.

> **Внимание:** поток из `n` элементов даёт `2**n` подмножеств — расход растёт экспоненциально.

```php
use IterTools\Stream;

$data = [1, 2, 3];

$result = Stream::of($data)
    ->powerset()
    ->toArray();
// [[], [1], [2], [3], [1, 2], [1, 3], [2, 3], [1, 2, 3]]
```

```php
use IterTools\Stream;

// Все комбинации фича-флагов для параметризованных тестов.
$result = Stream::of(['darkMode', 'beta', 'analytics'])
    ->powerset()
    ->toArray();
// [[], ['darkMode'], ['beta'], ['analytics'],
//  ['darkMode', 'beta'], ['darkMode', 'analytics'], ['beta', 'analytics'],
//  ['darkMode', 'beta', 'analytics']]
```

См. также [Combinatorics::powerset](combinatorics-iteration.md#powerset).

#### Compress
Отфильтровывает из потока элементы, которые не выбраны.

```$stream->compress(iterable $selectors): Stream```

Массив селекторов уточняет, какие элементы помещать в выборку (значение селектора `1`),
а какие исключать (значение селектора `0`).

```php
use IterTools\Stream;

$input = [1, 2, 3];

$result = Stream::of($input)
    ->compress([0, 1, 1])
    ->toArray();
// 2, 3
```

#### Compress Associative
Выбирает из хранимой коллекции элементы по заданным ключам.

```$stream->compressAssociative(array $keys): Stream```

* Ключами могут быть только строки или целые числа (по аналогии с ключами PHP-массивов).

```php
use IterTools\Stream;
$starWarsEpisodes = [
    'I'    => 'The Phantom Menace',
    'II'   => 'Attack of the Clones',
    'III'  => 'Revenge of the Sith',
    'IV'   => 'A New Hope',
    'V'    => 'The Empire Strikes Back',
    'VI'   => 'Return of the Jedi',
    'VII'  => 'The Force Awakens',
    'VIII' => 'The Last Jedi',
    'IX'   => 'The Rise of Skywalker',
];
$sequelTrilogyNumbers = ['VII', 'VIII', 'IX'];
$sequelTrilogy = Stream::of($starWarsEpisodes)
    ->compressAssociative($sequelTrilogyNumbers)
    ->toAssociativeArray();
// 'VII'  => 'The Force Awakens',
// 'VIII' => 'The Last Jedi',
// 'IX'   => 'The Rise of Skywalker',
```

#### Chunkwise
Итерирует элементы из потока с разбиением по чанкам.

```$stream->chunkwise(int $chunkSize): Stream```

Минимальный размер чанка — 1.

```php
use IterTools\Stream;

$friends = ['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey'];

$result = Stream::of($friends)
    ->chunkwise(2)
    ->toArray();
// ['Ross', 'Rachel'], ['Chandler', 'Monica'], ['Joey']
```

#### Chunkwise Overlap
Итерирует элементы из потока с разбиением по взаимонакладывающимся чанкам.

```$stream->chunkwiseOverlap(int $chunkSize, int $overlapSize, bool $includeIncompleteTail = true): Stream```

* Минимальный размер чанка — 1.
* Размер наложения должен быть меньше длины чанка.

```php
use IterTools\Stream;

$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9];

$result = Stream::of($friends)
    ->chunkwiseOverlap(3, 1)
    ->toArray()
// [1, 2, 3], [3, 4, 5], [5, 6, 7], [7, 8, 9]
```

#### Distinct
Фильтрует элементы из потока, сохраняя только уникальные значения.

```$stream->distinct(bool $strict = true): Stream```

По умолчанию выполняет сравнения в [режиме строгой типизации](README.md#режимы-типизации). Передайте значение `false` аргумента `$strict`, чтобы работать в режиме приведения типов.

```php
use IterTools\Stream;

$input = [1, 2, 1, 2, 3, 3, '1', '1', '2', '3'];
$stream = Stream::of($input)
    ->distinct()
    ->toArray();
// 1, 2, 3, '1', '2', '3'

$stream = Stream::of($input)
    ->distinct(false)
    ->toArray();
// 1, 2, 3
```

#### Distinct By
Возвращает поток, фильтрующий элементы и оставляющий только уникальные значения согласно заданной функции сравнения.

```$stream->distinctBy(callable $compareBy): Stream```

```php
use IterTools\Stream;

$streetFighterConsoleReleases = [
    ['id' => '112233', 'name' => 'Street Fighter 3 3rd Strike', 'console' => 'Dreamcast'],
    ['id' => '223344', 'name' => 'Street Fighter 3 3rd Strike', 'console' => 'PS4'],
    ['id' => '334455', 'name' => 'Street Fighter 3 3rd Strike', 'console' => 'PS5'],
    ['id' => '445566', 'name' => 'Street Fighter VI', 'console' => 'PS4'],
    ['id' => '556677', 'name' => 'Street Fighter VI', 'console' => 'PS5'],
    ['id' => '667799', 'name' => 'Street Fighter VI', 'console' => 'PC'],
];
$stream = Stream::of($streetFighterConsoleReleases)
    ->distinctBy(fn ($sfTitle) => $sfTitle['name'])
    ->toArray();
// Содержит одну запись для SF3 3rd Strike и одну для SFVI
```

#### Distinct Adjacent
Возвращает поток, удаляющий только подряд идущие дубликаты (поведение Unix `uniq`).

```$stream->distinctAdjacent(): Stream```

* Каждый элемент сравнивается строго (`===`) с предыдущим выданным элементом.
* Не подряд идущие дубликаты сохраняются.
* Работает с памятью O(1) — хранится только предыдущий элемент.
* Исходные ключи отбрасываются.

```php
use IterTools\Stream;

$result = Stream::of([1, 1, 2, 2, 3, 1, 1])
    ->distinctAdjacent()
    ->toArray();
// [1, 2, 3, 1]
```

```php
use IterTools\Stream;

$logLines = ['error: timeout', 'error: timeout', 'info: ok', 'error: timeout', 'error: timeout'];

$collapsed = Stream::of($logLines)
    ->distinctAdjacent()
    ->toArray();
// ['error: timeout', 'info: ok', 'error: timeout']
```

См. также [Set::distinctAdjacent](set-iteration.md#distinct-adjacent).

#### Distinct Adjacent By
Возвращает поток, удаляющий только подряд идущие дубликаты по ключу, используя заданную функцию ключа.

```$stream->distinctAdjacentBy(callable $keyFn): Stream```

* Извлечённый ключ каждого элемента сравнивается строго (`===`) с ключом предыдущего элемента.
* Не подряд идущие дубликаты по ключу сохраняются.
* Работает с памятью O(1) и вызывает `$keyFn` ровно один раз на элемент.
* Исходные ключи отбрасываются.

```php
use IterTools\Stream;

$words = ['apple', 'ant', 'banana', 'berry', 'apple'];

$firstLetterRuns = Stream::of($words)
    ->distinctAdjacentBy(fn ($s) => $s[0])
    ->toArray();
// ['apple', 'banana', 'apple']
```

```php
use IterTools\Stream;

$readings = [
    ['ts' => 60,  'v' => 1],
    ['ts' => 65,  'v' => 2],
    ['ts' => 119, 'v' => 3],
    ['ts' => 120, 'v' => 4],
    ['ts' => 121, 'v' => 5],
];

$compressed = Stream::of($readings)
    ->distinctAdjacentBy(fn ($r) => intdiv($r['ts'], 60))
    ->toArray();
// [['ts' => 60, 'v' => 1], ['ts' => 120, 'v' => 4]]
```

См. также [Set::distinctAdjacentBy](set-iteration.md#distinct-adjacent-by).

#### Drop While
Пропускает элементы из потока, пока предикат возвращает истину.

```$stream->dropWhile(callable $predicate): Stream```

После того как предикат впервые вернул `false`, все последующие элементы попадают в выборку.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5]

$result = Stream::of($input)
    ->dropWhile(fn ($value) => $value < 3)
    ->toArray();
// 3, 4, 5
```

#### Enumerate
Итерирует пары `[индекс, значение]`.

```$stream->enumerate(int $start = 0): Stream```

* Индекс генерируется последовательно начиная с `$start`, независимо от ключей исходной коллекции.
* Допускается отрицательное значение `$start`.

```php
use IterTools\Stream;

$seasons = ['spring', 'summer', 'autumn', 'winter'];

$result = Stream::of($seasons)
    ->enumerate()
    ->toArray();
// [[0, 'spring'], [1, 'summer'], [2, 'autumn'], [3, 'winter']]
```

#### Filter
Возвращает из потока только те элементы, для которых предикат возвращает истину.

```$stream->filter(callable $predicate): Stream```

По умолчанию (если не передан) предикат приводит элементы коллекции к `bool`.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->filter(fn ($value) => $value > 0)
    ->toArray();
// 1, 2, 3
```

#### Filter True
Возвращает из потока только истинные элементы. Истинность определяется предикатом.

```$stream->filterTrue(callable $predicate = null): Stream```

По умолчанию (если не передан) предикат приводит элементы коллекции к `bool`.

```php
use IterTools\Stream;
$input = [0, 1, 2, 3, 0, 4];
$result = Stream::of($input)
    ->filterTrue()
    ->toArray();
// 1, 2, 3, 4
```

#### Filter False
Возвращает из потока только ложные элементы. Истинность определяется предикатом.

```$stream->filterFalse(callable $predicate = null): Stream```

По умолчанию (если не передан) предикат приводит элементы коллекции к `bool`.

```php
use IterTools\Stream;

$input = [0, 1, 2, 3, 0, 4];

$result = Stream::of($input)
    ->filterFalse(fn ($value) => $value > 0)
    ->filterFalse()
    ->toArray();
// 0, 0
```

#### Filter Keys
Возвращает из потока только те элементы, для ключей которых предикат возвращает истину.

```$stream->filterKeys(callable $filter): Stream```

```php
$olympics = [
    2000 => 'Sydney',
    2002 => 'Salt Lake City',
    2004 => 'Athens',
    2006 => 'Turin',
    2008 => 'Beijing',
    2010 => 'Vancouver',
    2012 => 'London',
    2014 => 'Sochi',
    2016 => 'Rio de Janeiro',
    2018 => 'Pyeongchang',
    2020 => 'Tokyo',
    2022 => 'Beijing',
];
$winterFilter = fn ($year) => $year % 4 === 2;
$result = Stream::of($olympics)
    ->filterKeys($winterFilter)
    ->toAssociativeArray();
}
// 2002 => Salt Lake City
// 2006 => Turin
// 2010 => Vancouver
// 2014 => Sochi
// 2018 => Pyeongchang
// 2022 => Beijing
```

#### Flat Map
Отображение коллекции из потока с уплощением результата на 1 уровень вложенности.

```$stream->flatMap(callable $mapper): Stream```

```php
$data    = [1, 2, 3, 4, 5];
$mapper  fn ($item) => ($item % 2 === 0) ? [$item, $item] : $item;
$result = Stream::of($data)
    ->flatMap($mapper)
    ->toArray();
// [1, 2, 2, 3, 4, 4, 5]
```

#### Flatten
Многоуровневое уплощение коллекции из потока.

```$stream->flatten(int $dimensions = 1): Stream```

```php
$data = [1, [2, 3], [4, 5]];
$result = Stream::of($data)
    ->flatten($mapper)
    ->toArray();
// [1, 2, 3, 4, 5]
```

#### Frequencies
Абсолютная частота распределения элементов потока.

```$stream->frequencies(bool $strict = true): Stream```

```php
use IterTools\Stream;

$grades = ['A', 'A', 'B', 'B', 'B', 'C'];

$result = Stream::of($grades)
    ->frequencies()
    ->toAssociativeArray();

// ['A' => 2, 'B' => 3, 'C' => 1]
```

#### Group By
Группирует элементы из потока по заданному правилу.

```$stream->groupBy(callable $groupKeyFunction): Stream```

Функция `$groupKeyFunction` должна возвращать общий ключ для элементов группы.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->groupBy(fn ($item) => $item > 0 ? 'positive' : 'negative');

foreach ($result as $group => $item) {
    // 'positive' => [1, 2, 3], 'negative' => [-1, -2, -3]
}
```

#### Infinite Cycle
Бесконечно зацикливает перебор элементов потока.

```$stream->infiniteCycle(): Stream```

```php
use IterTools\Stream;

$input = [1, 2, 3];

$result = Stream::of($input)
    ->infiniteCycle()
    ->print();
// 1, 2, 3, 1, 2, 3, ...
```

#### Intersection With
Пересечение хранимой в потоке коллекции с другими переданными коллекциями.

```$stream->intersectionWith(iterable ...$iterables): Stream```

```php
use IterTools\Stream;

$numbers    = [1, 2, 3, 4, 5, 6, 7, 8, 9];
$numerics   = ['1', '2', 3, 4, 5, 6, 7, '8', '9'];
$oddNumbers = [1, 3, 5, 7, 9, 11];

$stream = Stream::of($numbers)
    ->intersectionWith($numerics, $oddNumbers)
    ->toArray();
// 3, 5, 7
```

#### Intersection Coercive With
Пересечение хранимой в потоке коллекции с другими переданными коллекциями в режиме [приведения типов](README.md#режимы-типизации).

```$stream->intersectionCoerciveWith(iterable ...$iterables): Stream```

```php
use IterTools\Stream;

$languages          = ['php', 'python', 'c++', 'java', 'c#', 'javascript', 'typescript'];
$scriptLanguages    = ['php', 'python', 'javascript', 'typescript'];
$supportsInterfaces = ['php', 'java', 'c#', 'typescript'];

$stream = Stream::of($languages)
    ->intersectionCoerciveWith($scriptLanguages, $supportsInterfaces)
    ->toArray();
// 'php', 'typescript'
```

#### Intersperse
Возвращает поток, в котором между последовательными элементами вставлен разделитель.

```$stream->intersperse(mixed $separator): Stream```

* Разделитель не выдаётся ни перед первым элементом, ни после последнего.
* Разделитель выдаётся как есть: массивы не разворачиваются, объекты сохраняют идентичность.
* Ключи исходной коллекции отбрасываются — на выходе список с последовательными целочисленными ключами.

```php
use IterTools\Stream;

$flow = '';
foreach (Stream::of(['fetch', 'parse', 'validate', 'persist'])->intersperse(' -> ') as $part) {
    $flow .= $part;
}
// 'fetch -> parse -> validate -> persist'
```

```php
use IterTools\Stream;

$row = '';
foreach (Stream::of(['name', 'email', 'role'])->intersperse(',') as $part) {
    $row .= $part;
}
// 'name,email,role'
```

См. также [Single::intersperse](single-iteration.md#intersperse).

#### Limit
Ограничивает итерирование элементов из потока заданным максимальным числом итераций.

Останавливает процесс итерирования, когда число итераций достигает `$limit`.

```$stream->limit(int $limit): Stream```

```php
Use IterTools\Single;

$matrixMovies = ['The Matrix', 'The Matrix Reloaded', 'The Matrix Revolutions', 'The Matrix Resurrections'];
$limit        = 1;

$goodMovies = Stream::of($matrixMovies)
    ->limit($limit)
    ->toArray();
// 'The Matrix' (and nothing else)
```

#### Map
Отображение хранимой в потоке коллекции с использованием callback-функции.

```$stream->map(callable $function): Stream```

```php
use IterTools\Stream;

$grades = [100, 95, 98, 89, 100];

$result = Stream::of($grades)
    ->map(fn ($grade) => $grade === 100 ? 'A' : 'F')
    ->toArray();
// A, F, F, F, A
```

#### Map Spread
Возвращает поток, где к каждому элементу применяется функция, при этом элемент распаковывается как позиционные аргументы функции.

```$stream->mapSpread(callable $function): Stream```

* Каждый элемент потока сам должен быть итерируемым; его значения передаются в `$function` позиционно через splat-оператор.
* Внутренние ключи отбрасываются — значения передаются позиционно, даже если внутренний элемент является ассоциативным массивом.
* Внешние ключи сохраняются (как и в `Stream::map`).
* Бросает `\InvalidArgumentException`, если какой-либо внутренний элемент не является итерируемым.

```php
use IterTools\Stream;

$result = Stream::of([[1, 2], [3, 4], [5, 6]])
    ->mapSpread(fn ($a, $b) => $a + $b)
    ->toArray();
// [3, 7, 11]
```

```php
use IterTools\Stream;

$names  = ['Alice', 'Bob', 'Carol'];
$scores = [92, 87, 95];

$result = Stream::of($names)
    ->zipWith($scores)
    ->mapSpread(fn (string $name, int $score) => "{$name}: {$score}")
    ->toArray();
// ['Alice: 92', 'Bob: 87', 'Carol: 95']
```

См. также [Single::mapSpread](single-iteration.md#map-spread).

#### Pairwise
Итерирует элементы из потока попарно (с наложением).

```$stream->pairwise(): Stream```

Итоговый поток окажется пустым, если исходный содержит меньше 2-х элементов.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$stream = Stream::of($input)
    ->pairwise()
    ->toArray();
// [1, 2], [2, 3], [3, 4], [4, 5]
```

#### Partial Intersection With
Частичное пересечение хранимой в потоке коллекции с другими переданными коллекциями.

```$stream->partialIntersectionWith(int $minIntersectionCount, iterable ...$iterables): Stream```

```php
use IterTools\Stream;

$numbers    = [1, 2, 3, 4, 5, 6, 7, 8, 9];
$numerics   = ['1', '2', 3, 4, 5, 6, 7, '8', '9'];
$oddNumbers = [1, 3, 5, 7, 9, 11];

$stream = Stream::of($numbers)
    ->partialIntersectionWith($numerics, $oddNumbers)
    ->toArray();
// 1, 3, 4, 5, 6, 7, 9
```

#### Partial Intersection Coercive With
Частичное пересечение хранимой в потоке коллекции с другими переданными коллекциями, вычисляемое в режиме [приведения типов](README.md#режимы-типизации).

```$stream->partialIntersectionCoerciveWith(int $minIntersectionCount, iterable ...$iterables): Stream```

```php
use IterTools\Stream;

$languages          = ['php', 'python', 'c++', 'java', 'c#', 'javascript', 'typescript'];
$scriptLanguages    = ['php', 'python', 'javascript', 'typescript'];
$supportsInterfaces = ['php', 'java', 'c#', 'typescript'];

$stream = Stream::of($languages)
    ->partialIntersectionCoerciveWith(2, $scriptLanguages, $supportsInterfaces)
    ->toArray();
// 'php', 'python', 'java', 'typescript', 'c#', 'javascript'
```

#### Reindex
Переиндексирует key-value коллекцию из потока, используя функцию-индексатор.

```$stream->reindex(callable $indexer): Stream```

```php
use IterTools\Stream;
$data = [
    [
        'title'   => 'Star Wars: Episode IV – A New Hope',
        'episode' => 'IV',
        'year'    => 1977,
    ],
    [
        'title'   => 'Star Wars: Episode V – The Empire Strikes Back',
        'episode' => 'V',
        'year'    => 1980,
    ],
    [
        'title' => 'Star Wars: Episode VI – Return of the Jedi',
        'episode' => 'VI',
        'year' => 1983,
    ],
];
$reindexFunc = fn (array $swFilm) => $swFilm['episode'];
$reindexResult = Stream::of($data)
    ->reindex($reindexFunc)
    ->toAssociativeArray();
// [
//     'IV' => [
//         'title'   => 'Star Wars: Episode IV – A New Hope',
//         'episode' => 'IV',
//         'year'    => 1977,
//     ],
//     'V' => [
//         'title'   => 'Star Wars: Episode V – The Empire Strikes Back',
//         'episode' => 'V',
//         'year'    => 1980,
//     ],
//     'VI' => [
//         'title' => 'Star Wars: Episode VI – Return of the Jedi',
//         'episode' => 'VI',
//         'year' => 1983,
//     ],
// ]
```

#### Relative Frequencies
Относительная частота распределения элементов потока.

```$stream->relativeFrequencies(bool $strict = true): Stream```

```php
use IterTools\Stream;

$grades = ['A', 'A', 'B', 'B', 'B', 'C'];

$result = Stream::of($grades)
    ->relativeFrequencies()
    ->toAssociativeArray();

// A => 0.33, B => 0.5, C => 0.166
```

#### Reverse
Итерирует коллекцию из потока в обратном порядке.

```$stream->reverse(): Stream```

```php
use IterTools\Stream;
$words = ['are', 'you', 'as', 'bored', 'as', 'I', 'am'];
$reversed = Stream::of($words)
    ->reverse()
    ->toString(' ');
// am I as bored as you are
```

#### Round Robin With
Поочерёдно отдаёт элементы из потока и заданных коллекций, чередуя источники.

```$stream->roundRobinWith(iterable ...$iterables): Stream```

В каждом раунде берётся по одному элементу из каждого источника, в котором ещё есть значения;
исчерпавшийся источник пропускается на последующих раундах. Итерирование завершается, когда
исчерпаны все источники. В отличие от `zipWith`, элементы возвращаются по одному, а не в виде кортежей.
Ключи источников отбрасываются; результат имеет последовательные целочисленные ключи.

```php
use IterTools\Stream;

$result = Stream::of(['A', 'B', 'C'])
    ->roundRobinWith(['D', 'E'], ['F', 'G', 'H'])
    ->toArray();
// ['A', 'D', 'F', 'B', 'E', 'G', 'C', 'H']
```

Round-robin-планирование позволяет равномерно вычерпывать задачи из очередей разной длины:
```php
$workerOne   = ['task-1', 'task-4', 'task-7'];
$workerTwo   = ['task-2', 'task-5'];
$workerThree = ['task-3', 'task-6', 'task-8', 'task-9'];

$schedule = Stream::of($workerOne)
    ->roundRobinWith($workerTwo, $workerThree)
    ->toArray();
// ['task-1', 'task-2', 'task-3', 'task-4', 'task-5', 'task-6', 'task-7', 'task-8', 'task-9']
```

См. также [Multi::roundRobin](multi-iteration.md#roundrobin).

#### Running Average
Накапливает среднее арифметическое элементов из потока в процессе итерирования.

```$stream->runningAverage(int|float|null $initialValue = null): Stream```

```php
use IterTools\Stream;

$input = [1, 3, 5];

$result = Stream::of($input)
    ->runningAverage();

foreach ($result as $item) {
    // 1, 2, 3
}
```

#### Running Difference
Накапливает разность элементов из потока в процессе итерирования.

```$stream->runningDifference(int|float|null $initialValue = null): Stream```

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->runningDifference()
    ->toArray();
// -1, -3, -6, -10, -15
```

#### Running Max
Возвращает поток, ищущий максимальный элемент из исходного потока в процессе итерирования.

```$stream->runningMax(int|float|null $initialValue = null): Stream```

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->runningMax()
    ->toArray();
// 1, 1, 2, 2, 3, 3
```

#### Running Min
Возвращает поток, ищущий минимальный элемент из исходного потока в процессе итерирования.

```$stream->runningMin(int|float|null $initialValue = null): Stream```

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->runningMin()
    ->toArray();
// 1, -1, -1, -2, -2, -3
```

#### Running Product
Возвращает поток, накапливающий произведение элементов из исходного потока в процессе итерирования.

```$stream->runningProduct(int|float|null $initialValue = null): Stream```

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->runningProduct()
    ->toArray();
// 1, 2, 6, 24, 120
```

#### Running Total
Возвращает поток, накапливающий сумму элементов из исходного потока в процессе итерирования.

```$stream->runningTotal(int|float|null $initialValue = null): Stream```

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->runningTotal()
    ->toArray();
// 1, 3, 6, 10, 15
```

#### Skip
Пропускает n элементов из потока и опциональным смещением.

```$stream->skip(int $count, int $offset = 0): Stream```

```php
use IterTools\Stream;

$movies = [
    'The Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith',
    'A New Hope', 'The Empire Strikes Back', 'Return of the Jedi',
    'The Force Awakens', 'The Last Jedi', 'The Rise of Skywalker'
];

$onlyTheBest = Stream::of($movies)
    ->skip(3)
    ->skip(3, 3)
    ->toArray();
// 'A New Hope', 'The Empire Strikes Back', 'Return of the Jedi'
```

#### Slice
Выделяет подвыборку коллекции из потока.

```$stream->slice(int $start = 0, int $count = null, int $step = 1)```

```php
use IterTools\Stream;
$olympics = [1992, 1994, 1996, 1998, 2000, 2002, 2004, 2006, 2008, 2010, 2012, 2014, 2016, 2018, 2020, 2022];
$summerOlympics = Stream::of($olympics)
    ->slice(0, 8, 2)
    ->toArray();
// [1992, 1996, 2000, 2004, 2008, 2012, 2016, 2020]
```

#### Sort
Сортирует хранимую в потоке коллекцию.

```$stream->sort(callable $comparator = null)```

Если `$comparator` не передан, элементы хранимой коллекции должны быть сравнимы.

```php
use IterTools\Stream;

$input = [3, 4, 5, 9, 8, 7, 1, 6, 2];

$result = Stream::of($input)
    ->sort()
    ->toArray();
// 1, 2, 3, 4, 5, 6, 7, 8, 9
```

#### Difference With
Возвращает поток, содержащий разность исходного потока с заданным набором коллекций. Элементы из исходного потока, не входящие ни в одну из заданных коллекций.

```$stream->differenceWith(iterable ...$iterables): Stream```

Если хотя бы в одной коллекции или в потоке встречаются повторяющиеся элементы, работают правила получения разности для [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Stream;

$a = [1, 2, 3, 4, 7];
$b = [2, 3, 5, 8];
$c = [1, 6, 9];

$stream = Stream::of($a)
    ->differenceWith($b, $c)
    ->toArray();
// 4, 7
```

#### Difference Coercive With
Возвращает поток, содержащий разность исходного потока с заданным набором коллекций, полученную в режиме [приведения типов](README.md#режимы-типизации).

```$stream->differenceCoerciveWith(iterable ...$iterables): Stream```

Если хотя бы в одной коллекции или в потоке встречаются повторяющиеся элементы, работают правила получения разности для [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Stream;

$a = [1, 2, 3, 4, 7];
$b = ['1', 2, 3, 5, 8];

$stream = Stream::of($a)
    ->differenceCoerciveWith($b)
    ->toArray();
// 4, 7
```

#### Symmetric difference With
Возвращает поток, содержащий симметрическую разность исходного потока с заданным набором коллекций.

```$stream->symmetricDifferenceWith(iterable ...$iterables): Stream```

Если хотя бы в одной коллекции или в потоке встречаются повторяющиеся элементы, работают правила получения разности для [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Stream;

$a = [1, 2, 3, 4, 7];
$b = ['1', 2, 3, 5, 8];
$c = [1, 2, 3, 6, 9];

$stream = Stream::of($a)
    ->symmetricDifferenceWith($b, $c)
    ->toArray();
// '1', 4, 5, 6, 7, 8, 9
```

#### Symmetric difference Coercive With
Возвращает поток, содержащий симметрическую разность исходного потока с заданным набором коллекций, полученную в режиме [приведения типов](README.md#режимы-типизации).

```$stream->symmetricDifferenceCoerciveWith(iterable ...$iterables): Stream```

Если хотя бы в одной коллекции или в потоке встречаются повторяющиеся элементы, работают правила получения разности для [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Stream;

$a = [1, 2, 3, 4, 7];
$b = ['1', 2, 3, 5, 8];
$c = [1, 2, 3, 6, 9];

$stream = Stream::of($a)
    ->symmetricDifferenceCoerciveWith($b, $c)
    ->toArray();
// 4, 5, 6, 7, 8, 9
```

#### Take While
Оставляет элементы в потоке, пока предикат возвращает истину.

```$stream->takeWhile(callable $predicate): Stream```

* Останавливает процесс итерации, как только предикат впервые вернет ложь.
* По умолчанию (если не передан) предикат приводит элементы коллекции к `bool`.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->takeWhile(fn ($value) => abs($value) < 3);

foreach ($result as $item) {
    // 1, -1, 2, -2
}
```

#### Union With
Возвращает поток с объединением хранимой коллекции с другими поданными на вход коллекциями.

```$stream->unionWith(iterable ...$iterables): Stream```

Если хотя бы в одной коллекции встречаются повторяющиеся элементы, работают правила объединения [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Stream;

$input = [1, 2, 3];

$stream = Stream::of($input)
    ->unionWith([3, 4, 5, 6])
    ->toArray();
// [1, 2, 3, 4, 5, 6]
```

#### Union Coercive With
Возвращает поток с объединением хранимой коллекции с другими поданными на вход коллекциями в режиме [приведения типов](README.md#режимы-типизации).

```$stream->unionCoerciveWith(iterable ...$iterables): Stream```

Если хотя бы в одной коллекции встречаются повторяющиеся элементы, работают правила объединения [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Stream;

$input = [1, 2, 3];

$stream = Stream::of($input)
    ->unionCoerciveWith(['3', 4, 5, 6])
    ->toArray();
// [1, 2, 3, 4, 5, 6]
```

#### Zip
Рассматривает сам поток как последовательность итерируемых коллекций и транспонирует их по столбцам.

```$stream->zip(): Stream```

Для коллекций разной длины итерирование останавливается, когда самая короткая строка закончится. Аналогично идиоме Python `zip(*rows)`.

```php
use IterTools\Stream;

$rows = [[1, 2, 3], [4, 5, 6], [7, 8, 9]];

$stream = Stream::of($rows)
    ->zip()
    ->toArray();
// [[1, 4, 7], [2, 5, 8], [3, 6, 9]]
```

Хорошо сочетается с `chunkwise()` — разбейте плоский поток на группы и транспонируйте:

```php
use IterTools\Stream;

$stream = Stream::of([1, 2, 3, 4, 5, 6])
    ->chunkwise(3)
    ->zip()
    ->toArray();
// [[1, 4], [2, 5], [3, 6]]
```

Естественно сочетается с `toPartition()` — составьте пары турнирной сетки, соединив верхнюю половину с перевёрнутой нижней (1 vs 8, 2 vs 7, 3 vs 6, 4 vs 5):

```php
use IterTools\Stream;

[$topHalf, $bottomHalf] = Stream::of([1, 2, 3, 4, 5, 6, 7, 8])
    ->toPartition(fn (int $seed): bool => $seed <= 4);

$matchups = Stream::of([$topHalf, array_reverse($bottomHalf)])
    ->zip()
    ->toArray();
// [[1, 8], [2, 7], [3, 6], [4, 5]]
```

Транспонирование таблицы «строки → столбцы» — когда записи приходят построчно, но нужно получить каждое поле отдельной серией:

```php
use IterTools\Stream;

$rows = [
    ['Alice', 30, 'NYC'],
    ['Bob',   25, 'LA'],
    ['Carol', 41, 'Austin'],
];

$columns = Stream::of($rows)
    ->zip()
    ->toArray();
// [['Alice', 'Bob', 'Carol'], [30, 25, 41], ['NYC', 'LA', 'Austin']]
```

Внешний поток должен быть конечным; он полностью потребляется при итерировании результата, до того как будет выдан первый кортеж. Внутренние строки после этого продвигаются лениво. Передача одного и того же экземпляра итератора более одного раза не поддерживается и может привести к неожиданному поведению.

#### Zip Longest
Рассматривает сам поток как последовательность итерируемых коллекций и транспонирует их по столбцам, продолжая до самой длинной строки.

```$stream->zipLongest(): Stream```

Для закончившихся строк выдаёт `null` на оставшихся итерациях. Аналогично идиоме Python `zip_longest(*rows)`.

```php
use IterTools\Stream;

$rows = [[1, 2, 3], [4, 5]];

$stream = Stream::of($rows)
    ->zipLongest()
    ->toArray();
// [[1, 4], [2, 5], [3, null]]
```

Сгруппировать помесячные показатели по годам при разной длине серий — короткие годы проявляются как `null`-пропуски вместо отбрасывания данных:

```php
use IterTools\Stream;

$rainfallByYear = [
    [3.2, 4.1, 5.0, 6.2],           // 2022
    [2.8, 3.9, 4.7],                // 2023 — сбой датчика в середине года
    [3.5, 4.3, 5.2, 6.8, 7.1],      // 2024
];

$byMonth = Stream::of($rainfallByYear)
    ->zipLongest()
    ->toArray();
// [[3.2, 2.8, 3.5], [4.1, 3.9, 4.3], [5.0, 4.7, 5.2], [6.2, null, 6.8], [null, null, 7.1]]
```

Внешний поток должен быть конечным; он полностью потребляется при итерировании результата, до того как будет выдан первый кортеж. Внутренние строки после этого продвигаются лениво. Передача одного и того же экземпляра итератора более одного раза не поддерживается и может привести к неожиданному поведению.

#### Zip Filled
Рассматривает сам поток как последовательность итерируемых коллекций и транспонирует их по столбцам, продолжая до самой длинной строки и подставляя филлер для отсутствующих значений.

```$stream->zipFilled(mixed $filler): Stream```

Для закончившихся строк выдаёт значение `$filler` на оставшихся итерациях.

```php
use IterTools\Stream;

$rows = [[1, 2, 3], [4, 5]];

$stream = Stream::of($rows)
    ->zipFilled('?')
    ->toArray();
// [[1, 4], [2, 5], [3, '?']]
```

Полезно, когда потребителю нужен числовой дефолт вместо `null` — например, квартальные продажи по командам, где недостающие кварталы должны считаться нулями при агрегации:

```php
use IterTools\Stream;

$salesByTeam = [
    [120, 150, 180, 210],   // Команда A — полный год
    [ 95, 110],             // Команда B — подключена во втором полугодии
    [140, 160, 175],        // Команда C — Q4 ещё не закрыт
];

$byQuarter = Stream::of($salesByTeam)
    ->zipFilled(0)
    ->toArray();
// [[120, 95, 140], [150, 110, 160], [180, 0, 175], [210, 0, 0]]
```

Внешний поток должен быть конечным; он полностью потребляется при итерировании результата, до того как будет выдан первый кортеж. Внутренние строки после этого продвигаются лениво. Передача одного и того же экземпляра итератора более одного раза не поддерживается и может привести к неожиданному поведению.

#### Zip Equal
Рассматривает сам поток как последовательность итерируемых коллекций равной длины и транспонирует их по столбцам.

```$stream->zipEqual(): Stream```

Работает как `Stream::zip()`, но бросает `\LengthException`, если длины строк не равны (по крайней мере одна строка закончится раньше других). Используется, когда равные длины являются обязательным инвариантом.

```php
use IterTools\Stream;

$rows = [[1, 2, 3], [4, 5, 6], [7, 8, 9]];

$stream = Stream::of($rows)
    ->zipEqual()
    ->toArray();
// [[1, 4, 7], [2, 5, 8], [3, 6, 9]]
```

Естественно подходит для CSV-подобных записей, где у каждой строки должно быть одинаковое число полей — транспонирование в столбцы обнаружит нарушение схемы как `\LengthException` вместо молчаливого усечения или дополнения:

```php
use IterTools\Stream;

$records = [
    ['id', 'name', 'email'],
    [1,    'Alice', 'alice@example.com'],
    [2,    'Bob',   'bob@example.com'],
];

$byField = Stream::of($records)
    ->zipEqual()
    ->toArray();
// [['id', 1, 2], ['name', 'Alice', 'Bob'], ['email', 'alice@example.com', 'bob@example.com']]
```

Внешний поток должен быть конечным; он полностью потребляется при итерировании результата, до того как будет выдан первый кортеж. Внутренние строки после этого продвигаются лениво. Передача одного и того же экземпляра итератора более одного раза не поддерживается и может привести к неожиданному поведению.

#### Zip With
Параллельно итерирует элементы из потока вместе с элементами переданных коллекций, пока не закончится самый короткий итератор.

```$stream->zipWith(iterable ...$iterables): Stream```

* Создает итератор, который агрегирует данные из нескольких итераторов.
* Работает аналогично функции `zip()` в Python.
* Для коллекций разной длины продолжает процесс итерирования до момента, пока самая короткая коллекция не закончится.

```php
use IterTools\Stream;

$input = [1, 2, 3];

$stream = Stream::of($input)
    ->zipWith([4, 5, 6])
    ->zipWith([7, 8, 9])
    ->toArray();
// [1, 4, 7], [2, 5, 8], [3, 6, 9]
```

#### Zip Filled With
Параллельно итерирует элементы из потока вместе с элементами переданных коллекций, пока не закончится самый длинный итератор.

Для закончившихся итераторов подставляет заданный филлер в кортеж значений итерации.

```$stream->zipFilledWith(mixed $default, iterable ...$iterables): Stream```

```php
use IterTools\Stream;

$input = [1, 2, 3];

$stream = Stream::of($input)
    ->zipFilledWith('?', ['A', 'B']);

foreach ($stream as $zipped) {
    // [1, A], [2, B], [3, ?]
}
```

#### Zip Longest With
Параллельно итерирует элементы из потока вместе с элементами переданных коллекций, пока не закончится самый длинный итератор.

```$stream->zipLongestWith(iterable ...$iterables): Stream```

* Создает итератор, который агрегирует данные из нескольких итераторов.
* Работает аналогично функции `zip_longest()` в Python.
* Для коллекций разной длины продолжает процесс итерирования до момента, пока самая длинная коллекция не закончится.
* Для коллекций разной длины отдает вместо элементов `null` для коллекций, которые закончились.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->zipLongestWith([4, 5, 6])
    ->zipLongestWith([7, 8, 9, 10]);

foreach ($result as $item) {
    // [1, 4, 7], [2, 5, 8], [3, 6, 9], [4, null, 10], [null, null, 5]
}
```

#### Zip Equal With
Параллельно итерирует элементы из потока вместе с элементами переданных коллекций (все коллекции должны быть одной длины).

```$stream->zipEqualWith(iterable ...$iterables): Stream```

Работает как `Multi::zip()`, но бросает `\LengthException`, когда выясняется, что длины коллекций разные
(когда закончился самая короткая коллекция).

```php
use IterTools\Stream;

$input = [1, 2, 3];

$result = Stream::of($input)
    ->zipEqualWith([4, 5, 6])
    ->zipEqualWith([7, 8, 9]);

foreach ($result as $item) {
    // [1, 4, 7], [2, 5, 8], [3, 6, 9]
}
```

### Завершающие операции

#### Саммари о потоке
##### All Match
Возвращает истину, если для всех элементов из потока предикат возвращает истину.

```$stream->allMatch(callable $predicate): bool```

```php
use IterTools\Summary;

$finalFantasyNumbers = [4, 5, 6];
$isOnSuperNintendo   = fn ($ff) => $ff >= 4 && $ff <= 6;

$boolean = Stream::of($finalFantasyNumbers)
    ->allMatch($isOnSuperNintendo);
// true
```

##### All Unique
Возвращает истину, все элементы коллекции потока уникальны.

```$stream->allUnique(bool $strict = true): bool```

По умолчанию работает в [режиме строгой типизации](README.md#режимы-типизации). Установите параметр `$strict` в `false` для работы в режиме приведения типов.

```php
use IterTools\Summary;

$items = ['fingerprints', 'snowflakes', 'eyes', 'DNA']

$boolean = Stream::of($items)
    ->allUnique();
// true
```

##### Any Match
Возвращает истину, если хотя бы для одного элемента из потока предикат возвращает истину.

```$stream->anyMatch(callable $predicate): bool```

```php
use IterTools\Summary;

$answers          = ['fish', 'towel', 42, "don't panic"];
$isUltimateAnswer = fn ($a) => a == 42;

$boolean = Stream::of($answers)
    ->anyMatch($answers, $isUltimateAnswer);
// true
```

##### Are Permutations With
Возвращает истину, если коллекция из потока и переданные коллекции являются перестановками друг друга.

```$stream->arePermutationsWith(...$iterables): bool```
```php
use IterTools\Summary;
$rite = ['r', 'i', 't', 'e'];
$reit = ['r', 'e', 'i', 't'];
$tier = ['t', 'i', 'e', 'r'];
$tire = ['t', 'i', 'r', 'e'];
$trie = ['t', 'r', 'i', 'e'];
$boolean = Stream::of(['i', 't', 'e', 'r'])
    ->arePermutationsWith($rite, $reit, $tier, $tire, $trie);
// true
```

##### Are Permutations Coercive With
Возвращает истину, если коллекция из потока и переданные коллекции являются перестановками друг друга
(в режиме [приведения типов](README.md#режимы-типизации)).

```$stream->arePermutationsCoerciveWith(...$iterables): bool```

```php
use IterTools\Summary;
$set2 = [2.0, '1', 3];
$set3 = [3, 2, 1];
$boolean = Stream::of([1, 2.0, '3'])
    ->arePermutationsCoerciveWith($set2, $set3);
// true
```

##### Contains
Возвращает истину, если поток содержит искомое значение, при [строгом сравнении типов](README.md#режимы-типизации).

```$stream->contains(mixed $needle): bool```

- Скаляры сравниваются строго по типу (`1` не равно `'1'`; `0` не равно `false`).
- Для объектов совпадает только один и тот же экземпляр.
- Массивы сравниваются через `===`.
- `NaN` никогда не совпадает с `NaN`.
- Обрывает итерацию при первом совпадении.

```php
use IterTools\Stream;

$primes = [2, 3, 5, 7, 11, 13];

$boolean = Stream::of($primes)->contains(7);
// true

$boolean = Stream::of($primes)->contains('7');
// false (строгое сравнение)
```

##### Contains Coercive
Возвращает истину, если поток содержит искомое значение, в режиме [приведения типов](README.md#режимы-типизации).

```$stream->containsCoercive(mixed $needle): bool```

- Скаляры сравниваются нестрого по значению (`1` совпадает с `'1'`; `0` совпадает с `false`; `'1e2'` совпадает с `100`).
- Объекты сравниваются по сериализованному значению (бросает `\InvalidArgumentException`, если искомое значение или элемент потока не сериализуется).
- Массивы сравниваются по сериализованному значению.
- `NaN` совпадает с `NaN`.
- Обрывает итерацию при первом совпадении.

```php
use IterTools\Stream;

$primes = [2, 3, 5, 7, 11, 13];

$boolean = Stream::of($primes)->containsCoercive('7');
// true (приведение типов)
```

##### Exactly N
Возвращает истину, если в точности для n элементов из потока предикат возвращает истину.

- Предикат является необязательным аргументом.
- По умолчанию (если не передан) предикат приводит элементы коллекции к `bool`.

```$stream->exactlyN(int $n, callable $predicate = null): bool```

```php
use IterTools\Summary;

$twoTruthsAndALie = [true, true, false];
$n                = 2;

$boolean = Stream::of($twoTruthsAndALie)->exactlyN($n);
// true
```

##### Is Empty
Возвращает истину, если коллекция потока пуста.

```$stream->isEmpty(): bool```

```php
use IterTools\Summary;

$numbers    = [0, 1, 2, 3, 4, 5];
$filterFunc = fn ($x) => $x > 10;

$boolean = Stream::($numbers)
    ->filter($filterFunc)
    ->isEmpty();
// true
```

##### Is Partitioned
Возвращает истину, если все истинные элементы коллекции из потока находятся в коллекции перед ложными
(истинность определяет предикат).

- Возвращает истину для пустой коллекции и для коллекции с одним элементом.
- Если предикат не был передан, истинность элемента получается через приведение его значения к булевому типу.

```$stream->isPartitioned(callable $predicate = null): bool```

```php
use IterTools\Summary;
$numbers          = [0, 2, 4, 1, 3, 5];
$evensBeforeOdds = fn ($item) => $item % 2 === 0;
$boolean = Stream::($numbers)
    ->isPartitioned($evensBeforeOdds);
// true
```

##### Is Sorted
Возвращает истину, если коллекция элементов из потока отсортирована в прямом порядке, иначе — ложь.

```$stream->isSorted(): bool```

Элементы должны быть сравнимы.

Для пустой коллекции или коллекции из одного элемента всегда возвращает истину.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->isSorted();
// true

$input = [1, 2, 3, 2, 1];

$result = Stream::of($input)
    ->isSorted();
// false
```

##### Is Reversed
Возвращает истину, если коллекция элементов из потока отсортирована в обратном порядке, иначе — ложь.

```$stream->isReversed(): bool```

Элементы должны быть сравнимы.

Для пустой коллекции или коллекции из одного элемента всегда возвращает истину.

```php
use IterTools\Stream;

$input = [5, 4, 3, 2, 1];

$result = Stream::of($input)
    ->isReversed();
// true

$input = [1, 2, 3, 2, 1];

$result = Stream::of($input)
    ->isReversed();
// false
```

##### None Match
Возвращает истину, если для всех элементов из потока предикат вернул ложь.

```$stream->noneMatch(callable $predicate): bool```

```php
use IterTools\Summary;

$grades         = [45, 50, 61, 0];
$isPassingGrade = fn ($grade) => $grade >= 70;

$boolean = Stream::of($grades)->noneMatch($isPassingGrade);
// true
```

##### Same With
Возвращает истину, если коллекция элементов из потока идентична переданным в аргументах коллекциям.

```$stream->sameWith(iterable ...$iterables): bool```

Если в метод не передать ни одной коллекции, он вернет истину.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->sameWith([1, 2, 3, 4, 5]);
// true

$result = Stream::of($input)
    ->sameWith([5, 4, 3, 2, 1]);
// false
```

##### Same Count With
Возвращает истину, если и коллекция элементов из потока, и все переданные коллекции имеют одинаковую длину.

```$stream->sameCountWith(iterable ...$iterables): bool```

Если в метод не передать ни одной коллекции, он вернет истину.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->sameCountWith([5, 4, 3, 2, 1]);
// true

$result = Stream::of($input)
    ->sameCountWith([1, 2, 3]);
// false
```

#### Редуцирование

##### To Average
Возвращает среднее арифметическое коллекции элементов из потока.

```$stream->toAverage(): mixed```

Для пустой коллекции вернет `null`.

```php
use IterTools\Stream;

$input = [2, 4, 6, 8];

$result = Stream::of($iterable)
    ->toAverage();
// 5
```

##### To Count
Возвращает длину коллекции элементов из потока.

```$stream->toCount(): mixed```

```php
use IterTools\Stream;

$input = [10, 20, 30, 40, 50];

$result = Stream::of($iterable)
    ->toCount();
// 5
```

##### To First
Возвращает первый элемент из коллекции в потоке.

```$stream->toFirst(): mixed```

Бросает `\LengthException` если хранимая в потоке коллекция пуста.

```php
use IterTools\Stream;

$input = [10, 20, 30];

$result = Stream::of($input)
    ->toFirst();
// 10
```

##### To First And Last
Возвращает первый и последний элементы из коллекции в потоке.

```$stream->toFirstAndLast(): array{mixed, mixed}```

Бросает `\LengthException` если хранимая в потоке коллекция пуста.

```php
use IterTools\Stream;

$input = [10, 20, 30];

$result = Stream::of($input)
    ->toFirstAndLast();
// [10, 30]
```

##### To First Match
Возвращает первый элемент из коллекции в потоке, удовлетворяющий предикату.

```$stream->toFirstMatch(callable $predicate, mixed $default = null): mixed```

- Результат предиката приводится к `bool` через `(bool)`.
- Завершает обход на первом совпадении.
- Возвращает `$default` (по умолчанию `null`), если совпадений нет.

```php
use IterTools\Stream;

$numbers = [1, 3, 5, 6, 7, 8];

$result = Stream::of($numbers)
    ->toFirstMatch(fn (int $n) => $n % 2 === 0);
// 6
```

##### To First Match Index
Возвращает индекс (отсчёт от нуля) первого элемента в потоке, удовлетворяющего предикату.

```$stream->toFirstMatchIndex(callable $predicate, mixed $default = null): mixed```

- Результат предиката приводится к `bool` через `(bool)`.
- Завершает обход на первом совпадении.
- Возвращает `$default` (по умолчанию `null`), если совпадений нет.

```php
use IterTools\Stream;

$numbers = [10, 20, 30, 40];

$result = Stream::of($numbers)
    ->toFirstMatchIndex(fn (int $n) => $n > 25);
// 2
```

##### To First Match Key
Возвращает ключ исходной коллекции для первого элемента в потоке, удовлетворяющего предикату.

```$stream->toFirstMatchKey(callable $predicate, mixed $default = null): mixed```

- Результат предиката приводится к `bool` через `(bool)`.
- Завершает обход на первом совпадении.
- Возвращает `$default` (по умолчанию `null`), если совпадений нет.

```php
use IterTools\Stream;

$users = ['alice' => 12, 'bob' => 17, 'carol' => 22, 'dan' => 30];

$result = Stream::of($users)
    ->toFirstMatchKey(fn (int $age) => $age >= 18);
// 'carol'
```

##### To Last
Возвращает последний элемент из коллекции в потоке.

```$stream->toLast(): mixed```

Бросает `\LengthException` если хранимая в потоке коллекция пуста.

```php
use IterTools\Stream;

$input = [10, 20, 30];

$result = Stream::of($input)
    ->toLast();
// 30
```

##### To Max
Возвращает максимальный элемент коллекции из потока.

```$stream->toMax(callable $compareBy = null): mixed```

- Функция `$compareBy` должна возвращать сравнимое значение.
- Если аргумент `$compareBy` не передан, элементы коллекции должны быть сравнимы.
- Для пустой коллекции вернет `null`.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($iterable)
    ->toMax();
// 3
```

##### To Min
Возвращает минимальный элемент коллекции из потока.

```$stream->toMin(callable $compareBy = null): mixed```

- Функция `$compareBy` должна возвращать сравнимое значение.
- Если аргумент `$compareBy` не передан, элементы коллекции должны быть сравнимы.
- Для пустой коллекции вернет `null`.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($iterable)
    ->toMin();
// -3
```

##### To Min Max
Возвращает минимальный и максимальный элементы коллекции из потока.

```$stream->toMinMax(callable $compareBy = null): array```

- Функция `$compareBy` должна возвращать сравнимое значение.
- Если аргумент `$compareBy` не передан, элементы коллекции должны быть сравнимы.
- Для пустой коллекции вернет `[null, null]`.

```php
use IterTools\Stream;

$numbers = [1, 2, 3, -1, -2, -3];

[$min, $max] = Stream::of($numbers)
    ->toMinMax();
// [-3, 3]
```

##### To Nth
Возвращает n-й элемент потока.

```$stream->toNth(int $position): mixed```

Для пустой коллекции возвращает `null`.

```php
use IterTools\Stream;

$lotrMovies = ['The Fellowship of the Ring', 'The Two Towers', 'The Return of the King'];

$result = Stream::of($lotrMovies)
    ->toNth(2);
// The Return of the King
```

##### To Product
Возвращает произведение элементов коллекции из потока.

```$stream->toProduct(): mixed```

Для пустой коллекции вернет `null`.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($iterable)
    ->toProduct();
// 120
```

##### To Random Value
Возвращает случайный элемент из коллекции потока.

```$stream->toRandomValue(): mixed```

```php
use IterTools\Stream;

$rpsHands = ['rock', 'paper', 'scissors']

$range = Stream::of($numbers)
    ->map('strtoupper')
    ->toRandomValue();
// e.g., rock
```

##### To Range
Возвращает разницу между максимальным и минимальным элементами коллекции из потока.

```$stream->toRange(): int|float```

Для пустой коллекции вернет `0`.

```php
use IterTools\Stream;

$grades = [100, 90, 80, 85, 95];

$range = Stream::of($numbers)
    ->toRange();
// 20
```

##### To String
Преобразует коллекцию из потока в строку, "склеивая" ее элементы.

* Значение необязательного аргумента `$separator` вставляется в качестве разделителя между элементами в строке.
* Значение необязательного аргумента `$prefix` вставляется в начало строки.
* Значение необязательного аргумента `$suffix` вставляется в конец строки.

```$stream->toString(string $separator = '', string $prefix = '', string $suffix = ''): string```

```php
use IterTools\Stream;

$words = ['IterTools', 'PHP', 'v1.0'];

$string = Stream::of($words)->toString($words);
// IterToolsPHPv1.0
$string = Stream::of($words)->toString($words, '-');
// IterTools-PHP-v1.0
$string = Stream::of($words)->toString($words, '-', 'Library: ');
// Library: IterTools-PHP-v1.0
$string = Stream::of($words)->toString($words, '-', 'Library: ', '!');
// Library: IterTools-PHP-v1.0!
```

##### To Sum
Возвращает сумму элементов коллекции из потока.

```$stream->toSum(): mixed```

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($iterable)
    ->toSum();
// 15
```

##### To Value
Редуцирует коллекцию из потока до значения, вычисляемого с использованием callback-функции.

В отличие от `array_reduce()`, работает с любыми `iterable` типами.

```$stream->toValue(callable $reducer, mixed $initialValue): mixed```

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($iterable)
    ->toValue(fn ($carry, $item) => $carry + $item);
// 15
```

#### Операции конвертации

##### To Array
Возвращает массив всех элементов из потока.

```$stream->toArray(): array```

```php
use IterTools\Stream;

$array = Stream::of([1, 1, 2, 2, 3, 4, 5])
    ->distinct()
    ->map(fn ($x) => $x**2)
    ->toArray();
// [1, 4, 9, 16, 25]
```

##### To Associative Array
Возвращает ассоциативный массив всех элементов из потока.

```$stream->toAssociativeArray(callable $keyFunc, callable $valueFunc): array```

```php
use IterTools\Stream;
$keyFunc
$array = Stream::of(['message 1', 'message 2', 'message 3'])
    ->map('strtoupper')
    ->toAssociativeArray(
        fn ($s) => \md5($s),
        fn ($s) => $s
    );
// [3b3f2272b3b904d342b2d0df2bf31ed4 => MESSAGE 1, 43638d919cfb8ea31979880f1a2bb146 => MESSAGE 2, ... ]
```

##### To Partition
Разделяет поток на два списка на основе предиката.

Возвращает массив из двух списков: `[истинные значения, ложные значения]`. Оба выходных массива — списки с переиндексацией (с нулевыми индексами); ключи исходной коллекции отбрасываются. Значение, возвращаемое предикатом, приводится к булевому типу через `(bool)`.

```$stream->toPartition(callable $predicate): array```

```php
use IterTools\Stream;

[$evens, $odds] = Stream::of([1, 2, 3, 4, 5, 6])
    ->toPartition(fn (int $n): bool => $n % 2 === 0);
// $evens: [2, 4, 6]
// $odds:  [1, 3, 5]
```

Так как обе части возвращаются вместе, `toPartition` естественно сочетается с операциями, потребляющими два списка. Например, при составлении турнирной сетки — разделяем номера посева на верхнюю и нижнюю половины, затем образуем пары верх vs перевёрнутая нижняя половина:

```php
use IterTools\Stream;

[$topHalf, $bottomHalf] = Stream::of([1, 2, 3, 4, 5, 6, 7, 8])
    ->toPartition(fn (int $seed): bool => $seed <= 4);

$matchups = Stream::of($topHalf)
    ->zipWith(array_reverse($bottomHalf))
    ->toArray();
// [[1, 8], [2, 7], [3, 6], [4, 5]]
```

##### Tee
Создает несколько одинаковых независимых потоков из данной коллекции.

```$stream->tee(int $count): array```
```php
use IterTools\Transform;
$daysOfWeek = ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun'];
$count = 3;
[$week1Stream, $week2Stream, $week3Stream] = Stream::of($daysOfWeek)
    ->tee($count);
// Каждый $weekStream содержит ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun']
```

#### Операции с побочными эффектами

##### Call For Each
Вызывает callback-функцию для каждого элемента из потока.

```$stream->callForEach(callable $function): void```

```php
use IterTools\Stream;

$languages = ['PHP', 'Python', 'Java', 'Go'];
$mascots   = ['elephant', 'snake', 'bean', 'gopher'];

$zipPrinter = fn ($zipped) => print("{$zipped[0]}'s mascot: {$zipped[1]}");

Stream::of($languages)
    ->zipWith($mascots)
    ->callForEach($zipPrinter);
// PHP's mascot: elephant
// Python's mascot: snake
// ...
```

##### Print
Вызывает `print()` для каждого элемента из потока.

* Элементы в потоке должны иметь строковое представление.

```$stream->print(string $separator = '', string $prefix = '', string $suffix = ''): void```

```php
use IterTools\Stream;

$words = ['IterTools', 'PHP', 'v1.0'];

Stream::of($words)->print();                       // IterToolsPHPv1.0
Stream::of($words)->print('-');                    // IterTools-PHP-v1.0
Stream::of($words)->print('-', 'Library: ');       // Library: IterTools-PHP-v1.0
Stream::of($words)->print('-', 'Library: ', '!');  // Library: IterTools-PHP-v1.0!
```

##### Print Line
Печатает элементы из потока каждый с новой строки.

* Элементы в потоке должны иметь строковое представление.

```$stream->println(): void```

```php
use IterTools\Stream;

$words = ['IterTools', 'PHP', 'v1.0'];

Stream::of($words)->printLn();
// IterTools
// PHP
// v1.0
```

##### To CSV File
Записывает содержимое потока в CSV файл.

```$stream->toCsvFile(resource $fileHandle, array $header = null, string 'separator = ',', string $enclosure = '"', string $escape = '\\'): void```

```php
use IterTools\Stream;
$starWarsMovies = [
    ['Star Wars: Episode IV – A New Hope', 'IV', 1977],
    ['Star Wars: Episode V – The Empire Strikes Back', 'V', 1980],
    ['Star Wars: Episode VI – Return of the Jedi', 'VI', 1983],
];
$header = ['title', 'episode', 'year'];
Stream::of($data)
    ->toCsvFile($fh, $header);
// title,episode,year
// "Star Wars: Episode IV – A New Hope",IV,1977
// "Star Wars: Episode V – The Empire Strikes Back",V,1980
// "Star Wars: Episode VI – Return of the Jedi",VI,1983
```

##### To File
Записывает содержимое потока в файл.

```$stream->toFile(resource $fileHandle, string $newLineSeparator = \PHP_EOL, string $header = null, string $footer = null): void```

```php
use IterTools\Stream;
$data = ['item1', 'item2', 'item3'];
$header = '<ul>';
$footer = '</ul>';
Stream::of($data)
    ->map(fn ($item) => "  <li>$item</li>")
    ->toFile($fh, \PHP_EOL, $header, $footer);
// <ul>
//   <li>item1</li>
//   <li>item2</li>
//   <li>item3</li>
// </ul>
```

### Операции для дебаггинга
#### Peek
Позволяет просмотреть каждый элемент между другими потоковыми операциями, чтобы выполнить какое-либо действие без влияния на поток.

```$stream->peek(callable $callback): void```

```php
use IterTools\Stream;

$logger = new SimpleLog\Logger('/tmp/log.txt', 'iterTools');

Stream::of(['some', 'items'])
  ->map('strtoupper')
  ->peek(fn ($x) => $logger->info($x))
  ->foreach($someComplexCallable);
```

#### Peek Stream
Позволяет просмотреть коллекцию потока между другими потоковыми операциями, чтобы выполнить какое-либо действие без влияния на поток.

```$stream->peekStream(callable $callback): Stream```

```php
use IterTools\Stream;

$logger = new SimpleLog\Logger('/tmp/log.txt', 'iterTools');

Stream::of(['some', 'items'])
  ->map('strtoupper')
  ->peekStream(fn ($stream) => $logger->info($stream))
  ->foreach($someComplexCallable);
```

#### Peek Print
Распечатывает каждый элемент хранимой коллекции в поток вывода между другими потоковыми операциями.

```$stream->peekPrint(string $separator = '', string $prefix = '', string $suffix = ''): void```

```php
use IterTools\Stream;

Stream::of(['some', 'items'])
  ->map('strtoupper')
  ->peekPrint()
  ->foreach($someComplexCallable);
```

#### Peek PrintR
Вызывает `print_r()` для каждого элемента хранимой коллекции между другими потоковыми операциями.

```$stream->peekPrintR(callable $callback): Stream```

```php
use IterTools\Stream;

Stream::of(['some', 'items'])
  ->map('strtoupper')
  ->peekPrintR()
  ->foreach($someComplexCallable);
```

##### Print R
Вызывает `print_r()` для каждого элемента из потока.

```$stream->printR(): void```

```php
use IterTools\Stream;

$items = [$string, $array, $object];

Stream::of($words)->printR();
// print_r output
```

##### Var Dump
Вызывает `var_dump()` для каждого элемента из потока.

```$stream->varDump(): void```

```php
use IterTools\Stream;

$items = [$string, $array, $object];

Stream::of($words)->varDump();
// var_dump output
```

# Итерирование одной коллекции

[Вернуться к главной странице](README.md)

Инструменты для итерирования и преобразования одной коллекции.

---

### Accumulate
Накапливает результат применения бинарного оператора по элементам коллекции.

```Single::accumulate(iterable $data, callable $op, mixed ...$initial)```

* Без начального значения: первый элемент результата — первый элемент коллекции без изменений, каждый следующий — `$op(аккумулятор, следующий_элемент)`.
* С начальным значением: первый элемент результата — начальное значение, каждый следующий — `$op(аккумулятор, следующий_элемент)`.
* Явный `null` является допустимым начальным значением (вариативная сигнатура отличает «нет начального значения» от «`null` в качестве начального значения»; это отличается от `Math::running*`, где `null` означает «нет начального значения»).
* Выбрасывает `\InvalidArgumentException`, если передано более одного начального значения.

```php
use IterTools\Single;

$numbers = [1, 2, 3, 4, 5];

foreach (Single::accumulate($numbers, fn ($a, $b) => $a + $b) as $runningSum) {
    print($runningSum . ' ');
}
// 1 3 6 10 15

foreach (Single::accumulate($numbers, fn ($a, $b) => $a + $b, 100) as $runningSum) {
    print($runningSum . ' ');
}
// 100 101 103 106 110 115
```

### Chunkwise
Итерирует коллекцию, разбитую на чанки одинаковой длины.

```Single::chunkwise(iterable $data, int $chunkSize)```

Минимальный размер чанка — 1.

```php
use IterTools\Single;

$movies = [
    'Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith',
    'A New Hope', 'Empire Strikes Back', 'Return of the Jedi',
    'The Force Awakens', 'The Last Jedi', 'The Rise of Skywalker'
];

foreach (Single::chunkwise($movies, 3) as $trilogy) {
    $trilogies[] = $trilogy;
}
// [
//     ['Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith'],
//     ['A New Hope', 'Empire Strikes Back', 'Return of the Jedi'],
//     ['The Force Awakens', 'The Last Jedi', 'The Rise of Skywalker]'
// ]
```

### Chunkwise Overlap
Итерирует коллекцию, разбитую на взаимонакладывающиеся чанки.

```Single::chunkwiseOverlap(iterable $data, int $chunkSize, int $overlapSize, bool $includeIncompleteTail = true)```

* Минимальный размер чанка — 1.
* Размер наложения должен быть меньше длины чанка.

```php
use IterTools\Single;

$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

foreach (Single::chunkwiseOverlap($numbers, 3, 1) as $chunk) {
    // [1, 2, 3], [3, 4, 5], [5, 6, 7], [7, 8, 9], [9, 10]
}
```

### Compress
Отфильтровывает невыбранные элементы из коллекции.

```Single::compress(string $data, $selectors)```

```php
use IterTools\Single;

$movies = [
    'Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith',
    'A New Hope', 'Empire Strikes Back', 'Return of the Jedi',
    'The Force Awakens', 'The Last Jedi', 'The Rise of Skywalker'
];
$goodMovies = [0, 0, 0, 1, 1, 1, 1, 0, 0];

foreach (Single::compress($movies, $goodMovies) as $goodMovie) {
    print($goodMovie);
}
// 'A New Hope', 'Empire Strikes Back', 'Return of the Jedi', 'The Force Awakens'
```

### Compress Associative
Возвращает элементы из коллекции по заданным ключам.

```Single::compressAssociative(string $data, array $selectorKeys)```

* Ключами могут быть только строки или целые числа (по аналогии с ключами PHP-массивов).

```php
use IterTools\Single;
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
$originalTrilogyNumbers = ['IV', 'V', 'VI'];
foreach (Single::compressAssociative($starWarsEpisodes, $originalTrilogyNumbers) as $episode => $title) {
    print("$episode: $title" . \PHP_EOL);
}
// IV: A New Hope
// V: The Empire Strikes Back
// VI: Return of the Jedi
```

### Drop While
Пропускает элементы, пока предикат возвращает истину.

После того как предикат впервые вернул `false`, все последующие элементы попадают в выборку.

```Single::dropWhile(iterable $data, callable $predicate)```

```php
use IterTools\Single;

$scores    = [50, 60, 70, 85, 65, 90];
$predicate = fn ($x) => $x < 70;

foreach (Single::dropWhile($scores, $predicate) as $score) {
    print($score);
}
// 70, 85, 65, 90
```

### Enumerate
Итерирует пары `[индекс, значение]`.

```Single::enumerate(iterable $data, int $start = 0)```

* Индекс генерируется последовательно начиная с `$start`, независимо от ключей исходной коллекции.
* Допускается отрицательное значение `$start`.

```php
use IterTools\Single;

$seasons = ['spring', 'summer', 'autumn', 'winter'];

foreach (Single::enumerate($seasons) as [$index, $season]) {
    print("$index: $season" . \PHP_EOL);
}
// 0: spring
// 1: summer
// 2: autumn
// 3: winter
```

### Filter
Возвращает только те элементы, для которых предикат возвращает истину.

```Single::filter(iterable $data, callable $predicate)```

```php
use IterTools\Single;

$starWarsEpisodes   = [1, 2, 3, 4, 5, 6, 7, 8, 9];
$goodMoviePredicate = fn ($episode) => $episode > 3 && $episode < 8;

foreach (Single::filter($starWarsEpisodes, $goodMoviePredicate) as $goodMovie) {
    print($goodMovie);
}
// 4, 5, 6, 7
```

### Filter True
Возвращает только истинные элементы из коллекции. Истинность определяется предикатом.

Если предикат не передан, значения элементов коллекции приводятся к `bool` для оценки.

```Single::filterFalse(iterable $data, callable $predicate = null)```

```php
use IterTools\Single;

$reportCardGrades = [100, 0, 95, 85, 0, 94, 0];
foreach (Single::filterTrue($reportCardGrades) as $goodGrade) {
    print($goodGrade);
}
// 100, 95, 85, 94
```

### Filter False
Возвращает только ложные элементы из коллекции. Истинность определяется предикатом.

Если предикат не передан, значения элементов коллекции приводятся к `bool` для оценки.

```Single::filterFalse(iterable $data, callable $predicate = null)```

```php
use IterTools\Single;

$alerts = [0, 1, 1, 0, 1, 0, 0, 1, 1];
foreach (Single::filterFalse($alerts) as $noAlert) {
    print($noAlert);
}
// 0, 0, 0, 0
```

### Filter Keys
Возвращает только те элементы, для ключей которых предикат возвращает истину.

```Single::filterKeys(iterable $data, callable $predicate)```
```php
use IterTools\Single;

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
$summerFilter = fn ($year) => $year % 4 === 0;
foreach (Single::filterKeys($olympics, $summerFilter) as $year => $hostCity) {
    print("$year: $hostCity" . \PHP_EOL);
}
// 2000: Sydney
// 2004: Athens
// 2008: Beijing
// 2012: London
// 2016: Rio de Janeiro
// 2020: Tokyo
```

### Flat Map
Отображение коллекции с уплощением результата на 1 уровень вложенности.

```Single::flatMap(iterable $data, callable $mapper)```

```php
use IterTools\Single;
$data   = [1, 2, 3, 4, 5];
$mapper = fn ($item) => [$item, -$item];
foreach (Single::flatMap($data, $mapper) as $number) {
    print($number . ' ');
}
// 1 -1 2 -2 3 -3 4 -4 5 -5
```

### Flatten
Многоуровневое уплощение коллекции.

```Single::flatten(iterable $data, int $dimensions = 1)```

```php
use IterTools\Single;
$multidimensional = [1, [2, 3], [4, 5]];
$flattened = [];
foreach (Single::flatten($multidimensional) as $number) {
    $flattened[] = $number;
}
// [1, 2, 3, 4, 5]
```

### Group By
Группирует элементы коллекции по заданному правилу.

```Single::groupBy(iterable $data, callable $groupKeyFunction, callable $itemKeyFunction = null)```

* Функция `$groupKeyFunction` должна возвращать общий ключ (или коллекцию ключей) для элементов группы.
* Функция `$itemKeyFunction` (опциональный аргумент) позволяет назначить кастомные индексы эелементам в группе.

```php
use IterTools\Single;

$cartoonCharacters = [
    ['Garfield', 'cat'],
    ['Tom', 'cat'],
    ['Felix', 'cat'],
    ['Heathcliff', 'cat'],
    ['Snoopy', 'dog'],
    ['Scooby-Doo', 'dog'],
    ['Odie', 'dog'],
    ['Donald', 'duck'],
    ['Daffy', 'duck'],
];

$charactersGroupedByAnimal = [];
foreach (Single::groupBy($cartoonCharacters, fn ($x) => $x[1]) as $animal => $characters) {
    $charactersGroupedByAnimal[$animal] = $characters;
}
/*
'cat' => [
    ['Garfield', 'cat'],
    ['Tom', 'cat'],
    ['Felix', 'cat'],
    ['Heathcliff', 'cat'],
],
'dog' => [
    ['Snoopy', 'dog'],
    ['Scooby-Doo', 'dog'],
    ['Odie', 'dog'],
],
'duck' => [
    ['Donald', 'duck'],
    ['Daffy', 'duck'],
*/
```

### Intersperse
Вставляет разделитель между последовательными элементами коллекции.

```Single::intersperse(iterable $data, mixed $separator)```

* Порядок выдачи: элемент, разделитель, элемент, разделитель, …, элемент.
* Разделитель не выдаётся ни перед первым элементом, ни после последнего.
* Разделитель выдаётся как есть: массивы не разворачиваются, объекты сохраняют идентичность.
* Ключи исходной коллекции отбрасываются — на выходе список с последовательными целочисленными ключами.

```php
use IterTools\Single;

$pipelineStages = ['fetch', 'parse', 'validate', 'persist'];

$flow = '';
foreach (Single::intersperse($pipelineStages, ' -> ') as $part) {
    $flow .= $part;
}
// 'fetch -> parse -> validate -> persist'
```

```php
use IterTools\Single;

$cells = ['name', 'email', 'role'];

$row = '';
foreach (Single::intersperse($cells, ',') as $part) {
    $row .= $part;
}
// 'name,email,role'
```

См. также [Stream::intersperse](stream.md#intersperse).

### Limit
Ограничивает итерирование коллекции заданным максимальным числом итераций.

Останавливает процесс итерирования, когда число итераций достигает `$limit`.

```Single::limit(iterable $data, int $limit)```

```php
use IterTools\Single;

$matrixMovies = ['The Matrix', 'The Matrix Reloaded', 'The Matrix Revolutions', 'The Matrix Resurrections'];
$limit        = 1;

foreach (Single::limit($matrixMovies, $limit) as $goodMovie) {
    print($goodMovie);
}
// 'The Matrix' (and nothing else)
```

### Map
Отображение коллекции с использованием callback-функции.

Результат выполнения представляет собой коллекцию результатов вызова callback-функции для каждого элемента.

```Single::map(iterable $data, callable $function)```

```php
use IterTools\Single;

$grades               = [100, 99, 95, 98, 100];
$strictParentsOpinion = fn ($g) => $g === 100 ? 'A' : 'F';

foreach (Single::map($grades, $strictParentsOpinion) as $actualGrade) {
    print($actualGrade);
}
// A, F, F, F, A
```

### Pairwise
Итерирует коллекцию попарно (с наложением).

Возвращает пустой генератор, если коллекция содержит меньше 2-х элементов.

```Single::pairwise(iterable $data)```

```php
use IterTools\Single;

$friends = ['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe'];

foreach (Single::pairwise($friends) as [$leftFriend, $rightFriend]) {
    print("{$leftFriend} and {$rightFriend}");
}
// Ross and Rachel, Rachel and Chandler, Chandler and Monica, ...
```

### Repeat
Повторяет данное значение заданное число раз.

```Single::repeat(mixed $item, int $repetitions)```

```php
use IterTools\Single;

$data        = 'Beetlejuice';
$repetitions = 3;

foreach (Single::repeat($data, $repetitions) as $repeated) {
    print($repeated);
}
// 'Beetlejuice', 'Beetlejuice', 'Beetlejuice'
```

### Reindex
Переиндексирует key-value коллекцию, используя функцию-индексатор.

```Single::reindex(string $data, callable $indexer)```

```php
use IterTools\Single;
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
$reindexedData = [];
foreach (Single::reindex($data, $reindexFunc) as $key => $filmData) {
    $reindexedData[$key] = $filmData;
}
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

### Reverse
Итерирует коллекцию в обратном порядке.

```Single::reverse(iterable $data)```

```php
use IterTools\Single;
$words = ['Alice', 'answers', 'your', 'questions', 'Bob'];
foreach (Single::reverse($words) as $word) {
    print($word . ' ');
}
// Bob questions your answers Alice
```

### Skip
Пропускает n элементов коллекции со смещением (опционально).

```Single::skip(iterable $data, int $count, int $offset = 0)```

```php
use IterTools\Single;

$movies = [
    'The Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith',
    'A New Hope', 'The Empire Strikes Back', 'Return of the Jedi',
    'The Force Awakens', 'The Last Jedi', 'The Rise of Skywalker'
];

$prequelsRemoved = [];
foreach (Single::skip($movies, 3) as $nonPrequel) {
    $prequelsRemoved[] = $nonPrequel;
} // Episodes IV - IX

$onlyTheBest = [];
foreach (Single::skip($prequelsRemoved, 3, 3) as $nonSequel) {
    $onlyTheBest[] = $nonSequel;
}
// 'A New Hope', 'The Empire Strikes Back', 'Return of the Jedi'
```

### Slice
Возвращает подвыборку коллекции.

```Single::slice(iterable $data, int $start = 0, int $count = null, int $step = 1)```

```php
use IterTools\Single;
$olympics = [1992, 1994, 1996, 1998, 2000, 2002, 2004, 2006, 2008, 2010, 2012, 2014, 2016, 2018, 2020, 2022];
$winterOlympics = [];
foreach (Single::slice($olympics, 1, 8, 2) as $winterYear) {
    $winterOlympics[] = $winterYear;
}
// [1994, 1998, 2002, 2006, 2010, 2014, 2018, 2022]
```

### String
Итерирует строку посимвольно.

```Single::string(string $string)```

```php
use IterTools\Single;

$string = 'MickeyMouse';

$listOfCharacters = [];
foreach (Single::string($string) as $character) {
    $listOfCharacters[] = $character;
}
// ['M', 'i', 'c', 'k', 'e', 'y', 'M', 'o', 'u', 's', 'e']
```

### Take While
Отдает элементы, пока предикат возвращает истину.

Останавливает процесс итерирования, как только предикат впервые вернет ложь.

```Single::takeWhile(iterable $data, callable $predicate)```
```php
use IterTools\Single;

$prices = [0, 0, 5, 10, 0, 0, 9];
$isFree = fn ($price) => $price == 0;

foreach (Single::takeWhile($prices, $isFree) as $freePrice) {
    print($freePrice);
}
// 0, 0
```

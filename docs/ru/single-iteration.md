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
//     ['The Force Awakens', 'The Last Jedi', 'The Rise of Skywalker']
// ]
```

### Chunkwise Overlap
Итерирует коллекцию, разбитую на взаимонакладывающиеся чанки.

```Single::chunkwiseOverlap(iterable $data, int $chunkSize, int $overlapSize, bool $includeIncompleteTail = true)```

* Минимальный размер чанка — 1.
* Размер наложения должен быть меньше длины чанка.
* См. также [Windowed](#windowed) — основанный на шаге аналог, дополнительно поддерживающий окна с пропусками (`$step > $size`).

```php
use IterTools\Single;

$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

foreach (Single::chunkwiseOverlap($numbers, 3, 1) as $chunk) {
    // [1, 2, 3], [3, 4, 5], [5, 6, 7], [7, 8, 9], [9, 10]
}
```

### Compress
Отфильтровывает невыбранные элементы из коллекции.

```Single::compress(iterable $data, iterable $selectors)```

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

```Single::compressAssociative(iterable $data, array $selectorKeys)```

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

### Drop Last
Перебирает все элементы итерируемого объекта, кроме последних `$count`.

Один проход на основе очереди: последние `$count` элементов никогда не возвращаются. Если `$count` равен `0`, возвращаются все элементы. Если `$count` больше или равен длине итерируемого объекта, не возвращается ничего. Ключи сохраняются.

```Single::dropLast(iterable $data, int $count)```

```php
use IterTools\Single;

$reportRows = ['Alice', 'Bob', 'Carol', 'TOTAL'];

foreach (Single::dropLast($reportRows, 1) as $name) {
    print($name);
}
// Alice, Bob, Carol
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

```Single::filterTrue(iterable $data, ?callable $predicate = null)```

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

```Single::filterFalse(iterable $data, ?callable $predicate = null)```

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

### Filter With Keys
Фильтрует коллекцию, передавая в предикат и значение, и ключ.

Оставляет элементы, для которых предикат — вызываемый как `$predicate($value, $key)` — возвращает истину (приводится к `(bool)`). Ключи сохраняются.

```Single::filterWithKeys(iterable $data, callable $predicate)```

```php
use IterTools\Single;

$inventory = ['apples' => 5, 'bananas' => 0, 'avocados' => 3, 'cherries' => 0];

$inStockStartingWithA = fn ($count, $name) => $count > 0 && \str_starts_with($name, 'a');

foreach (Single::filterWithKeys($inventory, $inStockStartingWithA) as $name => $count) {
    print("$name: $count" . \PHP_EOL);
}
// apples: 5
// avocados: 3
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

### Flat Map With Keys
Отображение коллекции функцией, учитывающей ключи, с последующим уплощением результата на 1 уровень.

Функция вызывается как `$func($value, $key, callable $self)`. Третий аргумент — сама функция, что позволяет рекурсивно уплощать вложенные коллекции с помощью стрелочных функций. Как и в `flatMap`, внешние и внутренние ключи отбрасываются — результат отдаётся с автоматически сгенерированными последовательными числовыми ключами. Для отображения 1:1 с сохранением ключей используйте `mapWithKeys`.

```Single::flatMapWithKeys(iterable $data, callable $func)```

```php
use IterTools\Single;
$data = ['a' => 1, 'b' => 2, 'c' => 3];
$func = fn ($value, $key) => [$key, $value];
foreach (Single::flatMapWithKeys($data, $func) as $item) {
    print($item . ' ');
}
// a 1 b 2 c 3
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

```Single::groupBy(iterable $data, callable $groupKeyFunction, ?callable $itemKeyFunction = null)```

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

Ленивая операция: источник никогда не продвигается дальше тех элементов, которые были возвращены, поэтому источник с побочными эффектами (файловый дескриптор, постраничные HTTP-запросы, курсор БД) не вычитывается лишний раз. При `$limit`, равном 0, источник не затрагивается вовсе.

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

### Map With Keys
Отображение коллекции функцией, в которую передаются и значение, и ключ.

Функция вызывается как `$func($value, $key)`. Преобразованное значение отдаётся с сохранением исходного ключа.

```Single::mapWithKeys(iterable $data, callable $func)```

```php
use IterTools\Single;

$prices = ['apple' => 1.5, 'banana' => 0.75, 'cherry' => 3.0];

$label = fn ($price, $name) => "$name: \$$price";

foreach (Single::mapWithKeys($prices, $label) as $key => $labeled) {
    print("$key => $labeled" . \PHP_EOL);
}
// apple => apple: $1.5
// banana => banana: $0.75
// cherry => cherry: $3
```

### Map Spread
Отображение коллекции, при котором каждый элемент распаковывается как позиционные аргументы функции.

```Single::mapSpread(iterable $data, callable $function)```

* Каждый элемент `$data` сам должен быть итерируемым; его значения передаются в `$function` позиционно через splat-оператор.
* Внутренние ключи отбрасываются — значения передаются позиционно, даже если внутренний элемент является ассоциативным массивом.
* Внешние ключи сохраняются (как и в `Single::map`).
* Бросает `\InvalidArgumentException`, если какой-либо внутренний элемент не является итерируемым.

```php
use IterTools\Single;

$pairs = [[1, 2], [3, 4], [5, 6]];

foreach (Single::mapSpread($pairs, fn ($a, $b) => $a + $b) as $sum) {
    print($sum);
}
// 3, 7, 11
```

```php
use IterTools\Multi;
use IterTools\Single;

$names  = ['Alice', 'Bob', 'Carol'];
$scores = [92, 87, 95];

$lines = Single::mapSpread(
    Multi::zip($names, $scores),
    fn (string $name, int $score) => "{$name}: {$score}"
);

foreach ($lines as $line) {
    print($line);
}
// 'Alice: 92', 'Bob: 87', 'Carol: 95'
```

См. также [Stream::mapSpread](stream.md#map-spread).

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

### Range
Лениво генерирует конечную арифметическую прогрессию чисел.

```Single::range(int|float $start, int|float $end, int|float $step = 1)```

* Направление выводится из соотношения `$start` и `$end`, а не из знака `$step`. Внутри используется модуль шага (`abs($step)`).
* Отрицательный `$step` допустим только если направление убывающее (или `$start == $end`).
* Соответствует числовой семантике встроенной функции `\range()` из PHP 8.3+ для входов `int|float`, одинаково на всех поддерживаемых версиях PHP — включая целочисленный вывод при шаге типа float с целым значением (`range(1, 5, 1.0)` → `[1, 2, 3, 4, 5]`), который встроенная функция `\range()` в PHP 8.2 приводит к float. Строки не поддерживаются (для строк используйте `Stream::ofRange`).
* Выбрасывает `\InvalidArgumentException`, если какой-либо операнд не является конечным (`INF`/`-INF`/`NAN`), если `$step == 0`, если знак шага конфликтует с направлением операндов или если `abs($step) > abs($end - $start)` (строго больше).
* Ленивая реализация: безопасно работает с большими границами при использовании с downstream-ограничителями.

```php
use IterTools\Single;

foreach (Single::range(1, 5) as $n) {
    print($n);
}
// 1, 2, 3, 4, 5

foreach (Single::range(5, 1) as $n) {
    print($n);
}
// 5, 4, 3, 2, 1

foreach (Single::range(0.0, 1.0, 0.25) as $n) {
    print($n);
}
// 0.0, 0.25, 0.5, 0.75, 1.0
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

```Single::reindex(iterable $data, callable $indexer)```

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

Ленивая операция: источник никогда не продвигается дальше последнего возвращённого элемента, поэтому источник с побочными эффектами (файловый дескриптор, постраничные HTTP-запросы, курсор БД) не вычитывается лишний раз. При `$count`, равном 0, источник не затрагивается вовсе. Элементы, пропускаемые из-за `$start` или `$step`, всё же должны быть вычитаны, чтобы быть пропущенными.

```Single::slice(iterable $data, int $start = 0, ?int $count = null, int $step = 1)```

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

### Take Last
Перебирает последние `$count` элементов итерируемого объекта.

Ленивый, но ограниченный: в памяти удерживается только кольцевой буфер размера `$count`, поэтому метод безопасен для очень больших (но конечных) входных данных. Если `$count` равен `0`, не возвращается ничего. Если `$count` больше длины итерируемого объекта, возвращаются все элементы. Ключи сохраняются.

```Single::takeLast(iterable $data, int $count)```

```php
use IterTools\Single;

$logLines = ['line 1', 'line 2', 'line 3', 'line 4', 'line 5'];

foreach (Single::takeLast($logLines, 2) as $line) {
    print($line);
}
// line 4, line 5
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

### Group Adjacent By
Группирует подряд идущие элементы, имеющие одинаковый ключ, возвращаемый функцией `$keyFn`.

```Single::groupAdjacentBy(iterable $data, callable $keyFn)```

* Отдаёт пары `[ключ_группы, list<значение>]` последовательно (а не ассоциативно).
* Повторяющиеся ключи, встречающиеся в разных подряд идущих сериях, дают **отдельные** группы (в отличие от `groupBy`).
* Ключи исходной коллекции отбрасываются; внешний массив — последовательный, внутренние группы — list-массивы.

```php
use IterTools\Single;

$readings = [1, 1, 2, 2, 1, 3];

foreach (Single::groupAdjacentBy($readings, fn ($x) => $x) as [$key, $run]) {
    print($key . ': ' . \implode(',', $run) . PHP_EOL);
}
// 1: 1,1
// 2: 2,2
// 1: 1
// 3: 3
```

### Pad Left
Дополняет коллекцию слева до длины не менее `$length`.

```Single::padLeft(iterable $data, int $length, mixed $fill)```

* Если коллекция уже имеет длину `$length` или больше, все элементы проходят без изменений (без обрезки).
* Ключи исходной коллекции отбрасываются; ключи результата — последовательные, начиная с 0.
* Бросает `\InvalidArgumentException`, если `$length` отрицателен.

```php
use IterTools\Single;

$values = [1, 2, 3];

foreach (Single::padLeft($values, 5, 0) as $value) {
    print($value);
}
// 0, 0, 1, 2, 3
```

### Pad Right
Дополняет коллекцию справа до длины не менее `$length`.

```Single::padRight(iterable $data, int $length, mixed $fill)```

* Если коллекция уже имеет длину `$length` или больше, все элементы проходят без изменений (без обрезки).
* Ключи исходной коллекции отбрасываются; ключи результата — последовательные, начиная с 0.
* Бросает `\InvalidArgumentException`, если `$length` отрицателен.

```php
use IterTools\Single;

$values = [1, 2, 3];

foreach (Single::padRight($values, 5, 0) as $value) {
    print($value);
}
// 1, 2, 3, 0, 0
```

### Split When
Разбивает коллекцию на группы, начиная новую группу при каждом совпадении предиката.

```Single::splitWhen(iterable $data, callable $predicate)```

* Совпавший элемент начинает следующую группу (становится её первым элементом).
* Если предикат совпадает с самым первым элементом, ведущая пустая группа не отдаётся.
* Пустая входная коллекция не отдаёт ничего.
* Ключи исходной коллекции отбрасываются; внешний массив — последовательный, внутренние группы — list-массивы.

```php
use IterTools\Single;

$values = [1, 2, 0, 3, 0, 4];

foreach (Single::splitWhen($values, fn ($x) => $x === 0) as $group) {
    print(\implode(',', $group) . PHP_EOL);
}
// 1,2
// 0,3
// 0,4
```

### Windowed
Итерирует скользящие окна из `$size` элементов, сдвигаясь на `$step` элементов между окнами.

```Single::windowed(iterable $data, int $size, int $step = 1, bool $partial = false)```

Это основанный на шаге аналог метода [Chunkwise Overlap](#chunkwise-overlap), который дополнительно поддерживает окна с пропусками (`$step > $size`), что `chunkwiseOverlap` выразить не может.

* Размер окна должен быть не меньше 1; шаг должен быть не меньше 1.
* Каждое окно — это list-массив с индексами от 0; ключи исходной коллекции отбрасываются. Память ограничена O(`$size`).
* При `1 <= $step <= $size` эквивалентно `chunkwiseOverlap($data, $size, $size - $step, includeIncompleteTail: $partial)`.
* При `$step > $size` `$step - $size` элементов после каждого полного окна отбрасываются (окна с пропусками).
* `$partial` управляет тем, отдаётся ли последнее неполное окно. **Обратите внимание:** по умолчанию `false` — противоположно `includeIncompleteTail` у `chunkwiseOverlap`, у которого по умолчанию `true`.

```php
use IterTools\Single;

$temperatures = [1, 2, 3, 4, 5];

foreach (Single::windowed($temperatures, 3) as $window) {
    // [1, 2, 3], [2, 3, 4], [3, 4, 5]
}

foreach (Single::windowed($temperatures, 2, 2, partial: true) as $window) {
    // [1, 2], [3, 4], [5]
}
```

### With First
Сопоставляет каждому элементу булев флаг, отмечающий, является ли он первым элементом.

```Single::withFirst(iterable $data)```

Отдаёт кортежи `[bool $isFirst, mixed $value]`. Полностью ленивый, память O(1). Ключи исходной коллекции отбрасываются; ключи результата — последовательные, начиная с 0.

```php
use IterTools\Single;

$lines = ['header', 'row 1', 'row 2'];

foreach (Single::withFirst($lines) as [$isFirst, $line]) {
    print($isFirst ? "H: $line" : "  $line");
}
// H: header
//   row 1
//   row 2
```

### With Last
Сопоставляет каждому элементу булев флаг, отмечающий, является ли он последним элементом.

```Single::withLast(iterable $data)```

Отдаёт кортежи `[bool $isLast, mixed $value]`. Использует опережающее чтение на один элемент, поэтому ленивый, память O(1). Ключи исходной коллекции отбрасываются; ключи результата — последовательные, начиная с 0.

```php
use IterTools\Single;

$items = ['a', 'b', 'c'];

foreach (Single::withLast($items) as [$isLast, $item]) {
    print($isLast ? "$item." : "$item, ");
}
// a, b, c.
```

### With First And Last
Сопоставляет каждому элементу булевы флаги, отмечающие, является ли он первым и/или последним элементом.

```Single::withFirstAndLast(iterable $data)```

Отдаёт кортежи `[bool $isFirst, bool $isLast, mixed $value]` — распространённый шаблон «пометить края». Коллекция из одного элемента отдаёт один кортеж `[true, true, $value]`. Использует опережающее чтение на один элемент, поэтому ленивый, память O(1). Ключи исходной коллекции отбрасываются; ключи результата — последовательные, начиная с 0.

```php
use IterTools\Single;

$items = ['a', 'b', 'c'];

foreach (Single::withFirstAndLast($items) as [$isFirst, $isLast, $item]) {
    // [true, false, 'a'], [false, false, 'b'], [false, true, 'c']
}
```

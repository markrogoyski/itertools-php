# Итерирование множеств и мультимножеств

[Вернуться к главной странице](README.md)

Инструменты для операций над множествами и мультимножествами на итерируемых сущностях.

---

### Distinct
Фильтрует коллекцию, выдавая только уникальные значения.

```Set::distinct(iterable $data, bool $strict = true)```

По умолчанию выполняет сравнение в [режиме строгой типизации](README.md#режимы-типизации). Передайте значение `false` аргумента `$strict`, чтобы работать в режиме приведения типов.

```php
use IterTools\Set;

$chessSet = ['rook', 'rook', 'knight', 'knight', 'bishop', 'bishop', 'king', 'queen', 'pawn', 'pawn', ... ];

foreach (Set::distinct($chessSet) as $chessPiece) {
    print($chessPiece);
}
// rook, knight, bishop, king, queen, pawn

$mixedTypes = [1, '1', 2, '2', 3];

foreach (Set::distinct($mixedTypes, false) as $datum) {
    print($datum);
}
// 1, 2, 3
```

### Distinct By
Фильтрует коллекцию, возвращая только уникальные элементы согласно заданной функции сравнения.

```Set::distinctBy(iterable $data, callable $compareBy)```

```php
use IterTools\Set;

$streetFighterConsoleReleases = [
    ['id' => '112233', 'name' => 'Street Fighter 3 3rd Strike', 'console' => 'Dreamcast'],
    ['id' => '223344', 'name' => 'Street Fighter 3 3rd Strike', 'console' => 'PS4'],
    ['id' => '334455', 'name' => 'Street Fighter 3 3rd Strike', 'console' => 'PS5'],
    ['id' => '445566', 'name' => 'Street Fighter VI', 'console' => 'PS4'],
    ['id' => '556677', 'name' => 'Street Fighter VI', 'console' => 'PS5'],
    ['id' => '667788', 'name' => 'Street Fighter VI', 'console' => 'PC'],
];
$compareBy = fn ($sfTitle) => $sfTitle['name'];

$uniqueTitles = [];
foreach (Set::distinctBy($streetFighterConsoleReleases, $compareBy) as $sfTitle) {
    $uniqueTitles[] = $sfTitle;
}

// Содержит одну запись для SF3 3rd Strike и одну для SFVI.
```

### Distinct Adjacent
Удаляет только подряд идущие дубликаты в коллекции (поведение Unix `uniq`).

```Set::distinctAdjacent(iterable $data)```

* Каждый элемент сравнивается строго (`===`) с предыдущим выданным элементом.
* Не подряд идущие дубликаты сохраняются.
* Работает с памятью O(1) — хранится только предыдущий элемент.
* Исходные ключи отбрасываются; результат — список с последовательными целочисленными ключами.

```php
use IterTools\Set;

$values = [1, 1, 2, 2, 3, 1, 1];

$result = [];
foreach (Set::distinctAdjacent($values) as $value) {
    $result[] = $value;
}
// [1, 2, 3, 1] — последняя 1 сохраняется, потому что не идёт подряд с более ранними
```

```php
use IterTools\Set;

$logLines = ['error: timeout', 'error: timeout', 'error: timeout', 'info: ok', 'error: timeout'];

$collapsed = [];
foreach (Set::distinctAdjacent($logLines) as $line) {
    $collapsed[] = $line;
}
// ['error: timeout', 'info: ok', 'error: timeout']
```

См. также [Stream::distinctAdjacent](stream.md#distinct-adjacent).

### Distinct Adjacent By
Удаляет только подряд идущие дубликаты в коллекции, сравнивая значения, возвращаемые функцией ключа.

```Set::distinctAdjacentBy(iterable $data, callable $keyFn)```

* Извлечённый ключ каждого элемента сравнивается строго (`===`) с ключом предыдущего элемента.
* Не подряд идущие дубликаты по ключу сохраняются.
* Работает с памятью O(1) и вызывает `$keyFn` ровно один раз на элемент.
* Исходные ключи отбрасываются; результат — список с последовательными целочисленными ключами.

```php
use IterTools\Set;

$words = ['apple', 'ant', 'banana', 'berry', 'apple'];

$firstLetterRuns = [];
foreach (Set::distinctAdjacentBy($words, fn ($s) => $s[0]) as $word) {
    $firstLetterRuns[] = $word;
}
// ['apple', 'banana', 'apple'] — первое слово в каждой серии с одинаковой первой буквой
```

```php
use IterTools\Set;

$readings = [
    ['ts' => 60,  'v' => 1],
    ['ts' => 65,  'v' => 2],
    ['ts' => 119, 'v' => 3],
    ['ts' => 120, 'v' => 4],
    ['ts' => 121, 'v' => 5],
];
$minuteKey = fn ($r) => intdiv($r['ts'], 60);

$compressed = [];
foreach (Set::distinctAdjacentBy($readings, $minuteKey) as $reading) {
    $compressed[] = $reading;
}
// сохраняется только первое показание из каждой серии в той же минуте:
// [['ts' => 60, 'v' => 1], ['ts' => 120, 'v' => 4]]
```

См. также [Stream::distinctAdjacentBy](stream.md#distinct-adjacent-by).

### Intersection
Итерирует пересечение коллекций.

```Set::intersection(iterable ...$iterables)```

Если хотя бы в одной коллекции встречаются повторяющиеся элементы, работают правила пересечения [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Set;

$chessPieces = ['rook', 'knight', 'bishop', 'queen', 'king', 'pawn'];
$shogiPieces = ['rook', 'knight', 'bishop' 'king', 'pawn', 'lance', 'gold general', 'silver general'];

foreach (Set::intersection($chessPieces, $shogiPieces) as $commonPiece) {
    print($commonPiece);
}
// rook, knight, bishop, king, pawn
```

### Intersection Coercive
Итерирует пересечение коллекций в режиме [приведения типов](README.md#режимы-типизации).

```Set::intersectionCoercive(iterable ...$iterables)```

Если хотя бы в одной коллекции встречаются повторяющиеся элементы, работают правила пересечения [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Set;

$numbers  = [1, 2, 3, 4, 5];
$numerics = ['1', '2', 3];

foreach (Set::intersectionCoercive($numbers, $numerics) as $commonNumber) {
    print($commonNumber);
}
// 1, 2, 3
```

### Partial Intersection
Итерирует [M-частичное пересечение](https://github.com/Smoren/partial-intersection-php) коллекций.

```Set::partialIntersection(int $minIntersectionCount, iterable ...$iterables)```

* Если хотя бы в одной коллекции встречаются повторяющиеся элементы, работают правила пересечения [мультимножеств](https://en.wikipedia.org/wiki/Multiset).
* Если `$minIntersectionCount = 1`, работают правила объединения [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Set;

$staticallyTyped    = ['c++', 'java', 'c#', 'go', 'haskell'];
$dynamicallyTyped   = ['php', 'python', 'javascript', 'typescript'];
$supportsInterfaces = ['php', 'java', 'c#', 'typescript'];

foreach (Set::partialIntersection(2, $staticallyTyped, $dynamicallyTyped, $supportsInterfaces) as $language) {
    print($language);
}
// c++, java, c#, go, php
```

### Partial Intersection Coercive
Итерирует [M-частичное пересечение](https://github.com/Smoren/partial-intersection-php) коллекций в режиме [приведения типов](README.md#режимы-типизации).

```Set::partialIntersectionCoercive(int $minIntersectionCount, iterable ...$iterables)```

* Если хотя бы в одной коллекции встречаются повторяющиеся элементы, работают правила пересечения [мультимножеств](https://en.wikipedia.org/wiki/Multiset).
* Если `$minIntersectionCount = 1`, работают правила объединения [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Set;

$set1 = [1, 2, 3],
$set2 = ['2', '3', 4, 5],
$set3 = [1, '2'],

foreach (Set::partialIntersectionCoercive(2, $set1, $set2, $set3) as $partiallyCommonNumber) {
    print($partiallyCommonNumber);
}
// 1, 2, 3
```

### Difference
Итерирует разность коллекций. Возвращает элементы из первой коллекции, не входящие ни в одну из остальных.

```Set::difference(iterable $a, iterable ...$iterables)```

Если хотя бы в одной коллекции встречаются повторяющиеся элементы, работают правила получения разности [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Set;

$a = [1, 2, 3, 4, 7];
$b = [2, 3, 5, 8];
$c = [1, 6, 9];

foreach (Set::difference($a, $b, $c) as $item) {
    print($item);
}
// 4, 7
```

### Difference Coercive
Итерирует разность коллекций в режиме [приведения типов](README.md#режимы-типизации).

```Set::differenceCoercive(iterable $a, iterable ...$iterables)```

Если хотя бы в одной коллекции встречаются повторяющиеся элементы, работают правила получения разности [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Set;

$a = [1, 2, 3, 4, 7];
$b = ['1', 2, 3, 5, 8];

foreach (Set::differenceCoercive($a, $b) as $item) {
    print($item);
}
// 4, 7
```

### Symmetric difference
Итерирует симметрическую разность коллекций.

```Set::symmetricDifference(iterable ...$iterables)```

Если хотя бы в одной коллекции встречаются повторяющиеся элементы, работают правила получения разности [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Set;

$a = [1, 2, 3, 4, 7];
$b = ['1', 2, 3, 5, 8];
$c = [1, 2, 3, 6, 9];

foreach (Set::symmetricDifference($a, $b, $c) as $item) {
    print($item);
}
// 1, 4, 5, 6, 7, 8, 9
```

### Symmetric difference Coercive
Итерирует симметрическую разность коллекций в режиме [приведения типов](README.md#режимы-типизации).

```Set::symmetricDifferenceCoercive(iterable ...$iterables)```

Если хотя бы в одной коллекции встречаются повторяющиеся элементы, работают правила получения разности [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Set;

$a = [1, 2, 3, 4, 7];
$b = ['1', 2, 3, 5, 8];
$c = [1, 2, 3, 6, 9];

foreach (Set::symmetricDifferenceCoercive($a, $b, $c) as $item) {
    print($item);
}
// 4, 5, 6, 7, 8, 9
```

### Union
Итерирует объединение коллекций.

```Set::union(iterable ...$iterables)```

Если хотя бы в одной коллекции встречаются повторяющиеся элементы, работают правила объединения [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Set;

$a = [1, 2, 3];
$b = [3, 4];
$c = [1, 2, 3, 6, 7];

foreach (Set::union($a, $b, $c) as $item) {
    print($item);
}
//1, 2, 3, 4, 6, 7
```

### Union Coercive
Итерирует объединение коллекций в режиме [приведения типов](README.md#режимы-типизации).

```Set::unionCoercive(iterable ...$iterables)```

Если хотя бы в одной коллекции встречаются повторяющиеся элементы, работают правила объединения [мультимножеств](https://en.wikipedia.org/wiki/Multiset).

```php
use IterTools\Set;

$a = ['1', 2, 3];
$b = [3, 4];
$c = [1, 2, 3, 6, 7];

foreach (Set::unionCoercive($a, $b, $c) as $item) {
    print($item);
}
//1, 2, 3, 4, 6, 7
```

### Duplicates
Отдаёт каждое дублирующееся значение по одному разу — в момент его второго появления.

```Set::duplicates(iterable $data, bool $strict = true)```

* Параметр `$strict` соответствует семантике сравнения в `Set::distinct`.
* Ключи исходной коллекции отбрасываются; ключи результата — последовательные, начиная с 0.

```php
use IterTools\Set;

$data = [1, 2, 1, 1, 2, 3];

foreach (Set::duplicates($data) as $value) {
    print($value);
}
// 1, 2
```

### Duplicates By
Отдаёт каждое значение, чей извлечённый ключ совпадает с уже встречавшимся, по одному разу — в момент второго появления такого ключа.

```Set::duplicatesBy(iterable $data, callable $keyFn)```

* Отдаётся первое значение, чей ключ совпал; последующие совпадения по тому же ключу не отдаются.
* Сравнение извлечённых ключей строгое.
* Ключи исходной коллекции отбрасываются; ключи результата — последовательные, начиная с 0.

```php
use IterTools\Set;

$users = [
    ['id' => 1, 'name' => 'Alice'],
    ['id' => 2, 'name' => 'Bob'],
    ['id' => 1, 'name' => 'Alicia'],
    ['id' => 3, 'name' => 'Carol'],
];

foreach (Set::duplicatesBy($users, fn ($u) => $u['id']) as $duplicate) {
    print($duplicate['name']);
}
// Alicia
```

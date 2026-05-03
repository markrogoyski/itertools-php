# Итерирование с сортировкой

[Вернуться к главной странице](README.md)

Инструменты для итерирования отсортированных коллекций.

---

### ASort
Сортирует коллекцию с сохранением ключей.

```Sort::sort(iterable $data, callable $comparator = null)```

Если `$comparator` не передан, элементы коллекции должны быть сравнимы.

```php
use IterTools\Single;
$worldPopulations = [
    'China'     => 1_439_323_776,
    'India'     => 1_380_004_385,
    'Indonesia' => 273_523_615,
    'Pakistan'  => 220_892_340,
    'USA'       => 331_002_651,
];
foreach (Sort::sort($worldPopulations) as $country => $population) {
    print("$country: $population" . \PHP_EOL);
}
// Pakistan: 220,892,340
// Indonesia: 273,523,615
// USA: 331,002,651
// India: 1,380,004,385
// China: 1,439,323,776
```

### Sort
Сортирует коллекцию.

```Sort::sort(iterable $data, callable $comparator = null)```

Если `$comparator` не передан, элементы коллекции должны быть сравнимы.

```php
use IterTools\Single;

$data = [3, 4, 5, 9, 8, 7, 1, 6, 2];

foreach (Sort::sort($data) as $datum) {
    print($datum);
}
// 1, 2, 3, 4, 5, 6, 7, 8, 9
```

### SortBy
Сортирует коллекцию по ключу, извлечённому из каждого элемента (преобразование Шварца).

```Sort::sortBy(iterable $data, callable $keyFn)```

Функция извлечения ключа вызывается ровно один раз для каждого элемента.
Исходные ключи отбрасываются (как у `Sort::sort`). Сортировка устойчивая:
элементы с равными ключами сохраняют исходный относительный порядок.

Аналог в Stream: [`Stream::sortBy()`](stream.md#sort-by).

```php
use IterTools\Sort;

$people = [
    (object)['name' => 'Alice', 'age' => 30],
    (object)['name' => 'Bob',   'age' => 20],
    (object)['name' => 'Carol', 'age' => 40],
];

foreach (Sort::sortBy($people, fn ($p) => $p->age) as $person) {
    print("{$person->name}: {$person->age}" . \PHP_EOL);
}
// Bob: 20
// Alice: 30
// Carol: 40
```

```php
use IterTools\Sort;

$words = ['banana', 'fig', 'cherry', 'apple'];

foreach (Sort::sortBy($words, fn (string $s) => \strlen($s)) as $word) {
    print($word . \PHP_EOL);
}
// fig
// apple
// banana
// cherry
```

### AsortBy
Сортирует коллекцию по ключу, извлечённому из каждого элемента (преобразование Шварца),
с сохранением ключей.

```Sort::asortBy(iterable $data, callable $keyFn)```

Функция извлечения ключа вызывается ровно один раз для каждого элемента.
Исходные ключи сохраняются (как у `Sort::asort`). Сортировка устойчивая:
элементы с равными ключами сохраняют исходный относительный порядок.

Аналог в Stream: [`Stream::asortBy()`](stream.md#asort-by).

```php
use IterTools\Sort;

$scores = [
    'Alice' => 87,
    'Bob'   => 92,
    'Carol' => 75,
];

foreach (Sort::asortBy($scores, fn (int $score) => $score) as $name => $score) {
    print("$name: $score" . \PHP_EOL);
}
// Carol: 75
// Alice: 87
// Bob: 92
```

```php
use IterTools\Sort;

$people = [
    'alice' => (object)['age' => 30],
    'bob'   => (object)['age' => 20],
    'carol' => (object)['age' => 40],
];

foreach (Sort::asortBy($people, fn ($p) => $p->age) as $key => $person) {
    print("$key => age {$person->age}" . \PHP_EOL);
}
// bob => age 20
// alice => age 30
// carol => age 40
```

### Largest
Возвращает n наибольших элементов коллекции в порядке убывания.

```Sort::largest(iterable $data, int $n, callable $keyFn = null)```

Использует ограниченную кучу размера `n` — полная сортировка входа не выполняется,
что эффективно для больших входов, когда нужны только несколько крайних значений
(`O(N log n)`).

- `$n` должно быть неотрицательным; иначе бросается `\InvalidArgumentException`.
- `$n = 0` возвращает пустой генератор без обхода входа.
- `$n` больше размера входа — возвращает весь вход, отсортированный по убыванию.
- Устойчивая: элементы с равными извлечёнными ключами сохраняются в исходном
  порядке, когда количество совпадений превышает доступные слоты, и эмитятся
  в порядке вставки среди равных.
- Политика NaN: элементы, чей ключ сравнения равен NaN, пропускаются.

Аналог в Stream: [`Stream::largest()`](stream.md#largest).

```php
use IterTools\Sort;

$data = [3, 1, 4, 1, 5, 9, 2, 6];

foreach (Sort::largest($data, 3) as $datum) {
    print($datum . \PHP_EOL);
}
// 9
// 6
// 5
```

```php
use IterTools\Sort;

$leaderboard = [
    (object)['name' => 'Alice', 'score' => 87],
    (object)['name' => 'Bob',   'score' => 92],
    (object)['name' => 'Carol', 'score' => 75],
    (object)['name' => 'Dave',  'score' => 95],
    (object)['name' => 'Eve',   'score' => 90],
];

foreach (Sort::largest($leaderboard, 3, fn ($p) => $p->score) as $player) {
    print("{$player->name}: {$player->score}" . \PHP_EOL);
}
// Dave: 95
// Bob: 92
// Eve: 90
```

### Smallest
Возвращает n наименьших элементов коллекции в порядке возрастания.

```Sort::smallest(iterable $data, int $n, callable $keyFn = null)```

Использует ограниченную кучу размера `n` — полная сортировка входа не выполняется,
что эффективно для больших входов, когда нужны только несколько крайних значений
(`O(N log n)`).

- `$n` должно быть неотрицательным; иначе бросается `\InvalidArgumentException`.
- `$n = 0` возвращает пустой генератор без обхода входа.
- `$n` больше размера входа — возвращает весь вход, отсортированный по возрастанию.
- Устойчивая: элементы с равными извлечёнными ключами сохраняются в исходном
  порядке, когда количество совпадений превышает доступные слоты, и эмитятся
  в порядке вставки среди равных.
- Политика NaN: элементы, чей ключ сравнения равен NaN, пропускаются.

Аналог в Stream: [`Stream::smallest()`](stream.md#smallest).

```php
use IterTools\Sort;

$data = [3, 1, 4, 1, 5, 9, 2, 6];

foreach (Sort::smallest($data, 3) as $datum) {
    print($datum . \PHP_EOL);
}
// 1
// 1
// 2
```

```php
use IterTools\Sort;

$requests = [
    (object)['id' => 'r1', 'durationMs' => 120],
    (object)['id' => 'r2', 'durationMs' => 50],
    (object)['id' => 'r3', 'durationMs' => 200],
    (object)['id' => 'r4', 'durationMs' => 80],
    (object)['id' => 'r5', 'durationMs' => 65],
];

foreach (Sort::smallest($requests, 3, fn ($r) => $r->durationMs) as $request) {
    print("{$request->id}: {$request->durationMs}ms" . \PHP_EOL);
}
// r2: 50ms
// r5: 65ms
// r4: 80ms
```

### Shuffle
Отдаёт элементы коллекции в случайном порядке.

```Sort::shuffle(iterable $data, ?\Random\Engine $engine = null)```

* Материализует входные данные.
* Ключи исходной коллекции отбрасываются; ключи результата — последовательные, начиная с 0.
* Опциональный параметр `$engine` позволяет задать детерминированный seed (например, `new \Random\Engine\Mt19937(42)`).

```php
use IterTools\Sort;

$deck = ['A♠', 'K♠', 'Q♠', 'J♠'];

foreach (Sort::shuffle($deck) as $card) {
    print($card . ' ');
}
// например: J♠ A♠ Q♠ K♠
```

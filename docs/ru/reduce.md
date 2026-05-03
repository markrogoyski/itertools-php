# Reduce

[Вернуться к главной странице](README.md)

Инструменты для редуцирования итерируемых коллекций к единственному значению.

---

### To Average
Возвращает среднее арифметическое элементов коллекции.

Для пустой коллекции возвращает `null`.

```Reduce::toAverage(iterable $data): float```

```php
use IterTools\Reduce;

$grades = [100, 90, 95, 85, 94];

$finalGrade = Reduce::toAverage($numbers);
// 92.8
```

### To Count
Возвращает длину данной коллекции.

```Reduce::toCount(iterable $data): int```

```php
use IterTools\Reduce;

$someIterable = ImportantThing::getCollectionAsIterable();

$length = Reduce::toCount($someIterable);
// 3
```

### To First
Возвращает первый элемент коллекции.

```Reduce::toFirst(iterable $data): mixed```

Бросает `\LengthException` если коллекция пуста.

```php
use IterTools\Reduce;

$input = [10, 20, 30];

$result = Reduce::toFirst($input);
// 10
```

### To First And Last
Возвращает первый и последний элементы коллекции.

```Reduce::toFirstAndLast(iterable $data): array{mixed, mixed}```

Бросает `\LengthException` если хранимая в потоке коллекция пуста.

```php
use IterTools\Reduce;

$input = [10, 20, 30];

$result = Reduce::toFirstAndLast($input);
// [10, 30]
```

### To First Match
Возвращает первый элемент коллекции, удовлетворяющий предикату.

```Reduce::toFirstMatch(iterable $data, callable $predicate, mixed $default = null): mixed```

- Результат предиката приводится к `bool` через `(bool)`.
- Завершает обход на первом совпадении — коллекция не потребляется полностью.
- Возвращает `$default` (по умолчанию `null`), если совпадений нет.

```php
use IterTools\Reduce;

$numbers = [1, 3, 5, 6, 7, 8];

$firstEven = Reduce::toFirstMatch($numbers, fn (int $n) => $n % 2 === 0);
// 6
```

### To First Match Index
Возвращает индекс (отсчёт от нуля) первого элемента, удовлетворяющего предикату.

```Reduce::toFirstMatchIndex(iterable $data, callable $predicate, mixed $default = null): mixed```

- Результат предиката приводится к `bool` через `(bool)`.
- Завершает обход на первом совпадении — коллекция не потребляется полностью.
- Возвращает `$default` (по умолчанию `null`), если совпадений нет.
- Позиция всегда отсчитывается от начала обхода, независимо от исходных ключей.

```php
use IterTools\Reduce;

$numbers = [10, 20, 30, 40];

$firstOver25Index = Reduce::toFirstMatchIndex($numbers, fn (int $n) => $n > 25);
// 2
```

```php
use IterTools\Reduce;

// Ленивый поиск с ранним выходом: генератор, который бросил бы исключение
// после совпадения, никогда не продвигается дальше совпадающей позиции.
$ids = (function (): \Generator {
    yield 1;
    yield 2;
    yield 3;
    throw new \RuntimeException('iterator advanced past match');
})();

$index = Reduce::toFirstMatchIndex($ids, fn (int $n) => $n === 2);
// 1
```

### To First Match Key
Возвращает ключ исходной коллекции для первого элемента, удовлетворяющего предикату.

```Reduce::toFirstMatchKey(iterable $data, callable $predicate, mixed $default = null): mixed```

- Результат предиката приводится к `bool` через `(bool)`.
- Завершает обход на первом совпадении — коллекция не потребляется полностью.
- Возвращает `$default` (по умолчанию `null`), если совпадений нет.
- Сохраняет исходный ключ (строковый для ассоциативных коллекций, целочисленный для списков).

```php
use IterTools\Reduce;

$users = ['alice' => 12, 'bob' => 17, 'carol' => 22, 'dan' => 30];

$firstAdultName = Reduce::toFirstMatchKey($users, fn (int $age) => $age >= 18);
// 'carol'
```

```php
use IterTools\Reduce;

$prices = ['usd' => 9.99, 'eur' => 8.49, 'jpy' => 1499.0];

$firstExpensiveCurrency = Reduce::toFirstMatchKey(
    $prices,
    fn (float $p) => $p > 1000,
    'none'
);
// 'jpy'
```

### To Last
Возвращает последний элемент коллекции.

```Reduce::toLast(iterable $data): mixed```

Бросает `\LengthException` если коллекция пуста.

```php
use IterTools\Reduce;

$input = [10, 20, 30];

$result = Reduce::toLast($input);
// 30
```

### To Last Match
Возвращает последний элемент коллекции, удовлетворяющий предикату.

```Reduce::toLastMatch(iterable $data, callable $predicate, mixed $default = null): mixed```

- Результат предиката приводится к `bool` через `(bool)`.
- Полностью потребляет коллекцию (без раннего выхода).
- Возвращает `$default` (по умолчанию `null`), если совпадений нет.

```php
use IterTools\Reduce;

$numbers = [1, 3, 5, 6, 7, 8, 9];

$lastEven = Reduce::toLastMatch($numbers, fn (int $n) => $n % 2 === 0);
// 8

$lastNegative = Reduce::toLastMatch($numbers, fn (int $n) => $n < 0, 'none');
// 'none'
```

### To Last Match Index
Возвращает индекс (отсчёт от нуля) последнего элемента, удовлетворяющего предикату.

```Reduce::toLastMatchIndex(iterable $data, callable $predicate, mixed $default = null): mixed```

- Результат предиката приводится к `bool` через `(bool)`.
- Полностью потребляет коллекцию.
- Возвращает `$default` (по умолчанию `null`), если совпадений нет.
- Для ассоциативных коллекций возвращает позицию (а не исходный ключ).

```php
use IterTools\Reduce;

$numbers = [10, 20, 30, 40, 5];

$lastOver25Index = Reduce::toLastMatchIndex($numbers, fn (int $n) => $n > 25);
// 3
```

### To Last Match Key
Возвращает ключ исходной коллекции для последнего элемента, удовлетворяющего предикату.

```Reduce::toLastMatchKey(iterable $data, callable $predicate, mixed $default = null): mixed```

- Результат предиката приводится к `bool` через `(bool)`.
- Полностью потребляет коллекцию.
- Возвращает `$default` (по умолчанию `null`), если совпадений нет.
- Сохраняет исходный ключ (строковый для ассоциативных коллекций, целочисленный для списков).

```php
use IterTools\Reduce;

$users = ['alice' => 12, 'bob' => 17, 'carol' => 22, 'dan' => 30];

$lastAdultName = Reduce::toLastMatchKey($users, fn (int $age) => $age >= 18);
// 'dan'
```

### To Max
Возвращает максимальный элемент коллекции.

```Reduce::toMax(iterable $data, callable $compareBy = null): mixed|null```

- Функция `$compareBy` должна возвращать сравнимое значение.
- Если аргумент `$compareBy` не передан, элементы коллекции должны быть сравнимы.
- Для пустой коллекции возвращает `null`.

```php
use IterTools\Reduce;

$numbers = [5, 3, 1, 2, 4];

$result = Reduce::toMax($numbers);
// 5

$movieRatings = [
    [
        'title' => 'Star Wars: Episode IV - A New Hope',
        'rating' => 4.6
    ],
    [
        'title' => 'Star Wars: Episode V - The Empire Strikes Back',
        'rating' => 4.8
    ],
    [
        'title' => 'Star Wars: Episode VI - Return of the Jedi',
        'rating' => 4.6
    ],
];
$compareBy = fn ($movie) => $movie['rating'];
$highestRatedMovie = Reduce::toMax($movieRatings, $compareBy);
// [
//     'title' => 'Star Wars: Episode V - The Empire Strikes Back',
//     'rating' => 4.8
// ];
```

### To Min
Возвращает минимальный элемент коллекции.

```Reduce::toMin(iterable $data, callable $compareBy = null): mixed|null```

- Функция `$compareBy` должна возвращать сравнимое значение.
- Если аргумент `$compareBy` не передан, элементы коллекции должны быть сравнимы.
- Для пустой коллекции возвращает `null`.

```php
use IterTools\Reduce;

$numbers = [5, 3, 1, 2, 4];

$result = Reduce::toMin($numbers);
// 1

$movieRatings = [
    [
        'title' => 'The Matrix',
        'rating' => 4.7
    ],
    [
        'title' => 'The Matrix Reloaded',
        'rating' => 4.3
    ],
    [
        'title' => 'The Matrix Revolutions',
        'rating' => 3.9
    ],
    [
        'title' => 'The Matrix Resurrections',
        'rating' => 2.5
    ],
];
$compareBy = fn ($movie) => $movie['rating'];
$lowestRatedMovie = Reduce::toMin($movieRatings, $compareBy);
// [
//     'title' => 'The Matrix Resurrections',
//     'rating' => 2.5
// ]
```

### To Min Max
Возвращает минимальный и максимальный элементы коллекции.

```Reduce::toMinMax(iterable $numbers, callable $compareBy = null): array```

- Функция `$compareBy` должна возвращать сравнимое значение.
- Если аргумент `$compareBy` не передан, элементы коллекции должны быть сравнимы.
- Для пустой коллекции возвращает `[null, null]`.

```php
use IterTools\Reduce;

$numbers = [1, 2, 7, -1, -2, -3];;

[$min, $max] = Reduce::toMinMax($numbers);
// [-3, 7]

$reportCard = [
    [
        'subject' => 'history',
        'grade' => 90
    ],
    [
        'subject' => 'math',
        'grade' => 98
    ],
    [
        'subject' => 'science',
        'grade' => 92
    ],
    [
        'subject' => 'english',
        'grade' => 85
    ],
    [
        'subject' => 'programming',
        'grade' => 100
    ],
];
$compareBy = fn ($class) => $class['grade'];
$bestAndWorstSubject = Reduce::toMinMax($reportCard, $compareBy);
// [
//     [
//         'subject' => 'english',
//         'grade' => 85
//     ],
//     [
//         'subject' => 'programming',
//         'grade' => 100
//     ],
// ]
```

### To Nth
Возвращает n-й элемент коллекции.

```Reduce::toNth(iterable $data, int $position): mixed```

```php
use IterTools\Reduce;

$lotrMovies = ['The Fellowship of the Ring', 'The Two Towers', 'The Return of the King'];

$rotk = Reduce::toNth($lotrMovies, 2);
// 20
```

### To Only
Возвращает единственный элемент коллекции.

```Reduce::toOnly(iterable $data): mixed```

- Бросает `\LengthException`, если коллекция пуста или содержит более одного элемента.
- Для ассоциативной коллекции с одним элементом возвращает значение (не ключ).
- Для проверки «совпадает ровно один элемент» используйте композицию `Stream::filter()->toOnly()`.

```php
use IterTools\Reduce;

$config = ['admin' => 'jane'];

$onlyAdmin = Reduce::toOnly($config);
// 'jane'
```

```php
use IterTools\Reduce;

Reduce::toOnly([]);        // бросает \LengthException
Reduce::toOnly([1, 2, 3]); // бросает \LengthException
```

### To Product
Возвращает произведение элементов коллекции.

Для пустой коллекции возвращает `null`.

```Reduce::toProduct(iterable $data): number|null```

```php
use IterTools\Reduce;

$primeFactors = [5, 2, 2];

$number = Reduce::toProduct($primeFactors);
// 20
```

### To Random Value
Возвращает случайный элемент из коллекции.

```Reduce::toRandomValue(iterable $data): mixed```

```php
use IterTools\Reduce;

$sfWakeupOptions = ['mid', 'low', 'overhead', 'throw', 'meaty'];

$wakeupOption = Reduce::toRandomValue($sfWakeupOptions);
// e.g., throw
```

### To Range
Возвращает разность максимального и минимального элементов коллекции.

```Reduce::toRange(iterable $numbers): int|float```

Для пустой коллекции возвращает `0`.

```php
use IterTools\Reduce;

$grades = [100, 90, 80, 85, 95];

$range = Reduce::toRange($numbers);
// 20
```

### To String
Преобразует коллекцию в строку, "склеивая" ее элементы.

* Значение необязательного аргумента `$separator` вставляется в качестве разделителя между элементами в строке.
* Значение необязательного аргумента `$prefix` вставляется в начало строки.
* Значение необязательного аргумента `$suffix` вставляется в конец строки.

```Reduce::toString(iterable $data, string $separator = '', string $prefix = '', string $suffix = ''): string```

```php
use IterTools\Reduce;

$words = ['IterTools', 'PHP', 'v1.0'];

$string = Reduce::toString($words);
// IterToolsPHPv1.0
$string = Reduce::toString($words, '-');
// IterTools-PHP-v1.0
$string = Reduce::toString($words, '-', 'Library: ');
// Library: IterTools-PHP-v1.0
$string = Reduce::toString($words, '-', 'Library: ', '!');
// Library: IterTools-PHP-v1.0!
```

### To Sum
Возвращает сумму элементов коллекции.

```Reduce::toSum(iterable $data): number```

```php
use IterTools\Reduce;

$parts = [10, 20, 30];

$sum = Reduce::toSum($parts);
// 60
```

### To Value
Редуцирует коллекцию до значения, вычисляемого с использованием callback-функции.

```Reduce::toValue(iterable $data, callable $reducer, mixed $initialValue): mixed```

```php
use IterTools\Reduce;

$input = [1, 2, 3, 4, 5];
$sum   = fn ($carry, $item) => $carry + $item;

$result = Reduce::toValue($input, $sum, 0);
// 15
```

### Consume
Полностью обходит коллекцию, отбрасывая значения.

```Reduce::consume(iterable $data): void```

- Полезно для принудительного выполнения «ленивого» конвейера, нужного только ради побочных эффектов.
- Ничего не возвращает.

```php
use IterTools\Reduce;
use IterTools\Single;

$log = [];

$pipeline = Single::map([1, 2, 3], function (int $n) use (&$log): int {
    $log[] = $n;
    return $n * 2;
});
// $log === []  (Single::map ленивая — пока ничего не выполнилось)

Reduce::consume($pipeline);
// $log === [1, 2, 3]
```

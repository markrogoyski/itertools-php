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

### To Count By
Сворачивает коллекцию в массив количеств, сгруппированных по значению, которое возвращает функция-ключ.

```Reduce::toCountBy(iterable $data, callable $keyFunc): array```

Функция-ключ должна возвращать `int` или `string` (единственные допустимые типы ключей массива). При любом другом типе возвращаемого значения выбрасывается `\TypeError` с указанием некорректного типа.

> Примечание: массивы PHP приводят числовые строки-ключи к целым числам — функция-ключ, возвращающая строку `"1"`, и функция-ключ, возвращающая целое `1`, объединяются в один целочисленный ключ `1` с суммарным количеством.

```php
use IterTools\Reduce;

$words = ['apple', 'pear', 'banana', 'kiwi', 'plum'];

$counts = Reduce::toCountBy($words, fn ($word) => \strlen($word));
// [5 => 1, 4 => 3, 6 => 1]
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

### To Median
Возвращает медиану коллекции.

```Reduce::toMedian(iterable $data): int|float|null```

- Для чётного количества элементов медиана равна среднему арифметическому двух средних значений; вычисление не переполняется, даже если сумма двух средних значений превышает `PHP_FLOAT_MAX`, и не теряет точность, если их размах выходит за пределы целочисленного диапазона (`toMedian([PHP_INT_MIN, PHP_INT_MAX])` равно `-0.5`, а не `0.0`). Два одинаковых средних значения дают само это значение, включая `INF`.
- Для пустой коллекции возвращает `null`.

```php
use IterTools\Reduce;

$grades = [100, 90, 95, 85, 94];

$median = Reduce::toMedian($grades);
// 94
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

### To Mode
Возвращает список мод коллекции (наиболее часто встречающихся значений).

```Reduce::toMode(iterable $data): array```

- Возвращает все значения с максимальной частотой в порядке первого появления (для коллекции без повторов возвращаются все её значения).
- Для мультимодальных данных возвращается несколько мод.
- Значения сравниваются строго, поэтому `1`, `1.0` и `'1'` считаются различными.
- Для пустой коллекции возвращает пустой массив.

```php
use IterTools\Reduce;

$votes = ['red', 'blue', 'red', 'green', 'blue', 'red'];

$modes = Reduce::toMode($votes);
// ['red']
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

### To Percentile
Возвращает значение коллекции для заданного процентиля.

```Reduce::toPercentile(iterable $data, float $percentile): int|float|null```

- Используется метод R-7 / линейной интерполяции (по умолчанию в NumPy). Процентиль `0` — это минимум, `100` — максимум.
- Аргумент `$percentile` должен находиться в диапазоне `[0, 100]`; иначе выбрасывается `\InvalidArgumentException`.
- Интерполяция не переполняется, даже если расстояние между двумя соседними значениями превышает `PHP_FLOAT_MAX`.
- Процентиль `50` для любых входных данных возвращает ровно то же, что и `toMedian`.
- Для пустой коллекции возвращает `null`.

```php
use IterTools\Reduce;

$scores = [10, 20, 30, 40, 50];

$p75 = Reduce::toPercentile($scores, 75);
// 40
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

### To Quantile
Возвращает значение коллекции для заданного квантиля.

```Reduce::toQuantile(iterable $data, float $quantile): int|float|null```

- Тонкая обёртка над `toPercentile`, принимающая квантиль в диапазоне `[0, 1]` (например, `0.25` — первый квартиль / 25-й процентиль).
- Аргумент `$quantile` должен находиться в диапазоне `[0, 1]`; иначе выбрасывается `\InvalidArgumentException`.
- Для пустой коллекции возвращает `null`.

```php
use IterTools\Reduce;

$scores = [10, 20, 30, 40, 50];

$q3 = Reduce::toQuantile($scores, 0.75);
// 40
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

### To Standard Deviation
Возвращает стандартное отклонение значений коллекции.

```Reduce::toStandardDeviation(iterable $data, bool $sample = false): float|null```

- Квадратный корень из дисперсии. По умолчанию — стандартное отклонение генеральной совокупности; при `$sample = true` — выборочное стандартное отклонение (поправка Бесселя).
- Наследует от `toVariance` однопроходность, память `O(1)` и поведение при переполнении.
- Возвращает `null` для пустой коллекции или для выборочного стандартного отклонения единственного значения. Стандартное отклонение генеральной совокупности для единственного значения равно `0.0`.
- **Случаи с `null` имеют приоритет над `NAN`**, как и в `toVariance`: `toStandardDeviation([INF], true)` возвращает `null`, а `toStandardDeviation([INF])` — `NAN`.

```php
use IterTools\Reduce;

$numbers = [2, 4, 4, 4, 5, 5, 7, 9];

$populationStdDev = Reduce::toStandardDeviation($numbers);
// 2.0

$sampleStdDev = Reduce::toStandardDeviation($numbers, true);
// 2.138...
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

### To Variance
Возвращает дисперсию значений коллекции.

```Reduce::toVariance(iterable $data, bool $sample = false): float|null```

- По умолчанию — дисперсия генеральной совокупности; при `$sample = true` — выборочная дисперсия (поправка Бесселя, деление на `N - 1`).
- Используется масштабированный онлайн-алгоритм с компенсированным текущим средним: один проход по коллекции с памятью `O(1)`, поэтому коллекция не материализуется и большие ленивые итерируемые объекты безопасны.
- Результат устойчив к порядку входных данных с точностью до ошибок округления, но **не** воспроизводится побитово: сложение чисел с плавающей точкой неассоциативно, поэтому разные порядки могут отличаться на последний ulp. Компенсированное среднее устраняет грубую зависимость от порядка при большом смещении с малым разбросом (например, `1e16, 1e16 + 2, 1e16 + 4`, где иначе ответ менялся бы на 50% в зависимости от порядка).
- Остаётся конечным всегда, когда дисперсия представима, даже если промежуточные величины уже нет: дисперсия `[-1.4e154, 1.4e154, 0]` равна `~1.31e308`, хотя дисперсия её первых двух значений (`1.96e308`) уже превышает `PHP_FLOAT_MAX`.
- **Любое неконечное значение во входных данных даёт `NAN`**, так как отклонения от бесконечного среднего — это `INF - INF`. Дисперсия, которую действительно невозможно представить, возвращается как `INF`, но никогда как отрицательное число.
- Возвращает `null` для пустой коллекции или для выборочной дисперсии единственного значения (`N - 1 = 0` не определено). Дисперсия генеральной совокупности для единственного значения равна `0.0`.
- **Случаи с `null` имеют приоритет над `NAN`**: `toVariance([INF], true)` возвращает `null`, а не `NAN`, так как по одному наблюдению выборочную дисперсию вычислить нельзя в принципе, каким бы это наблюдение ни было. Дисперсия генеральной совокупности для одного значения определена, поэтому `toVariance([INF])` возвращает `NAN`.

```php
use IterTools\Reduce;

$numbers = [1, 2, 3, 4, 5];

$populationVariance = Reduce::toVariance($numbers);
// 2.0

$sampleVariance = Reduce::toVariance($numbers, true);
// 2.5
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

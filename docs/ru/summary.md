# Саммари о коллекции

[Вернуться к главной странице](README.md)

Инструменты для получения булевых характеристик итерируемых коллекций.

---

### All Match
Возвращает истину, если для всех элементов коллекции предикат вернул истину.

```Summary::allMatch(iterable $data, callable $predicate): bool```

```php
use IterTools\Summary;

$finalFantasyNumbers = [4, 5, 6];
$isOnSuperNintendo   = fn ($ff) => $ff >= 4 && $ff <= 6;

$boolean = Summary::allMatch($finalFantasyNumbers, $isOnSuperNintendo);
// true

$isOnPlaystation = fn ($ff) => $ff >= 7 && $ff <= 9;

$boolean = Summary::allMatch($finalFantasyNumbers, $isOnPlaystation);
// false
```

### All Unique
Возвращает истину, все элементы коллекции уникальны.

```Summary::allUnique(iterable $data, bool $strict = true): bool```

По умолчанию работает в [режиме строгой типизации](README.md#режимы-типизации). Установите параметр `$strict` в `false` для работы в режиме приведения типов.

```php
use IterTools\Summary;

$items = ['fingerprints', 'snowflakes', 'eyes', 'DNA']

$boolean = Summary::allUnique($items);
// true
```

### Any Match
Возвращает истину, если хотя бы для одного элемента коллекции предикат вернул истину.

```Summary::anyMatch(iterable $data, callable $predicate): bool```

```php
use IterTools\Summary;

$answers          = ['fish', 'towel', 42, "don't panic"];
$isUltimateAnswer = fn ($a) => a == 42;

$boolean = Summary::anyMatch($answers, $isUltimateAnswer);
// true
```

### At Least N
Возвращает истину, если предикат возвращает истину как минимум для N элементов коллекции.

- Предикат является необязательным аргументом.
- По умолчанию предикат выполняет приведение значения элемента коллекции к типу `bool`.
- Прерывает итерацию сразу после того, как количество совпадений достигнет N.
- При `n <= 0` всегда возвращает истину.

```Summary::atLeastN(iterable $data, int $n, callable $predicate = null): bool```

```php
use IterTools\Summary;

$grades         = [45, 50, 61, 72, 85];
$isPassingGrade = fn ($grade) => $grade >= 70;

$boolean = Summary::atLeastN($grades, 2, $isPassingGrade);
// true

$boolean = Summary::atLeastN($grades, 3, $isPassingGrade);
// false
```

### At Most N
Возвращает истину, если предикат возвращает истину не более чем для N элементов коллекции.

- Предикат является необязательным аргументом.
- По умолчанию предикат выполняет приведение значения элемента коллекции к типу `bool`.
- Прерывает итерацию сразу после того, как количество совпадений превысит N.
- При `n < 0` всегда возвращает ложь.

```Summary::atMostN(iterable $data, int $n, callable $predicate = null): bool```

```php
use IterTools\Summary;

$attempts  = [false, false, true, false];
$isFailure = fn ($attempt) => $attempt === false;

$boolean = Summary::atMostN($attempts, 3, $isFailure);
// true

$boolean = Summary::atMostN($attempts, 2, $isFailure);
// false
```

### Are Permutations
Возвращает истину, если коллекции являются перестановками друг друга.

```Summary::arePermutations(iterable ...$iterables): bool```

```php
use IterTools\Summary;
$iter = ['i', 't', 'e', 'r'];
$rite = ['r', 'i', 't', 'e'];
$reit = ['r', 'e', 'i', 't'];
$tier = ['t', 'i', 'e', 'r'];
$tire = ['t', 'i', 'r', 'e'];
$trie = ['t', 'r', 'i', 'e'];
$boolean = Summary::arePermutations($iter, $rite, $reit, $tier, $tire, $trie);
// true
```

### Are Permutations Coercive
Возвращает истину, если коллекции являются перестановками друг друга
(в режиме [приведения типов](README.md#режимы-типизации)).

```Summary::arePermutationsCoercive(iterable ...$iterables): bool```

```php
use IterTools\Summary;
$set1 = [1, 2.0, '3'];
$set2 = [2.0, '1', 3];
$set3 = [3, 2, 1];
$boolean = Summary::arePermutationsCoercive($set1, $set2, $set3);
// true
```

### Contains
Возвращает истину, если коллекция содержит искомое значение, при [строгом сравнении типов](README.md#режимы-типизации).

```Summary::contains(iterable $data, mixed $needle): bool```

- Скаляры сравниваются строго по типу (`1` не равно `'1'`; `0` не равно `false`).
- Для объектов совпадает только один и тот же экземпляр.
- Массивы сравниваются через `===`.
- `NaN` никогда не совпадает с `NaN` (так как `NaN !== NaN`).
- Обрывает итерацию при первом совпадении.

```php
use IterTools\Summary;

$primes = [2, 3, 5, 7, 11, 13];

$boolean = Summary::contains($primes, 7);
// true

$boolean = Summary::contains($primes, 4);
// false

$boolean = Summary::contains($primes, '7');
// false (строгое сравнение)
```

### Contains Coercive
Возвращает истину, если коллекция содержит искомое значение, в режиме [приведения типов](README.md#режимы-типизации).

```Summary::containsCoercive(iterable $data, mixed $needle): bool```

- Скаляры сравниваются нестрого по значению (`1` совпадает с `'1'`; `0` совпадает с `false`; `'1e2'` совпадает с `100`).
- Объекты сравниваются по сериализованному значению (бросает `\InvalidArgumentException`, если искомое значение или элемент коллекции не сериализуется).
- Массивы сравниваются по сериализованному значению.
- `NaN` совпадает с `NaN` (согласованно с другими операциями приведения типов в библиотеке).
- Обрывает итерацию при первом совпадении: несериализуемый элемент встретится только при отсутствии совпадений до него.

```php
use IterTools\Summary;

$primes = [2, 3, 5, 7, 11, 13];

$boolean = Summary::containsCoercive($primes, '7');
// true (приведение типов)

$boolean = Summary::containsCoercive([100, 200, 300], '1e2');
// true
```

### Ends With
Возвращает истину, если коллекция заканчивается заданным суффиксом (при [строгом сравнении типов](README.md#режимы-типизации)).

- Сравнение значений идёт попарно; ключи игнорируются.
- Пустой суффикс возвращает истину без потребления исходной коллекции.
- И исходная коллекция, и суффикс должны быть конечными.

```Summary::endsWith(iterable $data, iterable $suffix): bool```

```php
use IterTools\Summary;

$path = ['var', 'log', 'nginx', 'access.log'];

$boolean = Summary::endsWith($path, ['access.log']);
// true

$boolean = Summary::endsWith($path, ['nginx', 'access.log']);
// true

$boolean = Summary::endsWith($path, ['error.log']);
// false
```

### Ends With Coercive
Возвращает истину, если коллекция заканчивается заданным суффиксом (в режиме [приведения типов](README.md#режимы-типизации)).

- Сравнение значений идёт попарно; ключи игнорируются.
- Пустой суффикс возвращает истину без потребления исходной коллекции.
- И исходная коллекция, и суффикс должны быть конечными.
- Нестрогое сравнение значений:
  - скаляры: сравниваются нестрого по значению (`1` совпадает с `'1'`, `0` совпадает с `false`)
  - объекты: сравниваются по сериализованному значению (бросает `\InvalidArgumentException`, если значение не сериализуется)
  - массивы: сравниваются по сериализованному значению
  - `NaN` совпадает с `NaN`

```Summary::endsWithCoercive(iterable $data, iterable $suffix): bool```

```php
use IterTools\Summary;

$digits = [1, 2, 3];

$boolean = Summary::endsWithCoercive($digits, ['2', '3']);
// true (приведение типов)

$boolean = Summary::endsWith($digits, ['2', '3']);
// false (строгое сравнение)
```

### Exactly N
Истинно, если предикат возвращает истину в точности для N элементов.

- Предикат является необязательным аргументом.
- По умолчанию предикат выполняет приведение значения элемента коллекции к типу `bool`.

```Summary::exactlyN(iterable $data, int $n, callable $predicate): bool```

```php
use IterTools\Summary;

$twoTruthsAndALie = [true, true, false];
$n                = 2;

$boolean = Summary::exactlyN($twoTruthsAndALie, $n);
// true

$ages      = [18, 21, 24, 54];
$n         = 4;
$predicate = fn ($age) => $age >= 21;

$boolean = Summary::isSorted($ages, $n, $predicate);
// false
```

### Is Partitioned
Возвращает истину, если все истинные элементы находятся в коллекции перед ложными (истинность определяет предикат).

- Возвращает истину для пустой коллекции и для коллекции с одним элементом.
- Если предикат не был передан, истинность элемента получается через приведение его значения к булевому типу.

```Summary::isPartitioned(iterable $data, callable $predicate = null): bool```

```php
use IterTools\Summary;
$numbers          = [0, 2, 4, 1, 3, 5];
$evensBeforeOdds = fn ($item) => $item % 2 === 0;
$boolean = Summary::isPartitioned($numbers, $evensBeforeOdds);
```

### Is Empty
Возвращает истину, если данная коллекция пуста.

```Summary::isEmpty(iterable $data): bool```

```php
use IterTools\Summary;

$data = []

$boolean = Summary::isEmpty($data);
// true
```

### Is Sorted
Возвращает истину, если коллекция отсортирована в прямом порядке.

- Элементы должны быть сравнимы.
- Для пустой коллекции или коллекции из одного элемента всегда возвращает истину.

```Summary::isSorted(iterable $data): bool```

```php
use IterTools\Summary;

$numbers = [1, 2, 3, 4, 5];

$boolean = Summary::isSorted($numbers);
// true

$numbers = [3, 2, 3, 4, 5];

$boolean = Summary::isSorted($numbers);
// false
```

### Is Reversed
Возвращает истину, если коллекция отсортирована в обратном порядке.

- Элементы коллекции должны быть сравнимы.
- Для пустой коллекции или коллекции из одного элемента всегда возвращает истину.

```Summary::isReversed(iterable $data): bool```

```php
use IterTools\Summary;

$numbers = [5, 4, 3, 2, 1];

$boolean = Summary::isReversed($numbers);
// true

$numbers = [1, 4, 3, 2, 1];

$boolean = Summary::isReversed($numbers);
// false
```

### None Match
Возвращает истину, если для всех элементов коллекции предикат вернул ложь.

```Summary::noneMatch(iterable $data, callable $predicate): bool```

```php
use IterTools\Summary;

$grades         = [45, 50, 61, 0];
$isPassingGrade = fn ($grade) => $grade >= 70;

$boolean = Summary::noneMatch($grades, $isPassingGrade);
// true
```

### Same
Истинно, если данные коллекции одинаковы.

Если в метод передать одну коллекцию или ни одной, он вернет истину.

```Summary::same(iterable ...$iterables): bool```

```php
use IterTools\Summary;

$cocaColaIngredients = ['carbonated water', 'sugar', 'caramel color', 'phosphoric acid'];
$pepsiIngredients    = ['carbonated water', 'sugar', 'caramel color', 'phosphoric acid'];

$boolean = Summary::same($cocaColaIngredients, $pepsiIngredients);
// true

$cocaColaIngredients = ['carbonated water', 'sugar', 'caramel color', 'phosphoric acid'];
$spriteIngredients   = ['carbonated water', 'sugar', 'citric acid', 'lemon lime flavorings'];

$boolean = Summary::same($cocaColaIngredients, $spriteIngredients);
// false
```

### Same Count
Истинно, если данные коллекции имеют одинаковую длину.

Если в метод передать одну коллекцию или ни одной, он вернет истину.

```Summary::sameCount(iterable ...$iterables): bool```

```php
use IterTools\Summary;

$prequels  = ['Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith'];
$originals = ['A New Hope', 'Empire Strikes Back', 'Return of the Jedi'];
$sequels   = ['The Force Awakens', 'The Last Jedi', 'The Rise of Skywalker'];

$boolean = Summary::sameCount($prequels, $originals, $sequels);
// true

$batmanMovies = ['Batman Begins', 'The Dark Knight', 'The Dark Knight Rises'];
$matrixMovies = ['The Matrix', 'The Matrix Reloaded', 'The Matrix Revolutions', 'The Matrix Resurrections'];

$result = Summary::sameCount($batmanMovies, $matrixMovies);
// false
```

### Starts With
Возвращает истину, если коллекция начинается с заданного префикса (при [строгом сравнении типов](README.md#режимы-типизации)).

- Сравнение значений идёт попарно; ключи игнорируются.
- Пустой префикс возвращает истину без потребления исходной коллекции.

```Summary::startsWith(iterable $data, iterable $prefix): bool```

```php
use IterTools\Summary;

$path = ['var', 'log', 'nginx', 'access.log'];

$boolean = Summary::startsWith($path, ['var']);
// true

$boolean = Summary::startsWith($path, ['var', 'log']);
// true

$boolean = Summary::startsWith($path, ['etc']);
// false
```

### Starts With Coercive
Возвращает истину, если коллекция начинается с заданного префикса (в режиме [приведения типов](README.md#режимы-типизации)).

- Сравнение значений идёт попарно; ключи игнорируются.
- Пустой префикс возвращает истину без потребления исходной коллекции.
- Нестрогое сравнение значений:
  - скаляры: сравниваются нестрого по значению (`1` совпадает с `'1'`, `0` совпадает с `false`)
  - объекты: сравниваются по сериализованному значению (бросает `\InvalidArgumentException`, если значение не сериализуется)
  - массивы: сравниваются по сериализованному значению
  - `NaN` совпадает с `NaN`

```Summary::startsWithCoercive(iterable $data, iterable $prefix): bool```

```php
use IterTools\Summary;

$digits = [1, 2, 3];

$boolean = Summary::startsWithCoercive($digits, ['1', '2']);
// true (приведение типов)

$boolean = Summary::startsWith($digits, ['1', '2']);
// false (строгое сравнение)
```

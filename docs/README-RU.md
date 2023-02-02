![MathPHP Logo](https://github.com/markrogoyski/itertools-php/blob/main/docs/image/IterToolsLogo.png?raw=true)

### IterTools PHP — инструментарий для работы с итерируемыми сущностями

Спроектирован для PHP аналогично itertools в Python.

[![Coverage Status](https://coveralls.io/repos/github/markrogoyski/itertools-php/badge.svg?branch=main)](https://coveralls.io/github/markrogoyski/itertools-php?branch=main)
[![License](https://poser.pugx.org/markrogoyski/math-php/license)](https://packagist.org/packages/markrogoyski/itertools-php)

### Функционал

IterTools поддерживает два вида конструкций для итерирования:

* Итерирование в цикле
* Итерирование в потоке цепочечных вызовов

**Пример итерирования в цикле**

```php
foreach (Multi::zip(['a', 'b'], [1, 2]) as [$letter, $number]) {
    print($letter . $number);  // a1, b2
}
```

**Пример итерирования в потоке цепочечных вызовов**

```php
$result = Stream::of([1, 1, 2, 2, 3, 4, 5])
    ->distinct()                     // [1, 2, 3, 4, 5]
    ->map(fn ($x) => $x**2)          // [1, 4, 9, 16, 25]
    ->filterTrue(fn ($x) => $x < 10) // [1, 4, 9]
    ->toSum();                       // 14
```

Методы библиотеки гарантировано работают с любыми `iterable` сущностями:
* `array`
* `Iterator`
* `Generator`
* `Traversable`

Краткий справочник
-----------

### Инструменты для итерирования в циклах

#### Итерирование нескольких коллекций
| Метод                       | Описание                                                                                    | Пример кода                         |
|-----------------------------|---------------------------------------------------------------------------------------------|-------------------------------------|
| [`chain`](#Chain)           | Последовательно итерирует коллекции                                                         | `Multi::chain($list1, $list2)`      |
| [`zip`](#Zip)               | Параллельно итерирует коллекции, пока не закончится самый короткий итератор                 | `Multi::zip($list1, $list2)`        |
| [`zipLongest`](#ZipLongest) | Параллельно итерирует коллекции, пока не закончится самый длинный итератор                  | `Multi::zipLongest($list1, $list2)` |
| [`zipEqual`](#ZipEqual)     | Параллельно итерирует коллекции одного размера, в случае разных размеров бросает исключение | `Multi::zipEqual($list1, $list2)`   |

#### Итерирование одной коллекции
| Метод                                    | Описание                                                                  | Пример кода                                                 |
|------------------------------------------|---------------------------------------------------------------------------|-------------------------------------------------------------|
| [`chunkwise`](#Chunkwise)                | Итерирует коллекцию, разбитую на чанки                                    | `Single::chunkwise($data, $chunkSize)`                      |
| [`chunkwiseOverlap`](#Chunkwise-Overlap) | Итерирует коллекцию, разбитую на взаимонакладывающиеся чанки              | `Single::chunkwiseOverlap($data, $chunkSize, $overlapSize)` |
| [`compress`](#Compress)                  | Отфильтровывает невыбранные элементы                                      | `Single::compress($data, $selectors)`                       |
| [`dropWhile`](#Drop-While)               | Пропускает элементы, пока предикат возвращает истину                      | `Single::dropWhile($data, $predicate)`                      |
| [`filterFalse`](#Filter-False)           | Возвращает только те элементы, для которых предикат возвращает ложь       | `Single::filterTrue($data, $predicate)`                     |
| [`filterTrue`](#Filter-True)             | Возвращает только те элементы, для которых предикат возвращает истину     | `Single::filterFalse($data, $predicate)`                    |
| [`groupBy`](#Group-By)                   | Группирует элементы коллекции                                             | `Single::groupBy($data, $groupKeyFunction)`                 |
| [`limit`](#Limit)                        | Ограничивает итерирование коллекции заданным максимальным числом итераций | `Single::limit($data, $limit)`                              |
| [`map`](#Map)                            | Отображение коллекции с использованием callback-функции                   | `Single::map($data, $function)`                             |
| [`pairwise`](#Pairwise)                  | Итерирует коллекцию попарно (с наложением)                                | `Single::pairwise($data)`                                   |
| [`repeat`](#Repeat)                      | Повторяет данное значние заданное число раз                               | `Single::repeat($item, $repetitions)`                       |
| [`string`](#String)                      | Итерирует строку посимвольно                                              | `Single::string($string)`                                   |
| [`takeWhile`](#Take-While)               | Отдает элементы, пока предикат возвращает истину                          | `Single::takeWhile($data, $predicate)`                      |

#### Бесконечное итерирование
| Метод                        | Описание                                             | Пример кода                      |
|------------------------------|------------------------------------------------------|----------------------------------|
| [`count`](#Count)            | Бесконечно перебирает последовательность целых чисел | `Infinite::count($start, $step)` |
| [`cycle`](#Cycle)            | Бесконечно зацикливает перебор коллекции             | `Infinite::cycle($collection)`   |
| [`repeat`](#Repeat-Infinite) | Бесконечно повторяет данное значение                 | `Infinite::repeat($item)`        |

#### Итерирование случайных значений
| Метод                                     | Описание                                 | Пример кода                                |
|-------------------------------------------|------------------------------------------|--------------------------------------------|
| [`choice`](#Choice)                       | Случайные выборы вариантов из списка     | `Random::choice($list, $repetitions)`      |
| [`coinFlip`](#CoinFlip)                   | Случайные броски монеты (0 или 1)        | `Random::coinFlip($repetitions)`           |
| [`number`](#Number)                       | Случайные целые числа                    | `Random::number($min, $max, $repetitions)` |
| [`percentage`](#Percentage)               | Случайные вещественные числа между 0 и 1 | `Random::percentage($repetitions)`         |
| [`rockPaperScissors`](#RockPaperScissors) | Случайный выбор "камень-ножницы-бумага"  | `Random::rockPaperScissors($repetitions)`  |

#### Математическое итерирование
| Метод                                      | Описание                            | Пример кода                                        |
|--------------------------------------------|-------------------------------------|----------------------------------------------------|
| [`runningAverage`](#Running-Average)       | Накопление среднего арифметического | `Math::runningAverage($numbers, $initialValue)`    |
| [`runningDifference`](#Running-Difference) | Накопление разности                 | `Math::runningDifference($numbers, $initialValue)` |
| [`runningMax`](#Running-Max)               | Поиск максимального значения        | `Math::runningMax($numbers, $initialValue)`        |
| [`runningMin`](#Running-Min)               | Поиск минимального значения         | `Math::runningMin($numbers, $initialValue)`        |
| [`runningProduct`](#Running-Product)       | Накопление произведения             | `Math::runningProduct($numbers, $initialValue)`    |
| [`runningTotal`](#Running-Total)           | Накопление суммы                    | `Math::runningTotal($numbers, $initialValue)`      |

#### Итерирование множеств и мультимножеств
| Метод                                                           | Описание                                                              | Пример кода                                                  |
|-----------------------------------------------------------------|-----------------------------------------------------------------------|--------------------------------------------------------------|
| [`distinct`](#Distinct)                                         | Фильтрует коллекцию, сохраняя только уникальные значения              | `Set::distinct($data)`                                       |
| [`intersection`](#Intersection)                                 | Пересечение нескольких коллекций                                      | `Set::intersection(...$iterables)`                           |
| [`intersectionCoercive`](#Intersection-Coercive)                | Пересечение нескольких коллекций в режиме приведения типов            | `Set::intersectionCoercive(...$iterables)`                   |
| [`partialIntersection`](#Partial-Intersection)                  | Частичное пересечение нескольких коллекций                            | `Set::partialIntersection($minCount, ...$iterables)`         |
| [`partialIntersectionCoercive`](#Partial-Intersection-Coercive) | Частичное пересечение нескольких коллекций в режиме приведения типов  | `Set::partialIntersectionCoercive($minCount, ...$iterables)` |
| [`symmetricDifference`](#Symmetric-Difference)                  | Симметрическая разница нескольких коллекций                           | `Set::symmetricDifference(...$iterables)`                    |
| [`symmetricDifferenceCoercive`](#Symmetric-Difference-Coercive) | Симметрическая разница нескольких коллекций в режиме приведения типов | `Set::symmetricDifferenceCoercive(...$iterables)`            |

#### Саммари о коллекции
| Метод                        | Описание                                                                       | Пример кода                                |
|------------------------------|--------------------------------------------------------------------------------|--------------------------------------------|
| [`allMatch`](#All-Match)     | Истинно, если предикат возвращает истину для всех элементов коллекции          | `Summary::allMatch($data, $predicate)`     |
| [`anyMatch`](#Any-Match)     | Истинно, если предикат возвращает истину хотя бы для одного элемента коллекции | `Summary::anyMatch($data, $predicate)`     |
| [`exactlyN`](#Exactly-N)     | Истинно, если предикат возвращает истину в точности для N элементов            | `Summary::exactlyN($data, $n, $predicate)` |
| [`isSorted`](#Is-Sorted)     | Истинно, если коллекция отсортирована в прямом порядке                         | `Summary::isSorted($data)`                 |
| [`isReversed`](#Is-Reversed) | Истинно, если коллекция отсортирована в обратном порядке                       | `Summary::isReversed($data)`               |
| [`noneMatch`](#None-Match)   | Истинно, если предикат возвращает ложь для всех элементов коллекции            | `Summary::noneMatch($data, $predicate)`    |
| [`same`](#Same)              | Истинно, если данные коллекции одинаковы                                       | `Summary::same(...$iterables)`             |
| [`sameCount`](#Same-Count)   | Истинно, если данные коллекции имеют одинаковую длину                          | `Summary::sameCount(...$iterables)`        |

#### Редуцирование
| Метод                      | Описание                                                                            | Пример кода                                                   |
|----------------------------|-------------------------------------------------------------------------------------|---------------------------------------------------------------|
| [`toAverage`](#To-Average) | Среднее арифметическое элементов коллекции                                          | `Reduce::toAverage($numbers)`                                 |
| [`toCount`](#To-Count)     | Длина коллекции                                                                     | `Reduce::toCount($data)`                                      |
| [`toMax`](#To-Max)         | Максимальный элемент коллекции                                                      | `Reduce::toMax($numbers, [$comparator])`                      |
| [`toMin`](#To-Min)         | Минимальный элемент коллекции                                                       | `Reduce::toMin($numbers, [$comparator])`                      |
| [`toMinMax`](#To-Min-Max)  | Минимальный и максимальный элемент коллекции                                        | `Reduce::toMinMax($numbers, [$comparator])`                   |
| [`toProduct`](#To-Product) | Произведение элементов коллекции                                                    | `Reduce::toProduct($numbers)`                                 |
| [`toRange`](#To-Range)     | Разница между максимальным и минимальным элементами коллекции                       | `Reduce::toRange($numbers)`                                   |
| [`toString`](#To-String)   | Преобразование коллекции в строку                                                   | `Reduce::toString($data, [$separator], [$prefix], [$suffix])` |
| [`toSum`](#To-Sum)         | Сумма элементов коллекции                                                           | `Reduce::toSum($numbers)`                                     |
| [`toValue`](#To-Value)     | Редуцирование коллекции до значения, вычисляемого с использованием callback-функции | `Reduce::toValue($data, $reducer, $initialValue)`             |

### Цепочечный вызов итераторов
#### Фабричные методы
| Источник                                         | Описание                                                                                               | Пример кода                                         |
|--------------------------------------------------|--------------------------------------------------------------------------------------------------------|-----------------------------------------------------|
| [`of`](#Of)                                      | Создает обертку для цепочечных вызовов из данной коллекции                                             | `Stream::of($iterable)`                             |
| [`ofCoinFlips`](#Of-Coin-Flips)                  | Создает обертку для цепочечных вызовов из бесконечных случайных бросков монеты                         | `Stream::ofCoinFlips($repetitions)`                 |
| [`ofEmpty`](#Of-Empty)                           | Создает обертку для цепочечных вызовов из пустой коллекции                                             | `Stream::ofEmpty()`                                 |
| [`ofRandomChoice`](#Of-Random-Choice)            | Создает обертку для цепочечных вызовов из бесконечных случайных выборов элемента из списка             | `Stream::ofRandomChoice($items, $repetitions)`      |
| [`ofRandomNumbers`](#Of-Random-Numbers)          | Создает обертку для цепочечных вызовов из бесконечного набора случайных целых чисел                    | `Stream::ofRandomNumbers($min, $max, $repetitions)` |
| [`ofRandomPercentage`](#Of-Random-Percentage)    | Создает обертку для цепочечных вызовов из бесконечного набора случайных вещественных чисел между 0 и 1 | `Stream::ofRandomPercentage($repetitions)`          |
| [`ofRockPaperScissors`](#Of-Rock-Paper-Scissors) | Создает обертку для цепочечных вызовов из бесконечных случайных выборов "камень-ножницы-бумага"        | `Stream::ofRockPaperScissors($repetitions)`         |

#### Цепочечные операции
| Операция                                                                  | Описание                                                                                                     | Пример кода                                                                       |
|---------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------|
| [`chainWith`](#Chain-With)                                                | Добавляет в конец итератора другие коллекции для последовательного итерирования                              | `$stream->chainWith(...$iterables)`                                               |
| [`compress`](#Compress-1)                                                 | Отфильтровывает из коллекции элементы, которые не выбраны                                                    | `$stream->compress($selectors)`                                                   |
| [`chunkwise`](#Chunkwise-1)                                               | Итерирует коллекцию с разбиением по чанкам                                                                   | `$stream->chunkwise($chunkSize)`                                                  |
| [`chunkwiseOverlap`](#Chunkwise-Overlap-1)                                | Итерирует коллекцию с разбиением по взаимонакладывающимся чанкам                                             | `$stream->chunkwiseOverlap($chunkSize, $overlap)`                                 |
| [`distinct`](#Distinct-1)                                                 | Фильтрует коллекцию, сохраняя только уникальные значения                                                     | `$stream->distinct($strict)`                                                      |
| [`dropWhile`](#Drop-While-1)                                              | Пропускает элементы из коллекции, пока предикат возвращает ложь                                              | `$stream->dropWhile($predicate)`                                                  |
| [`filterTrue`](#Filter-True-1)                                            | Возвращает из коллекции только те элементы, для которых предикат возвращает истину                           | `$stream->filterTrue($predicate)`                                                 |
| [`filterFalse`](#Filter-False-1)                                          | Возвращает из коллекции только те элементы, для которых предикат возвращает ложь                             | `$stream->filterFalse($predicate)`                                                |
| [`groupBy`](#Group-By-1)                                                  | Группирует элементы из коллекции по заданному правилу                                                        | `$stream->groupBy($groupKeyFunction)`                                             |
| [`infiniteCycle`](#Infinite-Cycle)                                        | Бесконечно зацикливает перебор коллекции                                                                     | `$stream->infiniteCycle()`                                                        |
| [`intersectionWith`](#Intersection-With)                                  | Возвращает пересечение хранимой коллекции с другими коллекциями                                              | `$stream->intersectionWith(...$iterables)`                                        |
| [`intersection CoerciveWith`](#Intersection-Coercive-With)                | Возвращает пересечение хранимой коллекции с другими коллекциями (в режиме приведения типов)                  | `$stream->intersectionCoerciveWith(...$iterables)`                                |
| [`limit`](#Limit-1)                                                       | Ограничивает итерирование коллекции заданным максимальным числом итераций                                    | `$stream->limit($limit)`                                                          |
| [`map`](#Map-1)                                                           | Отображение коллекции с использованием callback-функции                                                      | `$stream->map($function)`                                                         |
| [`pairwise`](#Pairwise-1)                                                 | Итерирует коллекцию попарно (с наложением)                                                                   | `$stream->pairwise()`                                                             |
| [`partialIntersectionWith`](#Partial-Intersection-With)                   | Возвращает частичное пересечение хранимой коллекции с другими коллекциями                                    | `$stream->partialIntersectionWith( $minIntersectionCount, ...$iterables)`         |
| [`partialIntersection CoerciveWith`](#Partial-Intersection-Coercive-With) | Возвращает частичное пересечение хранимой коллекции с другими коллекциями (в режиме приведения типов)        | `$stream->partialIntersectionCoerciveWith( $minIntersectionCount, ...$iterables)` |
| [`runningAverage`](#Running-Average-1)                                    | Накопление среднего арифметического элементов коллекции                                                      | `$stream->runningAverage($initialValue)`                                          |
| [`runningDifference`](#Running-Difference-1)                              | Накопление разности элементов коллекции                                                                      | `$stream->runningDifference($initialValue)`                                       |
| [`runningMax`](#Running-Max-1)                                            | Поиск максимального значения из коллекции                                                                    | `$stream->runningMax($initialValue)`                                              |
| [`runningMin`](#Running-Min-1)                                            | Поиск минимального значения из коллекции                                                                     | `$stream->runningMin($initialValue)`                                              |
| [`runningProduct`](#Running-Product-1)                                    | Накопление произведения элементов коллекции                                                                  | `$stream->runningProduct($initialValue)`                                          |
| [`runningTotal`](#Running-Total-1)                                        | Накопление суммы элементов коллекции                                                                         | `$stream->runningTotal($initialValue)`                                            |
| [`symmetricDifferenceWith`](#Symmetric-Difference-With)                   | Возвращает симметрическую разность хранимой коллекции с другими коллекциями                                  | `$this->symmetricDifferenceWith(...$iterables)`                                   |
| [`symmetricDifference CoerciveWith`](#Symmetric-Difference-Coercive-With) | Возвращает симметрическую разность хранимой коллекции с другими коллекциями (в режиме приведения типов)      | `$this->symmetricDifferenceCoerciveWith( ...$iterables)`                          |
| [`takeWhile`](#Take-While-1)                                              | Отдает элементы из коллекции, пока предикат возвращает истину                                                | `$stream->takeWhile($predicate)`                                                  |
| [`zipWith`](#Zip-With)                                                    | Параллельно итерирует коллекцию вместе с другими, пока не закончится самый короткий итератор                 | `$stream->zipWith(...$iterables)`                                                 |
| [`zipLongestWith`](#Zip-Longest-With)                                     | Параллельно итерирует коллекцию вместе с другими, пока не закончится самый длинный итератор                  | `$stream->zipLongestWith(...$iterables)`                                          |
| [`zipEqualWith`](#Zip-Equal-With)                                         | Параллельно итерирует коллекцию вместе с другими одного размера, в случае разных размеров бросает исключение | `$stream->zipEqualWith(...$iterables)`                                            |

#### Завершающие операции
##### Саммари о коллекции
| Операция                            | Описание                                                                       | Пример кода                             |
|-------------------------------------|--------------------------------------------------------------------------------|-----------------------------------------|
| [`allMatch`](#All-Match-1)          | Истинно, если предикат возвращает истину для всех элементов коллекции          | `$stream->allMatch($predicate)`         |
| [`anyMatch`](#Any-Match-1)          | Истинно, если предикат возвращает истину хотя бы для одного элемента коллекции | `$stream->anyMatch($predicate)`         |
| [`exactlyN`](#Exactly-N-1)          | Returns true if exactly n items are true according to predicate                | `$stream->exactlyN($n, $predicate)`     |
| [`isSorted`](#Is-Sorted-1)          | Истинно, если коллекция отсортирована в прямом порядке                         | `$stream->isSorted()`                   |
| [`isReversed`](#Is-Reversed-1)      | Истинно, если коллекция отсортирована в обратном порядке                       | `$stream->isReversed()`                 |
| [`noneMatch`](#None-Match-1)        | Истинно, если предикат возвращает ложь для всех элементов коллекции            | `$stream->noneMatch($predicate)`        |
| [`sameWith`](#Same-With)            | Истинно, если данные коллекции одинаковы                                       | `$stream->sameWith(...$iterables)`      |
| [`sameCountWith`](#Same-Count-With) | Истинно, если данные коллекции имеют одинаковую длину                          | `$stream->sameCountWith(...$iterables)` |

##### Редуцирование
| Terminal Operation             | Description                                                         | Code Snippet                                            |
|--------------------------------|---------------------------------------------------------------------|---------------------------------------------------------|
| [`toAverage`](#To-Average-1)   | Среднее арфиметическое элементов коллекции                          | `$stream->toAverage()`                                  |
| [`toCount`](#To-Count-1)       | Длина коллекции                                                     | `$stream->toCount()`                                    |
| [`toMax`](#To-Max-1)           | Максимальное значение из элементов коллекции                        | `$stream->toMax([$comparator])`                         |
| [`toMin`](#To-Min-1)           | Минимальное значение из элементов коллекции                         | `$stream->toMin([$comparator])`                         |
| [`toMinMax`](#To-Min-Max-1)    | Минимальное и максимальное значения из элементов коллекции          | `$stream->toMinMax([$comparator])`                      |
| [`toProduct`](#To-Product-1)   | Произведение элементов коллекции                                    | `$stream->toProduct()`                                  |
| [`toString`](#To-String-1)     | Преобразование коллекции в строку                                   | `$stream->toString([$separator], [$prefix], [$suffix])` |
| [`toSum`](#To-Sum-1)           | Сумма элементов коллекции                                           | `$stream->toSum()`                                      |
| [`toRange`](#To-Range-1)       | Разница между максимальным и минимальным элементами коллекции       | `$stream->toRange()`                                    |
| [`toValue`](#To-Value-1)       | Редуцирование коллекции до значения, вычисляемого callback-функцией | `$stream->toValue($reducer, $initialValue)`             |

##### Операции конвертации
| Terminal Operation             | Description                              | Code Snippet                                            |
|--------------------------------|------------------------------------------|---------------------------------------------------------|
| [`toArray`](#To-Array)         | Возвращает массив из элементов коллекции | `$stream->toArray()`                                    |

##### Операции с побочными эффектами
| Terminal Operation              | Description                                              | Code Snippet                                          |
|---------------------------------|----------------------------------------------------------|-------------------------------------------------------|
| [`callForEach`](#Call-For-Each) | Вызывает callback-функцию для каждого элемента коллекции | `$stream->callForEach($function)`                     |
| [`print`](#Print)               | `print` каждого элемента коллекции                       | `$stream->print([$separator], [$prefix], [$suffix])`  |
| [`printLn`](#Print-Line)        | `print` каждого элемента коллекции с новой строки        | `$stream->printLn()`                                  |
| [`printR`](#Print-R)            | `print_r` каждого элемента коллекции                     | `$stream->printR()`                                   |
| [`var_dump`](#Var-Dump)         | `var_dump` каждого элемента коллекции                    | `$stream->varDump()`                                  |

Установка
-----

Добавьте библиотеку в зависимости в файле `composer.json` вашего проекта:

```json
{
  "require": {
      "markrogoyski/itertools-php": "1.*"
  }
}
```

Используйте [composer](http://getcomposer.org), чтобы установить библиотеку:

```bash
$ php composer.phar install
```

Composer установит IterTools в папку vendor вашего проекта. После этого вы можете использовать функционал библиотеки
в файлах своего проекта, используя Composer Autoloader.

```php
require_once __DIR__ . '/vendor/autoload.php';
```

Альтернативный вариант установки. Выполните данную команду в терминале, находясь в корневой директории вашего проекта:

```
$ php composer.phar require markrogoyski/itertools-php:1.*
```

#### Минимальные требования
* PHP 7.4

Использование
-----
Все функции работают с `iterable` сущностями:
* `array` (тип)
* `Generator` (тип)
* `Iterator` (интерфейс)
* `Traversable` (интерфейс)

## Итерирование нескольких коллекций
### Chain
Последовательно итерирует коллекции поэлементно.

```Multi::chain(iterable ...$iterables)```
```php
use IterTools\Multi;

$prequels  = ['Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith'];
$originals = ['A New Hope', 'Empire Strikes Back', 'Return of the Jedi'];

foreach (Multi::chain($prequels, $originals) as $movie) {
    print($movie);
}
// 'Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith', 'A New Hope', 'Empire Strikes Back', 'Return of the Jedi'
```

### Zip
Параллельно итерирует коллекции, пока не закончится самый короткий итератор.

```Multi::zip(iterable ...$iterables)```
```php
use IterTools\Multi;

$languages = ['PHP', 'Python', 'Java', 'Go'];
$mascots   = ['elephant', 'snake', 'bean', 'gopher'];

foreach (Multi::zip($languages, $mascots) as [$language, $mascot]) {
    print("The {$language} language mascot is an {$mascot}.");
}
// The PHP language mascot is an elephant.
// ...
```

Может принимать больше двух коллекций на вход.
```php
$names          = ['Ryu', 'Ken', 'Chun Li', 'Guile'];
$countries      = ['Japan', 'USA', 'China', 'USA'];
$signatureMoves = ['hadouken', 'shoryuken', 'spinning bird kick', 'sonic boom'];

foreach (Multi::zip($names, $countries, $signatureMoves) as [$name, $country, $signatureMove]) {
    $streetFighter = new StreetFighter($name, $country, $signatureMove);
}
```
Примечание: для коллекций разных длин итерирование прекратиться, когда закончится самая короткая коллекция.

### ZipLongest
Параллельно итерирует коллекции, пока не закончится самый длинный итератор.

```Multi::zipLongest(iterable ...$iterables)```

Примечание: в случае итерирования коллекций разных длин для закончившихся коллекций в каждой следующей итерации будет подставляться значение `null`.

```php
use IterTools\Multi;

$letters = ['A', 'B', 'C'];
$numbers = [1, 2];

foreach (Multi::zipLongest($letters, $numbers) as [$letter, $number]) {
    // ['A', 1], ['B', 2], ['C', null]
}
```

### ZipEqual
Параллельно итерирует коллекции одного размера, в случае разных размеров бросает исключение.

Бросает `\LengthException`, если длины коллекций окажутся неравны, как только закончится самая короткая.

```Multi::zipEqual(iterable ...$iterables)```

```php
use IterTools\Multi;

$letters = ['A', 'B', 'C'];
$numbers = [1, 2, 3];

foreach (Multi::zipEqual($letters, $numbers) as [$letter, $number]) {
    // ['A', 1], ['B', 2], ['C', 3]
}
```

## Итерирование одной коллекции
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

```Single::chunkwiseOverlap(iterable $data, int $chunkSize, int $overlapSize)```

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

### Drop While
Пропускает элементы, пока предикат возвращает истину.

После того как предикат впервые вернул `true`, все последующие элементы попадают в выборку.

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

### Filter True
Возвращает только те элементы, для которых предикат возвращает истину.

По умолчанию (если не передан) предикат приводит элементы коллекции к `bool`.

```Single::filterFalse(iterable $data, callable $predicate)```

```php
use IterTools\Single;

$starWarsEpisodes   = [1, 2, 3, 4, 5, 6, 7, 8, 9];
$goodMoviePredicate = fn ($episode) => $episode > 3 && $episode < 8;

foreach (Single::filterTrue($starWarsEpisodes, $goodMoviePredicate) as $goodMovie) {
    print($goodMovie);
}
// 4, 5, 6, 7
```

### Filter False
Возвращает только те элементы, для которых предикат возвращает ложь.

По умолчанию (если не передан) предикат приводит элементы коллекции к `bool`.

```Single::filterFalse(iterable $data, callable $predicate)```

```php
use IterTools\Single;

$starWarsEpisodes   = [1, 2, 3, 4, 5, 6, 7, 8, 9];
$goodMoviePredicate = fn ($episode) => $episode > 3 && $episode < 8;

foreach (Single::filterFalse($starWarsEpisodes, $goodMoviePredicate) as $badMovie) {
    print($badMovie);
}
// 1, 2, 3, 8, 9
```

### Group By
Группирует элементы коллекции по заданному правилу.

Функция `$groupKeyFunction` должна возвращать общий ключ для элементов группы.

```Single::groupBy(iterable $data, callable $groupKeyFunction)```

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

## Бесконечное итерирование
### Count
Бесконечно перебирает последовательность целых чисел.

```Infinite::count(int $start = 1, int $step = 1)```

```php
use IterTools\Infinite;

$start = 1;
$step  = 1;

foreach (Infinite::count($start, $step) as $i) {
    print($i);
}
// 1, 2, 3, 4, 5 ...
```

### Cycle
Бесконечно зацикливает перебор коллекции.

```Infinite::cycle(iterable $iterable)```

```php
use IterTools\Infinite;

$hands = ['rock', 'paper', 'scissors'];

foreach (Infinite::cycle($hands) as $hand) {
    RockPaperScissors::playHand($hand);
}
// rock, paper, scissors, rock, paper, scissors, ...
```

### Repeat (Infinite)
Бесконечно повторяет данное значение.

```Infinite::repeat(mixed $item)```

```php
use IterTools\Infinite;

$dialogue = 'Are we there yet?';

foreach (Infinite::repeat($dialogue) as $repeated) {
    print($repeated);
}
// 'Are we there yet?', 'Are we there yet?', 'Are we there yet?', ...
```

## Итерирование случайных значений
### Choice
Генерирует случайные выборы вариантов из списка.

```Random::choice(array $items, int $repetitions)```

```php
use IterTools\Random;

$cards       = ['Ace', 'King', 'Queen', 'Jack', 'Joker'];
$repetitions = 10;

foreach (Random::choice($cards, $repetitions) as $card) {
    print($card);
}
// 'King', 'Jack', 'King', 'Ace', ... [random]
```

### CoinFlip
Генерирует случайные броски монеты (0 или 1).

```Random::coinFlip(int $repetitions)```

```php
use IterTools\Random;

$repetitions = 10;

foreach (Random::coinFlip($repetitions) as $coinFlip) {
    print($coinFlip);
}
// 1, 0, 1, 1, 0, ... [random]
```

### Number
Генерирует случайные целые числа.

```Random::number(int $min, int $max, int $repetitions)```

```php
use IterTools\Random;

$min         = 1;
$max         = 4;
$repetitions = 10;

foreach (Random::number($min, $max, $repetitions) as $number) {
    print($number);
}
// 3, 2, 5, 5, 1, 2, ... [random]
```

### Percentage
Генерирует случайные вещественные числа между 0 и 1.

```Random::percentage(int $repetitions)```

```php
use IterTools\Random;

$repetitions = 10;

foreach (Random::percentage($repetitions) as $percentage) {
    print($percentage);
}
// 0.30205562629132, 0.59648594775233, ... [random]
```

### RockPaperScissors
Случайный выбор "камень-ножницы-бумага".

```Random::rockPaperScissors(int $repetitions)```

```php
use IterTools\Random;

$repetitions = 10;

foreach (Random::rockPaperScissors($repetitions) as $rpsHand) {
    print($rpsHand);
}
// 'paper', 'rock', 'rock', 'scissors', ... [random]
```

## Математическое итерирование
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

## Итерирование множеств и мультимножеств
### Distinct
Фильтрует коллекцию, выдавая только уникальные значения.

```Set::distinct(iterable $data, bool $strict = true)```

По умолчанию выполняет сравнение в [режиме строгой типизации](#Режимы-типизации). Передайте значение `false` аргумента `$strict`, чтобы работать в режиме приведения типов.

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
Итерирует пересечение коллекций в режиме [приведения типов](#Режимы-типизации).

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
Итерирует [M-частичное пересечение](https://github.com/Smoren/partial-intersection-php) коллекций в режиме [приведения типов](#Режимы-типизации).

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
Итерирует симметрическую разность коллекций в режиме [приведения типов](#Режимы-типизации).

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

## Саммари о коллекции
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

## Reduce
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

### To Max
Возвращает максимальный элемент коллекции.

```Reduce::toMax(iterable $data, callable|null $comparator = null): mixed|null```

- Если `$comparator` не передан, элементы коллекции должны быть сравнимы.
- Для пустой коллекции возвращает `null`.

```php
use IterTools\Reduce;

$numbers = [5, 3, 1, 2, 4];

$result = Reduce::toMax($numbers);
// 5
```

### To Min
Возвращает минимальный элемент коллекции.

```Reduce::toMin(iterable $data, callable|null $comparator = null): mixed|null```

- Если `$comparator` не передан, элементы коллекции должны быть сравнимы.
- Для пустой коллекции возвращает `null`.

```php
use IterTools\Reduce;

$numbers = [5, 3, 1, 2, 4];

$result = Reduce::toMin($numbers);
// 1
```

### To Min Max
Возвращает минимальный и максимальный элементы коллекции.

```Reduce::toMinMax(iterable $numbers, callable|null $comparator = null): array```

- Если `$comparator` не передан, элементы коллекции должны быть сравнимы.
- Для пустой коллекции возвращает `[null, null]`.

```php
use IterTools\Reduce;

$numbers = [1, 2, 3, -1, -2, -3];

[$min, $max] = Reduce::toMinMax($numbers);
// [-3, 3]
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

## Цепочечный вызов итераторов

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

```$stream->chunkwiseOverlap(int $chunkSize, int $overlapSize): Stream```

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

По умолчанию выполняет сравнения в [режиме строгой типизации](#Режимы-типизации). Передайте значение `false` аргумента `$strict`, чтобы работать в режиме приведения типов.

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

#### Drop While
Пропускает элементы из потока, пока предикат возвращает ложь.

```$stream->dropWhile(callable $predicate): Stream```

После того как предикат впервые вернул `true`, все последующие элементы попадают в выборку.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5]

$result = Stream::of($input)
    ->dropWhile(fn ($value) => $value < 3)
    ->toArray();
// 3, 4, 5
```

#### Filter True
Возвращает из потока только те элементы, для которых предикат возвращает истину.

```$stream->filterTrue(callable $predicate): Stream```

По умолчанию (если не передан) предикат приводит элементы коллекции к `bool`.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->filterTrue(fn ($value) => $value > 0)
    ->toArray();
// 1, 2, 3
```

#### Filter False
Возвращает из потока только те элементы, для которых предикат возвращает ложь.

```$stream->filterFalse(callable $predicate): Stream```

По умолчанию (если не передан) предикат приводит элементы коллекции к `bool`.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->filterFalse(fn ($value) => $value > 0)
    ->toArray();
// -1, -2, -3
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
Пересечение хранимой в потоке коллекции с другими переданными коллекциями в режиме [приведения типов](#Режимы-типизации).

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
Частичное пересечение хранимой в потоке коллекции с другими переданными коллекциями, вычисляемое в режиме [приведения типов](#Режимы-типизации).

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
Возвращает поток, содержащий симметрическую разность исходного потока с заданным набором коллекций, полученную в режиме [приведения типов](#Режимы-типизации).

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

#### Саммари о коллекции
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

##### To Max
Возвращает максимальный элемент коллекции из потока.

```$stream->toMax(callable|null $comparator = null): mixed```

Если `$comparator` не передан, элементы коллекции должны быть сравнимы.

Для пустой коллекции вернет `null`.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($iterable)
    ->toMax();
// 3
```

##### To Min
Возвращает минимальный элемент коллекции из потока.

```$stream->toMin(callable|null $comparator = null): mixed```

Если `$comparator` не передан, элементы коллекции должны быть сравнимы.

Для пустой коллекции вернет `null`.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($iterable)
    ->toMin();
// -3
```

##### To Min Max
Возвращает минимальный и максимальный элементы коллекции из потока.

```$stream->toMinMax(callable|null $comparator = null): array```

Если `$comparator` не передан, элементы коллекции должны быть сравнимы.

Для пустой коллекции вернет `[null, null]`.

```php
use IterTools\Stream;

$numbers = [1, 2, 3, -1, -2, -3];

[$min, $max] = Stream::of($numbers)
    ->toMinMax();
// [-3, 3]
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

## Композиция вызовов
IterTools позволяет комбинировать вызовы методов, чтобы получать новые коллекции.
#### Zip Strings
```php
use IterTools\Multi;
use IterTools\Single;

$letters = 'ABCDEFGHI';
$numbers = '123456789';

foreach (Multi::zip(Single::string($letters), Single::string($numbers)) as [$letter, $number]) {
     $battleshipMove = new BattleshipMove($letter, $number)
}
// A1, B2, C3
```

#### Chain Strings
```php
use IterTools\Multi;
use IterTools\Single;

$letters = 'abc';
$numbers = '123';

foreach (Multi::chain(Single::string($letters), Single::string($numbers)) as $character) {
    print($character);
}
// a, b, c, 1, 2, 3
```

## Режимы типизации

Для методов, которые используют сравнение элементов коллекций для получения результата,
по умолчанию сравнения выполняются строго без приведения типов:

* scalars: сравнивает строго по типу;
* objects: всегда считает разные экземпляры неравными;
* arrays: сравнивает сериализованными.

В случае, если метод опционально поддерживает режим приведения типов (имеет аргумент `$strict`) при `$strict` установленном в `false`
либо если метод имеет в названии слово `Coercive`, он будет работать в нестрогом режиме сравнения:

* scalars: сравнивает нестрого по значению;
* objects: сравнивает сериализованными;
* arrays: сравнивает сериализованными.

Стандарты
---------

IterTools PHP соответствует следующим стандартам:

* PSR-1  - Basic coding standard (http://www.php-fig.org/psr/psr-1/)
* PSR-4  - Autoloader (http://www.php-fig.org/psr/psr-4/)
* PSR-12 - Extended coding style guide (http://www.php-fig.org/psr/psr-12/)

Лицензия
-------

IterTools PHP распространяется по лицензии MIT License.

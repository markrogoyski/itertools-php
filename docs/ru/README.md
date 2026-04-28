![IterToolsLogo Logo](https://github.com/markrogoyski/itertools-php/blob/main/docs/image/IterToolsLogo.png?raw=true)

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
    ->distinct()                 // [1, 2, 3, 4, 5]
    ->map(fn ($x) => $x**2)      // [1, 4, 9, 16, 25]
    ->filter(fn ($x) => $x < 10) // [1, 4, 9]
    ->toSum();                   // 14
```

Методы библиотеки гарантировано работают с любыми `iterable` сущностями:
* `array`
* `Iterator`
* `Generator`
* `Traversable`

Установка
-----

Добавьте библиотеку в зависимости в файле `composer.json` вашего проекта:

```json
{
  "require": {
      "markrogoyski/itertools-php": "2.*"
  }
}
```

Используйте [composer](http://getcomposer.org), чтобы установить библиотеку:

```bash
$ composer install
```

Composer установит IterTools в папку vendor вашего проекта. После этого вы можете использовать функционал библиотеки
в файлах своего проекта, используя Composer Autoloader.

```php
require_once __DIR__ . '/vendor/autoload.php';
```

Альтернативный вариант установки. Выполните данную команду в терминале, находясь в корневой директории вашего проекта:

```
$ composer require markrogoyski/itertools-php:2.*
```

#### Минимальные требования
* **PHP 8.2+**
    * (Для PHP 7.4–8.1 используйте [v1.9](https://github.com/markrogoyski/itertools-php/releases/tag/v1.9.0))

Краткий справочник
-----------

### Инструменты для итерирования в циклах

#### Итерирование нескольких коллекций
| Метод                                                          | Описание                                                                                    | Пример кода                                  |
|----------------------------------------------------------------|---------------------------------------------------------------------------------------------|----------------------------------------------|
| [`chain`](multi-iteration.md#chain)                           | Последовательно итерирует коллекции                                                         | `Multi::chain($list1, $list2)`               |
| [`roundRobin`](multi-iteration.md#roundrobin)                 | Поочерёдно отдаёт по одному элементу из каждой коллекции, чередуя источники                 | `Multi::roundRobin($list1, $list2)`          |
| [`zip`](multi-iteration.md#zip)                               | Параллельно итерирует коллекции, пока не закончится самый короткий итератор                 | `Multi::zip($list1, $list2)`                 |
| [`zipEqual`](multi-iteration.md#zipequal)                     | Параллельно итерирует коллекции одного размера, в случае разных размеров бросает исключение | `Multi::zipEqual($list1, $list2)`            |
| [`zipFilled`](multi-iteration.md#zipfilled)                   | Параллельно итерирует коллекции с подстановкой филлера для закончившихся итераторов         | `Multi::zipFilled($default, $list1, $list2)` |
| [`zipLongest`](multi-iteration.md#ziplongest)                 | Параллельно итерирует коллекции, пока не закончится самый длинный итератор                  | `Multi::zipLongest($list1, $list2)`          |

#### Итерирование одной коллекции
| Метод                                                                            | Описание                                                                     | Пример кода                                                 |
|----------------------------------------------------------------------------------|------------------------------------------------------------------------------|-------------------------------------------------------------|
| [`accumulate`](single-iteration.md#accumulate)                                  | Накапливает результат применения бинарного оператора                         | `Single::accumulate($data, $op, [$initial])`                |
| [`chunkwise`](single-iteration.md#chunkwise)                                    | Итерирует коллекцию, разбитую на чанки                                       | `Single::chunkwise($data, $chunkSize)`                      |
| [`chunkwiseOverlap`](single-iteration.md#chunkwise-overlap)                     | Итерирует коллекцию, разбитую на взаимонакладывающиеся чанки                 | `Single::chunkwiseOverlap($data, $chunkSize, $overlapSize)` |
| [`compress`](single-iteration.md#compress)                                      | Возвращает элементы из коллекции, выбранные селектором                       | `Single::compress($data, $selectors)`                       |
| [`compressAssociative`](single-iteration.md#compress-associative)               | Возвращает элементы из коллекции по заданным ключам                          | `Single::compressAssociative($data, $selectorKeys)`         |
| [`dropWhile`](single-iteration.md#drop-while)                                   | Пропускает элементы, пока предикат возвращает истину                         | `Single::dropWhile($data, $predicate)`                      |
| [`enumerate`](single-iteration.md#enumerate)                                    | Итерирует пары `[индекс, значение]`                                          | `Single::enumerate($data, [$start])`                        |
| [`filter`](single-iteration.md#filter)                                          | Возвращает только те элементы, для которых предикат возвращает истину        | `Single::filter($data)`                                     |
| [`filterTrue`](single-iteration.md#filter-true)                                 | Возвращает только истинные элементы из коллекции                             | `Single::filterTrue($data)`                                 |
| [`filterFalse`](single-iteration.md#filter-false)                               | Возвращает только ложные элементы из коллекции                               | `Single::filterFalse($data, $predicate)`                    |
| [`filterKeys`](single-iteration.md#filter-keys)                                 | Возвращает только те элементы, для ключей которых предикат возвращает истину | `Single::filterKeys($data, $predicate)`                     |
| [`flatMap`](single-iteration.md#flat-map)                                       | Отображение коллекции с уплощением результата на 1 уровень вложенности       | `Single::flaMap($data, $mapper)`                            |
| [`flatten`](single-iteration.md#flatten)                                        | Многоуровневое уплощение коллекции                                           | `Single::flatten($data, [$dimensions])`                     |
| [`groupBy`](single-iteration.md#group-by)                                       | Группирует элементы коллекции                                                | `Single::groupBy($data, $groupKeyFunction, [$itemKeyFunc])` |
| [`intersperse`](single-iteration.md#intersperse)                                | Вставляет разделитель между элементами коллекции                             | `Single::intersperse($data, $separator)`                    |
| [`limit`](single-iteration.md#limit)                                            | Ограничивает итерирование коллекции заданным максимальным числом итераций    | `Single::limit($data, $limit)`                              |
| [`map`](single-iteration.md#map)                                                | Отображение коллекции с использованием callback-функции                      | `Single::map($data, $function)`                             |
| [`mapSpread`](single-iteration.md#map-spread)                                   | Отображение, при котором элементы распаковываются как позиционные аргументы  | `Single::mapSpread($data, $function)`                       |
| [`pairwise`](single-iteration.md#pairwise)                                      | Итерирует коллекцию попарно (с наложением)                                   | `Single::pairwise($data)`                                   |
| [`reindex`](single-iteration.md#reindex)                                        | Переиндексирует key-value коллекцию                                          | `Single::reindex($data, $reindexer)`                        |
| [`repeat`](single-iteration.md#repeat)                                          | Повторяет данное значение заданное число раз                                 | `Single::repeat($item, $repetitions)`                       |
| [`reverse`](single-iteration.md#reverse)                                        | Итерирует коллекцию в обратном порядке                                       | `Single::reverse($data)`                                    |
| [`skip`](single-iteration.md#skip)                                              | Итерирует коллекцию, пропуская некоторое количество элементов подряд         | `Single::skip($data, $count, [$offset])`                    |
| [`slice`](single-iteration.md#slice)                                            | Возвращает подвыборку коллекции                                              | `Single::slice($data, [$start], [$count], [$step])`         |
| [`string`](single-iteration.md#string)                                          | Итерирует строку посимвольно                                                 | `Single::string($string)`                                   |
| [`takeWhile`](single-iteration.md#take-while)                                   | Отдает элементы, пока предикат возвращает истину                             | `Single::takeWhile($data, $predicate)`                      |

#### Бесконечное итерирование
| Метод                                                    | Описание                                             | Пример кода                      |
|----------------------------------------------------------|------------------------------------------------------|----------------------------------|
| [`count`](infinite-iteration.md#count)                  | Бесконечно перебирает последовательность целых чисел | `Infinite::count($start, $step)` |
| [`cycle`](infinite-iteration.md#cycle)                  | Бесконечно зацикливает перебор коллекции             | `Infinite::cycle($collection)`   |
| [`iterate`](infinite-iteration.md#iterate)              | Бесконечно применяет функцию к её предыдущему результату | `Infinite::iterate($initial, $function)` |
| [`repeat`](infinite-iteration.md#repeat)                | Бесконечно повторяет данное значение                 | `Infinite::repeat($item)`        |

#### Итерирование случайных значений
| Метод                                                              | Описание                                 | Пример кода                                |
|--------------------------------------------------------------------|------------------------------------------|--------------------------------------------|
| [`choice`](random-iteration.md#choice)                            | Случайные выборы вариантов из списка     | `Random::choice($list, $repetitions)`      |
| [`coinFlip`](random-iteration.md#coinflip)                        | Случайные броски монеты (0 или 1)        | `Random::coinFlip($repetitions)`           |
| [`number`](random-iteration.md#number)                            | Случайные целые числа                    | `Random::number($min, $max, $repetitions)` |
| [`percentage`](random-iteration.md#percentage)                    | Случайные вещественные числа между 0 и 1 | `Random::percentage($repetitions)`         |
| [`rockPaperScissors`](random-iteration.md#rockpaperscissors)      | Случайный выбор "камень-ножницы-бумага"  | `Random::rockPaperScissors($repetitions)`  |

#### Математическое итерирование
| Метод                                                                    | Описание                            | Пример кода                                        |
|--------------------------------------------------------------------------|-------------------------------------|----------------------------------------------------|
| [`frequencies`](math-iteration.md#frequencies)                          | Абсолютное распределение частот     | `Math::frequencies($data, [$strict])`              |
| [`relativeFrequencies`](math-iteration.md#relative-frequencies)         | Относительное распределение частот  | `Math::relativeFrequencies($data, [$strict])`      |
| [`runningAverage`](math-iteration.md#running-average)                   | Накопление среднего арифметического | `Math::runningAverage($numbers, $initialValue)`    |
| [`runningDifference`](math-iteration.md#running-difference)             | Накопление разности                 | `Math::runningDifference($numbers, $initialValue)` |
| [`runningMax`](math-iteration.md#running-max)                           | Поиск максимального значения        | `Math::runningMax($numbers, $initialValue)`        |
| [`runningMin`](math-iteration.md#running-min)                           | Поиск минимального значения         | `Math::runningMin($numbers, $initialValue)`        |
| [`runningProduct`](math-iteration.md#running-product)                   | Накопление произведения             | `Math::runningProduct($numbers, $initialValue)`    |
| [`runningTotal`](math-iteration.md#running-total)                       | Накопление суммы                    | `Math::runningTotal($numbers, $initialValue)`      |

#### Итерирование множеств и мультимножеств
| Метод                                                                                        | Описание                                                              | Пример кода                                                  |
|----------------------------------------------------------------------------------------------|-----------------------------------------------------------------------|--------------------------------------------------------------|
| [`distinct`](set-iteration.md#distinct)                                                     | Фильтрует коллекцию, сохраняя только уникальные значения              | `Set::distinct($data)`                                       |
| [`distinctAdjacent`](set-iteration.md#distinct-adjacent)                                    | Удаляет только подряд идущие дубликаты                                | `Set::distinctAdjacent($data)`                               |
| [`distinctAdjacentBy`](set-iteration.md#distinct-adjacent-by)                               | Удаляет только подряд идущие дубликаты по заданному ключу             | `Set::distinctAdjacentBy($data, $keyFn)`                     |
| [`intersection`](set-iteration.md#intersection)                                             | Пересечение нескольких коллекций                                      | `Set::intersection(...$iterables)`                           |
| [`intersectionCoercive`](set-iteration.md#intersection-coercive)                            | Пересечение нескольких коллекций в режиме приведения типов            | `Set::intersectionCoercive(...$iterables)`                   |
| [`partialIntersection`](set-iteration.md#partial-intersection)                              | Частичное пересечение нескольких коллекций                            | `Set::partialIntersection($minCount, ...$iterables)`         |
| [`partialIntersectionCoercive`](set-iteration.md#partial-intersection-coercive)             | Частичное пересечение нескольких коллекций в режиме приведения типов  | `Set::partialIntersectionCoercive($minCount, ...$iterables)` |
| [`difference`](set-iteration.md#difference)                                                 | Разность коллекций                                                   | `Set::difference($a, ...$iterables)`                         |
| [`differenceCoercive`](set-iteration.md#difference-coercive)                                | Разность коллекций в режиме приведения типов                         | `Set::differenceCoercive($a, ...$iterables)`                 |
| [`symmetricDifference`](set-iteration.md#symmetric-difference)                              | Симметрическая разница нескольких коллекций                           | `Set::symmetricDifference(...$iterables)`                    |
| [`symmetricDifferenceCoercive`](set-iteration.md#symmetric-difference-coercive)             | Симметрическая разница нескольких коллекций в режиме приведения типов | `Set::symmetricDifferenceCoercive(...$iterables)`            |
| [`union`](set-iteration.md#union)                                                           | Объединение нескольких коллекций                                      | `Set::union(...$iterables)`                                  |
| [`unionCoercive`](set-iteration.md#union-coercive)                                          | Объединение нескольких коллекций в режиме приведения типов            | `Set::unionCoercive(...$iterables)`                          |

#### Комбинаторное итерирование
| Метод                                                                                    | Описание                          | Пример кода                                             |
|------------------------------------------------------------------------------------------|-----------------------------------|---------------------------------------------------------|
| [`product`](combinatorics-iteration.md#product)                                         | Декартово произведение коллекций             | `Combinatorics::product(...$iterables)`                    |
| [`permutations`](combinatorics-iteration.md#permutations)                               | Перестановки элементов коллекции             | `Combinatorics::permutations($data, [$r])`                 |
| [`combinations`](combinatorics-iteration.md#combinations)                               | Сочетания элементов коллекции                | `Combinatorics::combinations($data, $r)`                   |
| [`combinationsWithReplacement`](combinatorics-iteration.md#combinations-with-replacement) | Сочетания с повторениями                   | `Combinatorics::combinationsWithReplacement($data, $r)`    |
| [`powerset`](combinatorics-iteration.md#powerset)                                       | Все подмножества коллекции (булеан)          | `Combinatorics::powerset($data)`                           |

#### Итерирование с сортировкой
| Итератор                                          | Описание                                 | Пример кода                         |
|---------------------------------------------------|------------------------------------------|-------------------------------------|
| [`asort`](sort-iteration.md#asort)               | Сортирует коллекцию с сохранением ключей           | `Sort::asort($data, [$comparator])` |
| [`asortBy`](sort-iteration.md#asortby)           | Сортирует коллекцию по извлечённому ключу с сохранением ключей | `Sort::asortBy($data, $keyFn)`    |
| [`largest`](sort-iteration.md#largest)           | n наибольших элементов (по убыванию)               | `Sort::largest($data, $n, [$keyFn])`|
| [`smallest`](sort-iteration.md#smallest)         | n наименьших элементов (по возрастанию)            | `Sort::smallest($data, $n, [$keyFn])`|
| [`sort`](sort-iteration.md#sort)                 | Сортирует коллекцию                                | `Sort::sort($data, [$comparator])`  |
| [`sortBy`](sort-iteration.md#sortby)             | Сортирует коллекцию по извлечённому ключу          | `Sort::sortBy($data, $keyFn)`       |

#### Итерирование файлов
| Итератор                                             | Описание                                      | Пример кода                    |
|------------------------------------------------------|-----------------------------------------------|--------------------------------|
| [`readCsv`](file-iteration.md#read-csv)             | Итерирует коллекции ячеек CSV-файла построчно | `File::readCsv($fileHandle)`   |
| [`readLines`](file-iteration.md#read-lines)         | Итерирует содержимое файла построчно          | `File::readLines($fileHandle)` |

#### Преобразование итерируемых сущностей
| Итератор                                                                    | Описание                                                       | Пример кода                                                      |
|-----------------------------------------------------------------------------|----------------------------------------------------------------|------------------------------------------------------------------|
| [`partition`](transform-iteration.md#partition)                            | Разделяет коллекцию на два списка: истинные и ложные           | `Transform::partition($data, $predicate)`                        |
| [`tee`](transform-iteration.md#tee)                                        | Создает несколько одинаковых независимых итераторов из данной коллекции | `Transform::tee($data, $count)`                                  |
| [`toArray`](transform-iteration.md#to-array)                               | Преобразует итерируемую коллекцию в массив                     | `Transform::toArray($data)`                                      |
| [`toAssociativeArray`](transform-iteration.md#to-associative-array)        | Преобразует итерируемую коллекцию в ассоциативный массив       | `Transform::toAssociativeArray($data, [$keyFunc], [$valueFunc])` |
| [`toIterator`](transform-iteration.md#to-iterator)                         | Преобразует итерируемую коллекцию в итератор                   | `Transform::toIterator($data)`                                   |

#### Саммари о коллекции
| Метод                                                                                    | Описание                                                                                             | Пример кода                                       |
|------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------|---------------------------------------------------|
| [`allMatch`](summary.md#all-match)                                                      | Истинно, если предикат возвращает истину для всех элементов коллекции                                | `Summary::allMatch($data, $predicate)`            |
| [`allUnique`](summary.md#all-unique)                                                    | Истинно, если все элементы коллекции уникальны                                                       | `Summary::allUnique($data, [$strict])`            |
| [`anyMatch`](summary.md#any-match)                                                      | Истинно, если предикат возвращает истину хотя бы для одного элемента коллекции                       | `Summary::anyMatch($data, $predicate)`            |
| [`arePermutations`](summary.md#are-permutations)                                        | Истинно, если коллекции являются перестановками друг друга                                           | `Summary::arePermutations(...$iterables)`         |
| [`arePermutationsCoercive`](summary.md#are-permutations-coercive)                       | Истинно, если коллекции являются перестановками друг друга (в режиме приведения типов)               | `Summary::arePermutationsCoercive(...$iterables)` |
| [`contains`](summary.md#contains)                                                       | Истинно, если коллекция содержит искомое значение                                                    | `Summary::contains($data, $needle)`               |
| [`containsCoercive`](summary.md#contains-coercive)                                      | Истинно, если коллекция содержит искомое значение (в режиме приведения типов)                        | `Summary::containsCoercive($data, $needle)`       |
| [`exactlyN`](summary.md#exactly-n)                                                      | Истинно, если предикат возвращает истину в точности для N элементов                                  | `Summary::exactlyN($data, $n, $predicate)`        |
| [`isPartitioned`](summary.md#is-partitioned)                                            | Истинно, если истинные элементы находятся в коллекции перед ложными (истинность определяет предикат) | `Summary::isPartitioned($data, $predicate)`       |
| [`isEmpty`](summary.md#is-empty)                                                        | Истинно, если коллекция пуста                                                                        | `Summary::isEmpty($data)`                         |
| [`isSorted`](summary.md#is-sorted)                                                      | Истинно, если коллекция отсортирована в прямом порядке                                               | `Summary::isSorted($data)`                        |
| [`isReversed`](summary.md#is-reversed)                                                  | Истинно, если коллекция отсортирована в обратном порядке                                             | `Summary::isReversed($data)`                      |
| [`noneMatch`](summary.md#none-match)                                                    | Истинно, если предикат возвращает ложь для всех элементов коллекции                                  | `Summary::noneMatch($data, $predicate)`           |
| [`same`](summary.md#same)                                                               | Истинно, если данные коллекции одинаковы                                                             | `Summary::same(...$iterables)`                    |
| [`sameCount`](summary.md#same-count)                                                    | Истинно, если данные коллекции имеют одинаковую длину                                                | `Summary::sameCount(...$iterables)`               |

#### Редуцирование
| Метод                                                        | Описание                                                                            | Пример кода                                                   |
|--------------------------------------------------------------|-------------------------------------------------------------------------------------|---------------------------------------------------------------|
| [`toAverage`](reduce.md#to-average)                         | Среднее арифметическое элементов коллекции                                          | `Reduce::toAverage($numbers)`                                 |
| [`toCount`](reduce.md#to-count)                             | Длина коллекции                                                                     | `Reduce::toCount($data)`                                      |
| [`toFirst`](reduce.md#to-first)                             | Первый элемент коллекции                                                            | `Reduce::toFirst()`                                           |
| [`toFirstAndLast`](reduce.md#to-first-and-last)             | Первый и последний элементы коллекции                                               | `Reduce::toFirstAndLast()`                                    |
| [`toFirstMatch`](reduce.md#to-first-match)                  | Первый элемент, удовлетворяющий предикату                                           | `Reduce::toFirstMatch($data, $predicate, [$default])`         |
| [`toFirstMatchIndex`](reduce.md#to-first-match-index)       | Индекс первого элемента, удовлетворяющего предикату                                 | `Reduce::toFirstMatchIndex($data, $predicate, [$default])`    |
| [`toFirstMatchKey`](reduce.md#to-first-match-key)           | Ключ первого элемента, удовлетворяющего предикату                                   | `Reduce::toFirstMatchKey($data, $predicate, [$default])`      |
| [`toLast`](reduce.md#to-last)                               | Последний элемент коллекции                                                         | `Reduce::toLast()`                                            |
| [`toMax`](reduce.md#to-max)                                 | Максимальный элемент коллекции                                                      | `Reduce::toMax($numbers, [$compareBy])`                       |
| [`toMin`](reduce.md#to-min)                                 | Минимальный элемент коллекции                                                       | `Reduce::toMin($numbers, [$compareBy])`                       |
| [`toMinMax`](reduce.md#to-min-max)                          | Минимальный и максимальный элемент коллекции                                        | `Reduce::toMinMax($numbers, [$compareBy])`                    |
| [`toNth`](reduce.md#to-nth)                                 | N-й элемент коллекции                                                               | `Reduce::toNth($data, $position)`                             |
| [`toProduct`](reduce.md#to-product)                         | Произведение элементов коллекции                                                    | `Reduce::toProduct($numbers)`                                 |
| [`toRandomValue`](reduce.md#to-random-value)                | Случайный элемент из коллекции                                                      | `Reduce::toRandomValue($data)`                                |
| [`toRange`](reduce.md#to-range)                             | Разница между максимальным и минимальным элементами коллекции                       | `Reduce::toRange($numbers)`                                   |
| [`toString`](reduce.md#to-string)                           | Преобразование коллекции в строку                                                   | `Reduce::toString($data, [$separator], [$prefix], [$suffix])` |
| [`toSum`](reduce.md#to-sum)                                 | Сумма элементов коллекции                                                           | `Reduce::toSum($numbers)`                                     |
| [`toValue`](reduce.md#to-value)                             | Редуцирование коллекции до значения, вычисляемого с использованием callback-функции | `Reduce::toValue($data, $reducer, $initialValue)`             |

### Цепочечный вызов итераторов
#### Фабричные методы
| Источник                                                              | Описание                                                                                             | Пример кода                                         |
|-----------------------------------------------------------------------|------------------------------------------------------------------------------------------------------|-----------------------------------------------------|
| [`of`](stream.md#of)                                                 | Создает поток для цепочечных вызовов из данной коллекции                                             | `Stream::of($iterable)`                             |
| [`ofCoinFlips`](stream.md#of-coin-flips)                             | Создает поток для цепочечных вызовов из бесконечных случайных бросков монеты                         | `Stream::ofCoinFlips($repetitions)`                 |
| [`ofCsvFile`](stream.md#of-csv-file)                                 | Создает поток для цепочечных вызовов из строк CSV-файла                                              | `Stream::ofCsvFile($fileHandle)`                    |
| [`ofFileLines`](stream.md#of-file-lines)                             | Создает поток для цепочечных вызовов из строк файла                                                  | `Stream::ofFileLines($fileHandle)`                  |
| [`ofEmpty`](stream.md#of-empty)                                      | Создает поток для цепочечных вызовов из пустой коллекции                                             | `Stream::ofEmpty()`                                 |
| [`ofRandomChoice`](stream.md#of-random-choice)                       | Создает поток для цепочечных вызовов из бесконечных случайных выборов элемента из списка             | `Stream::ofRandomChoice($items, $repetitions)`      |
| [`ofRandomNumbers`](stream.md#of-random-numbers)                     | Создает поток для цепочечных вызовов из бесконечного набора случайных целых чисел                    | `Stream::ofRandomNumbers($min, $max, $repetitions)` |
| [`ofRandomPercentage`](stream.md#of-random-percentage)               | Создает поток для цепочечных вызовов из бесконечного набора случайных вещественных чисел между 0 и 1 | `Stream::ofRandomPercentage($repetitions)`          |
| [`ofRange`](stream.md#of-range)                                      | Создает поток для цепочечных вызовов из арифметической прогрессии                                    | `Stream::ofRange($start, $end, $step)`              |
| [`ofRockPaperScissors`](stream.md#of-rock-paper-scissors)            | Создает поток для цепочечных вызовов из бесконечных случайных выборов "камень-ножницы-бумага"        | `Stream::ofRockPaperScissors($repetitions)`         |

#### Цепочечные операции
| Операция                                                                                  | Описание                                                                                                     | Пример кода                                                                       |
|-------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------|
| [`accumulate`](stream.md#accumulate)                                                     | Накапливает результат применения бинарного оператора                                                         | `$stream->accumulate($op, [$initial])`                                            |
| [`asort`](stream.md#asort)                                                               | Сортирует коллекцию с сохранением ключей                                                                     | `$stream->asort([$comparator])`                                                   |
| [`asortBy`](stream.md#asort-by)                                                          | Сортирует коллекцию по извлечённому ключу с сохранением ключей                                               | `$stream->asortBy($keyFn)`                                                        |
| [`chainWith`](stream.md#chain-with)                                                      | Добавляет в конец итератора другие коллекции для последовательного итерирования                              | `$stream->chainWith(...$iterables)`                                               |
| [`compress`](stream.md#compress)                                                         | Отфильтровывает из коллекции элементы, которые не выбраны                                                    | `$stream->compress($selectors)`                                                   |
| [`compressAssociative`](stream.md#compress-associative)                                  | Оставляет элементы с выбранными ключами                                                                      | `$stream->compressAssociative($selectorKeys)`                                     |
| [`chunkwise`](stream.md#chunkwise)                                                       | Итерирует коллекцию с разбиением по чанкам                                                                   | `$stream->chunkwise($chunkSize)`                                                  |
| [`chunkwiseOverlap`](stream.md#chunkwise-overlap)                                        | Итерирует коллекцию с разбиением по взаимонакладывающимся чанкам                                             | `$stream->chunkwiseOverlap($chunkSize, $overlap)`                                 |
| [`distinct`](stream.md#distinct)                                                         | Фильтрует коллекцию, сохраняя только уникальные значения                                                     | `$stream->distinct($strict)`                                                      |
| [`distinctAdjacent`](stream.md#distinct-adjacent)                                        | Удаляет только подряд идущие дубликаты                                                                       | `$stream->distinctAdjacent()`                                                     |
| [`distinctAdjacentBy`](stream.md#distinct-adjacent-by)                                   | Удаляет только подряд идущие дубликаты по заданному ключу                                                    | `$stream->distinctAdjacentBy($keyFn)`                                             |
| [`dropWhile`](stream.md#drop-while)                                                      | Пропускает элементы из коллекции, пока предикат возвращает истину                                            | `$stream->dropWhile($predicate)`                                                  |
| [`enumerate`](stream.md#enumerate)                                                       | Итерирует пары `[индекс, значение]`                                                                          | `$stream->enumerate([$start])`                                                    |
| [`filter`](stream.md#filter)                                                             | Возвращает из коллекции только те элементы, для которых предикат возвращает истину                           | `$stream->filterTrue($predicate)`                                                 |
| [`filterTrue`](stream.md#filter-true)                                                    | Возвращает только истинные элементы из коллекции                                                             | `$stream->filterTrue($predicate)`                                                 |
| [`filterFalse`](stream.md#filter-false)                                                  | Возвращает только ложные элементы из коллекции                                                               | `$stream->filterFalse($predicate)`                                                |
| [`filterKeys`](stream.md#filter-keys)                                                    | Возвращает только те элементы, для ключей которых предикат возвращает истину                                 | `$stream->filterKeys($predicate)`                                                 |
| [`flatMap`](stream.md#flat-map)                                                          | Отображение коллекции с уплощением результата на 1 уровень вложенности                                       | `$stream->flatMap($function)`                                                     |
| [`flatten`](stream.md#flatten)                                                           | Многоуровневое уплощение коллекции                                                                           | `$stream->flatten($dimensions)`                                                   |
| [`frequencies`](stream.md#frequencies)                                                   | Абсолютная частота вхождений                                                                                 | `$stream->frequencies([$strict])`                                                 |
| [`groupBy`](stream.md#group-by)                                                          | Группирует элементы из коллекции по заданному правилу                                                        | `$stream->groupBy($groupKeyFunction)`                                             |
| [`infiniteCycle`](stream.md#infinite-cycle)                                              | Бесконечно зацикливает перебор коллекции                                                                     | `$stream->infiniteCycle()`                                                        |
| [`intersectionWith`](stream.md#intersection-with)                                        | Возвращает пересечение хранимой коллекции с другими коллекциями                                              | `$stream->intersectionWith(...$iterables)`                                        |
| [`intersectionCoerciveWith`](stream.md#intersection-coercive-with)                       | Возвращает пересечение хранимой коллекции с другими коллекциями (в режиме приведения типов)                  | `$stream->intersectionCoerciveWith(...$iterables)`                                |
| [`intersperse`](stream.md#intersperse)                                                   | Вставляет разделитель между элементами коллекции                                                             | `$stream->intersperse($separator)`                                                |
| [`largest`](stream.md#largest)                                                           | Сводит поток к n наибольшим элементам (по убыванию)                                                          | `$stream->largest($n, [$keyFn])`                                                  |
| [`limit`](stream.md#limit)                                                               | Ограничивает итерирование коллекции заданным максимальным числом итераций                                    | `$stream->limit($limit)`                                                          |
| [`map`](stream.md#map)                                                                   | Отображение коллекции с использованием callback-функции                                                      | `$stream->map($function)`                                                         |
| [`mapSpread`](stream.md#map-spread)                                                      | Отображение, при котором элементы распаковываются как позиционные аргументы                                  | `$stream->mapSpread($function)`                                                   |
| [`pairwise`](stream.md#pairwise)                                                         | Итерирует коллекцию попарно (с наложением)                                                                   | `$stream->pairwise()`                                                             |
| [`partialIntersectionWith`](stream.md#partial-intersection-with)                         | Возвращает частичное пересечение хранимой коллекции с другими коллекциями                                    | `$stream->partialIntersectionWith( $minIntersectionCount, ...$iterables)`         |
| [`partialIntersectionCoerciveWith`](stream.md#partial-intersection-coercive-with)        | Возвращает частичное пересечение хранимой коллекции с другими коллекциями (в режиме приведения типов)        | `$stream->partialIntersectionCoerciveWith( $minIntersectionCount, ...$iterables)` |
| [`productWith`](stream.md#product-with)                                                  | Декартово произведение коллекции с другими коллекциями                                                       | `$stream->productWith(...$iterables)`                                             |
| [`permutations`](stream.md#permutations)                                                 | Перестановки элементов коллекции                                                                            | `$stream->permutations([$r])`                                                     |
| [`combinations`](stream.md#combinations)                                                 | Сочетания элементов коллекции                                                                               | `$stream->combinations($r)`                                                       |
| [`combinationsWithReplacement`](stream.md#combinations-with-replacement)                 | Сочетания с повторениями элементов потока                                                                   | `$stream->combinationsWithReplacement($r)`                                        |
| [`powerset`](stream.md#powerset)                                                         | Все подмножества элементов потока (булеан)                                                                  | `$stream->powerset()`                                                             |
| [`reindex`](stream.md#reindex)                                                           | Переиндексирует key-value коллекцию                                                                          | `$stream->reindex($reindexer)`                                                    |
| [`relativeFrequencies`](stream.md#relative-frequencies)                                  | Относительная частота вхождений                                                                              | `$stream->relativeFrequencies([$strict])`                                         |
| [`reverse`](stream.md#reverse)                                                           | Итерирует коллекцию в обратном порядке                                                                       | `$stream->reverse()`                                                              |
| [`roundRobinWith`](stream.md#round-robin-with)                                           | Поочерёдно отдаёт элементы из потока и заданных коллекций, чередуя источники                                 | `$stream->roundRobinWith(...$iterables)`                                          |
| [`runningAverage`](stream.md#running-average)                                            | Накопление среднего арифметического элементов коллекции                                                      | `$stream->runningAverage($initialValue)`                                          |
| [`runningDifference`](stream.md#running-difference)                                      | Накопление разности элементов коллекции                                                                      | `$stream->runningDifference($initialValue)`                                       |
| [`runningMax`](stream.md#running-max)                                                    | Поиск максимального значения из коллекции                                                                    | `$stream->runningMax($initialValue)`                                              |
| [`runningMin`](stream.md#running-min)                                                    | Поиск минимального значения из коллекции                                                                     | `$stream->runningMin($initialValue)`                                              |
| [`runningProduct`](stream.md#running-product)                                            | Накопление произведения элементов коллекции                                                                  | `$stream->runningProduct($initialValue)`                                          |
| [`runningTotal`](stream.md#running-total)                                                | Накопление суммы элементов коллекции                                                                         | `$stream->runningTotal($initialValue)`                                            |
| [`skip`](stream.md#skip)                                                                 | Пропускает n элементов коллекции                                                                             | `$stream->skip($count, [$offset])`                                                |
| [`slice`](stream.md#slice)                                                               | Возвращает подвыборку коллекции                                                                              | `$stream->slice([start], [$count], [step])`                                       |
| [`smallest`](stream.md#smallest)                                                         | Сводит поток к n наименьшим элементам (по возрастанию)                                                       | `$stream->smallest($n, [$keyFn])`                                                 |
| [`sort`](stream.md#sort)                                                                 | Сортирует хранимую коллекцию                                                                                 | `$stream->sort([$comparator])`                                                    |
| [`sortBy`](stream.md#sort-by)                                                            | Сортирует хранимую коллекцию по извлечённому ключу                                                           | `$stream->sortBy($keyFn)`                                                         |
| [`differenceWith`](stream.md#difference-with)                                            | Возвращает разность хранимой коллекции с другими коллекциями                                                 | `$stream->differenceWith(...$iterables)`                                          |
| [`differenceCoerciveWith`](stream.md#difference-coercive-with)                           | Возвращает разность хранимой коллекции с другими коллекциями (в режиме приведения типов)                     | `$stream->differenceCoerciveWith(...$iterables)`                                  |
| [`symmetricDifferenceWith`](stream.md#symmetric-difference-with)                         | Возвращает симметрическую разность хранимой коллекции с другими коллекциями                                  | `$this->symmetricDifferenceWith(...$iterables)`                                   |
| [`symmetricDifferenceCoerciveWith`](stream.md#symmetric-difference-coercive-with)        | Возвращает симметрическую разность хранимой коллекции с другими коллекциями (в режиме приведения типов)      | `$this->symmetricDifferenceCoerciveWith( ...$iterables)`                          |
| [`takeWhile`](stream.md#take-while)                                                      | Отдает элементы из коллекции, пока предикат возвращает истину                                                | `$stream->takeWhile($predicate)`                                                  |
| [`unionWith`](stream.md#union-with)                                                      | Возвращает объединение хранимой коллекции с другими коллекциями                                              | `$stream->unionWith(...$iterables)`                                               |
| [`unionCoerciveWith`](stream.md#union-coercive-with)                                     | Возвращает объединение хранимой коллекции с другими коллекциями (в режиме приведения типов)                  | `$stream->unionCoerciveWith(...$iterables)`                                       |
| [`zip`](stream.md#zip)                                                                   | Транспонирует поток строк по столбцам, останавливаясь по самой короткой строке                               | `$stream->zip()`                                                                  |
| [`zipLongest`](stream.md#zip-longest)                                                    | Транспонирует поток строк по столбцам до самой длинной строки (отсутствующие → null)                         | `$stream->zipLongest()`                                                           |
| [`zipFilled`](stream.md#zip-filled)                                                      | Транспонирует поток строк по столбцам до самой длинной, подставляя филлер                                    | `$stream->zipFilled($filler)`                                                     |
| [`zipEqual`](stream.md#zip-equal)                                                        | Транспонирует поток строк по столбцам, бросает исключение при разных длинах                                  | `$stream->zipEqual()`                                                             |
| [`zipWith`](stream.md#zip-with)                                                          | Параллельно итерирует коллекцию вместе с другими, пока не закончится самый короткий итератор                 | `$stream->zipWith(...$iterables)`                                                 |
| [`zipEqualWith`](stream.md#zip-equal-with)                                               | Параллельно итерирует коллекцию вместе с другими одного размера, в случае разных размеров бросает исключение | `$stream->zipEqualWith(...$iterables)`                                            |
| [`zipFilledWith`](stream.md#zip-filled-with)                                             | Параллельно итерирует коллекцию вместе с другими, подставляя для закончившихся заданный филлер               | `$stream->zipFilledWith($default, ...$iterables)`                                 |
| [`zipLongestWith`](stream.md#zip-longest-with)                                           | Параллельно итерирует коллекцию вместе с другими, пока не закончится самый длинный итератор                  | `$stream->zipLongestWith(...$iterables)`                                          |

#### Завершающие операции
##### Саммари о коллекции
| Операция                                                                               | Описание                                                                                             | Пример кода                                           |
|----------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------|-------------------------------------------------------|
| [`allMatch`](stream.md#all-match)                                                     | Истинно, если предикат возвращает истину для всех элементов коллекции                                | `$stream->allMatch($predicate)`                       |
| [`allUnique`](stream.md#all-unique)                                                   | Истинно, если все элементы коллекции уникальны                                                       | `$stream->allUnique([$strict]])`                      |
| [`anyMatch`](stream.md#any-match)                                                     | Истинно, если предикат возвращает истину хотя бы для одного элемента коллекции                       | `$stream->anyMatch($predicate)`                       |
| [`arePermutationsWith`](stream.md#are-permutations-with)                              | Истинно, если коллекции являются перестановками друг друга                                           | `$stream->arePermutationsWith(...$iterables)`         |
| [`arePermutationsCoerciveWith`](stream.md#are-permutations-coercive-with)             | Истинно, если коллекции являются перестановками друг друга (в режиме приведения типов)               | `$stream->arePermutationsCoerciveWith(...$iterables)` |
| [`contains`](stream.md#contains)                                                      | Истинно, если поток содержит искомое значение                                                        | `$stream->contains($needle)`                          |
| [`containsCoercive`](stream.md#contains-coercive)                                     | Истинно, если поток содержит искомое значение (в режиме приведения типов)                            | `$stream->containsCoercive($needle)`                  |
| [`exactlyN`](stream.md#exactly-n)                                                     | Истинно, если предикат возвращает истину в точности для N элементов                                  | `$stream->exactlyN($n, $predicate)`                   |
| [`isEmpty`](stream.md#is-empty)                                                       | Истинно, если коллекция пуста                                                                        | `$stream::isEmpty()`                                  |
| [`isPartitioned`](stream.md#is-partitioned)                                           | Истинно, если истинные элементы находятся в коллекции перед ложными (истинность определяет предикат) | `$stream::isPartitioned($predicate)`                  |
| [`isSorted`](stream.md#is-sorted)                                                     | Истинно, если коллекция отсортирована в прямом порядке                                               | `$stream->isSorted()`                                 |
| [`isReversed`](stream.md#is-reversed)                                                 | Истинно, если коллекция отсортирована в обратном порядке                                             | `$stream->isReversed()`                               |
| [`noneMatch`](stream.md#none-match)                                                   | Истинно, если предикат возвращает ложь для всех элементов коллекции                                  | `$stream->noneMatch($predicate)`                      |
| [`sameWith`](stream.md#same-with)                                                     | Истинно, если данные коллекции одинаковы                                                             | `$stream->sameWith(...$iterables)`                    |
| [`sameCountWith`](stream.md#same-count-with)                                          | Истинно, если данные коллекции имеют одинаковую длину                                                | `$stream->sameCountWith(...$iterables)`               |

##### Редуцирование
| Завершающая операция                                     | Описание                                                            | Пример кода                                             |
|----------------------------------------------------------|---------------------------------------------------------------------|---------------------------------------------------------|
| [`toAverage`](stream.md#to-average)                     | Среднее арифметическое элементов коллекции                          | `$stream->toAverage()`                                  |
| [`toCount`](stream.md#to-count)                         | Длина коллекции                                                     | `$stream->toCount()`                                    |
| [`toFirst`](stream.md#to-first)                         | Первый элемент коллекции                                            | `$stream->toFirst()`                                    |
| [`toFirstAndLast`](stream.md#to-first-and-last)         | Первый и последний элементы коллекции                               | `$stream->toFirstAndLast()`                             |
| [`toFirstMatch`](stream.md#to-first-match)              | Первый элемент, удовлетворяющий предикату                           | `$stream->toFirstMatch($predicate, [$default])`         |
| [`toFirstMatchIndex`](stream.md#to-first-match-index)   | Индекс первого элемента, удовлетворяющего предикату                 | `$stream->toFirstMatchIndex($predicate, [$default])`    |
| [`toFirstMatchKey`](stream.md#to-first-match-key)       | Ключ первого элемента, удовлетворяющего предикату                   | `$stream->toFirstMatchKey($predicate, [$default])`      |
| [`toLast`](stream.md#to-last)                           | Последний элемент коллекции                                         | `$stream->toLast()`                                     |
| [`toMax`](stream.md#to-max)                             | Максимальное значение из элементов коллекции                        | `$stream->toMax([$compareBy])`                          |
| [`toMin`](stream.md#to-min)                             | Минимальное значение из элементов коллекции                         | `$stream->toMin([$compareBy])`                          |
| [`toMinMax`](stream.md#to-min-max)                      | Минимальное и максимальное значения из элементов коллекции          | `$stream->toMinMax([$compareBy])`                       |
| [`toNth`](stream.md#to-nth)                             | N-й элемент коллекции                                               | `$stream->toNth($position)`                             |
| [`toProduct`](stream.md#to-product)                     | Произведение элементов коллекции                                    | `$stream->toProduct()`                                  |
| [`toString`](stream.md#to-string)                       | Преобразование коллекции в строку                                   | `$stream->toString([$separator], [$prefix], [$suffix])` |
| [`toSum`](stream.md#to-sum)                             | Сумма элементов коллекции                                           | `$stream->toSum()`                                      |
| [`toRandomValue`](stream.md#to-random-value)            | Случайный элемент из коллекции                                      | `$stream->toRandomValue()`                              |
| [`toRange`](stream.md#to-range)                         | Разница между максимальным и минимальным элементами коллекции       | `$stream->toRange()`                                    |
| [`toValue`](stream.md#to-value)                         | Редуцирование коллекции до значения, вычисляемого callback-функцией | `$stream->toValue($reducer, $initialValue)`             |

##### Операции трансформации
| Завершающая операция                                             | Описание                                                    | Пример кода                                             |
|------------------------------------------------------------------|-------------------------------------------------------------|---------------------------------------------------------|
| [`toArray`](stream.md#to-array)                                 | Возвращает массив из элементов потока                       | `$stream->toArray()`                                    |
| [`toAssociativeArray`](stream.md#to-associative-array)          | Возвращает ассоциативный массив из элементов потока         | `$stream->toAssociativeArray($keyFunc, $valueFunc)`     |
| [`toPartition`](stream.md#to-partition)                         | Разделяет поток на два списка: истинные и ложные            | `$stream->toPartition($predicate)`                      |
| [`tee`](stream.md#tee)                                          | Создает несколько одинаковых независимых потоков из данной коллекции | `$stream->tee($count)`                                  |

##### Операции с побочными эффектами
| Завершающая операция                                   | Описание                                              | Пример кода                                          |
|--------------------------------------------------------|-------------------------------------------------------|------------------------------------------------------|
| [`callForEach`](stream.md#call-for-each)               | Вызывает callback-функцию для каждого элемента потока | `$stream->callForEach($function)`                    |
| [`print`](stream.md#print)                             | `print` каждого элемента потока                       | `$stream->print([$separator], [$prefix], [$suffix])` |
| [`printLn`](stream.md#print-line)                      | `print` каждого элемента потока с новой строки        | `$stream->printLn()`                                 |
| [`toCsvFile`](stream.md#to-csv-file)                   | Записывает содержимое потока в CSV файл               | `$stream->toCsvFile($fileHandle, [$headers])`        |
| [`toFile`](stream.md#to-file)                          | Записывает содержимое потока в файл                   | `$stream->toFile($fileHandle)`                       |

#### Операции для дебаггинга
| Отладочная операция                              | Описание                                                          | Пример кода                      |
|--------------------------------------------------|-------------------------------------------------------------------|----------------------------------|
| [`peek`](stream.md#peek)                         | Просмотр каждого элемента коллекции между потоковыми операциями   | `$stream->peek($peekFunc)`       |
| [`peekStream`](stream.md#peek-stream)            | Просмотр всей коллекции потока между операциями                   | `$stream->peekStream($peekFunc)` |
| [`peekPrint`](stream.md#peek-print)              | Печать в поток вывода каждого элемента коллекции между операциями | `$stream->peekPrint()`           |
| [`peekPrintR`](stream.md#peek-printr)            | Вызов `print_r` для каждого элемента коллекции между операциями   | `$stream->peekPrintR()`          |
| [`printR`](stream.md#print-r)                    | `print_r` каждого элемента потока                                 | `$stream->printR()`              |
| [`varDump`](stream.md#var-dump)                  | `var_dump` каждого элемента потока                                | `$stream->varDump()`             |

Использование
-----
Все функции работают с `iterable` сущностями:
* `array` (тип)
* `Generator` (тип)
* `Iterator` (интерфейс)
* `Traversable` (интерфейс)

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

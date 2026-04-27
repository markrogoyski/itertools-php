# Итерирование нескольких коллекций

[Вернуться к главной странице](README.md)

Инструменты для одновременного итерирования нескольких коллекций.

---

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

### RoundRobin
Поочерёдно отдаёт по одному элементу из каждой коллекции, чередуя источники.

```Multi::roundRobin(iterable ...$iterables)```

В каждом раунде берётся по одному элементу из каждой коллекции, в которой ещё есть элементы;
исчерпавшаяся коллекция пропускается на последующих раундах. Итерирование завершается, когда
исчерпаны все коллекции. В отличие от `zip`, элементы возвращаются по одному, а не в виде кортежей.
Ключи исходных коллекций отбрасываются; результат имеет последовательные целочисленные ключи.

```php
use IterTools\Multi;

$queueA = ['A', 'B', 'C'];
$queueB = ['D', 'E'];
$queueC = ['F', 'G', 'H'];

foreach (Multi::roundRobin($queueA, $queueB, $queueC) as $item) {
    print($item);
}
// 'A', 'D', 'F', 'B', 'E', 'G', 'C', 'H'
```

Round-robin-планирование задач между очередями воркеров: задачи равномерно вычерпываются из всех
очередей, пока все они не опустеют:
```php
$workerOne   = ['task-1', 'task-4', 'task-7'];
$workerTwo   = ['task-2', 'task-5'];
$workerThree = ['task-3', 'task-6', 'task-8', 'task-9'];

$schedule = [];
foreach (Multi::roundRobin($workerOne, $workerTwo, $workerThree) as $task) {
    $schedule[] = $task;
}
// ['task-1', 'task-2', 'task-3', 'task-4', 'task-5', 'task-6', 'task-7', 'task-8', 'task-9']
```

См. также: [`Stream::roundRobinWith`](stream.md#round-robin-with).

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
Примечание: для коллекций разных длин итерирование прекратится, когда закончится самая короткая коллекция.

### ZipFilled
Параллельно итерирует коллекции, пока не закончится самый длинный итератор.
Для закончившихся коллекций в качестве значения подставляет заданный филлер.

```Multi::zipFilled(mixed $filler, iterable ...$iterables)```

```php
use IterTools\Multi;

$default = '?';
$letters = ['A', 'B'];
$numbers = [1, 2, 3];

foreach (Multi::zipFilled($default, $letters, $numbers) as [$letter, $number]) {
    // ['A', 1], ['B', 2], ['?', 3]
}
```

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

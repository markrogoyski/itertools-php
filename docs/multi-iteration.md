# Multi Iteration

[Back to main README](../README.md)

Tools for iterating multiple collections simultaneously.

---

### Chain
Chain multiple iterables together into a single continuous sequence.

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
Yield one value at a time from multiple iterables, rotating across sources.

```Multi::roundRobin(iterable ...$iterables)```

On each round, takes one value from each iterable that still has values; once an iterable is
exhausted, it is skipped in subsequent rounds. Iteration ends when every iterable is exhausted.
Unlike `zip`, values are yielded individually rather than as tuples. Source keys are discarded;
the output is sequentially re-indexed.

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

Round-robin scheduling across worker queues fairly drains tasks from every worker until all are empty:
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

See also: [`Stream::roundRobinWith`](stream.md#round-robin-with).

### Zip
Iterate multiple iterable collections simultaneously.

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

Zip works with multiple iterable inputs--not limited to just two.
```php
$names          = ['Ryu', 'Ken', 'Chun Li', 'Guile'];
$countries      = ['Japan', 'USA', 'China', 'USA'];
$signatureMoves = ['hadouken', 'shoryuken', 'spinning bird kick', 'sonic boom'];

foreach (Multi::zip($names, $countries, $signatureMoves) as [$name, $country, $signatureMove]) {
    $streetFighter = new StreetFighter($name, $country, $signatureMove);
}
```
Note: For uneven lengths, iteration stops when the shortest iterable is exhausted.

### ZipEqual
Iterate multiple iterable collections with equal lengths simultaneously.

Throws `\LengthException` if lengths are not equal, meaning that at least one iterator ends before the others.

```Multi::zipEqual(iterable ...$iterables)```

```php
use IterTools\Multi;

$letters = ['A', 'B', 'C'];
$numbers = [1, 2, 3];

foreach (Multi::zipEqual($letters, $numbers) as [$letter, $number]) {
    // ['A', 1], ['B', 2], ['C', 3]
}
```

### ZipFilled
Iterate multiple iterable collections simultaneously, using a default filler value if lengths are not equal.

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
Iterate multiple iterable collections simultaneously.

```Multi::zipLongest(iterable ...$iterables)```

For uneven lengths, the exhausted iterables will produce `null` for the remaining iterations.

```php
use IterTools\Multi;

$letters = ['A', 'B', 'C'];
$numbers = [1, 2];

foreach (Multi::zipLongest($letters, $numbers) as [$letter, $number]) {
    // ['A', 1], ['B', 2], ['C', null]
}
```

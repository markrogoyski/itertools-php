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

### Unzip
Transpose a sequence of rows into columns — the inverse of `zip`.

```Multi::unzip(iterable $rows)```

Yields one array per column. The column count is the width of the shortest row, so trailing
cells of longer rows are discarded — mirroring `Multi::zip`'s shortest-wins semantics. For any
uniform-positive-width input, the round-trip identity `Multi::zip(...Multi::unzip($rows))`
holds. The identity does not extend to zero-width input (e.g. `[[], []]` yields no columns,
losing the row count) nor to output produced by `zipFilled` / `zipLongest` (truncation drops
the padded trailing cells).

Although the return type is `\Generator`, this method is not lazy in any meaningful sense: the
entire input is buffered before the first column can be yielded, since column 0 cannot be
emitted until every row's first cell has been seen. Memory is O(N · W) for N rows of width W.

Throws `\InvalidArgumentException` if any row is not iterable, naming the zero-based row index.

Both row keys and inner-cell keys are discarded; output columns are sequentially indexed lists.

```php
use IterTools\Multi;

$pairs = [[1, 'a'], [2, 'b'], [3, 'c']];

foreach (Multi::unzip($pairs) as $column) {
    print_r($column);
}
// [1, 2, 3]
// ['a', 'b', 'c']
```

Splitting `(timestamp, value)` event tuples into two parallel series — useful when downstream
code expects timestamps and values as separate arrays:
```php
$events = [
    [1700000000, 12.5],
    [1700000060, 13.1],
    [1700000120, 12.9],
    [1700000180, 13.4],
];

[$timestamps, $values] = \iterator_to_array(Multi::unzip($events), false);
// $timestamps === [1700000000, 1700000060, 1700000120, 1700000180]
// $values     === [12.5, 13.1, 12.9, 13.4]
```

See also: [`Stream::unzip`](stream.md#unzip).

# Transform Iteration

[Back to main README](../README.md)

Tools for transforming iterables into other data structures.

---

### Partition
Partitions an iterable into two lists based on a predicate.

Returns a two-element list array: `[truthyValues, falsyValues]`. Both output arrays are reindexed (list arrays); source keys are discarded. Predicate return value is coerced via `(bool)` cast.

```Transform::partition(iterable $data, callable $predicate): array```

```php
use IterTools\Transform;

$numbers = [1, 2, 3, 4, 5, 6];

[$evens, $odds] = Transform::partition($numbers, fn (int $n): bool => $n % 2 === 0);
// $evens: [2, 4, 6]
// $odds:  [1, 3, 5]
```

### Tee
Return several independent (duplicated) iterators from a single iterable.

```Transform::tee(iterable $data, int $count): array```

```php
use IterTools\Transform;

$daysOfWeek = ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun'];
$count = 3;

[$week1, $week2, $week3] = Transform::tee($data, $count);
// Each $week contains iterator containing ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun']
```

### To Array
Transforms any iterable to an array.

```Transform::toArray(iterable $data): array```

```php
use IterTools\Transform;

$iterator = new \ArrayIterator([1, 2, 3, 4, 5]);

$array = Transform::toArray($iterator);
```

### To Associative Array
Transforms any iterable to an associative array.

```Transform::toAssociativeArray(iterable $data, callable $keyFunc = null, callable $valueFunc = null): array```

```php
use IterTools\Transform;

$messages = ['message 1', 'message 2', 'message 3'];

$keyFunc   = fn ($msg) => \md5($msg);
$valueFunc = fn ($msg) => strtoupper($msg);

$associativeArray = Transform::toAssociativeArray($messages, $keyFunc, $valueFunc);
// [
//     '1db65a6a0a818fd39655b95e33ada11d' => 'MESSAGE 1',
//     '83b2330607fe8f817ce6d24249dea373' => 'MESSAGE 2',
//     '037805d3ad7b10c5b8425427b516b5ce' => 'MESSAGE 3',
// ]
```

### To Iterator
Transforms any iterable to an iterator.

```Transform::toArray(iterable $data): array```

```php
use IterTools\Transform;

$array = [1, 2, 3, 4, 5];

$iterator = Transform::toIterator($array);
```

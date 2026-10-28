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

```Transform::tee(iterable $iterable, int $count): array```

```php
use IterTools\Transform;

$daysOfWeek = ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun'];
$count = 3;

[$week1, $week2, $week3] = Transform::tee($daysOfWeek, $count);
// Each $week contains iterator containing ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun']
```

A copy can be rewound as long as it has not advanced. Once it has, `rewind()` throws `\LogicException`, so the pairwise idiom of advancing one copy and zipping the copies throws when `zip` rewinds them. Use [`Single::pairwise()`](single-iteration.md#pairwise) for adjacent pairs.

If the source throws, every copy still yields the elements before the failure, then throws the same exception object when it reaches it. The source is not read again after it fails, so a copy never ends early without an exception.

### To Array
Transforms any iterable to an array.

```Transform::toArray(iterable $iterable): array```

```php
use IterTools\Transform;

$iterator = new \ArrayIterator([1, 2, 3, 4, 5]);

$array = Transform::toArray($iterator);
```

### To Associative Array
Transforms any iterable to an associative array.

```Transform::toAssociativeArray(iterable $iterable, ?callable $keyFunc = null, ?callable $valueFunc = null): array```

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

Keys follow PHP array semantics: a repeated key keeps its first position and its last value, and a decimal integer string such as `'1'` is stored as the int `1`. A key that is not an int or string (a float, `null`, `bool`, array or object) throws `\InvalidArgumentException` rather than being converted or dropped.

### To Iterator
Transforms any iterable to an iterator.

```Transform::toIterator(iterable $iterable): \Iterator```

```php
use IterTools\Transform;

$array = [1, 2, 3, 4, 5];

$iterator = Transform::toIterator($array);
```
### Memoize

`Transform::memoize(iterable $iterable): IteratorAggregate` makes a source replayable while consuming it only on demand. Each consumer replays cached `[key, value]` pairs from the beginning and shares one source traversal. Duplicate keys and object identity are preserved.

Consumers may be interleaved: cached positions are replayed independently, while whichever consumer first reaches the frontier advances the shared source by one item. If the source throws, the successful prefix remains replayable and every consumer reaching the failure boundary receives the same exception object. Re-entrant source advancement is unsupported. Cloning the result creates another handle on the same memoized sequence: the clone shares the source and the cache with the original.

The cache grows with the furthest position consumed and is retained for the wrapper lifetime. Arrays and replayable iterator aggregates are also wrapped and therefore retain both their original representation and the demanded cache. Use `tee(n)` for a known number of concurrent branches and potential buffer release; use `memoize()` when an unknown number of future consumers must replay from the start. Fully consuming an infinite source, or retaining a long prefix, can use unbounded memory.

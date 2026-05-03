# Set and Multiset Iteration

[Back to main README](../README.md)

Tools for set and multiset operations on iterables.

---

### Distinct
Filter out elements from the iterable only returning distinct elements.

```Set::distinct(iterable $data, bool $strict = true)```

Defaults to [strict type](../README.md#strict-and-coercive-types) comparisons. Set strict to false for type coercion comparisons.

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

### Distinct By
Filter out elements from the iterable only returning distinct elements according to a custom comparator function.

```Set::distinctBy(iterable $data, callable $compareBy)```

```php
use IterTools\Set;

$streetFighterConsoleReleases = [
    ['id' => '112233', 'name' => 'Street Fighter 3 3rd Strike', 'console' => 'Dreamcast'],
    ['id' => '223344', 'name' => 'Street Fighter 3 3rd Strike', 'console' => 'PS4'],
    ['id' => '334455', 'name' => 'Street Fighter 3 3rd Strike', 'console' => 'PS5'],
    ['id' => '445566', 'name' => 'Street Fighter VI', 'console' => 'PS4'],
    ['id' => '556677', 'name' => 'Street Fighter VI', 'console' => 'PS5'],
    ['id' => '667788', 'name' => 'Street Fighter VI', 'console' => 'PC'],
];
$compareBy = fn ($sfTitle) => $sfTitle['name'];

$uniqueTitles = [];
foreach (Set::distinctBy($streetFighterConsoleReleases, $compareBy) as $sfTitle) {
    $uniqueTitles[] = $sfTitle;
}

// Contains one SF3 3rd Strike entry and one SFVI entry.
```

### Distinct Adjacent
Remove only consecutive duplicates from an iterable (Unix `uniq` behavior).

```Set::distinctAdjacent(iterable $data)```

* Each element is compared strictly (`===`) to the previous element yielded.
* Non-adjacent duplicates are kept.
* Runs in O(1) memory — only the previous element is held.
* Source keys are discarded; the output is a list with sequential integer keys.

```php
use IterTools\Set;

$values = [1, 1, 2, 2, 3, 1, 1];

$result = [];
foreach (Set::distinctAdjacent($values) as $value) {
    $result[] = $value;
}
// [1, 2, 3, 1] — the trailing 1 stays because it is not adjacent to the earlier 1s
```

```php
use IterTools\Set;

$logLines = ['error: timeout', 'error: timeout', 'error: timeout', 'info: ok', 'error: timeout'];

$collapsed = [];
foreach (Set::distinctAdjacent($logLines) as $line) {
    $collapsed[] = $line;
}
// ['error: timeout', 'info: ok', 'error: timeout']
```

See also [Stream::distinctAdjacent](stream.md#distinct-adjacent).

### Distinct Adjacent By
Remove only consecutive duplicates from an iterable, comparing values returned by a custom key function.

```Set::distinctAdjacentBy(iterable $data, callable $keyFn)```

* Each element's extracted key is compared strictly (`===`) to the previous element's key.
* Non-adjacent duplicate keys are kept.
* Runs in O(1) memory and calls `$keyFn` once per element.
* Source keys are discarded; the output is a list with sequential integer keys.

```php
use IterTools\Set;

$words = ['apple', 'ant', 'banana', 'berry', 'apple'];

$firstLetterRuns = [];
foreach (Set::distinctAdjacentBy($words, fn ($s) => $s[0]) as $word) {
    $firstLetterRuns[] = $word;
}
// ['apple', 'banana', 'apple'] — first word of each run of same first letter
```

```php
use IterTools\Set;

$readings = [
    ['ts' => 60,  'v' => 1],
    ['ts' => 65,  'v' => 2],
    ['ts' => 119, 'v' => 3],
    ['ts' => 120, 'v' => 4],
    ['ts' => 121, 'v' => 5],
];
$minuteKey = fn ($r) => intdiv($r['ts'], 60);

$compressed = [];
foreach (Set::distinctAdjacentBy($readings, $minuteKey) as $reading) {
    $compressed[] = $reading;
}
// keeps only the first reading of each minute-bucket run:
// [['ts' => 60, 'v' => 1], ['ts' => 120, 'v' => 4]]
```

See also [Stream::distinctAdjacentBy](stream.md#distinct-adjacent-by).

### Intersection
Iterates intersection of iterables.

```Set::intersection(iterable ...$iterables)```

If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) intersection rules apply.

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
Iterates intersection of iterables using [type coercion](../README.md#strict-and-coercive-types).

```Set::intersectionCoercive(iterable ...$iterables)```

If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) intersection rules apply.

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
Iterates [M-partial intersection](https://github.com/Smoren/partial-intersection-php) of iterables.

```Set::partialIntersection(int $minIntersectionCount, iterable ...$iterables)```

* If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) intersection rules apply.

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
Iterates [M-partial intersection](https://github.com/Smoren/partial-intersection-php) of iterables using [type coercion](../README.md#strict-and-coercive-types).

```Set::partialIntersectionCoercive(int $minIntersectionCount, iterable ...$iterables)```

* If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) intersection rules apply.

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

### Difference
Iterates the difference of iterables. Returns elements from the first iterable not present in any of the other iterables.

```Set::difference(iterable $a, iterable ...$iterables)```

If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) difference rules apply.

```php
use IterTools\Set;

$a = [1, 2, 3, 4, 7];
$b = [2, 3, 5, 8];
$c = [1, 6, 9];

foreach (Set::difference($a, $b, $c) as $item) {
    print($item);
}
// 4, 7
```

### Difference Coercive
Iterates the difference of iterables using [type coercion](../README.md#strict-and-coercive-types).

```Set::differenceCoercive(iterable $a, iterable ...$iterables)```

If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) difference rules apply.

```php
use IterTools\Set;

$a = [1, 2, 3, 4, 7];
$b = ['1', 2, 3, 5, 8];

foreach (Set::differenceCoercive($a, $b) as $item) {
    print($item);
}
// 4, 7
```

### Symmetric difference
Iterates the symmetric difference of iterables.

```Set::symmetricDifference(iterable ...$iterables)```

If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) difference rules apply.

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
Iterates the symmetric difference of iterables with [type coercion](../README.md#strict-and-coercive-types).

```Set::symmetricDifferenceCoercive(iterable ...$iterables)```

If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) difference rules apply.

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

### Union
Iterates the union of iterables.

```Set::union(iterable ...$iterables)```

If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) union rules apply.

```php
use IterTools\Set;

$a = [1, 2, 3];
$b = [3, 4];
$c = [1, 2, 3, 6, 7];

foreach (Set::union($a, $b, $c) as $item) {
    print($item);
}
//1, 2, 3, 4, 6, 7
```

### Union Coercive
Iterates the union of iterables with [type coercion](../README.md#strict-and-coercive-types).

```Set::unionCoercive(iterable ...$iterables)```

If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) union rules apply.

```php
use IterTools\Set;

$a = ['1', 2, 3];
$b = [3, 4];
$c = [1, 2, 3, 6, 7];

foreach (Set::unionCoercive($a, $b, $c) as $item) {
    print($item);
}
//1, 2, 3, 4, 6, 7
```

### Duplicates
Yield each duplicated value once, at the moment its second occurrence is observed.

```Set::duplicates(iterable $data, bool $strict = true)```

* `$strict` mirrors the comparison semantics of `Set::distinct`.
* Source keys are discarded; output keys are sequential 0-indexed.

```php
use IterTools\Set;

$data = [1, 2, 1, 1, 2, 3];

foreach (Set::duplicates($data) as $value) {
    print($value);
}
// 1, 2
```

### Duplicates By
Yield each value whose extracted key duplicates a previously seen key, once at the moment of the second occurrence.

```Set::duplicatesBy(iterable $data, callable $keyFn)```

* The first value whose key collides is the one yielded; subsequent collisions for that key are not yielded again.
* Comparison of extracted keys is strict.
* Source keys are discarded; output keys are sequential 0-indexed.

```php
use IterTools\Set;

$users = [
    ['id' => 1, 'name' => 'Alice'],
    ['id' => 2, 'name' => 'Bob'],
    ['id' => 1, 'name' => 'Alicia'],
    ['id' => 3, 'name' => 'Carol'],
];

foreach (Set::duplicatesBy($users, fn ($u) => $u['id']) as $duplicate) {
    print($duplicate['name']);
}
// Alicia
```

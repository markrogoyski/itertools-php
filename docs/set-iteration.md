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

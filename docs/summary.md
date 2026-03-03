# Summary

[Back to main README](../README.md)

Tools for summarizing iterable collections with boolean results.

---

### All Match
Returns true if all elements match the predicate function.

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
Returns true if all elements are unique.

```Summary::allUnique(iterable $data, bool $strict = true): bool```

Defaults to [strict type](../README.md#strict-and-coercive-types) comparisons. Set strict to false for type coercion comparisons.

```php
use IterTools\Summary;

$items = ['fingerprints', 'snowflakes', 'eyes', 'DNA']

$boolean = Summary::allUnique($items);
// true
```

### Any Match
Returns true if any element matches the predicate function.

```Summary::anyMatch(iterable $data, callable $predicate): bool```

```php
use IterTools\Summary;

$answers          = ['fish', 'towel', 42, "don't panic"];
$isUltimateAnswer = fn ($a) => a == 42;

$boolean = Summary::anyMatch($answers, $isUltimateAnswer);
// true
```

### Are Permutations
Returns true if all iterables are permutations of each other.

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
Returns true if all iterables are permutations of each other with [type coercion](../README.md#strict-and-coercive-types).

```Summary::arePermutationsCoercive(iterable ...$iterables): bool```

```php
use IterTools\Summary;

$set1 = [1, 2.0, '3'];
$set2 = [2.0, '1', 3];
$set3 = [3, 2, 1];

$boolean = Summary::arePermutationsCoercive($set1, $set2, $set3);
// true
```

### Exactly N
Returns true if exactly n items are true according to a predicate function.

- Predicate is optional.
- Default predicate is boolean value of each item.

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

$boolean = Summary::exactlyN($ages, $n, $predicate);
// false
```

### Is Empty
Returns true if the iterable is empty having no items.

```Summary::isEmpty(iterable $data): bool```

```php
use IterTools\Summary;

$data = []

$boolean = Summary::isEmpty($data);
// true
```

### Is Partitioned
Returns true if all elements of given collection that satisfy the predicate appear before all elements that don't.

- Returns true for empty collection or for collection with single item.
- Default predicate if not provided is the boolean value of each data item.

```Summary::isPartitioned(iterable $data, callable $predicate = null): bool```

```php
use IterTools\Summary;

$numbers          = [0, 2, 4, 1, 3, 5];
$evensBeforeOdds = fn ($item) => $item % 2 === 0;

$boolean = Summary::isPartitioned($numbers, $evensBeforeOdds);
```

### Is Sorted
Returns true if elements are sorted, otherwise false.

- Elements must be comparable.
- Returns true if empty or has only one element.

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
Returns true if elements are reverse sorted, otherwise false.

- Elements must be comparable.
- Returns true if empty or has only one element.

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
Returns true if no element matches the predicate function.

```Summary::noneMatch(iterable $data, callable $predicate): bool```

```php
use IterTools\Summary;

$grades         = [45, 50, 61, 0];
$isPassingGrade = fn ($grade) => $grade >= 70;

$boolean = Summary::noneMatch($grades, $isPassingGrade);
// true
```

### Same
Returns true if all given collections are the same.

For single iterable or empty iterables list returns true.

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
Returns true if all given collections have the same lengths.

For single iterable or empty iterables list returns true.

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

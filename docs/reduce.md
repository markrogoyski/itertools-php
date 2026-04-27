# Reduce

[Back to main README](../README.md)

Tools for reducing iterable collections to single values.

---

### To Average
Reduces to the mean average.

Returns null if collection is empty.

```Reduce::toAverage(iterable $data): float```

```php
use IterTools\Reduce;

$grades = [100, 90, 95, 85, 94];

$finalGrade = Reduce::toAverage($numbers);
// 92.8
```

### To Count
Reduces iterable to its length.

```Reduce::toCount(iterable $data): int```

```php
use IterTools\Reduce;

$someIterable = ImportantThing::getCollectionAsIterable();

$length = Reduce::toCount($someIterable);
// 3
```

### To First
Reduces iterable to its first element.

```Reduce::toFirst(iterable $data): mixed```

Throws `\LengthException` if collection is empty.

```php
use IterTools\Reduce;

$medals = ['gold', 'silver', 'bronze'];

$first = Reduce::toFirst($medals);
// gold
```

### To First And Last
Reduces iterable to its first and last elements.

```Reduce::toFirstAndLast(iterable $data): array{mixed, mixed}```

Throws `\LengthException` if collection is empty.

```php
use IterTools\Reduce;

$weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

$firstAndLast = Reduce::toFirstAndLast($weekdays);
// [Monday, Friday]
```

### To First Match
Reduces iterable to its first element matching the predicate.

```Reduce::toFirstMatch(iterable $data, callable $predicate, mixed $default = null): mixed```

- Predicate return value is coerced via `(bool)` cast.
- Short-circuits on the first match — the iterable is not fully consumed.
- Returns `$default` (null by default) if no element matches.

```php
use IterTools\Reduce;

$numbers = [1, 3, 5, 6, 7, 8];

$firstEven = Reduce::toFirstMatch($numbers, fn (int $n) => $n % 2 === 0);
// 6

$firstNegative = Reduce::toFirstMatch($numbers, fn (int $n) => $n < 0, -1);
// -1
```

### To First Match Index
Reduces iterable to the zero-based position of the first element matching the predicate.

```Reduce::toFirstMatchIndex(iterable $data, callable $predicate, mixed $default = null): mixed```

- Predicate return value is coerced via `(bool)` cast.
- Short-circuits on the first match — the iterable is not fully consumed.
- Returns `$default` (null by default) if no element matches.
- Position is always counted from the start of iteration, regardless of source keys.

```php
use IterTools\Reduce;

$numbers = [10, 20, 30, 40];

$firstOver25Index = Reduce::toFirstMatchIndex($numbers, fn (int $n) => $n > 25);
// 2
```

```php
use IterTools\Reduce;

// Early-exit search: a generator that would throw on the item after the match
// is never advanced past the matching position.
$ids = (function (): \Generator {
    yield 1;
    yield 2;
    yield 3;
    throw new \RuntimeException('iterator advanced past match');
})();

$index = Reduce::toFirstMatchIndex($ids, fn (int $n) => $n === 2);
// 1
```

### To First Match Key
Reduces iterable to the source key of the first element matching the predicate.

```Reduce::toFirstMatchKey(iterable $data, callable $predicate, mixed $default = null): mixed```

- Predicate return value is coerced via `(bool)` cast.
- Short-circuits on the first match — the iterable is not fully consumed.
- Returns `$default` (null by default) if no element matches.
- Preserves the source key (string for associative input, int for list-shape input).

```php
use IterTools\Reduce;

$users = ['alice' => 12, 'bob' => 17, 'carol' => 22, 'dan' => 30];

$firstAdultName = Reduce::toFirstMatchKey($users, fn (int $age) => $age >= 18);
// 'carol'
```

```php
use IterTools\Reduce;

$prices = ['usd' => 9.99, 'eur' => 8.49, 'jpy' => 1499.0];

$firstExpensiveCurrency = Reduce::toFirstMatchKey(
    $prices,
    fn (float $p) => $p > 1000,
    'none'
);
// 'jpy'
```

### To Last
Reduces iterable to its last element.

```Reduce::toLast(iterable $data): mixed```

Throws `\LengthException` if collection is empty.

```php
use IterTools\Reduce;

$gnomesThreePhasePlan = ['Collect underpants', '?', 'Profit'];

$lastPhase = Reduce::toLast($gnomesThreePhasePlan);
// Profit
```

### To Max
Reduces to the max value.

```Reduce::toMax(iterable $data, callable $compareBy = null): mixed|null```

- Optional callable param `$compareBy` must return comparable value.
- If `$compareBy` is not provided then items of given collection must be comparable.
- Returns null if collection is empty.

```php
use IterTools\Reduce;

$numbers = [5, 3, 1, 2, 4];

$result = Reduce::toMax($numbers);
// 5

$movieRatings = [
    [
        'title' => 'Star Wars: Episode IV - A New Hope',
        'rating' => 4.6
    ],
    [
        'title' => 'Star Wars: Episode V - The Empire Strikes Back',
        'rating' => 4.8
    ],
    [
        'title' => 'Star Wars: Episode VI - Return of the Jedi',
        'rating' => 4.6
    ],
];
$compareBy = fn ($movie) => $movie['rating'];

$highestRatedMovie = Reduce::toMax($movieRatings, $compareBy);
// [
//     'title' => 'Star Wars: Episode V - The Empire Strikes Back',
//     'rating' => 4.8
// ];
```

### To Min
Reduces to the min value.

```Reduce::toMin(iterable $data, callable $compareBy = null): mixed|null```

- Optional callable param `$compareBy` must return comparable value.
- If `$compareBy` is not provided then items of given collection must be comparable.
- Returns null if collection is empty.

```php
use IterTools\Reduce;

$numbers = [5, 3, 1, 2, 4];

$result = Reduce::toMin($numbers);
// 1


$movieRatings = [
    [
        'title' => 'The Matrix',
        'rating' => 4.7
    ],
    [
        'title' => 'The Matrix Reloaded',
        'rating' => 4.3
    ],
    [
        'title' => 'The Matrix Revolutions',
        'rating' => 3.9
    ],
    [
        'title' => 'The Matrix Resurrections',
        'rating' => 2.5
    ],
];
$compareBy = fn ($movie) => $movie['rating'];

$lowestRatedMovie = Reduce::toMin($movieRatings, $compareBy);
// [
//     'title' => 'The Matrix Resurrections',
//     'rating' => 2.5
// ]
```

### To Min Max
Reduces to array of its upper and lower bounds (max and min).

```Reduce::toMinMax(iterable $numbers, callable $compareBy = null): array```

- Optional callable param `$compareBy` must return comparable value.
- If `$compareBy` is not provided then items of given collection must be comparable.
- Returns `[null, null]` if given collection is empty.

```php
use IterTools\Reduce;

$numbers = [1, 2, 7, -1, -2, -3];

[$min, $max] = Reduce::toMinMax($numbers);
// [-3, 7]

$reportCard = [
    [
        'subject' => 'history',
        'grade' => 90
    ],
    [
        'subject' => 'math',
        'grade' => 98
    ],
    [
        'subject' => 'science',
        'grade' => 92
    ],
    [
        'subject' => 'english',
        'grade' => 85
    ],
    [
        'subject' => 'programming',
        'grade' => 100
    ],
];
$compareBy = fn ($class) => $class['grade'];

$bestAndWorstSubject = Reduce::toMinMax($reportCard, $compareBy);
// [
//     [
//         'subject' => 'english',
//         'grade' => 85
//     ],
//     [
//         'subject' => 'programming',
//         'grade' => 100
//     ],
// ]
```

### To Nth
Reduces to value at the nth position.

```Reduce::toNth(iterable $data, int $position): mixed```

```php
use IterTools\Reduce;

$lotrMovies = ['The Fellowship of the Ring', 'The Two Towers', 'The Return of the King'];

$rotk = Reduce::toNth($lotrMovies, 2);
// 20
```

### To Product
Reduces to the product of its elements.

Returns null if collection is empty.

```Reduce::toProduct(iterable $data): number|null```

```php
use IterTools\Reduce;

$primeFactors = [5, 2, 2];

$number = Reduce::toProduct($primeFactors);
// 20
```

### To Random Value
Reduces given collection to a random value within it.

```Reduce::toRandomValue(iterable $data): mixed```

```php
use IterTools\Reduce;

$sfWakeupOptions = ['mid', 'low', 'overhead', 'throw', 'meaty'];

$wakeupOption = Reduce::toRandomValue($sfWakeupOptions);
// e.g., throw
```

### To Range
Reduces given collection to its range (difference between max and min).

```Reduce::toRange(iterable $numbers): int|float```

Returns `0` if iterable source is empty.

```php
use IterTools\Reduce;

$grades = [100, 90, 80, 85, 95];

$range = Reduce::toRange($numbers);
// 20
```

### To String
Reduces to a string joining all elements.

* Optional separator to insert between items.
* Optional prefix to prepend to the string.
* Optional suffix to append to the string.

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
Reduces to the sum of its elements.

```Reduce::toSum(iterable $data): number```

```php
use IterTools\Reduce;

$parts = [10, 20, 30];

$sum = Reduce::toSum($parts);
// 60
```

### To Value
Reduce elements to a single value using reducer function.

```Reduce::toValue(iterable $data, callable $reducer, mixed $initialValue): mixed```

```php
use IterTools\Reduce;

$input = [1, 2, 3, 4, 5];
$sum   = fn ($carry, $item) => $carry + $item;

$result = Reduce::toValue($input, $sum, 0);
// 15
```

# Sort Iteration

[Back to main README](../README.md)

Tools for iterating sorted collections.

---

### ASort
Iterate the collection sorted while maintaining the associative key index relations.

```Sort::sort(iterable $data, callable $comparator = null)```

Uses default sorting if optional comparator function not provided.

```php
use IterTools\Single;

$worldPopulations = [
    'China'     => 1_439_323_776,
    'India'     => 1_380_004_385,
    'Indonesia' => 273_523_615,
    'Pakistan'  => 220_892_340,
    'USA'       => 331_002_651,
];

foreach (Sort::sort($worldPopulations) as $country => $population) {
    print("$country: $population" . \PHP_EOL);
}
// Pakistan: 220,892,340
// Indonesia: 273,523,615
// USA: 331,002,651
// India: 1,380,004,385
// China: 1,439,323,776
```

### Sort
Iterate the collection sorted.

```Sort::sort(iterable $data, callable $comparator = null)```

Uses default sorting if optional comparator function not provided.

```php
use IterTools\Single;

$data = [3, 4, 5, 9, 8, 7, 1, 6, 2];

foreach (Sort::sort($data) as $datum) {
    print($datum);
}
// 1, 2, 3, 4, 5, 6, 7, 8, 9
```

### SortBy
Iterate the collection sorted by a key extracted from each element (Schwartzian transform).

```Sort::sortBy(iterable $data, callable $keyFn)```

The key function is called exactly once per element. Source keys are discarded
(matching `Sort::sort`). The sort is stable: elements with equal extracted keys
preserve their original relative order.

Stream equivalent: [`Stream::sortBy()`](stream.md#sort-by).

```php
use IterTools\Sort;

$people = [
    (object)['name' => 'Alice', 'age' => 30],
    (object)['name' => 'Bob',   'age' => 20],
    (object)['name' => 'Carol', 'age' => 40],
];

foreach (Sort::sortBy($people, fn ($p) => $p->age) as $person) {
    print("{$person->name}: {$person->age}" . \PHP_EOL);
}
// Bob: 20
// Alice: 30
// Carol: 40
```

```php
use IterTools\Sort;

$words = ['banana', 'fig', 'cherry', 'apple'];

foreach (Sort::sortBy($words, fn (string $s) => \strlen($s)) as $word) {
    print($word . \PHP_EOL);
}
// fig
// apple
// banana
// cherry
```

### AsortBy
Iterate the collection sorted by a key extracted from each element (Schwartzian
transform), maintaining the associative key index relations.

```Sort::asortBy(iterable $data, callable $keyFn)```

The key function is called exactly once per element. Source keys are preserved
(matching `Sort::asort`). The sort is stable: elements with equal extracted keys
preserve their original relative order.

Stream equivalent: [`Stream::asortBy()`](stream.md#asort-by).

```php
use IterTools\Sort;

$scores = [
    'Alice' => 87,
    'Bob'   => 92,
    'Carol' => 75,
];

foreach (Sort::asortBy($scores, fn (int $score) => $score) as $name => $score) {
    print("$name: $score" . \PHP_EOL);
}
// Carol: 75
// Alice: 87
// Bob: 92
```

```php
use IterTools\Sort;

$people = [
    'alice' => (object)['age' => 30],
    'bob'   => (object)['age' => 20],
    'carol' => (object)['age' => 40],
];

foreach (Sort::asortBy($people, fn ($p) => $p->age) as $key => $person) {
    print("$key => age {$person->age}" . \PHP_EOL);
}
// bob => age 20
// alice => age 30
// carol => age 40
```

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

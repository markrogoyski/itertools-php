# Итерирование с сортировкой

[Вернуться к главной странице](README.md)

Инструменты для итерирования отсортированных коллекций.

---

### ASort
Сортирует коллекцию с сохранением ключей.

```Sort::sort(iterable $data, callable $comparator = null)```

Если `$comparator` не передан, элементы коллекции должны быть сравнимы.

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
Сортирует коллекцию.

```Sort::sort(iterable $data, callable $comparator = null)```

Если `$comparator` не передан, элементы коллекции должны быть сравнимы.

```php
use IterTools\Single;

$data = [3, 4, 5, 9, 8, 7, 1, 6, 2];

foreach (Sort::sort($data) as $datum) {
    print($datum);
}
// 1, 2, 3, 4, 5, 6, 7, 8, 9
```

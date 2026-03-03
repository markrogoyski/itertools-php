# Math Iteration

[Back to main README](../README.md)

Tools for mathematical iteration operations.

---

### Frequencies
Returns a frequency distribution of the data.

```Math::frequencies(iterable $data, bool $strict = true): \Generator```

Defaults to [strict type](../README.md#strict-and-coercive-types) comparisons. Set strict to false for type coercion comparisons.

```php
use IterTools\Math;

$grades = ['A', 'A', 'B', 'B', 'B', 'C'];

foreach (Math::frequencies($grades) as $grade => $frequency) {
    print("$grade: $frequency" . \PHP_EOL);
}
// A: 2, B: 3, C: 1
```

### Relative Frequencies
Returns a relative frequency distribution of the data.

```Math::relativeFrequencies(iterable $data, bool $strict = true): \Generator```

Defaults to [strict type](../README.md#strict-and-coercive-types) comparisons. Set strict to false for type coercion comparisons.

```php
use IterTools\Math;

$grades = ['A', 'A', 'B', 'B', 'B', 'C'];

foreach (Math::relativeFrequencies($grades) as $grade => $frequency) {
    print("$grade: $frequency" . \PHP_EOL);
}
// A: 0.33, B: 0.5, C: 0.166
```

### Running Average
Accumulate the running average over a list of numbers.

```Math::runningAverage(iterable $numbers, int|float $initialValue = null)```

```php
use IterTools\Math;

$grades = [100, 80, 80, 90, 85];

foreach (Math::runningAverage($grades) as $runningAverage) {
    print($runningAverage);
}
// 100, 90, 86.667, 87.5, 87
```

### Running Difference
Accumulate the running difference over a list of numbers.

```Math::runningDifference(iterable $numbers, int|float $initialValue = null)```

```php
use IterTools\Math;

$credits = [1, 2, 3, 4, 5];

foreach (Math::runningDifference($credits) as $runningDifference) {
    print($runningDifference);
}
// -1, -3, -6, -10, -15
```
Provide an optional initial value to lead off the running difference.
```php
use IterTools\Math;

$dartsScores   = [50, 50, 25, 50];
$startingScore = 501;

foreach (Math::runningDifference($dartsScores, $startingScore) as $runningScore) {
    print($runningScore);
}
// 501, 451, 401, 376, 326
```

### Running Max
Accumulate the running maximum over a list of numbers.

```Math::runningMax(iterable $numbers, int|float $initialValue = null)```

```php
use IterTools\Math;

$numbers = [1, 2, 1, 3, 5];

foreach (Math::runningMax($numbers) as $runningMax) {
    print($runningMax);
}
// 1, 2, 2, 3, 5
```

### Running Min
Accumulate the running minimum over a list of numbers.

```Math::runningMin(iterable $numbers, int|float $initialValue = null)```

```php
use IterTools\Math;

$numbers = [3, 4, 2, 5, 1];

foreach (Math::runningMin($numbers) as $runningMin) {
    print($runningMin);
}
// 3, 3, 2, 2, 1
```

### Running Product
Accumulate the running product over a list of numbers.

```Math::runningProduct(iterable $numbers, int|float $initialValue = null)```

```php
use IterTools\Math;

$numbers = [1, 2, 3, 4, 5];

foreach (Math::runningProduct($numbers) as $runningProduct) {
    print($runningProduct);
}
// 1, 2, 6, 24, 120
```

Provide an optional initial value to lead off the running product.
```php
use IterTools\Math;

$numbers      = [1, 2, 3, 4, 5];
$initialValue = 5;

foreach (Math::runningProduct($numbers, $initialValue) as $runningProduct) {
    print($runningProduct);
}
// 5, 5, 10, 30, 120, 600
```

### Running Total
Accumulate the running total over a list of numbers.

```Math::runningTotal(iterable $numbers, int|float $initialValue = null)```

```php
use IterTools\Math;

$prices = [1, 2, 3, 4, 5];

foreach (Math::runningTotal($prices) as $runningTotal) {
    print($runningTotal);
}
// 1, 3, 6, 10, 15
```

Provide an optional initial value to lead off the running total.
```php
use IterTools\Math;

$prices       = [1, 2, 3, 4, 5];
$initialValue = 5;

foreach (Math::runningTotal($prices, $initialValue) as $runningTotal) {
    print($runningTotal);
}
// 5, 6, 8, 11, 15, 20
```

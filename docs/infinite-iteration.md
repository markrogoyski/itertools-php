# Infinite Iteration

[Back to main README](../README.md)

Tools for creating infinite iterators.

---

### Count
Count sequentially forever.

```Infinite::count(int $start = 1, int $step = 1)```

```php
use IterTools\Infinite;

$start = 1;
$step  = 1;

foreach (Infinite::count($start, $step) as $i) {
    print($i);
}
// 1, 2, 3, 4, 5 ...
```

### Cycle
Cycle through the elements of a collection sequentially forever.

```Infinite::cycle(iterable $iterable)```

```php
use IterTools\Infinite;

$hands = ['rock', 'paper', 'scissors'];

foreach (Infinite::cycle($hands) as $hand) {
    RockPaperScissors::playHand($hand);
}
// rock, paper, scissors, rock, paper, scissors, ...
```

### Iterate
Iterate forever by repeatedly applying a function to its previous output.

Yields `$initial` first, then `$function($initial)`, then `$function($function($initial))`, and so on without end. Always combine with a bounded consumer such as [`Single::limit`](single-iteration.md#limit) when collecting results.

```Infinite::iterate(mixed $initial, callable $function)```

```php
use IterTools\Infinite;
use IterTools\Single;

// Powers of 2: 1, 2, 4, 8, 16, 32, 64, 128
$powersOfTwo = Infinite::iterate(1, fn (int $x) => $x * 2);

foreach (Single::limit($powersOfTwo, 8) as $power) {
    print("$power ");
}
// 1 2 4 8 16 32 64 128
```

```php
use IterTools\Infinite;
use IterTools\Single;

// Collatz sequence starting from 27, take the first 50 terms.
$collatz = fn (int $n) => $n % 2 === 0 ? \intdiv($n, 2) : 3 * $n + 1;

foreach (Single::limit(Infinite::iterate(27, $collatz), 50) as $term) {
    print("$term ");
}
// 27 82 41 124 62 31 94 47 142 71 214 107 322 161 484 ...
```

### Repeat (Infinite)
Repeat an item forever.

```Infinite::repeat(mixed $item)```

```php
use IterTools\Infinite;

$dialogue = 'Are we there yet?';

foreach (Infinite::repeat($dialogue) as $repeated) {
    print($repeated);
}
// 'Are we there yet?', 'Are we there yet?', 'Are we there yet?', ...
```

### Generate
Yield values produced by a zero-arg supplier forever.

```Infinite::generate(callable $supplier)```

* Each iteration calls the supplier and yields its return value.
* Captured state in the supplier persists across calls.

```php
use IterTools\Infinite;
use IterTools\Single;

$counter = 0;
$next    = function () use (&$counter) {
    return ++$counter;
};

foreach (Single::limit(Infinite::generate($next), 5) as $n) {
    print($n);
}
// 1, 2, 3, 4, 5
```

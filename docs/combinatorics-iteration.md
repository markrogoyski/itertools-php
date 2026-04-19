# Combinatorics Iteration

[Back to main README](../README.md)

Tools for combinatoric iteration: cartesian product, permutations, combinations.

---

### Product
Cartesian product of the input iterables.

Output tuples are list arrays (0-indexed, in input order); source keys are discarded. Output order follows Python's `itertools.product` (lexicographic, input-order-preserving): the rightmost iterable advances fastest.

Input iterables must be finite. They are consumed once (materialized internally), so generators are supported but cannot be re-iterated afterwards.

Passing the same non-rewindable iterator instance (e.g. a `Generator`) more than once is not supported — the second occurrence will throw because the iterator has already been consumed. Pass distinct instances instead.

Special cases:
- `product()` of zero iterables yields one empty tuple: `[[]]`
- if any input iterable is empty, the result is empty

```Combinatorics::product(iterable ...$iterables): \Generator```

```php
use IterTools\Combinatorics;

$numbers = [1, 2];
$letters = ['a', 'b'];

foreach (Combinatorics::product($numbers, $letters) as $tuple) {
    print_r($tuple);
}
// [1, 'a']
// [1, 'b']
// [2, 'a']
// [2, 'b']
```

---

### Permutations
Permutations of an iterable.

Output tuples are list arrays (0-indexed, in input order); source keys are discarded. Output order follows Python's `itertools.permutations` (lexicographic by input position, not by value), so duplicate values are treated as position-unique: `permutations([1, 1])` yields `[[1, 1], [1, 1]]`.

Input iterable must be finite. It is consumed once (materialized internally), so generators are supported but cannot be re-iterated afterwards.

Special cases:
- `$r = 0` yields one empty tuple: `[[]]`
- `$r` greater than `count($data)` yields nothing
- `$r = null` means full-length permutations (equivalent to `$r = count($data)`)
- empty input with `$r = null` (or `$r = 0`) yields one empty tuple: `[[]]`

Throws `\InvalidArgumentException` if `$r` is negative.

```Combinatorics::permutations(iterable $data, ?int $r = null): \Generator```

```php
use IterTools\Combinatorics;

$data = [1, 2, 3];

foreach (Combinatorics::permutations($data) as $tuple) {
    print_r($tuple);
}
// [1, 2, 3]
// [1, 3, 2]
// [2, 1, 3]
// [2, 3, 1]
// [3, 1, 2]
// [3, 2, 1]
```

---

### Combinations
Combinations (without replacement) of an iterable.

Output tuples are list arrays (0-indexed, in input order); source keys are discarded. Output order follows Python's `itertools.combinations` (lexicographic by input position, not by value), so duplicate values are treated as position-unique: `combinations([1, 1], 2)` yields `[[1, 1]]`.

Input iterable must be finite. It is consumed once (materialized internally), so generators are supported but cannot be re-iterated afterwards.

Special cases:
- `$r = 0` yields one empty tuple: `[[]]`
- `$r` greater than `count($data)` yields nothing
- `$r = count($data)` yields exactly one tuple containing all input values

Throws `\InvalidArgumentException` if `$r` is negative.

```Combinatorics::combinations(iterable $data, int $r): \Generator```

```php
use IterTools\Combinatorics;

$data = [1, 2, 3, 4];

foreach (Combinatorics::combinations($data, 2) as $tuple) {
    print_r($tuple);
}
// [1, 2]
// [1, 3]
// [1, 4]
// [2, 3]
// [2, 4]
// [3, 4]
```

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

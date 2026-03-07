# Multi Iteration

[Back to main README](../README.md)

Tools for iterating multiple collections simultaneously.

---

### Chain
Chain multiple iterables together into a single continuous sequence.

```Multi::chain(iterable ...$iterables)```
```php
use IterTools\Multi;

$prequels  = ['Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith'];
$originals = ['A New Hope', 'Empire Strikes Back', 'Return of the Jedi'];

foreach (Multi::chain($prequels, $originals) as $movie) {
    print($movie);
}
// 'Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith', 'A New Hope', 'Empire Strikes Back', 'Return of the Jedi'
```

### Zip
Iterate multiple iterable collections simultaneously.

```Multi::zip(iterable ...$iterables)```
```php
use IterTools\Multi;

$languages = ['PHP', 'Python', 'Java', 'Go'];
$mascots   = ['elephant', 'snake', 'bean', 'gopher'];

foreach (Multi::zip($languages, $mascots) as [$language, $mascot]) {
    print("The {$language} language mascot is an {$mascot}.");
}
// The PHP language mascot is an elephant.
// ...
```

Zip works with multiple iterable inputs--not limited to just two.
```php
$names          = ['Ryu', 'Ken', 'Chun Li', 'Guile'];
$countries      = ['Japan', 'USA', 'China', 'USA'];
$signatureMoves = ['hadouken', 'shoryuken', 'spinning bird kick', 'sonic boom'];

foreach (Multi::zip($names, $countries, $signatureMoves) as [$name, $country, $signatureMove]) {
    $streetFighter = new StreetFighter($name, $country, $signatureMove);
}
```
Note: For uneven lengths, iteration stops when the shortest iterable is exhausted.

### ZipEqual
Iterate multiple iterable collections with equal lengths simultaneously.

Throws `\LengthException` if lengths are not equal, meaning that at least one iterator ends before the others.

```Multi::zipEqual(iterable ...$iterables)```

```php
use IterTools\Multi;

$letters = ['A', 'B', 'C'];
$numbers = [1, 2, 3];

foreach (Multi::zipEqual($letters, $numbers) as [$letter, $number]) {
    // ['A', 1], ['B', 2], ['C', 3]
}
```

### ZipFilled
Iterate multiple iterable collections simultaneously, using a default filler value if lengths are not equal.

```Multi::zipFilled(mixed $filler, iterable ...$iterables)```

```php
use IterTools\Multi;

$default = '?';
$letters = ['A', 'B'];
$numbers = [1, 2, 3];

foreach (Multi::zipFilled($default, $letters, $numbers) as [$letter, $number]) {
    // ['A', 1], ['B', 2], ['?', 3]
}
```

### ZipLongest
Iterate multiple iterable collections simultaneously.

```Multi::zipLongest(iterable ...$iterables)```

For uneven lengths, the exhausted iterables will produce `null` for the remaining iterations.

```php
use IterTools\Multi;

$letters = ['A', 'B', 'C'];
$numbers = [1, 2];

foreach (Multi::zipLongest($letters, $numbers) as [$letter, $number]) {
    // ['A', 1], ['B', 2], ['C', null]
}
```

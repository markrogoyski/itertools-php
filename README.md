# IterTools PHP

A PHP library of iteration tools to power up your loops.

Inspired by Python—designed for PHP.

Quick Reference
-----------

#### Multi Iteration
| Iterator | Description | Code Snippet |
| ----------- | ----------- | ----------- |
| [`chain`](#Chain) | Chain multiple iterables together | `Multi::chain($collection1, $collection2)` |
| [`zip`](#Zip) | Iterate multiple collections simultaneously | `Multi::zip($collection1, $collection2)` |
| [`zipLongest`](#ZipLongest) | Iterate multiple collections simultaneously | `Multi::zipLongest($collection1, $collection2)` |

#### Single Iteration
| Iterator | Description | Code Snippet |
| ----------- | ----------- | ----------- |
| [`string`](#String) | Iterate the characters of a string | `Single::string($string)` |
| [`repeat`](#Repeat) | Repeat an item | `Single::repeat($item, $repetitions)` |

#### Infinite Iteration
| Iterator | Description | Code Snippet |
| ----------- | ----------- | ----------- |
| [`count`](#Count) | Count sequentially forever | `Infinite::count($start, $step)` |
| [`cycle`](#Cycle) | Cycle through a collection | `Infinite::cycle($collection)` |
| [`repeat`](#Repeat-Infinite) | Repeat an item forever | `Infinite::repeat($item)` |

#### Math Iteration
| Iterator | Description | Code Snippet |
| ----------- | ----------- | ----------- |
| [`runningProduct`](#Running-Product) | Running product accumulation | `Math::runningProduct($numbers, $initialValue` |
| [`runningTotal`](#Running-Total) | Running total accumulation | `Math::runningTotal($numbers, $initialValue` |

Setup
-----

 Add the library to your `composer.json` file in your project:

```javascript
{
  "require": {
      "markrogoyski/itertools-php": "0.*"
  }
}
```

Use [composer](http://getcomposer.org) to install the library:

```bash
$ php composer.phar install
```

Composer will install IterTools inside your vendor folder. Then you can add the following to your
.php files to use the library with Autoloading.

```php
require_once __DIR__ . '/vendor/autoload.php';
```

Alternatively, use composer on the command line to require and install IterTools:

```
$ php composer.phar require markrogoyski/itertools-php:0.*
```

#### Minimum Requirements
 * PHP 7.4



Usage
-----
## Multi Iteration
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
    print("The {$mascot} is the mascot of the {$language} language.");
}
// The elephant is the mascot of the PHP language.
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


## Single Iteration
### String
Iterate the individual characters of a string.

```Single::string(string $string)```
```php
use IterTools\Single;

$string = 'IterTools';

$listOfCharacters = [];
foreach (Single::string($string) as $character) {
    $listOfCharacters[] = $character;
}
// ['I', 't', 'e', 'r', 'T', 'o', 'o', 'l', 's']
```

### Repeat
Repeat an item.

```Single::repeat(mixed $item, int $repetitions)```
```php
use IterTools\Single;

$item        = 'IterTools';
$repetitions = 3;

foreach (Infinite::repeat($item) as $repeated) {
    print($repeated);
}
// 'IterTools', 'IterTools', 'IterTools'
```

## Infinite Iteration
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

### Repeat (Infinite)
Repeat an item forever.

```Infinite::repeat(mixed $item)```
```php
use IterTools\Infinite;

$item = 'IterTools';

foreach (Infinite::repeat($item) as $repeated) {
    print($repeated);
}
// 'IterTools', 'IterTools', 'IterTools', ...
```
## Math Iteration

### Running Total
Accumulate the running total over a list of numbers.

```Math::runningTotal(iterable $numbers, int|float $initialValue = null)```
```php
use IterTools\Math;

$prices = [1, 2, 3, 4, 5];

foreach (Math::runningTotal($numbers) as $runningTotal) {
    print($runningTotal);
}
// 1, 3, 6, 10, 15
```

Provide an optional initial value to lead off the running total.
```php
use IterTools\Math;

$prices       = [1, 2, 3, 4, 5];
$initialValue = 5;

foreach (Math::runningTotal($numbers, $initialValue) as $runningTotal) {
    print($runningTotal);
}
// 5, 6, 8, 11, 15, 20
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

## Composition
IterTools can be combined to create new iterable compositions.
#### Zip Strings
```php
use IterTools\Multi;
use IterTools\Single;

$letters = 'abc';
$numbers = '123';

foreach (Multi::zip(Single::string($letters), Single::string($numbers)) as [$letter, $number]) {
     print("{$letter}{$number}");
}
// a1, b2, c3
```

#### Chain Strings
```php
use IterTools\Multi;
use IterTools\Single;

$letters = 'abc';
$numbers = '123';

foreach (Multi::chain(Single::string($letters), Single::string($numbers)) as $character) {
    print($character);
}
// a, b, c, 1, 2, 3
```
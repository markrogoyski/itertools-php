# IterTools PHP
### Iteration Tools Library for PHP


Quick Reference
-----------

#### Multi Iteration
| Iterator      | Description | Code Snippet |
| ----------- | ----------- | ----------- |
| [`zip`](#Zip) | Iterate multiple collections simultaneously | `Multi::zip($collection1, $collection2)` |
| [`zipLongest`](#ZipLongest) | Iterate multiple collections simultaneously | `Multi::zip($collection1, $collection2)` |

#### Single Iteration
| Iterator      | Description | Code Snippet |
| ----------- | ----------- | ----------- |
| [`string`](#String) | Iterate the characters of a string | `Single::string($string)` |
| [`repeat`](#Repeat) | Repeat an item | `Single::repeat($item, $repetitions)` |

#### Infinite Iteration
| Iterator      | Description | Code Snippet |
| ----------- | ----------- | ----------- |
| [`count`](#Count) | Count sequentially forever | `Infinite::count($start, $step)` |
| [`cycle`](#Cycle) | Cycle through a collection | `Infinite::cycle($collection)` |
| [`repeat`](#Repeat-Infinite) | Repeat an item forever | `Infinite::repeat($item)` |

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
$names         = ['Ryu', 'Ken', 'Chun Li', 'Guile'];
$country       = ['Japan', 'USA', 'China', 'USA'];
$signatureMove = ['hadouken', 'shoryuken', 'spinning bird kick', 'sonic boom'];

foreach (Multi::zip($names, $country, $signatureMove) as [$name, $country, $signatureMove]) {
    $streetFighter = new StreetFighter($name, $country, $signatureMove);
}
```
Note: For uneven lengths, iteration stops when the shortest iterable is exhausted.

### ZipLongest
Iterate multiple iterable collections simultaneously.

```Multi::zipLongest(iterable ...$iterables)```

For uneven lengths, the exhausted iterables will produce null for the remaining iterations.

```php
use IterTools\Multi;

$letters = ['A', 'B', 'C'];
$numbers   = [1, 2];

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
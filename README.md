![MathPHP Logo](https://github.com/markrogoyski/itertools-php/blob/main/docs/image/IterToolsLogo.png?raw=true)

### IterTools - PHP Iteration Tools to Power Up Your Loops

Inspired by Python—designed for PHP.

[![Coverage Status](https://coveralls.io/repos/github/markrogoyski/itertools-php/badge.svg?branch=main)](https://coveralls.io/github/markrogoyski/itertools-php?branch=main)
[![License](https://poser.pugx.org/markrogoyski/math-php/license)](https://packagist.org/packages/markrogoyski/itertools-php)

Quick Reference
-----------

#### Multi Iteration
| Iterator | Description | Code Snippet |
| ----------- | ----------- | ----------- |
| [`chain`](#Chain) | Chain multiple iterables together | `Multi::chain($list1, $list2)` |
| [`zip`](#Zip) | Iterate multiple collections simultaneously | `Multi::zip($list1, $list2)` |
| [`zipLongest`](#ZipLongest) | Iterate multiple collections simultaneously | `Multi::zipLongest($list1, $list2)` |

#### Single Iteration
| Iterator                         | Description                                     | Code Snippet                                |
|----------------------------------|-------------------------------------------------|---------------------------------------------|
| [`compress`](#Compress)          | Filter out elements not selected                | `Single::compress($data, $selectors)`       |
| [`dropWhile`](#Drop-While)       | Drop elements while predicate is true           | `Single::dropWhile($data, $predicate)`      |
| [`filterFalse`](#Filter-False)   | Filter out elements where predicate not false   | `Single::filterFalse($data, $predicate)`    |
| [`filterTrue`](#Filter-True)     | Filter out elements where predicate not true    | `Single::filterTrue($data, $predicate)`     |
| [`filterUnique`](#Filter-Unique) | Filter out elements: iterate only unique items  | `Single::filterUnique($data, $strict)`      |
| [`groupBy`](#Group-By)           | Group data by a common element                  | `Single::groupBy($data, $groupKeyFunction)` |
| [`repeat`](#Repeat)              | Repeat an item                                  | `Single::repeat($item, $repetitions)`       |
| [`string`](#String)              | Iterate the characters of a string              | `Single::string($string)`                   |
| [`takeWhile`](#Take-While)       | Iterate elements while predicate is true        | `Single::takeWhile($data, $predicate)`      |
| [`eachPair`](#Each-Pair)         | Iterate pairs of elements from given collection | `Single::eachPair($data)`                   |

#### Infinite Iteration
| Iterator | Description | Code Snippet |
| ----------- | ----------- | ----------- |
| [`count`](#Count) | Count sequentially forever | `Infinite::count($start, $step)` |
| [`cycle`](#Cycle) | Cycle through a collection | `Infinite::cycle($collection)` |
| [`repeat`](#Repeat-Infinite) | Repeat an item forever | `Infinite::repeat($item)` |

#### Random Iteration
| Iterator | Description | Code Snippet |
| ----------- | ----------- | ----------- |
| [`choice`](#Choice) | Random selections from list | `Random::choice($list, $repetitions)` |
| [`coinFlip`](#CoinFlip) | Random coin flips (0 or 1) | `Random::coinFlip($repetitions)` |
| [`number`](#Number) | Random numbers | `Random::number($min, $max, $repetitions)` |
| [`percentage`](#Percentage) | Random percentage between 0 and 1 | `Random::percentage($repetitions)` |
| [`rockPaperScissors`](#RockPaperScissors) | Random rock-paper-scissors hands | `Random::rockPaperScissors($repetitions)` |

#### Math Iteration
| Iterator | Description | Code Snippet |
| ----------- | ----------- | ----------- |
| [`runningAverage`](#Running-Averange) | Running average accumulation | `Math::runningAverage($numbers, $initialValue)` |
| [`runningDifference`](#Running-Difference) | Running difference accumulation | `Math::runningDifference($numbers, $initialValue)` |
| [`runningMax`](#Running-Max) | Running maximum accumulation | `Math::runningMax($numbers, $initialValue)` |
| [`runningMin`](#Running-Min) | Running minimum accumulation | `Math::runningMin($numbers, $initialValue)` |
| [`runningProduct`](#Running-Product) | Running product accumulation | `Math::runningProduct($numbers, $initialValue)` |
| [`runningTotal`](#Running-Total) | Running total accumulation | `Math::runningTotal($numbers, $initialValue)` |

#### Reduce
| Reducer                                     | Description                                                         | Code Snippet                                      |
|---------------------------------------------|---------------------------------------------------------------------|---------------------------------------------------|
| [`toValue`](#To-Value)                      | Reduce collection using callable reducer                            | `Reduce::toValue($data, $reducer, $initialValue)` |
| [`toMin`](#To-Min)                          | Reduce collection to it's greatest element                          | `Reduce::toMin($data)`                            |
| [`toMax`](#To-Max)                          | Reduce collection to it's smallest element                          | `Reduce::toMax($data)`                            |
| [`toCount`](#To-Count)                      | Reduce collection to it's length                                    | `Reduce::toCount($data)`                          |
| [`toSum`](#To-Sum)                          | Reduce collection to the sum of it's elements                       | `Reduce::toSum($data)`                            |
| [`toProduct`](#To-Product)                  | Reduce collection to the product of it's elements                   | `Reduce::toProduct($data)`                        |
| [`toAverage`](#To-Average)                  | Reduce collection to the average of it's elements                   | `Reduce::toAverage($data)`                        |
| [`isSortedDirectly`](#Is-Sorted-Directly)   | Reduce collection to true if it is sorted directly otherwise false  | `Reduce::isSortedDirectly($data)`                 |
| [`isSortedReversely`](#Is-Sorted-Reversely) | Reduce collection to true if it is sorted reversely otherwise false | `Reduce::isSortedReversely($data)`                |

Setup
-----

 Add the library to your `composer.json` file in your project:

```json
{
  "require": {
      "markrogoyski/itertools-php": "1.*"
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
$ php composer.phar require markrogoyski/itertools-php:1.*
```

#### Minimum Requirements
 * PHP 7.4



Usage
-----
All functions work on `iterable` collections:
* `array` (type)
* `Generator` (type)
* `Iterator` (interface)
* `Traversable` (interface)

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
### Compress
Compress an iterable by filtering out data that is not selected.

```Single::compress(string $data, $selectors)```
```php
use IterTools\Single;

$movies  = [
    'Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith',
    'A New Hope', 'Empire Strikes Back', 'Return of the Jedi',
    'The Force Awakens', 'The Last Jedi', 'The Rise of Skywalker'
];
$goodMovies = [0, 0, 0, 1, 1, 1, 1, 0, 0];

foreach (Single::compress($movies, $goodMovies) as $goodMovie) {
    print($goodMovie);
}
// 'A New Hope', 'Empire Strikes Back', 'Return of the Jedi', 'The Force Awakens'
```

### Drop While
Drop elements from the iterable while the predicate function is true.

Once the predicate function returns false once, all remaining elements are returned.

```Single::dropWhile(iterable $data, callable $predicate)```
```php
Use IterTools\Single;

$scores    = [50, 60, 70, 85, 65, 90];
$predicate = fn ($x) => $x < 70;

foreach (Single::dropWhile($scores, $predicate) as $score) {
    print($score);
}
// 70, 85, 65, 90
```

### Filter False
Filter out elements from the iterable only returning elements where the predicate function is false.

If no predicate is provided, the boolean value of the data is used.

```Single::filterFalse(iterable $data, callable $predicate)```
```php
Use IterTools\Single;

$starWarsEpisodes   = [1, 2, 3, 4, 5, 6, 7, 8, 9];
$goodMoviePredicate = fn ($episode) => $episode > 3 && $episode < 8;

foreach (Single::filterFalse($starWarsEpisodes, $goodMoviePredicate) as $badMovie) {
    print($badMovie);
}
// 1, 2, 3, 8, 9
```

### Filter True
Filter out elements from the iterable only returning elements where the predicate function is true.

If no predicate is provided, the boolean value of the data is used.

```Single::filterFalse(iterable $data, callable $predicate)```
```php
Use IterTools\Single;

$starWarsEpisodes   = [1, 2, 3, 4, 5, 6, 7, 8, 9];
$goodMoviePredicate = fn ($episode) => $episode > 3 && $episode < 8;

foreach (Single::filterTrue($starWarsEpisodes, $goodMoviePredicate) as $goodMovie) {
    print($goodMovie);
}
// 4, 5, 6, 7
```

### Filter Unique
Filter out elements from the iterable only returning unique elements.

If $strict is true:
- scalars: compares strictly by type;
  - objects: always treats different instances as not equal to each other;
  - arrays: compares serialized.

 If $strict is false:
  - scalars: compares non-strictly by value;
  - objects: compares serialized;
  - arrays: compares serialized.

```Single::filterUnique(iterable $data, bool $strict)```
```php
Use IterTools\Single;

$input = [1, 2, 1, 2, 3, 3, '1', '1', '2', '3'];

foreach (Single::filterUnique($input, true) as $datum) {
    print($datum);
}
// 1, 2, 3, '1', '2', '3'

foreach (Single::filterUnique($input, false) as $datum) {
    print($datum);
}
// 1, 2, 3
```

### Group By
Group data by a common data element.

The groupKeyFunction determines the key to group elements by.

```Single::groupBy(iterable $data, callable $groupKeyFunction)```
```php
Use IterTools\Single;

$cartoonCharacters = [
    ['Garfield', 'cat'],
    ['Tom', 'cat'],
    ['Felix', 'cat'],
    ['Heathcliff', 'cat'],
    ['Snoopy', 'dog'],
    ['Scooby-Doo', 'dog'],
    ['Odie', 'dog'],
    ['Donald', 'duck'],
    ['Daffy', 'duck'],
];

$charactersGroupedByAnimal = [];
foreach (Single::groupBy($cartoonCharacters, fn ($x) => $x[1]) as $animal => $characters) {
    $charactersGroupedByAnimal[$animal] = $characters;
}
/*
'cat' => [
    ['Garfield', 'cat'],
    ['Tom', 'cat'],
    ['Felix', 'cat'],
    ['Heathcliff', 'cat'],
],
'dog' => [
    ['Snoopy', 'dog'],
    ['Scooby-Doo', 'dog'],
    ['Odie', 'dog'],
],
'duck' => [
    ['Donald', 'duck'],
    ['Daffy', 'duck'],
*/
```

### Repeat
Repeat an item.

```Single::repeat(mixed $item, int $repetitions)```
```php
use IterTools\Single;

$data        = 'Beetlejuice';
$repetitions = 3;

foreach (Single::repeat($data, $repetitions) as $repeated) {
    print($repeated);
}
// 'Beetlejuice', 'Beetlejuice', 'Beetlejuice'
```

### String
Iterate the individual characters of a string.

```Single::string(string $string)```
```php
use IterTools\Single;

$string = 'MickeyMouse';

$listOfCharacters = [];
foreach (Single::string($string) as $character) {
    $listOfCharacters[] = $character;
}
// ['M', 'i', 'c', 'k', 'e', 'y', 'M', 'o', 'u', 's', 'e']
```

### Take While
Return elements from the iterable as long as the predicate is true.

Stops iteration as soon as the predicate returns false, even if other elements later on would eventually return true (different from filterTrue).

```Single::takeWhile(iterable $data, callable $predicate)```
```php
Use IterTools\Single;

$prices = [0, 0, 5, 10, 0, 0, 9];
$isFree = fn ($price) => $price == 0;

foreach (Single::takeWhile($prices, $isFree) as $freePrice) {
    print($freePrice);
}
// 0, 0
```

### Each pair
Return pairs of elements from given collection.

Returns empty generator if given collection contains less than 2 elements.

```Single::eachPair(iterable $data)```
```php
Use IterTools\Single;

$data = [1, 2, 3, 4, 5];

foreach (Single::eachPair($data) as [$lhs, $rhs]) {
    print("{$lhs}_{$lhs}");
}
// 1_2, 2_3, 3_4, 4_5
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

$dialogue = 'Are we there yet?';

foreach (Infinite::repeat($dialogue) as $repeated) {
    print($repeated);
}
// 'Are we there yet?', 'Are we there yet?', 'Are we there yet?', ...
```

## Random Iteration
### Choice
Generate random selections from an array of values.

```Random::choice(array $items, int $repetitions)```
```php
use IterTools\Random;

$cards       = ['Ace', 'King', 'Queen', 'Jack', 'Joker'];
$repetitions = 10;

foreach (Random::choice($cards, $repetitions) as $card) {
    print($card);
}
// 'King', 'Jack', 'King', 'Ace', ... [random]
```

### CoinFlip
Generate random coin flips (0 or 1).

```Random::coinFlip(int $repetitions)```
```php
use IterTools\Random;

$repetitions = 10;

foreach (Random::coinFlip($repetitions) as $coinFlip) {
    print($coinFlip);
}
// 1, 0, 1, 1, 0, ... [random]
```

### Number
Generate random numbers (integers).

```Random::number(int $min, int $max, int $repetitions)```
```php
use IterTools\Random;

$min         = 1;
$max         = 4;
$repetitions = 10;

foreach (Random::number($min, $max, $repetitions) as $number) {
    print($number);
}
// 3, 2, 5, 5, 1, 2, ... [random]
```

### Percentage
Generate a random percentage between 0 and 1.

```Random::percentage(int $repetitions)```
```php
use IterTools\Random;

$repetitions = 10;

foreach (Random::percentage($repetitions) as $percentage) {
    print($percentage);
}
// 0.30205562629132, 0.59648594775233, ... [random]
```

### RockPaperScissors
Generate random rock-paper-scissors hands.

```Random::rockPaperScissors(int $repetitions)```
```php
use IterTools\Random;

$repetitions = 10;

foreach (Random::rockPaperScissors($repetitions) as $rpsHand) {
    print($rpsHand);
}
// 'paper', 'rock', 'rock', 'scissors', ... [random]
```

## Math Iteration
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

## Reduce
### To Value
Reduce elements into a single value using reducer function.

Works like array_reduce() function, but can take in all iterable types, not array only.

```Reduce::toValue(iterable $data, callable $reducer, mixed $initialValue)```
```php
use IterTools\Reduce;

$input = [1, 2, 3, 4, 5];
$sum = fn ($carry, $item) => $carry + $item;

$result = Reduce::toValue($input, $sum, 0);
// 15
```

### To Min
Reduces given iterable to it's min value.

Items of given collection must be comparable.

Returns null if given collection is empty.

```Reduce::toMin(iterable $data)```
```php
use IterTools\Reduce;

$input = [5, 3, 1, 2, 4];

$result = Reduce::toMin($input);
// 1
```

### To Max
Reduces given iterable to it's max value.

Items of given collection must be comparable.

Returns null if given collection is empty.

```Reduce::toMax(iterable $data)```
```php
use IterTools\Reduce;

$input = [5, 3, 1, 2, 4];

$result = Reduce::toMax($input);
// 5
```

### To Count
Reduces given iterable to it's length.

```Reduce::toCount(iterable $data)```
```php
use IterTools\Reduce;

$input = [10, 20, 30];

$result = Reduce::toCount($input);
// 3
```

### To Sum
Reduces given collection to the sum of it's items.

```Reduce::toSum(iterable $data)```
```php
use IterTools\Reduce;

$input = [10, 20, 30];

$result = Reduce::toSum($input);
// 60
```

### To Product
Reduces given collection to the product of it's items.

Returns null if given collection is empty.

```Reduce::toProduct(iterable $data)```
```php
use IterTools\Reduce;

$input = [3, 4, 2];

$result = Reduce::toProduct($input);
// 24
```

### To Average
Reduces given collection to the average of it's items.

Returns null if given collection is empty.

```Reduce::toAverage(iterable $data)```
```php
use IterTools\Reduce;

$input = [2, 4, 6, 8];

$result = Reduce::toAverage($input);
// 5
```

### Is Sorted Directly
Returns true if given collection is sorted directly otherwise false.

Items of given collection must be comparable.

Returns true if given collection is empty or has one element.

```Reduce::isSortedDirectly(iterable $data)```
```php
use IterTools\Reduce;

$input = [1, 2, 3, 4, 5];

$result = Reduce::isSortedDirectly($input);
// true

$input = [3, 2, 3, 4, 5];

$result = Reduce::isSortedDirectly($input);
// false
```

### Is Sorted Reversely
Returns true if given collection is sorted reversely otherwise false.

Items of given collection must be comparable.

Returns true if given collection is empty or has one element.

```Reduce::isSortedReversely(iterable $data)```
```php
use IterTools\Reduce;

$input = [5, 4, 3, 2, 1];

$result = Reduce::isSortedReversely($input);
// true

$input = [1, 4, 3, 2, 1];

$result = Reduce::isSortedReversely($input);
// false
```

## Composition
IterTools can be combined to create new iterable compositions.
#### Zip Strings
```php
use IterTools\Multi;
use IterTools\Single;

$letters = 'ABCDEFGHI';
$numbers = '123456789';

foreach (Multi::zip(Single::string($letters), Single::string($numbers)) as [$letter, $number]) {
     $battleshipMove = new BattleshipMove($letter, $number)
}
// A1, B2, C3
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

Standards
---------

IterTools PHP conforms to the following standards:

 * PSR-1  - Basic coding standard (http://www.php-fig.org/psr/psr-1/)
 * PSR-4  - Autoloader (http://www.php-fig.org/psr/psr-4/)
 * PSR-12 - Extended coding style guide (http://www.php-fig.org/psr/psr-12/)

License
-------

IterTools PHP is licensed under the MIT License.
![MathPHP Logo](https://github.com/markrogoyski/itertools-php/blob/main/docs/image/IterToolsLogo.png?raw=true)

### IterTools - PHP Iteration Tools to Power Up Your Loops

Inspired by Python—designed for PHP.

[![Coverage Status](https://coveralls.io/repos/github/markrogoyski/itertools-php/badge.svg?branch=main)](https://coveralls.io/github/markrogoyski/itertools-php?branch=main)
[![License](https://poser.pugx.org/markrogoyski/math-php/license)](https://packagist.org/packages/markrogoyski/itertools-php)

Quick Reference
-----------

### Loop Iteration Tools

#### Multi Iteration
| Iterator                    | Description                                                                             | Code Snippet                        |
|-----------------------------|-----------------------------------------------------------------------------------------|-------------------------------------|
| [`chain`](#Chain)           | Chain multiple iterables together                                                       | `Multi::chain($list1, $list2)`      |
| [`zip`](#Zip)               | Iterate multiple collections simultaneously until the shortest iterator completes       | `Multi::zip($list1, $list2)`        |
| [`zipLongest`](#ZipLongest) | Iterate multiple collections simultaneously until the longest iterator completes        | `Multi::zipLongest($list1, $list2)` |
| [`zipEqual`](#ZipEqual)     | Iterate multiple collections of equal length simultaneously, error if lengths not equal | `Multi::zipEqual($list1, $list2)`   |

#### Single Iteration
| Iterator                                 | Description                                   | Code Snippet                                                |
|------------------------------------------|-----------------------------------------------|-------------------------------------------------------------|
| [`chunkwise`](#Chunkwise)                | Iterate by chunks                             | `Single::chunkwise($data, $chunkSize)`                      |
| [`chunkwiseOverlap`](#Chunkwise-Overlap) | Iterate by overlapped chunks                  | `Single::chunkwiseOverlap($data, $chunkSize, $overlapSize)` |
| [`compress`](#Compress)                  | Filter out elements not selected              | `Single::compress($data, $selectors)`                       |
| [`dropWhile`](#Drop-While)               | Drop elements while predicate is true         | `Single::dropWhile($data, $predicate)`                      |
| [`filterFalse`](#Filter-False)           | Filter out elements where predicate not false | `Single::filterFalse($data, $predicate)`                    |
| [`filterTrue`](#Filter-True)             | Filter out elements where predicate not true  | `Single::filterTrue($data, $predicate)`                     |
| [`groupBy`](#Group-By)                   | Group data by a common element                | `Single::groupBy($data, $groupKeyFunction)`                 |
| [`limit`](#Limit)                        | Iterate up to a limit                         | `Single::limit($data, $limit)`                              |
| [`pairwise`](#Pairwise)                  | Iterate successive overlapping pairs          | `Single::pairwise($data)`                                   |
| [`repeat`](#Repeat)                      | Repeat an item                                | `Single::repeat($item, $repetitions)`                       |
| [`string`](#String)                      | Iterate the characters of a string            | `Single::string($string)`                                   |
| [`takeWhile`](#Take-While)               | Iterate elements while predicate is true      | `Single::takeWhile($data, $predicate)`                      |

#### Infinite Iteration
| Iterator                     | Description                | Code Snippet                     |
| ---------------------------- | -------------------------- | -------------------------------- |
| [`count`](#Count)            | Count sequentially forever | `Infinite::count($start, $step)` |
| [`cycle`](#Cycle)            | Cycle through a collection | `Infinite::cycle($collection)`   |
| [`repeat`](#Repeat-Infinite) | Repeat an item forever     | `Infinite::repeat($item)`        |

#### Random Iteration
| Iterator                                  | Description                       | Code Snippet                               |
| ----------------------------------------- | --------------------------------- | ------------------------------------------ |
| [`choice`](#Choice)                       | Random selections from list       | `Random::choice($list, $repetitions)`      |
| [`coinFlip`](#CoinFlip)                   | Random coin flips (0 or 1)        | `Random::coinFlip($repetitions)`           |
| [`number`](#Number)                       | Random numbers                    | `Random::number($min, $max, $repetitions)` |
| [`percentage`](#Percentage)               | Random percentage between 0 and 1 | `Random::percentage($repetitions)`         |
| [`rockPaperScissors`](#RockPaperScissors) | Random rock-paper-scissors hands  | `Random::rockPaperScissors($repetitions)`  |

#### Math Iteration
| Iterator                                   | Description                     | Code Snippet                                       |
| ------------------------------------------ | ------------------------------- | -------------------------------------------------- |
| [`runningAverage`](#Running-Average)       | Running average accumulation    | `Math::runningAverage($numbers, $initialValue)`    |
| [`runningDifference`](#Running-Difference) | Running difference accumulation | `Math::runningDifference($numbers, $initialValue)` |
| [`runningMax`](#Running-Max)               | Running maximum accumulation    | `Math::runningMax($numbers, $initialValue)`        |
| [`runningMin`](#Running-Min)               | Running minimum accumulation    | `Math::runningMin($numbers, $initialValue)`        |
| [`runningProduct`](#Running-Product)       | Running product accumulation    | `Math::runningProduct($numbers, $initialValue)`    |
| [`runningTotal`](#Running-Total)           | Running total accumulation      | `Math::runningTotal($numbers, $initialValue)`      |

#### Summary
| Summary                      | Description                                             | Code Snippet                               |
|------------------------------|---------------------------------------------------------|--------------------------------------------|
| [`exactlyN`](#Exactly-N)     | True if exactly n items are true according to predicate | `Summary::exactlyN($data, $n, $predicate)` |
| [`isSorted`](#Is-Sorted)     | True if iterable sorted                                 | `Summary::isSorted($data)`                 |
| [`isReversed`](#Is-Reversed) | True if iterable reverse sorted                         | `Summary::isReversed($data)`               |
| [`same`](#Same)              | True if iterables are the same                          | `Summary::same(...$iterables)`             |
| [`sameCount`](#Same-Count)   | True if iterables have the same lengths                 | `Summary::sameCount(...$iterables)`        |

#### Reduce
| Reducer                    | Description                             | Code Snippet                                      |
|----------------------------|-----------------------------------------|---------------------------------------------------|
| [`toAverage`](#To-Average) | Mean average of elements                | `Reduce::toAverage($numbers)`                     |
| [`toCount`](#To-Count)     | Reduce to length of iterable            | `Reduce::toCount($data)`                          |
| [`toMax`](#To-Max)         | Reduce to its largest element           | `Reduce::toMax($numbers)`                         |
| [`toMin`](#To-Min)         | Reduce to its smallest element          | `Reduce::toMin($numbers)`                         |
| [`toProduct`](#To-Product) | Reduce to the product of its elements   | `Reduce::toProduct($numbers)`                     |
| [`toSum`](#To-Sum)         | Reduce to the sum of its elements       | `Reduce::toSum($numbers)`                         |
| [`toValue`](#To-Value)     | Reduce to value using callable reducer  | `Reduce::toValue($data, $reducer, $initialValue)` |

### Stream Iteration Tools
#### Stream Sources
| Source                                           | Description                                                     | Code Snippet                                        |
|--------------------------------------------------|-----------------------------------------------------------------|-----------------------------------------------------|
| [`of`](#Of)                                      | Start a fluent stream with an iterable                          | `Stream::of($iterable)`                             |
| [`ofEmpty`](#Of-Empty)                           | Start a fluent stream with empty iterable source                | `Stream::ofEmpty()`                                 |
| [`ofRandomChoice`](#Of-Random-Choice)            | Start a fluent stream random selections from an array of values | `Stream::ofRandomChoice($items, $repetitions)`      |
| [`ofRandomNumbers`](#Of-Random-Numbers)          | Start a fluent stream random numbers (integers)                 | `Stream::ofRandomNumbers($min, $max, $repetitions)` |
| [`ofRandomPercentage`](#Of-Random-Percentage)    | Start a fluent stream random percentages between 0 and 1        | `Stream::ofRandomPercentage($repetitions)`          |
| [`ofCoinFlips`](#Of-Coin-Flips)                  | Start a fluent stream random coin flips                         | `Stream::ofCoinFlips($repetitions)`                 |
| [`ofRockPaperScissors`](#Of-Rock-Paper-Scissors) | Start a fluent stream rock-paper-scissors hands                 | `Stream::ofRockPaperScissors($repetitions)`         |

#### Stream Operations
| Operation                                    | Description                                                                               | Code Snippet                                          |
|----------------------------------------------|-------------------------------------------------------------------------------------------|-------------------------------------------------------|
| [`chain`](#Chain-1)                          | Chain additional iterators to stream                                                      | `$stream->chain($selectors)`                          |
| [`compress`](#Compress-1)                    | Compress an iterable source by filtering out data that is not selected                    | `$stream->compress($selectors)`                       |
| [`chunkwise`](#Chunkwise-1)                  | Iterate by chunks                                                                         | `$stream->chunkwise($chunkSize)`                      |
| [`chunkwiseOverlap`](#Chunkwise-Overlap-1)   | Iterate by overlapped chunks                                                              | `$stream->chunkwiseOverlap($chunkSize, $overlapSize)` |
| [`dropWhile`](#Drop-While-1)                 | Drop elements from the iterable source while the predicate function is true               | `$stream->dropWhile($predicate)`                      |
| [`takeWhile`](#Take-While-1)                 | Return elements from the iterable source as long as the predicate is true                 | `$stream->takeWhile($predicate)`                      |
| [`filterTrue`](#Filter-True-1)               | Filter out elements from the iterable source where there predicate function is true       | `$stream->filterTrue($predicate)`                     |
| [`filterFalse`](#Filter-False-1)             | Filter out elements from the iterable source where the predicate function is false        | `$stream->filterFalse($predicate)`                    |
| [`groupBy`](#Group-By-1)                     | Group iterable source by a common data element                                            | `$stream->groupBy($groupKeyFunction)`                 |
| [`pairwise`](#Pairwise-1)                    | Return pairs of elements from iterable source                                             | `$stream->pairwise()`                                 |
| [`limit`](#Limit-1)                          | Limit the stream's iteration                                                              | `$stream->limit($limit)`                              |
| [`chainWith`](#Chain-With)                   | Chain iterable source withs given iterables together into a single iteration              | `$stream->chainWith(...$iterables)`                   |
| [`zipWith`](#Zip-With)                       | Iterate iterable source with another iterable collections simultaneously                  | `$stream->zipWith(...$iterables)`                     |
| [`zipLongestWith`](#Zip-Longest-With)        | Iterate iterable source with another iterable collections simultaneously                  | `$stream->zipLongestWith(...$iterables)`              |
| [`zipEqualWith`](#Zip-Equal-With)            | Iterate iterable source with another iterable collections of equal lengths simultaneously | `$stream->zipEqualWith(...$iterables)`                |
| [`infiniteCycle`](#Infinite-Cycle)           | Cycle through the elements of iterable source sequentially forever                        | `$stream->infiniteCycle()`                            |
| [`runningAverage`](#Running-Average-1)       | Accumulate the running average (mean) over iterable source                                | `$stream->runningAverage($initialValue)`              |
| [`runningDifference`](#Running-Difference-1) | Accumulate the running difference over iterable source                                    | `$stream->runningDifference($initialValue)`           |
| [`runningMax`](#Running-Max-1)               | Accumulate the running max over iterable source                                           | `$stream->runningMax($initialValue)`                  |
| [`runningMin`](#Running-Min-1)               | Accumulate the running min over iterable source                                           | `$stream->runningMin($initialValue)`                  |
| [`runningProduct`](#Running-Product-1)       | Accumulate the running product over iterable source                                       | `$stream->runningProduct($initialValue)`              |
| [`runningTotal`](#Running-Total-1)           | Accumulate the running total over iterable source                                         | `$stream->runningTotal($initialValue)`                |

#### Stream Terminal Operations
##### Summary Terminal Operations
| Terminal Operation                           | Description                                                                      | Code Snippet                                 |
|----------------------------------------------|----------------------------------------------------------------------------------|----------------------------------------------|
| [`isSorted`](#Is-Sorted-1)                   | Returns true if stream is sorted in ascending order                              | `$stream->isSorted()`                        |
| [`isReversed`](#Is-Reversed-1)               | Returns true if stream is sorted in reverse descending order                     | `$stream->isReversed()`                      |
| [`sameWith`](#Same-With)                     | Returns true if stream and all given collections are the same                    | `$stream->sameWith(...$iterables)`           |
| [`sameCountWith`](#Same-Count-With)          | Returns true if stream and all given collections have the same lengths           | `$stream->sameCountWith(...$iterables)`      |

##### Reduction Terminal Operations
| Terminal Operation                           | Description                                                                      | Code Snippet                                 |
|----------------------------------------------|----------------------------------------------------------------------------------|----------------------------------------------|
| [`toAverage`](#To-Average-1)                 | Reduces stream to the mean average of its items                                  | `$stream->toAverage()`                       |
| [`toCount`](#To-Count-1)                     | Reduces stream to its length                                                     | `$stream->toCount()`                         |
| [`toMax`](#To-Max-1)                         | Reduces stream to its max value                                                  | `$stream->toMax()`                           |
| [`toMin`](#To-Min-1)                         | Reduces stream to its min value                                                  | `$stream->toMin()`                           |
| [`toProduct`](#To-Product-1)                 | Reduces stream to the product of its items                                       | `$stream->toProduct()`                       |
| [`toSum`](#To-Sum-1)                         | Reduces stream to the sum of its items                                           | `$stream->toSum()`                           |
| [`toValue`](#To-Value-1)                     | Reduces stream like array_reduce() function                                      | `$stream->toValue($reducer, $initialValue)`  |

##### Side Effect Terminal Operations
| Terminal Operation         | Description                     | Code Snippet           |
|----------------------------|---------------------------------|------------------------|
| [`print`](#Print)          | `print` each item in the stream | `$stream->print()`     |
| [`printLn`](#Print-Ln)     | `print` each item on a new line | `$stream->printLn()`   |
| [`printR`](#Print-R)       | `print_r` each item             | `$stream->printR()`    |
| [`var_dump`](#Var-Dump)    | `var_dump` each item            | `$stream->varDump()`   |

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

## Single Iteration
### Chunkwise
Return elements in chunks of a certain size.

```Single::chunkwise(iterable $data, int $chunkSize)```

Chunk size must be at least 1.

```php
use IterTools\Single;

$movies = [
    'Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith',
    'A New Hope', 'Empire Strikes Back', 'Return of the Jedi',
    'The Force Awakens', 'The Last Jedi', 'The Rise of Skywalker'
];

foreach (Single::chunkwise($movies, 3) as $trilogy) {
    $trilogies[] = $trilogy;
}
// [
//     ['Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith'],
//     ['A New Hope', 'Empire Strikes Back', 'Return of the Jedi'],
//     ['The Force Awakens', 'The Last Jedi', 'The Rise of Skywalker]'
// ]
```

### Chunkwise Overlap
Return overlapped chunks of elements.

```Single::chunkwiseOverlap(iterable $data, int $chunkSize, int $overlapSize)```

* Chunk size must be at least 1.
* Overlap size must be less than chunk size.

```php
use IterTools\Single;

$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

foreach (Single::chunkwiseOverlap($numbers, 3, 1) as $chunk) {
    // [1, 2, 3], [3, 4, 5], [5, 6, 7], [7, 8, 9], [9, 10]
}
```

### Compress
Compress an iterable by filtering out data that is not selected.

```Single::compress(string $data, $selectors)```
```php
use IterTools\Single;

$movies = [
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
use IterTools\Single;

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
use IterTools\Single;

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
use IterTools\Single;

$starWarsEpisodes   = [1, 2, 3, 4, 5, 6, 7, 8, 9];
$goodMoviePredicate = fn ($episode) => $episode > 3 && $episode < 8;

foreach (Single::filterTrue($starWarsEpisodes, $goodMoviePredicate) as $goodMovie) {
    print($goodMovie);
}
// 4, 5, 6, 7
```

### Group By
Group data by a common data element.

The groupKeyFunction determines the key to group elements by.

```Single::groupBy(iterable $data, callable $groupKeyFunction)```
```php
use IterTools\Single;

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

### Limit
Iterate up to a limit.

Stops even if more data available if limit reached.

```Single::limit(iterable $data, int $limit)```

```php
use IterTools\Single;

$matrixMovies = ['The Matrix', 'The Matrix Reloaded', 'The Matrix Revolutions', 'The Matrix Resurrections'];
$limit        = 1;

foreach (Single::limit($matrixMovies, $limit) as $goodMovie) {
    print($goodMovie);
}
// 'The Matrix' (and nothing else)
```

### Pairwise
Returns successive overlapping pairs.

Returns empty generator if given collection contains fewer than 2 elements.

```Single::pairwise(iterable $data)```

```php
use IterTools\Single;

$friends = ['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey', 'Phoebe'];

foreach (Single::pairwise($friends) as [$leftFriend, $rightFriend]) {
    print("{$leftFriend} and {$rightFriend}");
}
// Ross and Rachel, Rachel and Chandler, Chandler and Monica, ...
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
use IterTools\Single;

$prices = [0, 0, 5, 10, 0, 0, 9];
$isFree = fn ($price) => $price == 0;

foreach (Single::takeWhile($prices, $isFree) as $freePrice) {
    print($freePrice);
}
// 0, 0
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

## Summary

### Exactly N
Returns true if exactly n items are true according to a predicate function.

- Predicate is optional.
- Default predicate is boolean value of each item.

```Summary::exactlyN(iterable $data, int $n, callable $predicate)```

```php
use IterTools\Summary;

$twoTruthsAndALie = [true, true, false];
$n                = 2;

$boolean = Summary::exactlyN($twoTruthsAndALie, $n);
// true

$ages      = [18, 21, 24, 54];
$n         = 4;
$predicate = fn ($age) => $age >= 21;

$boolean = Summary::isSorted($ages, $n, $predicate);
// false
```

### Is Sorted
Returns true if elements are sorted, otherwise false.

- Elements must be comparable.
- Returns true if empty or has only one element.

```Summary::isSorted(iterable $data)```

```php
use IterTools\Summary;

$numbers = [1, 2, 3, 4, 5];

$boolean = Summary::isSorted($numbers);
// true

$numbers = [3, 2, 3, 4, 5];

$boolean = Summary::isSorted($numbers);
// false
```

### Is Reversed
Returns true if elements are reverse sorted, otherwise false.

- Elements must be comparable.
- Returns true if empty or has only one element.

```Summary::isReversed(iterable $data)```

```php
use IterTools\Summary;

$numbers = [5, 4, 3, 2, 1];

$boolean = Summary::isReversed($numbers);
// true

$numbers = [1, 4, 3, 2, 1];

$boolean = Summary::isReversed($numbers);
// false
```

### Same
Returns true if all given collections are the same.

For single iterable or empty iterables list returns true.

```Summary::same(iterable ...$iterables)```

```php
use IterTools\Summary;

$cocaColaIngredients = ['carbonated water', 'sugar', 'caramel color', 'phosphoric acid'];
$pepsiIngredients    = ['carbonated water', 'sugar', 'caramel color', 'phosphoric acid'];

$boolean = Summary::same($cocaColaIngredients, $pepsiIngredients);
// true

$cocaColaIngredients = ['carbonated water', 'sugar', 'caramel color', 'phosphoric acid'];
$spriteIngredients   = ['carbonated water', 'sugar', 'citric acid', 'lemon lime flavorings'];

$boolean = Summary::same($cocaColaIngredients, $spriteIngredients);
// false
```

### Same Count
Returns true if all given collections have the same lengths.

For single iterable or empty iterables list returns true.

```Summary::sameCount(iterable ...$iterables)```

```php
use IterTools\Summary;

$prequels  = ['Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith'];
$originals = ['A New Hope', 'Empire Strikes Back', 'Return of the Jedi'];
$sequels   = ['The Force Awakens', 'The Last Jedi', 'The Rise of Skywalker'];

$boolean = Summary::sameCount($prequels, $originals, $sequels);
// true

$batmanMovies = ['Batman Begins', 'The Dark Knight', 'The Dark Knight Rises'];
$matrixMovies = ['The Matrix', 'The Matrix Reloaded', 'The Matrix Revolutions', 'The Matrix Resurrections'];

$result = Summary::sameCount($batmanMovies, $matrixMovies);
// false
```

## Reduce

### To Average
Reduces to the mean average.

Returns null if collection is empty.

```Reduce::toAverage(iterable $data)```
```php
use IterTools\Reduce;

$grades = [100, 90, 95, 85, 94];

$finalGrade = Reduce::toAverage($numbers);
// 92.8
```

### To Max
Reduces to the max value.

- Elements must be comparable.
- Returns null if collection is empty.

```Reduce::toMax(iterable $data)```
```php
use IterTools\Reduce;

$numbers = [5, 3, 1, 2, 4];

$result = Reduce::toMax($numbers);
// 5
```

### To Min
Reduces to the min value.

- Elements must be comparable.
- Returns null if collection is empty.

```Reduce::toMin(iterable $data)```
```php
use IterTools\Reduce;

$numbers = [5, 3, 1, 2, 4];

$result = Reduce::toMin($numbers);
// 1
```

### To Count
Reduces iterable to its length.

```Reduce::toCount(iterable $data)```
```php
use IterTools\Reduce;

$someIterable = ImportantThing::getCollectionAsIterable();

$length = Reduce::toCount($someIterable);
// 3
```

### To Product
Reduces to the product of its elements.

Returns null if collection is empty.

```Reduce::toProduct(iterable $data)```
```php
use IterTools\Reduce;

$primeFactors = [5, 2, 2];

$number = Reduce::toProduct($primeFactors);
// 20
```

### To Sum
Reduces to the sum of its elements.

```Reduce::toSum(iterable $data)```
```php
use IterTools\Reduce;

$parts = [10, 20, 30];

$sum = Reduce::toSum($parts);
// 60
```

### To Value
Reduce elements to a single value using reducer function.

```Reduce::toValue(iterable $data, callable $reducer, mixed $initialValue)```
```php
use IterTools\Reduce;

$input = [1, 2, 3, 4, 5];
$sum   = fn ($carry, $item) => $carry + $item;

$result = Reduce::toValue($input, $sum, 0);
// 15
```

## Stream

### Of
Creates iterable instance with fluent interface.

```Stream::of(iterable $iterable): self```

```php
use IterTools\Stream;

$input = [1, 2, 3];

$result = Stream::of($iterable)
    ->chainWith(
        [4, 5, 6],
        [7, 8, 9]
    )
    ->zipEqualWith([1, 2, 3, 4, 5, 6, 7, 8, 9])
    ->toValue(fn ($carry, $item) => $carry + array_sum($item));
// 90
```

### Of Empty
Creates iterable instance with fluent interface from empty iterable source.

```Stream::ofEmpty(): self```

```php
use IterTools\Stream;

$result = Stream::ofEmpty()
    ->chainWith([1, 2, 3]);

foreach ($result as $item) {
    // 1, 2, 3
}
```

### Of Random Choice
Creates iterable instance with fluent interface of random selections from an array of values.

```Stream::ofRandomChoice(array $items, int $repetitions): self```

```php
use IterTools\Stream;

$languages = ['PHP', 'Go', 'Python'];

$result = Stream::ofRandomChoice($languages, 5);

foreach ($result as $language) {
    // 'Go', 'PHP', 'Python', 'PHP', 'PHP' [random]
}
```

### Of Random Numbers
Creates iterable instance with fluent interface of random numbers (integers).

```Stream::ofRandomNumbers(int $min, int $max, int $repetitions): self```

```php
use IterTools\Stream;

$result = Stream::ofRandomNumbers(1, 3, 7);

foreach ($result as $item) {
    // 1, 2, 2, 1, 3, 2, 1 [random]
}
```

### Of Random Percentage
Creates iterable instance with fluent interface of random percentages between 0 and 1.

```Stream::ofRandomPercentage(int $repetitions): self```

```php
use IterTools\Stream;

$result = Stream::ofRandomPercentage(3);

foreach ($result as $item) {
    // 0.8012566976245, 0.81237281724151, 0.61676896329459 [random]
}
```

### Of Coin Flips
Creates iterable instance with fluent interface of random coin flips.

```Stream::ofCoinFlips(int $repetitions): self```

```php
use IterTools\Stream;

$result = Stream::ofCoinFlips(10);

foreach ($result as $item) {
    // 1, 0, 0, 1, 1, 1, 0, 1, 0, 0 [random]
}
```

### Of Rock Paper Scissors
Creates iterable instance with fluent interface of rock-paper-scissors hands.

```Stream::ofRockPaperScissors(int $repetitions): self```

```php
use IterTools\Stream;

$result = Stream::ofRockPaperScissors(5);

foreach ($result as $item) {
    // 'paper', 'rock', 'rock', 'scissors', 'paper' [random]
}
```

### Compress
Compress an iterable source by filtering out data that is not selected.

```$stream->compress(iterable $selectors): self```

Selectors indicate which data. True value selects item. False value filters out data.

```php
use IterTools\Stream;

$input = [1, 2, 3];

$result = Stream::of($input)
    ->compress([0, 1, 1]);
    
foreach ($result as $item) {
    // 2, 3
}
```

### Drop While
Drop elements from the iterable source while the predicate function is true.

```$stream->dropWhile(callable $predicate): self```

Once the predicate function returns false once, all remaining elements are returned.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5]

$result = Stream::of($input)
    ->dropWhile(fn ($value) => $value < 3);
    
foreach ($result as $item) {
    // 3, 4, 5
}
```

### Take While
Return elements from the iterable source as long as the predicate is true.

```$stream->takeWhile(callable $predicate): self```

If no predicate is provided, the boolean value of the data is used.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->takeWhile(fn ($value) => abs($value) < 3);

foreach ($result as $item) {
    // 1, -1, 2, -2
}
```

### Filter True
Filter out elements from the iterable source only returning elements where there predicate function is true.

```$stream->filterTrue(callable $predicate): self```

If no predicate is provided, the boolean value of the data is used.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->filterTrue(fn ($value) => $value > 0);

foreach ($result as $item) {
    // 1, 2, 3
}
```

### Filter False
Filter out elements from the iterable source only returning elements where the predicate function is false.

```$stream->filterFalse(callable $predicate): self```

If no predicate is provided, the boolean value of the data is used.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->filterFalse(fn ($value) => $value > 0);

foreach ($result as $item) {
    // -1, -2, -3
}
```

### Group By
Group iterable source by a common data element.

```$stream->groupBy(callable $groupKeyFunction): self```

The groupKeyFunction determines the key to group elements by.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->groupBy(fn ($item) => $item > 0 ? 'positive' : 'negative');

foreach ($result as $group => $item) {
    // 'positive' => [1, 2, 3], 'negative' => [-1, -2, -3]
}
```

### Pairwise
Return pairs of elements from iterable source.

```$stream->pairwise(): self```

Returns empty generator if given collection contains less than 2 elements.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->pairwise();

foreach ($result as $item) {
    // [1, 2], [2, 3], [3, 4], [4, 5]
}
```

### Chunkwise
Return chunks of elements from iterable source.

```$stream->chunkwise(int $chunkSize): self```

Chunk size must be at least 1.

```php
use IterTools\Stream;

$friends = ['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey'];

$result = Stream::of($friends)
    ->chunkwise(2);

foreach ($result as $chunk) {
    // ['Ross', 'Rachel'], ['Chandler', 'Monica'], ['Joey']
}
```

### Chunkwise Overlap
Return overlapped chunks of elements from iterable source.

```$stream->chunkwiseOverlap(int $chunkSize, int $overlapSize): self```

Chunk size must be at least 1.

Overlap size must be less than chunk size.

```php
use IterTools\Stream;

$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9];

$result = Stream::of($friends)
    ->chunkwiseOverlap(3, 1);

foreach ($result as $chunk) {
    // [1, 2, 3], [3, 4, 5], [5, 6, 7], [7, 8, 9]
}
```

### Limit
Stream up to a limit.

Stops even if more data available if limit reached.

```$stream->limit(int $limit)```

```php
Use IterTools\Single;

$matrixMovies = ['The Matrix', 'The Matrix Reloaded', 'The Matrix Revolutions', 'The Matrix Resurrections'];
$limit        = 1;

$goodMovies = Stream::of($matrixMovies)
    ->limit($limit)
    ->toArray();
// 'The Matrix' (and nothing else)
```

### Chain With
Chain iterable source withs given iterables together into a single iteration.

```$stream->chainWith(iterable ...$iterables): self```

Makes a single continuous sequence out of multiple sequences.

```php
use IterTools\Stream;

$input = [1, 2, 3];

$result = Stream::of($input)
    ->chainWith([4, 5, 6])
    ->chainWith([7, 8, 9]);

foreach ($result as $item) {
    // 1, 2, 3, 4, 5, 6, 7, 8, 9
}
```

### Zip With
Iterate iterable source with another iterable collections simultaneously.

```$stream->zipWith(iterable ...$iterables): self```

Make an iterator that aggregates items from multiple iterators.

Similar to Python's zip function.

For uneven lengths, iterations stops when the shortest iterable is exhausted.

```php
use IterTools\Stream;

$input = [1, 2, 3];

$result = Stream::of($input)
    ->zipWith([4, 5, 6])
    ->zipWith([7, 8, 9]);

foreach ($result as $item) {
    // [1, 4, 7], [2, 5, 8], [3, 6, 9]
}
```

### Zip Longest With
Iterate iterable source with another iterable collections simultaneously.

```$stream->zipLongestWith(iterable ...$iterables): self```

Make an iterator that aggregates items from multiple iterators.

Similar to Python's zip_longest function.

Iteration continues until the longest iterable is exhausted.

For uneven lengths, the exhausted iterables will produce null for the remaining iterations.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->zipLongestWith([4, 5, 6])
    ->zipLongestWith([7, 8, 9, 10]);

foreach ($result as $item) {
    // [1, 4, 7], [2, 5, 8], [3, 6, 9], [4, null, 10], [null, null, 5]
}
```

### Zip Equal With
Iterate iterable source with another iterable collections of equal lengths simultaneously.

```$stream->zipEqualWith(iterable ...$iterables): self```

Works like Multi::zip() method but throws \LengthException if lengths not equal,
i.e., at least one iterator ends before the others.

```php
use IterTools\Stream;

$input = [1, 2, 3];

$result = Stream::of($input)
    ->zipEqualWith([4, 5, 6])
    ->zipEqualWith([7, 8, 9]);

foreach ($result as $item) {
    // [1, 4, 7], [2, 5, 8], [3, 6, 9]
}
```

### Infinite Cycle
Cycle through the elements of iterable source sequentially forever.

```$stream->infiniteCycle(): self```

```php
use IterTools\Stream;

$input = [1, 2, 3];

$result = Stream::of($input)
    ->infiniteCycle();

foreach ($result as $item) {
    // 1, 2, 3, 1, 2, 3, ...
}
```

### Running Average
Accumulate the running average (mean) over iterable source.

```$stream->runningAverage(int|float|null $initialValue = null): self```

```php
use IterTools\Stream;

$input = [1, 3, 5];

$result = Stream::of($input)
    ->runningAverage();

foreach ($result as $item) {
    // 1, 2, 3
}
```

### Running Difference
Accumulate the running difference over iterable source.

```$stream->runningDifference(int|float|null $initialValue = null): self```

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->runningDifference();

foreach ($result as $item) {
    // -1, -3, -6, -10, -15
}
```

### Running Max
Accumulate the running max over iterable source.

```$stream->runningMax(int|float|null $initialValue = null): self```

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->runningMax();

foreach ($result as $item) {
    // 1, 1, 2, 2, 3, 3
}
```

### Running Min
Accumulate the running min over iterable source.

```$stream->runningMin(int|float|null $initialValue = null): self```

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->runningMin();

foreach ($result as $item) {
    // 1, -1, -1, -2, -2, -3
}
```

### Running Product
Accumulate the running product over iterable source.

```$stream->runningProduct(int|float|null $initialValue = null): self```

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->runningProduct();

foreach ($result as $item) {
    // 1, 2, 6, 24, 120
}
```

### Running Total
Accumulate the running total over iterable source.

```$stream->runningTotal(int|float|null $initialValue = null): self```

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->runningTotal();

foreach ($result as $item) {
    // 1, 3, 6, 10, 15
}
```

### Is Sorted
Returns true if iterable source is sorted in ascending order; otherwise false.

```$stream->isSorted(): bool```

Items of iterable source must be comparable.

Returns true if iterable source is empty or has only one element.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->isSorted();
// true

$input = [1, 2, 3, 2, 1];

$result = Stream::of($input)
    ->isSorted();
// false
```

### Is Reversed
Returns true if iterable source is sorted in reverse descending order; otherwise false.

```$stream->isReversed(): bool```

Items of iterable source must be comparable.

Returns true if iterable source is empty or has only one element.

```php
use IterTools\Stream;

$input = [5, 4, 3, 2, 1];

$result = Stream::of($input)
    ->isReversed();
// true

$input = [1, 2, 3, 2, 1];

$result = Stream::of($input)
    ->isReversed();
// false
```

### Same With
Returns true if iterable source and all given collections are the same.

```$stream->sameWith(iterable ...$iterables): bool```

For single iterable or empty iterables list returns true.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->sameWith([1, 2, 3, 4, 5]);
// true

$result = Stream::of($input)
    ->sameWith([5, 4, 3, 2, 1]);
// false
```

### Same Count With
Returns true if iterable source and all given collections have the same lengths.

```$stream->sameCountWith(iterable ...$iterables): bool```

For single iterable or empty iterables list returns true.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->sameCountWith([5, 4, 3, 2, 1]);
// true

$result = Stream::of($input)
    ->sameCountWith([1, 2, 3]);
// false
```

### To Average
Reduces iterable source to the mean average of its items.

```$stream->toAverage(): mixed```

Returns null if iterable source is empty.

```php
use IterTools\Stream;

$input = [2, 4, 6, 8];

$result = Stream::of($iterable)
    ->toAverage();
// 5
```

### To Count
Reduces iterable source to its length.

```$stream->toCount(): mixed```

```php
use IterTools\Stream;

$input = [10, 20, 30, 40, 50];

$result = Stream::of($iterable)
    ->toCount();
// 5
```

### To Max
Reduces iterable source to its max value.

```$stream->toMax(): mixed```

Items of iterable source must be comparable.

Returns null if iterable source is empty.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($iterable)
    ->toMax();
// 3
```

### To Min
Reduces iterable source to its min value.

```$stream->toMin(): mixed```

Items of iterable source must be comparable.

Returns null if iterable source is empty.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($iterable)
    ->toMin();
// -3
```

### To Product
Reduces iterable source to the product of its items.

```$stream->toProduct(): mixed```

Returns null if iterable source is empty.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($iterable)
    ->toProduct();
// 120
```

### To Sum
Reduces iterable source to the sum of its items.

```$stream->toSum(): mixed```

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($iterable)
    ->toSum();
// 15
```

### To Value
Reduces iterable source like array_reduce() function.

But unlike array_reduce(), it works with all iterable types.

```$stream->toValue(callable $reducer, mixed $initialValue): mixed```

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($iterable)
    ->toValue(fn ($carry, $item) => $carry + $item);
// 15
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

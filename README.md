![IterToolsLogo Logo](https://github.com/markrogoyski/itertools-php/blob/main/docs/image/IterToolsLogo.png?raw=true)

### IterTools - PHP Iteration Tools to Power Up Your Loops

Inspired by Python—designed for PHP.

[![Coverage Status](https://coveralls.io/repos/github/markrogoyski/itertools-php/badge.svg?branch=main)](https://coveralls.io/github/markrogoyski/itertools-php?branch=main)
[![License](https://poser.pugx.org/markrogoyski/itertools-php/license)](https://packagist.org/packages/markrogoyski/itertools-php)
[![Latest Stable Version](https://poser.pugx.org/markrogoyski/itertools-php/v)](https://packagist.org/packages/markrogoyski/itertools-php)
[![Downloads](https://poser.pugx.org/markrogoyski/itertools-php/downloads)](https://packagist.org/packages/markrogoyski/itertools-php)

### Features

IterTools makes you an iteration superstar by providing two types of tools:

* Loop iteration tools
* Stream iteration tools

**Loop Iteration Tools Example**

```php
foreach (Multi::zip(['a', 'b'], [1, 2]) as [$letter, $number]) {
    print($letter . $number);  // a1, b2
}
```

**Stream Iteration Tools Example**

```php
$result = Stream::of([1, 1, 2, 2, 3, 4, 5])
    ->distinct()                 // [1, 2, 3, 4, 5]
    ->map(fn ($x) => $x**2)      // [1, 4, 9, 16, 25]
    ->filter(fn ($x) => $x < 10) // [1, 4, 9]
    ->toSum();                   // 14
```

All functions work on `iterable` collections:
* `array` (type)
* `Generator` (type)
* `Iterator` (interface)
* `Traversable` (interface)

### README docs translated in other languages:
* [Русский](docs/ru/README.md)

Setup
-----

Add the library to your `composer.json` file in your project:

```json
{
  "require": {
      "markrogoyski/itertools-php": "2.*"
  }
}
```

Use [composer](http://getcomposer.org) to install the library:

```bash
$ composer install
```

Composer will install IterTools inside your vendor folder. Then you can add the following to your
.php files to use the library with Autoloading.

```php
require_once __DIR__ . '/vendor/autoload.php';
```

Alternatively, use composer on the command line to require and install IterTools:

```
$ composer require markrogoyski/itertools-php:2.*
```

#### Minimum Requirements
 * **PHP 8.2+**
     * (For PHP 7.4–8.1, use [v1.9](https://github.com/markrogoyski/itertools-php/releases/tag/v1.9.0))

Quick Reference
-----------

### Loop Iteration Tools

#### Multi Iteration
| Iterator                    | Description                                                                             | Code Snippet                                  |
|-----------------------------|-----------------------------------------------------------------------------------------|-----------------------------------------------|
| [`chain`](docs/multi-iteration.md#chain)           | Chain multiple iterables together                                                       | `Multi::chain($list1, $list2)`                |
| [`zip`](docs/multi-iteration.md#zip)               | Iterate multiple collections simultaneously until the shortest iterator completes       | `Multi::zip($list1, $list2)`                  |
| [`zipEqual`](docs/multi-iteration.md#zipequal)     | Iterate multiple collections of equal length simultaneously, error if lengths not equal | `Multi::zipEqual($list1, $list2)`             |
| [`zipFilled`](docs/multi-iteration.md#zipfilled)   | Iterate multiple collections, using a filler value if lengths not equal                 | `Multi::zipFilled($default, $list1, $list2)`  |
| [`zipLongest`](docs/multi-iteration.md#ziplongest) | Iterate multiple collections simultaneously until the longest iterator completes        | `Multi::zipLongest($list1, $list2)`           |

#### Single Iteration
| Iterator                                       | Description                                  | Code Snippet                                                |
|------------------------------------------------|----------------------------------------------|-------------------------------------------------------------|
| [`accumulate`](docs/single-iteration.md#accumulate)                    | Running result of a binary operator          | `Single::accumulate($data, $op, [$initial])`                |
| [`chunkwise`](docs/single-iteration.md#chunkwise)                      | Iterate by chunks                            | `Single::chunkwise($data, $chunkSize)`                      |
| [`chunkwiseOverlap`](docs/single-iteration.md#chunkwise-overlap)       | Iterate by overlapped chunks                 | `Single::chunkwiseOverlap($data, $chunkSize, $overlapSize)` |
| [`compress`](docs/single-iteration.md#compress)                        | Filter out elements not selected             | `Single::compress($data, $selectors)`                       |
| [`compressAssociative`](docs/single-iteration.md#compress-associative) | Filter out elements by keys not selected     | `Single::compressAssociative($data, $selectorKeys)`         |
| [`dropWhile`](docs/single-iteration.md#drop-while)                     | Drop elements while predicate is true        | `Single::dropWhile($data, $predicate)`                      |
| [`enumerate`](docs/single-iteration.md#enumerate)                      | Iterate [index, value] pairs                 | `Single::enumerate($data, [$start])`                        |
| [`filter`](docs/single-iteration.md#filter)                            | Filter for elements where predicate is true  | `Single::filterTrue($data, $predicate)`                     |
| [`filterTrue`](docs/single-iteration.md#filter-true)                   | Filter for truthy elements                   | `Single::filterTrue($data)`                                 |
| [`filterFalse`](docs/single-iteration.md#filter-false)                 | Filter for falsy elements                    | `Single::filterFalse($data)`                                |
| [`filterKeys`](docs/single-iteration.md#filter-keys)                   | Filter for keys where predicate is true      | `Single::filterKeys($data, $predicate)`                     |
| [`flatMap`](docs/single-iteration.md#flat-map)                         | Map function onto items and flatten result   | `Single::flaMap($data, $mapper)`                            |
| [`flatten`](docs/single-iteration.md#flatten)                          | Flatten multidimensional iterable            | `Single::flatten($data, [$dimensions])`                     |
| [`groupBy`](docs/single-iteration.md#group-by)                         | Group data by a common element               | `Single::groupBy($data, $groupKeyFunction, [$itemKeyFunc])` |
| [`limit`](docs/single-iteration.md#limit)                              | Iterate up to a limit                        | `Single::limit($data, $limit)`                              |
| [`map`](docs/single-iteration.md#map)                                  | Map function onto each item                  | `Single::map($data, $function)`                             |
| [`pairwise`](docs/single-iteration.md#pairwise)                        | Iterate successive overlapping pairs         | `Single::pairwise($data)`                                   |
| [`reindex`](docs/single-iteration.md#reindex)                          | Reindex keys of key-value iterable           | `Single::reindex($data, $reindexer)`                        |
| [`repeat`](docs/single-iteration.md#repeat)                            | Repeat an item a number of times             | `Single::repeat($item, $repetitions)`                       |
| [`reverse`](docs/single-iteration.md#reverse)                          | Iterate elements in reverse order            | `Single::reverse($data)`                                    |
| [`skip`](docs/single-iteration.md#skip)                                | Iterate after skipping elements              | `Single::skip($data, $count, [$offset])`                    |
| [`slice`](docs/single-iteration.md#slice)                              | Extract a slice of the iterable              | `Single::slice($data, [$start], [$count], [$step])`         |
| [`string`](docs/single-iteration.md#string)                            | Iterate the characters of a string           | `Single::string($string)`                                   |
| [`takeWhile`](docs/single-iteration.md#take-while)                     | Iterate elements while predicate is true     | `Single::takeWhile($data, $predicate)`                      |

#### Infinite Iteration
| Iterator                     | Description                | Code Snippet                     |
|------------------------------|----------------------------|----------------------------------|
| [`count`](docs/infinite-iteration.md#count)            | Count sequentially forever | `Infinite::count($start, $step)` |
| [`cycle`](docs/infinite-iteration.md#cycle)            | Cycle through a collection | `Infinite::cycle($collection)`   |
| [`repeat`](docs/infinite-iteration.md#repeat-infinite) | Repeat an item forever     | `Infinite::repeat($item)`        |

#### Random Iteration
| Iterator                                  | Description                       | Code Snippet                               |
|-------------------------------------------|-----------------------------------|--------------------------------------------|
| [`choice`](docs/random-iteration.md#choice)                       | Random selections from list       | `Random::choice($list, $repetitions)`      |
| [`coinFlip`](docs/random-iteration.md#coinflip)                   | Random coin flips (0 or 1)        | `Random::coinFlip($repetitions)`           |
| [`number`](docs/random-iteration.md#number)                       | Random numbers                    | `Random::number($min, $max, $repetitions)` |
| [`percentage`](docs/random-iteration.md#percentage)               | Random percentage between 0 and 1 | `Random::percentage($repetitions)`         |
| [`rockPaperScissors`](docs/random-iteration.md#rockpaperscissors) | Random rock-paper-scissors hands  | `Random::rockPaperScissors($repetitions)`  |

#### Math Iteration
| Iterator                                        | Description                             | Code Snippet                                       |
|-------------------------------------------------|-----------------------------------------|----------------------------------------------------|
| [`frequencies`](docs/math-iteration.md#frequencies)                   | Frequency distribution of data          | `Math::frequencies($data, [$strict])`              |
| [`relativeFrequencies`](docs/math-iteration.md#relative-frequencies)  | Relative frequency distribution of data | `Math::relativeFrequencies($data, [$strict])`      |
| [`runningAverage`](docs/math-iteration.md#running-average)            | Running average accumulation            | `Math::runningAverage($numbers, $initialValue)`    |
| [`runningDifference`](docs/math-iteration.md#running-difference)      | Running difference accumulation         | `Math::runningDifference($numbers, $initialValue)` |
| [`runningMax`](docs/math-iteration.md#running-max)                    | Running maximum accumulation            | `Math::runningMax($numbers, $initialValue)`        |
| [`runningMin`](docs/math-iteration.md#running-min)                    | Running minimum accumulation            | `Math::runningMin($numbers, $initialValue)`        |
| [`runningProduct`](docs/math-iteration.md#running-product)            | Running product accumulation            | `Math::runningProduct($numbers, $initialValue)`    |
| [`runningTotal`](docs/math-iteration.md#running-total)                | Running total accumulation              | `Math::runningTotal($numbers, $initialValue)`      |

#### Set and multiset Iteration
| Iterator                                                        | Description                                               | Code Snippet                                                 |
|-----------------------------------------------------------------|-----------------------------------------------------------|--------------------------------------------------------------|
| [`distinct`](docs/set-iteration.md#distinct)                                         | Iterate only distinct items                               | `Set::distinct($data)`                                       |
| [`distinctBy`](docs/set-iteration.md#distinct-by)                                    | Iterate only distinct items using custom comparator       | `Set::distinct($data, $compareBy)`                           |
| [`intersection`](docs/set-iteration.md#intersection)                                 | Intersection of iterables                                 | `Set::intersection(...$iterables)`                           |
| [`intersectionCoercive`](docs/set-iteration.md#intersection-coercive)                | Intersection with type coercion                           | `Set::intersectionCoercive(...$iterables)`                   |
| [`partialIntersection`](docs/set-iteration.md#partial-intersection)                  | Partial intersection of iterables                         | `Set::partialIntersection($minCount, ...$iterables)`         |
| [`partialIntersectionCoercive`](docs/set-iteration.md#partial-intersection-coercive) | Partial intersection with type coercion                   | `Set::partialIntersectionCoercive($minCount, ...$iterables)` |
| [`difference`](docs/set-iteration.md#difference)                                     | Difference of iterables                                   | `Set::difference($a, ...$iterables)`                         |
| [`differenceCoercive`](docs/set-iteration.md#difference-coercive)                    | Difference with type coercion                             | `Set::differenceCoercive($a, ...$iterables)`                 |
| [`symmetricDifference`](docs/set-iteration.md#symmetric-difference)                  | Symmetric difference of iterables                         | `Set::symmetricDifference(...$iterables)`                    |
| [`symmetricDifferenceCoercive`](docs/set-iteration.md#symmetric-difference-coercive) | Symmetric difference with type coercion                   | `Set::symmetricDifferenceCoercive(...$iterables)`            |
| [`union`](docs/set-iteration.md#union)                                               | Union of iterables                                        | `Set::union(...$iterables)`                                  |
| [`unionCoercive`](docs/set-iteration.md#union-coercive)                              | Union with type coercion                                  | `Set::unionCoercive(...$iterables)`                          |

#### Combinatorics
| Iterator                                                            | Description                     | Code Snippet                               |
|---------------------------------------------------------------------|---------------------------------|--------------------------------------------|
| [`product`](docs/combinatorics-iteration.md#product)                                            | Cartesian product of iterables  | `Combinatorics::product(...$iterables)`                    |
| [`permutations`](docs/combinatorics-iteration.md#permutations)                                  | Permutations of an iterable     | `Combinatorics::permutations($data, [$r])`                 |
| [`combinations`](docs/combinatorics-iteration.md#combinations)                                  | Combinations of an iterable     | `Combinatorics::combinations($data, $r)`                   |
| [`combinationsWithReplacement`](docs/combinatorics-iteration.md#combinations-with-replacement)  | Combinations with replacement   | `Combinatorics::combinationsWithReplacement($data, $r)`    |

#### Sort Iteration
| Iterator                                       | Description                                  | Code Snippet                                              |
|------------------------------------------------|----------------------------------------------|-----------------------------------------------------------|
| [`asort`](docs/sort-iteration.md#asort)                              | Iterate a sorted collection maintaining keys | `Sort::asort($data, [$comparator])`                       |
| [`sort`](docs/sort-iteration.md#sort)                                | Iterate a sorted collection                  | `Sort::sort($data, [$comparator])`                        |

#### File Iteration
| Iterator                                                        | Description                                               | Code Snippet                                                 |
|-----------------------------------------------------------------|-----------------------------------------------------------|--------------------------------------------------------------|
| [`readCsv`](docs/file-iteration.md#read-csv)                                          | Intersection a CSV file line by line                      | `File::readCsv($fileHandle)`                                 |
| [`readLines`](docs/file-iteration.md#read-lines)                                      | Iterate a file line by line                               | `File::readLines($fileHandle)`                               |

#### Transform Iteration
| Iterator                                       | Description                                  | Code Snippet                                                      |
|------------------------------------------------|----------------------------------------------|-------------------------------------------------------------------|
| [`partition`](docs/transform-iteration.md#partition)                      | Partition iterable into truthy and falsy lists | `Transform::partition($data, $predicate)`                       |
| [`tee`](docs/transform-iteration.md#tee)                                  | Iterate duplicate iterators                  | `Transform::tee($data, $count)`                                   |
| [`toArray`](docs/transform-iteration.md#to-array)                         | Transform iterable to an array               | `Transform::toArray($data)`                                       |
| [`toAssociativeArray`](docs/transform-iteration.md#to-associative-array)  | Transform iterable to an associative array   | `Transform::toAssociativeArray($data, [$keyFunc], [$valueFunc])`  |
| [`toIterator`](docs/transform-iteration.md#to-iterator)                   | Transform iterable to an iterator            | `Transform::toIterator($data)`                                    |

#### Summary
| Summary                                                 | Description                                                              | Code Snippet                                      |
|---------------------------------------------------------|--------------------------------------------------------------------------|---------------------------------------------------|
| [`allMatch`](docs/summary.md#all-match)                                | True if all items are true according to predicate                        | `Summary::allMatch($data, $predicate)`            |
| [`allUnique`](docs/summary.md#all-unique)                              | True if all items are unique                                             | `Summary::allUnique($data, [$strict])`            |
| [`anyMatch`](docs/summary.md#any-match)                                | True if any item is true according to predicate                          | `Summary::anyMatch($data, $predicate)`            |
| [`arePermutations`](docs/summary.md#are-permutations)                  | True if iterables are permutations of each other                         | `Summary::arePermutations(...$iterables)`         |
| [`arePermutationsCoercive`](docs/summary.md#are-permutations-coercive) | True if iterables are permutations of each other with type coercion      | `Summary::arePermutationsCoercive(...$iterables)` |
| [`contains`](docs/summary.md#contains)                                 | True if iterable contains the needle                                     | `Summary::contains($data, $needle)`               |
| [`containsCoercive`](docs/summary.md#contains-coercive)                | True if iterable contains the needle with type coercion                  | `Summary::containsCoercive($data, $needle)`       |
| [`exactlyN`](docs/summary.md#exactly-n)                                | True if exactly n items are true according to predicate                  | `Summary::exactlyN($data, $n, $predicate)`        |
| [`isEmpty`](docs/summary.md#is-empty)                                  | True if iterable has no items                                            | `Summary::isEmpty($data)`                         |
| [`isPartitioned`](docs/summary.md#is-partitioned)                      | True if partitioned with items true according to predicate before others | `Summary::isPartitioned($data, $predicate)`       |
| [`isSorted`](docs/summary.md#is-sorted)                                | True if iterable sorted                                                  | `Summary::isSorted($data)`                        |
| [`isReversed`](docs/summary.md#is-reversed)                            | True if iterable reverse sorted                                          | `Summary::isReversed($data)`                      |
| [`noneMatch`](docs/summary.md#none-match)                              | True if none of items true according to predicate                        | `Summary::noneMatch($data, $predicate)`           |
| [`same`](docs/summary.md#same)                                         | True if iterables are the same                                           | `Summary::same(...$iterables)`                    |
| [`sameCount`](docs/summary.md#same-count)                              | True if iterables have the same lengths                                  | `Summary::sameCount(...$iterables)`               |

#### Reduce
| Reducer                                | Description                                | Code Snippet                                                  |
|----------------------------------------|--------------------------------------------|---------------------------------------------------------------|
| [`toAverage`](docs/reduce.md#to-average)             | Mean average of elements                   | `Reduce::toAverage($numbers)`                                 |
| [`toCount`](docs/reduce.md#to-count)                 | Reduce to length of iterable               | `Reduce::toCount($data)`                                      |
| [`toFirst`](docs/reduce.md#to-first)                 | Reduce to its first value                  | `Reduce::toFirst($data)`                                      |
| [`toFirstAndLast`](docs/reduce.md#to-first-and-last) | Reduce to its first and last values        | `Reduce::toFirstAndLast($data)`                               |
| [`toFirstMatch`](docs/reduce.md#to-first-match)      | Reduce to first value matching predicate   | `Reduce::toFirstMatch($data, $predicate, [$default])`         |
| [`toLast`](docs/reduce.md#to-last)                   | Reduce to its last value                   | `Reduce::toLast()`                                            |
| [`toMax`](docs/reduce.md#to-max)                     | Reduce to its largest element              | `Reduce::toMax($numbers, [$compareBy])`                       |
| [`toMin`](docs/reduce.md#to-min)                     | Reduce to its smallest element             | `Reduce::toMin($numbers, [$compareBy])`                       |
| [`toMinMax`](docs/reduce.md#to-min-max)              | Reduce to array of upper and lower bounds  | `Reduce::toMinMax($numbers, [$compareBy])`                    |
| [`toNth`](docs/reduce.md#to-nth)                     | Reduce to value at nth position            | `Reduce::toNth($data, $position)`                             |
| [`toProduct`](docs/reduce.md#to-product)             | Reduce to the product of its elements      | `Reduce::toProduct($numbers)`                                 |
| [`toRandomValue`](docs/reduce.md#to-random-value)    | Reduce to random value from iterable       | `Reduce::toRandomValue($data)`                                |
| [`toRange`](docs/reduce.md#to-range)                 | Reduce to difference of max and min values | `Reduce::toRange($numbers)`                                   |
| [`toString`](docs/reduce.md#to-string)               | Reduce to joined string                    | `Reduce::toString($data, [$separator], [$prefix], [$suffix])` |
| [`toSum`](docs/reduce.md#to-sum)                     | Reduce to the sum of its elements          | `Reduce::toSum($numbers)`                                     |
| [`toValue`](docs/reduce.md#to-value)                 | Reduce to value using callable reducer     | `Reduce::toValue($data, $reducer, $initialValue)`             |

### Stream Iteration Tools
#### Stream Sources
| Source                                           | Description                                                     | Code Snippet                                        |
|--------------------------------------------------|-----------------------------------------------------------------|-----------------------------------------------------|
| [`of`](docs/stream.md#of)                                      | Create a stream from an iterable                                | `Stream::of($iterable)`                             |
| [`ofCoinFlips`](docs/stream.md#of-coin-flips)                  | Create a stream of random coin flips                            | `Stream::ofCoinFlips($repetitions)`                 |
| [`ofCsvFile`](docs/stream.md#of-csv-file)                      | Create a stream from a CSV file                                 | `Stream::ofCsvFile($fileHandle)`                    |
| [`ofEmpty`](docs/stream.md#of-empty)                           | Create an empty stream                                          | `Stream::ofEmpty()`                                 |
| [`ofFileLines`](docs/stream.md#of-file-lines)                  | Create a stream from lines of a file                            | `Stream::ofFileLines($fileHandle)`                  |
| [`ofRandomChoice`](docs/stream.md#of-random-choice)            | Create a stream of random selections                            | `Stream::ofRandomChoice($items, $repetitions)`      |
| [`ofRandomNumbers`](docs/stream.md#of-random-numbers)          | Create a stream of random numbers (integers)                    | `Stream::ofRandomNumbers($min, $max, $repetitions)` |
| [`ofRandomPercentage`](docs/stream.md#of-random-percentage)    | Create a stream of random percentages between 0 and 1           | `Stream::ofRandomPercentage($repetitions)`          |
| [`ofRange`](docs/stream.md#of-range)                           | Create a stream of a range of numbers                           | `Stream::ofRange($start, $end, $step)`              |
| [`ofRockPaperScissors`](docs/stream.md#of-rock-paper-scissors) | Create a stream of rock-paper-scissors hands                    | `Stream::ofRockPaperScissors($repetitions)`         |

#### Stream Operations
| Operation                                                                 | Description                                                                               | Code Snippet                                                                      |
|---------------------------------------------------------------------------|-------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------|
| [`accumulate`](docs/stream.md#accumulate)                                             | Running result of a binary operator                                                       | `$stream->accumulate($op, [$initial])`                                            |
| [`asort`](docs/stream.md#asort)                                                       | Sorts the iterable source maintaining keys                                                | `$stream->asort([$comparator])`                                                   |
| [`chainWith`](docs/stream.md#chain-with)                                                | Chain iterable source withs given iterables together into a single iteration              | `$stream->chainWith(...$iterables)`                                               |
| [`compress`](docs/stream.md#compress)                                                 | Compress source by filtering out data not selected                                        | `$stream->compress($selectors)`                                                   |
| [`compressAssociative`](docs/stream.md#compress-associative)                          | Compress source by filtering out keys not selected                                        | `$stream->compressAssociative($selectorKeys)`                                     |
| [`chunkwise`](docs/stream.md#chunkwise)                                               | Iterate by chunks                                                                         | `$stream->chunkwise($chunkSize)`                                                  |
| [`chunkwiseOverlap`](docs/stream.md#chunkwise-overlap)                                | Iterate by overlapped chunks                                                              | `$stream->chunkwiseOverlap($chunkSize, $overlap)`                                 |
| [`distinct`](docs/stream.md#distinct)                                                 | Filter out elements: iterate only unique items                                            | `$stream->distinct([$strict])`                                                    |
| [`distinctBy`](docs/stream.md#distinct-by)                                            | Filter out elements: iterate only unique items using custom comparator                    | `$stream->distinct($compareBy)`                                                   |
| [`dropWhile`](docs/stream.md#drop-while)                                              | Drop elements from the iterable source while the predicate function is true               | `$stream->dropWhile($predicate)`                                                  |
| [`enumerate`](docs/stream.md#enumerate)                                               | Iterate [index, value] pairs                                                              | `$stream->enumerate([$start])`                                                    |
| [`filter`](docs/stream.md#filter)                                                     | Filter for only elements where the predicate function is true                             | `$stream->filterTrue($predicate)`                                                 |
| [`filterTrue`](docs/stream.md#filter-true)                                            | Filter for only truthy elements                                                           | `$stream->filterTrue()`                                                           |
| [`filterFalse`](docs/stream.md#filter-false)                                          | Filter for only falsy elements                                                            | `$stream->filterFalse()`                                                          |
| [`filterKeys`](docs/stream.md#filter-keys)                                            | Filter for keys where predicate function is true                                          | `$stream->filterKeys($predicate)`                                                 |
| [`flatMap`](docs/stream.md#flat-map)                                                  | Map function onto elements and flatten result                                             | `$stream->flatMap($function)`                                                     |
| [`flatten`](docs/stream.md#flatten)                                                   | Flatten multidimensional stream                                                           | `$stream->flatten($dimensions)`                                                   |
| [`frequencies`](docs/stream.md#frequencies)                                           | Frequency distribution                                                                    | `$stream->frequencies([$strict])`                                                 |
| [`groupBy`](docs/stream.md#group-by)                                                  | Group iterable source by a common data element                                            | `$stream->groupBy($groupKeyFunction, [$itemKeyFunc])`                             |
| [`infiniteCycle`](docs/stream.md#infinite-cycle)                                        | Cycle through the elements of iterable source sequentially forever                        | `$stream->infiniteCycle()`                                                        |
| [`intersectionWith`](docs/stream.md#intersection-with)                                  | Intersect iterable source and given iterables                                             | `$stream->intersectionWith(...$iterables)`                                        |
| [`intersectionCoerciveWith`](docs/stream.md#intersection-coercive-with)                | Intersect iterable source and given iterables with type coercion                          | `$stream->intersectionCoerciveWith(...$iterables)`                                |
| [`limit`](docs/stream.md#limit)                                                       | Limit the stream's iteration                                                              | `$stream->limit($limit)`                                                          |
| [`map`](docs/stream.md#map)                                                           | Map function onto elements                                                                | `$stream->map($function)`                                                         |
| [`pairwise`](docs/stream.md#pairwise)                                                 | Return pairs of elements from iterable source                                             | `$stream->pairwise()`                                                             |
| [`partialIntersectionWith`](docs/stream.md#partial-intersection-with)                   | Partially intersect iterable source and given iterables                                   | `$stream->partialIntersectionWith( $minIntersectionCount, ...$iterables)`         |
| [`partialIntersectionCoerciveWith`](docs/stream.md#partial-intersection-coercive-with) | Partially intersect iterable source and given iterables with type coercion                | `$stream->partialIntersectionCoerciveWith( $minIntersectionCount, ...$iterables)` |
| [`productWith`](docs/stream.md#product-with)                                          | Cartesian product of stream with given iterables                                          | `$stream->productWith(...$iterables)`                                             |
| [`permutations`](docs/stream.md#permutations)                                         | Permutations of the stream's elements                                                     | `$stream->permutations([$r])`                                                     |
| [`combinations`](docs/stream.md#combinations)                                         | Combinations of the stream's elements                                                     | `$stream->combinations($r)`                                                       |
| [`combinationsWithReplacement`](docs/stream.md#combinations-with-replacement)         | Combinations with replacement of the stream's elements                                    | `$stream->combinationsWithReplacement($r)`                                        |
| [`reindex`](docs/stream.md#reindex)                                                   | Reindex keys of key-value stream                                                          | `$stream->reindex($reindexer)`                                                    |
| [`relativeFrequencies`](docs/stream.md#relative-frequencies)                          | Relative frequency distribution                                                           | `$stream->relativeFrequencies([$strict])`                                         |
| [`reverse`](docs/stream.md#reverse)                                                   | Reverse elements of the stream                                                            | `$stream->reverse()`                                                              |
| [`runningAverage`](docs/stream.md#running-average)                                    | Accumulate the running average (mean) over iterable source                                | `$stream->runningAverage($initialValue)`                                          |
| [`runningDifference`](docs/stream.md#running-difference)                              | Accumulate the running difference over iterable source                                    | `$stream->runningDifference($initialValue)`                                       |
| [`runningMax`](docs/stream.md#running-max)                                            | Accumulate the running max over iterable source                                           | `$stream->runningMax($initialValue)`                                              |
| [`runningMin`](docs/stream.md#running-min)                                            | Accumulate the running min over iterable source                                           | `$stream->runningMin($initialValue)`                                              |
| [`runningProduct`](docs/stream.md#running-product)                                    | Accumulate the running product over iterable source                                       | `$stream->runningProduct($initialValue)`                                          |
| [`runningTotal`](docs/stream.md#running-total)                                        | Accumulate the running total over iterable source                                         | `$stream->runningTotal($initialValue)`                                            |
| [`skip`](docs/stream.md#skip)                                                         | Skip some elements of the stream                                                          | `$stream->skip($count, [$offset])`                                                |
| [`slice`](docs/stream.md#slice)                                                       | Extract a slice of the stream                                                             | `$stream->slice([$start], [$count], [$step])`                                     |
| [`sort`](docs/stream.md#sort)                                                         | Sorts the stream                                                                          | `$stream->sort([$comparator])`                                                    |
| [`differenceWith`](docs/stream.md#difference-with)                                       | Difference of iterable source and given iterables                                         | `$stream->differenceWith(...$iterables)`                                          |
| [`differenceCoerciveWith`](docs/stream.md#difference-coercive-with)                     | Difference of iterable source and given iterables with type coercion                      | `$stream->differenceCoerciveWith(...$iterables)`                                  |
| [`symmetricDifferenceWith`](docs/stream.md#symmetric-difference-with)                   | Symmetric difference of iterable source and given iterables                               | `$stream->symmetricDifferenceWith(...$iterables)`                                 |
| [`symmetricDifferenceCoerciveWith`](docs/stream.md#symmetric-difference-coercive-with) | Symmetric difference of iterable source and given iterables with type coercion            | `$stream->symmetricDifferenceCoerciveWith(...$iterables)`                         |
| [`takeWhile`](docs/stream.md#take-while)                                              | Return elements from the iterable source as long as the predicate is true                 | `$stream->takeWhile($predicate)`                                                  |
| [`unionWith`](docs/stream.md#union-with)                                                | Union of stream with iterables                                                            | `$stream->unionWith(...$iterables)`                                               |
| [`unionCoerciveWith`](docs/stream.md#union-coercive-with)                               | Union of stream with iterables with type coercion                                         | `$stream->unionCoerciveWith(...$iterables)`                                       |
| [`zip`](docs/stream.md#zip)                                                             | Zip rows of the stream column-wise (transpose), stopping at shortest                      | `$stream->zip()`                                                                  |
| [`zipLongest`](docs/stream.md#zip-longest)                                              | Zip rows of the stream column-wise, continuing until longest (missing → null)             | `$stream->zipLongest()`                                                           |
| [`zipFilled`](docs/stream.md#zip-filled)                                                | Zip rows of the stream column-wise, continuing until longest with filler                  | `$stream->zipFilled($filler)`                                                     |
| [`zipEqual`](docs/stream.md#zip-equal)                                                  | Zip rows of the stream column-wise, throwing if lengths differ                            | `$stream->zipEqual()`                                                             |
| [`zipWith`](docs/stream.md#zip-with)                                                    | Iterate iterable source with another iterable collection simultaneously                   | `$stream->zipWith(...$iterables)`                                                 |
| [`zipEqualWith`](docs/stream.md#zip-equal-with)                                         | Iterate iterable source with another iterable collection of equal lengths simultaneously  | `$stream->zipEqualWith(...$iterables)`                                            |
| [`zipFilledWith`](docs/stream.md#zip-filled-with)                                       | Iterate iterable source with another iterable collection using default filler             | `$stream->zipFilledWith($default, ...$iterables)`                                 |
| [`zipLongestWith`](docs/stream.md#zip-longest-with)                                     | Iterate iterable source with another iterable collection simultaneously                   | `$stream->zipLongestWith(...$iterables)`                                          |

#### Stream Terminal Operations
##### Summary Terminal Operations
| Terminal Operation                                               | Description                                                                      | Code Snippet                                           |
|------------------------------------------------------------------|----------------------------------------------------------------------------------|--------------------------------------------------------|
| [`allMatch`](docs/stream.md#all-match)                                       | Returns true if all items in stream match predicate                              | `$stream->allMatch($predicate)`                        |
| [`allUnique`](docs/stream.md#all-unique)                                     | Returns true if all items in stream are unique                                   | `$stream->allUnique([$strict]])`                       |
| [`anyMatch`](docs/stream.md#any-match)                                       | Returns true if any item in stream matches predicate                             | `$stream->anyMatch($predicate)`                        |
| [`arePermutationsWith`](docs/stream.md#are-permutations-with)                  | Returns true if all iterables permutations of stream                             | `$stream->arePermutationsWith(...$iterables)`          |
| [`arePermutationsCoerciveWith`](docs/stream.md#are-permutations-coercive-with) | Returns true if all iterables permutations of stream with type coercion          | `$stream->arePermutationsCoerciveWith(...$iterables)`  |
| [`contains`](docs/stream.md#contains)                                        | Returns true if stream contains the needle                                       | `$stream->contains($needle)`                           |
| [`containsCoercive`](docs/stream.md#contains-coercive)                       | Returns true if stream contains the needle with type coercion                    | `$stream->containsCoercive($needle)`                   |
| [`exactlyN`](docs/stream.md#exactly-n)                                       | Returns true if exactly n items are true according to predicate                  | `$stream->exactlyN($n, $predicate)`                    |
| [`isEmpty`](docs/stream.md#is-empty)                                         | Returns true if stream has no items                                              | `$stream::isEmpty()`                                   |
| [`isPartitioned`](docs/stream.md#is-partitioned)                             | Returns true if partitioned with items true according to predicate before others | `$stream::isPartitioned($predicate)`                   |
| [`isSorted`](docs/stream.md#is-sorted)                                       | Returns true if stream is sorted in ascending order                              | `$stream->isSorted()`                                  |
| [`isReversed`](docs/stream.md#is-reversed)                                   | Returns true if stream is sorted in reverse descending order                     | `$stream->isReversed()`                                |
| [`noneMatch`](docs/stream.md#none-match)                                     | Returns true if none of the items in stream match predicate                      | `$stream->noneMatch($predicate)`                       |
| [`sameWith`](docs/stream.md#same-with)                                         | Returns true if stream and all given collections are the same                    | `$stream->sameWith(...$iterables)`                     |
| [`sameCountWith`](docs/stream.md#same-count-with)                              | Returns true if stream and all given collections have the same lengths           | `$stream->sameCountWith(...$iterables)`                |

##### Reduction Terminal Operations
| Terminal Operation                       | Description                                        | Code Snippet                                            |
|------------------------------------------|----------------------------------------------------|---------------------------------------------------------|
| [`toAverage`](docs/stream.md#to-average)             | Reduces stream to the mean average of its items    | `$stream->toAverage()`                                  |
| [`toCount`](docs/stream.md#to-count)                 | Reduces stream to its length                       | `$stream->toCount()`                                    |
| [`toFirst`](docs/stream.md#to-first)                 | Reduces stream to its first value                  | `$stream->toFirst()`                                    |
| [`toFirstAndLast`](docs/stream.md#to-first-and-last) | Reduces stream to its first and last values        | `$stream->toFirstAndLast()`                             |
| [`toFirstMatch`](docs/stream.md#to-first-match)      | Reduces stream to first value matching predicate   | `$stream->toFirstMatch($predicate, [$default])`         |
| [`toLast`](docs/stream.md#to-last)                   | Reduces stream to its last value                   | `$stream->toLast()`                                     |
| [`toMax`](docs/stream.md#to-max)                     | Reduces stream to its max value                    | `$stream->toMax([$compareBy])`                          |
| [`toMin`](docs/stream.md#to-min)                     | Reduces stream to its min value                    | `$stream->toMin([$compareBy])`                          |
| [`toMinMax`](docs/stream.md#to-min-max)              | Reduces stream to array of upper and lower bounds  | `$stream->toMinMax([$compareBy])`                       |
| [`toNth`](docs/stream.md#to-nth)                     | Reduces stream to value at nth position            | `$stream->toNth($position)`                             |
| [`toProduct`](docs/stream.md#to-product)             | Reduces stream to the product of its items         | `$stream->toProduct()`                                  |
| [`toString`](docs/stream.md#to-string)               | Reduces stream to joined string                    | `$stream->toString([$separator], [$prefix], [$suffix])` |
| [`toSum`](docs/stream.md#to-sum)                     | Reduces stream to the sum of its items             | `$stream->toSum()`                                      |
| [`toRandomValue`](docs/stream.md#to-random-value)    | Reduces stream to random value within it           | `$stream->toRandomValue()`                              |
| [`toRange`](docs/stream.md#to-range)                 | Reduces stream to difference of max and min values | `$stream->toRange()`                                    |
| [`toValue`](docs/stream.md#to-value)                 | Reduces stream like array_reduce() function        | `$stream->toValue($reducer, $initialValue)`             |

##### Transformation Terminal Operations
| Terminal Operation                              | Description                                           | Code Snippet                                            |
|-------------------------------------------------|-------------------------------------------------------|---------------------------------------------------------|
| [`toArray`](docs/stream.md#to-array)                        | Returns array of stream elements                      | `$stream->toArray()`                                    |
| [`toAssociativeArray`](docs/stream.md#to-associative-array) | Returns key-value map of stream elements              | `$stream->toAssociativeArray($keyFunc, $valueFunc)`     |
| [`toPartition`](docs/stream.md#to-partition)                | Partition stream into truthy and falsy lists          | `$stream->toPartition($predicate)`                      |
| [`tee`](docs/stream.md#tee)                                 | Returns array of multiple identical Streams           | `$stream->tee($count)`                                  |

##### Side Effect Terminal Operations
| Terminal Operation              | Description                                    | Code Snippet                                         |
|---------------------------------|------------------------------------------------|------------------------------------------------------|
| [`callForEach`](docs/stream.md#call-for-each) | Perform action via function on each item       | `$stream->callForEach($function)`                    |
| [`print`](docs/stream.md#print)               | `print` each item in the stream                | `$stream->print([$separator], [$prefix], [$suffix])` |
| [`printLn`](docs/stream.md#print-line)        | `print` each item on a new line                | `$stream->printLn()`                                 |
| [`toCsvFile`](docs/stream.md#to-csv-file)     | Write the contents of the stream to a CSV file | `$stream->toCsvFile($fileHandle, [$headers])`        |
| [`toFile`](docs/stream.md#to-file)            | Write the contents of the stream to a file     | `$stream->toFile($fileHandle)`                       |

#### Stream Debug Operations
| Debug Operation              | Description                                              | Code Snippet                     |
|------------------------------|----------------------------------------------------------|----------------------------------|
| [`peek`](docs/stream.md#peek)              | Peek at each element between stream operations           | `$stream->peek($peekFunc)`       |
| [`peekStream`](docs/stream.md#peek-stream) | Peek at the entire stream between operations             | `$stream->peekStream($peekFunc)` |
| [`peekPrint`](docs/stream.md#peek-print)   | Peek at each element by printing between operations      | `$stream->peekPrint()`           |
| [`peekPrintR`](docs/stream.md#peek-printr) | Peek at each element by doing print-r between operations | `$stream->peekPrintR()`          |
| [`printR`](docs/stream.md#print-r)         | `print_r` each item                                      | `$stream->printR()`              |
| [`varDump`](docs/stream.md#var-dump)       | `var_dump` each item                                     | `$stream->varDump()`             |

Documentation
-------------

Full documentation with detailed descriptions, signatures, and code examples for each function.

#### Loop Iteration
- [Multi Iteration](docs/multi-iteration.md) — Chain, Zip, ZipEqual, ZipFilled, ZipLongest
- [Single Iteration](docs/single-iteration.md) — Chunkwise, Compress, Filter, Map, Flatten, GroupBy, and more
- [Infinite Iteration](docs/infinite-iteration.md) — Count, Cycle, Repeat
- [Random Iteration](docs/random-iteration.md) — Choice, CoinFlip, Number, Percentage, RockPaperScissors
- [Math Iteration](docs/math-iteration.md) — Frequencies, Running Average/Difference/Max/Min/Product/Total
- [Set and Multiset Iteration](docs/set-iteration.md) — Distinct, Intersection, Difference, Symmetric Difference, Union
- [Combinatorics Iteration](docs/combinatorics-iteration.md) — Product, Permutations, Combinations, CombinationsWithReplacement
- [Sort Iteration](docs/sort-iteration.md) — ASort, Sort
- [File Iteration](docs/file-iteration.md) — ReadCsv, ReadLines
- [Transform Iteration](docs/transform-iteration.md) — Tee, ToArray, ToAssociativeArray, ToIterator

#### Summarize and Reduce
- [Summary](docs/summary.md) — AllMatch, AnyMatch, IsEmpty, IsSorted, and more
- [Reduce](docs/reduce.md) — ToAverage, ToSum, ToMin, ToMax, ToString, and more

#### Stream
- [Stream](docs/stream.md) — Sources, operations, terminal operations, and debug tools

Usage
-----
All functions work on `iterable` collections:
* `array` (type)
* `Generator` (type)
* `Iterator` (interface)
* `Traversable` (interface)

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

## Strict and Coercive Types

When there is an option, the default will do strict type comparisons:

* scalars: compares strictly by type
* objects: always treats different instances as not equal to each other
* arrays: compares serialized

When type coercion (non-strict types) is available and enabled via optional flag:

* scalars: compares by value via type juggling
* objects: compares serialized (throws `\InvalidArgumentException` if the object cannot be serialized)
* arrays: compares serialized

Standards
---------

IterTools PHP conforms to the following standards:

 * PSR-1  - Basic coding standard (http://www.php-fig.org/psr/psr-1/)
 * PSR-4  - Autoloader (http://www.php-fig.org/psr/psr-4/)
 * PSR-12 - Extended coding style guide (http://www.php-fig.org/psr/psr-12/)

License
-------

IterTools PHP is licensed under the MIT License.

Similar Libraries in Other Languages
------------------------------------

IterTools functionality is not limited to PHP and Python. Other languages have similar libraries.
Familiar functionality is available when working in other languages.

* [IterTools TypeScript/Javascript](https://github.com/Smoren/itertools-ts)
* [IterTools Python](https://docs.python.org/3/library/itertools.html): The original!

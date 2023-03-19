![MathPHP Logo](https://github.com/markrogoyski/itertools-php/blob/main/docs/image/IterToolsLogo.png?raw=true)

### IterTools - PHP Iteration Tools to Power Up Your Loops

Inspired by Python—designed for PHP.

[![Coverage Status](https://coveralls.io/repos/github/markrogoyski/itertools-php/badge.svg?branch=main)](https://coveralls.io/github/markrogoyski/itertools-php?branch=main)
[![License](https://poser.pugx.org/markrogoyski/math-php/license)](https://packagist.org/packages/markrogoyski/itertools-php)

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
* [Русский](docs/README-RU.md)

Quick Reference
-----------

### Loop Iteration Tools

#### Multi Iteration
| Iterator                    | Description                                                                             | Code Snippet                        |
|-----------------------------|-----------------------------------------------------------------------------------------|-------------------------------------|
| [`chain`](#Chain)           | Chain multiple iterables together                                                       | `Multi::chain($list1, $list2)`      |
| [`zip`](#Zip)               | Iterate multiple collections simultaneously until the shortest iterator completes       | `Multi::zip($list1, $list2)`        |
| [`zipEqual`](#ZipEqual)     | Iterate multiple collections of equal length simultaneously, error if lengths not equal | `Multi::zipEqual($list1, $list2)`   |
| [`zipLongest`](#ZipLongest) | Iterate multiple collections simultaneously until the longest iterator completes        | `Multi::zipLongest($list1, $list2)` |

#### Single Iteration
| Iterator                                       | Description                                  | Code Snippet                                                |
|------------------------------------------------|----------------------------------------------|-------------------------------------------------------------|
| [`chunkwise`](#Chunkwise)                      | Iterate by chunks                            | `Single::chunkwise($data, $chunkSize)`                      |
| [`chunkwiseOverlap`](#Chunkwise-Overlap)       | Iterate by overlapped chunks                 | `Single::chunkwiseOverlap($data, $chunkSize, $overlapSize)` |
| [`compress`](#Compress)                        | Filter out elements not selected             | `Single::compress($data, $selectors)`                       |
| [`compressAssociative`](#Compress-Associative) | Filter out elements by keys not selected     | `Single::compressAssociative($data, $selectorKeys)`         |
| [`dropWhile`](#Drop-While)                     | Drop elements while predicate is true        | `Single::dropWhile($data, $predicate)`                      |
| [`filter`](#Filter)                            | Filter for elements where predicate is true  | `Single::filterTrue($data, $predicate)`                     |
| [`filterTrue`](#Filter-True)                   | Filter for truthy elements                   | `Single::filterTrue($data)`                                 |
| [`filterFalse`](#Filter-False)                 | Filter for falsy elements                    | `Single::filterFalse($data)`                                |
| [`filterKeys`](#Filter-Keys)                   | Filter for keys where predicate is true      | `Single::filterKeys($data, $predicate)`                     |
| [`flatMap`](#Flat-Map)                         | Map function onto items and flatten result   | `Single::flaMap($data, $mapper)`                            |
| [`flatten`](#Flatten)                          | Flatten multidimensional iterable            | `Single::flatten($data, [$dimensions])`                     |
| [`groupBy`](#Group-By)                         | Group data by a common element               | `Single::groupBy($data, $groupKeyFunction, [$itemKeyFunc])` |
| [`limit`](#Limit)                              | Iterate up to a limit                        | `Single::limit($data, $limit)`                              |
| [`map`](#Map)                                  | Map function onto each item                  | `Single::map($data, $function)`                             |
| [`pairwise`](#Pairwise)                        | Iterate successive overlapping pairs         | `Single::pairwise($data)`                                   |
| [`reindex`](#Reindex)                          | Reindex keys of key-value iterable           | `Single::reindex($data, $reindexer)`                        |
| [`repeat`](#Repeat)                            | Repeat an item a number of times             | `Single::repeat($item, $repetitions)`                       |
| [`reverse`](#Reverse)                          | Iterate elements in reverse order            | `Single::reverse($data)`                                    |
| [`skip`](#Skip)                                | Iterate after skipping elements              | `Single::skip($data, $count, [$offset])`                    |
| [`slice`](#Slice)                              | Extract a slice of the iterable              | `Single::slice($data, [$start], [$count], [$step])`         |
| [`string`](#String)                            | Iterate the characters of a string           | `Single::string($string)`                                   |
| [`takeWhile`](#Take-While)                     | Iterate elements while predicate is true     | `Single::takeWhile($data, $predicate)`                      |

#### Infinite Iteration
| Iterator                     | Description                | Code Snippet                     |
|------------------------------|----------------------------|----------------------------------|
| [`count`](#Count)            | Count sequentially forever | `Infinite::count($start, $step)` |
| [`cycle`](#Cycle)            | Cycle through a collection | `Infinite::cycle($collection)`   |
| [`repeat`](#Repeat-Infinite) | Repeat an item forever     | `Infinite::repeat($item)`        |

#### Random Iteration
| Iterator                                  | Description                       | Code Snippet                               |
|-------------------------------------------|-----------------------------------|--------------------------------------------|
| [`choice`](#Choice)                       | Random selections from list       | `Random::choice($list, $repetitions)`      |
| [`coinFlip`](#CoinFlip)                   | Random coin flips (0 or 1)        | `Random::coinFlip($repetitions)`           |
| [`number`](#Number)                       | Random numbers                    | `Random::number($min, $max, $repetitions)` |
| [`percentage`](#Percentage)               | Random percentage between 0 and 1 | `Random::percentage($repetitions)`         |
| [`rockPaperScissors`](#RockPaperScissors) | Random rock-paper-scissors hands  | `Random::rockPaperScissors($repetitions)`  |

#### Math Iteration
| Iterator                                   | Description                     | Code Snippet                                       |
|--------------------------------------------|---------------------------------|----------------------------------------------------|
| [`runningAverage`](#Running-Average)       | Running average accumulation    | `Math::runningAverage($numbers, $initialValue)`    |
| [`runningDifference`](#Running-Difference) | Running difference accumulation | `Math::runningDifference($numbers, $initialValue)` |
| [`runningMax`](#Running-Max)               | Running maximum accumulation    | `Math::runningMax($numbers, $initialValue)`        |
| [`runningMin`](#Running-Min)               | Running minimum accumulation    | `Math::runningMin($numbers, $initialValue)`        |
| [`runningProduct`](#Running-Product)       | Running product accumulation    | `Math::runningProduct($numbers, $initialValue)`    |
| [`runningTotal`](#Running-Total)           | Running total accumulation      | `Math::runningTotal($numbers, $initialValue)`      |

#### Set and multiset Iteration
| Iterator                                                        | Description                                               | Code Snippet                                                 |
|-----------------------------------------------------------------|-----------------------------------------------------------|--------------------------------------------------------------|
| [`distinct`](#Distinct)                                         | Iterate only distinct items                               | `Set::distinct($data)`                                       |
| [`intersection`](#Intersection)                                 | Intersection of iterables                                 | `Set::intersection(...$iterables)`                           |
| [`intersectionCoercive`](#Intersection-Coercive)                | Intersection with type coercion                           | `Set::intersectionCoercive(...$iterables)`                   |
| [`partialIntersection`](#Partial-Intersection)                  | Partial intersection of iterables                         | `Set::partialIntersection($minCount, ...$iterables)`         |
| [`partialIntersectionCoercive`](#Partial-Intersection-Coercive) | Partial intersection with type coercion                   | `Set::partialIntersectionCoercive($minCount, ...$iterables)` |
| [`symmetricDifference`](#Symmetric-Difference)                  | Symmetric difference of iterables                         | `Set::symmetricDifference(...$iterables)`                    |
| [`symmetricDifferenceCoercive`](#Symmetric-Difference-Coercive) | Symmetric difference with type coercion                   | `Set::symmetricDifferenceCoercive(...$iterables)`            |
| [`union`](#Union)                                               | Union of iterables                                        | `Set::union(...$iterables)`                                  |
| [`unionCoercive`](#Union-Coercive)                              | Union with type coercion                                  | `Set::unionCoercive(...$iterables)`                          |

#### Sort Iteration
| Iterator                                       | Description                                  | Code Snippet                                              |
|------------------------------------------------|----------------------------------------------|-----------------------------------------------------------|
| [`asort`](#ASort)                              | Iterate a sorted collection maintaining keys | `Sort::asort($data, [$comparator])`                       |
| [`sort`](#Sort)                                | Iterate a sorted collection                  | `Sort::sort($data, [$comparator])`                        |

#### File Iteration
| Iterator                                                        | Description                                               | Code Snippet                                                 |
|-----------------------------------------------------------------|-----------------------------------------------------------|--------------------------------------------------------------|
| [`readCsv`](#Read-CSV)                                          | Intersection a CSV file line by line                      | `File::readCsv($fileHandle)`                                 |
| [`readLines`](#Read-Lines)                                      | Iterate a file line by line                               | `File::readLines($fileHandle)`                               |

#### Transform Iteration
| Iterator                                       | Description                                  | Code Snippet                                                      |
|------------------------------------------------|----------------------------------------------|-------------------------------------------------------------------|
| [`tee`](#Tee)                                  | Iterate duplicate iterators                  | `Transform::tee($data, $count)`                                   |
| [`toArray`](#To-Array)                         | Transform iterable to an array               | `Transform::toArray($data)`                                       |
| [`toAssociativeArray`](#To-Associative-Array)  | Transform iterable to an associative array   | `Transform::toAssociativeArray($data, [$keyFunc], [$valueFunc])`  |
| [`toIterator`](#To-Iterator)                   | Transform iterable to an iterator            | `Transform::toIterator($data)`                                    |

#### Summary
| Summary                                                 | Description                                                              | Code Snippet                                      |
|---------------------------------------------------------|--------------------------------------------------------------------------|---------------------------------------------------|
| [`allMatch`](#All-Match)                                | True if all items are true according to predicate                        | `Summary::allMatch($data, $predicate)`            |
| [`allUnique`](#All-Unique)                              | True if all items are unique                                             | `Summary::allUnique($data, [$strict])`            |
| [`anyMatch`](#Any-Match)                                | True if any item is true according to predicate                          | `Summary::anyMatch($data, $predicate)`            |
| [`arePermutations`](#Are-Permutations)                  | True if iterables are permutations of each other                         | `Summary::arePermutations(...$iterables)`         |
| [`arePermutationsCoercive`](#Are-Permutations-Coercive) | True if iterables are permutations of each other with type coercion      | `Summary::arePermutationsCoercive(...$iterables)` |
| [`exactlyN`](#Exactly-N)                                | True if exactly n items are true according to predicate                  | `Summary::exactlyN($data, $n, $predicate)`        |
| [`isEmpty`](#Is-Empty)                                  | True if iterable has no items                                            | `Summary::isEmpty($data)`                         |
| [`isPartitioned`](#Is-Partitioned)                      | True if partitioned with items true according to predicate before others | `Summary::isPartitioned($data, $predicate)`       |
| [`isSorted`](#Is-Sorted)                                | True if iterable sorted                                                  | `Summary::isSorted($data)`                        |
| [`isReversed`](#Is-Reversed)                            | True if iterable reverse sorted                                          | `Summary::isReversed($data)`                      |
| [`noneMatch`](#None-Match)                              | True if none of items true according to predicate                        | `Summary::noneMatch($data, $predicate)`           |
| [`same`](#Same)                                         | True if iterables are the same                                           | `Summary::same(...$iterables)`                    |
| [`sameCount`](#Same-Count)                              | True if iterables have the same lengths                                  | `Summary::sameCount(...$iterables)`               |

#### Reduce
| Reducer                                | Description                                | Code Snippet                                                  |
|----------------------------------------|--------------------------------------------|---------------------------------------------------------------|
| [`toAverage`](#To-Average)             | Mean average of elements                   | `Reduce::toAverage($numbers)`                                 |
| [`toCount`](#To-Count)                 | Reduce to length of iterable               | `Reduce::toCount($data)`                                      |
| [`toFirst`](#To-First)                 | Reduce to its first value                  | `Reduce::toFirst($data)`                                      |
| [`toFirstAndLast`](#To-First-And-Last) | Reduce to its first and last values        | `Reduce::toFirstAndLast($data)`                               |
| [`toLast`](#To-Last)                   | Reduce to its last value                   | `Reduce::toLast()`                                            |
| [`toMax`](#To-Max)                     | Reduce to its largest element              | `Reduce::toMax($numbers, [$compareBy])`                       |
| [`toMin`](#To-Min)                     | Reduce to its smallest element             | `Reduce::toMin($numbers, [$compareBy])`                       |
| [`toMinMax`](#To-Min-Max)              | Reduce to array of upper and lower bounds  | `Reduce::toMinMax($numbers, [$compareBy])`                    |
| [`toNth`](#To-Nth)                     | Reduce to value at nth position            | `Reduce::toNth($data, $position)`                             |
| [`toProduct`](#To-Product)             | Reduce to the product of its elements      | `Reduce::toProduct($numbers)`                                 |
| [`toRandomValue`](#To-Random-Value)    | Reduce to random value from iterable       | `Reduce::toRandomValue($data)`                                |
| [`toRange`](#To-Range)                 | Reduce to difference of max and min values | `Reduce::toRange($numbers)`                                   |
| [`toString`](#To-String)               | Reduce to joined string                    | `Reduce::toString($data, [$separator], [$prefix], [$suffix])` |
| [`toSum`](#To-Sum)                     | Reduce to the sum of its elements          | `Reduce::toSum($numbers)`                                     |
| [`toValue`](#To-Value)                 | Reduce to value using callable reducer     | `Reduce::toValue($data, $reducer, $initialValue)`             |

### Stream Iteration Tools
#### Stream Sources
| Source                                           | Description                                                     | Code Snippet                                        |
|--------------------------------------------------|-----------------------------------------------------------------|-----------------------------------------------------|
| [`of`](#Of)                                      | Create a stream from an iterable                                | `Stream::of($iterable)`                             |
| [`ofCoinFlips`](#Of-Coin-Flips)                  | Create a stream of random coin flips                            | `Stream::ofCoinFlips($repetitions)`                 |
| [`ofCsvFile`](#Of-CSV-File)                      | Create a stream from a CSV file                                 | `Stream::ofCsvFile($fileHandle)`                    |
| [`ofEmpty`](#Of-Empty)                           | Create an empty stream                                          | `Stream::ofEmpty()`                                 |
| [`ofFileLines`](#Of-File-Lines)                  | Create a stream from lines of a file                            | `Stream::ofFileLines($fileHandle)`                  |
| [`ofRandomChoice`](#Of-Random-Choice)            | Create a stream of random selections                            | `Stream::ofRandomChoice($items, $repetitions)`      |
| [`ofRandomNumbers`](#Of-Random-Numbers)          | Create a stream of random numbers (integers)                    | `Stream::ofRandomNumbers($min, $max, $repetitions)` |
| [`ofRandomPercentage`](#Of-Random-Percentage)    | Create a stream of random percentages between 0 and 1           | `Stream::ofRandomPercentage($repetitions)`          |
| [`ofRange`](#Of-Range)                           | Create a stream of a range of numbers                           | `Stream::ofRange($start, $end, $step)`              |
| [`ofRockPaperScissors`](#Of-Rock-Paper-Scissors) | Create a stream of rock-paper-scissors hands                    | `Stream::ofRockPaperScissors($repetitions)`         |

#### Stream Operations
| Operation                                                                 | Description                                                                               | Code Snippet                                                                      |
|---------------------------------------------------------------------------|-------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------|
| [`asort`](#ASort-1)                                                       | Sorts the iterable source maintaining keys                                                | `$stream->asort([$comparator])`                                                   |
| [`chainWith`](#Chain-With)                                                | Chain iterable source withs given iterables together into a single iteration              | `$stream->chainWith(...$iterables)`                                               |
| [`compress`](#Compress-1)                                                 | Compress source by filtering out data not selected                                        | `$stream->compress($selectors)`                                                   |
| [`compressAssociative`](#Compress-Associative-1)                          | Compress source by filtering out keys not selected                                        | `$stream->compressAssociative($selectorKeys)`                                     |
| [`chunkwise`](#Chunkwise-1)                                               | Iterate by chunks                                                                         | `$stream->chunkwise($chunkSize)`                                                  |
| [`chunkwiseOverlap`](#Chunkwise-Overlap-1)                                | Iterate by overlapped chunks                                                              | `$stream->chunkwiseOverlap($chunkSize, $overlap)`                                 |
| [`distinct`](#Distinct-1)                                                 | Filter out elements: iterate only unique items                                            | `$stream->distinct([$strict])`                                                    |
| [`dropWhile`](#Drop-While-1)                                              | Drop elements from the iterable source while the predicate function is true               | `$stream->dropWhile($predicate)`                                                  |
| [`filter`](#Filter-1)                                                     | Filter for only elements where the predicate function is true                             | `$stream->filterTrue($predicate)`                                                 |
| [`filterTrue`](#Filter-True-1)                                            | Filter for only truthy elements                                                           | `$stream->filterTrue()`                                                           |
| [`filterFalse`](#Filter-False-1)                                          | Filter for only falsy elements                                                            | `$stream->filterFalse()`                                                          |
| [`filterKeys`](#Filter-Keys-1)                                            | Filter for keys where predicate function is true                                          | `$stream->filterKeys($predicate)`                                                 |
| [`flatMap`](#Flat-Map-1)                                                  | Map function onto elements and flatten result                                             | `$stream->flatMap($function)`                                                     |
| [`flatten`](#Flatten-1)                                                   | Flatten multidimensional stream                                                           | `$stream->flatten($dimensions)`                                                   |
| [`groupBy`](#Group-By-1)                                                  | Group iterable source by a common data element                                            | `$stream->groupBy($groupKeyFunction, [$itemKeyFunc])`                             |
| [`infiniteCycle`](#Infinite-Cycle)                                        | Cycle through the elements of iterable source sequentially forever                        | `$stream->infiniteCycle()`                                                        |
| [`intersectionWith`](#Intersection-With)                                  | Intersect iterable source and given iterables                                             | `$stream->intersectionWith(...$iterables)`                                        |
| [`intersection CoerciveWith`](#Intersection-Coercive-With)                | Intersect iterable source and given iterables with type coercion                          | `$stream->intersectionCoerciveWith(...$iterables)`                                |
| [`limit`](#Limit-1)                                                       | Limit the stream's iteration                                                              | `$stream->limit($limit)`                                                          |
| [`map`](#Map-1)                                                           | Map function onto elements                                                                | `$stream->map($function)`                                                         |
| [`pairwise`](#Pairwise-1)                                                 | Return pairs of elements from iterable source                                             | `$stream->pairwise()`                                                             |
| [`partialIntersectionWith`](#Partial-Intersection-With)                   | Partially intersect iterable source and given iterables                                   | `$stream->partialIntersectionWith( $minIntersectionCount, ...$iterables)`         |
| [`partialIntersection CoerciveWith`](#Partial-Intersection-Coercive-With) | Partially intersect iterable source and given iterables with type coercion                | `$stream->partialIntersectionCoerciveWith( $minIntersectionCount, ...$iterables)` |
| [`reindex`](#Reindex-1)                                                   | Reindex keys of key-value stream                                                          | `$stream->reindex($reindexer)`                                                    |
| [`reverse`](#Reverse-1)                                                   | Reverse elements of the stream                                                            | `$stream->reverse()`                                                              |
| [`runningAverage`](#Running-Average-1)                                    | Accumulate the running average (mean) over iterable source                                | `$stream->runningAverage($initialValue)`                                          |
| [`runningDifference`](#Running-Difference-1)                              | Accumulate the running difference over iterable source                                    | `$stream->runningDifference($initialValue)`                                       |
| [`runningMax`](#Running-Max-1)                                            | Accumulate the running max over iterable source                                           | `$stream->runningMax($initialValue)`                                              |
| [`runningMin`](#Running-Min-1)                                            | Accumulate the running min over iterable source                                           | `$stream->runningMin($initialValue)`                                              |
| [`runningProduct`](#Running-Product-1)                                    | Accumulate the running product over iterable source                                       | `$stream->runningProduct($initialValue)`                                          |
| [`runningTotal`](#Running-Total-1)                                        | Accumulate the running total over iterable source                                         | `$stream->runningTotal($initialValue)`                                            |
| [`skip`](#Skip-1)                                                         | Skip some elements of the stream                                                          | `$stream->skip($count, [$offset])`                                                |
| [`slice`](#Slice-1)                                                       | Extract a slice of the stream                                                             | `$stream->slice([$start], [$count], [$step])`                                     |
| [`sort`](#Sort-1)                                                         | Sorts the stream                                                                          | `$stream->sort([$comparator])`                                                    |
| [`symmetricDifferenceWith`](#Symmetric-Difference-With)                   | Symmetric difference of iterable source and given iterables                               | `$this->symmetricDifferenceWith(...$iterables)`                                   |
| [`symmetricDifference CoerciveWith`](#Symmetric-Difference-Coercive-With) | Symmetric difference of iterable source and given iterables with type coercion            | `$this->symmetricDifferenceCoerciveWith( ...$iterables)`                          |
| [`takeWhile`](#Take-While-1)                                              | Return elements from the iterable source as long as the predicate is true                 | `$stream->takeWhile($predicate)`                                                  |
| [`unionWith`](#Union-With)                                                | Union of stream with iterables                                                            | `$stream->unionWith(...$iterables)`                                               |
| [`unionCoerciveWith`](#Union-Coercive-With)                               | Union of stream with iterables with type coercion                                         | `$stream->unionCoerciveWith(...$iterables)`                                       |
| [`zipWith`](#Zip-With)                                                    | Iterate iterable source with another iterable collections simultaneously                  | `$stream->zipWith(...$iterables)`                                                 |
| [`zipLongestWith`](#Zip-Longest-With)                                     | Iterate iterable source with another iterable collections simultaneously                  | `$stream->zipLongestWith(...$iterables)`                                          |
| [`zipEqualWith`](#Zip-Equal-With)                                         | Iterate iterable source with another iterable collections of equal lengths simultaneously | `$stream->zipEqualWith(...$iterables)`                                            |

#### Stream Terminal Operations
##### Summary Terminal Operations
| Terminal Operation                                               | Description                                                                      | Code Snippet                                           |
|------------------------------------------------------------------|----------------------------------------------------------------------------------|--------------------------------------------------------|
| [`allMatch`](#All-Match-1)                                       | Returns true if all items in stream match predicate                              | `$stream->allMatch($predicate)`                        |
| [`allUnique`](#All-Unique-1)                                     | Returns true if all items in stream are unique                                   | `$stream->allUnique([$strict]])`                       |
| [`anyMatch`](#Any-Match-1)                                       | Returns true if any item in stream matches predicate                             | `$stream->anyMatch($predicate)`                        |
| [`arePermutationsWith`](#Are-Permutations-With)                  | Returns true if all iterables permutations of stream                             | `$stream->arePermutationsWith(...$iterables)`          |
| [`arePermutationsCoerciveWith`](#Are-Permutations-Coercive-With) | Returns true if all iterables permutations of stream with type coercion          | `$stream->arePermutationsCoerciveWith(...$iterables)`  |
| [`exactlyN`](#Exactly-N-1)                                       | Returns true if exactly n items are true according to predicate                  | `$stream->exactlyN($n, $predicate)`                    |
| [`isEmpty`](#Is-Empty-1)                                         | Returns true if stream has no items                                              | `$stream::isEmpty()`                                   |
| [`isPartitioned`](#Is-Partitioned-1)                             | Returns true if partitioned with items true according to predicate before others | `$stream::isPartitioned($predicate)`                   |
| [`isSorted`](#Is-Sorted-1)                                       | Returns true if stream is sorted in ascending order                              | `$stream->isSorted()`                                  |
| [`isReversed`](#Is-Reversed-1)                                   | Returns true if stream is sorted in reverse descending order                     | `$stream->isReversed()`                                |
| [`noneMatch`](#None-Match-1)                                     | Returns true if none of the items in stream match predicate                      | `$stream->noneMatch($predicate)`                       |
| [`sameWith`](#Same-With)                                         | Returns true if stream and all given collections are the same                    | `$stream->sameWith(...$iterables)`                     |
| [`sameCountWith`](#Same-Count-With)                              | Returns true if stream and all given collections have the same lengths           | `$stream->sameCountWith(...$iterables)`                |

##### Reduction Terminal Operations
| Terminal Operation                       | Description                                        | Code Snippet                                            |
|------------------------------------------|----------------------------------------------------|---------------------------------------------------------|
| [`toAverage`](#To-Average-1)             | Reduces stream to the mean average of its items    | `$stream->toAverage()`                                  |
| [`toCount`](#To-Count-1)                 | Reduces stream to its length                       | `$stream->toCount()`                                    |
| [`toFirst`](#To-First-1)                 | Reduces stream to its first value                  | `$stream->toFirst()`                                    |
| [`toFirstAndLast`](#To-First-And-Last-1) | Reduces stream to its first and last values        | `$stream->toFirstAndLast()`                             |
| [`toLast`](#To-Last-1)                   | Reduces stream to its last value                   | `$stream->toLast()`                                     |
| [`toMax`](#To-Max-1)                     | Reduces stream to its max value                    | `$stream->toMax([$compareBy])`                          |
| [`toMin`](#To-Min-1)                     | Reduces stream to its min value                    | `$stream->toMin([$compareBy])`                          |
| [`toMinMax`](#To-Min-Max-1)              | Reduces stream to array of upper and lower bounds  | `$stream->toMinMax([$compareBy])`                       |
| [`toNth`](#To-Nth-1)                     | Reduces stream to value at nth position            | `$stream->toNth($position)`                             |
| [`toProduct`](#To-Product-1)             | Reduces stream to the product of its items         | `$stream->toProduct()`                                  |
| [`toString`](#To-String-1)               | Reduces stream to joined string                    | `$stream->toString([$separator], [$prefix], [$suffix])` |
| [`toSum`](#To-Sum-1)                     | Reduces stream to the sum of its items             | `$stream->toSum()`                                      |
| [`toRandomValue`](#To-Random-Value-1)    | Reduces stream to random value within it           | `$stream->toRandomValue()`                              |
| [`toRange`](#To-Range-1)                 | Reduces stream to difference of max and min values | `$stream->toRange()`                                    |
| [`toValue`](#To-Value-1)                 | Reduces stream like array_reduce() function        | `$stream->toValue($reducer, $initialValue)`             |

##### Transformation Terminal Operations
| Terminal Operation                              | Description                                           | Code Snippet                                            |
|-------------------------------------------------|-------------------------------------------------------|---------------------------------------------------------|
| [`toArray`](#To-Array-1)                        | Returns array of stream elements                      | `$stream->toArray()`                                    |
| [`toAssociativeArray`](#To-Associative-Array-1) | Returns key-value map of stream elements              | `$stream->toAssociativeArray($keyFunc, $valueFunc)`     |
| [`tee`](#Tee-1)                                 | Returns array of multiple identical Streams           | `$stream->tee($count)`                                  |

##### Side Effect Terminal Operations
| Terminal Operation              | Description                                    | Code Snippet                                          |
|---------------------------------|------------------------------------------------|-------------------------------------------------------|
| [`callForEach`](#Call-For-Each) | Perform action via function on each item       | `$stream->callForEach($function)`                     |
| [`print`](#Print)               | `print` each item in the stream                | `$stream->print([$separator], [$prefix], [$suffix])`  |
| [`printLn`](#Print-Line)        | `print` each item on a new line                | `$stream->printLn()`                                  |
| [`printR`](#Print-R)            | `print_r` each item                            | `$stream->printR()`                                   |
| [`toCsvFile`](#To-CSV-File)     | Write the contents of the stream to a CSV file | `$stream->toCsvFile($fileHandle, [$headers])`         |
| [`toFile`](#To-File)            | Write the contents of the stream to a file     | `$stream->toFile($fileHandle)`                        |
| [`var_dump`](#Var-Dump)         | `var_dump` each item                           | `$stream->varDump()`                                  |

#### Stream Debug Operations
| Debug Operation                   | Description                                              | Code Snippet                          |
|-----------------------------------|----------------------------------------------------------|---------------------------------------|
| [`peek`](#Peek)                   | Peek at each element between stream operations           | `$stream->peek($peekFunc)`            |
| [`peekStream`](#Peek-Stream)      | Peek at the entire stream between operations             | `$stream->peekStream($peekFunc)`      |
| [`peekPrint`](#Peek-Print)        | Peek at each element by printing between operations      | `$stream->peekPrint()`                |
| [`peekPrintR`](#Peek-PrintR)      | Peek at each element by doing print-r between operations | `$stream->peekPrintR()`               |

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

```Single::chunkwiseOverlap(iterable $data, int $chunkSize, int $overlapSize, bool $includeIncompleteTail = true)```

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

### Compress Associative
Compress an iterable by filtering out keys that are not selected.

```Single::compressAssociative(string $data, array $selectorKeys)```

* Standard PHP array/iterator keys only (string, integer).

```php
use IterTools\Single;

$starWarsEpisodes = [
    'I'    => 'The Phantom Menace',
    'II'   => 'Attack of the Clones',
    'III'  => 'Revenge of the Sith',
    'IV'   => 'A New Hope',
    'V'    => 'The Empire Strikes Back',
    'VI'   => 'Return of the Jedi',
    'VII'  => 'The Force Awakens',
    'VIII' => 'The Last Jedi',
    'IX'   => 'The Rise of Skywalker',
];
$originalTrilogyNumbers = ['IV', 'V', 'VI'];

foreach (Single::compressAssociative($starWarsEpisodes, $originalTrilogyNumbers) as $episode => $title) {
    print("$episode: $title" . \PHP_EOL);
}
// IV: A New Hope
// V: The Empire Strikes Back
// VI: Return of the Jedi
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

### Filter
Filter out elements from the iterable only returning elements where the predicate function is true.

```Single::filter(iterable $data, callable $predicate)```

```php
use IterTools\Single;

$starWarsEpisodes   = [1, 2, 3, 4, 5, 6, 7, 8, 9];
$goodMoviePredicate = fn ($episode) => $episode > 3 && $episode < 8;

foreach (Single::filter($starWarsEpisodes, $goodMoviePredicate) as $goodMovie) {
    print($goodMovie);
}
// 4, 5, 6, 7
```

### Filter True
Filter out elements from the iterable only returning elements that are truthy.

```Single::filterTrue(iterable $data)```

```php
use IterTools\Single;

$reportCardGrades = [100, 0, 95, 85, 0, 94, 0];

foreach (Single::filterTrue($reportCardGrades) as $goodGrade) {
    print($goodGrade);
}
// 100, 95, 85, 94
```

### Filter False
Filter out elements from the iterable only returning elements where the predicate function is false.

If no predicate is provided, the boolean value of the data is used.

```Single::filterFalse(iterable $data, callable $predicate)```

```php
use IterTools\Single;

$alerts = [0, 1, 1, 0, 1, 0, 0, 1, 1];

foreach (Single::filterFalse($alerts) as $noAlert) {
    print($noAlert);
}
// 0, 0, 0, 0
```

### Filter Keys
Filter out elements from the iterable only returning elements for which keys the predicate function is true.

```Single::filterKeys(iterable $data, callable $predicate)```

```php
use IterTools\Single;

$olympics = [
    2000 => 'Sydney',
    2002 => 'Salt Lake City',
    2004 => 'Athens',
    2006 => 'Turin',
    2008 => 'Beijing',
    2010 => 'Vancouver',
    2012 => 'London',
    2014 => 'Sochi',
    2016 => 'Rio de Janeiro',
    2018 => 'Pyeongchang',
    2020 => 'Tokyo',
    2022 => 'Beijing',
];

$summerFilter = fn ($year) => $year % 4 === 0;

foreach (Single::filterKeys($olympics, $summerFilter) as $year => $hostCity) {
    print("$year: $hostCity" . \PHP_EOL);
}
// 2000: Sydney
// 2004: Athens
// 2008: Beijing
// 2012: London
// 2016: Rio de Janeiro
// 2020: Tokyo
```

### Flat Map
Map a function only the elements of the iterable and then flatten the results.

```Single::flatMap(iterable $data, callable $mapper)```

```php
use IterTools\Single;

$data   = [1, 2, 3, 4, 5];
$mapper = fn ($item) => [$item, -$item];

foreach (Single::flatMap($data, $mapper) as $number) {
    print($number . ' ');
}
// 1 -1 2 -2 3 -3 4 -4 5 -5
```

### Flatten
Flatten a multidimensional iterable.

```Single::flatten(iterable $data, int $dimensions = 1)```

```php
use IterTools\Single;

$multidimensional = [1, [2, 3], [4, 5]];

$flattened = [];
foreach (Single::flatten($multidimensional) as $number) {
    $flattened[] = $number;
}
// [1, 2, 3, 4, 5]
```

### Group By
Group data by a common data element.

```Single::groupBy(iterable $data, callable $groupKeyFunction, callable $itemKeyFunction = null)```

* The `$groupKeyFunction` determines the key to group elements by.
* The optional `$itemKeyFunction` allows custom indexes within each group member.

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

### Map
Map a function onto each element.

```Single::map(iterable $data, callable $function)```

```php
use IterTools\Single;

$grades               = [100, 99, 95, 98, 100];
$strictParentsOpinion = fn ($g) => $g === 100 ? 'A' : 'F';

foreach (Single::map($grades, $strictParentsOpinion) as $actualGrade) {
    print($actualGrade);
}
// A, F, F, F, A
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

### Reindex
Reindex keys of key-value iterable using indexer function.

```Single::reindex(string $data, callable $indexer)```

```php
use IterTools\Single;

$data = [
    [
        'title'   => 'Star Wars: Episode IV – A New Hope',
        'episode' => 'IV',
        'year'    => 1977,
    ],
    [
        'title'   => 'Star Wars: Episode V – The Empire Strikes Back',
        'episode' => 'V',
        'year'    => 1980,
    ],
    [
        'title' => 'Star Wars: Episode VI – Return of the Jedi',
        'episode' => 'VI',
        'year' => 1983,
    ],
];
$reindexFunc = fn (array $swFilm) => $swFilm['episode'];

$reindexedData = [];
foreach (Single::reindex($data, $reindexFunc) as $key => $filmData) {
    $reindexedData[$key] = $filmData;
}
// [
//     'IV' => [
//         'title'   => 'Star Wars: Episode IV – A New Hope',
//         'episode' => 'IV',
//         'year'    => 1977,
//     ],
//     'V' => [
//         'title'   => 'Star Wars: Episode V – The Empire Strikes Back',
//         'episode' => 'V',
//         'year'    => 1980,
//     ],
//     'VI' => [
//         'title' => 'Star Wars: Episode VI – Return of the Jedi',
//         'episode' => 'VI',
//         'year' => 1983,
//     ],
// ]
```

### Reverse
Reverse the elements of an iterable.

```Single::reverse(iterable $data)```

```php
use IterTools\Single;

$words = ['Alice', 'answers', 'your', 'questions', 'Bob'];

foreach (Single::reverse($words) as $word) {
    print($word . ' ');
}
// Bob questions your answers Alice
```

### Skip
Skip n elements in the iterable after optional offset offset.

```Single::skip(iterable $data, int $count, int $offset = 0)```

```php
use IterTools\Single;

$movies = [
    'The Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith',
    'A New Hope', 'The Empire Strikes Back', 'Return of the Jedi',
    'The Force Awakens', 'The Last Jedi', 'The Rise of Skywalker'
];

$prequelsRemoved = [];
foreach (Single::skip($movies, 3) as $nonPrequel) {
    $prequelsRemoved[] = $nonPrequel;
} // Episodes IV - IX

$onlyTheBest = [];
foreach (Single::skip($prequelsRemoved, 3, 3) as $nonSequel) {
    $onlyTheBest[] = $nonSequel;
}
// 'A New Hope', 'The Empire Strikes Back', 'Return of the Jedi'
```

### Slice
Extract a slice of the iterable.

```Single::slice(iterable $data, int $start = 0, int $count = null, int $step = 1)```

```php
use IterTools\Single;

$olympics = [1992, 1994, 1996, 1998, 2000, 2002, 2004, 2006, 2008, 2010, 2012, 2014, 2016, 2018, 2020, 2022];
$winterOlympics = [];

foreach (Single::slice($olympics, 1, 8, 2) as $winterYear) {
    $winterOlympics[] = $winterYear;
}
// [1994, 1998, 2002, 2006, 2010, 2014, 2018, 2022]
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

## Set and multiset
### Distinct
Filter out elements from the iterable only returning distinct elements.

```Set::distinct(iterable $data, bool $strict = true)```

Defaults to [strict type](#Strict-and-Coercive-Types) comparisons. Set strict to false for type coercion comparisons.

```php
use IterTools\Set;

$chessSet = ['rook', 'rook', 'knight', 'knight', 'bishop', 'bishop', 'king', 'queen', 'pawn', 'pawn', ... ];

foreach (Set::distinct($chessSet) as $chessPiece) {
    print($chessPiece);
}
// rook, knight, bishop, king, queen, pawn

$mixedTypes = [1, '1', 2, '2', 3];

foreach (Set::distinct($mixedTypes, false) as $datum) {
    print($datum);
}
// 1, 2, 3
```

### Intersection
Iterates intersection of iterables.

```Set::intersection(iterable ...$iterables)```

If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) intersection rules apply.

```php
use IterTools\Set;

$chessPieces = ['rook', 'knight', 'bishop', 'queen', 'king', 'pawn'];
$shogiPieces = ['rook', 'knight', 'bishop' 'king', 'pawn', 'lance', 'gold general', 'silver general'];

foreach (Set::intersection($chessPieces, $shogiPieces) as $commonPiece) {
    print($commonPiece);
}
// rook, knight, bishop, king, pawn
```

### Intersection Coercive
Iterates intersection of iterables using [type coercion](#Strict-and-Coercive-Types).

```Set::intersectionCoercive(iterable ...$iterables)```

If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) intersection rules apply.

```php
use IterTools\Set;

$numbers  = [1, 2, 3, 4, 5];
$numerics = ['1', '2', 3];

foreach (Set::intersectionCoercive($numbers, $numerics) as $commonNumber) {
    print($commonNumber);
}
// 1, 2, 3
```

### Partial Intersection
Iterates [M-partial intersection](https://github.com/Smoren/partial-intersection-php) of iterables.

```Set::partialIntersection(int $minIntersectionCount, iterable ...$iterables)```

* If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) intersection rules apply.

```php
use IterTools\Set;

$staticallyTyped    = ['c++', 'java', 'c#', 'go', 'haskell'];
$dynamicallyTyped   = ['php', 'python', 'javascript', 'typescript'];
$supportsInterfaces = ['php', 'java', 'c#', 'typescript'];

foreach (Set::partialIntersection(2, $staticallyTyped, $dynamicallyTyped, $supportsInterfaces) as $language) {
    print($language);
}
// c++, java, c#, go, php
```

### Partial Intersection Coercive
Iterates [M-partial intersection](https://github.com/Smoren/partial-intersection-php) of iterables using [type coercion](#Strict-and-Coercive-Types).

```Set::partialIntersectionCoercive(int $minIntersectionCount, iterable ...$iterables)```

* If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) intersection rules apply.

```php
use IterTools\Set;

$set1 = [1, 2, 3],
$set2 = ['2', '3', 4, 5],
$set3 = [1, '2'],

foreach (Set::partialIntersectionCoercive(2, $set1, $set2, $set3) as $partiallyCommonNumber) {
    print($partiallyCommonNumber);
}
// 1, 2, 3
```

### Symmetric difference
Iterates the symmetric difference of iterables.

```Set::symmetricDifference(iterable ...$iterables)```

If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) difference rules apply.

```php
use IterTools\Set;

$a = [1, 2, 3, 4, 7];
$b = ['1', 2, 3, 5, 8];
$c = [1, 2, 3, 6, 9];

foreach (Set::symmetricDifference($a, $b, $c) as $item) {
    print($item);
}
// 1, 4, 5, 6, 7, 8, 9
```

### Symmetric difference Coercive
Iterates the symmetric difference of iterables with [type coercion](#Strict-and-Coercive-Types).

```Set::symmetricDifferenceCoercive(iterable ...$iterables)```

If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) difference rules apply.

```php
use IterTools\Set;

$a = [1, 2, 3, 4, 7];
$b = ['1', 2, 3, 5, 8];
$c = [1, 2, 3, 6, 9];

foreach (Set::symmetricDifferenceCoercive($a, $b, $c) as $item) {
    print($item);
}
// 4, 5, 6, 7, 8, 9
```

### Union
Iterates the union of iterables.

```Set::union(iterable ...$iterables)```

If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) union rules apply.

```php
use IterTools\Set;

$a = [1, 2, 3];
$b = [3, 4];
$c = [1, 2, 3, 6, 7];

foreach (Set::union($a, $b, $c) as $item) {
    print($item);
}
//1, 2, 3, 4, 6, 7
```

### Union Coercive
Iterates the union of iterables with [type coercion](#Strict-and-Coercive-Types).

```Set::unionCoercive(iterable ...$iterables)```

If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) union rules apply.

```php
use IterTools\Set;

$a = ['1', 2, 3];
$b = [3, 4];
$c = [1, 2, 3, 6, 7];

foreach (Set::unionCoercive($a, $b, $c) as $item) {
    print($item);
}
//1, 2, 3, 4, 6, 7
```

## Sort Iteration
### ASort
Iterate the collection sorted while maintaining the associative key index relations.

```Sort::sort(iterable $data, callable $comparator = null)```

Uses default sorting if optional comparator function not provided.

```php
use IterTools\Single;

$worldPopulations = [
    'China'     => 1_439_323_776,
    'India'     => 1_380_004_385,
    'Indonesia' => 273_523_615,
    'Pakistan'  => 220_892_340,
    'USA'       => 331_002_651,
];

foreach (Sort::sort($worldPopulations) as $country => $population) {
    print("$country: $population" . \PHP_EOL);
}
// Pakistan: 220,892,340
// Indonesia: 273,523,615
// USA: 331,002,651
// India: 1,380,004,385
// China: 1,439,323,776
```

### Sort
Iterate the collection sorted.

```Sort::sort(iterable $data, callable $comparator = null)```

Uses default sorting if optional comparator function not provided.

```php
use IterTools\Single;

$data = [3, 4, 5, 9, 8, 7, 1, 6, 2];

foreach (Sort::sort($data) as $datum) {
    print($datum);
}
// 1, 2, 3, 4, 5, 6, 7, 8, 9
```

## File
### Read CSV
Iterate the lines of a CSV file.

```File::readCsv(resource $fileHandle, string $separator = ',', string $enclosure = '"', string $escape = '\\')```

```php
use IterTools\File;

$fileHandle = \fopen('path/to/file.csv', 'r');

foreach (File::readCsv($fileHandle) as $row) {
    print_r($row);
}
// Each column field is an element of the array
```

### Read Lines
Iterate the lines of a file.

```File::readLines(resource $fileHandle)```

```php
use IterTools\File;

$fileHandle = \fopen('path/to/file.txt', 'r');

foreach (File::readLines($fileHandle) as $line) {
    print($line);
}
```

## Transform
### Tee
Return several independent (duplicated) iterators from a single iterable.

```Transform::tee(iterable $data, int $count): array```

```php
use IterTools\Transform;

$daysOfWeek = ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun'];
$count = 3;

[$week1, $week2, $week3] = Transform::tee($data, $count);
// Each $week contains iterator containing ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun']
```

### To Array
Transforms any iterable to an array.

```Transform::toArray(iterable $data): array```

```php
use IterTools\Transform;

$iterator = new \ArrayIterator([1, 2, 3, 4, 5]);

$array = Transform::toArray($iterator);
```

### To Associative Array
Transforms any iterable to an associative array.

```Transform::toAssociativeArray(iterable $data, callable $keyFunc = null, callable $valueFunc = null): array```

```php
use IterTools\Transform;

$messages = ['message 1', 'message 2', 'message 3'];

$keyFunc   = fn ($msg) => \md5($msg);
$valueFunc = fn ($msg) => strtoupper($msg);

$associativeArray = Transform::toAssociativeArray($messages, $keyFunc, $valueFunc);
// [
//     '1db65a6a0a818fd39655b95e33ada11d' => 'MESSAGE 1',
//     '83b2330607fe8f817ce6d24249dea373' => 'MESSAGE 2',
//     '037805d3ad7b10c5b8425427b516b5ce' => 'MESSAGE 3',
// ]
```

### To Iterator
Transforms any iterable to an iterator.

```Transform::toArray(iterable $data): array```

```php
use IterTools\Transform;

$array = [1, 2, 3, 4, 5];

$iterator = Transform::toIterator($array);
```

## Summary
### All Match
Returns true if all elements match the predicate function.

```Summary::allMatch(iterable $data, callable $predicate): bool```

```php
use IterTools\Summary;

$finalFantasyNumbers = [4, 5, 6];
$isOnSuperNintendo   = fn ($ff) => $ff >= 4 && $ff <= 6;

$boolean = Summary::allMatch($finalFantasyNumbers, $isOnSuperNintendo);
// true

$isOnPlaystation = fn ($ff) => $ff >= 7 && $ff <= 9;

$boolean = Summary::allMatch($finalFantasyNumbers, $isOnPlaystation);
// false
```

### All Unique
Returns true if all elements are unique.

```Summary::allUnique(iterable $data, bool $strict = true): bool```

Defaults to [strict type](#Strict-and-Coercive-Types) comparisons. Set strict to false for type coercion comparisons.

```php
use IterTools\Summary;

$items = ['fingerprints', 'snowflakes', 'eyes', 'DNA']

$boolean = Summary::allUnique($items);
// true
```

### Any Match
Returns true if any element matches the predicate function.

```Summary::anyMatch(iterable $data, callable $predicate): bool```

```php
use IterTools\Summary;

$answers          = ['fish', 'towel', 42, "don't panic"];
$isUltimateAnswer = fn ($a) => a == 42;

$boolean = Summary::anyMatch($answers, $isUltimateAnswer);
// true
```

### Are Permutations
Returns true if all iterables are permutations of each other.

```Summary::arePermutations(iterable ...$iterables): bool```

```php
use IterTools\Summary;

$iter = ['i', 't', 'e', 'r'];
$rite = ['r', 'i', 't', 'e'];
$reit = ['r', 'e', 'i', 't'];
$tier = ['t', 'i', 'e', 'r'];
$tire = ['t', 'i', 'r', 'e'];
$trie = ['t', 'r', 'i', 'e'];

$boolean = Summary::arePermutations($iter, $rite, $reit, $tier, $tire, $trie);
// true
```

### Are Permutations Coercive
Returns true if all iterables are permutations of each other with [type coercion](#Strict-and-Coercive-Types).

```Summary::arePermutationsCoercive(iterable ...$iterables): bool```

```php
use IterTools\Summary;

$set1 = [1, 2.0, '3'];
$set2 = [2.0, '1', 3];
$set3 = [3, 2, 1];

$boolean = Summary::arePermutationsCoercive($set1, $set2, $set3);
// true
```

### Exactly N
Returns true if exactly n items are true according to a predicate function.

- Predicate is optional.
- Default predicate is boolean value of each item.

```Summary::exactlyN(iterable $data, int $n, callable $predicate): bool```

```php
use IterTools\Summary;

$twoTruthsAndALie = [true, true, false];
$n                = 2;

$boolean = Summary::exactlyN($twoTruthsAndALie, $n);
// true

$ages      = [18, 21, 24, 54];
$n         = 4;
$predicate = fn ($age) => $age >= 21;

$boolean = Summary::exactlyN($ages, $n, $predicate);
// false
```

### Is Empty
Returns true if the iterable is empty having no items.

```Summary::isEmpty(iterable $data): bool```

```php
use IterTools\Summary;

$data = []

$boolean = Summary::isEmpty($data);
// true
```

### Is Partitioned
Returns true if all elements of given collection that satisfy the predicate appear before all elements that don't.

- Returns true for empty collection or for collection with single item.
- Default predicate if not provided is the boolean value of each data item.

```Summary::isPartitioned(iterable $data, callable $predicate = null): bool```

```php
use IterTools\Summary;

$numbers          = [0, 2, 4, 1, 3, 5];
$evensBeforeOdds = fn ($item) => $item % 2 === 0;

$boolean = Summary::isPartitioned($numbers, $evensBeforeOdds);
```

### Is Sorted
Returns true if elements are sorted, otherwise false.

- Elements must be comparable.
- Returns true if empty or has only one element.

```Summary::isSorted(iterable $data): bool```

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

```Summary::isReversed(iterable $data): bool```

```php
use IterTools\Summary;

$numbers = [5, 4, 3, 2, 1];

$boolean = Summary::isReversed($numbers);
// true

$numbers = [1, 4, 3, 2, 1];

$boolean = Summary::isReversed($numbers);
// false
```

### None Match
Returns true if no element matches the predicate function.

```Summary::noneMatch(iterable $data, callable $predicate): bool```

```php
use IterTools\Summary;

$grades         = [45, 50, 61, 0];
$isPassingGrade = fn ($grade) => $grade >= 70;

$boolean = Summary::noneMatch($grades, $isPassingGrade);
// true
```

### Same
Returns true if all given collections are the same.

For single iterable or empty iterables list returns true.

```Summary::same(iterable ...$iterables): bool```

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

```Summary::sameCount(iterable ...$iterables): bool```

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

```Reduce::toAverage(iterable $data): float```

```php
use IterTools\Reduce;

$grades = [100, 90, 95, 85, 94];

$finalGrade = Reduce::toAverage($numbers);
// 92.8
```

### To Count
Reduces iterable to its length.

```Reduce::toCount(iterable $data): int```

```php
use IterTools\Reduce;

$someIterable = ImportantThing::getCollectionAsIterable();

$length = Reduce::toCount($someIterable);
// 3
```

### To First
Reduces iterable to its first element.

```Reduce::toFirst(iterable $data): mixed```

Throws `\LengthException` if collection is empty.

```php
use IterTools\Reduce;

$medals = ['gold', 'silver', 'bronze'];

$first = Reduce::toFirst($medals);
// gold
```

### To First And Last
Reduces iterable to its first and last elements.

```Reduce::toFirstAndLast(iterable $data): array{mixed, mixed}```

Throws `\LengthException` if collection is empty.

```php
use IterTools\Reduce;

$weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

$firstAndLast = Reduce::toFirstAndLast($weekdays);
// [Monday, Friday]
```

### To Last
Reduces iterable to its last element.

```Reduce::toLast(iterable $data): mixed```

Throws `\LengthException` if collection is empty.

```php
use IterTools\Reduce;

$gnomesThreePhasePlan = ['Collect underpants', '?', 'Profit'];

$lastPhase = Reduce::toLast($gnomesThreePhasePlan);
// Profit
```

### To Max
Reduces to the max value.

```Reduce::toMax(iterable $data, callable $compareBy = null): mixed|null```

- Optional callable param `$compareBy` must return comparable value.
- If `$compareBy` is not provided then items of given collection must be comparable.
- Returns null if collection is empty.

```php
use IterTools\Reduce;

$numbers = [5, 3, 1, 2, 4];

$result = Reduce::toMax($numbers);
// 5

$movieRatings = [
    [
        'title' => 'Star Wars: Episode IV - A New Hope',
        'rating' => 4.6
    ],
    [
        'title' => 'Star Wars: Episode V - The Empire Strikes Back',
        'rating' => 4.8
    ],
    [
        'title' => 'Star Wars: Episode VI - Return of the Jedi',
        'rating' => 4.6
    ],
];
$compareBy = fn ($movie) => $movie['rating'];

$highestRatedMovie = Reduce::toMax($movieRatings, $compareBy);
// [
//     'title' => 'Star Wars: Episode V - The Empire Strikes Back',
//     'rating' => 4.8
// ];
```

### To Min
Reduces to the min value.

```Reduce::toMin(iterable $data, callable $compareBy = null): mixed|null```

- Optional callable param `$compareBy` must return comparable value.
- If `$compareBy` is not provided then items of given collection must be comparable.
- Returns null if collection is empty.

```php
use IterTools\Reduce;

$numbers = [5, 3, 1, 2, 4];

$result = Reduce::toMin($numbers);
// 1


$movieRatings = [
    [
        'title' => 'The Matrix',
        'rating' => 4.7
    ],
    [
        'title' => 'The Matrix Reloaded',
        'rating' => 4.3
    ],
    [
        'title' => 'The Matrix Revolutions',
        'rating' => 3.9
    ],
    [
        'title' => 'The Matrix Resurrections',
        'rating' => 2.5
    ],
];
$compareBy = fn ($movie) => $movie['rating'];

$lowestRatedMovie = Reduce::toMin($movieRatings, $compareBy);
// [
//     'title' => 'The Matrix Resurrections',
//     'rating' => 2.5
// ]
```

### To Min Max
Reduces to array of its upper and lower bounds (max and min).

```Reduce::toMinMax(iterable $numbers, callable $compareBy = null): array```

- Optional callable param `$compareBy` must return comparable value.
- If `$compareBy` is not provided then items of given collection must be comparable.
- Returns `[null, null]` if given collection is empty.

```php
use IterTools\Reduce;

$numbers = [1, 2, 7, -1, -2, -3];

[$min, $max] = Reduce::toMinMax($numbers);
// [-3, 7]

$reportCard = [
    [
        'subject' => 'history',
        'grade' => 90
    ],
    [
        'subject' => 'math',
        'grade' => 98
    ],
    [
        'subject' => 'science',
        'grade' => 92
    ],
    [
        'subject' => 'english',
        'grade' => 85
    ],
    [
        'subject' => 'programming',
        'grade' => 100
    ],
];
$compareBy = fn ($class) => $class['grade'];

$bestAndWorstSubject = Reduce::toMinMax($reportCard, $compareBy);
// [
//     [
//         'subject' => 'english',
//         'grade' => 85
//     ],
//     [
//         'subject' => 'programming',
//         'grade' => 100
//     ],
// ]
```

### To Nth
Reduces to value at the nth position.

```Reduce::toNth(iterable $data, int $position): mixed```

```php
use IterTools\Reduce;

$lotrMovies = ['The Fellowship of the Ring', 'The Two Towers', 'The Return of the King'];

$rotk = Reduce::toNth($lotrMovies, 2);
// 20
```

### To Product
Reduces to the product of its elements.

Returns null if collection is empty.

```Reduce::toProduct(iterable $data): number|null```

```php
use IterTools\Reduce;

$primeFactors = [5, 2, 2];

$number = Reduce::toProduct($primeFactors);
// 20
```

### To Random Value
Reduces given collection to a random value within it.

```Reduce::toRandomValue(iterable $data): mixed```

```php
use IterTools\Reduce;

$sfWakeupOptions = ['mid', 'low', 'overhead', 'throw', 'meaty'];

$wakeupOption = Reduce::toRandomValue($sfWakeupOptions);
// e.g., throw
```

### To Range
Reduces given collection to its range (difference between max and min).

```Reduce::toRange(iterable $numbers): int|float```

Returns `0` if iterable source is empty.

```php
use IterTools\Reduce;

$grades = [100, 90, 80, 85, 95];

$range = Reduce::toRange($numbers);
// 20
```

### To String
Reduces to a string joining all elements.

* Optional separator to insert between items.
* Optional prefix to prepend to the string.
* Optional suffix to append to the string.

```Reduce::toString(iterable $data, string $separator = '', string $prefix = '', string $suffix = ''): string```

```php
use IterTools\Reduce;

$words = ['IterTools', 'PHP', 'v1.0'];

$string = Reduce::toString($words);
// IterToolsPHPv1.0
$string = Reduce::toString($words, '-');
// IterTools-PHP-v1.0
$string = Reduce::toString($words, '-', 'Library: ');
// Library: IterTools-PHP-v1.0
$string = Reduce::toString($words, '-', 'Library: ', '!');
// Library: IterTools-PHP-v1.0!
```

### To Sum
Reduces to the sum of its elements.

```Reduce::toSum(iterable $data): number```

```php
use IterTools\Reduce;

$parts = [10, 20, 30];

$sum = Reduce::toSum($parts);
// 60
```

### To Value
Reduce elements to a single value using reducer function.

```Reduce::toValue(iterable $data, callable $reducer, mixed $initialValue): mixed```

```php
use IterTools\Reduce;

$input = [1, 2, 3, 4, 5];
$sum   = fn ($carry, $item) => $carry + $item;

$result = Reduce::toValue($input, $sum, 0);
// 15
```

## Stream

Streams provide a fluent interface to transform arrays and iterables through a pipeline of operations.

Streams are made up of:

1. One stream source factory method to create the stream.
2. Zero or more stream operators that transform the stream to a new stream.
3. Terminal operation of either:
   * Stream terminal operation to transform the stream to a value or data structure.
   ```php
   $result = Stream::of([1, 1, 2, 2, 3, 4, 5])
      ->distinct()                  // [1, 2, 3, 4, 5]
      ->map(fn ($x) => $x**2)       // [1, 4, 9, 16, 25]
      ->filter(fn ($x) => $x < 10)  // [1, 4, 9]
      ->toSum();                    // 14
   ```
   * The stream is iterated via a `foreach` loop.
   ```php
   $result = Stream::of([1, 1, 2, 2, 3, 4, 5])
      ->distinct()                  // [1, 2, 3, 4, 5]
      ->map(fn ($x) => $x**2)       // [1, 4, 9, 16, 25]
      ->filter(fn ($x) => $x < 10); // [1, 4, 9]

   foreach ($result as $item) {
       // 1, 4, 9
   }
   ```

### Stream Sources

#### Of
Creates stream from an iterable.

```Stream::of(iterable $iterable): Stream```

```php
use IterTools\Stream;

$iterable = [1, 2, 3];

$result = Stream::of($iterable)
    ->chainWith([4, 5, 6], [7, 8, 9])
    ->zipEqualWith([1, 2, 3, 4, 5, 6, 7, 8, 9])
    ->toValue(fn ($carry, $item) => $carry + array_sum($item));
// 90
```

#### Of Coin Flips
Creates stream of n random coin flips.

```Stream::ofCoinFlips(int $repetitions): Stream```

```php
use IterTools\Stream;

$result = Stream::ofCoinFlips(10)
    ->filterTrue()
    ->toCount();
// 5 (random)
```

#### Of CSV File
Creates a stream of rows of a CSV file.

```Stream::ofCsvFile(resource $fileHandle, string $separator = ',', string $enclosure = '"', string = $escape = '\\'): Stream```

```php
use IterTools\Stream;

$fileHandle = \fopen('path/to/file.csv', 'r');

$result = Stream::of($fileHandle)
    ->toArray();
```

#### Of Empty
Creates stream of nothing.

```Stream::ofEmpty(): Stream```

```php
use IterTools\Stream;

$result = Stream::ofEmpty()
    ->chainWith([1, 2, 3])
    ->toArray();
// 1, 2, 3
```

#### Of File Lines
Creates a stream of lines of a file.

```Stream::ofFileLines(resource $fileHandle): Stream```

```php
use IterTools\Stream;

$fileHandle = \fopen('path/to/file.txt', 'r');

$result = Stream::of($fileHandle)
    ->map('strtoupper');
    ->toArray();
```

#### Of Random Choice
Creates stream of random selections from an array of values.

```Stream::ofRandomChoice(array $items, int $repetitions): Stream```

```php
use IterTools\Stream;

$languages = ['PHP', 'Go', 'Python'];

$languages = Stream::ofRandomChoice($languages, 5)
    ->toArray();
// 'Go', 'PHP', 'Python', 'PHP', 'PHP' (random)
```

#### Of Random Numbers
Creates stream of random numbers (integers).

```Stream::ofRandomNumbers(int $min, int $max, int $repetitions): Stream```

```php
use IterTools\Stream;

$min  = 1;
$max  = 3;
$reps = 7;

$result = Stream::ofRandomNumbers($min, $max, $reps)
    ->toArray();
// 1, 2, 2, 1, 3, 2, 1 (random)
```

#### Of Random Percentage
Creates stream of random percentages between 0 and 1.

```Stream::ofRandomPercentage(int $repetitions): Stream```

```php
use IterTools\Stream;

$stream = Stream::ofRandomPercentage(3)
    ->toArray();
// 0.8012566976245, 0.81237281724151, 0.61676896329459 [random]
```

#### Of Range
Creates stream of a range of numbers.

```Stream::ofRange(int|float $start, int|float $end, int|float $step = 1): Stream```

```php
use IterTools\Stream;

$numbers = Stream::ofRange(0, 5)
    ->toArray();
// 0, 1, 2, 3, 4, 5
```

#### Of Rock Paper Scissors
Creates stream of rock-paper-scissors hands.

```Stream::ofRockPaperScissors(int $repetitions): Stream```

```php
use IterTools\Stream;

$rps = Stream::ofRockPaperScissors(5)
    ->toArray();
// 'paper', 'rock', 'rock', 'scissors', 'paper' [random]
```

### Stream Operations

#### ASort
Sorts the stream, maintaining keys.

```$stream->asort(callable $comparator = null)```

If comparator is not provided, the elements of the iterable source must be comparable.

```php
use IterTools\Stream;

$worldPopulations = [
    'China'     => 1_439_323_776,
    'India'     => 1_380_004_385,
    'Indonesia' => 273_523_615,
    'USA'       => 331_002_651,
];

$result = Stream::of($worldPopulations)
    ->filter(fn ($pop) => $pop > 300_000_000)
    ->asort()
    ->toAssociativeArray();
// USA   => 331_002_651,
// India => 1_380_004_385,
// China => 1_439_323_776,
```

#### Chain With
Return a stream chaining additional sources together into a single consecutive stream.

```$stream->chainWith(iterable ...$iterables): Stream```

```php
use IterTools\Stream;

$input = [1, 2, 3];

$result = Stream::of($input)
    ->chainWith([4, 5, 6])
    ->chainWith([7, 8, 9])
    ->toArray();
// 1, 2, 3, 4, 5, 6, 7, 8, 9
```

#### Compress
Compress to a new stream by filtering out data that is not selected.

```$stream->compress(iterable $selectors): Stream```

Selectors indicate which data. True value selects item. False value filters out data.

```php
use IterTools\Stream;

$input = [1, 2, 3];

$result = Stream::of($input)
    ->compress([0, 1, 1])
    ->toArray();
// 2, 3
```

#### Compress Associative
Compress to a new stream by filtering out keys that are not selected.

```$stream->compressAssociative(array $keys): Stream```

* Standard PHP array/iterator keys only (string, integer).

```php
use IterTools\Stream;

$starWarsEpisodes = [
    'I'    => 'The Phantom Menace',
    'II'   => 'Attack of the Clones',
    'III'  => 'Revenge of the Sith',
    'IV'   => 'A New Hope',
    'V'    => 'The Empire Strikes Back',
    'VI'   => 'Return of the Jedi',
    'VII'  => 'The Force Awakens',
    'VIII' => 'The Last Jedi',
    'IX'   => 'The Rise of Skywalker',
];
$sequelTrilogyNumbers = ['VII', 'VIII', 'IX'];

$sequelTrilogy = Stream::of($starWarsEpisodes)
    ->compressAssociative($sequelTrilogyNumbers)
    ->toAssociativeArray();
// 'VII'  => 'The Force Awakens',
// 'VIII' => 'The Last Jedi',
// 'IX'   => 'The Rise of Skywalker',
```

#### Chunkwise
Return a stream consisting of chunks of elements from the stream.

```$stream->chunkwise(int $chunkSize): Stream```

Chunk size must be at least 1.

```php
use IterTools\Stream;

$friends = ['Ross', 'Rachel', 'Chandler', 'Monica', 'Joey'];

$result = Stream::of($friends)
    ->chunkwise(2)
    ->toArray();
// ['Ross', 'Rachel'], ['Chandler', 'Monica'], ['Joey']
```

#### Chunkwise Overlap
Return a stream consisting of overlapping chunks of elements from the stream.

```$stream->chunkwiseOverlap(int $chunkSize, int $overlapSize, bool $includeIncompleteTail = true): Stream```

* Chunk size must be at least 1.
* Overlap size must be less than chunk size.

```php
use IterTools\Stream;

$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9];

$result = Stream::of($friends)
    ->chunkwiseOverlap(3, 1)
    ->toArray()
// [1, 2, 3], [3, 4, 5], [5, 6, 7], [7, 8, 9]
```

#### Distinct
Return a stream filtering out elements from the stream only returning distinct elements.

```$stream->distinct(bool $strict = true): Stream```

Defaults to [strict type](#Strict-and-Coercive-Types) comparisons. Set strict to false for type coercion comparisons.

```php
use IterTools\Stream;

$input = [1, 2, 1, 2, 3, 3, '1', '1', '2', '3'];
$stream = Stream::of($input)
    ->distinct()
    ->toArray();
// 1, 2, 3, '1', '2', '3'

$stream = Stream::of($input)
    ->distinct(false)
    ->toArray();
// 1, 2, 3
```

#### Drop While
Drop elements from the stream while the predicate function is true.

```$stream->dropWhile(callable $predicate): Stream```

Once the predicate function returns false once, all remaining elements are returned.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5]

$result = Stream::of($input)
    ->dropWhile(fn ($value) => $value < 3)
    ->toArray();
// 3, 4, 5
```

#### Filter
Filter out elements from the stream only keeping elements where there predicate function is true.

```$stream->filter(callable $predicate): Stream```

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->filter(fn ($value) => $value > 0)
    ->toArray();
// 1, 2, 3
```

#### Filter True
Filter out elements from the stream only keeping elements that are truthy.

```$stream->filterTrue(): Stream```

```php
use IterTools\Stream;

$input = [0, 1, 2, 3, 0, 4];

$result = Stream::of($input)
    ->filterTrue()
    ->toArray();
// 1, 2, 3, 4
```

#### Filter False
Filter out elements from the stream only keeping elements that are falsy.

```$stream->filterFalse(): Stream```

```php
use IterTools\Stream;

$input = [0, 1, 2, 3, 0, 4];

$result = Stream::of($input)
    ->filterFalse()
    ->toArray();
// 0, 0
```

#### Filter Keys
Filter out elements from stream only keeping elements where the predicate function on the keys are true.

```$stream->filterKeys(callable $filter): Stream```

```php
$olympics = [
    2000 => 'Sydney',
    2002 => 'Salt Lake City',
    2004 => 'Athens',
    2006 => 'Turin',
    2008 => 'Beijing',
    2010 => 'Vancouver',
    2012 => 'London',
    2014 => 'Sochi',
    2016 => 'Rio de Janeiro',
    2018 => 'Pyeongchang',
    2020 => 'Tokyo',
    2022 => 'Beijing',
];

$winterFilter = fn ($year) => $year % 4 === 2;

$result = Stream::of($olympics)
    ->filterKeys($winterFilter)
    ->toAssociativeArray();
}
// 2002 => Salt Lake City
// 2006 => Turin
// 2010 => Vancouver
// 2014 => Sochi
// 2018 => Pyeongchang
// 2022 => Beijing
```

#### Flat Map
Map a function onto the elements of the stream and flatten the results.

```$stream->flatMap(callable $mapper): Stream```

```php
$data    = [1, 2, 3, 4, 5];
$mapper  fn ($item) => ($item % 2 === 0) ? [$item, $item] : $item;

$result = Stream::of($data)
    ->flatMap($mapper)
    ->toArray();
// [1, 2, 2, 3, 4, 4, 5]
```

#### Flatten
Flatten a multidimensional stream.

```$stream->flatten(int $dimensions = 1): Stream```

```php
$data = [1, [2, 3], [4, 5]];

$result = Stream::of($data)
    ->flatten($mapper)
    ->toArray();
// [1, 2, 3, 4, 5]
```

#### Group By
Return a stream grouping by a common data element.

```$stream->groupBy(callable $groupKeyFunction, callable $itemKeyFunction = null): Stream```

* The `$groupKeyFunction` determines the key to group elements by.
* The optional `$itemKeyFunction` allows custom indexes within each group member.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$groups = Stream::of($input)
    ->groupBy(fn ($item) => $item > 0 ? 'positive' : 'negative');

foreach ($groups as $group => $item) {
    // 'positive' => [1, 2, 3], 'negative' => [-1, -2, -3]
}
```

#### Infinite Cycle
Return a stream cycling through the elements of stream sequentially forever.

```$stream->infiniteCycle(): Stream```

```php
use IterTools\Stream;

$input = [1, 2, 3];

$result = Stream::of($input)
    ->infiniteCycle()
    ->print();
// 1, 2, 3, 1, 2, 3, ...
```

#### Intersection With
Return a stream intersecting the stream with the input iterables.

```$stream->intersectionWith(iterable ...$iterables): Stream```

```php
use IterTools\Stream;

$numbers    = [1, 2, 3, 4, 5, 6, 7, 8, 9];
$numerics   = ['1', '2', 3, 4, 5, 6, 7, '8', '9'];
$oddNumbers = [1, 3, 5, 7, 9, 11];

$stream = Stream::of($numbers)
    ->intersectionWith($numerics, $oddNumbers)
    ->toArray();
// 3, 5, 7
```

#### Intersection Coercive With
Return a stream intersecting the stream with the input iterables using [type coercion](#Strict-and-Coercive-Types).

```$stream->intersectionCoerciveWith(iterable ...$iterables): Stream```

```php
use IterTools\Stream;

$languages          = ['php', 'python', 'c++', 'java', 'c#', 'javascript', 'typescript'];
$scriptLanguages    = ['php', 'python', 'javascript', 'typescript'];
$supportsInterfaces = ['php', 'java', 'c#', 'typescript'];

$stream = Stream::of($languages)
    ->intersectionCoerciveWith($scriptLanguages, $supportsInterfaces)
    ->toArray();
// 'php', 'typescript'
```

#### Limit
Return a stream up to a limit.

Stops even if more data available if limit reached.

```$stream->limit(int $limit): Stream```

```php
Use IterTools\Single;

$matrixMovies = ['The Matrix', 'The Matrix Reloaded', 'The Matrix Revolutions', 'The Matrix Resurrections'];
$limit        = 1;

$goodMovies = Stream::of($matrixMovies)
    ->limit($limit)
    ->toArray();
// 'The Matrix' (and nothing else)
```

#### Map
Return a stream containing the result of mapping a function onto each element of the stream.

```$stream->map(callable $function): Stream```

```php
use IterTools\Stream;

$grades = [100, 95, 98, 89, 100];

$result = Stream::of($grades)
    ->map(fn ($grade) => $grade === 100 ? 'A' : 'F')
    ->toArray();
// A, F, F, F, A
```

#### Pairwise
Return a stream consisting of pairs of elements from the stream.

```$stream->pairwise(): Stream```

Returns empty stream if given collection contains less than 2 elements.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$stream = Stream::of($input)
    ->pairwise()
    ->toArray();
// [1, 2], [2, 3], [3, 4], [4, 5]
```

#### Partial Intersection With
Return a stream partially intersecting the stream with the input iterables.

```$stream->partialIntersectionWith(int $minIntersectionCount, iterable ...$iterables): Stream```

```php
use IterTools\Stream;

$numbers    = [1, 2, 3, 4, 5, 6, 7, 8, 9];
$numerics   = ['1', '2', 3, 4, 5, 6, 7, '8', '9'];
$oddNumbers = [1, 3, 5, 7, 9, 11];

$stream = Stream::of($numbers)
    ->partialIntersectionWith($numerics, $oddNumbers)
    ->toArray();
// 1, 3, 4, 5, 6, 7, 9
```

#### Partial Intersection Coercive With
Return a stream partially intersecting the stream with the input iterables using [type coercion](#Strict-and-Coercive-Types).

```$stream->partialIntersectionCoerciveWith(int $minIntersectionCount, iterable ...$iterables): Stream```

```php
use IterTools\Stream;

$languages          = ['php', 'python', 'c++', 'java', 'c#', 'javascript', 'typescript'];
$scriptLanguages    = ['php', 'python', 'javascript', 'typescript'];
$supportsInterfaces = ['php', 'java', 'c#', 'typescript'];

$stream = Stream::of($languages)
    ->partialIntersectionCoerciveWith(2, $scriptLanguages, $supportsInterfaces)
    ->toArray();
// 'php', 'python', 'java', 'typescript', 'c#', 'javascript'
```

#### Reindex
Return a new stream of key-value elements reindexed by the key indexer function.

```$stream->reindex(callable $indexer): Stream```

```php
use IterTools\Single;

$data = [
    [
        'title'   => 'Star Wars: Episode IV – A New Hope',
        'episode' => 'IV',
        'year'    => 1977,
    ],
    [
        'title'   => 'Star Wars: Episode V – The Empire Strikes Back',
        'episode' => 'V',
        'year'    => 1980,
    ],
    [
        'title' => 'Star Wars: Episode VI – Return of the Jedi',
        'episode' => 'VI',
        'year' => 1983,
    ],
];
$reindexFunc = fn (array $swFilm) => $swFilm['episode'];

$reindexResult = Stream::of($data)
    ->reindex($reindexFunc)
    ->toAssociativeArray();
// [
//     'IV' => [
//         'title'   => 'Star Wars: Episode IV – A New Hope',
//         'episode' => 'IV',
//         'year'    => 1977,
//     ],
//     'V' => [
//         'title'   => 'Star Wars: Episode V – The Empire Strikes Back',
//         'episode' => 'V',
//         'year'    => 1980,
//     ],
//     'VI' => [
//         'title' => 'Star Wars: Episode VI – Return of the Jedi',
//         'episode' => 'VI',
//         'year' => 1983,
//     ],
// ]
```

#### Reverse
Reverse the elements of a stream.

```$stream->reverse(): Stream```

```php
use IterTools\Stream;

$words = ['are', 'you', 'as' ,'bored', 'as', 'I', 'am'];

$reversed = Stream::of($words)
    ->reverse()
    ->toString(' ');
// am I as bored as you are
```

#### Running Average
Return a stream accumulating the running average (mean) over the stream.

```$stream->runningAverage(int|float|null $initialValue = null): Stream```

```php
use IterTools\Stream;

$input = [1, 3, 5];

$result = Stream::of($input)
    ->runningAverage()
    ->toArray();
// 1, 2, 3
```

#### Running Difference
Return a stream accumulating the running difference over the stream.

```$stream->runningDifference(int|float|null $initialValue = null): Stream```

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->runningDifference()
    ->toArray();
// -1, -3, -6, -10, -15
```

#### Running Max
Return a stream accumulating the running max over the stream.

```$stream->runningMax(int|float|null $initialValue = null): Stream```

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->runningMax()
    ->toArray();
// 1, 1, 2, 2, 3, 3

```

#### Running Min
Return a stream accumulating the running min over the stream.

```$stream->runningMin(int|float|null $initialValue = null): Stream```

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->runningMin()
    ->toArray();
// 1, -1, -1, -2, -2, -3
```

#### Running Product
Return a stream accumulating the running product over the stream.

```$stream->runningProduct(int|float|null $initialValue = null): Stream```

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->runningProduct()
    ->toArray();
// 1, 2, 6, 24, 120

```

#### Running Total
Return a stream accumulating the running total over the stream.

```$stream->runningTotal(int|float|null $initialValue = null): Stream```

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($input)
    ->runningTotal()
    ->toArray();
// 1, 3, 6, 10, 15
```

#### Skip
Skip some elements of the stream.

```$stream->slice(int $start = 0, int $count = null, int $step = 1)```

```php
use IterTools\Stream;

$movies = [
    'The Phantom Menace', 'Attack of the Clones', 'Revenge of the Sith',
    'A New Hope', 'The Empire Strikes Back', 'Return of the Jedi',
    'The Force Awakens', 'The Last Jedi', 'The Rise of Skywalker'
];

$onlyTheBest = Stream::of($movies)
    ->skip(3)
    ->skip(3, 3)
    ->toArray();
// 'A New Hope', 'The Empire Strikes Back', 'Return of the Jedi'
```

#### Slice
Extract a slice of the stream.

```$stream->slice(int $start = 0, int $count = null, int $step = 1)```

```php
use IterTools\Stream;

$olympics = [1992, 1994, 1996, 1998, 2000, 2002, 2004, 2006, 2008, 2010, 2012, 2014, 2016, 2018, 2020, 2022];

$summerOlympics = Stream::of($olympics)
    ->slice(0, 8, 2)
    ->toArray();
// [1992, 1996, 2000, 2004, 2008, 2012, 2016, 2020]
```

#### Sort
Sorts the stream.

```$stream->sort(callable $comparator = null)```

If comparator is not provided, the elements of the iterable source must be comparable.

```php
use IterTools\Stream;

$input = [3, 4, 5, 9, 8, 7, 1, 6, 2];

$result = Stream::of($input)
    ->sort()
    ->toArray();
// 1, 2, 3, 4, 5, 6, 7, 8, 9
```

#### Symmetric difference With
Return a stream of the symmetric difference of the stream and the given iterables.

```$stream->symmetricDifferenceWith(iterable ...$iterables): Stream```

Note: If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) intersection rules apply.

```php
use IterTools\Stream;

$a = [1, 2, 3, 4, 7];
$b = ['1', 2, 3, 5, 8];
$c = [1, 2, 3, 6, 9];

$stream = Stream::of($a)
    ->symmetricDifferenceWith($b, $c)
    ->toArray();
// '1', 4, 5, 6, 7, 8, 9
```

#### Symmetric difference Coercive With
Return a stream of the symmetric difference of the stream and the given iterables using [type coercion](#Strict-and-Coercive-Types).

```$stream->symmetricDifferenceCoerciveWith(iterable ...$iterables): Stream```

Note: If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) intersection rules apply.

```php
use IterTools\Stream;

$a = [1, 2, 3, 4, 7];
$b = ['1', 2, 3, 5, 8];
$c = [1, 2, 3, 6, 9];

$stream = Stream::of($a)
    ->symmetricDifferenceCoerciveWith($b, $c)
    ->toArray();
// 4, 5, 6, 7, 8, 9
```

#### Take While
Keep elements from the stream as long as the predicate is true.

```$stream->takeWhile(callable $predicate): Stream```

If no predicate is provided, the boolean value of the data is used.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($input)
    ->takeWhile(fn ($value) => abs($value) < 3)
    ->toArray();
// 1, -1, 2, -2
```

#### Union With
Return a stream consisting of the union of the stream and the input iterables.

```$stream->unionWith(iterable ...$iterables): Stream```

Note: If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) union rules apply.

```php
use IterTools\Stream;

$input = [1, 2, 3];

$stream = Stream::of($input)
    ->unionWith([3, 4, 5, 6])
    ->toArray();
// [1, 2, 3, 4, 5, 6]
```

#### Union Coercive With
Return a stream consisting of the union of the stream and the input iterables using [type coercion](#Strict-and-Coercive-Types).

```$stream->unionCoerciveWith(iterable ...$iterables): Stream```

Note: If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) union rules apply.

```php
use IterTools\Stream;

$input = [1, 2, 3];

$stream = Stream::of($input)
    ->unionCoerciveWith(['3', 4, 5, 6])
    ->toArray();
// [1, 2, 3, 4, 5, 6]
```

#### Zip With
Return a stream consisting of multiple iterable collections streamed simultaneously.

```$stream->zipWith(iterable ...$iterables): Stream```

For uneven lengths, iterations stops when the shortest iterable is exhausted.

```php
use IterTools\Stream;

$input = [1, 2, 3];

$stream = Stream::of($input)
    ->zipWith([4, 5, 6])
    ->zipWith([7, 8, 9])
    ->toArray();
// [1, 4, 7], [2, 5, 8], [3, 6, 9]
```

#### Zip Longest With
Return a stream consisting of multiple iterable collections streamed simultaneously.

```$stream->zipLongestWith(iterable ...$iterables): Stream```

* Iteration continues until the longest iterable is exhausted.
* For uneven lengths, the exhausted iterables will produce null for the remaining iterations.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$stream = Stream::of($input)
    ->zipLongestWith([4, 5, 6])
    ->zipLongestWith([7, 8, 9, 10]);

foreach ($stream as $zipped) {
    // [1, 4, 7], [2, 5, 8], [3, 6, 9], [4, null, 10], [null, null, 5]
}
```

#### Zip Equal With
Return a stream consisting of multiple iterable collectionso f equal lengths streamed simultaneously.

```$stream->zipEqualWith(iterable ...$iterables): Stream```

Works like `Stream::zipWith()` method but throws \LengthException if lengths not equal,
i.e., at least one iterator ends before the others.

```php
use IterTools\Stream;

$input = [1, 2, 3];

$stream = Stream::of($input)
    ->zipEqualWith([4, 5, 6])
    ->zipEqualWith([7, 8, 9]);

foreach ($stream as $zipped) {
    // [1, 4, 7], [2, 5, 8], [3, 6, 9]
}
```

### Stream Terminal Operations

#### Stream Summary Terminal Operations
##### All Match
Returns true if all elements match the predicate function.

```$stream->allMatch(callable $predicate): bool```

```php
use IterTools\Summary;

$finalFantasyNumbers = [4, 5, 6];
$isOnSuperNintendo   = fn ($ff) => $ff >= 4 && $ff <= 6;

$boolean = Stream::of($finalFantasyNumbers)
    ->allMatch($isOnSuperNintendo);
// true
```

##### All Unique
Returns true if all elements are unique.

```$stream->allUnique(bool $strict = true): bool```

Defaults to [strict type](#Strict-and-Coercive-Types) comparisons. Set strict to false for type coercion comparisons.

```php
use IterTools\Summary;

$items = ['fingerprints', 'snowflakes', 'eyes', 'DNA']

$boolean = Stream::of($items)
    ->allUnique();
// true
```

##### Any Match
Returns true if any element matches the predicate function.

```$stream->anyMatch(callable $predicate): bool```

```php
use IterTools\Summary;

$answers          = ['fish', 'towel', 42, "don't panic"];
$isUltimateAnswer = fn ($a) => a == 42;

$boolean = Stream::of($answers)
    ->anyMatch($answers, $isUltimateAnswer);
// true
```

##### Are Permutations With
Returns true if all iterables are permutations with stream.

```$stream->arePermutationsWith(...$iterables): bool```

```php
use IterTools\Summary;

$rite = ['r', 'i', 't', 'e'];
$reit = ['r', 'e', 'i', 't'];
$tier = ['t', 'i', 'e', 'r'];
$tire = ['t', 'i', 'r', 'e'];
$trie = ['t', 'r', 'i', 'e'];

$boolean = Stream::of(['i', 't', 'e', 'r'])
    ->arePermutationsWith($rite, $reit, $tier, $tire, $trie);
// true
```

##### Are Permutations Coercive With
Returns true if all iterables are permutations with stream with [type coercion](#Strict-and-Coercive-Types).

```$stream->arePermutationsCoerciveWith(...$iterables): bool```

```php
use IterTools\Summary;

$set2 = [2.0, '1', 3];
$set3 = [3, 2, 1];

$boolean = Stream::of([1, 2.0, '3'])
    ->arePermutationsCoerciveWith($set2, $set3);
// true
```

##### Exactly N
Returns true if exactly n items are true according to a predicate function.

- Predicate is optional.
- Default predicate is boolean value of each item.

```$stream->exactlyN(int $n, callable $predicate = null): bool```

```php
use IterTools\Summary;

$twoTruthsAndALie = [true, true, false];
$n                = 2;

$boolean = Stream::of($twoTruthsAndALie)->exactlyN($n);
// true
```

##### Is Empty
Returns true if the stream is empty having no items.

```$stream->isEmpty(): bool```

```php
use IterTools\Summary;

$numbers    = [0, 1, 2, 3, 4, 5];
$filterFunc = fn ($x) => $x > 10;

$boolean = Stream::($numbers)
    ->filter($filterFunc)
    ->isEmpty();
// true
```

##### Is Partitioned
Returns true if all elements of given collection that satisfy the predicate appear before all elements that don't.

- Returns true for empty collection or for collection with single item.
- Default predicate if not provided is the boolean value of each data item.

```$stream->isPartitioned(callable $predicate = null): bool```

```php
use IterTools\Summary;

$numbers          = [0, 2, 4, 1, 3, 5];
$evensBeforeOdds = fn ($item) => $item % 2 === 0;

$boolean = Stream::($numbers)
    ->isPartitioned($evensBeforeOdds);
// true
```

##### Is Sorted
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

##### Is Reversed
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

##### None Match
Returns true if no element matches the predicate function.

```$stream->noneMatch(callable $predicate): bool```

```php
use IterTools\Summary;

$grades         = [45, 50, 61, 0];
$isPassingGrade = fn ($grade) => $grade >= 70;

$boolean = Stream::of($grades)->noneMatch($isPassingGrade);
// true
```

##### Same With
Returns true if iterable source and all given collections are the same.

```$stream->sameWith(iterable ...$iterables): bool```

For empty iterables list returns true.

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

##### Same Count With
Returns true if iterable source and all given collections have the same lengths.

```$stream->sameCountWith(iterable ...$iterables): bool```

For empty iterables list returns true.

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

#### Stream Reduction Terminal Operations

##### To Average
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

##### To Count
Reduces iterable source to its length.

```$stream->toCount(): mixed```

```php
use IterTools\Stream;

$input = [10, 20, 30, 40, 50];

$result = Stream::of($iterable)
    ->toCount();
// 5
```

##### To First
Reduces iterable source to its first element.

```$stream->toFirst(): mixed```

Throws `\LengthException` if iterable source is empty.

```php
use IterTools\Stream;

$input = [10, 20, 30];

$result = Stream::of($input)
    ->toFirst();
// 10
```

##### To First And Last
Reduces iterable source to its first and last elements.

```$stream->toFirstAndLast(): array{mixed, mixed}```

Throws `\LengthException` if iterable source is empty.

```php
use IterTools\Stream;

$input = [10, 20, 30];

$result = Stream::of($input)
    ->toFirstAndLast();
// [10, 30]
```

##### To Last
Reduces iterable source to its last element.

```$stream->toLast(): mixed```

Throws `\LengthException` if iterable source is empty.

```php
use IterTools\Stream;

$input = [10, 20, 30];

$result = Stream::of($input)
    ->toLast();
// 30
```

##### To Max
Reduces iterable source to its max value.

```$stream->toMax(callable $compareBy = null): mixed```

- Optional callable param `$compareBy` must return comparable value.
- If `$compareBy` is not provided then items of given collection must be comparable.
- Returns null if collection is empty.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($iterable)
    ->toMax();
// 3
```

##### To Min
Reduces iterable source to its min value.

```$stream->toMin(callable $compareBy = null): mixed```

- Optional callable param `$compareBy` must return comparable value.
- If `$compareBy` is not provided then items of given collection must be comparable.
- Returns null if collection is empty.

```php
use IterTools\Stream;

$input = [1, -1, 2, -2, 3, -3];

$result = Stream::of($iterable)
    ->toMin();
// -3
```

##### To Min Max
Reduces stream to array of its upper and lower bounds (max and min).

```$stream->toMinMax(callable $compareBy = null): array```

- Optional callable param `$compareBy` must return comparable value.
- If `$compareBy` is not provided then items of given collection must be comparable.
- Returns `[null, null]` if given collection is empty.

```php
use IterTools\Stream;

$numbers = [1, 2, 7, -1, -2, -3];

[$min, $max] = Stream::of($numbers)
    ->toMinMax();
// [-3, 7]
```

##### To Nth
Reduces stream to value at the nth position.

```$stream->toNth(int $position): mixed```

Returns null if iterable source is empty.

```php
use IterTools\Stream;

$lotrMovies = ['The Fellowship of the Ring', 'The Two Towers', 'The Return of the King'];

$result = Stream::of($lotrMovies)
    ->toNth(2);
// The Return of the King
```

##### To Product
Reduces stream to the product of its items.

```$stream->toProduct(): mixed```

Returns null if iterable source is empty.

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($iterable)
    ->toProduct();
// 120
```

##### To Random Value
Reduces stream to a random value within it.

```$stream->toRandomValue(): mixed```

```php
use IterTools\Stream;

$rpsHands = ['rock', 'paper', 'scissors']

$range = Stream::of($numbers)
    ->map('strtoupper')
    ->toRandomValue();
// e.g., rock
```

##### To Range
Reduces stream to its range (difference between max and min).

```$stream->toRange(): int|float```

Returns `0` if iterable source is empty.

```php
use IterTools\Stream;

$grades = [100, 90, 80, 85, 95];

$range = Stream::of($numbers)
    ->toRange();
// 20
```

##### To String
Reduces to a string joining all elements.

* Optional separator to insert between items.
* Optional prefix to prepend to the string.
* Optional suffix to append to the string.

```$stream->toString(string $separator = '', string $prefix = '', string $suffix = ''): string```

```php
use IterTools\Stream;

$words = ['IterTools', 'PHP', 'v1.0'];

$string = Stream::of($words)->toString($words);
// IterToolsPHPv1.0
$string = Stream::of($words)->toString($words, '-');
// IterTools-PHP-v1.0
$string = Stream::of($words)->toString($words, '-', 'Library: ');
// Library: IterTools-PHP-v1.0
$string = Stream::of($words)->toString($words, '-', 'Library: ', '!');
// Library: IterTools-PHP-v1.0!
```

##### To Sum
Reduces iterable source to the sum of its items.

```$stream->toSum(): mixed```

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($iterable)
    ->toSum();
// 15
```

##### To Value
Reduces iterable source like array_reduce() function.

But unlike `array_reduce()`, it works with all `iterable` types.

```$stream->toValue(callable $reducer, mixed $initialValue): mixed```

```php
use IterTools\Stream;

$input = [1, 2, 3, 4, 5];

$result = Stream::of($iterable)
    ->toValue(fn ($carry, $item) => $carry + $item);
// 15
```

#### Transformation Terminal Operations

##### To Array
Returns an array of stream elements.

```$stream->toArray(): array```

```php
use IterTools\Stream;

$array = Stream::of([1, 1, 2, 2, 3, 4, 5])
    ->distinct()
    ->map(fn ($x) => $x**2)
    ->toArray();
// [1, 4, 9, 16, 25]
```

##### To Associative Array
Returns a key-value map of stream elements.

```$stream->toAssociativeArray(callable $keyFunc, callable $valueFunc): array```

```php
use IterTools\Stream;

$keyFunc

$array = Stream::of(['message 1', 'message 2', 'message 3'])
    ->map('strtoupper')
    ->toAssociativeArray(
        fn ($s) => \md5($s),
        fn ($s) => $s
    );
// [3b3f2272b3b904d342b2d0df2bf31ed4 => MESSAGE 1, 43638d919cfb8ea31979880f1a2bb146 => MESSAGE 2, ... ]
```

##### Tee
Return several independent (duplicated) streams.

```$stream->tee(int $count): array```

```php
use IterTools\Transform;

$daysOfWeek = ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun'];
$count = 3;

[$week1Stream, $week2Stream, $week3Stream] = Stream::of($daysOfWeek)
    ->tee($count);

// Each $weekStream contains ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun']
```

#### Side Effect Terminal Operations

##### Call For Each
Perform an action via a callable on each item in the stream.

```$stream->callForEach(callable $function): void```

```php
use IterTools\Stream;

$languages = ['PHP', 'Python', 'Java', 'Go'];
$mascots   = ['elephant', 'snake', 'bean', 'gopher'];

$zipPrinter = fn ($zipped) => print("{$zipped[0]}'s mascot: {$zipped[1]}");

Stream::of($languages)
    ->zipWith($mascots)
    ->callForEach($zipPrinter);
// PHP's mascot: elephant
// Python's mascot: snake
// ...
```

##### Print
Prints each item in the stream.

* Items must be printable.

```$stream->print(string $separator = '', string $prefix = '', string $suffix = ''): void```

```php
use IterTools\Stream;

$words = ['IterTools', 'PHP', 'v1.0'];

Stream::of($words)->print();                       // IterToolsPHPv1.0
Stream::of($words)->print('-');                    // IterTools-PHP-v1.0
Stream::of($words)->print('-', 'Library: ');       // Library: IterTools-PHP-v1.0
Stream::of($words)->print('-', 'Library: ', '!');  // Library: IterTools-PHP-v1.0!
```

##### Print Line
Prints each item in the stream on its own line.

* Items must be printable.

```$stream->println(): void```

```php
use IterTools\Stream;

$words = ['IterTools', 'PHP', 'v1.0'];

Stream::of($words)->printLn();
// IterTools
// PHP
// v1.0
```

##### Print R
`print_r` each item in the stream.

```$stream->printR(): void```

```php
use IterTools\Stream;

$items = [$string, $array, $object];

Stream::of($words)->printR();
// print_r output
```

##### To CSV File
Write the contents of the stream to a CSV file.

```$stream->toCsvFile(resource $fileHandle, array $header = null, string 'separator = ',', string $enclosure = '"', string $escape = '\\'): void```

```php
use IterTools\Stream;

$starWarsMovies = [
    ['Star Wars: Episode IV – A New Hope', 'IV', 1977],
    ['Star Wars: Episode V – The Empire Strikes Back', 'V', 1980],
    ['Star Wars: Episode VI – Return of the Jedi', 'VI', 1983],
];
$header = ['title', 'episode', 'year'];

Stream::of($data)
    ->toCsvFile($fh, $header);
// title,episode,year
// "Star Wars: Episode IV – A New Hope",IV,1977
// "Star Wars: Episode V – The Empire Strikes Back",V,1980
// "Star Wars: Episode VI – Return of the Jedi",VI,1983
```

##### To File
Write the contents of the stream to a file.

```$stream->toFile(resource $fileHandle, string $newLineSeparator = \PHP_EOL, string $header = null, string $footer = null): void```

```php
use IterTools\Stream;

$data = ['item1', 'item2', 'item3'];
$header = '<ul>';
$footer = '</ul>';

Stream::of($data)
    ->map(fn ($item) => "  <li>$item</li>")
    ->toFile($fh, \PHP_EOL, $header, $footer);

// <ul>
//   <li>item1</li>
//   <li>item2</li>
//   <li>item3</li>
// </ul>
```

##### Var Dump
`var_dump` each item in the stream.

```$stream->varDump(): void```

```php
use IterTools\Stream;

$items = [$string, $array, $object];

Stream::of($words)->varDump();
// var_dump output
```

### Stream Terminal Operations

#### Peek
Peek at each element between other Stream operations to do some action without modifying the stream.

```$stream->peek(callable $callback): void```

```php
use IterTools\Stream;

$logger = new SimpleLog\Logger('/tmp/log.txt', 'iterTools');

Stream::of(['some', 'items'])
  ->map('strtoupper')
  ->peek(fn ($x) => $logger->info($x))
  ->foreach($someComplexCallable);
```

#### Peek Stream
Peek at the entire stream between other Stream operations to do some action without modifying the stream.

```$stream->peekStream(callable $callback): void```

```php
use IterTools\Stream;

$logger = new SimpleLog\Logger('/tmp/log.txt', 'iterTools');

Stream::of(['some', 'items'])
  ->map('strtoupper')
  ->peekStream(fn ($stream) => $logger->info($stream))
  ->foreach($someComplexCallable);
```

#### Peek Print
Peek at each element between other Stream operations to print each item without modifying the stream.

```$stream->peek(callable $callback): void```

```php
use IterTools\Stream;

Stream::of(['some', 'items'])
  ->map('strtoupper')
  ->peekPrint()
  ->foreach($someComplexCallable);
```

#### Peek PrintR
Peek at each element between other Stream operations to `print_r` each item without modifying the stream.

```$stream->peekPrintR(callable $callback): void```

```php
use IterTools\Stream;

Stream::of(['some', 'items'])
  ->map('strtoupper')
  ->peekPrintR()
  ->foreach($someComplexCallable);
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

## Strict and Coercive Types

When there is an option, the default will do strict type comparisons:

* scalars: compares strictly by type
* objects: always treats different instances as not equal to each other
* arrays: compares serialized

When type coercion (non-strict types) is available and enabled via optional flag:

* scalars: compares by value via type juggling
* objects: compares serialized
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

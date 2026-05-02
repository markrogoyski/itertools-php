# IterTools PHP Change Log

## v2.3.0 - 2026-05-02

### New Features
* Combinatorics
  * `powerset`
* Infinite
  * `iterate`
* Multi
  * `roundRobin`
  * `unzip`
* Reduce
  * `toFirstMatchIndex`
  * `toFirstMatchKey`
* Set
  * `distinctAdjacent`
  * `distinctAdjacentBy`
* Single
  * `intersperse`
  * `mapSpread`
* Sort
  * `sortBy`
  * `asortBy`
  * `largest`
  * `smallest`
* Stream
  * `asortBy`
  * `distinctAdjacent`
  * `distinctAdjacentBy`
  * `intersperse`
  * `largest`
  * `mapSpread`
  * `powerset`
  * `roundRobinWith`
  * `smallest`
  * `sortBy`
  * `toFirstMatchIndex`
  * `toFirstMatchKey`
  * `unzip`

### Improvements
* Reorganized translated docs

## v2.2.0 - 2026-04-19

### New Features
* Combinatorics (new)
  * `product`
  * `permutations`
  * `combinations`
  * `combinationsWithReplacement`
* Summary
  * `contains`
  * `containsCoercive`
* Reduce
  * `toFirstMatch`
* Single
  * `enumerate`
  * `accumulate`
* Transform
  * `partition`
* Stream
  * `accumulate`
  * `combinations`
  * `combinationsWithReplacement`
  * `contains`
  * `containsCoercive`
  * `enumerate`
  * `permutations`
  * `productWith`
  * `toFirstMatch`
  * `toPartition`
  * `zip`
  * `zipLongest`
  * `zipFilled`
  * `zipEqual`

### Improvements
* Fix NaN handling in `Math::runningMax` and `Math::runningMin` to preserve monotonicity (skip NaN values and carry forward accumulator; yield NaN when no prior value exists)
* Fix `Random::percentage` to always yield float (previously yielded int(0) when underlying RNG returned 0)
* Upgrade Psalm from v6 to v7 for PHP 8.5 compatibility

## v2.1.0 - 2026-03-29

# Improvements
* Fix `cycle` crash with generator-returning IteratorAggregate and null handling in min/max reductions
* Fix `allMatch`/`anyMatch`/`noneMatch` to use boolean coercion instead of strict comparison
* Fix `Sort::sort` dropping elements when iterable has duplicate keys
* Fix `Sort::asort` dropping elements when iterable has duplicate keys
* Fix `Summary::isEmpty` to rewind non-Generator iterators before checking.
* Fix NaN handling in `Summary::isSorted` and `isReversed`
* Skip NaN values in `Reduce::toMin/toMax/toMinMax` to prevent incorrect results
* Throw InvalidArgumentException for non-serializable objects in coercive comparison mode
* Throw InvalidArgumentException for non-positive count in `Transform::tee`
* Throw InvalidArgumentException for step=0 in `Infinite::count`
* Throw InvalidArgumentException for negative position in `Reduce::toNth`
* Throw InvalidArgumentException for negative overlap in `Single::chunkwiseOverlap`

## v2.0.0 - 2026-03-07

### Breaking Changes
* Minimum PHP version updated from 7.4 to 8.2
* Updated PHPUnit from ^9.0 to ^10.0

### New Features
* Set
  * `difference`
  * `differenceCoercive`
* Stream
  * `differenceWith`
  * `differenceCoerciveWith`

### Improvements
* Added native `mixed` and union type hints throughout the codebase
* Removed `#[\ReturnTypeWillChange]` attributes (replaced with proper return types)
* Updated CI to test PHP 8.2, 8.3, 8.4

## v1.9.0 - 2024-02-23

### Improvements
* Improvements for PHP 8.4 compatibility

## v1.8.0 - 2023-09-09

### New Features
* Set
  * `distinctBy`
* Stream
  * `distinctBy`

## v.1.7.0 - 2023-06-14

### New Features
* Math
  * `frequencies`
  * `relativeFrequencies`

### Improvements
* Internal improvements for static analysis

## v1.6.0 - 2023-04-16

### New Features
* Multi
  * `zipFilled`
* Reduce
  * `toNth`
* Stream
  * `toNth`
  * `zipFilledWith`

## v1.5.0 - 2023-03-19

### New Features
* Reduce
  * `toRandomValue`
* Set
  * `union`
  * `unionCoercive`
* Single
  * `skip`
* Summary
  * `allUnique`
  * `isEmpty`
* Stream
  * Stream Operations
    * `skip`
    * `unionWith`
    * `unionCoerciveWith`
  * Debug Operations
    * `peek`
    * `peekPrint`
    * `peekPrintR`
    * `peekStream`
  * Reduction Terminal Operations
    * `toRandomValue`
  * Summary Terminal Operations
    * `allUnique`
    * `isEmpty`
### Bug Fixes
* `Summary::allMatch` now returns true on empty iterables, as was documented.

## v1.4.0 - 2023-02-15

### New Features
* Single
  * `flatMap`
  * `flatten`
  * `reverse`
  * `slice`
* Summary
  * `arePermutations`
  * `arePermutationsCoercive`
  * `isPartitioned`
* Transform
  * `tee`
  * `toArray`
  * `toAssociativeArray`
  * `toIterator`
* Stream
  * Stream Operations
    * `flatMap`
    * `flatten`
    * `reverse`
    * `slice`
  * Summary Terminal Operations
    * `arePermutationsWith`
    * `arePermutationsCoerciveWith`
    * `isPartitioned`
  * Transformation
    * `tee`
    * `toArray`
    * `toAssociativeArray`
    * `toIterator`
### Improvements
* `Single::groupBy`
  * Allows the original grouping function to further separate into groups if the result of the grouping function is a list.
  * A new parameter added to take a function to index the values within each group.

## v1.3.0 - 2023-02-11

### New Features
* Single
  * `compressAssociative`
  * `filter`
  * `reindex`
  * `filterKeys`
* Reduce
  * `toFirst`
  * `toLast`
  * `toFirstAndLast`
  * `toMin` (parameter `$compareBy` added)
  * `toMax` (parameter `$compareBy` added)
  * `toMinMax` (parameter `$compareBy` added)
* Sort
  * `asort`
  * `sort`
* Stream
  * Source
    * `ofRange`
    * `ofFileLines`
    * `ofCsvFile`
  * Stream Operations
    * `asort`
    * `compressAssociative`
    * `filter`
    * `reindex`
    * `filterKeys`
    * `sort`
  * Reduction Terminal Operations
    * `toFirst`
    * `toLast`
    * `toFirstAndLast`
    * `toMin` (parameter `$compareBy` added)
    * `toMax` (parameter `$compareBy` added)
    * `toMinMax` (parameter `$compareBy` added)
  * Transformation Terminal Operations
    * `toAssociativeArray`
  * File Terminal Operations
    * `toFile`
    * `toCsvFile`
* Docs
  * Added Russian translation of README
### Improvements
* Add option whether to include incomplete chunks at the end of a `chunkwiseOverlap`
* Keys preserved during iteration when it makes sense to do so

## v1.2.0 - 2023-01-28

### New Features
* Single
  * `chunkwise`
  * `chunkwiseOverlap`
  * `limit`
  * `map`
* Reduce
  * `toMinMax`
  * `toRange`
  * `toString`
* Set
  * `distinct`
  * `intersection`
  * `intersectionCoercive`
  * `partialIntersection`
  * `partialIntersectionCoercive`
  * `symmetricDifference`
  * `symmetricDifferenceCoercive`
* Summary
  * `allMatch`
  * `anyMatch`
  * `exactlyN`
  * `noneMatch`
* Stream
  * Sources
    * `of`
    * `ofCoinFlips`
    * `of Empty`
    * `ofRandomChoice`
    * `ofRandomNumbers`
    * `ofRandomPercentage`
    * `ofRockPaperScissors`
  * Operations
    * `chainWith`
    * `compress`
    * `chunkwise`
    * `chunkwiseOverlap`
    * `distinct`
    * `dropWhile`
    * `filterTrue`
    * `filterFalse`
    * `groupBy`
    * `infiniteCycle`
    * `intersectionWith`
    * `intersectionCoerciveWith`
    * `limit`
    * `map`
    * `pairwise`
    * `partialIntersectionWith`
    * `partialIntersectionCoerciveWith`
    * `runningAverage`
    * `runningDifference`
    * `runningMax`
    * `runningMin`
    * `runningProduct`
    * `runningTotal`
    * `symmetricDifferenceWith`
    * `symmetricDifferenceCoerciveWith`
    * `takeWhile`
    * `zipWith`
    * `zipLongestWith`
    * `zipEqualWith`
  * Summary Terminal Operations
    * `allMatch`
    * `anyMatch`
    * `exactlyN`
    * `isSorted`
    * `isReversed`
    * `noneMatch`
    * `sameWith`
    * `sameCountWith`
  * Reduction Terminal Operations
    * `toAverage`
    * `toCount`
    * `toMax`
    * `toMin`
    * `toProduct`
    * `toString`
    * `toSum`
    * `toMinMax`
    * `toRange`
    * `toValue`
  * Transformation Terminal Operations
    * `toArray`
  * Side Effect Terminal Operations
    * `callForEach`
    * `print`
    * `printLn`
    * `printR`
    * `varDump`
### Backwards Incompatible Changes
* Multi Zip methods reset iteration keys and return as sequence of integers rather than arrays of original keys


## v1.1.0 - 2023-01-10

### New Features
* Multi
  * `zipEqual`
* Single
  * `pairwise`
* Summary
  * `isSorted`
  * `isReversed`
  * `same`
  * `sameCount`
* Reduce
  * `toAverage`
  * `toCount`
  * `toMax`
  * `toMin`
  * `toProduct`
  * `toSum`
  * `toValue`

## v1.0.0 - 2022-04-25

Initial release.

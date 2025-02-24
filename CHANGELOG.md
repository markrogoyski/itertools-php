# IterTools PHP Change Log

## v1.9.0 - 2024-02-023

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

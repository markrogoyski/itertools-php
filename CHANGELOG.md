# IterTools PHP Change Log

## [Unreleased]

### New Features
* Single
  * `range` — lazy finite arithmetic progression
  * `mapWithKeys` — key-aware map; callback receives `($value, $key)`, keys preserved
  * `filterWithKeys` — key-aware filter; predicate receives `($value, $key)`, keys preserved
  * `flatMapWithKeys` — key-aware flat map; callback receives `($value, $key, $self)`, result flattened by one level with auto-generated numeric keys
  * `takeLast` — iterate the last N elements; lazy-but-bounded ring buffer, keys preserved
  * `dropLast` — iterate all elements except the last N; single-pass queue, keys preserved
  * `windowed` — sliding windows of size N advancing by a step; supports gapped windows (`step > size`) and an optional trailing partial window
  * `withFirst` — pair each element with an is-first boolean flag; lazy, O(1) memory
  * `withLast` — pair each element with an is-last boolean flag; single-element lookahead, lazy, O(1) memory
  * `withFirstAndLast` — pair each element with is-first/is-last boolean flags ("mark ends"); single-element lookahead, lazy, O(1) memory
* Random
  * `reservoirSample` — single-pass uniform random sample (Algorithm R); does not materialize the input; returns the whole input in original order when `size >= count`
* Math
  * `frequenciesBy` — frequency distribution grouped by a key function; key function must return `int|string`
  * `relativeFrequenciesBy` — relative frequency distribution grouped by a key function; key function must return `int|string`
* Summary
  * `isSortedBy` — true if values projected by a key function are non-decreasing
  * `isReversedBy` — true if values projected by a key function are non-increasing
* Reduce
  * `toCountBy` — reduce to an array of counts keyed by a key function; key function must return `int|string`
  * `toMedian` — reduce to the median value; mean of the two middle values for even-length input, computed without overflowing when those values sum beyond `PHP_FLOAT_MAX` and without losing precision when their span exceeds the integer range; two identical middle values return that value, including `INF`
  * `toMode` — reduce to a list of the most frequent values (all modes, in first-seen order)
  * `toVariance` — reduce to the population (default) or sample variance; a scaled online algorithm with a compensated running mean, giving a single pass in `O(1)` memory that never materializes the input, is order-stable to within floating-point rounding, and stays finite whenever the variance is representable even when intermediate quantities are not. A non-finite value anywhere in the input yields `NAN`, except where the `null` cases (empty collection, sample variance of a single value) apply — those take precedence
  * `toStandardDeviation` — reduce to the population (default) or sample standard deviation; inherits `toVariance`'s single-pass, `O(1)`-memory, order-stability and overflow behavior
  * `toPercentile` — reduce to the value at a percentile `[0, 100]` (R-7 / linear interpolation); interpolation does not overflow when the two neighbouring values span more than `PHP_FLOAT_MAX`, and percentile `50` returns exactly what `toMedian` returns
  * `toQuantile` — reduce to the value at a quantile `[0, 1]`
* File
  * `writeLines` — write an iterable of lines to a file resource; separator inserted between lines, no trailing separator
  * `writeCsv` — write an iterable of rows to a file resource as CSV, with optional header row
  * `readCsvAssoc` — iterate CSV rows as associative arrays keyed by header (inferred from the first row or supplied explicitly); validates headers and row-length consistency. Rows are `array<int|string, string|null>`: headers are strings, but PHP coerces canonical numeric-string headers (`"1"`, `"2020"`) to integer array keys
* Stream
  * `mapWithKeys` — fluent key-aware map
  * `filterWithKeys` — fluent key-aware filter
  * `flatMapWithKeys` — fluent key-aware flat map
  * `takeLast` — fluent iterate the last N elements
  * `dropLast` — fluent iterate all elements except the last N
  * `frequenciesBy` — fluent frequency distribution grouped by a key function
  * `relativeFrequenciesBy` — fluent relative frequency distribution grouped by a key function
  * `isSortedBy` — terminal; true if values projected by a key function are non-decreasing
  * `isReversedBy` — terminal; true if values projected by a key function are non-increasing
  * `toCountBy` — terminal; reduce to an array of counts keyed by a key function
  * `toMedian` — terminal; reduce to the median value
  * `toMode` — terminal; reduce to a list of the most frequent values
  * `toVariance` — terminal; reduce to the population or sample variance
  * `toStandardDeviation` — terminal; reduce to the population or sample standard deviation
  * `toPercentile` — terminal; reduce to the value at a percentile `[0, 100]`
  * `toQuantile` — terminal; reduce to the value at a quantile `[0, 1]`
  * `ofCsvFileAssoc` — source; stream a CSV file as associative arrays keyed by header
  * `windowed` — fluent sliding windows of elements
  * `withFirst` — fluent pair each element with an is-first flag
  * `withLast` — fluent pair each element with an is-last flag
  * `withFirstAndLast` — fluent pair each element with is-first/is-last flags
  * `reservoirSample` — fluent but **eager** single-pass uniform random sample; consumes the upstream immediately at call time

### Breaking Changes

All of the following are confined to `Stream::ofRange`, which previously delegated straight to PHP's native `\range()` and therefore inherited its per-version behavior.

* `Stream::ofRange` numeric-string inputs are uniformly coerced to `int`/`float` before iteration. Previously, two matching numeric-string inputs without leading zeros would preserve string-typed output (e.g. `ofRange("1", "5")` yielded `["1", ..., "5"]`); now it yields `[1, ..., 5]`. Alpha string inputs continue to throw `\InvalidArgumentException` with the existing "must be numeric" message.
* `Stream::ofRange` validation errors are now `\InvalidArgumentException` rather than PHP's `\ValueError`. Which layer rejects the input determines when it is raised: non-numeric strings, and numeric strings that overflow to a non-finite value (e.g. `ofRange("1e309", "1e310")`), are still rejected eagerly by `Stream::ofRange` itself, while the checks performed by `Single::range` — zero step, conflicting step direction, step magnitude greater than the span, non-finite `int|float` operands — are deferred until the first iteration (e.g. on `->toArray()`).
* `Stream::ofRange(1, 5, -1)` now throws instead of yielding `[1, 2, 3, 4, 5]`. This affects PHP 8.2 callers only: PHP 8.3+ native `\range()` already rejected a negative step on an increasing range, so the sequence was never produced there. Use the absolute step magnitude or omit the step argument.
* `Stream::ofRange` pins native `\range()`'s PHP 8.3+ numeric semantics on every supported PHP version, via `Single::range`. Most visibly, an integer-valued float step on integer operands now yields ints (`ofRange(1, 5, 1.0)` → `[1, 2, 3, 4, 5]`); PHP 8.2's native `\range()` sends any float step down the float path and yielded floats there.

### Changes
* `Stream::ofRange` is now lazy — it no longer materializes the full sequence via `\range()` and delegates to `Single::range`. Composing it with downstream limiters (e.g. `Stream::ofRange(1, PHP_INT_MAX)->limit(5)`) is now safe.

### Bug Fixes
* `Stream::peek` is now lazy per element, as documented. Previously it eagerly consumed the entire upstream and invoked the callback for every element at the time `peek()` was called, before any downstream operation ran (e.g. `->peek($fn)->limit(3)` invoked the callback for every element of the source). The callback now fires once per element as downstream operations pull elements through the stream; elements never consumed downstream are never peeked. Values and keys pass through unchanged.
* `Single::limit` (and `Stream::limit`) no longer over-consume the source by one element. Previously the limit was checked only after the source had already been advanced, so `limit(3)` pulled 4 elements from the source and `limit(0)` pulled 1. The extra read was invisible for arrays, but observable — and unwanted — for side-effecting sources such as file handles, HTTP pagination, and database cursors, and it caused a source that errors past the limit to throw. `limit($n)` now pulls exactly the elements it yields, and `limit(0)` does not touch the source at all.
* `Single::slice` (and `Stream::slice`) no longer over-consume the source past the last yielded element, for the same reason. Previously `slice($data, 0, 3)` pulled 4 elements, and with a step it pulled further still (`slice($data, 0, 3, 2)` pulled 7 elements to yield 3). The count is now checked after yielding, and a `$count` of 0 does not touch the source at all. Elements skipped by `$start` or `$step` must still be pulled in order to be skipped over.

### Known Limitations
* `Stream::peekStream` remains whole-stream and eager, by design: the callback is invoked at the time `peekStream()` is called, and if the callback consumes its copy of the stream, the upstream is buffered so the main stream can replay it afterwards.
* `Stream::peekPrint` and `Stream::peekPrintR` delegate to `peekStream` and are therefore still eager — they print the entire upstream at call time, before any downstream operation runs, despite being documented as per-element. `Stream::peek`'s new laziness does not extend to them. Making them lazy is a behavioral change requiring a decision about prefix/separator/suffix placement on a stream that may never be fully consumed, and is deferred to a future release.

## v2.4.0 - 2026-05-06

### New Features
* Infinite
  * `generate`
* Random
  * `sample`
* Reduce
  * `consume`
  * `toLastMatch`
  * `toLastMatchIndex`
  * `toLastMatchKey`
  * `toOnly`
* Set
  * `duplicates`
  * `duplicatesBy`
* Single
  * `groupAdjacentBy`
  * `padLeft`
  * `padRight`
  * `splitWhen`
* Sort
  * `shuffle`
* Summary
  * `atLeastN`
  * `atMostN`
  * `endsWith`
  * `endsWithCoercive`
  * `startsWith`
  * `startsWithCoercive`
* Stream
  * `append`
  * `atLeastN`
  * `atMostN`
  * `consume`
  * `duplicates`
  * `duplicatesBy`
  * `endsWith`
  * `endsWithCoercive`
  * `groupAdjacentBy`
  * `padLeft`
  * `padRight`
  * `prepend`
  * `sample`
  * `shuffle`
  * `splitWhen`
  * `startsWith`
  * `startsWithCoercive`
  * `toLastMatch`
  * `toLastMatchIndex`
  * `toLastMatchKey`
  * `toOnly`

### Bug Fixes
* Coercive comparisons now collapse all numeric forms loose-equal to 0 or 1 (e.g. `1` vs `'1.0'`, `'01'`, `'1e0'`; `0` vs `'0.0'`, `-0.0`, `'-0'`) consistently with PHP's `==` semantics. Affects every coercive API in `Summary`, `Set`, `Math`, and `Stream`.

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

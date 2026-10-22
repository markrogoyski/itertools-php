# IterTools PHP Change Log

## v2.8.0 - TBD

### Bug Fixes
* Combinatorics: `powerset` repeated keys, so `iterator_to_array` kept only some subsets; keys are now sequential.
* Single: `flatten` repeated keys, so `iterator_to_array` dropped elements; keys are now sequential.
* Random: `choice` (and `Stream::ofRandomChoice`) returned `null` for associative or sparse arrays.
* Single: `accumulate` (and `Stream::accumulate`) ignored a named `initial` argument.
* Reduce: `toFirstAndLast` (and `Stream::toFirstAndLast`) iterated its input twice, which failed on single-pass sources; it now iterates once.
* File, Stream: `writeLines`, `writeCsv`, `toFile` and `toCsvFile` silently accepted failed or truncated writes; they now throw `\RuntimeException`. `writeCsv` and `toCsvFile` are about 1.2x slower writing to a file and 2x slower to an in-memory stream, as short writes are now detected; the bytes written are unchanged.
* Transform: `tee` (and `peekStream`, `peekPrint`, `peekPrintR`) replaced a cached `null` value or key with the source's current one, dropped the last line of an `\SplFileObject` without a trailing newline, and yielded nothing for `\SplQueue`, `\SplStack` and `\SplDoublyLinkedList`. Copies also kept each other alive, and an abandoned copy made the others buffer the whole source; dropped copies now release their buffer immediately.
* Infinite: `cycle` (and `Stream::infiniteCycle`) threw on the second pass for one-pass sources other than a bare generator, and dropped the first row of a `\PDOStatement`. Every non-array source is now read once and replayed from a cache.
* Summary: `same`, `arePermutations` and `arePermutationsCoercive` (and their Stream wrappers) returned `false` when a source threw `\LengthException`; the exception now propagates.
* Summary: `startsWith` and `startsWithCoercive` (and the Stream wrappers) advanced the source one element past the prefix; they now stop at the last element compared.
* Multi: `roundRobin` (and `Stream::roundRobinWith`) read more source values than it yielded; it is now lazy and pulls exactly the values it yields.
* Reduce: `toMedian` (and `toPercentile` at 50, `toQuantile` at 0.5, and the Stream wrappers) returned `NAN` when the two middle values were `-INF` and a finite negative number; it now returns `-INF`. The mean of the two middle values is now correctly rounded; before it could be 1 ulp off for integers beyond `2^53`, overflowing sums, subnormals and floats far apart in magnitude. An integer beyond ±2^53 paired with a non-integral float or a float beyond the integer range is still converted to float first, which can lose precision (on 64-bit PHP, `toMedian([PHP_INT_MIN + 1, -(float) PHP_INT_MIN])` gives `0.0` instead of `0.5`).
* Single: `groupBy` (and `Stream::groupBy`) added an item to the same group more than once when its group names converted to the same array key, such as `2020` and `'2020'`.
* Set, Multi, Summary: the set operations, `mergeSorted`, `mergeSortedBy`, `arePermutations` and `arePermutationsCoercive` (and their Stream wrappers) threw "Named parameter … overwrites previous argument" when an iterable was passed by a name such as `strict`, `filler`, `keyFunc` or `a`.
* Infinite: `count` went past `PHP_INT_MAX` (or `PHP_INT_MIN`) by yielding floats, repeating the same value forever; it now throws `\OverflowException` when the next value is out of the integer range, including an advance that is never read (e.g. `Multi::zip(['a'], Infinite::count(PHP_INT_MAX))` yields one tuple and then throws).
* Single: `range` (and `Stream::ofRange`) with a float step had several errors:
  * Yielded an extra or wrong element for operands too large for the step to resolve (`range(1e16, 1e16 + 4, 0.5)` gave 10 elements instead of 9), and for integers beyond `2^53` (`range(2**53 + 1, 2.0**53 + 4, 0.5)` gave 9 instead of 7). The count and endpoint now follow the exact span.
  * Threw "must not exceed span (0)" for distinct integers beyond `2^53` with a fractional step; the span is now taken from the integers themselves.
  * Accepted a step larger than the span near `PHP_INT_MAX` (`range(0, PHP_INT_MAX, (float) PHP_INT_MAX)`) or with float operands (`range(0.1, 2.0**53, 2.0**53)`); the step is now compared with the exact span and throws `\InvalidArgumentException`.
  * Computed values from `$start` converted to float, carrying its rounding error; values are now computed from the exact `$start` and correctly rounded.
  * Dropped the endpoint of a float range spanning a whole number of steps when rounding put the last value just past `$end` (`range(0, 55, 1.1)` stopped at 53.9).
  * Ended on `-0.0` when landing on a `-0.0` end; it now ends on `0.0`, as `$start + n·$step` gives.
  * An integer paired with a non-integral float or a float beyond the integer range is still converted to float first (`range(PHP_INT_MAX, 2.0**63, 0.5)` gives 1 element instead of 3).
* Math: `relativeFrequencies` and `relativeFrequenciesBy` (and their Stream wrappers) yielded `int(1)` instead of `1.0` for a single distinct value or group; they now always yield floats.
* Reduce: `toStandardDeviation` (and `Stream::toStandardDeviation`) returned `INF` or `0.0` when the variance overflowed or underflowed (`[-1e200, 1e200]` gave `INF` instead of `1e200`), returned `INF` when a value was more than `PHP_FLOAT_MAX` from the running mean (`[-1e308, 1e308]`), and was off by whole subnormal steps (the sample standard deviation of `[0, 5e-324]` gave `0.0` instead of `5.0E-324`). Results that did not hit these cases are unchanged.
* Set: `partialIntersection` and `partialIntersectionCoercive` (and the Stream wrappers) silently yielded nothing for a `$minIntersectionCount` below 1; they now throw `\InvalidArgumentException`.
* Reduce, File, Stream: `toString`, `writeCsv`, `print`, `printLn`, `printR`, `toCsvFile`, `peekPrint` and `peekPrintR` raised PHP 8.5's "unexpected NAN value was coerced to string" warning for a `NAN`; they now write `NAN` without a warning. A `NAN` nested inside an array or object passed to `printR` still warns.
* Reduce: `toRange` (and `Stream::toRange`) compared and subtracted an integer beyond `2^53` and a float with the integer converted to float (`toRange([2**53 + 1, 2.0**53])` gave `0.0` instead of `1.0`), and rounded differences beyond the integer range twice. Values are now compared exactly and differences correctly rounded, except that an integer paired with a non-integral float or a float beyond the integer range is still converted to float in the subtraction (`toRange([PHP_INT_MAX, 2.0**63])` gives `0.0` instead of `1.0`). A range of zero is now always `0.0`, never `-0.0`.
* Reduce, Math: `toMin`, `toMax`, `toMinMax`, `runningMin` and `runningMax` (and their Stream wrappers) compared an integer beyond `2^53` and a float with the integer converted to float, and could pick the wrong value (`toMin([2.0**53, 2**53 + 1])` gave `2**53 + 1`); they now compare by exact value.
* Summary, Sort: `isSorted`, `isReversed`, `isSortedBy`, `isReversedBy`, `sort`, `asort`, `sortBy` and `asortBy` (and their Stream wrappers) had the same integer-versus-float comparison error beyond `2^53`; they now compare by exact value. A custom `$comparator` is unchanged.
* Reduce: `toNth` (and `Stream::toNth`) threw `\LengthException` for an existing position when a `Countable` under-reported its size; it now reads the elements. `toCount` and `toRandomValue` still trust `Countable::count()`, which is now documented.
* Single, Summary, Reduce, Transform: functions that coerce an element or a predicate's return value to bool (`filter`, `takeWhile`, `dropWhile`, `allMatch`, `anyMatch`, `exactlyN`, `isPartitioned`, `partition`, `compress`, and others, with their Stream wrappers) raised PHP 8.5's "unexpected NAN value was coerced to bool" warning for a `NAN`; it is still `true`, and no longer warns. A `NAN` coerced inside a user's own predicate still warns.

### Changes
* Single: a float `range` (and `Stream::ofRange`) spanning a whole number of steps, to within the rounding its operands carry, now ends exactly on `$end`; `range(0.3, 0.9, 0.2)` yields `[0.3, 0.5, 0.7, 0.9]` where native `\range()` drops `0.9`.
* Transform: `tee` no longer reads the source when called; the first element is read when a copy is first used. A copy can be rewound while it has not advanced; once advanced, `rewind()` throws `\LogicException`. A cloned copy is an independent branch from the original's position.
* Multi: `roundRobin` (and `Stream::roundRobinWith`) no longer rewinds every source up front. A source that cannot be rewound now throws when its first turn comes, after earlier sources' values have been yielded.
* Infinite: `cycle` (and `Stream::infiniteCycle`) reads a generator lazily instead of in full when called, and preserves generator keys instead of renumbering from 0. A non-array source is read once, so later changes to it are not seen.
* Sort: `sort`, `asort`, `sortBy` and `asortBy` (and their Stream wrappers) with the default comparison now place a float `NAN` after every other value, in input order, and sort the rest correctly: `sort([2, 4, NAN, 5, 6, 1, 3])` gave `[1, 3, NAN, 2, 4, 5, 6]` and now gives `[1, 2, 3, 4, 5, 6, NAN]`. A custom `$comparator` is unchanged.
* Reduce: `toMedian`, `toPercentile` and `toQuantile` (and their Stream wrappers) now return `NAN` if any element is `NAN`, wherever it sits; before, the result depended on its position (`toMedian([NAN, 1, 2])` gave `2`).
* Reduce: `toMinMax` (and `Stream::toMinMax`) with `$compareBy` now returns the first of the tied minimum and maximum elements, like `toMin` and `toMax`, instead of the last.
* Set: `symmetricDifference` and `symmetricDifferenceCoercive` with a single iterable (and `Stream::symmetricDifferenceWith` and `symmetricDifferenceCoerciveWith` with no arguments) now yield that iterable's elements instead of nothing: `symmetricDifference([1, 2, 2, 3])` gives `[1, 2, 2, 3]`.
* File, Stream: documented that the default CSV `$escape = '\\'` does not round-trip a field ending in a backslash (e.g. `C:\Users\`), which reads back as an unclosed quote. Pass `$escape = ''` on both the write and read side to avoid it. The default stays `'\\'` in 2.x to keep existing files' bytes; 3.0 will change it to `''`.
* Single: `groupBy` (and `Stream::groupBy`) now accepts only `int`, `string` and `bool` group names, and throws `\TypeError` for other types (e.g. float or `null`), which were previously coerced to array keys (`4.0`, `4.5` and `4.7` merged into group `4`). `Reduce::toCountBy` also rejects `bool`.

## v2.7.0 - 2026-10-07

### Bug Fixes
* Set, Summary, Math, Reduce: floats were compared at the `precision` ini setting (14 digits by default) instead of by exact value, so `0.1 + 0.2` and `0.3` could be treated as equal and results depended on `precision`/`serialize_precision`; floats now compare by exact value.
* Set, Summary, Math, Reduce: in strict mode `-0.0` and `0.0` were treated as different values although `-0.0 === 0.0`; they are now equal.
* Set, Summary, Math, Reduce: in coercive mode, integers beyond `2^53` (e.g. `9007199254740992` and `9007199254740993`) were treated as equal; they now compare by exact value.
* Set, Summary, Math, Reduce: a resource was treated as a different value once closed; a resource now keeps one identity whether open or closed.
* Set, Summary, Math, Reduce: arrays containing closures or generators threw a bare `\Exception` instead of the documented `\InvalidArgumentException`, and arrays containing different resources compared equal to each other, and to `0`.
* Summary: `allUnique` could return `false` for distinct objects when objects were freed during iteration.
* Set, Summary, Math, Reduce: objects, closures, generators, and resources nested inside arrays could be falsely treated as equal to a later, unrelated value when the original was freed or its reference slot reassigned during iteration (e.g. a generator yielding `[&$slot]` and reassigning `$slot`).
* Set: `symmetricDifference` and `symmetricDifferenceCoercive` silently dropped arrays holding a reference slot that was reassigned after being yielded.
* Summary: `arePermutations` and `arePermutationsCoercive` could return `true` for non-permutations when arrays held reference slots reassigned onto a shared value.
* Summary: `containsCoercive` could match the wrong value when the needle was an array holding a closure, generator, or resource through a reassigned reference slot.

### Changes
* Arrays now compare element-wise under the active mode. In strict mode, nested objects, closures, generators, and resources compare by instance, and nested `-0.0` equals `0.0`. In coercive mode, `[1]` and `['1']` are now equal (e.g. `Set::distinct([[1], ['1']], false)` yields one element; `Set::unionCoercive([[1]], [['1']])` yields one).
* Arrays nested deeper than 256 levels, including self-referential arrays, now throw `\InvalidArgumentException`.
* Coercive scalar comparison is documented as numeric equivalence, not PHP `==`: `'abc'` is not equal to `true`, and `0`, `''`, `null`, and `false` remain one value.
* `intersection*`, `partialIntersection*`, `union*`, `difference*`, `arePermutations*`, and `Summary::allUnique` use less memory on large inputs.

## v2.6.0 - 2026-09-21

### New Features
* Multi
  * `mergeSorted`: lazily merge sorted iterables, preserving source order for equal values; source keys are discarded
  * `mergeSortedBy`: lazily merge iterables sorted by a projected key
* Single
  * `takeWhileWithKeys`: return elements while a key-aware predicate is truthy
  * `dropWhileWithKeys`: drop elements while a key-aware predicate is truthy
* Summary
  * `allMatchWithKeys`: true if all elements match a key-aware predicate
  * `anyMatchWithKeys`: true if any element matches a key-aware predicate
  * `noneMatchWithKeys`: true if no element matches a key-aware predicate
  * `allEqual`: true when all values have the same identity, coercive or strict
  * `allEqualBy`: true when all projected keys have the same identity, coercive or strict
* Transform
  * `memoize`: lazily cache an iterable so current and future consumers can replay it
* Stream
  * `mergeSortedWith`: fluent lazy merge of this stream with sorted iterables
  * `mergeSortedByWith`: fluent lazy merge of this stream with iterables sorted by a projected key
  * `takeWhileWithKeys`: fluent key-aware take-while
  * `dropWhileWithKeys`: fluent key-aware drop-while
  * `allMatchWithKeys`: terminal; true if all elements match a key-aware predicate
  * `anyMatchWithKeys`: terminal; true if any element matches a key-aware predicate
  * `noneMatchWithKeys`: terminal; true if no element matches a key-aware predicate
  * `allEqual`: terminal; true when all values have the same identity
  * `allEqualBy`: terminal; true when all projected keys have the same identity
  * `memoize`: lazily make the current stream replayable by caching demanded values
  * `peekWithKeys`: lazily invoke a callback with each value and key without changing the stream
  * `callForEachWithKeys`: terminal; invoke a callback with each value and key

### Bug Fixes
* Set: `distinct`, `distinctBy`, `duplicates`, `duplicatesBy`, `difference`, `intersection`, `partialIntersection`, `union`, and `symmetricDifference` could equate unrelated objects in strict mode. An object's identity string is derived from its `spl_object_id`, which PHP reuses once the object is freed, so an object hashed and then discarded could hand its identity to a later, unrelated object. Hashed values are now retained for the lifetime of the comparison.
* Set: `symmetricDifference`/`symmetricDifferenceCoercive` could emit an inconsistent representative value for an equivalence class; the last-seen value is now used consistently.
* Summary: `allEqual`/`allEqualBy` could treat a shared, mutated key object as unchanged in coercive mode because it short-circuited on instance identity instead of current value.

## v2.5.0 - 2026-07-25

### New Features
* Single
  * `range`: lazy finite arithmetic progression
  * `mapWithKeys`: key-aware map; callback receives `($value, $key)`, keys preserved
  * `filterWithKeys`: key-aware filter; predicate receives `($value, $key)`, keys preserved
  * `flatMapWithKeys`: key-aware flat map; callback receives `($value, $key, $self)`, result flattened by one level with auto-generated numeric keys
  * `takeLast`: iterate the last N elements; keys preserved
  * `dropLast`: iterate all elements except the last N; keys preserved
  * `windowed`: sliding windows of size N advancing by a step; supports gapped windows (`step > size`) and an optional trailing partial window
  * `withFirst`: pair each element with an is-first boolean flag
  * `withLast`: pair each element with an is-last boolean flag
  * `withFirstAndLast`: pair each element with is-first/is-last boolean flags
* Random
  * `reservoirSample`: single-pass uniform random sample (Algorithm R); returns the whole input in original order when `size >= count`
* Math
  * `frequenciesBy`: frequency distribution grouped by a key function
  * `relativeFrequenciesBy`: relative frequency distribution grouped by a key function
* Summary
  * `isSortedBy`: true if values projected by a key function are non-decreasing
  * `isReversedBy`: true if values projected by a key function are non-increasing
* Reduce
  * `toCountBy`: reduce to an array of counts keyed by a key function
  * `toMedian`: reduce to the median value; mean of the two middle values for even-length input, computed without overflow or precision loss
  * `toMode`: reduce to a list of the most frequent values (all modes, in first-seen order)
  * `toVariance`: reduce to the population (default) or sample variance; single pass in `O(1)` memory, order-stable, and overflow-safe. A non-finite value anywhere in the input yields `NAN`, except where the `null` cases (empty collection, sample variance of a single value) apply
  * `toStandardDeviation`: reduce to the population (default) or sample standard deviation; inherits `toVariance`'s behavior
  * `toPercentile`: reduce to the value at a percentile `[0, 100]` (R-7 / linear interpolation); overflow-safe, and percentile `50` matches `toMedian`
  * `toQuantile`: reduce to the value at a quantile `[0, 1]`
* File
  * `writeLines`: write an iterable of lines to a file resource; separator inserted between lines, no trailing separator
  * `writeCsv`: write an iterable of rows to a file resource as CSV, with optional header row
  * `readCsvAssoc`: iterate CSV rows as associative arrays keyed by header (inferred from the first row or supplied explicitly); validates headers and row-length consistency. Note that PHP coerces canonical numeric-string headers (`"1"`, `"2020"`) to integer array keys
* Stream
  * `mapWithKeys`: fluent key-aware map
  * `filterWithKeys`: fluent key-aware filter
  * `flatMapWithKeys`: fluent key-aware flat map
  * `takeLast`: fluent iterate the last N elements
  * `dropLast`: fluent iterate all elements except the last N
  * `frequenciesBy`: fluent frequency distribution grouped by a key function
  * `relativeFrequenciesBy`: fluent relative frequency distribution grouped by a key function
  * `isSortedBy`: terminal; true if values projected by a key function are non-decreasing
  * `isReversedBy`: terminal; true if values projected by a key function are non-increasing
  * `toCountBy`: terminal; reduce to an array of counts keyed by a key function
  * `toMedian`: terminal; reduce to the median value
  * `toMode`: terminal; reduce to a list of the most frequent values
  * `toVariance`: terminal; reduce to the population or sample variance
  * `toStandardDeviation`: terminal; reduce to the population or sample standard deviation
  * `toPercentile`: terminal; reduce to the value at a percentile `[0, 100]`
  * `toQuantile`: terminal; reduce to the value at a quantile `[0, 1]`
  * `ofCsvFileAssoc`: source; stream a CSV file as associative arrays keyed by header
  * `windowed`: fluent sliding windows of elements
  * `withFirst`: fluent pair each element with an is-first flag
  * `withLast`: fluent pair each element with an is-last flag
  * `withFirstAndLast`: fluent pair each element with is-first/is-last flags
  * `reservoirSample`: fluent but **eager** single-pass uniform random sample; consumes the upstream immediately at call time

### Breaking Changes

All of the following are confined to `Stream::ofRange`, which previously delegated straight to PHP's native `\range()` and therefore inherited its per-version behavior.

* Numeric-string inputs are uniformly coerced to `int`/`float` before iteration: `ofRange("1", "5")` now yields `[1, ..., 5]` rather than `["1", ..., "5"]`. Alpha strings still throw `\InvalidArgumentException`.
* Validation errors are now `\InvalidArgumentException` rather than PHP's `\ValueError`, and the rejecting layer determines when they are raised. Non-numeric strings and numeric strings that overflow to a non-finite value (e.g. `ofRange("1e309", "1e310")`) are still rejected eagerly by `Stream::ofRange`, while `Single::range`'s checks — zero step, conflicting step direction, step magnitude greater than the span, non-finite `int|float` operands — are deferred until the first iteration.
* `Stream::ofRange(1, 5, -1)` now throws instead of yielding `[1, 2, 3, 4, 5]`. This affects PHP 8.2 callers only, since PHP 8.3+ native `\range()` already rejected a negative step on an increasing range. Use the absolute step magnitude or omit the step argument.
* `Stream::ofRange` pins native `\range()`'s PHP 8.3+ numeric semantics on every supported PHP version, via `Single::range`. Most visibly, an integer-valued float step on integer operands now yields ints (`ofRange(1, 5, 1.0)` → `[1, 2, 3, 4, 5]`) where PHP 8.2 yielded floats.

### Changes
* `Stream::ofRange` is now lazy — it no longer materializes the full sequence via `\range()` and delegates to `Single::range`. Composing it with downstream limiters (e.g. `Stream::ofRange(1, PHP_INT_MAX)->limit(5)`) is now safe.

### Bug Fixes
* `Stream::peek` is now lazy per element, as documented. Previously it consumed the entire upstream and invoked the callback for every element at the time `peek()` was called, before any downstream operation ran. The callback now fires once per element as downstream operations pull elements through the stream; elements never consumed downstream are never peeked. Values and keys pass through unchanged.
* `Single::limit` (and `Stream::limit`) no longer over-consume the source by one element. Previously `limit(3)` pulled 4 elements and `limit(0)` pulled 1 — invisible for arrays, but observable for side-effecting sources such as file handles, HTTP pagination, and database cursors, and it caused a source that errors past the limit to throw. `limit($n)` now pulls exactly the elements it yields, and `limit(0)` does not touch the source at all.
* `Single::slice` (and `Stream::slice`) no longer over-consume the source past the last yielded element, for the same reason: `slice($data, 0, 3)` pulled 4 elements, and `slice($data, 0, 3, 2)` pulled 7 to yield 3. A `$count` of 0 does not touch the source at all. Elements skipped by `$start` or `$step` must still be pulled in order to be skipped over.

### Known Limitations
* `Stream::peekStream` remains whole-stream and eager, by design: the callback is invoked at the time `peekStream()` is called, and if it consumes its copy of the stream, the upstream is buffered so the main stream can replay it afterwards.
* `Stream::peekPrint` and `Stream::peekPrintR` delegate to `peekStream` and are therefore still eager — they print the entire upstream at call time, despite being documented as per-element. Making them lazy requires deciding prefix/separator/suffix placement on a stream that may never be fully consumed, and is deferred to a future release.

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

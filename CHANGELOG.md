# IterTools PHP Change Log

## v2.8.0 - TBD

A fix or change to a function also applies to its `Stream` wrapper.

### Bug Fixes

**Keys and arguments**
* Combinatorics, Single: `powerset` and `flatten` repeated keys, so `iterator_to_array` lost elements; keys are now sequential.
* Random: `choice` (and `Stream::ofRandomChoice`) returned `null` for associative or sparse arrays.
* Single: `accumulate` ignored a named `initial` argument.
* Stream: `accumulate` threw `\Error` ("Named parameter $data overwrites previous argument") for an initial value named `data`; it now throws `\InvalidArgumentException`, as `Single::accumulate` does for any name other than `initial`.
* Single: `groupBy` added an item to a group twice when its group names mapped to the same array key (`2020` and `'2020'`).
* Set, Multi, Summary: the set operations, `mergeSorted`, `mergeSortedBy` and `arePermutations*` threw "Named parameter … overwrites previous argument" when an iterable was passed by a name such as `strict`, `filler`, `keyFunc` or `a`.

**Reading sources**
* Reduce: `toFirstAndLast` iterated its input twice, failing on single-pass sources.
* Reduce: `toRandomValue` threw `\LengthException` for a non-empty `Countable` reporting a size of 0 (e.g. a `\CachingIterator` with `FULL_CACHE`), and read one that over-reports its size twice. It now reads the source once.
* Reduce: `toNth` threw `\LengthException` for an existing position when a `Countable` under-reported its size. (`toCount` still trusts `count()`, and `toRandomValue` a nonzero one; now documented.)
* Summary: `startsWith` and `startsWithCoercive` read one element past the prefix.
* Summary: `same` with a single source read it in full, which never returned for an infinite source.
* Summary: `same` and `arePermutations*` returned `false` when a source threw `\LengthException`; it now propagates.
* Multi: `roundRobin` read more values than it yielded; it is now lazy.
* Single: `padLeft` read the whole source before yielding, so it never yielded for an infinite source; once the source reaches `$length` it now streams the rest.
* Infinite: `cycle` threw on the second pass for one-pass sources other than a bare generator, and dropped the first row of a `\PDOStatement`.
* Transform: `tee` (and `peekStream`, `peekPrint`, `peekPrintR`) replaced a cached `null` value or key with the source's current one, dropped the last line of an `\SplFileObject` with no trailing newline, yielded nothing for `\SplQueue`, `\SplStack` and `\SplDoublyLinkedList`, and an abandoned copy made the others buffer the whole source. Dropped copies now release their buffer, as does a copy that `memoize` or `cycle` has read to its end.
* Transform, Infinite: `memoize` and `cycle` kept a non-array source after reading it to its end or after it threw; they now release it and replay their cached elements and the exception. A `tee` copy read through one, as in `cycle(new \LimitIterator($copy, 0, 3))`, no longer makes the other copies buffer the rest of the source.
* Transform: `memoize` clones split the source's elements between them and could rewind or re-read it; a clone now shares the source and cache with the original.
* Summary, Set, Single: `endsWith`, `endsWithCoercive`, `isSorted`, `isReversed`, `isSortedBy`, `isReversedBy`, `distinctAdjacent`, `distinctAdjacentBy` and `groupAdjacentBy` compared an array element only after reading later ones, so rows filled through one shared reference (as `PDOStatement::bindColumn()` does) all compared as the last row; `endsWithCoercive` also mishandled an object mutated between reads. Elements are now compared as they were when read. See README "Values compared after later reads". Two reads of one unchanged array with a reference slot now compare unequal if it holds a `NAN` outside its subarrays without reference slots: `distinctAdjacent` keeps both reads of a row `['id' => &$id, 'score' => NAN]`. Costs:
  * `isSorted` of two-field rows takes ~2x as long; of scalars, ~half as long.
  * Pathological nesting is slow: `$a = [$a, $a]` repeated twenty times takes 0.7 s (was 1 ms), and one element holding 10,000 distinct subarrays with reference slots about 2 s.
  * `endsWithCoercive` hashes every array and object element, not just the last `count($suffix)`; a non-serializable object still throws only if comparison reaches it.

**Errors and overflow**
* File: `writeLines`, `writeCsv`, `toFile` and `toCsvFile` silently accepted failed or truncated writes; they now throw `\RuntimeException`. `writeCsv` and `toCsvFile` are ~1.2x slower to a file and 2x slower to an in-memory stream; output bytes are unchanged.
* Set: `partialIntersection*` yielded nothing for a `$minIntersectionCount` below 1; it now throws `\InvalidArgumentException`.
* Infinite, Single: `count` and `enumerate` went past `PHP_INT_MAX` (or `PHP_INT_MIN`) by yielding floats that repeat forever; they now throw `\OverflowException` when the next value is out of range. For `count` this includes an advance that is never read (`Multi::zip(['a'], Infinite::count(PHP_INT_MAX))` yields one tuple, then throws).
* Reduce, File, Stream, Single, Summary, Transform: a `NAN` raised PHP 8.5's "unexpected NAN value was coerced to string/bool" warning in `toString`, `writeCsv`, `print`, `printLn`, `printR`, `toCsvFile`, `peekPrint`, `peekPrintR`, and in the bool-coercing functions (`filter`, `takeWhile`, `dropWhile`, `allMatch`, `anyMatch`, `exactlyN`, `isPartitioned`, `partition`, `compress`, …). Output is unchanged, without the warning. A `NAN` nested in a value passed to `printR`, or coerced inside a user's own predicate, still warns.

**Numeric precision**
* Integers beyond `2^53` were converted to float when compared with a float, giving wrong results (`toMin([2.0**53, 2**53 + 1])` gave `2**53 + 1`). They now compare by exact value in `toMin`, `toMax`, `toMinMax`, `runningMin`, `runningMax`, `toRange`, `toMedian`, `toPercentile`, `toQuantile`, `isSorted`, `isReversed`, `isSortedBy`, `isReversedBy`, `sort`, `asort`, `sortBy`, `asortBy`, `largest`, `smallest`, `mergeSorted` and `mergeSortedBy`, at the same speed unless the input mixes floats with such integers. A custom `$comparator` is unchanged.
  * `toVariance` and `toStandardDeviation` keep the differences between such integers, also when mixed with floats (`toVariance([2**53, 2**53 + 1])` gave `0.0`, now `0.25`); ~10% slower on integers, 4% on floats.
  * `toRange` differences are correctly rounded, and a zero range is always `0.0`, never `-0.0`.
  * Limitation: in arithmetic (`toMedian`, `toRange`, float `range`), an integer paired with a non-integral float or a float beyond the integer range is still converted to float (`toRange([PHP_INT_MAX, 2.0**63])` gives `0.0`, not `1.0`). `range` also checks the step against that converted span: `range(0.5, 2**53 + 1, 2.0**53)` throws, as `2^53` exceeds `2^53 - 0.5`.
* Reduce: `toMedian` (and percentile 50 / quantile 0.5) returned `NAN` for middle values `-INF` and a finite negative number (now `-INF`), and the mean of the two middle values could be 1 ulp off; it is now correctly rounded.
* Reduce: `toPercentile` and `toQuantile` rounded the rank `(n - 1) * p / 100` before testing it for an integer, so a rank just off an integer could land on it and one on an integer just off it: `toPercentile(range(0, 25), 28)` gave `7.000000000000001`, not `7`, and with an `INF` neighbour gave `INF`; `toPercentile([0, 0, INF, …], 100 / 7)` gave `0`, not `INF`. `toQuantile` also scaled its quantile to a percentile first, which rounds: `toQuantile([0, 0, INF, INF, INF, INF], 0.2)` gave `0`, not `INF`. The rank is now located from the exact product of the percentile or quantile itself, and a weight close to 0 or 1 keeps its digits, also below `PHP_FLOAT_MIN` (`toPercentile([0, 1e308], 5e-324)` gave `0`, now `4.94e-18`). Interpolating between integers beyond `2^53` converted the lower one to float before adding the offset (`toPercentile([2**53 + 1, 2**53 + 4], 25)` gave `2**53`, not `2**53 + 2`); such results are now computed exactly from the endpoints and the exact weight and rounded once, so they are correctly rounded. Other interpolations remain accurate to about an ulp, as in NumPy, rather than correctly rounded.
* Reduce, Math: `toAverage` and `runningAverage` returned `±INF` when the float sum overflowed although the mean is finite (`toAverage([1e308, 1e308])`). Non-overflowing results are unchanged. `runningAverage` keeps the sign of a `-0.0` initial value.
* Reduce: `toStandardDeviation` returned `INF` or `0.0` when the variance overflowed or underflowed (`[-1e200, 1e200]`) or a value was more than `PHP_FLOAT_MAX` from the running mean, and `toVariance` and `toStandardDeviation` were off by whole subnormal steps (sample variance of `[0, 3e-162]` gave `0.0`, not `5.0E-324`). Other results are unchanged.
* Math: `relativeFrequencies` and `relativeFrequenciesBy` yielded `int(1)` for a single distinct value; they now always yield floats.
* Single: `range` with a float step or a float operand:
  * Count and endpoint now follow the exact span, fixing extra or wrong elements for large operands (`range(1e16, 1e16 + 4, 0.5)` gave 10 elements, not 9) and integers beyond `2^53`, and a spurious "must not exceed span (0)" error.
  * A step larger than the exact span now throws `\InvalidArgumentException` (`range(0, PHP_INT_MAX, (float) PHP_INT_MAX)` was accepted with 64-bit ints, where the step rounds up to `2^63`).
  * Values are computed from the exact `$start` and correctly rounded; an endpoint lost to rounding is kept (`range(0, 55, 1.1)` stopped at 53.9), and a `-0.0` end yields `0.0`.
  * A float span that overflows no longer throws regardless of step count (`range(-1e308, 1e308, 1e308)` yields `[-1e308, 0.0, 1e308]`); an uncountable span names its operands rather than `INF`.
  * Integral floats within the integer range are taken as integers, as an integer partner already takes them, so `range(0.0, 27021597764222988.0, 2**53 + 3)` yields what `range(0, 27021597764222988.0, 2**53 + 3)` does, ending on `27021597764222984.0` (was `27021597764222988.0`). On other operands, an integer step beyond `2^53` that a float cannot hold is applied as its exact value, the one the step count uses, with each value rounded once: `range(0.5, 27021597764222988.0, 2**53 + 3)` ends on `27021597764222984.0` (was `27021597764222988.0`). An integer step that a float holds is applied as that float, as the equal float step is.
  * A non-integral operand that is a power of two could be taken as a decimal one digit longer than the shortest one that reads back as it, so `range(-2e-25, 6.617444900424222e-24, 6.817444900424222e-24)` threw "must not exceed span"; it now yields `[-2e-25, 6.617444900424222e-24]`.
  * With 32-bit ints, an integral float step of `(float) PHP_INT_MAX`, which is exactly `2^31 - 1` there, yielded floats; it now yields ints, as the equal int step does. An integral float past `2^53` that is its own shortest decimal, such as `2.0 ** 54`, was not recognized as exact, so `range(2.0 ** 54, 0.3, -2.0 ** 53)` also yielded `0.3`.

### Changes
* **Same source passed more than once.** An iterator or generator passed to one call more than once — directly, through a `Stream` or other `IteratorAggregate` returning it, or as a stream passed to its own methods — skipped values, threw ("Cannot traverse an already closed generator", "Cannot resume an already running generator", …) or gave wrong answers. Now:
  * `zip`, `zipLongest`, `zipEqual`, `zipFilled`, `roundRobin`, `mergeSorted`, `mergeSortedBy`, `union`, `intersection`, `partialIntersection` and `symmetricDifference` (with coercive forms and the row-wise `Stream::zip` family) read it as independent copies: `zip($it, $it)` over `[1, 2, 3]` yields `[[1, 1], [2, 2], [3, 3]]` (was `[[1], [2], [3]]`), and `mergeSorted($it, $it)` yields `[1, 1, 2, 2, 3, 3]`. `mergeSorted*` copies share a buffer of the current run of equal values, unbounded for an infinite source of equal values. `zipEqual` still throws on unequal lengths.
  * `product` yields every combination: `product($gen, $gen)` over `[1, 2]` yields `[1, 1], [1, 2], [2, 1], [2, 2]`.
  * `difference` subtracts a repeated subtrahend once per position it was passed in; when the first iterable is also a subtrahend, the result is empty.
  * `sameCount` returns `true`.
  * `same`, `arePermutations*`, `startsWith*` and `endsWith*` return `true` without reading it, including an array-backed stream compared with itself that holds `NAN` or a non-serializable object. For `same` and `arePermutations*`, an object passed more than once is not even resolved (an `IteratorAggregate`'s `getIterator()` is not called), and distinct objects that resolve to one iterator are not read. For `startsWith*` and `endsWith*` this holds when it is passed directly or through a `Stream`; through another `IteratorAggregate` it is read, then recognized, and an exception thrown while reading it propagates.
  * `chainWith` of a stream with itself yields its elements twice for arrays and rewindable iterators; over a generator (including after a step such as `map`) it throws after the first pass, as `Multi::chain($gen, $gen)` does.
  * A stream passed directly to its own methods stands for its elements at that point in the pipeline; a stream wrapped in another iterable (`Stream::of($s)`, `Transform::memoize($s)`) is not detected.
  * `Transform::toIterator` returns the iterator an aggregate's `getIterator()` returns, following nested aggregates, instead of wrapping it. An aggregate returning itself, or a cycle of aggregates, throws `\LogicException` (was `\Exception`, or a stack-overflow crash).
* Single: a float `range` spanning a whole number of steps, to within its operands' rounding, now ends exactly on `$end`: `range(0.3, 0.9, 0.2)` yields `[0.3, 0.5, 0.7, 0.9]` where native `\range()` drops `0.9`.
* Single: `groupBy` accepts only `int`, `string` and `bool` group names and item keys (a `null` item key still appends), and throws `\TypeError` for others, which were coerced (`4.5` and `4.7` merged into `4`). `Reduce::toCountBy` also rejects `bool`.
* Transform: `tee` reads nothing, and calls no `getIterator()` on an `IteratorAggregate`, until a copy is first used; `getIterator()` is then called once for all copies, and an exception it throws reaches every copy. A count below 1 throws before the iterable is touched. A copy can be rewound until it advances, then `rewind()` throws `\LogicException`. A cloned copy is an independent branch from the original's position, and can be rewound until it advances from there.
* Multi: `roundRobin` no longer rewinds every source up front; a non-rewindable source throws on its first turn, after earlier values have been yielded.
* Infinite: `cycle` reads a generator lazily and preserves its keys instead of renumbering. A non-array source is read once, so later changes to it are not seen.
* Sort: `sort`, `asort`, `sortBy` and `asortBy` with the default comparison place `NAN` after all other values, in input order, and sort the rest correctly (`sort([2, 4, NAN, 5, 6, 1, 3])` gave `[1, 3, NAN, 2, 4, 5, 6]`).
* Reduce: `toMedian`, `toPercentile` and `toQuantile` return `NAN` if any element is `NAN`; before, it depended on position (`toMedian([NAN, 1, 2])` gave `2`).
* Reduce: `toMinMax` with `$compareBy` returns the first of tied elements, like `toMin` and `toMax`, not the last.
* Set: `symmetricDifference*` of a single iterable yields its elements instead of nothing.
* File: documented that the default CSV `$escape = '\\'` does not round-trip a field ending in a backslash; pass `$escape = ''` when writing and reading. The default changes to `''` in 3.0.

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

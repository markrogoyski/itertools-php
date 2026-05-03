# Stream

[Back to main README](../README.md)

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

#### Accumulate
Accumulate the running result of applying a binary operator across the stream.

```$stream->accumulate(callable $op, mixed ...$initial): Stream```

* Without an initial value: the first yielded element is the first datum unchanged, and each subsequent element is `$op(accumulator, nextDatum)`.
* With an initial value: the first yielded element is the initial value, and each subsequent element is `$op(accumulator, nextDatum)`.
* Explicit `null` is a legitimate initial value.
* Throws `\InvalidArgumentException` if more than one initial value is passed.

```php
use IterTools\Stream;

$numbers = [1, 2, 3, 4, 5];

$runningSums = Stream::of($numbers)
    ->accumulate(fn ($a, $b) => $a + $b)
    ->toArray();
// [1, 3, 6, 10, 15]

$withInitial = Stream::of($numbers)
    ->accumulate(fn ($a, $b) => $a + $b, 100)
    ->toArray();
// [100, 101, 103, 106, 110, 115]
```

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

#### Product With
Cartesian product of the stream with zero or more additional iterables.

```$stream->productWith(iterable ...$iterables): Stream```

Tuples are list arrays (0-indexed, in input order). With zero extra iterables, each stream element is wrapped in a one-element tuple. If any iterable (stream or extra) is empty, the result is empty.

Note: Passing the same non-rewindable iterator instance more than once is not supported.

```php
use IterTools\Stream;

$numbers = [1, 2];
$letters = ['a', 'b'];

$result = Stream::of($numbers)
    ->productWith($letters)
    ->toArray();
// [[1, 'a'], [1, 'b'], [2, 'a'], [2, 'b']]
```

#### Permutations
Permutations of the stream's elements.

```$stream->permutations(?int $r = null): Stream```

Tuples are list arrays (0-indexed, in input order). Output order follows Python's `itertools.permutations` (lexicographic by input position): duplicate values are position-unique. `$r = 0` yields one empty tuple; `$r` greater than the stream length yields nothing; `$r = null` means full-length permutations.

Throws `\InvalidArgumentException` if `$r` is negative.

```php
use IterTools\Stream;

$data = [1, 2, 3];

$result = Stream::of($data)
    ->permutations(2)
    ->toArray();
// [[1, 2], [1, 3], [2, 1], [2, 3], [3, 1], [3, 2]]
```

#### Combinations
Combinations (without replacement) of the stream's elements.

```$stream->combinations(int $r): Stream```

Tuples are list arrays (0-indexed, in input order). Output order follows Python's `itertools.combinations` (lexicographic by input position): duplicate values are position-unique. `$r = 0` yields one empty tuple; `$r` greater than the stream length yields nothing.

Throws `\InvalidArgumentException` if `$r` is negative.

```php
use IterTools\Stream;

$data = [1, 2, 3, 4];

$result = Stream::of($data)
    ->combinations(2)
    ->toArray();
// [[1, 2], [1, 3], [1, 4], [2, 3], [2, 4], [3, 4]]
```

#### Combinations With Replacement
Combinations with replacement of the stream's elements.

```$stream->combinationsWithReplacement(int $r): Stream```

Tuples are list arrays (0-indexed, in input order). Output order follows Python's `itertools.combinations_with_replacement` (lexicographic by input position): duplicate values are position-unique and may produce duplicate output tuples. Unlike `combinations()`, `$r` may exceed the stream length — elements repeat. `$r = 0` yields one empty tuple.

Throws `\InvalidArgumentException` if `$r` is negative.

```php
use IterTools\Stream;

$data = [1, 2, 3];

$result = Stream::of($data)
    ->combinationsWithReplacement(2)
    ->toArray();
// [[1, 1], [1, 2], [1, 3], [2, 2], [2, 3], [3, 3]]
```

#### Powerset
Every subset of the stream's elements, ordered by length then by input position.

```$stream->powerset(): Stream```

Subsets are list arrays (0-indexed, in input order); source keys are discarded. Subsets are yielded in length-ascending order; within each length the order matches `Stream::combinations` (lexicographic by input position): duplicate values are position-unique. Empty stream yields one empty subset.

> **Warning:** a stream of `n` elements yields `2**n` subsets — consumption grows exponentially.

```php
use IterTools\Stream;

$data = [1, 2, 3];

$result = Stream::of($data)
    ->powerset()
    ->toArray();
// [[], [1], [2], [3], [1, 2], [1, 3], [2, 3], [1, 2, 3]]
```

```php
use IterTools\Stream;

// Every combination of feature flags to drive parameterized tests.
$result = Stream::of(['darkMode', 'beta', 'analytics'])
    ->powerset()
    ->toArray();
// [[], ['darkMode'], ['beta'], ['analytics'],
//  ['darkMode', 'beta'], ['darkMode', 'analytics'], ['beta', 'analytics'],
//  ['darkMode', 'beta', 'analytics']]
```

See also [Combinatorics::powerset](combinatorics-iteration.md#powerset).

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

Defaults to [strict type](../README.md#strict-and-coercive-types) comparisons. Set strict to false for type coercion comparisons.

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

#### Distinct By
Return a stream filtering out elements from the stream only returning distinct elements according to a custom comparator function.

```$stream->distinctBy(callable $compareBy): Stream```

```php
use IterTools\Stream;

$streetFighterConsoleReleases = [
    ['id' => '112233', 'name' => 'Street Fighter 3 3rd Strike', 'console' => 'Dreamcast'],
    ['id' => '223344', 'name' => 'Street Fighter 3 3rd Strike', 'console' => 'PS4'],
    ['id' => '334455', 'name' => 'Street Fighter 3 3rd Strike', 'console' => 'PS5'],
    ['id' => '445566', 'name' => 'Street Fighter VI', 'console' => 'PS4'],
    ['id' => '556677', 'name' => 'Street Fighter VI', 'console' => 'PS5'],
    ['id' => '667799', 'name' => 'Street Fighter VI', 'console' => 'PC'],
];
$stream = Stream::of($streetFighterConsoleReleases)
    ->distinctBy(fn ($sfTitle) => $sfTitle['name'])
    ->toArray();
// Contains one SF3 3rd Strike entry and one SFVI entry
```

#### Distinct Adjacent
Return a stream that removes only consecutive duplicates (Unix `uniq` behavior).

```$stream->distinctAdjacent(): Stream```

* Each element is compared strictly (`===`) to the previous element yielded.
* Non-adjacent duplicates are kept.
* Runs in O(1) memory — only the previous element is held.
* Source keys are discarded.

```php
use IterTools\Stream;

$result = Stream::of([1, 1, 2, 2, 3, 1, 1])
    ->distinctAdjacent()
    ->toArray();
// [1, 2, 3, 1]
```

```php
use IterTools\Stream;

$logLines = ['error: timeout', 'error: timeout', 'info: ok', 'error: timeout', 'error: timeout'];

$collapsed = Stream::of($logLines)
    ->distinctAdjacent()
    ->toArray();
// ['error: timeout', 'info: ok', 'error: timeout']
```

See also [Set::distinctAdjacent](set-iteration.md#distinct-adjacent).

#### Distinct Adjacent By
Return a stream that removes only consecutive duplicates by key, using a custom key function.

```$stream->distinctAdjacentBy(callable $keyFn): Stream```

* Each element's extracted key is compared strictly (`===`) to the previous element's key.
* Non-adjacent duplicate keys are kept.
* Runs in O(1) memory and calls `$keyFn` once per element.
* Source keys are discarded.

```php
use IterTools\Stream;

$words = ['apple', 'ant', 'banana', 'berry', 'apple'];

$firstLetterRuns = Stream::of($words)
    ->distinctAdjacentBy(fn ($s) => $s[0])
    ->toArray();
// ['apple', 'banana', 'apple']
```

```php
use IterTools\Stream;

$readings = [
    ['ts' => 60,  'v' => 1],
    ['ts' => 65,  'v' => 2],
    ['ts' => 119, 'v' => 3],
    ['ts' => 120, 'v' => 4],
    ['ts' => 121, 'v' => 5],
];

$compressed = Stream::of($readings)
    ->distinctAdjacentBy(fn ($r) => intdiv($r['ts'], 60))
    ->toArray();
// [['ts' => 60, 'v' => 1], ['ts' => 120, 'v' => 4]]
```

See also [Set::distinctAdjacentBy](set-iteration.md#distinct-adjacent-by).

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

#### Enumerate
Yield `[index, value]` pairs from the stream.

```$stream->enumerate(int $start = 0): Stream```

* The index is sequential starting from `$start`, independent of the source iterable's keys.
* Negative `$start` is allowed.

```php
use IterTools\Stream;

$seasons = ['spring', 'summer', 'autumn', 'winter'];

$result = Stream::of($seasons)
    ->enumerate()
    ->toArray();
// [[0, 'spring'], [1, 'summer'], [2, 'autumn'], [3, 'winter']]
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

#### Frequencies
Frequency distribution of the stream elements.

```$stream->frequencies(bool $strict = true): Stream```

```php
use IterTools\Stream;

$grades = ['A', 'A', 'B', 'B', 'B', 'C'];

$result = Stream::of($grades)
    ->frequencies()
    ->toAssociativeArray();

// ['A' => 2, 'B' => 3, 'C' => 1]
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
Return a stream intersecting the stream with the input iterables using [type coercion](../README.md#strict-and-coercive-types).

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

#### Intersperse
Return a stream with a separator inserted between consecutive elements.

```$stream->intersperse(mixed $separator): Stream```

* No separator is emitted before the first element or after the last element.
* The separator is yielded as-is on each pass: arrays are not expanded, objects retain identity.
* Source keys are discarded — the output is a list with sequential integer keys.

```php
use IterTools\Stream;

$flow = '';
foreach (Stream::of(['fetch', 'parse', 'validate', 'persist'])->intersperse(' -> ') as $part) {
    $flow .= $part;
}
// 'fetch -> parse -> validate -> persist'
```

```php
use IterTools\Stream;

$row = '';
foreach (Stream::of(['name', 'email', 'role'])->intersperse(',') as $part) {
    $row .= $part;
}
// 'name,email,role'
```

See also [Single::intersperse](single-iteration.md#intersperse).

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

#### Map Spread
Return a stream containing the result of mapping a function onto each element of the stream, unpacking each element positionally as arguments.

```$stream->mapSpread(callable $function): Stream```

* Each element of the stream must itself be iterable; its values are splatted into `$function` positionally.
* Inner keys are discarded — values flow positionally even when an inner element is an associative array.
* Outer keys are preserved (matching `Stream::map`).
* Throws `\InvalidArgumentException` if any inner element is not iterable.

```php
use IterTools\Stream;

$result = Stream::of([[1, 2], [3, 4], [5, 6]])
    ->mapSpread(fn ($a, $b) => $a + $b)
    ->toArray();
// [3, 7, 11]
```

```php
use IterTools\Stream;

$names  = ['Alice', 'Bob', 'Carol'];
$scores = [92, 87, 95];

$result = Stream::of($names)
    ->zipWith($scores)
    ->mapSpread(fn (string $name, int $score) => "{$name}: {$score}")
    ->toArray();
// ['Alice: 92', 'Bob: 87', 'Carol: 95']
```

See also [Single::mapSpread](single-iteration.md#map-spread).

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
Return a stream partially intersecting the stream with the input iterables using [type coercion](../README.md#strict-and-coercive-types).

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
use IterTools\Stream;

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

#### Relative Frequencies
Relative frequency distribution of the stream elements.

```$stream->relativeFrequencies(bool $strict = true): Stream```

```php
use IterTools\Stream;

$grades = ['A', 'A', 'B', 'B', 'B', 'C'];

$result = Stream::of($grades)
    ->relativeFrequencies()
    ->toAssociativeArray();

// A => 0.33, B => 0.5, C => 0.166
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

#### Round Robin With
Return a stream that yields one value at a time from the stream and the given iterables in round-robin order.

```$stream->roundRobinWith(iterable ...$iterables): Stream```

On each round, takes one value from each iterable that still has values; once an iterable is
exhausted, it is skipped in subsequent rounds. Iteration ends when every iterable is exhausted.
Unlike `zipWith`, values are yielded individually rather than as tuples. Source keys are discarded;
the output is sequentially re-indexed.

```php
use IterTools\Stream;

$result = Stream::of(['A', 'B', 'C'])
    ->roundRobinWith(['D', 'E'], ['F', 'G', 'H'])
    ->toArray();
// ['A', 'D', 'F', 'B', 'E', 'G', 'C', 'H']
```

Round-robin scheduling fairly drains items from worker queues of unequal size:
```php
$workerOne   = ['task-1', 'task-4', 'task-7'];
$workerTwo   = ['task-2', 'task-5'];
$workerThree = ['task-3', 'task-6', 'task-8', 'task-9'];

$schedule = Stream::of($workerOne)
    ->roundRobinWith($workerTwo, $workerThree)
    ->toArray();
// ['task-1', 'task-2', 'task-3', 'task-4', 'task-5', 'task-6', 'task-7', 'task-8', 'task-9']
```

See also [Multi::roundRobin](multi-iteration.md#roundrobin).

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

```$stream->skip(int $count, int $offset = 0): Stream```

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

#### Sort By
Sorts the stream using a key-extraction function (Schwartzian transform).

```$stream->sortBy(callable $keyFn)```

The key function is called exactly once per element. Source keys are discarded.
The sort is stable: elements with equal extracted keys preserve their original
relative order.

See also [`Sort::sortBy()`](sort-iteration.md#sortby).

```php
use IterTools\Stream;

$words = ['banana', 'fig', 'cherry', 'apple'];

$result = Stream::of($words)
    ->sortBy(fn (string $s) => \strlen($s))
    ->toArray();
// ['fig', 'apple', 'banana', 'cherry']
```

```php
use IterTools\Stream;

$people = [
    (object)['name' => 'Alice', 'age' => 30],
    (object)['name' => 'Bob',   'age' => 20],
    (object)['name' => 'Carol', 'age' => 40],
];

$names = Stream::of($people)
    ->sortBy(fn ($p) => $p->age)
    ->map(fn ($p) => $p->name)
    ->toArray();
// ['Bob', 'Alice', 'Carol']
```

#### Asort By
Sorts the stream using a key-extraction function (Schwartzian transform), maintaining keys.

```$stream->asortBy(callable $keyFn)```

The key function is called exactly once per element. Source keys are preserved.
The sort is stable: elements with equal extracted keys preserve their original
relative order.

See also [`Sort::asortBy()`](sort-iteration.md#asortby).

```php
use IterTools\Stream;

$scores = [
    'Alice' => 87,
    'Bob'   => 92,
    'Carol' => 75,
];

$result = Stream::of($scores)
    ->asortBy(fn (int $score) => $score)
    ->toAssociativeArray();
// ['Carol' => 75, 'Alice' => 87, 'Bob' => 92]
```

```php
use IterTools\Stream;

$people = [
    'alice' => (object)['age' => 30],
    'bob'   => (object)['age' => 20],
    'carol' => (object)['age' => 40],
];

$result = Stream::of($people)
    ->asortBy(fn ($p) => $p->age)
    ->toAssociativeArray();
// ['bob' => (object)['age' => 20], 'alice' => (object)['age' => 30], 'carol' => (object)['age' => 40]]
```

#### Largest
Reduces the stream to the n largest elements (descending order).

```$stream->largest(int $n, callable $keyFn = null)```

Uses a bounded heap of size `n` internally — the full stream is never sorted.
Stable under ties; skips NaN keys; throws `\InvalidArgumentException` for negative `$n`.

See also [`Sort::largest()`](sort-iteration.md#largest).

```php
use IterTools\Stream;

$data = [3, 1, 4, 1, 5, 9, 2, 6];

$result = Stream::of($data)
    ->largest(3)
    ->toArray();
// [9, 6, 5]
```

```php
use IterTools\Stream;

$leaderboard = [
    (object)['name' => 'Alice', 'score' => 87],
    (object)['name' => 'Bob',   'score' => 92],
    (object)['name' => 'Carol', 'score' => 75],
    (object)['name' => 'Dave',  'score' => 95],
    (object)['name' => 'Eve',   'score' => 90],
];

$result = Stream::of($leaderboard)
    ->largest(3, fn ($p) => $p->score)
    ->map(fn ($p) => $p->name)
    ->toArray();
// ['Dave', 'Bob', 'Eve']
```

#### Smallest
Reduces the stream to the n smallest elements (ascending order).

```$stream->smallest(int $n, callable $keyFn = null)```

Uses a bounded heap of size `n` internally — the full stream is never sorted.
Stable under ties; skips NaN keys; throws `\InvalidArgumentException` for negative `$n`.

See also [`Sort::smallest()`](sort-iteration.md#smallest).

```php
use IterTools\Stream;

$data = [3, 1, 4, 1, 5, 9, 2, 6];

$result = Stream::of($data)
    ->smallest(3)
    ->toArray();
// [1, 1, 2]
```

```php
use IterTools\Stream;

$requests = [
    (object)['id' => 'r1', 'durationMs' => 120],
    (object)['id' => 'r2', 'durationMs' => 50],
    (object)['id' => 'r3', 'durationMs' => 200],
    (object)['id' => 'r4', 'durationMs' => 80],
    (object)['id' => 'r5', 'durationMs' => 65],
];

$result = Stream::of($requests)
    ->smallest(3, fn ($r) => $r->durationMs)
    ->map(fn ($r) => $r->id)
    ->toArray();
// ['r2', 'r5', 'r4']
```

#### Difference With
Return a stream of the difference of the stream and the given iterables. Elements from the source not present in any given iterables.

```$stream->differenceWith(iterable ...$iterables): Stream```

Note: If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) difference rules apply.

```php
use IterTools\Stream;

$a = [1, 2, 3, 4, 7];
$b = [2, 3, 5, 8];
$c = [1, 6, 9];

$stream = Stream::of($a)
    ->differenceWith($b, $c)
    ->toArray();
// 4, 7
```

#### Difference Coercive With
Return a stream of the difference of the stream and the given iterables using [type coercion](../README.md#strict-and-coercive-types).

```$stream->differenceCoerciveWith(iterable ...$iterables): Stream```

Note: If input iterables produce duplicate items, then [multiset](https://en.wikipedia.org/wiki/Multiset) difference rules apply.

```php
use IterTools\Stream;

$a = [1, 2, 3, 4, 7];
$b = ['1', 2, 3, 5, 8];

$stream = Stream::of($a)
    ->differenceCoerciveWith($b)
    ->toArray();
// 4, 7
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
Return a stream of the symmetric difference of the stream and the given iterables using [type coercion](../README.md#strict-and-coercive-types).

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
Return a stream consisting of the union of the stream and the input iterables using [type coercion](../README.md#strict-and-coercive-types).

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

#### Unzip
Treat the stream itself as a sequence of rows and transpose into columns — the inverse of `zip`.

```$stream->unzip(): Stream```

Yields one column array per index up to the width of the shortest row. The outer stream and every row are fully consumed when the unzipped stream is iterated, before the first column can be yielded — column 0 cannot be emitted until every row's first cell is known.

```php
use IterTools\Stream;

$pairs = [[1, 'a'], [2, 'b'], [3, 'c']];

$columns = Stream::of($pairs)
    ->unzip()
    ->toArray();
// [[1, 2, 3], ['a', 'b', 'c']]
```

Splitting `(timestamp, value)` event tuples into two parallel series:

```php
use IterTools\Stream;

$events = [
    [1700000000, 12.5],
    [1700000060, 13.1],
    [1700000120, 12.9],
];

[$timestamps, $values] = Stream::of($events)
    ->unzip()
    ->toArray();
// $timestamps === [1700000000, 1700000060, 1700000120]
// $values     === [12.5, 13.1, 12.9]
```

#### Zip
Treat the stream itself as a sequence of iterables and zip them column-wise (transpose).

```$stream->zip(): Stream```

For uneven lengths, iteration stops when the shortest row is exhausted. Similar to Python's `zip(*rows)` idiom.

```php
use IterTools\Stream;

$rows = [[1, 2, 3], [4, 5, 6], [7, 8, 9]];

$stream = Stream::of($rows)
    ->zip()
    ->toArray();
// [[1, 4, 7], [2, 5, 8], [3, 6, 9]]
```

Pairs well with `chunkwise()` — chunk a flat stream into groups, then transpose:

```php
use IterTools\Stream;

$stream = Stream::of([1, 2, 3, 4, 5, 6])
    ->chunkwise(3)
    ->zip()
    ->toArray();
// [[1, 4], [2, 5], [3, 6]]
```

Pairs naturally with `toPartition()` — build tournament matchups by pairing the top half against the reversed bottom half (1 vs 8, 2 vs 7, 3 vs 6, 4 vs 5):

```php
use IterTools\Stream;

[$topHalf, $bottomHalf] = Stream::of([1, 2, 3, 4, 5, 6, 7, 8])
    ->toPartition(fn (int $seed): bool => $seed <= 4);

$matchups = Stream::of([$topHalf, array_reverse($bottomHalf)])
    ->zip()
    ->toArray();
// [[1, 8], [2, 7], [3, 6], [4, 5]]
```

Transpose a row-oriented table into columns — handy when records arrive row-by-row but you need each field as its own series:

```php
use IterTools\Stream;

$rows = [
    ['Alice', 30, 'NYC'],
    ['Bob',   25, 'LA'],
    ['Carol', 41, 'Austin'],
];

$columns = Stream::of($rows)
    ->zip()
    ->toArray();
// [['Alice', 'Bob', 'Carol'], [30, 25, 41], ['NYC', 'LA', 'Austin']]
```

The outer stream must be finite; it is consumed when the zipped stream is iterated, before the first tuple is yielded. Inner rows are advanced lazily after that. Passing the same iterator instance more than once is not supported and may not behave as expected.

#### Zip Longest
Treat the stream itself as a sequence of iterables and zip them column-wise (transpose), continuing until the longest row is exhausted.

```$stream->zipLongest(): Stream```

For uneven lengths, the exhausted rows will produce `null` for the remaining iterations. Similar to Python's `zip_longest(*rows)` idiom.

```php
use IterTools\Stream;

$rows = [[1, 2, 3], [4, 5]];

$stream = Stream::of($rows)
    ->zipLongest()
    ->toArray();
// [[1, 4], [2, 5], [3, null]]
```

Group month-by-month readings across years with uneven record lengths — short years surface as `null` gaps instead of dropping data:

```php
use IterTools\Stream;

$rainfallByYear = [
    [3.2, 4.1, 5.0, 6.2],           // 2022
    [2.8, 3.9, 4.7],                // 2023 — sensor outage mid-year
    [3.5, 4.3, 5.2, 6.8, 7.1],      // 2024
];

$byMonth = Stream::of($rainfallByYear)
    ->zipLongest()
    ->toArray();
// [[3.2, 2.8, 3.5], [4.1, 3.9, 4.3], [5.0, 4.7, 5.2], [6.2, null, 6.8], [null, null, 7.1]]
```

The outer stream must be finite; it is consumed when the zipped stream is iterated, before the first tuple is yielded. Inner rows are advanced lazily after that. Passing the same iterator instance more than once is not supported and may not behave as expected.

#### Zip Filled
Treat the stream itself as a sequence of iterables and zip them column-wise (transpose), continuing until the longest row is exhausted, using a filler value for missing entries.

```$stream->zipFilled(mixed $filler): Stream```

For uneven lengths, the exhausted rows will produce `$filler` for the remaining iterations.

```php
use IterTools\Stream;

$rows = [[1, 2, 3], [4, 5]];

$stream = Stream::of($rows)
    ->zipFilled('?')
    ->toArray();
// [[1, 4], [2, 5], [3, '?']]
```

Useful when a caller downstream wants a numeric default instead of `null` — e.g. quarterly sales figures across teams where missing quarters should count as zero for aggregation:

```php
use IterTools\Stream;

$salesByTeam = [
    [120, 150, 180, 210],   // Team A — full year
    [ 95, 110],             // Team B — onboarded H2
    [140, 160, 175],        // Team C — Q4 pending
];

$byQuarter = Stream::of($salesByTeam)
    ->zipFilled(0)
    ->toArray();
// [[120, 95, 140], [150, 110, 160], [180, 0, 175], [210, 0, 0]]
```

The outer stream must be finite; it is consumed when the zipped stream is iterated, before the first tuple is yielded. Inner rows are advanced lazily after that. Passing the same iterator instance more than once is not supported and may not behave as expected.

#### Zip Equal
Treat the stream itself as a sequence of iterables of equal lengths and zip them column-wise (transpose).

```$stream->zipEqual(): Stream```

Works like `Stream::zip()` but throws `\LengthException` if row lengths are not equal — i.e., at least one row ends before the others. Use this when equal lengths are a required invariant.

```php
use IterTools\Stream;

$rows = [[1, 2, 3], [4, 5, 6], [7, 8, 9]];

$stream = Stream::of($rows)
    ->zipEqual()
    ->toArray();
// [[1, 4, 7], [2, 5, 8], [3, 6, 9]]
```

A natural fit for CSV-style records where every row must have the same number of fields — transposing to columns surfaces a schema violation as a `\LengthException` instead of silently truncating or padding:

```php
use IterTools\Stream;

$records = [
    ['id', 'name', 'email'],
    [1,    'Alice', 'alice@example.com'],
    [2,    'Bob',   'bob@example.com'],
];

$byField = Stream::of($records)
    ->zipEqual()
    ->toArray();
// [['id', 1, 2], ['name', 'Alice', 'Bob'], ['email', 'alice@example.com', 'bob@example.com']]
```

The outer stream must be finite; it is consumed when the zipped stream is iterated, before the first tuple is yielded. Inner rows are advanced lazily after that. Passing the same iterator instance more than once is not supported and may not behave as expected.

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

#### Zip Filled With
Return a stream consisting of multiple iterable collections, using a default filler value if lengths no equal.

```$stream->zipFilledWith(mixed $default, iterable ...$iterables): Stream```

```php
use IterTools\Stream;

$input = [1, 2, 3];

$stream = Stream::of($input)
    ->zipFilledWith('?', ['A', 'B']);

foreach ($stream as $zipped) {
    // [1, A], [2, B], [3, ?]
}
```

#### Zip Equal With
Return a stream consisting of multiple iterable collections of equal lengths streamed simultaneously.

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

Defaults to [strict type](../README.md#strict-and-coercive-types) comparisons. Set strict to false for type coercion comparisons.

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

##### At Least N
Returns true if at least n items in the stream match the predicate function.

- Predicate is optional.
- Default predicate is the boolean value of each item.
- Short-circuits as soon as the count reaches n.
- `n <= 0` always returns true.

```$stream->atLeastN(int $n, callable $predicate = null): bool```

```php
use IterTools\Stream;

$grades         = [45, 50, 61, 72, 85];
$isPassingGrade = fn ($grade) => $grade >= 70;

$boolean = Stream::of($grades)->atLeastN(2, $isPassingGrade);
// true
```

##### At Most N
Returns true if at most n items in the stream match the predicate function.

- Predicate is optional.
- Default predicate is the boolean value of each item.
- Short-circuits as soon as the count exceeds n.
- `n < 0` always returns false.

```$stream->atMostN(int $n, callable $predicate = null): bool```

```php
use IterTools\Stream;

$attempts  = [false, false, true, false];
$isFailure = fn ($attempt) => $attempt === false;

$boolean = Stream::of($attempts)->atMostN(3, $isFailure);
// true
```

##### Are Permutations Coercive With
Returns true if all iterables are permutations with stream with [type coercion](../README.md#strict-and-coercive-types).

```$stream->arePermutationsCoerciveWith(...$iterables): bool```

```php
use IterTools\Summary;

$set2 = [2.0, '1', 3];
$set3 = [3, 2, 1];

$boolean = Stream::of([1, 2.0, '3'])
    ->arePermutationsCoerciveWith($set2, $set3);
// true
```

##### Contains
Returns true if the stream contains the needle using [strict-type comparison](../README.md#strict-and-coercive-types).

```$stream->contains(mixed $needle): bool```

- Scalars are compared strictly by type (`1` does not match `'1'`; `0` does not match `false`).
- Objects match only the same instance.
- Arrays are compared with `===`.
- `NaN` never matches `NaN`.
- Short-circuits on the first match.

```php
use IterTools\Stream;

$primes = [2, 3, 5, 7, 11, 13];

$boolean = Stream::of($primes)->contains(7);
// true

$boolean = Stream::of($primes)->contains('7');
// false (strict comparison)
```

##### Contains Coercive
Returns true if the stream contains the needle using [type coercion](../README.md#strict-and-coercive-types).

```$stream->containsCoercive(mixed $needle): bool```

- Scalars are compared non-strictly by value (`1` matches `'1'`; `0` matches `false`; `'1e2'` matches `100`).
- Objects are compared by serialized value (throws `\InvalidArgumentException` if needle or any visited datum is not serializable).
- Arrays are compared by serialized value.
- `NaN` matches `NaN`.
- Short-circuits on the first match.

```php
use IterTools\Stream;

$primes = [2, 3, 5, 7, 11, 13];

$boolean = Stream::of($primes)->containsCoercive('7');
// true (coercive comparison)
```

##### Ends With
Returns true if the stream ends with the given suffix using [strict-type comparison](../README.md#strict-and-coercive-types).

- Compares values pairwise; keys are ignored.
- Empty suffix returns true without consuming the stream.
- The stream must be finite.

```$stream->endsWith(iterable $suffix): bool```

```php
use IterTools\Stream;

$path = ['var', 'log', 'nginx', 'access.log'];

$boolean = Stream::of($path)->endsWith(['nginx', 'access.log']);
// true

$boolean = Stream::of($path)->endsWith(['error.log']);
// false
```

##### Ends With Coercive
Returns true if the stream ends with the given suffix using [type coercion](../README.md#strict-and-coercive-types).

- Compares values pairwise; keys are ignored.
- Empty suffix returns true without consuming the stream.
- The stream must be finite.
- Scalars are compared non-strictly by value, objects and arrays by serialized value, and `NaN` matches `NaN`.
- Throws `\InvalidArgumentException` if a non-serializable object is reached during comparison.

```$stream->endsWithCoercive(iterable $suffix): bool```

```php
use IterTools\Stream;

$digits = [1, 2, 3];

$boolean = Stream::of($digits)->endsWithCoercive(['2', '3']);
// true (coercive comparison)
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

##### Starts With
Returns true if the stream starts with the given prefix using [strict-type comparison](../README.md#strict-and-coercive-types).

- Compares values pairwise; keys are ignored.
- Empty prefix returns true without consuming the stream.

```$stream->startsWith(iterable $prefix): bool```

```php
use IterTools\Stream;

$path = ['var', 'log', 'nginx', 'access.log'];

$boolean = Stream::of($path)->startsWith(['var', 'log']);
// true

$boolean = Stream::of($path)->startsWith(['etc']);
// false
```

##### Starts With Coercive
Returns true if the stream starts with the given prefix using [type coercion](../README.md#strict-and-coercive-types).

- Compares values pairwise; keys are ignored.
- Empty prefix returns true without consuming the stream.
- Scalars are compared non-strictly by value, objects and arrays by serialized value, and `NaN` matches `NaN`.
- Throws `\InvalidArgumentException` if a non-serializable object is reached during comparison.

```$stream->startsWithCoercive(iterable $prefix): bool```

```php
use IterTools\Stream;

$digits = [1, 2, 3];

$boolean = Stream::of($digits)->startsWithCoercive(['1', '2']);
// true (coercive comparison)
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

##### To First Match
Reduces iterable source to the first element matching the predicate.

```$stream->toFirstMatch(callable $predicate, mixed $default = null): mixed```

- Predicate return value is coerced via `(bool)` cast.
- Short-circuits on the first match.
- Returns `$default` (null by default) if no element matches.

```php
use IterTools\Stream;

$numbers = [1, 3, 5, 6, 7, 8];

$result = Stream::of($numbers)
    ->toFirstMatch(fn (int $n) => $n % 2 === 0);
// 6
```

##### To First Match Index
Reduces iterable source to the zero-based position of the first element matching the predicate.

```$stream->toFirstMatchIndex(callable $predicate, mixed $default = null): mixed```

- Predicate return value is coerced via `(bool)` cast.
- Short-circuits on the first match.
- Returns `$default` (null by default) if no element matches.

```php
use IterTools\Stream;

$numbers = [10, 20, 30, 40];

$result = Stream::of($numbers)
    ->toFirstMatchIndex(fn (int $n) => $n > 25);
// 2
```

##### To First Match Key
Reduces iterable source to the source key of the first element matching the predicate.

```$stream->toFirstMatchKey(callable $predicate, mixed $default = null): mixed```

- Predicate return value is coerced via `(bool)` cast.
- Short-circuits on the first match.
- Returns `$default` (null by default) if no element matches.

```php
use IterTools\Stream;

$users = ['alice' => 12, 'bob' => 17, 'carol' => 22, 'dan' => 30];

$result = Stream::of($users)
    ->toFirstMatchKey(fn (int $age) => $age >= 18);
// 'carol'
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

##### To Last Match
Reduces iterable source to the last element matching the predicate.

```$stream->toLastMatch(callable $predicate, mixed $default = null): mixed```

- Predicate return value is coerced via `(bool)` cast.
- Consumes the entire iterable.
- Returns `$default` (null by default) if no element matches.

```php
use IterTools\Stream;

$numbers = [1, 3, 5, 6, 7, 8, 9];

$result = Stream::of($numbers)
    ->toLastMatch(fn (int $n) => $n % 2 === 0);
// 8
```

##### To Last Match Index
Reduces iterable source to the zero-based position of the last element matching the predicate.

```$stream->toLastMatchIndex(callable $predicate, mixed $default = null): mixed```

- Predicate return value is coerced via `(bool)` cast.
- Consumes the entire iterable.
- Returns `$default` (null by default) if no element matches.

```php
use IterTools\Stream;

$numbers = [10, 20, 30, 40, 5];

$result = Stream::of($numbers)
    ->toLastMatchIndex(fn (int $n) => $n > 25);
// 3
```

##### To Last Match Key
Reduces iterable source to the source key of the last element matching the predicate.

```$stream->toLastMatchKey(callable $predicate, mixed $default = null): mixed```

- Predicate return value is coerced via `(bool)` cast.
- Consumes the entire iterable.
- Returns `$default` (null by default) if no element matches.

```php
use IterTools\Stream;

$users = ['alice' => 12, 'bob' => 17, 'carol' => 22, 'dan' => 30];

$result = Stream::of($users)
    ->toLastMatchKey(fn (int $age) => $age >= 18);
// 'dan'
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

##### To Only
Reduces iterable source to its sole element.

```$stream->toOnly(): mixed```

- Throws `\LengthException` if the stream is empty or contains more than one element.
- Compose with `filter()` to assert that exactly one item matches a predicate.

```php
use IterTools\Stream;

$result = Stream::of([1, 2, 3, 4, 5])
    ->filter(fn (int $n) => $n === 3)
    ->toOnly();
// 3
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

##### To Partition
Partitions the stream into two lists based on a predicate.

Returns a two-element list array: `[truthyValues, falsyValues]`. Both output arrays are reindexed (list arrays); source keys are discarded. Predicate return value is coerced via `(bool)` cast.

```$stream->toPartition(callable $predicate): array```

```php
use IterTools\Stream;

[$evens, $odds] = Stream::of([1, 2, 3, 4, 5, 6])
    ->toPartition(fn (int $n): bool => $n % 2 === 0);
// $evens: [2, 4, 6]
// $odds:  [1, 3, 5]
```

Since both halves are returned together, `toPartition` pairs naturally with operations that consume two lists. For example, tournament seeding — partition seeds into top and bottom halves, then pair top vs reversed bottom to build matchups:

```php
use IterTools\Stream;

[$topHalf, $bottomHalf] = Stream::of([1, 2, 3, 4, 5, 6, 7, 8])
    ->toPartition(fn (int $seed): bool => $seed <= 4);

$matchups = Stream::of($topHalf)
    ->zipWith(array_reverse($bottomHalf))
    ->toArray();
// [[1, 8], [2, 7], [3, 6], [4, 5]]
```

Or, treating both halves as rows of a single stream and transposing them with [`zip`](#zip):

```php
use IterTools\Stream;

[$topHalf, $bottomHalf] = Stream::of([1, 2, 3, 4, 5, 6, 7, 8])
    ->toPartition(fn (int $seed): bool => $seed <= 4);

$matchups = Stream::of([$topHalf, array_reverse($bottomHalf)])
    ->zip()
    ->toArray();
// [[1, 8], [2, 7], [3, 6], [4, 5]]
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

##### Consume
Drains the stream, discarding values.

Useful for forcing evaluation of a lazy pipeline whose only purpose is its side effects (for example, a side-effectful `map()`).

```$stream->consume(): void```

```php
use IterTools\Stream;

$log = [];

$pipeline = Stream::of([1, 2, 3])
    ->map(function (int $n) use (&$log): int {
        $log[] = $n;
        return $n * 2;
    });
// $log === []  (map is lazy — nothing has run yet)

$pipeline->consume();
// $log === [1, 2, 3]
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

### Stream Debug Operations
#### Peek
Peek at each element between other Stream operations to do some action without modifying the stream.

```$stream->peek(callable $callback): Stream```

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

```$stream->peekStream(callable $callback): Stream```

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

```$stream->peekPrint(string $separator = '', string $prefix = '', string $suffix = ''): void```

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

##### Print R
`print_r` each item in the stream.

```$stream->printR(): void```

```php
use IterTools\Stream;

$items = [$string, $array, $object];

Stream::of($words)->printR();
// print_r output
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


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


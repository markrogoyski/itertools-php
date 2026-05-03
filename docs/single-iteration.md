# Single Iteration

[Back to main README](../README.md)

Tools for iterating and transforming a single collection.

---

### Accumulate
Accumulate the running result of applying a binary operator across an iterable.

```Single::accumulate(iterable $data, callable $op, mixed ...$initial)```

* Without an initial value: the first yielded element is the first datum unchanged, and each subsequent element is `$op(accumulator, nextDatum)`.
* With an initial value: the first yielded element is the initial value, and each subsequent element is `$op(accumulator, nextDatum)`.
* Explicit `null` is a legitimate initial value (the variadic sentinel distinguishes "no initial" from "null initial"; this diverges from `Math::running*`, where `null` means "no initial").
* Throws `\InvalidArgumentException` if more than one initial value is passed.

```php
use IterTools\Single;

$numbers = [1, 2, 3, 4, 5];

foreach (Single::accumulate($numbers, fn ($a, $b) => $a + $b) as $runningSum) {
    print($runningSum . ' ');
}
// 1 3 6 10 15

foreach (Single::accumulate($numbers, fn ($a, $b) => $a + $b, 100) as $runningSum) {
    print($runningSum . ' ');
}
// 100 101 103 106 110 115
```

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

### Enumerate
Yield `[index, value]` pairs from the iterable.

```Single::enumerate(iterable $data, int $start = 0)```

* The index is sequential starting from `$start`, independent of the source iterable's keys.
* Negative `$start` is allowed.

```php
use IterTools\Single;

$seasons = ['spring', 'summer', 'autumn', 'winter'];

foreach (Single::enumerate($seasons) as [$index, $season]) {
    print("$index: $season" . \PHP_EOL);
}
// 0: spring
// 1: summer
// 2: autumn
// 3: winter
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

### Intersperse
Insert a separator between consecutive elements of an iterable.

```Single::intersperse(iterable $data, mixed $separator)```

* Yields: element, separator, element, separator, …, element.
* No separator is emitted before the first element or after the last element.
* The separator is yielded as-is on each pass: arrays are not expanded, objects retain identity.
* Source keys are discarded — the output is a list with sequential integer keys.

```php
use IterTools\Single;

$pipelineStages = ['fetch', 'parse', 'validate', 'persist'];

$flow = '';
foreach (Single::intersperse($pipelineStages, ' -> ') as $part) {
    $flow .= $part;
}
// 'fetch -> parse -> validate -> persist'
```

```php
use IterTools\Single;

$cells = ['name', 'email', 'role'];

$row = '';
foreach (Single::intersperse($cells, ',') as $part) {
    $row .= $part;
}
// 'name,email,role'
```

See also [Stream::intersperse](stream.md#intersperse).

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

### Map Spread
Map a function onto each element, unpacking each element positionally as arguments.

```Single::mapSpread(iterable $data, callable $function)```

* Each element of `$data` must itself be iterable; its values are splatted into `$function` positionally.
* Inner keys are discarded — values flow positionally even when an inner element is an associative array.
* Outer keys are preserved (matching `Single::map`).
* Throws `\InvalidArgumentException` if any inner element is not iterable.

```php
use IterTools\Single;

$pairs = [[1, 2], [3, 4], [5, 6]];

foreach (Single::mapSpread($pairs, fn ($a, $b) => $a + $b) as $sum) {
    print($sum);
}
// 3, 7, 11
```

```php
use IterTools\Multi;
use IterTools\Single;

$names  = ['Alice', 'Bob', 'Carol'];
$scores = [92, 87, 95];

$lines = Single::mapSpread(
    Multi::zip($names, $scores),
    fn (string $name, int $score) => "{$name}: {$score}"
);

foreach ($lines as $line) {
    print($line);
}
// 'Alice: 92', 'Bob: 87', 'Carol: 95'
```

See also [Stream::mapSpread](stream.md#map-spread).

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

### Group Adjacent By
Group adjacent elements that share a key returned by `$keyFn`.

```Single::groupAdjacentBy(iterable $data, callable $keyFn)```

* Yields `[groupKey, list<value>]` pairs sequentially (not associatively).
* Repeated keys appearing in non-adjacent runs produce **separate** groups (unlike `groupBy`).
* Source keys are discarded; outer is sequential, inner groups are list arrays.

```php
use IterTools\Single;

$readings = [1, 1, 2, 2, 1, 3];

foreach (Single::groupAdjacentBy($readings, fn ($x) => $x) as [$key, $run]) {
    print($key . ': ' . \implode(',', $run) . PHP_EOL);
}
// 1: 1,1
// 2: 2,2
// 1: 1
// 3: 3
```

### Pad Left
Pad an iterable on the left so its yielded length is at least `$length`.

```Single::padLeft(iterable $data, int $length, mixed $fill)```

* If the source is already `$length` or longer, all elements pass through unchanged (no truncation).
* Source keys are discarded; output keys are sequential 0-indexed.
* Throws `\InvalidArgumentException` if `$length` is negative.

```php
use IterTools\Single;

$values = [1, 2, 3];

foreach (Single::padLeft($values, 5, 0) as $value) {
    print($value);
}
// 0, 0, 1, 2, 3
```

### Pad Right
Pad an iterable on the right so its yielded length is at least `$length`.

```Single::padRight(iterable $data, int $length, mixed $fill)```

* If the source is already `$length` or longer, all elements pass through unchanged (no truncation).
* Source keys are discarded; output keys are sequential 0-indexed.
* Throws `\InvalidArgumentException` if `$length` is negative.

```php
use IterTools\Single;

$values = [1, 2, 3];

foreach (Single::padRight($values, 5, 0) as $value) {
    print($value);
}
// 1, 2, 3, 0, 0
```

### Split When
Split an iterable into groups, starting a new group every time `$predicate` matches.

```Single::splitWhen(iterable $data, callable $predicate)```

* The matching element starts the next group (it is the first element of that group).
* No leading empty group is yielded if the predicate matches the first element.
* Empty input yields nothing.
* Source keys are discarded; outer is sequential, inner groups are list arrays.

```php
use IterTools\Single;

$values = [1, 2, 0, 3, 0, 4];

foreach (Single::splitWhen($values, fn ($x) => $x === 0) as $group) {
    print(\implode(',', $group) . PHP_EOL);
}
// 1,2
// 0,3
// 0,4
```

<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\ResourcePolicy;

/**
 * Provides fluent interface for working with iterables.
 *
 * @implements \IteratorAggregate<mixed>
 */
final class Stream implements \IteratorAggregate
{
    /**
     * @var iterable<mixed> iterable source
     */
    protected iterable $iterable;

    /**
     * @param iterable<mixed> $iterable
     */
    private function __construct(iterable $iterable)
    {
        $this->iterable = $iterable;
    }

    // STREAM SOURCES

    /**
     * Creates iterable instance with fluent interface.
     *
     * @param iterable<mixed> $iterable
     *
     * @return Stream
     */
    public static function of(iterable $iterable): self
    {
        return new self($iterable);
    }

    /**
     * Creates iterable instance with fluent interface of random coin flips.
     *
     * @param int $repetitions
     * @param \Random\Engine|null $engine
     *
     * @return Stream
     *
     * @see Random::coinFlip()
     */
    public static function ofCoinFlips(int $repetitions, ?\Random\Engine $engine = null): self
    {
        return new self(Random::coinFlip($repetitions, $engine));
    }

    /**
     * Creates iterable instance with fluent interface from empty iterable source.
     *
     * @return Stream
     */
    public static function ofEmpty(): self
    {
        return new self([]);
    }

    /**
     * Creates iterable instance with fluent interface of random selections from an array of values.
     *
     * @param array<mixed> $items
     * @param int     $repetitions
     * @param \Random\Engine|null $engine
     *
     * @return Stream
     *
     * @see Random::choice()
     */
    public static function ofRandomChoice(array $items, int $repetitions, ?\Random\Engine $engine = null): self
    {
        return new self(Random::choice($items, $repetitions, $engine));
    }

    /**
     * Creates iterable instance with fluent interface of random numbers (integers).
     *
     * @param int $min
     * @param int $max
     * @param int $repetitions
     * @param \Random\Engine|null $engine
     *
     * @return Stream
     *
     * @see Random::number()
     */
    public static function ofRandomNumbers(int $min, int $max, int $repetitions, ?\Random\Engine $engine = null): self
    {
        return new self(Random::number($min, $max, $repetitions, $engine));
    }

    /**
     * Creates iterable instance with fluent interface of random percentages between 0 and 1.
     *
     * @param int $repetitions
     * @param \Random\Engine|null $engine
     *
     * @return Stream
     *
     * @see Random::percentage()
     */
    public static function ofRandomPercentage(int $repetitions, ?\Random\Engine $engine = null): self
    {
        return new self(Random::percentage($repetitions, $engine));
    }

    /**
     * @param int|float|string $start First value of sequence
     * @param int|float|string $end Sequence ends upon reaching this value
     * @param int|float        $step (optional) Step increase between values. Defaults to 1.
     *
     * @return Stream
     */
    public static function ofRange(int|float|string $start, int|float|string $end, int|float $step = 1): self
    {
        if (!\is_numeric($start) || !\is_numeric($end)) {
            throw new \InvalidArgumentException(
                'Stream::ofRange values must be numeric, got: ' .
                \print_r(['start' => $start, 'end' => $end, 'step' => $step], true)
            );
        }

        return new self(\range($start, $end, $step));
    }

    /**
     * Creates iterable instance with fluent interface of rock-paper-scissors hands.
     *
     * @param int $repetitions
     * @param \Random\Engine|null $engine
     *
     * @return Stream
     *
     * @see Random::rockPaperScissors()
     */
    public static function ofRockPaperScissors(int $repetitions, ?\Random\Engine $engine = null): self
    {
        return new self(Random::rockPaperScissors($repetitions, $engine));
    }

    /**
     * Create a stream sourced from the lines of a file.
     *
     * @param resource $file File handle stream opened for reading
     *
     * @return Stream
     *
     * @see File::readLines()
     */
    public static function ofFileLines(mixed $file): self
    {
        return new self(File::readLines($file));
    }

    /**
     * Create a stream sourced from a CSV file.
     *
     * @param resource $file  File handle stream opened for reading
     * @param string $separator
     * @param string $enclosure
     * @param string $escape
     *
     * @return Stream
     *
     * @see File::readCsv()
     */
    public static function ofCsvFile(
        mixed $file,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ): self {
        return new self(File::readCsv($file, $separator, $enclosure, $escape));
    }

    // STREAM OPERATIONS

    /**
     * Accumulate the running result of applying a binary operator across the stream.
     *
     * With no initial value, the first yielded element is the first datum unchanged and each
     * subsequent element is $op(accumulator, nextDatum).
     *
     * With one initial value, the first yielded element is the initial value and each
     * subsequent element is $op(accumulator, nextDatum). Explicit null is a legitimate
     * initial value.
     *
     * @param callable(mixed, mixed): mixed $op
     * @param mixed                         ...$initial (Optional) zero or one initial values.
     *
     * @return Stream
     *
     * @throws \InvalidArgumentException if more than one initial value is passed.
     *
     * @see Single::accumulate()
     */
    public function accumulate(callable $op, mixed ...$initial): self
    {
        $this->iterable = Single::accumulate($this->iterable, $op, ...$initial);
        return $this;
    }

    /**
     * Compress an iterable source by filtering out data that is not selected.
     *
     * Selectors indicate which data. True value selects item. False value filters out data.
     *
     * @param iterable<bool> $selectors
     *
     * @return Stream
     *
     * @see Single::compress()
     */
    public function compress(iterable $selectors): self
    {
        $this->iterable = Single::compress($this->iterable, $selectors);
        return $this;
    }

    /**
     * Return elements from the iterable source only by given keys.
     *
     * Iterable source must contain only integer or string keys.
     *
     * Array of keys must contain only integer or string items.
     *
     * @param array<int|string> $keys
     *
     * @return Stream
     *
     * @see Single::compressAssociative()
     */
    public function compressAssociative(array $keys): self
    {
        /** @var iterable<int|string, mixed> $source */
        $source = $this->iterable;
        $this->iterable = Single::compressAssociative($source, $keys);
        return $this;
    }

    /**
     * Return elements indexed by callback-function.
     *
     * @param callable(mixed $value, mixed $key): scalar $indexer
     *
     * @return Stream
     *
     * @see Single::reindex()
     */
    public function reindex(callable $indexer): self
    {
        $this->iterable = Single::reindex($this->iterable, $indexer);
        return $this;
    }

    /**
     * Drop elements from the iterable source while the predicate function is true.
     *
     * Once the predicate function returns false once, all remaining elements are returned.
     *
     * @param callable $predicate
     *
     * @return Stream
     *
     * @see Single::dropWhile()
     */
    public function dropWhile(callable $predicate): self
    {
        $this->iterable = Single::dropWhile($this->iterable, $predicate);
        return $this;
    }

    /**
     * Yield [index, value] pairs from the stream.
     *
     * The index is sequential starting from $start, independent of the source iterable's keys.
     *
     * Negative $start is allowed.
     *
     * @param int $start
     *
     * @return Stream
     *
     * @see Single::enumerate()
     */
    public function enumerate(int $start = 0): self
    {
        $this->iterable = Single::enumerate($this->iterable, $start);
        return $this;
    }

    /**
     * Return elements from the iterable source as long as the predicate is true.
     *
     * If no predicate is provided, the boolean value of the data is used.
     *
     * @param callable $predicate
     *
     * @return Stream
     *
     * @see Single::takeWhile()
     */
    public function takeWhile(callable $predicate): self
    {
        $this->iterable = Single::takeWhile($this->iterable, $predicate);
        return $this;
    }

    /**
     * Filter out elements from the iterable source only returning elements where there predicate function is true.
     *
     * @param callable $predicate
     *
     * @return Stream
     *
     * @see Single::filterTrue()
     */
    public function filter(callable $predicate): self
    {
        return $this->filterTrue($predicate);
    }

    /**
     * Filter the stream to only elements that are naturally true.
     *
     * If predicate is provided, filters iterable to only elements where predicate is true.
     *
     * @param callable|null $predicate
     *
     * @return Stream
     *
     * @see Single::filterTrue()
     */
    public function filterTrue(?callable $predicate = null): self
    {
        $this->iterable = Single::filterTrue($this->iterable, $predicate);
        return $this;
    }

    /**
     * Filter the stream to only elements that are naturally false.
     *
     * If predicate is provided, filters iterable to only elements where predicate is false.
     *
     * @param callable|null $predicate
     *
     * @return Stream
     *
     * @see Single::filterFalse()
     */
    public function filterFalse(?callable $predicate = null): self
    {
        $this->iterable = Single::filterFalse($this->iterable, $predicate);
        return $this;
    }

    /**
     * Filter out elements from the iterable only returning elements for which keys the predicate function is true.
     *
     * @param callable $predicate
     *
     * @return Stream
     *
     * @see Single::filterKeys()
     */
    public function filterKeys(callable $predicate): self
    {
        $this->iterable = Single::filterKeys($this->iterable, $predicate);
        return $this;
    }

    /**
     * Group iterable source by a common data element.
     *
     * The groupKeyFunction determines the key (or multiple keys) to group elements by.
     *
     * The itemKeyFunction (optional) determines the key of element in group.
     *
     * @param callable $groupKeyFunction
     * @param callable|null $itemKeyFunction
     *
     * @return Stream
     *
     * @see Single::groupBy()
     */
    public function groupBy(callable $groupKeyFunction, ?callable $itemKeyFunction = null): self
    {
        $this->iterable = Single::groupBy($this->iterable, $groupKeyFunction, $itemKeyFunction);
        return $this;
    }

    /**
     * Return pairs of elements from iterable source.
     *
     * Returns empty generator if given collection contains less than 2 elements.
     *
     * @return Stream
     *
     * @see Single::pairwise()
     */
    public function pairwise(): self
    {
        $this->iterable = Single::pairwise($this->iterable);
        return $this;
    }

    /**
     * Return chunks of elements from iterable source.
     *
     * Chunk size must be at least 1.
     *
     * @param int $chunkSize
     *
     * @return Stream
     *
     * @see Single::chunkwise()
     */
    public function chunkwise(int $chunkSize): self
    {
        $this->iterable = Single::chunkwise($this->iterable, $chunkSize);
        return $this;
    }

    /**
     * Return overlapped chunks of elements from iterable source.
     *
     * Chunk size must be at least 1.
     *
     * Overlap size must be less than chunk size.
     *
     * @param int $chunkSize
     * @param int $overlapSize
     * @param bool $includeIncompleteTail
     *
     * @return Stream
     *
     * @see Single::chunkwiseOverlap()
     */
    public function chunkwiseOverlap(int $chunkSize, int $overlapSize, bool $includeIncompleteTail = true): self
    {
        $this->iterable = Single::chunkwiseOverlap($this->iterable, $chunkSize, $overlapSize, $includeIncompleteTail);
        return $this;
    }

    /**
     * Limit iteration to a max size limit
     *
     * @param int $limit
     *
     * @return Stream
     *
     * @see Single::limit
     */
    public function limit(int $limit): self
    {
        $this->iterable = Single::limit($this->iterable, $limit);
        return $this;
    }

    /**
     * Map a function onto every element of the stream
     *
     * @param callable $func
     *
     * @return Stream
     *
     * @see Single::map
     */
    public function map(callable $func): self
    {
        $this->iterable = Single::map($this->iterable, $func);
        return $this;
    }

    /**
     * Map a function onto every element of the stream, unpacking each element positionally as arguments.
     *
     * @param callable $function
     *
     * @return Stream
     *
     * @see Single::mapSpread()
     */
    public function mapSpread(callable $function): self
    {
        $this->iterable = Single::mapSpread($this->iterable, $function);
        return $this;
    }

    /**
     * Returns a new collection formed by applying a given callback function
     * to each element of the stream, and then flattening the result by one level.
     *
     * See Single::flatMap() for the recursive-flatMap pattern using the mapper's second argument.
     *
     * @param callable(mixed, callable): mixed $func
     *
     * @return Stream
     *
     * @see Single::flatMap()
     */
    public function flatMap(callable $func): self
    {
        $this->iterable = Single::flatMap($this->iterable, $func);
        return $this;
    }

    /**
     * Flatten a stream.
     *
     * @param int $dimensions
     *
     * @return Stream
     *
     * @see Single::flatten()
     */
    public function flatten(int $dimensions = 1): self
    {
        $this->iterable = Single::flatten($this->iterable, $dimensions);
        return $this;
    }

    /**
     * Insert a separator between consecutive elements of the stream.
     *
     * @param mixed $separator
     *
     * @return Stream
     *
     * @see Single::intersperse()
     */
    public function intersperse(mixed $separator): self
    {
        $this->iterable = Single::intersperse($this->iterable, $separator);
        return $this;
    }

    /**
     * Extract a slice of the stream.
     *
     * @param int<0, max> $start
     * @param int<0, max>|null $count
     * @param positive-int $step
     *
     * @return Stream
     *
     * @see Single::slice()
     */
    public function slice(int $start = 0, ?int $count = null, int $step = 1): self
    {
        $this->iterable = Single::slice($this->iterable, $start, $count, $step);
        return $this;
    }

    /**
     * Skip the n elements in the stream after offset.
     *
     * @param int<0, max> $count
     * @param int<0, max> $offset
     *
     * @return Stream
     *
     * @see Single::skip()
     */
    public function skip(int $count, int $offset = 0): self
    {
        $this->iterable = Single::skip($this->iterable, $count, $offset);
        return $this;
    }

    /**
     * Returns a frequency distribution of stream elements
     * showing how often each different value in the collection occurs.
     *
     * @param bool $strict
     *
     * @return $this
     */
    public function frequencies(bool $strict = true): self
    {
        $this->iterable = Math::frequencies($this->iterable, $strict);
        return $this;
    }

    /**
     * Returns a relative frequency distribution of stream elements
     * showing how often each different value in the collection occurs.
     *
     * @param bool $strict
     *
     * @return $this
     */
    public function relativeFrequencies(bool $strict = true): self
    {
        $this->iterable = Math::relativeFrequencies($this->iterable, $strict);
        return $this;
    }

    /**
     * Sorts the stream.
     *
     * If comparator is null, then elements of the iterable source must be comparable.
     *
     * @param (callable(mixed, mixed):int)|null $comparator (optional) function to determine how to sort elements if default sort is not appropriate.
     *
     * @return Stream
     *
     * @see Single::sort()
     */
    public function sort(?callable $comparator = null): self
    {
        $this->iterable = Sort::sort($this->iterable, $comparator);
        return $this;
    }

    /**
     * Sorts the stream, maintaining index key associations
     *
     * If comparator is null, then elements of the iterable source must be comparable.
     *
     * Note: When the iterable yields duplicate keys (e.g. a generator using `yield $value`
     * without explicit keys), later values silently overwrite earlier ones because keys must
     * be unique in the resulting array. Use sort() instead when key preservation is not needed.
     *
     * @param (callable(mixed, mixed):int)|null $comparator (optional) function to determine how to sort elements if default sort is not appropriate.
     *
     * @return Stream
     *
     * @see Sort::asort()
     */
    public function asort(?callable $comparator = null): self
    {
        $this->iterable = Sort::asort($this->iterable, $comparator);
        return $this;
    }

    /**
     * Sorts the stream using a key-extraction function (Schwartzian transform).
     *
     * The key function is called exactly once per element. Source keys are discarded.
     *
     * @param callable(mixed):mixed $keyFn function used to extract the comparison key from each element.
     *
     * @return Stream
     *
     * @see Sort::sortBy()
     */
    public function sortBy(callable $keyFn): self
    {
        $this->iterable = Sort::sortBy($this->iterable, $keyFn);
        return $this;
    }

    /**
     * Sorts the stream using a key-extraction function (Schwartzian transform), preserving key associations.
     *
     * The key function is called exactly once per element. Source keys are preserved.
     *
     * @param callable(mixed):mixed $keyFn function used to extract the comparison key from each element.
     *
     * @return Stream
     *
     * @see Sort::asortBy()
     */
    public function asortBy(callable $keyFn): self
    {
        $this->iterable = Sort::asortBy($this->iterable, $keyFn);
        return $this;
    }

    /**
     * Reduces the stream to the n largest elements (descending order).
     *
     * Uses a bounded heap of size n internally — the full stream is never sorted.
     *
     * @param int                        $n     number of largest elements to keep (must be non-negative).
     * @param callable(mixed):mixed|null $keyFn (optional) function used to extract the comparison key from each element.
     *
     * @return Stream
     *
     * @throws \InvalidArgumentException if $n is negative.
     *
     * @see Sort::largest()
     */
    public function largest(int $n, ?callable $keyFn = null): self
    {
        $this->iterable = Sort::largest($this->iterable, $n, $keyFn);
        return $this;
    }

    /**
     * Reduces the stream to the n smallest elements (ascending order).
     *
     * Uses a bounded heap of size n internally — the full stream is never sorted.
     *
     * @param int                        $n     number of smallest elements to keep (must be non-negative).
     * @param callable(mixed):mixed|null $keyFn (optional) function used to extract the comparison key from each element.
     *
     * @return Stream
     *
     * @throws \InvalidArgumentException if $n is negative.
     *
     * @see Sort::smallest()
     */
    public function smallest(int $n, ?callable $keyFn = null): self
    {
        $this->iterable = Sort::smallest($this->iterable, $n, $keyFn);
        return $this;
    }

    /**
     * Randomize the order of the stream.
     *
     * Materializes the stream. Source keys are discarded; output keys are sequential 0-indexed.
     *
     * @param \Random\Engine|null $engine optional Random engine (defaults to PHP default).
     *
     * @return Stream
     *
     * @see Sort::shuffle()
     */
    public function shuffle(?\Random\Engine $engine = null): self
    {
        $this->iterable = Sort::shuffle($this->iterable, $engine);
        return $this;
    }

    /**
     * Sample $size elements from the stream without replacement.
     *
     * Every input position is used at most once; duplicate values are allowed. Materializes
     * the stream. Output keys are sequential 0-indexed.
     *
     * @param int                 $size   number of elements to draw (0 ≤ $size ≤ stream length).
     * @param \Random\Engine|null $engine optional Random engine.
     *
     * @return Stream
     *
     * @throws \InvalidArgumentException if $size is negative.
     * @throws \LengthException          if $size exceeds the stream length.
     *
     * @see Random::sample()
     */
    public function sample(int $size, ?\Random\Engine $engine = null): self
    {
        $this->iterable = Random::sample($this->iterable, $size, $engine);
        return $this;
    }

    /**
     * Reverse the iterable source.
     *
     * @return Stream
     *
     * @see Single::reverse()
     */
    public function reverse(): self
    {
        $this->iterable = Single::reverse($this->iterable);
        return $this;
    }

    /**
     * Split the stream into groups, starting a new group every time $predicate matches.
     *
     * Source keys are discarded; outer is sequential, inner groups are list arrays.
     *
     * @param callable(mixed):bool $predicate
     *
     * @return Stream
     *
     * @see Single::splitWhen()
     */
    public function splitWhen(callable $predicate): self
    {
        $this->iterable = Single::splitWhen($this->iterable, $predicate);
        return $this;
    }

    /**
     * Group adjacent elements that share a key returned by $keyFn.
     *
     * Yields [groupKey, list<value>] pairs sequentially. Repeated keys appearing in
     * non-adjacent runs produce separate groups.
     *
     * @param callable(mixed):mixed $keyFn
     *
     * @return Stream
     *
     * @see Single::groupAdjacentBy()
     */
    public function groupAdjacentBy(callable $keyFn): self
    {
        $this->iterable = Single::groupAdjacentBy($this->iterable, $keyFn);
        return $this;
    }

    /**
     * Pad the stream on the left so its yielded length is at least $length.
     *
     * If the stream is already $length or longer, all elements pass through unchanged.
     * Source keys are discarded; output keys are sequential 0-indexed.
     *
     * @param int   $length minimum final length (must be non-negative)
     * @param mixed $fill   value used to pad
     *
     * @return Stream
     *
     * @throws \InvalidArgumentException if $length is negative.
     *
     * @see Single::padLeft()
     */
    public function padLeft(int $length, mixed $fill): self
    {
        $this->iterable = Single::padLeft($this->iterable, $length, $fill);
        return $this;
    }

    /**
     * Pad the stream on the right so its yielded length is at least $length.
     *
     * If the stream is already $length or longer, all elements pass through unchanged.
     * Source keys are discarded; output keys are sequential 0-indexed.
     *
     * @param int   $length minimum final length (must be non-negative)
     * @param mixed $fill   value used to pad
     *
     * @return Stream
     *
     * @throws \InvalidArgumentException if $length is negative.
     *
     * @see Single::padRight()
     */
    public function padRight(int $length, mixed $fill): self
    {
        $this->iterable = Single::padRight($this->iterable, $length, $fill);
        return $this;
    }

    /**
     * Chain iterable source withs given iterables together into a single iteration.
     *
     * Makes a single continuous sequence out of multiple sequences.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return Stream
     *
     * @see Multi::chain()
     */
    public function chainWith(iterable ...$iterables): self
    {
        $this->iterable = Multi::chain($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Prepend variadic values to the front of the stream.
     *
     * For inserting an entire iterable, use chainWith. Output keys are reindexed
     * sequentially across the entire stream. Empty variadic leaves the stream unchanged.
     *
     * @param mixed ...$values
     *
     * @return Stream
     */
    public function prepend(mixed ...$values): self
    {
        $this->iterable = Multi::chain($values, $this->iterable);
        return $this;
    }

    /**
     * Append variadic values to the end of the stream.
     *
     * For appending an entire iterable, use chainWith. Output keys are reindexed
     * sequentially across the entire stream. Empty variadic leaves the stream unchanged.
     *
     * @param mixed ...$values
     *
     * @return Stream
     */
    public function append(mixed ...$values): self
    {
        $this->iterable = Multi::chain($this->iterable, $values);
        return $this;
    }

    /**
     * Yield one value at a time from the stream and the given iterables in round-robin order.
     *
     * On each round, takes one value from each iterable that still has values; once an iterable
     * is exhausted, it is skipped in subsequent rounds. Iteration ends when every iterable is
     * exhausted. Unlike zipWith, values are yielded individually rather than as tuples.
     *
     * Keys from the source iterables are discarded; the output is sequentially re-indexed.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return Stream
     *
     * @see Multi::roundRobin()
     */
    public function roundRobinWith(iterable ...$iterables): self
    {
        $this->iterable = Multi::roundRobin($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Cartesian product of the stream with zero or more additional iterables.
     *
     * Stream::of($src)->productWith(...$iterables) is equivalent to
     * Combinatorics::product($src, ...$iterables).
     *
     * Output tuples are list arrays (0-indexed, in input order). Source keys are ignored.
     * With zero extra iterables, each stream element is wrapped into a one-element tuple.
     * If any iterable (including the stream itself) is empty, the result is empty.
     *
     * Note: Passing the same non-rewindable iterator instance more than once is not
     * supported — the second occurrence will throw because the iterator has already
     * been consumed. Pass distinct instances instead.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return Stream
     *
     * @see Combinatorics::product()
     */
    public function productWith(iterable ...$iterables): self
    {
        $this->iterable = Combinatorics::product($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Permutations of the stream's elements.
     *
     * Output tuples are list arrays (0-indexed, in input order). Source keys are ignored.
     * Output order follows Python's itertools.permutations (lexicographic by input position):
     * duplicate values are position-unique.
     *
     * The stream is finite and consumed once (materialized internally); generators are
     * supported but cannot be re-iterated afterwards.
     *
     * Special cases:
     *  - $r = 0 yields one empty tuple
     *  - $r greater than the stream length yields nothing
     *  - $r = null means full-length permutations
     *
     * @param int|null $r length of each permutation; null means full length
     *
     * @return Stream
     *
     * @throws \InvalidArgumentException if $r is negative
     *
     * @see Combinatorics::permutations()
     */
    public function permutations(?int $r = null): self
    {
        $this->iterable = Combinatorics::permutations($this->iterable, $r);
        return $this;
    }

    /**
     * Combinations (without replacement) of the stream's elements.
     *
     * Output tuples are list arrays (0-indexed, in input order). Source keys are ignored.
     * Output order follows Python's itertools.combinations (lexicographic by input position):
     * duplicate values are position-unique.
     *
     * The stream is finite and consumed once (materialized internally); generators are
     * supported but cannot be re-iterated afterwards.
     *
     * Special cases:
     *  - $r = 0 yields one empty tuple
     *  - $r greater than the stream length yields nothing
     *
     * @param int $r length of each combination
     *
     * @return Stream
     *
     * @throws \InvalidArgumentException if $r is negative
     *
     * @see Combinatorics::combinations()
     */
    public function combinations(int $r): self
    {
        $this->iterable = Combinatorics::combinations($this->iterable, $r);
        return $this;
    }

    /**
     * Combinations with replacement of the stream's elements.
     *
     * Output tuples are list arrays (0-indexed, in input order). Source keys are ignored.
     * Output order follows Python's itertools.combinations_with_replacement (lexicographic
     * by input position): duplicate values are position-unique and may produce duplicate
     * output tuples.
     *
     * The stream is finite and consumed once (materialized internally); generators are
     * supported but cannot be re-iterated afterwards.
     *
     * Unlike combinations(), $r may exceed the stream length — elements repeat.
     *
     * Special cases:
     *  - $r = 0 yields one empty tuple
     *  - empty stream with $r > 0 yields nothing
     *
     * @param int $r length of each combination
     *
     * @return Stream
     *
     * @throws \InvalidArgumentException if $r is negative
     *
     * @see Combinatorics::combinationsWithReplacement()
     */
    public function combinationsWithReplacement(int $r): self
    {
        $this->iterable = Combinatorics::combinationsWithReplacement($this->iterable, $r);
        return $this;
    }

    /**
     * Powerset of the stream's elements — every subset, ordered by length then by input position.
     *
     * Output subsets are list arrays (0-indexed, in input order). Source keys are ignored.
     * Subsets are yielded in length-ascending order; within each length the order matches
     * Stream::combinations (lexicographic by input position): duplicate values are
     * position-unique.
     *
     * The stream is finite and consumed once (materialized internally); generators are
     * supported but cannot be re-iterated afterwards.
     *
     * Warning: a stream of n elements yields 2**n subsets — consumption grows exponentially.
     *
     * Special cases:
     *  - empty stream yields one empty subset
     *
     * @return Stream
     *
     * @see Combinatorics::powerset()
     */
    public function powerset(): self
    {
        $this->iterable = Combinatorics::powerset($this->iterable);
        return $this;
    }

    /**
     * Treat the stream itself as a sequence of iterables and zip them column-wise (transpose).
     *
     * Similar to Python's zip(*rows) idiom.
     *
     * For uneven lengths, iteration stops when the shortest row is exhausted.
     *
     * The outer stream must be finite; it is consumed when the zipped stream is iterated,
     * before the first tuple is yielded. Inner rows are advanced lazily after that.
     *
     * Every element of the outer stream must be iterable; otherwise \InvalidArgumentException
     * is thrown at iteration time (not at call time).
     *
     * Note: Passing the same iterator instance more than once is not supported and may
     * not behave as expected.
     *
     * @return Stream
     *
     * @throws \InvalidArgumentException if any element of the stream is not iterable
     *
     * @see Multi::zip()
     */
    public function zip(): self
    {
        $source         = $this->iterable;
        $this->iterable = (static function () use ($source): \Generator {
            $rows = self::toRowIterators($source);
            yield from Multi::zip(...$rows);
        })();
        return $this;
    }

    /**
     * Treat the stream itself as a sequence of iterables and zip them column-wise (transpose),
     * continuing until the longest row is exhausted.
     *
     * Similar to Python's zip_longest(*rows) idiom.
     *
     * For uneven lengths, the exhausted rows will produce null for the remaining iterations.
     *
     * The outer stream must be finite; it is consumed when the zipped stream is iterated,
     * before the first tuple is yielded. Inner rows are advanced lazily after that.
     *
     * Every element of the outer stream must be iterable; otherwise \InvalidArgumentException
     * is thrown at iteration time (not at call time).
     *
     * Note: Passing the same iterator instance more than once is not supported and may
     * not behave as expected.
     *
     * @return Stream
     *
     * @throws \InvalidArgumentException if any element of the stream is not iterable
     *
     * @see Multi::zipLongest()
     */
    public function zipLongest(): self
    {
        $source         = $this->iterable;
        $this->iterable = (static function () use ($source): \Generator {
            $rows = self::toRowIterators($source);
            yield from Multi::zipLongest(...$rows);
        })();
        return $this;
    }

    /**
     * Treat the stream itself as a sequence of iterables and zip them column-wise (transpose),
     * continuing until the longest row is exhausted, using $filler for missing values.
     *
     * For uneven lengths, the exhausted rows will produce the $filler value for the remaining
     * iterations.
     *
     * The outer stream must be finite; it is consumed when the zipped stream is iterated,
     * before the first tuple is yielded. Inner rows are advanced lazily after that.
     *
     * Every element of the outer stream must be iterable; otherwise \InvalidArgumentException
     * is thrown at iteration time (not at call time).
     *
     * Note: Passing the same iterator instance more than once is not supported and may
     * not behave as expected.
     *
     * @param mixed $filler
     *
     * @return Stream
     *
     * @throws \InvalidArgumentException if any element of the stream is not iterable
     *
     * @see Multi::zipFilled()
     */
    public function zipFilled(mixed $filler): self
    {
        $source         = $this->iterable;
        $this->iterable = (static function () use ($source, $filler): \Generator {
            $rows = self::toRowIterators($source);
            yield from Multi::zipFilled($filler, ...$rows);
        })();
        return $this;
    }

    /**
     * Treat the stream itself as a sequence of iterables of equal lengths and zip them
     * column-wise (transpose).
     *
     * Works like Stream::zip() but throws \LengthException if row lengths are not equal,
     * i.e., at least one row ends before the others.
     *
     * The outer stream must be finite; it is consumed when the zipped stream is iterated,
     * before the first tuple is yielded. Inner rows are advanced lazily after that.
     *
     * Every element of the outer stream must be iterable; otherwise \InvalidArgumentException
     * is thrown at iteration time (not at call time).
     *
     * Note: Passing the same iterator instance more than once is not supported and may
     * not behave as expected.
     *
     * @return Stream
     *
     * @throws \InvalidArgumentException if any element of the stream is not iterable
     * @throws \LengthException if during iteration one row ends before the others
     *
     * @see Multi::zipEqual()
     */
    public function zipEqual(): self
    {
        $source         = $this->iterable;
        $this->iterable = (static function () use ($source): \Generator {
            $rows = self::toRowIterators($source);
            yield from Multi::zipEqual(...$rows);
        })();
        return $this;
    }

    /**
     * Treat the stream itself as a sequence of rows and transpose into columns.
     *
     * The inverse of zip: yields one column array per index up to the width of the shortest row.
     *
     * The outer stream and every row are fully consumed when the unzipped stream is iterated,
     * before the first column can be yielded — column 0 cannot be emitted until every row's
     * first cell is known.
     *
     * Every element of the outer stream must be iterable; otherwise \InvalidArgumentException
     * is thrown at iteration time (not at call time).
     *
     * @return Stream
     *
     * @throws \InvalidArgumentException if any element of the stream is not iterable
     *
     * @see Multi::unzip()
     */
    public function unzip(): self
    {
        $source         = $this->iterable;
        $this->iterable = (static function () use ($source): \Generator {
            $rows = self::toRowIterators($source);
            yield from Multi::unzip($rows);
        })();
        return $this;
    }

    /**
     * Materialize an outer iterable of iterables into an array of Iterator instances.
     *
     * Uses an explicit zero-based counter so error messages reference positional row index
     * independent of source keys (associative or sparse numeric).
     *
     * @param iterable<mixed> $rows
     *
     * @return array<\Iterator<mixed>>
     *
     * @throws \InvalidArgumentException if any element of $rows is not iterable
     */
    private static function toRowIterators(iterable $rows): array
    {
        $iterators = [];
        $rowIndex  = 0;
        foreach ($rows as $row) {
            if (!\is_iterable($row)) {
                throw new \InvalidArgumentException(
                    "Stream::zip*/unzip requires every element to be iterable; row {$rowIndex} is "
                    . \get_debug_type($row)
                );
            }
            $iterators[] = Transform::toIterator($row);
            $rowIndex++;
        }
        return $iterators;
    }

    /**
     * Iterate iterable source with another iterable collections simultaneously.
     *
     * Make an iterator that aggregates items from multiple iterators.
     * Similar to Python's zip function.
     *
     * For uneven lengths, iterations stops when the shortest iterable is exhausted.
     *
     * Note: Passing the same iterator instance more than once will not work as expected
     * because PHP's MultipleIterator silently ignores duplicate attachments.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return Stream
     *
     * @see Multi::zip()
     */
    public function zipWith(iterable ...$iterables): self
    {
        $this->iterable = Multi::zip($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterate iterable source with another iterable collections simultaneously.
     *
     * Make an iterator that aggregates items from multiple iterators.
     * Similar to Python's zip_longest function
     *
     * Iteration continues until the longest iterable is exhausted.
     * For uneven lengths, the exhausted iterables will produce null for the remaining iterations.
     *
     * Note: Passing the same iterator instance more than once will not work as expected
     * because PHP's MultipleIterator silently ignores duplicate attachments.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return Stream
     *
     * @see Multi::zipLongest()
     */
    public function zipLongestWith(iterable ...$iterables): self
    {
        $this->iterable = Multi::zipLongest($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterate iterable source with another iterable collections simultaneously.
     *
     * Make an iterator that aggregates items from multiple iterators.
     * Similar to Python's zip_longest function
     *
     * Iteration continues until the longest iterable is exhausted.
     * For uneven lengths, the exhausted iterables will produce $filler value for the remaining iterations.
     *
     * Note: Passing the same iterator instance more than once will not work as expected
     * because PHP's MultipleIterator silently ignores duplicate attachments.
     *
     * @param mixed $filler
     * @param iterable<mixed> ...$iterables
     *
     * @return Stream
     *
     * @see Multi::zipFilled()
     */
    public function zipFilledWith(mixed $filler, iterable ...$iterables): self
    {
        $this->iterable = Multi::zipFilled($filler, $this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterate iterable source with another iterable collections of equal lengths simultaneously.
     *
     * Works like Multi::zip() method but throws \LengthException if lengths not equal,
     * i.e., at least one iterator ends before the others.
     *
     * Note: Passing the same iterator instance more than once will not work as expected
     * because PHP's MultipleIterator silently ignores duplicate attachments.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return Stream
     *
     * @see Multi::zipEqual()
     */
    public function zipEqualWith(iterable ...$iterables): self
    {
        $this->iterable = Multi::zipEqual($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Filter out elements from the iterable source only returning unique elements.
     *
     * If $strict is true:
     *  - scalars: compares strictly by type;
     *  - objects: always treats different instances as not equal to each other;
     *  - arrays: compares serialized.
     *
     * If $strict is false:
     *  - scalars: compares non-strictly by value;
     *  - objects: compares serialized (throws \InvalidArgumentException if not serializable);
     *  - arrays: compares serialized.
     *
     * @param bool $strict
     *
     * @return Stream
     *
     * @see Single::distinct()
     */
    public function distinct(bool $strict = true): self
    {
        $this->iterable = Set::distinct($this->iterable, $strict);
        return $this;
    }

    /**
     * Filter out elements from the iterable source only returning unique elements.
     *
     * Using $compareBy function for getting comparable value.
     *
     * @param callable $compareBy
     *
     * @return $this
     *
     * @see Single::distinctBy()
     */
    public function distinctBy(callable $compareBy): self
    {
        $this->iterable = Set::distinctBy($this->iterable, $compareBy);
        return $this;
    }

    /**
     * Remove only consecutive duplicates from the stream (Unix `uniq` behavior).
     *
     * Each element is compared strictly (===) to the previous element yielded.
     * Non-adjacent duplicates are kept. Source keys are discarded.
     *
     * @return Stream
     *
     * @see Set::distinctAdjacent()
     */
    public function distinctAdjacent(): self
    {
        $this->iterable = Set::distinctAdjacent($this->iterable);
        return $this;
    }

    /**
     * Remove only consecutive duplicates from the stream, comparing values returned by $keyFn.
     *
     * Each element's extracted key is compared strictly (===) to the previous element's key.
     * Non-adjacent duplicates are kept. Source keys are discarded.
     *
     * @param callable $keyFn
     *
     * @return Stream
     *
     * @see Set::distinctAdjacentBy()
     */
    public function distinctAdjacentBy(callable $keyFn): self
    {
        $this->iterable = Set::distinctAdjacentBy($this->iterable, $keyFn);
        return $this;
    }

    /**
     * Yield each duplicated value once, at the moment its second occurrence is observed.
     *
     * Source keys are discarded; output keys are sequential 0-indexed.
     *
     * @param bool $strict
     *
     * @return Stream
     *
     * @see Set::duplicates()
     */
    public function duplicates(bool $strict = true): self
    {
        $this->iterable = Set::duplicates($this->iterable, $strict);
        return $this;
    }

    /**
     * Yield each value whose extracted key duplicates a previously seen key, once at the
     * moment of the second occurrence.
     *
     * Source keys are discarded; output keys are sequential 0-indexed.
     *
     * @param callable $keyFn
     *
     * @return Stream
     *
     * @see Set::duplicatesBy()
     */
    public function duplicatesBy(callable $keyFn): self
    {
        $this->iterable = Set::duplicatesBy($this->iterable, $keyFn);
        return $this;
    }

    /**
     * Cycle through the elements of iterable source sequentially forever
     *
     * @return Stream
     *
     * @see Infinite::cycle()
     */
    public function infiniteCycle(): self
    {
        $this->iterable = Infinite::cycle($this->iterable);
        return $this;
    }

    /**
     * Accumulate the running average (mean) over iterable source
     *
     * @param int|float|null $initialValue
     *
     * @return Stream
     *
     * @see Math::runningAverage()
     */
    public function runningAverage(int|float|null $initialValue = null): self
    {
        /** @var iterable<int|float> $iterable */
        $iterable = $this->iterable;
        $this->iterable = Math::runningAverage($iterable, $initialValue);
        return $this;
    }

    /**
     * Accumulate the running difference over iterable source
     *
     * @param int|float|null $initialValue
     *
     * @return Stream
     *
     * @see Math::runningDifference()
     */
    public function runningDifference(int|float|null $initialValue = null): self
    {
        /** @var iterable<int|float> $iterable */
        $iterable = $this->iterable;
        $this->iterable = Math::runningDifference($iterable, $initialValue);
        return $this;
    }

    /**
     * Accumulate the running max over iterable source
     *
     * @param int|float|null $initialValue
     *
     * @return Stream
     *
     * @see Math::runningMax()
     */
    public function runningMax(int|float|null $initialValue = null): self
    {
        /** @var iterable<int|float> $iterable */
        $iterable = $this->iterable;
        $this->iterable = Math::runningMax($iterable, $initialValue);
        return $this;
    }

    /**
     * Accumulate the running min over iterable source
     *
     * @param int|float|null $initialValue
     *
     * @return Stream
     *
     * @see Math::runningMin()
     */
    public function runningMin(int|float|null $initialValue = null): self
    {
        /** @var iterable<int|float> $iterable */
        $iterable = $this->iterable;
        $this->iterable = Math::runningMin($iterable, $initialValue);
        return $this;
    }

    /**
     * Accumulate the running product over iterable source
     *
     * @param int|float|null $initialValue
     *
     * @return Stream
     *
     * @see Math::runningProduct()
     */
    public function runningProduct(int|float|null $initialValue = null): self
    {
        /** @var iterable<int|float> $iterable */
        $iterable = $this->iterable;
        $this->iterable = Math::runningProduct($iterable, $initialValue);
        return $this;
    }

    /**
     * Accumulate the running total over iterable source
     *
     * @param int|float|null $initialValue
     *
     * @return Stream
     *
     * @see Math::runningTotal()
     */
    public function runningTotal(int|float|null $initialValue = null): self
    {
        /** @var iterable<int|float> $iterable */
        $iterable = $this->iterable;
        $this->iterable = Math::runningTotal($iterable, $initialValue);
        return $this;
    }

    /**
     * Iterates the intersection of iterable source and given iterables in strict type mode.
     *
     *  - scalars: compares strictly by type;
     *  - objects: always treats different instances as not equal to each other;
     *  - arrays: compares serialized.
     *
     * @param array<iterable<mixed>> ...$iterables
     *
     * @return Stream
     *
     * @see Set::intersection()
     */
    public function intersectionWith(iterable ...$iterables): self
    {
        $this->iterable = Set::intersection($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterates the intersection of iterable source and given iterables in non-strict type mode.
     *
     *  - scalars: compares non-strictly by value;
     *  - objects: compares serialized (throws \InvalidArgumentException if not serializable);
     *  - arrays: compares serialized.
     *
     * @param array<iterable<mixed>> ...$iterables
     *
     * @return Stream
     *
     * @see Set::intersectionCoercive()
     */
    public function intersectionCoerciveWith(iterable ...$iterables): self
    {
        $this->iterable = Set::intersectionCoercive($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterates the symmetric difference of iterable source and given iterables in strict type mode.
     *
     *  - scalars: compares strictly by type;
     *  - objects: always treats different instances as not equal to each other;
     *  - arrays: compares serialized.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return Stream
     *
     * @see Set::symmetricDifference()
     */
    public function symmetricDifferenceWith(iterable ...$iterables): self
    {
        $this->iterable = Set::symmetricDifference($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterates the symmetric difference of iterable source and given iterables in non-strict type mode.
     *
     *  - scalars: compares non-strictly by value;
     *  - objects: compares serialized (throws \InvalidArgumentException if not serializable);
     *  - arrays: compares serialized.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return Stream
     *
     * @see Set::symmetricDifferenceCoercive()
     */
    public function symmetricDifferenceCoerciveWith(iterable ...$iterables): self
    {
        $this->iterable = Set::symmetricDifferenceCoercive($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterates the difference of iterable source and given iterables in strict type mode.
     *
     * Returns elements from the source not present in any given iterables.
     *
     *  - scalars: compares strictly by type;
     *  - objects: always treats different instances as not equal to each other;
     *  - arrays: compares serialized.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return Stream
     *
     * @see Set::difference()
     */
    public function differenceWith(iterable ...$iterables): self
    {
        $this->iterable = Set::difference($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterates the difference of iterable source and given iterables in non-strict type mode.
     *
     * Returns elements from the source not present in any given iterables.
     *
     *  - scalars: compares non-strictly by value;
     *  - objects: compares serialized (throws \InvalidArgumentException if not serializable);
     *  - arrays: compares serialized.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return Stream
     *
     * @see Set::differenceCoercive()
     */
    public function differenceCoerciveWith(iterable ...$iterables): self
    {
        $this->iterable = Set::differenceCoercive($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterates partial intersection of iterable source and given iterables in strict type mode.
     *
     *  - scalars: compares strictly by type;
     *  - objects: always treats different instances as not equal to each other;
     *  - arrays: compares serialized.
     *
     * @param positive-int $minIntersectionCount
     * @param array<iterable<mixed>> ...$iterables
     *
     * @return Stream
     *
     * @see Set::partialIntersection()
     */
    public function partialIntersectionWith(int $minIntersectionCount, iterable ...$iterables): self
    {
        $this->iterable = Set::partialIntersection($minIntersectionCount, $this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterates partial intersection of iterable source and given iterables in non-strict type mode.
     *
     *  - scalars: compares non-strictly by value;
     *  - objects: compares serialized (throws \InvalidArgumentException if not serializable);
     *  - arrays: compares serialized.
     *
     * @param positive-int $minIntersectionCount
     * @param array<iterable<mixed>> ...$iterables
     *
     * @return Stream
     *
     * @see Set::partialIntersectionCoercive()
     */
    public function partialIntersectionCoerciveWith(int $minIntersectionCount, iterable ...$iterables): self
    {
        $this->iterable = Set::partialIntersectionCoercive($minIntersectionCount, $this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterates union of iterable source and given iterables in strict type mode.
     *
     *  - scalars: compares strictly by type;
     *  - objects: always treats different instances as not equal to each other;
     *  - arrays: compares serialized.
     *
     * @param array<iterable<mixed>> ...$iterables
     *
     * @return Stream
     *
     * @see Set::union()
     */
    public function unionWith(iterable ...$iterables): self
    {
        $this->iterable = Set::union($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterates union of iterable source and given iterables using type coercion.
     *
     *  - scalars: compares non-strictly by value;
     *  - objects: compares serialized (throws \InvalidArgumentException if not serializable);
     *  - arrays: compares serialized.
     *
     * @param array<iterable<mixed>> ...$iterables
     *
     * @return Stream
     *
     * @see Set::unionCoercive()
     */
    public function unionCoerciveWith(iterable ...$iterables): self
    {
        $this->iterable = Set::unionCoercive($this->iterable, ...$iterables);
        return $this;
    }

    // STREAM DEBUG OPERATIONS

    /**
     * Peek at each element between other Stream operations to do some action without modifying the stream.
     *
     * Useful for debugging purposes.
     *
     * @param callable $callback
     *
     * @return Stream
     */
    public function peek(callable $callback): self
    {
        [$this->iterable, $peekable] = Transform::tee($this->iterable, 2);

        foreach ($peekable as $element) {
            $callback($element);
        }

        return $this;
    }

    /**
     * Peek at the entire stream between other Stream operations to do some action without modifying the stream.
     *
     * Useful for debugging purposes.
     *
     * @param callable $callback
     *
     * @return Stream
     */
    public function peekStream(callable $callback): self
    {
        [$this->iterable, $peekable] = Transform::tee($this->iterable, 2);
        $callback(Stream::of($peekable));

        return $this;
    }

    /**
     * Peek at each element between other Stream operations to print each item without modifying the stream.
     *
     * Useful for debugging purposes.
     *
     * @param string $separator
     * @param string $prefix
     * @param string $suffix
     *
     * @return Stream
     */
    public function peekPrint(string $separator = '', string $prefix = '', string $suffix = ''): self
    {
        $this->peekStream(fn (Stream $stream) => $stream->print($separator, $prefix, $suffix));
        return $this;
    }

    /**
     * Peek at each element between other Stream operations to print_r each item without modifying the stream.
     *
     * Useful for debugging purposes.
     *
     * @return Stream
     */
    public function peekPrintR(): self
    {
        $this->peekStream(fn (Stream $stream) => $stream->printR());
        return $this;
    }

    // STREAM TERMINAL OPERATIONS

    /**
     * Converts iterable source to array.
     *
     * @return array<mixed>
     *
     * @see Transform::toArray()
     */
    public function toArray(): array
    {
        return Transform::toArray($this->iterable);
    }

    /**
     * Converts iterable source to an associative array.
     *
     * @param (callable(mixed, mixed):mixed)|null $keyFunc fn ($value, $key) => Custom Logic
     * @param (callable(mixed, mixed):mixed)|null $valueFunc fn ($value, $key) => Custom logic
     *
     * @return array<mixed>
     *
     * @see Transform::toAssociativeArray()
     */
    public function toAssociativeArray(?callable $keyFunc = null, ?callable $valueFunc = null): array
    {
        return Transform::toAssociativeArray($this->iterable, $keyFunc, $valueFunc);
    }

    /**
     * Partitions the stream into two lists based on a predicate.
     *
     * Returns a two-element list array: [truthyValues, falsyValues].
     * Both output arrays are reindexed (list arrays); source keys are discarded.
     *
     * Predicate return value is coerced via (bool) cast.
     *
     * @param callable $predicate
     *
     * @return array{0: array<mixed>, 1: array<mixed>}
     *
     * @see Transform::partition()
     */
    public function toPartition(callable $predicate): array
    {
        return Transform::partition($this->iterable, $predicate);
    }

    /**
     * Return several independent streams from current stream.
     *
     * Once a tee() has been created, the original iterable should not be used anywhere else;
     * otherwise, the iterable could get advanced without the tee objects being informed.
     *
     * This tool may require significant auxiliary storage (depending on how much temporary data needs to be stored).
     * In general, if one iterator uses most or all of the data before another iterator starts,
     * it is faster to use toArray() instead of tee().
     *
     * @param positive-int $count
     *
     * @return array<Stream>
     *
     * @see Transform::tee()
     */
    public function tee(int $count): array
    {
        return \array_map(fn ($it) => new self($it), Transform::tee($this->iterable, $count));
    }

    /**
     * Writes iterable source to the file line by line.
     *
     * Elements of the iterable source must be stringable.
     *
     * @param resource    $fileResource     File handle stream opened for writing
     * @param string      $newlineSeparator (optional) inserted between each line, typically the newline character.
     * @param string|null $header           (optional) prepended to string
     * @param string|null $footer           (optional) appended to string
     *
     * @return void
     */
    public function toFile(mixed $fileResource, string $newlineSeparator = \PHP_EOL, ?string $header = null, ?string $footer = null): void
    {
        ResourcePolicy::assertIsSatisfied($fileResource);

        $firstIteration = true;

        if ($header !== null) {
            \fputs($fileResource, $header . $newlineSeparator);
        }

        foreach ($this->iterable as $line) {
            /** @psalm-suppress MixedArgument */
            $line = \is_float($line) && \is_nan($line) ? 'NAN' : \strval($line); // @phpstan-ignore argument.type

            if ($firstIteration) {
                $firstIteration = false;
            } else {
                $line = $newlineSeparator . $line;
            }

            \fputs($fileResource, $line);
        }

        if ($footer !== null) {
            \fputs($fileResource, $newlineSeparator . $footer);
        }
    }

    /**
     * Writes iterable source to the file line by line.
     *
     * Elements of the iterable source must be array of scalars.
     *
     * @param resource           $fileResource File handle stream opened for writing
     * @param array<string>|null $header (optional) header row before data, labelling the columns
     * @param string             $separator
     * @param string             $enclosure
     * @param string             $escape
     *
     * @return void
     */
    public function toCsvFile(
        mixed $fileResource,
        ?array $header = null,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ): void {
        ResourcePolicy::assertIsSatisfied($fileResource);

        if ($header !== null) {
            \fputcsv($fileResource, $header, $separator, $enclosure, $escape);
        }

        /** @var array<scalar> $row */
        foreach ($this->iterable as $row) {
            \fputcsv($fileResource, $row, $separator, $enclosure, $escape);
        }
    }

    /**
     * Returns true if all elements match the predicate function.
     *
     * Empty iterables return true.
     *
     * @param callable $predicate
     *
     * @return bool
     *
     * @see Summary::allMatch
     */
    public function allMatch(callable $predicate): bool
    {
        return Summary::allMatch($this->iterable, $predicate);
    }

    /**
     * Returns true if any element matches the predicate function.
     *
     * Empty iterables return false.
     *
     * @param callable $predicate
     *
     * @return bool
     *
     * @see Summary::anyMatch
     */
    public function anyMatch(callable $predicate): bool
    {
        return Summary::anyMatch($this->iterable, $predicate);
    }

    /**
     * Returns true if the stream contains the needle (using strict-type comparison).
     *
     * Strict-type comparison:
     *  - scalars: compares strictly by type (1 does not match '1', 0 does not match false)
     *  - objects: matches only the same instance
     *  - arrays: compares strictly by ===
     *  - NaN: never matches NaN (since NaN !== NaN)
     *
     * Short-circuits on first match.
     *
     * @param mixed $needle
     *
     * @return bool
     *
     * @see Summary::contains()
     */
    public function contains(mixed $needle): bool
    {
        return Summary::contains($this->iterable, $needle);
    }

    /**
     * Returns true if the stream contains the needle (using type coercion).
     *
     * Coercive (non-strict) type comparison:
     *  - scalars: compares non-strictly by value (1 matches '1', 0 matches false, '1e2' matches 100)
     *  - objects: compares serialized (throws \InvalidArgumentException if needle or any visited datum is not serializable)
     *  - arrays: compares serialized
     *  - NaN: matches NaN (consistent with other coercive operations in this library)
     *
     * Short-circuits on first match.
     *
     * @param mixed $needle
     *
     * @return bool
     *
     * @throws \InvalidArgumentException if the needle is not serializable, or if a non-serializable datum is reached before any match
     *
     * @see Summary::containsCoercive()
     */
    public function containsCoercive(mixed $needle): bool
    {
        return Summary::containsCoercive($this->iterable, $needle);
    }

    /**
     * Returns true if exactly n items in the stream are true where the predicate function is true.
     *
     * Default predicate if not provided is the boolean value of each data item.
     *
     * @param int           $n
     * @param callable|null $predicate
     *
     * @return bool
     *
     * @see Summary::exactlyN()
     */
    public function exactlyN(int $n, ?callable $predicate = null): bool
    {
        return Summary::exactlyN($this->iterable, $n, $predicate);
    }

    /**
     * Returns true if at least n items in the stream are true where the predicate function is true.
     *
     * Default predicate if not provided is the boolean value of each data item.
     *
     * Short-circuits as soon as the count reaches $n.
     *
     * @param int           $n
     * @param callable|null $predicate
     *
     * @return bool
     *
     * @see Summary::atLeastN()
     */
    public function atLeastN(int $n, ?callable $predicate = null): bool
    {
        return Summary::atLeastN($this->iterable, $n, $predicate);
    }

    /**
     * Returns true if at most n items in the stream are true where the predicate function is true.
     *
     * Default predicate if not provided is the boolean value of each data item.
     *
     * Short-circuits as soon as the count exceeds $n.
     *
     * @param int           $n
     * @param callable|null $predicate
     *
     * @return bool
     *
     * @see Summary::atMostN()
     */
    public function atMostN(int $n, ?callable $predicate = null): bool
    {
        return Summary::atMostN($this->iterable, $n, $predicate);
    }

    /**
     * Returns true if the stream starts with the given prefix (using strict-type comparison).
     *
     * Compares values pairwise; keys are ignored.
     *
     * Empty prefix → true without consuming the stream.
     *
     * @param iterable<mixed> $prefix
     *
     * @return bool
     *
     * @see Summary::startsWith()
     */
    public function startsWith(iterable $prefix): bool
    {
        return Summary::startsWith($this->iterable, $prefix);
    }

    /**
     * Returns true if the stream starts with the given prefix (using type coercion).
     *
     * Compares values pairwise; keys are ignored.
     *
     * Empty prefix → true without consuming the stream.
     *
     * @param iterable<mixed> $prefix
     *
     * @return bool
     *
     * @see Summary::startsWithCoercive()
     */
    public function startsWithCoercive(iterable $prefix): bool
    {
        return Summary::startsWithCoercive($this->iterable, $prefix);
    }

    /**
     * Returns true if the stream ends with the given suffix (using strict-type comparison).
     *
     * Compares values pairwise; keys are ignored.
     *
     * Empty suffix → true without consuming the stream. The stream must be finite.
     *
     * @param iterable<mixed> $suffix
     *
     * @return bool
     *
     * @see Summary::endsWith()
     */
    public function endsWith(iterable $suffix): bool
    {
        return Summary::endsWith($this->iterable, $suffix);
    }

    /**
     * Returns true if the stream ends with the given suffix (using type coercion).
     *
     * Compares values pairwise; keys are ignored.
     *
     * Empty suffix → true without consuming the stream. The stream must be finite.
     *
     * @param iterable<mixed> $suffix
     *
     * @return bool
     *
     * @see Summary::endsWithCoercive()
     */
    public function endsWithCoercive(iterable $suffix): bool
    {
        return Summary::endsWithCoercive($this->iterable, $suffix);
    }

    /**
     * Returns true if all elements in stream that satisfy the predicate
     * appear before all elements that don't.
     *
     * Returns true for empty stream or for stream storing only single item.
     *
     * Default predicate if not provided is the boolean value of each data item.
     *
     * @see https://en.cppreference.com/w/cpp/algorithm/is_partitioned
     *
     * @param callable|null $predicate
     *
     * @return bool
     *
     * @see Summary::isPartitioned()
     */
    public function isPartitioned(?callable $predicate = null): bool
    {
        return Summary::isPartitioned($this->iterable, $predicate);
    }

    /**
     * Returns true if stream is empty.
     *
     * @return bool
     *
     * @see Summary::isEmpty()
     */
    public function isEmpty(): bool
    {
        return Summary::isEmpty($this->iterable);
    }

    /**
     * Returns true if no element matches the predicate function.
     *
     * Empty iterables return true.
     *
     * @param callable $predicate
     *
     * @return bool
     *
     * @see Summary::noneMatch
     */
    public function noneMatch(callable $predicate): bool
    {
        return Summary::noneMatch($this->iterable, $predicate);
    }

    /**
     * Returns true if all elements in stream are unique.
     *
     * Empty iterables return true.
     *
     * @param bool $strict
     *
     * @return bool
     *
     * @see Summary::allUnique()
     */
    public function allUnique(bool $strict = true): bool
    {
        return Summary::allUnique($this->iterable, $strict);
    }

    /**
     * Returns true if iterable source is sorted in ascending order; otherwise false.
     *
     * Items of iterable source must be comparable.
     *
     * Returns true if iterable source is empty or has only one element.
     *
     * @return bool
     *
     * @see Summary::isSorted()
     */
    public function isSorted(): bool
    {
        return Summary::isSorted($this->iterable);
    }

    /**
     * Returns true if iterable source is sorted in reverse descending order; otherwise false.
     *
     * Items of iterable source must be comparable.
     *
     * Returns true if iterable source is empty or has only one element.
     *
     * @return bool
     *
     * @see Summary::isReversed()
     */
    public function isReversed(): bool
    {
        return Summary::isReversed($this->iterable);
    }

    /**
     * Returns true if iterable source and all given collections are the same.
     *
     * For single iterable or empty iterables list returns true.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     *
     * @see Summary::same()
     */
    public function sameWith(iterable ...$iterables): bool
    {
        return Summary::same($this->iterable, ...$iterables);
    }

    /**
     * Returns true if iterable source and all given collections have the same lengths.
     *
     * For single iterable or empty iterables list returns true.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     *
     * @see Summary::sameCount()
     */
    public function sameCountWith(iterable ...$iterables): bool
    {
        return Summary::sameCount($this->iterable, ...$iterables);
    }

    /**
     * Returns true if stream and given collections are permutations of each other (using strict-type comparisons).
     *
     * Returns true if no collections given.
     *
     * Strict-type comparisons:
     *  - scalars: compares strictly by type
     *  - objects: always treats different instances as not equal to each other
     *  - arrays: compares serialized
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     *
     * @see Summary::arePermutations()
     * @see https://en.cppreference.com/w/cpp/algorithm/is_permutation
     */
    public function arePermutationsWith(iterable ...$iterables): bool
    {
        return Summary::arePermutations($this->iterable, ...$iterables);
    }

    /**
     * Returns true if stream and given collections are permutations of each other (using type coercion).
     *
     * Returns true if no collections given.
     *
     * Coercive (non-strict) type comparisons:
     *  - scalars: compares non-strictly by value
     *  - objects: compares serialized (throws \InvalidArgumentException if not serializable)
     *  - arrays: compares serialized
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return bool
     *
     * @see Summary::arePermutationsCoercive()
     * @see https://en.cppreference.com/w/cpp/algorithm/is_permutation
     */
    public function arePermutationsCoerciveWith(iterable ...$iterables): bool
    {
        return Summary::arePermutationsCoercive($this->iterable, ...$iterables);
    }

    /**
     * Reduces iterable source to the mean average of its items.
     *
     * Returns null if iterable source is empty.
     *
     * @return int|float|null
     *
     * @see Reduce::toAverage()
     */
    public function toAverage(): int|float|null
    {
        /** @var iterable<numeric> $iterable */
        $iterable = $this->iterable;
        return Reduce::toAverage($iterable);
    }

    /**
     * Reduces iterable source to its length.
     *
     * @return int
     *
     * @see Reduce::toCount()
     */
    public function toCount(): int
    {
        return Reduce::toCount($this->iterable);
    }

    /**
     * Reduces iterable source to its max value.
     *
     * Callable param $compareBy must return comparable value.
     *
     * If $compareBy is not proposed then items of iterable source must be comparable.
     *
     * Returns null if iterable source is empty.
     *
     * @param callable|null $compareBy
     *
     * @return mixed
     *
     * @see Reduce::toMax()
     */
    public function toMax(?callable $compareBy = null): mixed
    {
        return Reduce::toMax($this->iterable, $compareBy);
    }

    /**
     * Reduces iterable source to its min value.
     *
     * Callable param $compareBy must return comparable value.
     *
     * If $compareBy is not proposed then items of iterable source must be comparable.
     *
     * Returns null if iterable source is empty.
     *
     * @param callable|null $compareBy
     *
     * @return mixed
     *
     * @see Reduce::toMin()
     */
    public function toMin(?callable $compareBy = null): mixed
    {
        return Reduce::toMin($this->iterable, $compareBy);
    }

    /**
     * Reduces iterable source to array of its upper and lower bounds.
     *
     * Callable param $compareBy must return comparable value.
     *
     * If $compareBy is not proposed then items of iterable source must be comparable.
     *
     * Returns [null, null] if iterable source is empty.
     *
     * @param callable|null $compareBy
     *
     * @return array{numeric, numeric}|array{null, null}
     *
     * @see Reduce::toMinMax()
     */
    public function toMinMax(?callable $compareBy = null): array
    {
        /** @var iterable<numeric> $iterable */
        $iterable = $this->iterable;
        return Reduce::toMinMax($iterable, $compareBy);
    }

    /**
     * Reduces iterable source to the product of its items.
     *
     * Returns null if iterable source is empty.
     *
     * @return int|float|null
     *
     * @see Reduce::toProduct()
     */
    public function toProduct(): int|float|null
    {
        /** @var iterable<numeric> $iterable */
        $iterable = $this->iterable;
        return Reduce::toProduct($iterable);
    }

    /**
     * Reduces iterable to a string with optional glue, prefix, and suffix.
     *
     * Returns empty string (with optional prefix and suffix) if stream is empty.
     *
     * @param string   $separator (optional) inserted between each item. Ex: ', ' for 1, 2, 3, ...
     * @param string   $prefix (optional) prepended to string
     * @param string   $suffix (optional) appended to string
     *
     * @return string
     *
     * @see Reduce::toString
     */
    public function toString(string $separator = '', string $prefix = '', string $suffix = ''): string
    {
        return Reduce::toString($this->iterable, $separator, $prefix, $suffix);
    }

    /**
     * Reduces iterable source to the sum of its items.
     *
     * @return int|float
     *
     * @see Reduce::toSum()
     */
    public function toSum(): int|float
    {
        /** @var iterable<numeric> $iterable */
        $iterable = $this->iterable;
        return Reduce::toSum($iterable);
    }

    /**
     * Reduces iterable source to its amplitude.
     *
     * Returns 0 if iterable source is empty.
     *
     * @return int|float
     *
     * @see Reduce::toRange()
     */
    public function toRange(): int|float
    {
        /** @var iterable<numeric> $iterable */
        $iterable = $this->iterable;
        return Reduce::toRange($iterable);
    }

    /**
     * Returns the first element of iterable source.
     *
     * @return mixed
     *
     * @throws \LengthException if iterable source is empty.
     *
     * @see Reduce::toFirst()
     */
    public function toFirst(): mixed
    {
        return Reduce::toFirst($this->iterable);
    }

    /**
     * Returns the last element of iterable source.
     *
     * @return mixed
     *
     * @throws \LengthException if iterable source is empty.
     *
     * @see Reduce::toLast()
     */
    public function toLast(): mixed
    {
        return Reduce::toLast($this->iterable);
    }

    /**
     * Returns the first element of iterable source.
     *
     * @return array{mixed, mixed}
     *
     * @throws \LengthException if iterable source is empty.
     *
     * @see Reduce::toFirstAndLast()
     */
    public function toFirstAndLast(): array
    {
        return Reduce::toFirstAndLast($this->iterable);
    }

    /**
     * Returns a random element of stream.
     *
     * @return mixed
     *
     * @throws \LengthException if iterable source is empty.
     *
     * @see Reduce::toRandomValue()
     */
    public function toRandomValue(): mixed
    {
        return Reduce::toRandomValue($this->iterable);
    }

    /**
     * Reduces iterable source like array_reduce() function.
     *
     * But unlike array_reduce(), it works with all iterable types.
     *
     * @param callable $reducer
     * @param mixed $initialValue
     *
     * @return mixed
     *
     * @see Reduce::toValue()
     */
    public function toValue(callable $reducer, mixed $initialValue = null): mixed
    {
        return Reduce::toValue($this->iterable, $reducer, $initialValue);
    }

    /**
     * Reduces iterable source to its nth element.
     *
     * @param int $position
     * @return mixed
     *
     * @see Reduce::toNth()
     */
    public function toNth(int $position): mixed
    {
        return Reduce::toNth($this->iterable, $position);
    }

    /**
     * Reduces iterable source to the first element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast.
     *
     * Short-circuits on first match. Returns $default when no element matches.
     *
     * @param callable $predicate
     * @param mixed    $default value returned when no element matches
     *
     * @return mixed
     *
     * @see Reduce::toFirstMatch()
     */
    public function toFirstMatch(callable $predicate, mixed $default = null): mixed
    {
        return Reduce::toFirstMatch($this->iterable, $predicate, $default);
    }

    /**
     * Reduces iterable source to the zero-based position of the first element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast.
     *
     * Short-circuits on first match. Returns $default when no element matches.
     *
     * @param callable $predicate
     * @param mixed    $default value returned when no element matches
     *
     * @return mixed
     *
     * @see Reduce::toFirstMatchIndex()
     */
    public function toFirstMatchIndex(callable $predicate, mixed $default = null): mixed
    {
        return Reduce::toFirstMatchIndex($this->iterable, $predicate, $default);
    }

    /**
     * Reduces iterable source to the source key of the first element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast.
     *
     * Short-circuits on first match. Returns $default when no element matches.
     *
     * @param callable $predicate
     * @param mixed    $default value returned when no element matches
     *
     * @return mixed
     *
     * @see Reduce::toFirstMatchKey()
     */
    public function toFirstMatchKey(callable $predicate, mixed $default = null): mixed
    {
        return Reduce::toFirstMatchKey($this->iterable, $predicate, $default);
    }

    /**
     * Reduces iterable source to the last element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast.
     *
     * Consumes the entire iterable. Returns $default when no element matches.
     *
     * @param callable $predicate
     * @param mixed    $default value returned when no element matches
     *
     * @return mixed
     *
     * @see Reduce::toLastMatch()
     */
    public function toLastMatch(callable $predicate, mixed $default = null): mixed
    {
        return Reduce::toLastMatch($this->iterable, $predicate, $default);
    }

    /**
     * Reduces iterable source to the zero-based position of the last element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast.
     *
     * Consumes the entire iterable. Returns $default when no element matches.
     *
     * @param callable $predicate
     * @param mixed    $default value returned when no element matches
     *
     * @return mixed
     *
     * @see Reduce::toLastMatchIndex()
     */
    public function toLastMatchIndex(callable $predicate, mixed $default = null): mixed
    {
        return Reduce::toLastMatchIndex($this->iterable, $predicate, $default);
    }

    /**
     * Reduces iterable source to the source key of the last element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast.
     *
     * Consumes the entire iterable. Returns $default when no element matches.
     *
     * @param callable $predicate
     * @param mixed    $default value returned when no element matches
     *
     * @return mixed
     *
     * @see Reduce::toLastMatchKey()
     */
    public function toLastMatchKey(callable $predicate, mixed $default = null): mixed
    {
        return Reduce::toLastMatchKey($this->iterable, $predicate, $default);
    }

    /**
     * Reduces iterable source to its sole element.
     *
     * @return mixed
     *
     * @throws \LengthException if iterable is empty or contains more than one element
     *
     * @see Reduce::toOnly()
     */
    public function toOnly(): mixed
    {
        return Reduce::toOnly($this->iterable);
    }

    /**
     * Drains the stream, discarding values.
     *
     * Useful for forcing evaluation of a lazy pipeline whose only purpose is its side effects
     * (e.g. a side-effectful map() or a Generator source that writes to a log).
     *
     * @return void
     *
     * @see Reduce::consume()
     */
    public function consume(): void
    {
        Reduce::consume($this->iterable);
    }

    // TERMINAL OPERATIONS WITH SIDE EFFECTS - PRINT

    /**
     * Print each item in the stream
     *
     * @param string $separator (optional) inserted between each item. Ex: ', ' for 1, 2, 3, ...
     * @param string $prefix (optional) prepended to string
     * @param string $suffix (optional) appended to string
     *
     * @return void
     */
    public function print(string $separator = '', string $prefix = '', string $suffix = ''): void
    {
        $string = Reduce::toString($this->iterable, $separator, $prefix, $suffix);
        print($string);
    }

    /**
     * Print each item in the stream on a new line
     *
     * @return void
     */
    public function printLn(): void
    {
        foreach ($this->iterable as $item) {
            /** @phpstan-ignore cast.string */
            print((string) $item . \PHP_EOL);
        }
    }

    /**
     * Print_R each item in the stream
     *
     * @return void
     */
    public function printR(): void
    {
        foreach ($this->iterable as $item) {
            \print_r($item);
        }
    }

    /**
     * Var_Dump each item in the stream
     *
     * @return void
     */
    public function varDump(): void
    {
        foreach ($this->iterable as $item) {
            /** @psalm-suppress ForbiddenCode */
            \var_dump($item);
        }
    }

    public function callForEach(callable $func): void
    {
        foreach ($this->iterable as $item) {
            $func($item);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return \Iterator<mixed>
     */
    public function getIterator(): \Iterator
    {
        return Transform::toIterator($this->iterable);
    }
}

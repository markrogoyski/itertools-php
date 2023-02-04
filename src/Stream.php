<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\IteratorFactory;
use IterTools\Util\ResourcePolicy;

/**
 * Provides fluent interface for working with iterables.
 *
 * @implements \IteratorAggregate<mixed>
 */
class Stream implements \IteratorAggregate
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
     * @return Stream<mixed>
     */
    public static function of(iterable $iterable): self
    {
        return new self($iterable);
    }

    /**
     * Creates iterable instance with fluent interface of random coin flips.
     *
     * @param int $repetitions
     *
     * @return Stream<mixed>
     *
     * @see Random::coinFlip()
     */
    public static function ofCoinFlips(int $repetitions): self
    {
        return new self(Random::coinFlip($repetitions));
    }

    /**
     * Creates iterable instance with fluent interface from empty iterable source.
     *
     * @return Stream<mixed>
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
     *
     * @return Stream<mixed>
     *
     * @see Random::choice()
     */
    public static function ofRandomChoice(array $items, int $repetitions): self
    {
        return new self(Random::choice($items, $repetitions));
    }

    /**
     * Creates iterable instance with fluent interface of random numbers (integers).
     *
     * @param int $min
     * @param int $max
     * @param int $repetitions
     *
     * @return Stream<mixed>
     *
     * @see Random::number()
     */
    public static function ofRandomNumbers(int $min, int $max, int $repetitions): self
    {
        return new self(Random::number($min, $max, $repetitions));
    }

    /**
     * Creates iterable instance with fluent interface of random percentages between 0 and 1.
     *
     * @param int $repetitions
     *
     * @return Stream<mixed>
     *
     * @see Random::percentage()
     */
    public static function ofRandomPercentage(int $repetitions): self
    {
        return new self(Random::percentage($repetitions));
    }

    /**
     * @param int|float|mixed $start First value of sequence
     * @param int|float|mixed $end Sequence ends upon reaching this value
     * @param int|float       $step (optional) Step increase between values. Defaults to 1.
     *
     * @return Stream<int|float>
     */
    public static function ofRange($start, $end, $step = 1): self
    {
        if (!\is_numeric($start) || !\is_numeric($end) || !\is_numeric($step)) {
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
     *
     * @return Stream<mixed>
     *
     * @see Random::rockPaperScissors()
     */
    public static function ofRockPaperScissors(int $repetitions): self
    {
        return new self(Random::rockPaperScissors($repetitions));
    }

    /**
     * Create a stream sourced from the lines of a file.
     *
     * @param resource $file File handle stream opened for reading
     *
     * @return Stream<mixed>
     *
     * @see File::readLines()
     */
    public static function ofFileLines($file): self
    {
        return new self(File::readLines($file));
    }

    /**
     * Create a stream sourced from a CSG file.
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
        $file,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ): self {
        return new self(File::readCsv($file, $separator, $enclosure, $escape));
    }

    // STREAM OPERATIONS

    /**
     * Compress an iterable source by filtering out data that is not selected.
     *
     * Selectors indicate which data. True value selects item. False value filters out data.
     *
     * @param iterable<bool> $selectors
     *
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
     *
     * @see Single::dropWhile()
     */
    public function dropWhile(callable $predicate): self
    {
        $this->iterable = Single::dropWhile($this->iterable, $predicate);
        return $this;
    }

    /**
     * Return elements from the iterable source as long as the predicate is true.
     *
     * If no predicate is provided, the boolean value of the data is used.
     *
     * @param callable $predicate
     *
     * @return $this
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
     * If no predicate is provided, the boolean value of the data is used.
     *
     * @param callable|null $predicate
     *
     * @return $this
     *
     * @see Single::filterTrue()
     */
    public function filterTrue(callable $predicate = null): self
    {
        $this->iterable = Single::filterTrue($this->iterable, $predicate);
        return $this;
    }

    /**
     * Filter out elements from the iterable source only returning elements where the predicate function is false.
     *
     * If no predicate is provided, the boolean value of the data is used.
     *
     * @param callable|null $predicate
     *
     * @return $this
     *
     * @see Single::filterFalse()
     */
    public function filterFalse(callable $predicate = null): self
    {
        $this->iterable = Single::filterFalse($this->iterable, $predicate);
        return $this;
    }

    /**
     * Filter out elements from the iterable only returning elements for which keys the predicate function is true.
     *
     * @param callable $predicate
     *
     * @return $this
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
     * The groupKeyFunction determines the key to group elements by.
     *
     * @param callable $groupKeyFunction
     *
     * @return $this
     *
     * @see Single::groupBy()
     */
    public function groupBy(callable $groupKeyFunction): self
    {
        $this->iterable = Single::groupBy($this->iterable, $groupKeyFunction);
        return $this;
    }

    /**
     * Return pairs of elements from iterable source.
     *
     * Returns empty generator if given collection contains less than 2 elements.
     *
     * @return $this
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
     * @return $this
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
     *
     * @return $this
     *
     * @see Single::chunkwiseOverlap()
     */
    public function chunkwiseOverlap(int $chunkSize, int $overlapSize): self
    {
        $this->iterable = Single::chunkwiseOverlap($this->iterable, $chunkSize, $overlapSize);
        return $this;
    }

    /**
     * Limit iteration to a max size limit
     *
     * @param int $limit
     *
     * @return $this
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
     * @return $this
     *
     * @see Single::map
     */
    public function map(callable $func): self
    {
        $this->iterable = Single::map($this->iterable, $func);
        return $this;
    }

    /**
     * Chain iterable source withs given iterables together into a single iteration.
     *
     * Makes a single continuous sequence out of multiple sequences.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return $this
     *
     * @see Multi::chain()
     */
    public function chainWith(iterable ...$iterables): self
    {
        $this->iterable = Multi::chain($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterate iterable source with another iterable collections simultaneously.
     *
     * Make an iterator that aggregates items from multiple iterators.
     * Similar to Python's zip function.
     *
     * For uneven lengths, iterations stops when the shortest iterable is exhausted.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return $this
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
     * @param iterable<mixed> ...$iterables
     *
     * @return $this
     *
     * @see Multi::zipLongest()
     */
    public function zipLongestWith(iterable ...$iterables): self
    {
        $this->iterable = Multi::zipLongest($this->iterable, ...$iterables);
        return $this;
    }

    /**
     * Iterate iterable source with another iterable collections of equal lengths simultaneously.
     *
     * Works like Multi::zip() method but throws \LengthException if lengths not equal,
     * i.e., at least one iterator ends before the others.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return $this
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
     *  - objects: compares serialized;
     *  - arrays: compares serialized.
     *
     * @param bool $strict
     *
     * @return $this
     *
     * @see Single::distinct()
     */
    public function distinct(bool $strict = true): self
    {
        $this->iterable = Set::distinct($this->iterable, $strict);
        return $this;
    }

    /**
     * Cycle through the elements of iterable source sequentially forever
     *
     * @return $this
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
     * @return $this
     *
     * @see Math::runningAverage()
     */
    public function runningAverage($initialValue = null): self
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
     * @return $this
     *
     * @see Math::runningDifference()
     */
    public function runningDifference($initialValue = null): self
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
     * @return $this
     *
     * @see Math::runningMax()
     */
    public function runningMax($initialValue = null): self
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
     * @return $this
     *
     * @see Math::runningMin()
     */
    public function runningMin($initialValue = null): self
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
     * @return $this
     *
     * @see Math::runningProduct()
     */
    public function runningProduct($initialValue = null): self
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
     * @return $this
     *
     * @see Math::runningTotal()
     */
    public function runningTotal($initialValue = null): self
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
     * @return $this
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
     *  - objects: compares serialized;
     *  - arrays: compares serialized.
     *
     * @param array<iterable<mixed>> ...$iterables
     *
     * @return $this
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
     * @return $this
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
     *  - objects: compares serialized;
     *  - arrays: compares serialized.
     *
     * @param iterable<mixed> ...$iterables
     *
     * @return $this
     *
     * @see Set::symmetricDifferenceCoercive()
     */
    public function symmetricDifferenceCoerciveWith(iterable ...$iterables): self
    {
        $this->iterable = Set::symmetricDifferenceCoercive($this->iterable, ...$iterables);
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
     * @return $this
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
     *  - objects: compares serialized;
     *  - arrays: compares serialized.
     *
     * @param positive-int $minIntersectionCount
     * @param array<iterable<mixed>> ...$iterables
     *
     * @return $this
     *
     * @see Set::partialIntersectionCoercive()
     */
    public function partialIntersectionCoerciveWith(int $minIntersectionCount, iterable ...$iterables): self
    {
        $this->iterable = Set::partialIntersectionCoercive($minIntersectionCount, $this->iterable, ...$iterables);
        return $this;
    }

    // STREAM TERMINAL OPERATIONS

    /**
     * Converts iterable source to array.
     *
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->iterable as $item) {
            $result[] = $item;
        }
        return $result;
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
    public function toFile($fileResource, string $newlineSeparator = \PHP_EOL, ?string $header = null, ?string $footer = null): void
    {
        ResourcePolicy::assertIsSatisfied($fileResource);

        $firstIteration = true;

        if ($header !== null) {
            \fputs($fileResource, $header . $newlineSeparator);
        }

        foreach ($this->iterable as $line) {
            if ($firstIteration) {
                $firstIteration = false;
            } else {
                $line = $newlineSeparator . \strval($line);
            }

            \fputs($fileResource, \strval($line));
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
        $fileResource,
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
     * Converts iterable source to an associative array.
     *
     * @param callable|null $keyFunc fn ($value, $key) => Custom Logic
     * @param callable|null $valueFunc fn ($value, $key) => Custom logic
     *
     * @return array<mixed>
     */
    public function toAssociativeArray(callable $keyFunc = null, callable $valueFunc = null): array
    {
        if ($keyFunc === null) {
            $keyFunc = fn ($item, $key) => $key;
        }
        if ($valueFunc === null) {
            $valueFunc = fn ($item, $key) => $item;
        }

        $result = [];
        foreach ($this->iterable as $key => $item) {
            $result[$keyFunc($item, $key)] = $valueFunc($item, $key);
        }
        return $result;
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
     * Returns true if exactly n items in the stream are true where the predicate function is true.
     *
     * Default predicate if not provided is the boolean value of each data item.
     *
     * @param int           $n
     * @param callable|null $predicate
     *
     * @return bool
     */
    public function exactlyN(int $n, callable $predicate = null): bool
    {
        return Summary::exactlyN($this->iterable, $n, $predicate);
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
     * Reduces iterable source to the mean average of its items.
     *
     * Returns null if iterable source is empty.
     *
     * @return int|float|null
     *
     * @see Reduce::toAverage()
     */
    public function toAverage()
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
     * Items of iterable source must be comparable.
     *
     * Returns null if iterable source is empty.
     *
     * @return mixed
     *
     * @see Reduce::toMax()
     */
    public function toMax()
    {
        return Reduce::toMax($this->iterable);
    }

    /**
     * Reduces iterable source to its min value.
     *
     * Items of iterable source must be comparable.
     *
     * Returns null if iterable source is empty.
     *
     * @return mixed
     *
     * @see Reduce::toMin()
     */
    public function toMin()
    {
        return Reduce::toMin($this->iterable);
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
    public function toProduct()
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
        $iterable = $this->iterable;
        return Reduce::toString($iterable, $separator, $prefix, $suffix);
    }

    /**
     * Reduces iterable source to the sum of its items.
     *
     * @return int|float
     *
     * @see Reduce::toSum()
     */
    public function toSum()
    {
        /** @var iterable<numeric> $iterable */
        $iterable = $this->iterable;
        return Reduce::toSum($iterable);
    }

    /**
     * Reduces iterable source to array of its upper and lower bounds.
     *
     * Returns [null, null] if iterable source is empty.
     *
     * @return array{numeric, numeric}|array{null, null}
     *
     * @see Reduce::toMinMax()
     */
    public function toMinMax(): array
    {
        /** @var iterable<numeric> $iterable */
        $iterable = $this->iterable;
        return Reduce::toMinMax($iterable);
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
    public function toRange()
    {
        /** @var iterable<numeric> $iterable */
        $iterable = $this->iterable;
        return Reduce::toRange($iterable);
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
    public function toValue(callable $reducer, $initialValue = null)
    {
        return Reduce::toValue($this->iterable, $reducer, $initialValue);
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
            print($item . \PHP_EOL);
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
        return IteratorFactory::makeIterator($this->iterable);
    }
}

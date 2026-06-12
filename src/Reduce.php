<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\NoValueMonad;
use IterTools\Util\UniqueExtractor;

final class Reduce
{
    /**
     * Reduces given collection like array_reduce() function.
     *
     * But unlike array_reduce(), it works with all iterable types.
     *
     * @template T
     * @param iterable<mixed> $data
     * @param callable        $reducer
     * @param T               $initialValue
     *
     * @return T
     */
    public static function toValue(iterable $data, callable $reducer, mixed $initialValue = null): mixed
    {
        /** @var mixed $carry */
        $carry = $initialValue;

        foreach ($data as $datum) {
            /** @var mixed $datum */
            $carry = $reducer($carry, $datum);
        }

        /** @var T */
        return $carry;
    }

    /**
     * Reduces given iterable to its min value.
     *
     * Optional callable param $compareBy must return comparable value.
     * If $compareBy is not provided then items of given collection must be comparable.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<mixed> $data
     * @param callable|null   $compareBy (optional) function to extract comparable value from element. Ex: $item->getSomeValue()
     *
     * @return mixed|null
     */
    public static function toMin(iterable $data, ?callable $compareBy = null): mixed
    {
        if ($compareBy !== null) {
            /** @var mixed|NoValueMonad $result */
            $result = static::toValue(
                $data,
                static function (mixed $carry, mixed $datum) use ($compareBy): mixed {
                    $comparableValue = $compareBy($datum);
                    if (\is_float($comparableValue) && \is_nan($comparableValue)) {
                        return $carry;
                    }
                    return ($carry instanceof NoValueMonad || $comparableValue < $compareBy($carry))
                        ? $datum
                        : $carry;
                },
                NoValueMonad::getInstance()
            );
        } else {
            /** @var mixed|NoValueMonad $result */
            $result = static::toValue(
                $data,
                static function (mixed $carry, mixed $datum): mixed {
                    if (\is_float($datum) && \is_nan($datum)) {
                        return $carry;
                    }
                    return ($carry instanceof NoValueMonad) ? $datum : \min($carry, $datum);
                },
                NoValueMonad::getInstance()
            );
        }

        return ($result instanceof NoValueMonad) ? null : $result;
    }

    /**
     * Reduces given iterable to its max value.
     *
     * Optional callable param $compareBy must return comparable value.
     * If $compareBy is not provided then items of given collection must be comparable.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<mixed> $data
     * @param callable|null   $compareBy (optional) function to extract comparable value from element. Ex: $item->getSomeValue()
     *
     * @return mixed|null
     */
    public static function toMax(iterable $data, ?callable $compareBy = null): mixed
    {
        if ($compareBy !== null) {
            /** @var mixed|NoValueMonad $result */
            $result = static::toValue(
                $data,
                static function (mixed $carry, mixed $datum) use ($compareBy): mixed {
                    $comparableValue = $compareBy($datum);
                    if (\is_float($comparableValue) && \is_nan($comparableValue)) {
                        return $carry;
                    }
                    return ($carry instanceof NoValueMonad || $comparableValue > $compareBy($carry))
                        ? $datum
                        : $carry;
                },
                NoValueMonad::getInstance()
            );
        } else {
            /** @var mixed|NoValueMonad $result */
            $result = static::toValue(
                $data,
                static function (mixed $carry, mixed $datum): mixed {
                    if (\is_float($datum) && \is_nan($datum)) {
                        return $carry;
                    }
                    return ($carry instanceof NoValueMonad) ? $datum : \max($carry, $datum);
                },
                NoValueMonad::getInstance()
            );
        }

        return ($result instanceof NoValueMonad) ? null : $result;
    }

    /**
     * Reduces given collection to array of its upper and lower bounds.
     *
     * Callable param $compareBy must return comparable value.
     *
     * If $compareBy is not proposed then items of given collection must be comparable.
     *
     * Returns [null, null] if given collection is empty.
     *
     * @param iterable<numeric> $numbers
     * @param callable|null     $compareBy
     *
     * @return array{numeric, numeric}|array{null, null}
     */
    public static function toMinMax(iterable $numbers, ?callable $compareBy = null): array
    {
        if ($compareBy !== null) {
            /** @var array{mixed|NoValueMonad, mixed|NoValueMonad} $result */
            $result = static::toValue($numbers, static function (array $carry, mixed $datum) use ($compareBy): array {
                $comparableValue = $compareBy($datum);
                if (\is_float($comparableValue) && \is_nan($comparableValue)) {
                    return $carry;
                }
                return [
                    ($carry[0] instanceof NoValueMonad || $comparableValue <= $compareBy($carry[0]))
                        ? $datum
                        : $carry[0],
                    ($carry[1] instanceof NoValueMonad || $comparableValue >= $compareBy($carry[1]))
                        ? $datum
                        : $carry[1],
                ];
            }, [NoValueMonad::getInstance(), NoValueMonad::getInstance()]);
        } else {
            /** @var array{mixed|NoValueMonad, mixed|NoValueMonad} $result */
            $result = static::toValue(
                $numbers,
                static function (array $carry, mixed $datum): array {
                    if (\is_float($datum) && \is_nan($datum)) {
                        return $carry;
                    }
                    return [
                        ($carry[0] instanceof NoValueMonad) ? $datum : \min($carry[0], $datum),
                        ($carry[1] instanceof NoValueMonad) ? $datum : \max($carry[1], $datum),
                    ];
                },
                [NoValueMonad::getInstance(), NoValueMonad::getInstance()]
            );
        }

        /** @var array{numeric, numeric}|array{null, null} */
        return [
            ($result[0] instanceof NoValueMonad) ? null : $result[0],
            ($result[1] instanceof NoValueMonad) ? null : $result[1],
        ];
    }

    /**
     * Reduces given iterable to its length.
     *
     * @param iterable<mixed> $data
     *
     * @return int
     */
    public static function toCount(iterable $data): int
    {
        if (\is_countable($data)) {
            return \count($data);
        }

        return static::toValue($data, fn (int $carry): int => $carry + 1, 0);
    }

    /**
     * Reduces given iterable to an array of counts keyed by the value returned
     * from the key function.
     *
     * Single pass over the input. The key function must return an int or string
     * (the only valid array key types); any other return type throws a \TypeError
     * naming the offending type. This avoids PHP's implicit array-key coercion
     * (deprecation notices for null and float keys, surprising bool→1/0 collapse).
     *
     * Note: PHP arrays coerce numeric-string keys to int — a key function that
     * returns the string "1" and one that returns the int 1 collapse into a single
     * int key 1 with the combined count. Callers needing to distinguish those should
     * use a different data structure upstream.
     *
     * @template T
     *
     * @param iterable<T>       $data
     * @param callable(T): mixed $keyFunc must return int|string at runtime
     *
     * @return array<int|string, int>
     *
     * @throws \TypeError if $keyFunc returns a value that is not int|string
     */
    public static function toCountBy(iterable $data, callable $keyFunc): array
    {
        $counts = [];

        foreach ($data as $datum) {
            $key = $keyFunc($datum);
            if (!\is_int($key) && !\is_string($key)) {
                throw new \TypeError(
                    \sprintf('Key function must return int|string, got %s', \gettype($key)),
                );
            }

            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        return $counts;
    }

    /**
     * Reduces given collection to the sum of its items.
     *
     * @param iterable<numeric> $data
     *
     * @return int|float
     * @phpstan-ignore return.unusedType
     */
    public static function toSum(iterable $data): int|float
    {
        /** @psalm-suppress MixedOperand */
        return static::toValue($data, fn (int|float $carry, mixed $datum): int|float => $carry + $datum, 0); // @phpstan-ignore binaryOp.invalid
    }

    /**
     * Reduces given collection to the product of its items.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<numeric> $data
     *
     * @return int|float|null
     * @phpstan-ignore return.unusedType, return.unusedType
     */
    public static function toProduct(iterable $data): int|float|null
    {
        /** @psalm-suppress MixedOperand */
        return static::toValue($data, fn (int|float|null $carry, mixed $datum): int|float => ($carry ?? 1) * $datum); // @phpstan-ignore binaryOp.invalid
    }

    /**
     * Reduces given collection to the mean average of its items.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<numeric> $data
     *
     * @return int|float|null
     */
    public static function toAverage(iterable $data): int|float|null
    {
        /**
         * @param array{int, int|float} $carry
         * @param int|float $datum
         * @return array{int, int|float}
         */
        /** @psalm-suppress MixedOperand */
        $accumulator = static function (array $carry, mixed $datum): array {
            /** @var int $count */
            /** @var int|float $sum */
            [$count, $sum] = $carry;
            /** @phpstan-ignore binaryOp.invalid */
            return [$count + 1, $sum + $datum];
        };

        /** @var array{int, int|float} $result */
        $result = static::toValue($data, $accumulator, [0, 0]);
        [$count, $sum] = $result;

        /** @psalm-suppress InvalidOperand */
        return $count ? ($sum / $count) : null;
    }

    /**
     * Reduces given collection to its median value.
     *
     * For an even number of elements the median is the linear interpolation
     * (mean) of the two middle values.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<int|float> $data
     *
     * @return int|float|null
     */
    public static function toMedian(iterable $data): int|float|null
    {
        $values = static::toSortedValues($data);

        $count = \count($values);
        if ($count === 0) {
            return null;
        }

        $mid = \intdiv($count, 2);

        if ($count % 2 === 1) {
            return $values[$mid];
        }

        /** @psalm-suppress InvalidOperand */
        return ($values[$mid - 1] + $values[$mid]) / 2;
    }

    /**
     * Reduces given collection to a list of its modes (the most frequent values).
     *
     * Returns every value tied for the maximum frequency, in first-seen order
     * (so an all-unique input returns all of its values). Multimodal inputs
     * return multiple modes.
     *
     * Returns an empty array if given collection is empty.
     *
     * @param iterable<mixed> $data
     *
     * @return list<mixed>
     */
    public static function toMode(iterable $data): array
    {
        /** @var array<string, int> $counts */
        $counts = [];
        /** @var array<string, mixed> $values */
        $values = [];

        foreach ($data as $datum) {
            $hash = UniqueExtractor::getString($datum, true);

            if (!\array_key_exists($hash, $counts)) {
                $counts[$hash] = 0;
                $values[$hash] = $datum;
            }

            $counts[$hash]++;
        }

        if (\count($counts) === 0) {
            return [];
        }

        $maxCount = \max($counts);

        /** @var list<mixed> $modes */
        $modes = [];
        foreach ($counts as $hash => $count) {
            if ($count === $maxCount) {
                /** @var mixed $mode */
                $mode = $values[$hash];
                $modes[] = $mode;
            }
        }

        return $modes;
    }

    /**
     * Reduces given collection to the variance of its values.
     *
     * Population variance by default; pass $sample = true for the sample variance
     * (Bessel's correction — divides by N - 1).
     *
     * Returns null if given collection is empty. Also returns null for the sample
     * variance of a single value (N - 1 = 0 is undefined). The population variance
     * of a single value is 0.0.
     *
     * @param iterable<int|float> $data
     * @param bool                $sample population variance when false (default), sample variance when true
     *
     * @return float|null
     */
    public static function toVariance(iterable $data, bool $sample = false): ?float
    {
        $values = static::toNumericList($data);

        $count = \count($values);
        if ($count === 0 || ($sample && $count === 1)) {
            return null;
        }

        /** @psalm-suppress InvalidOperand */
        $mean = \array_sum($values) / $count;

        $sumSquaredDiffs = 0.0;
        foreach ($values as $value) {
            /** @psalm-suppress InvalidOperand */
            $sumSquaredDiffs += ($value - $mean) ** 2;
        }

        $divisor = $sample ? $count - 1 : $count;

        /** @psalm-suppress InvalidOperand */
        return $sumSquaredDiffs / $divisor;
    }

    /**
     * Reduces given collection to the standard deviation of its values.
     *
     * Square root of the variance. Population standard deviation by default; pass
     * $sample = true for the sample standard deviation (Bessel's correction).
     *
     * Returns null whenever the underlying variance is null (empty collection, or
     * the sample standard deviation of a single value). The population standard
     * deviation of a single value is 0.0.
     *
     * @param iterable<int|float> $data
     * @param bool                $sample population standard deviation when false (default), sample when true
     *
     * @return float|null
     */
    public static function toStandardDeviation(iterable $data, bool $sample = false): ?float
    {
        $variance = static::toVariance($data, $sample);

        return $variance === null ? null : \sqrt($variance);
    }

    /**
     * Reduces given collection to its value at the given percentile.
     *
     * Uses the R-7 / linear-interpolation method (the NumPy default): the
     * percentile rank is mapped onto the sorted values and interpolated between
     * the two nearest ranks. Percentile 0 is the minimum, 100 is the maximum.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<int|float> $data
     * @param float               $percentile in the inclusive range [0, 100]
     *
     * @return int|float|null
     *
     * @throws \InvalidArgumentException if $percentile is outside [0, 100] or NAN
     */
    public static function toPercentile(iterable $data, float $percentile): int|float|null
    {
        if (\is_nan($percentile) || $percentile < 0 || $percentile > 100) {
            $shown = \is_nan($percentile) ? 'NAN' : (string) $percentile;
            throw new \InvalidArgumentException("Percentile must be between 0 and 100. Got {$shown}.");
        }

        $values = static::toSortedValues($data);

        $count = \count($values);
        if ($count === 0) {
            return null;
        }

        if ($count === 1) {
            return $values[0];
        }

        /** @psalm-suppress InvalidOperand */
        $rank       = ($count - 1) * ($percentile / 100);
        $lowerIndex = (int) \floor($rank);
        /** @psalm-suppress InvalidOperand */
        $fraction   = $rank - $lowerIndex;

        if ($fraction === 0.0) {
            return $values[$lowerIndex];
        }

        /** @psalm-suppress InvalidOperand */
        return $values[$lowerIndex] + $fraction * ($values[$lowerIndex + 1] - $values[$lowerIndex]);
    }

    /**
     * Reduces given collection to its value at the given quantile.
     *
     * Thin wrapper over {@see Reduce::toPercentile()} that accepts a quantile in the
     * inclusive range [0, 1] (e.g. 0.25 is the first quartile / 25th percentile).
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<int|float> $data
     * @param float               $quantile in the inclusive range [0, 1]
     *
     * @return int|float|null
     *
     * @throws \InvalidArgumentException if $quantile is outside [0, 1] or NAN
     */
    public static function toQuantile(iterable $data, float $quantile): int|float|null
    {
        if (\is_nan($quantile) || $quantile < 0 || $quantile > 1) {
            $shown = \is_nan($quantile) ? 'NAN' : (string) $quantile;
            throw new \InvalidArgumentException("Quantile must be between 0 and 1. Got {$shown}.");
        }

        return static::toPercentile($data, $quantile * 100.0);
    }

    /**
     * Materializes an iterable of numbers into a list.
     *
     * @param iterable<int|float> $data
     *
     * @return list<int|float>
     */
    private static function toNumericList(iterable $data): array
    {
        $values = [];
        foreach ($data as $datum) {
            $values[] = $datum;
        }

        return $values;
    }

    /**
     * Materializes an iterable of numbers into an ascending-sorted list.
     *
     * @param iterable<int|float> $data
     *
     * @return list<int|float>
     */
    private static function toSortedValues(iterable $data): array
    {
        $values = static::toNumericList($data);
        \sort($values);

        return $values;
    }

    /**
     * Reduces to a string with optional glue, prefix, and suffix.
     *
     * Returns empty string (with optional prefix and suffix) if collection is empty.
     *
     * @param iterable<mixed> $data
     * @param string          $separator (optional) inserted between each item. Ex: ', ' for 1, 2, 3, ...
     * @param string          $prefix (optional) prepended to string
     * @param string          $suffix (optional) appended to string
     *
     * @return string
     */
    public static function toString(iterable $data, string $separator = '', string $prefix = '', string $suffix = ''): string
    {
        /** @var list<string|int|float> $items */
        $items = [];
        foreach ($data as $datum) {
            /** @var string|int|float $datum */
            $items[] = $datum;
        }

        $joined = \implode($separator, $items);
        return $prefix . $joined . $suffix;
    }

    /**
     * Reduces given collection to its range.
     *
     * Returns 0 if given collection is empty.
     *
     * @param iterable<numeric> $numbers
     *
     * @return int|float
     */
    public static function toRange(iterable $numbers): int|float
    {
        [$min, $max] = static::toMinMax($numbers);

        /** @psalm-suppress InvalidOperand */
        return ($max ?? 0) - ($min ?? 0);
    }

    /**
     * Reduces given collection to its first value.
     *
     * @param iterable<mixed> $data
     * @return mixed
     *
     * @throws \LengthException if collection is empty
     */
    public static function toFirst(iterable $data): mixed
    {
        foreach ($data as $datum) {
            /** @var mixed $datum */
            return $datum;
        }

        throw new \LengthException('collection is empty');
    }

    /**
     * Reduces given collection to its last value.
     *
     * @param iterable<mixed> $data
     * @return mixed
     *
     * @throws \LengthException if collection is empty
     */
    public static function toLast(iterable $data): mixed
    {
        /** @var mixed|NoValueMonad $result */
        $result = static::toValue($data, fn (mixed $carry, mixed $datum): mixed => $datum, NoValueMonad::getInstance());

        if ($result instanceof NoValueMonad) {
            throw new \LengthException('collection is empty');
        }

        return $result;
    }

    /**
     * Reduces given collection to its first and last values.
     *
     * @param iterable<mixed> $data
     * @return array{mixed, mixed}
     *
     * @throws \LengthException if collection is empty
     */
    public static function toFirstAndLast(iterable $data): array
    {
        return [static::toFirst($data), static::toLast($data)];
    }

    /**
     * Reduces given collection random value in from within it.
     *
     * @param iterable<mixed> $data
     *
     * @return mixed
     *
     * @throws \LengthException if given iterable is empty
     */
    public static function toRandomValue(iterable $data): mixed
    {
        if (\is_countable($data)) {
            if (\count($data) === 0) {
                throw new \LengthException('Given iterable must be non-empty');
            }

            $targetIndex = \mt_rand(0, \count($data) - 1);

            $index = 0;
            foreach ($data as $datum) {
                /** @var mixed $datum */
                if ($targetIndex === $index) {
                    return $datum;
                }

                ++$index;
            }
        }

        $data = Transform::toArray($data);

        if (\count($data) === 0) {
            throw new \LengthException('Given iterable must be non-empty');
        }

        return $data[\array_rand($data)];
    }

    /**
     * Reduces given iterable to the first element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast, matching Summary::allMatch/anyMatch.
     *
     * Short-circuits on first match — does not consume the rest of the iterable.
     *
     * If no element matches, returns $default (null by default).
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     * @param mixed           $default value returned when no element matches
     *
     * @return mixed
     */
    public static function toFirstMatch(iterable $data, callable $predicate, mixed $default = null): mixed
    {
        foreach ($data as $datum) {
            /** @var mixed $datum */
            if ((bool) $predicate($datum)) {
                return $datum;
            }
        }

        return $default;
    }

    /**
     * Reduces given iterable to the zero-based position of the first element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast, matching Summary::allMatch/anyMatch.
     *
     * Short-circuits on first match — does not consume the rest of the iterable.
     *
     * If no element matches, returns $default (null by default).
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     * @param mixed           $default value returned when no element matches
     *
     * @return mixed
     */
    public static function toFirstMatchIndex(iterable $data, callable $predicate, mixed $default = null): mixed
    {
        $index = 0;
        foreach ($data as $datum) {
            /** @var mixed $datum */
            if ((bool) $predicate($datum)) {
                return $index;
            }
            ++$index;
        }

        return $default;
    }

    /**
     * Reduces given iterable to the source key of the first element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast, matching Summary::allMatch/anyMatch.
     *
     * Short-circuits on first match — does not consume the rest of the iterable.
     *
     * If no element matches, returns $default (null by default).
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     * @param mixed           $default value returned when no element matches
     *
     * @return mixed
     */
    public static function toFirstMatchKey(iterable $data, callable $predicate, mixed $default = null): mixed
    {
        foreach ($data as $key => $datum) {
            /** @var mixed $datum */
            if ((bool) $predicate($datum)) {
                return $key;
            }
        }

        return $default;
    }

    /**
     * Reduces given iterable to the last element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast, matching Summary::allMatch/anyMatch.
     *
     * Consumes the entire iterable (cannot short-circuit).
     *
     * If no element matches, returns $default (null by default).
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     * @param mixed           $default value returned when no element matches
     *
     * @return mixed
     */
    public static function toLastMatch(iterable $data, callable $predicate, mixed $default = null): mixed
    {
        /** @var mixed $result */
        $result = $default;
        foreach ($data as $datum) {
            /** @var mixed $datum */
            if ((bool) $predicate($datum)) {
                $result = $datum;
            }
        }

        return $result;
    }

    /**
     * Reduces given iterable to the zero-based position of the last element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast, matching Summary::allMatch/anyMatch.
     *
     * Consumes the entire iterable (cannot short-circuit).
     *
     * If no element matches, returns $default (null by default).
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     * @param mixed           $default value returned when no element matches
     *
     * @return mixed
     */
    public static function toLastMatchIndex(iterable $data, callable $predicate, mixed $default = null): mixed
    {
        /** @var mixed $result */
        $result = $default;
        $index  = 0;
        foreach ($data as $datum) {
            /** @var mixed $datum */
            if ((bool) $predicate($datum)) {
                $result = $index;
            }
            ++$index;
        }

        return $result;
    }

    /**
     * Reduces given iterable to the source key of the last element matching the predicate.
     *
     * Predicate return value is coerced via (bool) cast, matching Summary::allMatch/anyMatch.
     *
     * Consumes the entire iterable (cannot short-circuit).
     *
     * If no element matches, returns $default (null by default).
     *
     * @param iterable<mixed> $data
     * @param callable        $predicate
     * @param mixed           $default value returned when no element matches
     *
     * @return mixed
     */
    public static function toLastMatchKey(iterable $data, callable $predicate, mixed $default = null): mixed
    {
        /** @var mixed $result */
        $result = $default;
        foreach ($data as $key => $datum) {
            /** @var mixed $datum */
            if ((bool) $predicate($datum)) {
                /** @var mixed $result */
                $result = $key;
            }
        }

        return $result;
    }

    /**
     * Reduces given iterable to its sole element.
     *
     * Throws \LengthException if the iterable contains zero or two-or-more elements.
     *
     * Compose with Stream::filter()->toOnly() if you need a predicate variant.
     *
     * @param iterable<mixed> $data
     *
     * @return mixed
     *
     * @throws \LengthException if iterable is empty or contains more than one element
     */
    public static function toOnly(iterable $data): mixed
    {
        $result = NoValueMonad::getInstance();
        $found  = false;
        foreach ($data as $datum) {
            if ($found) {
                throw new \LengthException('iterable must contain exactly one element; found 2 or more');
            }
            /** @var mixed $result */
            $result = $datum;
            $found  = true;
        }

        if (!$found) {
            throw new \LengthException('iterable must contain exactly one element; found 0');
        }

        return $result;
    }

    /**
     * Drains the given iterable, discarding values.
     *
     * Useful for forcing evaluation of a lazy pipeline whose only purpose is its side effects
     * (e.g. a side-effectful Single::map() or a Generator that writes to a log).
     *
     * @param iterable<mixed> $data
     *
     * @return void
     */
    public static function consume(iterable $data): void
    {
        foreach ($data as $_) {
            // intentionally empty
        }
    }

    /**
     * Reduces given iterable to the value at the nth position.
     *
     * @template T
     *
     * @param iterable<T> $data
     * @param int $position
     *
     * @return T
     *
     * @throws \LengthException if given iterable does not contain item with target position.
     */
    public static function toNth(iterable $data, int $position): mixed
    {
        if ($position < 0) {
            throw new \InvalidArgumentException("Position must be non-negative. Got {$position}.");
        }

        if (\is_countable($data) && \count($data) <= $position) {
            throw new \LengthException("Given iterable does not contain item with position {$position}");
        }

        $i = 0;
        foreach ($data as $datum) {
            if ($i === $position) {
                return $datum;
            }
            ++$i;
        }

        throw new \LengthException("Given iterable does not contain item with position {$position}");
    }
}

<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\NoValueMonad;

class Reduce
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
    public static function toValue(iterable $data, callable $reducer, $initialValue = null)
    {
        $carry = $initialValue;

        foreach ($data as $datum) {
            $carry = $reducer($carry, $datum);
        }

        return $carry;
    }

    /**
     * Reduces given iterable to its min value.
     *
     * If comparator is null then items of given collection must be comparable.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<mixed> $data
     * @param callable|null   $comparator
     *
     * @return mixed|null
     */
    public static function toMin(iterable $data, ?callable $comparator = null)
    {
        if ($comparator !== null) {
            return static::toValue(
                $data,
                fn ($carry, $datum) => $comparator($datum, $carry ?? $datum) <= 0 ? $datum : $carry
            );
        }

        return static::toValue($data, fn ($carry, $datum) => \min($carry ?? $datum, $datum));
    }

    /**
     * Reduces given iterable to its max value.
     *
     * If comparator is null then items of given collection must be comparable.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<mixed> $data
     * @param callable|null   $comparator
     *
     * @return mixed|null
     */
    public static function toMax(iterable $data, ?callable $comparator = null)
    {
        if ($comparator !== null) {
            return static::toValue(
                $data,
                fn ($carry, $datum) => $comparator($datum, $carry ?? $datum) >= 0 ? $datum : $carry
            );
        }

        return static::toValue($data, fn ($carry, $datum) => \max($carry ?? $datum, $datum));
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

        return static::toValue($data, fn ($carry) => $carry + 1, 0);
    }

    /**
     * Reduces given collection to the sum of its items.
     *
     * @param iterable<numeric> $data
     *
     * @return int|float
     */
    public static function toSum(iterable $data)
    {
        return static::toValue($data, fn ($carry, $datum) => $carry + $datum, 0);
    }

    /**
     * Reduces given collection to the product of its items.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<numeric> $data
     *
     * @return int|float|null
     */
    public static function toProduct(iterable $data)
    {
        return static::toValue($data, fn ($carry, $datum) => ($carry ?? 1) * $datum);
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
    public static function toAverage(iterable $data)
    {
        [$count, $sum] = static::toValue($data, static function ($carry, $datum): array {
            [$count, $sum] = $carry;
            return [$count + 1, $sum + $datum];
        }, [0, 0]);

        return $count ? ($sum / $count) : null;
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
        $items = [];
        foreach ($data as $datum) {
            $items[] = $datum;
        }

        $joined = \implode($separator, $items);
        return $prefix . $joined . $suffix;
    }

    /**
     * Reduces given collection to array of its upper and lower bounds.
     *
     * If comparator is null then items of given collection must be comparable.
     *
     * Returns [null, null] if given collection is empty.
     *
     * @param iterable<numeric> $numbers
     * @param callable|null   $comparator
     *
     * @return array{numeric, numeric}|array{null, null}
     */
    public static function toMinMax(iterable $numbers, ?callable $comparator = null): array
    {
        if ($comparator !== null) {
            return static::toValue($numbers, static function ($carry, $datum) use ($comparator) {
                return [
                    $comparator($datum, $carry[0] ?? $datum) <= 0 ? $datum : $carry[0],
                    $comparator($datum, $carry[1] ?? $datum) >= 0 ? $datum : $carry[1],
                ];
            }, [null, null]);
        }

        return static::toValue(
            $numbers,
            fn ($carry, $datum) => [\min($carry[0] ?? $datum, $datum), \max($carry[1] ?? $datum, $datum)],
            [null, null]
        );
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
    public static function toRange(iterable $numbers)
    {
        [$min, $max] = static::toMinMax($numbers);

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
    public static function toFirst(iterable $data)
    {
        foreach ($data as $datum) {
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
    public static function toLast(iterable $data)
    {
        /** @var mixed|NoValueMonad $result */
        $result = static::toValue($data, fn ($carry, $datum) => $datum, NoValueMonad::getInstance());

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
}

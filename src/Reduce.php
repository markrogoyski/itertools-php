<?php

declare(strict_types=1);

namespace IterTools;

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
     * Items of given collection must be comparable.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<mixed> $data
     *
     * @return mixed|null
     */
    public static function toMin(iterable $data)
    {
        return static::toValue($data, static function ($carry, $datum) {
            return \min($carry ?? $datum, $datum);
        });
    }

    /**
     * Reduces given iterable to its max value.
     *
     * Items of given collection must be comparable.
     *
     * Returns null if given collection is empty.
     *
     * @param iterable<mixed> $data
     *
     * @return mixed|null
     */
    public static function toMax(iterable $data)
    {
        return static::toValue($data, static function ($carry, $datum) {
            return \max($carry ?? $datum, $datum);
        });
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

        return static::toValue($data, static function ($carry) {
            return $carry + 1;
        }, 0);
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
        return static::toValue($data, static function ($carry, $datum) {
            return $carry + $datum;
        }, 0);
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
        return static::toValue($data, static function ($carry, $datum) {
            return ($carry ?? 1) * $datum;
        });
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
     * Reduces given collection to the vector length using items as coordinates.
     *
     * Returns 0 if given collection is empty.
     *
     * @param iterable<numeric> $vector
     *
     * @return float
     */
    public static function toVectorLength(iterable $vector): float
    {
        $result = 0;
        foreach ($vector as $coordinate) {
            $result += $coordinate**2;
        }
        return sqrt($result);
    }
}

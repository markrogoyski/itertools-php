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
     * Reduces given iterable to it's min value.
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
            return min($carry ?? $datum, $datum);
        });
    }

    /**
     * Reduces given iterable to it's max value.
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
            return max($carry ?? $datum, $datum);
        });
    }

    /**
     * Reduces given iterable to it's length.
     *
     * @param iterable<mixed> $data
     *
     * @return int
     */
    public static function toCount(iterable $data): int
    {
        if (is_countable($data)) {
            return count($data);
        }

        return static::toValue($data, static function ($carry) {
            return $carry+1;
        }, 0);
    }

    /**
     * Reduces given collection to the sum of it's items.
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
     * Reduces given collection to the product of it's items.
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
     * Reduces given collection to the average of it's items.
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
     * Returns true if given collection is sorted directly otherwise false.
     *
     * Items of given collection must be comparable.
     *
     * Returns true if given collection is empty or has one element.
     *
     * @param iterable<mixed> $data
     *
     * @return bool
     */
    public static function isSorted(iterable $data): bool
    {
        foreach (Single::pairwise($data) as [$lhs, $rhs]) {
            if ($rhs < $lhs) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if given collection is sorted reversely otherwise false.
     *
     * Items of given collection must be comparable.
     *
     * Returns true if given collection is empty or has one element.
     *
     * @param iterable<mixed> $data
     *
     * @return bool
     */
    public static function isReversed(iterable $data): bool
    {
        foreach (Single::pairwise($data) as [$lhs, $rhs]) {
            if ($rhs > $lhs) {
                return false;
            }
        }

        return true;
    }
}

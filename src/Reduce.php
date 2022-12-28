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
            return min($carry ?? $datum, $datum);
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
            return max($carry ?? $datum, $datum);
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
            return count($data);
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
     * Returns true if given collection is exactly the same as another ones.
     *
     * @param iterable<mixed> $data
     * @param iterable<mixed> $n
     * @param iterable<mixed> ...$extra
     *
     * @return bool
     */
    public static function exactlyN(iterable $data, iterable $n, iterable ...$extra): bool
    {
        $iterables = [$data, $n, ...$extra];

        try {
            foreach (Multi::zipStrict(...$iterables) as $values) {
                foreach (Single::pairwise($values) as [$lhs, $rhs]) {
                    if ($lhs !== $rhs) {
                        return false;
                    }
                }
            }
        } catch (\OutOfRangeException $e) {
            return false;
        }

        return true;

        // FIXME: remove this code
        // =========================================== //
        // OLD REALISATION WITHOUT Single::zipStrict() //
        // =========================================== //

        //$iterators = array_map(static function (iterable $iterable): \Iterator {
        //    $iterator = Util::makeIterator($iterable);
        //
        //    if ($iterator instanceof \IteratorIterator) {
        //        $iterator->rewind();
        //    }
        //
        //    return $iterator;
        //}, [$data, $n, ...$extra]);
        //
        //while (true) {
        //    $statuses = array_map(static function (\Iterator $iterator) {
        //        return $iterator->valid();
        //    }, $iterators);
        //
        //    /** @var array{bool}|array{bool, bool} $uniqueStatuses */
        //    $uniqueStatuses = iterator_to_array(Single::filterUnique($statuses));
        //
        //    if (count($uniqueStatuses) > 1) {
        //        return false;
        //    }
        //
        //    if (!$uniqueStatuses[0]) {
        //        return true;
        //    }
        //
        //    /**
        //     * @var \Iterator $lhs
        //     * @var \Iterator $rhs
        //     */
        //    foreach (Single::pairwise($iterators) as [$lhs, $rhs]) {
        //        if ($lhs->current() !== $rhs->current()) {
        //            return false;
        //        }
        //    }
        //
        //    foreach ($iterators as $iterator) {
        //        $iterator->next();
        //    }
        //}
    }
}

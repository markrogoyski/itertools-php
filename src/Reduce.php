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
    public static function toMin(iterable $data, callable $compareBy = null)
    {
        if ($compareBy !== null) {
            return static::toValue(
                $data,
                fn ($carry, $datum) => $compareBy($datum) < $compareBy($carry ?? $datum)
                    ? $datum
                    : $carry ?? $datum
            );
        }

        return static::toValue($data, fn ($carry, $datum) => \min($carry ?? $datum, $datum));
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
    public static function toMax(iterable $data, callable $compareBy = null)
    {
        if ($compareBy !== null) {
            return static::toValue(
                $data,
                fn ($carry, $datum) => $compareBy($datum) > $compareBy($carry ?? $datum)
                    ? $datum
                    : $carry ?? $datum
            );
        }

        return static::toValue($data, fn ($carry, $datum) => \max($carry ?? $datum, $datum));
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
    public static function toMinMax(iterable $numbers, callable $compareBy = null): array
    {
        if ($compareBy !== null) {
            return static::toValue($numbers, static function (array $carry, $datum) use ($compareBy) {
                return [
                    $compareBy($datum) <= $compareBy($carry[0] ?? $datum)
                        ? $datum
                        : $carry[0] ?? $datum,
                    $compareBy($datum) >= $compareBy($carry[1] ?? $datum)
                        ? $datum
                        : $carry[1] ?? $datum,
                ];
            }, [null, null]);
        }

        return static::toValue(
            $numbers,
            fn ($carry, $datum) => [
                \min($carry[0] ?? $datum, $datum),
                \max($carry[1] ?? $datum, $datum)
            ],
            [null, null]
        );
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

    /**
     * Reduces given collection to value of it's random element.
     *
     * @param iterable<mixed> $data
     *
     * @return mixed
     *
     * @throws \LengthException if given iterable is empty
     */
    public static function toRandomValue(iterable $data)
    {
        if (\is_countable($data)) {
            if (\count($data) === 0) {
                throw new \LengthException('Given iterable must be non-empty');
            }

            $targetIndex = \mt_rand(0, \count($data) - 1);

            $index = 0;
            foreach ($data as $datum) {
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

        return $data[array_rand($data)];
    }
}

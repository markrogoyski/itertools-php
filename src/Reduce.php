<?php

declare(strict_types=1);

namespace IterTools;

use IterTools\Util\NoValueMonad;

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

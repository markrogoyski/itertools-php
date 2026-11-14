<?php

declare(strict_types=1);

namespace IterTools\Util\Values;

/**
 * @internal
 * The library's one coercion of an element or a predicate's return value to bool.
 */
final class Truthiness
{
    /**
     * Coerces a value to bool as a (bool) cast does: a float NAN is true.
     *
     * PHP 8.5 raises "unexpected NAN value was coerced to bool" for (bool) NAN, so a NAN is answered
     * before the cast, the way the string conversions write a NAN as 'NAN' before converting.
     *
     * For anything but a float, this is (bool) $value. A loop that coerces every element or predicate result
     * writes `\is_float($x) ? Truthiness::of($x) : (bool) $x`, which skips the call for all but floats.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function of(mixed $value): bool
    {
        return (\is_float($value) && \is_nan($value)) || (bool) $value;
    }
}

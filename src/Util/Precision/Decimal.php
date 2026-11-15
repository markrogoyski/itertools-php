<?php

declare(strict_types=1);

namespace IterTools\Util\Precision;

/**
 * @internal
 * Shortest round-tripping decimal representation of a float.
 */
final class Decimal
{
    /**
     * Whether an integral float past 2^53 is its own shortest decimal: the shortest one that reads back as it.
     *
     * The shortest decimal is a few digits D times 10^k. It is exact when 10^k divides the float and D is the
     * quotient, which needs k <= 22, as 5^23 doesn't divide a 53-bit significand.
     *
     * @param float $x integral, past 2^53
     *
     * @return bool
     */
    public static function isShortestDecimal(float $x): bool
    {
        [$significand, $exponent, $digits] = self::shortestDecimal($x);
        $power = $exponent - $digits;
        if ($power < 0 || $power > 22) {
            return false;
        }
        $scale = (float)('1e' . $power);
        $magnitude = \abs($x);

        // The digits are compared as strings: they may be more than a 32-bit int holds. The quotient is an integral
        // float, exact as 10^$power divides $magnitude, which %.0f writes exactly.
        return \fmod($magnitude, $scale) === 0.0
            && \ltrim(\str_replace('.', '', $significand), '0') === \ltrim(\sprintf('%.0f', $magnitude / $scale), '0');
    }

    /**
     * The shortest decimal that reads back as |$x|, as its significand d.ddd, its exponent and the number of digits
     * after the point.
     *
     * @param float $x finite and nonzero
     *
     * @return array{string, int, int}
     */
    public static function shortestDecimal(float $x): array
    {
        $magnitude = \abs($x);
        $digits = 0;
        // 17 digits always read back, so the loop ends by then.
        while (true) {
            [$significand, $exponent] = \explode('e', \sprintf('%.' . $digits . 'e', $magnitude));
            $integer = \str_replace('.', '', $significand);
            $power = (int)$exponent - $digits;
            // Just below a power of two, floats are twice as dense as just above it, so the decimal nearest $x can
            // read back as a neighbour of $x while the next decimal up or down at that length reads back as $x:
            // 2^-77 is 6.6174449004242214e-24, and 6.617444900424221e-24 reads back as the float below it, while
            // 6.617444900424222e-24 reads back as 2^-77.
            $candidates = [$integer, self::incrementDigits($integer), self::decrementDigits($integer)];
            foreach ($candidates as $candidate) {
                if ((float)($candidate . 'e' . $power) === $magnitude) {
                    return self::decimalParts($candidate, $power);
                }
            }
            ++$digits;
        }
    }

    /**
     * A decimal integer written in digits, plus one, in digits: the operands of shortestDecimal() have up to 17
     * digits, more than a 32-bit int holds.
     *
     * @param string $integer digits
     *
     * @return string
     */
    private static function incrementDigits(string $integer): string
    {
        $length = \strlen($integer);
        $position = $length - 1;
        while ($position >= 0 && $integer[$position] === '9') {
            --$position;
        }
        if ($position < 0) {
            return '1' . \str_repeat('0', $length);
        }

        return \substr($integer, 0, $position)
            . (string)((int)$integer[$position] + 1)
            . \str_repeat('0', $length - $position - 1);
    }

    /**
     * A positive decimal integer written in digits, minus one, in digits, which may keep a leading zero.
     *
     * @param string $integer digits, not all zeros
     *
     * @return string
     */
    private static function decrementDigits(string $integer): string
    {
        $length = \strlen($integer);
        $position = $length - 1;
        while ($position > 0 && $integer[$position] === '0') {
            --$position;
        }

        return \substr($integer, 0, $position)
            . (string)((int)$integer[$position] - 1)
            . \str_repeat('9', $length - $position - 1);
    }

    /**
     * The decimal $integer times 10^$power, as shortestDecimal() returns it: its significand d.ddd without leading
     * or trailing zeros, its exponent and the number of digits after the point.
     *
     * @param string $integer digits, not all zeros
     * @param int    $power
     *
     * @return array{string, int, int}
     */
    private static function decimalParts(string $integer, int $power): array
    {
        $integer = \ltrim($integer, '0');
        $trimmed = \rtrim($integer, '0');
        $power += \strlen($integer) - \strlen($trimmed);
        $length = \strlen($trimmed);
        $significand = $length > 1 ? $trimmed[0] . '.' . \substr($trimmed, 1) : $trimmed;

        return [$significand, $power + $length - 1, $length - 1];
    }
}

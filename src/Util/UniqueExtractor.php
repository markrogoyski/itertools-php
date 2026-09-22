<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 * Tool for extracting unique IDs and hashes of any PHP variables and data structures.
 *
 * This class is the equality kernel behind the library's strict and coercive comparisons.
 *
 * Strict mode ($strict = true): two values are equal iff $a === $b, except NAN equals NAN at
 * any depth. Scalars compare by type and exact value (floats bit-exact, -0.0 === 0.0).
 * Objects, closures, and generators compare by instance; resources compare by id, open or
 * closed. Arrays compare recursively: same keys in the same order, values compared under
 * these same rules.
 *
 * Coercive mode ($strict = false): numeric equivalence. int/float/bool/null/''/numeric
 * strings compare by numeric value (subject to PHP's numeric-string rules at the integer
 * boundary); non-numeric strings compare by exact content; NAN equals NAN. Objects compare
 * via serialize() and throw \InvalidArgumentException when not serializable — a documented
 * limitation, since serialize() has its own precision and resource-encoding rules. Closures
 * and generators still compare by instance; resources still compare by id. Arrays compare
 * recursively under these coercive rules.
 *
 * Limits: arrays nested deeper than the depth limit (see MAX_DEPTH) throw
 * \InvalidArgumentException. This is a depth limit, not cycle detection.
 *
 * See README.md, section "Strict and Coercive Types".
 *
 * Based on PHP Type Tool's UniqueExtractor.
 * Contributed to IterTools by @author Smoren <ofigate@gmail.com>
 * @see https://github.com/Smoren/type-tools-php
 */
final class UniqueExtractor
{
    /**
     * @internal
     * Returns unique ID string of given variable by its value and type.
     *
     * Follows the strict or coercive contract documented on this class and in README.md,
     * section "Strict and Coercive Types".
     *
     * @param mixed $var
     * @param bool $strict
     *
     * @return string
     *
     * @psalm-suppress MixedArgument, InvalidOperand
     */
    public static function getString(mixed $var, bool $strict): string
    {
        return match (true) {
            \is_array($var) => 'array_' . \serialize($var),
            \is_resource($var) => 'resource_' . \get_resource_type($var) . '_' . (string) $var,
            $var instanceof \Generator => 'generator_' . \spl_object_id($var),
            $var instanceof \Closure => 'closure_' . \spl_object_id($var),
            \is_object($var) => 'object_' . ($strict ? \spl_object_id($var) : self::serializeObject($var)),
            \is_float($var) && \is_nan($var) => 'double_NAN',
            $strict && \is_bool($var) => 'boolean_' . \intval($var),
            /** @phpstan-ignore cast.string */
            $strict => \gettype($var) . '_' . (string) $var,
            \is_bool($var) => 'numeric_' . self::normalizeNumeric(\floatval($var)),
            \is_numeric($var) => 'numeric_' . self::normalizeNumeric(\floatval($var)),
            $var === null || $var === '' => 'numeric_0',
            /** @phpstan-ignore cast.string */
            default => 'scalar_' . (string) $var,
        };
    }

    private static function normalizeNumeric(float $value): string
    {
        return $value === 0.0 ? '0' : (string) $value;
    }

    /**
     * @throws \InvalidArgumentException if the object cannot be serialized
     */
    private static function serializeObject(object $var): string
    {
        try {
            return \serialize($var);
        } catch (\Throwable $e) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Object of class %s cannot be serialized for non-strict comparison. '
                    . 'Use strict mode or pass a serializable object.',
                    $var::class,
                ),
                0,
                $e,
            );
        }
    }
}

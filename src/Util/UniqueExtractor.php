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
 * Keys derived from an identity are only meaningful while that identity is alive: see
 * {@see Identity} and {@see self::identify()}, which returns the key together with the values it
 * was derived from.
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
     * The key format is internal: it encodes the value as `type:payload` and may change. Only
     * equality and inequality of keys is meaningful, never their spelling.
     *
     * @param mixed $var
     * @param bool $strict
     *
     * @return string
     */
    public static function getString(mixed $var, bool $strict): string
    {
        $anchors = [];

        return self::key($var, $strict, $anchors);
    }

    /**
     * @internal
     * Returns the unique ID string of given variable together with the values it was derived from.
     *
     * Same key as {@see self::getString()}, plus every value that was hashed by identity while
     * computing it. A consumer that keeps the key must keep the {@see Identity} — see that class
     * for why an id alone is not enough to tell two values apart over time.
     *
     * @param mixed $var
     * @param bool $strict
     *
     * @return Identity
     */
    public static function identify(mixed $var, bool $strict): Identity
    {
        $anchors = [];
        $key = self::key($var, $strict, $anchors);

        return new Identity($key, $anchors);
    }

    /**
     * Key of any value, appending every value hashed by identity to $anchors.
     *
     * This is the single routine behind both entry points: getString() throws the collected
     * anchors away, identify() hands them to the caller. Values hashed by content (scalars,
     * strings, serialized objects) contribute no anchors.
     *
     * @param mixed $var
     * @param bool $strict
     * @param list<object|resource> $anchors
     *
     * @return string
     *
     * @psalm-suppress MixedArgument, InvalidOperand
     */
    private static function key(mixed $var, bool $strict, array &$anchors): string
    {
        return match (true) {
            $var === null => $strict ? 'null' : 'int:0',
            \is_bool($var) => ($strict ? 'bool:' : 'int:') . (int) $var,
            \is_int($var) => 'int:' . $var,
            \is_float($var) => self::floatKey($var, $strict),
            \is_string($var) => self::stringKey($var, $strict),
            // A closed resource is no longer \is_resource(), so it needs its own test, and that
            // test has to run before \is_object(). Its id survives the close, so an open and a
            // closed handle keep the same key.
            \is_resource($var), \gettype($var) === 'resource (closed)' => self::resourceKey($var, $anchors),
            \is_object($var) => self::objectKey($var, $strict, $anchors),
            // Only arrays are left. They still hash by \serialize(), whose equality matches
            // neither mode exactly; hashing them element-wise is a separate change. When they do
            // recurse, each element goes back through this routine with the same $anchors list,
            // so nested identities are collected without either entry point changing shape.
            default => 'array_' . \serialize($var),
        };
    }

    /**
     * Key of a float: bit-exact, except that integral values collapse onto their integer in coercive mode.
     */
    private static function floatKey(float $var, bool $strict): string
    {
        if (\is_nan($var)) {
            return 'nan';
        }

        // -0.0 === 0.0 and they are the same number, so the sign of zero must not reach the key.
        $value = $var === 0.0 ? 0.0 : $var;

        if (!$strict && self::isIntegralWithinIntRange($value)) {
            return 'int:' . (int) $value;
        }

        return 'float:' . \bin2hex(\pack('E', $value));
    }

    /**
     * Is the float a whole number that the platform's int type can hold exactly?
     */
    private static function isIntegralWithinIntRange(float $value): bool
    {
        // The upper bound is exclusive and derived from PHP_INT_MIN: (float) PHP_INT_MAX rounds
        // up to 2**63, which is out of int range, so comparing against it would accept a float
        // whose int cast is undefined. INF and -INF fail these comparisons and never fold.
        return $value == \floor($value)
            && $value >= (float) \PHP_INT_MIN
            && $value < -(float) \PHP_INT_MIN;
    }

    /**
     * Key of a string: exact content, except that coercive mode hashes numeric strings by their number.
     *
     * @psalm-suppress InvalidOperand "$var + 0" deliberately applies PHP's numeric-string rules
     */
    private static function stringKey(string $var, bool $strict): string
    {
        if ($strict) {
            return self::rawStringKey($var);
        }

        if ($var === '') {
            return 'int:0';
        }

        if (\is_numeric($var)) {
            // "$s + 0" applies PHP's own numeric-string rules and yields an int or a float,
            // which is then hashed by the rules for that type.
            $number = $var + 0;

            return \is_int($number) ? 'int:' . $number : self::floatKey($number, false);
        }

        return self::rawStringKey($var);
    }

    /**
     * Key of a string's exact bytes, length-framed so that delimiters and NUL bytes cannot collide.
     */
    private static function rawStringKey(string $var): string
    {
        return 'string:' . \strlen($var) . ':' . $var;
    }

    /**
     * Key of a resource: its id, open or closed. The handle anchors it.
     *
     * @param mixed $var an open or closed resource
     * @param list<object|resource> $anchors
     *
     * @psalm-suppress InvalidArgument a closed resource keeps the id get_resource_id() reads
     */
    private static function resourceKey(mixed $var, array &$anchors): string
    {
        /** @var resource $var */
        $anchors[] = $var;

        return 'resource:' . \get_resource_id($var);
    }

    /**
     * Key of an object: by instance, except for ordinary objects in coercive mode.
     *
     * An object hashed by instance anchors itself; one hashed by serialized state is compared by
     * content and needs no anchor.
     *
     * @param object $var
     * @param bool $strict
     * @param list<object|resource> $anchors
     */
    private static function objectKey(object $var, bool $strict, array &$anchors): string
    {
        if ($strict || $var instanceof \Generator || $var instanceof \Closure) {
            $anchors[] = $var;

            return 'object:' . \spl_object_id($var);
        }

        $serialized = self::serializeObject($var);

        return 'serialized:' . \strlen($serialized) . ':' . $serialized;
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

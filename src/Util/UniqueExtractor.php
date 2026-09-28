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
     * Deepest array nesting that can be hashed.
     *
     * Depth is counted per value: a scalar is depth 0, `[$scalar]` is depth 1, `[[$scalar]]` is
     * depth 2. A value at depth greater than this throws \InvalidArgumentException.
     */
    public const MAX_DEPTH = 256;

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

        return self::key($var, $strict, $anchors, 0);
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
        $key = self::key($var, $strict, $anchors, 0);

        return new Identity($key, \array_values($anchors));
    }

    /**
     * Identity key for one anchor: 'o' followed by its spl_object_id() for an object, 'r' followed
     * by its get_resource_id() for a resource (open or closed). Separate prefixes because the two
     * id spaces overlap.
     *
     * Used both here, to de-duplicate the anchors collected within one call, and by consumers
     * (ValueCounter, UsageMap) that de-duplicate anchors across values.
     *
     * @internal
     *
     * @param object|resource|closed-resource $anchor
     *
     * @psalm-suppress InvalidArgument a closed resource keeps the id get_resource_id() reads
     */
    public static function anchorId(mixed $anchor): string
    {
        return \is_object($anchor) ? 'o' . \spl_object_id($anchor) : 'r' . \get_resource_id($anchor);
    }

    /**
     * Key of any value, appending every value hashed by identity to $anchors.
     *
     * This is the single routine behind both entry points: getString() throws the collected
     * anchors away, identify() hands them to the caller, de-duplicated. Values hashed by content
     * (scalars, strings, serialized objects) contribute no anchors.
     *
     * $anchors is keyed by {@see self::anchorId()} rather than being a plain list, so an object or
     * resource nested repeatedly within the same value — e.g. the same object under two branches
     * of an array — is appended once; see {@see Identity} for why that de-duplication is safe.
     *
     * @param mixed $var
     * @param bool $strict
     * @param array<string, object|resource|closed-resource> $anchors keyed by self::anchorId()
     * @param int $depth number of arrays enclosing $var, 0 at the top value; see self::MAX_DEPTH
     *
     * @return string
     *
     * @throws \InvalidArgumentException if $var nests arrays deeper than self::MAX_DEPTH
     */
    private static function key(mixed $var, bool $strict, array &$anchors, int $depth): string
    {
        // Arrays are handled before the match so that every arm below describes a leaf value.
        if (\is_array($var)) {
            return self::arrayKey($var, $strict, $anchors, $depth);
        }

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
            // Unreachable: arrays are taken above and every remaining PHP type has an arm. The
            // arm exists because match(true) would otherwise raise \UnhandledMatchError.
            default => throw new \InvalidArgumentException('Unsupported value type: ' . \gettype($var)),
        };
    }

    /**
     * Key of an array: `array:[{keytoken}={len}:{childkey};...]`.
     *
     * Key tokens are `i{n}` for an integer key and `s{len}:{str}` for a string key; the string-key
     * length prefix is what stops a key's content from imitating the `=` and `;` separators. No
     * element count is needed: reading from `array:[`, each child ends where its `{len}:` prefix
     * says, so a `]` inside a child is never mistaken for the array's end, and the array ends at
     * the first `]` found where a key token would otherwise start. The key therefore parses back
     * to exactly one sequence of (key, child) pairs, and two different arrays cannot share it.
     * Children go back through {@see self::key()} with the same mode and the same $anchors list,
     * which is what makes an array equal to another exactly when its elements are.
     *
     * @param array<array-key, mixed> $var
     * @param bool $strict
     * @param array<string, object|resource|closed-resource> $anchors keyed by self::anchorId()
     * @param int $depth number of arrays enclosing $var, 0 if $var is the top value
     *
     * @throws \InvalidArgumentException if the nesting exceeds self::MAX_DEPTH
     */
    private static function arrayKey(array $var, bool $strict, array &$anchors, int $depth): string
    {
        // $var itself sits one level below the array that holds it, so it is at depth $depth + 1.
        // This is a depth limit, not cycle detection: a self-referential array is caught only
        // because PHP keeps the reference slot when the array is copied, so the recursion revisits
        // it and the counter keeps climbing. Cycle detection would need per-call bookkeeping of
        // every array already visited, and there is no behavior to stay compatible with — PHP's
        // own == fatals on a self-referential array — so a fixed limit is the cheaper contract.
        if ($depth >= self::MAX_DEPTH) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Array is nested deeper than the maximum depth of %d and cannot be hashed. '
                    . 'This is a depth limit, not cycle detection: a self-referential array reaches it too.',
                    self::MAX_DEPTH,
                ),
            );
        }

        $elements = '';
        foreach ($var as $key => $value) {
            $childKey = self::key($value, $strict, $anchors, $depth + 1);
            $elements .= self::arrayKeyToken($key) . '=' . \strlen($childKey) . ':' . $childKey . ';';
        }

        return 'array:[' . $elements . ']';
    }

    /**
     * Token for one array key: `i{n}` for an integer key, `s{len}:{str}` for a string key.
     *
     * @param array-key $key
     */
    private static function arrayKeyToken(int|string $key): string
    {
        return \is_int($key) ? 'i' . $key : 's' . \strlen($key) . ':' . $key;
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
     * @param array<string, object|resource|closed-resource> $anchors keyed by self::anchorId()
     *
     * @psalm-suppress InvalidArgument a closed resource keeps the id get_resource_id() reads
     */
    private static function resourceKey(mixed $var, array &$anchors): string
    {
        /** @var resource $var */
        $anchors[self::anchorId($var)] = $var;

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
     * @param array<string, object|resource|closed-resource> $anchors keyed by self::anchorId()
     */
    private static function objectKey(object $var, bool $strict, array &$anchors): string
    {
        if ($strict || $var instanceof \Generator || $var instanceof \Closure) {
            $anchors[self::anchorId($var)] = $var;

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

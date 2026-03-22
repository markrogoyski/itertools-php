<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 * Tool for extracting unique IDs and hashes of any PHP variables and data structures.
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
     * If $strict is true:
     *  - scalars: result is unique strictly by type;
     *  - objects: result is unique by instance;
     *  - arrays: result is unique by serialized value;
     *  - resources: result is unique by instance.
     *
     * If $strict is false:
     *  - scalars: result is unique by value;
     *  - objects: result is unique by serialized value (throws \InvalidArgumentException if not serializable);
     *  - arrays: result is unique by serialized value.
     *  - resources: result is unique by instance.
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
            \gettype($var) === 'boolean' => 'boolean_' . \intval($var),
            /** @phpstan-ignore cast.string */
            $strict => \gettype($var) . '_' . (string) $var,
            !$var => 'boolean_0',
            /** @phpstan-ignore argument.type */
            \strval($var) === '1' => 'boolean_1',
            \is_numeric($var) => 'numeric_' . \floatval($var),
            /** @phpstan-ignore cast.string */
            default => 'scalar_' . (string) $var,
        };
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

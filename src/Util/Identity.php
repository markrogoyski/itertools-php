<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 * A value's hash key together with the anchors that key depends on.
 *
 * An anchor is a value that {@see UniqueExtractor} hashed by identity rather than by content:
 * every object in strict mode (ordinary objects, enum cases, closures, generators), closures,
 * generators and resources in coercive mode, and — once arrays hash recursively — the same kinds
 * of values found inside an array.
 *
 * Those keys are built from `spl_object_id()` and `get_resource_id()`, and neither id is unique
 * over time: it is unique only among the values that are alive at the moment it is read. PHP
 * hands a freed object's id to the next object it allocates, so a key computed from a value that
 * has since been released can be re-issued to a later, unrelated value, which then compares equal
 * to it. Any consumer that keeps a key across iterations — a `distinct` filter, a frequency
 * table, a usage map — must therefore keep the anchors that produced the key alive for as long as
 * it keeps the key.
 *
 * Retaining the value the key was computed from is not enough. PHP preserves reference slots when
 * an array is copied, so a retained `[&$slot]` follows later assignments to `$slot` and releases
 * the objects it held before; a retained representative array can therefore stop pinning the very
 * identities its key was derived from. The anchors are the identity-bearing values themselves, so
 * holding them pins those ids whatever the surrounding array does.
 *
 * Anchors are never deduplicated by id: an id is only stable while its anchor is held, which is
 * the whole point of holding them.
 *
 * See README.md, section "Strict and Coercive Types", subsection "Retained values".
 */
final class Identity
{
    /**
     * @param string $key unique ID string of the value, in the sense of {@see UniqueExtractor::getString()}
     * @param list<object|resource> $anchors every value hashed by identity while computing the key
     */
    public function __construct(public readonly string $key, public readonly array $anchors)
    {
    }
}

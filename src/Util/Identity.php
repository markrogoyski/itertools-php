<?php

declare(strict_types=1);

namespace IterTools\Util;

/**
 * @internal
 * A value's hash key together with the anchors that key depends on.
 *
 * An anchor is a value that {@see UniqueExtractor} hashed by identity rather than by content:
 * every object in strict mode (ordinary objects, enum cases, closures, generators), closures,
 * generators and resources in coercive mode, and the same kinds of values found inside an array
 * at any depth.
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
 * Anchors are de-duplicated by the id they hold: {@see UniqueExtractor::key()} collects them into
 * an array keyed by {@see UniqueExtractor::anchorId()} before this class is handed a plain list,
 * so the same object or resource met twice while computing one key contributes one anchor. This is
 * safe because the array itself holds the anchor, so its id cannot be reused while held — a value
 * met again with the same id during the same call must therefore be the same anchor.
 *
 * A coercive-mode object is hashed by serialized state and so is deliberately not anchored; an
 * object whose `__serialize()` or `__sleep()` embeds its own identity (`spl_object_id()`,
 * `spl_object_hash()`) therefore puts a recyclable id into a key that nothing pins, and is outside
 * this contract.
 *
 * See README.md, section "Strict and Coercive Types", subsection "Retained values".
 */
final class Identity
{
    /**
     * @param string $key unique ID string of the value, in the sense of {@see UniqueExtractor::getString()}
     * @param list<object|resource|closed-resource> $anchors every value hashed by identity while computing the key
     */
    public function __construct(public readonly string $key, public readonly array $anchors)
    {
    }
}

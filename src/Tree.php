<?php

declare(strict_types=1);

namespace IterTools;

/**
 * @phpstan-type DictAccess = array<string, mixed>|\ArrayAccess<string, mixed>|object
 */
class Tree
{
    /**
     * Iterates a tree like a flat collection using deep traversal.
     *
     * If $childrenContainerKey is not null looks for children items using by this key only.
     *
     * Otherwise, considers any subarray to contain children.
     *
     * @param iterable<DictAccess> $data
     * @param ?string              $childrenContainerKey
     *
     * @return \Generator
     */
    public static function traverseDeep(iterable $data, ?string $childrenContainerKey = null): \Generator
    {
        yield from static::traverseDeepRecursive($data, $childrenContainerKey);
    }

    /**
     * Iterates a tree like a flat collection using wide traversal.
     *
     * If $childrenContainerKey is not null looks for children items using by this key only.
     *
     * Otherwise, considers any subarray to contain children.
     *
     * @param iterable<DictAccess> $data
     * @param ?string              $childrenContainerKey
     *
     * @return \Generator
     */
    public static function traverseWide(iterable $data, ?string $childrenContainerKey = null): \Generator
    {
        $level = 0;
        do {
            $subLevelContainer = [];
            foreach ($data as $datum) {
                if ($childrenContainerKey !== null) {
                    yield $level => $datum;
                    $childrenContainer = static::accessValue($datum, $childrenContainerKey);
                } else {
                    if (!is_iterable($datum)) {
                        yield $level => $datum;
                    }
                    $childrenContainer = $datum;
                }
                if (is_iterable($childrenContainer)) {
                    foreach ($childrenContainer as $child) {
                        $subLevelContainer[] = $child;
                    }
                }
            }
            $data = $subLevelContainer;
            ++$level;
        } while (count($subLevelContainer));
    }

    /**
     * Recursive helper method for wide traversal.
     *
     * @param iterable<DictAccess> $data
     * @param ?string              $childrenContainerKey
     * @param int                  $initialLevel
     *
     * @return \Generator
     */
    protected static function traverseDeepRecursive(
        iterable $data,
        ?string $childrenContainerKey = null,
        int $initialLevel = 0
    ): \Generator {
        $level = $initialLevel;
        foreach ($data as $datum) {
            if ($childrenContainerKey !== null) {
                yield $level => $datum;
                $childrenContainer = static::accessValue($datum, $childrenContainerKey);
            } else {
                if (!is_iterable($datum)) {
                    yield $level => $datum;
                }
                $childrenContainer = $datum;
            }
            if (is_iterable($childrenContainer)) {
                yield from static::traverseDeepRecursive($childrenContainer, $childrenContainerKey, $level+1);
            }
        }
    }

    /**
     * Access value from container by property name.
     *
     * Works with:
     * - arrays;
     * - ArrayAccess objects;
     * - stdClass objects
     * - another objects (using public properties or getters).
     *
     * @param DictAccess|mixed $container
     * @param string $key
     *
     * @return mixed|null
     */
    protected static function accessValue($container, string $key)
    {
        switch (true) {
            case is_array($container):
                if (array_key_exists($key, $container)) {
                    return $container[$key];
                }
                break;
            case $container instanceof \ArrayAccess:
                if ($container->offsetExists($key)) {
                    return $container[$key];
                }
                break;
            case $container instanceof \stdClass:
                if (property_exists($container, $key)) {
                    return $container->{$key};
                }
                break;
            case is_object($container):
                if (property_exists($container, $key)) {
                    $ref = new \ReflectionProperty(get_class($container), $key);
                    if ($ref->isPublic()) {
                        return $container->{$key};
                    }
                }

                $getterName = 'get'.ucfirst($key);
                if (method_exists($container, $getterName)) {
                    $ref = new \ReflectionMethod(get_class($container), $getterName);
                    if ($ref->isPublic()) {
                        return $container->{$getterName}();
                    }
                }
                break;
        }

        return null;
    }
}
